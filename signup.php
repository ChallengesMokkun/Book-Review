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


  $age_list = getAge();
  $gender_list = getGender();

  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;

    if(!empty($pre_post) || !empty($quit)){
      $email = (!empty($_POST['email'])) ? $_POST['email'] : null;
      $login_id = (!empty($_POST['login_id'])) ? $_POST['login_id'] : null;
      $pass = (!empty($_POST['pass'])) ? $_POST['pass'] : null;
      $pass_re = (!empty($_POST['pass_re'])) ? $_POST['pass_re'] : null;
      $age_id = (isset($_POST['age_id'])) ? $_POST['age_id'] : null;
      $age_flag = (isset($_POST['age_flag'])) ? $_POST['age_flag'] : null;
      $gender_id = (isset($_POST['gender_id'])) ? $_POST['gender_id'] : null;
      $gender_flag = (isset($_POST['gender_flag'])) ? $_POST['gender_flag'] : null;
    }

    if(!empty($pre_post)){

      //バリデーション
      //email !empty 255文字以内 email形式
      validEmail($email,'email');
      validMax($email,'email');
      validEnter($email,'email');

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
      
      //age_id !isset
      //age_flag !isset
      validEnterOkZero($age_id,'age_id');
      validEnterOkZero($age_flag,'age_flag');

      //gender_id !isset
      //gender_flag !isset
      validEnterOkZero($gender_id,'gender_id');
      validEnterOkZero($gender_flag,'gender_flag');

      if(empty($err_msg)){
        //バリデーション
        //email 重複しない
        validDupEmail($email,'email');
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

    if(!empty($post)){
      $email = $_POST['email'];
      $login_id = $_POST['login_id'];
      $pass = $_POST['pass'];
      $age_id = $_POST['age_id'];
      $age_flag = $_POST['age_flag'];
      $gender_id = $_POST['gender_id'];
      $gender_flag = $_POST['gender_flag'];

      //DBにユーザー情報を追加
      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO user (email,login_id,pass,age_id,age_flag,gender_id,gender_flag,create_date) VALUES (:email,:login_id,:pass,:age_id,:age_flag,:gender_id,:gender_flag,:create_date)';
        $data = array(
          ':email' => $email,
          ':login_id' => $login_id,
          ':pass' => password_hash($pass,PASSWORD_DEFAULT),
          ':age_id' => $age_id,
          ':age_flag' => $age_flag,
          ':gender_id' => $gender_id,
          ':gender_flag' => $gender_flag,
          ':create_date' => date('Y-m-d H:i:s')
        );

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          //ログイン処理
          $ses_limit = 60*60;

          $_SESSION['u_id'] = $dbh->lastInsertId();
          $_SESSION['u_login_date'] = time();
          $_SESSION['u_login_limit'] = $ses_limit;

          debug('会員登録完了');
          //成功メッセージ
          $_SESSION['success'] = MSG05;
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
    $login_id = null;
    $pass = null;
    $pass_re = null;
    $age_id = null;
    $age_flag =  null;
    $gender_id =  null;
    $gender_flag =  null;
  }

  $title = 'ユーザー登録';
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
              <label for="email" class="p-member-form__form-label c-form-label">メールアドレス</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('email'); ?></div>
              <input type="text" name="email" placeholder="メールアドレス" id="email" class="p-member-form__textform c-textform <?php is_Err('email'); ?>" value="<?php echo keepTextData($email,'email'); ?>">
            </div>
            <div class="p-member-form__form-row">
              <label for="login_id" class="p-member-form__form-label c-form-label">ログインID(6文字以上)</label>
              <p class="p-member-form__form-text c-form-text">!?-_;:!&#%=<>\*?+$|^.()[]と半角英数字</p>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('login_id'); ?></div>
              <input type="text" name="login_id" placeholder="ログインID" id="login_id" class="p-member-form__textform c-textform <?php is_Err('login_id'); ?>" value="<?php echo keepTextData($login_id,'login_id'); ?>">
            </div>
            <div class="p-member-form__form-row">
              <label for="pass" class="p-member-form__form-label c-form-label">パスワード(8文字以上)</label>
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
              <label for="age_id" class="p-member-form__form-label c-form-label">年齢</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('age_id'); ?></div>
              <select name="age_id" id="age_id" class="p-member-form__selectform c-selectform <?php is_Err('age_id'); ?>">
                <option value="0" <?php keepSelectData($age_id,'age_id',0,true,false); ?>>回答しない</option>
                <?php
                  if(!empty($age_list)){
                    foreach($age_list as $key => $val){
                ?>
                <option value="<?php echo $val['age_id']; ?>" <?php keepSelectData($age_id,'age_id',$val['age_id'],true,false); ?>><?php echo $val['age']; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
              <div class="p-member-form__radio-wrapper">
                <span>
                  <input type="radio" name="age_flag" value="0" class="p-member-form__radioform c-radioform" id="age_private" <?php keepSelectData($age_flag,'age_flag',0,false,false); ?>>
                  <label for="age_private" class="p-member-form__radio-label c-radioform__label">公開しない</label>
                </span>
                <span>
                  <input type="radio" name="age_flag" value="1" class="p-member-form__radioform c-radioform" id="age_public" <?php keepSelectData($age_flag,'age_flag',1,false,false); ?>>
                  <label for="age_public" class="p-member-form__radio-label c-radioform__label">公開する</label>
                </span>
              </div>
            </div>
            <div class="p-member-form__form-row">
              <label for="gender_id" class="p-member-form__form-label c-form-label">ジェンダー</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('gender_id'); ?></div>
              <select name="gender_id" id="gender_id" class="p-member-form__selectform c-selectform <?php is_Err('gender_id'); ?>">
                <option value="0" <?php keepSelectData($gender_id,'gender_id',0,true,false); ?>>回答しない</option>
                <?php
                  if(!empty($gender_list)){
                    foreach($gender_list as $key => $val){
                ?>
                <option value="<?php echo $val['gender_id']; ?>" <?php keepSelectData($gender_id,'gender_id',$val['gender_id'],true,false); ?>><?php echo $val['gender']; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
              <div class="p-member-form__radio-wrapper">
                <span>
                  <input type="radio" name="gender_flag" value="0" class="p-member-form__radioform c-radioform" id="gender_private" <?php keepSelectData($gender_flag,'gender_flag',0,false,false); ?>>
                  <label for="gender_private" class="p-member-form__radio-label c-radioform__label">公開しない</label>
                </span>
                <span>
                  <input type="radio" name="gender_flag" value="1" class="p-member-form__radioform c-radioform" id="gender_public" <?php keepSelectData($gender_flag,'gender_flag',1,false,false); ?>>
                  <label for="gender_public" class="p-member-form__radio-label c-radioform__label">公開する</label>
                </span>
              </div>
            </div>
            <div class="p-member-form__btn-wrapper">
              <input type="submit" value="登録する" name="pre_post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
              <a href="index.php" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">戻る</a>
            </div>
          </div>
        <?php }else{ ?>
          <div class="p-member-confirm">
            <div class="p-member-confirm__confirm-msg c-confirm-msg">
              <p  class="p-member-confirm__text c-confirm-msg__text">
                下記の内容を送信します。<br>
                よろしいでしょうか。
              </p>
            </div>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">メールアドレス</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo sanitize($email); ?></p>
              <input type="hidden" name="email" value="<?php echo sanitize($email); ?>">
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
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">年齢</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo (empty($age_id)) ? '回答しない' : $age_list[($age_id - 1)]['age']; ?></p>
              <input type="hidden" name="age_id" value="<?php echo sanitize($age_id); ?>">
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo (empty($age_flag)) ? '公開しない' : '公開する'; ?></p>
              <input type="hidden" name="age_flag" value="<?php echo sanitize($age_flag); ?>">
            </div>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">ジェンダー</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo (empty($gender_id)) ? '回答しない' : $gender_list[($gender_id - 1)]['gender']; ?></p>
              <input type="hidden" name="gender_id" value="<?php echo sanitize($gender_id); ?>">
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo (empty($gender_flag)) ? '公開しない' : '公開する'; ?></p>
              <input type="hidden" name="gender_flag" value="<?php echo sanitize($gender_flag); ?>">
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