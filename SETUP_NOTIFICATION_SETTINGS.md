# Setup Notification Settings

## Step 1: Create/Update Database Table

You need to run the SQL file to create or update the `notification_settings` table.

### If you DON'T have the table yet:
Run the full SQL file: `db/notification_settings.sql`

### If you ALREADY have the table (from previous setup):
Run the update SQL file: `db/update_notification_settings.sql`

### Option A: Using phpMyAdmin
1. Login to phpMyAdmin
2. Select your database (`u573967329_sanvihr`)
3. Click on "SQL" tab
4. Copy and paste the contents of `db/notification_settings.sql`
5. Click "Go" to execute

### Option B: Using MySQL Command Line
```bash
mysql -u your_username -p your_database < db/notification_settings.sql
```

### Option C: Direct SQL Execution
Run this SQL in your database:

```sql
CREATE TABLE IF NOT EXISTS `notification_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = enabled, 0 = disabled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notification_settings` (`attendance_notifications_enabled`) VALUES (1);
```

## Step 2: Verify Setup

1. Go to **Settings → Push Notifications** in the admin panel
2. You should see the toggle switch
3. The default state is **Enabled** (notifications active)

## Troubleshooting

If you still see "Failed to load notification settings":
1. Check that the table was created successfully
2. Verify the table name matches exactly: `notification_settings`
3. Check database connection in `.env` file
4. Clear browser cache and reload the page

