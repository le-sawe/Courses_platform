<?php 
function redirect_to_logout(){
    echo "LOGOUT";
    Add_Message("LOGOUT",3);
    //header('Location: ../account/logout.php'); 
    exit;
}
function redirect_to_list(){
    //echo "TO LIST";
    redirect_to("account/profile");
    exit;
}
function get_all_types($conn){
    $model = new model_access('course_type',array('*'),$conn);
    return $model->get();
}
function get_all_languages($conn){
    $model = new model_access('language',array('*'),$conn);
    return $model->get();
}
function get_all_materials($conn){
    $model = new model_access('material',array('*'),$conn);
    return $model->get();
}
function get_all_sub_materials($conn){
    $model = new model_access('sub_material',array('*'),$conn);
    return $model->get();
}
function get_all_keywords_words($conn){
    $result = array();
    $model = new model_access('keyword',array('keyword_word'),$conn);
    if($model->get() ==false){return array();}
    foreach($model->get() as $row){
        array_push($result , $row['keyword_word']);
    }
    return $result;
}
function get_uni_material_title_code_sub($id,$conn){
    $model = new model_access('university_material',array('*'),$conn);
    $data =$model->get(' WHERE university_material_id = '.$id)[0];
    if($data ==false){return false;}
    return array( $data['university_material_title'],$data['university_material_code'],$data['university_material_sub_material']);
}

?>