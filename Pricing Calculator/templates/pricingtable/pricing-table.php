<div class="alert alert-success" id="pc_field_deleted">
	<strong><?php _e('Success!', ''); ?></strong> <?php _e('One of your saved form field was deleted.', 'extendons-price-calculator'); ?>
</div>

<table class="table table-striped ext_pricing_table_class" cellspacing="0" cellpadding="6" width="100%">
	<thead class="ext_pricing_table_head">
	    <tr>
	        <td width="30%" class="text-center">
	        	<p><?php _e('Measurement Range', 'extendons-price-calculator'); ?></p>
	        </td>
	        <td width="30%">
	        	<p><?php _e('Price Per Unit', 'extendons-price-calculator'); ?></p>
	        </td>
	        <td width="30%">
	        	<p><?php _e('Sale Price Per Unit', 'extendons-price-calculator'); ?></p>
	        </td>
	        <td width="10%">
	        	<p><?php _e('Action', 'extendons-price-calculator'); ?></p>
	        </td>
	    </tr>
	</thead>

	<tbody class="new_ext_row">

		<?php
		$pc_data_ranges = get_post_meta($post->ID, '_pc_product_price_ranges', true);

		if($pc_data_ranges !='') { 
			
			$i = 100000;
			
			foreach ($pc_data_ranges as $value) { ?>

		<tr class="savedvalues_<?php echo $i;?>" id="<?php echo $i.'_savedvalues'; ?>">
			<td width="30%">
				<div class="form-group">
                    <!-- max_lw: start_rang used like max length -->
                    <!-- max_lw: end_rang used like max width -->
				    <input type="number" step="any" min="0.001" value="<?php echo $value['start_rang']; ?>" name="price_table[<?php echo $i; ?>][start_rang]" class="form-control" id="start_range" placeholder="<?php _e('Start Range', 'extendons-price-calculator'); ?>">
				    <input type="number" step="any" min="0.001" value="<?php echo $value['end_rang']; ?>" name="price_table[<?php echo $i; ?>][end_rang]" class="form-control" id="end_range" placeholder="<?php _e('End Range', 'extendons-price-calculator'); ?>">
				</div>
			</td>
			<td width="30%">
				<div class="form-group">
				    <input type="number" step="any" min="0.001" value="<?php echo $value['price_per_unit']; ?>" name="price_table[<?php echo $i; ?>][price_per_unit]" class="form-control" id="regular_price" placeholder="<?php _e('Regular Price', 'extendons-price-calculator'); ?>">
				</div>
			</td>
			<td width="30%">
				<div class="form-group">
				    <input type="number" step="any" min="0.001" value="<?php echo $value['sale_price_per_unit']; ?>" name="price_table[<?php echo $i; ?>][sale_price_per_unit]" class="form-control" id="sale_price" placeholder="<?php _e('Sale Price', 'extendons-price-calculator'); ?>">
				</div>
			</td>
			<td width="10%">
				<div class="form-group">
				    <button type="button" id="<?php echo $i.'_savedvalues'; ?>" onclick="save_delete_range_row(this.id);" class="btn btn-danger btn-block">
				    	<i class="fa fa-trash" aria-hidden="true"></i> <?php _e('Delete', 'extendons-price-calculator'); ?>
				    </button>
				    <input type="hidden" id="pc_c_product_id" value="<?php echo $post->ID; ?>">
				</div>
			</td>
		</tr>

		<?php $i++; } } ?>
	
	</tbody>
	<tfoot>
		<tr>
			<td colspan="2"></td>
			<td colspan="2">
				<button type="button" id="ext_add_new_rule_btn" class="btn btn-primary btn-block">
					<i class="fa fa-plus-square" aria-hidden="true"></i> <?php _e('Add New Row', 'extendons-price-calculator'); ?>
				</button>
			</td>
		</tr>
	</tfoot>
</table>

<script type="text/javascript">
	 // remove the row before save
    function unsaved_delete_range_row(id) {
        jQuery('tr#'+id).remove();
    }

    function save_delete_range_row(id) {
    	var condition = 'pc_delete_saved_row';
		var p_id = jQuery('#pc_c_product_id').val();
		jQuery.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				action : 'pc_deleting_saved_field',
				condition : condition,
				id : id,
				p_id : p_id,
			},
			success : function(response) {
				jQuery('#pc_field_deleted').show().delay(3000).fadeOut();
				jQuery('#'+id).remove();
			}
		});
    }

</script>