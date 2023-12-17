<?php 
session_start();

$site_name = "courses" ;

$dev_mode = true ;
$google_people = false ;

if ($dev_mode){// dev mode
    $media_url = "http://localhost/courses/media/";
    $media_dir = "C:/xampp/htdocs/courses/media/";

    $static_url = "http://localhost/courses/static/";

    $base_url = 'http://localhost/courses/';
    $base_dir = 'C:/xampp/htdocs/courses/';

    $utils_dir = 'C:/xampp/htdocs/courses/tools/php/utils/';
    $utils_url = 'http://localhost/courses/tools/php/utils/';

    $host = 'localhost';

    $username = "root";
    $password = "";

}else{ // public mode
    $media_url = ""; 
    $media_dir = "";

    $static_url = "";

    $base_url = "";
    $base_dir = "";

    $utils_dir = "";
    $utils_url = "";

    $host = '';

    $username = "";
    $password = "";
}
// 


// google

    // recaptcha
        $recaptcha_public="";
        $recaptcha_secret="";
        $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    // OAuth
        $oauth_client_id = "";
        $oauth_client_secret = "";
    // terms
        $google_terms='<spam>This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy"> Privacy Policy</a> and <a href="https://policies.google.com/terms">Terms of Service</a> apply.</spam>';

// Data Base

    $servername = "";
    $table_name = '';
    // Create connection
    $conn = new mysqli($servername, $username, $password, $table_name );
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

// Auth

    function Check_auth($permited_type){
        global $base_url;
        if(!isset($_SESSION['verified']) || empty($_SESSION['verified']) || $_SESSION['verified']!=true || !in_array($_SESSION['member_type'], $permited_type) ){
            $_SESSION['url'] = $_SERVER['REQUEST_URI'];
            header('Location: '.$GLOBALS['base_url'].'account/auth/login.php');
            exit;

        }
        if (!$_SESSION['member_email_verified']){
            Add_Message("Please Verify Your Email",2);
            if(strcmp(explode("@",$_SESSION['member_email'])[1],'gmail.com')==0){
                Add_Message('Note that since you have a gmail account ,you can verify your email by just signing with google <a class="mx-2 btn btn-white btn-rounded btn-sm px-3" href='.$base_url.'account/auth/google.php>
                <i class="fab fa-2x fa-google"></i> 
              </a>',0);

            }

        }
    }

// Messages

    if(!isset($_SESSION['message'])){
        $_SESSION['message']=[];
    }

    function Add_Message($message,$level){// 0 -->info , 1 --> success , 2 --> warning , 3 --> danger
            $_SESSION['message'][]=array($message,$level);
    }

    function Clear_Message(){
        $_SESSION['message']=[];
    }
    
    function Print_message(){// 0 --> info , 1 --> success , 2 --> warning , 3 --> danger
        if(sizeof($_SESSION['message']) >0){
            $_SESSION['message']=array_unique($_SESSION['message'], SORT_REGULAR);
            $counter=0;
            foreach($_SESSION['message'] as $message){
                $counter ++;
            if($message[1]==0){// info
                    echo "<div onclick=\" remove_alert('alert".$counter."');\"  id='alert".$counter."' class='w-100 my-0 alert bg-info text-center white-text h4'>".$message[0]."</div>";
            }
            if($message[1]==1){// success
                    echo "<div onclick=\" remove_alert('alert".$counter."');\"  id='alert".$counter."' class='w-100 my-0 alert bg-success text-center white-text h4'>".$message[0]."</div>";
            }
            if($message[1]==2){// warning
                    echo "<div onclick=\" remove_alert('alert".$counter."');\"  id='alert".$counter."' class='w-100 my-0 alert bg-warning text-center white-text h4'>".$message[0]."</div>";
            }
            if($message[1]==3){// danger  
                    echo "<div  onclick=\" remove_alert('alert".$counter."');\"  id='alert".$counter."' class='w-100 my-0 alert bg-danger text-center white-text h4'>".$message[0]."</div>";
            }
            }
            Clear_Message();
        }
    }

    function all_good(){
        if(sizeof($_SESSION['message']) === 0){
            return true ;
        }else{
            foreach($_SESSION['message'] as $message){
                if($message[1]==3){
                    return false;
                }
            }
        }
        return true ;
    }
?>