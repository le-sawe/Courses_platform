<?php 
// INCLUDE

include '../../tools/php/initial.php';
include '../../tools/php/parameters.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/files.php';
include $utils_dir.'other/string.php';
include $utils_dir.'course/course.php';
include $utils_dir.'account/member.php';

$courses = new course($conn);
// CHECK AUTH

Check_auth([1,2]);

if($_SERVER['REQUEST_METHOD']== "POST"){
    //Delete course
    if(isset($_POST['course'])&& !empty($_POST['course'])){
        // check is numerique
        if(!is_numeric($_POST['course']) || $_POST['course']<0){
           redirect_to("account/auth/logout.php");
        }    
        // check if the course deleted by the same person who create it
        if($conn->query("SELECT course_title FROM course WHERE course_id = ".$_POST['course']." AND course_made_by = ".$_SESSION['member_id']."")->num_rows !=1){
            redirect_to("account/auth/logout.php");
        }
        $the_course = new course($conn,$_POST['course']);
        $the_course->delete();

    }
}

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
    ),$conn);
$my_work = $course->get('
        INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
        INNER JOIN member ON course.course_made_by = member.member_id
        INNER JOIN language ON course.course_language = language.language_id
        LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
        WHERE course_made_by ='.$_SESSION["member_id"].'
        ORDER BY course.course_create_date  DESC;',
    );
$my_likes = $course->get('
    INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
    INNER JOIN member ON course.course_made_by = member.member_id
    INNER JOIN language ON course.course_language = language.language_id
    INNER JOIN liked_course ON liked_course.liked_course_course = course.course_id	
    LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
    WHERE liked_course.liked_course_member ='.$_SESSION["member_id"].'
    ORDER BY liked_course.liked_course_date DESC; ',
);
$my_saves = $course->get('
    INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
    INNER JOIN member ON course.course_made_by = member.member_id
    INNER JOIN language ON course.course_language = language.language_id
    LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
    INNER JOIN save_course ON course.course_id = save_course.save_course_summary_course
    WHERE save_course.save_course_member ='.$_SESSION["member_id"].' 
    ORDER BY save_course.save_course_date  DESC;');
?>




<html>
    <head>
        <title>My Account</title>
        <?php include "../../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../../tools/php/visual/navigation.php';?>
        <div class="card classic-tabs py-3  mx-auto my-4" style="max-width:1200px;">
            <div class="mx-4">
                <div class="row d-flex justify-content-end">
                    <a href="edit.php" class="black-text" style="border:0"><i class="fas fa-2x fa-edit"></i></a>
                </div>
                <div class="row mt-4">
                    <img src="<?php echo $_SESSION['member_profile_url'] ?>" class=" rounded-circle mx-auto" style="width:200px;height:200px;object-fit: cover;" alt="" srcset="">
                </div>
                <div class="row my-3">
                    <strong class="text-center h4 mx-auto">@<?php echo $_SESSION['member_username']?></strong>
                </div>
                <div class="row my-3">
                    <strong class="text-center h4 mx-auto">Likes : <?php echo $_SESSION['member_likes'] ?></strong>
                </div>
            </div>
            <ul class="nav lg-nav tabs-black d-flex justify-content-around rounded mx-2 p-0"  role="tablist">
                <li class="nav-item m-0">
                    <a class="nav-link waves-light active " id="courses-md" data-toggle="tab" href="#my_courses" role="tab" aria-controls="my_courses"
                    aria-selected="true">My Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link waves-light" id="likes-md" data-toggle="tab" href="#my_likes" role="tab" aria-controls="my_likes"
                    aria-selected="false">My Likes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link waves-light" id="saves-md" data-toggle="tab" href="#my_saves" role="tab" aria-controls="my_saves"
                    aria-selected="false">My Saves</a>
                </li>
            </ul>
            <div class="tab-content  pt-5" >
                <div class="tab-pane fade show active" id="my_courses" role="tabpanel" aria-labelledby="courses-md">
                        <div class="d-flex justify-content-between flex-wrap  " >
                            <?php $courses->print_courses($my_work,1) ?>
                        </div>  
                </div>
                <div class="tab-pane fade" id="my_likes" role="tabpanel" aria-labelledby="likes-md">
                        <div class="d-flex justify-content-between flex-wrap  " >
                            <?php $courses->print_courses($my_likes) ?>
                        </div>  
                </div>
                <div class="tab-pane fade" id="my_saves" role="tabpanel" aria-labelledby="saves-md">
                        <div class="d-flex justify-content-between flex-wrap  " >
                            <?php $courses->print_courses($my_saves) ?>
                        </div>  
                </div>
            </div>
        </div>
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>
    </body>
</html>