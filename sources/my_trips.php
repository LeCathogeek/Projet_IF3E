<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/basic_functions.php';
$db = getDb();

$userId = (int) $_SESSION['id'];

$stmt = $db->prepare('SELECT firstname, account_status, account_status FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}
$stmt = $db->prepare('SELECT t.id, t.departure_city, t.destination_city, t.departure_date, t.departure_time,
            t.status AS trip_status, v.brand, v.model, COALESCE(SUM(b.seats_booked), 0) AS total_places_reservees
    FROM trips t JOIN vehicles v ON v.id = t.vehicle_id LEFT JOIN bookings b ON b.trip_id = t.id WHERE t.driver_id = :driver_id
    GROUP BY t.id, t.departure_city, t.destination_city, t.departure_date, t.departure_time, t.status, v.brand, v.model
    ORDER BY t.departure_date DESC, t.departure_time DESC');
$stmt->execute([':driver_id' => $userId]);
$trips = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>My trips</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>My trips</h1>

    <?php
echoMenu();
?>
    <?php if (empty($trips)): ?>
        <p>You did not publish any trip yet.</p>
        <p><a href="create_trip.php">Publish a trip</a></p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Start</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Hour</th>
                <th>Car</th>
                <th>Remaining seats</th>
                <th>Price</th>
                <th>Status</th>
                <th>Reservations</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($trips as $trip): ?>
                <tr>
                    <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                    <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                    <td><?= htmlspecialchars($trip['departure_date']) ?></td>
                    <td><?= htmlspecialchars($trip['departure_time']) ?></td>
                    <td><?= htmlspecialchars($trip['brand']) ?> <?= htmlspecialchars($trip['model']) ?></td>
                    <td><?= (int) $trip['total_places_reservees'] ?></td>
                    <td><?= number_format((float) $trip['price_per_seat'], 2, ',', ' ') ?> €</td>
                    <td><?= htmlspecialchars($trip['trip_status']) ?></td>
                    <td><?= (int) $trip['total_places_reservees'] ?></td>
                    <td>
                        <?php if (($trip['trip_status'] ?? '') === 'Completed'): ?>
                            <a href="driver_reviews.php?trip_id=<?= (int) $trip['id'] ?>">See reviews</a>
                        <?php elseif (($trip['trip_status'] ?? '') === 'Open' || ($trip['trip_status'] ?? '') === 'Full'): ?>
                            <form method="post" action="cancel_trip.php" style="display:inline;">
                                <input type="hidden" name="trip_id" value="<?= (int) $trip['id'] ?>">
                                <button type="submit">Cancel trip</button>
                            </form>
                            <form method="post" action="complete_trip.php" style="display:inline;">
                                <input type="hidden" name="trip_id" value="<?= (int) $trip['id'] ?>">
                                <button type="submit">Mark as completed</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
