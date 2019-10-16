<?php
session_start();

$error_msg="";
$username="";

require_once('data/config.php');
openDbConnection();

if ($_SERVER["REQUEST_METHOD"]=="POST"){
	$username=$_POST["username"];
	$password=$_POST["password"];

	$query="SELECT * FROM `admin` WHERE username=? AND password=?";
	$row=R::getRow($query,array($username,$password));
	if ($row==FALSE){
		$error_msg="Invalid username/password";
	}else{
		$row["user_type"]="admin";
		$_SESSION["admin"]=$row;
		header("Location: index.php");
		exit();
	}
}
?>

<html>
	<head>
		<title>Login</title>
		<?php include "common_header_files_nl.php"; ?>
	</head>
	<body>
		<div class="col-md-4 col-xs-12 col-sm-9 border-box login-box">
			<div style="margin-top: 10px;width:80%;margin:auto;">
				<img src="images/logo.png" style="width:100%;" />
			</div>
			<div style="margin-top: 50px;">
				<form method="POST">
					<input type="hidden" value="1" name="" />
					<div class="form-group">
						<?php if ($error_msg!=""){ ?>
						<div class="error-msg" style="margin-bottom: 5px;"><?php echo $error_msg; ?></div>
						<?php } ?>
						<div class="input-group">
  							<span class="input-group-addon" id="basic-addon1">@</span>
  							<input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo $username;?>">
						</div>
						<div class="input-group">
  							<span class="input-group-addon" id="basic-addon1">@</span>
  							<input type="password" class="form-control" placeholder="Password" name="password" value="<?php //echo $username;?>">
						</div>
					</div>
					<div style="margin-top: 30px;">
						<button type="submit" class="btn btn-primary btn-common">Login</button>
					</div>
				</form>
			</div>
		</div>
		<?php include "footer.php"; ?>
	</body>
</html>