<?php 
/* 

1. Admin Or Member Access
2.Page Goal :
    Modify Course
*-/*/


include '../../tools/php/initial.php';
include '../../tools/php/parameters.php';
include $utils_dir.'course/mycourses/coursetools.php';
include $utils_dir.'course/keyword/keyword.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/files.php';
include $utils_dir.'other/model.php';


Check_auth([1,2]);
if (!$_SESSION['member_email_verified']){
    Add_Message("SOMTHING WRONG WITH YOUR EMAIL , PLEASE CONTACT US",3);
    redirect_to("home.php");
}
//Get types
$all_types = get_all_types($conn);

//Get Language
    $all_languages =get_all_languages($conn);

//Get Material
    $all_materials = get_all_materials($conn);

//Get Sub Material
    $all_sub_materials = get_all_sub_materials($conn);


$there_is_a_new_file = false;
$there_is_a_new_keyword = false;

// Proccess the Post request
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        // recaptcha 
        $recaptcha_response =$_POST['g-recaptcha-response'];
        $recaptcha=file_get_contents($recaptcha_url.'?secret='.$recaptcha_secret.'&response='.$recaptcha_response);
        $recaptcha=json_decode($recaptcha,true);
        
        // Add_Message("RECAPTCHA SUCCESS : ".$recaptcha['success'],0);
        // Add_Message("RECAPTCHA Action : ".$recaptcha['action'],0);
        if(!($recaptcha['success'] == 1) ){
            Add_Message("Modify COURSE FAILED ",3);
            redirect_to('course/');        
           
        }        


        // check if its uni mode 
            if(isset($_POST['uni_material']) and !empty($_POST['uni_material'])){$uni_mode=true ;}else{$uni_mode=false ;}
        
            // get uni material detail
            if($uni_mode){
                $uni_material = $_POST['uni_material'];
                $uni_material_title = get_uni_material_title_code_sub($_POST['uni_material'],$conn)[0];
                $uni_material_code = get_uni_material_title_code_sub($_POST['uni_material'],$conn)[1];
                $uni_material_sub_material = get_uni_material_title_code_sub($_POST['uni_material'],$conn)[2];
                $_POST['sub_material'] = $uni_material_sub_material;
            }
    // GET THE COURSE
        // COURSE ID SET AND NOT EMPTY
            if (!set_and_not_empty(array("course"),1)){
                redirect_to_logout();
            }
        //GET THE COURSE
            $course_result=$conn->query("SELECT  course_made_by FROM course  WHERE course_id =".$_POST['course']."; ");
            // if data not found
            if($course_result->num_rows !=1){
                redirect_to_list();
            }
            
        // COURSE IS MADE BY THE SAME PERSON WHO WANT TO  MODIFY IT
            while($row = $course_result->fetch_assoc()){
                if($row['course_made_by'] != $_SESSION['member_id']){
                    redirect_to_logout();
                }
            }
        // SAVE COURSE ID
            $course_id =$_POST['course'];

    // OLD FILES PROCCESSING
        // GET THE OLD FILES RELATED TO THE COURSE FROM DB
            $files_result=$conn->query("SELECT course_file_id FROM course_file  WHERE course_file_course = ".$course_id.";");
            // if data not found
            if($files_result->num_rows <1){
                redirect_to_list();
            }
        // GET THE OLD FILES STATUS AFTER MODIFYING 
            $file_to_be_deleted_index =array();   
            // old file number
            $old_file_remaining_number =0;    
            while($row = $files_result->fetch_assoc()){
                // VALIDATION 1 OLD FILE STATUS (SET AND NOT EMPTY)
                    if(empty($_POST['old_file'.$row['course_file_id']]) || !isset($_POST['old_file'.$row['course_file_id']])){                  
                        redirect_to_logout();
                    }
                //GET THE FILES TO BE DELETED
                    if($_POST['old_file'.$row['course_file_id']] == "true" ){
                        array_push($file_to_be_deleted_index,$row['course_file_id']);
                    }else{$old_file_remaining_number ++;}
            }
    // OLD KEYWORDS PROCCESSING
        //GET THE OLD KEYWORDS RELATED TO THE COURSE FROM DB
            $keywords_result=$conn->query("SELECT * FROM keyword_course WHERE keyword_course_course = ".$course_id.";");
            // if data not found
            if($keywords_result->num_rows <1){
                redirect_to_list();
                Add_Message("keyword result not found",3);
                redirect_to("course/mycourses/modify.php?course=".$course_id);

            }
        // GET THE OLD KEYWORDS STATUS AFTER MODIFYING 
            $keyword_course_relation_to_be_deleted =array();
            while($row = $keywords_result->fetch_assoc()){
                // VALIDATION 1 OLD KEYWORD
                    if(empty($_POST['old_keyword'.$row['keyword_course_id']]) || !isset($_POST['old_keyword'.$row['keyword_course_id']])){
                        redirect_to_logout();
                    }
                // GET THE KEYWROD TO BE DELETED
                    if($_POST['old_keyword'.$row['keyword_course_id']] =="true" ){
                        array_push($keyword_course_relation_to_be_deleted,$row['keyword_course_id']);
                    }
            }

    // Check if there a new files uploaded
        if(!empty($_FILES['file']['tmp_name'])){$new_files = true;}else{$new_files = false;}
        if(!empty($_POST['keyword'])){$new_keywords = true;}else{$new_keywords = false;}
    // Check if all files deleted 
        if($old_file_remaining_number == 0 && $new_files == false){
            Add_Message("At least you must have a one file related to the course",3);
            redirect_to("course/mycourses/modify.php?course=".$course_id);
        }
    // VALIDATION 1 (NOT EMPTY and SET)
        //FILE VALIDATION 1
            if($new_files){ // if there a new uploaded file
                if(sizeof($_FILES['file']['tmp_name']) + $old_file_remaining_number>10){
                    Add_Message("You Can't have more than 10 files related to the same course",3);
                    redirect_to("course/mycourses/modify.php?course=".$course_id);
                }
                foreach($_FILES['file']['tmp_name'] as $file_tmp_name){
                    if(!isset($file_tmp_name) || empty($file_tmp_name)){
                        Add_Message("INPUT FILE EMPTY !",3);
                        redirect_to("course/mycourses/modify.php?course=".$course_id);
                        break;
                    }
                }
            }
        // KEYWORDS VALIDATION 1
        if($new_keywords){
            // make all the keyword to lower case
            $_POST['keyword']= array_map('strtolower', $_POST['keyword']);
            // Remove the Dubplicated keyword
            $_POST['keyword']=array_unique($_POST['keyword'], SORT_REGULAR);
            foreach($_POST['keyword'] as $keyword){
                if(!isset($keyword) || empty($keyword)){
                    Add_Message('INPUT KEYWORD EMPTY !',3);
                    redirect_to("course/mycourses/modify.php?course=".$course_id);
                    break;
                }
            }
        }
        // COURSE DETAIL VALIDATION 1
            if(!set_and_not_empty(array('title','description','language','sub_material','type'),1)){
                echo "COURSE DETAIL";
                Add_Message("INPUT COURSE DETAIL EMPTY !",3);
                redirect_to("course/mycourses/modify.php?course=".$course_id);

            }
    // VALIDATION 2         
        if(all_good()){
            //FILE VALIDATION 2 (ITS UPLADEDD , SIZE , TYPE)
            if($new_files){
                for($i=0;$i<sizeof($_FILES['file']['tmp_name']);$i++){
                    // upload validation
                        if(is_uploaded_file($_FILES['file']['tmp_name'][$i])){ //if its uploaded
                            //size validation
                                if($_FILES['file']['size'][$i] >=$para_file_max_size){              
                                    Add_Message("INVALID FILE SIZE , THE FILE :".$$_FILES['file']['name'][$i]. " , HAVE SIZE :".$_FILES['file']['size'][$i]/1000000 ."Mb",3);
                                    redirect_to("course/mycourses/modify.php?course=".$course_id);
           
                                }
                            // type validation
                                if((!in_array($_FILES['file']['type'][$i], $para_file_type_acceptable))) {
                                    Add_Message("INACCEPTABLE FILE TYPE , FILE :".$_FILES['file']['name'][$i]. "",3);
                                    redirect_to("course/mycourses/modify.php?course=".$course_id);

                                    
                                }   
                        }else{// if its not uploaded
                            Add_Message("File not uploaded, file :".$_FILES['file']['name'][$i]. "",3);
                            redirect_to("course/mycourses/modify.php?course=".$course_id);

                        }
                }
            }
            //KEYWORD VALIDATION 2 (LENGTH)
            if($new_keywords){
                foreach($_POST['keyword'] as $keyword){
                    // length validatoin
                    if(strlen($keyword)>=$para_keyword_max_length || strlen($keyword)<=$para_keyword_min_length ){
                        Add_Message("Key words should be less than 20 word, keyword :".$keyword_index_list[$i]. "",3);
                        redirect_to("course/mycourses/modify.php?course=".$course_id);

                    }
                }
            }
            // DETAIL VALIDATION 2
                //TITLE (LENGTH)
                    if(strlen($_POST['title']) >$para_title_max_length || strlen($_POST['title']) <$para_title_min_length){
                        Add_Message("title words should be between ".$para_title_min_length." and ".$para_title_max_length." word",3);
                        redirect_to("course/mycourses/modify.php?course=".$course_id);
                    }
                    // Auto Backslashe
                    $_POST['title']=$conn -> real_escape_string($_POST['title']);
                //DESCRIPTION (LENGTH)
                    if(strlen($_POST['description']) >$para_description_max_length || strlen($_POST['description']) < $para_description_min_length){
                        Add_Message("Description words should be between ".$para_description_min_length." and ".$para_description_max_length." word",3);
                        redirect_to("course/mycourses/modify.php?course=".$course_id);
                    }
                    // Auto Backslashe
                    $_POST['description']=$conn -> real_escape_string($_POST['description']);
                //LANGUAGE (ISNUMERIC AND POSITIVE)
                    if(!is_numeric($_POST['language']) || $_POST['language'] <0){
                        redirect_to_logout();
                    }
                //TYPE (ISNUMERIC AND POSITIVE)
                    if(!is_numeric($_POST['type']) || $_POST['type'] <0){
                        redirect_to_logout();
                    }
                //SUB_MATERIAL (ISNUMBERIC AND POSITIVE)
                    if(!is_numeric($_POST['sub_material']) && $_POST['sub_material']>0 ){
                        redirect_to_logout();
                    }  
        } 
        // UPDATE DATA
            if(all_good()) {
                // UPDATE COURSE 
                    if(!$uni_mode){$uni_material ="NULL";$sub_material_edit_syntax ="course_sub_material =".$_POST['sub_material'].",";}else{$sub_material_edit_syntax="";}
                    $course_update = "UPDATE course SET course_title = '".$_POST['title']."', course_description ='".$_POST['description']."', ".$sub_material_edit_syntax." course_language =".$_POST['language'].", course_type =".$_POST['type'].", course_verified =false  WHERE course_id = '".$course_id."';";
                    if($conn->query($course_update)!==TRUE){
                        Add_Message("Error in Data (details) update you should not see this --> in detail :".$conn->error,3);
                        redirect_to("course/mycourses/modify.php?course=".$course_id);
                    }
                // FILES AND KEYWORDS PROCCESSING 
                    if(all_good()) {
                        // DELETE KEYWORD TO BE DELETED
                            if(sizeof($keyword_course_relation_to_be_deleted)>0){
                                $keyword_delete_sql = "DELETE FROM keyword_course WHERE ";
                                for($i=0;$i<sizeof($keyword_course_relation_to_be_deleted);$i++){
                                    $keyword_delete_sql .=" keyword_course_id = ".$keyword_course_relation_to_be_deleted[$i]." OR";
                                }
                                $keyword_delete_sql=rtrim($keyword_delete_sql,"OR");
                                $keyword_delete_sql =  $keyword_delete_sql." ;";
                                if($conn->query($keyword_delete_sql)!==TRUE){
                                    Add_Message("Error in Data (keyword) delete you should not see this --> in detail :".$conn->error."<br>the sql : ".$keyword_delete_sql,3);
                                    redirect_to("course/mycourses/modify.php?course=".$course_id);

                                }
                            }
                        // Add and link keywords
                            if(isset($_POST['keyword']) and !empty($_POST['keyword'])){
                                $keyword = new  course_keyword($conn);
                                $keyword->add_and_link($_POST['keyword'],$course_id);        
                            }
                        // Delete file
                            if(sizeof($file_to_be_deleted_index)>0){
                                $file_delete_sql = "DELETE FROM course_file WHERE ";
                                for($i=0;$i<sizeof($file_to_be_deleted_index);$i++){
                                    $file_delete_sql .=" course_file_id = ".$file_to_be_deleted_index[$i]." OR";
                                    // delete the file 
                                    $the_course_file = new model_access('course_file',array("course_file_url"),$conn);
                                    $the_course_file_url = $the_course_file ->get("where course_file_id = ".$file_to_be_deleted_index[$i])[0]["course_file_url"];
                                    delete_file($media_dir.''.$the_course_file_url);
                                }
                                $file_delete_sql=rtrim($file_delete_sql,"OR");
                                $file_delete_sql =  $file_delete_sql.";";
                                if($conn->query($file_delete_sql)!==TRUE){
                                    Add_Message("Error in Data (file) delete you should not see this --> in detail :".$conn->error,3);
                                    redirect_to("course/mycourses/modify.php?course=".$course_id);

                                }
                            }
                        //INSERT FILE
                        if(all_good() && $new_files) {          // diferrent que 0 pas -1 vrai ?            
                            $file_insert_sql = "INSERT INTO course_file (course_file_course,course_file_url) VALUES";
                            for($i=0;$i<sizeof($_FILES['file']['tmp_name']);$i++){
                                // CREATE PATH
                                    $path = $media_dir."course_files/".$course_id;
                                    if (!file_exists($path)) {mkdir ($path);}
                                    $path = $media_dir."course_files/".$course_id."/".$i;
                                    if (!file_exists($path)) {mkdir ($path);}
                                // CREATE THE FILE
                                    move_uploaded_file($_FILES['file']['tmp_name'][$i],$media_dir."course_files/".$course_id."/".$i."/".$_FILES['file']['name'][$i]);
                                // STOCK THE FILE URL IN DB 
                                    $file_insert_sql .="(".$course_id." ,'course_files/".$course_id."/".$i."/".$_FILES['file']['name'][$i]."' ) ,";
                            }
                            $file_insert_sql=rtrim($file_insert_sql,",");
                            $file_insert_sql =  $file_insert_sql.";";
                            if($conn->query($file_insert_sql)!==TRUE){
                                Add_Message("Error in Data (file) insert you should not see this --> in detail :".$conn->error,3);
                                redirect_to("course/mycourses/modify.php?course=".$course_id);

                            }
                        }
                        // if there is no error in all the proccess then redirect to list page
                        if(all_good()) {
                            Add_Message("course updated" ,1);
                            redirect_to_list();
                        }
                    }
            } 
    }
// Proccess the Get reuqest
    if ($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(isset($_GET['course'])&& !empty($_GET['course'])){

            

            // Course Detail
            $course_sql = "SELECT  * FROM course  WHERE course_id =".$_GET['course']."; ";
            $course_result=$conn->query($course_sql);

            // Files 

            $files = new model_access('course_file',array('course_file_url','course_file_id'),$conn);
            $get_files = $files->get("where course_file_course = ".$_GET['course']);
            //set file limite
            if($get_files == false){
                $file_limite= 10;
            }else{
                $file_limite =10 - sizeof($get_files);
            }
            //keywords

            $keyword_model  = new model_access("keyword_course",array('*'),$conn);
            $keywords_result = $keyword_model ->get("INNER JOIN keyword ON keyword_course.keyword_course_keyword = keyword.keyword_id WHERE keyword_course.keyword_course_course = ".$_GET['course'].";");
            
            if($keywords_result !=false){
                $keyword_count = sizeof($keywords_result);
            }else{$keyword_count = 0;}
            
            if($course_result->num_rows ==1){
                while($row = $course_result->fetch_assoc()){
                    // check if the user is the one who create this course
                    if($row['course_made_by'] == $_SESSION['member_id']){
                        $course_id =$row['course_id'];
                        $course_title =$row['course_title'];
                        $course_description =$row['course_description'];
                        $course_sub_material =$row['course_sub_material'];
                        $course_language =$row['course_language'];
                        $course_type =$row['course_type'];
                        $course_create_date =$row['course_create_date'];
                        $course_uni_material =$row['course_uni_material'];
                        // check if its uni mode
                            if(isset( $course_uni_material) and !empty( $course_uni_material)){$uni_mode=true ;}else{$uni_mode=false ;}
                        
                        // get uni material detail
                            if($uni_mode){
                                $uni_material =  $course_uni_material;
                                $uni_material_title = get_uni_material_title_code_sub( $course_uni_material,$conn)[0];
                                $uni_material_code = get_uni_material_title_code_sub( $course_uni_material,$conn)[1];
                                $uni_material_sub_material = get_uni_material_title_code_sub( $course_uni_material,$conn)[2];
                            }

                    }else{
                        redirect_to_list();
                    }
                }
            }else{
                redirect_to_list();
            }

        }else{
            redirect_to_list();
        }
    }

?>
<html>
    <head>
        <title>Modify Course</title>
        <?php include "../../tools/php/essential/header.php"?>

    </head>
    <body class="teal lighten-5">
        <?php include '../../tools/php/visual/navigation.php'?>
        <form method="post" action="modify.php?course=<?php echo $course_id ?>" id="modify" class="  card my-3 mx-auto p-4" style ="max-width:1200px;" enctype="multipart/form-data">
        <div class="d-flex justify-content-between ">
            <h2 class="h2-responsive text-black">
                <i class="fas fa-pen mr-2"></i> 
                Modify Course<?php if($uni_mode){echo 'in the material : <span class="text-info">'.$uni_material_title.' # '.$uni_material_code.'</span>';} ?> 
            </h2>


        </div>
        <hr class='bg-dark'>
        <div class="row d-flex-justify-content-between ">
            <div class='col-lg-6 '>
                <!-- title -->
                <div class="form-row">    
                    <div class="md-form ">
                        <i class="fas fa-angle-double-right prefix active"></i>
                        <input required type="text"  name="title" id="title" minlength=<?php echo $para_title_min_length?> maxlength=<?php echo $para_title_max_length?> value="<?php echo $course_title ; ?>" class="form-control ">
                        <label for="title" class="active">Title</label>
                    </div>
                </div>
                
                <!-- description -->
                <div class="form-row">
                    <div class="md-form mb-4 black-textarea active-black-textarea" style="width:100%">
                        <i class="fas fa-angle-double-right prefix active"></i>
                        <textarea required name="description" minlength=<?php echo $para_description_min_length?> maxlength=<?php echo $para_description_max_length?> id="description" class="md-textarea form-control"  rows="3"><?php echo $course_description ; ?></textarea>
                        <label for="description" class="active">Course Description</label>
                    </div>
                </div>

                <!-- language material sub_material -->
                <div class="form-row d-flex justify-content-around">
                    <div class=" ">
                        <select name="language" id="language" class=" mdb-select md-form colorful-select dropdown-dark " >
                            <?php
                               if(sizeof($all_languages)>0){
                                foreach($all_languages as $row){
                                        $selected ="";
                                        if ($course_language ==$row["language_id"]){$selected = "selected";}
                                        echo '<option value="'.$row["language_id"].'" '.$selected.'>'.$row["language_title"].'</option>';
                                    }
                                }
                            ?>
                        </select>
                        <label  for="language" class="mdb-main-label">Language</label>
                    </div>
                    <div class="" >
                        <select required id='select_type' name="type" class="mdb-select md-form colorful-select dropdown-dark " >
                            <?php 
                                 if(sizeof($all_types) >0){
                                    foreach($all_types as $row){
                                        if($row['course_type_id'] == $course_type){
                                            $selected="selected";}else{$selected="";}
                                        echo '<option '.$selected.' value="'.$row["course_type_id"].'">
                                        '.$row["course_type_title"].'
                                        </option>';
                                    }
                                }
                            ?>   
                        </select>
                        <label  for="select_type" class="mdb-main-label">Type</label>
                    </div>
                </div>
                <div class="form-row d-flex justify-content-around">
                    <div class="" >
                        <select <?php if($uni_mode){echo "disabled";}?> id='selectmaterial' class="mdb-select md-form colorful-select dropdown-dark " onchange="materialselect()">
                            <option value =-1 >ALL</option>
                            <?php 
                                if(sizeof($all_materials) >0){
                                    foreach($all_materials as $row){
                                        echo '<option  value="'.$row["material_id"].'">
                                        '.$row["material_title"].'
                                        </option>';
                                    }
                                }
                            ?>   
                        </select>
                        <label  for="selectmaterial" class="mdb-main-label">Material</label>
                    </div>
                    <div class="">
                        <select <?php if($uni_mode){echo "disabled";}?> required class="mdb-select md-form colorful-select dropdown-dark " name="sub_material" id="sub_material"></select>
                        <label for="sub_material" class="mdb-main-label">Sub Material</label>
                    </div>
                    <?php if($uni_mode){echo "<small class='text-info text-center'>Note that you can't change the material and sub material when you are adding a course related to a university material you can find more information here featurelink</small>";}?>    

                </div>
            </div>
            <div class='col-lg-6 '>
                <!-- Files -->
                <div class="d-flex justify-content-between flex-wrap">
                    <h2 class="h2-responsive mb-0 mt-3">
                        <i class="fas fa-file-upload mr-2"></i> PDF upload 
                    </h2>
                    <button type="Button" class="btn btn-outline-black  btn-rounded mx-auto" onclick="add_file_input()"> <i class="fas fa-plus"></i> ADD FIlE</button>
                </div>
                <hr class="bg-dark">
                <small class="badge-danger p-1 m-1  rounded" data-toggle="tooltip" title="In case of multiple pdf , your pdfs will not be rendered on the detail page  " >! WE HIGHLY RECOMMEND TO UPLOAD ONLY 1 PDF !</small>
                <div id='files_input_div' class="form-row w-100">
                
                <?php   
                        if($get_files != false){
                            foreach($get_files as $row){
                                echo '
                                <span class="text-danger mt-2" id ="old_file_status_'.$row["course_file_id"].'"></span><br>
                                <div class="row w-100 mt-2" id = "old_file_container_'.$row["course_file_id"].'">
                                    <div class="file-field col">
                                        <a href="'.$media_url."".$row["course_file_url"].'" target="_blank"  class="btn btn-black btn-sm mb-2 float-left">
                                            <span><i class="fa-2x fas fa-file mr-2" aria-hidden="true"></i></span>
                                        </a>
                                        <div class="file-path-wrapper">
                                                <input type="text" value="'.$row["course_file_url"].'" class="file-path validate">
                                        </div>
                                    </div>
                                    <button class="btn btn-black col btn-sm my-auto" style="max-height:35px;" type="button" id="old_file_button_'.$row["course_file_id"].'" onclick ="toggle_old_file('.$row["course_file_id"].')">Remove <i class="fas fa-trash ml-2"></i></button>
                                </div>
                                    <input type="hidden" name="old_file'.$row["course_file_id"].'" id="old_file'.$row["course_file_id"].'" value ="false" >

                                    ';
                            }
                        }  
                    ?>
                </div>
                <br>
                <br>
                <br>
                <!-- keywords -->
                <div class="d-flex justify-content-between flex-wrap">
                        <h2 class="h2-responsive mb-0 mt-3">
                            <i class="fas mr-2 mx-auto fa-hashtag"></i> Keywrods 
                        </h2>
                        <button type="Button" class="btn btn-outline-black  btn-rounded" onclick="add_keyword_input()"> 
                            <i class="fas fa-plus"></i> ADD KEYWORD
                        </button>
                    </div>
                <hr class="bg-dark">
                <div id='keywords_input_div'>
                <?php 
                    if($keywords_result !=false){
                        foreach($keywords_result as $row){
                            echo '
                            <span class ="text-danger" id ="old_keyword_status_'.$row["keyword_course_id"].'"></span><br>
                            <div class="row">
                                <div  id = "old_keyword_container_'.$row["keyword_course_id"].'" class=" col md-form">
                                    <i class="fas mr-2 fa-hashtag prefix active"></i>
                                    <input type="text" id="old_keyword_input_'.$row["keyword_course_id"].'" name="old_keyword_input_'.$row["keyword_course_id"].'" class="form-control-sm" value="'.$row["keyword_word"].'" readonly>
                                    <label for="old_keyword_input_'.$row["keyword_course_id"].'" class="active">#KEYWORD</label>
                                    <input type="hidden" name="old_keyword'.$row["keyword_course_id"].'" id="old_keyword'.$row["keyword_course_id"].'" value ="false" >
                                </div>
                                
                                <button class="btn btn-black col btn-sm my-auto" style="max-height:35px;" type = "button" id="old_keyword_button_'.$row["keyword_course_id"].'" onclick ="toggle_old_keyword('.$row["keyword_course_id"].')" >Remove <i class="fas fa-trash ml-2"></i></button>
                            </div>


                          
                                ';
                        }
                    } 
                ?>
                </div><br>
            </div>
        </div>
        <input name="keyword_count" id="keyword_count" type="hidden" value=-1>
        <input type="hidden" value="<?php echo $course_id ?>" name="course" ><br>
        <input name="uni_material" id="uni_material" type="hidden" value= <?php if ($uni_mode){echo $uni_material;} ?>>

        <button class="btn btn-outline-black g-recaptcha btn-block" type="submit" data-sitekey="<?php echo $recaptcha_public ?>" data-callback='onSubmit' data-action='submit'>Modify Course</button>            
        <datalist id="keywords_list">

</datalist>
    
            
        </form>
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>
        <?php include $utils_dir.'course/mycourses/courses_js.php'?>

        <script>            
        function onSubmit(token) {
                document.getElementById("modify").submit();
            }
            initial_setup(<?php echo $course_sub_material ; ?>);     
            // remove old file
            function toggle_old_file(element_index){
                    var old_file_status = document.getElementById("old_file_status_"+element_index);
                    var old_file_button = document.getElementById("old_file_button_"+element_index);
                    var old_file_input = document.getElementById("old_file"+element_index);
                    console.log(old_file_input.value);
                    if(old_file_input.value =="false"){
                        old_file_input.value ="true" ;
                        old_file_button.innerHTML ='Undo remove <i class="fas fa-trash-restore ml-2"></i>';
                        old_file_status.innerHTML ='<i class="fas fa-angle-double-down"></i> This file will be removed <i class="fas fa-angle-double-down"></i> ';
                    }
                    else if(old_file_input.value =="true"){
                        old_file_input.value ="false" ;
                        old_file_button.innerHTML ='Remove <i class="fas fa-trash ml-2" ></i>';
                        old_file_status.innerHTML =' ';
                    }
                }
            // remove old keyword
                function toggle_old_keyword(element_index){
                    var old_keyword_status = document.getElementById("old_keyword_status_"+element_index);
                    var old_keyword_button = document.getElementById("old_keyword_button_"+element_index);
                    var old_keyword_input = document.getElementById("old_keyword"+element_index);
                    if(old_keyword_input.value =="false"){
                        old_keyword_input.value ="true" ;
                        old_keyword_button.innerHTML ='Undo remove <i class="fas fa-trash-restore ml-2"></i>';
                        old_keyword_status.innerHTML =' <i class="fas fa-angle-double-down"></i> This keyword will be removed <i class="fas fa-angle-double-down"></i>';
                    }
                    else if(old_keyword_input.value =="true"){
                        old_keyword_input.value ="false" ;
                        old_keyword_button.innerHTML ='Remove <i class="fas fa-trash ml-2"></i>';
                        old_keyword_status.innerHTML =' ';
                    }
                }      
        </script>
        

    </body>
    
</html>