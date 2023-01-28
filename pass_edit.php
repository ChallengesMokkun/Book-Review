<?php
  //function.phpを読み込む
  require('function.php');
  //user_auth.phpを読み込む
  require('user_auth.php');

  $u_id = $_SESSION['u_id'];

  if(!empty($_POST)){
    $old_pass = $_POST['old_pass'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    //バリデーション
    //pass !empty 8文字以上 255文字以下 半角英数字記号
    validIdPass($old_pass,'old_pass',LOG03);
    validMax($old_pass,'old_pass',255,LOG03);
    validMin($old_pass,'old_pass',8,LOG03);
    validEnter($old_pass,'old_pass');

    //pass !empty  8文字以上 255文字以下 半角英数字記号
    validIdPass($pass,'pass');
    validMax($pass,'pass');
    validMin($pass,'pass',8,MIN02);
    validEnter($pass,'pass');

    //pass_re !empty passと一致
    validRetype($pass,$pass_re,'pass_re');
    validEnter($pass_re,'pass_re');

    if(empty($err_msg)){
      //バリデーション
      //古いパスワードが正しい
      validPassword($old_pass,'old_pass',$u_id);

      if(empty($err_msg)){
        //新しいパスワードが古いものと違う値
        validDiff($old_pass,$pass,'pass');

        if(empty($err_msg)){
          //更新
          try{
            $dbh = dbConnect();
            $sql = 'UPDATE user SET pass = :pass WHERE u_id = :u_id AND delete_flag = 0';
            $data = array(
              ':pass' => password_hash($pass,PASSWORD_DEFAULT),
              ':u_id' => $u_id
            );
    
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
              //成功メッセージ
              $_SESSION['success'] = MSG06;
              //マイページへ移動
              header('Location:mypage.php');
              exit();
            }
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

      }else{
        debug('バリデーションエラー');
      }

    }else{
      debug('バリデーションエラー');
    }

    


  }else{
    $old_pass = null;
    $pass = null;
    $pass_re = null;
  }

  $title = 'パスワード変更';
  require('head.php');
  debugStart();
?>
  <body>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <form action="" method="post" class="p-member-form">
          <div class="p-member-form__err-msg c-err-msg"><?php echoErr('common'); ?></div>
          <div class="p-member-form__form-row">
            <label for="old_pass" class="p-member-form__form-label c-form-label">現在のパスワード</label>
            <div class="p-member-form__err-msg c-err-msg"><?php echoErr('old_pass'); ?></div>
            <div class="p-member-form__passform-wrapper">
              <input type="password" name="old_pass" placeholder="現在のパスワード" id="old_pass" class="p-member-form__passwordform c-passwordform js-passform <?php is_Err('old_pass'); ?>" value="<?php echo keepTextData($old_pass,'old_pass'); ?>">
              <i class="fa-solid fa-eye p-member-form__fonticon c-fonticon js-pass-show"></i>
            </div>
          </div>
          <div class="p-member-form__form-row">
            <label for="pass" class="p-member-form__form-label c-form-label">新しいパスワード(8文字以上)</label>
            <p class="form_sub_text">!?-_;:!&#%=<>\*?+$|^.()[]と半角英数字</p>
            <div class="p-member-form__err-msg c-err-msg"><?php echoErr('pass'); ?></div>
            <div class="p-member-form__passform-wrapper">
              <input type="password" name="pass" placeholder="新しいパスワード" id="pass" class="p-member-form__passwordform c-passwordform js-passform <?php is_Err('pass'); ?>" value="<?php echo keepTextData($pass,'pass'); ?>">
              <i class="fa-solid fa-eye p-member-form__fonticon c-fonticon js-pass-show"></i>
            </div>
          </div>
          <div class="p-member-form__form-row">
            <label for="pass_re" class="p-member-form__form-label c-form-label">新しいパスワード(再入力)</label>
            <div class="p-member-form__err-msg c-err-msg"><?php echoErr('pass_re'); ?></div>
            <div class="p-member-form__passform-wrapper"></div>
            <input type="password" name="pass_re" placeholder="新しいパスワード(再入力)" id="pass_re" class="p-member-form__passwordform c-passwordform js-passform <?php is_Err('pass_re'); ?>" value="<?php echo keepTextData($pass_re,'pass_re'); ?>">
            <i class="fa-solid fa-eye p-member-form__fonticon c-fonticon js-pass-show"></i>
          </div>
          <div class="p-member-form__btn-wrapper">
            <input type="submit" value="更新する" class="p-member-form__btn c-btn c-btn--active c-btn--m">
            <a href="mypage.php" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">戻る</a>
          </div>
        </form>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>