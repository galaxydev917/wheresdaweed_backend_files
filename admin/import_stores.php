<?php
set_time_limit(0);
session_start();
if (!isset($_SESSION["admin"])){
	header("Location: login.php");
	exit();
}

require_once('data/config.php');
openDbConnection();

$states=R::getAll("SELECT * FROM weedstate");

$imported_stores_count=0;
$not_imported_stores=array();

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    if (isset($_FILES["media_file"]) && $_FILES["media_file"]["size"]>0){
        $filename=$_FILES["media_file"]["name"];
        $fullfilepath="stores_csv_".time();
        $vals=explode(".",$filename);
        $file_ext="csv";
        if (count($vals)>1){
            $file_ext=$vals[count($vals)-1];
        }
        $fullfilepath.=".".$file_ext;
        move_uploaded_file($_FILES["media_file"]["tmp_name"],$fullfilepath);
        $row = 1;
        $last_state="";
        if (($handle = fopen($fullfilepath, "r")) !== FALSE) {
            $all_stores=R::getAll("SELECT * FROM weedstore");
            while (($data = fgetcsv($handle,2000,",")) !== FALSE) {
                if ($row==1){
                    $row++;
                    continue;
                }
                try{
                    $state=trim($data[0]);
                    $name=trim($data[1]);
                    $address=trim($data[2]);
                    $phone=trim($data[3]);
                    $phone=trim(str_replace(" ","",$phone));
                    if ($phone!=""){
                        if ($phone[0]!='+'){
                            $phone="+".$phone;
                        }
                    }
                    if ($state==""){
                        $state=$last_state;
                    }else{
                        $last_state=$state;
                    }

                    $state_found=false;
                    foreach ($states as $st){
                        if (strtolower($st['state_name'])==strtolower($state)){
                            $state_found=true;
                            break;
                        }
                    }
                    if (!$state_found){
                        $ns=R::dispense('weedstate');
                        $ns->state_name=$state;
                        $nsid=R::store($ns);
                        $states[]=array("id"=>$nsid,"state_name"=>$state);
                    }

                    $store_found=false;
                    foreach ($all_stores as $as){
                        if (strtolower($as["store_name"])==strtolower($name)
                            && strtolower($as["state"])==strtolower($state)
                            && strtolower($as["store_address"])==strtolower($address)){
                            $store_found=true;
                            break;
                        }
                    }
                    if ($store_found){
                        continue;
                    }

                    //$image_url=json_encode(array("https://www.gstatic.com/webp/gallery/4.jpg"));
                    $image_url=json_encode(array("http://jadedss.com/wheresdaweed/api/media/default_store_pic.png"));
                
                    $store=R::dispense('weedstore');
                    $store->store_name=$name;
                    $store->store_phone=$phone;
                    $store->store_address=$address;
                    $store->state=$state;
                    $store->image_url=$image_url;
                    $sid=R::store($store);
                    $imported_stores_count++;

                    $all_stores[]=array("store_name"=>$name,"state"=>$state,"store_address"=>$address,"store_phone"=>$phone);

                }catch(SQL $ex){
                    $not_imported_stores[]=array("name"=>$name,"reason"=>$ex->getMessage());
                }catch(RedException $ex){
                    $not_imported_stores[]=array("name"=>$name,"reason"=>$ex->getMessage());
                }catch(Exception $ex){
                    $not_imported_stores[]=array("name"=>$name,"reason"=>$ex->getMessage());
                }
            }
            fclose($handle);
        }

        unlink($fullfilepath);
    }
}

?>

<html>
    <head>
        <title>Import Stores</title>
		<?php include "common_header_files.php"; ?>

		<style type="text/css">
			.action-add-new {
                margin-top: 10px;
                margin-bottom: 20px;
            }
		</style>
    </head>
    <body>
        <?php include "top_header.php"; ?>
		<div class="top-menu-container">
			<?php include "top_menu.php"; ?>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-9 col-xs-12">
        <div class="action-heading">
            Import Stores
        </div>
        <div style="margin-top: 10px;">
            <?php echo $imported_stores_count." stores imported"; ?>
        </div>
        <div style="margin-top: 15px;">
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="media_file" />
                <div style="margin-top: 10px;">
                    <input type="submit" value="Import" />
                </div>
            </form>
        </div>
        <?php if (count($not_imported_stores)>0){ ?>
        <div style="margin-top: 15px;">
            <h3>Import Errors</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Store</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($not_imported_stores as $ni) { ?>
                    <tr>
                        <td><?php echo $ni["name"]; ?></td>
                        <td><?php echo $ni["reason"]; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
        </div>
        <?php include "footer.php"; ?>
    </body>
</html>