<?php 

    include '../../tools/php/initial.php';
    include "../tools/form.php";
    include $utils_dir."other/model.php";
    include $utils_dir."other/string.php";
    Check_auth([1]);
    if($_SERVER['REQUEST_METHOD']=="POST"){
        if(!empty($_POST['model'])){
            switch ($_POST['model']){
                case "member_type":
                   $mymodel = new model_access("member_type",array('member_type_title'),$conn);
                   $mymodel->insert(array(add_double_apostrophe($_POST['title'])));
                    Add_Message($_POST['model'].' Created Successfully ',1);
                   header('Location: index.php?model='.$_POST['model'].'');
                   exit;
                  break;
                case "member":
                   $mymodel = new model_access("member",array('member_username','member_name','member_phone_number','member_email','member_pass','member_birth_date','member_profile_url','member_type','member_verified','member_email_verified'),$conn);
                   if(empty($_POST['phone'])){$_POST['phone']="NULL";}else{
                     $_POST['phone']=add_double_apostrophe($_POST['phone']);
                   }
                   if(empty($_POST['birthday'])){$_POST['birthday']="NULL";}else{
                    $_POST['birthday']='STR_TO_DATE("'. $_POST['birthday'].'","%Y-%m-%d")';
                   }
                   if(empty($_POST['verified'])){$_POST['verified']='0';}else{$_POST['verified']='1';}
                   if(empty($_POST['email_verified'])){$_POST['email_verified']='0';}else{$_POST['email_verified']='1';}

                   $mymodel->insert(array(
                     add_double_apostrophe($_POST['username']),
                     add_double_apostrophe($_POST['name']),
                     $_POST['phone'],
                     add_double_apostrophe($_POST['email']),
                     add_double_apostrophe($_POST['pass']),
                     $_POST['birthday'],
                     add_double_apostrophe('profile/admin/'.$_FILES['profile']['name']),
                     $_POST['member_type'],
                     $_POST['verified'],
                     $_POST['email_verified'],
                    ));
                    upload_file($media_dir.'profile/admin/','profile');
                    Add_Message($_POST['model'].' Created Successfully ',1);
                   header('Location: index.php?model='.$_POST['model'].'');
                   exit;
                  break;
                case "language":
                    $mymodel = new model_access("language",array('language_title'),$conn);
                    $mymodel->insert(array(add_double_apostrophe($_POST['title'])));
                    Add_Message($_POST['model'].' Created Successfully ',1);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "material":
                    $mymodel = new model_access("material",array('material_title'),$conn);
                    $mymodel->insert(array(add_double_apostrophe($_POST['title'])));
                    Add_Message($_POST['model'].' Created Successfully ',1);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "sub_material":
                    $mymodel = new model_access("sub_material",array('sub_material_title','sub_material_material'),$conn);
                    $mymodel->insert(array(add_double_apostrophe($_POST['title']),$_POST['material']));     
                    Add_Message($_POST['model'].' Created Successfully ',1);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "course_type":
                    $mymodel = new model_access("course_type",array('course_type_title'),$conn);
                    $mymodel->insert(array(add_double_apostrophe($_POST['title'])));
                    Add_Message($_POST['model'].' Created Successfully ',1);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "course":
                    $frame =2;
                  break;
                case "keyword":
                    $mymodel = new model_access("keyword",array('keyword_word'),$conn);
                    $mymodel->insert(array(add_double_apostrophe($_POST['word'])));
                    Add_Message($_POST['model'].' Created Successfully ',1);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "university":
                    $mymodel = new model_access("university",array('university_name','university_profile_url'),$conn);
                    upload_file($media_dir.'universities/','profile');
                    Add_Message($_POST['name'],0);
                    Add_Message($_POST['name'],0);
                    Add_Message($_POST['name'],0);
                    $mymodel->insert(array(add_double_apostrophe($_POST['name']),'"universities/'.$_FILES['profile']['name'].'"'));
                    Add_Message($_POST['model'].' Created Successfully ',1);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "university_material":
                    $mymodel = new model_access("university_material",array('university_material_title','university_material_code','university_material_university','university_material_sub_material'),$conn);
                    $mymodel->insert(array(
                      add_double_apostrophe($_POST['title']),
                      add_double_apostrophe($_POST['code']),
                      $_POST['university'],
                      $_POST['sub_material']
                      )
                      );
                    Add_Message($_POST['model'].' Created Successfully ',1);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                    break;
              }
        }
    }
    if($_SERVER['REQUEST_METHOD']=="GET"){
        if(!empty($_GET['model'])){
            $myform = new form("method = 'post' action ='create.php' class='mx-auto my-4' style='width:600px' enctype= multipart/form-data" );
            $myform->add_hidden('model',$_GET['model']);
            switch ($_GET['model']){
                case "member":
                  $myform->add_text('username',null,null,"Username");     
                  $myform->add_text('name',null,null,"Name");     
                  $myform->add_text('phone',null,null,"Phone Number");     
                  $myform->add_text('email',null,null,"Email");     
                  $myform->add_text('pass',null,null,"Pass");     
                  $myform->add_date('birthday',null,null,"Birth Day");
                  $myform->add_file("profile","User Profile");
                  $myform->add_select_plus('member_type','member_type_id','member_type_title',$conn,null,null,"Type"); 
                  $myform->add_check('verified',null,null,"Member Verified");
                  $myform->add_check('email_verified',null,null,"Email Verified");
                    break;
                case "member_type":
                    $myform->add_text('title',null,null,"Member Type Title");
                  break;
                case "language":
                    $myform->add_text('title',null,null,"Language Title");
                  break;
                case "material":
                    $myform->add_text('title',null,null,"Material Title");
                  break;
                case "sub_material":
                    $myform->add_text('title',null,null,"Material Title");
                    $myform->add_select_plus('material','material_id','material_title',$conn,null,null,"Material");              
                  break;
                case "course_type":
                    $myform->add_text('title',null,null,"Course Type Title");
                  break;
                case "course":
                    $frame =2;
                  break;
                case "keyword":
                    $myform->add_text('word',null,null,"Keyword");
                  break;
                case "university":
                    $myform->add_text('name',null,null,"University Name");
                    $myform->add_file("profile","University Profile");
                  break;
                case "university_material":
                    $myform->add_text('title',null,null,"University Material Title");
                    $myform->add_text('code',null,null,"University Material Code");
                    $myform->add_select_plus('sub_material','sub_material_id','sub_material_title',$conn,null,null,"Sub Material"); //sub material
                    $myform->add_select_plus('university','university_id','university_name',$conn,null,null,"University"); // university 
                    break;
              }
        }
    }

?>
<html>
    <head>
        <title>Create</title>
        <?php include $base_dir."tools/php/essential/header.php"?>
    </head>
    <body >
    <?php include $base_dir.'tools/php/visual/admin_navigation.php'?>
        <div class="card">
        <?php
        echo '<h2 class="h2-responsive black-text m-5"> ADD '.$_GET['model'].' </h2> <hr class="bg-dark mx-3">';
         if (isset($frame) && $frame ==1){
            echo '<iframe class="w-100 mt-5" style="height:600px;"  src="../../account/signup.php"></iframe>';
        }
        else if (isset($frame) && $frame ==2){
            echo '<iframe class="w-100 mt-5" style="height:800px;"  src="../../mycourses/add.php"></iframe>';
        }
        else{
            $myform->touch();
        }?>      
        </div>
        <?php include $base_dir."tools/php/essential/footer.php"?>
        <?php include $base_dir."tools/php/visual/footer.php"?>
    </body>
    <script>
        $(document).ready(function() {
        $('.mdb-select').materialSelect();
      });
      $('input[type="checkbox"]').change(function(){
        this.value = (Number(this.checked));
      });
      $('.datepicker').pickadate({
          format: ' yyyy-mm-dd',
          formatSubmit: 'yyyy-mm-dd',
          today: '',
          
      });
    </script>
</html>