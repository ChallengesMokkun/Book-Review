<?php
  
  //function.phpを読み込む
  require('function.php');

  //ログインしてないことを確認する
  $admin_login = is_Login(false);
  $user_login = is_Login();
  if(!empty($admin_login)){
    header('Location:admin_menu_page_secret.php');
    exit();
  }
  if(!empty($user_login)){
    header('Location:index.php');
    exit();
  }

  require('login.php');

  $title = 'ユーザーログイン';
  require('head.php');
  debugStart();
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <div class="l-col1-form__err-msg c-err-msg"><?php echoErr('common'); ?></div>
        <form action="" method="post" class="p-login-form">
          <div class="p-login-form__form-row">
            <label for="login_id" class="p-login-form__form-label c-form-label">ログインID</label>
            <div class="l-col1-form__err-msg c-err-msg"><?php echoErr('login_id'); ?></div>
            <input type="text" name="login_id" placeholder="ログインID" id="login_id" class="p-login-form__textform c-textform <?php is_Err('common'); ?>" value="<?php echo keepTextData($login_id,'login_id'); ?>">
          </div>
          <div class="p-login-form__form-row">
            <label for="pass" class="p-login-form__form-label c-form-label">パスワード</label>
            <div class="l-col1-form__err-msg c-err-msg"><?php echoErr('pass'); ?></div>
            <div class="p-login-form__passform-wrapper">
              <input type="password" name="pass" placeholder="パスワード" id="pass" class="p-login-form__passwordform c-passwordform js-passform <?php is_Err('common'); ?>" value="<?php echo keepTextData($pass,'pass'); ?>">
              <i class="fa-solid fa-eye p-login-form__fonticon c-fonticon js-pass-show"></i>
            </div>
          </div>
          <div class="p-login-form__text-wrapper">
            <input type="checkbox" name="keep_login" id="keep_login" class="p-login-form__checkboxform c-checkboxform">
            <label for="keep_login" class="p-login-form__checkbox-label c-checkboxform__label">ログインしたままにする</label>
          </div>
          <input type="submit" value="ログイン" class="p-login-form__btn c-btn c-btn--active c-btn--l">
          <div class="p-login-form__text-wrapper">
            <p class="p-login-form__text"><a href="signup.php" class="p-login-form__link">こちらから会員登録できます</a></p>
            <p class="p-login-form__text"><a href="key_sender.php" class="p-login-form__link">こちらはパスワード忘れの方</a></p>
          </div>
        </form>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>