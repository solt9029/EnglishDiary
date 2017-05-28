<?php
session_start();

require_once("../func.php");
require_once("../config.php");

try{
	$pdo=new PDO($dns_info,$db_user,$db_password);
}catch(Exception $e){
	//$e->getMessage();
}

if(isset($_SESSION["user_id"])){
	header("location:home.php");
	exit;
}

if(isset($_POST["login"])){
	$user_id=$_POST["user_id"];
	$password=$_POST["password"];
	$hash=getHash($password);
	$sql="SELECT * FROM usersinfo WHERE UserID=?";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array($user_id));
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		if($row["UserID"]==$user_id && $row["Password"]==$hash){
			$_SESSION["user_id"]=$user_id;
			header("location:home.php");
			exit;
		}
	}
	//check
	echo "<script>window.alert('Make sure User ID and Password are correct.');</script>";
	/*header("location:index.php");*/
	/*exit;*/
} 

if(isset($_POST["signup"]) && strlen($_POST["user_id"])>0 && strlen($_POST["user_id"])<=20 && strlen($_POST["password"])>0 && strlen($_POST["password"])<=50){
	$user_id=$_POST["user_id"];

	//check whether there is already a same UserID or not.
	$sql="SELECT * FROM usersinfo WHERE UserID=?";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array($user_id));
	$exist=false;
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		if($row["UserID"]==$user_id){
			echo "<script>window.alert('This User ID is already used by someone.');</script>";
			$exist=true;
			/*header("location:index.php");*/
			/*exit;*/
		}
	}
	if($exist==false){
		$password=$_POST["password"];
		$hash=getHash($password);
		$sql="INSERT INTO usersinfo VALUE(?,?)";
		$stmt=$pdo->prepare($sql);
		$stmt->execute(array($user_id,$hash));
		$_SESSION["user_id"]=$user_id;
		/*echo "<script>window.alert('Your User ID was created correctly.');location.href='index.php';</script>";*/
		header("location:home.php");
		exit;
	}
}

?>
<html>
	<head>
		<title>EnglishDiary</title>
		<link rel="stylesheet" href="../styles/index.css">
		<script src="../scripts/jquery.js"></script>
		<meta charset="utf-8">
	</head>
	<body>
		<p class="site-title-sub">To Study English Writing</p>
		<h1 class="site-title">EnglishDiary</h1>
		<form method="post" name="form" class="form" id="form">
			<input type="text" name="user_id" class="textform" placeholder="User ID"><br>
			<input type="password" name="password" class="textform" placeholder="Password"><br>
			<input type="submit" class="button button-showy" name="login" value="Log In">
			<input type="submit" class="button" name="signup" value="Sign Up">	
		</form>	
	</body>
</html>