// COMPLETE FIX: Unregister old service worker and register new one
// Copy and paste this ENTIRE script into console (after typing "allow pasting")

(async function() {
    console.log('🔧 ===== FIXING SERVICE WORKER CACHE =====');
    
    // Step 1: Unregister ALL service workers
    console.log('\n1️⃣ Unregistering all old service workers...');
    const registrations = await navigator.serviceWorker.getRegistrations();
    
    if (registrations.length === 0) {
        console.log('   ✅ No service workers to unregister');
    } else {
        for (let registration of registrations) {
            const unregistered = await registration.unregister();
            console.log('   ' + (unregistered ? '✅' : '❌') + ' Unregistered:', registration.scope);
        }
    }
    
    // Step 2: Clear all caches
    console.log('\n2️⃣ Clearing all caches...');
    const cacheNames = await caches.keys();
    for (let cacheName of cacheNames) {
        await caches.delete(cacheName);
        console.log('   ✅ Deleted cache:', cacheName);
    }
    
    // Step 3: Wait a moment
    console.log('\n3️⃣ Waiting for cleanup...');
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Step 4: Register new service worker
    console.log('\n4️⃣ Registering new service worker...');
    try {
        const registration = await navigator.serviceWorker.register('/service-worker.js', {
            scope: '/'
        });
        console.log('   ✅ Service worker registered!');
        
        // Wait for it to be ready
        await navigator.serviceWorker.ready;
        console.log('   ✅ Service worker is ready!');
        
        // Force update
        if (registration.update) {
            await registration.update();
            console.log('   ✅ Service worker updated!');
        }
        
        console.log('\n✅✅✅ SERVICE WORKER FIXED!');
        console.log('📝 Next: Close this tab and open a NEW tab, then run ENABLE_DESKTOP_NOTIFICATIONS.js');
        console.log('💡 Or: Hard refresh this page (Ctrl+Shift+R) and run ENABLE_DESKTOP_NOTIFICATIONS.js');
        
    } catch (error) {
        console.error('   ❌ Failed to register:', error);
        console.error('   Error:', error.message);
    }
    
    console.log('\n🔧 ===== END =====');
})();




