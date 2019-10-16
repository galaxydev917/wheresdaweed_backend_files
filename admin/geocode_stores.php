<?php 
// function to geocode address, it will return false if unable to geocode address
function geocode($store,$address){
    echo "Geocoding ".$store['store_name']."<br />";
    // url encode the address
    $address = urlencode($address);
     
    // google map geocode api url
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyCFOiN9JZFxc1K5wNbugBlqaYBw1VZpu0E";
 
    // get the json response
    $resp_json = file_get_contents($url);
     
    // decode the json
    $resp = json_decode($resp_json, true);
 
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
        $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
        $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
            return $data_arr;
        }else{
            return false;
        }    
    }else{
        echo "<strong>ERROR: {$resp['status']}</strong><br />";
        return false;
    }
}

set_time_limit(0);
session_start();
if (!isset($_SESSION["admin"])){
	header("Location: login.php");
	exit();
}

require_once('data/config.php');
openDbConnection();

$stores=R::getAll("SELECT id,store_name,store_address FROM weedstore WHERE store_address IS NOT NULL AND store_address<>'' AND (store_latitude IS NULL OR store_longitude IS NULL) LIMIT 0,3000");
foreach ($stores as $st){
    $resp=geocode($st,$st['store_address']);
    if ($resp!==false){
        //print_r($resp);
        //exit();
        R::exec("UPDATE weedstore SET store_latitude=?,store_longitude=? WHERE id=?",array($resp[0],$resp[1],$st['id']));
    }
}

?>