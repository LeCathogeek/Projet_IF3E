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
    $brand = trim((string) ($_POST['brand'] ?? ''));
    $model = trim((string) ($_POST['model'] ?? ''));
    $registrationNumber = trim((string) ($_POST['registration_number'] ?? ''));
    $seatCount = (int) ($_POST['seat_count'] ?? 0);
    $color = trim((string) ($_POST['color'] ?? ''));
    $year = (int) ($_POST['year'] ?? 0);

    if ($brand === '' || $model === '' || $registrationNumber === '' || $seatCount <= 0) {
        $_SESSION['flash_error'] = 'All the compulsory fields have to be fullfilled.';
        header('Location: add_vehicle.php');
        exit;
    }

    $stmt = $db->prepare('
        INSERT INTO vehicles (owner_id, brand, model, registration_number, seat_count, color, year)
        VALUES (:owner_id, :brand, :model, :registration_number, :seat_count, :color, :year)
    ');

    $stmt->execute([
        ':owner_id' => $userId,
        ':brand' => $brand,
        ':model' => $model,
        ':registration_number' => $registrationNumber,
        ':seat_count' => $seatCount,
        ':color' => $color,
        ':year' => $year,
    ]);

    $_SESSION['flash_success'] = 'Véhicule ajouté avec succès.';
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
    <h1>Add a car</h1>

    <?php
echoMenu();
?>

    <form method="post">
        <label>Brand</label><br>
        <input type="text" name="brand" required><br><br>

        <label>Model</label><br>
        <input type="text" name="model" required><br><br>

        <label>Registration number</label><br>
        <input type="text" name="registration_number" required><br><br>

        <label>Number of seats</label><br>
        <input type="number" name="seat_count" min="1" required><br><br>

        <label>Colour</label><br>
        <input type="text" name="color"><br><br>

        <label>Year</label><br>
        <input type="number" name="year" min="1900" max="2100"><br><br>

        <button type="submit">Add</button>
    </form>
</body>
</html>
