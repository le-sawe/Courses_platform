<?php 
include '../../../tools/php/initial.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/model.php';

Check_auth([1,2]);

if(set_and_not_empty(array('sub_material'),1)){
    $liked_sub_material_sql= "SELECT liked_sub_material_material FROM liked_sub_material WHERE  liked_sub_material_member = ".$_SESSION['member_id'].";";
    $liked_sub_material_result =$conn->query($liked_sub_material_sql);
    //Stock liked sub material material id  on array
    if($liked_sub_material_result->num_rows>0){
        while($row = $liked_sub_material_result->fetch_assoc()){
            echo $row['liked_sub_material_material'];
            echo ',';
        }
    }
}
?>