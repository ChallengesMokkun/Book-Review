<?php
  require('function.php');
  $title = 'いいねボタン';
  debugStart();

  if(!empty($_POST)){
    try{
      $com_id = $_POST['com_id'];
      $good_flag = $_POST['good_flag'];
      $good_num = getGoodNum($com_id);
  
      if(is_numeric($good_num)){
          $dbh = dbConnect();
  
        if(empty($good_flag)){
          //いいね+1
          $sql = 'UPDATE comment SET good_num = :good_num WHERE com_id = :com_id AND delete_flag = 0';
          $data = array(
            ':good_num' => $good_num + 1,
            ':com_id' => $com_id
          );
          $stmt = queryPost($dbh,$sql,$data);
    
          if($stmt){
            $good_num = getGoodNum($com_id);
            $good_num = (is_numeric($good_num)) ? $good_num : 'たくさん!';
            echo $good_num;
            debug('いいねの数更新+完了');
          }
    
        }else{
          //いいね-1
          $sql = 'UPDATE comment SET good_num = :good_num WHERE com_id = :com_id AND delete_flag = 0';
          $data = array(
            ':good_num' => $good_num - 1,
            ':com_id' => $com_id
          );
          $stmt = queryPost($dbh,$sql,$data);
  
          if($stmt){
            $good_num = getGoodNum($com_id);
            $good_num = (is_numeric($good_num)) ? $good_num : 'たくさん!';
            echo $good_num;
            debug('いいねの数更新-完了');
          }
        }
      }
  
    }catch (Exception $e){
      debug('エラー発生: '.$e->getMessage());
    }

  }else{
    header('Location:index.php');
    exit();
  }
