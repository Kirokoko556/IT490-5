#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$mydb = new mysqli('127.0.0.1','carter','abcde','IT490');

function doSignup($username,$password,$firstname,$lastname,$email)
{
	global $mydb;
        // check username
        $usr = "select username from users where username = ?;";
        $uquery = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($uquery, $usr))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($uquery, "s", $username);
        mysqli_stmt_execute($uquery);
        $usrresult = mysqli_stmt_get_result($uquery);
        if (mysqli_fetch_assoc($usrresult) !== Null)
        {
                return false;
                exit();

	}
        mysqli_stmt_close($uquery);
        // check email
        $e = "select email from users where email = ?;";
        $equery = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($equery, $e))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($equery, "s", $email);
        mysqli_stmt_execute($equery);
        $emailresult = mysqli_stmt_get_result($equery);
        if (mysqli_fetch_assoc($emailresult) !== Null)
        {
                return false;
                exit();
        }
        mysqli_stmt_close($equery);
        //hash password
        $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
        // insert parameters into users table
        $insert = "insert into users (username, password, firstName, lastName, email) values(?,?,?,?,?);";
        $insertstmt = mysqli_stmt_init($mydb);
	if (!mysqli_stmt_prepare($insertstmt, $insert))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($insertstmt, "sssss", $username, $hashedpassword, $firstname, $lastname, $email);
        mysqli_stmt_execute($insertstmt);
	mysqli_stmt_close($insertstmt);
	$usertable = "create table ? (services varchar(255));";
	$createstmt = mysqli_stmt_init($mydb);
        if (!mysqli_stmt_prepare($createstmt, $usertable))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($createstmt, "s", $username);
        mysqli_stmt_execute($createstmt);
        mysqli_stmt_close($createstmt);
        return true;
}


function doLogin($username,$password)
{
	global $mydb;
	// lookup username in database
	$usr = "select username from users where username = ?;";
	$uquery = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($uquery, $usr))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($uquery, "s", $username);
        mysqli_stmt_execute($uquery);
        $usrresult = mysqli_stmt_get_result($uquery);
	if (mysqli_fetch_assoc($usrresult) == Null)
	{
		return false;
		exit();
	}
	mysqli_stmt_close($uquery);
	// check password
	$pwd = "select password from users where username = ?;";
	$pquery = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($pquery, $pwd))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($pquery, "s", $username);
        mysqli_stmt_execute($pquery);
        $pwdresult = mysqli_stmt_get_result($pquery);
	$pwdquery = mysqli_fetch_assoc($pwdresult);
	foreach($pwdquery as $key => $value)
	{

		if (password_verify($password, $value) == false)
        	{
			return false;
			exit();
		}
	}
	mysqli_stmt_close($pquery);
        return true;
}

function getMovie($title)
{
	global $mydb;
	$mv = "select title from moviesAndEpisodes where title = ?;";
        $mquery = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($mquery, $mv))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($mquery, "s", $title);
        mysqli_stmt_execute($mquery);
	$movieresult = mysqli_stmt_get_result($mquery);
	mysqli_stmt_close($mquery);
	if (mysqli_fetch_assoc($movieresult) == Null)
	{
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
		$request['type'] = "movie";
		$request['title'] = $title;
		$request['message'] = $msg;
		$response = $client->send_request($request);
		if($response == false)
		{
			return false;
			exit();
		}
		list($title, $country, $type, $service, $genre) = $response;
		$insert = "insert into moviesAndEpisodes (title, country, type, service, genre) values(?,?,?,?,?);";
        	$insertstmt = mysqli_stmt_init($mydb);
        	if(!mysqli_stmt_prepare($insertstmt, $insert))
        	{
                	return false;
                	exit();
        	}
        	mysqli_stmt_bind_param($insertstmt, "sssss", $title, $country, $type, $service, $genre);
        	mysqli_stmt_execute($insertstmt);
		mysqli_stmt_close($insertstmt);
	}
	$moviedata = "select * from moviesAndEpisodes where title  = ?;";
        $mdquery = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($mdquery, $moviedata))
        {
        	return false;
                exit();
        }
        mysqli_stmt_bind_param($mdquery, "s", $title);
        mysqli_stmt_execute($mdquery);
        $mdresult = mysqli_stmt_get_result($mdquery);
	$mfetch = mysqli_fetch_assoc($mdresult);
	$mvarray = array();
	foreach($mfetch as $key => $value)
	{
		array_push($mvarray, $value);
	}
	mysqli_stmt_close($mdquery);
	return $mvarray;
}

function genreRecommendation($genre)
{
	global $mydb;
        $g = "select title, service from moviesAndEpisodes where genre = ?;";
        $gquery = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($gquery, $g))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($gquery, "s", $genre);
        mysqli_stmt_execute($gquery);
	$genreresult = mysqli_stmt_get_result($gquery);
	$genreassoc = mysqli_fetch_assoc($genreresult);
	mysqli_stmt_close($gquery);
        if ($genreassoc == Null)
	{
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
                $request['type'] = "genre";
                $request['title'] = $genre;
                $request['message'] = $msg;
                $response = $client->send_request($request);
                if($response == false)
                {
                        return false;
                        exit();
                }

	}
	
	return;
}

function addService($username, $service)
{
	global $mydb
	$select = "select services from ? where services = ?;";
        $selectstmt = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($selectstmt, $select))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($selectstmt, "ss", $username, $service);
        mysqli_stmt_execute($selectstmt);
	$selectresult = mysqli_stmt_get_result($selectstmt);
	$selectassoc = mysqli_fetch_assoc($selectresult);
	mysqli_stmt_close($selectstmt);
	if($selectassoc == Null)
	{
		$insert = "insert into ? (services) values(?);";
		$insertstmt = mysqli_stmt_init($mydb);
		if(!mysqli_stmt_prepare($insertstmt, $insert))
        	{
                	return false;
                	exit();
        	}
        	mysqli_stmt_bind_param($insertstmt, "ss", $username, $service);
        	mysqli_stmt_execute($insertstmt);
        	mysqli_stmt_close($insertstmt);
		return true;
	}
	return false;

}

function removeService($username, $service)
{
	global $mydb
	$select = "select service from ? where service = ?;";
        $selectstmt = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($selectstmt, $select))
        {
                return false;
                exit();
        }
        mysqli_stmt_bind_param($selectstmt, "ss", $username, $service);
        mysqli_stmt_execute($selectstmt);
	$selectresult = mysqli_stmt_get_result($selectstmt);
        $selectassoc = mysqli_fetch_assoc($selectresult);
	mysqli_stmt_close($selectstmt);
        if($selectassoc == Null)
        {
		return false;
		exit();
	}
	$delete = "delete from ? where services = ?;";
        $deletestmt = mysqli_stmt_init($mydb);
        if(!mysqli_stmt_prepare($deletestmt, $delete))
        {
        	return false;
                exit();
        }
        mysqli_stmt_bind_param($deletestmt, "ss", $username, $service);
        mysqli_stmt_execute($deletestmt);
        mysqli_stmt_close($deletestmt);
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
      return doLogin($request["username"],$request["password"]);
    case "register":
      return doSignup($request["username"],$request["password"], $request["firstname"], $request["lastname"], $request["email"]);
    case "validate_session":
      return doValidate($request['sessionId']);
    case "title":
      return getMovie($request['title']);
    case "genre":
      return genreRecommendation($request['genre']);
    case "add service":
      return addService($request['username'], $request['service']);
    case "remove service":
      return removeService($request['username'], $request['service']);
  }
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>

