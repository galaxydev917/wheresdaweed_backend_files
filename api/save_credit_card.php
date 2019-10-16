<?php
header('Access-Control-Allow-Origin: *');

try{
if ($_SERVER["REQUEST_METHOD"]=="POST"){   
    require_once('data/config.php');
    openDbConnection();
    /*$user_id=$_POST["user_id"];
    $latitude=$_POST["latitude"];
    $longitude=$_POST["longitude"];

    $user=R::getRow("SELECT id FROM user WHERE id=?",array($user_id));
    if ($user==false){
        $result=array("status"=>404,"msg"=>"Invalid user");
        goto output;
    }
    
    $user=R::load('user',$user_id);
    $user->last_latitude=$latitude;
    $user->last_longitude=$longitude;
    R::store($user);*/
    $result=array("status"=>200);
}else{
    $result=array("status"=>405,"msg"=>"Method not allowed");
}
}catch(Exception $ex){
    $result=array("status"=>500,"msg"=>$ex->getMessage());
}
output:
echo json_encode($result);
exit();
?>