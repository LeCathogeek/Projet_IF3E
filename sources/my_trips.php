<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/db.php';
$db = getDb();

$userId = (int) $_SESSION['id'];

$stmt = $db->prepare('SELECT firstname FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$stmt = $db->prepare('
    SELECT t.id, t.departure_city, t.destination_city, t.departure_date, t.departure_time,
           t.seats_available, t.price_per_seat, t.status,
           v.brand, v.model,
           COALESCE(SUM(b.seats_booked), 0) AS total_places_reservees
    FROM trips t
    JOIN vehicles v ON v.id = t.vehicle_id
    LEFT JOIN bookings b ON b.trip_id = t.id
    WHERE t.driver_id = :driver_id
    GROUP BY t.id, t.departure_city, t.destination_city, t.departure_date, t.departure_time,
             t.seats_available, t.price_per_seat, t.status, v.brand, v.model
    ORDER BY t.departure_date DESC, t.departure_time DESC
');
$stmt->execute([':driver_id' => $userId]);
$trips = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes trajets</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>Mes trajets</h1>

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

    <p>Bonjour <?= htmlspecialchars($user['firstname']) ?>.</p>

    <?php if (empty($trips)): ?>
        <p>Vous n'avez publié aucun trajet pour le moment.</p>
        <p><a href="create_trip.php">Publier un trajet</a></p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Départ</th>
                <th>Destination</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Voiture</th>
                <th>Places restantes</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Réservations</th>
            </tr>
            <?php foreach ($trips as $trip): ?>
                <tr>
                    <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                    <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                    <td><?= htmlspecialchars($trip['departure_date']) ?></td>
                    <td><?= htmlspecialchars($trip['departure_time']) ?></td>
                    <td><?= htmlspecialchars($trip['brand']) ?> <?= htmlspecialchars($trip['model']) ?></td>
                    <td><?= (int) $trip['seats_available'] ?></td>
                    <td><?= number_format((float) $trip['price_per_seat'], 2, ',', ' ') ?> €</td>
                    <td><?= htmlspecialchars($trip['status']) ?></td>
                    <td><?= (int) $trip['total_places_reservees'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
