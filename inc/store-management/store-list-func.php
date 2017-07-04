<?php
include('../../constants.php');
include('../../db.php');
$stores_table = "stores";	
$editstoreid = $_POST['editstoreid'];
$editstorename = $_POST['editstorename'];
$editstoreaddr = $_POST['editstoreaddr'];
$editstorecity = $_POST['editstorecity'];
$editstorestate = $_POST['editstorestate'];
$editstorecountry = $_POST['editstorecountry'];
$editstorepincode = $_POST['editstorepincode'];
/*$editstorezone = $_POST['storeeditzone'];*/
$finaladdr = $editstoreaddr." ".$editstorecity." ".$editstorestate." ".$editstorecountry." ".$editstorepincode;
$edcoordinates = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($finaladdr) . '&sensor=true');
$edcoordinates = json_decode($edcoordinates);
$editstorelat  = $edcoordinates->results[0]->geometry->location->lat;
$editstorelong = $edcoordinates->results[0]->geometry->location->lng;

$sql = "UPDATE ".$stores_table." SET store_name='".$editstorename."', store_address='".$editstoreaddr."',store_city='".$editstorecity."',store_state='".$editstorestate."',store_country='".$editstorecountry."',store_postalcode='".$editstorepincode."', store_latitude='".$editstorelat."',store_longitude='".$editstorelong."' ,store_zone_id ='' WHERE store_id=".$editstoreid." ";

if ($conn->query($sql) === TRUE) {
   echo "<script>alert('Record Updated !!');</script>";
   echo "<script>parent.jQuery.fancybox.close();</script>";
   echo "<script>parent.location.reload(true);</script>";
  
} else {
	echo "<script>alert('Error while Updating Record !!');</script>";
	echo "<script>parent.jQuery.fancybox.close();</script>";
    echo "<script>parent.location.reload(true);</script>";
}
?>