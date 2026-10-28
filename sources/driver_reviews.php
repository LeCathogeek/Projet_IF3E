<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/basic_functions.php';
$db = getDb();

$userId = (int) $_SESSION['id'];

$successMessage = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$errorMessage = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$tripId = (int) ($_GET['trip_id'] ?? 0);

$stmt = $db->prepare('SELECT r.id, r.rating, r.comment, r.created_at,
        u.firstname AS passenger_firstname, u.name AS passenger_name,
        t.departure_city, t.destination_city, t.departure_date, t.departure_time
    FROM reviews r
    JOIN users u ON u.id = r.passenger_id
    JOIN trips t ON t.id = r.trip_id
    WHERE r.driver_id = :driver_id
    ORDER BY r.created_at DESC');
$stmt->execute([':driver_id' => $userId]);
$reviews = $stmt->fetchAll();

$stmt = $db->prepare('SELECT AVG(rating) AS average_rating, COUNT(*) AS total_reviews FROM reviews WHERE driver_id = :driver_id');
$stmt->execute([':driver_id' => $userId]);
$ratingSummary = $stmt->fetch();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>My reviews</title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    <h1>My reviews</h1>
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

    <p><strong>Average rating:</strong> <?= number_format((float) ($ratingSummary['average_rating'] ?? 0), 2, ',', ' ') ?> / 5</p>
    <p><strong>Total reviews:</strong> <?= (int) ($ratingSummary['total_reviews'] ?? 0) ?></p>

    <?php if (empty($reviews)): ?>
        <p>No review has been left for your trips yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Passenger</th>
                <th>Trip</th>
                <th>Date</th>
                <th>Rating</th>
                <th>Comment</th>
            </tr>
            <?php foreach ($reviews as $review): ?>
                <tr>
                    <td><?= htmlspecialchars($review['passenger_firstname']) ?> <?= htmlspecialchars($review['passenger_name']) ?></td>
                    <td><?= htmlspecialchars($review['departure_city']) ?> → <?= htmlspecialchars($review['destination_city']) ?></td>
                    <td><?= htmlspecialchars($review['departure_date']) ?> at <?= htmlspecialchars($review['departure_time']) ?></td>
                    <td><?= (int) $review['rating'] ?> / 5</td>
                    <td><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
