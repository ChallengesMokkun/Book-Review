<?php
  if(basename($_SERVER['PHP_SELF']) === 'side_admin_menu.php'){
    header('Location:index.php');
    exit();
  }
?>
<div class="p-sidebar">
  <div class="p-sidebar__label c-side-menu-label">
    <p class="p-sidebar__title c-side-menu-label__title">管理人メニュー</p>
  </div>
  <nav class="p-sidebar__nav">
    <ul class="p-sidebar__lists">
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_menu_page_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">メニューページ</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_inquiry_search_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">問い合わせ一覧</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_content_edit_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">書籍追加</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_content_search_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">書籍管理</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_seller_search_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">出版社名管理</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_genre_search_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">ジャンル管理</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_category_search_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">カテゴリー管理</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_comment_search_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">コメント管理</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_account_search_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">ユーザーID管理</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="admin_pass_edit_secret.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">パスワード変更</a></li>
    </ul>
  </nav>
</div>