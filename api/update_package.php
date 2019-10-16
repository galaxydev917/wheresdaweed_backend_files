<?php
header('Access-Control-Allow-Origin: *');

if ($_SERVER["REQUEST_METHOD"]=="POST"){   
    require_once('data/config.php');
    openDbConnection();
    $user_id=$_POST["user_id"];
    $package_id=$_POST["package_id"];
    $user=R::getRow("SELECT id FROM user WHERE id=?",array($user_id));
    if ($user==false){
        $result=array("status"=>404,"msg"=>"Invalid user");
        goto output;
    }
    $user=R::load('user',$user_id);
    $user->user_package=$package_id;
    R::store($user);
    $user=R::getRow("SELECT * FROM user WHERE id=?",array($user_id));
    $result=array("status"=>200,"user"=>$user,"package"=>$package_id);
}else{
    $result=array("status"=>405,"msg"=>"Method not allowed");
}
output:
echo json_encode($result);
exit();
?>