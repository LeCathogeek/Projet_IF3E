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

$stmt = $db->prepare('SELECT COUNT(*) AS total_users FROM users WHERE account_status = 1');
$stmt->execute();
$totalUsers = (int) $stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) AS total_trips FROM trips');
$stmt->execute();
$totalTrips = (int) $stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) AS total_bookings FROM bookings');
$stmt->execute();
$totalBookings = (int) $stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) AS total_reviews FROM reviews');
$stmt->execute();
$totalReviews = (int) $stmt->fetchColumn();

$stmt = $db->prepare('SELECT AVG(rating) AS average_rating FROM reviews');
$stmt->execute();
$averageRating = (float) ($stmt->fetchColumn() ?? 0);

$stmt = $db->prepare('SELECT SUM(total_price) AS total_amount FROM bookings WHERE status = "Confirmed"');
$stmt->execute();
$totalAmount = (float) ($stmt->fetchColumn() ?? 0);

$stmt = $db->prepare('SELECT COUNT(*) AS open_trips FROM trips WHERE status = "Open"');
$stmt->execute();
$openTrips = (int) $stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) AS pending_payments FROM bookings WHERE status = "Pending"');
$stmt->execute();
$pendingPayments = (int) $stmt->fetchColumn();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin stats</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Admin stats</h1>

    <?php
    require __DIR__ . '/basic_functions.php';
    echoMenu();
    ?>

    <h3>Textual stats</h3>
    <ul>
        <li>Active users: <?= (int) $totalUsers ?></li>
        <li>Total trips: <?= (int) $totalTrips ?></li>
        <li>Total bookings: <?= (int) $totalBookings ?></li>
        <li>Total reviews: <?= (int) $totalReviews ?></li>
        <li>Average review score: <?= number_format($averageRating, 2, ',', ' ') ?> / 5</li>
        <li>Open trips: <?= (int) $openTrips ?></li>
        <li>Pending payments: <?= (int) $pendingPayments ?></li>
        <li>Total confirmed revenue: <?= number_format($totalAmount, 2, ',', ' ') ?> €</li>
    </ul>
</body>
</html>
