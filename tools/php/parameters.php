<?php 
$para_name_max_length =30; // Used in the name validation
$para_name_min_length =2; // Used in the name validation
$para_username_max_length =20; // Used in the name validation
$para_username_min_length =4; // Used in the name validation
$para_password_max_length= 30; // Used in the password validation
$para_password_min_length= 8; // Used in the password validation
$para_phone_number_max_length= 20;// Used in the phone number validatoin
$para_phone_number_min_length= 6;// Used in the phone number validatoin
$mk_date_min=mktime(0, 0, 0, date("m"), date("d"), date("Y")-100);
$para_birth_date_min = date("Y-m-d", $mk_date_min);//Used in the birth_date validation
$mk_date_max=mktime(0, 0, 0, date("m"), date("d"), date("Y")-8);
$para_birth_date_max = date("Y-m-d", $mk_date_max);//Used in the birth_date validation
$para_profile_max_size  = 4097152;//Used in profile size validation
$para_profile_type_acceptable = array(//Used in profil type validation
    'image/jpeg',
    'image/jpg',
    'image/png'
);
$para_file_max_size  = 40097152;//Used in file size validation
$para_file_type_acceptable = array(//Used in file type validation
    'application/pdf',
);
$para_keyword_max_length = 30 ;//Used in keyword validation
$para_keyword_min_length = 1 ;//Used in keyword validation
$para_title_max_length = 50 ;//Used in title validation
$para_title_min_length = 4 ;//Used in title validation
$para_description_max_length = 2083 ;//Used in description validation
$para_description_min_length = 0 ;//Used in description validation



?>