# Push Notification Setup Guide

## ✅ 100% FREE Solution - No Paid Services!

**This push notification system is completely FREE:**
- ✅ Uses **VAPID** (free, open standard)
- ✅ Uses browser's **native Push API** (free)
- ✅ **No third-party services** required
- ✅ **No subscriptions** or monthly fees
- ✅ **No per-notification charges**
- ✅ **Self-hosted** on your server

**You don't need Firebase, OneSignal, or any paid services!**

---

This guide will help you set up push notifications for mobile devices when employees check in or check out.

## Prerequisites

1. Your website must be served over HTTPS (required for push notifications)
2. The `push_subscriptions` table must exist in your database
3. The `minishlink/web-push` package is already installed

## Step 1: Generate VAPID Keys

VAPID keys are required for push notifications. Run the following command:

```bash
php generate-vapid-keys.php
```

This will generate a public key and private key. Copy these keys.

## Step 2: Configure Environment Variables

Add the VAPID keys to your `.env` file (or set them as environment variables):

```env
VAPID_PUBLIC_KEY=your_public_key_here
VAPID_PRIVATE_KEY=your_private_key_here
VAPID_SUBJECT=mailto:admin@yourdomain.com
```

**Important:** 
- Replace `your_public_key_here` and `your_private_key_here` with the keys generated in Step 1
- Replace `admin@yourdomain.com` with your actual admin email
- Keep your private key secure and never commit it to version control

## Step 3: Database Setup

Ensure your `push_subscriptions` table has the following structure:

```sql
CREATE TABLE `push_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `endpoint` text NOT NULL,
  `keys` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## Step 4: Test the Setup

1. **Login as Admin on Mobile Device:**
   - Open your website on a mobile browser (Chrome, Firefox, Safari)
   - Login as an admin user
   - The browser will automatically prompt for notification permission
   - Allow notifications

2. **Add to Home Screen (PWA):**
   - On mobile, use the browser menu to "Add to Home Screen"
   - This makes the app installable as a PWA

3. **Test Check-In/Check-Out:**
   - Have an employee check in or check out
   - The admin should receive a push notification on their mobile device

## How It Works

1. **When Admin Logs In:**
   - The service worker registers automatically
   - Push subscription is created and saved to database
   - Admin's device is now subscribed to notifications

2. **When Employee Checks In/Out:**
   - The system sends push notifications to all admin subscriptions
   - Admins receive notifications even if the app is closed

3. **Notification Content:**
   - Check-In: "Employee Name has checked in at [time]"
   - Check-Out: "Employee Name has checked out at [time] (Status: [status])"

## Troubleshooting

### Notifications Not Working?

1. **Check HTTPS:**
   - Push notifications only work over HTTPS
   - Ensure your site is using SSL certificate

2. **Check Browser Support:**
   - Chrome/Edge: Full support
   - Firefox: Full support
   - Safari (iOS): Requires iOS 16.4+ and proper PWA setup

3. **Check Service Worker:**
   - Open browser DevTools > Application > Service Workers
   - Ensure service worker is registered and active

4. **Check Console:**
   - Open browser DevTools > Console
   - Look for any errors related to push notifications

5. **Check VAPID Keys:**
   - Ensure VAPID keys are correctly set in .env
   - Keys must match between server and client

6. **Check Database:**
   - Verify subscriptions are being saved: `SELECT * FROM push_subscriptions`
   - Ensure admin user_id matches in subscriptions

### Service Worker Not Registering?

- Check that `/service-worker.js` is accessible
- Check browser console for errors
- Ensure the site is served over HTTPS

### Notifications Not Received?

- Check notification permissions in browser settings
- Verify admin is logged in and subscription exists in database
- Check server logs for push notification errors

## API Endpoints

- `GET /api/push/public-key` - Get VAPID public key
- `POST /api/push/subscribe` - Subscribe to push notifications
- `POST /api/push/unsubscribe` - Unsubscribe from push notifications

## Files Created/Modified

1. **Models:**
   - `app/Models/PushSubscriptionModel.php`

2. **Services:**
   - `app/Services/PushNotificationService.php`

3. **Controllers:**
   - `app/Controllers/api/PushNotificationController.php`
   - `app/Controllers/api/AttendanceController.php` (modified)

4. **Frontend:**
   - `public/service-worker.js`
   - `public/manifest.json`
   - `app/Views/dashboard/navbar.php` (modified)
   - `app/Views/dashboard/header_link.php` (modified)

5. **Routes:**
   - `app/Config/Routes.php` (modified)

## Security Notes

- VAPID private key should never be exposed to clients
- Only the public key is sent to the browser
- Subscriptions are tied to user accounts
- Invalid subscriptions are automatically cleaned up

## Support

If you encounter issues, check:
1. Server error logs: `writable/logs/`
2. Browser console for client-side errors
3. Service worker status in DevTools
4. Database subscription records

