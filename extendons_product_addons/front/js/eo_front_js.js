jQuery(document).ready(function($) { 


	$( ".datepick" ).datepicker({
		 showOn: "button",
		 buttonImage: URL+"images/calendar_clock.png",
		 buttonImageOnly: true,
	});
	$( ".timepick" ).timepicker({
	    showSecond: false,
	    showOn: "button",
		buttonImage: URL+"images/clock.png",
		buttonImageOnly: true,

	});


});


jQuery('#aaa').click(function(){ 
	jQuery().fancybox({
	  
	  thumbs   : false,
	  hash     : false,
	});
});

