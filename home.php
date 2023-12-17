<?php 
/*
1.Member Or Admin Access
2. page goal :
    Change Name 
    Change Password
*/


include 'tools/php/initial.php';
include 'tools/php/parameters.php';
include $utils_dir.'other/model.php';
include $utils_dir.'course/course.php';

Check_auth([1,2]);
$courses = new course($conn);

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
$course_data = $course->get('
    INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
    INNER JOIN member ON course.course_made_by = member.member_id
    INNER JOIN language ON course.course_language = language.language_id
    INNER JOIN liked_sub_material ON course.course_sub_material = liked_sub_material.liked_sub_material_material
    LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
    WHERE liked_sub_material.liked_sub_material_member ='.$_SESSION["member_id"].'
    ORDER BY course.course_score  , course.course_create_date DESC LIMIT 6;'
);
$my_work = $course->get('
        INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
        INNER JOIN member ON course.course_made_by = member.member_id
        INNER JOIN language ON course.course_language = language.language_id
        LEFT JOIN university_material ON course.course_uni_material = university_material.university_material_id
        WHERE course_made_by ='.$_SESSION["member_id"].'
        ORDER BY course.course_create_date  DESC LIMIT 3;'
    );



?>
<html>
<head>
    <title>Home</title>
    <?php include "tools/php/essential/header.php"?>
</head>
<body class="bg-dark">
    <?php include 'tools/php/visual/navigation.php';?>
    <div class="row p-md-3 w-100 m-0 mx-auto" style="max-width:2000px;padding-bottom: 0px!important;">
        <div class= "col-12 p-3">
            <div class="card p-3" >
                <h3>Check the courses provided as part of the Univsersities materials. <a class="btn  btn-black  rounded" href="<?php echo $base_url; ?>universities">Universities</a> </h3>
                
                <small>We currently support only one universitie and a few materials, please contact us if you want to add a course under different universitie or material.  Email : contact@supahaka.com</small>
            </div>
        </div>
    </div>
    <div class="row p-md-3 w-100 m-0 mx-auto" style="max-width:2000px;padding-top: 0px!important;">
        <div class= "col-xl-4 col-lg-5 col-md-6 p-3">
            <div class="card p-3" >
                <div class="d-flex justify-content-between flex-wrap"><h3>Recent Courses By You </h3> <a href='<?php echo $base_url ?>course/mycourses/add.php' class='btn btn-black btn-md rounded'>ADD</a></div>
                <hr>
                <div class="d-flex justify-content-around flex-wrap">

                <?php         
                    if($courses->print_courses($my_work,2)){echo"<a href='".$base_url."account/profile/' class='btn btn-black btn-md rounded w-100 mt-3 '>Manage</a>";}else{
                        echo "
                        <img src='".$base_url."static/img/empty.jpg'  style='max-width:400px;' class='rounded w-100 ' >  
                        <a href='".$base_url."course/mycourses/add.php' class='btn btn-black btn-md rounded w-100 mt-3 '>ADD</a>
                        
                        ";
                    };
                ?>
                </div>

            </div>
        </div>
        <div class= " col-xl-8 col-lg-7 col-md-6 p-3">
            <div class="card p-3 pr-0">
                <div class="d-flex justify-content-between"><h3>Based on Your Prefference</h3> <a href='<?php echo $base_url ?>account/settings/'class="black-text" style="border:0"><i class="fas fa-2x fa-cogs"></i></a> </div>
                <hr>
                <div class="d-flex justify-content-around flex-wrap">
                    <?php         
                         if($courses->print_courses($course_data)){}else{
                            echo "
                        <img src='".$base_url."static/img/empty.jpg' style='max-width:400px;'  class='rounded w-100 ' >  
                        <a href='".$base_url."account/settings/' class='btn btn-black btn-md rounded w-100 mt-3 '>Set up your prefferences</a>
                        ";
                        };
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include "tools/php/essential/footer.php"?>
    <?php include "tools/php/visual/footer.php"?>
    
</body>
</html>