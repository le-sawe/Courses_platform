<?php

// include 
    include '../../tools/php/initial.php';
    include $utils_dir . 'other/model.php';
    include $utils_dir . 'other/string.php';
    include $utils_dir . 'other/send_email.php';
    include $utils_dir . 'account/member.php';
    include $utils_dir . 'other/redirect.php';
    require_once '../../tools/vendor/autoload.php';
    include $utils_dir . 'account/google.php';


// proccessing the code from google
if (isset($_GET['code']) && !empty($_GET['code'])) {
    // Get Token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    // Access
        $client->setAccessToken($token);

    // GOOGLE PEOPLE  NOT USED .
        if ($google_people) {

            $gpeople = new Google_Service_PeopleService($client);
            // parameter
            $optParams = [
                'personFields' => 'addresses,birthdays,genders,names,phoneNumbers',
            ];
            // get the user
            $user = $gpeople->people->get('people/me', $optParams);
            // get detail
            $birth_day_date = $user->getBirthdays()[0]['date'];
            $phone_number = $user->getPhoneNumbers()[0]["canonicalForm"];
            $birth_day_sql = 'STR_TO_DATE("' . $birth_day_date['year'] . '-' . $birth_day_date['month'] . '-' . $birth_day_date['day'] . '","%Y-%m-%d")';

            if (empty($phone_number)) {
                $phone_number = "NULL";
            } else {
                $phone_number = add_double_apostrophe($phone_number);
            }
        } else {
            $phone_number = "NULL";
            $birth_day_sql = "NULL";
        }
    //



    // Google Oauth2
        $gauth = new Google_Service_Oauth2($client);
        $google_info = $gauth->userinfo->get();

    // Get Name , email and picture from google
        $name = $google_info->name;
        $email = $google_info->email;
        $picture = explode('=', $google_info->picture)[0];// get the right size of picture   
    

    $member = new member($conn);
    if ($member->login($email)){// if login success
        Add_Message("Sign in with google success .", 1);
        // verify email if its not verified 
        $member->verify_email($email);
        // password recover proccess 
        $pass_ver = new model_access("pass_verification",array('pass_verification_code','pass_verification_member'),$conn);
        $pass_ver_code =$pass_ver->get("WHERE pass_verification_member =".$_SESSION['member_id']);
        if($pass_ver_code != false){// if the user has send a reset password request then when he sign in with google we will direct him to recover his password
            $pass_ver_code =$pass_ver_code[0]['pass_verification_code'];
            redirect_to("account/manage/recover_pass.php?code=".$pass_ver_code);
        }
      
        redirect_to("index.php");
    } else { // if login failed (he dont have an account )then signup
        $member->signup($name,$email,"null",true,$picture);
        Add_Message("Sign up with google .", 1);
        redirect_to("index.php");
    }
}else{
    redirect_to("index.php");
}
