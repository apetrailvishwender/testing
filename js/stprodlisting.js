$(document).ready(function(){
	
	setTimeout(UpdateFunc, 1000);
	//var timer, delay = 1000000; //5 minutes counted in milliseconds.
	function UpdateFunc() {
		$.ajax({
				type: "POST",			
				dataType: "json",	
				url:"https://mycloudsportal.com/app/inc/store-management/store-list-prod.php", 
				beforeSend: function(){
					$("#preloaderdiv").show();
				},
				success:function(response) {
					$("#preloaderdiv").hide();
					if(response.status == 1) {
	                	//console.log(response.storeproddetails);
	                	$(".collection_listing_main").append(response.storeproddetails);
					} else if(response.status == 2) {							
						alert("Sorry, problems are getting while getting delivery prices.!!");			
						$("#preloaderdiv").hide();		
					} else {
						alert(response);
					}		
				}
		});	
	}	
});