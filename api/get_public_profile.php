<?php
header('Access-Control-Allow-Origin: *');

require_once('data/config.php');
openDbConnection();

include_once("function.php");

$user_id=isset($_GET["user_id"])?$_GET["user_id"]:0;
$profile_id=isset($_GET["profile_id"])?$_GET["profile_id"]:0;
if ($user_id==0){
    $result=array("status"=>403,"msg"=>"Please login to view profile");
    goto output;
}
if ($profile_id==0){
    $result=array("status"=>405,"msg"=>"Please enter valid profile id");
    goto output;
}

$user=R::getRow("SELECT * FROM user WHERE id=?",array($profile_id));
if ($user==NULL || $user==FALSE){
    $result=array("status"=>403,"msg"=>"User not found");
    goto output;
}

$query="SELECT p.* FROM `weedstore` p WHERE owner_user_id=? ORDER BY store_name";
$nots=R::getAll($query,array($user_id));
$result=array("status"=>200,"my_stores"=>$nots,"user"=>$user);

output:
echo json_encode($result);
exit();
?>