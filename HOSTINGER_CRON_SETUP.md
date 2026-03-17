# Hostinger Cron Jobs Setup for CodeIgniter 4

## Overview
This guide shows how to set up cron jobs in Hostinger for:
1. **Birthday Notifications** - Runs once daily at midnight
2. **Checkout Reminder** - Runs every minute

## Important: CodeIgniter 4 vs Laravel

**Laravel uses:** `artisan schedule:run`  
**CodeIgniter 4 uses:** `spark [command]`

## Step-by-Step Setup in Hostinger

### 1. Birthday Notification Cron Job

**Schedule:** Once daily at midnight (12:00 AM)

**In Hostinger Cron Jobs interface:**

1. **Time Settings:**
   - **Minute:** `0`
   - **Hour:** `0`
   - **Day:** `*` (every day)
   - **Month:** `*` (every month)
   - **Weekday:** `*` (every day of week)

2. **Command to Run:**
   ```bash
   /bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /opt/alt/php82/usr/bin/php spark notify:birthday >> /dev/null 2>&1"
   ```

   **Alternative (if PHP 8.2 path is different, try these):**
   ```bash
   /bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /usr/bin/php spark notify:birthday >> /dev/null 2>&1"
   ```

   Or check your PHP version:
   ```bash
   /bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /opt/alt/php81/usr/bin/php spark notify:birthday >> /dev/null 2>&1"
   ```

3. Click **Save**

---

### 2. Checkout Reminder Cron Job

**Schedule:** Every minute

**In Hostinger Cron Jobs interface:**

1. **Time Settings:**
   - **Minute:** `*` (every minute)
   - **Hour:** `*` (every hour)
   - **Day:** `*` (every day)
   - **Month:** `*` (every month)
   - **Weekday:** `*` (every day of week)

2. **Command to Run:**
   ```bash
   /bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /opt/alt/php82/usr/bin/php spark notify:checkout-reminder >> /dev/null 2>&1"
   ```

   **Alternative (if PHP 8.2 path is different):**
   ```bash
   /bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /usr/bin/php spark notify:checkout-reminder >> /dev/null 2>&1"
   ```

3. Click **Save**

---

## Finding Your PHP Path

If the commands don't work, you need to find your PHP CLI path. You can:

1. **SSH into your server** and run:
   ```bash
   which php
   ```
   This will show the PHP path.

2. **Common Hostinger PHP paths:**
   - `/opt/alt/php82/usr/bin/php` (PHP 8.2)
   - `/opt/alt/php81/usr/bin/php` (PHP 8.1)
   - `/opt/alt/php80/usr/bin/php` (PHP 8.0)
   - `/usr/bin/php` (Default PHP)

3. **Check PHP version:**
   ```bash
   /opt/alt/php82/usr/bin/php -v
   ```

---

## Testing the Commands

Before setting up cron jobs, test the commands manually via SSH:

### Test Birthday Command:
```bash
cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html
/opt/alt/php82/usr/bin/php spark notify:birthday
```

**Expected output:**
- `No birthdays today 🎂` (if no birthdays)
- `✅ Notified admins for: [Employee Name]` (if birthdays found)

### Test Checkout Reminder Command:
```bash
cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html
/opt/alt/php82/usr/bin/php spark notify:checkout-reminder
```

**Expected output:**
- `Current time: HH:MM, End time: HH:MM, Difference: X minutes. Not sending reminders yet.` (if not the right time)
- `⏰ It's 10 minutes before office end time. Sending checkout reminders...` (if it's the right time)

---

## Complete Cron Job Commands (Copy & Paste)

### Birthday Notification (Daily at Midnight):
```
0 0 * * * /bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /opt/alt/php82/usr/bin/php spark notify:birthday >> /dev/null 2>&1"
```

### Checkout Reminder (Every Minute):
```
* * * * * /bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && /opt/alt/php82/usr/bin/php spark notify:checkout-reminder >> /dev/null 2>&1"
```

---

## Troubleshooting

### Command not found?
- Make sure you're using `spark` (not `artisan`)
- Check the PHP path is correct
- Verify the project path is correct

### Permission denied?
- Make sure `spark` file is executable:
  ```bash
  chmod +x /home/u573967329/domains/sanvihr.fableadtech.com/public_html/spark
  ```

### Cron job not running?
1. Check cron job syntax in Hostinger
2. Use "View Output" button in Hostinger to see errors
3. Check server logs: `writable/logs/log-YYYY-MM-DD.log`

### Wrong PHP version?
- Check which PHP version your site uses in Hostinger
- Update the PHP path in the cron command accordingly

---

## Summary

**Birthday Notification:**
- **Schedule:** `0 0 * * *` (midnight daily)
- **Command:** `spark notify:birthday`

**Checkout Reminder:**
- **Schedule:** `* * * * *` (every minute)
- **Command:** `spark notify:checkout-reminder`

Both commands use the same format:
```bash
/bin/bash -c "cd /home/u573967329/domains/sanvihr.fableadtech.com/public_html && [PHP_PATH] spark [COMMAND] >> /dev/null 2>&1"
```









