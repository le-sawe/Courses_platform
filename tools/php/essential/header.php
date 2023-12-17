<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3XHWQSJJ0R"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3XHWQSJJ0R');
</script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4812959302242779"
     crossorigin="anonymous"></script>
<!-- icon -->
<link rel="icon" type="image/png" href="<?php echo $static_url ?>img/tenji_logo.jpg"/>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
<!-- Bootstrap core CSS -->
<link rel="stylesheet" href="<?php echo $static_url ;?>css/bootstrap.min.css">
<!-- Material Design Bootstrap -->
<link rel="stylesheet" href="<?php echo $static_url ;?>css/mdb.min.css">
<!-- ad block test !-->
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- google recaptcha -->
<script src="https://www.google.com/recaptcha/api.js"></script>


<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-CH35TN2SWX');
</script>

<!-- Custom styles -->
<style>
    * {
 font-size: 100%;
 font-family: poppins;
}
.bold_font{
    font-size: 100%;
 font-family: poppins_bold;
}
    @font-face {
  font-family: 'poppins';
  src: url('<?php echo $static_url ;?>font/poppins/Poppins-ExtraLight.ttf')  format('truetype'); /* Legacy iOS */
}
    @font-face {
  font-family: 'poppins_bold';
  src: url('<?php echo $static_url ;?>font/poppins/Poppins-SemiBold.ttf')  format('truetype'); /* Legacy iOS */
}
body {
  background-repeat: no-repeat;
  background-size: cover;
  background-image: url("<?php echo $static_url;?>img/bw.jpg");
}



footer {
  position: absolute;
  right: 0;
  bottom: 0;
  left: 0;

}
.grecaptcha-badge { 
    bottom:60px !important; 
} 
</style>

  <!-- SCRIPTS -->
  <!-- JQuery -->
  <script src="<?php echo $static_url ;?>js/jquery.min.js"></script>
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="<?php echo $static_url ;?>js/popper.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="<?php echo $static_url ;?>js/bootstrap.js"></script>
  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="<?php echo $static_url ;?>js/mdb.min.js"></script>
 <!-- Remove alert --> 
 <script >
   function remove_alert(id){
     $("#"+id).remove();
    }
    async function detectAdBlock() {
      let adBlockEnabled = false
      const googleAdUrl = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'
      try {
        await fetch(new Request(googleAdUrl)).catch(_ => adBlockEnabled = true)
      } catch (e) {
        adBlockEnabled = true
      } finally {
        console.log(`AdBlock Enabled: ${adBlockEnabled}`)
      }
    }
    detectAdBlock();
      (adsbygoogle = window.adsbygoogle || []).push({});
 </script>
