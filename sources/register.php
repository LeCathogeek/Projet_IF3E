<html>
<head>
<title>Projet Databases IF3E</title>
<link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
<h1>Welcome ! Let's create your new account !</h1>
<div class="formular">
<form method="post" name="form" action="index.html">
  <label for="fname">First name : </label><br>
  <input type="text" id="fname" name="fname" required="required"><br>
  <label for="lname">Last name : </label><br>
  <input type="text" id="lname" name="lname" required="required"><br>
  <label for="email">Email : </label><br>
  <input type="text" id="email" name="email" required="required"><br>
  <label for="password">Password : </label><br>
  <input type="password" id="password" name="password" required="required"><br>
  <label for="phone">Phone number : </label><br>
  <input type="text" id="phone" name="phone" required="required"><br>
  <label for="lname">Birthdate : </label><br>
  <input type="date" id="birthdate" name="birthdate" required="required"><br>
  <label for="lname">Internet path to your avatar image : </label><br>
  <input type="text" id="avatar" name="avatar"><br>
<br />
<br />
<input type="submit" />
</form>
<script>
document.addEventListener('DOMContentLoaded', function () {

const form = document.querySelector('form');
form.addEventListener('submit', function (e) {
const email = (form.elements['email'] || {}).value || '';
const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[A-Za-z]{2,}$/;
    if (!regex.test(email.trim())) {
      e.preventDefault();
      alert('Invalid e-mail adress.');
      return false;
    }
    alert("Success !");
    return true;
  });
});
</script>
</div>
<a href="index.html"><button>Home</button></a>
</body>
</html>
