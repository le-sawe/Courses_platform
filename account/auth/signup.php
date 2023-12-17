<?php 

include '../../tools/php/initial.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'other/send_email.php';
include $utils_dir.'account/member.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/string.php';
include '../../tools/php/parameters.php';



// if its a post request
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // recaptcha 
    $recaptcha_response =$_POST['g-recaptcha-response'];
    $recaptcha=file_get_contents($recaptcha_url.'?secret='.$recaptcha_secret.'&response='.$recaptcha_response);
    $recaptcha=json_decode($recaptcha,true);
    if(!($recaptcha['success'] == 1)){
        Add_Message(" Google Recaptcha failed ",3);
        redirect_to("account/auth/signup.php");
    }

    // if there is no empty or not set field 
    if(isset($_POST['email']) && isset($_POST['name']) && isset($_POST['pass']) && isset($_POST['pass2']) && !(empty($_POST['email']) || empty($_POST['name']) || empty($_POST['pass']) || empty($_POST['pass2']))){
        //Save the input (bcs if the proccess failed the user dont have to retype the email or password)
            $_SESSION['form_name']=$_POST['name'];
            $_SESSION['form_email']=$_POST['email'];

        //Validate Name
            if(!(strlen($_POST['name']) >= $para_name_min_length && strlen($_POST['name']) <= $para_name_max_length)){// length 
                Add_Message("The name length should be between ".$para_name_min_length." and ".$para_name_max_length."",3);
            }  
        //Validate E-mail 
            if (!(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
                Add_Message("Invalid email format !",3);

            }else{ // if its a valid email 
                $member = new member($conn);
                $the_member =$member->get($_POST['email']);
                if($the_member==false){// if this email not assciated with an account then ...
                    //Validate Password
                        //check if the two pass match  (New and Retype)
                        if(strcmp($_POST['pass2'],$_POST['pass'])!=0){
                            Add_Message('The two password doesn t match !',3);
                        }
                        //Password Length
                        if(!(strlen($_POST['pass']) >=$para_password_min_length && strlen($_POST['pass']) <=$para_password_max_length)){
                            Add_Message('The password length should be between '.$para_password_min_length.' and '.$para_password_max_length.' !',3);
                        }
                        if(all_good()){//sign up
                            $member->signup($_POST['name'],$_POST['email'],password_hash($_POST['pass'], PASSWORD_DEFAULT));
                            redirect_to("index.php");

                        }
                }else{// email already exist
                    Add_Message('This email ('.$the_member['member_email'].') already exsit, Log in instead ?<a class="btn btn-sm  btn-black  mx-2" href="'.$base_url.'account/auth/login.php">Login</a>',3);
                }
            }
    }else{//if there is a empty or a not set filed
        Add_Message('There is an empty input !',3);

    }
}

 
?>
</html>
<head>
    <title>Signup</title>
    <?php include "../../tools/php/essential/header.php"?>

</head>
<body>
<?php include '../../tools/php/visual/navigation.php'?>
    <form method="post" action='signup.php' id="signup" class="  card my-3 mx-auto p-3" style ="max-width:700px">
    <h2 class="h2-responsive black-text"> Sign up <i class="fas fa-user-plus ml-1"></i></h2>
        <hr class='bg-dark'>

        <div class="form-row">
            <div class="col">
                <!-- First name -->
                <div class="md-form">
                    <input type="text" placeholder="your name" name="name" id="Name" value="<?php if(isset($_SESSION['form_name'])){echo $_SESSION['form_name'];} ?>" class="form-control">
                    <label for="Name">Name</label>
                </div>
            </div>
            <div class="col">
                <!-- Email -->
                <div class="md-form">
                    <input type="email" name="email" id="email" class="form-control" value="<?php if(isset($_SESSION['form_email'])){echo $_SESSION['form_email'];} ?>" >
                    <label for="email">Email</label>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col">
                <!-- New password -->
                <div class="md-form">
                    <input type="password" id="new_pass"  name="pass" class="form-control">
                    <label for="new_pass"> Password</label>
                </div>
            </div>
            <div class="col">
                <!-- Retype password -->
                <div class="md-form">
                    <input type="password" id="retype_pass" placeholder="Retype password" name="pass2" class="form-control">
                    <label for="retype_pass">Retype Password</label>
                </div>
            </div>
        </div>
        <div class="form-row"><button class="g-recaptcha btn btn-black btn-md rounded" data-sitekey="<?php echo $recaptcha_public ?>" data-callback='onSubmit' data-action='submit'>Sign up</button></div>
                <hr>
        <a class="mx-auto" href='<?php echo $base_url;?>account/auth/google_redirect.php'><img src="<?php echo $static_url ?>img/google_sign_in.png" class="img-fluid mx-auto" style="height:50px;" ></a>

        <small class="mt-3 ">By Signing up you agree to our <a href="" class="text-info"> privacy policy</a> and  <a href="" class="text-info"> terms of service</a>.</small>
    </form>
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>
        <script>
   function onSubmit(token) {
     document.getElementById("signup").submit();
   }
   
 </script>
</body>
</html>