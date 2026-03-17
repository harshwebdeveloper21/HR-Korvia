# Check Subscription Status - Quick Guide

## What You Should See in Console

After refreshing the page, you should see these messages in order:

### ✅ Good Signs:
1. `Service Worker registered` ✅ (You have this!)
2. `Service Worker is ready` ✅
3. `Notification permission: granted` (or `default` or `denied`)
4. `Current subscription: None` or `Current subscription: Exists`
5. `Fetching VAPID public key...`
6. `Public key received, creating subscription...`
7. `Push subscription created: [endpoint]`
8. `Subscription saved to server successfully` ✅

### ❌ Problem Signs:
- `Notification permission: denied` → Need to enable notifications
- `Failed to get public key` → API endpoint issue
- `Failed to create subscription` → Permission or key issue
- `Failed to save subscription` → Server/database issue

## Quick Diagnostic Commands

### 1. Check Notification Permission
```javascript
console.log('Permission:', Notification.permission);
```
**Expected:** `"granted"` (not `"default"` or `"denied"`)

### 2. Check Service Worker
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('Service Workers:', regs.length);
    if (regs.length > 0) {
        regs[0].pushManager.getSubscription().then(sub => {
            console.log('Subscription:', sub ? 'EXISTS ✅' : 'NONE ❌');
            if (sub) {
                console.log('Endpoint:', sub.endpoint.substring(0, 50) + '...');
            }
        });
    }
});
```

### 3. Check Subscription Status
```javascript
fetch('/api/push/status', {
    headers: {'Authorization': 'Bearer ' + localStorage.getItem('token')}
}).then(r => r.json()).then(data => {
    console.log('Database subscriptions:', data);
    console.log('Count:', data.count);
});
```

### 4. Force Create Subscription
```javascript
// If subscription doesn't exist, create it
navigator.serviceWorker.ready.then(registration => {
    registration.pushManager.getSubscription().then(async subscription => {
        if (!subscription) {
            console.log('Creating subscription...');
            
            // Get public key
            const keyResponse = await fetch('/api/push/public-key', {
                headers: {'Authorization': 'Bearer ' + localStorage.getItem('token')}
            });
            const keyData = await keyResponse.json();
            
            if (keyData.status === 'success') {
                // Create subscription
                const sub = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(keyData.publicKey)
                });
                
                console.log('Subscription created:', sub.endpoint);
                
                // Save to server
                const p256dh = btoa(String.fromCharCode(...new Uint8Array(sub.getKey('p256dh'))));
                const auth = btoa(String.fromCharCode(...new Uint8Array(sub.getKey('auth'))));
                
                const saveResponse = await fetch('/api/push/subscribe', {
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
                
                const saveData = await saveResponse.json();
                console.log('Save result:', saveData);
            }
        } else {
            console.log('Subscription already exists');
        }
    });
});
```

## Next Steps Based on What You See

### If you see "Service Worker registered" but nothing else:
1. Check if notification permission is granted
2. Run the diagnostic commands above
3. Check browser console for errors

### If you see "Notification permission: denied":
1. Click the "Enable Notifications" button (if visible)
2. Or go to browser settings → Site settings → Notifications → Allow
3. Refresh the page

### If you see "Failed to get public key":
1. Check if you're logged in
2. Check if token exists: `localStorage.getItem('token')`
3. Check network tab for 404/500 errors

### If you see "Failed to save subscription":
1. Check server logs
2. Check database table structure
3. Verify user is admin

## What to Do Right Now

1. **Open browser console** (F12)
2. **Run this command:**
```javascript
navigator.serviceWorker.ready.then(reg => {
    console.log('Permission:', Notification.permission);
    reg.pushManager.getSubscription().then(sub => {
        console.log('Subscription:', sub ? 'EXISTS' : 'NONE');
        if (!sub) {
            console.log('❌ No subscription - need to create one');
        } else {
            console.log('✅ Subscription exists:', sub.endpoint.substring(0, 50));
        }
    });
});
```

3. **Share the output** - This will tell us exactly what's missing!




