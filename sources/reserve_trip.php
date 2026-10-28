<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/basic_functions.php';
$db = getDb();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search_trips.php');
    exit;
}

$userId = (int) $_SESSION['id'];
$tripId = (int) ($_POST['trip_id'] ?? 0);
$seats = (int) ($_POST['seats'] ?? 0);

if ($tripId <= 0 || $seats <= 0) {
    $_SESSION['flash_error'] = 'Invalid reservation.';
    header('Location: search_trips.php');
    exit;
}

$db->beginTransaction();

try {
    $stmt = $db->prepare('SELECT id, seats_available, price_per_seat, status FROM trips WHERE id = :trip_id LIMIT 1');
    $stmt->execute([':trip_id' => $tripId]);
    $trip = $stmt->fetch();

    if (!$trip) {
        throw new Exception('This trip is no longer available.');
    }

    if ($trip['status'] !== 'Open') {
        throw new Exception('This trip is no longer available.');
    }

    if ($seats > (int) $trip['seats_available']) {
        throw new Exception('Not enough places for this reservation.');
    }

    $totalPrice = $seats * (float) $trip['price_per_seat'];
    $newSeats = (int) $trip['seats_available'] - $seats;

    $stmt = $db->prepare('INSERT INTO bookings (trip_id, passenger_id, seats_booked, booking_date, total_price, status) VALUES (:trip_id, :passenger_id, :seats_booked, date("now"), :total_price, "Pending")');
    $stmt->execute([
        ':trip_id' => $tripId,
        ':passenger_id' => $userId,
        ':seats_booked' => $seats,
        ':total_price' => $totalPrice,
    ]);

    $stmt = $db->prepare('UPDATE trips SET seats_available = :seats_available WHERE id = :trip_id');
    $stmt->execute([
        ':seats_available' => $newSeats,
        ':trip_id' => $tripId,
    ]);

    if ($newSeats === 0) {
        $stmt = $db->prepare('UPDATE trips SET status = "Full" WHERE id = :trip_id');
        $stmt->execute([':trip_id' => $tripId]);
    }

    $db->commit();

    $_SESSION['flash_success'] = 'You successfully booked the trip !';
    header('Location: search_trips.php');
    exit;
} catch (Exception $e) {
    $db->rollBack();

    $_SESSION['flash_error'] = $e->getMessage();
    header('Location: search_trips.php');
    exit;
}
