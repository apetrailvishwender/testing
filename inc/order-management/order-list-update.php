<?php
include('../../constants.php');
include('../../db.php');
$orders_table = "orders";	
$drivers_table = "drivers";	
$users_sql = "select * from ".$orders_table." id DESC";
$users_records = $conn->query($users_sql);

$info = isset($_GET['info']) ? $_GET['info'] : '';
if ( !empty( $info ) ) {
	if($info="edit") {
		$editlid = $_GET["eid"];
		if ( !empty( $editlid ) ) { 
			$selected_order_sql = "select * from ".$orders_table." where id = ".$editlid." ";
			$selected_order_records = $conn->query($selected_order_sql);
			
			if ( $selected_order_records->num_rows > 0 ) {
				while($selected_order_record = $selected_order_records->fetch_assoc()) {	
				
				?>
				<div class="wrap"> 
					<h2>Edit Order Details</h2>
					<br/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css"/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/bootstrap.css"/>
					 <script src="<?php echo APP_URL; ?>/js/jquery-1.11.3.min.js"></script>
					 <script src="<?php echo APP_URL; ?>/js/bootstrap.min.js"></script>
					 <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.js"></script>
					 <form role="form" action="order-list-func.php" method="post" id="editorderform">
					  <div class="form-group">
					    <label for="editid">ID:</label>
					    <input type="text" class="form-control" id="editorderid" name="editorderid" readonly value="<?php echo $selected_order_record['id']; ?>">
					  </div>
					  <div class="form-group">
					    <label for="editordername">Order Number:</label>
					    <input type="text" class="form-control" id="editordername" name="editordername" readonly value="<?php echo $selected_order_record['order_id']; ?>">
					  </div>
					  <div class="form-group">
					  <label for="ordereditdrivers">Select Driver:</label>
						  <select class="form-control" id="ordereditdriver" name="ordereditdriver">
						    <option id="0" value="0">Select</option>
						  	<?php
						  	$orderdriverid 			  = $selected_order_record['order_driver_id'];
						  	$all_drivers_sql 		  = "select * from ".$drivers_table." where driver_is_deleted='0' ";
							$all_driver_records 	  = $conn->query($all_drivers_sql);
							if ( $all_driver_records->num_rows > 0 ) {
								while($all_driver_record = $all_driver_records->fetch_assoc()) {
									 if($all_driver_record['driver_id'] == $orderdriverid) {
									 	$selected_driver = "selected=selected";
									 } else {
									 	$selected_driver = "";
									 }
									 echo "<option id=".$all_driver_record['driver_id']." ".$selected_driver." value=".$all_driver_record['driver_id'].">".$all_driver_record['driver_name']."</option>";
								}	
							}
							?>
						  </select>
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
		 $("#editorderform").validate();
    });		 
</script>