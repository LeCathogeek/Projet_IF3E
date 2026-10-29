<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
require __DIR__ . '/basic_functions.php';
$db = getDb();
$userId = (int) $_SESSION['id'];
$stmt = $db->prepare('SELECT firstname, account_status FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$successMessage = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$errorMessage = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$stmt = $db->prepare('SELECT b.id, b.status, b.seats_booked, b.total_price,
        t.id AS trip_id, t.departure_city, t.destination_city, t.departure_date, t.departure_time, t.status AS trip_status,
        p.payment_status, EXISTS (SELECT 1 FROM reviews r WHERE r.trip_id = t.id AND r.passenger_id = :user_id
        ) AS has_reviewed FROM bookings b JOIN trips t ON t.id = b.trip_id LEFT JOIN payments p ON p.booking_id = b.id
    WHERE b.passenger_id = :user_id ORDER BY b.id DESC');
$stmt->execute([':user_id' => $userId]);
$bookings = $stmt->fetchAll();
?>
<html>
<head>
<title>Carpooling project IF3E</title>
<link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
<h1>Welcome back <?= htmlspecialchars($user['firstname']) ?> !</h1>
<h2>Nice to see you !</h2>

<?php
echoMenu();
?>
<?php if ($successMessage !== ''): ?>
    <script>
        alert("<?= htmlspecialchars(addslashes($successMessage), ENT_QUOTES, 'UTF-8') ?>");
    </script>
<?php endif; ?>

<?php if ($errorMessage !== ''): ?>
    <script>
        alert("<?= htmlspecialchars(addslashes($errorMessage), ENT_QUOTES, 'UTF-8') ?>");
    </script>
<?php endif; ?>

<h3>My reservations</h3>
<?php if (!empty($bookings)): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Departure</th>
            <th>Destination</th>
            <th>Date</th>
            <th>Time</th>
            <th>Places</th>
            <th>Total</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Action</th>
        </tr>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= htmlspecialchars($booking['departure_city']) ?></td>
                <td><?= htmlspecialchars($booking['destination_city']) ?></td>
                <td><?= htmlspecialchars($booking['departure_date']) ?></td>
                <td><?= htmlspecialchars($booking['departure_time']) ?></td>
                <td><?= (int) $booking['seats_booked'] ?></td>
                <td><?= number_format((float) $booking['total_price'], 2, ',', ' ') ?> €</td>
                <td><?= htmlspecialchars($booking['status']) ?></td>
                <td><?= htmlspecialchars($booking['payment_status'] ?? 'Pending') ?></td>
                <td>
                    <?php if (($booking['trip_status'] ?? '') === 'Completed' && (int) $booking['has_reviewed'] === 0): ?>
                        <a href="add_review.php?trip_id=<?= (int) $booking['trip_id'] ?>">Leave a review</a>
                    <?php elseif (($booking['trip_status'] ?? '') === 'Completed'): ?>
                        Reviewed
                    <?php elseif (($booking['status'] ?? 'Pending') === 'Pending'): ?>
                        <a href="pay_booking.php?booking_id=<?= (int) $booking['id'] ?>">Pay</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>You have no reservation yet.</p>
<?php endif; ?>
<?php
echo '<a href="signout.php"><button type="button">Log out</button></a>';
?>
</body>
</html>
