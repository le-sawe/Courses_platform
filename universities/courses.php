<?php 

// get all data then search you idiot
//delte search.php
include '../tools/php/initial.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'course/course.php';


$courso = new course($conn);

$sugg_search=array();


if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    // get uni material

        $university_material = new model_access("university_material",array('*'),$conn);
        $university_material_data = $university_material->get('WHERE university_material_id= '.$_GET['material'].'')[0];
        $uni_material_title = $university_material_data['university_material_title'];
        $uni_material_code = $university_material_data['university_material_code'];

    // get uni courses
    $course = new model_access("course",array(
        'university_material.university_material_title',
        'university_material.university_material_code',
        'course_id',
        'course_title',
        'course_description',
        'language.language_title',
        'sub_material.sub_material_title',
        'member.member_username',
        'course_create_date',
        'course_views',
        'course_likes',
        'course_comments',
        ),$conn);
    $course_data = $course->get('
    INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
    INNER JOIN member ON course.course_made_by = member.member_id
    INNER JOIN language ON course.course_language = language.language_id
    LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
    WHERE course.course_uni_material = '.$_GET['material'].'
    ORDER BY course.course_score DESC , course.course_create_date  ;'
);


                
    }else{// if its not a get request
        redirect_to("universities/courses.php");
    }

?>


<html>
    <head>
        <title>Courses - <?php echo $uni_material_title ?> </title>
        <meta name="description" content="List of courses in <?php echo $uni_material_title ?> material ">
        <?php include "../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../tools/php/visual/navigation.php'?>    
        <div class=" p-md-3 mx-auto my-3" style="max-width:1200px">
        
            <div class="d-flex justify-content-between flex-wrap mx-auto rounded p-3 my-3 card" >
                <h2 class="h2 text-left ml-4"><?php echo $uni_material_title.'  #'.$uni_material_code.' ' ?> Courses</h2>
                <hr class="bg-dark mb-0">
                <a href="<?php echo $base_url?>course/mycourses/add.php?uni_material=<?php echo $_GET['material']; ?>" class="btn btn-black btn-block mt-2">Add Course Related to <?php echo $uni_material_title.'  #'.$uni_material_code.' ' ?></a>
                <?php         
                   $courso->print_courses($course_data);
                ?>
            </div>
        </div>
        <?php include "../tools/php/visual/footer.php"?>
        <?php include "../tools/php/essential/footer.php"?>
    </body>
</html>
