# Checkout Reminder Notification Setup

## Overview
Employees receive automatic push notifications 10 minutes before office end time to remind them to checkout. This requires:
1. Employees to subscribe to push notifications (now allowed)
2. A cron job to run the checkout reminder command every minute

## Step 1: Update Database

Run the SQL update to add the `checkout_reminder_enabled` field:

```sql
-- File: db/add_checkout_reminder_setting.sql
ALTER TABLE `notification_settings` 
ADD COLUMN IF NOT EXISTS `checkout_reminder_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled' AFTER `birthday_notifications_enabled`;
```

Or run directly:
```sql
ALTER TABLE `notification_settings` 
ADD COLUMN `checkout_reminder_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled' AFTER `birthday_notifications_enabled`;
```

## Step 2: Setup Cron Job

The checkout reminder command needs to run **every minute** to check if it's 10 minutes before office end time.

### Option A: Using cPanel Cron Jobs

1. Login to cPanel
2. Go to **Cron Jobs** section
3. Add a new cron job with these settings:
   - **Minute**: `*` (every minute)
   - **Hour**: `*` (every hour)
   - **Day**: `*` (every day)
   - **Month**: `*` (every month)
   - **Weekday**: `*` (every day of week)
   - **Command**: 
   ```bash
   /usr/bin/php /home/u573967329/domains/sanvihr.fableadtech.com/public_html/spark notify:checkout-reminder
   ```

### Option B: Using SSH/Crontab

1. SSH into your server
2. Edit crontab:
   ```bash
   crontab -e
   ```
3. Add this line (runs every minute):
   ```bash
   * * * * * /usr/bin/php /home/u573967329/domains/sanvihr.fableadtech.com/public_html/spark notify:checkout-reminder >> /dev/null 2>&1
   ```
4. Save and exit

### Option C: Using Direct PHP Path

If `spark` doesn't work, use the full PHP path:
```bash
* * * * * cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /usr/bin/php spark notify:checkout-reminder >> /dev/null 2>&1
```

## Step 3: Configure Office End Time

Make sure the office end time is configured in **Settings → Company Rules → End Time**.

The system will automatically:
- Calculate 10 minutes before the configured end time
- Check every minute if current time matches (with 1-minute tolerance)
- Send notifications to employees who are checked in but not checked out

## Step 4: Employee Subscription

Employees need to:
1. Login to the system
2. Allow push notifications when prompted (or enable in browser settings)
3. They will automatically subscribe to receive checkout reminders

**Note**: Employees can now subscribe to push notifications (previously only admins could).

## Step 5: Test the Command

Test the command manually to ensure it works:

```bash
cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html
php spark notify:checkout-reminder
```

You should see output like:
- `⏰ It's 10 minutes before office end time. Sending checkout reminders...`
- `✅ Sent checkout reminder to: [Employee Name]`
- `Checkout reminders sent successfully ✅ (X employee(s))`

Or if not the right time:
- `Current time: HH:MM, End time: HH:MM, Difference: X minutes. Not sending reminders yet.`

## How It Works

1. **Every minute**, the cron job runs the `notify:checkout-reminder` command
2. The command:
   - Gets office end time from Company Rules
   - Calculates 10 minutes before end time
   - Checks if current time is 10 minutes before (with 1-minute tolerance: 9-11 minutes)
   - Finds all employees checked in but not checked out today
   - Sends push notification to each employee
3. **Employees receive notification** with message: "⏰ Don't forget to checkout! Office time ends in 10 minutes (HH:MM)"
4. **Clicking notification** opens the attendance page

## Notification Settings

You can enable/disable checkout reminders in:
- **Settings → Push Notifications → Checkout Reminder** (if UI is added)
- Or directly in database: `UPDATE notification_settings SET checkout_reminder_enabled = 0;` (to disable)

## Troubleshooting

### Cron job not running?
1. Check cron job syntax: `* * * * *` means every minute
2. Verify PHP path: Run `which php` to get correct path
3. Check file permissions: Ensure `spark` file is executable
4. Check server timezone: Command uses `Asia/Kolkata` timezone

### Notifications not sending?
1. Check if checkout reminder notifications are enabled in Settings
2. Verify office end time is configured in Company Rules
3. Verify employees have subscribed to push notifications (they need to login and allow notifications)
4. Check that employees are checked in but not checked out
5. Review server logs: `writable/logs/log-YYYY-MM-DD.log`

### Employees not receiving notifications?
1. Employees must login and allow push notifications
2. Check browser notification permissions
3. Verify employee subscriptions exist in database:
   ```sql
   SELECT ps.*, u.username, u.role 
   FROM push_subscriptions ps 
   JOIN users u ON ps.user_id = u.id 
   WHERE u.role IN ('employee', 'hr');
   ```

### Multiple notifications?
- The command checks time with 1-minute tolerance (9-11 minutes before)
- Each employee receives only one notification per day
- If you see duplicates, check if cron job is running multiple times

## Manual Testing

To test without waiting for the exact time:

1. Temporarily modify the command to test with a specific time
2. Or manually check in an employee and wait for the reminder
3. Or adjust the office end time temporarily to test

## Notes

- **Timezone**: Uses `Asia/Kolkata` timezone
- **Frequency**: Checks every minute, sends only when 10 minutes before end time
- **Scope**: Only employees and HR who are checked in but not checked out
- **Requirement**: Office end time must be configured in Company Rules
- **Enable/Disable**: Can be toggled via `checkout_reminder_enabled` setting









