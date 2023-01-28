<?php
  //function.phpを読み込む
  require('function.php');
  //user_auth.phpを読み込む
  require('user_auth.php');
  
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
  
  $logic_flag = (!empty($_GET['logic_flag']) && is_numeric($_GET['logic_flag'])) ? $_GET['logic_flag'] : null;
  $flow_flag = (isset($_GET['flow_flag']) && is_numeric($_GET['flow_flag'])) ? $_GET['flow_flag'] : 0;

  $words = (!empty($word)) ? explode(' ',mb_convert_kana($word,'s')) : null;
  $current_min_record = ($current_page - 1) * $record_span;

  //DBから各属性を読み込む
  $sellers = getSeller();
  $genres = getGenre();
  $categories = getCategory();

  $u_id = $_SESSION['u_id'];

  $contents = getSearchFavorite($u_id,$words,$words_logic_flag,$g_id,$category,$category_flag,$s_id,$year,null,$logic_flag,$flow_flag,$current_page,$record_span);

  if(!empty($_POST)){
    $f_id = array_search('削除',$_POST,true);
    $post_data = array_values($_POST);
    $b_id = $post_data[(array_search('削除',array_values($_POST),true) - 1)];

    $fav_num = getFavNum($b_id);

    //削除
    try{
      $dbh = dbConnect();
      $sql = 'DELETE FROM favorite WHERE f_id = :f_id';
      $data = array(':f_id' => $f_id);
      
      $stmt = queryPost($dbh,$sql,$data);

      if(is_numeric($fav_num)){
        //お気に入り数の更新
        $sql = 'UPDATE book SET fav_num = :fav_num WHERE b_id = :b_id AND delete_flag = 0';
        $data = array(
          ':fav_num' => $fav_num - 1,
          ':b_id' => $b_id
        );

        $stmt = queryPost($dbh,$sql,$data);
      }

      if($stmt){
        //成功メッセージ
        $_SESSION['success'] = MSG14;
        //ページリロード
        header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
        exit();
      }
    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      appendErr('common',ERR01);
    }
  }
  $title = 'お気に入り一覧';
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
        <p class="p-search-top__search-result c-search-result"><?php echo $contents['total_record']; ?>件のお気に入りが見つかりました<br><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?php echo $current_page; ?>ページ目&nbsp;&nbsp;<?php echo $current_min_record + 1; ?>&nbsp;-&nbsp;<?php echo count($contents['data']) + $current_min_record; ?>件</p>
        <?php }else{ ?>
        <p class="p-search-top__search-result c-search-result">お気に入りは見つかりませんでした</p>
        <?php } ?>
        <p class="p-search-top__search-option js-modal-trigger">検索オプション</p>
      </div>
      <div class="l-col2">
        <article class="l-main">
          <div class="l-main__err-msg c-err-msg"><?php echoErr('common'); ?></div>
          <form action="" method="post">
            <div class="p-found-content">
            <?php
              if(!empty($contents)){
                foreach($contents['data'] as $key => $val){
            ?>
              <div class="p-found-content__content-panel c-content-panel">
                <div class="p-found-content__img-wrapper">
                  <a href="content_detail.php<?php echo (!empty(keepGETparam())) ? keepGETparam().'&b_id='.$val['b_id'] : '?b_id='.$val['b_id']; ?>" class="p-found-content__img-link"><img src="<?php displayImg($val['pic1']); ?>" alt="書籍" class="p-found-content__img"></a>
                </div>
                <p class="p-found-content__title c-content-panel__title">
                  <a href="content_detail.php<?php echo (!empty(keepGETparam())) ? keepGETparam().'&b_id='.$val['b_id'] : '?b_id='.$val['b_id']; ?>" class="p-found-content__title-link c-content-panel__title-link">
                    <?php echo (mb_strlen($val['content_title']) > 27) ? mb_substr(preg_replace('/　|\s+/','', sanitize($val['content_title'])),0,27,'UTF-8').'...' : preg_replace('/　|\s+/','', sanitize($val['content_title'])); ?>
                  </a>
                </p>
                <table class="p-found-content__table c-content-panel__table">
                  <tr><td class="p-found-content__left-cell"><i class="fa-sharp fa-solid fa-star p-found-content__fonticon c-fonticon"></i></td><td class="p-found-content__right-cell"><?php echo $val['average_rate']; ?></td></tr>
                  <tr><td class="p-found-content__left-cell"><i class="fa-sharp fa-solid fa-heart p-found-content__fonticon c-fonticon c-fonticon--active-fav"></i></td><td class="p-found-content__right-cell"><?php echo $val['fav_num']; ?></td></tr>
                  <tr><td class="p-found-content__left-cell"><input type="hidden" name="<?php echo 'b_id'.sanitize($val['b_id']); ?>" value="<?php echo sanitize($val['b_id']); ?>"><input type="submit" name="<?php echo $val['f_id']; ?>" value="削除" class="p-found-content__cmd c-choice-cmd"></td><td class="p-found-content__right-cell"></td></tr>
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
              pagenation('favorite.php',$current_page,$contents['total_page']);
            }
          ?>
        </article>
        <aside class="l-sub">
          <?php require('side_user_menu.php'); ?>
        </aside>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>