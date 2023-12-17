<?php 
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

    function getBase64ImageSize($base64Image){ //return memory size in B, KB, MB
        try{
            $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
            $size_in_kb    = $size_in_bytes / 1024;
            $size_in_mb    = $size_in_kb / 1024;
    
            return $size_in_mb;
        }
        catch(Exception $e){
            return $e;
        }
    }
    
    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }  
            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }   
        }
        return rmdir($dir);
    }

    function delete_file($file_path){
        if (file_exists($file_path)) {
            unlink($file_path);
         }
    }
?>