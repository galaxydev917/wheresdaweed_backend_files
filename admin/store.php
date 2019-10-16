<?php
session_start();
if (!isset($_SESSION["admin"])){
	header("Location: login.php");
	exit();
}
require_once('data/config.php');
openDbConnection();

$id=isset($_GET["id"])?$_GET["id"]:"0";
$store=R::load('weedstore',$id);

if ($_SERVER["REQUEST_METHOD"]=="POST"){
	$store_name=$_POST["store_name"];
	$store_phone=$_POST["store_phone"];
    $state=$_POST["state"];
    $store_address=$_POST['store_address'];
    $store_latitude=$_POST["store_latitude"];
    $store_longitude=$_POST["store_longitude"];
    $image_url=json_encode(array("http://jadedss.com/wheresdaweed/api/media/default_store_pic.png"));
    
    $store->store_name=$store_name;
    $store->store_phone=$store_phone;
    $store->store_address=$store_address;
    $store->store_latitude=$store_latitude;
    $store->store_longitude=$store_longitude;
    $store->state=$state;
    $store->image_url=$image_url;
    $sid=R::store($store);

    header("Location: stores.php");
    exit();
}

$loggedInUser=$_SESSION["admin"];
?>
<html>
	<head>
		<title>
            <?php
            if (isset($_GET["id"])){
                echo "Edit Store";
            }else{
                echo "Add New Store";
            }
            ?>
        </title>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCFOiN9JZFxc1K5wNbugBlqaYBw1VZpu0E&libraries=places"></script>

		<?php include "common_header_files.php"; ?>

		<style type="text/css">
			.dataTables_info {
				display:none;
			}
		</style>
	</head>
	<body>
		<?php include "top_header.php"; ?>
		<div class="top-menu-container">
			<?php include "top_menu.php"; ?>
		</div>
		<?php if (strtolower($loggedInUser["user_type"])=="admin"){ ?>
		<div class="col-lg-4 col-md-6 col-sm-9 col-xs-12">
			<div class="action-heading">
                <?php
                if (isset($_GET["id"])){
                    echo "Edit Store";
                }else{
                    echo "Add New Store";
                }
                ?>
			</div>
			<div>
				<form method="POST">
					<div class="form-group">
                        <input type="text" class="form-control" value="<?php echo $store->store_name; ?>" id="txtStoreName" placeholder="Store name" name="store_name" />
                        <input type="text" class="form-control" value="<?php echo $store->store_phone; ?>" id="txtStorePhone" placeholder="Store phone#" name="store_phone" />
                        <input type="text" class="form-control" value="<?php echo $store->store_address; ?>" id="txtStoreAddress" placeholder="Store Address" name="store_address" />
                        <input type="text" class="form-control" value="<?php echo $store->store_latitude; ?>" id="txtStoreLatitude" placeholder="Latitude" name="store_latitude" />
                        <input type="text" class="form-control" value="<?php echo $store->store_longitude; ?>" id="txtStoreLongitude" placeholder="Longitude" name="store_longitude" />
                        <input type="text" class="form-control" value="<?php echo $store->state; ?>" id="txtState" placeholder="State" name="state" />
					</div>
					<button type="submit" onclick="return validateForm();" class="btn btn-primary btn-save">Save Store</button>
				</form>
			</div>
		</div>
		<hr style="width: 98%;" />
		<?php } ?>
		<?php include "footer.php"; ?>

        <script type="text/javascript">

            function validateForm(){
                if ($("#txtStoreName").val()==""){
                    alert("Please enter store name");
                    return false;
                }
                if ($("#txtStorePhone").val()==""){
                    alert("Please enter store phone");
                    return false;
                }
                if ($("#txtStoreAddress").val()==""){
                    alert("Please enter store address");
                    return false;
                }
                if ($("#txtStoreLatitude").val()==""){
                    alert("Please enter store latitude");
                    return false;
                }
                if ($("#txtStoreLongitude").val()==""){
                    alert("Please enter store longitude");
                    return false;
                }
                if ($("#txtState").val()==""){
                    alert("Please enter state");
                    return false;
                }

                return true;
            }
            

			var input = document.getElementById('txtStoreAddress');
			var autocomplete = new google.maps.places.Autocomplete(input);
			autocomplete.addListener('place_changed', function() {
          		let place = autocomplete.getPlace();
          		if (!place.geometry) {
            		// User entered the name of a Place that was not suggested and
            		// pressed the Enter key, or the Place Details request failed.
            		window.alert("No details available for input: '" + place.name + "'");
            		return;
                }  
                let latitude=place.geometry.location.lat();
                let longitude=place.geometry.location.lng();
                $("#txtStoreLatitude").val(latitude);
                $("#txtStoreLongitude").val(longitude);
        	});
		</script>

	</body>
</html>