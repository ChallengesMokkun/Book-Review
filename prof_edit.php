<?php
  //function.phpを読み込む
  require('function.php');
  //user_auth.phpを読み込む
  require('user_auth.php');

  $u_id = $_SESSION['u_id'];
  $dbInfo = getUserProf($u_id);
  $age_list = getAge();
  $gender_list = getGender();

  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;

    if(!empty($pre_post) || !empty($quit)){
      $age_id = (isset($_POST['age_id'])) ? $_POST['age_id'] : null;
      $age_flag = (isset($_POST['age_flag'])) ? $_POST['age_flag'] : null;
      $gender_id = (isset($_POST['gender_id'])) ? $_POST['gender_id'] : null;
      $gender_flag = (isset($_POST['gender_flag'])) ? $_POST['gender_flag'] : null;
    }

    if(!empty($pre_post)){
      //バリデーション
      //age_id !isset
      //age_flag !isset
      if($dbInfo['age_id'] !== $age_id){
        validEnterOkZero($age_id,'age_id');
      }
      if($dbInfo['age_flag'] !== $age_flag){
        validEnterOkZero($age_flag,'age_flag');
      }
      //gender_id !isset
      //gender_flag !isset
      if($dbInfo['gender_id'] !== $gender_id){
        validEnterOkZero($gender_id,'gender_id');
      }
      if($dbInfo['gender_flag'] !== $gender_flag){
        validEnterOkZero($gender_flag,'gender_flag');
      }

      if(empty($err_msg)){
        debug('チェック完了');

      }else{
        debug('バリデーションエラー');
        $pre_post = false;
      }
    }

    if(!empty($post)){
      $age_id = $_POST['age_id'];
      $age_flag = $_POST['age_flag'];
      $gender_id = $_POST['gender_id'];
      $gender_flag = $_POST['gender_flag'];

      //更新
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE user SET age_id = :age_id,age_flag = :age_flag,gender_id = :gender_id,gender_flag = :gender_flag WHERE u_id = :u_id AND delete_flag = 0';
        $data = array(
          ':age_id' => $age_id,
          ':age_flag' => $age_flag,
          ':gender_id' => $gender_id,
          ':gender_flag' => $gender_flag,
          ':u_id' => $u_id
        );

        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
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
    $age_id = null;
    $age_flag =  null;
    $gender_id =  null;
    $gender_flag =  null;
  }

  $title = 'プロフィール変更';
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