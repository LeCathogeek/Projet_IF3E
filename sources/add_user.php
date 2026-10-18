<html lang="en">
<head>
<title>Add students</title>
<link rel="stylesheet"
href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body>
<?php
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php');
        exit;
    }

    // Connexion PDO SQLite
    $pdo = new PDO('sqlite:' . __DIR__ . '/databases/main_database.sq3');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération et nettoyage des champs
    $name = trim($_POST['name'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phonenumber = trim($_POST['phonenumber'] ?? '');
    $birthdate = trim($_POST['birthdate'] ?? '');
    $profilepicture = trim($_POST['avatar'] ?? '');
    $accountcreationdate = date('Y-m-d');
    $accountstatus = 0;

    // Validations côté serveur
    if ($name === '' || $firstname === '') {
        throw new Exception('Le nom et le prénom sont requis.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script> alert('Mauvais email'); </script>"; 
    }
    if (strlen($password) < 8) {
         echo "<script> alert('Mauvais email'); </script>"; 
    }
    // Optionnel: vérification format téléphone simple
    if ($phonenumber !== '' && !preg_match('/^[0-9 +()-]{6,20}$/', $phonenumber)) {
         echo "<script> alert('Mauvais email'); </script>"; 
    }
    // Optionnel: url d'avatar valide si fournie
    if ($profilepicture !== '' && !filter_var($profilepicture, FILTER_VALIDATE_URL)) {
         echo "<script> alert('Votre URL n'est pas valide.'); </script>"; 
    }
    // Optionnel: validation date (YYYY-MM-DD attendue par <input type="date">)
    if ($birthdate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
         echo "<script> alert('Date de naissance invalide.'); </script>"; 
    }

    // Vérifier si l'email existe déjà (prévenir violation UNIQUE et donner message propre)
    $check = $pdo->prepare('SELECT COUNT(1) FROM users WHERE email = :email');
    $check->execute([':email' => $email]);
    if ($check->fetchColumn() > 0) {
        throw new Exception('Un compte avec cette adresse e-mail existe déjà.');
    }

    // INSÉRER SANS FOURNIR id : SQLite s'en chargera (AUTOINCREMENT si la table est définie ainsi)
    $sql = "INSERT INTO users
      (name, firstname, email, password, phone_number, birthdate, account_creation_date, account_status, profile_picture)
      VALUES
      (:name, :firstname, :email, :password, :phonenumber, :birthdate, :accountcreationdate, :accountstatus, :profilepicture)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':firstname' => $firstname,
        ':email' => $email,
        ':password' => $password,
        ':phonenumber' => $phonenumber,
        ':birthdate' => $birthdate,
        ':accountcreationdate' => $accountcreationdate,
        ':accountstatus' => $accountstatus,
        ':profilepicture' => $profilepicture
    ]);

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    // Gestion spécifique pour contrainte d'unicité si la vérification préalable a échoué
    http_response_code(500);
    echo 'Erreur base de données : ' . htmlspecialchars($e->getMessage());
    exit;
} catch (Exception $e) {
    http_response_code(400);
    echo 'Erreur : ' . htmlspecialchars($e->getMessage());
    exit;
}
?>
</body>
</html>
