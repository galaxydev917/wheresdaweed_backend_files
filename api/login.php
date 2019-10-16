<?php
header('Access-Control-Allow-Origin: *');

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    if (!isset($_POST["email"]) || !isset($_POST["password"])){
        $result=array("status"=>404,"msg"=>"Invalid request (parameter missing)");
        goto output;
    }
    require_once('data/config.php');
    openDbConnection();
    $email=$_POST["email"];
    $password = $_POST["password"];
    $user=R::getRow("SELECT * FROM user WHERE email=? AND password=?",array($email,$password));
    if ($user!=NULL){
        $result=array("status"=>200,"user"=>$user);
    }else{
        $result=array("status"=>404,"msg"=>"Invalid email/password");
    }
}else{
    $result=array("status"=>405,"msg"=>"Method not allowed");
}
output:
echo json_encode($result);
exit();
?>