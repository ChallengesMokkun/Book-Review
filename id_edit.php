<?php
  //function.phpを読み込む
  require('function.php');
  //user_auth.phpを読み込む
  require('user_auth.php');

  $u_id = $_SESSION['u_id'];
  $old_login_id = getUserId($u_id);

  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;

    if(!empty($pre_post) || !empty($quit)){
      $login_id = (!empty($_POST['login_id'])) ? $_POST['login_id'] : null;
      $pass = (!empty($_POST['pass'])) ? $_POST['pass'] : null;
    }
    if(!empty($pre_post)){
      //バリデーション
      //login_id !empty 255文字以内 6文字以上 半角英数字記号
      validIdPass($login_id,'login_id');
      validMax($login_id,'login_id');
      validMin($login_id,'login_id',6,MIN01);
      validEnter($login_id,'login_id');
      validDiff($old_login_id,$login_id,'login_id');

      //pass !empty 8文字以上 255文字以下 半角英数字記号
      validIdPass($pass,'pass',LOG03);
      validMax($pass,'pass',255,LOG03);
      validMin($pass,'pass',8,LOG03);
      validEnter($pass,'pass');

      if(empty($err_msg)){
        //バリデーション
        //パスワードが正しい
        validPassword($pass,'pass',$u_id);
        //login_id 重複しない
        validDupId($login_id,'login_id');

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

    if($post){
      $login_id = $_POST['login_id'];

      //更新
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE user SET login_id = :login_id WHERE u_id = :u_id AND delete_flag = 0';
        $data = array(
          ':login_id' => $login_id,
          ':u_id' => $u_id
        );

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          //成功メッセージ
          $_SESSION['success'] = MSG07;
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
    $login_id = null;
    $pass = null;
  }

  $title = 'ID変更';
  require('head.php');
  debugStart();
?>
  <body>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <form action="" method="post" class="form_width">
          <div class="l-col1-form__err-msg c-err-msg"><?php echoErr('common'); ?></div>
        <?php if(empty($pre_post)){ ?>
          <div class="p-member-form">
            <div class="p-member-form__form-row">
              <p>現在のログインID</p>
              <p><?php echo $old_login_id; ?></p>
            </div>
            <div class="p-member-form__form-row">
              <label for="login_id" class="p-member-form__form-label c-form-label">新しいログインID</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('login_id'); ?></div>
              <input type="text" name="login_id" placeholder="新しいログインID" id="login_id" class="p-member-form__textform c-textform <?php is_Err('login_id'); ?>" value="<?php echo keepTextData($login_id,'login_id'); ?>">
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
              <p class="p-member-confirm__confirm-title c-confirm-title">新しいログインID</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo sanitize($login_id); ?></p>
              <input type="hidden" name="login_id" value="<?php echo sanitize($login_id); ?>">
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