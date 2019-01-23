<body>

  <header class="site-width">
    <p class="js-success-msg success-msg site-width"><?php echo getSessionFlash();?></p>


    <nav id="nav">
      <div class="menu menu-left">
        <h1><a href="index.php"><?php echo $title;?></a></h1>
      </div>
      <div class="menu menu-right">
        <a href="index.php">chat</a>
        <li><a href="signup.php">ユーザー登録</a></li>
        <li><a href="login.php">ログイン</a></li>
        <li><a href="logout.php">ログアウト</a></li>
      </div>
    </nav>

  </header>
