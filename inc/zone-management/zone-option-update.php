<?php
include('../../constants.php');
include('../../db.php');
$stores_table = "stores";	
$zones_table = "zones";	
$zone_detail_table="zone_detail";

$info = isset($_GET['info']) ? $_GET['info'] : '';
if ( !empty( $info ) ) {

if($info == "delall") {
	
		if(isset($_POST['delete'])) {
			$delete_id = $_POST['zoneid'];
			$id = count($delete_id );
			if (count($id) > 0) {
				if(!empty($delete_id)) {
					foreach ($delete_id as $id_d) {
						//$delete = "delete from ".$zone_detail_table." where id=".$id_d;
						$delete = "delete from ".$zone_detail_table." where id=".$id_d." ";
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
	if ( $info=="del" ) {
		$delid = $_GET["did"];
		if ( !empty( $delid ) ) {
			
			$sql = "delete from ".$zone_detail_table." where id=".$delid." ";

			if ($conn->query($sql) === TRUE) {

			   echo "<script>alert('Record Deleted !!');</script>";
			   echo "<script>window.location = '".HOME_URL."'</script>";
			  
			} else {
				echo "<script>alert('Error while Deleting Record !!');</script>";
				echo "<script>window.location = '".HOME_URL."'</script>";
			}

		}
	}
	
}	

//-----------------------------------------------
if($info="edit") {
		$editlid = $_GET["eid"];
		if ( !empty( $editlid ) ) { 
			$selected_store_sql = "select * from ".$zone_detail_table." where id = ".$editlid." ";
			$selected_product_records = $conn->query($selected_store_sql);
			
			
				if ( $selected_product_records->num_rows > 0 ) {
				while($selected_product_record = $selected_product_records->fetch_assoc()) {		
					
				?>
				<div class="wrap"> 
					<h2>Edit Zone Option Details</h2>
					<br/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css"/>
					 <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/bootstrap.css"/>
					 <script src="<?php echo APP_URL; ?>/js/jquery-1.11.3.min.js"></script>
					 <script src="<?php echo APP_URL; ?>/js/bootstrap.min.js"></script>
					 <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.js"></script>
					 <form role="form" action="zone-option-edit.php" method="post" id="editzoneoptionform">
			  <div class="form-group">
			  <input type="hidden" class="form-control" id="editzoneid" name="editzoneid" value="<?php echo $selected_product_record['id']; ?>">
			    <label for="zonefrom">Zone From:</label>
			    <select class="form-control" id="zone_from" name="zone_from">
				    <option id="0" value="0">Select</option>
				  	<?php
				  	$all_zones_sql 		  = "select * from ".$zones_table." where zone_is_deleted='0' ";
					$all_zone_records 	  = $conn->query($all_zones_sql);
					if ( $all_zone_records->num_rows > 0 ) {
						while($all_zone_record = $all_zone_records->fetch_assoc()) {
							if($all_zone_record['zone_id'] == $selected_product_record['zone_from'])
							{
								echo "<option selected='selected' id=".$all_zone_record['zone_id']." value=".$all_zone_record['zone_id'].">".$all_zone_record['zone_name']."</option>";
							}
							else{
							 echo "<option id=".$all_zone_record['zone_id']." value=".$all_zone_record['zone_id'].">".$all_zone_record['zone_name']."</option>";
							}
						}	
					}
				  	?>
				  </select>
			  </div>
			  <div class="form-group">
			    <label for="zoneto">Zone To:</label>
			   <select class="form-control" id="zone_to" name="zone_to">
				    <option id="0" value="0">Select</option>
				  	<?php
					$all_zones_sql 		  = "select * from ".$zones_table." where zone_is_deleted='0' ";
					$all_zone_records 	  = $conn->query($all_zones_sql);
				  	if ( $all_zone_records->num_rows > 0 ) {
						
						while($all_zone_record = $all_zone_records->fetch_assoc()) {
							if($all_zone_record['zone_id'] == $selected_product_record['zone_to'])
							{
								echo "<option selected='selected' id=".$all_zone_record['zone_id']." value=".$all_zone_record['zone_id'].">".$all_zone_record['zone_name']."</option>";
							}
							else{
							 echo "<option id=".$all_zone_record['zone_id']." value=".$all_zone_record['zone_id'].">".$all_zone_record['zone_name']."</option>";
							}
						}	
					}
				  	?>
				  </select>
			  </div>
			  <div class="form-group">
			    <label for="addstorename">Price:</label>
			    <input type="text" class="form-control" id="price" name="price" value="<?php echo $selected_product_record['price'];?>">
			  </div>
			   
			   
					  <button type="submit" class="btn btn-default">Update</button>
					</form>
				</div>
				<?php
				}
				}				
			
		 
	}
}

//--------------------------------------------------------


?>
<script>
	jQuery(document).ready(function() {
		 $("#editzoneoptionform").validate();
	});
</script>