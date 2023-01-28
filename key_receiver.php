<?php
  //function.phpを読み込む
  require('function.php');

  //ログインしてないことを確認する
  $user_login = is_Login();
  $admin_login = is_Login(false);
  if(!empty($user_login)){
    header('Location:index.php');
    exit();
  }
  if(!empty($admin_login)){
    header('Location:admin_menu_page_secret.php');
    exit();
  }

  //認証キーがなければ別ページへ移動させる
  if(empty($_SESSION['auth_key'])){
    header('Location:index.php');
    exit();
  }

  $email = $_SESSION['email'];

  if(!empty($_POST)){
    $auth_key = (!empty($_POST['auth_key'])) ? $_POST['auth_key'] : null;
    //バリデーション
    //auth_key !empty 半角英数字・ちょうどの文字数(形式) 一致
    validHalf($auth_key,'auth_key');
    validLettersNum($auth_key,'auth_key',12);
    validEnter($auth_key,'auth_key');
    validRetype($auth_key,$_SESSION['auth_key'],'auth_key',LOG03);

    if(empty($err_msg)){
      //仮パスワード発行+更新+メール送信
      $tmp_pass = makeRandomStr();
      
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE user SET pass = :pass WHERE delete_flag = 0 AND email = :email';
        $data = array(
          ':pass' => password_hash($tmp_pass,PASSWORD_DEFAULT),
          ':email' => $email
        );

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          debug('仮パスワード発行完了');
          debug('仮パスワード: '.$tmp_pass);

          $sub = '仮パスワードの発行 | Book-Review';
          $from = 'challenges.mokkun6@gmail.com';
          $text = <<<EOT
Book-Reviewご利用者様

日頃からBook-Reviewをご利用いただきましてありがとうございます。
仮パスワードを発行いたしました。
あなたの仮パスワードは
{$tmp_pass}
です。

ログインなさいましたら、マイページからパスワードを変更できます。

不明点がございましたらお問い合わせフォームよりお伺いしたします。
今後ともよろしくお願いいたします。


Book-Reviewスタッフ代表
Mokkun
EOT;

          sendMail($email,$sub,$text,$from);

          session_unset();
          //成功メッセージ
          $_SESSION['success'] = MSG16;

          debug('ログインページに移動');
          header('Location:login_page.php');
          exit();
        }

      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        appendErr('common',ERR01);
      }

    }else{
      debug('バリデーションエラー');
    }

  }else{
    $auth_key = null;
  }

  $title = '仮パスワードの発行';
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
        <form action="" method="post" class="p-member-form">
          <div class="p-member-form__confirm-msg c-confirm-msg">
            <p class="p-member-form__text c-confirm-msg__text">
              ご本人様確認をさせていただきます。<br>
              確認後、仮パスワードを発行いたします。
            </p>
          </div>
          <div class="p-member-form__form-row">
            <label for="auth_key" class="p-member-form__form-label c-form-label">認証キー</label>
            <div class="p-member-form__err-msg c-err-msg"><?php echoErr('auth_key'); ?></div>
            <input type="text" name="auth_key" placeholder="認証キー" id="auth_key" class="p-member-form__textform c-textform <?php is_Err('auth_key'); ?>" value="<?php echo keepTextData($auth_key,'auth_key'); ?>">
          </div>
          <div class="u-margin-t-m">
            <input type="submit" value="送信する" class="p-member-form__btn c-btn c-btn--active c-btn--l">
          </div>
        </form>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>