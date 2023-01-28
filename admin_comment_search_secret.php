<?php
  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');

  $record_span = 20;
  //GETパラメータ
  $current_page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
  $word = (!empty($_GET['word'])) ? $_GET['word'] : null;
  $reported = (!empty($_GET['reported'])) ? $_GET['reported'] : null;
  $delete_appear = (!empty($_GET['delete_appear'])) ? $_GET['delete_appear'] : null;
  $flow_flag = (!empty($_GET['flow_flag']) && is_numeric($_GET['flow_flag'])) ? $_GET['flow_flag'] : null;
  $logic_flag = (!empty($_GET['logic_flag']) && is_numeric($_GET['logic_flag'])) ? $_GET['logic_flag'] : null;
  $words_logic_flag = (!empty($_GET['words_logic_flag']) && is_numeric($_GET['words_logic_flag'])) ? $_GET['words_logic_flag'] : null;

  $words = (!empty($word)) ? explode(' ',mb_convert_kana($word,'s')) : null;
  $current_min_record = ($current_page - 1) * $record_span;

  $comments = getSearchComment($words,$words_logic_flag,$reported,$delete_appear,$flow_flag,$logic_flag,$current_page,$record_span);

  if(!empty($_POST)){
    $delete_all = (!empty($_POST['delete_all'])) ? true : false;
    
    if(!empty($delete_all)){
      //選択したもの全て物理削除
      if(!empty($_POST['comments'])){
        foreach($_POST['comments'] as $key => $val){
          //$val １つ１つに対して、物理削除
          try{
            $dbh = dbConnect();
            $sql = 'DELETE FROM comment WHERE delete_flag = 1 AND com_id = :com_id';
            $data = array(':com_id' => $val);
            
            $stmt = queryPost($dbh,$sql,$data);
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

        //成功メッセージ
        $_SESSION['success'] = MSG25;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();

      }else{
        debug('バリデーションエラー');
        appendErr('common',REQ02);
      }

    }else{
      //個別に削除または復元または物理削除

      if(in_array('削除',$_POST,true)){
        //削除
        $com_id = array_search('削除',$_POST,true);
        $post_data = array_values($_POST);
        $b_id = $post_data[(array_search('削除',array_values($_POST),true) - 2)];
        $u_id = $post_data[(array_search('削除',array_values($_POST),true) - 1)];

        $nums = getReportDeleteNum($u_id);
        if(!empty($nums)){
          $delete_num = $nums['delete_num'];
        }

        $com_num = getComNum($b_id);

        try{
          $dbh = dbConnect();
          $sql = 'UPDATE comment SET delete_flag = 1 WHERE com_id = :com_id';
          $data = array(':com_id' => $com_id);
          
          $stmt = queryPost($dbh,$sql,$data);

          //削除回数の更新
          if(!empty($nums)){
            $sql = 'UPDATE user SET delete_num = :delete_num WHERE u_id = :u_id';
            $data = array(
              ':delete_num' => $delete_num + 1,
              ':u_id' => $u_id
            );
  
            $stmt = queryPost($dbh,$sql,$data);
          }
          //コメント数の更新
          if(is_numeric($com_num)){
            $sql = 'UPDATE book SET com_num = :com_num WHERE b_id = :b_id';
            $data = array(
              ':com_num' => $com_num - 1,
              ':b_id' => $b_id
            );

            $stmt = queryPost($dbh,$sql,$data);
          }
          

          if($stmt){
            //成功メッセージ
            $_SESSION['success'] = MSG10;
            //ページリロード
            header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
            exit();
          }

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

      }elseif(in_array('復元',$_POST,true)){
        debug('復元を押した');
        //復元
        $com_id = array_search('復元',$_POST,true);
        $post_data = array_values($_POST);
        $b_id = $post_data[(array_search('復元',array_values($_POST),true) - 2)];
        $u_id = $post_data[(array_search('復元',array_values($_POST),true) - 1)];

        $nums = getReportDeleteNum($u_id);
        if(!empty($nums)){
          $delete_num = $nums['delete_num'];
        }

        $com_num = getComNum($b_id);

        try{
          $dbh = dbConnect();
          $sql = 'UPDATE comment SET delete_flag = 0 WHERE com_id = :com_id';
          $data = array(':com_id' => $com_id);
          
          $stmt = queryPost($dbh,$sql,$data);

          //削除回数の更新(ユーザー)
          if(!empty($nums)){
            $sql = 'UPDATE user SET delete_num = :delete_num WHERE u_id = :u_id';
            $data = array(
              ':delete_num' => $delete_num - 1,
              ':u_id' => $u_id
            );
  
            $stmt = queryPost($dbh,$sql,$data);
          }

          //コメント数の更新
          if(is_numeric($com_num)){
            $sql = 'UPDATE book SET com_num = :com_num WHERE b_id = :b_id';
            $data = array(
              ':com_num' => $com_num + 1,
              ':b_id' => $b_id
            );

            $stmt = queryPost($dbh,$sql,$data);
          }

          if($stmt){
            //成功メッセージ
            $_SESSION['success'] = MSG26;
            //ページリロード
            header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
            exit();
          }

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

      }elseif(in_array('完全削除',$_POST,true)){
        //物理削除
        $com_id = array_search('完全削除',$_POST,true);
        $post_data = array_values($_POST);
        $u_id = $post_data[(array_search('完全削除',array_values($_POST),true) - 1)];

        try{
          $dbh = dbConnect();
          $sql = 'DELETE FROM comment WHERE delete_flag = 1 AND com_id = :com_id';
          $data = array(':com_id' => $com_id);
          
          $stmt = queryPost($dbh,$sql,$data);

          if($stmt){
            //成功メッセージ
            $_SESSION['success'] = MSG25;
            //ページリロード
            header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
            exit();
          }

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }
      }
    }
  }

  $title = 'コメント検索';
  require('head.php');
  debugStart();
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <div class="p-modal">
      <div class="p-modal__modal-back js-modal-back js-modal-quit"></div>
      <div class="p-modal__modal-window js-modal-window">
        <?php require('modal_comments_search_menu.php'); ?>
      </div>
    </div>
    <main class="l-container">
      <div class="p-search-top">
        <h2 class="p-search-top__page-title c-page-title"><?php echo $title; ?></h2>
        <?php if(!empty($comments['total_record']) && !empty($comments['data'])){ ?>
        <p class="p-search-top__search-result c-search-result"><?php echo $comments['total_record']; ?>件のコメントが見つかりました<br><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?php echo $current_page; ?>ページ目&nbsp;&nbsp;<?php echo $current_min_record + 1; ?>&nbsp;-&nbsp;<?php echo count($comments['data']) + $current_min_record; ?>件</p>
        <?php }else{ ?>
        <p class="p-search-top__search-result c-search-result">コメントは見つかりませんでした</p>
        <?php } ?>
        <p class="p-search-top__search-option js-modal-trigger">検索オプション</p>
      </div>
      <div class="l-col2">
        <article class="l-main">
          <div class="p-found-record">
            <div class="p-found-record__err-msg c-err-msg"><?php echoErr('common'); ?></div>
            <form action="" method="post">
              <?php if(!empty($delete_appear)){ ?>
              <div class="p-found-record__cmd-wrapper">
                <div><input type="submit" name="delete_all" value="選択したものを完全に削除" class="p-found-record__choice-cmd c-choice-cmd"></div>
                <div>
                  <input type="checkbox" id="js-check-all" class="p-found-record__checkboxform c-checkboxform js-search-checkbox">
                  <label for="js-check-all" class="p-found-record__select-all-cmd c-select-all-cmd">全て選択&nbsp;/&nbsp;全て選択解除</label>
                </div>
                <p class="p-found-record__check-num c-check-num js-check-num"></p>
              </div>
              <?php } ?>
              <div>
                <?php
                  if(!empty($comments['data'])){
                    foreach($comments['data'] as $key => $val){
                ?>
                <div class="p-found-record__record-row">
                  <div>
                    <?php if(!empty($delete_appear)){ ?>
                    <input type="checkbox" name="comments[]" value="<?php echo sanitize($val['com_id']); ?>" id="com_id<?php echo sanitize($val['com_id']); ?>" class="p-found-record__checkboxform c-checkboxform js-record-checkbox js-search-checkbox">
                    <?php } ?>
                    <span class="p-found-record__notice"><?php if(!empty($val['delete_flag'])) echo '削除済&nbsp;'; ?></span>
                    <a href="content_detail.php?b_id=<?php echo sanitize($val['b_id']); ?>#com_id<?php echo sanitize($val['com_id']); ?>" class="p-found-record__record-link">
                      <?php echo (mb_strlen($val['text']) > 39) ? mb_substr(sanitize($val['text']),0,39,'UTF-8').'...' : sanitize($val['text']); ?>
                    </a>
                  </div>
                  <div>
                    <?php if(!empty($delete_appear)){ ?>
                    <label for="com_id<?php echo sanitize($val['com_id']); ?>" class="p-found-record__record-info c-record-info"><?php echo mb_substr(sanitize($val['create_date']),0,-3,'UTF-8'); ?></label>
                    <?php }else{ ?>
                    <span class="p-found-record__record-info c-record-info"><?php echo mb_substr(sanitize($val['create_date']),0,-3,'UTF-8'); ?></span>
                    <?php } ?>
                    <span class="p-found-record__record-info c-record-info">通報&nbsp;<span class="p-found-record__notice"><?php echo sanitize($val['reported_num']); ?></span>件</span>
                    <input type="hidden" name="<?php echo 'com_id'.sanitize($val['com_id']).'_b_id'; ?>" value="<?php echo sanitize($val['b_id']); ?>">
                    <input type="hidden" name="<?php echo 'com_id'.sanitize($val['com_id']).'_u_id'; ?>" value="<?php echo sanitize($val['u_id']); ?>">
                    <input type="submit" name="<?php echo sanitize($val['com_id']); ?>" value="<?php echo (!empty($val['delete_flag'])) ? '復元' : '削除'; ?>" class="p-found-record__cmd c-choice-cmd <?php if(!empty($val['delete_flag'])) echo 'u-margin-r-sm'; ?>">
                    <?php if(!empty($val['delete_flag'])){ ?>
                    <input type="submit" name="<?php echo sanitize($val['com_id']); ?>" value="完全削除" class="p-found-record__cmd c-choice-cmd">
                    <?php } ?>
                  </div>
                </div>
                <?php
                      }
                    }
                ?>
              </div>
            </form>
            <?php
              if(!empty($inquiries['total_page']) && !empty($comments['data'])){
                pagenation('admin_comment_search_secret.php',$current_page,$comments['total_page']);
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