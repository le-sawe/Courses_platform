<?php 

include '../../tools/php/initial.php';
include $utils_dir.'account/member.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/redirect.php';
// remember me
if(isset($_COOKIE['member_email']) && isset($_COOKIE['member_pass'])){
    $member = new member($conn);

    if(strcmp($_COOKIE['member_pass'], $member->get($_COOKIE['member_email'])['member_pass']) == 0){// check pass
        $member->login($_COOKIE['member_email']);
        // redirect 
        if(isset($_SESSION['url'])) {                     
            header('Location: http://'.$host.''.$_SESSION['url']);
            exit;
        }else if ($_SESSION['member_type'] ==1){ // admin
            redirect_to("admin/");
        }
        elseif ($_SESSION['member_type'] ==2){ // member
            redirect_to("index.php");
        }
    }
}


// if its a post request
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // recaptcha 
        $recaptcha_response =$_POST['g-recaptcha-response'];
        $recaptcha=file_get_contents($recaptcha_url.'?secret='.$recaptcha_secret.'&response='.$recaptcha_response);
        $recaptcha=json_decode($recaptcha,true);        
        if(!($recaptcha['success'] == 1) ){
            Add_Message("Google Recaptcha failed ",3);
            redirect_to("account/auth/login.php"); 
        }
    

    // if there is no empty or not set field 
    if(isset($_POST['email'])  && isset($_POST['pass']) && !(empty($_POST['email']) || empty($_POST['pass']))){
        $member = new member($conn);
        // get the member
            $the_member = $member->get($_POST['email']);
            //fetch
            if($the_member == false){// Account Not Found
                Add_Message('There is no account with this username or email ,<a class="mx-2 black-text h5 btn btn-white btn-rounded  px-3" href='.$base_url.'account/auth/signup.php>
                Sign up 
            </a> ',3);
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // if its an email 
                    if(strcmp(explode("@",$_POST['email'])[1],'gmail.com')==0){ // check if its an gmail account 
                        Add_Message('Note that since you are trying to sign in with a gmail account ,you can sign up with google 
                        <a class="mx-2 btn btn-white btn-rounded btn-sm px-3" href='.$base_url.'account/auth/google_redirect.php>
                            <i class="fab fa-2x fa-google"></i> 
                        </a>',0);
                    }
                }
            }else{// Account Found
                    if($member->auth($_POST['email'],$_POST['pass'])){// if authentification success
                        // remember me
                        if(!empty($_POST["remember"])) {
                            // set cookies
                            setcookie ("member_email", $_SESSION['member_email'],time()+ (10 * 365 * 24 * 60 * 60));
                            setcookie ("member_pass",$_POST["pass"],time()+ (10 * 365 * 24 * 60 * 60));
                        }else{
                            if(isset($_COOKIE["member_email"]) || isset($_COOKIE["member_pass"])) {
                                setcookie ("member_email","");
                                setcookie ("member_pass","");
                            }
                        }
                        Add_Message("Sign in",1);
                        // redirect 
                        if(isset($_SESSION['url'])) {                     
                            header('Location: http://'.$host.''.$_SESSION['url']);
                            exit;
                        }
                        if ($_SESSION['member_type'] ==1){ // admin
                            redirect_to("admin/");
                        }
                        elseif ($_SESSION['member_type'] ==2){ // member
                            redirect_to("index.php");
                        }
                    }else{// if authentification failed
                        Add_Message("Hi ".$the_member['member_name'].", Wrong password !  <a class='btn btn-warning rounded btn-sm' href='".$base_url."account/manage/recover_pass.php?member=".$the_member['member_username']."'>RECOVER PASSWORD</a>",3);
                    }         
                }           
    }
    else{//if there is a empty or a not set field    
        Add_Message("empty field ",3);
    }
}
?>
</html>
<head>
    <title>Login</title>
    <?php include "../../tools/php/essential/header.php"?>

</head>
<body>
<?php include '../../tools/php/visual/navigation.php'?>

    <form method="post" id="login" action='login.php'  class="  card my-3 mx-auto p-3" style ="max-width:700px">
        <h2 class="h2-responsive black-text "> Log in <i class="fas fa-sign-in-alt ml-1"></i></h2>
        <hr class='bg-dark'>
        <div class="form-row">
            <div class="col">
                <!-- Email -->
                <div class="md-form">
                    <input type="text" id="email" class="form-control" value="<?php if(isset($_SESSION['form_email'])){echo $_SESSION['form_email'];} ?>" name="email">
                    <label for="email">Email or username</label>
                </div>
            </div>
            <div class="col">
                <!--  password -->
                <div class="md-form">
                    <input type="password" id="pass" name="pass" class="form-control">
                    <label for="pass"> Password</label>
                </div>
            </div>
        </div>
        <div class="form-row form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php if(isset($_COOKIE["member_email"])) { ?> checked <?php } ?>>
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <br>
        <br>
        <div class="form-row">
            <button class="g-recaptcha btn btn-black btn-md rounded" 
                data-sitekey="<?php echo $recaptcha_public ?>" 
                data-callback='onSubmit' 
                data-action='submit'>
                Log in
            </button>
        </div>
        
        <hr>
        <a class="mx-auto" href='<?php echo $base_url;?>account/auth/google_redirect.php'><img src="<?php echo $static_url ?>img/google_sign_in.png" class="img-fluid mx-auto" style="height:50px;" ></a>

    </form>
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>
        <script>
   function onSubmit(token) {
     document.getElementById("login").submit();
   }
 </script>
</body>
</html>