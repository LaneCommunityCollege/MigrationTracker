<?php

require_once("config.php");
session_start();
//check to see if we're just asking for a session number:
if(isset($_GET['session'])){
	session_cache_limiter('nocache');
	header('Expires: ' . gmdate('r', 0));
	header('Content-type: application/json');
	if(isset($_SESSION['user'])){
		//grab the user's name
		$stmt = $db->prepare("SELECT name FROM users WHERE userid=:userid");
		$stmt->execute(array("userid"=>$_SESSION['user']));
		$name = $stmt->fetchColumn();
		echo json_encode(array('session'=>1, 'name'=>$name));
	}
	else
		echo json_encode(array('session'=>0));
}
//check to see if we want to log out
elseif(isset($_GET['logout'])){
	session_destroy();
	echo 1;
}
//otherwise, try to log in
else{
	$email = $_POST['email'];
	$password = $_POST['password'];
	//fetch this email's salt:
	$stmt = $db->prepare("SELECT salt FROM users WHERE email=:email");
	$stmt->execute(array("email"=>$email));
	$salt = $stmt->fetchColumn();
	if($salt == ''  || $email == '' || $password == ''){
		echo json_encode(array('status'=>0,'message'=>"Invalid Username or Password"));
	}
	else{
		//build and hash the password we're going to hash:
		$password = hash("whirlpool", $password . $salt);
		$stmt = $db->prepare("SELECT *, count(userid) as c FROM users WHERE email=:email AND password=:password");
		$stmt->execute(array("email"=>$email, "password"=>$password));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		session_cache_limiter('nocache');
		header('Expires: ' . gmdate('r', 0));
		header('Content-type: application/json');
		if($result['c'] == 1){
			//set a session variable, then redirect them back to the same page (only time the page reloads)
			//this way we can grab the session variable and submit it with ajax requests
			$_SESSION['user'] = $result['userid'];
			echo json_encode(array('status'=>1,'name'=>$result['name'],'email'=>md5(strtolower(trim($result['email'])))));
		}
		else{
			echo json_encode(array('status'=>0,'message'=>"Invalid Username or Password"));
		}
	}
}
?>
