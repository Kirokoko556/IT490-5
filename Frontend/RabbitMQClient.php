#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$username = $_POST['LoginInfo'];
$password = $_POST['Password'];

$client = new rabbitMQClient("testRabbitMQ.ini","testServer");
if (isset($argv[1]))
{
  $msg = $argv[1];
}
else
{
  $msg = "test message";
}

$request = array();
$request['type'] = "login";
$request['username'] = $username;
$request['password'] = $password;
$request['message'] = $msg;
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";
if ($response=='true') {
  header('Location: employee.php');
}
else {
  header('Location: login.php');
}

/*function is_logged_in($redirect= false, $destination="login.php") 
{
  $isLoggedIn= isset($_SESSION["username"]);
  if ($redirect && !$isLoggedIn) {
    flash("You need to be logged in'");
    die(header("Location: $destination")); 
  }
  return $isLoggedIn;
  }
}
*/
echo $argv[0]." END".PHP_EOL;

