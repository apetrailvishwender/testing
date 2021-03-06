<script>
	jQuery(document).ready(function() {
		jQuery("a.iframe").fancybox({
			maxWidth	: 2000,
			maxHeight	: 500,
			width		: '60%',
			height		: '20%',
			autoSize	: true,
			type		: 'iframe',
		});
	});
</script>
<?php
include('db.php');
$zones_table = "zones";	
$users_sql = "select * from ".$zones_table." where zone_is_deleted='0' order by zone_id DESC";
$users_records = $conn->query($users_sql);
$output = 'var zone_db = {';
$count = 0;
if ( $users_records->num_rows > 0 ) { 
	while($obj = $users_records->fetch_assoc()) {
		$output .= "'$count': {'zone_id': '" . $obj['zone_id'] . "', 'zone_name': '" . str_replace("'", "\'", $obj['zone_name']) . "','zone_delivery_price':'".$obj['zone_delivery_price']."','zone_urgent_delivery_price':'".$obj['zone_urgent_delivery_price']."','zone_central_zipcode':'".$obj['zone_central_zipcode']."'},\n";
		$count++;
	}	
}
$output = substr($output, 0, -2);
$output .= '};';
//echo $output;
file_put_contents('js/zones.js', $output);

$kml = new SimpleXMLElement(file_get_contents('StoreFinderMapnew.kml'));
$json = json_encode($kml);
$array = json_decode($json,TRUE);
$placemark_arr = $array['Document']['Folder']['Placemark'];

foreach($placemark_arr as $key => $placemark_item) {
	$f_zone_names[] = $placemark_item['name'];
	$sql = "INSERT INTO ".$zones_table." (zone_name, zone_coordinates) VALUES ('".$placemark_item['name']."', '".$placemark_item['Polygon']['outerBoundaryIs']['LinearRing']['coordinates']."') ON DUPLICATE KEY UPDATE zone_coordinates = VALUES(zone_coordinates)";
	$conn->query($sql);
}
if(isset($_GET['success']) && $_GET['success'] == 1)
{
	$kml = new SimpleXMLElement(file_get_contents('StoreFinderMapnew.kml'));
	$json = json_encode($kml);
	$array = json_decode($json,TRUE);
	$placemark_arr = $array['Document']['Folder'][0]['Placemark'];

	foreach($placemark_arr as $key => $placemark_item) {
		$f_zone_names[] = $placemark_item['name'];
		$sql = "INSERT INTO ".$zones_table." (zone_name, zone_coordinates) VALUES ('".$placemark_item['name']."', '".$placemark_item['Polygon']['outerBoundaryIs']['LinearRing']['coordinates']."') ON DUPLICATE KEY UPDATE zone_coordinates = VALUES(zone_coordinates)";
		$conn->query($sql);
	}?>
 	<div class="success"><?php echo "Successfully Import Zone File.";?></div>
<?php } 
?>
<div class="wrap"> 
	<h2>Zones Listing</h2>
	<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#add-zone">Import Zone File</button>
	<!-- Modal -->
	<div id="add-zone" class="modal fade" role="dialog">
	  <div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Add New Zone</h4>
	      </div>
	      <div class="modal-body">
	        <form role="form" action="inc/zone-management/zone-list-add.php" method="POST" enctype="multipart/form-data" id="addzoneform">
			  <div class="form-group">
			    <label for="addzonename">Browse File:</label>
			    <input type="file" class="form-control required" id="addzone" name="addzone" value="">
			  </div>
			  <button type="submit" class="btn btn-default">Import</button>
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
	$users_sql = "select * from ".$zones_table." where zone_is_deleted='0' order by zone_id DESC";
	$users_records = $conn->query($users_sql);
	?>
	<form name="zones_list" method="post" action="inc/zone-management/zone-list-update.php?info=delall">	
	<table class="" id="zonelist" style="width:100%">
		<thead>
			<tr>
				<th width="10px" style="background:none"><input type="checkbox" id="alluser" /></th>
				<th width="14px">ID</th>
				<th>Zone Name</th> 
				<!--<th>Zone coordinates</th>--> 
				<th>Delivery Price</th>
				<th>Urgent Delivery Price</th>    
				<th>Zone Central Zipcode</th>                       
				<th style="text-align: center;">Action</th>
			</tr>
		</thead>
		<tbody>				     
			<?php
			$no = 1;
			if ( $users_records->num_rows > 0 ) { ?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery('#zonelist').dataTable({ 
							"aaSorting": [[ 0, "desc" ]],
						});
					});	
				</script>
				<?php
				 while($users_record = $users_records->fetch_assoc()) {
					$id        	  		  = $users_record['zone_id'];
					$zonename 	  		  = $users_record['zone_name'];
					$zonecoord			  = $users_record['zone_coordinates'];	
					$zonedl_price 	  	  = $users_record['zone_delivery_price'];
					$zone_asap_dlprice 	  = $users_record['zone_urgent_delivery_price'];
					$zone_central_zipcode = $users_record['zone_central_zipcode'];
					?>
					<tr>
					<th><input class="userid" type="checkbox" name="usercodeid[]" id="usercodeid" value="<?php echo $id; ?>" /></th>
						<td><?php echo $id; ?></td>
						<td nowrap><?php echo $zonename; ?></td>
						<!--<td nowrap>
                        <?php 
						$smallzonecoord = substr($zonecoord, 0, 100);
						echo $smallzonecoord . "...."; ?></td>   --> 
						<td nowrap><?php if($zonedl_price!="") { echo $zonedl_price ; } else { '----'; } ?></td>  
						<td nowrap><?php if($zone_asap_dlprice!="") { echo $zone_asap_dlprice ; } else { '----'; } ?></td>
						<td nowrap><?php if($zone_central_zipcode!="") { echo $zone_central_zipcode ; } else { '----'; } ?></td>               
						<td style="text-align: center;">							
							<!--<a onclick="javascript:return confirm('Are you sure, want to delete record of <?php echo $zonename; ?>?')" href="inc/zone-management/zone-list-update.php?info=del&did=<?php echo $id;?>">
							<img src="images/delete.png" title="Delete" alt="Delete" />
							</a>
							&nbsp;&nbsp;&nbsp;-->
							<a href="inc/zone-management/zone-list-update.php?info=edit&eid=<?php echo $id;?>" class="iframe">
							<img src="images/edit.png" title="Edit" alt="Edit" />
							</a>
						</td>            
					</tr>
				<?php $no += 1;							
				}
				$conn->close();
			} else {
				echo '<h3>No Zone Records Found !!</h3>';
			} ?>					
		</tbody>
	</table>
	<p>
		<!--<input type="submit" name="delete" class="add-new-h2 button-secondary" onclick="javascript:return confirm('Are you sure, want to delete all checked record?')" value="Delete">-->
	</p>
	</form>
</div>
<script>
	jQuery(document).ready(function() {
		 $("#addzoneform").validate();
		 jQuery.validator.addMethod(
		    "money",
		    function(value, element) {
		        var isValidMoney = /^\d{0,8}(\.\d{0,3})?$/.test(value);
		        return this.optional(element) || isValidMoney;
		    },
		    "Insert proper price"
		);
		setTimeout(function(){ $('.success').fadeOut() }, 1000);
	});
</script>