<?php 
include '../../../tools/php/initial.php';


if(isset($_GET['word']) and !empty($_GET['word'])){
    $_GET['word'] =$conn -> real_escape_string($_GET['word']);
    $sql ="SELECT keyword_word FROM keyword WHERE keyword_word LIKE '%".$_GET['word']."%' ORDER BY keyword_score DESC LIMIT 5;";
    $result =$conn ->query($sql);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $words_to_json[] =$row['keyword_word'];
        }
        print json_encode($words_to_json);
    }else{print json_encode(array());}
}
?>