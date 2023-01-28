<?php
  
  //function.phpを読み込む
  require('function.php');

  //ユーザーがログインしているか確認する ログインしていればメールアドレスを取得する
  $user_login = is_Login();
  if(!empty($user_login)){
    $email = getUserEmail($_SESSION['u_id']);
  }

  $subject_list = getSubject();

  
  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;

    if(!empty($pre_post) || !empty($quit)){
      $name = (!empty($_POST['name'])) ? $_POST['name'] : null;
      if(empty($user_login)){
        $email = (!empty($_POST['email'])) ? $_POST['email'] : null;
      }
      $subject_id = (!empty($_POST['subject_id'])) ? $_POST['subject_id'] : null;
      $text = (!empty($_POST['text'])) ? $_POST['text'] : null;
    }

    if(!empty($pre_post)){
      //バリデーション
      //name !empty 255文字以内
      validMax($name,'name');
      validEnter($name,'name');
      //email !empty 255文字以内 email形式
      if(empty($user_login)){
        validEmail($email,'email');
        validMax($email,'email');
        validEnter($email,'email');
      }
      //subject_id !empty
      validEnter($subject_id,'subject_id',REQ04);
      //text !empty 3000文字以内
      validMax($text,'text',3000,MAX02);
      validEnter($text,'text');

      if(empty($err_msg)){
        debug('チェック完了');

      }else{
        debug('バリデーションエラー');
        $pre_post = false;
      }
    }

    if(!empty($post)){
      $name = $_POST['name'];
      $email = $_POST['email'];
      $subject_id = $_POST['subject_id'];
      $text = $_POST['text'];

      //追加
      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO inquiry (name,email,subject_id,text,create_date) VALUES (:name,:email,:subject_id,:text,:create_date)';
        $data = array(
          ':name' => $name,
          ':email' => $email,
          ':subject_id' => $subject_id,
          ':text' => $text,
          ':create_date' => date('Y-m-d H:i:s')
        );

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          $subject = $subject_list[($subject_id - 1)]['subject'];
          $from = 'challenges.mokkun6@gmail.com';
          $mail_sub = 'お問い合わせを受け付けました | Book-Review';
          $text = <<<EOT
Book-Reviewご訪問者
{$name} 様

Book-Reviewにご訪問いただきましてありがとうございます。
お問い合わせを受け付けました。
回答が必要な場合は、3営業日内に回答いたします。

確認のため、お問い合せ内容を以下に記載します。
-----------------------------------
内容:
{$subject}

詳細:
{$text}
-----------------------------------
以上です。

今後ともよろしくお願いいたします。


Book-Reviewスタッフ代表
Mokkun
EOT;
          sendMail($email,$mail_sub,$text,$from);

          //成功メッセージ
          $_SESSION['success'] = MSG18;
          //トップページへ移動
          header('Location:index.php');
          exit();

        }
      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        appendErr('common',ERR01);
      }
    }

    $_POST = array();

  }else{
    $name = null;
    $email = (empty($user_login)) ? null : $email;
    $subject_id = null;
    $text = null;
  }

  $title = 'お問い合わせ';
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
            <div class="p-member-form__confirm-msg c-confirm-msg">
            <?php if(!empty($user_login)){ ?>
              <p class="p-member-confirm__text c-confirm-msg__text">
                お問い合わせを受け付けます。<br>
                ご登録のメールアドレス宛に<br>
                回答いたします。
              </p>
            <?php }else{ ?>
              <p class="p-member-confirm__text c-confirm-msg__text">
                お問い合わせを受け付けます。<br>
                ご入力のメールアドレス宛に<br>
                回答いたします。
              </p>
            <?php } ?>
            </div>
            <div class="p-member-form__form-row">
              <label for="name" class="p-member-form__form-label c-form-label">お名前</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('name'); ?></div>
              <input type="text" name="name" placeholder="お名前" id="name" class="p-member-form__textform c-textform <?php is_Err('name'); ?>" value="<?php echo keepTextData($name,'name'); ?>">
            </div>
            <?php if(empty($user_login)){ ?>
            <div class="p-member-form__form-row">
              <label for="email" class="p-member-form__form-label c-form-label">メールアドレス</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('email'); ?></div>
              <input type="text" name="email" placeholder="メールアドレス" id="email" class="p-member-form__textform c-textform <?php is_Err('email'); ?>" value="<?php echo keepTextData($email,'email'); ?>">
            </div>
            <?php } ?>
            <div class="p-member-form__form-row">
              <label for="subject_id" class="p-member-form__form-label c-form-label">内容</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('subject_id'); ?></div>
              <select name="subject_id" id="subject_id" class="p-member-form__selectform c-selectform <?php is_Err('subject_id'); ?>">
                <option value="0">選択してください</option>
                <?php
                  if(!empty($subject_list)){
                    foreach($subject_list as $key => $val){
                ?>
                <option value="<?php echo $val['subject_id']; ?>" <?php keepSelectData($subject_id,'subject_id',$val['subject_id'],true,false); ?>><?php echo $val['subject']; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="p-member-form__form-row">
              <label for="text" class="p-member-form__form-label c-form-label">詳細</label>
              <div class="p-member-form__err-msg c-err-msg"><?php echoErr('text'); ?></div>
              <textarea name="text" placeholder="詳細" id="text" class="p-member-form__textarea c-textarea c-textarea--m js-textarea <?php is_Err('text'); ?>"><?php echo keepTextData($text,'text'); ?></textarea>
              <p class="p-member-form__check-num c-check-num js-count"><span class="js-count-num">0</span>&nbsp;/&nbsp;<span class="js-count-limit">3000</span>文字</p>
            </div>
            <div class="p-member-form__btn-wrapper">
              <input type="submit" value="送信する" name="pre_post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
              <a href="index.php" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">戻る</a>
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
              <p class="p-member-confirm__confirm-title c-confirm-title">お名前</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo sanitize($name); ?></p>
              <input type="hidden" name="name" value="<?php echo sanitize($name); ?>">
              <input type="hidden" name="email" value="<?php echo sanitize($email); ?>">
            </div>
            <?php if(empty($user_login)){ ?>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">メールアドレス</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo sanitize($email); ?></p>
            </div>
            <?php } ?>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">内容</p>
              <p class="p-member-confirm__confirm-text c-confirm-text"><?php echo $subject_list[($subject_id - 1)]['subject']; ?></p>
              <input type="hidden" name="subject_id" value="<?php echo sanitize($subject_id); ?>">
            </div>
            <div class="p-member-confirm__form-row">
              <p class="p-member-confirm__confirm-title c-confirm-title">詳細</p>
              <p  class="p-member-confirm__confirm-text c-confirm-text"><?php echo nl2br(sanitize($text)); ?></p>
              <input type="hidden" name="text" value="<?php echo sanitize($text); ?>">
            </div>
            <div class="p-member-confirm__btn-wrapper">
              <input type="submit" value="戻る" name="quit" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">
              <input type="submit" value="送信する" name="post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
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