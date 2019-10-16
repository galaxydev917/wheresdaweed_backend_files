<?php
session_start();
if (!isset($_SESSION["admin"])){
	header("Location: login.php");
	exit();
}

require_once('data/config.php');
openDbConnection();

$startPage="0";
$pageLen="50";
$searchStr="";
$displayStart=0;
$orderArray=array(array(0,"asc"));

if ($_SERVER["REQUEST_METHOD"]=="POST"){

}else{
	$startPage=isset($_GET["page"])?$_GET["page"]:"0";
	$pageLen=isset($_GET["page_len"])?$_GET["page_len"]:"50";
	$searchStr=isset($_GET["search"])?$_GET["search"]:"";
	if (isset($_GET["order"])){
		$orderArray=json_decode($_GET["order"],TRUE);
	}
}

continue_page:

$displayStart=$pageLen*$startPage;

$query="SELECT mi.*,st.store_name FROM user mi";
$query.=" LEFT JOIN weedstore st ON st.owner_user_id=mi.id";
$query.=" ORDER BY mi.name";
$items=R::getAll($query,array());

?>
<html>
	<head>
		<title>Users</title>
		<?php include "common_header_files.php"; ?>

		<style type="text/css">
			.dataTables_info {
				display:none;
			}
		</style>
	</head>
	<body>
		<div class="top-bar-container">
			<div class="top-bar-logo-container">
				<img src="images/logo.png" style="width:100%;" />
			</div>
		</div>
		<div class="top-menu-container">
			<?php include "top_menu.php"; ?>
		</div>
		
		<div class="col-md-12">
			<div class="action-heading">Users</div>
			<table class="table table-bordered table-light" id="tblData">
				<thead>
				<tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered On</th>
					<th>Owned Store</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($items as $item){?><a>
				<tr>
					<td>
						<?php echo $item["name"];?>
					</td>
					<td>
						<?php echo $item["email"];?>
                    </td>
                    <td>
						<?php echo $item["register_dt"];?>
					</td>
					<td>
						<?php echo $item["store_name"]; ?>
					</td>
					<td>
						<a href="#" onclick="return deleteItem(<?php echo $item['id']; ?>);">Delete</a>
					</td>
				</tr>
				<?php }?>
				</tbody>
			</table>
		</div>
		<?php include "footer.php"; ?>

		<script type="text/javascript">
			var tblGrid=null;

			$(document).ready(function() {
				//$('#tblData').DataTable({searching:false,pagingType:'numbers',lengthMenu: [50,100,150,200,250,300,350,400,450,500]});
				tblGrid=$('#tblData').DataTable({searching:true,pagingType:'numbers',
					order: <?php echo json_encode($orderArray); ?>,
					lengthMenu: [50,100,150,200,250,300,350,400,450,500],
					pageLength: <?php echo $pageLen; ?>,
					displayStart: <?php echo $displayStart; ?>,
					search: {search: "<?php echo $searchStr; ?>"}
				});
			});

			function deleteItem(id){
				if (confirm('Are you sure you want to delete?')){
					redUrl="users.php?page="+tblGrid.page()+"&page_len="+tblGrid.page.len();
					redUrl+="&search="+encodeURIComponent(tblGrid.search());
					redUrl+="&order="+encodeURIComponent(JSON.stringify(tblGrid.order()));
					console.log(redUrl);
					loc="delete_user.php?id="+id+"&redirect="+encodeURIComponent(redUrl);
					console.log(loc);
					window.location=loc;
				}
				return false;	
			}

			function editItem(id){
				redUrl="users.php?page="+tblGrid.page()+"&page_len="+tblGrid.page.len();
				redUrl+="&search="+encodeURIComponent(tblGrid.search());
				redUrl+="&order="+encodeURIComponent(JSON.stringify(tblGrid.order()));
				console.log(redUrl);
				loc="edit_user.php?id="+id+"&redirect="+encodeURIComponent(redUrl);
				console.log(loc);
				window.location=loc;
				return false;	
			}

			function validateUserForm(){
				if ($("#txtName").val()==""){
					alert("Please enter name");
					return false;
				}
				if ($("#txtEmail").val()==""){
					alert("Please enter email");
					return false;
				}
				if ($("#txtPassword").val()==""){
					alert("Please enter password");
					return false;
				}
				if ($("#txtCPassword").val()==""){
					alert("Please enter confirm password");
					return false;
				}
				return true;
			}
		</script>
	</body>
</html>