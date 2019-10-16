<?php
header('Access-Control-Allow-Origin: *');

if ($_SERVER["REQUEST_METHOD"]=="POST"){   
    require_once('data/config.php');
    openDbConnection();
    $user_id=$_POST["user_id"];
    $name=$_POST["name"];
    $email=$_POST["email"];
    $new_password=$_POST["new_password"];
    $image_url=$_POST["image_url"];
    if ($image_url==""){
        $fullfilepath="media/default_profile_pic.jpg";
        $emUrl = "http".(!empty($_SERVER['HTTPS'])?"s":"").
        "://".$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?"":(":".$_SERVER['SERVER_PORT'])).$_SERVER['REQUEST_URI'];
        $codeUrl=dirname($emUrl)."/".$fullfilepath;
        $image_url=$codeUrl;
    }

    $user=R::getRow("SELECT id FROM user WHERE id=?",array($user_id));
    if ($user==false){
        $result=array("status"=>404,"msg"=>"Invalid user");
        goto output;
    }
    
    $user=R::load('user',$user_id);
    $user->name=$name;
    $user->email=$email;
    $user->profile_pic_url=$image_url;
    if ($new_password!=""){
        $user->password=$new_password;
    }
    R::store($user);
    $user=R::getRow("SELECT * FROM user WHERE id=?",array($user_id));
    $result=array("status"=>200,"user"=>$user);
}else{
    $result=array("status"=>405,"msg"=>"Method not allowed");
}
output:
echo json_encode($result);
exit();
?>