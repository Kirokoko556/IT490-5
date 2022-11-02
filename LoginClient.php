<?php

session_start();

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$firstName = "";
$lastName = "";
$username = "";
$email    = "";
$password = "";

global $db, $username, $password;
$password  =  e($_POST['Password']);
echo $password;
$username = e($_POST['LoginInfo']);
echo $username;

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

echo $argv[0]." END".PHP_EOL;
?>
