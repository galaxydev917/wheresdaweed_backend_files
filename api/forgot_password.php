<?php
header('Access-Control-Allow-Origin: *');

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    if (!isset($_POST["email"])){
        $result=array("status"=>404,"msg"=>"Invalid request (parameter missing)");
        goto output;
    }
    require_once('data/config.php');
    openDbConnection();
    $email=$_POST["email"];
    $user=R::getRow("SELECT * FROM user WHERE email=?",array($email));
    if ($user!=NULL){
        $result=array("status"=>200);
    }else{
        $result=array("status"=>404,"msg"=>"Invalid email address");
    }
}else{
    $result=array("status"=>405,"msg"=>"Method not allowed");
}
output:
echo json_encode($result);
exit();
?>