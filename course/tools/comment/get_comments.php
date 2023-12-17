<?php 
include '../../../tools/php/initial.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/string.php';
include $utils_dir.'course/course.php';

Check_auth([1,2]);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(set_and_not_empty(array('course'),1)){  
        $the_course = new course($conn,$_POST['course']);
        print json_encode($the_course->get_comments());
    }
}
?>