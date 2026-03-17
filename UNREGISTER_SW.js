// Quick script to unregister all service workers
// Copy and paste this into console, then refresh page

(async function() {
    console.log('🗑️ Unregistering all service workers...');
    const registrations = await navigator.serviceWorker.getRegistrations();
    
    for (let registration of registrations) {
        await registration.unregister();
        console.log('✅ Unregistered:', registration.scope);
    }
    
    console.log('✅ All service workers unregistered!');
    console.log('🔄 Now refresh the page (Ctrl+Shift+R) and run ENABLE_DESKTOP_NOTIFICATIONS.js again');
})();




