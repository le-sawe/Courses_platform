<?php 
include '../../../tools/php/initial.php';
include $utils_dir.'other/model.php';
include $utils_dir.'course/course.php';
include $utils_dir.'other/string.php';

Check_auth([1,2]);

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(set_and_not_empty(array('comment','course'),1)){  
        $the_course = new course($conn, $_POST['course']);
        $the_course->add_comment($_SESSION["member_id"],$_POST['comment']);
        
    }
}
 
?>