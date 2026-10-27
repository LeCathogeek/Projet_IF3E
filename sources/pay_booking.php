<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/db.php';
$db = getDb();

$userId = (int) $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = (int) ($_POST['booking_id'] ?? 0);

    if ($bookingId <= 0) {
        $_SESSION['flash_error'] = 'Invalid payment.';
        header('Location: dashboard.php');
        exit;
    }

    $stmt = $db->prepare('SELECT b.id, b.passenger_id, b.total_price, b.status FROM bookings b WHERE b.id = :booking_id LIMIT 1');
    $stmt->execute([':booking_id' => $bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $_SESSION['flash_error'] = 'This payment does not exist.';
        header('Location: dashboard.php');
        exit;
    }

    if ((int) $booking['passenger_id'] !== $userId) {
        $_SESSION['flash_error'] = 'You cannot pay this reservation.';
        header('Location: dashboard.php');
        exit;
    }

    if ($booking['status'] !== 'Pending') {
        $_SESSION['flash_error'] = 'This reservation is already paid or cancelled.';
        header('Location: dashboard.php');
        exit;
    }

    $stmt = $db->prepare('SELECT id FROM payments WHERE booking_id = :booking_id LIMIT 1');
    $stmt->execute([':booking_id' => $bookingId]);
    $payment = $stmt->fetch();

    if ($payment) {
        $stmt = $db->prepare('UPDATE payments SET amount = :amount, payment_date = date("now"), payment_method = "Card", payment_status = "Paid" WHERE booking_id = :booking_id');
        $stmt->execute([
            ':amount' => (float) $booking['total_price'],
            ':booking_id' => $bookingId,
        ]);
    } else {
        $stmt = $db->prepare('INSERT INTO payments (booking_id, amount, payment_date, payment_method, payment_status) VALUES (:booking_id, :amount, date("now"), "Card", "Paid")');
        $stmt->execute([
            ':booking_id' => $bookingId,
            ':amount' => (float) $booking['total_price'],
        ]);
    }

    $stmt = $db->prepare('UPDATE bookings SET status = "Confirmed" WHERE id = :booking_id');
    $stmt->execute([':booking_id' => $bookingId]);

    $_SESSION['flash_success'] = 'Payment confirmed.';
    header('Location: dashboard.php');
    exit;
}

$bookingId = (int) ($_GET['booking_id'] ?? 0);

if ($bookingId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $db->prepare('SELECT b.id, b.total_price, b.status, t.departure_city, t.destination_city, t.departure_date, t.departure_time FROM bookings b JOIN trips t ON t.id = b.trip_id WHERE b.id = :booking_id AND b.passenger_id = :passenger_id LIMIT 1');
$stmt->execute([
    ':booking_id' => $bookingId,
    ':passenger_id' => $userId,
]);
$booking = $stmt->fetch();

if (!$booking) {
    $_SESSION['flash_error'] = 'This reservation does not exist.';
    header('Location: dashboard.php');
    exit;
}

if ($booking['status'] !== 'Pending') {
    $_SESSION['flash_error'] = 'This reservation is already paid or cancelled.';
    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Payment</h1>

    <?php
echoMenu();
?>

    <p><strong>Trip :</strong> <?= htmlspecialchars($booking['departure_city']) ?> → <?= htmlspecialchars($booking['destination_city']) ?></p>
    <p><strong>Date :</strong> <?= htmlspecialchars($booking['departure_date']) ?> at <?= htmlspecialchars($booking['departure_time']) ?></p>
    <p><strong>Total :</strong> <?= number_format((float) $booking['total_price'], 2, ',', ' ') ?> €</p>

    <form method="post" action="pay_booking.php">
        <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
        <button type="submit">Confirm payment</button>
    </form>
</body>
</html>
