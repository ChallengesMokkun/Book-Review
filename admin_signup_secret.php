<?php
  
  //function.phpを読み込む
  require('function.php');
  //ログインしていないことを確かめる
  $admin_login = is_Login(false);
  if(!empty($admin_login)){
    header('Location:admin_menu_page_secret.php');
    exit();
  }

  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;

    if(!empty($pre_post) || !empty($quit)){
      $login_id = (!empty($_POST['login_id'])) ? $_POST['login_id'] : null;
      $pass = (!empty($_POST['pass'])) ? $_POST['pass'] : null;
      $pass_re = (!empty($_POST['pass_re'])) ? $_POST['pass_re'] : null;
      $key_word = (!empty($_POST['key_word'])) ? $_POST['key_word'] : null;

    }

    if(!empty($pre_post)){
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

      //pass_re !empty passと一致
      validRetype($pass,$pass_re,'pass_re');
      validEnter($pass_re,'pass_re');

      //key_word !empty 255文字以内　特定文字列と一致
      if(!empty($key_word)){
        if($key_word !== 'preview_mokkuns'){
          appendErr('key_word',LOG03);
        }
      }
      validMax($key_word,'key_word');
      validEnter($key_word,'key_word');

      if(empty($err_msg)){
        //バリデーション
        //login_id 重複しない
        validDupId($login_id,'login_id',false);

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
      $login_id = $_POST['login_id'];
      $pass = $_POST['pass'];
      //DBにユーザー情報を追加
      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO admin (login_id,pass,create_date) VALUES (:login_id,:pass,:create_date)';
        $data = array(
          ':login_id' => $login_id,
          ':pass' => password_hash($pass,PASSWORD_DEFAULT),
          ':create_date' => date('Y-m-d H:i:s')
        );

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          //ログイン処理
          $ses_limit = 60*60;

          $_SESSION['a_id'] = $dbh->lastInsertId();
          $_SESSION['a_login_date'] = time();
          $_SESSION['a_login_limit'] = $ses_limit;

          //管理人ページへ移動
          header('Location:admin_menu_page_secret.php');
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
    $pass_re = null;
    $key_word = null;
  }

  $title = '管理人登録';
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
              <label for="login_id" class="p-member-form__form-label c-form-label">ログインID</label>
              <p class="p-member-form__form-text c-form-text">!?-_;:!&#%=<>\*?+$|^.()[]と半角英数字</p>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('login_id'); ?></div>
              <input type="text" name="login_id" placeholder="ログインID" id="login_id" class="p-member-form__textform c-textform <?php is_Err('login_id'); ?>" value="<?php echo keepTextData($login_id,'login_id'); ?>">
            </div>
            <div class="p-member-form__form-row">
              <label for="pass" class="p-member-form__form-label c-form-label">パスワード</label>
              <p class="p-member-form__form-text c-form-text">!?-_;:!&#%=<>\*?+$|^.()[]と半角英数字</p>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('pass'); ?></div>
              <div class="p-member-form__passform-wrapper">
                <input type="password" name="pass" placeholder="パスワード" id="pass" class="p-member-form__passwordform c-passwordform js-passform <?php is_Err('pass'); ?>" value="<?php echo keepTextData($pass,'pass'); ?>">
                <i class="fa-solid fa-eye p-member-form__fonticon c-fonticon js-pass-show"></i>
              </div>
            </div>
            <div class="p-member-form__form-row">
              <label for="pass_re" class="p-member-form__form-label c-form-label">パスワード(再入力)</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('pass_re'); ?></div>
              <div class="p-member-form__passform-wrapper">
                <input type="password" name="pass_re" placeholder="パスワード(再入力)" id="pass_re" class="p-member-form__passwordform c-passwordform js-passform <?php is_Err('pass_re'); ?>" value="<?php echo keepTextData($pass_re,'pass_re'); ?>">
                <i class="fa-solid fa-eye p-member-form__fonticon c-fonticon js-pass-show"></i>
              </div>
            </div>
            <div class="p-member-form__form-row">
              <label for="key_word" class="p-member-form__form-label c-form-label">あいことば</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('key_word'); ?></div>
              <input type="text" name="key_word" placeholder="あいことば" id="key_word" class="p-member-form__textform c-textform <?php is_Err('key_word'); ?>" value="<?php echo keepTextData($key_word,'key_word'); ?>">
            </div>
            <div class="p-member-form__btn-wrapper">
              <input type="submit" value="登録する" name="pre_post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
              <a href="index.php" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">戻る</a>
            </div>
          </div>
        <?php }else{ ?>
          <div class="p-member-confirm">
            <div class="p-member-confirm__confirm-msg c-confirm-msg">
              <p class="p-member-confirm__text c-confirm-msg__text">
                下記の内容を送信します。
                よろしいでしょうか。
              </p>
            </div>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">ログインID</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo sanitize($login_id); ?></p>
              <input type="hidden" name="login_id" value="<?php echo sanitize($login_id); ?>">
            </div>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">パスワード</p>
              <p class="p-member-confirm__confirm-text c-confirm-text">セキュリティのため表示しません</p>
              <input type="hidden" name="pass" value="<?php echo sanitize($pass); ?>">
            </div>
            <div class="p-member-confirm__btn-wrapper">
              <input type="submit" value="戻る" name="quit" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">
              <input type="submit" value="登録する" name="post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
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