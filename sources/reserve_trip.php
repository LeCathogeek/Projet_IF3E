<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/db.php';
$db = getDb();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search_trips.php');
    exit;
}

$userId = (int) $_SESSION['id'];
$tripId = (int) ($_POST['trip_id'] ?? 0);
$seats = (int) ($_POST['seats'] ?? 0);

if ($tripId <= 0 || $seats <= 0) {
    $_SESSION['flash_error'] = 'Réservation invalide.';
    header('Location: search_trips.php');
    exit;
}

$stmt = $db->prepare('
    SELECT id, seats_available, price_per_seat
    FROM trips
    WHERE id = :trip_id AND status = "Open"
    LIMIT 1
');
$stmt->execute([':trip_id' => $tripId]);
$trip = $stmt->fetch();

if (!$trip) {
    $_SESSION['flash_error'] = 'Ce trajet est introuvable ou n’est plus disponible.';
    header('Location: search_trips.php');
    exit;
}

if ($seats > (int) $trip['seats_available']) {
    $_SESSION['flash_error'] = 'Pas assez de places disponibles pour cette réservation.';
    header('Location: search_trips.php');
    exit;
}

$totalPrice = $seats * (float) $trip['price_per_seat'];

$db->beginTransaction();

$stmt = $db->prepare('
    INSERT INTO bookings (trip_id, passenger_id, seats_booked, booking_date, total_price, status)
    VALUES (:trip_id, :passenger_id, :seats_booked, date("now"), :total_price, "Pending")
');
$stmt->execute([
    ':trip_id' => $tripId,
    ':passenger_id' => $userId,
    ':seats_booked' => $seats,
    ':total_price' => $totalPrice,
]);

$stmt = $db->prepare('
    UPDATE trips
    SET seats_available = seats_available - :seats
    WHERE id = :trip_id
');
$stmt->execute([
    ':trip_id' => $tripId,
    ':seats' => $seats,
]);

$db->commit();

$_SESSION['flash_success'] = 'Réservation enregistrée avec succès.';
header('Location: search_trips.php');
exit;
