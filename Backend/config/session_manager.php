<?php
// Set session cookie lifetime to 7 days
$lifetime = 604800;

// Set the garbage collection max lifetime to match (ensures server doesn't delete session
ini_set('session.gc_maxlifetime', $lifetime);

// Set session cookie parameters
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => '/',
    'domain' => '', // Default domain
    'secure' => false, // Set to true if using HTTPS
    'httponly' => true // Helps prevent XSS attacks stealing cookies
]);

// Start the session
session_start();

// If user is logged in, extend the cookie lifetime by another 7 days from now
if (isset($_SESSION['user_id'])) {
    setcookie(session_name(), session_id(), time() + $lifetime, '/');
}
?>