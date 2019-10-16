<?php
session_start();
if (!isset($_SESSION["admin"])){
	header("Location: login.php");
	exit();
}

$loggedInUser=$_SESSION["admin"];

include "data/config.php";
include "common.php";
openDbConnection();


?>

<html>
	<head>
		<title>DCM Admin</title>
		
		<?php include "common_header_files.php"; ?>
	</head>

	<body>
		<?php include "top_header.php"; ?>
		<div class="top-menu-container">
			<?php include "top_menu.php"; ?>
		</div>
		
		<?php include "footer.php"; ?>
	</body>

</html>