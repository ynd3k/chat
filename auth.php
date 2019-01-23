<?php

dbg('##ログイン認証##');

if(!empty($_SESSION['user_id'])){
  if( ($_SESSION['login_date'] + $_SESSION['login_limit']) > time() ){
    dbg('セッション有効期限内です');
    $_SESSION['login_date'] = time();

    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      header("Location:index.php");
    }

  }else{
    dbg('セッションが切れてます');
    session_destroy();
    header("Location:login.php");
  }

}else{
  dbg('セッションがないので未ログインです');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
    header("Location:login.php");
  }
}
 ?>
