<?php
session_start();

try {
    $conn = new PDO('sqlite:' . __DIR__ . '/databases/main_database.sq3');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion impossible : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email = trim($_POST['email'] ?? '');
$passwordSubmitted = $_POST['password'] ?? '';

if ($email === '' || $passwordSubmitted === '') {
    header("Location: login.php");
    exit;
}

try {
    $sql = "SELECT email, password FROM users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwordSubmitted, $user['password'])) {
        $_SESSION['email'] = $user['email'];
        header("Location: dashboard.php");
        exit;
    }

    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>
<html lang="en">
<head>
<title>TD7</title>
<link rel="stylesheet" href="../styles/styles.css" />
</head>
<body>
</body>
</html>