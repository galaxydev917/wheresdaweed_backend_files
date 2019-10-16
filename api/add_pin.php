<?php
header('Access-Control-Allow-Origin: *');

if ($_SERVER["REQUEST_METHOD"]=="POST"){   
    
    require_once('data/config.php');
    openDbConnection();
    include "function.php";

    $user_id=$_POST["user_id"];
    $name=$_POST["name"];
    $description=$_POST["description"];
    $image_url=$_POST["image_url"];
    $category=$_POST["category"];
    $place_name=$_POST["place_name"];
    $latitude=$_POST["latitude"];
    $longitude=$_POST["longitude"];
    $event_start_dt=$_POST["event_start_dt"];
    
    $post=R::dispense('pin');
    $post->user_id=$user_id;
    $post->name=$name;
    $post->description=$description;
    $post->image_url=$image_url;
    $post->place_name=$place_name;
    $post->category=$category;
    $post->pin_latitude=$latitude;
    $post->pin_longitude=$longitude;
    $post->event_start_dt=$event_start_dt;
    $pstId=R::store($post);

    $post=R::getRow("SELECT * FROM pin WHERE id=?",array($pstId));
    
    $result=array("status"=>200,"pin"=>$post);
}else{
    $result=array("status"=>404,"msg"=>"Method not allowed");
}
output:
echo json_encode($result);
exit();
?>