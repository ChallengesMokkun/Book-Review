<?php
  //function.phpを読み込む
  require('function.php');
  //user_auth.phpを読み込む
  require('user_auth.php');

  $u_id = $_SESSION['u_id'];
  $old_email = getUserEmail($u_id);

  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;

    if(!empty($pre_post) || !empty($quit)){
      $email = (!empty($_POST['email'])) ? $_POST['email'] : null;
      $pass = (!empty($_POST['pass'])) ? $_POST['pass'] : null;
    }
    if(!empty($pre_post)){
      //バリデーション
      //email !empty 255文字以内 email形式 今とは違う
      validEmail($email,'email');
      validMax($email,'email');
      validEnter($email,'email');
      validDiff($old_email,$email,'email');

      //pass !empty 8文字以上 255文字以下 半角英数字記号
      validIdPass($pass,'pass',LOG03);
      validMax($pass,'pass',255,LOG03);
      validMin($pass,'pass',8,LOG03);
      validEnter($pass,'pass');

      if(empty($err_msg)){
        //バリデーション
        //email 重複しない
        validDupEmail($email,'email');
        //パスワードが正しい
        validPassword($pass,'pass',$u_id);

        if(empty($err_msg)){
          debug('チェック完了');

        }else{
          debug('バリデーションエラー');
          $pre_post = false;
        }

      }else{
        debug('バリデーションエラー');
        $pre_post = false;
      }
    }

    if(!empty($post)){
      $email = $_POST['email'];

      //更新
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE user SET email = :email WHERE u_id = :u_id AND delete_flag = 0';
        $data = array(
          ':email' => $email,
          ':u_id' => $u_id
        );

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          //成功メッセージ
          $_SESSION['success'] = MSG12;
          //マイページへ移動
          header('Location:mypage.php');
          exit();
        }
        
      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        appendErr('common',ERR01);
      }

    }

    $_POST = array();

  }else{
    $email = null;
    $pass = null;
  }

  $title = 'メールアドレス変更';
  require('head.php');
  debugStart();
?>
  <body>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <form action="" method="post">
          <div class="l-col1-form__err-msg c-err-msg"><?php echoErr('common'); ?></div>
        <?php if(empty($pre_post)){ ?>
          <div class="p-member-form">
            <div class="p-member-form__form-row">
              <p class="p-member-form__confirm-title c-confirm-title">現在のメールアドレス</p>
              <p class="p-member-form__confirm-text c-confirm-text"><?php echo $old_email; ?></p>
            </div>
            <div class="p-member-form__form-row">
              <label for="email" class="p-member-form__form-label c-form-label">新しいメールアドレス</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('email'); ?></div>
              <input type="text" name="email" placeholder="新しいメールアドレス" id="email" class="p-member-form__textform c-textform <?php is_Err('email'); ?>" value="<?php echo keepTextData($email,'email'); ?>">
            </div>
            <div class="p-member-form__form-row">
              <label for="pass" class="p-member-form__form-label c-form-label">パスワード</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('pass'); ?></div>
              <div class="p-member-form__passform-wrapper">
                <input type="password" name="pass" placeholder="パスワード" id="pass" class="p-member-form__passwordform c-passwordform js-passform <?php is_Err('pass'); ?>" value="<?php echo keepTextData($pass,'pass'); ?>">
                <i class="fa-solid fa-eye p-member-form__fonticon c-fonticon js-pass-show"></i>
              </div>
            </div>
            <div class="p-member-form__btn-wrapper">
              <input type="submit" value="更新する" name="pre_post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
              <a href="mypage.php" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">戻る</a>
            </div>
          </div>
        <?php }else{ ?>
          <div class="p-member-confirm">
            <div class="p-member-confirm__confirm-msg c-confirm-msg">
              <p class="p-member-confirm__text c-confirm-msg__text">
                下記の内容を送信します。<br>
                よろしいでしょうか。
              </p>
            </div>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">新しいメールアドレス</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo sanitize($email); ?></p>
              <input type="hidden" name="email" value="<?php echo sanitize($email); ?>">
            </div>
            <div class="p-member-confirm__btn-wrapper">
              <input type="submit" value="戻る" name="quit" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">
              <input type="submit" value="更新する" name="post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
            </div>
          </div>
        <?php } ?>
        </form>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>