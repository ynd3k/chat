<?php

require('function.php');
dbg('##ユーザー登録画面##');


if(!empty($_POST)){
  dbg('signup post ok');
  dbg2($_POST);

  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $passRe = $_POST['pass-re'];

  validEmpty($name,'name');
  validEmpty($email,'email');
  validEmpty($pass,'pass');
  validEmpty($passRe,'pass-re');

  if(empty($err_msg)){
    dbg('から入力チェックはok');
    validMaxLen($name,'name');
    validMaxLen($email,'email');
    validMaxLen($pass,'pass');
    validMaxLen($passRe,'pass-re');

    validEmail($email,'email');
    validEmailDup($email,'email');

    validMinLen($pass,'pass');
    validHalf($pass,'pass');
    validMatch($pass,$passRe,'pass-re');

    if(empty($err_msg)){
      dbg('バリデーションおｋです');

      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO users (name,email,pass,create_date) VALUES (:name,:email,:pass,:create_date)';
        $data = array(':name'=>$name,':email'=>$email,':pass'=>password_hash($pass,PASSWORD_DEFAULT),':create_date'=>date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
          dbg('ユーザー登録が完了しました。ページ遷移する');

          $sessionLimit = 60*60;
          $_SESSION['user_id'] = $dbh->lastInsertId();
          $_SESSION['login_date'] = time();
          $_SESSION['login_limit'] = $sessionLimit;
          $_SESSION['success-msg'] = 'ユーザー登録が完了しました。';

          header("Location:index.php");
          exit;
        }
      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        global $err_msg;
        $err_msg['common'] = MSG0;
      }
    }else{
      dbg('aaa');
    }

  }else{
    dbg('!?');
  }


}else{
  dbg('signup post none');
}
?>

<?php
$title = 'ユーザー登録';
require('head.php');
require('header.php');
?>

  <main class="site-width" id="">
    <form class="form-signup" method="post">
      <label>
        名前<span class="err-msg"><?php echo showErrMsg('name');?></span> <br>
        <input type="text" name="name" class="form-input-signup-login" value="<?php echo inputHold('name');?>"><br>
      </label>

      <label>
        Email<span class="err-msg"><?php echo showErrMsg('email');?></span> <br>
        <input type="text" name="email" class="form-input-signup-login" value="<?php echo inputHold('email');?>"><br>
      </label>

      <label>
        パスワード<span class="err-msg"><?php echo showErrMsg('pass');?></span><br>
        <input type="password" name="pass" value="<?php echo inputHold('pass');?>" class="form-input-signup-login"><br>
      </label>
      <label>

        パスワード(再入力)<span class="err-msg"><?php echo showErrMsg('pass-re');?></span><br>
        <input type="password" name="pass-re" value="<?php echo inputHold('pass-re');?>" class="form-input-signup-login"><br>
      </label>
        <input type="submit" value="OK" name="submit" class="ok signup-ok">
  </form>
  </main>

<?php require('footer.php');?>
