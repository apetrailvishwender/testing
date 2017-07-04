<?php
include('db.php');
include('constants.php');
$data = $_POST['fdata'];
$api_key = "AIzaSyAL6J3V-qq4qryg8WZLGQxGcAm4E5SHK-k";

foreach($_POST['fdata'] as $key=>$value)
{
	if($value['name'] == "productids[]") {
		$post[$value['name']][] .= $value['value'];
	} else {
		$post[$value['name']] = $value['value'];
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
$kml = new SimpleXMLElement(file_get_contents('StoreFinderMapnew.kml'));
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
$delivery_postal_code = $post['userpostalcode'];
$cart_products = $post['productids[]'];
$delivery_time = $post['deliverytime'];
$fdelivery_time =  date('h:i a', strtotime($delivery_time));
$delivery_date = $post['deliverydate'];
$products_table = "products";	
$stores_table = "stores";
$zone_table = "zones";
$products_count = count(array_unique($cart_products));
foreach($cart_products as $cart_product) {
	$products_sql = "select * from `".$products_table."` where `product_id`='".$cart_product."'";
	$product_records = $conn->query($products_sql) or die(mysql_error()); 
	if ( $product_records->num_rows > 0 ) {
		while($product_record = $product_records->fetch_assoc()) {
			$product_name = $product_record['product_name'];
			$storelist = $product_record['product_store_list'];
			if(empty($storelist)) {
				$store_postalcodes[] = '000000';
			}
			$storelistarr = array();
			$storelistarr = unserialize($storelist);
			$storelistarr = array_unique($storelistarr);
			foreach($storelistarr as $storeitem) {
				$stores_sql = "select store_postalcode from ".$stores_table." where store_id='".$storeitem."'";
				$store_records = $conn->query($stores_sql) or die(mysql_error()); 
				if ( $store_records->num_rows > 0 ) {
					while($store_record = $store_records->fetch_assoc()) {
						$store_postalcodes[] = $store_record['store_postalcode'];
												
					}	
				}	
			}
		}	
	}
}
$stores = array_unique($store_postalcodes);
$chargesarray = array();
$mainarray = array();
foreach ($stores as $store_postalcode){
	
	$edcoordinates = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($store_postalcode) . '&sensor=true&key='.$api_key);
	
	$edcoordinates = json_decode($edcoordinates);
	
	$longitude_x  = $edcoordinates->results[0]->geometry->location->lat;
	$latitude_y = $edcoordinates->results[0]->geometry->location->lng; 
	
	$z = 0;	
	 foreach($fzone_coordinates as $fzone_coordinate) {	
				$zonearr = array_unique($fzone_coordinate['zonename']);
				$vertices_x = $fzone_coordinate['coordx'];
				$vertices_y = $fzone_coordinate['coordy'];
				$points_polygon = count($vertices_x) - 1; 
				if (is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)){
						
						$zonesql = "select * from `".$zone_table."` where `zone_name`='".$zonearr[0]."'";
						$zone_records = $conn->query($zonesql) or die(mysql_error()); 
						if ( $zone_records->num_rows > 0 ) 
						{
							while($zone_record = $zone_records->fetch_assoc()) 
							{
								
								$zonename = $zone_record['zone_name'];
								$zoneprice = $zone_record['zone_delivery_price'];
								$zoneurgentprice = $zone_record['zone_urgent_delivery_price'];
								//$chargesarray['storezipcode'] = $store_postalcode;
								//$chargesarray['deliveryprice'] = $zoneprice;
								//$chargesarray['urgentdeliveryprice'] = $zoneurgentprice;
								$mainarray[] = $zoneprice;
								$chargesarray[] = $zoneurgentprice;
								$mainzonename[] = $zonename;
							}
						}
						
					}	
			}
}
/*print_r($store_postalcodes);*/
/*$z = 0;
foreach($zone_coordinates['allzones']['coords'] as $fzone_coordinate) {
	
	foreach($fzone_coordinate as $fzone_coord) {
		$fzone_coordr = explode(",",$fzone_coord);
		$vertices_x[$z] = $fzone_coordr[0];
		$vertices_y[$z] = $fzone_coordr[1];
		$z++;
	}
}
$z = 0;
foreach($fzone_coordinates as $fszone_coordinate) {
	$finalArr[$z] = $fszone_coordinate; 
	$z++;
}*/

if(!empty($store_postalcodes)) {
	$response['status'] = 1;
	$response['store_postalcodes'] = $store_postalcodes;
	$response['zone_name'] = $mainzonename;
	$response['deliveryprice'] = $mainarray;
	$response['urgentdeliveryprice'] = $chargesarray;
	$response['products_count'] = $products_count;
	$response['delivery_date'] = $delivery_date;
	$response['delivery_time'] = $delivery_time;
} else {
	$response['status'] = 2;
}	

echo json_encode($response);	
die(); 
?>