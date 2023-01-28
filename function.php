<?php
  if(basename($_SERVER['PHP_SELF']) === 'function.php'){
    header('Location:index.php');
    exit();
  }
  //エラーの設定========================================================================
  ini_set('log_errors','on');
  ini_set('error_log','php.log');

  //デバッグ===========================================================================
  $debug_flag = false;

  function debug($str){
    global $debug_flag;
    if(!empty($debug_flag)){
      error_log('DEBUG: '.$str);
    }
  }

  function debugStart(){
    global $title;
    error_log('========================================================================');
    error_log('デバッグ開始');
    error_log(basename($_SERVER['PHP_SELF']).' '.$title);
    error_log(print_r($_SESSION,true));
    error_log('========================================================================');
  }

  function debugFinish(){
    error_log('========================================================================');
    error_log('デバッグ終了');
    error_log('========================================================================');
  }

  //セッション===========================================================================
  session_save_path('/var/tmp');
  ini_set('session.gc_maxlifetime',60*60*24*30);
  ini_set('session.cookie_lifetime',60*60*24*30);

  session_start();
  session_regenerate_id();

  //DBの設定============================================================================
  define('DB_SOFT','DBソフト名');
  define('DB_NAME','DB名');
  define('HOST_NAME','ホスト名');

  function dbConnect(){
    $dsn = DB_SOFT.':dbname='.DB_NAME.';host='.HOST_NAME.';charset=utf8';
    $user = 'ユーザー';
    $pass = 'パスワード';
    //MySQL
    $options = array(
      PDO::ATTR_ERRMODE => (!empty($debug_flag)) ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );

    $dbh = new PDO($dsn,$user,$pass,$options);

    return $dbh;
  }

  function queryPost($dbh,$sql,$data){
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)){
      debug('クエリ失敗');
      debug('失敗したSQL: '.$sql);
      return 0;
    }
    debug('クエリ成功');
    return $stmt;
  }

  //定数===============================================================================
  //define('','');

  //エラー定数
  //入力必須系
  define('REQ01','入力必須です');
  define('REQ02','削除するものを選択してください');
  define('REQ03','復元するものを選択してください');
  define('REQ04','いずれかをお選びください');
  define('REQ05','対応済にするものを選択してください');
  define('REQ06','要対応に戻すものを選択してください');

  //上限系
  define('MAX01','255文字以内でご入力ください');
  define('MAX02','3000字以内でご入力ください');
  define('MAX03','10000字以内でご入力ください');
  define('MAX04','11桁以内でご入力ください');
  define('MAX05','5個以内で選択してください');
  define('MAX06','2048文字以内でご入力ください');
  define('MAX07','2.66MB以内まで対応しています');

  //下限系
  define('MIN01','6文字以上でご入力ください');
  define('MIN02','8文字以上でご入力ください');

  //形式系
  define('TYP01','Email形式でご入力ください');
  define('TYP02','使用可能な文字でご入力ください');
  define('TYP03','よみがなに使用できない文字が含まれています');
  define('TYP04','半角数字で西暦をご入力ください');
  define('TYP05','半角数字でご入力ください');
  define('TYP06','URL形式でご入力ください');
  define('TYP07','対応していない形式です');

  //重複系
  define('DUP01','このアドレスは登録できません');
  define('DUP02','このIDは登録できません');

  //非論理系
  define('LOG01','正しく2度ご入力ください');
  define('LOG02','未登録かどちらかが違います');
  define('LOG03','入力内容に誤りがあります');
  define('LOG04','現在とは違うものをご入力ください');
  define('LOG05','未登録か誤りがあります');
  define('LOG06','アップロードできませんでした');

  //その他
  define('ERR01','接続エラーが発生しました');

  //メッセージ定数
  define('MSG01','コンテンツを登録しました');
  define('MSG02','コンテンツを更新しました');
  define('MSG03','コンテンツを削除しました');
  define('MSG04','コンテンツを復元しました');
  define('MSG05','会員登録しました');
  define('MSG06','パスワードを変更しました');
  define('MSG07','IDを変更しました');
  define('MSG08','評価しました');
  define('MSG09','レビューを投稿しました');
  define('MSG10','コメントを削除しました');
  define('MSG11','コメントを通報しました');
  define('MSG12','メールアドレスを変更しました');
  define('MSG13','お気に入りに追加しました');
  define('MSG14','お気に入りから削除しました');
  define('MSG15','認証キーを発行しました');
  define('MSG16','仮パスワードを発行しました');
  define('MSG17','ログインしました');
  define('MSG18','お問い合わせを送信しました');
  define('MSG19','出版社名を追加しました');
  define('MSG20','出版社名を更新しました');
  define('MSG21','ジャンルを追加しました');
  define('MSG22','ジャンルを更新しました');
  define('MSG23','カテゴリーを追加しました');
  define('MSG24','カテゴリーを更新しました');
  define('MSG25','コメントを完全に削除しました');
  define('MSG26','コメントを復元しました');
  define('MSG27','IDを削除しました');
  define('MSG28','IDを復元しました');
  define('MSG29','対応済にしました');
  define('MSG30','要対応にしました');
  define('MSG31','メモを追加しました');
  define('MSG32','メモを削除しました');

  //エラーメッセージ============================================================================
  //メッセージ格納
  $err_msg = array();

  //エラーメッセージ関数
  //$err_msgに追加
  function appendErr($key,$err_code){
    global $err_msg;
    $err_msg[$key] = $err_code;
  }

  //エラーメッセージがあれば表示
  function echoErr($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
      echo $err_msg[$key];
    }
  }

  //エラーメッセージがあるか確認(あればc-err-inputクラスを追加する)
  function is_Err($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
      echo 'c-err-input';
    }
  }
  

  //バリデーション関数===============================================================================

  //入力必須(!empty)
  function validEnter($val,$key,$err_code = REQ01){
    if(empty($val)){
      appendErr($key,$err_code);
    }
  }

  //入力必須(0を認める・空白を認めない)
  function validEnterOkZero($val,$key,$err_code = REQ01){
    if(!isset($val) || mb_strlen(preg_replace('/　|\s+/','',$val)) === 0){
      appendErr($key,$err_code);
    }
  }

  //再入力確認
  function validRetype($val1,$val2,$key,$err_code = LOG01){
    if(!empty($val1) && !empty($val2)){
      if($val1 !== $val2){
        appendErr($key,$err_code);
      }
    }
  }

  //異なる値確認
  function validDiff($val1,$val2,$key){
    if(!empty($val1) && !empty($val2)){
      if($val1 === $val2){
        appendErr($key,LOG04);
      }
    }
  }

  //文字数確認(下限)
  function validMin($val,$key,$min,$err_code){
    if(!empty($val)){
      if(mb_strlen($val) < $min){
        appendErr($key,$err_code);
      }
    }
  }

  //文字数確認(上限)
  function validMax($val,$key,$max = 255,$err_code = MAX01){
    if(!empty($val)){
      if(mb_strlen($val) > $max){
        appendErr($key,$err_code);
      }
    }
  }

  //文字数確認(ぴったり)
  function validLettersNum($val,$key,$num){
    if(!empty($val)){
      if(mb_strlen($val) !== $num){
        appendErr($key,LOG03);
      }
    }
  }

  //要素数確認(上限)
  function validMaxNum($array,$key,$num = 5,$err_code = MAX05){
    if(!empty($array)){
      if(count($array) > 5){
        appendErr($key,MAX05);
      }
    }
  }

  //形式確認(メアド)
  function validEmail($val,$key,$err_code = TYP01){
    if(!empty($val)){
      if(!preg_match('/^[a-z0-9._+^~-]+@[a-z0-9.-]+$/i',$val)){
        appendErr($key,$err_code);
      }
    }
  }

  //形式確認(半角英数字記号)
  function validIdPass($val,$key,$err_code = TYP02){
    if(!empty($val)){
      if(!preg_match('/^[a-zA-Z0-9!?-_;:!&#%=<>\\\*\?\+\$\|\^\.\(\)\[\]]+$/',$val)){
        appendErr($key,$err_code);
      }
    }
  }

  //形式確認(半角英数字)
  function validHalf($val,$key){
    if(!empty($val)){
      if(!preg_match('/^[a-zA-Z0-9]+$/',$val)){
        appendErr($key,LOG03);
      }
    }
  }

  //形式確認(ひらがなカタカナ半角英数字)
  function validKana($val,$key){
    if(!empty($val)){
      if(!preg_match('/^[ぁ-んァ-ヶーa-zA-Z0-9]+$/u',$val)){
        appendErr($key,TYP03);
      }
    }
  }

  //形式確認(西暦)
  function validYear($val,$key){
    if(!empty($val)){
      if(!preg_match('/19\d{2}|2\d{3}/',$val)){
        appendErr($key,TYP04);
      }
    }
  }

  //形式確認(半角数字)
  function validHalfNum($val,$key){
    if(!empty($val)){
      if(!preg_match('/^[0-9]+$/',$val)){
        appendErr($key,TYP05);
      }
    }
  }

  //形式確認(URL)
  function validURL($val,$key){
    if(!empty($val)){
      if(!preg_match('/(https?|ftp)(:\/\/[\w\/:%#\$&\?\(\)~\.=\+\-]+)/',$val)){
        appendErr($key,TYP06);
      }
    }
  }

  //重複確認(メアド)
  function validDupEmail($val,$key){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM user WHERE email = :email AND delete_flag = 0'; //つど ユーザーテーブル
      $data = array(':email' => $val);

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if(!empty(array_shift($result))){
        appendErr($key,DUP01);
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      appendErr('common',ERR01);
    }
  }

  //重複確認(ログインID)
  function validDupId($val,$key,$user_flag = true){
    try{
      $dbh = dbConnect();
      $sql = (!empty($user_flag)) ? 'SELECT count(*) FROM user WHERE login_id = :login_id AND delete_flag = 0' : 'SELECT count(*) FROM admin WHERE login_id = :login_id AND delete_flag = 0';
      $data = array(':login_id' => $val);

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if(!empty(array_shift($result))){
        appendErr($key,DUP02);
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      appendErr('common',ERR01);
    }
  }

  //パスワード確認(ログイン中)
  function validPassword($val,$key,$id,$user_flag = true){
    try{
      $dbh = dbConnect();
      $sql = (!empty($user_flag)) ? 'SELECT pass FROM user WHERE u_id = :id AND delete_flag = 0' : 'SELECT pass FROM admin WHERE a_id = :id AND delete_flag = 0';
      $data = array(':id' => $id);

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if(!password_verify($val,array_shift($result))){
        appendErr($key,LOG03);
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      appendErr('common',ERR01);
    }
  }


  //入力保持関数===============================================================================

  //無害化関数
  function sanitize($val){
    return htmlspecialchars($val,ENT_QUOTES);
  }

  //テキスト入力保持関数
  function keepTextData($val,$key,$method_flag = false){
    $method = (!empty($method_flag)) ? $_GET : $_POST;

    global $dbInfo; //DBから読み込む情報
    global $err_msg;
    if(!empty($dbInfo)){
      if(!empty($err_msg)){
        if(!empty($val)){
          return sanitize($val);
        }else{
          return sanitize($dbInfo[$key]);
        }

      }else{
        if(!empty($val)){
          return sanitize($val);
        }else{
          return sanitize($dbInfo[$key]);
        }
      }

    }else{
      //DBを参照せず、値が送信された時
      if(isset($val)){
        return sanitize($val);
      }
    }
  }

  //選択肢入力保持関数
  function keepSelectData($val,$key,$num,$type_flag = false,$method_flag = true){
    $method = (!empty($method_flag)) ? $_GET : $_POST;

    global $dbInfo; //DBから読み込む情報
    global $err_msg;
    if(!empty($dbInfo)){
      //DBを参照する時
      if(!empty($err_msg[$key])){
        if(isset($val)){
          //値が送信された時
          if((int)$val === (int)$num){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }

        }else{
          if((int)$dbInfo[$key] === (int)$num){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }
        }

      }else{
        if(isset($val)){
          //値が送信された時
          if((int)$val === (int)$num){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }

        }else{
          //値が送信されていないとき
          if((int)$dbInfo[$key] === (int)$num){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }
        }
      }

    }else{
      //DBを参照しない時
      //値が送信されていないとき
      if(!isset($val) && (int)$num === 0){
        //true→optionのとき / false→radio・checkboxのとき
        echo (!empty($type_flag)) ? 'selected' : 'checked';

      }else{
        //値が送信された時
        if((int)$val === (int)$num){
          //true→optionのとき / false→radio・checkboxのとき
          echo (!empty($type_flag)) ? 'selected' : 'checked';
        }
      }
    }
  }

  //選択肢入力保持関数(複数選択)
  function keepSelectComplex($array,$key,$num,$type_flag = false){

    global $dbArray; //DBから読み込む情報
    global $err_msg;
    if(!empty($dbArray)){
      //DBを参照する時
      if(!empty($err_msg[$key])){
        if(!empty($array)){
          //値が送信された時
          if(in_array($num,$array,true)){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }

        }else{
          if(in_array($num,$dbArray,true)){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }
        }

      }else{
        if(!empty($array)){
          //値が送信された時
          if(in_array($num,$array,true)){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }

        }else{
          //値が送信されていないとき
          if(in_array($num,$dbArray,true)){
            //true→optionのとき / false→radio・checkboxのとき
            echo (!empty($type_flag)) ? 'selected' : 'checked';
          }
        }
      }

    }else{
      //DBを参照しない時
      //値が送信された時
      if(!empty($array) && in_array($num,$array,true)){
        //true→optionのとき / false→radio・checkboxのとき
        echo (!empty($type_flag)) ? 'selected' : 'checked';
      }
    }
  }

  //DBカラム結合関数
  function mergeDBcolumn($num,$db_column){
    global $dbInfo;
    $dbArray = array();

    if(!empty($dbInfo)){
      for($i=1;$i<=$num;$i++){
        if($dbInfo[$db_column.$i] !== null){
            $dbArray[] = $dbInfo[$db_column.$i];
        }
      }
    }

    return $dbArray;
  }

  //GETパラメータ維持関数
  function keepGETparam($del_key = array()){
    if(!empty($_GET)){
      $str = '?';
      foreach($_GET as $key => $val){
        if(!in_array($key,$del_key,true)){
          if(is_array($val)){
            foreach($val as $val_key => $val_val){
              $str .= $key.'[]='.$val_val.'&';
            }
          }else{
            $str .= $key.'='.$val.'&';
          }
        }
      }
      return mb_substr($str,0,-1,'UTF-8');
    }
  }

  //その他(DB関連以外)========================================================================
  
  //ログイン確認関数
  function is_Login($user_flag = true){
    if(!empty($user_flag)){
      //ユーザーがログインしているか調べる
      if(!empty($_SESSION['u_login_date']) && (time() <= $_SESSION['u_login_date'] + $_SESSION['u_login_limit'])){
        return true;

      }else{
        return false;
      }

    }else{
      //管理人がログインしているか調べる
      if(!empty($_SESSION['a_login_date']) && (time() <= $_SESSION['a_login_date'] + $_SESSION['a_login_limit'])){
        return true;

      }else{
        return false;
      }
    }
  }

  //認証キー・仮パスワード生成関数
  function makeRandomStr(){
    $str = '';
    $letters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    for($i = 0; $i < 12; $i++){
      $str .= $letters[mt_rand(0,61)];
    }

    return $str;
  }

  //input[type="datetime-local"]からDATETIMEへフォーマット
  function local2Datetime($datetime){
    return mb_substr($datetime,0,10,'UTF-8').' '.mb_substr($datetime,11,5,'UTF-8').':00';
  }
  //DATETIMEからinput[type="datetime-local"]へフォーマット
  function datetime2Local($datetime){
    if(!empty($datetime)){
      return mb_substr($datetime,0,10,'UTF-8').'T'.mb_substr($datetime,11,5,'UTF-8');
    }
  }
  //DATETIMEからY/n/j G:iへフォーマット
  function datetime2Calendar($datetime){
    $date = strtotime(sanitize($datetime));
    echo date('Y/n/j G:i',$date);
  }

  //メール送信関数
  function sendMail($to,$sub,$text,$from){
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');

    mb_send_mail($to,$sub,$text,'From: '.$from);
  }

  //画像アップロード関数
  function uploadImg($file,$key){
    //1.エラー情報が正確に格納されてるか調べる
    if(isset($file['error']) && is_int($file['error'])){
      try{
        switch($file['error']){
          case UPLOAD_ERR_OK:
            break;
          case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException(L0G06);
          case UPLOAD_ERR_INI_SIZE:
            throw new RuntimeException(MAX07);
          case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException(MAX07);
          default:
            throw new RuntimeException(LOG06);
        }

        //2.ファイル形式を調べる
        $type = @exif_imagetype($file['tmp_name']);
        if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_WEBP],true)){
          throw new RuntimeException(TYP07);
        }

        //3.アップロード先にアップロードできたかどうか確認する
        $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
        if(!move_uploaded_file($file['tmp_name'],$path)){
          throw new RuntimeException(LOG06);
        }

        //4.ファイル権限を設定する
        chmod($path,0644);

        //アップロード先のパスを返す
        return $path;

      }catch (RuntimeException $e){
        debug('エラー: '.$e->getMessage());
        appendErr($key,$e->getMessage());
      }
    }
  }

  //画像表示関数
  function displayImg($img){
    if(!empty($img)){
      echo $img;
    }else{
      echo 'img/no_image.png';
    }
  }

  //成功メッセージ表示関数
  function getMsg(){
    if(!empty($_SESSION['success'])){
      $msg = $_SESSION['success'];
      unset($_SESSION['success']);
      echo $msg;
    }
  }

  //ページネーション
  function pagenation($url,$current_page,$total_page,$page_span = 5){
    $current_page = (int)$current_page;
    $total_page = (int)$total_page;
    
    if($total_page < $page_span){
      $min_page = 1;
      $max_page = $total_page;

    }elseif($current_page <= ($page_span - 1) / 2){
      $min_page = 1;
      $max_page = $page_span;

    }elseif($total_page - $current_page < ($page_span - 1) / 2){
      $min_page = $total_page - ($page_span - 1);
      $max_page = $total_page;

    }else{
      $min_page = $current_page - ($page_span - 1) / 2;
      $max_page = $current_page + ($page_span - 1) / 2;
    }

      echo '<nav class="p-pagenation">';
        echo '<ul class="p-pagenation__pagelist">';
        if($current_page !== 1){
          echo '<div class="p-pagenation__prev-wrapper">';
            echo '<li class="p-pagenation__page c-page">';
              echo '<a href="'.$url.keepGETparam(array('p'));
              echo (!empty(keepGETparam(array('p')))) ? '&' : '?';
              echo 'p=1" class="p-pagenation__link c-page__link">';
              echo '|&lt;';
              echo '</a>';
            echo '</li>';
            echo '<li class="p-pagenation__page c-page">';
              echo '<a href="'.$url.keepGETparam(array('p'));
              echo (!empty(keepGETparam(array('p')))) ? '&' : '?';
              echo 'p='.((int)$_GET['p'] - 1).'" class="p-pagenation__link c-page__link">';
              echo '&lt;&nbsp;prev';
              echo '</a>';
            echo '</li>';
          echo '</div>';
        }
          echo '<div class="p-pagenation__number-wrapper">';
      if($total_page > 1){
        for($i=$min_page; $i<=$max_page; $i++){
            echo '<li class="p-pagenation__page c-page';
            echo ($i === $current_page) ? ' c-page--active">' : '">';
              echo '<a href="'.$url.keepGETparam(array('p'));
              echo (!empty(keepGETparam(array('p')))) ? '&' : '?';
              echo 'p='.$i.'" class="p-pagenation__link c-page__link';
              echo ($i === $current_page) ? ' c-page__link--active">' : '">';
              echo $i;
              echo '</a>';
            echo '</li>';
        }
      }
          echo '</div>';
        if($total_page > 1 && $current_page !== $total_page){
          echo '<div class="p-pagenation__next-wrapper">';
            echo '<li class="p-pagenation__page c-page">';
              echo '<a href="'.$url.keepGETparam(array('p'));
              echo (!empty(keepGETparam(array('p')))) ? '&' : '?';
              echo 'p=';
              echo  (!empty($_GET['p'])) ? ((int)$_GET['p'] + 1) : 2;
              echo '" class="p-pagenation__link c-page__link">';
              echo 'next&nbsp;&gt;';
              echo '</a>';
            echo '</li>';
            echo '<li class="p-pagenation__page c-page">';
              echo '<a href="'.$url.keepGETparam(array('p'));
              echo (!empty(keepGETparam(array('p')))) ? '&' : '?';
              echo 'p='.$total_page.'" class="p-pagenation__link c-page__link">';
              echo '&gt;|';
              echo '</a>';
            echo '</li>';
          echo '</div>';
        }
        echo '</ul>';
      echo '</nav>';

  }

  //DB取得関数===============================================================================

  //固定取得===============================================================================
  //年齢取得関数
  function getAge(){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT age_id,age FROM age WHERE delete_flag = 0';
      $data = array();

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch(Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('年齢を取得できない');
    }
  }

  //ジェンダー取得関数
  function getGender(){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT gender_id,gender FROM gender WHERE delete_flag = 0';
      $data = array();

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch(Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('ジェンダーを取得できない');
    }
  }

  //問い合わせ内容取得関数
  function getSubject(){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT subject_id,subject FROM subject WHERE delete_flag = 0';
      $data = array();

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch(Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('問い合わせ内容を取得できない');
    }
  }

  //出版社名取得関数
  function getSeller($flag = false){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT s_id,s_name FROM seller WHERE delete_flag = 0';
      if(empty($flag)){
        $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci ASC';
      }

      $data = array();

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch(Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('出版社名を取得できない');
    }
  }

  //ジャンル取得関数
  function getGenre($flag = false){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT g_id,genre FROM genre WHERE delete_flag = 0';
      if(empty($flag)){
        $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci ASC';
      }

      $data = array();

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch(Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('ジャンルを取得できない');
    }
  }

  //カテゴリー取得関数
  function getCategory($flag = false){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT c_id,c_name FROM category WHERE delete_flag = 0';
      if(empty($flag)){
        $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci ASC';
      }

      $data = array();

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch(Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('カテゴリーを取得できない');
    }
  }

  //ユーザープロフィール取得関数
  function getUserProf($u_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT age_id,age_flag,gender_id,gender_flag FROM user WHERE u_id = :u_id AND delete_flag = 0';
      $data = array(':u_id' => $u_id);

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('プロフィールを取得できない');
    }
  }

  //メールアドレス取得関数
  function getUserEmail($u_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT email FROM user WHERE u_id = :u_id AND delete_flag = 0';
      $data = array(':u_id' => $u_id);

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return array_shift($result);

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('メールアドレスを取得できない');
    }
  }

  //ログインID取得関数
  function getUserId($u_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT login_id FROM user WHERE u_id = :u_id AND delete_flag = 0';
      $data = array(':u_id' => $u_id);

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return array_shift($result);

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('ログインIDを取得できない');
    }
  }

  //問い合わせ取得関数(1件)
  function getInquiry($i_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT name,email,subject_id,text,create_date FROM inquiry WHERE i_id = :i_id';
      $data = array(':i_id' => $i_id);

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('問い合わせを取得できない');
    }
  }

  //問い合わせメモ取得関数
  function getAnswer($i_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT answer_id,a_id,answer,create_date FROM answer WHERE i_id = :i_id AND delete_flag = 0 ORDER BY create_date DESC';
      $data = array(':i_id' => $i_id);

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('問い合わせメモを取得できない');
    }
  }

  //コンテンツ取得関数(1件)
  function getContent($b_id,$admin_flag = false){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT content_title,catchphrase,text,year,price,author,s_id,g_id,c_id1,c_id2,c_id3,c_id4,c_id5,com_off,pic1,pic2,pic3,release_date,visited,average_rate,com_num,fav_num,delete_flag,update_date FROM book WHERE b_id = :b_id';
      if(empty($admin_flag)){
        $sql .= ' AND delete_flag = 0';
      }

      $data = array(':b_id' => $b_id);

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetch(PDO::FETCH_ASSOC);

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('コンテンツを取得できない');
    }
  }

  //コメント取得関数(1コンテンツ)
  function getComment($b_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT com_id,u_id,age_id,age_flag,gender_id,gender_flag,rate_score,text,good_num,reported_num,create_date FROM comment WHERE b_id = :b_id AND delete_flag = 0 ORDER BY create_date DESC';
      $data = array(':b_id' => $b_id);

      $stmt = queryPost($dbh,$sql,$data);
      if($stmt){
        return $stmt->fetchAll();

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('コメントを取得できない');
    }
  }

  //コメントいいねの数取得関数(1コメント)
  function getGoodNum($com_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT good_num FROM comment WHERE com_id = :com_id AND delete_flag = 0';
      $data = array(':com_id' => $com_id);

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if($stmt){
        return array_shift($result);
  
      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('コメントを取得できない');
    }
  }

  //評価取得関数
  function getRating($b_id){
    try{
      //評価件数
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM rating WHERE b_id = :b_id AND delete_flag = 0';
      $data = array(':b_id' => $b_id);

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC); 
      if($stmt){
        $rating['num'] = array_shift($result);

      }else{
        return false;
      }

      //評価合計得点
      $sql = 'SELECT SUM(rate_score) FROM rating WHERE b_id = :b_id AND delete_flag = 0';

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC); 
      if($stmt){
        $rating['total'] = array_shift($result);
        return $rating;
        
      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('評価を取得できない');
    }
  }

  //コメント通報・削除回数取得(ユーザー)
  function getReportDeleteNum($u_id){
    if(!empty($u_id)){
      try{
        $dbh = dbConnect();
        $sql = 'SELECT reported_num,delete_num FROM user WHERE u_id = :u_id';
        $data = array(':u_id' => $u_id);
  
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
          return $stmt->fetch(PDO::FETCH_ASSOC);
  
        }else{
          return false;
        }
  
      }catch (Exception $e){
        debug('エラー発生: '.$e->getMessage());
        debug('通報・削除回数を取得できない');
      }

    }else{
      return false;
    }
  }

  //お気に入り確認関数
  function is_Favorite($u_id,$b_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM favorite WHERE u_id = :u_id AND b_id = :b_id';
      $data = array(
        ':u_id' => $u_id,
        ':b_id' => $b_id
      );

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      if(!empty(array_shift($result))){
        return true;
      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('お気に入りを確認できない');
    }
  }

  //お気に入り数確認関数
  function getFavNum($b_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM favorite WHERE b_id = :b_id';
      $data = array(':b_id' => $b_id);

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(is_numeric($result['count(*)'])){
        return array_shift($result);
      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('お気に入り数を確認できない');
    }
  }

  //コメント数取得関数
  function getComNum($b_id){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM comment WHERE b_id = :b_id AND delete_flag = 0';
      $data = array(':b_id' => $b_id);

      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(is_numeric($result['count(*)'])){
        return array_shift($result);
      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('コメント数を確認できない');
    }
  }

  //検索取得===============================================================================
  //アカウント取得関数
  function getEditAccount($words,$words_logic_flag,$reported_num,$delete_num,$delete_appear,$logic_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //ユーザーIDの総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM user WHERE delete_flag = ?';
      //各条件
      if(!empty($words) || !empty($reported_num) || !empty($delete_num)){
        $sql .= ' AND (';
      }

      //通報された回数
      if(!empty($reported_num)){
        $sql .= 'reported_num >= ?';
      }
      //コメント削除された回数
      if(!empty($delete_num)){
        if(!empty($reported_num)){
          $sql .= (empty($logic_flag)) ? ' AND delete_num >= ?' : ' OR delete_num >= ?';

        }else{
          $sql .= 'delete_num >= ?';
        }
      }
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = 'login_id COLLATE utf8_unicode_ci LIKE ?';//特定のカラムから探す
          $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : 'login_id COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : 'login_id COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        if(!empty($delete_num) || !empty($reported_num)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;

        }else{
          $sql .= $words_condition;
        }
      }

      if(!empty($words) || !empty($reported_num) || !empty($delete_num)){
        $sql .= ')';
      }

      $stmt = $dbh->prepare($sql);

      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //通報された回数
      if(!empty($reported_num)){
        $stmt->bindValue(2,$reported_num);
      }
      //コメント削除された回数
      if(!empty($delete_num)){
        if(!empty($reported_num)){
          $stmt->bindValue(3,$delete_num);

        }else{
          $stmt->bindValue(2,$delete_num);
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($reported_num)){
          if(!empty($delete_num)){
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 3,$search_words[($i - 1)]);
            }

          }else{
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 2,$search_words[($i - 1)]);
            }
          }

        }else{
          if(!empty($delete_num)){
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 2,$search_words[($i - 1)]);
            }

          }else{
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 1,$search_words[($i - 1)]);
            }
          }
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //ユーザーIDを取得する
      $sql = 'SELECT u_id,login_id,reported_num,delete_num,delete_flag FROM user WHERE delete_flag = ?';

      //各条件
      if(!empty($words) || !empty($reported_num) || !empty($delete_num)){
        $sql .= ' AND (';
      }

      //通報された回数
      if(!empty($reported_num)){
        $sql .= 'reported_num >= ?';
      }
      //コメント削除された回数
      if(!empty($delete_num)){
        if(!empty($reported_num)){
          $sql .= (empty($logic_flag)) ? ' AND delete_num >= ?' : ' OR delete_num >= ?';

        }else{
          $sql .= 'delete_num >= ?';
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($delete_num) || !empty($reported_num)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;
  
        }else{
          $sql .= $words_condition;
        }
      }

      if(!empty($words) || !empty($reported_num) || !empty($delete_num)){
        $sql .= ')';
      }

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);

      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //通報された回数
      if(!empty($reported_num)){
        $stmt->bindValue(2,$reported_num);
      }
      //コメント削除された回数
      if(!empty($delete_num)){
        if(!empty($reported_num)){
          $stmt->bindValue(3,$delete_num);

        }else{
          $stmt->bindValue(2,$delete_num);
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($reported_num)){
          if(!empty($delete_num)){
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 3,$search_words[($i - 1)]);
            }
            $stmt->bindValue(count($words) + 4,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(count($words) + 5,$current_min_record,PDO::PARAM_INT);

          }else{
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 2,$search_words[($i - 1)]);
            }
            $stmt->bindValue(count($words) + 3,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(count($words) + 4,$current_min_record,PDO::PARAM_INT);
          }

  
        }else{
          if(!empty($delete_num)){
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 2,$search_words[($i - 1)]);
            }
            $stmt->bindValue(count($words) + 3,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(count($words) + 4,$current_min_record,PDO::PARAM_INT);

          }else{
            for($i=1; $i<=count($words); $i++){
              $stmt->bindValue($i + 1,$search_words[($i - 1)]);
            }
            $stmt->bindValue(count($words) + 2,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(count($words) + 3,$current_min_record,PDO::PARAM_INT);
          }
        }

      }else{
        if(!empty($reported_num)){
          if(!empty($delete_num)){
            $stmt->bindValue(4,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(5,$current_min_record,PDO::PARAM_INT);

          }else{
            $stmt->bindValue(3,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(4,$current_min_record,PDO::PARAM_INT);
          }

        }else{
          if(!empty($delete_num)){
            $stmt->bindValue(3,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(4,$current_min_record,PDO::PARAM_INT);
          }else{
            $stmt->bindValue(2,$record_span,PDO::PARAM_INT);
            $stmt->bindValue(3,$current_min_record,PDO::PARAM_INT);
          }
        }
      }

      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('ユーザーIDを取得できない');
    }
  }

  //カテゴリー取得(編集用)
  function getEditCategory($words,$words_logic_flag,$flow_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //カテゴリーの総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM category';
      //各条件
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = 'c_name COLLATE utf8_unicode_ci LIKE ?';//特定のカラムから探す
          $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : 'c_name COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : 'c_name COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        $sql .= ' WHERE '.$words_condition;
      }

      //並び順
      $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci';
      $sql .= (empty($flow_flag)) ? ' ASC' : ' DESC';

      $stmt = $dbh->prepare($sql);

      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i,$search_words[($i - 1)]);
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //カテゴリーを取得する
      $sql = 'SELECT c_id,c_name,kana FROM category';

      //フリーワード
      if(!empty($words)){
        $sql .= ' WHERE '.$words_condition;
      }

      //並び順
      $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci';
      $sql .= (empty($flow_flag)) ? ' ASC' : ' DESC';

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);

      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i,$search_words[($i - 1)]);
        }
        $stmt->bindValue(count($words) + 1,$record_span,PDO::PARAM_INT);
        $stmt->bindValue(count($words) + 2,$current_min_record,PDO::PARAM_INT);

      }else{
        $stmt->bindValue(1,$record_span,PDO::PARAM_INT);
        $stmt->bindValue(2,$current_min_record,PDO::PARAM_INT);
      }


      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('カテゴリーを取得できない');
    }
  }

  //ジャンルー取得(編集用)
  function getEditGenre($words,$words_logic_flag,$flow_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //カテゴリーの総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM genre';
      //各条件
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = 'genre COLLATE utf8_unicode_ci LIKE ?';//特定のカラムから探す
          $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : 'genre COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : 'genre COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        $sql .= ' WHERE '.$words_condition;
      }

      //並び順
      $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci';
      $sql .= (empty($flow_flag)) ? ' ASC' : ' DESC';

      $stmt = $dbh->prepare($sql);

      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i,$search_words[($i - 1)]);
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //カテゴリーを取得する
      $sql = 'SELECT g_id,genre,kana FROM genre';

      //フリーワード
      if(!empty($words)){
        $sql .= ' WHERE '.$words_condition;
      }

      //並び順
      $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci';
      $sql .= (empty($flow_flag)) ? ' ASC' : ' DESC';

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);

      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i,$search_words[($i - 1)]);
        }
        $stmt->bindValue(count($words) + 1,$record_span,PDO::PARAM_INT);
        $stmt->bindValue(count($words) + 2,$current_min_record,PDO::PARAM_INT);

      }else{
        $stmt->bindValue(1,$record_span,PDO::PARAM_INT);
        $stmt->bindValue(2,$current_min_record,PDO::PARAM_INT);
      }


      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('ジャンルを取得できない');
    }
  }

  //出版社名取得(編集用)
  function getEditSeller($words,$words_logic_flag,$flow_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //出版社名の総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM seller';
      //各条件
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = 's_name COLLATE utf8_unicode_ci LIKE ?';//特定のカラムから探す
          $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : 's_name COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : 's_name COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        $sql .= ' WHERE '.$words_condition;
      }

      //並び順
      $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci';
      $sql .= (empty($flow_flag)) ? ' ASC' : ' DESC';

      $stmt = $dbh->prepare($sql);

      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i,$search_words[($i - 1)]);
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //出版社名を取得する
      $sql = 'SELECT s_id,s_name,kana FROM seller';

      //フリーワード
      if(!empty($words)){
        $sql .= ' WHERE '.$words_condition;
      }

      //並び順
      $sql .= ' ORDER BY kana COLLATE utf8_unicode_ci';
      $sql .= (empty($flow_flag)) ? ' ASC' : ' DESC';

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);

      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i,$search_words[($i - 1)]);
        }
        $stmt->bindValue(count($words) + 1,$record_span,PDO::PARAM_INT);
        $stmt->bindValue(count($words) + 2,$current_min_record,PDO::PARAM_INT);

      }else{
        $stmt->bindValue(1,$record_span,PDO::PARAM_INT);
        $stmt->bindValue(2,$current_min_record,PDO::PARAM_INT);
      }


      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('出版社名を取得できない');
    }
  }

  //問い合わせ取得関数(一覧)
  function getSearchInquiry($words,$words_logic_flag,$subject_id,$start,$finish,$delete_appear,$logic_flag,$flow_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //問い合わせの総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM inquiry WHERE delete_flag = ?';
      //各条件
      if(!empty($words) || !empty($subject_id) || !empty($start) || !empty($finish)){
        $sql .= ' AND (';
      }
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = 'text COLLATE utf8_unicode_ci LIKE ?';//特定のカラムから探す
          $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : 'text COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : 'text COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        $sql .= $words_condition;
      }
      //内容
      if(!empty($subject_id)){
        if(!empty($words)){
          $sql .= (empty($logic_flag)) ? ' AND subject_id = ?' : ' OR subject_id = ?';

        }else{
          $sql .= 'subject_id = ?';
        }
        
      }
      //時間
      if(empty($start) || empty($finish)){
        //どちらか一方のみ
        //時間(from)
        if(!empty($start)){
          if(!empty($words) || !empty($subject_id)){
            $sql .= (empty($logic_flag)) ? ' AND create_date >= ?' : ' OR create_date >= ?';
          }else{
            $sql .= 'create_date >= ?';
          }
        }
        //時間(to)
        if(!empty($finish)){
          if(!empty($words) || !empty($subject_id)){
            $sql .= (empty($logic_flag)) ? ' AND create_date <= ?' : ' OR create_date <= ?';
  
          }else{
            $sql .= 'create_date <= ?';
          }
        }

      }else{
        //両方
        if(!empty($words) || !empty($subject_id)){
          $sql .= (empty($logic_flag)) ? ' AND (create_date >= ? AND create_date <= ?)' : ' OR (create_date >= ? AND create_date <= ?)';

        }else{
          $sql .= 'create_date >= ? AND create_date <= ?';
        }
      }


      if(!empty($words) || !empty($subject_id) || !empty($start) || !empty($finish)){
        $sql .= ')';
      }
      //並び順
      $sql .= ' ORDER BY create_date';
      $sql .= (empty($flow_flag)) ? ' DESC' : ' ASC';

      $stmt = $dbh->prepare($sql);

      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i + 1,$search_words[($i - 1)]);
        }
      }
      //内容
      if(!empty($subject_id)){
        if(!empty($words)){
          $stmt->bindValue(count($words) + 2,$subject_id);
        }else{
          $stmt->bindValue(2,$subject_id);
        }
      }
      //時間(from)
      if(!empty($start)){
        if(!empty($words)){
          if(!empty($subject_id)){
            $stmt->bindValue(count($words) + 3,$start);
          }else{
            $stmt->bindValue(count($words) + 2,$start);
          }
        }else{
          if(!empty($subject_id)){
            $stmt->bindValue(3,$start);
          }else{
            $stmt->bindValue(2,$start);
          }
        }
      }
      //時間(to)
      if(!empty($finish)){
        if(!empty($words)){
          if(!empty($subject_id)){
            if(!empty($start)){
              $stmt->bindValue(count($words) + 4,$finish);
            }else{
              $stmt->bindValue(count($words) + 3,$finish);
            }

          }else{
            if(!empty($start)){
              $stmt->bindValue(count($words) + 3,$finish);
            }else{
              $stmt->bindValue(count($words) + 2,$finish);
            }
          }

        }else{
          if(!empty($subject_id)){
            if(!empty($start)){
              $stmt->bindValue(4,$finish);
            }else{
              $stmt->bindValue(3,$finish);
            }

          }else{
            if(!empty($start)){
              $stmt->bindValue(3,$finish);
            }else{
              $stmt->bindValue(2,$finish);
            }
          }
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //問い合わせを取得する
      $sql = 'SELECT i_id,subject_id,text,delete_flag,create_date FROM inquiry WHERE delete_flag = ?';

      //各条件
      if(!empty($words) || !empty($subject_id) || !empty($start) || !empty($finish)){
        $sql .= ' AND (';
      }
      //フリーワード
      if(!empty($words)){
        $sql .= $words_condition;
      }
      //内容
      if(!empty($subject_id)){
        if(!empty($words)){
          $sql .= (empty($logic_flag)) ? ' AND subject_id = ?' : ' OR subject_id = ?';

        }else{
          $sql .= 'subject_id = ?';
        }
      }
      if(empty($start) || empty($finish)){
        //どちらか一方のみ
        //時間(from)
        if(!empty($start)){
          if(!empty($words) || !empty($subject_id)){
            $sql .= (empty($logic_flag)) ? ' AND create_date >= ?' : ' OR create_date >= ?';
          }else{
            $sql .= 'create_date >= ?';
          }
        }
        //時間(to)
        if(!empty($finish)){
          if(!empty($words) || !empty($subject_id)){
            $sql .= (empty($logic_flag)) ? ' AND create_date <= ?' : ' OR create_date <= ?';
  
          }else{
            $sql .= 'create_date <= ?';
          }
        }

      }else{
        //両方
        if(!empty($words) || !empty($subject_id)){
          $sql .= (empty($logic_flag)) ? ' AND (create_date >= ? AND create_date <= ?)' : ' OR (create_date >= ? AND create_date <= ?)';

        }else{
          $sql .= 'create_date >= ? AND create_date <= ?';
        }
      }


      if(!empty($words) || !empty($subject_id) || !empty($start) || !empty($finish)){
        $sql .= ')';
      }
      //並び順
      $sql .= ' ORDER BY create_date';
      $sql .= (empty($flow_flag)) ? ' DESC' : ' ASC';

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);

      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //フリーワード
      if(!empty($words)){
        for($i=1; $i<=count($words); $i++){
          $stmt->bindValue($i + 1,$search_words[($i - 1)]);
        }
      }
      //内容
      if(!empty($subject_id)){
        if(!empty($words)){
          $stmt->bindValue(count($words) + 2,$subject_id);
        }else{
          $stmt->bindValue(2,$subject_id);
        }
      }
      //時間(from)
      if(!empty($start)){
        if(!empty($words)){
          if(!empty($subject_id)){
            $stmt->bindValue(count($words) + 3,$start);
          }else{
            $stmt->bindValue(count($words) + 2,$start);
          }
        }else{
          if(!empty($subject_id)){
            $stmt->bindValue(3,$start);
          }else{
            $stmt->bindValue(2,$start);
          }
        }
      }
      //時間(to)
      if(!empty($finish)){
        if(!empty($words)){
          if(!empty($subject_id)){
            if(!empty($start)){
              $stmt->bindValue(count($words) + 4,$finish);
              $stmt->bindValue(count($words) + 5,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 6,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(count($words) + 3,$finish);
              $stmt->bindValue(count($words) + 4,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 5,$current_min_record,PDO::PARAM_INT);
            }

          }else{
            if(!empty($start)){
              $stmt->bindValue(count($words) + 3,$finish);
              $stmt->bindValue(count($words) + 4,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 5,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(count($words) + 2,$finish);
              $stmt->bindValue(count($words) + 3,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 4,$current_min_record,PDO::PARAM_INT);
            }
          }

        }else{
          if(!empty($subject_id)){
            if(!empty($start)){
              $stmt->bindValue(4,$finish);
              $stmt->bindValue(5,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(6,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(3,$finish);
              $stmt->bindValue(4,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(5,$current_min_record,PDO::PARAM_INT);
            }

          }else{
            if(!empty($start)){
              $stmt->bindValue(3,$finish);
              $stmt->bindValue(4,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(5,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(2,$finish);
              $stmt->bindValue(3,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(4,$current_min_record,PDO::PARAM_INT);
            }
          }
        }

      }else{
        if(!empty($words)){
          if(!empty($subject_id)){
            if(!empty($start)){
              $stmt->bindValue(count($words) + 4,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 5,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(count($words) + 3,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 4,$current_min_record,PDO::PARAM_INT);
            }

          }else{
            if(!empty($start)){
              $stmt->bindValue(count($words) + 3,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 4,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(count($words) + 2,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(count($words) + 3,$current_min_record,PDO::PARAM_INT);
            }
          }

        }else{
          if(!empty($subject_id)){
            if(!empty($start)){
              $stmt->bindValue(4,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(5,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(3,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(4,$current_min_record,PDO::PARAM_INT);
            }
            
          }else{
            if(!empty($start)){
              $stmt->bindValue(3,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(4,$current_min_record,PDO::PARAM_INT);
            }else{
              $stmt->bindValue(2,$record_span,PDO::PARAM_INT);
              $stmt->bindValue(3,$current_min_record,PDO::PARAM_INT);
            }
          }
        }
      }
        
      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('問い合わせを取得できない');
    }
  }

  //コンテンツ取得(検索)
  function getSearchContent($words,$words_logic_flag,$g_id,$category,$category_flag,$s_id,$year,$delete_appear,$logic_flag,$flow_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //コンテンツの総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM book WHERE delete_flag = ? AND (release_date IS ? OR release_date <= ?)';
      //各条件
      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ' AND (';
      }

      //ジャンル
      if(!empty($g_id)){
        $sql .= 'g_id = ?';
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $sql .= (empty($logic_flag)) ? ' AND s_id = ?' : ' OR s_id = ?';

        }else{
          $sql .= 's_id = ?';
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id) || !empty($s_id)){
          $sql .= (empty($logic_flag)) ? ' AND year = ?' : ' OR year = ?';

        }else{
          $sql .= 'year = ?';
        }
      }
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = '(content_title COLLATE utf8_unicode_ci LIKE ? OR author COLLATE utf8_unicode_ci LIKE ? OR text COLLATE utf8_unicode_ci LIKE ? OR catchphrase COLLATE utf8_unicode_ci LIKE ?)';//特定のカラムから探す
          for($i=1;$i<=4;$i++){
            $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
          }
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : '(content_title COLLATE utf8_unicode_ci LIKE ? OR author COLLATE utf8_unicode_ci LIKE ? OR text COLLATE utf8_unicode_ci LIKE ? OR catchphrase COLLATE utf8_unicode_ci LIKE ?)';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : '(content_title COLLATE utf8_unicode_ci LIKE ? OR author COLLATE utf8_unicode_ci LIKE ? OR text COLLATE utf8_unicode_ci LIKE ? OR catchphrase COLLATE utf8_unicode_ci LIKE ?)';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        if(!empty($g_id) || !empty($s_id) || !empty($year)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;

        }else{
          $sql .= $words_condition;
        }
      }
      //カテゴリー
      if(!empty($category)){
        foreach($category as $val){
          $category_condition[] = '(c_id1 = ? OR c_id2 = ? OR c_id3 = ? OR c_id4 = ? OR c_id5 = ?)';//特定のカラムから探す
          for($i=1;$i<=5;$i++){
            $search_category[] = $val;
          }
        }
        if(empty($category_flag)){
          $category_condition = (count($category) > 1) ? '('.implode(' AND ',$category_condition).')' : '(c_id1 = ? OR c_id2 = ? OR c_id3 = ? OR c_id4 = ? OR c_id5 = ?)';//カテゴリーが1つのときと複数のとき 特定のカラムから探す
        }else{
          $category_condition = (count($category) > 1) ? '('.implode(' OR ',$category_condition).')' : '(c_id1 = ? OR c_id2 = ? OR c_id3 = ? OR c_id4 = ? OR c_id5 = ?)';//カテゴリーが1つのときと複数のとき 特定のカラムから探す
        }

        if(!empty($g_id) || !empty($s_id) || !empty($year) || !empty($words)){
          $sql .= (empty($logic_flag)) ? ' AND '.$category_condition : ' OR '.$category_condition;

        }else{
          $sql .= $category_condition;
        }
      }

      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ')';
      }

      //並び順
      switch($flow_flag){
        case 0:
          $sql .= ' ORDER BY b_id DESC';
          break;
        case 1:
          $sql .= ' ORDER BY average_rate DESC';
          break;
        case 2:
          $sql .= ' ORDER BY fav_num DESC';
          break;
        case 3:
          $sql .= ' ORDER BY com_num DESC';
          break;
        case 4:
          $sql .= ' ORDER BY visited DESC';
          break;
        case 5:
          $sql .= ' ORDER BY year DESC';
          break;
        case 6:
          $sql .= ' ORDER BY year ASC';
          break;
        case 7:
          $sql .= ' ORDER BY content_title ASC';
          break;
        case 8:
          $sql .= ' ORDER BY content_title DESC';
          break;
        default:
          break;
      }

      $stmt = $dbh->prepare($sql);


      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //公開日時
      $stmt->bindValue(2,NULL);
      $stmt->bindValue(3,date('Y-m-d H:i:s'));
      //ジャンル
      if(!empty($g_id)){
        $stmt->bindValue(4,$g_id);
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $stmt->bindValue(5,$s_id);
        }else{
          $stmt->bindValue(4,$s_id);
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            $stmt->bindValue(6,$year);
          }else{
            $stmt->bindValue(5,$year);
          }
        }else{
          if(!empty($s_id)){
            $stmt->bindValue(5,$year);
          }else{
            $stmt->bindValue(4,$year);
          }
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 6,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 3,$search_words[($i - 1)]);
              }
            }
          }
        }
      }
      //カテゴリー
      if(!empty($category)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 6,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 6,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 3,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 3,$search_category[($i - 1)]);
                }
              }
            }
          }
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //コンテンツを取得する
      $sql = 'SELECT b_id,content_title,com_off,pic1,visited,average_rate,com_num,fav_num,create_date,delete_flag,update_date FROM book WHERE delete_flag = ? AND (release_date IS ? OR release_date <= ?)';

      //各条件
      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ' AND (';
      }

      //ジャンル
      if(!empty($g_id)){
        $sql .= 'g_id = ?';
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $sql .= (empty($logic_flag)) ? ' AND s_id = ?' : ' OR s_id = ?';

        }else{
          $sql .= 's_id = ?';
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id) || !empty($s_id)){
          $sql .= (empty($logic_flag)) ? ' AND year = ?' : ' OR year = ?';

        }else{
          $sql .= 'year = ?';
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($g_id) || !empty($s_id) || !empty($year)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;
  
        }else{
          $sql .= $words_condition;
        }
      }
      //カテゴリー
      if(!empty($category)){
        if(!empty($g_id) || !empty($s_id) || !empty($year) || !empty($words)){
          $sql .= (empty($logic_flag)) ? ' AND '.$category_condition : ' OR '.$category_condition;
  
        }else{
          $sql .= $category_condition;
        }
      }

      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ')';
      }

      //並び順
      switch($flow_flag){
        case 0:
          $sql .= ' ORDER BY b_id DESC';
          break;
        case 1:
          $sql .= ' ORDER BY average_rate DESC';
          break;
        case 2:
          $sql .= ' ORDER BY fav_num DESC';
          break;
        case 3:
          $sql .= ' ORDER BY com_num DESC';
          break;
        case 4:
          $sql .= ' ORDER BY visited DESC';
          break;
        case 5:
          $sql .= ' ORDER BY year DESC';
          break;
        case 6:
          $sql .= ' ORDER BY year ASC';
          break;
        case 7:
          $sql .= ' ORDER BY content_title ASC';
          break;
        case 8:
          $sql .= ' ORDER BY content_title DESC';
          break;
        default:
          break;
      }

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);

      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //公開日時
      $stmt->bindValue(2,NULL);
      $stmt->bindValue(3,date('Y-m-d H:i:s'));
      //ジャンル
      if(!empty($g_id)){
        $stmt->bindValue(4,$g_id);
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $stmt->bindValue(5,$s_id);
        }else{
          $stmt->bindValue(4,$s_id);
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            $stmt->bindValue(6,$year);
          }else{
            $stmt->bindValue(5,$year);
          }
        }else{
          if(!empty($s_id)){
            $stmt->bindValue(5,$year);
          }else{
            $stmt->bindValue(4,$year);
          }
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 6,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 3,$search_words[($i - 1)]);
              }
            }
          }
        }
      }
      //カテゴリー
      if(!empty($category)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 6,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 8,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 6,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 8,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 3,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 3,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 5,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }

      }else{
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 8,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(8,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(7,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(6,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 5,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(5,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }
      }

      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('コンテンツを取得できない');
    }
  }

  //コメント取得関数
  function getSearchComment($words,$words_logic_flag,$reported,$delete_appear,$flow_flag,$logic_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //コメントの総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM comment WHERE delete_flag = ?';
      //各条件
      if(!empty($words) || !empty($reported)){
        $sql .= ' AND (';
      }

      //通報されたコメント
      if(!empty($reported)){
        $sql .= 'reported_num >= ?';
      }
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = 'text COLLATE utf8_unicode_ci LIKE ?';//特定のカラムから探す
          $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : 'text COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : 'text COLLATE utf8_unicode_ci LIKE ?';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        if(!empty($reported)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;

        }else{
          $sql .= $words_condition;
        }
      }

      if(!empty($words) || !empty($reported)){
        $sql .= ')';
      }

      $sql .= (empty($flow_flag)) ? ' ORDER BY create_date DESC' : ' ORDER BY create_date ASC';

      $stmt = $dbh->prepare($sql);

      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //通報されたコメント
      if(!empty($reported)){
        $stmt->bindValue(2,1);
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($reported)){
          for($i=1; $i<=count($words); $i++){
            $stmt->bindValue($i + 2,$search_words[($i - 1)]);
          }
        }else{
          for($i=1; $i<=count($words); $i++){
            $stmt->bindValue($i + 1,$search_words[($i - 1)]);
          }
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //コメントを取得する
      $sql = 'SELECT com_id,b_id,u_id,text,reported_num,create_date,delete_flag FROM comment WHERE delete_flag = ?';

      //各条件
      if(!empty($words) || !empty($reported)){
        $sql .= ' AND (';
      }

      //通報されたコメント
      if(!empty($reported)){
        $sql .= 'reported_num >= ?';
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($reported)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;
  
        }else{
          $sql .= $words_condition;
        }
      }

      if(!empty($words) || !empty($reported)){
        $sql .= ')';
      }
      $sql .= (empty($flow_flag)) ? ' ORDER BY create_date DESC' : ' ORDER BY create_date ASC';

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);

      //削除済みを非表示/表示
      if(empty($delete_appear)){
        $stmt->bindValue(1,0);
      }else{
        $stmt->bindValue(1,1);
      }
      //通報された回数
      if(!empty($reported)){
        $stmt->bindValue(2,1);
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($reported)){
          for($i=1; $i<=count($words); $i++){
            $stmt->bindValue($i + 2,$search_words[($i - 1)]);
          }
          $stmt->bindValue(count($words) + 3,$record_span,PDO::PARAM_INT);
          $stmt->bindValue(count($words) + 4,$current_min_record,PDO::PARAM_INT);
        }else{
          for($i=1; $i<=count($words); $i++){
            $stmt->bindValue($i + 1,$search_words[($i - 1)]);
          }
          $stmt->bindValue(count($words) + 2,$record_span,PDO::PARAM_INT);
          $stmt->bindValue(count($words) + 3,$current_min_record,PDO::PARAM_INT);
        }

      }else{
        if(!empty($reported)){
          $stmt->bindValue(3,$record_span,PDO::PARAM_INT);
          $stmt->bindValue(4,$current_min_record,PDO::PARAM_INT);
        }else{
          $stmt->bindValue(2,$record_span,PDO::PARAM_INT);
          $stmt->bindValue(3,$current_min_record,PDO::PARAM_INT);
        }
      }

      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('コメントを取得できない');
    }
  }

  //コンテンツ取得(検索)
  function getSearchFavorite($u_id,$words,$words_logic_flag,$g_id,$category,$category_flag,$s_id,$year,$delete_appear,$logic_flag,$flow_flag,$current_page,$record_span){
    $current_min_record = ($current_page - 1) * $record_span;
    $record = array();
    try{
      //コンテンツの総数を取得する
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM favorite AS f LEFT JOIN book AS b ON f.b_id = b.b_id WHERE b.delete_flag = 0 AND u_id = ? AND (release_date IS ? OR release_date <= ?)';
      //各条件
      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ' AND (';
      }

      //ジャンル
      if(!empty($g_id)){
        $sql .= 'g_id = ?';
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $sql .= (empty($logic_flag)) ? ' AND s_id = ?' : ' OR s_id = ?';

        }else{
          $sql .= 's_id = ?';
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id) || !empty($s_id)){
          $sql .= (empty($logic_flag)) ? ' AND year = ?' : ' OR year = ?';

        }else{
          $sql .= 'year = ?';
        }
      }
      //フリーワード
      if(!empty($words)){
        foreach($words as $val){
          $words_condition[] = '(content_title COLLATE utf8_unicode_ci LIKE ? OR author COLLATE utf8_unicode_ci LIKE ? OR text COLLATE utf8_unicode_ci LIKE ? OR catchphrase COLLATE utf8_unicode_ci LIKE ?)';//特定のカラムから探す
          for($i=1;$i<=4;$i++){
            $search_words[] = '%'.preg_replace('/(?=[!_%])/','',$val).'%';
          }
        }
        if(empty($words_logic_flag)){
          $words_condition = (count($words) > 1) ? '('.implode(' AND ',$words_condition).')' : '(content_title COLLATE utf8_unicode_ci LIKE ? OR author COLLATE utf8_unicode_ci LIKE ? OR text COLLATE utf8_unicode_ci LIKE ? OR catchphrase COLLATE utf8_unicode_ci LIKE ?)';//語句が1つのときと複数のとき 特定のカラムから探す
        }else{
          $words_condition = (count($words) > 1) ? '('.implode(' OR ',$words_condition).')' : '(content_title COLLATE utf8_unicode_ci LIKE ? OR author COLLATE utf8_unicode_ci LIKE ? OR text COLLATE utf8_unicode_ci LIKE ? OR catchphrase COLLATE utf8_unicode_ci LIKE ?)';//語句が1つのときと複数のとき 特定のカラムから探す
        }

        if(!empty($g_id) || !empty($s_id) || !empty($year)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;

        }else{
          $sql .= $words_condition;
        }
      }
      //カテゴリー
      if(!empty($category)){
        foreach($category as $val){
          $category_condition[] = '(c_id1 = ? OR c_id2 = ? OR c_id3 = ? OR c_id4 = ? OR c_id5 = ?)';//特定のカラムから探す
          for($i=1;$i<=5;$i++){
            $search_category[] = $val;
          }
        }
        if(empty($category_flag)){
          $category_condition = (count($category) > 1) ? '('.implode(' AND ',$category_condition).')' : '(c_id1 = ? OR c_id2 = ? OR c_id3 = ? OR c_id4 = ? OR c_id5 = ?)';//カテゴリーが1つのときと複数のとき 特定のカラムから探す
        }else{
          $category_condition = (count($category) > 1) ? '('.implode(' OR ',$category_condition).')' : '(c_id1 = ? OR c_id2 = ? OR c_id3 = ? OR c_id4 = ? OR c_id5 = ?)';//カテゴリーが1つのときと複数のとき 特定のカラムから探す
        }

        if(!empty($g_id) || !empty($s_id) || !empty($year) || !empty($words)){
          $sql .= (empty($logic_flag)) ? ' AND '.$category_condition : ' OR '.$category_condition;

        }else{
          $sql .= $category_condition;
        }
      }

      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ')';
      }

      //並び順
      switch($flow_flag){
        case 0:
          $sql .= ' ORDER BY f_id DESC';
          break;
        case 1:
          $sql .= ' ORDER BY average_rate DESC';
          break;
        case 2:
          $sql .= ' ORDER BY fav_num DESC';
          break;
        case 3:
          $sql .= ' ORDER BY com_num DESC';
          break;
        case 4:
          $sql .= ' ORDER BY visited DESC';
          break;
        case 5:
          $sql .= ' ORDER BY year DESC';
          break;
        case 6:
          $sql .= ' ORDER BY year ASC';
          break;
        case 7:
          $sql .= ' ORDER BY content_title ASC';
          break;
        case 8:
          $sql .= ' ORDER BY content_title DESC';
          break;
        default:
          break;
      }

      $stmt = $dbh->prepare($sql);


      $stmt->bindValue(1,$u_id);

      //公開日時
      $stmt->bindValue(2,NULL);
      $stmt->bindValue(3,date('Y-m-d H:i:s'));
      //ジャンル
      if(!empty($g_id)){
        $stmt->bindValue(4,$g_id);
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $stmt->bindValue(5,$s_id);
        }else{
          $stmt->bindValue(4,$s_id);
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            $stmt->bindValue(6,$year);
          }else{
            $stmt->bindValue(5,$year);
          }
        }else{
          if(!empty($s_id)){
            $stmt->bindValue(5,$year);
          }else{
            $stmt->bindValue(4,$year);
          }
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 6,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 3,$search_words[($i - 1)]);
              }
            }
          }
        }
      }
      //カテゴリー
      if(!empty($category)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 6,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 6,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 3,$search_category[($i - 1)]);
                }
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 3,$search_category[($i - 1)]);
                }
              }
            }
          }
        }
      }

      $stmt->execute();
      
      if($stmt){
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
          $record['total_record'] = array_shift($result);
          $record['total_page'] = ceil($record['total_record'] / $record_span);
        }

      }else{
        return false;
      }

      //コンテンツを取得する
      $sql = 'SELECT b.b_id,content_title,com_off,pic1,visited,average_rate,com_num,fav_num,b.create_date,b.update_date,f_id,f.create_date AS register_date FROM favorite AS f LEFT JOIN book AS b ON f.b_id = b.b_id WHERE b.delete_flag = 0 AND u_id = ? AND (release_date IS ? OR release_date <= ?)';

      //各条件
      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ' AND (';
      }

      //ジャンル
      if(!empty($g_id)){
        $sql .= 'g_id = ?';
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $sql .= (empty($logic_flag)) ? ' AND s_id = ?' : ' OR s_id = ?';

        }else{
          $sql .= 's_id = ?';
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id) || !empty($s_id)){
          $sql .= (empty($logic_flag)) ? ' AND year = ?' : ' OR year = ?';

        }else{
          $sql .= 'year = ?';
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($g_id) || !empty($s_id) || !empty($year)){
          $sql .= (empty($logic_flag)) ? ' AND '.$words_condition : ' OR '.$words_condition;
  
        }else{
          $sql .= $words_condition;
        }
      }
      //カテゴリー
      if(!empty($category)){
        if(!empty($g_id) || !empty($s_id) || !empty($year) || !empty($words)){
          $sql .= (empty($logic_flag)) ? ' AND '.$category_condition : ' OR '.$category_condition;
  
        }else{
          $sql .= $category_condition;
        }
      }

      if(!empty($words) || !empty($g_id) || !empty($category) || !empty($s_id) || !empty($year)){
        $sql .= ')';
      }

      //並び順
      switch($flow_flag){
        case 0:
          $sql .= ' ORDER BY f_id DESC';
          break;
        case 1:
          $sql .= ' ORDER BY average_rate DESC';
          break;
        case 2:
          $sql .= ' ORDER BY fav_num DESC';
          break;
        case 3:
          $sql .= ' ORDER BY com_num DESC';
          break;
        case 4:
          $sql .= ' ORDER BY visited DESC';
          break;
        case 5:
          $sql .= ' ORDER BY year DESC';
          break;
        case 6:
          $sql .= ' ORDER BY year ASC';
          break;
        case 7:
          $sql .= ' ORDER BY content_title ASC';
          break;
        case 8:
          $sql .= ' ORDER BY content_title DESC';
          break;
        default:
          break;
      }

      $sql .= ' LIMIT ? OFFSET ?';

      $stmt = $dbh->prepare($sql);


      $stmt->bindValue(1,$u_id);
      //公開日時
      $stmt->bindValue(2,NULL);
      $stmt->bindValue(3,date('Y-m-d H:i:s'));
      //ジャンル
      if(!empty($g_id)){
        $stmt->bindValue(4,$g_id);
      }
      //出版社
      if(!empty($s_id)){
        if(!empty($g_id)){
          $stmt->bindValue(5,$s_id);
        }else{
          $stmt->bindValue(4,$s_id);
        }
      }
      //発売年
      if(!empty($year)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            $stmt->bindValue(6,$year);
          }else{
            $stmt->bindValue(5,$year);
          }
        }else{
          if(!empty($s_id)){
            $stmt->bindValue(5,$year);
          }else{
            $stmt->bindValue(4,$year);
          }
        }
      }
      //フリーワード
      if(!empty($words)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 6,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 5,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }
          }else{
            if(!empty($year)){
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 4,$search_words[($i - 1)]);
              }
            }else{
              for($i=1; $i<=count($search_words); $i++){
                $stmt->bindValue($i + 3,$search_words[($i - 1)]);
              }
            }
          }
        }
      }
      //カテゴリー
      if(!empty($category)){
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 6,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 8,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 6,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 8,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 5,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 4,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 6,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + count($search_words) + 3,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_words) + count($search_category) + 4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + count($search_category) + 5,$current_min_record,PDO::PARAM_INT);
              }else{
                for($i=1; $i<=count($search_category); $i++){
                  $stmt->bindValue($i + 3,$search_category[($i - 1)]);
                }
                $stmt->bindValue(count($search_category) + 4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_category) + 5,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }

      }else{
        if(!empty($g_id)){
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 8,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(7,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(8,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(7,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }else{
          if(!empty($s_id)){
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 7,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(6,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(7,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(6,$current_min_record,PDO::PARAM_INT);
              }
            }
          }else{
            if(!empty($year)){
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 6,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(5,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(6,$current_min_record,PDO::PARAM_INT);
              }
            }else{
              if(!empty($words)){
                $stmt->bindValue(count($search_words) + 4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(count($search_words) + 5,$current_min_record,PDO::PARAM_INT);
              }else{
                $stmt->bindValue(4,$record_span,PDO::PARAM_INT);
                $stmt->bindValue(5,$current_min_record,PDO::PARAM_INT);
              }
            }
          }
        }
      }

      $stmt->execute();
      if($stmt){
        $record['data'] = $stmt->fetchAll();
        return $record;

      }else{
        return false;
      }

    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
      debug('コンテンツを取得できない');
    }
  }

