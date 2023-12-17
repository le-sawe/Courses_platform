# Courses
**Courses** is platform serves as a versatile hub for knowledge sharing and learning opportunities.
Users can upload and access a diverse courses in PDF format, fostering an inclusive environment for educational exploration.



## Key Features:
1. Course Uploads: Individuals can seamlessly share their courses, enabling a rich database of diverse learning materials.
2. University Section: A dedicated segment caters to university-related materials, ensuring a focused repository aligned with academic curricula.
3. Keyword-Based Search: The platform employs a sophisticated keyword system that simplifies course discovery and suggests popular courses based on a scoring mechanism tied to each keyword.
4. User Interactions: Users can engage with courses by liking, saving, and commenting, fostering a collaborative learning community.
5. Security Measures: To maintain a secure environment, we've integrated Google reCAPTCHA and OAuth, ensuring authentication and safeguarding against potential threats.

**The project took months of daily individual work in 2021.**
**It was hosted recently as tenji.org, but it didn't success do to the lack of content in the platform.**
**Now it is opensource you can access it however you want.**

---

## Setup
- tools/php/initial.php:
    - Set developer_mode to true
    - put your rights urls and directories (check for capital letter in the directory path)
    - put the rights parameters for google recaptcha and oath
- run the database/db.php to create the database
- Change if needed the parameters in tools/php/paramters.php
- Setup email settings in php/utils/other/send_email.php
- Change in php/utils/account/member.php the email_content html
- setup your site map at sitemap.php

---

## Dev Feature:
- Message System (error - warning -success messages ...)
- Email verification mechanism
- Recover password mechanism
- Google recaptcha 
- Google oauth (sign in with google)
- Sitemap
- keyword search 
- keyword ranking system (with score (popularity))
- Form class (rendering and structuring form by calling function that create your desired fields)
- Model class (Create read modify and delete from your database in a more efficient and structured way)

---
## Database Structure

- auth_logs :
    - auth_logs_id
    - auth_logs_ip
    - auth_logs_member
    - auth_logs_time

- university :
    - university_id
    - university_name
    - university_profile_url

- university_material :
    - university_material_id
    - university_material_title
    - university_material_code
    - university_material_university
    - university_material_sub_material

- member :
    - member_id
    - member_username
    - member_name
    - member_pass
    - member_email
    - member_type
    - member_phone_number
    - member_birth_date
    - member_profile_url
    - member_verified
    - member_score
    - member_likes
    - member_email_verified
    - member_create_date

- member_type :
    - member_type_id
    - member_type_title

- email_verification :
    - email_verification_id
    - email_verification_member
    - email_verification_code
    - email_verification_code_date

- pass_verification :
    - pass_verification_id
    - pass_verification_member
    - pass_verification_code
    - pass_verification_code_date

- language :
    - language_id
    - language_title

- material :
    - material_id
    - material_title

- sub_material :
    - sub_material_id
    - sub_material_title
    - sub_material_material

- liked_sub_material :
    - liked_sub_material_id
    - liked_sub_material_material
    - liked_sub_material_member
    - liked_sub_material_date


- course :
    - course_id
    - course_title
    - course_description
    - course_type
    - course_sub_material
    - course_uni_material
    - course_language
    - course_made_by
    - course_likes
    - course_comments
    - course_saves
    - course_views
    - course_score
    - course_verified
    - course_hide
    - course_create_date

- course_type :
    - course_type_id
    - course_type_title

- course_file :
    - course_file_id
    - course_file_course
    - course_file_url

- liked_course :
    - liked_course_id
    - liked_course_course
    - liked_course_member
    - liked_course_date

- comment_course :
    - comment_course_id
    - comment_course_text
    - comment_course_course
    - comment_course_member
    - comment_course_date

- view :
    - view_id
    - view_member
    - view_course
    - view_date

- save_course :
    - save_course_id
    - save_course_member
    - save_course_summary_course
    - save_course_date

- keyword :
    - keyword_id
    - keyword_score
    - keyword_word

- keyword_course :
    - keyword_course_id
    - keyword_course_course
    - keyword_course_keyword

- report :
    - report_id
    - report_member
    - report_content
    - report_date

- report :
    - report_type_id
    - report_type_title    

--- 

## Webiste Tree :
- account/
    - auth/
        - proccess_google.php
        - google_redirect.php
        - login.php
        - logout.php
        - signup.php
    - manage/
        - email_verification.php
        - recover_pass.php
    - profile/
        - edit.php
        - index.php
        - upload_image.php
    - settings/
        - index.php
    - tools/like_material/
        - get_likes.php
        - toggle_like_material.php
- admin/
    - models/
        - create.php
        - index.php
        - modify.php
    - tools/
        - form.php
    - index.php
- course/
    - mycourses/
        - add.php
        - modify.php
    - tools/
        - comment/
            - add_comment.php
            - delete_comment.php
            - get_comments.php
        - keyword/
            - keyword_engine.php
        - likes/
            - get_likes.php
            - toggle_like.php
        - save/
            - toggle_save.php
    - course_search.php
- database /
    - backup.php
    - db.php
- error/
    - index.php
- media/
    - course_files/
    - profile/
    - universities/
- people/
    - index.php
- static/
    - css/
    - font/
    - img/
    - js/
- tools/
    - backup/
    - php/
        - essential/
            - footer.php
            - header.php
        - utils/
            - account/
                - google.php
                - member.php
            - course/
                - keyword/
                    - keyword.php
                - mycourses/
                    - courses_js.php
                    - coursetools.php
                - course.php
            - other/
                - files.php
                - model.php
                - redirect.php
                - send_email.php
                - string.php
        - visual/
            - admin_navigation.php
            - footer.php
            - navigation.php
        - initial.php
        - parameters.php
- universities/
    - courses.php
    - index.php
    - materials.php
- home.php
- index.php
    
---
## Detail of the code:

- account/auth/proccess_google.php :
    - no interface page
    - The goal from this page is to proccess the code given from google after sign in with google and redirection
    - it will fetch the user : 
        - email 
        - name
        - profile 
    - then it will sign in if the account already exist on our database or it will sign up with this account 
    - Note that while signing in with google , if the user email is not verified then it will be verified , and if the user have a recover pass request , it will redirect him to change his password

- account/auth/google_redirect_php.php :
    - no interface page 
    - this page will redirect you to sign in with google
- 
- account/auth/login.php :
    - interface page
    - The goal from this page is authenticate the user 
    - the user can login directly if the last time he sign in with checking the remember me box
    - the login proccess is protected with google_recaptcha 
    - the login proccess :
        1. check for empty input 
        2. get the member 
            if we dont find the member :
                if he was trying to sign in with an gmail account we will recommend to him sing up with google
                else we will recomd him to sign up 
        -- if the account was found --
        3. We will try to authenticate 
            if authentification faield :
                we will metion that we know the user and we will show him a link to recover his password
        -- if the auth success -- 
        4. remember me proccess (if the box was checked )
            set cookies (member_email , member_pass)
        5. redirect to the index.php or to the destination before the redirection to login.php

- account/auth/logout.php :
    - no interface page 
    1. remove the session 
    2. remove cookies 
    3. redirect to login

- account/auth/signup.php :
    - interface page 
    - The goal from this page is to create a user 
    - the sign up proccess is protected with google recaptcha
    - sign up proccess :
        1. check for empty input 
        2. save (name and email ) in session for better experience 
        3. validate name by length
        4. validate email by format 
        4. check if the email is already associated with an account 
        5. validate password (two password match , length) 
        6. sign up

- account/mamange/email_verification.php :
    - The goal from this page is to verify the email 
    - it recive the code as get parametre and verify it using member class 

- account/mamange/recover_pass.php :
    - interface page 
    - The goal from this page is to reset the password of user :
    - there are two face for this page :
        - proccess the recover password request :
            1. check for empty value 
            2. password validation (two pass match ,length)
            3. code validation 
            4. proccess the code ( get the member id from it )
            5. get the member 
            6. check if exisit
            7. get member detail (name , email)
            8. get pass verification (according to the code)
            9. check if exisit
            10. update the member (change the password)
            11. delete teh verification email 
            12. send email (Recover password success) 
            13. redirect

        - create a reset password reqeust :
            1. check if the account exist 
            2. get account detail (id , name ,email)
            3. check if we have already send him a code 
            4. genereate verficication code 
            5. send email with the code 
            6. if he have an gmail account we will mention to hime that he can recover his password with signing in with google
    
- account/profile/edit.php :
    - interface page 
    - The goal of this site is to edit the member profile detail 
    - edit name proccess :
        1. validate length 
        2. validate content 
        3. update the name 
    - edit username proccess :
        1. username to lower case 
        2. validate by length 
        3. validate by content
        4. check if already exist 
        5. update 
    - edit birth date :
        1. convert the date 
        2. validate the date by dates limits
        3. update 
    - delete profile :
        1. check if its not the default img 
        2. get profile url 
        3. delete the image
        4. set the profile image as default 
    - edit phone number :
        1. remove spaces 
        2. keep only the numbers 
        3. validate the phone numbe by length 
        4. check if already exist 
        5. update 

- account/profile/index.php :
    - interface page 
    - The goal of this page is to let the user see his work , hid likes , his saves and see his score 
    - You can delete your course in this page 
    - the courses is ordered by date
    - delete course proccess :    
        1. course id validation (numeric , the one who create the course is the same one who want to delete it)
        2. delete 
    - course fields to show :
        - uni_material_title
        - uni_material_code
        - id
        - title
        - description 
        - language
        - sub_material_title
        - material_title
        - made_by
        - date
        - views
        - likes
        - comments

- account/profile/upload_image.php :
    - no interface page 
    - The goal of this page is to change the member profile image
    - The proccess :
        1. get the image data
        2. create image detail (name ,dir ,url)
        3. create path
        3. check size 
        4. upload
        5. delete the old one 
        6. update 

- account/settings/index.php :
    - The goal of this page is to change your password and set materials preferences
    - Change password proccess :
        1. check if empty 
        2. check if old pass match 
        3. check if the two new pass match 
        4. validate password by length 
        5. update pass

- account/tools/like_material/get_likes.php :
    - data interface page 
    - The goal of this page is to get the liked material by the member 
    - it will return the result as list of ids of the sub materials liked by the user 

- account/tools/like_material/toggle_like_material.php :
    - no interface page 
    - The goal of this page is to like or dislike a sub_material by user 
    - proccess :
        1. check if the user have liked this sub materia l
        2. add like , or remove like 

- course/mycourses/add.php :
    - interface page 
    - The goal of this page is to create a course 
    - add course proccess protected by recaptcha
    - proccess :
        - Get request :
            1. check email verification (u must have a verified email to add a course) 
            2. get course types
            3. get langages 
            4. get materials
            5. check uni mode (if this course related to a university material )
                -- if uni mode --
                - get material detail 
        - Post request :
            1. recaptcha
            2. check if uni mode 
                -- if uni mode --
                . get uni material detail 
            3. Validation 1 
                - File validation (file number validation)
                - Keyword validation (lower case ,remove duplicated keyword ,check if empty)
                - Course Detail validation (empty check)
            4. Save initial input
            5. Validation 2 
                - File validation ( its uploaded , size ,type)
                - Keywords validation (length)
                - Detail validation (
                    -  title (length , backslashe)
                    -  description (length , backslashe)
                    -  language (numeric ,positive)
                    -  type (numeric , positive)
                    -  sub_material (numeric , positive) 
                )
            6. Insert Data
                - Insert Course 
                - Get the id of the course 
                - Create view 
                - keyword add and link
                - Insert file
                    - create path
                    - move the file 
                    - stock the file url in the db
            7. unset initial input 
            8. redirect 

- course/mycourses/modify.php :
    - interface page 
    - The goal of this page is to modify a course 
    - This proccess is protected with google recaptcha 
    - The proccess :
        1. Check if email verified 
        2. get types 
        3. get languages 
        4. get materials 
        5. get sub materials 
            -- if its a post request --
            1. recaptcha validation 
            2. check if its uni mode 
                -- if uni mode -- 
                get uni material detail 
            3. get course id 
            4. get the course 
            5. check if the course exist 
            6. check if the course made by the same one who want to modify it 
            7. old files proccess :
                - get teh old files related to the course 
                - check if exsist 
                - get the old files status after modifying (deleted or not)
                    - get old file number 
                    - check if empty 
                    - get the files to be deleted 
            8. old keywords proccess :
                - get the old keywords status after modifying (deleted or not)
                    - get the old keywords related to the course 
                    - check if exist 
                    - check if empty 
                    - get the keywords to be deleted 
            9. check if there is new fils uploaded 
            10. check if there is still at least one file related to the course after the modification 
            11. Validation 1 
                - file validation 1 (empty check)
                - keyword validation 1 (make all keyword to lower case ,remove duplicated , empty check)
                - course detail validation 1 (empty check for {title , desc , lang , sub_mat , type})
            12. Validation 2 
                - file validation 2 ( its uploaded , size , type )
                - keyword validation 2 (length)  
                - detail validation 2 {
                    - title (length , backslashe)
                    - description  (length backslashe )
                    - language (numeric positive )
                    - type (numeric positive )
                    - sub material (numeric positive )
                }
            13. Update data 
                - update course  
                - delete keywords to be deleted 
                - add and link new keywords 
                - delete files 
                - insert files 
                    - create path 
                    - move the file
                    - save file url in db 
            14. redirect 
            -- if its a GET request -- 
            1. get course detail 
            2. get files 
            3. set file limite 
            4. get keywords 
            5. check if the one who want to modify the course is the same who create it 
            6. check if uni mode 
                - get uni material detail 
            
        - Note : uni mode is when the course have a relation to an university material 

- course/tools/comment/add_comment.php :
    - no interface page 
    - The goal of this page is to add a comment related to the course 

- course/tools/comment/delete_comment.php :
    - no interface page 
    - The goal of this page is to check if the one who made the comment is the same who want to delete it , then delete the comment

- course/tools/comment/get_comments.php :
    - data interface page 
    - The goal of this page is to get all the comments related to the course 

- course/tools/keyword/keyword_engine.php :
    - no interface page
    - The goal of this page is to return an autocomplete options for a keyword search 
    - proccess :
        1. get the word 
        2. sql search under this format ( LIKE %the_word%) , order by score and limite to 5 
        3. stock the result in array 
        4. print the array as json 

- course/tools/likes/get_likes.php :
    - data interface page 
    - The goal of this page is to get the number of likes for a course 

- course/tools/likes/toggle_like.php :
    - no interface page 
    - The goal of this page is to like or dislike a course by the member 

- course/tools/save/toggle_save.php :
    - no interface page 
    - The goal of this page is to save or unsave a course by the member 

- course/tools/course_search.php :
    - data interface page 
    - The goal of this page is to return an autocomplete options for a course search 
    - the options are (top 2 keywords , top 2 course title )
    - The proccess :
        1. get the word and filter it (backslashe proccess)
        2. sql search under this format in keywords  ( LIKE %the_word%) , order by score and limite to 2
        3. stock the results in array 
        4. sql search under this format in course ( LIKE %the_word%) , order by score and limite to 2
        5. stock the results in array
        6. print the array as json 

- course/detail.php :
    - interface page 
    - The goal of this page is to visualize the detail , comment , like and save a course 
    - there are a button for refresh the course detail and likes , comments 
    - The proccess :
        1. get the course 
        2. get the files related to the course 
        3. get the keywords related to the course 
        4. check if the course already viewed by the member 
            -- if its not viewed by the membe then -- 
            add view 
        5. check if the member is already liked the course 
        6. check if the member is already saved the course 
    - in the javascipt :
        - print comments function 
        - like function 
        - save function 
        - add like 
        - add save 
        - add comments 
        - get comments 
        - get likes 
        - delete comment
        - reload_l_c (it will refresh the likes and comments )

- course/index.php :
    - interface page 
    - The goal of this page is to see the top 10 courses , filter courses by sub material ,search for a course 
    - The proccess :
        1. get material 
        2. get sub material 
        3. create course model object 
        4. check for filter 
            -- if so --
            - add to the query (where course_sub_material = sub_material id )
        5. check for search 
            --if so -- 
            - get the keywords related to the search 
            - get the courses related to this keywords 
            - get the courses related to the search 
        6. get the top 10 courses

- database/backup.php :
    - no interface page 
    - The goal of this page is to backup sql data 
    - not ready to function now 

- database/db.php :
    - no interface page 
    - The goal of this page is create the database , tabels and some object 
    - If the database already created it will drop it then create new one 

- universities/index.php :
    - interface page 
    - The goal of this page is to visualize the list of universities 

- universities/materials.php :
    - interface page 
    - The goal of this page is to search and visualize the list of materials related to a selected university

- universities/courses.php :
    - interface page 
    - The goal of this page is to add , and visualize the courses related to a selected uni material 

- people/index.php :
    - interface page 
    - The goal of this page is to see the work (courses ) of an user
    - the proccess :
        1. get member detail 
        2. get the list of courses made by the member 

- home.php :
    - interface page 
    - The goal of this page is to show quick links 

- index.php :
    - interface page
    - This is the land page 
    - you can sign in and sign up from it 



---

- essential/footer.php :
    - this code is included on every page have an interface 
    - it contain the javascript scripts and links

- essential/header.php :
    - this code is included on every page have an interface 
    - it contain css style and links , javascript scripts and links

- visual/admin_navigation.php :
    - this code included in the admin section it ths same as the regular navigation but with a link to the model.php in the admin 

- visual/footer.php :
    - this code is included in every page have an interface 
    - its just the footer 

- visual/navigation.php :
    - this code is inculded in every page have an interface
    - it contain quick links for the page and the profile of the user 
    - the links :
        - if signing in
            - home ,discover ,universities , profile ,settings , logout 
        - else 
            - home, discover ,universities , sign in with google ,sign in , sign up   

- initial.php :
    - this code is included in every page
    - it contain :
        - session start 
        - the site name 
        - media url 
        - media dir 
        - static url 
        - base url 
        - base dir 
        - utils dir 
        - utils url 
        - host name 
        - database name 
        - databae password 
        - database server name 
        - database table name 
        - the database connection object 
        - google keys for (recaptcah ,oauth)
        - the check authentification function 
            - check if the user is signing in or not 
            - remind the user to verifiy his email 
        - Message system 
            - add message function 
            - clear message function 
            - print messages function 
                - let the messages be visualized in the site 
        - all good function 
            - check if there a danger message then it will return false 

- parameters.php :
    - this code is included in the sites who have forms and inser data 
    - this code contain the parameters of the fields  (limits)

- utils/account/google.php :
    - this code included in page who use google oauth2 
    - it generate a google client object

- utils/account/member.php :
    - this code is included on most of the pages that have to deal with the member 
    - this class offer the following functions :
        - get :
            - take (id or username or email as parameter) and return the detail of the member 
            
            - the proccess :
                1. get the identifier 
                2. check if its an email 
                3. check if its an id 
                4. check if its an username (if its not email or id then it must be username )
                5. using model class get the member 
                6. return member detail (assoc array)

        - signup :
            - take (name and email and password and google (optional) and picture (optional)) as parameter 
            - and create a acccount 
            - and send email verification 
            - then login 
            - the proccess :
                1. get site name and base_url 
                2. genereate username 
                3. check if the cas is a sign in with google case 
                    -- if so -- 
                    - pass its null 
                    - the verification is true 
                    -- else -- 
                    - add double apostrophe to teh pass 
                    - verification is false 
                    - put a default picture
                4. create account using model class 
                5. email verification 
                    -- if google is false -- 
                    - generate verification code 
                    - create verification email object using model class 
                    - send email with the verification code 
                6. login 

        - login :
            - it take a member identifier as parameter and the member not exsist it will return false 
            - this function it will save the detail of the member on the session 
            - the proccess :
                1. get the member 
                2. save the member detail in the session 
                    - id 
                    - name 
                    - email 
                    - pass 
                    - likes 
                    - type 
                    - username 
                    - phone number 
                    - profile 
                    - birth date 
                    - verified 
                    - email verified 
                    - auth verified 
                3. create a auth_logs object with model class 
                4. return true  

        - auth :
            - it take (identifier and password ) as parameters 
            - it will verify if the password match with the user 
            - if so it will authenticate the user 

        - generate username  :
            - its a private function used by the sign up function
            - it will take email as a parameter and it will return a unique username 
            - the proccess :
                1. the username is the words befor @ and the first character after @ 
                2. if the username is longer than 15 characte then the username is the last 15 character 
                3. add 4 random character to the username 
                4. check if the username already exist 
                    -- if so --
                    - take the last 15 character from the usrname and add a 4 random character 
                5. return the username 

        - verify email :
            - it will take (identifier and code (optimal)) as parameters and return true if the proccess success 
            - the proccess :
                1. get the site name and the base url 
                2. get the member
                3. check if the member is not verified 
                4. get the email verification object (where the member is the condition )
                5. compare the result and the code
                6. update the member situation 
                7. delet eteh email verification object 
                8. send email 
                9. login 
                10. return true 

        - refresh stat :  
            - it will take (identifer ) as parameter 
            - and it will refresh the likes of member 
            - the proccess :
                1. get the memebre 
                2. get the courses maded by the member 
                3. count the likes on those course 
                4. update the member 

- utils/course/keyword/keyword.php :
    - this code inculded in the add and modify course 
    - this class till now , have a only function :
        - add_and_link : it will take list of keywords and course id as parameteres and create the new keywords , link up the keywords with the course 
        - and increase the score of the keywords linked 
        - the proccess :
            - foreach keyword in the list of the keywords 
                1. lowecase and add backslash for the keyword
                2. check if the keyword already exist 
                    -- if we dont find it --
                    - create the keyword 
                    - set the score to 1
                    -- else -- 
                    - get the keyword is 
                    - get the keyword id +1
                3. linkup the keyword
                4. update the score  
        
- utils/course/mycourses/courses_js.php :
    - this code included in the add and modify course 
    - contain javascript functions 
        - File validation (file size validation )
        - add file input :
            - create row div 
            - create the file field div 
            - create the fiedl div 
            - create the file button div 
            - create the span 
            - create the icon 
            - create the file path div 
            - create the text input div 
            - create input  
            - create remvoe file input 
        - remove file input 
        - add keyword input 
            - create the row div 
            - create the keyword field div 
            - create the keyword field div 
            - create prefix icon 
            - create input 
            - create  label 
            - create remove button 
        - remove keyword input 
        - keyword options (show the keywords options when typing )    
        - material select (when selecteing a malterial the sub material list show the only sub material list related to thie material selected) 
        - initial setup (initialize the material select )          
- utils/course/mycourses/coursetools.php :
    - this code is included on the modify and add course 
    - this code conatin a php functions :
        - redirect to logout 
        - redirect to list 
        - get all types (course types )
        - get all languages 
        - get all materials 
        - get all sub materials 
        - get all keywords words 
        - get uni material title cod sub (return array of (uni material title , uni material code , uni material sub material ))

- utils/course/course.php :
    - this code included where we have to deal with courses 
    - Class courses :
        - refresh the course :
            - re get the detail of the course 
        - refresh stat :
            - sync the (likes ,comments , saves , views and score )
            - proccess :
                1. get the count of the likes related to the course 
                2. get the count of the comments related to the course 
                3. get the count of the saves related to the course 
                4. get the count of the views related to the course 
                5. calculate teh score (
                    - view -> 1 point 
                    - like -> 2 point 
                    - comment -> 3 point 
                    - saves -> 4 point
                )
                6. update the course 

        - delete :
            - this function delete the course and all related thing to it 
            - proccess :
                1. delete course sql 
                2. delete course files sql 
                3. delete course comments sql 
                4. delete course likes sql 
                5. delete coure files (directory)
                6. refresh member stat
        
        - print courses :
            - it get courses data and print it with two mode 
            - one who show delete and modify buttons 
            - and the second show only the detail 

        - toggle save :
            - if the member have already saved the course then unsave it ( delete the save object )
            - if the member haven't already saved the course then save it ( insert save object )
            - then refresh course stat

        - toggle like :
            - same as save 

        - add commment :
            - add comment realted to the course (create comment object )
            - then refresh course stat

        - delete comment :
            - delete comment 
            - check if the one who want to delete this comment is the one who create it 
            - refresh course stat 

        - get likes :
            - refresh stat 
            - refresh the course 
            - return the course likes 

        - get comments:
            - get all comments related to the course 
            - return them as array (text ,datae ,username , profile ,comment id)

- utils/other/files.php :
    - this code included where we need to deal with files 
    - this code contain  :
        - upload file function :
            - enter the file name and the path that you want to save the file on it and it will do it to you
        - create path :
            - enter a path and this function will generate directories until it reach the path 
        - det base 64 image size :
            - inter the string and it will return the size of it in mega bites 
        - delete directory :
            - this function will delete all files and directory for the given value 
        - delete fille :
            - if the file exist it will simply unlink it 

- utils/other/model.php 
    - this code is included on all the files where we have to deal with getting , inserting ,updating deleting objects in sql 
    - to construct the model object you have to enter the table name , array of table fields , connection object
    - Class model contain :
        - get :
            - all the parameters all optional 
            - it will return to you the query result of selecting the fields from the table 
            - option :
                - you can add in the option while condtion
                - you can add in the option inner join
            - additional_files 
                - this will add the colums to the result 
                - like adding count ,sum ,max ...
            - exception fields :
                - this will remove the given colum from the result 
                - like when you don't need the id of the object .... 
            - print sql :
                - it will show the sql code as a information message 

        - insert :
            - this function is not completed 
            - this function will take an array of values and insert data to sql relative on the fields given when construction 
            - exception :
                - you can remove field from the insert operation 
                    - usually you except the id field cs its automaticlly genrated 
            - mode :
                - for future ... (switch between multiple inserting and inserting an only one object )

            - print sql ...

        - update :
            - the update function it will take an array of values and array of fields and the condition to where aplly those update 
            - the values are associated only with the fields parameter 
            - condition :
                - id = value ...

            - print_sql .... 

        - delete :
            - just give the condition to locate the object and it will delete it for you 
            - print sql ....  

- utils/other/redirect.php :
    - this code used where we need a redirection after som action .... 
    - this code contain only one function redirect_to and its simple 

- utils/other/send_email.php :
    - we use php mailer to send email using google smtp serveer 
    - smtp debug  0 (the level show more detail when we send email )
    - smtp auth true or false 
    - smtp secure ssl (https )
    - port (google smtp service port 465)
    - host ..
    - username  (your email )
    - password (the pass of your email )
    - address 
    - from (from your email )
    .....

- utils/other/string.php :
    - this code used where we trait with strings ...
    - the code contain :
        - genreate random string :
            - it will take the length of the strign and generate a random string with that length 
        - extractstring
            - it will give you the string between the start and the end 
        - start with :
            - it will give you true or false (depending on the string start)
        - add double apostrophe :
            - just add double apostrophe in the start and the end 
            - used on querys 
        - chechk_email :
            - check if the string is a valid form email 
        - setand not empty :
            - check if the variable is set and not empty (POST , GET)

---
Hope that help, you can contact me at mhd2002mswi@gmail.com or sawe@lesawe.com

---