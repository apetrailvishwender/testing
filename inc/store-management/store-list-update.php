<?php
echo "hii";
include('../../constants.php');
include('../../db.php');
$stores_table = "stores";	
$zones_table = "zones";	
$users_sql = "select * from ".$stores_table." where store_is_deleted='0' order by store_id DESC";
$users_records = $conn->query($users_sql);

$info = isset($_GET['info']) ? $_GET['info'] : '';
if ( !empty( $info ) ) {

	if ( $info=="del" ) {
		$delid = $_GET["did"];
		if ( !empty( $delid ) ) {
			
			$sql = "update ".$stores_table." set store_is_deleted='1' where store_id=".$delid." ";

			if ($conn->query($sql) === TRUE) {

			   echo "<script>alert('Record Deleted !!');</script>";
			   echo "<script>window.location = '".HOME_URL."'</script>";
			  
			} else {
				echo "<script>alert('Error while Deleting Record !!');</script>";
				echo "<script>window.location = '".HOME_URL."'</script>";
			}

		}
	}
	if($info == "delall") {
		if(isset($_POST['delete'])) {
			$delete_id = $_POST['usercodeid'];
			$id = count($delete_id );
			if (count($id) > 0) {
				if(!empty($delete_id)) {
					foreach ($delete_id as $id_d) {
						$delete = "update ".$stores_table." set store_is_deleted='1' where store_id=".$id_d." ";
						$conn->query($delete);
					}
				} else {
					echo "<div style='clear:both;'></div><div class='updated' id='message'><p><strong>".__('No records selected.')."</strong></p></div>";
				}
				
			}
			if($delete) {
				echo "<script>alert('Selected Records Deleted !!');</script>";
				echo "<script>window.location = '".HOME_URL."'</script>";
			}
		}
	}
	if($info="edit") {
		$editlid = $_GET["eid"];
		if ( !empty( $editlid ) ) { 
			$selected_store_sql = "select * from ".$stores_table." where store_id = ".$editlid." ";
			$selected_store_records = $conn->query($selected_store_sql);
			
			if ( $selected_store_records->num_rows > 0 ) {
				while($selected_store_record = $selected_store_records->fetch_assoc()) {	
					$storezoneid = $selected_store_record['store_zone_id'];
					$zones_sql 		  = "select zone_name from ".$zones_table." where zone_id = ".$storezoneid." ";
					$zone_records 	  = $conn->query($zones_sql);
					if ( $zone_records->num_rows > 0 ) {
						while($zone_record = $zone_records->fetch_assoc()) {
							$zone_name = $zone_record['zone_name'];
						}	
					}
				?>
				<div class="wrap"> 
					<h2>Edit Store Details</h2>
					<br/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css"/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/bootstrap.css"/>
					 <script src="<?php echo APP_URL; ?>/js/jquery-1.11.3.min.js"></script>
					 <script src="<?php echo APP_URL; ?>/js/bootstrap.min.js"></script>
					 <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.js"></script>
					 <form role="form" action="store-list-func.php" method="post" id="editstoreform">
					  <div class="form-group">
					    <label for="editid">ID:</label>
					    <input type="text" class="form-control" id="editstoreid" name="editstoreid" readonly value="<?php echo $selected_store_record['store_id']; ?>">
					  </div>
					  <div class="form-group">
					    <label for="editstorename">Store Name:</label>
					    <input type="text" class="form-control" id="editstorename" name="editstorename" value="<?php echo $selected_store_record['store_name']; ?>">
					  </div>
					   <div class="form-group">
					    <label for="editstorename">Address:</label>
					    <textarea rows="4" class="form-control required" id="editstoreaddr" name="editstoreaddr"><?php echo $selected_store_record['store_address']; ?></textarea>
					  </div>
					  <div class="form-group">
					    <label for="editstorename">City:</label>
					    <input type="text" class="form-control" id="editstorecity" name="editstorecity" value="<?php echo $selected_store_record['store_city']; ?>">
					  </div>
					   <div class="form-group">
					    <label for="editstorename">State:</label>
					    <input type="text" class="form-control" id="editstorestate" name="editstorestate" value="<?php echo $selected_store_record['store_state']; ?>">
					   </div>
					   <div class="form-group">
					    <label for="editstorename">Country:</label>
					    <input type="text" class="form-control" id="editstorecountry" name="editstorecountry" value="<?php echo $selected_store_record['store_country']; ?>">
					   </div>
					   <div class="form-group">
					    <label for="editstorename">Pincode:</label>
					    <input type="text" class="form-control required" id="editstorepincode" name="editstorepincode" value="<?php echo $selected_store_record['store_postalcode']; ?>">
					   </div> 
					  <!--<div class="form-group">
					  <label for="storeeditzone">Select Zone:</label>
						  <select class="form-control" id="storeeditzone" name="storeeditzone">
						    <option id="0" value="0">Select</option>
						  	<?php
						  	$all_zones_sql 		  = "select * from ".$zones_table." where zone_is_deleted='0' ";
							$all_zone_records 	  = $conn->query($all_zones_sql);
							if ( $all_zone_records->num_rows > 0 ) {
								while($all_zone_record = $all_zone_records->fetch_assoc()) {
									 if($all_zone_record['zone_id'] == $storezoneid) {
									 	$selected_zone = "selected=selected";
									 } else {
									 	$selected_zone = "";
									 }
									 echo "<option id=".$all_zone_record['zone_id']." ".$selected_zone." value=".$all_zone_record['zone_id'].">".$all_zone_record['zone_name']."</option>";
								}	
							}
						  	?>
						  </select>
					 </div>-->
					  <button type="submit" class="btn btn-default">Update</button>
					</form>
				</div>
				<?php
				}	
			}
		 }
	}
}	
?>
<script>
	jQuery(document).ready(function() {
		 $("#editstoreform").validate();
	});
</script>