<?php

require('function.php');
require('auth.php');

dbg('##ログイン画面##');


if(!empty($_POST)){
  dbg2($_POST);
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $login_keep = (!empty($_POST['login-keep'])) ? true : false;

  validEmpty($email,'email');
  validEmpty($pass,'pass');

  if(empty($err_msg)){
    try{
      $dbh = dbConnect();
      $sql = 'SELECT pass,id FROM users WHERE email=:email AND delete_flg=0';
      $data = array(':email'=>$email);
      $stmt = queryPost($dbh,$sql,$data);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      $isMatch = ($result) ? password_verify($pass,array_shift($result)) : false;
      if($stmt && $isMatch){
        dbg('パス一致');

        $sessionLimit = 60*60;
        $_SESSION['user_id'] = array_shift($result);
        $_SESSION['login_date'] = time();
        $_SESSION['login_limit'] = ($login_keep) ? $sessionLimit *24*30 : $sessionLimit;
        $_SESSION['success-msg'] = 'ログインしました';

        header("Location:index.php");
        exit;
      }else{
        dbg('パス一致しない');
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      global $err_msg;
      $err_msg['common'] = MSG0;
    }
  }
}

?>

<?php
$title = 'ログイン画面';
require('head.php');
require('header.php');
?>

  <main class="site-width" id="">
    <form class="form-signup" method="post">
      <label>
        Email<span class="err-msg"><?php echo showErrMsg('email');?></span> <br>
        <input type="text" name="email" class="form-input-signup-login" value="<?php echo inputHold('email');?>"><br>
      </label>

      <label>
        パスワード<span class="err-msg"><?php echo showErrMsg('pass');?></span><br>
        <input type="password" name="pass" value="<?php echo inputHold('pass');?>" class="form-input-signup-login"><br>
      </label>

      <label class="login-keep">
        ログイン保持
        <input type="checkbox" name="login-keep" value="keep">
      </label>

        <input type="submit" value="OK" name="submit" class="ok signup-ok">
  </form>
  </main>

<?php require('footer.php');?>
