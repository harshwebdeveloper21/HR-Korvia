# Debug Push Notifications - Step by Step

## ✅ FREE Solution - No Paid Services Required

**Important:** This push notification system is **100% FREE** and uses:
- **VAPID (Voluntary Application Server Identification)** - Free, open standard
- **Browser's native Push API** - Free, built into browsers
- **No third-party services** - No Firebase, OneSignal, or other paid services
- **No subscriptions** - No monthly fees or per-notification charges
- **Self-hosted** - Everything runs on your server

**You don't need to:**
- ❌ Sign up for any paid service
- ❌ Purchase any subscription
- ❌ Pay per notification
- ❌ Use any third-party APIs

**Everything is free and runs on your own server!**

## Quick Test Steps

### 1. Check if Subscription Exists

Open browser console and run:
```javascript
// Check subscription status
fetch('/api/push/status', {
    headers: {
        'Authorization': 'Bearer ' + localStorage.getItem('token')
    }
}).then(r => r.json()).then(console.log);
```

**Expected Result:**
- If you see `count: 1` or more, subscription exists ✅
- If you see `count: 0`, subscription doesn't exist ❌

### 2. Test Push Notification

In browser console, run:
```javascript
testPushNotification();
```

**Expected Result:**
- You should see a success message
- Check your mobile device for the notification
- Check server logs for push notification results

### 3. Check Server Logs

```bash
tail -f writable/logs/log-*.log | grep -i "push\|notification\|vapid"
```

Look for:
- "Sending push notification to admins"
- "Found X admin subscriptions"
- "Push notification sent successfully"
- Any error messages

### 4. Check Database

```sql
SELECT * FROM push_subscriptions WHERE user_id = [YOUR_ADMIN_USER_ID];
```

**Expected:**
- At least one row with your user_id
- Valid endpoint URL
- Valid keys JSON

### 5. Verify Service Worker

In browser console:
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('Service Workers:', regs);
    regs.forEach(reg => {
        reg.pushManager.getSubscription().then(sub => {
            console.log('Subscription:', sub);
        });
    });
});
```

## Common Issues

### Issue: "No subscriptions found"
**Solution:**
1. Make sure you're logged in as admin
2. Refresh the page
3. Allow notifications when prompted
4. Check console for "Subscription saved to server successfully"

### Issue: "Subscription exists but no notifications"
**Solution:**
1. Check server logs for errors
2. Verify VAPID keys in .env match
3. Test with `testPushNotification()` function
4. Check if employee actually checked in/out

### Issue: "Service Worker not active"
**Solution:**
1. Unregister old service workers:
   ```javascript
   navigator.serviceWorker.getRegistrations().then(regs => {
       regs.forEach(reg => reg.unregister());
   });
   ```
2. Hard refresh page (Ctrl+F5)
3. Check console for service worker registration

## Testing Checklist

- [ ] Service worker registered (check console)
- [ ] Notification permission granted (check browser settings)
- [ ] Subscription saved in database
- [ ] Subscription visible in `/api/push/status`
- [ ] Test notification works (`testPushNotification()`)
- [ ] Server logs show push attempts
- [ ] Employee check-in triggers notification
- [ ] Notification appears on mobile device

## Manual Test

1. **Login as admin on mobile**
2. **Open browser console** (if possible) or check network tab
3. **Check subscription:**
   - Go to `/api/push/status`
   - Should show your subscription
4. **Test notification:**
   - Run `testPushNotification()` in console
   - Or have an employee check in
5. **Check mobile device:**
   - Notification should appear
   - Even if browser is closed

## Still Not Working?

1. Check HTTPS - must be served over HTTPS
2. Check browser - Chrome/Firefox recommended
3. Check permissions - must be "granted" not "default"
4. Check logs - server logs will show exact errors
5. Check database - subscription must exist
6. Check VAPID keys - must match in .env

