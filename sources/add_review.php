<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/basic_functions.php';
$db = getDb();

$userId = (int) $_SESSION['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tripId = (int) ($_POST['trip_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim((string) ($_POST['comment'] ?? ''));

    if ($tripId <= 0 || $rating < 1 || $rating > 5) {
        $_SESSION['flash_error'] = 'Please select a valid rating.';
        header('Location: dashboard.php');
        exit;
    }

    $stmt = $db->prepare('SELECT t.id, t.driver_id, t.status, b.id AS booking_id
        FROM trips t
        JOIN bookings b ON b.trip_id = t.id
        WHERE t.id = :trip_id AND b.passenger_id = :passenger_id
        LIMIT 1');
    $stmt->execute([
        ':trip_id' => $tripId,
        ':passenger_id' => $userId,
    ]);
    $trip = $stmt->fetch();
    if (!$trip || ($trip['status'] ?? '') !== 'Completed') {
        $_SESSION['flash_error'] = 'You can only review completed trips.';
        header('Location: dashboard.php');
        exit;
    }
    $existing = $db->prepare('SELECT id FROM reviews WHERE trip_id = :trip_id AND passenger_id = :passenger_id LIMIT 1');
    $existing->execute([
        ':trip_id' => $tripId,
        ':passenger_id' => $userId,
    ]);
    if ($existing->fetch()) {
        $_SESSION['flash_error'] = 'You already left a review for this trip.';
        header('Location: dashboard.php');
        exit;
    }
    $insert = $db->prepare('INSERT INTO reviews (trip_id, passenger_id, driver_id, rating, comment, created_at)
        VALUES (:trip_id, :passenger_id, :driver_id, :rating, :comment, datetime("now"))');
    $insert->execute([':trip_id' => $tripId, ':passenger_id' => $userId, ':driver_id' => (int) $trip['driver_id'], ':rating' => $rating, ':comment' => $comment,]);
    $_SESSION['flash_success'] = 'Your review has been saved.';
    header('Location: dashboard.php');
    exit;
}

$tripId = (int) ($_GET['trip_id'] ?? 0);
if ($tripId <= 0) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $db->prepare('SELECT t.id, t.driver_id, t.departure_city, t.destination_city, t.departure_date, t.departure_time, t.status,
        u.firstname AS driver_firstname, u.name AS driver_name FROM trips t JOIN bookings b ON b.trip_id = t.id
    JOIN users u ON u.id = t.driver_id WHERE t.id = :trip_id AND b.passenger_id = :passenger_id LIMIT 1');
$stmt->execute([':trip_id' => $tripId, ':passenger_id' => $userId,]);
$trip = $stmt->fetch();

if (!$trip || ($trip['status'] ?? '') !== 'Completed') {
    header('Location: dashboard.php');
    exit;
}

$existing = $db->prepare('SELECT id FROM reviews WHERE trip_id = :trip_id AND passenger_id = :passenger_id LIMIT 1');
$existing->execute([
    ':trip_id' => $tripId,
    ':passenger_id' => $userId,
]);
if ($existing->fetch()) {
    $_SESSION['flash_error'] = 'You already left a review for this trip.';
    header('Location: dashboard.php');
    exit;
}

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carpooling project IF3E</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Leave a review</h1>
    <?php
    echoMenu();
    ?>
    <p><strong>Driver:</strong> <?= htmlspecialchars($trip['driver_firstname']) ?> <?= htmlspecialchars($trip['driver_name']) ?></p>
    <p><strong>Trip:</strong> <?= htmlspecialchars($trip['departure_city']) ?> → <?= htmlspecialchars($trip['destination_city']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($trip['departure_date']) ?> at <?= htmlspecialchars($trip['departure_time']) ?></p>

    <form method="post" action="add_review.php">
        <input type="hidden" name="trip_id" value="<?= (int) $trip['id'] ?>">

        <label for="rating">Rating</label><br>
        <select id="rating" name="rating" required>
            <option value="">-- Select --</option>
            <option value="5">5 / 5</option>
            <option value="4">4 / 5</option>
            <option value="3">3 / 5</option>
            <option value="2">2 / 5</option>
            <option value="1">1 / 5</option>
        </select><br><br>

        <label for="comment">Comment</label><br>
        <textarea id="comment" name="comment" rows="5" cols="60" placeholder="Describe your trip..."></textarea><br><br>

        <button type="submit">Send review</button>
    </form>
</body>
</html>
