<?php
    // settings.php you can change your settings and prefference 

    // include
    include '../../tools/php/initial.php';
    include '../../tools/php/parameters.php';
    include $utils_dir.'other/model.php';

    // Check auth
    Check_auth([1,2]); 

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        // PASSWORD
            // if there is no empty or not set field  for Changing password
            if(isset($_POST['oldpass']) && isset($_POST['pass']) && isset($_POST['pass2']) && !( empty($_POST['pass']) ||  empty($_POST['pass2']))){
                // Check if old pass match
                if(password_verify($_POST['oldpass'], $_SESSION['member_pass']) || empty($_SESSION['member_pass'])){     
                    //check if the two pass match (New , and Retype)
                    if(strcmp($_POST['pass2'],$_POST['pass'])==0){
                        // Validate Password
                        if(strlen($_POST['pass']) >=$para_password_min_length && strlen($_POST['pass']) <=$para_password_max_length){
                            //Update Password
                            $edit_pass_sql =' UPDATE member SET member_pass = "'.password_hash($_POST['pass'], PASSWORD_DEFAULT).'" WHERE member_id = "'.$_SESSION['member_id'].'";';                       
                            if ($conn->query($edit_pass_sql) === TRUE) {
                                $_SESSION['member_pass']= password_hash($_POST['pass'], PASSWORD_DEFAULT);
                                Add_Message("Password Changed",0);
                            }else{
                                Add_Message("ERROR CONNECTION",3);
                            }
                        }else{
                            Add_Message("The password length should be between ".$para_password_min_length." and ".$para_password_max_length." !",3);
                        }
                    }else{
                        Add_Message("The two password doesn t match",3);
                    }
                }else{
                    Add_Message("The Old password dosen't match",3);
                }
                
            }
    }
    //Get Data
        // get sub material
            $sub_material = new model_access('sub_material',array('sub_material_id','sub_material_title','sub_material_material'),$conn);
            $all_sub_material_array = $sub_material->get();
        // get liked sub material
            $liked_sub_material = new model_access('liked_sub_material',array('liked_sub_material_material'),$conn);
            $liked_sub_material_array = $liked_sub_material->get("WHERE liked_sub_material_member = ".$_SESSION['member_id']." ;");
        // get  material
            $material = new model_access('material',array('*'),$conn);
            $all_material_array = $material->get();

        
?>
<html>
    <head>
        <title>Settings </title>
        <?php include "../../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../../tools/php/visual/navigation.php';?>

        <div class=" classic-tabs  card p-3 mx-auto my-4" style="max-width:1200px;">
            <h2 class="h2 text-center">Security && Prefference </h2>
            <hr class="bg-dark ">
            <div class="d-flex justify-content-around row"> 
                <div class="col-md-2 mt-5" >
                    <ul class="nav tabs-black p-1 rounded"  role="tablist">
                        <li class="nav-item m-0 w-100">
                            <a class="nav-link waves-light  w-100" id="security-md" data-toggle="tab" href="#security" role="tab" aria-controls="security"
                            aria-selected="true">Security</a>
                        </li>
                        <li class="nav-item m-0 w-100">
                            <a class="nav-link waves-light active w-100" id="prefference-md" data-toggle="tab" href="#prefference" role="tab" aria-controls="prefference"
                            aria-selected="false">Prefference</a>
                        </li>

                    </ul>
                </div>
           
                <div class="tab-content col-md-10"  >
                    <div class="tab-pane fade  " id="security" role="tabpanel" aria-labelledby="security-md">
                        <form method="post" action='index.php' >
                            
                            <div class="form-row">
                                <div class="col">
                                    <!-- Old password -->
                                    <div class="md-form">
                                        <input type="password" name="oldpass" id="old_password" class="form-control">
                                        <label for="old_password">Old Password</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <!-- Email -->
                                    <div class="md-form">
                                        <input type="email" id="email" class="form-control" value="<?php echo $_SESSION['member_email']; ?>" readonly>
                                        <label for="email"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col">
                                    <!-- New password -->
                                    <div class="md-form">
                                        <input type="password" id="new_pass" name="pass" class="form-control">
                                        <label for="new_pass">New Password</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <!-- Retype password -->
                                    <div class="md-form">
                                        <input type="password" id="retype_pass" name="pass2" class="form-control">
                                        <label for="retype_pass">Retype Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row"><button class="btn btn-black btn-block rounded" type="submit">Edit</button></div>
                        </form>
                    </div>
                    <div class="tab-pane fade show active" id="prefference" role="tabpanel" aria-labelledby="prefference-md">
                        <form method="post" id="like" action="<?php echo $base_url?>account/tools/like_material/toggle_like_material.php" >
                            <input type = "hidden" name="like" value="1" >
                            <input id="sub_id_submit" type = "hidden" name="sub_material" value="1">
                        </form>
                        <div >
                                <?php 
                                    if($all_material_array != false){
                                        foreach($all_material_array as $material){
                                            echo'
                                            <button class="btn btn-black W-100" type="button" data-toggle="collapse" data-target="#'.$material['material_title'].'collapse" aria-expanded="false" aria-controls="'.$material['material_title'].'collapse">
                                                '.$material['material_title'].'                              
                                            </button>';

                                            echo'
                                            <div class="collapse " id="'.$material['material_title'].'collapse">
                                                <div  class="d-flex justify-content-center flex-wrap">
                                        ';
                                            foreach($all_sub_material_array as $sub_material){
                                                if($sub_material["sub_material_material"]==$material['material_id']){
                                                    if($liked_sub_material_array != false && in_array($sub_material['sub_material_id'], $liked_sub_material_array)){
                                                        $heart='<i class="fas fa-heart mx-2"></i>';
                                                    }else{$heart='';}
                                                    echo '                             
                                                        <button type="button" onclick="like('.$sub_material['sub_material_id'].')" class="btn btn-outline-black mx-3" >
                                                            '.$sub_material['sub_material_title'].'  <span id="sub_material'.$sub_material['sub_material_id'].'">'.$heart.' </span>
                                                        </button>
                                                        ' ;
                                                }
                                            }
                                        echo'   </div>
                                            </div>';
                                        }
                                    }
                                ?>
                        </div>

                    </div>
                </div>
            </div> 
        </div>
                        
        <?php include "../../tools/php/visual/footer.php"?>
        <?php include "../../tools/php/essential/footer.php"?>
        <script>
            function like(id){
                document.getElementById('sub_id_submit').value=id;
                var frm = $('#like');
                $.ajax({
                    type: frm.attr('method'),
                    url: frm.attr('action'),
                    data: frm.serialize(),
                    success: function (data) {
                        get_likes();
                        console.log(data);
                    },
                    error: function (data) {

                    },
                });
            }
            // Get comments
            function get_likes(){
                var all_sub_material_result = <?php echo json_encode($all_sub_material_array); ?>;
                
                $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url?>account/tools/like_material/get_likes.php",
                    data: {
                        sub_material : 1
                    },
                    success: function (data) {
                        data = data.split(","); 
                        data.pop();
                        //clear 
                        for (let i = 0; i < all_sub_material_result.length ; i++) {
                            document.getElementById("sub_material"+all_sub_material_result[i]['sub_material_id']).innerHTML="";
                        }
                        //add
                        for (let i = 0; i < data.length ; i++) {
                            document.getElementById("sub_material"+data[i]).innerHTML="<i class='fas fa-heart mx-2'></i>";
                        }
                    },
                    error: function (data) {

                    },
                });
            }
            get_likes();
        </script>
    </body>           

</html>