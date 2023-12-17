<?php 
    include '../../tools/php/initial.php';
    include "../tools/form.php";
    include $utils_dir."other/model.php";
    Check_auth([1]);

    if($_SERVER["REQUEST_METHOD"]=="POST"){
      if(isset($_POST['model']) && !empty($_POST['model'] ) && isset($_POST['id']) && !empty($_POST['id']) &&isset($_POST['field']) && !empty($_POST['field'] )){
        
        $model = new model_access($_POST['model'],$_POST['field'],$conn);
        $model->delete($_POST['field']." = ".$_POST['id']);
        Add_Message("The ".$_POST['model']." With id : ".$_POST['id']." Deleted",3);
        header('Location: index.php?model='.$_POST['model'].'');
        exit;
        
      }
    }
    if($_SERVER["REQUEST_METHOD"]=="GET"){
      if(isset($_GET['model']) && !empty($_GET['model'] )){

      
        switch ($_GET['model']) {
          case "member":
              $mymodel = new model_access('member',array('member_id','member_name','member_username','member_phone_number','member_email','member_pass','member_birth_date','member_type','member_profile_url','member_verified','member_email_verified','member_create_date'),$conn);
              $data_to_view = $mymodel->get("INNER JOIN member_type ON member_type.member_type_id= member.member_type;" , array('member_type.member_type_title'), array('member_type'));  
              $fields_to_view=array('Id','Name','Username','Phone','Email','Pass','Birth Date','Profile URL','Verified','Email Status','Create Date','Type');      
              break;
          case "member_type":
              $mymodel = new model_access('member_type',array('member_type_id','member_type_title'),$conn);
              $data_to_view = $mymodel->get();
              $fields_to_view=array("Id","Title");
            break;
          case "language":
              $mymodel = new model_access('language',array('language_id','language_title'),$conn);
              $data_to_view = $mymodel->get();
              $fields_to_view=array("Id","Title");
            break;
          case "material":
              $mymodel = new model_access('material',array('material_id','material_title'),$conn);
              $data_to_view = $mymodel->get();
              $fields_to_view=array("Id","Title");
            break;
          case "sub_material":
              $mymodel = new model_access('sub_material',array('sub_material_id','sub_material_title','sub_material_material'),$conn);
              $data_to_view =$mymodel->get("INNER JOIN material ON material.material_id= sub_material.sub_material_material;" , array('material.material_title'),array('sub_material_material'));      
              $fields_to_view=array("Id","Title","Material");  
                      
            break;
          case "course_type":
              $mymodel = new model_access('course_type',array('course_type_id','course_type_title'),$conn);
              $data_to_view = $mymodel->get();      
              $fields_to_view=array("Id","Title");  
            break;
          case "course":
              $mymodel = new model_access('course',array('course_id','course_title','course_type','course_sub_material','course_language','course_made_by','course_create_date'),$conn);
              $data_to_view = $mymodel->get(
              "INNER JOIN language ON course.course_language = language.language_id
              INNER JOIN member ON course.course_made_by = member.member_id
              INNER JOIN course_type On course.course_type = course_type.course_type_id
              INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
              LEFT JOIN view ON course.course_id = view.view_course
              LEFT JOIN liked_course ON course.course_id = liked_course.liked_course_course
              LEFT JOIN comment_course ON course.course_id = comment_course.comment_course_course
              GROUP BY course.course_title ;" 
              , array('course_type.course_type_title','COUNT(DISTINCT liked_course.liked_course_id)','COUNT(DISTINCT comment_course.comment_course_id)','COUNT(DISTINCT view.view_id)','member.member_name','sub_material.sub_material_title' ,'language.language_title ')
              , array("course_type","course_sub_material","course_language","course_made_by"));
              $fields_to_view=array("Id","Title","Date","Type","Likes","Comments","View","Created by","Sub Material","language");  
            break;
          case "keyword":
              $mymodel = new model_access('keyword',array('keyword_id','keyword_word'),$conn);
              $data_to_view = $mymodel->get();      
              $fields_to_view=array("Id","Title");
            break;
          case "university":
              $mymodel = new model_access('university',array('university_id','university_name','university_profile_url'),$conn);
              $data_to_view = $mymodel->get();      
              $fields_to_view=array("Id","Name","Profile URL");
            break;
          case "university_material":
              $mymodel = new model_access('university_material',array('university_material_id','university_material_title','university_material_code','university_material_university','university_material_sub_material'),$conn);
              $data_to_view = $mymodel->get("
              INNER JOIN university ON university.university_id = university_material.university_material_university
              INNER JOIN sub_material ON sub_material.sub_material_id = university_material.university_material_sub_material
              ",array("university.university_name","sub_material.sub_material_title"),array("university_material_university","university_material_sub_material"));      
              $fields_to_view=array("Id","Title","Code","University","Sub Material");
              break;
            }

          }
        }$option_in_select =array('member','member_type','language','sub_material','material','course_type','course','keyword','university','university_material');
   
    
?>
<html>
    <head>
        <title>Models</title>
        <?php include $base_dir."tools/php/essential/header.php"?>
    </head>
    <body >
       <div class="card">
       <?php include $base_dir.'tools/php/visual/admin_navigation.php'?>
        <div class= 'd-flex justify-content-around'>

            <form action="index.php" class="form-row p-3 m-2 d-flex justify-content-center" method="GET">
              <select required name="model"  onchange="this.form.submit()" id="model" class=" mdb-select md-form colorful-select dropdown-dark " >
                <?php foreach($option_in_select as $option){
                  $selected="";
                  if(isset($_GET['model']) && !empty ($_GET['model']) && strcmp($_GET['model'],$option)==0){
                    $selected = "selected";
                  }
                  
                  echo '<option '.$selected.' value="'.$option.'" name="model">'.$option.'</option>';
                }
                ?>
                
              </select>
              
            </form>
            <?php if(isset($_GET['model']) && !empty ($_GET['model'])){
              echo'
              <form action="create.php" class="form-row p-3 m-2 d-flex justify-content-center" method="GET">
                <input type="hidden" name="model" value="'.$_GET['model'].'">
                <button class="btn btn-black rounded mx-4" type="submit">ADD</button>
              </form>
              ';
            }?>
            
        </div>
        <div class='m-4'>
            <?php 
            
            if(isset($mymodel)){table_result($data_to_view, $fields_to_view,$_GET['model']);}?>
        </div>
       </div>
        <?php include $base_dir."tools/php/essential/footer.php"?>
        <?php include $base_dir."tools/php/visual/footer.php"?>
    </body>
    <script>
        $(document).ready(function() {
        $('.mdb-select').materialSelect();
    });
    </script>
</html>
