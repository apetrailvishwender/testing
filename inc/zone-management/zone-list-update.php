
<?php
include('../../constants.php');
include('../../db.php');
$zones_table = "zones";	
$users_sql = "select * from ".$zones_table." where zone_is_deleted='0' order by zone_id DESC";
$users_records = $conn->query($users_sql);

$info = isset($_GET['info']) ? $_GET['info'] : '';
if ( !empty( $info ) ) {

	if ( $info=="del" ) {
		$delid = $_GET["did"];
		if ( !empty( $delid ) ) {
			$sql = "update ".$zones_table." set zone_is_deleted='1' where zone_id=".$delid." ";

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
						/*$delete = "delete from ".$zones_table." where id=".$id_d." ";*/
						$delete = "update ".$zones_table." set zone_is_deleted='1' where zone_id=".$id_d." ";
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
			$selected_zone_sql = "select * from ".$zones_table." where zone_id = ".$editlid." ";
			$selected_zone_records = $conn->query($selected_zone_sql);
			
			if ( $selected_zone_records->num_rows > 0 ) {
				while($selected_zone_record = $selected_zone_records->fetch_assoc()) {	
					$zonezoneid = $selected_zone_record['zone_id'];
					$zones_sql 		  = "select zone_name from ".$zones_table." where zone_id = ".$zonezoneid." ";
					$zone_records 	  = $conn->query($zones_sql);
					if ( $zone_records->num_rows > 0 ) {
						while($zone_record = $zone_records->fetch_assoc()) {
							$zone_name = $zone_record['zone_name'];
						}	
					}
				?>
				<div class="wrap"> 
					<h2>Edit Zone Details</h2>
					<br/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css"/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/bootstrap.css"/>
					 <script src="<?php echo APP_URL; ?>/js/jquery-1.11.3.min.js"></script>
					 <script src="<?php echo APP_URL; ?>/js/bootstrap.min.js"></script>
					 <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.js"></script>
					 <form role="form" action="zone-list-func.php" method="post" id="editzoneform">
					  <div class="form-group">
					    <label for="editid">ID:</label>
					    <input type="text" class="form-control required" id="editzoneid" name="editzoneid" readonly value="<?php echo $selected_zone_record['zone_id']; ?>">
					  </div>
					  <div class="form-group">
					    <label for="editzonename">Zone Name:</label>
					    <input type="text" class="form-control required" id="editzonename" name="editzonename" readonly value="<?php echo $selected_zone_record['zone_name']; ?>">
					  </div>
					  <!-- <div class="form-group">
					    <label for="editzonename">Central zip code:</label>
					    <input type="text" class="form-control required" id="editzonectpincode" name="editzonectpincode" readonly value="<?php echo $selected_zone_record['zone_name']; ?>">
					  </div>-->
					  <div class="form-group">
					    <label for="editzonecentralpincode">Central Zipcode:</label>
					    <input type="text" class="form-control required" id="editzonecentralzipcode" name="editzonecentralzipcode" value="<?php echo $selected_zone_record['zone_central_zipcode']; ?>">
					  </div>
					   <div class="form-group">
					    <label for="editzonedlprice">Delivery Price:</label>
					    <input type="text" class="form-control required money" id="editzonedlprice" name="editzonedlprice" value="<?php echo $selected_zone_record['zone_delivery_price']; ?>">
					  </div>
					   <div class="form-group">
					    <label for="editzoneasapdlprice">ASAP Delivery Price:</label>
					    <input type="text" class="form-control required money" id="editzoneasapdlprice" name="editzoneasapdlprice" value="<?php echo $selected_zone_record['zone_urgent_delivery_price']; ?>">
					  </div>
					  
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
		 $("#editzoneform").validate();
		 jQuery.validator.addMethod(
		    "money",
		    function(value, element) {
		        var isValidMoney = /^\d{0,8}(\.\d{0,3})?$/.test(value);
		        return this.optional(element) || isValidMoney;
		    },
		    "Insert proper price."
		);
	});
</script>