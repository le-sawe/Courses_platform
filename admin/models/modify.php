<?php 

    include '../../tools/php/initial.php';
    include "../tools/form.php";
    include $utils_dir."other/model.php";
    include $utils_dir."other/string.php";
    Check_auth([1]);
    if($_SERVER['REQUEST_METHOD']=="POST"){
        if(!empty($_POST['model'])){
            switch ($_POST['model']){
                case "member":
                  $mymodel = new model_access("member",
                    array('member_username','member_name','member_phone_number','member_email','member_pass','member_birth_date','member_profile_url','member_type','member_verified','member_email_verified'),
                    $conn);
                  $data=$mymodel->get(' WHERE member_id ='.$_POST['id'],null,null,null)[0];

                  if(empty($_POST['phone'])){$_POST['phone']="NULL";}else{
                    $_POST['phone']=add_double_apostrophe($_POST['phone']);
                  }
                  if(empty($_POST['birthday'])){$_POST['birthday']="NULL";}else{
                  $_POST['birthday']='STR_TO_DATE("'. $_POST['birthday'].'","%Y-%m-%d")';
                  }
                  if(empty($_FILES['profile']['name'])){
                    $file_syntax=add_double_apostrophe($data['member_profile_url']);
                  }else{
                    upload_file($media_dir.'profile/admin/','profile');
                    $file_syntax = add_double_apostrophe('profile/admin/'.$_FILES['profile']['name']);
                  }
                  if(empty($_POST['verified'])){$_POST['verified']='0';}else{$_POST['verified']='1';}
                  if(empty($_POST['email_verified'])){$_POST['email_verified']='0';}else{$_POST['email_verified']='1';}

                  $mymodel->update(array(
                    add_double_apostrophe($_POST['username']),
                    add_double_apostrophe($_POST['name']),
                    $_POST['phone'],
                    add_double_apostrophe($_POST['email']),
                    add_double_apostrophe($_POST['pass']),
                    $_POST['birthday'],
                    $file_syntax,
                    $_POST['member_type'],
                    $_POST['verified'],
                    $_POST['email_verified'],
                  ),
                  'member_id ='.$_POST['id'],
                  array('member_username','member_name','member_phone_number','member_email','member_pass','member_birth_date','member_profile_url','member_type','member_verified','member_email_verified'));
                  
                  Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                  exit;
                  break;
                case "member_type":
                    $mymodel = new model_access("member_type",array('member_type_title'),$conn);
                    $mymodel->update(array(add_double_apostrophe($_POST['title'])),'member_type_id =' .$_POST['id'],array('member_type_title'));
                    Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                    break;
                case "language":
                    $mymodel = new model_access("language",array('language_title'),$conn);
                    $mymodel->update(array(add_double_apostrophe($_POST['title'])),'language_id =' .$_POST['id'],array('language_title'));
                     Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                     header('Location: index.php?model='.$_POST['model'].'');
                     exit;
                     break;
                case "material":
                    $mymodel = new model_access("material",array('material_title'),$conn);
                    $mymodel->update(array(add_double_apostrophe($_POST['title'])),'material_id =' .$_POST['id'],array('material_title'));
                    Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "sub_material":
                    $mymodel = new model_access("sub_material",array('sub_material_title','sub_material_material'),$conn);
                    $mymodel->update(array(add_double_apostrophe($_POST['title']),$_POST['material']),'sub_material_id =' .$_POST['id'],array('sub_material_title','sub_material_material'));     
                    Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "course_type":
                    $mymodel = new model_access("course_type",array('course_type_title'),$conn);
                    $mymodel->update(array(add_double_apostrophe($_POST['title'])),'course_type_id =' .$_POST['id'],array('course_type_title'));
                    Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "course":
                    $frame =2;
                  break;
                case "keyword":
                    $mymodel = new model_access("keyword",array('keyword_word'),$conn);
                    $mymodel->update(array(add_double_apostrophe($_POST['word'])),'keyword_id =' .$_POST['id'],array('keyword_word'));
                    Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "university":
                    $mymodel = new model_access("university",array('university_name','university_profile_url'),$conn);
                    $data=$mymodel->get(' WHERE university_id ='.$_GET['id'],null,null,null)[0];
                    if(empty($_FILES['profile']['name'])){$file_syntax=add_double_apostrophe($data['university_profile_url']);}else{
                      upload_file($media_dir.'universities/','profile');
                      $file_syntax = add_double_apostrophe('profile/admin/'.$_FILES['profile']['name']);
                    }
                    $mymodel->update(array(add_double_apostrophe($_POST['name']),$file_syntax),'university_id =' .$_POST['id'],array('university_name','university_profile_url'));
                    Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                  break;
                case "university_material":
                    $mymodel = new model_access("university_material",array('university_material_title','university_material_code','university_material_university','university_material_sub_material'),$conn);
                    $mymodel->update(array(add_double_apostrophe($_POST['title']),add_double_apostrophe($_POST['code']),$_POST['sub_material'],$_POST['university']),'university_material_id =' .$_POST['id'],array('university_material_title','university_material_code','university_material_sub_material','university_material_university'));
                    Add_Message($_POST['model'].' With Id : '.$_POST['id'].' Modified',2);
                    header('Location: index.php?model='.$_POST['model'].'');
                    exit;
                    break;
              }
        }
    }
    if($_SERVER['REQUEST_METHOD']=="GET"){
        if(!empty($_GET['model'])){
            $myform = new form("method = 'post' action ='modify.php' class='m-5 p-3' style='' enctype= multipart/form-data" );
            $myform->add_hidden('model',$_GET['model']);
            $myform->add_hidden('id',$_GET['id']);
            
            switch ($_GET['model']){
                case "member":
                  $myobject = new model_access('member',array('member_username','member_name','member_phone_number','member_email','member_pass','member_birth_date','member_profile_url','member_type','member_verified','member_email_verified'),$conn);
                  $data=$myobject->get(' WHERE member_id ='.$_GET['id'],null,null,null)[0];
                  $myform->add_text('username',null,$data['member_username'],"Username");     
                  $myform->add_text('name',null,$data['member_name'],"Name");     
                  $myform->add_text('phone',null,$data['member_phone_number'],"Phone Number");     
                  $myform->add_text('email',null,$data['member_email'],"Email");     
                  $myform->add_text('pass',null,$data['member_pass'],"Pass");     
                  $myform->add_date('birthday',null,$data['member_birth_date'],"Birth Day");
                  $myform->add_html("<img src='".$media_url.$data['member_profile_url']."' class='rounded img-fluid ' style='width:400px;'>");
                  $myform->add_file("profile","User Profile");
                  $myform->add_select_plus('member_type','member_type_id','member_type_title',$conn,null,$data['member_type'],"Type"); 
                  $myform->add_check('verified',null,$data['member_verified'],"Member Verified");
                  $myform->add_check('email_verified',null,$data['member_email_verified'],"Email Verified");
                  break;
                case "member_type":
                    $myobject = new model_access('member_type',array('member_type_title'),$conn);
                    $initial_title=$myobject->get(' WHERE member_type_id ='.$_GET['id'],null,null,null)[0]['member_type_title'];
                    $myform->add_text('title',null,$initial_title,"Member Type Title");
                  break;
                case "language":
                    $myobject = new model_access('language',array('language_title'),$conn);
                    $initial_title=$myobject->get(' WHERE language_id ='.$_GET['id'],null,null,null)[0]['language_title'];
                    $myform->add_text('title',null, $initial_title,"Language Title");
                  break;
                case "material":
                    $myobject = new model_access('material',array('material_title'),$conn);
                    $initial_title=$myobject->get(' WHERE material_id ='.$_GET['id'],null,null,null)[0]['material_title'];
                    $myform->add_text('title',null,$initial_title,"Material Title");
                  break;
                case "sub_material":
                    $myobject = new model_access('sub_material',array('sub_material_title','sub_material_material'),$conn);
                    $title_material=$myobject->get(' WHERE sub_material_id ='.$_GET['id'],null,null,null)[0];
                    $myform->add_text('title',null,$title_material['sub_material_title'],"Material Title");
                    $myform->add_select_plus('material','material_id','material_title',$conn,null,$title_material['sub_material_material'],"Material");              
                  break;
                case "course_type":
                    $myobject = new model_access('course_type',array('course_type_title'),$conn);
                    $initial_title=$myobject->get(' WHERE course_type_id ='.$_GET['id'],null,null,null)[0]['course_type_title'];
                    $myform->add_text('title',null, $initial_title,"Course Type Title");
                  break;
                case "course":
                    Add_Message("WTF YOU CANT MODIFY A COURSE BRO GO AWAY",3);
                  break;
                case "keyword":
                    $myobject = new model_access('Keyword',array('keyword_word'),$conn);
                    $initial_title=$myobject->get(' WHERE keyword_id ='.$_GET['id'],null,null,null)[0]['keyword_word'];
                    $myform->add_text('word',null,$initial_title,"Keyword");
                  break;
                case "university":
                    $myobject = new model_access('university',array('university_name'),$conn);
                    $initial_title=$myobject->get(' WHERE university_id ='.$_GET['id'],null,null,null)[0]['university_name'];
                    $myform->add_text('name',null,$initial_title,"University Name");
                    $myform->add_file("profile","University Profile");
                    break;
                case "university_material":
                    $myobject = new model_access('university_material',array('university_material_title','university_material_code','university_material_sub_material','university_material_university'),$conn);
                    $data=$myobject->get(' WHERE university_material_id ='.$_GET['id'],null,null,null)[0];
                    $myform->add_text('title',null,$data['university_material_title'],"University Material Title");
                    $myform->add_text('code',null,$data['university_material_code'],"University Material Code");
                    $myform->add_select_plus('sub_material','sub_material_id','sub_material_title',$conn,null,$data['university_material_sub_material'],"Sub Material"); //sub material
                    $myform->add_select_plus('university','university_id','university_name',$conn,null,$data['university_material_university'],"University"); // university 
                    break;
              }
        }
    }

?>
<html>
    <head>
        <title>Modify</title>
        <?php include $base_dir."tools/php/essential/header.php"?>
    </head>
    <body >
    <?php include $base_dir.'tools/php/visual/admin_navigation.php'?>
        <div class="card">
        <?php
        echo '<h2 class="h2-responsive black-text m-5"> Modify '.$_GET['model'].' </h2> <hr class="bg-dark mx-3">';
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