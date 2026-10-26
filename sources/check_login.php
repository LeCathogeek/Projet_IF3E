<?php
session_start();

try {
    require __DIR__ . '/db.php';
    $conn = getDb();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: login.php');
        exit;
    }

    $email = trim($_POST['email'] ?? '');
    $passwordSubmitted = $_POST['password'] ?? '';

    if ($email === '' || $passwordSubmitted === '') {
        $_SESSION['flash_error'] = 'Veuillez remplir votre email et votre mot de passe.';
        header('Location: login.php');
        exit;
    }

    $sql = 'SELECT id, password FROM users WHERE email = :email LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($passwordSubmitted, $user['password'])) {
        $_SESSION['id'] = (int) $user['id'];
        header('Location: dashboard.php');
        exit;
    }

    $_SESSION['flash_error'] = 'Mot de passe incorrect.';
    header('Location: login.php');
    exit;
} catch (PDOException $e) {
    die('Erreur SQL : ' . $e->getMessage());
}
