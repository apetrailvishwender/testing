<?php
include('../../constants.php');
include('../../db.php');
$zone_table = "zone_detail";	
if(!empty($_POST)) {
	$zone_from = $_POST['zone_from'];
	$zone_to = $_POST['zone_to'];
	$price = $_POST['price'];
	if($zone_from == 0 || $zone_to == 0)
	{
		echo "<script>alert('Error while Adding Record. Please select zone!!');</script>";
		echo "<script>window.location = '".HOME_URL."'</script>";
	}
	else if($zone_from == $zone_to)
	{
		echo "<script>alert('Error while Adding Record. \'Zone from\' and \'Zone to\' should be different.!!');</script>";
		echo "<script>window.location = '".HOME_URL."'</script>";
	}
	else
	{
		$qr=" select count(*) as total from ".$zone_table." where zone_from=".$zone_from." and zone_to =".$zone_to." ";
		$result = $conn->query($qr);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if($row['total'] > 0)
				{
				echo "<script>alert('Error while Adding Record. Zone option already exists!!');</script>";
				echo "<script>window.location = '".HOME_URL."'</script>";
				}
			}
		}
		
		$sql = "INSERT INTO ".$zone_table." (zone_from, zone_to, price ) VALUES (".$zone_from.", ".$zone_to.", ".$price.")";
		if ($conn->query($sql) === TRUE) {
		   echo "<script>alert('Record Added !!');</script>";
		   echo "<script>window.location = '".HOME_URL."'</script>";
		  
		} else {
			echo "<script>alert('Error while Adding Record !!');</script>";
			echo "<script>window.location = '".HOME_URL."'</script>";
		}
	}
} else {
	echo "<script>window.location = '".HOME_URL."'</script>";
}

?>