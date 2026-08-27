<footer class="footer" style="background: #E66136; color: white;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <span class="text-center footer-font-size-sm" style="color: white !important;">© 2026 Copyright - Fablead Developers Technolab</span>
            </div>
            <!-- <div class="col-6 text-end">
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <a class="text-white" href="" target="_blank">Support</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-white" href="" target="_blank">Help Center</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-white" href="" target="_blank">Privacy</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="text-white" href="" target="_blank">Terms</a>
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