<?php 

Class model_access {

    public $model_name ;
    public $model_fields;
    public $conn;
    public $data;
    public $additional_fields;

    public function __construct($model_name, $model_fields,$conn) {
        $this->model_name = $model_name;
        $this->model_fields = $model_fields;
        $this->conn = $conn;
    }
    
    public function get($option=null,$additional_fields=null,$exception_fields=null,$print_sql=null){
        $result =array();
        $get_sql = "SELECT " ;
        //Print Fields
        foreach($this->model_fields as $field){
            if(empty($exception_fields) || !in_array($field,$exception_fields)){
                $get_sql .= $field." ,";
            }
        }
        // Print additional fields
        if(!empty($additional_fields)){
            $this->additional_fields = $additional_fields;
            foreach($additional_fields as $field){
                $get_sql .= $field." ,";
            }
        }
        $get_sql=substr($get_sql, 0, -1); // remove the comma
        $get_sql .= " FROM  ".$this->model_name." "; // set the table name
        // add optoin if exist
        if(!empty($option)){
            $get_sql .= $option ;
        }
        //Get the query
        $get_result = $this->conn ->query($get_sql);
        if(!empty($print_sql)){
            Add_Message($get_sql,0);
        }
        //fetch result
        if($get_result->num_rows > 0){
            while($row = $get_result->fetch_assoc()){
                array_push($result,$row); // put the result in the array
            }    
            $this->data = $result ; // stock the result in the public variable
            
            return $result; // return the result
            
        }else{
            return false;
        }
    }

    public function insert($values, $exception=null,$mode=1,$print_sql=null){ // insert an object in the model (values , mode 1 --> single object 2---> multiple object , fields exception )
        $insert_sql = "INSERT INTO ".$this->model_name." (";
        // add fields
        foreach($this->model_fields as $field){
            if(empty($exception) || !in_array($field,$exception)){
                $insert_sql .= $field." ,";
            }
        }
        $insert_sql=substr($insert_sql, 0, -1);//remove the last comma
        $insert_sql .= ') VALUES';
        // insert values
        if($mode == 1){ //single insert
            $insert_sql .= '(';
            foreach($values as $value){
                $insert_sql .= $value." ,";
            }
            $insert_sql=substr($insert_sql, 0, -1); // remove the last comma
            $insert_sql .= ") ;";
            if(!empty($print_sql)){
                Add_Message($insert_sql,0);
            }
            if($this->conn->query($insert_sql)=== TRUE){// insert the data
                return true;
            }else{
                return false;
            }
        }    
    }

    public function update($values,$condition,$fields,$print_sql=null){ // update object in the model (values , condition to find the object , fields to be updated)
        $update_sql = "UPDATE ".$this->model_name." SET ";
        $counter =0 ;
        // printing values and fields
        foreach($fields as $field){
            $update_sql .= $field . " = ".$values[$counter]." ,";
            $counter ++;
        } 
        $update_sql = substr($update_sql,0,-1); // remove comma
        $update_sql .=" WHERE ".$condition." ;"; // add the condition
        if(!empty($print_sql)){
            Add_Message($update_sql,0);
        }
        $this->conn->query($update_sql);// update the data
    }

    public function delete($condition,$print_sql=null){
        $delete_sql = "DELETE FROM ".$this->model_name . " WHERE ".$condition." ;";
        if(!empty($print_sql)){
            Add_Message($delete_sql,0);
        }
        $this->conn->query($delete_sql);
    }
}
?>