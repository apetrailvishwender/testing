<?php
$totalzones = 0;
if(!empty($_POST)) {
	$ttlzonearr = $_POST['ttlzone'];
	foreach($ttlzonearr as $ttlzone) {
		$totalzones += $ttlzone;
	}
echo "total zones :".$totalzones;
}

?>