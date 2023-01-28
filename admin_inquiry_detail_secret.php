<?php
  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');

  $i_id = (!empty($_GET['i_id']) && is_numeric($_GET['i_id'])) ? $_GET['i_id'] : null;
  if(empty($i_id)){
    debug('$i_idが不正な値');
    header('Location:admin_inquiry_search_secret.php');
    exit();
  }

  $a_id = $_SESSION['a_id'];
  //DBから取得
  $subject_list = getSubject();
  $inquiry = getInquiry($i_id);
  $answers = getAnswer($i_id);

  if(!empty($_POST)){
    $save = (!empty($_POST['save'])) ? true : false;

    if(!empty($save)){
      $answer = $_POST['answer'];
      //バリデーション
      //!empty 3000字以内
      validMax($answer,'answer',3000,MAX02);
      validEnter($answer,'answer');

      if(empty($err_msg)){
        //追加
        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO answer (i_id,a_id,answer,create_date) VALUES (:i_id,:a_id,:answer,:create_date)';
          $data = array(
            ':i_id' => $i_id,
            ':a_id' => $a_id,
            ':answer' => $answer,
            ':create_date' => date('Y-m-d H:i:s')
          );

          $stmt = queryPost($dbh,$sql,$data);

          //成功メッセージ
          $_SESSION['success'] = MSG31;
          //ページリロード
          header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
          exit();

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

      }else{
        debug('バリデーションエラー');
      }

    }else{
      $target = array_key_last($_POST);
      //削除
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE answer SET delete_flag = 1 WHERE answer_id = :answer_id';
        $data = array(':answer_id' => $target);
        
        $stmt = queryPost($dbh,$sql,$data);

        //成功メッセージ
        $_SESSION['success'] = MSG32;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();

      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        appendErr('common',ERR01);
      }
    }

  }else{
    $answer = null;
  }
  
  $title = '問い合わせ詳細';
  require('head.php');
  debugStart();
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1">
        <h2 class="l-col1__page-title c-page-title"><?php echo $title; ?></h2>
        <div class="p-inquiry-confirm">
          <div class="p-inquiry-confirm__confirm-row">
            <p class="p-inquiry-confirm__confirm-title c-confirm-title">内容</p>
            <p class="p-inquiry-confirm__confirm-text c-confirm-text"><?php echo sanitize($subject_list[($inquiry['subject_id'] - 1)]['subject']); ?></p>
          </div>
          <div class="p-inquiry-confirm__confirm-row">
            <p class="p-inquiry-confirm__confirm-title c-confirm-title">詳細</p>
            <p class="p-inquiry-confirm__confirm-text c-confirm-text"><?php echo nl2br(sanitize($inquiry['text'])); ?></p>
          </div>
          <div class="p-inquiry-confirm__confirm-row">
            <p class="p-inquiry-confirm__confirm-title c-confirm-title">名前</p>
            <p class="p-inquiry-confirm__confirm-text c-confirm-text"><?php echo sanitize($inquiry['name']); ?></p>
          </div>
          <div class="p-inquiry-confirm__confirm-row">
            <p class="p-inquiry-confirm__confirm-title c-confirm-title">メールアドレス</p>
            <p class="p-inquiry-confirm__confirm-text c-confirm-text"><?php echo sanitize($inquiry['email']); ?></p>
          </div>
        </div>
        <form action="" method="post" class="p-admin-inquiry">
          <div class="p-admin-inquiry__err-msg c-err-msg"><?php echoErr('common'); ?></div>
          <div class="p-admin-inquiry__memoform">
            <h2 class="p-admin-inquiry__heading c-sub-heading">管理人メモ</h2>
            <div class="p-admin-inquiry__err-msg c-err-msg"><?php echoErr('answer'); ?></div>
            <textarea name="answer" class="p-admin-inquiry__textarea c-textarea c-textarea--m js-textarea <?php is_Err('answer'); ?>"><?php echo keepTextData($answer,'answer'); ?></textarea>
            <p class="p-admin-inquiry__check-num c-check-num js-count"><span class="js-count-num">0</span>&nbsp;/&nbsp;<span class="js-count-limit">3000</span>文字</p>
            <input type="submit" name='save' value="保存" class="p-admin-inquiry__btn c-btn c-btn--active c-btn--l">
          </div>
          <div class="p-admin-inquiry__history">
            <h2 class="p-admin-inquiry__heading c-sub-heading">履歴</h2>
            <?php
              if(!empty($answers)){
                foreach($answers as $key => $val){
            ?>
            <div class="p-admin-inquiry__memo">
              <p class="p-admin-inquiry__confirm-text c-confirm-text">
                <?php echo nl2br(sanitize($val['answer'])); ?><br>
                <?php echo mb_substr(sanitize($val['create_date']),0,-3,'UTF-8'); ?>
              </p>
              <input type="submit" name="<?php echo sanitize($val['answer_id']); ?>" value="削除" class="p-admin-inquiry__cmd c-choice-cmd">
            </div>
            <?php
                }
              }else{
            ?>
            <p class="p-admin-inquiry__text">まだメモはありません</p>
            <?php } ?>
          </div>
        </form>
        <a href="admin_inquiry_search_secret.php<?php if(!empty(keepGETparam(array('i_id')))) echo keepGETparam(array('i_id')); ?>" class="c-btn c-btn--inactive c-btn--s u-block">戻る</a>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>