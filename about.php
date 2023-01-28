<?php
  require('function.php');
  
  $title = 'このサイトについて';
  require('head.php');
?>
  <body>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1">
        <h2 class="l-col1__page-title c-page-title"><?php echo $title; ?></h2>
        <p class="l-col1__text">
          このサイトは、書籍のデータベースかつレビュー投稿サイトです！<br>
          あなたが読みたい本を見つけるための手助けになればと思います！
        </p>
      </div>
    </main>
<?php
  require('footer.php');

?>