<?php 
// initial setup
session_start();

// remove all session variables
session_unset();

// destroy the session
session_destroy();

// remove cookie
setcookie ("member_email","",time() - 3600);
setcookie ("member_pass","",time() - 3600);

header('Location: login.php');
exit;
?>