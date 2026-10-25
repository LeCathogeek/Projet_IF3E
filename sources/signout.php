<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: index.php");
exit;
?>
<html lang="en">
<head>
<title>Sign out</title>
<link rel="stylesheet" href="../styles/styles.css" />
</head>
<body>
</body>
</html>
