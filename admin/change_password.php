<?php 
$title="Change Password";
session_start();
if(!isset($_SESSION['admin'])){
	header("location:login.php");
	exit();
}
$user=$_SESSION["admin"];
require_once('data/config.php');
openDbConnection();
if($_SERVER["REQUEST_METHOD"]=="POST"){
  	$password = $_POST['login_pass'];
  	$password2 = $_POST['login_pass2'];
  	if ($password!=$password2){
    	echo "<script>alert('Passwords not matched');</script>";
  	}else{
      R::exec("UPDATE `admin` SET password=? WHERE id=?",array($password,$user['id']));
      echo "<script>alert('Password changed successfully');window.location='index.php';</script>";
  	}
}
closeDbConnection();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Change Password</title>
		<?php include "common_header_files.php"; ?>

		<style type="text/css">
			.dataTables_info {
				display:none;
			}
		</style>
	</head>
	<body>
		<div class="top-bar-container">
			<div class="top-bar-logo-container">
				<img src="images/logo.png" style="width:100%;" />
			</div>
		</div>
		<div class="top-menu-container">
			<?php include "top_menu.php"; ?>
		</div>
		
		<div class="col-md-6">
			<div class="action-heading">Change Password</div>
			<form method="POST">
          <div class="form-group">
						<div class="input-group">
  							<span class="input-group-addon" id="basic-addon1">@</span>
  							<input type="password" class="form-control" placeholder="New Password" name="login_pass">
						</div>
						<div class="input-group">
  							<span class="input-group-addon" id="basic-addon1">@</span>
  							<input type="password" class="form-control" placeholder="Confirm Password" name="login_pass2">
						</div>
					</div>
					<div style="margin-top: 30px;">
						<button type="submit" class="btn btn-primary btn-common">Update password</button>
					</div>
      </form>
		</div>
		<?php include "footer.php"; ?>
	</body>
</html>