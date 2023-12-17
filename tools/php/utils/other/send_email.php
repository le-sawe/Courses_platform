<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require $base_dir.'tools/vendor/phpmailer/phpmailer/src/Exception.php';
require $base_dir.'tools/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require $base_dir.'tools/vendor/phpmailer/phpmailer/src/SMTP.php';


function send_mail($destination,$name,$subject,$content){
  $mail = new PHPMailer();
  //$mail->IsSMTP(); to work on live server
  $mail->Mailer = "smtp";
  
  $mail->SMTPDebug  = 0;  
  $mail->SMTPAuth   = TRUE;
  $mail->SMTPSecure = "ssl";
  $mail->Port       = 465;
  $mail->Host       = "";
  $mail->Username   = "...";
  $mail->Password   = "...";
  
  $mail->IsHTML(true);
  $mail->AddAddress($destination, $name);
  $mail->SetFrom("... (email)", "... (title)");
  //$mail->AddReplyTo("reply-to-email@domain", "reply-to-name");
  $mail->AddCC($destination, $name);
  $mail->Subject = $subject;//"Test is Test Email sent via Gmail SMTP Server using PHP Mailer";
  //$content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";
  $mail->MsgHTML($content); 
  if(!$mail->Send()) {
    echo "Error while sending Email.";
    var_dump($mail);
  } 
}
?>
