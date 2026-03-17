# Fix: Empty push_subscriptions Table

## Problem
Your `push_subscriptions` table is empty, which means subscriptions are not being saved to the database.

## Step-by-Step Fix

### Step 1: Check Browser Console

1. **Login as admin** on your mobile device or desktop
2. **Open browser console** (F12)
3. **Refresh the page** (hard refresh: Ctrl+F5)
4. **Look for these messages:**
   - ✅ "Service Worker registered"
   - ✅ "Notification permission: granted"
   - ✅ "Push subscription created"
   - ✅ "Subscription saved to server successfully"

### Step 2: Check for Errors

In browser console, look for:
- ❌ Red error messages
- ❌ "Failed to save subscription"
- ❌ "Unauthorized" errors
- ❌ Network errors (404, 500, etc.)

### Step 3: Manually Test Subscription

In browser console, run:
```javascript
// Check current subscription
navigator.serviceWorker.ready.then(registration => {
    registration.pushManager.getSubscription().then(subscription => {
        if (subscription) {
            console.log('Subscription exists:', subscription.endpoint);
            
            // Try to save it manually
            fetch('/api/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: btoa(String.fromCharCode(...new Uint8Array(subscription.getKey('p256dh')))),
                        auth: btoa(String.fromCharCode(...new Uint8Array(subscription.getKey('auth'))))
                    }
                })
            }).then(r => r.json()).then(console.log);
        } else {
            console.log('No subscription found. Need to create one.');
        }
    });
});
```

### Step 4: Check Server Logs

```bash
tail -f writable/logs/log-*.log | grep -i "push\|subscription"
```

Look for:
- "Push subscription request received"
- "Subscription created successfully"
- Any error messages

### Step 5: Verify Route is Working

Test the endpoint directly:
```bash
curl -X POST https://sanvihr.fableadtech.com/api/push/subscribe \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"endpoint":"test","keys":{"p256dh":"test","auth":"test"}}'
```

### Step 6: Check Database Table Structure

Make sure the table has the correct structure:
```sql
DESCRIBE push_subscriptions;
```

Should show:
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `user_id` (INT)
- `endpoint` (TEXT)
- `keys` (TEXT)
- `created_at` (DATETIME)
- `updated_at` (DATETIME)

## Common Issues

### Issue 1: "Unauthorized" Error
**Solution:**
- Make sure you're logged in
- Check that token exists: `localStorage.getItem('token')`
- Token might be expired - try logging out and back in

### Issue 2: "Endpoint and keys are required"
**Solution:**
- Check browser console for the actual request
- Make sure subscription object has endpoint and keys
- Check network tab to see what's being sent

### Issue 3: Subscription Created But Not Saved
**Solution:**
- Check server logs for database errors
- Verify table permissions
- Check if insert is actually being called

### Issue 4: Route Not Found (404)
**Solution:**
- Verify route exists in `app/Config/Routes.php`
- Check route: `POST /api/push/subscribe`
- Clear CodeIgniter cache if needed

## Quick Fix: Force Subscription

If nothing works, you can manually insert a test subscription:

1. **Get your subscription from browser:**
```javascript
navigator.serviceWorker.ready.then(reg => {
    reg.pushManager.getSubscription().then(sub => {
        console.log('Endpoint:', sub.endpoint);
        console.log('p256dh:', btoa(String.fromCharCode(...new Uint8Array(sub.getKey('p256dh')))));
        console.log('auth:', btoa(String.fromCharCode(...new Uint8Array(sub.getKey('auth')))));
    });
});
```

2. **Manually insert into database:**
```sql
INSERT INTO push_subscriptions (user_id, endpoint, keys, created_at, updated_at) 
VALUES (
    YOUR_ADMIN_USER_ID,
    'YOUR_ENDPOINT_URL',
    '{"p256dh":"YOUR_P256DH_KEY","auth":"YOUR_AUTH_KEY"}',
    NOW(),
    NOW()
);
```

## After Fixing

Once subscription is saved:
1. Check database: `SELECT * FROM push_subscriptions;`
2. Test notification: Run `testPushNotification()` in console
3. Have employee check in/out
4. Check mobile device for notification

## Still Empty?

If table is still empty after trying everything:
1. Check server error logs
2. Check browser network tab for failed requests
3. Verify user is admin
4. Check if JavaScript errors are preventing subscription




