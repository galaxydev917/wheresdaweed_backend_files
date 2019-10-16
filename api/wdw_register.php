<?php
header('Access-Control-Allow-Origin: *');

try{
if ($_SERVER["REQUEST_METHOD"]=="POST"){   
    if (!isset($_POST["email"]) || !isset($_POST["password"])){
        $result=array("status"=>404,"msg"=>"Invalid request (parameter missing)");
        goto output;
    }
    require_once('data/config.php');
    openDbConnection();
    $name=$_POST["name"];
    $email=$_POST["email"];
    $password = $_POST["password"];
    $image_url=$_POST["image_url"];
    $facebook_profile_id=$_POST["facebook_profile_id"];
    if ($image_url==""){
        $fullfilepath="media/default_profile_pic.jpg";
        $emUrl = "http".(!empty($_SERVER['HTTPS'])?"s":"").
        "://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?"":(":".$_SERVER['SERVER_PORT'])).$_SERVER['REQUEST_URI'];
        $codeUrl=dirname($emUrl)."/".$fullfilepath;
        $image_url=$codeUrl;
    }

    $user=R::findOne('user','email=?',array($email));
    if ($user!=NULL && $facebook_profile_id==""){
        $result=array("status"=>404,"msg"=>"Email already registered");
        goto output;
    }else if ($user==NULL){
        $user=R::dispense('user');
        $user->password="";
    }
    $user->name=$name;
    $user->email=$email;
    if ($facebook_profile_id==""){
        $user->password=$password;
    }
    $user->register_dt=date('Y-m-d H:i:s');
    $user->profile_pic_url=$image_url;
    R::store($user);

    $user=R::getRow("SELECT * FROM user WHERE email=?",array($email));
    $result=array("status"=>200,"user"=>$user);
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