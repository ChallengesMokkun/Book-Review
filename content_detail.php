<?php

  //function.phpを読み込む
  require('function.php');

  $b_id = (!empty($_GET['b_id']) && is_numeric($_GET['b_id'])) ? $_GET['b_id'] : null;

  $admin_login = is_Login(false);
  //DBからコンテンツを読み込む
  $dbInfo = getContent($b_id,$admin_login);
  //コンテンツを読み込めない場合、empty($admin_login)ならindex.php !empty($admin_login)ならadmin_content_search_secret.phpへ移動
  if(empty($dbInfo)){
    if(!empty($admin_login)){
      header('Location:admin_content_search_secret.php');
      exit();

    }else{
      header('Location:index.php');
      exit();
    }
  }

  //$com_offを調べる(empty($com_off)ならコメントON、!empty($com_off)ならコメントOFF)
  $com_off = (!empty($dbInfo['com_off'])) ? true : false;
  //DBから各属性を読み込む
  $sellers = getSeller(true);
  $genres = getGenre(true);
  $categories = getCategory(true);
  $age_list = getAge();
  $gender_list = getGender();
  //設定されているカテゴリーを取得する
  $dbArray = mergeDBcolumn(5,'c_id');

  //コメントを取得
  $comments = getComment($b_id);

  //評価を取得
  $rating = getRating($b_id);

  //empty($admin_login)なら閲覧数+1する
  if(empty($admin_login) && (!isset($_SERVER['HTTP_REFERER']) || (isset($_SERVER['HTTP_REFERER']) && basename($_SERVER['HTTP_REFERER']) !== basename($_SERVER['PHP_SELF']).keepGETparam()))){
    try{
      $dbh = dbConnect();
      $sql = 'UPDATE book SET visited = :visited WHERE b_id = :b_id AND delete_flag = 0';
      $data = array(
        ':visited' => $dbInfo['visited'] + 1,
        ':b_id' => $b_id
        );

      $stmt = queryPost($dbh,$sql,$data);

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
    }
  }
  //ユーザーがログインしてるか確認
  $user_login = is_Login();
  if(!empty($user_login)){
    $u_id = $_SESSION['u_id'];
    $fav_flag = is_Favorite($u_id,$b_id);
  }

  //コマンドの値
  $report_cmd = '通報する';
  $delete_cmd = '削除';
  $good_cmd = '参考になった';

  if(!empty($_POST)){
    $rating_flag = (!empty($_POST['rating'])) ? true : false;
    $delete_flag = (!empty(in_array($delete_cmd,$_POST,true))) ? true : false;
    $report_flag = (!empty(in_array($report_cmd,$_POST,true))) ? true : false;
    
    if(!empty($rating_flag)){
      $rate_score = $_POST['rate_score'];
      $text = $_POST['text'];

      $comment_flag = (empty($text)) ? false : true;
      if(!empty($comment_flag)){
        $comment_flag = (mb_strlen(preg_replace('/　|\s+/', '', $text)) === 0) ? false : true;
      }

      if(!empty($user_login)){
        $user_prof = getUserProf($u_id);
      }

      if(empty($comment_flag)){
        //評価をDBに追加
        try{
          $dbh = dbConnect();
          if((!empty($user_login))){
            $sql = 'INSERT INTO rating (b_id,age_id,age_flag,gender_id,gender_flag,rate_score,create_date) VALUES (:b_id,:age_id,:age_flag,:gender_id,:gender_flag,:rate_score,:create_date)';
            $data = array(
              ':b_id' => $b_id,
              ':age_id' => $user_prof['age_id'],
              ':age_flag' => $user_prof['age_flag'],
              ':gender_id' => $user_prof['gender_id'],
              ':gender_flag' => $user_prof['gender_flag'],
              ':rate_score' => $rate_score,
              ':create_date' => date('Y-m-d H:i:s')
            );

          }else{
            $sql = 'INSERT INTO rating (b_id,rate_score,create_date) VALUES (:b_id,:rate_score,:create_date)';
            $data = array(
              ':b_id' => $b_id,
              ':rate_score' => $rate_score,
              ':create_date' => date('Y-m-d H:i:s')
            );
          }

          $stmt = queryPost($dbh,$sql,$data);

          //コンテンツのaverage_rateを更新
          $sql = 'UPDATE book SET average_rate = :average_rate WHERE b_id = :b_id AND delete_flag = 0';
          $data = array(
            ':average_rate' => round(($rating['total'] + $rate_score) / ($rating['num'] + 1),2),
            ':b_id' => $b_id
          );

          $stmt = queryPost($dbh,$sql,$data);

          if($stmt){
            //成功メッセージ
            $_SESSION['success'] = MSG08;
            //ページリロード
            header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
            exit();
          }

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }
  
      }elseif(!empty($comment_flag)){
        //バリデーション
        validMax($text,'text',3000,MAX02);
        
        if(empty($err_msg)){
          try{
            $dbh = dbConnect();

            //評価とコメントをDBに追加
            if(!empty($user_login)){
              //評価をDBに追加
              $sql = 'INSERT INTO rating (b_id,age_id,age_flag,gender_id,gender_flag,rate_score,create_date) VALUES (:b_id,:age_id,:age_flag,:gender_id,:gender_flag,:rate_score,:create_date)';
              $data = array(
                ':b_id' => $b_id,
                ':age_id' => $user_prof['age_id'],
                ':age_flag' => $user_prof['age_flag'],
                ':gender_id' => $user_prof['gender_id'],
                ':gender_flag' => $user_prof['gender_flag'],
                ':rate_score' => $rate_score,
                ':create_date' => date('Y-m-d H:i:s')
              );
              $stmt = queryPost($dbh,$sql,$data);

              //コメントをDBに追加
              $sql = 'INSERT INTO comment (b_id,u_id,age_id,age_flag,gender_id,gender_flag,rate_score,text,create_date) VALUES (:b_id,:u_id,:age_id,:age_flag,:gender_id,:gender_flag,:rate_score,:text,:create_date)';
              $data = array(
                ':b_id' => $b_id,
                ':u_id' => $u_id,
                ':age_id' => $user_prof['age_id'],
                ':age_flag' => $user_prof['age_flag'],
                ':gender_id' => $user_prof['gender_id'],
                ':gender_flag' => $user_prof['gender_flag'],
                ':rate_score' => $rate_score,
                ':text' => $text,
                ':create_date' => date('Y-m-d H:i:s')
              );
              $stmt = queryPost($dbh,$sql,$data);

            }else{
              //評価をDBに追加
              $sql = 'INSERT INTO rating (b_id,rate_score,create_date) VALUES (:b_id,:rate_score,:create_date)';
              $data = array(
                ':b_id' => $b_id,
                ':rate_score' => $rate_score,
                ':create_date' => date('Y-m-d H:i:s')
              );
              $stmt = queryPost($dbh,$sql,$data);

              //コメントをDBに追加
              $sql = 'INSERT INTO comment (b_id,rate_score,text,create_date) VALUES (:b_id,:rate_score,:text,:create_date)';
              $data = array(
                ':b_id' => $b_id,
                ':rate_score' => $rate_score,
                ':text' => $text,
                ':create_date' => date('Y-m-d H:i:s')
              );
              $stmt = queryPost($dbh,$sql,$data);
            }
  
            //コンテンツのaverage_rateを更新
            $sql = 'UPDATE book SET average_rate = :average_rate WHERE b_id = :b_id AND delete_flag = 0';
            $data = array(
              ':average_rate' => round(($rating['total'] + $rate_score) / ($rating['num'] + 1),2),
              ':b_id' => $b_id
            );

            $stmt = queryPost($dbh,$sql,$data);

            //コメント数を更新
            $sql = 'UPDATE book SET com_num = :com_num WHERE b_id = :b_id AND delete_flag = 0';
            $data = array(
              ':com_num' => count($comments) + 1,
              ':b_id' => $b_id
            );

            $stmt = queryPost($dbh,$sql,$data);

            if($stmt){
              //成功メッセージ
              $_SESSION['success'] = MSG09;
              //ページリロード
              header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
              exit();
            }
  
          }catch (Exception $e){
            debug('エラー発生: '.$e->getMessage());
            appendErr('common',ERR01);
          }

        }else{
          debug('バリデーションエラー');
        }
      }

    }elseif(!empty($delete_flag)){
      if(!empty($admin_login)){
        //コメント削除
        $com_id = array_search($delete_cmd,$_POST,true);
        $post_data = array_values($_POST);
        $u_id = $post_data[(array_search($delete_cmd,array_values($_POST),true) - 2)];

        $nums = getReportDeleteNum($u_id);
        if(!empty($nums)){
          $delete_num = $nums['delete_num'];
        }

        try{
          //コメント論理削除
          $dbh = dbConnect();
          $sql = 'UPDATE comment SET delete_flag = 1 WHERE com_id = :com_id AND delete_flag = 0';
          $data = array(':com_id' => $com_id);

          $stmt = queryPost($dbh,$sql,$data);

          //削除回数の更新(ユーザー)
          if(!empty($nums)){
            $sql = 'UPDATE user SET delete_num = :delete_num WHERE u_id = :u_id';
            $data = array(
              ':delete_num' => $delete_num + 1,
              ':u_id' => $u_id
            );
  
            $stmt = queryPost($dbh,$sql,$data);
          }

          $sql = 'UPDATE book SET com_num = :com_num WHERE b_id = :b_id';
          $data = array(
            ':com_num' => count($comments) - 1,
            ':b_id' => $b_id
          );

          $stmt = queryPost($dbh,$sql,$data);

          if($stmt){
            //成功メッセージ
            $_SESSION['success'] = MSG10;
            //ページリロード
            header('Location:'.$_SERVER['PHP_SELF'].keepGETparam());
            exit();
          }

        }catch (Exception $e){
          debug('エラー発生: '.$e->getMessage());
          appendErr('common',ERR01);
        }
      }

    }elseif(!empty($report_flag)){
      //コメント通報
      $com_id = array_search($report_cmd,$_POST,true);
      $post_data = array_values($_POST);
      $u_id = $post_data[(array_search($report_cmd,array_values($_POST),true) - 2)];
      $reported_num = $post_data[(array_search($report_cmd,array_values($_POST),true) - 1)];

      $nums = getReportDeleteNum($u_id);
      if(!empty($nums)){
        $user_reported_num = $nums['reported_num'];
      }
      
      try{
        //通報回数の更新(コメント)
        $dbh = dbConnect();
        $sql = 'UPDATE comment SET reported_num = :reported_num WHERE com_id = :com_id AND delete_flag = 0';
        $data = array(
          ':reported_num' => $reported_num + 1,
          ':com_id' => $com_id
        );

        $stmt = queryPost($dbh,$sql,$data);

        //通報回数の更新(ユーザー)
        if(!empty($nums)){
          $sql = 'UPDATE user SET reported_num = :reported_num WHERE u_id = :u_id';
          $data = array(
            ':reported_num' => $user_reported_num + 1,
            ':u_id' => $u_id
          );

          $stmt = queryPost($dbh,$sql,$data);
        }

        if($stmt){
          //成功メッセージ
          $_SESSION['success'] = MSG11;
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





  $title = 'コンテンツタイトル';
  debugStart();
  $title = sanitize($dbInfo['content_title']);
  require('head.php');
?>
  <body>
    <p class="c-success-msg js-success-msg"><?php getMsg(); ?></p>
    <?php require('header.php'); ?>
    <main class="l-container">
      <div class="p-content-header">
        <div class="p-content-header__element-wrapper">
          <span class="p-content-header__element">
            <span class="p-content-header__icon-wrapper">
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php echo ($dbInfo['average_rate'] >= 0.5 && $dbInfo['average_rate'] < 1) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php if($dbInfo['average_rate'] >= 1) echo 'c-fonticon--active-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php echo ($dbInfo['average_rate'] >= 1.5 && $dbInfo['average_rate'] < 2) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php if($dbInfo['average_rate'] >= 2) echo 'c-fonticon--active-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php echo ($dbInfo['average_rate'] >= 2.5 && $dbInfo['average_rate'] < 3) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php if($dbInfo['average_rate'] >= 3) echo 'c-fonticon--active-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php echo ($dbInfo['average_rate'] >= 3.5 && $dbInfo['average_rate'] < 4) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php if($dbInfo['average_rate'] >= 4) echo 'c-fonticon--active-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php echo ($dbInfo['average_rate'] >= 4.5 && $dbInfo['average_rate'] < 5) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
              <i class="fa-sharp fa-solid fa-star p-content-header__fonticon c-fonticon <?php if($dbInfo['average_rate'] >= 5) echo 'c-fonticon--active-star'; ?>"></i>
            </span>
            <span class="p-content-header__num-info"><?php echo sprintf('%.2f',sanitize($dbInfo['average_rate'])); ?></span>
          </span>
          <span class="p-content-header__element">
            <span class="p-content-header__icon-wrapper"><i class="fa-sharp fa-solid fa-eye p-content-header__fonticon c-fonticon"></i></span>
            <span class="p-content-header__num-info"><?php echo sanitize($dbInfo['visited']); ?></span>
          </span>
          <?php if(empty($com_off)){ ?>
          <span class="p-content-header__element">
            <span class="p-content-header__icon-wrapper"><i class="fa-sharp fa-solid fa-message p-content-header__fonticon c-fonticon"></i></span>
            <span class="p-content-header__num-info"><?php echo sanitize($dbInfo['com_num']); ?></span>
          </span>
          <?php } ?>
          <span class="p-content-header__element">
            <span class="p-content-header__icon-wrapper"><i class="fa-sharp fa-solid fa-heart p-content-header__fonticon c-fonticon <?php echo (!empty($fav_flag)) ? 'c-fonticon--active-fav' : 'c-fonticon--inactive-fav'; ?> <?php if(!empty($user_login)) echo 'js-favorite'; ?>" data-fav_content="<?php echo $b_id; ?>" <?php if(!empty($user_login)) echo 'style="cursor: pointer"'; ?>></i></span>
            <span class="p-content-header__num-info js-fav-num"><?php echo sanitize($dbInfo['fav_num']); ?></span>
          </span>
        </div>
        <h2 class="p-content-header__sub-heading c-sub-heading"><?php echo nl2br(sanitize($dbInfo['content_title'])); ?></h2>
        <div class="p-content-header__attr-area">
          <span class="p-content-header__content-attr c-content-attr">
            <span class="p-content-header__label c-content-attr__label">著者:&nbsp;</span>
            <span class="p-content-header__value c-content-attr__value"><?php echo sanitize($dbInfo['author']); ?></span>
          </span>
          <span class="p-content-header__content-attr c-content-attr">
            <span class="p-content-header__label c-content-attr__label">発売年:&nbsp;</span>
            <span class="p-content-header__value c-content-attr__value"><?php echo sanitize($dbInfo['year']); ?></span>
          </span>
          <span class="p-content-header__content-attr c-content-attr">
            <span class="p-content-header__label c-content-attr__label">出版社:&nbsp;</span>
            <span class="p-content-header__value c-content-attr__value"><a href="index.php?s_id=<?php echo sanitize($dbInfo['s_id']); ?>" class="p-content-header__link"><?php echo $sellers[($dbInfo['s_id'] - 1)]['s_name']; ?></a></span>
          </span>
          <span class="p-content-header__content-attr c-content-attr">
            <span class="p-content-header__label c-content-attr__label">ジャンル:&nbsp;</span>
            <span class="p-content-header__value c-content-attr__value"><a href="index.php?g_id=<?php echo sanitize($dbInfo['g_id']); ?>" class="p-content-header__link"><?php echo sanitize($genres[($dbInfo['g_id'] - 1)]['genre']); ?></a></span>
          </span>
          <span class="p-content-header__content-attr c-content-attr">
            <span class="p-content-header__label c-content-attr__label">カテゴリー:&nbsp;</span>
            <span class="p-content-header__value c-content-attr__value">
              <?php for($i=1; $i<=count($dbArray); $i++){ ?>
                <a href="index.php?category[]=<?php echo sanitize($dbInfo['c_id'.$i]); ?>" class="p-content-header__links"><?php echo $categories[($dbArray[($i - 1)] - 1)]['c_name']; ?></a>
              <?php } ?>
            </span>
          </span>
        </div>
      </div>
      <div class="p-content-detail">
        <div class="p-content-detail__img-area">
          <div class="p-content-detail__main-img-wrapper">
            <img src="<?php displayImg($dbInfo['pic1']) ?>" alt="<?php echo sanitize($dbInfo['content_title']); ?>" class="p-content-detail__main-img js-main-img">
          </div>
        </div>
        <div class="p-content-detail__info-area">
          <?php if(!empty($dbInfo['pic2'])){ ?>
          <div class="p-content-detail__sub-img-area">
            <div class="p-content-detail__sub-img-wrapper">
              <img src="<?php echo sanitize($dbInfo['pic1']); ?>" alt="写真1" class="p-content-detail__sub-img js-sub-img">
            </div>
            <div class="p-content-detail__sub-img-wrapper">
              <img src="<?php echo sanitize($dbInfo['pic2']); ?>" alt="写真2" class="p-content-detail__sub-img js-sub-img">
            </div>
            <?php if(!empty($dbInfo['pic3'])){ ?>
            <div class="p-content-detail__sub-img-wrapper">
              <img src="<?php echo sanitize($dbInfo['pic3']); ?>" alt="写真3" class="p-content-detail__sub-img js-sub-img">
            </div>
            <?php } ?>
          </div>
          <?php } ?>
          <div>
            <?php if(!empty($dbInfo['catchphrase'])){ ?>
            <h3 class="p-content-detail__sub-heading c-sub-heading"><?php echo nl2br(sanitize($dbInfo['catchphrase'])); ?></h3>
            <?php } ?>
            <p class="p-content-detail__introduction"><?php echo nl2br(sanitize($dbInfo['text'])); ?><br>(出版社より)</p>
          </div>
        </div>
      </div>
      <div class="p-content-rating">
        <div class="p-content-rating__err-msg c-err-msg"><?php echoErr('common'); ?></div>
        <form action="" method="post">
          <div class="p-content-rating__wrapper">
            <input type="range" name="rate_score" list="score-list" step="0.5" min="0" max="5" value="3.5" class="p-content-rating__score-range js-score-input">
            <datalist id="score-list">
              <option value="0">
              <option value="0.5">
              <option value="1">
              <option value="1.5">
              <option value="2">
              <option value="2.5">
              <option value="3">
              <option value="3.5">
              <option value="4">
              <option value="4.5">
              <option value="5">
            </datalist>
            <span class="js-score-indicate p-content-rating__score">3.5</span>
          </div>
          <?php if(empty($com_off)){ ?>
          <div class="c-err-msg"><?php echoErr('text'); ?></div>
          <div class="p-content-rating__textarea-wrapper">
            <textarea name="text" placeholder="レビューを投稿できます。評価だけの場合は空欄でかまいません。" class="p-content-rating__textarea c-textarea c-textarea--s <?php is_Err('text'); ?>"></textarea>
          </div>
          <input type="submit" value="レビューする" name="rating" class="p-content-rating__comment-btn c-btn c-btn--active c-btn--l">
          <?php } ?>
        </form>
      </div>
      <?php if(empty($com_off)){ ?>
      <div class="p-content-comment">
        <h3 class="p-content-comment__sub-heading c-sub-heading">コメント</h3>
        <?php
          if(!empty($comments)){
            foreach($comments as $key => $val){
        ?>
        <form action="" method="post">
          <div class="p-content-comment__comment-row" id="com_id<?php echo sanitize($val['com_id']); ?>">
            <div class="p-content-comment__comment-wrapper">
              <div class="p-content-comment__score-wrapper">
                <span class="p-content-comment__icon-wrapper">
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php echo ($val['rate_score'] >= 0.5 && $val['rate_score'] < 1) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php if($val['rate_score'] >= 1) echo 'c-fonticon--active-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php echo ($val['rate_score'] >= 1.5 && $val['rate_score'] < 2) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php if($val['rate_score'] >= 2) echo 'c-fonticon--active-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php echo ($val['rate_score'] >= 2.5 && $val['rate_score'] < 3) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php if($val['rate_score'] >= 3) echo 'c-fonticon--active-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php echo ($val['rate_score'] >= 3.5 && $val['rate_score'] < 4) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php if($val['rate_score'] >= 4) echo 'c-fonticon--active-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php echo ($val['rate_score'] >= 4.5 && $val['rate_score'] < 5) ? 'c-fonticon--active-star-half' : 'c-fonticon--hide-star'; ?>"></i>
                  <i class="fa-sharp fa-solid fa-star p-content-comment__fonticon c-fonticon <?php if($val['rate_score'] >= 5) echo 'c-fonticon--active-star'; ?>"></i>
                </span>
                <span class="p-content-comment__num-info"><?php echo $val['rate_score']; ?>点</span>
              </div>
              <p class="p-content-comment__confirm-text c-confirm-text"><?php echo nl2br(sanitize($val['text'])); ?></p>
              <div>
                <span class="p-content-comment__record-info c-record-info"><?php echo mb_substr(sanitize($val['create_date']),0,-3,'UTF-8').'&nbsp;'; ?></span>
                <span class="p-content-comment__record-item c-record-item">
                <?php
                  if(!empty($val['age_id']) && !empty($val['age_flag'])){
                    echo $age_list[($val['age_id'] - 1)]['age'].'&nbsp;';
                  }
                  if(!empty($val['gender_id']) && !empty($val['gender_flag'])){
                    echo $gender_list[($val['gender_id'] - 1)]['gender'];
                  }
                ?>
                </span>
              </div>
            </div>
            <input type="hidden" name="<?php echo 'com_id'.sanitize($val['com_id']).'_u_id'; ?>" value="<?php echo sanitize($val['u_id']); ?>">
            <input type="hidden" name="<?php echo 'com_id'.sanitize($val['com_id']).'_reported_num'; ?>" value="<?php echo sanitize($val['reported_num']); ?>">
            <div class="p-content-comment__cmd-wrapper">
              <span>
                <span class="p-content-comment__cmd c-choice-cmd js-good" data-good_comment="<?php echo sanitize($val['com_id']); ?>">
                  <i class="fa-solid fa-thumbs-up p-content-comment__fonticon js-good-icon" data-already_good="false"></i>
                  <span><?php echo $good_cmd; ?></span>
                </span>
                <span class="p-content-comment__num-info js-good-num"><?php echo sanitize($val['good_num']); ?></span>
              </span>
              <?php if(!empty($admin_login)){ ?>
              <input type="submit" name="<?php echo sanitize($val['com_id']); ?>" value="<?php echo $delete_cmd; ?>" class="p-content-comment__cmd c-choice-cmd">
              <?php }else{ ?>
              <input type="submit" name="<?php echo sanitize($val['com_id']); ?>" value="<?php echo $report_cmd; ?>" class="p-content-comment__cmd c-choice-cmd">
              <?php } ?>
            </div>
          </div>
        </form>
          <?php
              }
            }else{ 
          ?>
          <p class="p-content-comment__text">まだコメントはありません</p>
          <?php } ?>
      </div>
      <?php } ?>
      <a href="<?php echo (!empty($admin_login)) ? 'admin_content_search_secret.php'.keepGETparam(array('b_id')) : 'index.php'.keepGETparam(array('b_id')); ?>" class="c-btn c-btn--inactive u-block c-btn--s">戻る</a>
    </main>
<?php
  require('footer.php');
  debugFinish();
?>