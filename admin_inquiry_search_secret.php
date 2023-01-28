<?php
  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');

  $record_span = 20;
  //GETパラメータ
  $current_page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
  $subject_id = (!empty($_GET['subject_id']) && is_numeric($_GET['subject_id'])) ? $_GET['subject_id'] : null;
  $start = (!empty($_GET['start']) && (empty($_GET['finish']) || (!empty($_GET['finish']) && strtotime(local2Datetime($_GET['finish'])) > strtotime(local2Datetime($_GET['start']))))) ? local2Datetime($_GET['start']) : null; //datetime-localからY-m-d H:i:sに変換する
  $finish = (!empty($_GET['finish']) && (empty($_GET['start']) || (!empty($_GET['start']) && strtotime(local2Datetime($_GET['finish'])) > strtotime(local2Datetime($_GET['start']))))) ? local2Datetime($_GET['finish']) : null; //datetime-localからY-m-d H:i:sに変換する
  $delete_appear = (!empty($_GET['delete_appear'])) ? $_GET['delete_appear'] : null;
  $flow_flag = (!empty($_GET['flow_flag']) && is_numeric($_GET['flow_flag'])) ? $_GET['flow_flag'] : null;
  $logic_flag = (!empty($_GET['logic_flag']) && is_numeric($_GET['logic_flag'])) ? $_GET['logic_flag'] : null;
  $word = (!empty($_GET['word']) && mb_strlen($_GET['word']) <= 255) ? $_GET['word'] : null;
  $words_logic_flag = (!empty($_GET['words_logic_flag']) && is_numeric($_GET['words_logic_flag'])) ? $_GET['words_logic_flag'] : null;

  $words = (!empty($word)) ? explode(' ',mb_convert_kana($word,'s')) : null;
  $current_min_record = ($current_page - 1) * $record_span;

  $subject_list = getSubject();

  //問い合わせを取得する
  $inquiries = getSearchInquiry($words,$words_logic_flag,$subject_id,$start,$finish,$delete_appear,$logic_flag,$flow_flag,$current_page,$record_span);


  if(!empty($_POST)){
    $delete_all = (!empty($_POST['delete_all'])) ? true : false;
    $restore_all = (!empty($_POST['restore_all'])) ? true : false;
    
    if(!empty($delete_all)){
      //選択したもの全て対応済にする
      if(!empty($_POST['inquiries'])){
        foreach($_POST['inquiries'] as $key => $val){
          //$val １つ１つに対して、delete_flag = 1に更新する
          try{
            $dbh = dbConnect();
            $sql = 'UPDATE inquiry SET delete_flag = 1 WHERE i_id = :i_id';
            $data = array(':i_id' => $val);
            
            $stmt = queryPost($dbh,$sql,$data);
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

        //成功メッセージ
        $_SESSION['success'] = MSG29;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();

      }else{
        debug('バリデーションエラー');
        appendErr('common',REQ05);
      }

    }elseif(!empty($restore_all)){
      //選択したもの全て復元
      if(!empty($_POST['inquiries'])){
        foreach($_POST['inquiries'] as $key => $val){
          //$val １つ１つに対して、delete_flag = 0に更新する
          try{
            $dbh = dbConnect();
            $sql = 'UPDATE inquiry SET delete_flag = 0 WHERE i_id = :i_id';
            $data = array(':i_id' => $val);
            
            $stmt = queryPost($dbh,$sql,$data);
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

        //成功メッセージ
        $_SESSION['success'] = MSG30;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();

      }else{
        debug('バリデーションエラー');
        appendErr('common',REQ06);
      }

    }else{
      //個別に削除または復元
      $target = array_key_last($_POST);

      if(end($_POST) === '対応済にする'){
        //削除
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE inquiry SET delete_flag = 1 WHERE i_id = :i_id';
          $data = array(':i_id' => $target);
          
          $stmt = queryPost($dbh,$sql,$data);

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

        //成功メッセージ
        $_SESSION['success'] = MSG29;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();
        
      }elseif(end($_POST) === '要対応に戻す'){
        //復元
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE inquiry SET delete_flag = 0 WHERE i_id = :i_id';
          $data = array(':i_id' => $target);
          
          $stmt = queryPost($dbh,$sql,$data);

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

        //成功メッセージ
        $_SESSION['success'] = MSG30;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();
      }
    }
  }


  $title = '問い合わせ一覧';
  require('head.php');
  debugStart();
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <div class="p-modal">
      <div class="p-modal__modal-back js-modal-back js-modal-quit"></div>
      <div class="p-modal__modal-window js-modal-window">
        <?php require('modal_inquiry_search_menu.php'); ?>
      </div>
    </div>
    <main class="l-container">
      <div class="p-search-top">
        <h2 class="p-search-top__page-title c-page-title"><?php echo $title; ?></h2>
        <?php if(!empty($inquiries['total_record']) && !empty($inquiries['data'])){ ?>
        <p class="p-search-top__search-result c-search-result"><?php echo $inquiries['total_record']; ?>件の問い合わせが見つかりました<br><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?php echo $current_page; ?>ページ目&nbsp;&nbsp;<?php echo $current_min_record + 1; ?>&nbsp;-&nbsp;<?php echo count($inquiries['data']) + $current_min_record; ?>件</p>
        <?php }else{ ?>
        <p class="p-search-top__search-result c-search-result">問い合わせは見つかりませんでした</p>
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
                <div><input type="submit"  name="delete_all" value="選択したものを対応済にする" class="p-found-record__choice-cmd c-choice-cmd"></div>
                <?php }else{ ?>
                <div><input type="submit" name="restore_all" value="選択したものを要対応に戻す" class="p-found-record__choice-cmd c-choice-cmd"></div>
                <?php } ?>
                <div>
                  <input type="checkbox" id="js-check-all" class="p-found-record__checkboxform c-checkboxform js-search-checkbox">
                  <label for="js-check-all" class="p-found-record__select-all-cmd c-select-all-cmd">全て選択&nbsp;/&nbsp;全て選択解除</label>
                </div>
                <p class="p-found-record__check-num c-check-num js-check-num"></p>
              </div>
              <div>
                <?php
                  if(!empty($inquiries['data'])){
                    foreach($inquiries['data'] as $key => $val){
                ?>
                <div class="p-found-record__record-row">
                  <div>
                    <input type="checkbox" name="inquiries[]" value="<?php echo $val['i_id']; ?>" id="i_id<?php echo $val['i_id']; ?>" class="p-found-record__checkboxform c-checkboxform js-record-checkbox js-search-checkbox">
                    <span class="p-found-record__record-info c-record-info <?php if(empty($val['delete_flag'])) echo 'p-found-record__notice'; ?>"><?php echo (!empty($val['delete_flag'])) ? '対応済' : '要対応'; ?></span>
                    <a href="admin_inquiry_detail_secret.php<?php echo (!empty(keepGETparam())) ? keepGETparam().'&i_id='.$val['i_id'] : '?i_id='.$val['i_id']; ?>" class="p-found-record__record-link">
                      【<?php echo $subject_list[($val['subject_id'] - 1)]['subject']; ?>】&nbsp;<?php echo (mb_strlen($val['text']) > 24) ? mb_substr(sanitize($val['text']),0,24,'UTF-8') : sanitize($val['text']); ?>...
                    </a>
                  </div>
                  <div>
                    <label for="i_id<?php echo $val['i_id']; ?>" class="p-found-record__record-info c-record-info"><?php echo mb_substr(sanitize($val['create_date']),0,-3,'UTF-8'); ?></label>
                    <input type="submit" name="<?php echo $val['i_id']; ?>" value="<?php echo (!empty($val['delete_flag'])) ? '要対応に戻す' : '対応済にする'; ?>" class="p-found-record__cmd c-choice-cmd">
                  </div>
                </div>
                <?php
                      }
                    }
                ?>
              </div>
            </form>
            <?php
              if(!empty($inquiries['total_page']) && !empty($inquiries['data'])){
                pagenation('admin_inquiry_search_secret.php',$current_page,$inquiries['total_page']);
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