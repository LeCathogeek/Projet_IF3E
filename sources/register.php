<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Projet Databases IF3E — Inscription</title>
  <link rel="stylesheet" href="stylesheets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
  <style type="text/css">
    .iti__flag {
  background-color: #000000;}
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
</head>
<body>
  <h1>Welcome ! Let's create your new account !</h1>
  <div class="formular">
    <form method="post" name="form" action="add_user.php" novalidate>
      <label for="firstname">First name :</label><br>
      <input type="text" id="firstname" name="firstname" required="required"><br>
      <label for="name">Last name :</label><br>
      <input type="text" id="name" name="name" required="required"><br>
      <label for="email">Email :</label><br>
      <input type="email" id="email" name="email" required="required"><br>
      <label for="password">Password :</label><br>
      <input type="password" id="password" name="password" minlength="8" required="required"><br>
      <label for="phonenumber">Phone number :</label><br>
      <input type="tel" id="phonenumber" name="phonenumber" pattern="[0-9 +()-]{6,20}"><br>
      <label for="birthdate">Birthdate :</label><br>
      <input type="date" id="birthdate" name="birthdate" required="required"><br>
      <label for="avatar">Internet path to your avatar image :</label><br>
      <input type="url" id="avatar" name="avatar"><br>

      <br/><input type="submit" value="Register">
    </form>
  </div>
  <div class="alert alert-info" style="display: none;"></div>
  <a href="index.php"><button>Home</button></a>
</body>
<script>
   const phoneInputField = document.querySelector("#phonenumber");
   const phoneInput = window.intlTelInput(phoneInputField, {
     utilsScript:
       "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",});
</script>
</html>
