<?php
header('Access-Control-Allow-Origin: *');

if ($_SERVER["REQUEST_METHOD"]=="POST"){   
    require_once('data/config.php');
    openDbConnection();
    $user_id=$_POST["user_id"];
    $web_url=$_POST["web_url"];
    $description=$_POST["description"];
    $store_id=$_POST["store_id"];
    $delivery=$_POST["delivery"];
    $delivery_timings=$_POST["delivery_timings"];
    $store_timings=$_POST["store_timings"];
    $image_url=json_decode($_POST["image_url"],true);
    $store=R::getRow("SELECT id FROM weedstore WHERE id=?",array($store_id));
    if ($store==false){
        $result=array("status"=>404,"msg"=>"Invalid store");
        goto output;
    }

    $default_image_url="http://jadedss.com/wheresdaweed/api/media/default_store_pic.png";
    if (count($image_url)<=0){
        $image_url=json_encode(array($default_image_url));
    }else{
        $image_url=$_POST["image_url"];
    }

    $store=R::load('weedstore',$store_id);
    $store->is_claimed=1;
    $store->owner_user_id=$user_id;
    $store->web_url=$web_url;
    $store->store_description=$description;
    if ($delivery=="true"){
        $store->delivery=1;
    }else{
        $store->delivery=0;
    }
    $store->store_timings=$store_timings;
    $store->delivery_timings=$delivery_timings;
    $store->image_url=$image_url;
    R::store($store);
    $store=R::getRow("SELECT * FROM weedstore WHERE id=?",array($store_id));
    $result=array("status"=>200,"store"=>$store);
}else{
    $result=array("status"=>405,"msg"=>"Method not allowed");
}
output:
echo json_encode($result);
exit();
?>