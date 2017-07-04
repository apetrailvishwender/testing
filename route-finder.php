<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Find a route using Geolocation and Google Maps API</title>
     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDYiSjgxjeUVKpD_3yIiZDuoy4OZJoAJBc&libraries=geometry" type="text/javascript"></script>
    <script src="js/jquery-1.11.3.min.js"></script>
     <script src="js/latlongLocation.js"></script>
    <?php
    include('db.php');
    include('constants.php');
    $data = $_POST['fdata'];

	foreach($_POST['fdata'] as $key=>$value)
	{
		if($value['name'] == "productids[]") {
			$post[$value['name']][] .= $value['value'];
		} else {
			$post[$value['name']] = $value['value'];
		}
		
	}

	$delivery_postal_code = $post['userpostalcode'];
	$cart_products = $post['productids[]'];
	$delivery_time = $post['deliverytime'];
	$fdelivery_time =  date('h:i a', strtotime($delivery_time));
	$delivery_date = $post['deliverydate'];
	$fdelivery_date =  date('d/m/Y', strtotime($delivery_date));
    $products_table = "products";	
	$stores_table = "stores";
	foreach($cart_products as $cart_product) {
		$products_sql = "select * from `".$products_table."` where `product_id`='".$cart_product."'";
		$product_records = $conn->query($products_sql) or die(mysql_error()); 
		if ( $product_records->num_rows > 0 ) {
			while($product_record = $product_records->fetch_assoc()) {
				$product_name = $product_record['product_name'];
				$storelist = $product_record['product_store_list'];
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
    $kml = new SimpleXMLElement(file_get_contents('StoreFinderMapnew.kml'));
	$json = json_encode($kml);
	$array = json_decode($json,TRUE);
	$placemark_arr = $array['Document']['Folder'][0]['Placemark'];
	
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
	$z = 0;
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
	}	
	$zb = 0; ?>
	<script>
	var no_of_zones = 0;
	var no_of_zonesarr = [];
	var zoneresult = "";
	var final_count = <?php echo count($finalArr); ?>;
	 var allZoneArr = <?php echo json_encode($finalArr); ?>;
	 var fzone_xcordinates = [];
	 var fzone_ycordinates = [];
	 var fzoneCoords = "";
	 var fzoneCoordsArr = [];
	 var fzoneTriangle = [];
	 var x = 0;
	 var zone_xcordinates = <?php echo json_encode($vertices_x); ?>;
	 var zone_ycordinates = <?php echo json_encode($vertices_y); ?>;
	 var zoneCoords = "";
	 var zoneCoordsArr = [];
	 for(var i = 0; i<zone_xcordinates.length;i++) {
	 	zoneCoords = {lat:  parseFloat(zone_ycordinates[i]),lng: parseFloat(zone_xcordinates[i]) };
	 	zoneCoordsArr.push(zoneCoords);
	 	
	 }
	</script>
    <style type="text/css">
      .map {
        width: 100%;
        height: 800px;
        margin-top: 10px;
      }
    </style>
  </head>
  <body>
    <?php 
	$f = 0;
    foreach($store_postalcodes as $store_postalcode) { ?>
    	<form id="calculate-route-<?php echo $f;?>" name="calculate-route-<?php echo $f;?>" action="#" method="get">
	      <input type="hidden" id="from-<?php echo $f;?>" name="from-<?php echo $f;?>" value="<?php echo $store_postalcode;?>" />
	      <!--<input type="hidden" id="ttlzone_<?php echo $f; ?>" name="ttlzone_<?php echo $f; ?>" value="" class="totalzonecs"/>-->
	      <input type="hidden" id="to-<?php echo $f;?>" name="to-<?php echo $f;?>" value="<?php echo $delivery_postal_code; ?>"/>
	    </form>
	    <div id="map-<?php echo $f;?>" class="map" style="display: none;"></div>
    <?php
     $f++;
     } ?>
     <form id="totalzoneform" name="totalzoneform" action="<?php echo APP_URL;?>/totalzones.php" method="post">
    <?php 
    $a = 0; 
    foreach($store_postalcodes as $store_postalcode) { 
    	  echo '<input type="hidden" id="ttlzone_'.$a.'" name="ttlzone[]" value="" class="totalzonecs">';     
     $a++; } ?>
     </form>
     <div id="zoneresult"></div>
     <?php
	$fj = 0 ;
	foreach($store_postalcodes as $store_postalcode) { 
	?>
	<script>
      $(window).load(function() {
      	var finalzonearr = [];
      	//var no_of_zones_<?php echo $fj; ?> = 0;
      	// If the browser supports the Geolocation API
	    if (typeof navigator.geolocation == "undefined") {
	      $("#error").text("Your browser doesn't support the Geolocation API");
	      return;
	    }
	     name = 'calculateRoute<?php echo $fj; ?>';
	     window[name] = function (from,to) {
	        var myOptions = {
	          zoom: 7,
	          center: new google.maps.LatLng(72.869186, 21.194584),
	          mapTypeId: google.maps.MapTypeId.ROADMAP
	        };
			
		  var test = 0;	
	      var mapObject = new google.maps.Map(document.getElementById("map-<?php echo $fj; ?>"), myOptions);
		
	        var directionsService = new google.maps.DirectionsService();
	        var directionsRequest = {
	          origin: from,
	          destination: to,
	          travelMode: google.maps.DirectionsTravelMode.DRIVING,
	          unitSystem: google.maps.UnitSystem.METRIC
	        };
	        directionsService.route(
	          directionsRequest,
	          function(response, status)
	          {
	            if (status == google.maps.DirectionsStatus.OK)
	            {
			    	var path = response.routes[0].overview_path;
			    	var legs = response.routes[0].legs;
					startLocation = new Object();
	        		endLocation = new Object();
			        for (i=0;i<legs.length;i++) {	
			          var steps = legs[i].steps;
			          for (j=0;j<steps.length;j++) {
		            	 startLocation.latlng = steps[j].start_location.toString();
		            	 startLocation.latlng = startLocation.latlng.replace('(', '');
		            	 startLocation.latlng = startLocation.latlng.replace(')', '');
		            	 startLocation.latlng = startLocation.latlng.replace(' ', '');
		            	 var startlocarray = startLocation.latlng.split(',');
		            	 endLocation.latlng = steps[j].end_location.toString(); 
	        			 endLocation.latlng = startLocation.latlng.replace('(', '');
		            	 endLocation.latlng = startLocation.latlng.replace(')', '');
		            	 endLocation.latlng = startLocation.latlng.replace(' ', '');
		            	 var endlocarray = endLocation.latlng.split(',');
		            	 for(var a = 0; a < allZoneArr.length; a++){
						 	fzoneCoordsArr = [];
						    var fzone_xcordinates = allZoneArr[a].coordx;
						    var fzone_ycordinates = allZoneArr[a].coordy;
						    var fzone_name = allZoneArr[a].zonename[0];
						    for(var i = 0; i<fzone_xcordinates.length;i++) {
							 	fzoneCoords = {lat:  parseFloat(fzone_xcordinates[i]),lng: parseFloat(fzone_ycordinates[i]) };
							 	fzoneCoordsArr.push(fzoneCoords);
							 	
							}
							fzoneTriangle = new google.maps.Polygon({
							    paths: fzoneCoordsArr,
							    strokeColor: '#FFFF',
							    strokeOpacity: 0.8,
							    strokeWeight: 2,
							    fillColor: '#7e7d82',
							    fillOpacity: 0.01
							});
						    fzoneTriangle.setMap(mapObject);
						    
						    var sisWithinPolygon = fzoneTriangle.containsLatLng(parseFloat(startlocarray[0]), parseFloat(startlocarray[1]));
						 	if(sisWithinPolygon == true) {
						 		if(j==0) {
								}
								if(j == (steps.length-1)) {	
								} 
						 		finalzonearr.push(fzone_name);
						 		finalzonearr = $.unique(finalzonearr);
							}
						 }  
			          }
			        }
			        test = finalzonearr.length;
			        $("#ttlzone_<?php echo $fj; ?>").val(test);
			        //$("input[name='ttlzone[]'").val(test);
	             	new google.maps.DirectionsRenderer({
	                map: mapObject,
	                directions: response
	              });
	            } else {
					$("#error").append("Unable to retrieve your route<br />");
				}
	          }
	        );
	      }	
     	  calculateRoute<?php echo $fj; ?>($("#from-<?php echo $fj; ?>").val(), $("#to-<?php echo $fj;?>").val());
      });
    </script>
    <?php $fj++; } ?>
    <script>
    	$(document).ready(function() { 
    	    var totalzones = 0;
			setTimeout(alertFunc, 5000);
			function alertFunc() {
	    		$( ".totalzonecs" ).each(function() {
	    		  totalzones += parseInt($( this ).val()) ;
				});	
				//$("#totalzoneform").submit();
			}
    	});
    </script>
  </body>
</html>