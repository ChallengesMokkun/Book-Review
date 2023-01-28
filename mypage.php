<?php
  //function.phpを読み込む
  require('function.php');
  //user_auth.phpを読み込む
  require('user_auth.php');
  //DBからお気に入り最新8件を読み込む

  $record_span = 12;
  $u_id = $_SESSION['u_id'];

  $contents = getSearchFavorite($u_id,null,null,null,null,null,null,null,null,null,0,1,$record_span);

  $title = 'マイページ';
  require('head.php');
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <main class="l-container">
    <div class="p-mypage-top">
      <h2 class="p-mypage-top__page-title c-page-title"><?php echo $title; ?></h2>
      <?php if(!empty($contents['total_record'])){ ?>
      <p class="p-mypage-top__text"><a href="favorite.php" class="p-mypage-top__link">お気に入り一覧へ</a></p>
      <p class="p-mypage-top__search-result c-search-result">お気に入りの書籍&nbsp;最新<?php echo count($contents['data']); ?>件</p>
      <?php }else{ ?>
      <p class="p-mypage-top__search-result c-search-result">
        まだお気に入りはありません<br>
        書籍のページでハートマークを押すとお気に入り登録できます
      </p>
      <?php } ?>
    </div>
      <div class="l-col2">
        <article class="l-main">
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
              </table>
            </div>
          <?php
              }
            }
          ?>
          </div>
        </article>
        <aside class="l-sub">
          <?php
            require('side_user_menu.php');
          ?>
        </aside>
      </div>
    </main>
<?php
  require('footer.php');

?>