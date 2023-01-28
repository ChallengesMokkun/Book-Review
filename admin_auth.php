<?php
  if(basename($_SERVER['PHP_SELF']) === 'admin_auth.php'){
    header('Location:index.php');
    exit();
  }

  //ログイン確認
  if(!empty($_SESSION['a_login_date'])){
    //ログイン期限内か確認
    if(time() > $_SESSION['a_login_date'] + $_SESSION['a_login_limit']){
      //ログイン期限を過ぎている
      session_destroy();
      if(basename($_SERVER['PHP_SELF']) !== 'admin_login_secret.php'){
        header('Location:admin_login_secret.php');
        exit();
      }

    }else{
      //ログイン期限内
      $_SESSION['a_login_date'] = time();

    }
  }else{
    debug('未ログインユーザー');
    header('Location:admin_login_secret.php');
    exit();
  }
