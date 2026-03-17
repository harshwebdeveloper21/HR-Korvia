<?php
/**
 * Generate VAPID Keys for Web Push Notifications
 * 
 * Run this script to generate VAPID keys:
 * php generate-vapid-keys.php
 * 
 * Then add the keys to your .env file:
 * VAPID_PUBLIC_KEY=your_public_key_here
 * VAPID_PRIVATE_KEY=your_private_key_here
 * VAPID_SUBJECT=mailto:admin@yourdomain.com
 */

require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

try {
    $keys = VAPID::createVapidKeys();
    
    echo "\n";
    echo "========================================\n";
    echo "VAPID Keys Generated Successfully!\n";
    echo "========================================\n\n";
    echo "Public Key:\n";
    echo $keys['publicKey'] . "\n\n";
    echo "Private Key:\n";
    echo $keys['privateKey'] . "\n\n";
    echo "========================================\n";
    echo "Add these to your .env file:\n";
    echo "========================================\n";
    echo "VAPID_PUBLIC_KEY=" . $keys['publicKey'] . "\n";
    echo "VAPID_PRIVATE_KEY=" . $keys['privateKey'] . "\n";
    echo "VAPID_SUBJECT=mailto:admin@yourdomain.com\n";
    echo "\n";
    echo "IMPORTANT: Keep your private key secure and never share it!\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "Error generating VAPID keys: " . $e->getMessage() . "\n";
    exit(1);
}

