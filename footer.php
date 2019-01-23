    <footer>
    </footer>
    <?php dbg('----- 終了 ----'."\n");?>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
      $(function(){
        var $jsSuccessMsg = $('.js-success-msg');
        var msg = $jsSuccessMsg.text();
        if(msg){
          $jsSuccessMsg.slideToggle('slow');
          setTimeout(function(){ $jsSuccessMsg.slideToggle('slow');},4000);
        }

        var $logmode = $('.logmode-bt'),
            $log = $('.logmode-log');

        var $search_bt = $('.search-bt'),
            $search_word = $('.search-word'),
            $search_log = $('.search-log');

        $logmode.on('click',function(){
          $log.slideToggle('slow');
        });

        $search_log.on('click',function(){
          $(this).slideToggle('slow');
        });


      });

    </script>
  </body>
</html>
