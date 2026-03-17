# Mobile Push Notification Troubleshooting Guide

## Quick Checklist

1. ✅ **VAPID Keys Configured** - Keys are in .env file
2. ⚠️ **HTTPS Required** - Site must be served over HTTPS
3. ⚠️ **Admin Login** - Only admin users receive notifications
4. ⚠️ **Permission Granted** - Browser must have notification permission

## Step-by-Step Testing

### 1. Login as Admin on Mobile

- Open your website on mobile browser (Chrome recommended)
- Login as admin user
- The system will automatically try to register for push notifications

### 2. Check Browser Console

On mobile, you can check console logs:
- **Chrome Android**: Connect phone via USB, enable USB debugging, use Chrome DevTools
- **Safari iOS**: Use Safari Web Inspector on Mac

Look for these messages:
- ✅ "Service Worker registered"
- ✅ "Service Worker is ready"
- ✅ "Successfully subscribed to push notifications"
- ✅ "Subscription saved to server successfully"

### 3. Check Notification Permission

If you see a message "Enable Notifications" at bottom right:
- Click the "Enable" button
- Allow notifications when browser prompts
- You should see a success message

### 4. Verify Subscription in Database

Run this SQL query:
```sql
SELECT * FROM push_subscriptions WHERE user_id = [YOUR_ADMIN_USER_ID];
```

You should see at least one subscription record.

### 5. Test Check-In/Check-Out

- Have an employee check in or check out
- Admin should receive push notification on mobile
- Notification should appear even if app is closed

## Common Issues & Solutions

### Issue 1: "Push notifications are not supported"
**Solution**: 
- Use Chrome or Firefox on Android
- iOS Safari requires iOS 16.4+ and proper PWA setup
- Ensure site is served over HTTPS

### Issue 2: "Notification permission denied"
**Solution**:
- Click the "Enable" button that appears
- Or go to browser settings > Site settings > Notifications > Allow
- Clear browser cache and try again

### Issue 3: "Service Worker not registered"
**Solution**:
- Check that `/service-worker.js` is accessible
- Open browser DevTools > Application > Service Workers
- Check for errors
- Try unregistering and refreshing

### Issue 4: "Subscription saved but no notifications"
**Solution**:
- Check server logs for push notification errors
- Verify VAPID keys are correct in .env
- Check that employee check-in/check-out is triggering notifications
- Verify admin user_id matches subscription user_id

### Issue 5: Notifications work on desktop but not mobile
**Solution**:
- Mobile browsers have stricter requirements
- Ensure site is added to home screen (PWA)
- Check mobile browser notification settings
- Try different mobile browser

## Manual Testing Steps

1. **Open mobile browser console** (if possible)
2. **Login as admin**
3. **Check for these console messages**:
   ```
   Service Worker registered: [object]
   Service Worker is ready
   Notification permission: granted
   Push subscription created: [endpoint]
   Subscription saved to server successfully
   ```

4. **If permission is "default"**:
   - Look for "Enable Notifications" button
   - Click it and allow notifications

5. **Test notification**:
   - Have employee check in
   - Check mobile device for notification
   - Notification should appear even if browser is closed

## Debugging Commands

### Check Service Worker Status
```javascript
// In browser console
navigator.serviceWorker.getRegistrations().then(registrations => {
  console.log('Service Workers:', registrations);
});

// Check subscription
navigator.serviceWorker.ready.then(registration => {
  registration.pushManager.getSubscription().then(subscription => {
    console.log('Subscription:', subscription);
  });
});
```

### Check Notification Permission
```javascript
// In browser console
console.log('Notification permission:', Notification.permission);
```

### Manually Request Permission
```javascript
// In browser console
Notification.requestPermission().then(permission => {
  console.log('Permission:', permission);
});
```

## Server-Side Debugging

### Check Logs
```bash
tail -f writable/logs/log-*.log | grep -i "push\|vapid\|notification"
```

### Test Push Notification Manually
You can test by calling the notification service directly in PHP:
```php
$pushService = new \App\Services\PushNotificationService();
$result = $pushService->notifyAdmins('Test', 'This is a test notification');
print_r($result);
```

## Still Not Working?

1. **Check HTTPS**: Push notifications ONLY work over HTTPS
2. **Check Browser**: Use Chrome or Firefox (Safari has limitations)
3. **Check Permissions**: Browser must allow notifications
4. **Check Service Worker**: Must be registered and active
5. **Check Database**: Subscription must exist for admin user
6. **Check VAPID Keys**: Must match between server and client
7. **Check Logs**: Server logs will show push notification errors

## Contact Support

If still not working, provide:
- Browser type and version
- Mobile device type
- Console error messages
- Server log errors
- Database subscription records

