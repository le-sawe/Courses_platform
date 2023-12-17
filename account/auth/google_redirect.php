<?php
// redirect to sign in with google 
include '../../tools/php/initial.php';
include $utils_dir.'account/google.php';
header('Location: '.$client->createAuthUrl());
?>
