<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(isset($_GET['msg']) && $_GET['msg'] == 'success') {
	$message = 'Rule Added Successfully!';
	echo '<div id="message" class="updated notice notice-success is-dismissible"><p>'.$message.'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
}
echo '<div id="message" class="updated notice notice-success is-dismissible notice-hidden"><p id="message_body"></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
?>

<script type="text/javascript">
    function conf(str) {
       if(confirm("Are you sure you want delete") == true){ location.replace(str);}
    }

</script>


<?php 
	global $wpdb;
	$action = "admin.php?page=eo-product-global-custom-options-add-rule";
	$action2 = "admin.php?page=eo-product-global-custom-options";
	$table_name = $wpdb->prefix . "eopa_global_rule_table";
	$table_name2 = $wpdb->prefix . "eopa_poptions_table";
	$table_name3 = $wpdb->prefix . "eopa_rowoption_table";
	$table_name4 = $wpdb->prefix . "eopa_temp_table";

	if(isset($_GET['del_id']) && $_GET['del_id'] != ''){
		if ( !current_user_can( apply_filters( 'eopa_capability', 'manage_options' ) ) )
			die( '-1' );
			$retrieved_nonce = $_REQUEST['_eopadelwpnonce'];
			if (!wp_verify_nonce($retrieved_nonce, 'delete_my_rec' ) ) die( 'Failed security check' );
		
		$wpdb->query("delete from ".$table_name."  where rule_id = '".intval($_GET['del_id'])."'");
		$wpdb->query("delete from ".$table_name2."  where global_rule_id = '".intval($_GET['del_id'])."'");
		$wpdb->query("delete from ".$table_name3."  where global_rule_id = '".intval($_GET['del_id'])."'");
		
		$message = 'Rule Deleted Successfully!';
		echo '<div id="message" class="updated notice notice-success is-dismissible"><p>'.$message.'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
		
	}




	$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
	if ( empty( $pagenum ) )
		$pagenum = 1;

	$per_page = (int) get_user_option( 'ms_users_per_page' );
	if ( empty( $per_page ) || $per_page < 1 )
		$per_page = 15;

	$per_page = apply_filters( 'ms_users_per_page', $per_page );

	if(!empty($_GET['orderby']) && !empty($_GET['order'])){
		$orderby = 'order by '.$_GET['orderby'].' '.$_GET['order'];	
		if($_GET['order'] == 'asc'){
			$actionOrder = $action2.'&orderby=rule_name&amp;order=desc';
		}
		if($_GET['order'] == 'desc'){
			$actionOrder = $action2.'&orderby=rule_name&amp;order=asc';
		}
	}else{
		$orderby = 'order by rule_id desc';	
		$actionOrder = $action2.'&orderby=rule_name&amp;order=asc';	
	}
	
	$where = '';
	if(!empty($_POST['s'])){
		$where = "WHERE rule_name like '%".$_POST['s']."%' ";
	}
	
	$query = "SELECT * FROM ".$table_name." ".$where.$orderby;
	
	$total = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT(rule_id)', $query ) );

	$query .= " LIMIT " . intval( ( $pagenum - 1 ) * $per_page) . ", " . intval( $per_page );

	$rules_list = $wpdb->get_results( $query, ARRAY_A );
	
	$num_pages = ceil( $total / $per_page );
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'paged', '%#%' ),
		'format' => '',
		'end_size'     => 1,
		'mid_size'     => 9,
		'prev_text' => __( '&laquo;' ),
		'next_text' => __( '&raquo;' ),
		'total' => $num_pages,
		'current' => $pagenum
	));

?>

<div class="wrap">
	<h2><?php _e('Product Global Addons Rules','eopa'); ?></h2>

	<h3><?php _e('Manage Rules','eopa'); ?>
		<a href="admin.php?page=eo-product-global-custom-options-add-rule" class="add-new-h2"><?php _e('Add New', 'eopa'); ?></a>
	</h3>
	<div id="import_export_form">
		<div class="form-field">
			<input type="file" name="file" id="file" />
			<button class="button button-primary" id="import-rules">Import</button>
			<div id="total"></div>
		</div>

		<div class="form-field export_field">
			<label>Export all rules.</label>
			<button class="button button-primary" id="export-rules">Export</button>
		</div>
	</div>

	<form action="" method="post">
		<p class="search-box">
			<label class="screen-reader-text" for="user-search-input"><?php _e('Search:', 'eopa'); ?></label>
			<input type="search" id="user-search-input" name="s" value="<?php if(!empty($_REQUEST['s'])) echo $_REQUEST['s']; ?>">
			<input type="submit" name="search" id="search-submit" class="button button-primary button-large" value="Search">
		</p>
	</form> 


	<?php if ( $page_links ) { ?>
	      <div class="tablenav-pages">
	        <?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
				number_format_i18n( min( $pagenum * $per_page, $total ) ),
				number_format_i18n( $total ),
				$page_links
				); echo $page_links_text; ?>
	      </div>
    <?php } ?>



	<table class="wp-list-table widefat fixed users" cellspacing="0">
		<thead>
			<tr>
	        <th scope="col" id="currencyid" class="manage-column column-currencyid sortable desc" style=" width:50px; text-align: center">
	        <span style="padding: 10px;"><?php _e("ID", 'eopa'); ?></span>
	        </th>

	        <th scope="col" id="currname" class="manage-column column-currname sortable desc" style=" text-align: center">
	        <a href="<?php echo $actionOrder?>"><span><?php _e("Rule name", "eopa"); ?></span><span class="sorting-indicator"></span></a>
	        </th>

			<th scope="col" id="status" class="manage-column column-status sortable desc" style=" text-align: center">
	        <span><?php _e("Applied On", "eopa"); ?></span>
	        </th>

	        <th scope="col" id="status" class="manage-column column-status sortable desc" style=" text-align: center">
	        <span><?php _e("Status", "eopa"); ?></span>
	        </th>

			
			<th scope="col" id="actions" class="manage-column column-counter" style=" text-align: center">
	        <span><?php _e("Actions", "eopa"); ?></span>
	        </th>
			</tr>
		</thead>

		<tbody>


		<?php if(!empty($rules_list)) { 

			$my_nonce = wp_create_nonce('delete_my_rec');
				 $i= 1;
				 foreach($rules_list as $row) {

				 	$class = 'alternate';
					if($i%2)
						$class='';

		?>


			<tr id="user-<?php echo $row['rule_id']?>" class="<?php echo $class; ?>">
				<td class="username column-username" style=" text-align: center"><?php echo $row['rule_id']; ?></td>
				
				<td class="username column-username"><!-- <a href="<?php //echo $action; ?>&id=<?php //echo $row['rule_id']?>"> -->
				<?php echo $row['rule_name']; ?>
				<!-- </a> -->
				
				</td>
				<td class="username column-username" style=" text-align: center; text-transform: capitalize; "><?php echo $row['applied_on']; ?></td>
				<td class="username column-username" style=" text-align: center;  text-transform: capitalize;"><?php echo $row['rule_status']; ?></td>
				<td class="username column-username" style=" text-align: center"><a href=<?php echo $action."&id=".$row['rule_id']?>><?php _e("Edit", "eopa"); ?></a> | <a href="javascript:void(0)" onclick="conf('<?php echo $action2; ?>&del_id=<?php echo $row['rule_id']?>&_eopadelwpnonce=<?php echo $my_nonce ?>')" ><?php _e("Delete", "eopa"); ?></a>
			</tr>

			

		<?php $i++; } } else { ?>

			<tr id="user-1" class="alternate"><td colspan="5"><?php _e("No record found.", "eopa"); ?></td></tr>

		<?php }
		wp_reset_query(); ?>

		</tbody>


		<tfoot>
			<tr>
	        <th scope="col" id="currencyid" class="manage-column column-currencyid sortable desc" style=" width:50px; text-align: center">
	        <span style="padding: 10px;"><?php _e("ID", 'eopa'); ?></span>
	        </th>

	        <th scope="col" id="currname" class="manage-column column-currname sortable desc" style=" text-align: center">
	        <a href="<?php echo $actionOrder?>"><span><?php _e("Rule name", "eopa"); ?></span><span class="sorting-indicator"></span></a>
	        </th>

			<th scope="col" id="status" class="manage-column column-status sortable desc" style=" text-align: center">
	        <span><?php _e("Applied On", "eopa"); ?></span>
	        </th>

	        <th scope="col" id="status" class="manage-column column-status sortable desc" style=" text-align: center">
	        <span><?php _e("Status", "eopa"); ?></span>
	        </th>

			
			<th scope="col" id="actions" class="manage-column column-counter" style=" text-align: center">
	        <span><?php _e("Actions", "eopa"); ?></span>
	        </th>
			</tr>
		</tfoot>
	</table>	
</div>



<script>
	var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
	var pluginUrl = '<?php echo EOPA_URL;?>';
	jQuery(document).ready(function($) {
		$('#export-rules').on('click', function() {
			$.ajax({
			    type: 'POST',
			    url: ajaxurl,
			    data: {
			    	action: 'exportRules'
			    },
			    success: function(response) {
			    	console.log(response);
			    	response = JSON.parse(response);
			    	console.log(response);
			    	if(response.status == 'success') {
			    		// window.location = pluginUrl + 'files/'+response.res;
			    		$('#message_body').text('Rules Exported Successfully!');
			    		$('#message_body').parents('#message').css('display', 'block');
			    	}
			    },
			    
			});
		});

		$('#import-rules').on('click', function import_db() {
				
				if($('#file').val() == '') 
					return false;

		        var ajaxData = new FormData();

			    ajaxData.append( 'action', 'importRules' );
			    ajaxData.append( 'is_new', 'yes' );
			    
			    
			    // or maybe skip the nonce for now

			    $.each($('#file')[0].files, function(i, file) {
			        ajaxData.append('file-'+i, file);
			    });

			    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
			    var total = $('#total');
		        $.ajax({
		            url: ajaxurl,
		            type: 'POST',
		            cache: false,
		            contentType: false,
		            processData: false,

		            data: ajaxData,

		            beforeSend: function(jqXHR, settings){
		            },
		            success: function(data, textStatus, jqXHR){
		            	// console.log(JSON.parse(jqXHR.responseText));
		            	console.log(jqXHR.responseText);
		            	var obj = JSON.parse(jqXHR.responseText);
					 //    //note that indicator is not a valid features property, you should change it!
					    if(obj.nor == 'no_record') {
				       		total.html('<div class="error extmsg"><p><?php _e("Could not import data. Check your server settings","eopa"); ?></p></div>');
				       	} else {
					    	total.html('<div class="updated extmsg"><p> <b>'+ obj.nor +'</b> <?php _e(" rules(s) are imported from the uploaded file.","eopa"); ?></p></div>');
				       	}
		            	$('#file').val('');
						
		            	var file = obj.file;
				        $.ajax({
						    type: 'POST',
						    url: ajaxurl,
						    data: {"action": "deleteImportedfile", "file":file},
						    success: function() {
						    	total.show().fadeOut(10000);
						    }
						});
		            },
		            error: function(jqXHR, textStatus, errorThrown){
		                //console.log("A JS error has occurred.");

		                total.html('<div class="error extmsg"><p><?php _e("Could not import all data because your server have low execution time, so import process is aborting. increase server max_execution_time and then retry!","eopa"); ?></p></div>');
		            },
		            complete: function(jqXHR, textStatus){
		            	total.show().fadeOut(10000);
		            	// $('#file').val('');
		                //console.log("Ajax is finished.");
		            }
		        });  
		    });
	});
</script>
