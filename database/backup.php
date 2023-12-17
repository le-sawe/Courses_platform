<?php
include "../tools/php/initial.php";
Check_auth([1]);
  if($dev_mode){
    $command = "C:/xampp/mysql/bin/mysqldump --user=".$username." --password=".$password." -h localhost ".$table_name." > ../tools/backup/courses".date("Y-m-d")."db.sql";
    exec($command);
  }else{
    $command = "sudo mysqldump --user=".$username." --password=".$password." -h localhost ".$table_name." > /sql_backup/".date("Y-m-d")."db.sql";
    exec($command);
  echo $command ;
  }
?>