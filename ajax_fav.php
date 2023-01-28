<?php
  require('function.php');
  $title = 'お気に入り登録・削除';
  debugStart();

  if(!empty($_POST) && is_Login()){
    $b_id = $_POST['b_id'];
    $u_id = $_SESSION['u_id'];

    try{
      $fav_flag = is_Favorite($u_id,$b_id);
      $fav_num = getFavNum($b_id);

      if(empty($fav_flag)){
        //お気に入り登録
        $dbh = dbConnect();
        $sql = 'INSERT INTO favorite (u_id,b_id,create_date) VALUES (:u_id,:b_id,:create_date)';
        $data = array(
          ':u_id' => $u_id,
          ':b_id' => $b_id,
          ':create_date' => date('Y-m-d H:i:s')
        );
  
        $stmt = queryPost($dbh,$sql,$data);

        if(is_numeric($fav_num)){
          $sql = 'UPDATE book SET fav_num = :fav_num WHERE b_id = :b_id AND delete_flag = 0';
          $data = array(
            ':fav_num' => $fav_num + 1,
            ':b_id' => $b_id
          );
          $stmt = queryPost($dbh,$sql,$data);
        }

        if($stmt){
          $fav_num = getFavNum($b_id);
          $fav_num = (is_numeric($fav_num)) ? $fav_num : 'たくさん!';
          echo $fav_num;
          debug('お気に入り登録完了');
        }

      }else{
        //お気に入り削除
        $dbh = dbConnect();
        $sql = 'DELETE FROM favorite WHERE u_id = :u_id AND b_id = :b_id';
        $data = array(
          ':u_id' => $u_id,
          ':b_id' => $b_id,
        );
  
        $stmt = queryPost($dbh,$sql,$data);

        if(is_numeric($fav_num)){
          $sql = 'UPDATE book SET fav_num = :fav_num WHERE b_id = :b_id AND delete_flag = 0';
          $data = array(
            ':fav_num' => $fav_num - 1,
            ':b_id' => $b_id
          );
          $stmt = queryPost($dbh,$sql,$data);
        }

        if($stmt){
          $fav_num = getFavNum($b_id);
          $fav_num = (is_numeric($fav_num)) ? $fav_num : 'たくさん!';
          echo $fav_num;
          debug('お気に入り削除完了');
        }
      }



    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
    }
    
  }else{
    header('Location:index.php');
    exit();
  }