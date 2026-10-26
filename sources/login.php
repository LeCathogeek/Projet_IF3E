<?php
session_start();

require __DIR__ . '/db.php';
$pdo = getDb();

$message = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
?>
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
  <input type="email" id="email" name="email" required><br>
  <label for="password">Password : </label><br>
  <input type="password" id="password" name="password" required>
  <br />
  <br />
  <input type="submit" value="Connect"/>
</form>
</div>
<a href="index.php"><button>Home</button></a>
<script>
<?php if ($message !== ''): ?>
alert("<?= htmlspecialchars(addslashes($message), ENT_QUOTES, 'UTF-8') ?>");
<?php endif; ?>
</script>
</body>
</html>
