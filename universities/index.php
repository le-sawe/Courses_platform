<?php 


// get all data then search you idiot
//delte search.php
include '../tools/php/initial.php';
include $utils_dir.'other/model.php';

$university = new model_access('university',array('*'),$conn);
$university_data = $university->get();

?>


<html>
    <head>
        <title>Courses - Universities</title>
        <meta name="description" content="List of suported Universities">
        <?php include "../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../tools/php/visual/navigation.php'?>  
            <div class=" p-md-3 mx-auto my-3" style="max-width:1200px">
                <div class="card p-3 my-3 " >
                    <h2 class="h2 text-left ml-4">Universities</h2>
                    <hr class="bg-dark ">
                    <?php         
                        if($university_data != false){
                            foreach($university_data as $row) {

                                echo'  
                                <div class="card m-3 p-2" >
                                    <div class="card-body m-0 p-0">
                                        <div class="d-flex justify-content-between flex-wrap">
                                            <div class="d-flex justify-content-start  ">
                                                <img class="rounded my-auto mr-5     " width="50px" src="'.$media_url."".$row['university_profile_url'].'" alt="Card image cap">
                                                <h4 class="card-title my-auto">  
                                                '.$row['university_name'].'
                                                </h4>

                                            </div>
                                            <div class="ml-auto">
                                                <a  href="materials.php?uni='.$row['university_id'].'" class="btn mt-2  btn-black rounded mr-auto">
                                                    Materials <i class="fas fa-angle-double-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>';      
                            }
                        }
                    ?>
                </div>  
            </div>  
            
        <?php include "../tools/php/visual/footer.php"?>

        <?php include "../tools/php/essential/footer.php"?>

    </body>
</html>
