<?php

	session_start();
include ../IT490connect.php

$db = $mydb

$FName = "";
$LName = "";
$Username = "";
$email    = "";
$errors   = array(); 

if (isset($_POST['register'])) {

	global $db, $errors, $Username, $email, $FName, $LName;
	$FName       =  e($_POST['FName']);
	$LName       =  e($_POST['LName']);
	$Username       =  e($_POST['Username']);
	$Email       =  e($_POST['Email']);
	$Password  =  e($_POST['Password']);

	if (count($errors) == 0) {
		$Password = md5($Password);
// 										<!--- need to send the following items to database: Admin	Username	Email	Password	First Name	Last Name --->
		if (isset($_POST['Admin'])) {
			$Admin = e($_POST['Admin']);
			$query = "INSERT INTO `EmployeeInfo` (`EmployeeID`, `Admin`, `Username`, `Email`, `Password`, `First Name`, `Last Name`) VALUES (NULL, '$Admin', '$Username', '$Email', '$Password', '$FName', '$LName');";
					 
			mysqli_query($db, $query);
			$_SESSION['success']  = "New user successfully created!!";
            $_SESSION["loggedin"] =true;
			header('location: admin.php');
		}else{
			$query = "INSERT INTO `EmployeeInfo` (`EmployeeID`, `Admin`, `Username`, `Email`, `Password`, `First Name`, `Last Name`) VALUES (NULL, '0', '$Username', '$Email', '$Password', '$FName', '$LName');";
			mysqli_query($db, $query);

			$logged = mysqli_insert_id($db);

			$_SESSION['user'] = getId($logged); 
            $_SESSION["loggedin"] = true;
			header('location: employee.php');				
		}
	}
}

function getId($id){
	global $db;
	$query = "SELECT * FROM EmployeeInfo WHERE EmployeeID=" . $id;
	$result = mysqli_query($db, $query);
	$user = mysqli_fetch_assoc($result);
	return $user;
}

function e($val){
	global $db;
	return mysqli_real_escape_string($db, trim($val));
}

function display_error() {
	global $errors;

	if (count($errors) > 0){
		echo '<div class="error">';
			foreach ($errors as $error){
				echo $error .'<br>';
			}
		echo '</div>';
	}
}	


if (isset($_POST['login'])) {

	//global $db, $Username, $errors;
	global $db, $errors;

	//$Username = e($_POST['Username']);
	$Password = e($_POST['Password']);
	$LoginInfo = e($_POST['LoginInfo']);
    //$Email = e($_POST['Email']);
	

	if (count($errors) == 0) {
		$Password = md5($Password);

		$query = "SELECT * FROM EmployeeInfo WHERE Username='$LoginInfo' OR Email='$LoginInfo' AND Password='$Password' LIMIT 1";
		$results = mysqli_query($db, $query);

		if (mysqli_num_rows($results) == 1) { 
			$logged = mysqli_fetch_assoc($results);
			if ($logged['Admin'] == '1') {

				$_SESSION['user'] = $logged;
                $_SESSION["loggedin"] = true;
				header('location: admin.php');		  
			}
            else{
				$_SESSION['user'] = $logged;
                $_SESSION["loggedin"] = true;
				header('location: employee.php');
			}
		}else {
			array_push($errors, "Invalid username/password");
		}
	}
}
