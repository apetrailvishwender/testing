 <script>
	jQuery(document).ready(function() {
		jQuery("a.siframe").fancybox({
			maxWidth	: 2000,
			width		: '60%',
			height		: '20%',
			autoSize	: true,
			type		: 'iframe',
		});
	});
</script>
<?php
include('db.php');
$api_key = "AIzaSyAL6J3V-qq4qryg8WZLGQxGcAm4E5SHK-k";
$stores_table = "stores";	
$zones_table = "zones";	
$users_sql = "select * from ".$stores_table." where store_is_deleted='0' and store_postalcode!='00000' order by store_id DESC";
$users_records = $conn->query($users_sql);
$output = 'var store_db = {';
$count = 0;
if ( $users_records->num_rows > 0 ) { 
	while($obj = $users_records->fetch_assoc()) {
		$output .= "'$count': {'store_id': '" . $obj['store_id'] . "', 'store_name': '" . str_replace("'", "\'", $obj['store_name']) . "','store_address':'".str_replace("'", "\'", $obj['store_address']) ."','store_city':'".$obj['store_city']."','store_state':'".$obj['store_state']."','store_country':'".$obj['store_country']."','store_postalcode':'".$obj['store_postalcode']."','store_latitude':'".$obj['store_latitude']."','store_longitude':'".$obj['store_longitude']."','store_zone_id':'".$obj['store_zone_id']."'},\n";
		$count++;
	}	
}
$output = substr($output, 0, -2);
$output .= '};';
file_put_contents('js/stores.js', $output);

	
function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
{
  $i = $j = $c = 0;
  for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
    if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
     ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
       $c = !$c;
  }
  return $c;
}
?>
<div class="wrap"> 
	<h2>Stores Listing</h2>
	<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#add-store">Add New Store</button>
	<!-- Modal -->
	<div id="add-store" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Add New Store</h4>
	      </div>
	      <div class="modal-body">
	        <form role="form" action="inc/store-management/store-list-add.php" method="post" id="addstoreform">
			  <div class="form-group">
			    <label for="addstorename">Store Name:</label>
			    <input type="text" class="form-control required" id="addstorename" name="addstorename" value="">
			  </div>
			  <div class="form-group">
			    <label for="addstorename">Address:</label>
			    <textarea rows="4" class="form-control required" id="addstoreaddr" name="addstoreaddr"></textarea>
			  </div>
			  <div class="form-group">
			    <label for="addstorename">City:</label>
			    <input type="text" class="form-control" id="addstorecity" name="addstorecity" value="">
			  </div>
			   <div class="form-group">
			    <label for="addstorename">State:</label>
			    <input type="text" class="form-control" id="addstorestate" name="addstorestate" value="">
			   </div>
			   <div class="form-group">
			    <label for="addstorename">Country:</label>
			    <input type="text" class="form-control" id="addstorecountry" name="addstorecountry" value="">
			   </div>
			   <div class="form-group">
			    <label for="addstorename">Pincode:</label>
			    <input type="text" class="form-control required" id="addstorepincode" name="addstorepincode" value="">
			   </div>
			  <!--<div class="form-group">
			  <label for="storeaddzone">Select Zone:</label>
				  <select class="form-control" id="storeaddzone" name="storeaddzone">
				    <option id="0" value="0">Select</option>
				  	<?php
				  	$all_zones_sql 		  = "select * from ".$zones_table." where zone_is_deleted='0' ";
					$all_zone_records 	  = $conn->query($all_zones_sql);
					if ( $all_zone_records->num_rows > 0 ) {
						while($all_zone_record = $all_zone_records->fetch_assoc()) {
							 echo "<option id=".$all_zone_record['zone_id']." value=".$all_zone_record['zone_id'].">".$all_zone_record['zone_name']."</option>";
						}	
					}
				  	?>
				  </select>
			 </div>-->
			  <button type="submit" class="btn btn-default">ADD</button>
			</form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
	<br/>
	<br/>
	<?php
	$users_sql = "select * from ".$stores_table." where store_is_deleted='0' and store_postalcode!='00000' order by store_id DESC";
	$users_records = $conn->query($users_sql);
	?>
	<form name="stores_list" method="post" action="inc/store-management/store-list-update.php?info=delall">	
	<table class="" id="storelist" style="width:100%">
		<thead>
			<tr>
				<th width="10px" style="background:none"><input type="checkbox" id="alluser" /></th>
				<th width="14px">ID</th>
				<th>Name</th> 
				<th width="215px">Address</th>
				<th width="215px">City</th>
				<th width="215px">State</th>
				<th width="215px">Country</th>  
				<th width="215px">Pincode</th> 
				<th width="215px">Zone Name</th>                           
				<th style="text-align: center;">Action</th>
			</tr>
		</thead>
		<tbody>				     
			<?php
			$no = 1;
			if ( $users_records->num_rows > 0 ) { ?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery('#storelist').dataTable({ 
							"aaSorting": [[ 0, "desc" ]],
						});
						jQuery('#alluser').click(function(event) {  //on click
						if(this.checked) { // check select status
						    jQuery('.userid').each(function() { //loop through each checkbox
						        this.checked = true;  //select all checkboxes with class "checkbox1"              
						    });
						}else{
						    jQuery('.userid').each(function() { //loop through each checkbox
						        this.checked = false; //deselect all checkboxes with class "checkbox1"                      
						    });        
						}
						});
					});	
				</script>
				<?php
				$kml = new SimpleXMLElement(file_get_contents('StoreFinderMapnew.kml'));
				$json = json_encode($kml);
				$array = json_decode($json,TRUE);
				/*echo "<pre>";
				print_r($array);
				echo "</pre>";*/ 
				$placemark_arr = $array['Document']['Folder']['0']['Placemark'];
				foreach($placemark_arr as $key => $placemark_item) {
					$zone_dt['zone_dt']['zone_name'][$key] = $placemark_item['name'];
					$zone_dt['zone_dt']['zone_coordinates'][$key] = $placemark_item['Polygon']['outerBoundaryIs']['LinearRing']['coordinates'];
				}
				$zone_coordinates = array();
				$count = count($zone_dt['zone_dt']['zone_name']);
				for($i = 0 ; $i < $count ; $i++) {
					$zone_coordinates['allzones']['coords'][$i] = explode(" ",$zone_dt['zone_dt']['zone_coordinates'][$i]);
					$zone_coordinates['allzones']['zones'][$i] = 'zone_'.$i;
				}
				
				$jcount = count($zone_coordinates['allzones']['coords']);
				
				$fzone_coordinates = array();
				
				$b = 0;
				foreach($zone_coordinates['allzones']['coords'] as $zone_coordinate) {
					
								$a = 0;
								foreach($zone_coordinate as $zone_coord) {
								$explode_zone_coord = explode(",",$zone_coord);
								$fzone_coordinates[$zone_coordinates['allzones']['zones'][$b]]['coordy'][$a] = $explode_zone_coord[0];
								$fzone_coordinates[$zone_coordinates['allzones']['zones'][$b]]['coordx'][$a] = $explode_zone_coord[1];
								$fzone_coordinates[$zone_coordinates['allzones']['zones'][$b]]['zonename'][$a] = $zone_dt['zone_dt']['zone_name'][$b];
								$a++;	
							}
						
						$b++;	
						
				}
				 while($users_record = $users_records->fetch_assoc()) {
					$id        	  = $users_record['store_id'];
					$storename 	  = $users_record['store_name'];
					$storeaddr 	  = $users_record['store_address'];
					$storecity 	  = $users_record['store_city'];
					$storestate   = $users_record['store_state'];
					$storecountry = $users_record['store_country'];
					$storepincode = $users_record['store_postalcode'];
					$storezoneid  = $users_record['store_zone_id'];
					if($storezoneid == "0") {
						$zone_name = "-------";
					} else {
						$zones_sql 	  = "select zone_name from ".$zones_table." where zone_id = ".$storezoneid." and zone_is_deleted='0' ";
						$zone_records = $conn->query($zones_sql);
						if ( $zone_records->num_rows > 0 ) {
							while($zone_record = $zone_records->fetch_assoc()) {
								$zone_name = $zone_record['zone_name'];
							}	
						} else {
								$zone_name = "-------";
						}
					}
					$finaladdr = $storeaddr." ".$storecity." ".$storestate." ".$storecountry." ".$store_postalcode;
					$edcoordinates = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($finaladdr) . '&sensor=true&key='.$api_key);
					
					$edcoordinates = json_decode($edcoordinates);
					$longitude_x  = $edcoordinates->results[0]->geometry->location->lat;
					$latitude_y = $edcoordinates->results[0]->geometry->location->lng; 
					$z = 0;
					?>
					<tr>
					<th><input class="userid" type="checkbox" name="usercodeid[]" id="usercodeid" value="<?php echo $id; ?>" /></th>
						<td><?php echo $id; ?></td>
						<td nowrap><?php echo $storename; ?></td>
						<td nowrap style="width:240px;"><?php echo $storeaddr; ?></td>
						<td nowrap><?php echo $storecity; ?></td>
						<td nowrap><?php echo $storestate; ?></td>
						<td nowrap><?php echo $storecountry; ?></td>
						<td nowrap><?php echo $storepincode; ?></td>                
						<td nowrap>
							<?php
								
							 foreach($fzone_coordinates as $fzone_coordinate) {
						
								$zonearr = array_unique($fzone_coordinate['zonename']);
								
								$vertices_x = $fzone_coordinate['coordx'];
								$vertices_y = $fzone_coordinate['coordy'];
								
								$points_polygon = count($vertices_x) - 1; 
								
								if (is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)){
								  	echo $zonearr[0];
								  	echo '<br/>';
								  	$zonesmid_sql 	  = "UPDATE zones SET zone_central_zipcode = '$storepincode' WHERE zone_name = '$zonearr[0]'";
								  	
								  	$zonemid_records = $conn->query($zonesmid_sql);
								}else{
									
								}
							}
							?>
						</td>
						<td style="text-align: center;">							
							<a onclick="javascript:return confirm('Are you sure, want to delete record of <?php echo $storename; ?>?')" href="inc/store-management/store-list-update.php?info=del&did=<?php echo $id;?>">
							<img src="images/delete.png" title="Delete" alt="Delete" />
							</a>
							&nbsp;&nbsp;&nbsp;
							<a href="inc/store-management/store-list-update.php?info=edit&eid=<?php echo $id;?>" class="siframe">
							<img src="images/edit.png" title="Edit" alt="Edit" />
							</a>
						</td>             
					</tr>
				<?php $no += 1;							
				}
				$conn->close();
			} else {
				echo '<h3>No Store Records Found !!</h3>';
			} ?>					
		</tbody>
	</table>
	<p>
		<input type="submit" name="delete" class="add-new-h2 button-secondary" onclick="javascript:return confirm('Are you sure, want to delete all checked record?')" value="Delete">
	</p>
	</form>
</div>
<script>
	jQuery(document).ready(function() {
		 $("#addstoreform").validate();
	});
</script>