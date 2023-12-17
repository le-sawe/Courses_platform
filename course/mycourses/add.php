<?php 

/*
1. Admin Or Member Access
2.Page Goal :
    Add Course
3. Proccess 
    . CHECK UNI MODE 
    .GET HOW MANY FILES ARE ADDED
    .GET HOW MANY KEYWORD ARE ADDED
    .VALIDATION 1 (NOT EMPTY and SET)
        .FILE VALIDATION 1
        .KEYWORDS VALIDATION 1
        .COURSE DETAIL VALIDATION 1
    .VALIDATION 2 
        .FILE VALIDATION 2 (ITS UPLADEDD , SIZE , TYPE)
        .KEYWORD VALIDATION 2 (LENGTH)
        .DETAIL VALIDATION 2
            .TITLE (LENGTH)
            .DESCRIPTION (LENGTH)
            .LANGUAGE (ISNUMERIC AND POSITIVE)
            .SUB_MATERIAL (ISNUMBERIC AND POSITIVE)
    . VALIDATION 3 MAGIC 
        ---> json , csv 
        ---> proccesing 
        ---> true , false 
    .INSERT DATA
        .INSERT COURSE 
        .INSERT FILES AND KEYWORDS
            .GET COURSE ID
            .CREATE A VIEW
            .INSERT KEYWORDS (NEW ONLY)
            .LINKUP KEYWORD AND COURSE
            .INSERT FILE
            .CREATE PATH
            .CREATE THE FILE
            .STOCK THE FILE URL IN DB
    .REDIRECT
    */

include '../../tools/php/initial.php';
include '../../tools/php/parameters.php';
include $utils_dir.'course/mycourses/coursetools.php';
include $utils_dir.'course/course.php';
include $utils_dir.'course/keyword/keyword.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'other/string.php';

$file_limite =10;
$keyword_count =0 ;
// auth check
    Check_auth([1,2]);

// email verification check
    if (!$_SESSION['member_email_verified']){
        Add_Message("Please VERIFY YOUR EMAIL at first to add a course",3);
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

// Proccess the Get request 
    if ($_SERVER['REQUEST_METHOD'] === "GET"){
        // check if its uni mode
            if(isset($_GET['uni_material']) and !empty($_GET['uni_material'])){$uni_mode=true ;}else{$uni_mode=false ;}
        // get uni material detail
            if($uni_mode){
                $uni_material = $_GET['uni_material'];
                $uni_material_title = get_uni_material_title_code_sub($_GET['uni_material'],$conn)[0];
                $uni_material_code = get_uni_material_title_code_sub($_GET['uni_material'],$conn)[1];
                $uni_material_sub_material = get_uni_material_title_code_sub($_GET['uni_material'],$conn)[2];
            }
    }
    

// Proccess the Post request
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        // recaptcha 
        $recaptcha_response =$_POST['g-recaptcha-response'];
        $recaptcha=file_get_contents($recaptcha_url.'?secret='.$recaptcha_secret.'&response='.$recaptcha_response);
        $recaptcha=json_decode($recaptcha,true);
        
        if(!($recaptcha['success'] == 1) ){
            Add_Message("ADD COURSE FAILED ",3);
            redirect_to('mycourses/add.php');        
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

        
        // VALIDATION 1 (NOT EMPTY and SET)
            //FILE VALIDATION 1
                //Validate file number 
                if(sizeof($_FILES['file']['tmp_name'])>10){
                    Add_Message("You Can't add more than 10 files",3);
                    redirect_to('mycourses/add.php');
                }           
                foreach($_FILES['file']['tmp_name'] as $file_tmp_name){
                    if(!isset($file_tmp_name) || empty($file_tmp_name)){
                        Add_Message('INPUT FILE EMPTY !',3);
                        break;
                    }
                }
            // KEYWORDS VALIDATION 1
                // make all the keyword to lower case
                $_POST['keyword']= array_map('strtolower', $_POST['keyword']);
                // Remove the Dubplicated keyword
                $_POST['keyword']=array_unique($_POST['keyword'], SORT_REGULAR);
                // check if empty
                foreach($_POST['keyword'] as $keyword){
                    if(!isset($keyword) || empty($keyword)){
                        Add_Message('INPUT KEYWORD EMPTY !',3);
                        break;
                    }
                }

            // COURSE DETAIL VALIDATION 1
                if(!set_and_not_empty(array('title','description','language','sub_material','type'),1)){
                    echo "COURSE DETAIL";
                    Add_Message('INPUT COURSE DETAIL EMPTY !',3);
                }
            // save initail input
                $_SESSION['add_course_title']= $_POST['title'];
                $_SESSION['add_course_description']= $_POST['description'];
                $_SESSION['add_course_language']= $_POST['language'];
                $_SESSION['add_course_sub_material']= $_POST['sub_material'];
                $_SESSION['add_course_type']= $_POST['type'];
        // VALIDATION 2         
            if(all_good()){
                //FILE VALIDATION 2 (ITS UPLADEDD , SIZE , TYPE)
                    for($i=0;$i<sizeof($_FILES['file']['tmp_name']);$i++){
                        // upload validation
                            if(is_uploaded_file($_FILES['file']['tmp_name'][$i])){ //if its uploaded
                                //size validation
                                    if($_FILES['file']['size'][$i] >=$para_file_max_size){              
                                        Add_Message("INVALID FILE SIZE , THE FILE :".$_FILES['file']['name'][$i]. " , HAVE SIZE :".$_FILES['file']['size'][$i]/1000000 ."Mb",3);
            
                                    }
                                // type validation
                                    if((!in_array($_FILES['file']['type'][$i], $para_file_type_acceptable))) {
                                        Add_Message("INACCEPTABLE FILE TYPE , FILE :".$_FILES['file']['name'][$i]. "",3);

                                    }   
                            }else{// if its not uploaded
                                Add_Message("File not uploaded, file :".$_FILES['file']['name'][$i]. "",3);

                            }
                    }
                //KEYWORD VALIDATION 2 (LENGTH)
                foreach($_POST['keyword'] as $keyword){
                        // length validatoin
                        if(strlen($keyword)>=$para_keyword_max_length || strlen($keyword)<=$para_keyword_min_length ){
                            Add_Message("Key words should be between ".$para_keyword_min_length." and ".$para_keyword_max_length." word, keyword :".$keyword_index_list[$i]. "",3);

                        }
                    }
                // DETAIL VALIDATION 2
                    //TITLE (LENGTH)
                        if(strlen($_POST['title']) > $para_title_max_length || strlen($_POST['title']) <$para_title_min_length){
                            Add_Message("title words should be between ".$para_title_min_length." and ".$para_title_max_length." word",3);
                        }
                    // Auto Backslashe
                        $_POST['title']=$conn -> real_escape_string($_POST['title']);
                        
                    //DESCRIPTION (LENGTH)
                        if(strlen($_POST['description']) >$para_description_max_length || strlen($_POST['description']) < $para_description_min_length){
                            Add_Message("Description words should be between ".$para_description_min_length." and ".$para_description_max_length." word",3);
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
                    //UNI_MATERIAL (ISNUMBERIC AND POSITIVE)
                        if(!is_numeric($_POST['uni_material']) && $_POST['uni_material']>0 ){
                            redirect_to_logout();
                        }  
            } 
        // INSERT DATA
            if(all_good()) {
                // INSERT COURSE 
                    if(!$uni_mode){$uni_material ="NULL";}
                    $course = new model_access("course",array('course_title','course_description','course_sub_material','course_type','course_language','course_verified','course_made_by' ,'course_uni_material'),$conn);
                    if( $course->insert(array(
                        add_double_apostrophe($_POST['title']),
                        add_double_apostrophe($_POST['description']),
                        $_POST['sub_material'],
                        $_POST['type'],
                        $_POST['language'],
                        0,
                        $_SESSION['member_id'],
                        $uni_material)
                    )==false){
                        Add_Message("Error in Data (details) insert you should not see this --> in detail :".$conn->error,3);

                    }

                // FILES AND KEYWORDS PROCCESSING
                    if(all_good()) {
                        // GET COURSE ID
                            $course_id = $conn->insert_id;
                        // CREATE A VIEW
                            $view = new model_access("view",array('view_member','view_course'),$conn);
                            $view->insert(array(
                                add_double_apostrophe($_SESSION['member_id']),
                                $course_id
                            ));
                            // you should do some fixes here but for no
                            $the_courso = new course($conn,$course_id);
                            $the_courso->refresh_stat();
                            // $create_view_sql="INSERT INTO view (view_member,view_summary_course) VALUES(".$_SESSION['member_id'].",".$course_id.");";
                            // $conn->query($create_view_sql);
                            $view_id = $conn->insert_id;
                            $keyword = new  course_keyword($conn);
                            $keyword->add_and_link($_POST['keyword'],$course_id);
                        
                        //INSERT FILE
                            if(all_good()) {                      
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

                                    }
                                // if there is no error in all the proccess then redirect to list page
                                    if(all_good()) {

                                        // unset initail input
                                            unset($_SESSION['add_course_title']);
                                            unset($_SESSION['add_course_description']);
                                            unset($_SESSION['add_course_language']);
                                            unset($_SESSION['add_course_sub_material']);
                                            unset($_SESSION['add_course_type']);      

                                        Add_Message("The course is created ",1);
                                        if($uni_mode){
                                            Add_Message("This course now is on public and it 's related to the material :  ".$uni_material_code,0);
                                        }else{
                                            Add_Message("This course now is on public ",0);
                                        }
                                        redirect_to_list();
                                        
                                    }
                            }
                    }
            } 
    }
?>
<html>
    <head>
        <title>Add Course</title>
        <?php include "../../tools/php/essential/header.php"?>

    </head>
    <body class="teal lighten-5">
        <?php include '../../tools/php/visual/navigation.php'?>
        <form method="post" action="add.php" id ='add_course' class="  card my-3 mx-2 mx-auto p-4" style ="max-width:1200px;" enctype="multipart/form-data">
            <h2 class="h2-responsive black-text "><i class="fas fa-plus mr-2"></i> Add Course <?php if($uni_mode){echo ' to the material : <span class="text-info">'.$uni_material_title.' # '.$uni_material_code.'</span>';} ?> </h2>
            <hr class='bg-dark'>
            <?php if(!$uni_mode){echo "<small class='badge badge-info text-center'>if you want to add course under a university material please go to (universities->select your university (materials >> ) -> Select the material (coursess >> ) -> add course related to the material </small>";}?>
            <div class="row d-flex-justify-content-between">
                <div class='col-lg-5 '>
                    <!-- title -->
                    <div class="form-row">    
                        <div class="md-form ">
                            <i class="fas fa-angle-double-right prefix"></i>
                            <input type="text"  minlength=<?php echo $para_title_min_length?> maxlength=<?php echo $para_title_max_length?>  name="title" id="title"  class="form-control"
                            value="<?php if(isset($_SESSION['add_course_title'])){echo $_SESSION['add_course_title'];} ?>" required>
                            <label for="title">Title</label>
                        </div>
                    </div>
                    
                    <!-- description -->
                    <div class="form-row">
                        <div class="md-form mb-4 black-textarea active-black-textarea" style="width:100%">
                            <i class="fas fa-angle-double-right prefix"></i>
                            <textarea minlength=<?php echo $para_description_min_length?> maxlength=<?php echo $para_description_max_length?> name="description" id="description" class="md-textarea form-control" required rows="3"><?php if(isset($_SESSION['add_course_description'])){echo $_SESSION['add_course_description'];} ?></textarea>
                        <label for="description">Course Description</label>
                        </div>
                    </div>

                    <!-- language material  -->
                    <div class="form-row d-flex justify-content-around">
                        <div class=" ">
                            <select required name="language" id="language" class=" mdb-select md-form colorful-select dropdown-dark " >
                                <?php
                                    if(sizeof($all_languages)>0){
                                        foreach($all_languages as $row){
                                            if(isset($_SESSION['add_course_language']) and $_SESSION['add_course_language'] == $row["language_id"]){$selected="selected";}else{$selected="";}
                                            echo '<option '.$selected.' value="'.$row["language_id"].'">'.$row["language_title"].'</option>';
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
                                            if(isset($_SESSION['add_course_type']) and $_SESSION['add_course_type'] == $row["course_type_id"]){$selected="selected";}else{$selected="";}
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
                            <select <?php if($uni_mode){echo "disabled";} ?> id='selectmaterial' class="mdb-select md-form colorful-select dropdown-dark " onchange="materialselect()">
                                <option value =-1 >ALL</option>
                                <?php 
                                    if(sizeof($all_materials) >0){
                                        foreach($all_materials as $row){
                                            echo '<option value="'.$row["material_id"].'">
                                            '.$row["material_title"].'
                                            </option>';
                                        }
                                    }
                                    ?>   
                            </select>
                            <label  for="selectmaterial" class="mdb-main-label">Material</label>
                        </div>
                        <div class="">
                            <select <?php if($uni_mode){echo "disabled";} ?> required class="mdb-select md-form colorful-select dropdown-dark " name="sub_material" id="sub_material"></select>
                            <label for="sub_material" class="mdb-main-label">Sub Material</label>
                        </div>
                        <?php if($uni_mode){echo "<small class='text-info text-center'>Note that you can't change the material and sub material when you are adding a course related to a university material you can find more information here featurelink</small>";}?>    
                    </div>
                </div>
                <div class='col-lg-7 '>
                    <!-- Files -->
                    <div class="d-flex justify-content-between flex-wrap">
                        <h2 class="h2-responsive mb-0 mt-3">
                            <i class="fas fa-file-upload mr-2"></i> PDF upload 
                        </h2>
                        <button type="Button" class="btn btn-outline-black  btn-rounded mx-auto" onclick="add_file_input()"> <i class="fas fa-plus"></i> ADD FIlE</button>
                    </div>
                    <hr class="bg-dark">
                    <small class="badge-danger p-1 m-1  rounded" data-toggle="tooltip" title="In case of multiple pdf , your pdfs will not be rendered on the detail page  " >! WE HIGHLY RECOMMEND TO UPLOAD ONLY 1 PDF !</small>
                    <div id='files_input_div' class="form-row w-100 mt-2"></div>
                    <br>
                    <br>
                    <br>
                    <!-- keywords -->
                    <div class="d-flex justify-content-between flex-wrap">
                        <h2 class="h2-responsive mb-0 mt-3">
                            <i class="fas mr-2 mx-auto fa-hashtag"></i> Keywrods 
                        </h2>
                        <button type="Button"    class="btn btn-outline-light  btn-rounded" "> 
                                AUTO KEYWORD 
                        </button>
                        <button type="Button" class="btn btn-outline-black  btn-rounded" onclick="add_keyword_input()"> 
                            <i class="fas fa-plus"></i> ADD KEYWORD
                        </button>
                    </div>
                    <hr class="bg-dark">
                    <div id='keywords_input_div'></div>
                    <input name="keyword_count" id="keyword_count" type="hidden" value=0>
                    <br>
                    <br>
                    <br>
                </div>
                <input name="uni_material" id="uni_material" type="hidden" value= <?php if ($uni_mode){echo $uni_material;} ?>>
            </div>
            <button class="btn btn-outline-black g-recaptcha btn-block" type="submit" data-sitekey="<?php echo $recaptcha_public ?>" data-callback='onSubmit' data-action='submit'>ADD Course</button>            
            <datalist id="keywords_list">

            </datalist>

            
            
        </form>
        
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>

        <?php include $utils_dir.'course/mycourses/courses_js.php'?>
        <script>
            
        </script>
        <script>   
            initial_setup(<?php if($uni_mode){echo $uni_material_sub_material;}else if (isset($_SESSION['add_course_sub_material'])){echo $_SESSION['add_course_sub_material'];}else{echo "-1";}?>);
            add_file_input();
            add_keyword_input();     
            function onSubmit(token) {
                document.getElementById("add_course").submit();
            }          
        </script>
        

    </body>
    
</html>