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
    <title>Admin Page</title>
</head>
<body>
<h1>Admin</h1>
<a href='logout.php'>Logout</a>;
</body>
</html>
