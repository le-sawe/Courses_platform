<?php 

    Class form{
        public $result;
        public function __construct($header=null) {
            if(empty($header)){
                $header = "method = 'post' action='".$_SERVER['PHP_SELF']."' enctype= multipart/form-data";
            }
            $this->result = "<form ".$header." >";
        }

        public function add_text($name,$header=null,$value=null,$label=null){
            if (empty($header)){$header=' class="form-control"';}
            $this->result .= "
                <div class='form-row'>
                    <div class ='md-form'>
                        <input type='text' name='".$name."' id='".$name."' ".$header." value = '".$value."'></input>
                        <label for='".$name."'>".$label."</label>
                    </div>
                </div>
            ";         
        }

        public function add_text_area($name,$header=null,$value=null,$label=null){
            if (empty($header)){$header='class="md-textarea form-control"';}
            $this->result .= "
                <div class ='form-row '>
                    <div class ='md-form '>
                        <textarea name='".$name."' id='".$name."' ".$header." >".$value."</textarea>
                        <label for='".$name."'>".$label."</label>
                    </div>
                </div>
            ";         
        }

        public function add_select($name,$values,$options,$header=null,$selected=null,$label=null){  
            if (empty($header)){$header='class="mdb-select md-form colorful-select dropdown-dark "';}
            $this->result .= "
                <div class ='form-row '>
                    <select name='".$name."' id='".$name."' ".$header." >";
                        $counter =0 ;
                        foreach($values as $value){
                            $selected_syntax ='';
                            if(!empty($selected) && $value == $selected){$selected_syntax="selected";}
                            $this->result .="<option ".$selected_syntax." value=".$value." >".$options[$counter]."</selected> ";
                            $counter ++;
                        }
            $this->result .= "
                    </select>
                    <label for='".$name."'>".$label."</label>
                </div>
            ";  
        }

        public function add_select_plus($model_name,$value_index,$option_index,$conn,$header=null,$selected=null,$label=null){
            $model = new model_access($model_name ,array($value_index,$option_index),$conn);
            $model_objects =$model->get();  
            $value=array();
            $options=array();
            foreach($model_objects as $object){
                array_push($value,$object[$value_index]);
                array_push($options,$object[$option_index]);
            }
            $this->add_select($model_name,$value,$options,$header,$selected,$label=null);
        }
        public function add_file($name,$label=null){
            $this->result .=' 
                <div class="file-field form-row">
                    <div class="btn btn-black btn-sm float-left">
                        <span>Choose file</span>
                        <input name="'.$name.'" " type="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input name="'.$name.'" class="file-path validate" value="" type="text" placeholder="'.$label.'">
                    </div>
                </div>';
        }

        public function add_date($name,$header=null,$value=null,$label=null){
            if(empty($header)){$header='placeholder="Selected date"';}
            $this->result .=' 
            <div class="form-row">
                <div class="md-form ">
                    <input  id = '.$name.' '.$header.' type="text" data-value="'.date('Y-m-d',strtotime($value)).'"  name = '.$name.' class="form-control datepicker">
                    <label  for = '.$name.' class="active"> '.$label.'</label>
                </div>
            </div>
            ';
        }

        public function add_check($name,$header=null,$checked=false,$label=null){
            if(empty($header)){$header='class="form-check-input"';}
            if($checked){$checked ='checked';}else{$checked='';}
            $this->result .='
            <div class="form-row ">
                <div class=" form-check">
                    <input type="checkbox" '.$header.' id="'.$name.'" name="'.$name.'" '.$checked.'>
                    <label class="form-check-label" for="'.$name.'">'.$label.'</label>
                </div>
            </div>
            ';
        }

        public function add_hidden($name,$value){
            $this->result .="<br>
            <input type='hidden' name=".$name." value = ".$value.">
            ";
        }
        public function add_html($value){
            $this->result .=$value;
        }
        public function touch(){
            $this->result .='
            <button type="submit" class=" btn btn-black  ">Submit</button><br>
            </form>';
            echo $this->result;
        }
    }

    function table_result($data,$fields,$model_name=null){
  
        echo '<table class="table">
        <thead class="black white-text">
            <tr>';
            // print fields name
            foreach($fields as $value){
               
                echo '<th scope="col">'.$value.'</th>';
            } 
            echo'
            <th scope="col text-danger">Delete</th>
            <th scope="col text-warning">Modify</th>
            </tr>
        </thead>
        <tbody>
        ';
        foreach($data as $row) {
            echo '<tr>';
            $first_round=true;
            foreach($row as $field_name => $value){
                if($first_round){$identifier = $value;$first_round=false;$the_field=$field_name;}
                echo '<td>'.$value.'</td>';
            }
            
            echo '
            <td scope="col"><form method="post" action="'.$_SERVER['PHP_SELF'].'" ><input type="hidden" name="field" value="'.$the_field.'"><input type="hidden" name="model" value="'.$model_name.'"> <input type="hidden" name="id" value="'.$identifier.'"> <button type="submit" class="btn btn-danger" ">Delete</button></form></td>
            <td scope="col"><form method="get" action="modify.php" ><input type="hidden" name="model" value="'.$model_name.'"> <input type="hidden" name="id" value="'.$identifier.'"> <button type="submit" class="btn btn-warning" ">Modify</button></form></td>
            </tr>';
            }
        echo '</tbody>
        </table>';
    }

    function upload_file($path,$name){
        create_path($path);
        move_uploaded_file($_FILES[$name]["tmp_name"],$path.$_FILES[$name]['name']);
    }

    function create_path($path) {
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = create_path($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }

  
?>