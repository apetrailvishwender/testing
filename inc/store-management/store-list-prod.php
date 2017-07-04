<?php
include('../../constants.php');
include('../../db.php');
$products_table = "products";	
$stores_table = "stores";
$api_key = "AIzaSyAL6J3V-qq4qryg8WZLGQxGcAm4E5SHK-k";
$data = $_POST['fdata'];

foreach($_POST['fdata'] as $key=>$value)
{
	$post[$value['name']] = $value['value'];	
}
$delivery_postal_code = $post['store_prod_finder'];
$kml = new SimpleXMLElement(file_get_contents("https://mycloudsportal.com/app/StoreFinderMapnew.kml"));
$json = json_encode($kml);
$array = json_decode($json,TRUE);

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
$z = 0;
$zonearr = array();
$zonelist = array();

foreach($fzone_coordinates as $fzone_coordinate) {
	
	$zonearr = array_unique($fzone_coordinate['zonename']);
	$vertices_x = $fzone_coordinate['coordx'];
	$vertices_y = $fzone_coordinate['coordy'];
	$points_polygon = count($vertices_x) - 1; 
	if(!empty($_POST)) {
		$postalcode = $delivery_postal_code;
		$edcoordinates = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($postalcode) . '&sensor=true&key='.$api_key);
		$edcoordinates = json_decode($edcoordinates);
		$longitude_x  = $edcoordinates->results[0]->geometry->location->lat;
		$latitude_y = $edcoordinates->results[0]->geometry->location->lng;
	} else {
		$longitude_x = ""; 
		$latitude_y = ""; 
	}
	
	if (is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)){
	  	$zonelist[] = $zonearr[0];
	} 
}
$products_sql = "select * from ".$products_table." order by id DESC";
$products_records = $conn->query($products_sql);
$stores_sql = "select * from ".$stores_table." where store_is_deleted='0' and store_postalcode!='00000' order by store_id DESC";
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
	$store_id;
	$storeaddr 	  = $stores_record['store_address'];
	$storecity 	  = $stores_record['store_city'];
	$longitude_x  = $stores_record['store_latitude'];
	$latitude_y   = $stores_record['store_longitude'];
	 foreach($fzone_coordinates as $fzone_coordinate) {					
		$zonearr = array_unique($fzone_coordinate['zonename']);
		$vertices_x = $fzone_coordinate['coordx'];
		$vertices_y = $fzone_coordinate['coordy'];
		
		$points_polygon = count($vertices_x) - 1; 
		if (is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)){
		foreach($zonelist as $zoneitem){
		$zonequery = "SELECT * FROM distance where distance != '' and zone_one = '".trim($zoneitem)."' ORDER BY CAST( distance AS DECIMAL( 10, 5 ) ) ASC";
	
		$zonerecords = $conn->query($zonequery);
	while($zonedata = $zonerecords->fetch_assoc()){
		
		if(in_array($zonedata['zone_two'],$zonelist)) {
				//$storedistance = $zonedata['distance'];
			
				$st_name = $stores_record['store_name'];
				$st_addr = $stores_record['store_address'].",".$stores_record['store_city'].",".$stores_record['store_state'].",".$stores_record['store_country']."-".$stores_record['store_postalcode'];
				
				foreach($mainarr as $key => $value) {
					$storeid = $key;
					if($store_id == $storeid )
					{
						$st_sql = "select * from ".$stores_table." where store_id=".$storeid." and store_is_deleted='0' and store_postalcode!='00000' order by store_id DESC";
						$st_records = $conn->query($st_sql);
						while($st_record = $st_records->fetch_assoc()) 					{
						$st_name = $st_record['store_name'];
						$st_addr = $st_record['store_address'].",".$st_record['store_city'].",".$st_record['store_state'].",".$st_record['store_country']."-".$st_record['store_postalcode'];
						$response['storearr'][$st]['stname'][] = $st_name; 
						$response['storearr'][$st]['staddr'][] = $st_addr;
						$response['storearr'][$st]['stdistance'][] = $storedistance;
						$response['storeproddetails'] .= "<div class='store-details'><h3>".$st_name."</h3>"; 
						$response['storeproddetails'] .= "<p>".$st_addr."</p>"; 
						 
					}	
						$prodlists = $value;
						$newprodlists = array();
						foreach($prodlists as $prodlist) {
							$newprodlists[] = $prodlist;
						}
						$i=0;
						foreach($newprodlists as $newprodlist) {
							$pd_sql = "select * from ".$products_table." where product_id=".$newprodlist."";
							$pd_records = $conn->query($pd_sql);
							while($pd_record = $pd_records->fetch_assoc()) {
								$prodid = $pd_record['product_id'];
								$prodname = $pd_record['product_name'];
								$prodimg = $pd_record['product_img'];
								$prodprice = $pd_record['product_price'];
								$response['prodarr'][$st][] = $prodid; 
								$response['storearr'][$st]['products'][] = $prodid;
								$response['storeproddetails'] .= "<div class='item'><a href='https://24sevens.myshopify.com/pages/store-products?store=".$storeid."'><img src=".$prodimg." title=".$prodname." height='150px' width='150px'/></a>";
								$response['storeproddetails'] .= "<h5>".$prodname."</h5></div>";
							}
							$i++;
							if($i==4) break;
						}
						$response['storeproddetails'].= "</div>";
						$st++;
					}
				}
			} else {
				
			}
			}
		}
	}	  	
		}
	}

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

$response['status'] = 1;
echo json_encode(array_unique($response));	
die(); 
?>