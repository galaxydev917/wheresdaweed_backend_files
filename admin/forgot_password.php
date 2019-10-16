<?php

function generate_password_reset_code($seedVal){
	$initVal=md5($seedVal);
	$initVal.=time();
	$finalVal=md5($initVal);
	return $finalVal;
}

session_start();

$error_msg="";
$username="";

require_once('data/config.php');
include_once "email.php";
openDbConnection();

if ($_SERVER["REQUEST_METHOD"]=="POST"){
	$username=$_POST["username"];
	
	$query="SELECT * FROM user WHERE email=?";
	$user=R::getRow($query,array($username));
	if ($user==FALSE){
		$error_msg="Email not registered";
	}else{
		$fp=R::dispense('resetpassword');
		$fp->code=generate_password_reset_code($user['id'].''.$user['email']);
		$fp->request_dt=date('Y-m-d H:i:s');
		$fp->user_id=$user['id'];
		R::store($fp);

		$configuration=R::load('configuration',1);

		$emUrl = "http".(!empty($_SERVER['HTTPS'])?"s":"").
        "://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?"":(":".$_SERVER['SERVER_PORT'])).$_SERVER['REQUEST_URI'];
        $codeUrl=dirname($emUrl)."/reset_password.php?code=".$fp->code;

		$emailBody="<img src='http://quality.diversifiedcustodial.com/images/logo.png' style='width:180px;' />";
		$emailBody.="<br /><br />";
		$emailBody.="Dear ".$user['name'].",<br /><br />";
		$emailBody.="Please click on the link below to reset your password:<br />";
		$emailBody.="<a href='".$codeUrl."'>".$codeUrl."</a>";
		$emailBody.="<br /><br />";
		$emailBody.="Thank you,<br />";
		$emailBody.="DCM Quality Team";

		email_send($user['email'],"Your password reset request",$emailBody,$configuration->sender_email);

		echo "<script type='text/javascript'>";
		echo "alert('Link to reset your password is sent to your email address');";
		echo "window.location='index.php';";
		echo "</script>";
	}
}
?>

<html>
	<head>
		<title>Forgot Password</title>
		
		<?php include "common_header_files_nl.php"; ?>
	</head>
	<body>
		<div class="col-md-4 col-xs-12 col-sm-9 border-box login-box">
			<div style="margin-top: 10px;width:80%;margin:auto;">
				<img src="images/logo.png" style="width:100%;" />
			</div>
			<div style="margin-top: 50px;">
				<form method="POST">
					<div class="form-group">
						<?php if ($error_msg!=""){ ?>
						<div class="error-msg" style="margin-bottom: 5px;"><?php echo $error_msg; ?></div>
						<?php } ?>
						<div class="input-group">
  							<span class="input-group-addon" id="basic-addon1">@</span>
  							<input type="email" class="form-control" placeholder="Email" name="username" value="<?php echo $username;?>">
						</div>
					</div>
					<div style="margin-top: 30px;">
						<button type="submit" class="btn btn-primary btn-common">Submit</button>
					</div>
				</form>
			</div>
		</div>
		<?php include "footer.php"; ?>
	</body>
</html>