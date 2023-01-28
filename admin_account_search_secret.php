<?php

  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');

  $record_span = 20;
  //GETパラメータ
  $current_page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
  $reported_num = (isset($_GET['reported_num']) && is_numeric($_GET['reported_num']) && mb_strlen($_GET['reported_num']) <= 11) ? $_GET['reported_num'] : null;
  $delete_num = (isset($_GET['delete_num']) && is_numeric($_GET['delete_num']) && mb_strlen($_GET['delete_num']) <= 11) ? $_GET['delete_num'] : null;
  $delete_appear = (!empty($_GET['delete_appear'])) ? $_GET['delete_appear'] : 0;
  $logic_flag = (!empty($_GET['logic_flag']) && is_numeric($_GET['logic_flag'])) ? $_GET['logic_flag'] : null;
  $word = (!empty($_GET['word']) && mb_strlen($_GET['word']) <= 255) ? $_GET['word'] : null;
  $words_logic_flag = (!empty($_GET['words_logic_flag']) && is_numeric($_GET['words_logic_flag'])) ? $_GET['words_logic_flag'] : null;

  $words = (!empty($word)) ? explode(' ',mb_convert_kana($word,'s')) : null;
  $current_min_record = ($current_page - 1) * $record_span;
  
  $accounts = getEditAccount($words,$words_logic_flag,$reported_num,$delete_num,$delete_appear,$logic_flag,$current_page,$record_span);


  if(!empty($_POST)){
    $delete_all = (!empty($_POST['delete_all'])) ? true : false;
    $restore_all = (!empty($_POST['restore_all'])) ? true : false;
    
    if(!empty($delete_all)){
      //選択したもの全て削除
      if(!empty($_POST['accounts'])){
        foreach($_POST['accounts'] as $key => $val){
          //$val １つ１つに対して、delete_flag = 1に更新する
          try{
            $dbh = dbConnect();
            $sql = 'UPDATE user SET delete_flag = 1 WHERE u_id = :u_id';
            $data = array(':u_id' => $val);
            
            $stmt = queryPost($dbh,$sql,$data);
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

        //成功メッセージ
        $_SESSION['success'] = MSG27;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();

      }else{
        debug('バリデーションエラー');
        appendErr('common',REQ02);
      }

    }elseif(!empty($restore_all)){
      //選択したもの全て復元
      if(!empty($_POST['accounts'])){
        foreach($_POST['accounts'] as $key => $val){
          //$val １つ１つに対して、delete_flag = 0に更新する
          try{
            $dbh = dbConnect();
            $sql = 'UPDATE user SET delete_flag = 0 WHERE u_id = :u_id';
            $data = array(':u_id' => $val);
            
            $stmt = queryPost($dbh,$sql,$data);
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

        //成功メッセージ
        $_SESSION['success'] = MSG28;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();

      }else{
        debug('バリデーションエラー');
        appendErr('common',REQ03);
      }

    }else{
      //個別に削除または復元
      $target = array_key_last($_POST);

      if(end($_POST) === '削除'){
        //削除
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE user SET delete_flag = 1 WHERE u_id = :u_id';
          $data = array(':u_id' => $target);
          
          $stmt = queryPost($dbh,$sql,$data);

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

        //成功メッセージ
        $_SESSION['success'] = MSG27;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();
        
      }elseif(end($_POST) === '復元'){
        debug('復元を押した');
        //復元
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE user SET delete_flag = 0 WHERE u_id = :u_id';
          $data = array(':u_id' => $target);
          
          $stmt = queryPost($dbh,$sql,$data);

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

        //成功メッセージ
        $_SESSION['success'] = MSG28;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();
      }
    }
  }

  $title = 'ユーザーID管理';
  require('head.php');
  debugStart();

?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <div class="p-modal">
      <div class="p-modal__modal-back js-modal-back js-modal-quit"></div>
      <div class="p-modal__modal-window js-modal-window">
        <?php require('modal_accounts_search_menu.php'); ?>
      </div>
    </div>
    <main class="l-container">
      <div class="p-search-top">
        <h2 class="p-search-top__page-title c-page-title"><?php echo $title; ?></h2>
        <?php if(!empty($accounts['total_record']) && !empty($accounts['data'])){ ?>
        <p class="p-search-top__search-result c-search-result"><?php echo $accounts['total_record']; ?>件のIDが見つかりました<br><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?php echo $current_page; ?>ページ目&nbsp;&nbsp;<?php echo $current_min_record + 1; ?>&nbsp;-&nbsp;<?php echo count($accounts['data']) + $current_min_record; ?>件</p>
        <?php }else{ ?>
        <p class="p-search-top__search-result c-search-result">ユーザーIDは見つかりませんでした</p>
        <?php } ?>
        <p class="p-search-top__search-option js-modal-trigger">検索オプション</p>
      </div>
      <div class="l-col2">
        <article class="l-main">
          <div class="p-found-record">
            <div class="p-found-record__err-msg c-err-msg"><?php echoErr('common'); ?></div>
            <form action="" method="post">
              <div class="p-found-record__cmd-wrapper">
                <?php if(empty($delete_appear)){ ?>
                <div><input type="submit" name="delete_all" value="選択したものを削除" class="p-found-record__choice-cmd c-choice-cmd"></div>
                <?php }else{ ?>
                <div><input type="submit" name="restore_all" value="選択したものを復元" class="p-found-record__choice-cmd c-choice-cmd"></div>
                <?php } ?>
                <div>
                  <input type="checkbox" id="js-check-all" class="p-found-record__checkboxform c-checkboxform js-search-checkbox">
                  <label for="js-check-all" class="p-found-record__select-all-cmd c-select-all-cmd">全て選択&nbsp;/&nbsp;全て選択解除</label>
                </div>
                <p class="p-found-record__check-num c-check-num js-check-num"></p>
              </div>
              <div>
                <?php
                  if(!empty($accounts['data'])){
                    foreach($accounts['data'] as $key => $val){
                ?>
                <div class="p-found-record__record-row">
                  <div>
                    <input type="checkbox" name="accounts[]" value="<?php echo $val['u_id']; ?>" id="u_id<?php echo $val['u_id']; ?>" class="p-found-record__checkboxform c-checkboxform js-record-checkbox js-search-checkbox">
                    <label for="u_id<?php echo $val['u_id']; ?>" class="p-found-record__record-item c-record-item"><span class="p-found-record__notice"><?php if(!empty($val['delete_flag'])) echo '削除済&nbsp;'; ?></span><?php echo $val['login_id']; ?></label>
                  </div>
                  <div>
                    <span class="p-found-record__record-info c-record-info">通報&nbsp;<span class="<?php if(!empty($val['reported_num'])) echo 'p-found-record__notice'; ?>"><?php echo $val['reported_num']; ?></span>回</span>
                    <span class="p-found-record__record-info c-record-info">コメント削除&nbsp;<span class="<?php if(!empty($val['delete_num'])) echo 'p-found-record__notice'; ?>"><?php echo $val['delete_num']; ?></span>回</span>
                    <input type="submit" name="<?php echo $val['u_id']; ?>" value="<?php echo (!empty($val['delete_flag'])) ? '復元' : '削除'; ?>" class="p-found-record__cmd c-choice-cmd">
                  </div>
                </div>
                <?php
                      }
                    }
                ?>
              </div>
            </form>
            <?php
              if(!empty($accounts['total_page']) && !empty($accounts['data'])){
                pagenation('admin_account_search_secret.php',$current_page,$accounts['total_page']);
              }
            ?>
          </div>
        </article>
        <aside class="l-sub">
          <?php
            require('side_admin_menu.php');
          ?>
        </aside>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>