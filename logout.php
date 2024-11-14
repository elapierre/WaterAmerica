<?php

session_start(); // Start the session to access session data
session_destroy(); // Destroy the session, effectively logging out the user by clearing all session data
header('Location: login.php'); // Redirect the user to the login page after logout
exit(); // Stop further script execution after the redirection

?>
