# Birthday Notification Cron Job Setup

## Overview
Birthday notifications are sent automatically at 12 AM (midnight) every day to admins when employees have birthdays. This requires a cron job to run the birthday notification command.

## Step 1: Update Database

Run the SQL update to add the `birthday_notifications_enabled` field:

```sql
-- File: db/update_notification_settings.sql
ALTER TABLE `notification_settings` 
ADD COLUMN IF NOT EXISTS `birthday_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled' AFTER `leave_notifications_enabled`;
```

Or if you don't have the table yet, run the full SQL:
```sql
-- File: db/notification_settings.sql
```

## Step 2: Setup Cron Job

### Option A: Using cPanel Cron Jobs

1. Login to cPanel
2. Go to **Cron Jobs** section
3. Add a new cron job with these settings:
   - **Minute**: `0`
   - **Hour**: `0` (12 AM midnight)
   - **Day**: `*` (every day)
   - **Month**: `*` (every month)
   - **Weekday**: `*` (every day of week)
   - **Command**: 
   ```bash
   /usr/bin/php /home/u573967329/domains/sanvihr.fableadtech.com/public_html/spark notify:birthday
   ```

### Option B: Using SSH/Crontab

1. SSH into your server
2. Edit crontab:
   ```bash
   crontab -e
   ```
3. Add this line (runs at 12 AM every day):
   ```bash
   0 0 * * * /usr/bin/php /home/u573967329/domains/sanvihr.fableadtech.com/public_html/spark notify:birthday >> /dev/null 2>&1
   ```
4. Save and exit

### Option C: Using Direct PHP Path

If `spark` doesn't work, use the full PHP path:
```bash
0 0 * * * cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /usr/bin/php spark notify:birthday >> /dev/null 2>&1
```

## Step 3: Test the Command

Test the command manually to ensure it works:

```bash
cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html
php spark notify:birthday
```

You should see output like:
- `No birthdays today 🎂` (if no birthdays)
- `✅ Notified admins for: [Employee Name]` (if birthdays found)
- `Birthday notifications sent successfully ✅`

## Step 4: Verify Cron Job

After setting up, wait until midnight or test manually. Check:
1. Admin receives push notifications for birthdays
2. Server logs show the command running
3. Only one notification per day per birthday

## Troubleshooting

### Cron job not running?
1. Check cron job syntax: `0 0 * * *` means 12 AM every day
2. Verify PHP path: Run `which php` to get correct path
3. Check file permissions: Ensure `spark` file is executable
4. Check server timezone: Command uses `Asia/Kolkata` timezone

### Notifications not sending?
1. Check if birthday notifications are enabled in Settings → Push Notifications
2. Verify employees have `date_of_birth` set in their profile
3. Check admin push subscriptions exist
4. Review server logs: `writable/logs/log-YYYY-MM-DD.log`

### Multiple notifications?
- The command only runs once per day via cron
- Each employee birthday triggers one notification to all admins
- If you see duplicates, check if cron job is running multiple times

## Manual Testing

To test without waiting for midnight:

```bash
# Run the command manually
php spark notify:birthday

# Or test with a specific date (modify the command temporarily)
```

## Notes

- **Timezone**: Uses `Asia/Kolkata` timezone
- **Frequency**: Runs once per day at 12 AM
- **Scope**: Only checks employees and HR (not admins)
- **Requirement**: Employees must have `date_of_birth` set in their profile
- **Enable/Disable**: Can be toggled in Settings → Push Notifications


