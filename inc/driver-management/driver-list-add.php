<?php
include('../../constants.php');
include('../../db.php');
$drivers_table = "drivers";	
$adddrivername = $_POST['adddrivername'];
$adddriverzone = $_POST['driveraddzone']; 
$sql = "INSERT INTO ".$drivers_table." (driver_name, driver_zone_id) VALUES ('".$adddrivername."', ".$adddriverzone.")";

if ($conn->query($sql) === TRUE) {
   echo "<script>alert('Record Added !!');</script>";
   echo "<script>window.location = '".HOME_URL."'</script>";
  
} else {
	echo "<script>alert('Error while Adding Record !!');</script>";
	echo "<script>window.location = '".HOME_URL."'</script>";
}
?>