<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/basic_functions.php';
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

$successMessage = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$errorMessage = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$departure = trim((string) ($_GET['departure'] ?? ''));
$destination = trim((string) ($_GET['destination'] ?? ''));
$date = trim((string) ($_GET['date'] ?? ''));
$seats = max(1, (int) ($_GET['seats'] ?? 1));
$maxPrice = trim((string) ($_GET['max_price'] ?? ''));

$sql = "
    SELECT t.id, t.driver_id, t.departure_city, t.destination_city, t.departure_date, t.departure_time, t.seats_available, t.price_per_seat, t.status, u.firstname AS driver_name,
           v.brand, v.model FROM trips t JOIN users u ON u.id = t.driver_id
    LEFT JOIN vehicles v ON v.id = t.vehicle_id
    WHERE t.status = 'Open'";

$params = [];

if ($departure !== '') {
    $sql .= ' AND LOWER(t.departure_city) = LOWER(:departure)';
    $params[':departure'] = $departure;
}

if ($destination !== '') {
    $sql .= ' AND LOWER(t.destination_city) = LOWER(:destination)';
    $params[':destination'] = $destination;
}

if ($date !== '') {
    $sql .= ' AND t.departure_date = :date';
    $params[':date'] = $date;
}

if ($seats > 0) {
    $sql .= ' AND t.seats_available >= :seats';
    $params[':seats'] = $seats;
}

if ($maxPrice !== '') {
    $sql .= ' AND t.price_per_seat <= :max_price';
    $params[':max_price'] = (float) str_replace(',', '.', $maxPrice);
}

$sql .= ' ORDER BY t.departure_date ASC, t.departure_time ASC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$trips = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Find a trip</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Find a trip</h1>
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

    <form method="get" action="search_trips.php">
        <label for="departure">Departure :</label><br>
        <input type="text" id="departure" name="departure" value="<?= htmlspecialchars($departure) ?>"><br><br>

        <label for="destination">Destination :</label><br>
        <input type="text" id="destination" name="destination" value="<?= htmlspecialchars($destination) ?>"><br><br>

        <label for="date">Date :</label><br>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>"><br><br>

        <label for="seats">Minimal number of seats :</label><br>
        <input type="number" id="seats" name="seats" min="1" value="<?= (int) $seats ?>"><br><br>

        <label for="max_price">Maximal price (€) :</label><br>
        <input type="number" id="max_price" name="max_price" min="0" step="0.01" value="<?= htmlspecialchars($maxPrice) ?>"><br><br>

        <button type="submit">Search</button>
    </form>

    <hr>

    <?php if (empty($trips)): ?>
        <p>No trip available for those criteria.</p>
    <?php else: ?>
        <?php foreach ($trips as $trip): ?>
            <div style="border:1px solid #ccc; padding:12px; margin:12px 0; max-width:900px;">
                <p><strong>Driver :</strong> <?= htmlspecialchars($trip['driver_name']) ?></p>
                <p><strong>Car :</strong> <?= htmlspecialchars(($trip['brand'] ?? '')) ?> <?= htmlspecialchars(($trip['model'] ?? '')) ?></p>
                <p><strong>Departure :</strong> <?= htmlspecialchars($trip['departure_city']) ?></p>
                <p><strong>Destination :</strong> <?= htmlspecialchars($trip['destination_city']) ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($trip['departure_date']) ?></p>
                <p><strong>Hour :</strong> <?= htmlspecialchars($trip['departure_time']) ?></p>
                <p><strong>Available seats :</strong> <?= (int) $trip['seats_available'] ?></p>
                <p><strong>Price :</strong> <?= number_format((float) $trip['price_per_seat'], 2, ',', ' ') ?> €</p>
                <p><strong>Status :</strong> <?= htmlspecialchars($trip['status']) ?></p>

                <form method="post" action="reserve_trip.php">
                    <input type="hidden" name="trip_id" value="<?= (int) $trip['id'] ?>">
                    <label for="seats_<?= (int) $trip['id'] ?>">Number of seats :</label>
                    <input type="number" id="seats_<?= (int) $trip['id'] ?>" name="seats" min="1" max="<?= (int) $trip['seats_available'] ?>" value="1">
                    <button type="submit">Book</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
