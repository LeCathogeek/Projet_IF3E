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
    $brand = trim((string) ($_POST['brand'] ?? ''));
    $model = trim((string) ($_POST['model'] ?? ''));
    $registrationNumber = trim((string) ($_POST['registration_number'] ?? ''));
    $seatCount = (int) ($_POST['seat_count'] ?? 0);
    $color = trim((string) ($_POST['color'] ?? ''));
    $year = (int) ($_POST['year'] ?? 0);

    if ($brand === '' || $model === '' || $registrationNumber === '' || $seatCount <= 0) {
        $_SESSION['flash_error'] = 'Tous les champs obligatoires doivent être remplis.';
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
    <title>Ajouter une voiture</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Ajouter une voiture</h1>

    <div id="navigation">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="search_trips.php">Rechercher un trajet</a></li>
            <li><a href="add_vehicle.php">Ajouter une voiture</a></li>
            <li><a href="create_trip.php">Publier un trajet</a></li>
            <li><a href="my_trips.php">Mes trajets</a></li>
        </ul>
        <a href="signout.php"><button type="button">Déconnexion</button></a>
    </div>

    <form method="post">
        <label>Marque</label><br>
        <input type="text" name="brand" required><br><br>

        <label>Modèle</label><br>
        <input type="text" name="model" required><br><br>

        <label>Immatriculation</label><br>
        <input type="text" name="registration_number" required><br><br>

        <label>Nombre de places</label><br>
        <input type="number" name="seat_count" min="1" required><br><br>

        <label>Couleur</label><br>
        <input type="text" name="color"><br><br>

        <label>Année</label><br>
        <input type="number" name="year" min="1900" max="2100"><br><br>

        <button type="submit">Ajouter</button>
    </form>
</body>
</html>
