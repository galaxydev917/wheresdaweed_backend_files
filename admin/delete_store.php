<?php
session_start();
if (!isset($_SESSION["admin"])){
	header("Location: login.php");
	exit();
}
if (!isset($_GET["id"])){
	header("Location: stores.php");
	exit();
}

$redirectUrl="stores.php";

if (isset($_GET["redirect"])){
	$redirectUrl=$_GET["redirect"];
}else{
	$redirectUrl=$_SERVER['HTTP_REFERER'];
}

$id=$_GET["id"];
require_once('data/config.php');
openDbConnection();
$query="DELETE FROM weedstore WHERE id=".$id;
R::exec($query,array());
header("Location: ".$redirectUrl);
exit();
?>