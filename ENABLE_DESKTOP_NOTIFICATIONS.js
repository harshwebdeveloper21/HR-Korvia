// IMPORTANT: Chrome blocks pasting by default!
// STEP 1: Open browser console (F12)
// STEP 2: Type this EXACTLY: allow pasting
// STEP 3: Press Enter
// STEP 4: NOW paste this ENTIRE script below
// STEP 5: Press Enter again

(async function() {
    console.log('🖥️ ===== DESKTOP NOTIFICATION SETUP =====');
    
    // Step 1: Check permission
    console.log('\n1️⃣ Checking notification permission...');
    let permission = Notification.permission;
    console.log('   Current permission:', permission);
    
    if (permission !== 'granted') {
        console.log('   ⚠️ Permission not granted, requesting...');
        permission = await Notification.requestPermission();
        console.log('   Result:', permission);
        
        if (permission !== 'granted') {
            console.error('   ❌ Permission denied. Please enable in browser settings.');
            console.log('   💡 Go to: Browser Settings → Site Settings → Notifications → Allow');
            return;
        }
    }
    console.log('   ✅ Permission granted!');
    
    // Step 2: Test native notification
    console.log('\n2️⃣ Testing native notification...');
    try {
        const testNotif = new Notification('Desktop Test', {
            body: 'If you see this, desktop notifications work!',
            icon: '/favicon.ico'
        });
        console.log('   ✅ Native notification works!');
        setTimeout(() => testNotif.close(), 3000);
    } catch (e) {
        console.error('   ❌ Native notification failed:', e);
        return;
    }
    
    // Step 3: Unregister old service workers and register new one
    console.log('\n3️⃣ Checking and updating service worker...');
    
    // First, unregister ALL existing service workers to clear cache
    const oldRegs = await navigator.serviceWorker.getRegistrations();
    if (oldRegs.length > 0) {
        console.log('   🗑️ Found ' + oldRegs.length + ' old service worker(s), unregistering...');
        for (let oldReg of oldRegs) {
            await oldReg.unregister();
            console.log('   ✅ Unregistered:', oldReg.scope);
        }
        console.log('   ⏳ Waiting 1 second for cleanup...');
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
    
    // Now register the new service worker with cache-busting
    console.log('   📝 Registering new service worker...');
    let reg;
    try {
        // Add cache-busting query parameter to force browser to load new version
        const cacheBuster = '?v=' + Date.now();
        reg = await navigator.serviceWorker.register('/service-worker.js' + cacheBuster, {
            scope: '/'
        });
        console.log('   ✅ Service worker registered!');
        console.log('   ⏳ Waiting for service worker to activate...');
        
        // Wait for the service worker to be ready (with timeout)
        console.log('   ⏳ Waiting for service worker to activate (max 5 seconds)...');
        try {
            // Wait for ready with 5 second timeout
            await Promise.race([
                navigator.serviceWorker.ready,
                new Promise((_, reject) => 
                    setTimeout(() => reject(new Error('Timeout')), 5000)
                )
            ]);
            console.log('   ✅ Service worker is ready!');
        } catch (e) {
            console.log('   ⚠️ Service worker still activating, but continuing...');
            console.log('   💡 This is OK - subscription can be created while activating');
        }
        
        // Check registration state
        if (reg.installing) {
            console.log('   📝 Service worker state: Installing (will activate soon)');
        } else if (reg.waiting) {
            console.log('   ⏳ Service worker state: Waiting (refresh page to activate)');
        } else if (reg.active) {
            console.log('   ✅ Service worker state: Active!');
        }
        
        // Give it a moment to finish installing
        console.log('   ⏳ Waiting 2 seconds for service worker to finish installing...');
        await new Promise(resolve => setTimeout(resolve, 2000));
    } catch (e) {
        console.error('   ❌ Failed to register service worker:', e);
        console.error('   Error details:', e.message);
        return;
    }
    
    // Step 4: Check existing subscription
    console.log('\n4️⃣ Checking existing subscription...');
    let sub = await reg.pushManager.getSubscription();
    
    if (!sub) {
        console.log('   ⚠️ No subscription found, creating one...');
        
        // Get public key
        const token = localStorage.getItem('token');
        if (!token) {
            console.error('   ❌ No token found! Please login again.');
            return;
        }
        
        console.log('   📡 Fetching public key...');
        const keyRes = await fetch('/api/push/public-key', {
            headers: {'Authorization': 'Bearer ' + token}
        });
        
        if (!keyRes.ok) {
            console.error('   ❌ Failed to get public key:', keyRes.status);
            return;
        }
        
        const keyData = await keyRes.json();
        console.log('   🔑 Public key response:', keyData.status);
        
        if (keyData.status !== 'success' || !keyData.publicKey) {
            console.error('   ❌ Invalid public key');
            return;
        }
        
        // Convert key
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        
        // Create subscription
        console.log('   📝 Creating push subscription...');
        try {
            sub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(keyData.publicKey)
            });
            console.log('   ✅ Subscription created!');
            console.log('   📋 Endpoint:', sub.endpoint.substring(0, 60) + '...');
        } catch (e) {
            console.error('   ❌ Failed to create subscription:', e);
            return;
        }
    } else {
        console.log('   ✅ Subscription already exists');
        console.log('   📋 Endpoint:', sub.endpoint.substring(0, 60) + '...');
    }
    
    // Step 5: Save to server
    console.log('\n5️⃣ Saving subscription to server...');
    const p256dh = btoa(String.fromCharCode(...new Uint8Array(sub.getKey('p256dh'))));
    const auth = btoa(String.fromCharCode(...new Uint8Array(sub.getKey('auth'))));
    
    const saveRes = await fetch('/api/push/subscribe', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
        body: JSON.stringify({
            endpoint: sub.endpoint,
            keys: { p256dh: p256dh, auth: auth }
        })
    });
    
    const saveData = await saveRes.json();
    console.log('   💾 Save response:', saveData.status);
    
    if (saveData.status === 'success') {
        console.log('   ✅✅✅ DESKTOP SUBSCRIPTION SAVED TO DATABASE!');
        console.log('   📝 Subscription ID:', saveData.subscription_id);
    } else {
        console.error('   ❌ Failed to save:', saveData.message);
        return;
    }
    
    // Step 6: Verify in database
    console.log('\n6️⃣ Verifying in database...');
    const verifyRes = await fetch('/api/push/status', {
        headers: {'Authorization': 'Bearer ' + localStorage.getItem('token')}
    });
    const verifyData = await verifyRes.json();
    console.log('   📊 Total subscriptions:', verifyData.count);
    console.log('   📋 Subscriptions:', verifyData.subscriptions);
    
    if (verifyData.count > 0) {
        console.log('   ✅✅✅ VERIFIED! Desktop subscription is in database!');
    } else {
        console.error('   ❌ NOT FOUND in database!');
    }
    
    // Step 7: Test push notification
    console.log('\n7️⃣ Testing push notification...');
    console.log('   💡 Run: testPushNotification()');
    console.log('   💡 Or have an employee check in/out');
    
    console.log('\n✅✅✅ DESKTOP SETUP COMPLETE!');
    console.log('📝 Next steps:');
    console.log('   1. Check database: SELECT * FROM push_subscriptions;');
    console.log('   2. Test: testPushNotification()');
    console.log('   3. Have employee check in/out');
    console.log('   4. Check desktop for notification');
    
    console.log('\n🖥️ ===== END =====');
})();

