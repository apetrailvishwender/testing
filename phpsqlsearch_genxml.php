<?php
include('db.php');
$stores_table = "stores";	
$zones_table = "zones";	
$users_sql = "select * from ".$stores_table." where store_is_deleted='0' order by store_id DESC";
$users_records = $conn->query($users_sql);

$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];

$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);


$query = sprintf("SELECT * ,( 6371 * acos( cos( radians('%s') ) * cos( radians( store_latitude ) ) * cos( radians( store_longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( store_latitude ) ) ) ) AS store_distance FROM stores ORDER BY store_distance",
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($center_lng),
  mysql_real_escape_string($center_lat));
    
$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}
header("Content-type: text/xml");


while ($row = @mysql_fetch_assoc($result)){
  	
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("name", $row['store_name']);
  $newnode->setAttribute("address", $row['store_address'].','.$row['store_city'].','.$row['store_state'].','.$row['store_country'].' - '.$row['store_postalcode']);
  $newnode->setAttribute("lat", $row['store_latitude']);
  $newnode->setAttribute("lng", $row['store_longitude']);
  $newnode->setAttribute("distance", $row['store_distance']);
}
echo $dom->saveXML();
?>