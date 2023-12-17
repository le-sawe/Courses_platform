<?php 
include 'tools/php/initial.php';
include 'tools/php/parameters.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/model.php';
include $utils_dir.'account/member.php';

// for remember me 
 if(isset($_SESSION['verified']) and !empty($_SESSION['verified']) and $_SESSION['verified']==true ){
    redirect_to("home.php");
}else if(isset($_COOKIE['member_email']) && isset($_COOKIE['member_pass'])){
    $member = new member($conn);

    if(strcmp($_COOKIE['member_pass'], $member->get($_COOKIE['member_email'])['member_pass']) == 0){
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


?>
<html>
    <head>
        <title>Courses</title>
        <meta name="description" content="Share knowledge ">
        <?php include "tools/php/essential/header.php"?>
        <style>
            .grecaptcha-badge { 
                visibility: hidden;
            }
        </style>
    </head>
    <body class="" style="padding-bottom:40px;">
        <?php include 'tools/php/visual/navigation.php';?>
        <div style="max-width:1200px;" class="mb-3 d-flex justify-content-around mx-auto flex-wrap row" >
            <div class="col-md-4 mx-auto" style="margin-top:100px;max-width:500px;">
                <div class="card  light card-body " >
                    <h3>Connect</h3>
                        <hr>
                    <a class="mx-2 btn btn-black btn-rounded btn-sm px-3 h4" href="<?php echo $base_url; ?>account/auth/google_redirect.php">
                        <span class="h6">Connect with Google</span> <i class="fab fa-2x fa-google mx-2"></i> 
                    </a>
                   
                </div>
            </div>
            <div class="col-md-5 mx-auto" style="margin-top:100px;max-width:500px;" >
                <!-- Form -->
                <div class="card  light  " >
                    <form class="card-body mb-0" id="signup" method="post" action="account/auth/signup.php">
                        <h3>Sign up</h3>
                        <hr>
                        <div class="form-row">
                            <div class="col">
                                <!-- First name -->
                                <div class="md-form">
                                    <input type="text"  name="name" id="Name"  class="form-control">
                                    <label for="Name">Name</label>
                                </div>
                            </div>
                            <div class="col">
                                <!-- Email -->
                                <div class="md-form">
                                    <input type="email"  name="email" id="email" class="form-control"   >
                                    <label for="email">Email</label>
                                </div>
                            </div>
                        </div>
                        <br>
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
                                    <input type="password" id="retype_pass"  name="pass2" class="form-control">
                                    <label for="retype_pass">Retype Password</label>
                                </div>
                            </div>
                        </div>
                        <br>
                            <button class="g-recaptcha btn btn-outline-black btn-block btn-rounded" data-sitekey="<?php echo $recaptcha_public ?>" data-callback='onSubmit_signup' data-action='submit' type="submit">
                            Sign up </button>
                    </form>
                         <button type="button" class=" mx-2 btn btn-black btn-rounded btn-sm px-3 h4" data-toggle="modal" data-target="#loginmodel"><span class="h6">Login instead </span></button>
                        <small class="text-center my-1" style="font-size: x-small;">This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy">Privacy Policy</a> and <a href="https://policies.google.com/terms">Terms of Service</a> apply.</small>
                        
                </div>
                <!-- /.Form -->
            </div>
        </div>
             
        <!--Modal Form Login with Avatar Demo -->
        <div class="modal fade" id="loginmodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog cascading-modal modal-avatar modal-sm" role="document">
                <!-- Content -->
                <div class="modal-content light ">

                   
                    <!-- Body -->
                    <form method="post" action="account/auth/login.php" id="login" class="modal-body text-center mb-1">
                        
                        <div class="md-form ml-0 mr-0">
                            <input type="text" id="email_login" name="email" class="form-control ml-0">
                            <label for="email_login" class="ml-0">Your Email or Username</label>
                        </div>
                        <div class="md-form ml-0 mr-0">
                            <input type="password" id="pass_login" name="pass"" class="form-control ml-0">
                            <label for="pass_login" class="ml-0">Enter password</label>
                        </div>
                        <div class="md-form ml-0 mr-0 form-check">
                            <input type="checkbox" class="form-check-input ml-0" id="remember" name="remember">
                            <label class="form-check-label ml-0" for="remember">Remember me</label>
                        </div>
                        <div class="text-center mt-4">
                        
                            <button type="submit" class= "g-recaptcha btn btn-outline-black mt-1" data-sitekey="<?php echo $recaptcha_public ?>" data-callback='onSubmit_login' data-action='submit'>Login <i class="fas fa-sign-in-alt ml-1"></i></button>
                        </div>
                    </form>

                </div>
                    <!-- Content -->
            </div>
        </div>
    <?php include "tools/php/visual/footer.php"?>
    <?php include "tools/php/essential/footer.php"?>
    <script>
    new WOW().init();
    function onSubmit_login(token) {
     document.getElementById("login").submit();
   }
    function onSubmit_signup(token) {
     document.getElementById("signup").submit();
   }

  </script>
    </body>
</html>
