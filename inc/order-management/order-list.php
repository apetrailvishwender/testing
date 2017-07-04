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

$orders_table = "orders";	
$drivers_table = "drivers";
$users_sql = "select * from ".$orders_table." order by id DESC";
$users_records = $conn->query($users_sql);
if ( $users_records->num_rows > 0 ) {
	while($users_record = $users_records->fetch_assoc()) {
		$order_ids[] = $users_record['order_id'];
	}	
}	
//$api_url = 'https://7c7eab4a93b747dfb25aac56e2f658db:4cb42d6c89f8e2447534039f3de29731@247s.myshopify.com';
$api_url = 'https://6143cddd141541615eb006119cd232be:4f79e7163888d6bf77af717e923f37e9@24sevens.myshopify.com';
$counts_url = $api_url . '/admin/orders/count.json';

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_URL, $counts_url);
$result2 = curl_exec($ch2);
curl_close($ch2);

$count_json = json_decode( $result2, true);

$counts = $count_json['count'];
$pagesize = round($counts/50);
$page = $pagesize + 1;
$orders = array(); 
$orgorder = array();
for($i=1;$i<=$page;$i++){	
	$orders_url = $api_url . '/admin/orders.json?status=any&limit=50&page='.$i;	
	$ch1 = curl_init();
	curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch1, CURLOPT_URL, $orders_url);
	$result1 = curl_exec($ch1);
	curl_close($ch1);
	$order_json = json_decode($result1, true);
	$orders = $order_json['orders'];

	if(!empty($orders)) {
		$orgorder = array_merge_recursive($orgorder,$orders);	
	} else {
		$orders = array();
		$orgorder = array_merge_recursive($orgorder,$orders);	
	}
	foreach($orgorder as $res) {
		
		$order_id = $res['order_number'];
		$f_order_ids[]  = $res['order_number'];
		$customer_email = $res['email'];
		$order_price = $res['total_price'];
		$order_address1 = $res['shipping_address']['address1'];
		$order_city = $res['shipping_address']['city'];
		$order_zip = $res['shipping_address']['zip'];
		$order_state = $res['shipping_address']['province'];
		$order_country = $res['shipping_address']['country'];
		$order_shipping_address = $order_address1.','.$order_city.','.$order_state.','.$order_country.'-'.$order_zip;
		
		$add_order = "INSERT INTO ".$orders_table."(order_id,order_price,customer_email,order_shipping_address,order_driver_id) VALUES ('".$order_id."',".$order_price.", '".$customer_email."','".$order_shipping_address."','') ON DUPLICATE KEY UPDATE order_price = VALUES(order_price),customer_email = VALUES(customer_email),order_shipping_address = VALUES(order_shipping_address)";

		$conn->query($add_order);

	}
	$result = array_diff($order_ids,$f_order_ids);
	if(!empty($result)) {
		foreach($result as $delel) {
			$del_order = "DELETE FROM ".$orders_table." WHERE order_id=".$delel." ";
			$conn->query($del_order);
		}
	}
}
$users_sql = "select * from ".$orders_table." order by id DESC";
$users_records = $conn->query($users_sql);
if(!empty($users_records)) {
?>
<div class="wrap"> 
	<h2>Orders Listing</h2>
	<form name="orders_list" method="get" action="inc/order-management/order-list-update.php?info=delall">	
	<table class="" id="ouserlist" style="width:100%">
		<thead>
			<tr>
				<th width="14px">ID</th>
				<th>Order Number</th>
				<th width="215px">Price</th>
				<th>Customer Email</th>
				<th>Shipping Address</th> 
				<th width="215px">Driver Name</th>                             
				<th style="text-align: center;">Action</th>
			</tr>
		</thead>
		<tbody>				     
			<?php
			$no = 1;
			if ( $users_records->num_rows > 0 ) { ?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery('#ouserlist').dataTable({ 
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
				 while($users_record = $users_records->fetch_assoc()) {
				 	
				 	$id        		  = $users_record['id'];
					$orderid          = $users_record['order_id'];
					$orderprice 	  = $users_record['order_price'];
					$customeremail 	  = $users_record['customer_email'];
					$shipping_address = $users_record['order_shipping_address'];
					$driver_id		  = $users_record['order_driver_id'];
					if($driver_id == "0") {
						$driver_name = "-------";
					} else {
						$drivers_sql 	  = "select driver_name from ".$drivers_table." where driver_id = ".$driver_id." ";
						$driver_records = $conn->query($drivers_sql);
						if ( $driver_records->num_rows > 0 ) {
							while($driver_record = $driver_records->fetch_assoc()) {
								$driver_name = $driver_record['driver_name'];
							}	
						}
					}
					?>
					<tr>
						<td><?php echo $id; ?></td>
						<td><?php echo $orderid; ?></td>
						<td><?php echo $orderprice; ?></td>
						<td><?php echo $customeremail; ?></td>
						<td style="width: 200px;"><?php echo $shipping_address; ?></td>
						<td><?php if(!empty($driver_name)) { echo $driver_name; } else { echo 'No driver selected!'; } ?></td>          
						<td style="text-align: center;">							
							<a href="inc/order-management/order-list-update.php?info=edit&eid=<?php echo $id;?>" class="iframe">
							<img src="images/edit.png" title="Edit" alt="Edit" />
							</a>
						</td>             
					</tr>
				<?php $no += 1;							
				}
				$conn->close();
			} else {
				echo '<h3>No Orders Found !!</h3>';
			} ?>					
		</tbody>
	</table>
	<p></p>
	</form>
</div>
<?php } else { ?>
	<div style="text-align: center;">No orders found !!</div>
<?php } ?>  