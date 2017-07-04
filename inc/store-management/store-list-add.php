<?php
include('../../constants.php');
include('../../db.php');
$stores_table = "stores";	
if(!empty($_POST)) {
	$addstorename = $_POST['addstorename'];
	$addstoreaddr = $_POST['addstoreaddr'];
	$addstorecity = $_POST['addstorecity'];
	$addstorestate = $_POST['addstorestate'];
	$addstorecountry = $_POST['addstorecountry'];
	$addstorepincode = $_POST['addstorepincode'];
	$addstorezone = $_POST['storeaddzone']; 
	$finaladdr = $addstoreaddr." ".$addstorecity." ".$addstorestate." ".$addstorecountry." ".$addstorepincode;
	$coordinates = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($finaladdr) . '&sensor=true');
	$coordinates = json_decode($coordinates);
	$addstorelat = $coordinates->results[0]->geometry->location->lat;
	$addstorelong = $coordinates->results[0]->geometry->location->lng;
	
	/*$sql = "INSERT INTO ".$stores_table." (store_name, store_address, store_city, store_state, store_country, store_postalcode, store_latitude, store_longitude ,store_zone_id) VALUES ('".$addstorename."', '".$addstoreaddr."', '".$addstorecity."', '".$addstorestate."', '".$addstorecountry."', '".$addstorepincode."', '".$addstorelat."', '".$addstorelong."' ,".$addstorezone.")";
	*/
	$sql = "INSERT INTO ".$stores_table." (store_name, store_address, store_city, store_state, store_country, store_postalcode, store_latitude, store_longitude ) VALUES ('".$addstorename."', '".$addstoreaddr."', '".$addstorecity."', '".$addstorestate."', '".$addstorecountry."', '".$addstorepincode."', '".$addstorelat."', '".$addstorelong."' )";
	if ($conn->query($sql) === TRUE) {
	   echo "<script>alert('Record Added !!');</script>";
	   echo "<script>window.location = '".HOME_URL."'</script>";
	  
	} else {
		echo "<script>alert('Error while Adding Record !!');</script>";
		echo "<script>window.location = '".HOME_URL."'</script>";
	}
} else {
	echo "<script>window.location = '".HOME_URL."'</script>";
}

?>