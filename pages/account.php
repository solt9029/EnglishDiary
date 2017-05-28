<?php
session_start();

require_once("../func.php");
require_once("../config.php");

try{
	$pdo=new PDO($dns_info,$db_user,$db_password);
}catch(Exception $e){
	//$e->getMessage();
}

if(!isset($_SESSION["user_id"])){
	header("location:index.php");
	exit;
}

$user_self=$_SESSION["user_id"];

if(!isset($_GET["account"])){
	header("location:account.php?account={$user_self}");
	exit;
}

$show_account=$_GET["account"];

if(isset($_GET["logout"])){
	unset($_SESSION["user_id"]);
	header("location:index.php");
	exit;
}

if(isset($_GET["follow"])){
	$sql="INSERT INTO following VALUES(?,?)";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array($user_self,$show_account));
	header("location:account.php?account={$show_account}");
	exit;
}

if(isset($_GET["unfollow"])){
	$sql="DELETE FROM following WHERE UserID=? AND Following=?";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array($user_self,$show_account));
	header("location:account.php?account={$show_account}");
	exit;
}	

?>

<html>
	<head>
		<title>EnglishDiary</title>
		<link rel="stylesheet" href="../styles/reset.css">
		<link rel="stylesheet" href="../styles/style.css">
		<meta charset="utf-8">
	</head>
	<body>
		<header class="header">
			<h1 class="logo">
				<a href="index.php">EnglishDiary</a>
			</h1>
			<nav class="global-nav">
				<ul>
					<li class="nav-item"><a href="home.php">Home</a></li>
					<li class="nav-item active"><a href="account.php">Account</a></li>
				</ul>
			</nav>
		</header>
		<div class="wrapper clearfix">
			<main class="main">
				<h2 class="heading"><?php echo $show_account; ?>'s Diary 
				<?php
if($show_account==$user_self){
	echo "(<a href='account.php?account={$show_account}&logout=logout'>Log Out</a>)";
}else{
	$sql="SELECT * FROM following WHERE UserID=?";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array($user_self));
	$show=false;
	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
		if($row["Following"]==$show_account){
			echo "(<a href='account.php?account={$show_account}&unfollow=unfollow'>Unfollow</a>)";
			$show=true;
		}
	}
	if($show==false){
		echo "(<a href='account.php?account={$show_account}&follow=follow'>Follow</a>)";
	}
}
				?>
				</h2>
				<ul class="diary-list">
					<?php
$sql="SELECT * FROM diary WHERE UserID=? ORDER BY Date DESC";
$stmt=$pdo->prepare($sql);
$stmt->execute(array($show_account));
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
	echo "<li class='diary-item'>";
	echo "<div class='diary-title'>";
	echo "<span class='user-id'>●<a href='account.php?account={$row["UserID"]}'>{$row["UserID"]}</a></span>";
	echo "<span class='diary-date'>({$row["Date"]})</span>";
	echo "</div>";
	echo "<div class='diary-content'>";
	echo "<span class='diary-head'>【{$row["Head"]}】</span>";
	echo "<span class='diary-body'>{$row["Body"]}</span>";
	echo "</div>";
	echo "</li>";
}
					?>
				</ul>
				<h2 class="heading">Following</h2>
				<ul class="following-list">
					<?php 
$sql="SELECT * FROM following WHERE UserID=?";
$stmt=$pdo->prepare($sql);
$stmt->execute(array($show_account));
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
	echo "<li class='following-user'>";
	echo "●<a href='account.php?account={$row["Following"]}'>{$row["Following"]}</a>";
	echo "</li>";
}
					?>
				</ul>
			</main>
			<div class="sidemenu">
				<h2 class="heading">Your Account</h2>
				<div class="account-info">
					<p class="welcome">Welcome!!</p>
					<p class="user-id">【User ID】<?php echo "<a href='account.php?account={$user_self}'>{$user_self}</a> (<a href='account.php?account={$user_self}&logout=LogOut'>Log Out</a>)"; ?></p>
					<!--<p class="user-prof">【Profile】</p>-->
					<img src="../images/user-icon.jpg" class="user-icon">
				</div>
				<h2 class="heading">Search</h2>
				<form class="search-box" method="get">
					<input type="text" name="account" class="textform">
					<input type="submit" name="search" value="Search" class="search-button">
					<p class="text">You can search an account here.</p>
				</form>
			</div>
		</div>
		</div>
		<footer class="footer">
			<ul class="myweb-list">
				<li class="myweb-item"><a href="http://solt9029.jimdo.com/">TOEIC勉強会</a></li>
				<li class="myweb-item"><a href="http://solt9029.esy.es/solt9029/home.html">SOLT9029</a></li>
				<li class="myweb-item"><a href="http://blog.livedoor.jp/solt9029/">食塩が出現する日記</a></li>
			</ul>
			<p class="copyright">Copyright (c) 2016 EnglishDiary</p>
		</footer>
	</body>
</html>