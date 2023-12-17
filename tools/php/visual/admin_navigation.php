
<!--Navbar-->
<nav class="navbar navbar-expand-sm navbar-dark black ">

  <!-- Navbar brand -->
  <a class="navbar-brand" href="<?php echo $base_url; ?>">LCourse</a>

  <!-- Collapse button -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navi" aria-controls="navi" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Collapsible content -->
  <div class="collapse navbar-collapse" id="navi">

    <!-- Links -->
    <ul class="navbar-nav mr-auto">
      
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $base_url; ?>admin/models"> Model</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $base_url; ?>course"><i class="fas fa-search"></i> Discover</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $base_url; ?>universities"><i class="fas fa-university"></i> Universities</a>
      </li>

      
    </ul>
    <ul class="navbar-nav ml-auto">
      
      
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
                    <img src="'.$_SESSION['member_profile_url'].'" class="img-fluid z-depth-1 rounded-circle" style="width:30px;height:30px">
                  </a>
                  <div class="dropdown-menu dropdown-menu-right dropdown-dark" aria-labelledby="right_drop">
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
<?php Print_message();?>
<!--/.Navbar-->