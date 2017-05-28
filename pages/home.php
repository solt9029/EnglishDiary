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

if(isset($_POST["diary"]) && strlen($_POST["head"])>0 && strlen($_POST["head"])<=50 && strlen($_POST["body"])>0 && strlen($_POST["body"])<=1000){
	$head=$_POST["head"];
	$body=htmlspecialchars($_POST["body"]);
	$date=date("Y-m-d H:i:s");
	$sql="INSERT INTO diary VALUE(?,?,?,?)";
	$stmt=$pdo->prepare($sql);
	$stmt->execute(array($user_self,$head,$body,$date));
	header("location:home.php");
	exit;
}

$rnd=rand(0,46723);
$sql="SELECT * FROM dict WHERE Id=?";
$stmt=$pdo->prepare($sql);
$stmt->execute(array($rnd));
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
	$keyword=$row["Word"];
}

if(isset($_GET["account"])){
	header("location:account.php?account={$_GET["account"]}");
	exit;
}

?>

<html>
	<head>
		<title>EnglishDiary</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="../styles/reset.css">
		<link rel="stylesheet" href="../styles/style.css">
		<script src="../scripts/prototype.js"></script>
		<script src="../scripts/home.js"></script>
	</head>
	<body>
		<header class="header">
			<h1 class="logo">
				<a href="index.php">EnglishDiary</a>
			</h1>
			<nav class="global-nav">
				<ul>
					<li class="nav-item active"><a href="home.php">Home</a></li>
					<li class="nav-item"><a href="account.php">Account</a></li>
				</ul>
			</nav>
		</header>
		<div class="wrapper clearfix">
			<main class="main">
				<h2 class="heading">Diary Post</h2>
				<form method="post" class="diary-post">
					<div class="head-label"><label>Head:<input type="text" name="head" id="head" class="textform" value="<?php echo $keyword; ?>"></label></div>	
					<div class="body-label"><label>Body:<textarea name="body" id="body" class="textareaform"></textarea></label></div>
					<input type="submit" name="diary" id="diary" value="Diary" class="button button-showy">
				</form>
				<h2 class="heading">Diary List</h2>
				<ul class="diary-list">
					<?php
$following_list=array();
$sql="SELECT * FROM following WHERE UserID=?";
$stmt=$pdo->prepare($sql);
$stmt->execute(array($user_self));
$following_count=0;
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
	$following_list[$following_count]=$row["Following"];
	$following_count++;
}

$sql="SELECT * FROM diary WHERE UserID='{$user_self}'";
foreach($following_list as $f){
	$sql.=" OR UserID='{$f}'";
}
$sql.=" ORDER BY date DESC";
$stmt=$pdo->prepare($sql);
$stmt->execute(null);
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
		<footer class="footer">
			<ul class="myweb-list">
				<li class="myweb-item"><a>TOEIC勉強会</a></li>
				<li class="myweb-item"><a>SOLT9029</a></li>
				<li class="myweb-item"><a>食塩が出現する日記</a></li>
			</ul>
			<p class="copyright">Copyright (c) 2016 EnglishDiary</p>
		</footer>
	</body>
</html>