<?php 
// INCLUDE

include '../tools/php/initial.php';
include '../tools/php/parameters.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/model.php';
include $utils_dir.'account/member.php';
include $utils_dir.'course/course.php';

$courso = new course($conn);
// CHECK AUTH

Check_auth([1,2]);
if(isset($_GET['member']) and !empty($_GET['member'])){
    $member = new member($conn);
    $the_member = $member->get($_GET['member']);
    if($the_member == false){
        redirect_to("index.php");
    }
}else{redirect_to("index.php");}

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
        WHERE course_made_by ='.$the_member["member_id"].'
        ORDER BY course.course_title  ;',
    );
    // picture
    if(!startsWith($the_member['member_profile_url'],"http")){
        $the_member['member_profile_url'] = $media_url.''.$the_member['member_profile_url'];
    }
?>




<html>
    <head>
        <title><?php echo $the_member['member_username'] ?></title>
        <?php include "../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../tools/php/visual/navigation.php';?>
        <div class="card classic-tabs py-3  mx-auto my-4" style="max-width:1200px;">
            <div class="mx-4">
                
                <div class="row mt-4">
                    <img src="<?php echo $the_member['member_profile_url'] ?>" class="fluid-img rounded-circle mx-auto" style="width:30%;max-width:200px;" alt="" srcset="">
                </div>
                <div class="row my-3">
                    <strong class="text-center h4 mx-auto">@<?php echo $the_member['member_username']?></strong>
                </div>
                <div class="row my-3">
                    <strong class="text-center h4 mx-auto">Likes : <?php echo $the_member['member_likes'] ?></strong>
                </div>
            </div>
            <ul class="nav lg-nav tabs-black d-flex justify-content-around rounded mx-2 p-0"  role="tablist">
                <li class="nav-item m-0">
                    <a class="nav-link waves-light active " id="courses-md" data-toggle="tab" href="#courses" role="tab" aria-controls="courses"
                    aria-selected="true">Courses made by <?php echo $the_member['member_username']?></a>
                </li>             
            </ul>
            <div class="tab-content  pt-5" >
                <div class="tab-pane fade show active" id="courses" role="tabpanel" aria-labelledby="courses-md">
                        <div class="d-flex justify-content-between flex-wrap  " >
                            <?php $courso->print_courses($my_work) ?>
                        </div>  
                </div>
            </div>
        </div>
        <?php include "../tools/php/visual/footer.php"?>
        <?php include "../tools/php/essential/footer.php"?>
    </body>
</html>