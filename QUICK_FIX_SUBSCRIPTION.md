# Quick Fix: Get Subscription Saved to Database

## The Problem
Your `push_subscriptions` table is empty, so notifications can't be sent even when employees check in/out.

## Quick Solution - Run This in Browser Console

**After refreshing the page, run this complete script:**

```javascript
(async function() {
    console.log('=== FORCING SUBSCRIPTION CREATION ===');
    
    // 1. Check service worker
    const regs = await navigator.serviceWorker.getRegistrations();
    if (regs.length === 0) {
        console.error('❌ No service worker found!');
        return;
    }
    
    const reg = regs[0];
    console.log('✅ Service worker found');
    
    // 2. Check permission
    if (Notification.permission !== 'granted') {
        console.log('⚠️ Requesting permission...');
        const perm = await Notification.requestPermission();
        if (perm !== 'granted') {
            console.error('❌ Permission denied');
            return;
        }
    }
    console.log('✅ Permission granted');
    
    // 3. Check existing subscription
    let sub = await reg.pushManager.getSubscription();
    
    if (!sub) {
        console.log('📝 No subscription, creating one...');
        
        // Get public key
        const token = localStorage.getItem('token');
        if (!token) {
            console.error('❌ No token found! Please login again.');
            return;
        }
        
        console.log('📡 Fetching public key...');
        const keyRes = await fetch('/api/push/public-key', {
            headers: {'Authorization': 'Bearer ' + token}
        });
        
        if (!keyRes.ok) {
            console.error('❌ Failed to get public key:', keyRes.status);
            return;
        }
        
        const keyData = await keyRes.json();
        console.log('🔑 Public key response:', keyData);
        
        if (keyData.status !== 'success' || !keyData.publicKey) {
            console.error('❌ Invalid public key response');
            return;
        }
        
        // Convert key
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
        
        // Create subscription
        console.log('📝 Creating push subscription...');
        sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(keyData.publicKey)
        });
        
        console.log('✅ Subscription created!');
        console.log('📋 Endpoint:', sub.endpoint.substring(0, 50) + '...');
    } else {
        console.log('✅ Subscription already exists');
    }
    
    // 4. Save to server
    console.log('📤 Saving to server...');
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
    console.log('💾 Save response:', saveData);
    
    if (saveData.status === 'success') {
        console.log('✅✅✅ SUCCESS! Subscription saved to database!');
        console.log('✅ Check your database now - you should see a row!');
        
        // Verify
        const verifyRes = await fetch('/api/push/status', {
            headers: {'Authorization': 'Bearer ' + localStorage.getItem('token')}
        });
        const verifyData = await verifyRes.json();
        console.log('✅ Verification:', verifyData);
    } else {
        console.error('❌ Failed to save:', saveData.message);
    }
    
    console.log('=== DONE ===');
})();
```

## After Running This Script

1. **Check Database:**
   ```sql
   SELECT * FROM push_subscriptions;
   ```
   You should see at least one row now!

2. **Test Notification:**
   ```javascript
   testPushNotification();
   ```

3. **Have Employee Check In/Out:**
   - You should now receive notifications!

## If Script Fails

Check the console for errors:
- ❌ "No token found" → Login again
- ❌ "Failed to get public key" → Check API endpoint
- ❌ "Failed to save" → Check server logs

## Check Server Logs

```bash
tail -f writable/logs/log-*.log | grep -i "push\|subscription"
```

You should see:
- "Push subscription request received"
- "Subscription created successfully"




