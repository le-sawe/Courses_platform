<?php 
function redirect_to($to){
    global $base_url;
    header('Location: '.$base_url.''.$to); 
    exit;
}
?>