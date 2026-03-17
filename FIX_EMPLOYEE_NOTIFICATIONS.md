# Fix: Employees Receiving Notifications

## Problem
Employees are receiving push notifications when they shouldn't. Only admins should receive notifications.

## Solution

### Step 1: Run Cleanup Script
Run this command to remove all non-admin subscriptions from the database:

```bash
php cleanup-non-admin-subscriptions.php
```

This will:
- ✅ Keep all admin subscriptions
- ❌ Delete all employee/HR subscriptions
- ❌ Delete subscriptions for users that don't exist

### Step 2: Verify the Fix
The code has been updated to:
1. ✅ **Prevent employees from subscribing** - The `subscribe()` endpoint now checks if the user is an admin
2. ✅ **Auto-delete non-admin subscriptions** - If an employee tries to subscribe, any existing non-admin subscription for that endpoint will be deleted
3. ✅ **Only send to admins** - The `notifyAdmins()` method only queries for admin subscriptions

### Step 3: Test
1. **As Admin:**
   - Login as admin on mobile/desktop
   - Push notifications should work ✅

2. **As Employee:**
   - Login as employee
   - Try to check in/out
   - Employee should NOT receive notifications ✅
   - Admin should receive notifications ✅

## How It Works Now

1. **Subscription:** Only admins can subscribe to push notifications
2. **Sending:** When any employee checks in/out, notifications are sent to ALL admin subscriptions in the database
3. **Receiving:** Only admins receive notifications (even if they're logged out)

## Database Check

To verify your database is clean, run this SQL query:

```sql
SELECT 
    ps.id,
    ps.user_id,
    u.username,
    u.role,
    ps.created_at
FROM push_subscriptions ps
LEFT JOIN users u ON ps.user_id = u.id
ORDER BY ps.id;
```

**Expected Result:** All subscriptions should have `role = 'admin'`

If you see any subscriptions with `role != 'admin'`, run the cleanup script again.



