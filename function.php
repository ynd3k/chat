<?php

ini_set('log_errors','on');
ini_set('error_log','php.log');

define('MSG01','入力必須です');
define('MSG02','255文字以内で入力してください');
define('MSG03','Emailの形式で入力してください');

define('MSG0','エラーが発生しました');

define('MSG05','すでに登録されているEmailです');
define('MSG06','パスワードは6文字以上で入力してください');
define('MSG07','パスワードは半角で入力してください');
define('MSG08','パスワードとパスワード(再入力)が一致しません');

//セッション
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_lifetime',60*60*24*30);

session_start();
session_regenerate_id();

dbg2($_SESSION);


function dbg($str){
  error_log(''.$str);
}

function dbg2($array){
  error_log(print_r($array,true));
}

function validEmpty($str,$key){
  global $err_msg;
  if(empty($str)){
    $err_msg[$key] = MSG01;
  }
}

function validMaxLen($str,$key,$length = 255){
  global $err_msg;
  if(mb_strlen($str) > $length){
    $err_msg[$key] = MSG02;
  }
}

function validMinLen($str,$key,$length = 6){
  global $err_msg;
  if(mb_strlen($str) < $length){
    $err_msg[$key] = MSG06;
  }
}

function validEmail($str, $key){
  global $err_msg;
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    $err_msg[$key] = MSG03;
  }
}

function validHalf($str,$key){
  global $err_msg;
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    $err_msg[$key] = MSG07;
  }
}

function validMatch($str1,$str2,$key){
  global $err_msg;
  if($str1 !== $str2){
    $err_msg[$key] = MSG08;
  }
}

function validEmailDup($email,$key){
  try{
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE email=:email AND delete_flg=0';
    $data = array(':email'=>$email);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty(array_shift($result))){
      dbg('email重複しています');
      global $err_msg;
      $err_msg[$key] = MSG05;
    }else{
      dbg('email　ok');
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG0;
  }
}

function queryPost($dbh,$sql,$data){
  $stmt = $dbh->prepare($sql);

  if(!$stmt->execute($data)){
    dbh('クエリ失敗です');
    global $err_msg;
    $err_msg['common'] = MSG0;
    return 0;
  }else{
    dbg('クエリ成功です');
    return $stmt;
  }
}

function dbConnect(){
  $dsn = 'mysql:dbname=chat;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  return new PDO($dsn,$user,$password,$options);
}
function showErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return ' エラー: '.$err_msg[$key];
  }
}

function inputHold($key){
  if(!empty($_POST[$key])){
    return $_POST[$key];
  }
}

function getSessionFlash(){

  if(!empty($_SESSION['success-msg'])){
    $data = $_SESSION['success-msg'];
    $_SESSION['success-msg'] = '';
    return $data;
  }
}

function getComments($user_id){
  try{
    $dbh = dbConnect();
    $sql = 'SELECT msg FROM message WHERE user_id=:user_id';
    $data = array(':user_id'=>$user_id);
    $stmt = queryPost($dbh,$sql,$data);
    $recode_count = $stmt->rowCount();

    if($recode_count > 8 && $recode_count > 1){
      $offset = $recode_count - 8;
    }else{
      $offset = 0;
    }

    $sql .= ' ORDER BY create_date ASC LIMIT 8 OFFSET '.$offset;
    $data = array(':user_id'=>$user_id);
    $stmt = queryPost($dbh,$sql,$data);
    if($stmt){
      return $stmt->fetchAll();
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG0;
  }
}

function getLog($user_id){
  try{
    $dbh = dbConnect();
    $sql = 'SELECT msg,user_id FROM message WHERE user_id=:user_id';
    $data = array(':user_id'=>$user_id);
    $stmt = queryPost($dbh,$sql,$data);
    $recode_count = $stmt->rowCount();

    if($stmt && $recode_count > 1) return $stmt->fetchAll();

  }catch(Eception $e){
    error_log('エラー発生：'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG0;
  }
}

function getUserData($user_id){
  try{
    $dbh = dbConnect();
    $sql = 'SELECT id,name FROM users WHERE id=:id';
    $data = array(':id'=>$user_id);
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG0;
  }
}
//前回と同じコメントを送信しないように
function isComDup($msg){
  try{
    $dbh = dbConnect();
    $sql = 'SELECT msg FROM message ORDER BY id DESC LIMIT 1';
    $data = array(':msg'=>$msg);
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $last_msg = array_shift($result);
      dbg('DBの最新メッセージ:'.$last_msg);

      return ($last_msg === $msg) ? true : false ;
    }

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG0;
  }
}

function searchLog($search_word){
  try{
    $dbh = dbConnect();
    $sql = 'SELECT * FROM message WHERE msg = :msg';
    $data = array(':msg'=>$search_word);
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt) return $stmt->fetchAll();

  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    global $err_msg;
    $err_msg['common'] = MSG0;
  }
}
?>
