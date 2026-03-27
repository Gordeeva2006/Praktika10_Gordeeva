<?php
	session_start();
	include("../settings/connect_datebase.php");
	require_once("../libs/autoload.php");
	
	$login = $_POST['login'];
	
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."';");
	
	$id = -1;
	if($user_read = $query_user->fetch_row()) {
		$id = $user_read[0];
	}
	
	if(isset($_POST["g-recaptcha-response"]) == false) {
		echo "Не прошли капчу";
		exit;
	}
	$Secret = "6Ld_0pksAAAAAFHCw-3MYgjCQuLIij7ah1s1Q96v";
	$Recaptcha = new \ReCaptcha\ReCaptcha($Secret);
	$Response = $Recaptcha->verify($_POST["g-recaptcha-response"], $_SERVE['REMOTE_ADDR']);
	if($Response->isSuccess()) {
		$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
		$user_new = $query_user->fetch_row();
		$id = $user_new[0];
		if($id != -1) $_SESSION['user'] = $id; 
		echo $id;
	}

	function PasswordGeneration() {
		$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; 
		$max=10; 
		$size=StrLen($chars)-1; 
		$password="";
		
		while($max--) {
			$password.=$chars[rand(0,$size)];
		}
		
		return $password;
	}
	
	if($id != 0) {
		$password = PasswordGeneration();
		$query_password = $mysqli->query("SELECT * FROM `users` WHERE `password`= '".md5($password)."';");
		while($password_read = $query_password->fetch_row()) {
			$password = PasswordGeneration();
		}
		// обновляем пароль
		$mysqli->query("UPDATE `users` SET `password`='".md5($password)."' WHERE `login` = '".$login."'");
	}
	
	// echo $id;
?>