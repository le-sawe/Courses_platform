<?php 
require_once '../../tools/vendor/autoload.php';
// Google oauth 
$client = new Google_Client();
$client->setClientId($oauth_client_id);
$client->setClientSecret($oauth_client_secret);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
$client->setRedirectUri($base_url."account/auth/proccess_google.php");

$client->addScope('profile');
$client->addScope('https://www.googleapis.com/auth/userinfo.email');
if($google_people){
    $client->addScope('https://www.googleapis.com/auth/user.birthday.read');
    $client->addScope('https://www.googleapis.com/auth/user.phonenumbers.read');
}

?>