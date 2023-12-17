
<?php 
include '../../tools/php/initial.php';
include '../../tools/php/parameters.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/files.php';
include $utils_dir.'other/redirect.php';
Check_auth([1,2]);

if(isset($_POST['image'])){
	$data = $_POST['image'];
	$image_array_1 = explode(";", $data);
	$image_array_2 = explode(",", $image_array_1[1]);
	$data = base64_decode($image_array_2[1]);
	// image name
	$image_name = "at".time()."_profile.jpeg";
	$image_dir =$media_dir."profile/".$_SESSION['member_id']."/".$image_name;
	$image_url =$media_url."profile/".$_SESSION['member_id']."/".$image_name;
	// create path
	create_path($media_dir."profile/".$_SESSION['member_id']."/");
	// check size
	if(getBase64ImageSize($data) >= $para_profile_max_size){
		Add_MEssage("Your Profil image Size :" .getBase64ImageSize($data)." it should be less than ".$para_profile_max_size , 3);
	}
	
	// upload
	file_put_contents($image_dir, $data);
	
	// Delete the old image
		// if its not the default image
		if (strcmp($_SESSION['member_profile_url'], $media_url.'profile/tenji_light.png') != 0){
			// get profile url
			$member = new model_access('member',array('member_profile_url'),$conn);
			$profile_url=$member->get("where member_id =".$_SESSION['member_id']."; ")[0]['member_profile_url'];
			delete_file($media_dir.''.$profile_url);
		}
	

	// sql 
	$profile_sql = "UPDATE member SET member_profile_url = 'profile/".$_SESSION['member_id']."/".$image_name."' WHERE member_id = ".$_SESSION['member_id'].";";
	if($conn->query($profile_sql)!==TRUE){
		Add_Message("Error upload file  :".$conn->error,3); 
	}else{	
		// sesssion
		$_SESSION['member_profile_url']=$image_url;
	}

	echo $image_url;
}
?>


