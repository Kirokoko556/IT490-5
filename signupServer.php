#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function doSignup($username,$password)
{
	// lookup username in database
	$mydb = new mysqli('127.0.0.1','carter','abcde','IT490');
	$usr = "select username from users where username ='$username'";
	$uquery = mysqli_query($mydb,$usr);
	$usrquery = mysqli_fetch_assoc($uquery);
	if ($usrquery == Null)
	{
		return false;
		exit();
	}
	// check password
	$pwd = "select password from users where username ='$username'";
	$pquery = mysqli_query($mydb,$pwd);
	$pwdquery = mysqli_fetch_assoc($pquery);
	foreach($pwdquery as $key => $value)
	{
		if ($password !== $value)
        	{
			return false;
			exit();
		}
	}
        return true;
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
      return doSignup($request["username"],$request["password"]);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>

