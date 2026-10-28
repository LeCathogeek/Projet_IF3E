<?php
session_start();
require __DIR__ . '/basic_functions.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php');
        exit;
    }

    $pdo = getDb();

    $name = trim($_POST['name'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phonenumber = trim($_POST['phonenumber'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $profilepicture = trim($_POST['avatar'] ?? '');

    if ($name === '' || $firstname === '') {
        throw new Exception('Le nom et le prénom sont requis.');
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email adress.');
    }

    if (strlen($password) < 8) {
        throw new Exception('The password must at least contain 8 characters.');
    }

    if ($phonenumber !== '' && !preg_match('/^[0-9 +()\-]{6,20}$/', $phonenumber)) {
        throw new Exception('Invalid phone number.');
    }

    if ($profilepicture !== '' && !filter_var($profilepicture, FILTER_VALIDATE_URL)) {
        throw new Exception('Invalid avatar URL.');
    }

    if ($birthdate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
        throw new Exception('Invalid birthdate.');
    }

    $check = $pdo->prepare('SELECT COUNT(1) FROM users WHERE email = :email');
    $check->execute([':email' => $email]);
    if ((int) $check->fetchColumn() > 0) {
        throw new Exception('An account with this email already exists');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users
        (name, firstname, email, password, phone_number, birthdate, account_creation_date, account_status, profile_picture)
        VALUES
        (:name, :firstname, :email, :password, :phonenumber, :birthdate, :accountcreationdate, :accountstatus, :profilepicture)';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':firstname' => $firstname,
        ':email' => $email,
        ':password' => $passwordHash,
        ':phonenumber' => $phonenumber,
        ':birthdate' => $birthdate,
        ':accountcreationdate' => date('Y-m-d'),
        ':accountstatus' => 0,
        ':profilepicture' => $profilepicture,
    ]);

    header('Location: login.php');
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Error database: ' . htmlspecialchars($e->getMessage());
    exit;
} catch (Exception $e) {
    http_response_code(400);
    echo 'Error : ' . htmlspecialchars($e->getMessage());
    exit;
}
