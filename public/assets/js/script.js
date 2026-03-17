document.addEventListener("DOMContentLoaded", function() {
    // Hide loader after the page is fully loaded
    var loader = document.getElementById("loader");
    if (loader) {
        loader.style.display = "none";
    }
    
    // Show main content
    var content = document.getElementById("content");
    if (content) {
        content.style.display = "block";
    }
});
