<?php 
include '../../../tools/php/initial.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/model.php';
include $utils_dir.'course/course.php';


Check_auth([1,2]);
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(set_and_not_empty(array('comment','course'),1)){
        // check if the one who create the comment is the same one who want to delete it
        $the_course = new course ($conn ,$_POST['course']);
        $the_course ->delete_comment($_POST['comment'],$_SESSION['member_id']);
    }
}
?>