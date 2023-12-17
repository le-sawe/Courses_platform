<?php 
/*
1. Admin Or Member Access
2.Page Goal :
    See Course Detail :
        Course Title
        Language
        Sub Material
        Description
        Files(download)
        Keywords
        View Count
        Course Create date
    Add View :
        if the user see the course for the first time then Create a view 
*/
include '../tools/php/initial.php';
include $utils_dir.'course/course.php';
include $utils_dir.'other/model.php';
include $utils_dir.'other/redirect.php';
include $utils_dir.'other/string.php';

$a_member=true;
if(!isset($_SESSION['verified']) || empty($_SESSION['verified']) || $_SESSION['verified']!=true){
    $a_member=false;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['course'])&& !empty($_GET['course'])){
        // you should do some fixes here but for now 
        $the_courso = new course($conn ,$_GET['course']);
        // Get Course Detail
            $course = new model_access("course",array('course.course_title' ,'course.course_description' ,'material.material_title','sub_material.sub_material_title' ,'language.language_title'  , 'course.course_create_date' ,'member.member_username'),$conn);
            $course_result = $course->get("
                INNER JOIN sub_material ON course.course_sub_material = sub_material.sub_material_id
                INNER JOIN material ON sub_material.sub_material_material = material.material_id
                INNER JOIN member ON course.course_made_by = member.member_id
                INNER JOIN language ON course.course_language = language.language_id WHERE course_id =".$_GET['course'].";");
            if($course_result == false){redirect_to('course/index.php');}
        //Get Files Detail : (file url)
            $files = new model_access('course_file',array('course_file_url'),$conn);
            $files_result = $files->get("WHERE course_file_course = ".$_GET['course']);

        //Get Keywords : (keyword Word)
            $keywords = new model_access('keyword',array('keyword.keyword_word'),$conn);
            $keywords_result =$keywords->get("INNER JOIN keyword_course ON keyword_course.keyword_course_keyword=keyword.keyword_id WHERE keyword_course.keyword_course_course = ".$_GET['course'].";");

        //View : (view count)
            $view = new model_access('view',array('view_id','view_member','view_course'),$conn);
            $views_result = $view->get("WHERE view_course = ".$_GET['course']." ;" ,array('COUNT(view_id)'));       

        if($a_member){
        //ADD VIEW
            // Check if its the first time that the user see this course
                $check_view = $view->get("WHERE view_member = ".$_SESSION['member_id']." AND view_course =".$_GET['course'].";");
            //if its the first time that the user see this course ---> add course
                if($check_view == false){
                    //Add
                    $view ->insert(array($_SESSION['member_id'],$_GET['course']),array('view_id'));
                    // refresh
                    $the_courso->refresh_stat();
                }
            

        // Like 
            $like = new model_access('liked_course',array('*'),$conn);
            $get_member_like = $like->get('WHERE liked_course_member ='.$_SESSION['member_id'].' And liked_course_course ='.$_GET['course']);
            if($get_member_like == false){
                $liked=0;
            }else{
                $liked =1;
            }
        // save 
            $save = new model_access('save_course',array('*'),$conn);
            $get_member_save = $save->get('WHERE save_course_member ='.$_SESSION['member_id'].' And save_course_summary_course ='.$_GET['course'] );
            if($get_member_save == false){
                $saved=0;
            }else{
                $saved =1;
            }
        }
        if($course_result !=false){
           foreach($course_result as $course_row){ 
               $the_course_title = $course_row["course_title"];
               $the_course_desc = $course_row["course_description"];
           }
    }
         
     }
}else{exit();}

?>
<html>
    <head>
        <title>Courses - <?php echo $the_course_title ;?> Course Detail  </title>
        <meta name="description" content="<?php echo $the_course_desc ;?> ">
        <meta name="keywords" content="
        <?php 
            if($keywords_result!= false){
                foreach($keywords_result as $keywords_row){
                    echo $keywords_row["keyword_word"].' , ';
                }
    
            }
        ?>
        ">
        <?php include "../tools/php/essential/header.php"?>
        <script
            src="<?php echo $base_url ?>static/js/pdf.min.js">
        </script>

    </head>
    <body class="teal lighten-5">
    <?php include '../tools/php/visual/navigation.php'?>
        <?php 
            if($a_member){
                $reload_btn_syntax='<button class=" " style="border:0;background-color:transparent;" id="reload" onclick="reload_l_c();"><i class="fas fa-sync-alt"></i> </button>';
            }else{
                $reload_btn_syntax="";
            }
            if($course_result !=false){
                foreach($course_result as $course_row){
                    echo '
                        <div class="card mt-5 wider reverse mx-auto" style="max-width:1200px">
                            <!-- Card content -->
                            <div class="card-body text-left">
                                <!-- Title -->
                                <h1 class="card-title d-flex justify-content-between"><strong>'.$course_row["course_title"].'</strong> '.$reload_btn_syntax.'</h1>
                                <hr class="">
                                <h4 class="font-weight-bold black-text py-2"> '.$course_row["language_title"].' , '.$course_row["material_title"].' , '.$course_row["sub_material_title"].' at '.date("Y-m-d", strtotime($course_row['course_create_date'])).' by <a class="text-info" href="'.$base_url.'people?member='.$course_row["member_username"].'" >'.$course_row["member_username"].'</a> </h4>                              
                                <p class="h6 ml-3"> '.$course_row["course_description"].'</p>
                                ';
                                $counter =0 ;
                                //FILES
                                if($files_result!= false){
                                    echo '<div class="row ml-1"><h5 class="font-weight-bold black-text py-2">Download the pdfs :</h5>';
                                    foreach($files_result as $files_row){
                                        $counter ++;
                                        echo '
                                           <a class="mx-2 mt-2 font-weight-bold black-text h4" data-toggle="tooltip" title="The link of the file : '.$media_url.''.$files_row["course_file_url"].'"  href="'.$media_url.''.$files_row["course_file_url"].'" download>#'.$counter.' <i class="fas fa-file-download"></i></a>  
                                        ';
                                        $url_to_render=''.$media_url.''.$files_row["course_file_url"].'';
                                    }
                                    echo '</div>';
                                }
                                //KEYWORDS
                                echo '<div class="row ml-1"><h5 class="font-weight-bold black-text py-2">Keywords :</h5>';
                                if($keywords_result!= false){
                                    foreach($keywords_result as $keywords_row){
                                        echo '
                                            <span class="black-text h5 mx-2 mt-2">#'.$keywords_row["keyword_word"].'</span> 
                                        ';
                                    }
                                    echo '</div>
                                    <hr>';
                                }
                                //PDF RENDERING
                                if($counter==1){
                                    echo'<a class="mx-auto my-2 text-center badge badge-success w-100"  href="'.$url_to_render.'" download >Download the pdf to see the original resolution</a>
                                        <div class="mx-auto text-center text-info" id="pdf_loading">LOADING </div>
                                        <div id="my_pdf_viewer">
                                            <div id="canvas_container" class="w-100 rounded" style="overflow: auto;text-align:center" >
                                                <canvas id="pdf_renderer"></canvas>
                                            </div>
                                            <div class="d-flex justify-content-center my-2">
                                                <div id="navigation_controls">
                                                    <div class="def-number-input number-input safari_only">
                                                        <button id="go_previous" onclick="this.parentNode.querySelector(\'input[type=number]\').stepDown()" class="minus"></button>
                                                        <input id="current_page" class="quantity" min="0" name="quantity" value="1" type="number">
                                                        <button id="go_next" onclick="this.parentNode.querySelector(\'input[type=number]\').stepUp()" class="plus"></button>
                                                    </div>
                                                </div>
                                            </div>                                 
                                            
                                        </div>';
                                }
                                // VIEWS
                                if($views_result != false && $a_member){
                                    foreach($views_result as $views_row){
                                        echo '
                                        <hr class=" my-4"> 
                                        <div class="row ml-2">
                                            <h4 class="font-weight-bold black-text py-2" data-toggle="tooltip" title="Users views"> Views  : '.$views_row["COUNT(view_id)"].' , Likes : <span id="like_count"></span> </h4>
                                        </div>
                                        ';
                                    }
                                }

                }
            }
        ?>
        <?php if($a_member){
            if ($liked==0){
                $liked_syntax = 'class="btn btn-outline-block" ><i class="far fa-thumbs-up fa-2x"></i> LIKE';
            }else{
                $liked_syntax = 'class="btn btn-black" ><i class="far fa-thumbs-down fa-2x"></i> DISLIKE';
            }
            if ($saved==0){
                $saved_syntax='class="btn btn-outline-block" ><i class="far fa-save fa-2x"></i> Save';
            }else{
                $saved_syntax='class="btn btn-black" ><i class="fas fa-ban fa-2x"></i> UNSAVE';
            }
            echo'
            <div class="row ml-2" >
                <form id="like" action="'.$base_url.'course/tools/likes/toggle_like.php" method="post" data-autosubmit>
                    <input id="like_status" type="hidden" name="like" value='.$liked.'>
                    <input type="hidden" name="course" value='.$_GET['course'].'>
                    <button id="like_btn" type="submit" '.$liked_syntax.' </button>
                    <button id="like_load" type="button" class="btn btn-black" style="display:none" ><div class="loadere mx-auto mt-2"> <br></div></button>
                </form>
                <form id="save" action="'.$base_url.'course/tools/save/toggle_save.php" method="post" data-autosubmit>
                    <input id="save_status" type="hidden" name="save" value='.$saved.'>
                    <input type="hidden" name="course" value='.$_GET['course'].'>
                    <button id="save_btn" type="submit" '.$saved_syntax.' </button>
                    <button id="save_load" type="button" class="btn btn-black" style="display:none" ><div class="loadere mx-auto mt-2"> <br></div></button>
                </form>
            </div>
            
            
            <hr>
            <div class="chat-message">
                <h4 class="card-title"><strong>Comments</strong></h4>
                <hr class="">
                <ul class="list-unstyled chat" id="comments_section">
                    
                </ul>

            </div>
            
                <form id ="comment_form" action="'.$base_url.'course/tools/comment/add_comment.php" method="post" data-autosubmit>
                <input type="hidden" name="course" value='.$_GET['course'].'>
                <div class="md-form mb-4 black-textarea active-black-textarea" style="width:100%">
                    <i class="fas fa-angle-double-right prefix"></i>
                    <textarea name="comment" id="comment_area" class="md-textarea form-control" rows="3"></textarea>
                    <label for="comment">Comment</label>
                </div>
                <button id="comment_btn" class="btn btn-outline-block" type="submit">Send</button>
                </form>
                ';
         }
        ?>
       

         </div>
        </div>

         
        <?php include "../tools/php/visual/footer.php"?>
        <?php include "../tools/php/essential/footer.php"?>
        <script>
            function print_comments(array){
                var base_url = '<?php echo $base_url ?>';
                var media_url = '<?php echo $media_url ?>';
                var comments_section_div =document.getElementById('comments_section');
                comments_section_div.innerHTML ="";
                for(i=0;i<array.length;i++){
                    if(!array[i][3].startsWith('http')){
                        array[i][3] = media_url+array[i][3];
                    }
                    if(array[i][2] == '<?php if($a_member){echo $_SESSION['member_username'] ;}else{echo "p";} ?>'){
                        delete_syntax = " <button class=' btn-sm btn-danger mx-2' onclick='delete_comment("+array[i][4]+")' style='border:0'  ><i class=' fas fa-xs fa-times'></i> </button>";
                    }else{delete_syntax="";}
                    comments_section_div.innerHTML +="<li class='mb-4'><div class='chat-body white p-3 ml-2 z-depth-1'><div class='header d-flex justify-content-between'><div><a class='black-text h5' href='"+base_url+"people/?member="+array[i][2]+"'><img width='40px' src='"+array[i][3]+"' alt='avatar' class=' rounded-circle fluid-img mr-2 '> "+array[i][2]+"</a></div> <div> <small class='pull-right text-muted'>"+array[i][1]+"</small>"+delete_syntax+"</div></div><hr class='w-100'><p class='mb-0'>"+array[i][0]+"</p></div></li>";
                }
            }
            <?php if(!$a_member){ echo "/*";} ?>
 
            function like(){       
                if(document.getElementById('like_status').value == 0){
                    document.getElementById('like_btn').innerHTML="<i class='far fa-2x fa-thumbs-down'></i> DISLIKE";
                    document.getElementById('like_btn').className="btn btn-black";
                    document.getElementById('like_status').value = -1;
                }else{
                    document.getElementById('like_btn').innerHTML="<i class='far fa-2x fa-thumbs-up'></i> LIKE";
                    document.getElementById('like_btn').className="btn btn-outline-block";
                    document.getElementById('like_status').value = 0;
                }
            }

            function save(){       
                if(document.getElementById('save_status').value == 0){
                    document.getElementById('save_btn').innerHTML="<i class=' fa-2x fas fa-ban'></i> UNSAVE";
                    document.getElementById('save_btn').className="btn btn-black";
                    document.getElementById('save_status').value = -1;
                }else{
                    document.getElementById('save_btn').innerHTML="<i class='far fa-2x fa-save'></i> SAVE";
                    document.getElementById('save_btn').className="btn btn-outline-block";
                    document.getElementById('save_status').value = 0;
                }
            }

            $(function () {
            $('[data-toggle="tooltip"]').tooltip()
            })
            // add like
            var frm = $('#like');

            frm.submit(function (e) {
                document.getElementById('like_btn').style.display = 'none';
                document.getElementById('like_load').style.display = 'block';
                e.preventDefault();

                $.ajax({
                    type: frm.attr('method'),
                    url: frm.attr('action'),
                    data: frm.serialize(),
                    success: function (data) {
                        like();
                        get_likes()
                        document.getElementById('like_load').style.display = 'none';
                        document.getElementById('like_btn').style.display = 'block';
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    },
                });
            });

            // add save
            var form = $('#save');

            form.submit(function (e) {
                document.getElementById('save_btn').style.display = 'none';
                document.getElementById('save_load').style.display = 'block';
                e.preventDefault();

                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function (data) {
                        save();
                        document.getElementById('save_load').style.display = 'none';
                        document.getElementById('save_btn').style.display = 'block';
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    },
                });
            });


            // add comment
            var frmcomment = $('#comment_form');

            frmcomment.submit(function (e) {

                e.preventDefault();

                $.ajax({
                    type: frmcomment.attr('method'),
                    url: frmcomment.attr('action'),
                    data: frmcomment.serialize(),
                    success: function (data) {
                        get_comments();
                        $("#comment_area").val("");
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    },
                });
            });
            
            // Get comments
            function get_comments(){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url ?>course/tools/comment/get_comments.php",
                    data: {
                        course: <?php echo $_GET['course'] ;?>
                    },
                    success: function (data) {
                        const comment_list = JSON.parse(data);
                        print_comments(comment_list);
                        $("#comment_area").val("");
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    },
                });
            }

            // Get Likes
            function get_likes(){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url ?>course/tools/likes/get_likes.php",
                    data: {
                        course: <?php echo $_GET['course'] ;?>
                    },
                    success: function (data) {
                        document.getElementById("like_count").innerHTML=data;
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    },
                });
            }
            
            // Delete Comment
            function delete_comment(id){
                $.ajax({
                    type: "POST",
                    url: "<?php echo $base_url ?>course/tools/comment/delete_comment.php",
                    data: {
                        comment: id ,
                        course : <?php echo $_GET['course'];?>
                    },
                    success: function (data) {
                        get_comments();
                    },
                    error: function (data) {
                        console.log('An error occurred.');
                    },
                });
            }
            
            
            function reload_l_c(){
                $('#reload').removeClass().addClass('animated rotateIn').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                    $('#reload').removeClass();
                });

                get_likes();
                get_comments();
            }
            reload_l_c();
            <?php if(!$a_member){ echo "*/";} ?>


        // PDF _---------------------------------------------------------
        
        // get pdf size 

        var fileSize = '';
        var http = new XMLHttpRequest();
        http.open('HEAD', '<?php echo $url_to_render ?>', false); // false = Synchronous
    
        http.send(null); // it will stop here until this http request is complete
    
        // when we are here, we already have a response, b/c we used Synchronous XHR
    
        if (http.status === 200) {
            fileSize = http.getResponseHeader('content-length');
        }
        document.getElementById("pdf_loading").innerHTML = "Loading ...  <br> The pdf size is "+Math.floor(fileSize /1024 /1024)+" mb <br> it may take a while <div class='loader mx-auto mt-2'> <br></div><span class='badge badge-warning text-center'>Warning : if you have IDM (internet download manager ) the pdf will be downloaded instead of rendering  , pleas turn off the extension</span> ";


        var myState = {
            pdf: null,
            currentPage: 1,
            zoom: 1
        }
      
        pdfjsLib.getDocument('<?php echo $url_to_render ?>').then((pdf) => {
            myState.pdf = pdf;
            render();
 
        });
 
        function render() {
            myState.pdf.getPage(myState.currentPage).then((page) => {
                var scale = 1.5;
                var viewport = page.getViewport({ scale: scale, });
                // Support HiDPI-screens.
                var outputScale = window.devicePixelRatio || 1;
                    
                var canvas = document.getElementById("pdf_renderer");
                var ctx = canvas.getContext('2d');
      
                var viewport = page.getViewport(myState.zoom);
                var ratio = 100*(viewport.height/viewport.width);
                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + "px";
                canvas.style.height =  Math.floor(viewport.height) + "px";
                canvas.style.maxWidth  ="100%";
                canvas.style.maxHeight  =Math.floor(ratio) +"vw";
                console.log(Math.floor(ratio) +"vw");
                var transform = outputScale !== 1
                  ? [outputScale, 0, 0, outputScale, 0, 0]
                  : null;
          
                page.render({
                    canvasContext: ctx,
                    transform: transform,
                    viewport: viewport
                }).promise.then(function(){
                    document.getElementById("pdf_loading").style.display = "none";
                });
            });
        }
        document.getElementById('go_previous')
            .addEventListener('click', (e) => {
            if(myState.pdf == null|| myState.currentPage == 1) 
            return;
                
            myState.currentPage -= 1;
            document.getElementById("current_page").value = myState.currentPage;
            render();
        });
        document.getElementById('go_next')
            .addEventListener('click', (e) => {
            if(myState.pdf == null || myState.currentPage > myState.pdf._pdfInfo.numPages) 
            return;
                
            myState.currentPage += 1;
            document.getElementById("current_page").value = myState.currentPage;
            render();
        });
        document.getElementById('current_page')
        .addEventListener('keypress', (e) => {
            if(myState.pdf == null) return;
        
            // Get key code
            var code = (e.keyCode ? e.keyCode : e.which);
        
            // If key code matches that of the Enter key
            if(code == 13) {
                var desiredPage = document.getElementById('current_page').valueAsNumber;
                                
                if(desiredPage >= 1 && desiredPage <= myState.pdf._pdfInfo.numPages) {
                    myState.currentPage = desiredPage;
                    document.getElementById("current_page").value = desiredPage;
                    render();
                }
            }
        });
    </script>
    <style>
            .loader {
              border: 16px solid white; /* Light grey */
              border-top: 16px solid black; /* Blue */
              border-radius: 50%;
              width: 120px;
              height: 120px;
              animation: spin 2s linear infinite;
            }
            .loadere {
              border: 6px solid white; /* Light grey */
              border-top: 6px solid black; /* Blue */
              border-radius: 50%;
              width: 40px;
              height: 40px;
              animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
              0% { transform: rotate(0deg); }
              100% { transform: rotate(360deg); }
            }
            .number-input input[type="number"] {
            -webkit-appearance: textfield;
            -moz-appearance: textfield;
            appearance: textfield;
            }

            .number-input input[type=number]::-webkit-inner-spin-button,
            .number-input input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            }

            .number-input {
            display: flex;
            justify-content: space-around;
            align-items: center;
            }

            .number-input button {
            -webkit-appearance: none;
            background-color: transparent;
            border: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin: 0;
            position: relative;
            }

            .number-input button:before,
            .number-input button:after {
            display: inline-block;
            position: absolute;
            content: '';
            height: 2px;
            transform: translate(-50%, -50%);
            }

            .number-input button.plus:after {
            transform: translate(-50%, -50%) rotate(90deg);
            }

            .number-input input[type=number] {
            text-align: center;
            }

            .number-input.number-input {
            border: 1px solid #ced4da;
            width: 10rem;
            border-radius: .25rem;
            }

            .number-input.number-input button {
            width: 2.6rem;
            height: .7rem;
            }

            .number-input.number-input button.minus {
            padding-left: 10px;
            }

            .number-input.number-input button:before,
            .number-input.number-input button:after {
            width: .7rem;
            background-color: #495057;
            }

            .number-input.number-input input[type=number] {
            max-width: 4rem;
            padding: .5rem;
            border: 1px solid #ced4da;
            border-width: 0 1px;
            font-size: 1rem;
            height: 2rem;
            color: #495057;
            }

            @media not all and (min-resolution:.001dpcm) {
            @supports (-webkit-appearance: none) and (stroke-color:transparent) {

                .number-input.def-number-input.safari_only button:before,
                .number-input.def-number-input.safari_only button:after {
                margin-top: -.3rem;
                }
            }
            }
    </style>
    </body>
</html>