<?php
require('function.php');
dbg('##ログアウトします##');



session_unset();

$_SESSION['success-msg'] = 'ログアウトしました';

header("Location:login.php");

dbg('ログアウトしました');
?>
