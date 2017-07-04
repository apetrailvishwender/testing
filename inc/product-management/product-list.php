<script>
	jQuery(document).ready(function() {
		jQuery("a.piframe").fancybox({
			maxWidth	: 2000,
			width		: '60%',
			height		: '20%',
			autoSize	: true,
			type		: 'iframe',
		});
	});
</script>
<?php
session_start();
include('db.php');
echo $_SESSION['shop'];
echo $_SESSION['token'];
$products_table = "products";	
$stores_table = "stores";
$users_sql = "select * from ".$products_table." order by id DESC";
$users_records = $conn->query($users_sql);
if ( $users_records->num_rows > 0 ) {
	while($users_record = $users_records->fetch_assoc()) {
		$prod_ids[] = $users_record['product_id'];
	}	
}	

$api_url = 'https://6143cddd141541615eb006119cd232be:4f79e7163888d6bf77af717e923f37e9@24sevens.myshopify.com';
$counts_url = $api_url . '/admin/products/count.json';

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
$products = array(); 
$orgproduct = array();
for($i=1;$i<=$page;$i++){	
	$products_url = $api_url . '/admin/products.json?status=any&limit=50&page='.$i;	
	$ch1 = curl_init();
	curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch1, CURLOPT_URL, $products_url);
	$result1 = curl_exec($ch1);
	curl_close($ch1);
	$product_json = json_decode($result1, true);
	$products = $product_json['products'];
	if(!empty($products)) {
		$orgproduct = array_merge_recursive($orgproduct,$products);	
	} else {
		$products = array();
		$orgproduct = array_merge_recursive($orgproduct,$products);	
	}
	/*echo '<pre/>';
	print_r($orgproduct);*/
	foreach($orgproduct as $res) {
		
		$fproduct_id = $res['variants'][0]['product_id'];
		$f_prod_ids[]  = $res['variants'][0]['product_id'];
		$product_name = $res['title'];
		$product_desc = $res['body_html'];
		$product_price = $res['variants'][0]['price'];
		$product_image = $res['image']['src'];
		$add_product = "INSERT INTO ".$products_table."(product_id, product_name, product_description,product_price,product_img,product_store_list) VALUES ('".$fproduct_id."', '".$product_name."', '".$product_desc."', '".$product_price."', '".$product_image."','') ON DUPLICATE KEY UPDATE product_name = VALUES(product_name),product_description = VALUES(product_description),product_price = VALUES(product_price),product_img = VALUES(product_img)";
			
		$conn->query($add_product);

	}
	$result = array_diff($prod_ids,$f_prod_ids);
	if(!empty($result)) {
		foreach($result as $delel) {
			$del_product = "DELETE FROM ".$products_table." WHERE product_id=".$delel." ";
			$conn->query($del_product);
		}
	}
}
$del_delprice = "DELETE FROM ".$products_table." WHERE product_id=5579743942";
$conn->query($del_delprice);
$del_urgent_delprice = "DELETE FROM ".$products_table." WHERE product_id=5579621254";
$conn->query($del_urgent_delprice);

$users_sql = "select * from ".$products_table." order by id DESC";
$users_records = $conn->query($users_sql);

$output = 'var products_db = {';
$count = 0;
if ( $users_records->num_rows > 0 ) { 
	while($obj = $users_records->fetch_assoc()) {
		$store_ids = array();
		$flstores = '';
		$stores = '';
		$productstorelist = $obj['product_store_list'];
		$productstorelistarr = array();
		$productstorelistarr = unserialize($productstorelist);
		if (is_array($productstorelistarr) || is_object($productstorelistarr))
		{
		    foreach($productstorelistarr as $storeid) {
		    	
				$stores_sql = "select store_id from ".$stores_table." where store_id = ".$storeid." and store_is_deleted='0' ";
				$store_records = $conn->query($stores_sql);
				if ( $store_records->num_rows > 0 ) {

					while($store_record = $store_records->fetch_assoc()) {
						$store_ids[] = $store_record['store_id'];
					}	
				} 
			} 
			foreach($store_ids as $store_id) {
				$stores .= $store_id.", ";
			}
			$flstores = trim($stores,', ');
		}
		if(!empty($obj['product_store_list'])) {
			$output .= "'$count': {'product_id': '" . $obj['product_id'] . "', 'product_name': '" . str_replace("'", "\'", $obj['product_name']) . "','product_img':'".str_replace("'", "\'", $obj['product_img']) ."','product_price':'".$obj['product_price']."','product_store_list':'".$flstores."'},\n";
		}
		$count++;
	}	
}
$output = substr($output, 0, -2);
$output .= '};';
file_put_contents('js/products.js', $output);

$users_sql = "select * from ".$products_table." order by id DESC";
$users_records = $conn->query($users_sql);

?>
<div class="wrap"> 
	<h2>Products Listing</h2>
	<form name="products_list" method="get" action="inc/product-management/product-list-update.php?info=delall">	
	<table class="" id="puserlist" style="width:100%">
		<thead>
			<tr>
				<th width="14px">ID</th>
				<th>Product Image</th>
				<th>Product ID</th>
				<th>Product Name</th>
				<!--<th>Product Description</th>--> 
				<th width="215px">Price</th>
				<th width="215px">Selected Stores</th>                             
				<th style="text-align: center;">Action</th>
			</tr>
		</thead>
		<tbody>				     
			<?php
			$no = 1;
			if ( $users_records->num_rows > 0 ) { ?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						jQuery('#puserlist').dataTable({ 
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
				 	$snames 		  = "";
				 	$store_names 	  = array();
				 	$id        		  = $users_record['id'];
					$productid        = $users_record['product_id'];
					$productname 	  = $users_record['product_name'];
					$productimg 	  = $users_record['product_img'];
					$productdec 	  = $users_record['product_description'];
					$productprice 	  = $users_record['product_price'];
					$productstorelist = $users_record['product_store_list'];
					$productstorelistarr = array();
					$productstorelistarr = unserialize($productstorelist);
					if (is_array($productstorelistarr) || is_object($productstorelistarr))
					{
					    foreach($productstorelistarr as $storeid) {
					    	
							$stores_sql 	  = "select store_name from ".$stores_table." where store_id = ".$storeid." and store_is_deleted='0' ";
							$store_records = $conn->query($stores_sql);
							if ( $store_records->num_rows > 0 ) {
								while($store_record = $store_records->fetch_assoc()) {
									$store_names[] = $store_record['store_name'];
								}	
							} else {
								$store_names = array();
							}
						}
					} else {
						$store_names = array();
					}
					?>
					<tr>
						<td><?php echo $id; ?></td>
						<td><?php if(!empty($productimg)) { ?><img src="<?php echo $productimg; ?>" style="width: 50px;height: 50px;"><?php } ?></td>
						<td><?php echo $productid; ?></td>
						<td><?php echo $productname; ?></td>
						<!--<td style="width: 200px;"><?php echo strip_tags($productdec); ?></td>-->
						<td><?php echo $productprice; ?></td>
						<td><?php if(!empty($store_names)) { foreach ($store_names as $value) {  $snames .= $value.", ";  } echo trim($snames,', ');  } else { echo 'No stores selected!'; } ?></td>                
						<td style="text-align: center;">							
							<a href="inc/product-management/product-list-update.php?info=edit&eid=<?php echo $id;?>" class="piframe">
							<img src="images/edit.png" title="Edit" alt="Edit" />
							</a>
						</td>             
					</tr>
				<?php $no += 1;							
				}
				$conn->close();
			} else {
				echo '<h3>No Products Found !!</h3>';
			} ?>					
		</tbody>
	</table>
	<p></p>
	</form>
</div>