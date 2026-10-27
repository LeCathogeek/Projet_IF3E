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
        $_SESSION['flash_error'] = 'Please fill your email adress and your password.';
        header('Location: login.php');
        exit;
    }

    $sql = 'SELECT id, password, is_admin FROM users WHERE email = :email LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($passwordSubmitted, $user['password'])) {
        $_SESSION['id'] = (int) $user['id'];
        $_SESSION['is_admin'] = (int) ($user['is_admin'] ?? 0);
        header('Location: dashboard.php');
        exit;
    }

    $_SESSION['flash_error'] = 'Wrong password.';
    header('Location: login.php');
    exit;
} catch (PDOException $e) {
    die('Erreur SQL : ' . $e->getMessage());
}
