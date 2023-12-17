<?php 
include '../../tools/php/initial.php';


if(isset($_GET['word']) and !empty($_GET['word'])){
    $_GET['word'] =$conn -> real_escape_string($_GET['word']);
    $empty_result = true;
    $keywrod_sql ="SELECT keyword_word FROM keyword WHERE keyword_word LIKE '%".$_GET['word']."%' ORDER BY keyword_score DESC LIMIT 2;";
    $keyword_result =$conn ->query($keywrod_sql);
    if($keyword_result->num_rows > 0){
        $empty_result = false;
        while($row = $keyword_result->fetch_assoc()){
            $words_to_json[] =$row['keyword_word'];
        }
    }
    $course_sql = "SELECT course_title FROM course WHERE course_title LIKE '%".$_GET['word']."%' ORDER BY course_score DESC LIMIT 2;";
    $course_result = $conn ->query($course_sql) ;
    if($course_result-> num_rows >0){
        $empty_result = false;
        while($row = $course_result->fetch_assoc()){
            $words_to_json[] =$row['course_title'];
        }
    }
    if($empty_result){
        print json_encode(array());
    }else{
        print json_encode($words_to_json);
    }
}
?>