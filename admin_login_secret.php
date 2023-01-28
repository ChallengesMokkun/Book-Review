<?php
  
  //function.phpを読み込む
  require('function.php');

  //ログインしていないことを確認する
  $admin_login = is_Login(false);
  if(!empty($admin_login)){
    header('Location:admin_menu_page_secret.php');
    exit();
  }

  $login_id = null;
  $pass = null;

  if(!empty($_POST)){
    $login_id = $_POST['login_id'];
    $pass = $_POST['pass'];

    //バリデーション
    //login_id !empty 255文字以内 6文字以上 半角英数字記号
    validIdPass($login_id,'login_id');
    validMax($login_id,'login_id');
    validMin($login_id,'login_id',6,MIN01);
    validEnter($login_id,'login_id');

    //pass !empty  8文字以上 255文字以下 半角英数字記号
    validIdPass($pass,'pass');
    validMax($pass,'pass');
    validMin($pass,'pass',8,MIN02);
    validEnter($pass,'pass');

    if(empty($err_msg)){
      try{
        $dbh = dbConnect();
        $sql = 'SELECT a_id,pass FROM admin WHERE delete_flag = 0 AND login_id = :login_id';
        $data = array(':login_id' => $login_id);

        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($result) && password_verify($pass,$result['pass'])){
          debug('チェック完了');
          //ログイン処理
          $ses_limit = 60*60;
          $_SESSION['a_id'] = $result['a_id'];
          $_SESSION['a_login_date'] = time();
          $_SESSION['a_login_limit'] = $ses_limit;
          $_SESSION['success'] = MSG17;

          header('Location:admin_menu_page_secret.php');
          exit();

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

  }

  $title = '管理人ログイン';
  require('head.php');
  debugStart();
?>
  <body>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <form action="" method="post" class="p-login-form">
          <div class="p-login-form__err-msg c-err-msg"><?php echoErr('common'); ?></div>
          <div class="p-login-form__form-row">
            <label for="login_id" class="p-login-form__form-label c-form-label">ログインID</label>
            <div class="p-login-form__err-msg c-err-msg"><?php echoErr('login_id'); ?></div>
            <input type="text" name="login_id" placeholder="ログインID" id="login_id" class="p-login-form__textform c-textform <?php is_Err('common'); ?>" value="<?php echo keepTextData($login_id,'login_id'); ?>">
          </div>
          <div class="p-login-form__form-row">
            <label for="pass" class="p-login-form__form-label c-form-label">パスワード</label>
            <div class="p-login-form__err-msg c-err-msg"><?php echoErr('pass'); ?></div>
            <div class="p-login-form__passform-wrapper">
              <input type="password" name="pass" placeholder="パスワード" id="pass" class="p-login-form__passwordform c-passwordform js-passform <?php is_Err('common'); ?>" value="<?php echo keepTextData($pass,'pass'); ?>">
              <i class="fa-solid fa-eye p-login-form__fonticon c-fonticon js-pass-show"></i>
            </div>
          </div>
          <input type="submit" value="ログイン" class="p-login-form__btn c-btn c-btn--active c-btn--l">
        </form>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>