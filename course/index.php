<?php 
/*
1. Admin Or Member Access
2.Page Goal :
    See List of Courses where every course have the following detail :
        View Count
        Member Name (the one who create the course)
        Course Sub Material 
        Course Title 
        Language
        Create date
    Filter the list by sub_material 
    Search for a Course

*/

// get all data then search you idiot
//delte search.php
include '../tools/php/initial.php';
include $utils_dir.'other/model.php';
include $utils_dir.'course/course.php';

$filter=false;
$search = false;
$search_keyword='';
$courses = new course($conn);




if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    //GET Material
        $materials = new model_access('material',array('*'),$conn);
        $all_material_array = $materials->get();
        
    //Get Sub Material
        $sub_materials = new model_access('sub_material',array('*'),$conn);
        $all_sub_material_array = $sub_materials->get();
                
    

    $course = new model_access("course",array(
        'university_material.university_material_title',
        'university_material.university_material_code',
        'course_id',
        'course_title',
        'course_description',
        'language.language_title',
        'sub_material.sub_material_title',
        'member.member_name',
        'member.member_username',
        'course_create_date',
        'course_views',
        'course_likes',
        'course_comments',
        'course_sub_material',
        ),$conn);
    
    //Filter
        $_SESSION['filter_field'] = false;
        if(isset($_GET['filter'])&& !empty($_GET['filter'])){
            if($_GET['filter'] !=-1 and is_numeric($_GET['filter'])){
                $_SESSION['filter_field'] = $_GET['filter'];
                $course_data = $course->get('
                    INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
                    INNER JOIN member ON course.course_made_by = member.member_id
                    INNER JOIN language ON course.course_language = language.language_id
                    LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
                    WHERE course_sub_material = '.$_GET['filter'].'
                    ORDER BY course.course_score DESC , course.course_create_date DESC ;'
                );
                
            }else{exit();}
        }
    //Search
        else if(isset($_GET['search'])&& !empty($_GET['search'])){
            $additional_search_syntax ='';
            $search_keyword =$conn -> real_escape_string($_GET['search']);
            // searching by keyword 
                // get the keywords related to the search
                    $keyword = new model_access('keyword',array('keyword_id'),$conn);
                    $keyword_search =$keyword->get("Where keyword_word ='".$search_keyword."';");
                // get  the keywords_course (link between keyword and course) 
                    if($keyword_search !=false){
                        $keyword_course = new model_access('keyword_course',array("keyword_course_course"),$conn);
                        $search_syntax = "WHERE ";
                        foreach($keyword_search as $row){
                            $search_syntax .=' keyword_course_keyword ='.$row['keyword_id'].' OR';
                        }
                        $search_syntax =substr($search_syntax, 0, -2);
                        $keyword_courses = $keyword_course->get($search_syntax);
                        
                        if($keyword_courses !=false){
                            $additional_search_syntax = "OR ";

                            foreach($keyword_courses as $row){
                                $additional_search_syntax .= ' course_id ='.$row["keyword_course_course"].' OR';
                                ;
                            }
                            $additional_search_syntax =substr($additional_search_syntax, 0, -2);                     
                        }
                    }
            // searching by courses title
            $course_data = $course->get('
                INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
                INNER JOIN member ON course.course_made_by = member.member_id
                INNER JOIN language ON course.course_language = language.language_id
                LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
                WHERE course_title LIKE "%'.$_GET['search'].'%" '.$additional_search_syntax.'
                ORDER BY course.course_score  , course.course_create_date DESC ;'
            );

        }else{
            //GET COURSES DETAIL (View Count , Member Name (the one who create the course) , Course Sub Material , Course Title , Language  , Create Date)
            $course_data = $course->get('
                INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
                INNER JOIN member ON course.course_made_by = member.member_id
                INNER JOIN language ON course.course_language = language.language_id
                LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
                ORDER BY course.course_score  , course.course_create_date DESC LIMIT 6;'
            );
        }
    

    if($_SESSION['filter_field'] != false){
        foreach($all_sub_material_array as $sub_material){ 
            if($_SESSION['filter_field'] == $sub_material['sub_material_id']){
                $_SESSION['filter_field'] =  $sub_material['sub_material_title'];
            }
        }
    }
}else{
    exit();
}


?>


<html>
    <head>
        <title>Courses - Discover <?php echo $_SESSION['filter_field'] ;?></title>
        <meta name="description" content="Discover your wished course , search by title and keywords  ... ">
        <?php include "../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../tools/php/visual/navigation.php'?>
        <div class="row p-md-3 w-100 m-0 mx-auto" style="max-width:2000px;">
            <div class="col-xl-4  p-3">
                <div class="card p-3" >
                    <div class="d-flex justify-content-between"><h3>Filter by materials</h3> </div>
                    <hr>
                    <?php 
                        if($all_material_array !=false){
                            foreach($all_material_array as $material){
                                echo'
                                <button class="btn btn-black" type="button" data-toggle="collapse" data-target="#material'.$material['material_id'].'collapse" aria-expanded="false" aria-controls="'.$material['material_title'].'collapse">
                                    '.$material['material_title'].'                              
                                </button>';
                                echo'<div class="collapse " id="material'.$material['material_id'].'collapse">
                                <div class="d-flex justify-content-center flex-wrap">';
                                if($all_sub_material_array !=false){
                                    foreach($all_sub_material_array as $sub_material){
                                        if($sub_material['sub_material_material']==$material['material_id']){
                                            echo '<form mehtod="get" action="index.php">
                                                    <input type = "hidden" name="filter" value="'.$sub_material['sub_material_id'].'" >
                                                    <button type="submit" class="btn btn-outline-black mx-3" >'.$sub_material['sub_material_title'].'</button>
                                                </form>' ;
                                        }
                                    }
                                }
                                echo'</div></div>';
                            }
                        }
                    ?>                    
                </div>
            </div>
            <div class= " col-xl-8 p-3">
                <div class=" card p-3 pr-0" >
                    <div class="d-flex justify-content-between"><h3>Discover</h3> </div>
                    <hr>
                    <div class="d-flex justify-content-around flex-wrap">
                        <?php         
                            $courses->print_courses($course_data);
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <?php include "../tools/php/visual/footer.php"?>
        <?php include "../tools/php/essential/footer.php"?>
        <script language="JavaScript" type="text/javascript" >
            <?php   

            // Get all sub material and stock them on javascript array
            $all_sub_material_array_to_js = json_encode($all_sub_material_array);
            echo "var all_sub_material_array = ". $all_sub_material_array_to_js . ";\n";
            ?>


        </script>

    </body>
</html>
