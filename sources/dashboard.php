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
if (isset($_SESSION['id'])) {
    try {
    $conn = new PDO('sqlite:' . __DIR__ . '/databases/main_database.sq3');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion impossible : " . $e->getMessage());
}
try {
    $sql = "SELECT firstname, account_status FROM users WHERE id = :id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
    $firstname = $user['firstname'];
    echo "<p>Hello $firstname !</p>";
    echo '<br />';
    echo '<div id="navigation">';
    echo '<ul id="navigation">';
    echo '<li><a href="#" title="My cars">My cars</a></li>';
    echo '<li><a href="#" title="My trips">My trips</a></li>';
    echo '<li><a href="#" title="Search a trip">Search a trip</a></li>';
    if ($user['account_status'] == 1) {
    echo '<li><a href="#" title="admin">Admin</a></li>';
    }
    echo '</ul>';
    echo '<a href="signout.php"><button type="button">Sign out</button></a>';
    echo '</div>';
}
else {
    echo "Error ! Please login.";
    echo '<a href="login.php">Back to login</a>';
}
?>
</body>
</html>