<?php 
function getHash($_p){
	$salt="qwertyuiopasdfghjklzxcvbnm";
	$password_with_salt=$_p.$salt;
	$hash=hash("sha512",$password_with_salt);
	return $hash;
}

?>