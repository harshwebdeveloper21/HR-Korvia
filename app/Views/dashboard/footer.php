<footer class="footer">
    <div class="container-fluid">
        <div class="row text-muted">
            <div class="col-6 text-start">
                <span class="text-center footer-font-size-sm">Copyright © 2026. All rights reserved.</span>
            </div>
            <!-- <div class="col-6 text-end">
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a class="text-dark" href="" target="_blank">Support</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-dark" href="" target="_blank">Help Center</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-dark" href="" target="_blank">Privacy</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-dark" href="" target="_blank">Terms</a>
                    </li>
                </ul>
            </div> -->
        </div>
    </div>

</footer>
<script>
  // Function to check the session token
// function checkSessionToken() {
//     // Retrieve the token from local storage
//     const token = localStorage.getItem('user_token');

//     // If token is missing, redirect to the login page
//     if (!token) {
//         window.location.href = '/login';
//     } else {
//         // Optionally, decode the token to check its expiration
//         const payload = JSON.parse(atob(token.split('.')[1]));
//         const currentTime = Math.floor(Date.now() / 1000);

//         // If the token has expired, redirect to the login page
//         if (payload.exp < currentTime) {
//             localStorage.removeItem('user_token');
//             window.location.href = '/login';
//         }
//     }
// }

// // Set an interval to check the token every 2 seconds (2000 milliseconds)
// setInterval(checkSessionToken, 2000);

</script>