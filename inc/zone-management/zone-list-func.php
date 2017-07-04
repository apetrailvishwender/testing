<?php
include('../../constants.php');
include('../../db.php');
$zones_table = "zones";	
$editzoneid = $_POST['editzoneid'];
$editzonename = $_POST['editzonename'];
$editzonedlprice = $_POST['editzonedlprice'];
$editzoneasapdlprice = $_POST['editzoneasapdlprice'];
$editzonecentralzipcode = $_POST['editzonecentralzipcode'];
$edcoordinates = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($editzonecentralzipcode) . '&sensor=true');
$edcoordinates = json_decode($edcoordinates);
$editcentralzipcodelat  = $edcoordinates->results[0]->geometry->location->lat;
$editcentralzipcodelong = $edcoordinates->results[0]->geometry->location->lng;

$sql = "UPDATE ".$zones_table." SET zone_delivery_price =".$editzonedlprice.",zone_urgent_delivery_price=".$editzoneasapdlprice.",zone_central_zipcode='".$editzonecentralzipcode."',zone_centralzipcode_latitude = '".$editcentralzipcodelat."',zone_centralzipcode_longitude='".$editcentralzipcodelong."' WHERE zone_id=".$editzoneid." ";

			
if ($conn->query($sql) === TRUE) {
	$sql3 = "select DISTINCT zone_one as zone1, zone_two as zone2 from distance";
	$distance_records = $conn->query($sql3);
	while($drecords = $distance_records->fetch_assoc()){
		$uniquezone[]= $drecords;			
	}
	$count = count($uniquezone);
	$users_sql = "select * from ".$zones_table." where zone_is_deleted='0'";
	$users_records = $conn->query($users_sql);
	while( $records = $users_records->fetch_assoc()){
		$destination = 	$records['zone_name'];
		$desc = $records['zone_central_zipcode'];
		$distancejson = file_get_contents('http://maps.googleapis.com/maps/api/distancematrix/json?origins="'. urlencode($editzonecentralzipcode).'"&destinations="'.urlencode($desc).'"&sensor=true');
		$distance = json_decode($distancejson);
		$distancekm  = $distance->rows[0]->elements[0]->distance->text;
		if($count > 0){
			for($i=0;$i<=$count;$i++){
				$zone1[] = $uniquezone[$i]['zone1'];
				if(!in_array($editzonename,$zone1)){
					$sql4 = "INSERT INTO distance (zone_one, zone_two, distance) VALUES ('".$editzonename."', '".$destination."', '".$distancekm."')";
				}		
			}
		} else {
			$sql4 = "INSERT INTO distance (zone_one, zone_two, distance) VALUES ('".$editzonename."', '".$destination."', '".$distancekm."')";
			
		}
		$conn->query($sql4);
	}
	  echo "<script>alert('Record Updated !!');</script>";
	  echo "<script>parent.jQuery.fancybox.close();</script>";
	  echo "<script>parent.location.reload(true);</script>";
} else {
	echo "<script>alert('Error while Updating Record !!');</script>";
	echo "<script>parent.jQuery.fancybox.close();</script>";
    echo "<script>parent.location.reload(true);</script>";
}
?>