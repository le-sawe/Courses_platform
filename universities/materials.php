<?php 


// get all data then search you idiot
//delte search.php
include '../tools/php/initial.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/redirect.php';

if ($_SERVER['REQUEST_METHOD']== "GET"){
    if(isset($_GET['uni']) and !empty($_GET['uni'])){
        // sugg search
            $sugg_search_uni=array();
        // get Data
            // University Name
                $university = new model_access('university',array('university_name'),$conn);
                $university_name = $university->get('WHERE university_id = '.$_GET['uni'])[0]['university_name'];

            // get uni materials
                $university_material = new model_access("university_material",array('*'),$conn);
                $uni_materials_array = $university_material->get('WHERE university_material_university = '.$_GET['uni'].'');
         
    }
}else{
    redirect_to("universites/index.php");
}

?>


<html>
    <head>
        <title>Courses - <?php echo $university_name ?> Materials </title>
        <meta name="description" content="List of materials in the <?php echo $university_name ?> university">
        <?php include "../tools/php/essential/header.php"?>
    </head>
    <body class="teal lighten-5">
        <?php include '../tools/php/visual/navigation.php'?>  
            <div class=" p-md-3 mx-auto my-3" style="max-width:1200px">

                <div class="card mx-auto rounded p-3 my-3 " >
                    <h2 class="h2 text-left ml-4"><?php echo $university_name ?> Materials</h2>
                    <hr class="bg-dark ">
                    <div  class= "my-2 mb-5  card p-3 py-0" >
                    <div  class= "m-0 p-0 md-form   my-auto" >
                        <input name="search" class=" my-auto form-control mdb-autocomplete " id="search-autocomplete_uni" style="height:30px" oninput="search_options('search-autocomplete_uni')" placeholder="Search by university material or code" aria-label="Search" >
                        <button class="btn btn-black btn-md rounded " onclick="go_to();"><i class="fas fa-search"></i> Search</button>
                        <button class="mdb-autocomplete-clear">
                            <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="https://www.w3.org/2000/svg">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                                <path d="M0 0h24v24H0z" fill="none" />
                            </svg>
                        </button>
                        
                    </div>
                    </div>
                    <?php         
                        if($uni_materials_array !=false){
                            foreach($uni_materials_array as $row) {
                                // sugg search
                                array_push($sugg_search_uni,$row['university_material_title']);
                                array_push($sugg_search_uni,$row['university_material_code']);

                                echo'  
                            
                                
                                <div class="card m-3 p-2" >
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between flex-wrap">
                                            <div class="d-flex justify-content-start  mb-2" id = "'.$row['university_material_code'].'">
                                            <h4 class="card-title my-auto" id = "'.$row['university_material_title'].'">  
                                            '.$row['university_material_title'].' , 
                                            '.$row['university_material_code'].'  
                                            </h4>

                                            </div>
                                            <div class="ml-auto">
                                                <a  href="courses.php?material='.$row['university_material_id'].'" class="btn mt-2 btn btn-black btn-rounded mr-auto">
                                                    Courses <i class="fas fa-angle-double-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ';      
                            }
                        }
                    ?>
                </div>  
            </div>  
            
        <?php include "../tools/php/visual/footer.php"?>

        <?php include "../tools/php/essential/footer.php"?>
        <script>
            <?php   
            $js_array_uni = json_encode($sugg_search_uni);
            echo "var sugg_search_uni = ". $js_array_uni . ";\n";
            ?>
            $('#search-autocomplete_uni').mdbAutocomplete({
                data: sugg_search_uni
            });
            function go_to(){
                id=document.getElementById('search-autocomplete_uni').value;
                document.getElementById(id).scrollIntoView();
                document.getElementById(id).classList.add("text-info");

            }
        </script>
    </body>
</html>
