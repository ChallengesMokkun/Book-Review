<?php
  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');

  $record_span = 12;
  //GETパラメータ
  $current_page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
  $word = (!empty($_GET['word'])) ? $_GET['word'] : null;
  $words_logic_flag = (!empty($_GET['words_logic_flag']) && is_numeric($_GET['words_logic_flag'])) ? $_GET['words_logic_flag'] : null;
  $g_id = (!empty($_GET['g_id'])  && is_numeric($_GET['g_id'])) ? $_GET['g_id'] : null;
  $category = (!empty($_GET['category'])) ? $_GET['category'] : null;
  $category_flag = (!empty($_GET['category_flag']) && is_numeric($_GET['category_flag'])) ? $_GET['category_flag'] : null;
  $s_id = (!empty($_GET['s_id']) && is_numeric($_GET['s_id'])) ? $_GET['s_id'] : null;
  $year = (!empty($_GET['year']) && is_numeric($_GET['year'])) ? $_GET['year'] : null;
  $delete_appear = (!empty($_GET['delete_appear'])) ? $_GET['delete_appear'] : null;
  $logic_flag = (!empty($_GET['logic_flag']) && is_numeric($_GET['logic_flag'])) ? $_GET['logic_flag'] : null;
  $flow_flag = (isset($_GET['flow_flag']) && is_numeric($_GET['flow_flag'])) ? $_GET['flow_flag'] : 0;

  $words = (!empty($word)) ? explode(' ',mb_convert_kana($word,'s')) : null;
  $current_min_record = ($current_page - 1) * $record_span;

  $contents = getSearchContent($words,$words_logic_flag,$g_id,$category,$category_flag,$s_id,$year,$delete_appear,$logic_flag,$flow_flag,$current_page,$record_span);

  
  //DBから各属性を読み込む
  $sellers = getSeller();
  $genres = getGenre();
  $categories = getCategory();

  if(!empty($_POST)){
    $delete_all = (!empty($_POST['delete_all'])) ? true : false;
    $restore_all = (!empty($_POST['restore_all'])) ? true : false;

    if(!empty($delete_all)){
      //選択したもの全て削除
      if(!empty($_POST['contents'])){
        foreach($_POST['contents'] as $key => $val){
          //$val １つ１つに対して、delete_flag = 1に更新する
          try{
            $dbh = dbConnect();
            $sql = 'UPDATE book SET delete_flag = 1 WHERE b_id = :b_id';
            $data = array(':b_id' => $val);
            
            $stmt = queryPost($dbh,$sql,$data);
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

        //成功メッセージ
        $_SESSION['success'] = MSG03;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();

      }else{
        debug('バリデーションエラー');
        appendErr('common',REQ02);
      }

    }elseif(!empty($restore_all)){
      //選択したもの全て復元
      if(!empty($_POST['contents'])){
        foreach($_POST['contents'] as $key => $val){
          //$val １つ１つに対して、delete_flag = 0に更新する
          try{
            $dbh = dbConnect();
            $sql = 'UPDATE book SET delete_flag = 0 WHERE b_id = :b_id';
            $data = array(':b_id' => $val);
            
            $stmt = queryPost($dbh,$sql,$data);
            
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }
        }

        //成功メッセージ
        $_SESSION['success'] = MSG04;
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
          $sql = 'UPDATE book SET delete_flag = 1 WHERE b_id = :b_id';
          $data = array(':b_id' => $target);
          
          $stmt = queryPost($dbh,$sql,$data);

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

        //成功メッセージ
        $_SESSION['success'] = MSG03;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();
        
      }elseif(end($_POST) === '復元'){
        debug('復元を押した');
        //復元
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE book SET delete_flag = 0 WHERE b_id = :b_id';
          $data = array(':b_id' => $target);
          
          $stmt = queryPost($dbh,$sql,$data);

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

        //成功メッセージ
        $_SESSION['success'] = MSG04;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();
      }
    }
  }

  $title = '書籍管理';
  require('head.php');
  debugStart();
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <div class="p-modal">
      <div class="p-modal__modal-back js-modal-back js-modal-quit"></div>
      <div class="p-modal__modal-window js-modal-window">
        <?php require('modal_contents_search_menu.php'); ?>
      </div>
    </div>
    <main class="l-container">
      <div class="p-search-top">
        <h2 class="p-search-top__page-title c-page-title"><?php echo $title; ?></h2>
        <?php if(!empty($contents['total_record']) && !empty($contents['data'])){ ?>
        <p class="p-search-top__search-result c-search-result"><?php echo $contents['total_record']; ?>件の書籍が見つかりました<br><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?php echo $current_page; ?>ページ目&nbsp;&nbsp;<?php echo $current_min_record + 1; ?>&nbsp;-&nbsp;<?php echo count($contents['data']) + $current_min_record; ?>件</p>
        <?php }else{ ?>
        <p class="p-search-top__search-result c-search-result">書籍は見つかりませんでした</p>
        <?php } ?>
        <p class="p-search-top__search-option js-modal-trigger">検索オプション</p>
      </div>
      <div class="l-col2">
        <article class="l-main">
          <div class="l-main__err-msg c-err-msg"><?php echoErr('common'); ?></div>
          <form action="" method="post">
            <div class="p-found-record__cmd-wrapper">
              <?php if(empty($delete_appear)){ ?>
              <div><input type="submit"  name="delete_all" value="選択したものを削除" class="p-found-record__choice-cmd c-choice-cmd"></div>
              <?php }else{ ?>
              <div><input type="submit" name="restore_all" value="選択したものを復元" class="p-found-record__choice-cmd c-choice-cmd"></div>
              <?php } ?>
              <input type="checkbox" id="js-check-all" class="p-found-record__checkboxform c-checkboxform js-search-checkbox">
              <label for="js-check-all" class="p-found-record__select-all-cmd c-select-all-cmd">全て選択&nbsp;/&nbsp;全て選択解除</label>
              <p class="p-found-record__check-num c-check-num js-check-num"></p>
            </div>
            <div class="p-found-content">
            <?php
                if(!empty($contents)){
                  foreach($contents['data'] as $key => $val){
            ?>
              <div class="p-found-content__content-panel c-content-panel">
                <div class="p-found-content__img-wrapper u-pos-relative">
                  <input type="checkbox" name="contents[]" value="<?php echo $val['b_id']; ?>" class="p-found-content__checkbox c-content-panel__checkbox js-record-checkbox js-search-checkbox">
                  <a href="content_detail.php<?php echo (!empty(keepGETparam())) ? keepGETparam().'&b_id='.$val['b_id'] : '?b_id='.$val['b_id']; ?>" class="p-found-content__img-link"><img src="<?php displayImg($val['pic1']); ?>" alt="書籍" class="p-found-content__img"></a>
                </div>
                <p class="p-found-content__title c-content-panel__title"><a href="content_detail.php<?php echo (!empty(keepGETparam())) ? keepGETparam().'&b_id='.$val['b_id'] : '?b_id='.$val['b_id']; ?>" class="p-found-content__title-link c-content-panel__title-link"><?php echo (mb_strlen($val['content_title']) > 27) ? mb_substr(preg_replace('/　|\s+/','', sanitize($val['content_title'])),0,27,'UTF-8').'...' : preg_replace('/　|\s+/','', sanitize($val['content_title'])); ?></a></p>
                <table class="p-found-content__table c-content-panel__table">
                  <tr><td class="p-found-content__left-cell"><i class="fa-sharp fa-solid fa-star p-found-content__fonticon c-fonticon"></i></td><td class="p-found-content__right-cell"><?php echo $val['average_rate']; ?></td></tr>
                  <tr><td class="p-found-content__left-cell"><i class="fa-sharp fa-solid fa-heart p-found-content__fonticon c-fonticon"></i></td><td class="p-found-content__right-cell"><?php echo $val['fav_num']; ?></td></tr>
                  <tr><td class="p-found-content__left-cell"><a href="admin_content_edit_secret.php?b_id=<?php echo $val['b_id']; ?>" class="p-found-content__cmd c-choice-cmd">編集</a></td><td class="p-found-content__right-cell"><input type="submit" name="<?php echo $val['b_id']; ?>" value="<?php echo (!empty($val['delete_flag'])) ? '復元' : '削除'; ?>" class="p-found-content__cmd c-choice-cmd"></td></tr>
                </table>
              </div>
            <?php
                  }
                }
            ?>
            </div>
          </form>
          <?php
            if(!empty($contents['total_page']) && !empty($contents['data'])){
              pagenation('admin_content_search_secret.php',$current_page,$contents['total_page']);
            }
          ?>
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