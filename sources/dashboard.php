<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/db.php';
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

$stmt = $db->prepare('
    SELECT b.id, b.status, b.seats_booked, b.total_price,
           t.departure_city, t.destination_city, t.departure_date, t.departure_time
    FROM bookings b
    JOIN trips t ON t.id = b.trip_id
    WHERE b.passenger_id = :user_id
    ORDER BY b.id DESC
');
$stmt->execute([':user_id' => $userId]);
$bookings = $stmt->fetchAll();

$trips = $db->query(
    "SELECT t.*, u.firstname AS driver_name
     FROM trips t
     JOIN users u ON u.id = t.driver_id
     WHERE t.status = 'Open'
     ORDER BY t.departure_date, t.departure_time
     LIMIT 10"
)->fetchAll();
?>
<html>
<head>
<title>Projet Databases IF3E</title>
<link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
<h1>Welcome back !</h1>
<h2>Nice to see you !</h2>
<p>Hello <?= htmlspecialchars($user['firstname']) ?> !</p>

<div id="navigation">
    <ul>
        <li><a href="dashboard.php" title="Dashboard">Dashboard</a></li>
        <li><a href="search_trips.php" title="Search a trip">Search a trip</a></li>
        <li><a href="#" title="My cars">My cars</a></li>
        <li><a href="#" title="My trips">My trips</a></li>
        <?php if ((int) $user['account_status'] === 1): ?>
            <li><a href="#" title="admin">Admin</a></li>
        <?php endif; ?>
    </ul>
    <a href="signout.php"><button type="button">Sign out</button></a>
</div>

<h3>Available trips</h3>
<?php if (empty($trips)): ?>
    <p>No trip is currently available.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Driver</th>
            <th>Departure</th>
            <th>Destination</th>
            <th>Date</th>
            <th>Time</th>
            <th>Seats</th>
            <th>Price</th>
        </tr>
        <?php foreach ($trips as $trip): ?>
            <tr>
                <td><?= htmlspecialchars($trip['driver_name']) ?></td>
                <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                <td><?= htmlspecialchars($trip['departure_date']) ?></td>
                <td><?= htmlspecialchars($trip['departure_time']) ?></td>
                <td><?= (int) $trip['seats_available'] ?></td>
                <td><?= number_format((float) $trip['price_per_seat'], 2, ',', ' ') ?> €</td>
            </tr>
        <?php endforeach; ?>
    </table>
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
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>You have no reservation yet.</p>
<?php endif; ?>
</body>
</html>
