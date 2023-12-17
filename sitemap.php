<?php 

//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! IMPORTANT 
// Replace ..... with Your domain
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! IMPORTANT 


include 'tools/php/initial.php';

$courses_id_query = $conn->query( "SELECT course_id FROM course ;");

$courses_id_list=array();

if($courses_id_query->num_rows >0){

    while($row = $courses_id_query->fetch_array()){

        array_push($courses_id_list,$row['course_id']);

    }

}



$university_id_query = $conn->query( "SELECT university_id FROM university ;");

$university_id_list=array();

if($university_id_query->num_rows >0){

    while($row = $university_id_query->fetch_array()){

        array_push($university_id_list,$row['university_id']);

    }

}



$university_material_id_query = $conn->query( "SELECT university_material_id FROM university_material ;");

$university_material_id_list=array();

if($university_material_id_query->num_rows >0){

    while($row = $university_material_id_query->fetch_array()){

        array_push($university_material_id_list,$row['university_material_id']);

    }

}

$sub_material_id_query = $conn->query( "SELECT sub_material_id FROM sub_material ;");

$sub_material_id_list=array();

if($sub_material_id_query->num_rows >0){

    while($row = $sub_material_id_query->fetch_array()){

        array_push($sub_material_id_list,$row['sub_material_id']);

    }

}



?>



<?php echo '<?xml version="1.0" encoding="UTF-8"?>';?>



<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

   <url>

      <loc>https://...../old/</loc>

   </url>

   <url>

      <loc>https://...../old/course/</loc>

   </url>

   <url>

      <loc>https://...../old/universities/</loc>

   </url>



   <?php 

    if(sizeof($courses_id_list) >0){

        foreach($courses_id_list as $course_id){

            echo'

            <url>

                <loc>https://...../old/course/detail.php?course='.$course_id.'</loc>

            </url>

            ';

        }

    }

    if(sizeof($university_id_list) >0){

        foreach($university_id_list as $university_id){

            echo'

            <url>

                <loc>https://...../old/universities/materials.php?uni='.$university_id.'</loc>

            </url>

            ';

        }

    }

    if(sizeof($university_material_id_list) >0){

        foreach($university_material_id_list as $university_material_id){

            echo'

            <url>

                <loc>https://...../old/universities/courses.php?material='.$university_material_id.'/</loc>

            </url>

            ';

        }

    }

    if(sizeof($sub_material_id_list) >0){

        foreach($sub_material_id_list as $sub_material_id){

            echo'

            <url>

                <loc>https://...../old/course/index.php?filter='.$sub_material_id.'</loc>

            </url>

            ';

        }

    }



   ?>

</urlset> 

