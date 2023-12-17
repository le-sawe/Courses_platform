<?php 
include '../../../tools/php/initial.php';
include $utils_dir.'other/string.php';
include $utils_dir.'other/model.php';

Check_auth([1,2]);


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(set_and_not_empty(array('like','sub_material'),1)){
         //Change like situation
            // Check if the user have liked this sub material
                $like_sub_material = new model_access ('liked_sub_material',array("liked_sub_material_id","liked_sub_material_member","liked_sub_material_material"),$conn);
                $check_liked = $like_sub_material->get(" WHERE liked_sub_material_member = ".$_SESSION['member_id']." AND liked_sub_material_material =".$_POST['sub_material']);
                
                // if the user have like the sub material
                if($check_liked ==false){//Add sub_material
                    $like_sub_material->insert(array($_SESSION['member_id'],$_POST['sub_material']),array("liked_sub_material_id"));

                }else{ //Remove like    
                    $like_id=$check_liked[0]['liked_sub_material_id'];
                    $like_sub_material->delete("liked_sub_material_id = ".$like_id." ;");

                }
    }     
}
?>