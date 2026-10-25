<html>
<head>
<title>Projet Databases IF3E</title>
<link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
<h1>Welcome back ! It's been a while !</h1>
<div class="formular">
<form action="check_login.php" method="post">
  <label for="email">E-mail : </label><br>
  <input type="text" id="email" name="email"><br>
  <label for="password">Password : </label><br>
  <input type="text" id="password" name="password">
<br />
<br />
<input type="submit" value="Connect"/>
</form>
</div>
<a href="index.php"><button>Home</button></a>
<script>
<?php if (!empty($message)): ?>
alert("<?= addslashes($message) ?>");
<?php endif; ?>
</script>
</body>
</html>
