<html>
<head>
<title>Log In</title>

<?php
require_once('commonheadcontents.php')
?>

</head>


<body>
  <div class="login">
    <h1>Вход в систему</h1>
    <form method="post" action="logincheck.php<?php
		if(isset($_GET['location']))
			echo '?location='.$_GET['location'];
	  ?>">
      <p><input type="text" name="login" value="" placeholder="адрес эл.почты"></p>
      <p><input type="password" name="password" value="" placeholder="пароль"></p>
      <p class="remember_me">
        <label>
          <input type="checkbox" name="remember_me" id="remember_me">
          Оставаться в системе
        </label>        
      </p>
      <p class="submit"><input type="submit" name="commit" value="Войти"></p>
      <?php
	  session_start();
  	  if(isset($_SESSION['loginerror']))
	  {
	  	echo '<p class = "loginerror">';
	  	echo $_SESSION['loginerror'];
	  	echo '</p>';
	  }	 
     ?>
    </form>
  </div>
 
  <div class="login-help">
      <p>Забыли пароль? <a href="#">Нажмите сюда, чтобы сгенерировать новый</a>.</p>
  </div>
</body>
</html>