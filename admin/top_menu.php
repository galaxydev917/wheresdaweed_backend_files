<?php
$loggedInUser=$_SESSION["admin"];
?>
<a href="index.php">Home</a>

<?php if ($loggedInUser["user_type"]=="admin"){ ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="users.php">Users</a>
<?php } ?>
<?php if ($loggedInUser["user_type"]=="admin"){ ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="stores.php">Stores</a>
<?php } ?>
<?php if (1==2 && $loggedInUser["user_type"]=="admin"){ ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="packages.php">Packages</a>
<?php } ?>
<?php if (1==2 && $loggedInUser["user_type"]=="admin"){ ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="unique_devices.php">Devices</a>
<?php } ?>
<?php if (1==2 && $loggedInUser["user_type"]=="admin"){ ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="verify_requests.php">Verify Requests</a>
<?php } ?>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="change_password.php">Change Password</a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="logout.php">Logout</a>