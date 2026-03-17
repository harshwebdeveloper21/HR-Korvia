# Push Notification Behavior: Admin vs Employee

## Summary

### ✅ **Admin Users**
- **On Login**: Automatically subscribed to push notifications
- **On Logout**: Subscription **REMOVED** (notifications only active during login session)
- **Why**: Admins receive notifications only when they are logged in and actively using the system

### ✅ **Employee/HR Users**
- **On Login**: NOT subscribed (code prevents it)
- **On Logout**: Any existing subscriptions are **REMOVED**
- **Why**: Employees should NOT receive notifications about other employees

---

## Detailed Behavior

### Admin Login Flow

1. **Admin logs in** → Dashboard loads
2. **JavaScript detects admin role** → Calls `initializePushNotifications()`
3. **Service worker registers** → Requests notification permission
4. **Subscription created** → Saved to database with `user_id` and `role = 'admin'`
5. **Admin can now receive notifications** → Even when logged out

### Admin Logout Flow

1. **Admin clicks logout**
2. **Browser unsubscribes** → Removes subscription from browser
3. **Server removes subscription** → Deletes from database
4. **Admin stops receiving notifications** → Until next login

### Employee Login Flow

1. **Employee logs in** → Dashboard loads
2. **JavaScript detects employee role** → Does NOT call `initializePushNotifications()`
3. **No subscription created** → Employee cannot subscribe
4. **If employee tries to subscribe** → Server returns 403 error

### Employee Logout Flow

1. **Employee clicks logout**
2. **Browser unsubscribes** → Removes subscription from browser
3. **Server removes subscription** → Deletes from database
4. **Employee stops receiving notifications** → Immediately

---

## Code Implementation

### Login (Automatic Subscription for Admins)
**File**: `app/Views/dashboard/navbar.php`
```javascript
<?php if ($role === 'admin') : ?>
    // Admin Login: Automatically subscribe to push notifications
    initializePushNotifications().then(() => {
        console.log('✅ Subscribed to push notifications');
    });
<?php endif; ?>
```

### Logout (Remove All Subscriptions)
**File**: `app/Controllers/api/AuthController.php`
```php
// Remove push notification subscriptions for ALL users on logout
$pushSubscriptionModel->where('user_id', $user->sub)->delete();
log_message('info', 'Removed subscription(s) for user on logout');
```

### Subscribe Endpoint (Admin Only)
**File**: `app/Controllers/api/PushNotificationController.php`
```php
if ($user->role !== 'admin') {
    return $this->respond([
        'status' => 'error',
        'message' => 'Only admins can subscribe to push notifications'
    ], 403);
}
```

---

## Testing Checklist

### Test Admin:
- [ ] Login as admin → Should see subscription created in console
- [ ] Check database → Should see subscription with `user_id` = admin ID
- [ ] Have employee check in → Admin should receive notification (while logged in)
- [ ] Logout as admin → Subscription should be removed from database
- [ ] Have employee check in → Admin should NOT receive notification (logged out)
- [ ] Login again as admin → Should create new subscription

### Test Employee:
- [ ] Login as employee → Should NOT see subscription attempt
- [ ] Check database → Should NOT see subscription for employee
- [ ] Try to subscribe manually → Should get 403 error
- [ ] Logout as employee → Should see unsubscribe in console
- [ ] Have another employee check in → Employee should NOT receive notification

---

## Important Notes

1. **Push notifications are session-based**: Subscriptions are created on login and removed on logout
2. **Admins subscribe on login**: Automatically subscribed when admin logs in
3. **Admins unsubscribe on logout**: Subscription removed so notifications only work during active session
4. **Employees cannot subscribe**: Code prevents it at multiple levels (client-side and server-side)
5. **Automatic cleanup**: All subscriptions are removed on logout (both admin and employee)
6. **Safety checks**: System verifies all subscriptions belong to active admin users before sending

---

## Troubleshooting

### Admin not receiving notifications:
1. Check if admin is subscribed: Run `cleanup-non-admin-subscriptions.php`
2. Check browser console for subscription errors
3. Verify VAPID keys are configured correctly
4. Check service worker is registered

### Employee receiving notifications:
1. Run cleanup script: `php cleanup-non-admin-subscriptions.php`
2. Ask employee to clear browser cache
3. Verify employee is not logged in as admin on another device
4. Check database for employee subscriptions

