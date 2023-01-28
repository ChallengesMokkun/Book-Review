<?php
  if(basename($_SERVER['PHP_SELF']) === 'side_user_menu.php'){
    header('Location:index.php');
    exit();
  }
?>
<div class="p-sidebar">
  <div class="p-sidebar__label c-side-menu-label">
    <p class="p-sidebar__title c-side-menu-label__title">マイメニュー</p>
    <?php if(basename($_SERVER['PHP_SELF']) !== 'mypage.php'){ ?>
    <?php } ?>
  </div>
  <nav class="p-sidebar__nav">
    <ul class="p-sidebar__lists">
      <!--マイページ以外で表示-->
      <?php if(basename($_SERVER['PHP_SELF']) !== 'mypage.php'){ ?>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="mypage.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">マイページ</a></li>
      <?php } ?>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="favorite.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">お気に入り一覧</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="prof_edit.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">プロフィール変更</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="email_edit.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">メールアドレス変更</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="id_edit.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">ログインID変更</a></li>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="pass_edit.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">パスワード変更</a></li>
      <!--マイページでのみ表示-->
      <?php if(basename($_SERVER['PHP_SELF']) === 'mypage.php'){ ?>
      <li class="p-sidebar__menu-link c-menu-link c-menu-link--sidebar"><a href="withdraw.php" class="p-sidebar__link c-menu-link__link c-menu-link__link--sidebar">ユーザー退会</a></li>
      <?php } ?>
    </ul>
  </nav>
</div>