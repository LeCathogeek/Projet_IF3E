<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/basic_functions.php';
$db = getDb();

if ((int) ($_SESSION['account_status'] ?? 0) !== 1) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    $isAdmin = (int) ($_POST['account_status'] ?? 0);

    if ($userId <= 0) {
        $_SESSION['flash_error'] = 'Invalid user.';
        header('Location: admin_users.php');
        exit;
    }

    if ((int) $_SESSION['id'] === $userId) {
        $_SESSION['flash_error'] = 'You cannot change your own admin role.';
        header('Location: admin_users.php');
        exit;
    }

    $stmt = $db->prepare('UPDATE users SET account_status = :account_status WHERE id = :user_id');
    $stmt->execute([
        ':account_status' => $isAdmin,
        ':user_id' => $userId,
    ]);

    $_SESSION['flash_success'] = 'User role updated.';
    header('Location: admin_users.php');
    exit;
}

$stmt = $db->prepare('SELECT id, firstname, name, email, account_status FROM users ORDER BY firstname, name');
$stmt->execute();
$users = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin users</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Admin users</h1>

    <?php
    echoMenu();
    ?>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= (int) $user['id'] ?></td>
                <td><?= htmlspecialchars($user['firstname']) ?> <?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= ((int) $user['account_status'] === 1) ? 'Admin' : 'User' ?></td>
                <td>
                    <?php if ((int) $user['id'] !== (int) $_SESSION['id']): ?>
                        <form method="post" action="admin_users.php">
                            <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                            <input type="hidden" name="account_status" value="<?= ((int) $user['account_status'] === 1) ? 0 : 1 ?>">
                            <button type="submit"><?= ((int) $user['account_status'] === 1) ? 'Remove admin' : 'Make admin' ?></button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
