<?php
header('Access-Control-Allow-Origin: *');

require_once('data/config.php');
openDbConnection();

$user_id=isset($_GET["user_id"])?$_GET["user_id"]:0;
if ($user_id==0){
    $result=array("status"=>403,"msg"=>"Please login to view the stores");
    goto output;
}

$stores=R::getAll("SELECT * FROM weedstore ORDER BY store_name");
$states=R::getAll("SELECT * FROM weedstate ORDER BY state_name");

/*$stores[]=array("id"=>1,"store_name"=>"Test store1","store_phone"=>"+923228261068","is_claimed"=>"0","image_url"=>"https://www.gstatic.com/webp/gallery/4.jpg","state"=>"CA");
$stores[]=array("id"=>2,"store_name"=>"Test store2","store_phone"=>"+923228261067","is_claimed"=>"1","image_url"=>"https://www.gstatic.com/webp/gallery/5.jpg","state"=>"CA");
$stores[]=array("id"=>3,"store_name"=>"Test store3","store_phone"=>"+923228261066","is_claimed"=>"0","image_url"=>"https://www.gstatic.com/webp/gallery/3.jpg","state"=>"AL");*/

$result=array("status"=>200,"stores"=>$stores,"states"=>$states);
goto output;

output:
echo json_encode($result);
exit();
?>