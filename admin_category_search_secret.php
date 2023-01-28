<?php
  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');
  
  $record_span = 20;
  //GETパラメータ
  $current_page = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
  $word = (!empty($_GET['word'])) ? $_GET['word'] : null;
  $words_logic_flag = (!empty($_GET['words_logic_flag']) && is_numeric($_GET['words_logic_flag'])) ? $_GET['words_logic_flag'] : null;
  $flow_flag = (!empty($_GET['flow_flag']) && is_numeric($_GET['flow_flag'])) ? $_GET['flow_flag'] : null;

  $words = (!empty($word)) ? explode(' ',mb_convert_kana($word,'s')) : null;
  $current_min_record = ($current_page - 1) * $record_span;

  $categories = getEditCategory($words,$words_logic_flag,$flow_flag,$current_page,$record_span);

  if(!empty($_POST)){
    $append_flag = (!empty($_POST['append'])) ? true : false;

    if(!empty($append_flag)){
      $new_c_name = array_shift($_POST);
      $new_kana = array_shift($_POST);

      //バリデーション
      //c_name !empty 255文字以内
      validMax($new_c_name,'common');
      validEnter($new_c_name,'common');
      //kana !empty 255文字以内 ひらがなカタカナ半角英数字
      validKana($new_kana,'common');
      validMax($new_kana,'common');
      validEnter($new_kana,'common');

      
      if(empty($err_msg)){
        //追加
        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO category (c_name,kana,create_date) VALUES (:c_name,:kana,:create_date)';
          $data = array(
            ':c_name' => $new_c_name,
            ':kana' => $new_kana,
            ':create_date' => date('Y-m-d H:i:s')
          );

          $stmt = queryPost($dbh,$sql,$data);
          if($stmt){
            //成功メッセージ
            $_SESSION['success'] = MSG23;
            //ページリロード
            header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
            exit();
          }
        
        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }
      }


    }else{
      $target = array_search('更新する',$_POST,true);
      $renew = array_values($_POST);
      $new_c_name = $renew[(array_search('更新する',array_values($_POST),true) - 2)];
      $new_kana = $renew[(array_search('更新する',array_values($_POST),true) - 1)];

      //バリデーション
      //c_name !empty 255文字以内
      validMax($new_c_name,'common');
      validEnter($new_c_name,'common');
      //kana !empty 255文字以内 ひらがなカタカナ半角英数字
      validKana($new_kana,'common');
      validMax($new_kana,'common');
      validEnter($new_kana,'common');

      
      if(empty($err_msg)){
        //更新
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE category SET c_name = :c_name,kana = :kana WHERE c_id = :c_id';
          $data = array(
            ':c_name' => $new_c_name,
            ':kana' => $new_kana,
            ':c_id' => $target
          );

          $stmt = queryPost($dbh,$sql,$data);
          if($stmt){
            //成功メッセージ
            $_SESSION['success'] = MSG24;
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

  $title = 'カテゴリー管理';
  require('head.php');
  debugStart();
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <div class="p-modal">
      <div class="p-modal__modal-back js-modal-back js-modal-quit"></div>
      <div class="p-modal__modal-window js-modal-window">
        <?php require('modal_attribute_search_menu.php'); ?>
      </div>
    </div>
    <main class="l-container">
      <div class="p-search-top">
        <h2 class="p-search-top__page-title c-page-title"><?php echo $title; ?></h2>
        <?php if(!empty($categories['total_record']) && !empty($categories['data'])){ ?>
        <p class="p-search-top__search-result c-search-result"><?php echo $categories['total_record']; ?>件のカテゴリーが見つかりました<br><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><?php echo $current_page; ?>ページ目&nbsp;&nbsp;<?php echo $current_min_record + 1; ?>&nbsp;-&nbsp;<?php echo count($categories['data']) + $current_min_record; ?>件</p>
        <?php }else{ ?>
        <p class="p-search-top__search-result c-search-result">カテゴリーは見つかりませんでした</p>
        <?php } ?>
        <p class="p-search-top__search-option js-modal-trigger">検索オプション</p>
      </div>
      <div class="l-col2">
        <article class="l-main">
          <div class="p-found-record">
            <div class="p-found-record__err-msg c-err-msg">
              <?php
                if(!empty($err_msg['common'])){
                  echo (!empty($_POST['append'])) ? '(新しく追加) ' : '(編集) ';
                  echo $err_msg['common'];
                } 
              ?>
            </div>
            <form action="" method="post">
              <div class="p-found-record__add-form">
                <p class="p-found-record__cmd c-choice-cmd js-open-btn">新しく追加する</p>
                <div class="p-found-record__form-wrapper js-opened-area">
                  <input type="text" name="new_c_name" placeholder="カテゴリー" class="p-found-record__textform c-textform">
                  <input type="text" name="new_kana" placeholder="よみがな" class="p-found-record__textform c-textform">
                  <input type="submit" value="追加する" name="append" class="p-found-record__btn c-btn c-btn--active">
                </div>
              </div>
              <div>
            <?php
              if(!empty($categories['data'])){
                foreach($categories['data'] as $key => $val){
            ?>
                <div class="p-found-record__record-row">
                  <span class="p-found-record__record-info c-record-info">◆&nbsp;<?php echo $val['c_name']; ?></span>
                  <span class="p-found-record__cmd c-choice-cmd js-open-btn">編集する</span>
                  <div class="p-found-record__form-wrapper js-opened-area">
                    <input type="text" name="<?php echo $val['c_id']; ?>_c_name" placeholder="カテゴリー" class="p-found-record__textform c-textform">
                    <input type="text" name="<?php echo $val['c_id']; ?>_kana" placeholder="よみがな" class="p-found-record__textform c-textform">
                    <input type="submit" name="<?php echo $val['c_id']; ?>" value="更新する" class="p-found-record__btn c-btn c-btn--active">
                  </div>
                </div>
            <?php
                }
              }
            ?>
              </div>
            </form>
            <?php
              if(!empty($categories['total_page']) && !empty($categories['data'])){
                pagenation('admin_category_search_secret.php',$current_page,$categories['total_page']);
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