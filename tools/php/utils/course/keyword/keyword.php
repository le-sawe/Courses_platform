<?php 
// require model.php
// require string.php
Class course_keyword{
    public $conn ;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function add_and_link($keywords,$course){
        $keyword = new model_access('keyword',array('keyword_id','keyword_score','keyword_word'),$this->conn);
        $keyword_course = new model_access('keyword_course',array('keyword_course_course','keyword_course_keyword'),$this->conn);
        foreach($keywords as $word){
            $word = add_double_apostrophe(strtolower($this->conn -> real_escape_string($word)));

            // insert keyword
            $the_keyword =$keyword->get("WHERE keyword_word =".$word);
            if($the_keyword == false){
                $keyword->insert(array($word),array('keyword_id','keyword_score'));
                $the_keyword_id = $this->conn->insert_id;
                $the_score = 1; // its a new keyword so the initial is 0 score --> after linkup it will be 1
            }else{
                $the_keyword_id = $the_keyword[0]['keyword_id'];
                $the_score =$the_keyword[0]['keyword_score'] +1; // add at the initial value of the keyword a 1
            }
            // linkup keyword
            $keyword_course->insert(array($course,$the_keyword_id));
            // add score 
            $keyword->update(array($the_score),"keyword_id =".$the_keyword_id,array('keyword_score'));
        }
    }
}
?>