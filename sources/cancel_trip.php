<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/basic_functions.php';
$db = getDb();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my_trips.php');
    exit;
}

$userId = (int) $_SESSION['id'];
$tripId = (int) ($_POST['trip_id'] ?? 0);

if ($tripId <= 0) {
    $_SESSION['flash_error'] = 'Invalid trip.';
    header('Location: my_trips.php');
    exit;
}

$stmt = $db->prepare('SELECT id, driver_id, status FROM trips WHERE id = :trip_id AND driver_id = :driver_id LIMIT 1');
$stmt->execute([
    ':trip_id' => $tripId,
    ':driver_id' => $userId,
]);
$trip = $stmt->fetch();

if (!$trip) {
    $_SESSION['flash_error'] = 'This trip does not belong to you.';
    header('Location: my_trips.php');
    exit;
}

if ($trip['status'] === 'Completed') {
    $_SESSION['flash_error'] = 'This trip is already completed.';
    header('Location: my_trips.php');
    exit;
}

$stmt = $db->prepare('UPDATE trips SET status = "Cancelled" WHERE id = :trip_id');
$stmt->execute([':trip_id' => $tripId]);

$stmt = $db->prepare('UPDATE bookings SET status = "Cancelled" WHERE trip_id = :trip_id AND status != "Completed"');
$stmt->execute([':trip_id' => $tripId]);

$_SESSION['flash_success'] = 'This trip has been cancelled.';
header('Location: my_trips.php');
exit;
