<?php 

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}   

function extractString($string, $start, $end) {
    $string = " ".$string;
    $ini = strpos($string, $start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function startsWith ($string, $startString){
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function add_double_apostrophe($string){
    return '"'.$string.'"';
}

function check_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }else{
        return true;
    }
 }

 function set_and_not_empty($array_of_index,$method){
    if($method=1){
        foreach($array_of_index as $value){
            if(!isset($_POST[$value])){return false;}
            if(empty($_POST[$value])){return false;}
        }
        return true;
    }
    if($method=2){
        foreach($array_of_index as $value){
            if(!isset($_GET[$value])){return false;}
            if(empty($_GET[$value])){return false;}
        }
        return true;
    }
}
?>