<?php

  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');
  
  $title = '管理人メニュー';
  require('head.php');
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1-form">
        <h2 class="l-col1-form__page-title c-page-title"><?php echo $title; ?></h2>
        <div class="p-menu-page">
          <nav>
            <ul class="p-menu-page__lists">
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_content_edit_secret.php" class="p-menu-page__link c-menu-link__link">書籍追加</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_inquiry_search_secret.php" class="p-menu-page__link c-menu-link__link">問い合わせ一覧</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_content_search_secret.php" class="p-menu-page__link c-menu-link__link">書籍管理</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_seller_search_secret.php" class="p-menu-page__link c-menu-link__link">出版社名管理</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_genre_search_secret.php" class="p-menu-page__link c-menu-link__link">ジャンル管理</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_category_search_secret.php" class="p-menu-page__link c-menu-link__link">カテゴリー管理</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_comment_search_secret.php" class="p-menu-page__link c-menu-link__link">コメント管理</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_account_search_secret.php" class="p-menu-page__link c-menu-link__link">ユーザーID管理</a></li>
              <li class="p-menu-page__menu-link c-menu-link"><a href="admin_pass_edit_secret.php" class="p-menu-page__link c-menu-link__link">パスワード変更</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </main>
<?php
  require('footer.php');

?>