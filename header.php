<?php
  if(basename($_SERVER['PHP_SELF']) === 'header.php'){
    header('Location:index.php');
    exit();
  }
?>
    <header class="l-header">
      <div class="l-header__inner">
        <h1 class="l-header__logo"><a href="index.php" class="l-header__logo-link">Book-Review</a></h1>
        <div class="l-header__push-wrapper js-header-push">
          <span></span>
          <span></span>
          <span></span>
        </div>
        <nav class="l-header__nav js-header-menu-nav">
          <ul class="l-header__main js-header-menu-list">
            <?php if(!empty(is_Login())){ ?>
            <li class="l-header__menu"><a href="mypage.php" class="l-header__menu-link">MYPAGE</a></li>
            <?php } ?>
            <li class="l-header__menu"><a href="about.php" class="l-header__menu-link">ABOUT</a></li>
            <li class="l-header__menu"><a href="inquiry.php" class="l-header__menu-link">INQUIRY</a></li>
            <?php if(empty(is_Login()) && empty(is_Login(false))){ ?>
            <li class="l-header__menu"><a href="login_page.php" class="l-header__menu-link">LOGIN</a></li>
            <?php }else{ ?>
            <li class="l-header__menu"><a href="logout.php" class="l-header__menu-link">LOGOUT</a></li>
            <?php } ?>
          </ul>
        </nav>
      </div>
    </header>