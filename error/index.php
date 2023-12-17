<?php 
include "../tools/php/initial.php";
include $utils_dir."other/redirect.php";
$error_list = array(
    "500"=>"Internal Server Error",
    "400"=>"Bad Request",
    "403"=>"Forbiden",
    "404"=>"Page Not Found",
    );
if($_SERVER['REQUEST_METHOD'] != "GET"){
    redirect_to("index.php");
}
if(!isset($_GET['code']) or empty($_GET['code'])){
    redirect_to("index.php");
}
if(!isset($error_list[$_GET['code']])){
    redirect_to("index.php");
}
?>
<html>
<head>
    <title>Home</title>
    <?php include "../tools/php/essential/header.php"?>
</head>
<body class="teal lighten-5">
    <?php include '../tools/php/visual/navigation.php';?>
    <h1 class="mx-auto badge-danger p-4 text-center" style=" position: relative;top: 30%;" > ERROR <?php echo $_GET['code']; ?> 
        <br>
        <hr>
        <small><?php echo $error_list[$_GET['code']]; ?></small>
    </h1>
    <?php include "../tools/php/essential/footer.php"?>
    <?php include "../tools/php/visual/footer.php"?>
    
</body>
</html>
