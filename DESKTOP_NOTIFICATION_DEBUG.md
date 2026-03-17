# Desktop Notification Debugging

## Why Desktop Might Not Work

Desktop notifications have different requirements than mobile:

1. **OS Notification Settings** - Must be enabled in Windows/Mac settings
2. **Browser Notification Settings** - Must be enabled in browser
3. **Do Not Disturb Mode** - Must be off
4. **Focus Assist** (Windows) - Must allow notifications
5. **Separate Subscription** - Desktop needs its own subscription

## Step-by-Step Desktop Fix

### Step 1: Check Desktop Subscription Exists

Run in desktop browser console:

```javascript
// Check if desktop subscription exists
navigator.serviceWorker.ready.then(reg => {
    reg.pushManager.getSubscription().then(sub => {
        if (sub) {
            console.log('✅ Desktop subscription EXISTS');
            console.log('Endpoint:', sub.endpoint.substring(0, 60));
            
            // Check if it's saved in database
            fetch('/api/push/status', {
                headers: {'Authorization': 'Bearer ' + localStorage.getItem('token')}
            }).then(r => r.json()).then(data => {
                console.log('Database subscriptions:', data);
                if (data.count === 0) {
                    console.error('❌ Subscription NOT in database! Need to save it.');
                } else {
                    console.log('✅ Subscription is in database');
                }
            });
        } else {
            console.error('❌ No desktop subscription - need to create one');
        }
    });
});
```

### Step 2: Force Create Desktop Subscription

If no subscription exists, run this:

```javascript
(async function() {
    console.log('=== CREATING DESKTOP SUBSCRIPTION ===');
    
    // 1. Check permission
    if (Notification.permission !== 'granted') {
        const perm = await Notification.requestPermission();
        if (perm !== 'granted') {
            console.error('❌ Permission denied');
            return;
        }
    }
    
    // 2. Get service worker
    const regs = await navigator.serviceWorker.getRegistrations();
    const reg = regs[0];
    
    // 3. Get public key
    const keyRes = await fetch('/api/push/public-key', {
        headers: {'Authorization': 'Bearer ' + localStorage.getItem('token')}
    });
    const keyData = await keyRes.json();
    
    if (keyData.status !== 'success') {
        console.error('❌ Failed to get public key');
        return;
    }
    
    // 4. Create subscription
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
    
    const sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(keyData.publicKey)
    });
    
    console.log('✅ Subscription created:', sub.endpoint.substring(0, 50));
    
    // 5. Save to server
    const p256dh = btoa(String.fromCharCode(...new Uint8Array(sub.getKey('p256dh'))));
    const auth = btoa(String.fromCharCode(...new Uint8Array(sub.getKey('auth'))));
    
    const saveRes = await fetch('/api/push/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        body: JSON.stringify({
            endpoint: sub.endpoint,
            keys: { p256dh: p256dh, auth: auth }
        })
    });
    
    const saveData = await saveRes.json();
    console.log('Save result:', saveData);
    
    if (saveData.status === 'success') {
        console.log('✅✅✅ DESKTOP SUBSCRIPTION SAVED!');
    }
})();
```

### Step 3: Check Database

```sql
SELECT * FROM push_subscriptions;
```

You should see:
- **Mobile subscription** (endpoint contains "fcm.googleapis.com" or "wns2-")
- **Desktop subscription** (endpoint contains "updates.push.services.mozilla.com" or "fcm.googleapis.com" with different endpoint)

### Step 4: Check OS Notification Settings

**Windows:**
1. Settings → System → Notifications
2. Make sure notifications are enabled
3. Check "Focus assist" is off
4. Check browser is allowed to send notifications

**Mac:**
1. System Preferences → Notifications
2. Find your browser (Chrome/Firefox)
3. Make sure notifications are enabled
4. Check "Do Not Disturb" is off

### Step 5: Test Desktop Notification

```javascript
// Test if desktop can show notifications
if (Notification.permission === 'granted') {
    new Notification('Test Desktop Notification', {
        body: 'If you see this, desktop notifications work!',
        icon: '/favicon.ico'
    });
} else {
    console.error('❌ Permission not granted');
}
```

### Step 6: Test Push Notification

```javascript
testPushNotification();
```

## Common Desktop Issues

### Issue 1: "Permission denied"
**Solution:**
- Click lock icon in address bar
- Site settings → Notifications → Allow
- Or browser settings → Privacy → Site settings → Notifications

### Issue 2: "Subscription exists but no notifications"
**Solution:**
- Check OS notification settings
- Check "Do Not Disturb" / "Focus Assist" is off
- Check browser is not in fullscreen mode (some browsers block notifications)
- Try different browser (Chrome vs Firefox)

### Issue 3: "Notifications appear but disappear quickly"
**Solution:**
- This is normal for desktop
- Check notification center/action center
- Notifications might be in system tray

### Issue 4: "Works on mobile but not desktop"
**Solution:**
- Mobile and desktop are SEPARATE subscriptions
- You need BOTH in database
- Each device must enable separately

## Verify Desktop Subscription

Run this complete check:

```javascript
(async function() {
    console.log('=== DESKTOP NOTIFICATION CHECK ===');
    
    // 1. Permission
    console.log('1. Permission:', Notification.permission);
    
    // 2. Service Worker
    const regs = await navigator.serviceWorker.getRegistrations();
    console.log('2. Service Workers:', regs.length);
    
    if (regs.length > 0) {
        const reg = regs[0];
        const sub = await reg.pushManager.getSubscription();
        console.log('3. Subscription:', sub ? 'EXISTS ✅' : 'NONE ❌');
        
        if (sub) {
            console.log('4. Endpoint:', sub.endpoint.substring(0, 60));
            
            // Check database
            const statusRes = await fetch('/api/push/status', {
                headers: {'Authorization': 'Bearer ' + localStorage.getItem('token')}
            });
            const statusData = await statusRes.json();
            console.log('5. Database count:', statusData.count);
            console.log('6. Subscriptions:', statusData.subscriptions);
        }
    }
    
    // 7. Test native notification
    if (Notification.permission === 'granted') {
        try {
            const testNotif = new Notification('Desktop Test', {
                body: 'If you see this, notifications work!',
                icon: '/favicon.ico'
            });
            console.log('7. Native notification test: ✅ SUCCESS');
            setTimeout(() => testNotif.close(), 3000);
        } catch (e) {
            console.error('7. Native notification test: ❌ FAILED', e);
        }
    }
    
    console.log('=== CHECK COMPLETE ===');
})();
```

## Still Not Working?

1. **Check browser:** Use Chrome or Firefox (not Safari on Windows)
2. **Check OS:** Make sure Windows/Mac notifications are enabled
3. **Check database:** Verify desktop subscription exists
4. **Check logs:** Look for push notification errors
5. **Try different browser:** Chrome vs Firefox might behave differently

## Important Notes

- **Desktop notifications are different from mobile**
- **Each device needs its own subscription**
- **Desktop might show notifications in system tray/notification center**
- **Some browsers block notifications in fullscreen mode**
- **OS "Do Not Disturb" can block notifications**




