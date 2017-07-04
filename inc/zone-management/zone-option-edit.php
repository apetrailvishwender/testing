<?php
include('../../constants.php');
include('../../db.php');
$zone_table = "zone_detail";	
if(!empty($_POST)) {
	$zone_from = $_POST['zone_from'];
	$zone_to = $_POST['zone_to'];
	$price = $_POST['price'];
	$id=$_POST['editzoneid'];
	if($zone_from == 0 || $zone_to == 0)
	{
		echo "<script>alert('Error while Adding Record. Please select zone!!');</script>";
		 echo "<script>parent.jQuery.fancybox.close();</script>";
		echo "<script>window.location = '".HOME_URL."'</script>";
	}
	if($zone_from == $zone_to)
	{
		echo "<script>alert('Error while Adding Record. \'Zone from\' and \'Zone to\' should be different.!!');</script>";
		 echo "<script>parent.jQuery.fancybox.close();</script>";
		echo "<script>window.location = '".HOME_URL."'</script>";
	}
	$qr=" select count(*) as total from ".$zone_table." where zone_from=".$zone_from." and zone_to =".$zone_to." and id !=".$id." ";
	$result = $conn->query($qr);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if($row['total'] > 0)
			{
			echo "<script>alert('Error while Adding Record. Zone option already exists!!');</script>";
			 echo "<script>parent.jQuery.fancybox.close();</script>";
			echo "<script>window.location = '".HOME_URL."'</script>";
			}
		}
	}
	$sql = "UPDATE ".$zone_table." SET zone_from=".$zone_from.", zone_to=".$zone_to.", price=".$price." where id=".$id;
	if ($conn->query($sql) === TRUE) {
   
   	 echo "<script>alert('Record Updated !!');</script>";
     echo "<script>parent.jQuery.fancybox.close();</script>";
     echo "<script>parent.location.reload(true);</script>";
   
  
} else {
	echo "<script>alert('Error while Updating Record !!');</script>";
	echo "<script>parent.jQuery.fancybox.close();</script>";
	echo "<script>parent.location.reload(true);</script>";
}

}
?>