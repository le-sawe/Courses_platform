<?php 
include '../../tools/php/initial.php';
include '../../tools/php/parameters.php';
include $utils_dir.'other/send_email.php';
include $utils_dir.'other/model.php';
include $utils_dir.'account/member.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/redirect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['member']) && !empty($_GET['member'])){// REQUEST TO RESET 
        $identifier =$_GET['member'];
        // Get member
            $member = new member($conn);
            $get_member=$member->get($identifier);

        if($get_member != false){ // if account exsist
            
            $member_id=$get_member['member_id'];
            $member_name = $get_member['member_name'];
            $member_email = $get_member['member_email'];

            // check if we have already sended a code 
            // Get the email verification 
            $pass_verification = new model_access('pass_verification',array('*'),$conn);
            $get_pass_verification = $pass_verification->get("where pass_verification_member = ".$member_id."");

            if($get_pass_verification == false){
                //email verification
                $ver_code = generateRandomString()."*".$member_id."*".generateRandomString();
                $conn->query("INSERT INTO pass_verification (pass_verification_member,pass_verification_code)VALUES(".$member_id.",'".$ver_code."')");
            //send mail
                $subject ="ACCOUNT RECOVER";
                $content ="Hi ".$member_name.", <br>
                    To recover your account, Please  <a href='".$base_url."account/mamange/recover_pass.php?code=".$ver_code."'>click here </a> ,
                    in case the link dosent open : '".$base_url."account/mamange/recover_pass.php?code=".$ver_code."'
                ";
                send_mail($member_email,$member_name,$subject,$content); 
                Add_Message("To Recover you account, Please check your email inbox ",0);
                if(strcmp(explode("@",$member_email)[1],'gmail.com')==0){
                    Add_Message('Note that since you have a gmail account ,you can recover your account by just signing with google 
                    <a class="mx-2 btn btn-white btn-rounded btn-sm px-3" href='.$base_url.'account/auth/google_redirect.php?reset_pass=1>
                    <i class="fab fa-2x fa-google"></i> 
                  </a>',0);
    
                }
            }else{
                Add_Message("we've already send you a code pleas check your inbox ",0);
                Add_Message("in case you didnt find it pleas contact us ",2);
            }  
        }   
    }
    if(isset($_GET['code']) && !empty($_GET['code'])){
        $_SESSION['pass_rec_code']=$_GET['code'];
    } 
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['code']) && !empty($_POST['code']) and
       isset($_POST['pass']) && !empty($_POST['pass']) and
       isset($_POST['pass2']) && !empty($_POST['pass2'])){  // RESET THE PASSWORD
        $_SESSION['pass_rec_code']=$_POST['code']; //save the code
        // PASSWORD VALIDATION 
            //check if the two pass match (New , and Retype)
                if(!(strcmp($_POST['pass2'],$_POST['pass'])==0)){
                    Add_Message("The two password doesn t match",3);
                }
            // Validate Password
                if(!(strlen($_POST['pass']) >=$para_password_min_length && strlen($_POST['pass']) <=$para_password_max_length)){
                    Add_Message("The password length should be between ".$para_password_min_length." and ".$para_password_max_length." !",3);
                }
        //CODE VALIDATION
        if(!(strlen($_POST['code']) >20)){
            Add_Message("INVALID CODE",3);
        }
        if(all_good()){
            $member_id = extractString($_POST['code'],'*','*');
            // Get member
                $get_member = "SELECT member_name ,member_email FROM member WHERE member_id = ".$member_id.";";
                $get_member_result = $conn->query($get_member);
            if(!($get_member_result->num_rows > 0)){
                Add_Message("INVALID CODE ",3);
            }
            if(all_good()){
                while($row= $get_member_result->fetch_assoc()){
                    // GET SOME DETAIL
                        $member_name = $row['member_name'];
                        $member_email = $row['member_email'];
                }
                // Get the email verification 
                    $get_pass_verification = "SELECT * FROM pass_verification where pass_verification_code = '".$_POST['code']."';";
                    $get_pass_verification_result = $conn->query($get_pass_verification);
                    if(!($get_pass_verification_result->num_rows >0)){
                        Add_Message("INVALID CODE ",3);
                    }
                    if(all_good()){     
                        // UPDATE MEMBER --> VERIFIED
                            $update_member = "UPDATE member SET member_pass = '".password_hash($_POST['pass'], PASSWORD_DEFAULT)."' WHERE member_id = ".$member_id.";";
                            $conn->query($update_member);
                            Add_Message("PASSWORD CHANGED ",1);
                        
                        // DELETE THE VERIFICATION EMAIL
                            $delete_verification = "DELETE FROM pass_verification where pass_verification_code = '".$_POST['code']."';";
                            $conn->query($delete_verification);
                            unset($_SESSION['pass_rec_code']);
                        //send mail
                            $subject ="NEW PASSWORD  , LCOURSE";
                            $content ="Hi ".$member_name.", <br>
                                Your account was recovered successfully
                            ";
                            send_mail($member_email,$member_name,$subject,$content); 
                            redirect_to("index.php");
                    }                
            }
        }
    }else{
        Add_Message("EMPTY INPUT",3);
    }
}

?>
</html>
<head>
    <title>Recover Password</title>
    <?php include "../../tools/php/essential/header.php"?>

</head>
<body>
    <?php include '../../tools/php/visual/navigation.php'?>

    <form method="post" action='recover_pass.php'  class="card my-3 mx-auto p-3" style ="max-width:700px">
        <h2 class="h2-responsive black-text "> RECOVER PASSWORD</h2>
        <hr class='bg-dark'>
        <div class="form-row">
            <div class="col">
                <!-- RECOVER CODE -->
                <div class="md-form">
                    <input type="text" name="code" id="code" value="<?php if(isset($_SESSION['pass_rec_code'])){ echo $_SESSION['pass_rec_code'];} ?>" class="form-control">
                    <label for="code">RECOVER CODE</label>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="col">
                <!-- New password -->
                <div class="md-form">
                    <input type="password" id="new_pass" name="pass" class="form-control">
                    <label for="new_pass">New Password</label>
                </div>
                <small  class="form-text text-muted mb-4">At least 8 characters , at most 16 characters</small>
            </div>
            <div class="col">
                <!-- Retype password -->
                <div class="md-form">
                    <input type="password" id="retype_pass" name="pass2" class="form-control">
                    <label for="retype_pass">Retype Password</label>
                </div>
            </div>
        </div>
        <br>
        <br>
        <div class="form-row"><button class="btn btn-black btn-md rounded" type="submit">RECOVER</button></div>
    </form>
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>
</body>
</html>