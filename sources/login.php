<html>
<head>
<title>Projet Databases IF3E</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Welcome back ! It's been a while !</h1>
<div class="formular">
<form>
  <label for="email">E-mail : </label><br>
  <input type="text" id="email" name="email"><br>
  <label for="password">Password : </label><br>
  <input type="text" id="password" name="password">
<br />
<br />
<button>Let's go !</button>
</form>
</div>
    <?php
$db = new SQLite3('databases/main_database.sq3');
$sql = "SELECT * FROM users;";
$result = $db->query($sql);
while ($row = $result->fetchArray(SQLITE3_ASSOC)){
  echo 'L\'e-mail de ' . $row['name'] . ' est ' . $row['email'] . '<br/>';
}
unset($db);
?>
<a href="index.html"><button>Home</button></a>
</body>
</html>
