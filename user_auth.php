<?php
  if(basename($_SERVER['PHP_SELF']) === 'user_auth.php'){
    header('Location:index.php');
    exit();
  }

  //ログイン確認
  if(!empty($_SESSION['u_login_date'])){
    //ログイン期限内か確認
    if(time() > $_SESSION['u_login_date'] + $_SESSION['u_login_limit']){
      //ログイン期限を過ぎている
      session_destroy();
      if(basename($_SERVER['PHP_SELF']) !== 'login_page.php'){
        header('Location:login_page.php');
        exit();
      }

    }else{
      //ログイン期限内
      $_SESSION['u_login_date'] = time();

    }
  }else{
    debug('未ログインユーザー');
    header('Location:login_page.php');
    exit();
  }
