<?php
function array_values_recursive($ary)  {
$lst = array();
foreach( array_keys($ary) as $k ) {
$v = $ary[$k];
if (is_scalar($v)) {
$lst[] = $v;
} elseif (is_array($v)) {
$lst = array_merge($lst,array_values_recursive($v));
}
}
return $lst;
}
function sluggify($url)
{
    # Prep string with some basic normalization
    $url = strtolower($url);
    $url = strip_tags($url);
    $url = stripslashes($url);
    $url = html_entity_decode($url);

    # Remove quotes (can't, etc.)
    $url = str_replace('\'', '', $url);

    # Replace non-alpha numeric with hyphens
    $match = '/[^a-z0-9]+/';
    $replace = '-';
    $url = preg_replace($match, $replace, $url);

    $url = trim($url, '-');

    return $url;
}
include('../../constants.php');
include('../../db.php');	
$stores_table = "stores";
$products_table = "products";
$api_key = "AIzaSyAL6J3V-qq4qryg8WZLGQxGcAm4E5SHK-k";
$data = $_POST['fdata'];
/* $productquery = "Select store_product_list from $stores_table where store_id = '".$data."' ";
$stores_records = $conn->query($productquery);
while($stores_record = $stores_records->fetch_assoc()) {
	$storeproductlistarr = array();
	$storeproductlist = $stores_record['store_product_list'];
	$storeid = $stores_record['store_id'];	
	$storeproductlistar = unserialize($storeproductlist);
	$productlists = array_unique(array_values_recursive($storeproductlistar));
	foreach($productlists as $productid){
		$productquery1 = "Select * from $product_table where id = '".$productid."' ";
		$product_records = $conn->query($productquery1);
		$product_result = $product_records->fetch_assoc();
		$prdimg = $product_result['product_img'];
		$prdname = $product_result['product_name'];
		$prdurl = sluggify($prdname);
		$prdprice = $product_price['product_price'];
		$response['productdetails'] .= "<div class='product col-sm-3'><div class='product_wrapper'><div class='product_img'><a href='http://24sevens.myshopify.com/products/".$prdurl."'><img src='".$prdimg."' title=".$prdname." height='150px' width='150px'/></a></div>";
		$response['productdetails'] .= "<div class='product_info'><div class='product_name'><a href='http://24sevens.myshopify.com/products/".$prdurl."'>".$prdname."</a></div><div class='product_price'>".$prdprice."</div></div></div></div>";		
	}
}*/
$products_sql = "select * from ".$products_table." order by id DESC";
$products_records = $conn->query($products_sql);
$stores_sql = "select * from ".$stores_table." where store_is_deleted='0' and store_postalcode!='00000' and store_id = '".$data."' order by store_id DESC";
$stores_records = $conn->query($stores_sql);
$s = 0;
$st = 0;
while($stores_record = $stores_records->fetch_assoc()) {
	while($products_record = $products_records->fetch_assoc()) {
		$snames 		  = "";
		$store_names 	  = array();
		$id        		  = $products_record['id'];
		$productid        = $products_record['product_id'];
		$productname 	  = $products_record['product_name'];
		$productstorelist = $products_record['product_store_list'];
		$productstorelistarr = array();
		$productstorelistarr = unserialize($productstorelist);
		if (is_array($productstorelistarr) || is_object($productstorelistarr))
		{
			$store_ids = array();
			$prod_ids = array();
			$finalstorearr['stores'] = array();
		    foreach($productstorelistarr as $storeid) {
				$stores_sql    = "select * from ".$stores_table." where store_id = ".$storeid." and store_is_deleted='0' ";
				$store_records = $conn->query($stores_sql);
				if ( $store_records->num_rows > 0 ) {
					while($store_record = $store_records->fetch_assoc()) {
						$prod_ids[] = $productid;
						$store_ids[] = $store_record['store_id'];
					}	
				} else {
					$store_ids = array();
				}
			}
		} else {
			 $store_ids = array();
		}
		if(!empty($store_ids)) {
			$storearr[$s]['products'] = $productid;
			$storearr[$s]['stores'] = $store_ids;
			$s++;
		} 
	}
	$k = 0;
	foreach($storearr as $storeitem) {
		foreach($storeitem['stores'] as $storearitem) {
			$mainarr[$storearitem][$k] = $storeitem['products'];
			$k++;
		}
	}
	$store_id = $stores_record['store_id'];
	$storeaddr 	  = $stores_record['store_address'];
	$storecity 	  = $stores_record['store_city'];
	
	
				$storedistance = $zonedata['distance'];
			
				$st_name = $stores_record['store_name'];
				$st_addr = $stores_record['store_address'].",".$stores_record['store_city'].",".$stores_record['store_state'].",".$stores_record['store_country']."-".$stores_record['store_postalcode'];
				
				foreach($mainarr as $key => $value) {
					$storeid = $key;
					if($store_id == $storeid )
					{
						$st_sql = "select * from ".$stores_table." where store_id=".$storeid." and store_is_deleted='0' and store_postalcode!='00000' and  store_id = '".$data."' order by store_id DESC";
						$st_records = $conn->query($st_sql);
						while($st_record = $st_records->fetch_assoc()) 					{
						$st_name = $st_record['store_name'];
						$st_addr = $st_record['store_address'].",".$st_record['store_city'].",".$st_record['store_state'].",".$st_record['store_country']."-".$st_record['store_postalcode'];
						$response['storearr'][$st]['stname'][] = $st_name; 
						$response['storearr'][$st]['staddr'][] = $st_addr;
						$response['storearr'][$st]['stdistance'][] = $storedistance;
						$response['productdetails'] .= "<div class='store-details'><h3>".$st_name."</h3>"; 
						$response['productdetails'] .= "<p>".$st_addr."</p>"; 
						 
					}	
						$prodlists = $value;
						$newprodlists = array();
						foreach($prodlists as $prodlist) {
							$newprodlists[] = $prodlist;
						}
						foreach($newprodlists as $newprodlist) {
							$pd_sql = "select * from ".$products_table." where product_id=".$newprodlist."";
							$pd_records = $conn->query($pd_sql);
							while($pd_record = $pd_records->fetch_assoc()) {
								$prodid = $pd_record['product_id'];
								$prodname = $pd_record['product_name'];
								$prodimg = $pd_record['product_img'];
								$prodprice = $pd_record['product_price'];
								$prdurl = sluggify($prodname);
								$response['prodarr'][$st][] = $prodid; 
								$response['storearr'][$st]['products'][] = $prodid;
								$response['productdetails'] .= "<div class='product col-sm-3'><div class='product_wrapper'><div class='product_img'><a href='http://24sevens.myshopify.com/products/".$prdurl."'><img src='".$prodimg."' title=".$prodname." height='150px' width='150px'/></a></div>";
								$response['productdetails'] .= "<div class='product_info'><div class='product_name'><a href='http://24sevens.myshopify.com/products/".$prdurl."'>".$prodname."</a></div><div class='product_price'>".$prodprice."</div></div></div></div>";
							}
						}
						$response['productdetails'].= "</div>";
						$st++;
					}
				}
}
echo json_encode($response);	
?>