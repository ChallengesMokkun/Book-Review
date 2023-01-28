<?php
  if(basename($_SERVER['PHP_SELF']) === 'footer.php'){
    header('Location:index.php');
    exit();
  }
?>
    <footer class="l-footer">
      <div class="l-footer__inner">
        <p class="l-footer__text">Book-Review</p>
      </div>
    </footer>
    <script src="js/jquery-3.6.1.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>