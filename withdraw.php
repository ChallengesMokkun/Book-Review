<?php
  //function.phpを読み込む
  require('function.php');
  //user_auth.phpを読み込む
  require('user_auth.php');

  $u_id = $_SESSION['u_id'];

  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;

    if(!empty($post)){
      //退会処理
      try{
        $dbh = dbConnect();
        $sql1 = 'UPDATE user SET delete_flag = 1 WHERE u_id = :u_id';
        $sql2 = 'UPDATE favorite SET delete_flag = 1 WHERE u_id = :u_id';
        $sql3 = 'UPDATE comment SET delete_flag = 1 WHERE u_id = :u_id';
        $sql4 = 'UPDATE rating SET delete_flag = 1 WHERE u_id = :u_id';
        $data = array(':u_id' => $u_id);

        $stmt1 = queryPost($dbh,$sql1,$data);
        $stmt2 = queryPost($dbh,$sql2,$data);
        $stmt3 = queryPost($dbh,$sql3,$data);
        $stmt4 = queryPost($dbh,$sql4,$data);

        if($stmt1){
          debug('退会処理完了');
          session_destroy();
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
  }

  $title = '退会';
  require('head.php');
?>
  <body>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <div class="l-col1-form__err-msg c-err-msg"><?php echoErr('common'); ?></div>
        <form action="" method="post">
        <?php if(empty($pre_post) || !empty($quit)){ ?>
          <div class="p-member-form">
            <div class="p-member-form__confirm-msg c-confirm-msg">
              <p class="p-member-form__text c-confirm-msg__text">
                退会されますと<br>
                各種データは復元できません。<br>
                よろしいでしょうか。
              </p>
            </div>
            <div class="p-member-form__btn-wrapper">
              <input type="submit" value="退会する" name="pre_post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
              <a href="mypage.php" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">戻る</a>
            </div>
          </div>
        <?php }else{ ?>
          <div class="p-member-confirm">
            <div class="p-member-confirm__confirm-msg c-confirm-msg">
              <p class="p-member-confirm__text c-confirm-msg__text">
                全てのデータが消去されます。<br>
                本当に退会なさいますか。
              </p>
            </div>
            <div class="p-member-confirm__btn-wrapper">
              <input type="submit" value="戻る" name="quit" class="p-member-form__btn c-btn c-btn--inactive c-btn--m">
              <input type="submit" value="退会する" name="post" class="p-member-form__btn c-btn c-btn--active c-btn--m">
            </div>
          </div>
        <?php } ?>
        </form>
      </div>
    </main>
<?php
  require('footer.php');

?>