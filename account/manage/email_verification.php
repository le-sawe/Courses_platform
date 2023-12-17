<?php 
include '../../tools/php/initial.php';
include $utils_dir."other/redirect.php";
include $utils_dir.'other/send_email.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/string.php';
include $utils_dir.'account/member.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['code']) && isset($_GET['code'])){
        $member_id = extractString($_GET['code'],'-','-');
        $member = new member($conn);
        if(strlen($_GET['code']) >20){
            if($member->verify_email($member_id,$_GET['code'])){
                Add_Message("your email has been successfully verified",0);   
                redirect_to("index.php");
            }
        }
    }
}
redirect_to("index.php");

?>