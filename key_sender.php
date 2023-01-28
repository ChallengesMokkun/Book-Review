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

  if(!empty($_POST)){
    $email = (!empty($_POST['email'])) ? $_POST['email'] : null;
    //バリデーション
    //email !empty 255文字以内 email形式 一致
    validEmail($email,'email',LOG03);
    validMax($email,'email',255,LOG03);
    validEnter($email,'email');

    if(empty($err_msg)){
      //バリデーション
      //emailが登録されている
      try{
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM user WHERE email = :email AND delete_flag = 0'; //つど ユーザーテーブル
        $data = array(':email' => $email);
  
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
  
        if(empty(array_shift($result))){
          appendErr('email',LOG05);
        }
  
      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        appendErr('common',ERR01);
      }

      if(empty($err_msg)){
        //認証キーの発行+メール送信
        $auth_key = makeRandomStr();
        $auth_limit_minutes = 30;
        $sub = '認証キーの発行 | Book-Review';
        $from = 'challenges.mokkun6@gmail.com';

        debug('認証キー: '.$auth_key);

        $text = <<<EOT
Book-Reviewご利用者様

日頃からBook-Reviewをご利用いただきましてありがとうございます。
仮パスワード発行のための認証キーを発行いたしました。
あなたの認証キーは
{$auth_key}
です。

ただいまより{$auth_limit_minutes}分以内に認証キーを入力してください。
仮パスワード発行は下記URLから行えます。
http://localhost:8888/05_WEBservice/Book-Review/key_receiver.php

なお時間を過ぎましたら、お手数ですが再び認証キーを発行していただきますようお願いいたします。

不明点がございましたらお問い合わせフォームよりお伺いしたします。
今後ともよろしくお願いいたします。


Book-Reviewスタッフ代表
Mokkun
EOT;
        sendMail($email,$sub,$text,$from);

        $_SESSION['auth_key'] = $auth_key;
        $_SESSION['auth_limit'] = $auth_limit_minutes * 60 + time();
        $_SESSION['email'] = $email;
        //成功メッセージ
        $_SESSION['success'] = MSG15;

        //仮パスワード発行ページへ移動
        header('Location:key_receiver.php');
        exit();


      }else{
        debug('バリデーションエラー');
      }

    }else{
      debug('バリデーションエラー');
    }

  }else{
    $email = null;
  }

  $title = '認証キーの発行';
  require('head.php');
  debugStart();
?>
  <body>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <div class="l-col1-form__err-msg c-err-msg"><?php echoErr('common'); ?></div>
        <form action="" method="post" class="p-member-form">
          <div class="p-member-form__confirm-msg c-confirm-msg">
            <p class="p-member-form__text c-confirm-msg__text">
              ご本人様確認のため<br>
              登録されているメールアドレス宛に<br>
              認証キーをお送りします。<br>
              よろしいでしょうか。
            </p>
          </div>
          <div class="p-member-form__form-row">
            <label for="email" class="p-member-form__form-label c-form-label">メールアドレス</label>
            <div class="p-member-form__err-msg c-err-msg"><?php echoErr('email'); ?></div>
            <input type="text" name="email" placeholder="メールアドレス" id="email" class="p-member-form__textform c-textform<?php is_Err('email'); ?>" value="<?php echo keepTextData($email,'email'); ?>">
          </div>
          <div class="p-member-form__btn-wrapper">
            <input type="submit" value="送信する" class="p-member-form__btn c-btn c-btn--active c-btn--m">
            <a href="index.php" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">戻る</a>
          </div>
        </form>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>