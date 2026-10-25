<?php
session_start();
?>
<html>
<head>
<title>Projet Databases IF3E</title>
<link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
<h1>Welcome back !</h1>
<h2>Nice to see you !</h2>
<?php
if (isset($_SESSION['email'])) {
    $email = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
    echo "<p>Hello $email !</p>";
    echo '<br />';
    echo '<a href="signout.php"><button type="button">Sign out</button></a>';
}
else {
    echo "Error ! Please login.";
    echo '<a href="login.php">Back to login</a>';
}
?>
</body>
</html>