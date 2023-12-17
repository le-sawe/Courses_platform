<?php
include '../../tools/php/initial.php';
include '../../tools/php/parameters.php';
include $utils_dir.'account/member.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/files.php';
include $utils_dir.'other/string.php';
Check_auth([1,2]);

// if its a post request
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    // Name
        // if the name input set and not empty and has changed 
        if( isset($_POST['name']) && !(empty($_POST['name'])) && (strcmp($_POST['name'],$_SESSION['member_name']) != 0)){
            // Validate by size
                if(!(strlen($_POST['name']) >=$para_name_min_length && strlen($_POST['name']) <=$para_name_max_length)){   
                    Add_Message("The name length should be between ".$para_name_min_length." and ".$para_name_max_length,3);
                }
            // Validate by content
                if (!ctype_alpha(str_replace(' ', '', $_POST['name']))) {
                    Add_Message("The name should contain only english letters",3);
                } 
            // Update Name
                if(all_good()){
                    $member = new model_access("member",array("member_name"),$conn);
                    $member->update(array(add_double_apostrophe($_POST['name'])),"member_id =".$_SESSION['member_id'],array('member_name'));
                    $_SESSION['member_name']= $_POST['name'];   // Change the name on the session
                }       
        }

    // Username
        // if username set and not empty and changed
        if( isset($_POST['username']) && !(empty($_POST['username'])) && (strcmp($_POST['username'],$_SESSION['member_username']) != 0)){
            // lower
            $_POST['username']=strtolower($_POST['username']);
            // Validate by size
            if(!(strlen($_POST['username']) >=$para_username_min_length && strlen($_POST['username']) <=$para_username_max_length)){   
                Add_Message("The name length should be between ".$para_username_min_length." and ".$para_username_max_length,3);
            }
            // Validate by content 
            $test_username=str_replace('_', '', $_POST['username']);
            $test_username=str_replace('.', '', $_POST['username']);
            if (!ctype_alnum (str_replace('_', '', $test_username))) {
                Add_Message("The username should contain only (letters , numbers , _ )",3);
            } 
            // Validate if already taken 
            if(all_good()){
                $member = new member($conn);
                if($member->get($_POST['username']) != false){
                    Add_Message("This username already taken",3);
                }
            }
            // Update
            if(all_good()){
                $member = new model_access('member',array('member_id'),$conn);
                $member->update(array(add_double_apostrophe($_POST['username'])),"member_id = ".$_SESSION['member_id'],array("member_username"));
                $_SESSION['member_username']= $_POST['username'];
            }
        }    
    
    // BirthDate
        // if there is no empty or not set field  for Changing Birthdate
        if( isset($_POST['birth_date']) && !(empty($_POST['birth_date'])) ){
            // validate Birthdate            
            if(strtotime($_POST['birth_date']) >= strtotime($para_birth_date_min) && strtotime($_POST['birth_date']) <= strtotime($para_birth_date_max) ){   
                //update the Birthdate
                $edit_birth_sql =' UPDATE member SET member_birth_date = STR_TO_DATE("'.$_POST['birth_date'].'","%Y-%m-%d") WHERE member_id = '.$_SESSION['member_id'].';';  
                if ($conn->query($edit_birth_sql) === TRUE) {
                    $_SESSION['member_birth_date']=  date('Y-m-d',strtotime($_POST['birth_date']));   // Change the name on the session
                }else{
                    Add_Message("ERROR CONNECTION",3);
                }    

            }else{
                Add_Message("The birthdate should be between ".$para_birth_date_min." and  ".$para_birth_date_max,3);
                }
        }    
    // Delete file
        if(isset($_POST["delete_profile"]) && !empty($_POST["delete_profile"])){
            // Delete the old image
                // if its not the default image
                if (strcmp($_SESSION['member_profile_url'], $media_url.'profile/tenji_light.png') != 0){
                    // get profile url
                    $member = new model_access('member',array('member_profile_url'),$conn);
                    $profile_url=$member->get("where member_id =".$_SESSION['member_id']."; ")[0]['member_profile_url'];
                    delete_file($media_dir.''.$profile_url);
                    
                }
            $profile_sql = "UPDATE member SET member_profile_url = 'profile/tenji_light.png';";         
            $_SESSION['member_profile_url']=$media_url.'profile/tenji_light.png';
            
        }
    // Phone Number
        if(isset($_POST['phone_number']) && !(empty($_POST['phone_number'])) ) {
            //remove spaces
            $_POST['phone_number'] =str_replace(' ', '', $_POST['phone_number']);
            // keep only numbers
            $_POST['phone_number'] = preg_replace("/[^0-9]/", '', $_POST['phone_number']);
            if(strcmp($_POST['phone_number'],$_SESSION['member_phone_number']) != 0){
                // validate phone number
                if(strlen($_POST['phone_number']) >=$para_phone_number_min_length && strlen($_POST['phone_number']) <=$para_phone_number_max_length){   
                    // check if this phone number exsit in the database
                    $phone_check_sql = "SELECT member_phone_number FROM member WHERE member_phone_number = '".$_POST['phone_number']."';";
                    $phone_check_result = $conn ->query($phone_check_sql);
                    if($phone_check_result ->num_rows ==0){// if exsit
                        $edit_phone_number_sql =' UPDATE member SET member_phone_number = "'.$_POST['phone_number'].'" WHERE member_id = '.$_SESSION['member_id'].';';  
                        if ($conn->query($edit_phone_number_sql) === TRUE) {
                            $_SESSION['member_phone_number']= $_POST['phone_number'];   // Change the name on the session
                        }else{
                            Add_Message("ERROR CONNECTION",3); 
                        }  
                    }else{
                        Add_Message("This Phone number already registered with an account",3);
                    }
                }
            }
        } 
}

?>
<html>
    <head>
        <title>Edit Profile</title>
        <link rel="stylesheet" href="https://unpkg.com/dropzone/dist/dropzone.css" />
		<link href="https://unpkg.com/cropperjs/dist/cropper.css" rel="stylesheet"/>
		<script src="https://unpkg.com/dropzone"></script>
		<script src="https://unpkg.com/cropperjs"></script>
        <style>

            .image_area {
            position: relative;
            max-width:400px;
            }

            img {
                display: block;
                max-width: 100%;
            }

            .preview {
                overflow: hidden;
                width: 160px; 
                height: 160px;
                margin: 10px;
                border: 1px solid red;
            }

            .modal-lg{
                max-width: 1000px !important;
            }

            .overlay {
            position: absolute;
            bottom: 0px;
            left: 0;
            right: 0;
            background-color: rgba(255, 255, 255, 0.5);
            overflow: hidden;
            height: 0;
            transition: .5s ease;
            width: 100%;
            }

            .image_area:hover .overlay {
            height: 50%;
            cursor: pointer;
            }

            .text {
            color: #333;
            font-size: 20px;
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            text-align: center;
            }
             .picker__date-display {
    background-color: #000 !important;
}

        </style>
        <?php include "../../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../../tools/php/visual/navigation.php';?>
        <div class="card mt-4 p-3 mx-auto" style="max-width:1200px;">   
            <div class="d-flex justify-content-start ">
                <a href="index.php" class="black-text h4 m-2"> <i class="fas fa-angle-double-left"></i> Profile </a>
            </div>     
            <div class="row " >        
                <div  class=" col-lg-6 my-3 mx-auto p-3"  >
                  
                        <div class="image_area mx-auto d-flex justify-content-center " >
                            <form method="post" class="m-0 z-depth-2">
                                <label for="upload_image"  style="margin:0">
                                    <img src="<?php echo $_SESSION['member_profile_url']?>" id="uploaded_image" class="img-responsive img-fluid rounded  mx-auto img-circle " />
                                    <div class="overlay">
                                        <div class="text">Change Your Profile</div>
                                    </div>
                                    <input type="file" name="image" class="image" id="upload_image" style="display:none" />
                                </label>
                            </form>
                        </div>

                        <form method="post" class="row " action="edit.php" >
                            <input value="DELETE" name="delete_profile" type="hidden" name="delete">
                            <button type="submit" class=" btn btn-black white-text btn-rounded btn-sm mx-auto">Delete</button>
                        </form>

                    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Crop Image Before Upload</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="img-container">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <img src="" id="sample_image" />
                                            </div>
                                            <div class="col-md-4">
                                                <div class="preview"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="crop" class="btn btn-black btn-rounded">Crop</button>
                                    <button type="button" class="btn btn-white btn-rounded text-black" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form method="post" action='edit.php' class=" col-lg-5   my-3 mx-auto p-3" >
                    <div class="form-row">
                        <div class="col">
                            <!-- First name -->
                            <div class="md-form">
                                <input type="text"  name="name" id="Name" value="<?php echo $_SESSION['member_name']; ?>" class="form-control">
                                <label for="Name" class="active">Name</label>
                            </div>
                        </div>
                        
                        <div class="col">
                            <!-- username -->
                            <div class="md-form">
                                <input type="text" name="username" id="username" value="<?php echo $_SESSION['member_username']; ?>" class="form-control">
                                <label for="Name" class="active" >Username</label>
                            </div>
                        </div>            
                    </div>
                    
                    <div class="form-row">
                        <div class="md-form col">
                            
                            <input placeholder="Selected date" type="text" <?php if(!empty($_SESSION['member_birth_date'])){ echo 'data-value="'.$_SESSION['member_birth_date'].'"';}?>  name = "birth_date" class="form-control datepicker">
                            <label for="ex_date"  <?php if(!empty($_SESSION['member_birth_date'])){ echo 'class="active"';}?> >Birth Date</label>
                        </div>
                        <div class="md-form col">
                            <input type="text" <?php if(!empty($_SESSION['member_phone_number'])){ echo 'value="'.$_SESSION['member_phone_number'].'"';}else{echo "placeholder=' '";}?>  name = "phone_number" class="form-control ">
                            <label for="phone_number" <?php if(!empty($_SESSION['member_phone_number'])){ echo 'class="active"';}?>>Phone Number</label>
                        </div>
                    </div>
                    <div class="form-row mt-3"><button class="btn btn-black btn-block rounded" type="submit">Edit</button></div>
                </form>
            </div>
        </div>
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>
        <script>
             $('.datepicker').pickadate({
                selectYears: 60,
                format: ' yyyy-mm-dd',
                formatSubmit: 'yyyy-mm-dd',
                today: '',
                max: new Date(<?php  echo date("Y,m,d", $mk_date_max);?>),
                min: new Date(<?php  echo date("Y,m,d", $mk_date_min);?>)
            });
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })

            $(document).ready(function(){

                var $modal = $('#modal');

                var image = document.getElementById('sample_image');

                var cropper;

                $('#upload_image').change(function(event){
                    var files = event.target.files;

                    var done = function(url){
                        image.src = url;
                        $modal.modal('show');
                    };

                    if(files && files.length > 0)
                    {
                        reader = new FileReader();
                        reader.onload = function(event)
                        {
                            done(reader.result);
                        };
                        reader.readAsDataURL(files[0]);
                    }
                });

                $modal.on('shown.bs.modal', function() {
                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 3,
                        preview:'.preview'
                    });
                }).on('hidden.bs.modal', function(){
                    cropper.destroy();
                    cropper = null;
                });

                $('#crop').click(function(){
                    canvas = cropper.getCroppedCanvas({
                        width:400,
                        height:400
                    });

                    canvas.toBlob(function(blob){
                        url = URL.createObjectURL(blob);
                        var reader = new FileReader();
                        reader.readAsDataURL(blob);
                        reader.onloadend = function(){
                            var base64data = reader.result;
                            $.ajax({
                                url:'upload_image.php',
                                method:'POST',
                                data:{image:base64data},
                                success:function(data)
                                {
                                    $modal.modal('hide');
                                    console.log(data);
                                    $('#uploaded_image').attr('src', data);
                                    window.location.href = '<?php echo $base_url ?>account/profile/edit.php';
                                }
                            });
                        };
                    });
                });

                });
        </script>
    </body>
</html>