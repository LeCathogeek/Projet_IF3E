<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Projet Databases IF3E — Inscription</title>
  <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
  <h1>Welcome ! Let's create your new account !</h1>
  <div class="formular">
    <form method="post" name="form" action="add_user.php" novalidate>
      <label for="firstname">First name :</label><br>
      <input type="text" id="firstname" name="firstname" required><br>

      <label for="name">Last name :</label><br>
      <input type="text" id="name" name="name" required><br>

      <label for="email">Email :</label><br>
      <input type="email" id="email" name="email" required><br>

      <label for="password">Password :</label><br>
      <input type="password" id="password" name="password" minlength="8" required><br>

      <label for="phonenumber">Phone number :</label><br>
      <input type="tel" id="phonenumber" name="phonenumber" pattern="[0-9 +()-]{6,20}"><br>

      <label for="birthdate">Birthdate :</label><br>
      <input type="date" id="birthdate" name="birthdate" required><br>

      <label for="avatar">Internet path to your avatar image :</label><br>
      <input type="url" id="avatar" name="avatar"><br>

      <br/><input type="submit" value="Créer un compte">
    </form>
  </div>

  <a href="index.php"><button>Home</button></a>
</body>
</html>
