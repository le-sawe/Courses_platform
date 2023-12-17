<?php 
include "../tools/php/initial.php";




// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


//Create tabels

// Create connection
$conn = new mysqli($servername, $username, $password, 'supafmyd_course_db');
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// auth_logs table
$sql = "CREATE TABLE auth_logs (
    auth_logs_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auth_logs_ip VARCHAR(30) NOT NULL,
    auth_logs_member INT(6) NOT NULL,
    auth_logs_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
)";
 if ($conn->query($sql) === TRUE) {
    echo "Table auth_logs created successfully <br> <br>";
  } else {
    echo "Error creating table: " . $conn->error ."<br>";
}


// university table
$sql = "CREATE TABLE university (
    university_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_name VARCHAR(60) NOT NULL,
    university_profile_url VARCHAR(2083) DEFAULT 'profile/tenji_light.png' 
)";
 if ($conn->query($sql) === TRUE) {
    echo "Table university created successfully <br> <br>";
  } else {
    echo "Error creating table: " . $conn->error ."<br>";
}
// university_material table
$sql = "CREATE TABLE university_material (
    university_material_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    university_material_title VARCHAR(60) NOT NULL,
    university_material_code VARCHAR(30) NOT NULL,
    university_material_university INT(6)  NOT NULL ,
    university_material_sub_material INT(6)  NOT NULL 
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table university_material created successfully <br> <br>";
    } else {
        echo "Error creating table: " . $conn->error ."<br>";
    }
// member table
$sql = "CREATE TABLE member (
    member_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_username VARCHAR(30) UNIQUE NOT NULL,
    member_name VARCHAR(30) NOT NULL,
    member_pass VARCHAR(2083) ,
    member_email VARCHAR(320) UNIQUE,
    member_type INT(6) NOT NULL,
    member_phone_number VARCHAR(16) UNIQUE DEFAULT NULL,
    member_birth_date DATETIME DEFAULT NULL ,
    member_profile_url VARCHAR(2083) DEFAULT 'profile/tenji_light.png' ,
    member_verified BOOLEAN  NOT NULL DEFAULT FALSE ,
    member_score INT(9) DEFAULT 0,
    member_likes INT(9) DEFAULT 0,
    member_email_verified BOOLEAN  NOT NULL DEFAULT FALSE ,
    member_create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
    )";
    //member_latlon  POINT ,
    if ($conn->query($sql) === TRUE) {
      echo "Table member created successfully <br> <br>";
    } else {
      echo "Error creating table: " . $conn->error ."<br>";
    }
    
    // email_verification table
    $sql = "CREATE TABLE email_verification (
    email_verification_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email_verification_member INT(6) NOT NULL,
    email_verification_code VARCHAR(2083) NOT NULL,
    email_verification_code_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table email_verification created successfully <br> <br>";
    } else {
        echo "Error creating table: " . $conn->error ."<br>";
    }
    
    // pass_verification table
    $sql = "CREATE TABLE pass_verification (
    pass_verification_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pass_verification_member INT(6) NOT NULL,
    pass_verification_code VARCHAR(2083) NOT NULL,
    pass_verification_code_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table pass_verification created successfully <br> <br>";
    } else {
        echo "Error creating table: " . $conn->error ."<br>";
    }

    // member_type table
    $sql = "CREATE TABLE member_type (
    member_type_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_type_title VARCHAR(30) NOT NULL
    
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table member_type created successfully <br> <br>";
    } else {
        echo "Error creating table: " . $conn->error ."<br>";
    }
    
    // language table
    $sql = "CREATE TABLE language (
    language_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    language_title VARCHAR(30) NOT NULL
    
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table language created successfully <br> <br>";
    } else {
        echo "Error creating table: " . $conn->error ."<br>";
    }
    
    
    // material table
    $sql = "CREATE TABLE material (
        material_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        material_supported BOOLEAN  NOT NULL,
        material_title VARCHAR(30) NOT NULL
        )";
        
        if ($conn->query($sql) === TRUE) {
          echo "Table material created successfully <br> <br>";
        } else {
          echo "Error creating table: " . $conn->error ."<br>";
        }

    // sub_material table
        $sql = "CREATE TABLE sub_material (
        sub_material_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        sub_material_title VARCHAR(30) NOT NULL,
        sub_material_supported BOOLEAN  NOT NULL,
        sub_material_material INT(6)  NOT NULL
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table sub_material created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }
        
    // liked_sub_material table
        $sql = "CREATE TABLE liked_sub_material (
        liked_sub_material_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        liked_sub_material_material INT(6)  NOT NULL,
        liked_sub_material_member INT(6)  NOT NULL,
        liked_sub_material_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table liked_sub_material created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }
        
        
    // liked_course table
        $sql = "CREATE TABLE liked_course (
        liked_course_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        liked_course_course INT(6)  NOT NULL,
        liked_course_member INT(6)  NOT NULL,
        liked_course_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table liked_course created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }   
        
    // comment_course table
        $sql = "CREATE TABLE comment_course (
        comment_course_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        comment_course_text VARCHAR(2083) NOT NULL,
        comment_course_course INT(6)  NOT NULL,
        comment_course_member INT(6)  NOT NULL,
        comment_course_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table comment_course created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }
        
    // course table
        $sql = "CREATE TABLE course (
        course_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        course_title VARCHAR(2083) NOT NULL,
        course_description VARCHAR(2083) NOT NULL,
        course_type INT(6) DEFAULT 1  NOT NULL,
        course_sub_material INT(6)  NOT NULL,
        course_uni_material INT(6)  ,
        course_language INT(6)  NOT NULL,
        course_made_by INT(6)  NOT NULL,
        course_likes INT(9) DEFAULT 0,
        course_comments INT(9) DEFAULT 0,
        course_saves INT(9) DEFAULT 0,
        course_views INT(9) DEFAULT 0,
        course_score INT(9) DEFAULT 0,
        course_verified BOOLEAN  NOT NULL,
        course_hide BOOLEAN  DEFAULT false NOT NULL,
        course_create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table course created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }

    // course_type table
    $sql = "CREATE TABLE course_type (
        course_type_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        course_type_title VARCHAR(30) NOT NULL
        )";
        
        if ($conn->query($sql) === TRUE) {
          echo "Table course_type created successfully <br> <br>";
        } else {
          echo "Error creating table: " . $conn->error ."<br>";
        }
        
    // course_file table
        $sql = "CREATE TABLE course_file (
        course_file_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        course_file_course INT(6)  NOT NULL,
        course_file_url TEXT  NOT NULL     
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table course_file created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }

    // keyword table
        $sql = "CREATE TABLE keyword (
        keyword_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        keyword_score INT(9) DEFAULT 0,
        keyword_word VARCHAR(30)  NOT NULL  UNIQUE
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table keyword created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }

    // keyword_course table
        $sql = "CREATE TABLE keyword_course (
        keyword_course_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        keyword_course_course INT(6)  NOT NULL,
        keyword_course_keyword INT(6)  NOT NULL
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table keyword created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }

    // report table
        $sql = "CREATE TABLE report (
        report_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        report_member INT(6)  NOT NULL,
        report_content VARCHAR(2083) NOT NULL,
        report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP   
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table report created successfully <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }
    
    // report_type table
        $sql = "CREATE TABLE report_type (
            report_type_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            report_type_title VARCHAR(30) NOT NULL
            )";
            
            if ($conn->query($sql) === TRUE) {
              echo "Table report_type created successfully <br> <br>";
            } else {
              echo "Error creating table: " . $conn->error ."<br>";
            }
    
    // view table
    $sql = "CREATE TABLE view (
        view_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        view_member INT(6)  NOT NULL,
        view_course INT(6)  NOT NULL  ,
        view_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP   
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table view created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }

    // save_course table
    $sql = "CREATE TABLE save_course (
        save_course_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        save_course_member INT(6)  NOT NULL,
        save_course_summary_course INT(6)  NOT NULL  ,
        save_course_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP   
        )";
        
        if ($conn->query($sql) === TRUE) {
            echo "Table save_course created successfully <br> <br>";
        } else {
            echo "Error creating table: " . $conn->error ."<br>";
        }



        //Create initial data ---------------------------------------------------------------------------

        // member_type
            $sql = "INSERT INTO member_type (member_type_id, member_type_title)
            VALUES  (1, 'admin' ),
                    (2, 'member' );
            ";

            if ($conn->query($sql) === TRUE) {
            echo "New record created successfully  in member_type table <br> ";
            } else {
            echo "Error: " . $sql . "<br>" . $conn->error ."<br>";
            }

        // courses_type
            $sql = "INSERT INTO course_type (course_type_id, course_type_title)
            VALUES  (1, 'OTHERS' );
            ";

            if ($conn->query($sql) === TRUE) {
            echo "New record created successfully  in course_type table <br> ";
            } else {
            echo "Error: " . $sql . "<br>" . $conn->error ."<br>";
            }

        // member
            $sql = "INSERT INTO member ( member_name ,member_username,member_email,member_pass,member_type ,member_phone_number,member_email_verified)
            VALUES  ('mohamad','omencodes', 'omencodes@gmail.com','".password_hash('touchworldo@4$Alex', PASSWORD_DEFAULT)."',1,'71 429 509',TRUE );
            
            ";

            if ($conn->query($sql) === TRUE) {
            echo "New record created successfully in member table  <br> ";
            } else {
            echo "Error: " . $sql . "<br>" . $conn->error ."<br>";
            }
    
        // language
            $sql = "INSERT INTO language ( language_title )
            VALUES ('English'),
            ('Francaise');
            ";

            if ($conn->query($sql) === TRUE) {
            echo "New record created successfully  in language table <br>";
            } else {
            echo "Error: " . $sql . "<br>" . $conn->error ."<br>";
            }
        ?>