<?php
  require('function.php');
  
  if(!empty($_SESSION['a_login_date'])){
    $admin_login = true;
  }else{
    $admin_login = false;
  }

  session_destroy();

  if(!empty($admin_login)){
    header('Location:admin_login_secret.php');
    exit();

  }else{
    header('Location:login_page.php');
    exit();
  }