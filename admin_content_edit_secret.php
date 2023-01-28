<?php
  //function.phpを読み込む
  require('function.php');
  //admin_auth.phpを読み込む
  require('admin_auth.php');

  //コンテンツID
  $b_id = (!empty($_GET['b_id']) && is_numeric($_GET['b_id'])) ? $_GET['b_id'] : null;
  $edit_flag = (!empty($b_id)) ? true : false;

  if(!empty($edit_flag)){
    //DBの情報を読み取る
    $dbInfo = getContent($b_id,true);
    //DBの情報が読み取れなければ、管理人メニューページに移動させる
    if(empty($dbInfo)){
      header('Location:admin_menu_page_secret.php');
      exit();
    }
    $dbArray = mergeDBcolumn(5,'c_id');
    //削除されたかどうか調べる
    $delete_flag = (!empty($dbInfo['delete_flag'])) ? true : false;
    
  }

  $confirm_flag = (!empty($_POST['pre_post']) || !empty($_POST['pre_delete'])) ? true : false;

  //DBから各属性を読み込む
  $sellers = getSeller($confirm_flag);
  $genres = getGenre($confirm_flag);
  $categories = getCategory($confirm_flag);


  if(!empty($_POST)){
    $pre_post = (!empty($_POST['pre_post'])) ? true : false;
    $pre_delete = (!empty($_POST['pre_delete'])) ? true : false;
    $quit = (!empty($_POST['quit'])) ? true : false;
    $post = (!empty($_POST['post'])) ? true : false;
    $delete = (!empty($_POST['delete'])) ? true : false;

    if(!empty($pre_post) || !empty($quit) || !empty($pre_delete)){
      $content_title = (!empty($_POST['content_title'])) ? $_POST['content_title'] : null;
      $catchphrase = (!empty($_POST['catchphrase'])) ? $_POST['catchphrase'] : null;
      $text = (!empty($_POST['text'])) ? $_POST['text'] : null;
      $author = (!empty($_POST['author'])) ? $_POST['author'] : null;
      $s_id = (!empty($_POST['s_id']) && is_numeric($_POST['s_id'])) ? $_POST['s_id'] : null;
      $year = (!empty($_POST['year'])) ? $_POST['year'] : null;
      $price = (isset($_POST['price'])) ? $_POST['price'] : null;
      $g_id = (!empty($_POST['g_id']) && is_numeric($_POST['g_id'])) ? $_POST['g_id'] : null;
      $category = (!empty($_POST['category'])) ? $_POST['category'] : null;
      $com_off = (isset($_POST['com_off'])) ? $_POST['com_off'] : null;
      $release_date = (!empty($_POST['release_date'])) ? local2Datetime($_POST['release_date']) : null;//datetime-localからY-m-d H:i:sに変換する
    }

    if(!empty($pre_post)){
      $pic1 = (!empty($dbInfo['pic1'])) ? $dbInfo['pic1'] : null;
      $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'],'pic1') : $pic1;
      $pic2 = (!empty($dbInfo['pic2'])) ? $dbInfo['pic2'] : null;
      $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'],'pic2') : $pic2;
      $pic3 = (!empty($dbInfo['pic3'])) ? $dbInfo['pic3'] : null;
      $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'],'pic3') : $pic3;

      if(empty($pic1)){
        //$pic1が空の場合
        if(!empty($pic2)){
          //$pic2が空でない時、上に詰める
          $pic1 = $pic2;
          if(!empty($pic3)){
            //$pic3も空でない時、上に詰める
            $pic2 = $pic3;
            $pic3 = null;
          }else{
            //$pic1,$pic3が空で、$pic2が空でない
            $pic2 = null;
          }
        }elseif(!empty($pic3)){
          //$pic1,$pic2が空で かつ $pic3が空でない時、上に詰める
          $pic1 = $pic3;
          $pic3 = null;
        }
      }elseif(empty($pic2) && !empty($pic3)){
        $pic2 = $pic3;
        $pic3 = null;
      }  

      //バリデーション
      //content_title　(DBと変更があれば) !empty()と255文字以内
      if(empty($dbInfo) || $dbInfo['content_title'] !== $content_title){
        validMax($content_title,'content_title');
        validEnter($content_title,'content_title');
      }
      //catchphrase (DBと変更があれば) !empty()と255文字以内
      if(empty($dbInfo) && !empty($catchphrase) || (!empty($dbInfo['catchphrase']) && !empty($catchphrase) && $dbInfo['catchphrase'] !== $catchphrase)){
        validMax($catchphrase,'catchphrase');
      }
      //text (DBと変更があれば) !empty()と10000文字以内
      if(empty($dbInfo) || $dbInfo['text'] !== $text){
        validMax($text,'text',10000,MAX03);
        validEnter($text,'text');
      }
      //author (DBと変更があれば) !empty()と255文字以内
      if(empty($dbInfo) || $dbInfo['author'] !== $author){
        validMax($author,'author');
        validEnter($author,'author');
      }
      //s_id (DBと変更があれば) !empty()
      if(empty($dbInfo) || $dbInfo['s_id'] !== $s_id){
        validEnter($s_id,'s_id',REQ04);
      }
      //year (DBと変更があれば) !empty()　西暦(4桁)
      if(empty($dbInfo) || $dbInfo['year'] !== $year){
        validYear($year,'year');
        validEnter($year,'year');
      }
      //price (DBと変更があれば) もしisset()なら 半角数字と11文字以内
      if((empty($dbInfo) && isset($price)) || (!empty($dbInfo['price']) && isset($price) && $dbInfo['price'] !== $price)){
        validMax($price,'price',11,MAX04);
        validHalfNum($price,'price');
      }
      //g_id (DBと変更があれば) !empty()
      if(empty($dbInfo) || $dbInfo['g_id'] !== $g_id){
        validEnter($g_id,'g_id',REQ04);
      }
      //category count($_POST['category']) <= 5と!empty()を確認する
      validMaxNum($category,'category');
      validEnter($category,'category',REQ04);
      //com_off isset
      if(empty($dbInfo) || $dbInfo['com_off'] !== $com_off){
        validEnterOkZero($com_off,'com_off');
      }

      if(empty($err_msg)){
        debug('チェック完了');
        
      }else{
        debug('バリデーションエラー');
        $pre_post = false;
      }

    }
    
    //登録・更新復元
    if(!empty($post)){
      $content_title = $_POST['content_title'];
      $catchphrase = (!empty($_POST['catchphrase'])) ? $_POST['catchphrase'] : null;
      $text = $_POST['text'];
      $author = $_POST['author'];
      $s_id = $_POST['s_id'];
      $year = $_POST['year'];
      $price = (isset($_POST['price'])) ? $_POST['price'] : null;
      $g_id = $_POST['g_id'];
      $category = $_POST['category'];
      $com_off = $_POST['com_off'];
      $pic1 = (!empty($_POST['pic1'])) ? $_POST['pic1'] : null;
      $pic2 = (!empty($_POST['pic2'])) ? $_POST['pic2'] : null;
      $pic3 = (!empty($_POST['pic3'])) ? $_POST['pic3'] : null;
      $release_date = (!empty($_POST['release_date'])) ? local2Datetime($_POST['release_date']) : null;

      for($i=1;$i<=count($category);$i++){
        ${'c_id'.$i} = $category[($i - 1)];
      }
      if(!empty(5 - count($category))){
        for($i = count($category) + 1; $i<=5; $i++){
          ${'c_id'.$i} = null;
        }
      }
      debug(print_r($_POST,true));


      if(empty($edit_flag)){
        //新しく登録
        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO book (content_title,catchphrase,text,year,price,author,s_id,g_id,c_id1,c_id2,c_id3,c_id4,c_id5,com_off,pic1,pic2,pic3,release_date,create_date) VALUES (:content_title,:catchphrase,:text,:year,:price,:author,:s_id,:g_id,:c_id1,:c_id2,:c_id3,:c_id4,:c_id5,:com_off,:pic1,:pic2,:pic3,:release_date,:create_date)';
          $data = array(
            ':content_title' => $content_title,
            ':catchphrase' => $catchphrase,
            ':text' => $text,
            ':year' => $year,
            ':price' => $price,
            ':author' => $author,
            ':s_id' => $s_id,
            ':g_id' => $g_id,
            ':c_id1' => $c_id1,
            ':c_id2' => $c_id2,
            ':c_id3' => $c_id3,
            ':c_id4' => $c_id4,
            ':c_id5' => $c_id5,
            ':com_off' => $com_off,
            ':pic1' => $pic1,
            ':pic2' => $pic2,
            ':pic3' => $pic3,
            ':release_date' => $release_date,
            ':create_date' => date('Y-m-d H:i:s')
          );

          $stmt = queryPost($dbh,$sql,$data);

          if($stmt){
            //成功メッセージ
            debug('コンテンツ追加完了');
            $_SESSION['success'] = MSG01;
            
            //ページリロード
            header('Location:'.$_SERVER['PHP_SELF'].'?b_id='.$dbh->lastInsertId());
            exit();
          }
        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }

      }else{
        //更新復元
        try{
          $dbh = dbConnect();
          $sql = 'UPDATE book SET content_title = :content_title,catchphrase = :catchphrase,text = :text,year = :year,price = :price,author = :author,s_id = :s_id,g_id = :g_id,c_id1 = :c_id1,c_id2 = :c_id2,c_id3 = :c_id3,c_id4 = :c_id4,c_id5 = :c_id5,com_off = :com_off,pic1 = :pic1,pic2 = :pic2,pic3 = :pic3,release_date = :release_date';
          if(!empty($delete_flag)){
            $sql .= ',delete_flag = 0';
          }
          $sql .= ' WHERE b_id = :b_id';
          $data = array(
            ':content_title' => $content_title,
            ':catchphrase' => $catchphrase,
            ':text' => $text,
            ':year' => $year,
            ':price' => $price,
            ':author' => $author,
            ':s_id' => $s_id,
            ':g_id' => $g_id,
            ':c_id1' => $c_id1,
            ':c_id2' => $c_id2,
            ':c_id3' => $c_id3,
            ':c_id4' => $c_id4,
            ':c_id5' => $c_id5,
            ':com_off' => $com_off,
            ':pic1' => $pic1,
            ':pic2' => $pic2,
            ':pic3' => $pic3,
            ':release_date' => $release_date,
            ':b_id' => $b_id
          );

          $stmt = queryPost($dbh,$sql,$data);

          if($stmt){
            //成功メッセージ
            debug('コンテンツ更新完了');
            $_SESSION['success'] = MSG02;

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
    //削除
    if(!empty($delete)){
      try{
        $dbh = dbConnect();
        $sql = 'UPDATE book SET delete_flag = 1 WHERE b_id = :b_id AND delete_flag = 0';
        $data = array(':b_id' => $b_id);

        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
          //成功メッセージ
          debug('コンテンツ削除完了');
          $_SESSION['success'] = MSG03;

          //コンテンツ検索ページへ移動
          header('Location:admin_content_search_secret.php'.keepGETparam(array('b_id')));
          exit();
        }
      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        appendErr('common',ERR01);
      }
    }

    $_POST = array();

  }else{
    $content_title = null;
    $catchphrase = null;
    $text = null;
    $author = null;
    $s_id = null;
    $year = null;
    $price = null;
    $g_id = null;
    $com_off = null;
    $pic1 = null;
    $pic2 = null;
    $pic3 = null;
    $release_date = null;
    $category = array();
  }


  $title = (empty($edit_flag)) ? '書籍登録' : '書籍編集';
  require('head.php');
  debugStart();
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="l-col1">
        <h2 class="l-col1__page-title c-page-title"><?php echo $title; ?></h2>
        <div class="l-col1__err-msg c-err-msg"><?php echoErr('common'); ?></div>
        <form action="" method="post" enctype="multipart/form-data">
        <?php if(!empty($quit) || (empty($pre_post) && empty($pre_delete))){ ?>
          <div class="p-content-form">
            <div class="p-content-form__form-row">
              <label for="content_title" class="p-content-form__form-label c-form-label">タイトル</label>
              <div class="c-err-msg"><?php echoErr('content_title'); ?></div>
              <textarea name="content_title" placeholder="タイトル" id="content_title" class="p-content-form__textarea c-textarea c-textarea--s js-textarea <?php is_Err('content_title'); ?>"><?php echo keepTextData($content_title,'content_title'); ?></textarea>
              <p class="p-content-form__check-num c-check-num js-count"><span class="js-count-num">0</span>&nbsp;/&nbsp;<span class="js-count-limit">255</span>文字</p>
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="catchphrase">キャッチコピー(なくても可)</label>
              <div class="c-err-msg"><?php echoErr('catchphrase'); ?></div>
              <textarea name="catchphrase" placeholder="キャッチコピー" id="catchphrase" class="p-content-form__textarea c-textarea c-textarea--s js-textarea <?php is_Err('catchphrase'); ?>"><?php echo keepTextData($catchphrase,'catchphrase'); ?></textarea>
              <p class="p-content-form__check-num c-check-num js-count"><span class="js-count-num">0</span>&nbsp;/&nbsp;<span class="js-count-limit">255</span>文字</p>
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="text">説明文</label>
              <div class="c-err-msg"><?php echoErr('text'); ?></div>
              <textarea name="text" placeholder="説明文" id="text" class="p-content-form__textarea c-textarea c-textarea--l js-textarea <?php is_Err('text'); ?>"><?php echo keepTextData($text,'text'); ?></textarea>
              <p class="p-content-form__check-num c-check-num js-count"><span class="js-count-num">0</span>&nbsp;/&nbsp;<span class="js-count-limit">10000</span>文字</p>
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="author">著者</label>
              <div class="c-err-msg"><?php echoErr('author'); ?></div>
              <input type="text" placeholder="著者" name="author" id="author" class="p-content-form__textform c-textform <?php is_Err('author'); ?>" value="<?php echo keepTextData($author,'author'); ?>">
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="s_id">出版社</label>
              <div class="c-err-msg"><?php echoErr('s_id'); ?></div>
              <select name="s_id" id="s_id" class="p-content-form__selectform c-selectform <?php is_Err('s_id'); ?>">
                <option value="0">選択してください</option>
                <?php
                  if(!empty($sellers)){
                    foreach($sellers as $key => $val){
                ?>
                <option value="<?php echo $val['s_id']; ?>" <?php keepSelectData($s_id,'s_id',$val['s_id'],true,false); ?>><?php echo $val['s_name']; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="year">発売年</label>
              <div class="c-err-msg"><?php echoErr('year'); ?></div>
              <input type="number" name="year" id="year" min="1900" max="2999" placeholder="西暦(半角数字)"  class="p-content-form__numberform c-numberform <?php is_Err('year'); ?>" value="<?php echo keepTextData($year,'year'); ?>">
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="price">定価</label>
              <div class="c-err-msg"><?php echoErr('price'); ?></div>
              <input type="text" placeholder="定価(あれば)" name="price" id="price" class="p-content-form__textform c-textform <?php is_Err('price'); ?>" value="<?php echo keepTextData($price,'price'); ?>">
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="g_id">ジャンル</label>
              <div class="c-err-msg"><?php echoErr('g_id'); ?></div>
              <select name="g_id" id="g_id" class="p-content-form__selectform c-selectform <?php is_Err('g_id'); ?>">
                <option value="0">選択してください</option>
                <?php
                  if(!empty($genres)){
                    foreach($genres as $key => $val){
                ?>
                <option value="<?php echo $val['g_id']; ?>" <?php keepSelectData($g_id,'g_id',$val['g_id'],true,false); ?>><?php echo $val['genre']; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="p-content-form__form-row">
              <p class="p-content-form__form-label c-form-label">カテゴリー(<span class="js_check_limit">5</span>つまで)</p>
              <div class="c-err-msg"><?php echoErr('category'); ?></div>
              <p class="p-content-form__check-num c-check-num u-hide js-check-num"></p>
              <div class="p-content-form__checkboxes">
                <?php
                  if(!empty($categories)){
                    foreach($categories as $key => $val){
                ?>
                <div class="p-content-form__box-wrapper">
                  <input type="checkbox" name="category[]" id="c_id<?php echo $val['c_id']; ?>" value="<?php echo $val['c_id']; ?>" class="p-content-form__checkboxform c-checkboxform js-search-checkbox js-record-checkbox" <?php keepSelectComplex($category,'category',$val['c_id']); ?>>
                  <label class="p-content-form__checkbox-label c-checkboxform__label" for="c_id<?php echo $val['c_id']; ?>"><?php echo $val['c_name']; ?></label>
                </div>
                <?php
                    }
                  }
                ?>
              </div>
            </div>
            <div class="p-content-form__form-row">
              <p class="p-content-form__form-label c-form-label">コメント</p>
              <div class="c-err-msg"><?php echoErr('com_off'); ?></div>
              <input type="radio" name="com_off" value="0" class="p-content-form__radioform c-radioform" id="on" <?php keepSelectData($com_off,'com_off',0,false,false); ?>>
              <label for="on" class="p-content-form__radio-label c-radioform__label">オン</label>
              <input type="radio" name="com_off" value="1" class="p-content-form__radioform c-radioform" id="off" <?php keepSelectData($com_off,'com_off',1,false,false); ?>>
              <label for="off" class="p-content-form__radio-label c-radioform__label">オフ</label>
            </div>
            <div class="p-content-form__form-row">
              <p class="p-content-form__form-label c-form-label">画像1<br>(2.66MB以内/jpeg・png・gif・webp)</p>
              <div class="c-err-msg"><?php echoErr('pic1'); ?></div>
              <div class="p-content-form__img-form-wrapper">
                <label for="pic1" class="p-content-form__img-label c-img-label">
                  ドラッグ＆ドロップ
                  <input type="hidden" name="MAX_FILE_SIZE" value="2796202">
                  <input type="file" name="pic1" id="pic1" class="p-content-form__fileform c-fileform js-edit-img-form <?php is_Err('pic1'); ?>">
                  <img src="<?php if(!empty($dbInfo['pic1'])) echo $dbInfo['pic1']; ?>" alt="画像1" style="<?php if(empty($dbInfo['pic1'])) echo 'display: none;'; ?>" class="p-content-form__img js-edit-img">
                </label>
              </div>
            </div>
            <div class="p-content-form__form-row">
              <p class="p-content-form__form-label c-form-label">画像2<br>(2.66MB以内/jpeg・png・gif・webp)</p>
              <div class="c-err-msg"><?php echoErr('pic2'); ?></div>
              <div class="p-content-form__img-form-wrapper">
                <label for="pic2" class="p-content-form__img-label c-img-label">
                  ドラッグ＆ドロップ
                  <input type="hidden" name="MAX_FILE_SIZE" value="2796202">
                  <input type="file" name="pic2" id="pic2" class="p-content-form__fileform c-fileform js-edit-img-form <?php is_Err('pic2'); ?>">
                  <img src="<?php if(!empty($dbInfo['pic2'])) echo $dbInfo['pic2']; ?>" alt="画像2" style="<?php if(empty($dbInfo['pic2'])) echo 'display: none;'; ?>" class="p-content-form__img js-edit-img">
                </label>
              </div>
            </div>
            <div class="p-content-form__form-row">
              <p class="p-content-form__form-label c-form-label">画像3<br>(2.66MB以内/jpeg・png・gif・webp)</p>
              <div class="c-err-msg"><?php echoErr('pic3'); ?></div>
              <div class="p-content-form__img-form-wrapper">
                <label for="pic3" class="p-content-form__img-label c-img-label">
                  ドラッグ＆ドロップ
                  <input type="hidden" name="MAX_FILE_SIZE" value="2796202">
                  <input type="file" name="pic3" id="pic3" class="p-content-form__fileform c-fileform c-fileform js-edit-img-form <?php is_Err('pic3'); ?>">
                  <img src="<?php if(!empty($dbInfo['pic3'])) echo $dbInfo['pic3']; ?>" alt="画像3" style="<?php if(empty($dbInfo['pic3'])) echo 'display: none;'; ?>" class="p-content-form__img js-edit-img">
                </label>
              </div>
            </div>
            <div class="p-content-form__form-row">
              <label class="p-content-form__form-label c-form-label" for="release_date">公開予定日時</label>
              <div class="c-err-msg"><?php echoErr('release_date'); ?></div>
              <input type="datetime-local" name="release_date" id="release_date" class="p-content-form__datetimelocalform c-datetimelocalform <?php is_Err('release_date'); ?>" value="<?php echo (!empty($release_date) || !empty($dbInfo['release_date'])) ? datetime2Local(sanitize(keepTextData($release_date,'release_date'))) : ''; ?>">
            </div>
            <?php if(empty($edit_flag) || !empty($delete_flag)){ //新しく登録する+削除されたものを復元するとき ?>
            <div class="p-content-form__btn-wrapper">
              <input type="submit" value="<?php echo (empty($edit_flag)) ? '登録する' : '復元する'; ?>" name="pre_post" class="p-content-form__btn c-btn c-btn--active">
              <a href="admin_menu_page_secret.php" class="p-content-form__btn c-btn c-btn--inactive">戻る</a>
            </div>
            <?php }else{ //削除されていないものを編集するとき ?>
            <div class="p-content-form__btn-wrapper">
              <input type="submit" value="更新する" name="pre_post" class="p-content-form__btn c-btn c-btn--active">
              <input type="submit" value="削除する" name="pre_delete" class="p-content-form__btn c-btn c-btn--inactive">
              <a href="admin_content_search_secret.php<?php echo keepGETparam(array('b_id')); ?>" class="p-content-form__btn c-btn c-btn--inactive">戻る</a>
            </div>
            <?php } ?>
          </div>
        <?php }else{ ?>
          <div class="p-content-confirm">
            <?php if(!empty($pre_post)){ ?>
            <div class="p-content-confirm__confirm-msg c-confirm-msg">
              <p class="p-content-confirm__text c-confirm-msg__text">
                下記の内容を送信します。<br>
                よろしいでしょうか。
              </p>
            </div>
            <?php }elseif(!empty($pre_delete)){ ?>
            <div class="p-content-confirm__confirm-msg c-confirm-msg">
              <p class="c-confirm-msg__text">
                下記の書籍を削除します。<br>
                よろしいでしょうか。
              </p>
            </div>
            <?php } ?>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">タイトル</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo nl2br(sanitize($content_title)); ?></p>
              <input type="hidden" name="content_title" value="<?php echo sanitize($content_title); ?>">
            </div>
            <?php if(!empty($catchphrase)){ ?>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">キャッチコピー</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo nl2br(sanitize($catchphrase)); ?></p>
              <input type="hidden" name="catchphrase" value="<?php echo sanitize($catchphrase); ?>">
            </div>
            <?php } ?>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">説明文</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo nl2br(sanitize($text)); ?></p>
              <input type="hidden" name="text" value="<?php echo sanitize($text); ?>">
            </div>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">著者</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo sanitize($author); ?></p>
              <input type="hidden" name="author" value="<?php echo sanitize($author); ?>">
            </div>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">出版社</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo $sellers[($s_id - 1)]['s_name']; ?></p>
              <input type="hidden" name="s_id" value="<?php echo sanitize($s_id); ?>">
            </div>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">発売年</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo sanitize($year); ?>年</p>
              <input type="hidden" name="year" value="<?php echo sanitize($year); ?>">
            </div>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">定価</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo (isset($price) && is_numeric($price)) ? number_format(sanitize($price)).'円' : 'オープン価格'; ?></p>
              <?php if(isset($price) && is_numeric($price)){ ?>
              <input type="hidden" name="price" value="<?php echo sanitize($price); ?>">
              <?php } ?>
            </div>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">ジャンル</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo $genres[($g_id - 1)]['genre']; ?></p>
              <input type="hidden" name="g_id" value="<?php echo sanitize($g_id); ?>">
            </div>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">カテゴリー</p>
              <p class="p-content-confirm__confirm-text c-confirm-text">
              <?php for($i = 0; $i < count($category); $i++){ ?>
                <span>
                  <span class="p-content-confirm__confirm-category c-confirm-text__category"><?php echo $categories[($category[$i] - 1)]['c_name']; ?></span>
                  <input type="hidden" name="category[]" value="<?php echo sanitize($category[$i]); ?>">
                  <br>
                </span>
              <?php } ?>
              </p>
            </div>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">コメント</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo (empty($com_off)) ? 'オン' : 'オフ'; ?></p>
              <input type="hidden" name="com_off" value="<?php echo sanitize($com_off); ?>">
            </div>
            <?php if(!empty($pic1)){ ?>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">画像1</p>
              <img src="<?php echo sanitize($pic1); ?>" alt="画像1" class="p-content-confirm__img">
              <input type="hidden" name="pic1" value="<?php echo sanitize($pic1); ?>">
            </div>
            <?php } ?>
            <?php if(!empty($pic2)){ ?>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">画像2</p>
              <img src="<?php echo sanitize($pic2); ?>" alt="画像2" class="p-content-confirm__img">
              <input type="hidden" name="pic2" value="<?php echo sanitize($pic2); ?>">
            </div>
            <?php } ?>
            <?php if(!empty($pic3)){ ?>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">画像3</p>
              <img src="<?php echo sanitize($pic3); ?>" alt="画像3" class="p-content-confirm__img">
              <input type="hidden" name="pic3" value="<?php echo sanitize($pic3); ?>">
            </div>
            <?php } ?>
            <?php if(!empty($release_date)){ ?>
            <div class="p-content-confirm__confirm-row">
              <p class="p-content-confirm__confirm-title c-confirm-title">公開予定日時</p>
              <p class="p-content-confirm__confirm-text c-confirm-text"><?php echo datetime2Calendar(sanitize($release_date)); ?></p>
              <input type="hidden" name="release_date" value="<?php echo sanitize($release_date); ?>">
            </div>
            <?php } ?>
            <?php if(!empty($pre_post)){ ?>
            <div class="p-content-confirm__btn-wrapper">
              <input type="submit" value="戻る" name="quit" class="p-content-confirm__btn c-btn c-btn--inactive">
              <input type="submit" 
              value="<?php 
                if(empty($edit_flag)){
                  echo '登録する';
                }elseif(empty($delete_flag)){
                  echo '更新する';
                }else{
                  echo '復元する';
                }
              ?>" name="post" class="p-content-confirm__btn c-btn c-btn--active">
            </div>
            <?php }elseif(!empty($pre_delete)){ ?>
            <div class="p-content-confirm__btn-wrapper">
              <input type="submit" value="戻る" name="quit" class="p-content-confirm__btn c-btn c-btn--inactive">
              <input type="submit" value="削除する" name="delete" class="p-content-confirm__btn c-btn c-btn--active">
            </div>
            <?php } ?>
          </div>
        <?php } ?>
        </form>
      </div>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>