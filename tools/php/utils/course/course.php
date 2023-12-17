<?php 
// we need string.php , model.php , some time member.php 
Class course{
    public $course_id;
    public $conn;
    public $course;


    public function __construct($conn,$course_id=null){
        $this->conn = $conn;
        if($course_id !=null){
            $this->course_id = $course_id;
            $this->refresh_the_course();
        }
    }

    public function refresh_the_course(){
        $this_course = new model_access("course",array("*"),$this->conn);
        $this_course =$this_course ->get("WHERE course_id = ".$this->course_id);
        if ($this_course != false){
            $this->course = $this_course[0];
        }
    }

    public function refresh_stat(){
         
        // get likes
            $likes = new model_access("liked_course",array("COUNT(liked_course_id)"),$this->conn);
            $likes_count = $likes->get("WHERE liked_course_course =".$this->course_id);
            if($likes_count !=false){
                $likes_count =$likes_count[0]['COUNT(liked_course_id)'];
            }
        
        // get comments 
            $comments = new model_access("comment_course",array("COUNT(comment_course_id)"),$this->conn);
            $comments_count = $comments->get("WHERE comment_course_course =".$this->course_id);
            if($comments_count !=false){
                $comments_count= $comments_count[0]['COUNT(comment_course_id)'];
            }
        // get saves 
            $saves = new model_access("save_course",array("COUNT(save_course_id)"),$this->conn);
            $saves_count = $saves->get("WHERE save_course_summary_course=".$this->course_id);
            if($saves_count !=false){
                $saves_count= $saves_count[0]['COUNT(save_course_id)'];
            }
        // get views 
            $views = new model_access("view",array("COUNT(view_id)"),$this->conn);
            $views_count = $views->get("WHERE view_course=".$this->course_id);
            if($views_count !=false){
                $views_count= $views_count[0]['COUNT(view_id)'];
            }
        // Score calcule
            if(($views_count or $saves_count or $comments_count or $likes_count) == false){
                $score=false;
            }else{
                $score = $views_count + $likes_count *2 + $comments_count * 3 + $saves_count *4; 
                
            }

        if($score != false){
            // update 
            $course = new model_access('course',array(""),$this->conn);
            $course->update(array($likes_count,$comments_count,$saves_count,$views_count,$score),"course_id =".$this->course_id ,array("course_likes","course_comments","course_saves","course_views","course_score"));
            $this->refresh_the_course();
        }
    }

    public function delete(){ 
        global $media_dir;
        $delete_course_sql = "DELETE FROM course WHERE course_id = ".$this->course_id;
        $delete_course_files_sql = "DELETE FROM course_file WHERE course_file_course = ".$this->course_id;
        $delete_course_comments_sql = "DELETE FROM comment_course WHERE comment_course_course = ".$this->course_id;
        $delete_course_likes_sql = "DELETE FROM liked_course WHERE liked_course_course = ".$this->course_id;
        $this->conn->query($delete_course_sql);
        $this->conn->query($delete_course_files_sql);
        $this->conn->query($delete_course_comments_sql);
        $this->conn->query($delete_course_likes_sql);
        // Delete Files
        deleteDirectory($media_dir."course_files/".$this->course_id);
        // refresh member stat
        $the_member =new member($this->conn);
        $the_member->refresh_stat($this->course['course_made_by']);

    }

    public function print_courses($data,$mode=0){
        global $base_url;
        $light_mode = false ;
        $Courses_mode = false ;
        if($data !=false){
            foreach($data as $row ){
                 //row to print
                 if(!empty($row['university_material_title'])){
                    $uni_syntax='<h5 class="card-title">'.$row['university_material_title'].' , '.$row['university_material_code'].' </h5>';
                }else{
                    $uni_syntax ="";
                }
                if($mode==  1){
                    $edit_syntax_icons ='
                    <a href="'.$base_url.'course/mycourses/modify.php?course='.$row['course_id'].'" class="mx-auto my-0 btn btn-black btn-bl rounded waves-effect waves-light"><i class="fas fa-pen"></i></a>
                    <a  data-toggle="modal" data-target="#delete_course_'.$row['course_id'].'" class="mx-auto my-0 btn btn-black btn-bl rounded waves-effect waves-light"><i class="fas fa-trash"></i></a>
               ';
                    $edit_syntax='
                    
                    <div class="modal fade " id="delete_course_'.$row['course_id'].'" tabindex="-1" role="dialog" aria-labelledby="delete course" >
                        <div class="modal-dialog modal-notify  " role="document">
                            <!-- Content -->
                            <div class="modal-content black ">
                                <!-- Header -->
                                <div class="modal-header red darken-4 white-text">
                                    <p class="heading white-text">Delete Course :'.$row['course_title'].'</p>
    
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" class="white-textgit ">×</span>
                                    </button>
                                </div>
    
                                <!-- Body -->
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <p></p>
                                            <p class="text-center text-white"><i class="fas fa-trash fa-7x "></i></p>
                                        </div>
    
                                        <div class="col-9">
                                            <p class="text-white">
                                            <strong>Are you sure that you want to delete the course with the following detail :</strong>
                                            <br><span class="ml-2"> # Title :'.$row['course_title'].'</span>
                                            <br><span class="ml-2"> # Description : <small>'.$row['course_description'].'</small></span>
                                            <br><span class="ml-2"> # Sub Material : '.$row['sub_material_title'].'</span>
                                            <br><span class="ml-2"> # Views : '.$row['course_views'].'</span>
                                            <br><span class="ml-2"> # Likes : '.$row['course_likes'].'</span>
                                            <br><span class="ml-2"> # Comments : '.$row['course_comments'].'</span>
                                            <br><span class="ml-2"> # Create Date  : '.$row['course_create_date'].'</span>
    
                                            </p>
                                            <hr>
                                            <form method="post" action="index.php">
                                                <input type="hidden" name="course" value="'.$row['course_id'].'">
                                                <button type="submit" class="btn btn-outline-white rounded mx-auto">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ';
                }elseif($mode==  2){
                    $light_mode = true ;
                }elseif($mode==  3){
                    $Courses_mode = true ;
                }else{
                    $edit_syntax="";
                    $edit_syntax_icons="";
                }
                if($light_mode){
                    echo '  
                    <div class="card m-2 z-depth-2 w-100" style="max-width:500px;" >
                        <a href="'.$base_url.'course/detail.php?course='.$row['course_id'].'" class=" text-dark p-3">
                            <h3 class="mx-auto">'.$row['course_title'].' </h3>
                        </a>
                        <div class="rounded-bottom black text-center p-3 mt-2">
                            <ul class="list-unstyled list-inline m-0 font-small">
                                <li class="list-inline-item pr-2 white-text"> '.date("Y-m-d", strtotime($row['course_create_date'])).' </li>
                                <li class="list-inline-item pr-2"><a href="#" class="white-text"><i class="fas fa-eye"></i> '.$row['course_views'].' </a></li>
                                <li class="list-inline-item pr-2"><a href="#" class="white-text"><i class="far fa-thumbs-up"></i>  '.$row['course_likes'].'</a></li>
                                <li class="list-inline-item pr-2"><a href="#" class="white-text"><i class="far fa-comment"></i>  '.$row['course_comments'].'</a></li>
                            </ul>
                        </div>
                    </div>
                ';
                }elseif($mode==  3){
                    echo '  
                    <div class="card m-2 z-depth-2 w-100" style="max-width:500px;" >
                        <div class="  p-3">
                            <h3 class="mx-auto">'.$row['course_title'].' </h3>
                        </div>
                        <hr class="mx-2 mt-0" >
                       <div class="card-body px-2 py-0 mt-1">
                           <div class="">
                               <h5 class="card-title">'.$row['language_title'].' , '.$row['sub_material_title'].' </h5>
                           </div>                 
                        </div>
                        <div class="justify-content-around d-flex row">
                           <a  class="mx-auto my-0 btn btn-black btn-bl rounded waves-effect waves-light " href="'.$base_url.'course/Courses_detail.php?course='.$row['course_id'].'">Detail <i class="fas fa-chevron-right "></i></a>
                       </div>
   
                        <div class="rounded-bottom black text-center p-3 mt-2">
                            <ul class="list-unstyled list-inline m-0 font-small">
                                <li class="list-inline-item pr-2 white-text"> '.date("Y-m-d", strtotime($row['course_create_date'])).' </li>
                            </ul>
                        </div>
                    </div>
                ';
                }else{
                    echo '  
                     <div class="card m-2 z-depth-2 w-100" style="max-width:500px;" >
                         <div class="  p-3">
                             <h3 class="mx-auto">'.$row['course_title'].' </h3>
                         </div>
                         <hr class="mx-2 mt-0" >
                        <div class="card-body px-2 py-0 mt-1">
                            <div class="">
                                <h5 class="card-title">'.$row['language_title'].' , '.$row['sub_material_title'].' </h5>
                                '.$uni_syntax.'
                            </div>                 
                         </div>
                         '.$edit_syntax.'
                         <div class="justify-content-around d-flex row">
                            '.$edit_syntax_icons.'
                            <a  class="mx-auto my-0 btn btn-black btn-bl rounded waves-effect waves-light " href="'.$base_url.'course/detail.php?course='.$row['course_id'].'"><i class="fas fa-chevron-right "></i></a>
                        </div>
    
                         <div class="rounded-bottom black text-center p-3 mt-2">
                             <ul class="list-unstyled list-inline m-0 font-small">
                                 <li class="list-inline-item pr-2 white-text"> '.date("Y-m-d", strtotime($row['course_create_date'])).' </li>
                                 <li class="list-inline-item pr-2"><a href="#" class="white-text"><i class="fas fa-eye"></i> '.$row['course_views'].' </a></li>
                                 <li class="list-inline-item pr-2"><a href="#" class="white-text"><i class="far fa-thumbs-up"></i>  '.$row['course_likes'].'</a></li>
                                 <li class="list-inline-item pr-2"><a href="#" class="white-text"><i class="far fa-comment"></i>  '.$row['course_comments'].'</a></li>
                             </ul>
                         </div>
                     </div>
                 ';
                }
             }
             return true ;
         }else{return false;}
    }
    
    public function toggle_save($member_id){
        $save = new model_access('save_course',array('save_course_id','save_course_member','save_course_summary_course'),$this->conn);

        if($save->get("WHERE save_course_member = ".$member_id." AND save_course_summary_course =".$this->course_id."") !=false){
            // if the member already have saved this course
            // unsave the course 
            $save ->delete("save_course_member = ".$member_id." AND save_course_summary_course =".$this->course_id."");
            // refresh course stat
            $this->refresh_stat();
        }else{
            // if the course not saved by the member 
            // save the course 
            $save ->insert(array($member_id,$this->course_id),array('save_course_id'));
            // refresh course stat
            $this->refresh_stat();
        }
    }

    public function toggle_like($member_id){
        $like = new model_access('liked_course',array('liked_course_id','liked_course_member','liked_course_course'),$this->conn);
        if($like->get("WHERE liked_course_member = ".$member_id." AND liked_course_course =".$this->course_id."") !=false){
            // if the course already liked by the member 
            // unlike the course
            $like ->delete("liked_course_member = ".$member_id." AND liked_course_course =".$this->course_id."");
            $this->refresh_stat();
        }else{
            // if the course not liked by the course 
            // like the course 
            $like ->insert(array($member_id,$this->course_id),array('liked_course_id'));
            $this->refresh_stat();
        }
        // refresh member stat
        $the_member =new member($this->conn);
        $the_member->refresh_stat($this->course['course_made_by']);
    }
    
    public function get_likes(){
        $this->refresh_stat();
        $this->refresh_the_course();
        return $this->course['course_likes'];
    }

    public function add_comment($member_id,$content){ 
        // add comment 
        $comment = new model_access('comment_course',array('comment_course_member','comment_course_course','comment_course_text'),$this->conn);
        $comment->insert(array($member_id,$this->course_id,add_double_apostrophe($this->conn->real_escape_string($content))));
        $this->refresh_stat();
    }

    public function delete_comment($comment_id,$member_id){
        $comment_model = new model_access('comment_course',array('comment_course_member'),$this->conn);
        // check if this comment is for this course and the one who made this course is the same who want to delete it 
        $the_comment = $comment_model->get("WHERE comment_course_course = ".$this->course_id." AND comment_course_id = ".$comment_id." AND comment_course_member =".$member_id);

        if($the_comment !=false){
            // if all ok (member relation ok , and the course relation ok)
            $comment_model->delete('comment_course_id = '.$comment_id);
            $this->refresh_stat();
        }


    }

    public function get_comments(){
        global $media_url;
        $comment = new model_access('comment_course',array('comment_course_id','comment_course.comment_course_text' , 'comment_course.comment_course_date', 'member.member_username' , 'member.member_profile_url'),$this->conn);
        $comment = $comment->get("INNER JOIN member ON member.member_id = comment_course.comment_course_member WHERE comment_course_course = ".$this->course_id."  ORDER BY comment_course.comment_course_date ASC ;");

        $comment_result_to_json = array();
        if($comment !=false){
            foreach($comment as $row){
                // photo by google or on ower servers
                if(!startsWith($row['member_profile_url'],"http")){
                    $row['member_profile_url'] = $media_url.''.$row['member_profile_url'];
                }
                $comment_result_to_json[] =array($row['comment_course_text'],date("Y-m-d", strtotime($row['comment_course_date'])),$row['member_username'],$row['member_profile_url'],$row['comment_course_id']);
            }
        }
        // text , date , made_by username ,made_by profile , id
        return $comment_result_to_json;
    }
}
?>