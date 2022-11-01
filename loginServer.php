#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include("IT490connect.inc.php");

function doLogin($username,$password)
{
    // lookup username in database
	$sql = "select * from users where username = $username";
	$mydb = new mysqli('127.0.0.1','carter','abcde','IT490');
	$stmt = mysqli_stmt_init($mydb);
	if (!mysqli_stmt_prepare($stmt, $sql))
	{
		header("location: ../login.php=wronglogin");
		print("username not found");
		return false;
		exit();
	}
	// check password
	$pwd = "select password from users where username = $username";
	if ($password !== $pwd)
        {
		header("location: ../login.php?error=wronglogin");
		print("incorrect password");
		return false;
		exit();
	}
         else
	 {
		 print("login successful");
		 return true;
	 }
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>

