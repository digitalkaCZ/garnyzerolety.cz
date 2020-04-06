<?php
require_once EOPA_PLUGIN_DIR . 'admin/class-eo-product-addons-admin.php';
$custom_options = new EO_Product_Addons_Admin();
$product_options = $custom_options->getProductOptions($post->ID);
$meta_value = get_post_meta( $post->ID, '_exclude_global_options', true );
//print_r($product_options);

//echo $post->ID;
?>

<script type="text/javascript">
	function showHideStock(fieldId) {

		if(jQuery('#field' + fieldId).find('select[name="product_option['+ fieldId +'][manage_stock]"]').val() == 'yes') {

			jQuery('#field' + fieldId).find('.datath5').each(function() {
				jQuery(this).show();
			});
		} else {
			jQuery('#field' + fieldId).find('.datath5').each(function() {
			    console.log(jQuery(this));
				jQuery(this).hide();
			});
		}
	}
</script>
			
<div class="field_wrapper">
	<div class="field_success"></div>
	<div class="addButton">
		<input onClick="addFields()" type="button" id="btnAdd" class="button button-primary button-large" value="<?php echo _e('Add New Option','eopa'); ?>"> 
	</div>

	<form id="featured_upload" method="post" action="" enctype="multipart/form-data">
		<div class="addFormFields">
			<label><b><?php echo _e('Exclude Global Options ?', 'eopa'); ?></b></label>
			<input <?php echo checked('yes', $meta_value); ?> type="checkbox" name="exclude_global_options" value="yes" style="margin-top: 2px; margin-left: 10px;">
		</div>
		<?php 
			if(count($product_options)!= 0) {
			foreach ($product_options as $product_option) { 

			$product_option_rows = $custom_options->getProductOptionRows($product_option->id);
		?>
		<input type="hidden" value="yes" name="editpro" />
		<input type="hidden" value="<?php echo $product_option->id; ?>" name="product_option[<?php echo $product_option->id; ?>][option_id]" />
		<div class="addFormFields" id="field<?php echo $product_option->id; ?>">
			<input onClick="delFields('<?php echo $product_option->id; ?>')" type="button" class="btnDel button btn-danger button-large" value="<?php echo _e('Delete Option','eopa'); ?>">
			<div class="topFields">
				<table class="datatable">
					<thead>
					    <tr>
					    	<th class="datath1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
					    	<th class="datath2"><label><b><?php echo _e('Input Type:','eopa'); ?></b></label></th>
					    	<th class="datath3"><label><b><?php echo _e('Is Required:','eopa'); ?></b></label></th>
					    	<th class="datath4"><label><b>Sort Order:</b></label></th>
					    	</tr>
					</thead>
					<tbody>
						<tr>
							<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option->option_title; ?>" name="product_option[<?php echo $product_option->id; ?>][option_title]"  id="title" /></td>
							<td class="datath2">
					    		<select class="select_type inputs" name="product_option[<?php echo $product_option->id; ?>][option_type]" id="type" onChange="showFields('<?php echo $product_option->id; ?>',this.value)">
					    			<option value=""><?php echo _e('-- Please select --','eopa'); ?></option>
					    			<optgroup label="Text">
					    				<option value="field" <?php selected('field',$product_option->option_field_type); ?>><?php echo _e('Field','eopa'); ?></option>
					    				<option value="area" <?php selected('area',$product_option->option_field_type); ?>><?php echo _e('Area','eopa'); ?></option>
					    			</optgroup>
					    			<optgroup label="File">
					    				<option value="file" <?php selected('file',$product_option->option_field_type); ?>><?php echo _e('File','eopa')?></option>
					    			</optgroup>
					    			<optgroup label="Select">
					    				<option value="drop_down" <?php selected('drop_down',$product_option->option_field_type); ?>><?php echo _e('Drop-down','eopa'); ?></option>
		            					<option value="radio" <?php selected('radio',$product_option->option_field_type); ?>><?php echo _e('Radio Buttons','eopa'); ?></option>
		            					<option value="simple_radio" <?php selected('simple_radio',$product_option->option_field_type); ?>><?php echo _e('Simple Radio Buttons','eopa'); ?></option>
		            					<option value="checkbox" <?php selected('checkbox',$product_option->option_field_type); ?>><?php echo _e('Checkbox','eopa'); ?></option>
		            					<option value="multiple" <?php selected('multiple',$product_option->option_field_type); ?>><?php echo _e('Multiple Select','eopa'); ?></option>
		            				</optgroup>
				            		<optgroup label="Date">
				            			<option value="date" <?php selected('date',$product_option->option_field_type); ?>><?php echo _e('Date','eopa'); ?></option>
				            			<option value="time" <?php selected('time',$product_option->option_field_type); ?>><?php echo _e('Time','eopa')?></option>
				            		</optgroup>
				            		<optgroup label="Color">
				            			<option value="color" <?php selected('color',$product_option->option_field_type); ?>><?php echo _e('Color Picker','eopa'); ?></option>
				            		</optgroup>
				            		<optgroup label="Google Fonts">
				            			<option value="google_font" <?php selected('google_font',$product_option->option_field_type); ?>><?php echo _e('Google Fonts','eopa'); ?></option>
				            		</optgroup>
				            		<optgroup label="Google Map">
				            			<option value="google_map" <?php selected('google_map',$product_option->option_field_type); ?>><?php echo _e('Google Map','eopa'); ?></option>
				            		</optgroup>
				            		<optgroup label="Range Picker Field">
				            			<option value="range_picker" <?php selected('range_picker',$product_option->option_field_type); ?>><?php echo _e('Range Picker','eopa'); ?></option>
				            		</optgroup>
		            			</select>
					        </td>
							<td class="datath3">
								<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][option_is_required]" id="is_required">
			                		<option value="yes" <?php selected('yes',$product_option->option_is_required); ?>><?php echo _e('Yes','eopa'); ?></option>
			                		<option value="no" <?php selected('no',$product_option->option_is_required); ?>><?php echo _e('No','eopa'); ?></option>
			                	</select>
							</td>
							<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option->option_sort_order; ?>" name="product_option[<?php echo $product_option->id; ?>][option_sort_order]" id="sort_order<?php echo $product_option->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="stock_wrapper">
				<label>Manage Stock</label>
				<select name="product_option[<?php echo $product_option->id; ?>][manage_stock]">
					<option value="no" <?php echo selected($product_option->manage_stock, 'no');?>>No</option>
					<option value="yes" <?php echo selected($product_option->manage_stock, 'yes');?>>Yes</option>
				</select>
			</div>
			<div class="widd">
				<label for="clogic"><?php _e('Conditional Logic:','eopa'); ?></label>
	  			<div class="widd_wrapper">
		  			<div class="showf">
		  				<select name="product_option[<?php echo $product_option->id; ?>][showif]">
		  					<option value="" <?php echo selected($product_option->showif,''); ?>><?php _e('Select','eopa'); ?></option>
		  					<option value="Show" <?php echo selected($product_option->showif,'Show'); ?>><?php _e('Show','eopa'); ?></option>
		  					<option value="Hide" <?php echo selected($product_option->showif,'Hide'); ?>><?php _e('Hide','eopa'); ?></option>
		  				</select>
		  			</div>
		  			<div class="showf_text"><?php _e('if value of','eopa'); ?></div>
		  			<div class="showf clshowf" id="cl">
		  				<?php 
		  				global $wpdb;
			            $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE id!=%d AND global_rule_id IS NULL", $product_option->id));      
			            ?>
			            <select name="product_option[<?php echo $product_option->id; ?>][cfield]" class="cfields">
			            <option value=""><?php _e('Select','eopa'); ?></option>
			            <?php 
			            foreach($results as $res) { ?>
							<option value="<?php echo $res->id; ?>" <?php echo selected($product_option->cfield,$res->id); ?>><?php echo $res->option_title; ?></option>
			            <?php } ?>
			            </select>
		  			</div>
		  			<div class="showf" id="cll">
		  				<select id="cll_select" name="product_option[<?php echo $product_option->id; ?>][ccondition]" class="cfields">
		  					<option value="" <?php echo selected($product_option->ccondition,''); ?>><?php _e('Select','eopa'); ?></option>
		  					<option value="is not empty" <?php echo selected($product_option->ccondition,'is not empty'); ?>><?php _e('is not empty','eopa'); ?></option>
		  					<option value="is equal to" <?php echo selected($product_option->ccondition,'is equal to'); ?>><?php _e('is equal to','eopa'); ?></option>
		  					<option value="is not equal to" <?php echo selected($product_option->ccondition,'is not equal to'); ?>><?php _e('is not equal to','eopa'); ?></option>
		  					<option value="is checked" <?php echo selected($product_option->ccondition,'is checked'); ?>><?php _e('is checked','eopa'); ?></option>
		  				</select>
		  			</div>

		  			<div class="showf" id="clll">
		  				<input type="text" name="product_option[<?php echo $product_option->id; ?>][ccondition_value]" class="clll_field" size="13" value="<?php echo $product_option->ccondition_value; ?>">
		  			</div>
	  			</div>
	  		</div>


			<div class="bottom_fields">
				<div id="textField<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'field') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath1"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Max Characters:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option->option_price; ?>" name="product_option[<?php echo $product_option->id; ?>][text_option_price]" id="price<?php echo $product_option->id ?>" onChange="PriceOnly(this.id);" /></td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][text_option_price_type]" id="fieldprice_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
								<td class="datath3"><input class="inputs" type="text" value="<?php echo $product_option->option_maxchars; ?>" name="product_option[<?php echo $product_option->id; ?>][text_option_maxchars]" id="maxchars<?php echo $product_option->id; ?>" onChange="MaxCharsonlyNumber(this.id);" /></td>
								<td class="datath5">
									<input type="text" name="product_option[<?php echo $product_option->id;?>][text_stock]" value="<?php echo $product_option->stock;?>" class="ffstock" />
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][field_price_per_char]" value="1" <?php checked('1',$product_option->enable_price_per_char); ?> />
									<?php _e('Enable Price per character', 'eopa'); ?>
								</td>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][field_multiply_price_by_qty]" value="1" <?php checked('1', $product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>

				<div id="textArea<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'area') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath1"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Max Characters:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option->option_price; ?>" name="product_option[<?php echo $product_option->id; ?>][area_option_price]" id="price<?php echo $product_option->id ?>" onChange="PriceOnly(this.id);" /></td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][area_option_price_type]" id="areaprice_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
								<td class="datath3"><input class="inputs" type="text" value="<?php echo $product_option->option_maxchars; ?>" name="product_option[<?php echo $product_option->id; ?>][area_option_maxchars]" id="maxchars<?php echo $product_option->id; ?>" onChange="MaxCharsonlyNumber(this.id);" /></td>
								<td class="datath5">
									<input type="text" name="product_option[<?php echo $product_option->id;?>][area_stock]" value="<?php echo $product_option->stock;?>" />
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][textarea_price_per_char]"  value="1"   <?php checked('1',$product_option->enable_price_per_char); ?> />
									<?php _e('Enable Price per character', 'eopa'); ?>
								</td>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][textarea_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="file<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'file') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath1"><label><b><?php echo _e('Allowed Extensions <span>(add with comma(,) separated)</span>:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath3"><input class="inputs" type="text" value="<?php echo $product_option->option_price; ?>" name="product_option[<?php echo $product_option->id; ?>][file_option_price]" id="price<?php echo $product_option->id ?>" onChange="PriceOnly(this.id);" /></td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][file_option_price_type]" id="areaprice_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
								<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option->option_allowed_file_extensions; ?>" name="product_option[<?php echo $product_option->id; ?>][option_allowed_file_extensions]" id="fileallowedex" /></td>
								<td class="datath5">
									<input type="text" name="product_option[<?php echo $product_option->id; ?>][stock]" value="<?php echo $product_option->stock;?>" />
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][file_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="dropdown<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'drop_down') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
					<table class="datatable" id="POITable<?php echo $product_option->id; ?>">
						<thead>
						    <tr>
						    	<th class="datath1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath4"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    	<th></th>
						    </tr>
						</thead>
						<tbody>

						<?php if($product_option->option_field_type == 'drop_down') { ?>
						<?php foreach ($product_option_rows as $product_option_row) { ?>
							
							<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
								<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_title]" id="dropdowntitle" /></td>
								<td class="datath2">
						    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
						        </td>
								<td class="datath3">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_price_type]" id="dropdownprice_type">
				                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
								<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
								<td class="datath5">
									<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
								</td>
								<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
							</tr>

						<?php } } ?>

							<!-- <tr>
								<td class="<?php echo $product_option->id; ?>droprowdata" colspan="5">
									<?php $custom_options->addForm2(0,$product_option->id); ?>
								</td>
							</tr> -->

						</tbody>
						
						<tfoot>
							<tr class="addButton">
								<td><input onClick="addNewDropRow(this, <?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
								<td colspan="4">
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][dropdown_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							
						</tbody>
					</table>
				</div>
				<div id="multiselect<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'multiple') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
					<table class="datatable" id="MultiTable<?php echo $product_option->id; ?>">
						<thead>
						    <tr>
						    	<th class="datath1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath4"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    	<th></th>
						    </tr>
						</thead>
						<tbody>
							<?php if($product_option->option_field_type == 'multiple') { ?>
							<?php foreach ($product_option_rows as $product_option_row) { ?>
							
								<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
									<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_title]" id="dropdowntitle" /></td>
									<td class="datath2">
							    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
							        </td>
									<td class="datath3">
										<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_price_type]" id="dropdownprice_type">
					                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
									</td>
									<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
								</tr>
							
							<?php } } ?>

							<tr>
								<td class="<?php echo $product_option->id; ?>multirowdata" colspan="5">
									<?php $custom_options->addMultiForm(0,$product_option->id); ?>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr class="addButton">
						   		<td><input onClick="addNewMultiRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
						   		<td colspan="4">
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][ms_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
						   	</tr>
						</tfoot>
						<tbody>
							
						</tbody>
					</table>
				</div>
				<div id="radio<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'radio') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
					<table class="datatable" id="RadioTable<?php echo $product_option->id; ?>">
						<thead>
						    <tr>
						    	<th style="display:none"></th>
						    	<th class="radioth1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
						    	<th><label><b><?php echo _e('Radio Image', 'eopa'); ?></b></label></th>
						    	<th><label><b><?php echo _e('Product Image', 'eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath4"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    	<th></th>
						    </tr>
						</thead>
						<tbody>
							<?php if($product_option->option_field_type == 'radio') { ?>
							<?php foreach ($product_option_rows as $product_option_row) { ?>
							
								<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
									<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_title]" id="dropdowntitle" /></td>
									
									<td>
										<div class="imgdis" id="radioimgdisplay<?php echo $product_option_row->id; ?>">
											<img src="<?php echo $product_option_row->option_image; ?>" width="50" />
										</div>
										<input type="hidden" value="<?php echo $product_option_row->option_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_image_url]" id="radioimage_url<?php echo $product_option_row->id; ?>" class="regular-text">
										<input onClick="radioimm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
										
									</td>

									<td>
										<div class="imgdis" id="radioproimgdisplay<?php echo $product_option_row->id; ?>">
											<img src="<?php echo $product_option_row->option_pro_image; ?>" width="50" />
										</div>
										<input type="hidden" value="<?php echo $product_option_row->option_pro_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_proimage_url]" id="radioproimage_url<?php echo $product_option_row->id; ?>" class="regular-text">
										<input onClick="radioproimm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
										
									</td>

									<td class="datath2">
							    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
							        </td>
									<td class="datath3">
										<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
					                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
									</td>
									<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
								</tr>
							
							<?php } } ?>

							<tr>
								<td class="<?php echo $product_option->id; ?>radiorowdata" colspan="7">
									<?php $custom_options->addRadioForm(0,$product_option->id); ?>
								</td>
							</tr>

						</tbody>
						<tfoot>
							<tr class="addButton">
						   		<td colspan="7"><input onClick="addNewRadioRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
						   		<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][radio_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
						   	</tr>
						</tfoot>
						<tbody>
							
						</tbody>
					</table>
				</div>

				<div id="simple_radio<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'simple_radio') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
					<table class="datatable" id="RadioTable<?php echo $product_option->id; ?>">
						<thead>
						    <tr>
						    	<th style="display:none"></th>
						    	<th class="datath1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
						    	
						    	<th class="datath2"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath4"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    	<th></th>
						    </tr>
						</thead>
						<tbody>
							<?php if($product_option->option_field_type == 'simple_radio') { ?>
							<?php foreach ($product_option_rows as $product_option_row) { ?>
							
								<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
									<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_title]" id="dropdowntitle" /></td>
									
									

									<td class="datath2">
							    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
							        </td>
									<td class="datath3">
										<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
					                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
									</td>
									<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
								</tr>
							
							<?php } } ?>

							<tr>
								<td class="<?php echo $product_option->id; ?>simpleradiorowdata" colspan="7">
									<?php $custom_options->addSimpleRadioForm(0,$product_option->id); ?>
								</td>
							</tr>

						</tbody>
						<tfoot>
							<tr class="addButton">
						   		<td colspan="7"><input onClick="addNewSimpleRadioRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
						   		<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][sr_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
						   	</tr>
						</tfoot>
						<tbody>
							
						</tbody>
					</table>
				</div>

				<div id="checkbox<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'checkbox') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
					<table class="datatable" id="CheckboxTable<?php echo $product_option->id; ?>">
						<thead>
						    <tr>
						    	<th style="display:none"></th>
						    	<th class="datath1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
						    	<th><label><b><?php echo _e('Checkbox Image', 'eopa'); ?></b></label></th>
						    	<th><label><b><?php echo _e('Product Image', 'eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath4"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
						    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
						    	<th></th>
						    </tr>
						</thead>
						<tbody>
							
						<?php if($product_option->option_field_type == 'checkbox') { ?>
							<?php foreach ($product_option_rows as $product_option_row) { ?>
							
								<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
									<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_title]" id="dropdowntitle" /></td>
									
									<td>
										<div class="imgdis" id="checkboximgdisplay<?php echo $product_option_row->id; ?>">
											<img src="<?php echo $product_option_row->option_image; ?>" width="50" />
										</div>
										<input type="hidden" value="<?php echo $product_option_row->option_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_image_url]" id="checkboximage_url<?php echo $product_option_row->id; ?>" class="regular-text">
										<input onClick="checkboximm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
										
									</td>

									<td>
										<div class="imgdis" id="checkboxproimgdisplay<?php echo $product_option_row->id; ?>">
											<img src="<?php echo $product_option_row->option_pro_image; ?>" width="50" />
										</div>
										<input type="hidden" value="<?php echo $product_option_row->option_pro_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_proimage_url]" id="checkboxproimage_url<?php echo $product_option_row->id; ?>" class="regular-text">
										<input onClick="checkboxproimm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
										
									</td>

									<td class="datath2">
							    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
							        </td>
									<td class="datath3">
										<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_price_type]" id="dropdownprice_type">
					                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
									</td>
									<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
								</tr>
							
							<?php } } ?>

							<tr>
								<td class="<?php echo $product_option->id; ?>checkboxrowdata" colspan="7">
									<?php $custom_options->addCheckboxForm(0,$product_option->id); ?>
								</td>
							</tr>

						</tbody>
						<tfoot>
							<tr class="addButton">
						   		<td colspan="7"><input onClick="addNewCheckboxRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
						   		<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][cb_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
						   	</tr>
						</tfoot>
						<tbody>
							
						</tbody>
					</table>
				</div>
				
				<div id="date<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'date') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath3"><input class="inputs" type="text" value="<?php echo $product_option->option_price; ?>" name="product_option[<?php echo $product_option->id; ?>][date_option_price]" id="price<?php echo $product_option->id ?>" onChange="PriceOnly(this.id);" /></td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][date_option_price_type]" id="dateprice_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][date_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="time<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'time') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath3"><input class="inputs" type="text" value="<?php echo $product_option->option_price; ?>" name="product_option[<?php echo $product_option->id; ?>][time_option_price]" id="price<?php echo $product_option->id ?>" onChange="PriceOnly(this.id);" /></td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][time_option_price_type]" id="timeprice_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][time_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="color<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'color') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath3">
									<input class="inputs" type="text" name="product_option[<?php echo $product_option->id; ?>][color_option_price]" id="price<?php echo $product_option->id; ?>" onChange="PriceOnly(this.id);" value="<?php echo $product_option->option_price;?>" />
								</td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][color_option_price_type]" id="colorprice_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][color_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="google_font<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'google_font') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath3">
									<input class="inputs" type="text" name="product_option[<?php echo $product_option->id; ?>][google_font_option_price]" id="price<?php echo $product_option->id; ?>" onChange="PriceOnly(this.id);" value="<?php echo $product_option->option_price;?>" />
								</td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][google_font_option_price_type]" id="google_font_price_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][gf_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="google_map<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'google_map') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath3">
									<input class="inputs" type="text" name="product_option[<?php echo $product_option->id; ?>][google_map_option_price]" id="price<?php echo $product_option->id; ?>" onChange="PriceOnly(this.id);" value="<?php echo $product_option->option_price;?>" />
								</td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][google_map_option_price_type]" id="google_map_price_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][gm_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
				<div id="range_picker<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'range_picker') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath1"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Min Value:','eopa'); ?></b></label></th>
						    	<th class="datath4"><label><b><?php _e('Max Value:', 'eopa');?></b></label></th>
						    </tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath1">
									<input class="inputs" type="text" name="product_option[<?php echo $product_option->id; ?>][range_picker_option_price]" id="price<?php echo $product_option->id; ?>" onChange="PriceOnly(this.id);" value="<?php echo $product_option->option_price;?>" />
								</td>
								<td class="datath2">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][range_picker_option_price_type]" id="range_picker_price_type">
				                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
				                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
				                	</select>
								</td>
								<td class="datath3">
									<input class="inputs" type="text" name="product_option[<?php echo $product_option->id; ?>][range_picker_min_value]" id="min_value<?php echo $product_option->id; ?>" value="<?php echo $product_option->min_value;?>" />
								</td>
								<td class="datath4">
									<input class="inputs" type="text" name="product_option[<?php echo $product_option->id; ?>][range_picker_max_value]" id="price<?php echo $product_option->id; ?>" value="<?php echo $product_option->max_value;?>" />
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][rp_price_per_char]"  value="yes"  <?php checked('yes',$product_option->enable_price_per_char); ?> />
									<?php _e('Enable Price per unit', 'eopa'); ?>
								</td>
								<td>
									<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][rp_multiply_price_by_qty]"  value="1"  <?php checked('1',$product_option->multiply_price_by_qty); ?> />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

		</div>
		<script>
			
			showHideStock('<?php echo $product_option->id; ?>');
			jQuery('#field<?php echo $product_option->id; ?>').find('select[name="product_option[<?php echo $product_option->id; ?>][manage_stock]"]').on('change', function() {showHideStock('<?php echo $product_option->id; ?>'); });
		</script>
		<?php } } ?>

		<div class="tt">
			<?php $custom_options->addForm(0); ?>
		</div>
	</form>

</div>
					 

<script type="text/javascript">

function SortOrderonlyNumber(id){
	    var DataVal = document.getElementById(id).value;
	    if(!DataVal.match(/^[1-9][0-9]*$/)) {
	    	 alert("<?php echo _e('Only Integer values are allowed in Sort Order field!', 'eopa'); ?>");
	    	 document.getElementById(id).value = DataVal.replace(/[^0-9]/g,'');
	    	 ('#'+id).focus();

	    }

	    
	}

	function MaxCharsonlyNumber(id){
	    var DataVal = document.getElementById(id).value;
	    if(!DataVal.match(/^[1-9][0-9]*$/)) {
	    	 alert("<?php echo _e('Only Integer values are allowed in Max Characters field!', 'eopa'); ?>");
	    	 document.getElementById(id).value = DataVal.replace(/[^0-9]/g,'');
	    	 ('#'+id).focus();

	    }

	    
	}

	function PriceOnly(id){
	    var DataVal = document.getElementById(id).value;
	    if(!DataVal.match(/^[1-9.-][0-9.-]*$/)) {
	    	 alert("<?php echo _e('Only Numaric values are allowed in Price field!', 'eopa'); ?>");
	    	 document.getElementById(id).value = DataVal.replace(/[^0-9.-]/g,'');
	    	 ('#'+id).focus();

	    }

	    
	}
	
	function radioimm(id, field_id) { 

			//$('#upload-btn').click(function(e) {
				//e.preventDefault();
				
				var image = wp.media({ 
					title: '<?php echo _e("Upload Image", "eopa") ?>',
					// mutiple: true if you want to upload multiple files at once
					multiple: false
				}).open()
				.on('select', function(){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					// We convert uploaded_image to a JSON object to make accessing it easier
					// Output to the console uploaded_image
					//console.log(uploaded_image);
					var image_url = uploaded_image.toJSON().url;
					// Let's assign the url value to the input field
					jQuery('#tr'+id+'_'+field_id+' #radioimage_url'+id).val(image_url);
					jQuery('#tr'+id+'_'+field_id+' #radioimgdisplay'+id).html("<img width='50' src='"+image_url+"'/>");
				});
			//});
		
 
	}

	function radioproimm(id, field_id) {  

			//$('#upload-btn').click(function(e) {
				//e.preventDefault();
				
				var image = wp.media({ 
					title: '<?php echo _e("Upload Image", "eopa") ?>',
					// mutiple: true if you want to upload multiple files at once
					multiple: false
				}).open()
				.on('select', function(){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					// We convert uploaded_image to a JSON object to make accessing it easier
					// Output to the console uploaded_image
					//console.log(uploaded_image);
					var image_url = uploaded_image.toJSON().url;
					// Let's assign the url value to the input field
					jQuery('#tr'+id+'_'+field_id+' #radioproimage_url'+id).val(image_url);
					jQuery('#tr'+id+'_'+field_id+' #radioproimgdisplay'+id).html("<img width='50' src='"+image_url+"'/>");
				});
			//});
		
 
	}


	function checkboximm(id, field_id) {  

			//$('#upload-btn').click(function(e) {
				//e.preventDefault();
				
				var image = wp.media({ 
					title: '<?php echo _e("Upload Image", "eopa") ?>',
					// mutiple: true if you want to upload multiple files at once
					multiple: false
				}).open()
				.on('select', function(){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					// We convert uploaded_image to a JSON object to make accessing it easier
					// Output to the console uploaded_image
					//console.log(uploaded_image);
					var image_url = uploaded_image.toJSON().url;
					// Let's assign the url value to the input field
					jQuery('#tr'+id+'_'+field_id+' #checkboximage_url'+id).val(image_url);
					jQuery('#tr'+id+'_'+field_id+' #checkboximgdisplay'+id).html("<img width='50' src='"+image_url+"'/>");
				});
			//});
		
 
	}

	function checkboxproimm(id, field_id) {  

			//$('#upload-btn').click(function(e) {
				//e.preventDefault();
				
				var image = wp.media({ 
					title: '<?php echo _e("Upload Image", "eopa") ?>',
					// mutiple: true if you want to upload multiple files at once
					multiple: false
				}).open()
				.on('select', function(){
					// This will return the selected image from the Media Uploader, the result is an object
					var uploaded_image = image.state().get('selection').first();
					// We convert uploaded_image to a JSON object to make accessing it easier
					// Output to the console uploaded_image
					//console.log(uploaded_image);
					var image_url = uploaded_image.toJSON().url;
					// Let's assign the url value to the input field
					jQuery('#tr'+id+'_'+field_id+' #checkboxproimage_url'+id).val(image_url);
					jQuery('#tr'+id+'_'+field_id+' #checkboxproimgdisplay'+id).html("<img width='50' src='"+image_url+"'/>");
				});
			//});
		
 
	}	
	


	jQuery(document).ready(function($) { 
		
		
	   $('#field').toggle(function(){ 
		   $('#field_div').removeClass('ui-state-default widget').addClass('ui-state-default widget open');
		   $("#bw").slideDown('slow');
		   
	   },function(){
		$('#bw').removeClass('ui-state-default widget open').addClass('ui-state-default widget');
		   $("#bw").slideUp('slow');
	   });

	});

	function addFields() {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: 'action=addoptionTempData',
			success: function(data) {
			   jQuery('.tt').append(data);
			}
		});
	}

	function addNewDropRow(e, field_id) {
		
		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addrowTempData", "field_id":field_id},
			success: function(data) {
			   // jQuery('.'+field_id+'droprowdata').append(data);
			   // console.log(jQuery(e).parents('tfoot').prev());
			   jQuery(e).parents('tfoot').prev().append(data);
			   showHideStock(field_id);
			}
		});

	}


	function addNewMultiRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addmultirowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'multirowdata').append(data);
                showHideStock(field_id);
			}
		});

	}


	function addNewRadioRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addradiorowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'radiorowdata').append(data);
                showHideStock(field_id);
			}
		});

	}

	function addNewSimpleRadioRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addsimpleradiorowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'simpleradiorowdata').append(data);
                showHideStock(field_id);
			}
		});

	}


	function addNewCheckboxRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addcheckboxrowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'checkboxrowdata').append(data);
                showHideStock(field_id);
			}
		});

	}


	function delFields(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		if(confirm("<?php echo _e('Are you sure to delete this option? This action can not be undone.', 'eopa') ?>"))
		{
			jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {"action": "deloptionTempData", "field_id":field_id},
			success: function() {

				jQuery("#field"+field_id).fadeOut('slow');
				jQuery("#field"+field_id).remove();

				jQuery('.field_success').html("<div class='updated notice alert'><?php echo _e('Update product to save the changes!', 'eopa'); ?></div>");
				window.scrollTo(0, 0);
				
				jQuery('.alert').delay(5000).fadeOut('slow');

			}
			});

		}
	return false;
	}


	function deleteDropRow(id, field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		if(confirm("<?php echo _e('Are you sure to delete this row? This action can not be undone.', 'eopa') ?>"))
		{
			jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {"action": "delrowTempData", "field_id":field_id, "id":id},
			success: function() {

				jQuery("#tr"+id+"_"+field_id).fadeOut('slow');
				jQuery("#tr"+id+"_"+field_id).remove();
				jQuery('.rowfield_success'+field_id).html("<div id='message' class='updated notice alert'><?php echo _e('Row Deleted Sucessfully!', 'eopa'); ?></div>");
				jQuery('.rowfield_success'+field_id+" #message").delay(5000).fadeOut('slow');

			}
			});

		}
	return false;
	}





	function showFields(field_id, value) {

		if(value == 'field') {

			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');
			
			jQuery('#textField'+field_id).slideDown('slow');

			jQuery('#field'+field_id).find('.datath5').hide();
			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
			showHideStock(field_id);
		} else if(value == 'area') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#field'+field_id).find('.datath5').hide();

			jQuery('#textArea'+field_id).slideDown('slow');

			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
			showHideStock(field_id);
		} else if(value == 'file') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#file'+field_id).slideDown('slow');
			showHideStock(field_id);

		} else if(value == 'drop_down') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#dropdown'+field_id).slideDown('slow');

			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
			showHideStock(field_id);

		} else if(value == 'radio') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#radio'+field_id).slideDown('slow');
			
			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
			showHideStock(field_id);
		} else if(value == 'simple_radio') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#simple_radio'+field_id).slideDown('slow');
			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
			showHideStock(field_id);
		} else if(value == 'checkbox') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#checkbox'+field_id).slideDown('slow');
			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
			showHideStock(field_id);
		} else if(value == 'multiple') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#multiselect'+field_id).slideDown('slow');
			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
			showHideStock(field_id);
		} else if(value == 'date') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#date'+field_id).slideDown('slow');
			showHideStock(field_id);
		} else if(value == 'time') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#time'+field_id).slideDown('slow');
			showHideStock(field_id);
		} else if(value == 'color') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#color'+field_id).slideDown('slow');
			showHideStock(field_id);
		} else if(value == 'google_font') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#google_font'+field_id).slideDown('slow');
			showHideStock(field_id);
		} else if(value == 'google_map') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#google_map'+field_id).slideDown('slow');
			showHideStock(field_id);
		} else if(value == 'range_picker') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			
			jQuery('#range_picker'+field_id).slideDown('slow');
			showHideStock(field_id);
		} else {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
		}

	}
	




</script>
