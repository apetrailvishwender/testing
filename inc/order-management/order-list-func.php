<?php
include('../../constants.php');
include('../../db.php');
$orders_table = "orders";	
$editorderid = $_POST['editorderid'];
$editorderdriver = $_POST['ordereditdriver'];
$sql = "UPDATE ".$orders_table." SET order_driver_id='".$editorderdriver."' WHERE id=".$editorderid." ";
$conn->query($sql);
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