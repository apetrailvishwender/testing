<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "247dev"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
	/*Stores Table*/

	$store_sql = "CREATE TABLE IF NOT EXISTS `stores` (
	   `store_id` int(11) unsigned NOT NULL auto_increment,
	   `store_name` varchar(100) DEFAULT NULL ,
	   `store_address` varchar(255) DEFAULT NULL ,
	   `store_city`	varchar(80) DEFAULT NULL,
	   `store_state` varchar(80) DEFAULT NULL,
	   `store_country`	varchar(80) DEFAULT NULL,
	   `store_postalcode`	varchar(25) DEFAULT '00000',
	   `store_latitude`	float(10,6) DEFAULT NULL,
	   `store_longitude` float(10,6) DEFAULT NULL,
	   `store_zone_id`	int(11),
	   `store_is_deleted` int(1) DEFAULT 0,
	   	PRIMARY KEY  (`store_id`)
	   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

	$conn->query($store_sql);

	/* Zones Table */

	$zone_sql = "CREATE TABLE IF NOT EXISTS `zones` (
	   `zone_id` int(11) unsigned NOT NULL auto_increment,
	   `zone_name` varchar(100) DEFAULT NULL ,
	   `zone_coordinates` TEXT DEFAULT NULL, 
	   `zone_delivery_price` float(10,2) DEFAULT NULL ,
	   `zone_urgent_delivery_price`	float(10,2) DEFAULT NULL,
	   `zone_central_zipcode` int(10) DEFAULT NULL,
	   `zone_centralzipcode_latitude_` varchar(50) DEFAULT NULL ,
	   `zone_centralzipcode_longitude` varchar(50) DEFAULT NULL ,
	   `zone_is_deleted` int(1) DEFAULT 0,
	   	PRIMARY KEY  (`zone_id`)
	   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

 	/*$zone_sql = "CREATE TABLE IF NOT EXISTS `zones` (
	   `zone_id` int(11) unsigned NOT NULL auto_increment,
	   `zone_name` varchar(100) DEFAULT NULL UNIQUE,
	   `zone_coordinates` TEXT DEFAULT NULL, 
	   `zone_is_deleted` int(1) DEFAULT 0,
	   	PRIMARY KEY  (`zone_id`)
	   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";*/
	$conn->query($zone_sql);

	/* Drivers Table */

	$driver_sql = "CREATE TABLE IF NOT EXISTS `drivers` (
	   `driver_id` int(11) unsigned NOT NULL auto_increment,
	   `driver_name` varchar(100) DEFAULT NULL ,
	   `driver_zone_id` int(11) DEFAULT NULL ,
	   `driver_is_deleted` int(1) DEFAULT 0,
	   	PRIMARY KEY  (`driver_id`)
	   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

	$conn->query($driver_sql);

	/* Orders Table */

	$order_sql = "CREATE TABLE IF NOT EXISTS `orders` (
	   `id` int(11) unsigned NOT NULL auto_increment,
	   `order_id` varchar(50) DEFAULT NULL UNIQUE,
	   `order_price` float(10,6) DEFAULT NULL,
	   `customer_email` varchar(255) DEFAULT NULL,
	   `order_shipping_address`	varchar(255) DEFAULT NULL,
	    `order_driver_id` int(11) DEFAULT NULL,
	   	PRIMARY KEY  (`id`)
	   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

	$conn->query($order_sql);

	/*Products Table*/

	$product_sql = "CREATE TABLE IF NOT EXISTS `products` (
	   `id` int(11) unsigned NOT NULL auto_increment,
	   `product_id` varchar(50) DEFAULT NULL UNIQUE,
	   `product_name` varchar(100) DEFAULT NULL ,
	   `product_description` varchar(255) DEFAULT NULL ,
	   `product_price`	varchar(80) DEFAULT NULL,
	   `product_img` TEXT DEFAULT NULL,
	   `product_store_list` TEXT DEFAULT NULL,
	   	PRIMARY KEY  (`id`)
	   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

	$conn->query($product_sql);
	

}


?>