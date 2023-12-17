<?php 
// require initial.php
// require model.php
// require string.php
// require sendemail.php


Class member{
    public $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function get($identifier){
        $member = new model_access('member',array('*'),$this->conn);
        // the identifier is email so get member by email
        if(check_email($identifier)){
            $identifier_type = "member_email";
            $identifier = add_double_apostrophe($identifier);
        }else if(is_numeric($identifier)){
            $identifier_type = "member_id";
        }else{// the identifier is username so get member by username
            $identifier_type = "member_username";
            $identifier = add_double_apostrophe($identifier);
        }
        $result =$member->get('WHERE '.$identifier_type.' = '.$identifier.' ;');
        if($result != false){
            return $result[0];
        }else{
            return false;
        }
    }

    public function signup($name,$email,$pass,$google=false,$picture=false){
        $email = strtolower($email); // email to lower case
        global $site_name ,$base_url;
        $username = $this->generate_username($email);
        
        // if sign in with google 
            if($google){
                $pass = "NULL";
                $verification =1;
            }else{
                $pass =add_double_apostrophe($pass);
                $verification =0;
                $picture = "profile/tenji_light.png";
            }

        // Creating account
            $member = new model_access('member',array('member_username','member_name','member_email','member_pass','member_type','member_email_verified','member_profile_url'),$this->conn);
            $member->insert(array(add_double_apostrophe($username),add_double_apostrophe($name),add_double_apostrophe($email),$pass,2, $verification,add_double_apostrophe($picture)));

        // Email Verification
            //if sign in with google then no need to verify email 
            if(!$google){
                // send email verification
                    // generate verification code 
                        $member_id = $this->conn->insert_id;
                        $verification_code = generateRandomString()."-".$member_id."-".generateRandomString();
                    // Create verification
                        $email_verification = new model_access('email_verification',array('email_verification_member','email_verification_code'),$this->conn);
                        $email_verification ->insert(array($member_id,add_double_apostrophe($verification_code)));
                    // Send Email 
                        $email_subject = $site_name;
                        $email_content = '
                            <html> 
                                <head> 
                                    <title>Welcome to Tenji</title> 
                                    <!-- Font Awesome -->
                                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
                                    <!-- Bootstrap core CSS -->
                                    <link rel="stylesheet" href="https://tenji.org/static/css/bootstrap.min.css">
                                    <!-- Material Design Bootstrap -->
                                    <link rel="stylesheet" href="https://tenji.org/static/css/mdb.min.css">
                            
                                    <!-- Custom styles -->
                                    <style>
                                            * {
                                        font-size: 100%;
                                        font-family: Roboto ;
                                        }
                                        table {
                                            background-repeat: no-repeat;
                                            background-position: center;
                                            background-size: cover;
                                            background-color:black;
                                            background-image: url("https://drive.google.com/uc?id=1CiSKYy6ASjNF_BKEUKkqyw2y20VIcZOY");
                                            font-family: poppins;
                                            color: white;
                                        }
                                    </style>
                                </head> 
                                <body class="text-white"> 
                                    <table cellspacing="0" style=" width: 100%;padding: 45px;"> 
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 40px;">Verify your email address </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;"><hr class="bg-light mx-auto" style="max-width:450px;"></th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;">TO CONFIRM YOUR EMAIL  </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th ><a style="font-size: 25px;color:white;" href="'.$base_url.'account/manage/email_verification.php?code='.$verification_code.'" class="btn btn-light btn-lg">CLICK HERE</a>  </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;"><hr class="bg-light mx-auto" style="max-width:450px;"></th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 85px;">tenji</th>
                                        </tr>   
                                        <tr class="d-flex justify-content-center w-100">  
                                            <th style="font-size: 25px;">
                                            <a href=" www.tenji.org" class="white-text" style="font-size: 25px;color:white;" > www.tenji.org</a>
                                            </th>
                                        </tr>  
                                    </table> 
                                </body> 
                            </html>'; 
                        send_mail($email,$name,$email_subject,$email_content); 
            }else{
                // Send email
                $email_subject = $site_name;
                $email_content = '
                            <html> 
                                <head> 
                                    <title>Welcome to Tenji</title> 
                                    <!-- Font Awesome -->
                                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
                                    <!-- Bootstrap core CSS -->
                                    <link rel="stylesheet" href="https://tenji.org/static/css/bootstrap.min.css">
                                    <!-- Material Design Bootstrap -->
                                    <link rel="stylesheet" href="https://tenji.org/static/css/mdb.min.css">
                            
                                    <!-- Custom styles -->
                                    <style>
                                            * {
                                        font-size: 100%;
                                        font-family: Roboto ;
                                        }
                                        table {
                                            background-repeat: no-repeat;
                                            background-position: center;
                                            background-size: cover;
                                            background-color:black;
                                            background-image: url("https://drive.google.com/uc?id=1CiSKYy6ASjNF_BKEUKkqyw2y20VIcZOY");
                                            font-family: poppins;
                                            color: white;
                                        }
                                    </style>
                                </head> 
                                <body class="text-white"> 
                                    <table cellspacing="0" style=" width: 100%;padding: 45px;"> 
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 40px;">Welcome to Tenji </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;"><hr class="bg-light mx-auto" style="max-width:450px;"></th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;">Check your profile </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th ><a style="font-size: 25px;color:white;" href="'.$base_url.'account/profile/" class="btn btn-light btn-lg">CLICK HERE</a>  </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;"><hr class="bg-light mx-auto" style="max-width:450px;"></th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 85px;">tenji</th>
                                        </tr>   
                                        <tr class="d-flex justify-content-center w-100">  
                                            <th style="font-size: 25px;">
                                            <a href=" www.tenji.org" class="white-text" style="font-size: 25px;color:white;" > www.tenji.org</a>
                                            </th>
                                        </tr>  
                                    </table> 
                                </body> 
                            </html>'; 
                send_mail($email,$name,$email_subject,$email_content); 
            }
        $this->login($email);
    }

    public function login($identifier){
        $the_member =$this->get($identifier);    
        if($the_member != false){ // we found the email so sign in
            $_SESSION['member_id']= $the_member['member_id'];
            $_SESSION['member_name']= $the_member['member_name'];
            $_SESSION['member_email']= $the_member['member_email'];
            $_SESSION['member_pass']= $the_member['member_pass'];
            $_SESSION['member_likes']= $the_member['member_likes'];
            $_SESSION['member_type']= $the_member['member_type'];
            $_SESSION['member_username']= $the_member['member_username'];
            $_SESSION['member_phone_number']= $the_member['member_phone_number'];
            // profile
            if(!startsWith($the_member['member_profile_url'],"http")){
                global $media_url;
                $the_member['member_profile_url'] = $media_url.''.$the_member['member_profile_url'];
            }
            $_SESSION['member_profile_url']= $the_member['member_profile_url'];
            $_SESSION['member_birth_date']= $the_member['member_birth_date'];
            $_SESSION['member_verified']= $the_member['member_verified'];
            $_SESSION['member_email_verified']= $the_member['member_email_verified'];
            $_SESSION['verified']= true;  
        }else{
            return false;
        }
        $log = new model_access("auth_logs",array('auth_logs_ip','auth_logs_member'),$this->conn);
        $log ->insert(array(add_double_apostrophe($_SERVER['REMOTE_ADDR']) ,$the_member['member_id']));
        return true;
    }

    public function auth($identifier,$pass){
        if(password_verify($pass, $this->get($identifier)['member_pass'])){
            $this->login($identifier);
            return true;
        }else{
            return false;
        }
    }

    private function generate_username($email){
        $username =explode("@",$email)[0].'_'.explode("@",$email)[1][0];
        if(strlen($username) > 15){
            $username = substr($username, -15);
        }
        $username = $username.''.generateRandomString(4);
        while($this->get($username) != false){
            $username = substr($username, -15);
            $username = $username.''.generateRandomString(4);
        }
        return $username;
    }

    public function verify_email($identifier,$code=false){
        global $site_name ,$base_url;
        $ok = false;
        // Check if its not verified 
        $the_member = $this->get($identifier);
        $email_verification = new model_access('email_verification',array('email_verification_code','email_verification_member'),$this->conn);
        if($the_member['member_email_verified'] == 0){
            if($code==false){// WITHOUT CODE
                $ok =true;        
            }else{// WITH CODE 
                $email_verification_object =$email_verification->get("WHERE email_verification_member =".$the_member['member_id']);
                if($email_verification_object != false){
                    if(strcmp($code ,$email_verification_object[0]['email_verification_code']) ==0){
                        $ok = true;
                    }
                }
            }
            if($ok){
                // update member situation 
                    $member = new model_access('member',array('member_email_verified'),$this->conn);
                    $member->update(array(1),"member_id =".$the_member['member_id'],array("member_email_verified"));
                // delete verification message 
                    $email_verification->delete("email_verification_member =".$the_member['member_id']);
                // Send email
                $email_subject = $site_name;
                $email_content = '
                            <html> 
                                <head> 
                                    <title>Welcome to Tenji</title> 
                                    <!-- Font Awesome -->
                                    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
                                    <!-- Bootstrap core CSS -->
                                    <link rel="stylesheet" href="https://tenji.org/static/css/bootstrap.min.css">
                                    <!-- Material Design Bootstrap -->
                                    <link rel="stylesheet" href="https://tenji.org/static/css/mdb.min.css">
                            
                                    <!-- Custom styles -->
                                    <style>
                                            * {
                                        font-size: 100%;
                                        font-family: Roboto ;
                                        }
                                        table {
                                            background-repeat: no-repeat;
                                            background-position: center;
                                            background-size: cover;
                                            background-color:black;
                                            background-image: url("https://drive.google.com/uc?id=1CiSKYy6ASjNF_BKEUKkqyw2y20VIcZOY");
                                            font-family: poppins;
                                            color: white;
                                        }
                                    </style>
                                </head> 
                                <body class="text-white"> 
                                    <table cellspacing="0" style=" width: 100%;padding: 45px;"> 
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 40px;">Welcome to Tenji </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;"><hr class="bg-light mx-auto" style="max-width:450px;"></th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;">Check your profile </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th ><a style="font-size: 25px;color:white;" href="'.$base_url.'account/profile/" class="btn btn-light btn-lg">CLICK HERE</a>  </th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 25px;"><hr class="bg-light mx-auto" style="max-width:450px;"></th>
                                        </tr>  
                                        <tr class="d-flex justify-content-center w-100"> 
                                            <th style="font-size: 85px;">tenji</th>
                                        </tr>   
                                        <tr class="d-flex justify-content-center w-100">  
                                            <th style="font-size: 25px;">
                                            <a href=" www.tenji.org" class="white-text" style="font-size: 25px;color:white;" > www.tenji.org</a>
                                            </th>
                                        </tr>  
                                    </table> 
                                </body> 
                            </html>'; 
                send_mail($the_member['member_email'],$the_member['member_name'],$email_subject,$email_content); 
                $this->login($identifier);
                return true;

            }else{}
        }
    }

    public function refresh_stat($identifier){
        // get the member id 
        $the_member_id = $this->get($identifier)['member_id'];
        // for now we gona refresh only the member likes

        // we will reliy on the course likes satat ( we will not gona view all the courses maded by the member joined by the likes , no , we gona just get the likes stat from the course )
        // so for better result we have to make course->refresh_stat then this performe this function 

        $courses = new model_access("course",array("SUM(course_likes)"),$this->conn);
        $result = $courses->get("WHERE course_made_by = ".$the_member_id);
        if ($result !=false){
            $result = $result[0]["SUM(course_likes)"];
            // update the member stat 
            $the_member = new model_access("member",array(''),$this->conn);
            $the_member->update(array($result),"member_id =  ".$the_member_id,array("member_likes"));

        }
        else{return false;}
    }
    
}

?>