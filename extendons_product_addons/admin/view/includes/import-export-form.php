<form id="import_export_form">
	<h3>Import/Export Products</h3>
	<div class="formfield">
		<input type="file" name="file" id="file" />
		<button class="button button-primary" id="import">Import</button>
	</div>
	<div class="formfield export_field">
		<label>Export all products along with custom field.</label>
		<button class="button button-primary" id="export">Export</button>
	</div>
</form>

<script>
	var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
	jQuery(document).ready(function($) {
		$('#export').on('click', function() {
			$.ajax({
			    type: 'POST',
			    url: ajaxurl,
			    data: {
			    	action: 'exportData'
			    },
			    success: function(response) {
			    	console.log(response);
			  //   	response = JSON.parse(response);

			  //   	var total = $('#total');
			  //      	if(response.nor == 'no_record') {
			  //      		total.html('<div class="error extmsg"><p><?php _e("No record found!","eopa"); ?></p></div>');
			  //      		total.show().fadeOut(10000);
			  //      	} else {
			  //      		window.location.href = urlPath+'files/'+response.zip;
			  //      		total.html('<div class="updated extmsg"><p> <b>'+response.records+'</b> <?php _e(" record(s) are exported in <b>'+response.total+'</b> file(s) and downloaded as zip file.","eopa"); ?></p></div>');
			  //      		total.show().fadeOut(10000);
			  //      	}
			  //       var file = dirPath + 'files/'+response.zip;
			  //       $.ajax({
					//     type: 'POST',
					//     url: ajaxurl,
					//     data: {"action": "deleteFile", "file":file},
					//     success: function() {
					//     	$('#extcsv_expbt').prop('disabled', false);
					//     }
					// });
			    },
			    
			});
		});
	});
</script>