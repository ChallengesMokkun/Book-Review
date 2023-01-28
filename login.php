<?php
  
  $login_id = null;
  $pass = null;

  if(!empty($_POST)){
    $login_id = (!empty($_POST['login_id'])) ? $_POST['login_id'] : null;
    $pass = (!empty($_POST['pass'])) ? $_POST['pass'] : null;
    $keep_login = (!empty($_POST['keep_login'])) ? $_POST['keep_login'] : null;

    //バリデーション
    //login_id !empty 255文字以内 6文字以上 半角英数字記号
    validMax($login_id,'login_id');
    validMin($login_id,'login_id',6,MIN01);
    validIdPass($login_id,'login_id');
    validEnter($login_id,'login_id');

    //pass !empty  8文字以上 255文字以下 半角英数字記号
    validMax($pass,'pass');
    validMin($pass,'pass',8,MIN02);
    validIdPass($pass,'pass');
    validEnter($pass,'pass');

    if(empty($err_msg)){
      try{
        $dbh = dbConnect();
        $sql = 'SELECT u_id,pass FROM user WHERE delete_flag = 0 AND login_id = :login_id';
        $data = array(':login_id' => $login_id);

        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($result) && password_verify($pass,$result['pass'])){
          debug('チェック完了');
          //ログイン処理
          $ses_limit = (!empty($keep_login)) ? 60*60*24*30 : 60*60;
          $_SESSION['u_id'] = $result['u_id'];
          $_SESSION['u_login_date'] = time();
          $_SESSION['u_login_limit'] = $ses_limit;
          //成功メッセージ
          $_SESSION['success'] = MSG17;

          if(basename($_SERVER['PHP_SELF']) === 'login_page.php'){
            //ログインページからログインしたとき
            header('Location:mypage.php');
            exit();

          }elseif(basename($_SERVER['PHP_SELF']) === 'index.php' && !empty(keepGETparam())){
            //サイドバーからログインしたとき
            header('Location:index.php'.keepGETparam());
            exit();

          }else{
            header('Location:index.php');
            exit();

          }
          
        }else{
          debug('バリデーションエラー');
          appendErr('common',LOG02);
        }

      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        appendErr('common',ERR01);
      }
    }else{
      debug('バリデーションエラー');
    }

  }elseif(basename($_SERVER['PHP_SELF']) === 'login.php'){
    //直接アクセスした時
    header('Location:index.php');
    exit();
  }
