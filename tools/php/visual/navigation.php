
<!--Navbar-->
<nav class="navbar navbar-expand-md navbar-dark black ">

  <!-- Navbar brand -->
  <a class="navbar-brand mx-3 h3" href="<?php echo $base_url; ?>"><p class="h1">Courses<small class="h6">lib</small></p></a>

  <!-- Collapse button -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navi" aria-controls="navi" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Collapsible content -->
  <div class="collapse navbar-collapse flex-wrap" id="navi">

    <!-- Links -->
    <ul class="navbar-nav  mr-auto ">
      <li class="nav-item h5  mr-3" style="width:20vw;min-width:270px;">
          <form method='get' action = '<?php echo $base_url; ?>course/index.php' class= "m-0 p-0 md-form   my-auto" >
                <input name="search" class=" my-auto form-control mdb-autocomplete dark white-text " id="search-autocomplete" style="height:30px" oninput="search_options('search-autocomplete')" placeholder="Search by keyword , course name ..." aria-label="Search" >
                <input type="submit" hidden />
                <button class="mdb-autocomplete-clear">
                  <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="https://www.w3.org/2000/svg">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    <path d="M0 0h24v24H0z" fill="none" />
                  </svg>
                </button>
                
          </form>

      </li>
      
      <li class="nav-item">
        <a class="nav-link h5 m-0" href="<?php echo $base_url; ?>course"> Discover</a>
      </li>
      <li class="nav-item">
        <a class="nav-link h5 m-0" href="<?php echo $base_url; ?>universities"> Universities</a>
      </li>

      
    </ul>
    <ul class="navbar-nav ml-auto ">
      
      
      <?php 
    if(!isset($_SESSION['verified']) || empty($_SESSION['verified']) || $_SESSION['verified']!=true){
        echo '  
                <li class="nav-item d-flex align-items-center"><a class="mx-2 btn btn-outline-white btn-rounded btn-sm " href="'.$base_url.'account/auth/login.php">Login</a></li>
                <li class="nav-item d-flex align-items-center"><a class="mx-2 btn btn-outline-white btn-rounded btn-sm " href="'.$base_url.'account/auth/signup.php">Sign up</a></li>
                <li class="nav-item">
                  <a class="mx-2 btn btn-white btn-rounded btn-sm px-3" href='.$base_url.'account/auth/google_redirect.php>
                    <i class="fab fa-2x fa-google"></i> 
                  </a>
                </li>
        ';
    }else{echo '
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" id="right_drop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="'.$_SESSION['member_profile_url'].'" class="img-fluid z-depth-1 rounded " style= "display: block;object-fit: cover;width:30px;" >
                  </a>
                  <div class="dropdown-menu dropdown-menu-right dropdown-dark" aria-labelledby="right_drop">
                    <a class="dropdown-item" href="'.$base_url.'course/mycourses/add.php">Add Course</a>
                    <a class="dropdown-item" href="'.$base_url.'account/profile/">Profile</a>
                    <a class="dropdown-item" href="'.$base_url.'account/settings/">Settings</a>
                    <a class="dropdown-item text-danger" href="'.$base_url.'account/auth/logout.php">Log Out</a>
                    
                  </div>
                </li>
        ';}
    ?>
    </ul>
    <!-- Links -->

    
  </div>
  <!-- Collapsible content -->

</nav>
<script language="JavaScript" type="text/javascript" >
  auto_complete_data=[];
  function search_options(id){
    
        input = document.getElementById(id);
        document.querySelectorAll('.mdb-autocomplete-wrap').forEach(el => el.remove());
        if (input.value.length > 1) {
                $.ajax({
                type: "GET",
                url: "<?php echo $base_url; ?>course/tools/course_search.php",
                data: {
                    word: input.value
                },
                success: function (data) {
                    const keyword_list = JSON.parse(data);
                    auto_complete_data=[];
                    for(i=0;i<keyword_list.length;i++){
                        auto_complete_data.push(keyword_list[i]);
                    }
                    
                },
                error: function (data) {
                    console.log('An error occurred.');
                },
            });
            
        }
        $('#search-autocomplete').mdbAutocomplete({
        data: auto_complete_data
        });
    }
    $('#search-autocomplete').mdbAutocomplete({
    data: auto_complete_data
    });
</script>
<style>
  input::placeholder {
  color: white!important;

}
</style>
<?php Print_message();?>
<!--/.Navbar-->
