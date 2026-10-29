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
    $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);
    $departureCity = trim((string) ($_POST['departure_city'] ?? ''));
    $destinationCity = trim((string) ($_POST['destination_city'] ?? ''));
    $departureDate = trim((string) ($_POST['departure_date'] ?? ''));
    $departureTime = trim((string) ($_POST['departure_time'] ?? ''));
    $seatsAvailable = (int) ($_POST['seats_available'] ?? 0);
    $pricePerSeat = (float) ($_POST['price_per_seat'] ?? 0);

    if ($vehicleId <= 0 || $departureCity === '' || $destinationCity === '' || $departureDate === '' || $departureTime === '' || $seatsAvailable <= 0 || $pricePerSeat < 0) {
        $_SESSION['flash_error'] = 'Please fill all the fields as required.';
        header('Location: create_trip.php');
        exit;
    }

    $checkVehicle = $db->prepare('
        SELECT id
        FROM vehicles
        WHERE id = :id AND owner_id = :owner_id
        LIMIT 1
    ');
    $checkVehicle->execute([
        ':id' => $vehicleId,
        ':owner_id' => $userId,
    ]);

    if (!$checkVehicle->fetch()) {
        $_SESSION['flash_error'] = "This car doesn't belong to you.";
        header('Location: create_trip.php');
        exit;
    }

    $stmt = $db->prepare('
        INSERT INTO trips (
            driver_id, vehicle_id, departure_city, destination_city,
            departure_date, departure_time, seats_available, price_per_seat, status
        ) VALUES (
            :driver_id, :vehicle_id, :departure_city, :destination_city,
            :departure_date, :departure_time, :seats_available, :price_per_seat, "Open"
        )
    ');

    $stmt->execute([
        ':driver_id' => $userId,
        ':vehicle_id' => $vehicleId,
        ':departure_city' => $departureCity,
        ':destination_city' => $destinationCity,
        ':departure_date' => $departureDate,
        ':departure_time' => $departureTime,
        ':seats_available' => $seatsAvailable,
        ':price_per_seat' => $pricePerSeat,
    ]);

    $_SESSION['flash_success'] = 'This trip has been published !';
    header('Location: my_trips.php');
    exit;
}

$vehicles = $db->prepare('SELECT id, brand, model, seat_count FROM vehicles WHERE owner_id = :owner_id ORDER BY brand, model');
$vehicles->execute([':owner_id' => $userId]);
$vehicleList = $vehicles->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carpooling project IF3E</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Publish a trip</h1>

    <?php
echoMenu();
?>

    <?php if (empty($vehicleList)): ?>
        <p>You have not recorded any vehicle yet.</p>
        <p><a href="add_vehicle.php">Add a car.</a></p>
    <?php else: ?>
        <form method="post">
            <label>Choose a car</label><br>
            <select name="vehicle_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($vehicleList as $vehicle): ?>
                    <option value="<?= (int) $vehicle['id'] ?>">
                        <?= htmlspecialchars($vehicle['brand']) ?> <?= htmlspecialchars($vehicle['model']) ?>
                        (<?= (int) $vehicle['seat_count'] ?> places)
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label>Departure city</label><br>
            <input type="text" name="departure_city" required><br><br>

            <label>Destination city</label><br>
            <input type="text" name="destination_city" required><br><br>

            <label>Departure date</label><br>
            <input type="date" name="departure_date" required><br><br>

            <label>Departure hour</label><br>
            <input type="time" name="departure_time" required><br><br>

            <label>Remaining seats</label><br>
            <input type="number" name="seats_available" min="1" required><br><br>

            <label>Price per seat (€)</label><br>
            <input type="number" name="price_per_seat" min="0" step="0.01" required><br><br>

            <button type="submit">Publish trip</button>
        </form>
    <?php endif; ?>
</body>
</html>
