<?php
require('function.php');
require('auth.php');
dbg('##チャット画面##');



if(!empty($_POST)){
dbg('POST送信あり');
dbg2($_POST);
//dbg2($_SESSION);

$comDupFlag = isComDup($_POST['comment']);

  if( !empty($_POST['comment']) && $comDupFlag ){
    dbg('前回のコメントと同じなので何も行わない');
  }else{

    $comment = $_POST['comment'];

    validEmpty($comment,'comment');
    validMaxLen($comment,'comment');
    if(empty($err_msg)){
      try{
        $dbh = dbConnect();
        $sql = 'INSERT INTO message (msg,user_id,create_date) VALUES (:msg,:user_id,:create_date)';
        $data = array(':msg'=>$comment,':user_id'=>$_SESSION['user_id'],':create_date'=>date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh,$sql,$data);

      }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        global $err_msg;
        $err_msg['common'] = MSG0;
      }
    }else{
      dbg('コメントが空か２５５より多い');
    }
  }
}

?>


<?php
$title = 'チャット';
require('head.php');
require('header.php');
 ?>

<?php
$log = getLog($_SESSION['user_id']);
$search_word = (!empty($_GET['search'])) ? $_GET['search'] : '';
$log_search = searchLog($search_word);

$userId = $_SESSION['user_id'];
$userName = getUserData($userId)['name'];
?>


    <main id="main" class="site-width">
      <div id="contents"><!--contentsに relative -->
        <a class="logmode-bt">ログモード</a>
        <form class="" action="" method="get">
          <input type="submit" name="" value="検索" class="search-bt">
          <input type="text" name="search" class="search-word">
        </form>
        <div class="search-log <?php if(!empty($log_search)) echo 'active';?>">
          <?php
          if(!empty($log_search)){
           foreach ($log_search as $key => $val) {
             if($val['user_id'] === $userId){
               echo $userName .'(ID:'.$val['user_id'] .') :  '. $val['msg'];
          ?><br>
        <?php }}}?>
        </div>

        <div class="logmode-log">
          <?php
          if(!empty($log)){
            foreach ($log as $key => $val) { ?>
            <?php echo $userName .'(ID:'.$val['user_id'] .') :  '. $val['msg'];?><br>
          <?php }} ?>
        </div>
        <a href="" class="player-num-id">6人(ID:<?php echo $userId;?>)</a>

        <!-- コメント一覧表示-->
        <div class="com-block">
        <?php $dbComments = getComments($_SESSION['user_id']);
          foreach ($dbComments as $key => $val) {?>
            <div class="com-container"><span class="comments"><?php echo $val['msg'];?></span><br></div>
          <?php } ?>
        </div>

        <!-- コメント一覧表示-->

        <div class="mona"><img src="images/mona.png"></div>
        <span class="username"><?php echo $userName;?></span>

        <div class="under-bt">
          <a href="" class="unser-left-bt sound"><span class="sd">音</span></a>
          <a href="" class="unser-left-bt quality">品質</a>
          <a href="" class="unser-left-bt effect">効果</a>
          <a href="" class="unser-left-bt trans">透明</a>
          <a href="" class="unser-left-bt time">時間</a>
          <form class="under-left-bt2">
            <a href="" class="unser-left-bt ignore">無視</a>
            <a href="" class="unser-left-bt state">状態</a>
            <a href="" class="unser-left-bt delete">全消</a>
          </form>
          <div class="form-wrap">
            <form action="" method="post" class="form">
              <input type="text" name="comment" class="form-com com">
              <input type="submit" name="submit" value="OK" class="form-com ok">
            </form>
          </div>
        </div>

      </div>
    </main>

<?php require('footer.php');?>
