<?php
  session_start();
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
      header("location: login.php");
      exit;
  }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employee Page</title>
</head>
<body>
<h1>Employee</h1>
<a href='logout.php'>Logout</a>;
</body>
</html>
