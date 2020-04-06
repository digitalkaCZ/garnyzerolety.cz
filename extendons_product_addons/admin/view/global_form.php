<?php
	require_once EOPA_PLUGIN_DIR . 'admin/class-eo-product-addons-admin.php';
	$custom_options = new EO_Product_Addons_Admin();
	$product_options = '';
	$product_optionss = array();

	if(isset($_GET['id']) && $_GET['id']!='') {
		$product_options = $custom_options->getProductGlobalOptions($_GET['id']);
		$product_optionss = $custom_options->getProductGlobalOptionsData($product_options->rule_id);
	}

	function search_array($needle, $haystack) {
	    if(in_array($needle, $haystack)) {
	         return true;
	    }
	    foreach($haystack as $element) {
	         if(is_array($element) && search_array($needle, $element))
	              return true;
	    }
	    return false;
	}
?>
			
<div class="field_wrapper">
	<h2><?php echo _e('Product Global Addons','eopa'); ?></h2>
	<p><?php echo _e('Global Options are those optios that you want to show with all products.', 'eopa'); ?></p>

	<ul id="info-nav">
		<li <?php if(isset($_POST['prosearch']) && $_POST['prosearch']!='') {  ?> class="nocurrent" <?php } ?>><a href="#custom_options"><span><?php echo _e('Custom Options','eopa'); ?></span></a></li>
		<li <?php if(isset($_POST['prosearch']) && $_POST['prosearch']!='') {  ?> class="current" <?php } ?>><a href="#products"><span><?php echo _e('Applied to','eopa'); ?></span></a></li> 
        
		
    </ul>
	<div class="global_form">
		<form id="savefields" method="post" action="" enctype="multipart/form-data">
			
			<div id="info">
				<div id="custom_options" class="hide">
					<div class="field_success"></div>

					<input type="hidden" name="id" value="<?php if(isset($_GET['id']) && $_GET['id']!='') { echo $_GET['id']; } ?>">

					<div class="rulename">
						<label><?php echo _e('Rule Name','eopa'); ?> <i><?php echo _e('Rule Name is for internal purpose only.','eopa'); ?></i></label>
						<input value="<?php if(!empty($product_options)) echo $product_options->rule_name; ?>" class="rule_inputs" type="text" name="rule_name" id="rule_name" />
					</div>


					<div class="rulename">
						<label><?php echo _e('Rule Status','eopa'); ?></label>
						

						<select name="rule_status" id="rule_status" class="rule_seect">
							<option value="enable" <?php if(!empty($product_options)) echo selected("enable", $product_options->rule_status) ?>><?php echo _e('Enable','eopa'); ?></option>
							<option value="disable" <?php if(!empty($product_options)) echo selected("disable", $product_options->rule_status) ?>><?php echo _e('Disable','eopa'); ?></option>
						</select>
					</div>


					<div class="addButtonGlobal">
						<input onClick="addGlobalFields()" type="button" id="btnAdd" class="button button-primary button-large" value="<?php echo _e('Add New Option','eopa'); ?>"> 
					</div>
					<?php 
						if(count($product_optionss)!= 0) {
						foreach ($product_optionss as $product_option) { 

						$product_option_rows = $custom_options->getProductOptionRows($product_option->id);
					?>
						
						<input type="hidden" value="<?php echo $product_option->id; ?>" name="product_option[<?php echo $product_option->id; ?>][option_id]" />
						
						<div class="addFormFields" id="field<?php echo $product_option->id; ?>">
								<input onClick="delGlobalFields('<?php echo $product_option->id; ?>')" type="button" class="btnDel button btn-danger button-large" value="<?php echo _e('Delete Option','eopa'); ?>">
								<div class="topFields">
									<table class="globaldatatable">
										<thead>
										    <tr>
										    	<th class="datath1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
										    	<th class="datath2"><label><b><?php echo _e('Input Type:','eopa'); ?></b></label></th>
										    	<th class="datath3"><label><b><?php echo _e('Is Required:','eopa'); ?></b></label></th>
										    	<th class="datath4"><label><b><?php echo _e('Sort Order:', 'eopa') ?></b></label></th>
										    	</tr>
										</thead>
										<tbody>
											<tr>
												<td class="datath1"><input class="inputs" type="text" value="<?php printf( __('%s', 'eopa' ), $product_option->option_title );?>" name="product_option[<?php echo $product_option->id; ?>][option_title]"  id="title" /></td>
												<td class="datath2">
										    		<select class="select_type inputs" name="product_option[<?php echo $product_option->id; ?>][option_type]" id="type" onChange="showGlobalFields('<?php echo $product_option->id; ?>',this.value)">
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
						  			<label for="clogic"><b><?php _e('Conditional Logic:','eopa'); ?></b></label>
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
								            $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE id!=%d AND global_rule_id IS NOT NULL", $product_option->id));      
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
										<table class="globaldatatable">
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
														<input type="text" name="product_option[<?php echo $product_option->id; ?>][text_stock]" value="<?php echo $product_option->stock;?>" />
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td>
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][field_price_per_char]" value="1" <?php checked('1',$product_option->enable_price_per_char); ?> />
														<?php _e('Enable Price per character', 'eopa'); ?>
													</td>
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][field_multiply_price_by_qty]" value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
														<?php _e('Multiply price by quantity', 'eopa'); ?>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<div id="textArea<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'area') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
										<table class="globaldatatable">
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
														<input type="text" name="product_option[<?php echo $product_option->id; ?>][area_stock]" value="<?php echo $product_option->stock;?>" />
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td>
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][textarea_price_per_char]"  value="1" <?php checked('1',$product_option->enable_price_per_char); ?> />
														<?php _e('Enable Price per character', 'eopa'); ?>
													</td>
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][textarea_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?>/>
														<?php _e('Multiply price by quantity', 'eopa'); ?>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>

									<div id="file<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'file') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
										<table class="globaldatatable">
											<thead>
											    <tr>
											    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
											    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
											    	<th class="datath1"><label><b><?php echo _e('Allowed Extensions <span>(add with comma(,) separated)</span>:','eopa'); ?></b></label></th>
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
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][file_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
														<?php _e('Multiply price by quantity', 'eopa'); ?>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<div id="dropdown<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'drop_down') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
										<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
										<table class="globaldatatable" id="POITable<?php echo $product_option->id; ?>">
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
													<td><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
												</tr>

											<?php } } ?>

												<tr>
													<td class="<?php echo $product_option->id; ?>globaldroprowdata">
														<?php $custom_options->addGlobalForm2(0,$product_option->id); ?>
													</td>
												</tr>

											</tbody>
											<tfoot> 
												<tr class="addButton">
											   		<td><input onClick="addGlobalNewDropRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
											   		<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][dropdown_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
										<table class="globaldatatable" id="MultiTable<?php echo $product_option->id; ?>">
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
														<td><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
													</tr>
												
												<?php } } ?>

												<tr>
													<td class="<?php echo $product_option->id; ?>globalmultirowdata">
														<?php $custom_options->addGlobalMultiForm(0,$product_option->id); ?>
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr class="addButton">
											   		<td><input onClick="addGlobalNewMultiRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
											   		<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][ms_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
										<table class="globaldatatable" id="RadioTable<?php echo $product_option->id; ?>">
											<thead>
											    <tr>
											    	<th class="datathpro1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
											    	<th class="datathpro2"><label><b><?php echo _e('Radio Image', 'eopa') ?></b></label></th>
											    	<th class="datathpro3"><label><b><?php echo _e('Product Image', 'eopa'); ?></b></label></th>
											    	<th class="datathpro4"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
											    	<th class="datathpro5"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
											    	<th class="datathpro6"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
											    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
											    	<th class="datathpro7"></th>
											    </tr>
											</thead>
											<tbody>
												<?php if($product_option->option_field_type == 'radio') { ?>
												<?php foreach ($product_option_rows as $product_option_row) { ?>
												
													<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
														<td class="datathpro1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_title]" id="dropdowntitle" /></td>
														
														<td class="datathpro2">
															<div class="imgdis" id="radioimgdisplay<?php echo $product_option_row->id; ?>">
																<?php if($product_option_row->option_image == '') { ?>
																	<img src="<?php echo EOPA_URL ?>/images/no_image.png" width="50" />
																<?php } else { ?>
																	<img src="<?php echo $product_option_row->option_image; ?>" width="50" />
																<?php } ?>
															</div>
															<input type="hidden" value="<?php echo $product_option_row->option_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_image_url]" id="radioimage_url<?php echo $product_option_row->id; ?>" class="regular-text">
															<input onClick="radioimm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
															
														</td>

														<td class="datathpro3">
															<div class="imgdis" id="radioproimgdisplay<?php echo $product_option_row->id; ?>">
																<?php if($product_option_row->option_pro_image == '') { ?>
																	<img src="<?php echo EOPA_URL ?>/images/no_image.png" width="50" />
																<?php } else { ?>
																	<img src="<?php echo $product_option_row->option_pro_image; ?>" width="50" />
																<?php } ?>
															</div>
															<input type="hidden" value="<?php echo $product_option_row->option_pro_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_proimage_url]" id="radioproimage_url<?php echo $product_option_row->id; ?>" class="regular-text">
															<input onClick="radioproimm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
															
														</td>

														<td class="datathpro4">
												    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
												        </td>
														<td class="datathpro5">
															<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
										                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
										                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
										                	</select>
														</td>
														<td class="datathpro6"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
														<td class="datath5">
															<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
														</td>
														<td class="datathpro7"><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')">Remove</a></td>
													</tr>
												
												<?php } } ?>

												<tr>
													<td class="<?php echo $product_option->id; ?>globalradiorowdata" colspan="7">
														<?php $custom_options->addGlobalRadioForm(0,$product_option->id); ?>
													</td>
												</tr>

											</tbody>
											<tfoot>
												<tr class="addButton">
											   		<td><input onClick="addGlobalNewRadioRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
											   		<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][radio_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
										<table class="globaldatatable" id="RadioTable<?php echo $product_option->id; ?>">
											<thead>
											    <tr>
											    	<th class="datathpro1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
											    	
											    	<th class="datathpro4"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
											    	<th class="datathpro5"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
											    	<th class="datathpro6"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
											    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
											    	<th class="datathpro7"></th>
											    </tr>
											</thead>
											<tbody>
												<?php if($product_option->option_field_type == 'simple_radio') { ?>
												
												<?php foreach ($product_option_rows as $product_option_row) { ?>
												
													<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
														<td class="datathpro1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_title]" id="dropdowntitle" /></td>
														
														

														<td class="datathpro4">
												    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
												        </td>
														<td class="datathpro5">
															<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
										                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
										                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
										                	</select>
														</td>
														<td class="datathpro6"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
														<td class="datath5">
															<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
														</td>
														<td class="datathpro7"><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')">Remove</a></td>
													</tr>
												
												<?php } } ?>

												<tr>
													<td class="<?php echo $product_option->id; ?>globalsimpleradiorowdata" colspan="7">
														<?php $custom_options->addGlobalSimpleRadioForm(0,$product_option->id); ?>
													</td>
												</tr>

											</tbody>
											<tfoot>
												<tr class="addButton">
											   		<td><input onClick="addGlobalNewSimpleRadioRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
											   		<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][sr_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
										<table class="globaldatatable" id="CheckboxTable<?php echo $product_option->id; ?>">
											<thead>
											    <tr>
											    	<th class="datathpro1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
											    	<th class="datathpro2"><label><b><?php echo _e('Checkbox Image', 'eopa') ?></b></label></th>
											    	<th class="datathpro3"><label><b><?php echo _e('Product Image', 'eopa'); ?></b></label></th>
											    	<th class="datathpro4"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
											    	<th class="datathpro5"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
											    	<th class="datathpro6"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
											    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
											    	<th class="datathpro7"></th>
											    </tr>
											</thead>
											<tbody>
												
											<?php if($product_option->option_field_type == 'checkbox') { ?>
												<?php foreach ($product_option_rows as $product_option_row) { ?>
												
													<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
														<td class="datathpro1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_title]" id="dropdowntitle" /></td>
														
														<td class="datathpro2">
															<div class="imgdis" id="checkboximgdisplay<?php echo $product_option_row->id; ?>">
																<?php if($product_option_row->option_image == '') { ?>
																	<img src="<?php echo EOPA_URL ?>/images/no_image.png" width="50" />
																<?php } else { ?>
																	<img src="<?php echo $product_option_row->option_image; ?>" width="50" />
																<?php } ?>
															</div>
															<input type="hidden" value="<?php echo $product_option_row->option_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_image_url]" id="checkboximage_url<?php echo $product_option_row->id; ?>" class="regular-text">
															<input onClick="checkboximm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
															
														</td>

														<td class="datathpro3">
															<div class="imgdis" id="checkboxproimgdisplay<?php echo $product_option_row->id; ?>">
																<?php if($product_option_row->option_pro_image == '') { ?>
																	<img src="<?php echo EOPA_URL ?>/images/no_image.png" width="50" />
																<?php } else { ?>
																	<img src="<?php echo $product_option_row->option_pro_image; ?>" width="50" />
																<?php } ?>
															</div>
															<input type="hidden" value="<?php echo $product_option_row->option_pro_image; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_proimage_url]" id="checkboxproimage_url<?php echo $product_option_row->id; ?>" class="regular-text">
															<input onClick="checkboxproimm('<?php echo $product_option_row->id; ?>', '<?php echo $product_option_row->option_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
															
														</td>

														<td class="datathpro4">
												    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
												        </td>
														<td class="datathpro5">
															<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_price_type]" id="dropdownprice_type">
										                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','eopa'); ?></option>
										                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','eopa'); ?></option>
										                	</select>
														</td>
														<td class="datathpro6"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][check_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
														<td class="datath5">
															<input type="text" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][stock]" value="<?php echo $product_option_row->stock;?>" />
														</td>
														<td class="datathpro7"><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')"><?php echo _e('Remove', 'eopa'); ?></a></td>
													</tr>
												
												<?php } } ?>

												<tr>
													<td class="<?php echo $product_option->id; ?>globalcheckboxrowdata" colspan="7">
														<?php $custom_options->addGlobalcheckboxForm(0,$product_option->id); ?>
													</td>
												</tr>

											</tbody>
											<tfoot>
												<tr class="addButton">
											   		<td><input onClick="addGlobalNewCheckboxRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>">
											   		</td>
											   		<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][cb_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
														<?php _e('Multiply price by quantity', 'eopa'); ?>
													</td>
											   	</tr>
											</tfoot>
											<tbody>
												
											</tbody>
										</table>
									</div>
									
									<div id="date<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'date') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
										<table class="globaldatatable">
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
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][date_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
														<?php _e('Multiply price by quantity', 'eopa'); ?>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<div id="time<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'time') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
										<table class="globaldatatable">
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
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][time_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
											    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
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
													<td class="datath5">
														<input type="number" name="product_option[<?php echo $product_option->id;?>][color_stock]" value="<?php echo $product_option->stock;?>" />
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][color_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][gf_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][gm_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
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
											    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
											    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
											    	<th class="datath3"><label><b><?php echo _e('Min Value:','eopa'); ?></b></label></th>
						    						<th class="datath4"><label><b><?php _e('Max Value:', 'eopa');?></b></label></th>
											    </tr>
											</thead>
											<tbody>
												<tr>
													<td class="datath3">
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
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][price_per_char]"  value="yes"  <?php checked('1',$product_option->enable_price_per_char); ?> />
														<?php _e('Enable Price per unit', 'eopa'); ?>
													</td>
													<td colspan="4">
														<input type="checkbox" name="product_option[<?php echo $product_option->id; ?>][rp_multiply_price_by_qty]"  value="1" <?php checked('1',$product_option->multiply_price_by_qty); ?> />
														<?php _e('Multiply price by quantity', 'eopa'); ?>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>

							</div>
							<script>
								function showHideStock(fieldId) {
                                    // console.log(jQuery('#field' + fieldId).find('select[name="product_option['+ fieldId +'][manage_stock]"]').val());
									if(jQuery('#field' + fieldId).find('select[name="product_option['+ fieldId +'][manage_stock]"]').val() == 'yes') {
										jQuery('#field' + fieldId).find('.datath5').show();
									} else {
										jQuery('#field' + fieldId).find('.datath5').hide();
									}
								}
								showHideStock('<?php echo $product_option->id; ?>');
								jQuery('#field<?php echo $product_option->id; ?>').find('select[name="product_option[<?php echo $product_option->id; ?>][manage_stock]"]').on('change', function() {showHideStock(<?php echo $product_option->id; ?>);});
							</script>

					<?php } } ?>
					<div class="GlobalFields">
						<?php $custom_options->addGlobalForm(0); ?>
					</div>
				</div>

				<div id="products" class="hide">
					<h3><span><?php _e( 'Applied to', 'eopa' ); ?></span></h3>

					<div class="appledtodiv">
						<input <?php if(!empty($product_options)) echo checked("products", $product_options->applied_on); ?> type="radio" name="applied_to" value="products" onclick="getAppliedto(this.value)" /> <label><?php _e("Products", "eopa"); ?></label>
						<input <?php if(!empty($product_options)) echo checked("categories", $product_options->applied_on); ?> type="radio" name="applied_to" value="categories"  onclick="getAppliedto(this.value)" /> <label><?php _e("Categories", "eopa"); ?></label>
					</div>

					<div class="appproducts" id="pros">

						<h3><?php _e("Choose Products", "eopa"); ?></h3>

						<?php 
							
							if(!empty($product_options)) {

								$proArray = explode(',', $product_options->proids); 	
							} else {
								$proArray = array();
							}
							
						?>
						
						<select class="se2-basic-multiple se2" name="proids[]" multiple="multiple">
						  
							<?php
								$args     = array( 'post_type' => 'product', 'posts_per_page' => -1, 'post_status' => 'publish' );
								$products = get_posts( $args ); 

								if(!empty($products)) {

								foreach($products as $product) {
							?>

								<option <?php if(in_array($product->ID, $proArray)) { ?> selected="selected" <?php } ?> value="<?php echo $product->ID ?>"><?php _e($product->post_title, "eopa"); ?></option>

							<?php } } ?>

						</select>


					</div>


					<div class="appproducts" id="cats">
						
						<h3><?php _e("Choose Categories", "eopa"); ?></h3>
						<?php 
							
							if(!empty($product_options)) {

								$catArray = explode(',', $product_options->catids); 	
							} else {
								$catArray = array();
							}
							
						?>

						<ul class="bef-tree">
							<?php 


								$args = array(
							          'taxonomy' => 'product_cat',
							          'hide_empty' => false,
							          'parent'   => 0
							      );
							  	$product_cat = get_terms( $args );
							  	foreach ($product_cat as $parent_product_cat) {
							?>

								<li class="parent_cat"><input <?php if(in_array($parent_product_cat->term_id, $catArray)) { echo "checked"; } ?> class="parent" type="checkbox" name="catids[]" value="<?php echo $parent_product_cat->term_id; ?>"><?php echo $parent_product_cat->name; ?>


								<?php 

									$child_args = array(
							              'taxonomy' => 'product_cat',
							              'hide_empty' => false,
							              'parent'   => $parent_product_cat->term_id
							          );
							  		$child_product_cats = get_terms( $child_args );
							  		if(!empty($child_product_cats)) {

								?>

								<ul>
									<?php foreach ($child_product_cats as $child_product_cat) { ?>
										<li class="child_cat"><input  <?php if(in_array($child_product_cat->term_id, $catArray)) { echo "checked"; } ?> class="child" type="checkbox" name="catids[]" value="<?php echo $child_product_cat->term_id; ?>"><?php echo $child_product_cat->name; ?></li>
									<?php } ?>
								</ul>

								<?php } ?>
								</li>

							<?php } ?>
						</ul>

					</div>
					
	                
				</div>

			</div>

			<p>
                <input id="eopa-add-gobal-option" type="button" onClick="savedata(this)" name="eopa-add-global-option" class="button-primary" value="<?php _e( 'Save Rule', 'eopa' ); ?>" />
            </p>

		</form>
		
	</div>

</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
	    $('#proexample').DataTable( {
	        "pagingType": "full_numbers",
	    } );
	} );

	jQuery(document).ready(function() {
	    jQuery('.se2-basic-multiple').select2();
	});	


	jQuery(document).ready(function() {
	   
		var value = jQuery("input[name=applied_to]:checked").val();

		if(value == 'products') {
			jQuery("#pros").show();
			jQuery("#cats").hide();
		} else if(value == 'categories') {
			jQuery("#pros").hide();
			jQuery("#cats").show();	
		} else {
			jQuery("#pros").hide();
			jQuery("#cats").hide();

		}

	});	

	function getAppliedto(value) {

		if(value == 'products') {
			jQuery("#pros").show();
			jQuery("#cats").hide();
		} else if(value == 'categories') {
			jQuery("#pros").hide();
			jQuery("#cats").show();	
		} else {
			jQuery("#pros").hide();
			jQuery("#cats").hide();

		}

	}





	jQuery('input[type="checkbox"]').change(function(e) {

	  var checked = $(this).prop("checked"),
	      container = $(this).parent(),
	      siblings = container.siblings();

	  container.find('input[type="checkbox"]').prop({
	    indeterminate: false,
	    checked: checked
	  });

	  function checkSiblings(el) {

	    var parent = el.parent().parent(),
	        all = true;

	    el.siblings().each(function() {
	      return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
	    });

	    if (all && checked) {

	      parent.children('input[type="checkbox"]').prop({
	        indeterminate: false,
	        checked: checked
	      });

	      checkSiblings(parent);

	    } else if (all && !checked) {

	      parent.children('input[type="checkbox"]').prop("checked", checked);
	      parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
	      checkSiblings(parent);

	    } else {

	      el.parents("li").children('input[type="checkbox"]').prop({
	        indeterminate: true,
	        checked: false
	      });

	    }

	  }

	  checkSiblings(container);
	});


</script>

<script type="text/javascript">

	function SortOrderonlyNumber(id){
	    var DataVal = document.getElementById(id).value;
	    if(!DataVal.match(/^[1-9][0-9]*$/)) {
	    	 alert('<?php echo _e("Only Integer values are allowed in Sort Order field!", "eopa"); ?>');
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

	jQuery(document).ready(function($) {

	<?php if(isset($_POST['prosearch']) && $_POST['prosearch']!='') { ?>
	  $( '#info #products').show();
	  $( '#info #custom_options' ).hide();
	 <?php } else { ?>
	 $( '#info #products').hide();
	 <?php } ?>
	  
	  $('#info-nav li').click(function(e) {
	    $('#info .hide').hide();
	    $('#info-nav .current').removeClass("current");
	    $('#info-nav .nocurrent').removeClass("nocurrent");
	    $(this).addClass('current');
	    
	    var clicked = $(this).find('a:first').attr('href');
	    $('#info ' + clicked).fadeIn('fast');
	    e.preventDefault();
	  }).eq(0).addClass('current');

	  

	});
	
	function addGlobalFields() {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: 'action=addGlobalOptionTempData',
			success: function(data) {
			   jQuery('.GlobalFields').append(data);
			   jQuery("html, body").animate({ scrollTop: jQuery(document).height()-jQuery(window).height() });
			}
		});
	}

	function radioimm(id, field_id) { 

			//$('#upload-btn').click(function(e) {
				//e.preventDefault();
				
				var image = wp.media({ 
					title: '<?php echo _e("Upload Image", 'eopa') ?>',
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
					title: '<?php echo _e("Upload Image", 'eopa') ?>',
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
					title: '<?php echo _e("Upload Image", 'eopa') ?>',
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
					title: '<?php echo _e("Upload Image", 'eopa') ?>',
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

	function addGlobalNewDropRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addGlobalrowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'globaldroprowdata').append(data);
			   showHideStock(field_id);
			}
		});

	}

	function addGlobalNewMultiRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addGlobalmultirowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'globalmultirowdata').append(data);
                showHideStock(field_id);
			}
		});

	}

	function addGlobalNewRadioRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addGlobalradiorowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'globalradiorowdata').append(data);
                showHideStock(field_id);
			}
		});

	}

	function addGlobalNewSimpleRadioRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addGlobalSimpleradiorowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'globalsimpleradiorowdata').append(data);
                showHideStock(field_id);
			}
		});

	}



	function addGlobalNewCheckboxRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addGlobalcheckboxrowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'globalcheckboxrowdata').append(data);
                showHideStock(field_id);
			}
		});

	}

	function delGlobalFields(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		if(confirm("Are you sure to delete this option? This action can not be undone."))
		{
			jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {"action": "deloptionTempData", "field_id":field_id},
			success: function() {


				jQuery("#field"+field_id).fadeOut('slow');
				jQuery("#field"+field_id).remove();
				jQuery('.field_success').html("<div class='updated notice alert'><?php echo _e('Option Deleted Sucessfully!', 'eopa'); ?></div>");
				window.scrollTo(0, 0);
				
				jQuery('.alert').delay(5000).fadeOut('slow');


			}
			});

		}
	return false;
	}


	

	
	



	function deleteGlobalDropRow(id, field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		if(confirm("<?php echo _e('Are you sure to delete this row? This action can not be undone.', 'eopa'); ?>"))
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


	function showGlobalFields(field_id, value) {

		if(value == 'field') {

			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
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
		} else if(value == 'area') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#textArea'+field_id).slideDown('slow');

			jQuery('#field'+field_id).find('.datath5').hide();
			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
		} else if(value == 'color') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');
			
			jQuery('#color'+field_id).slideDown('slow');

			jQuery('#field'+field_id).find('.datath5').hide();
			jQuery('#field' + field_id).find('select[name="product_option['+field_id+'][manage_stock]"]').on('change', function() { showHideStock(field_id);});
		} else if(value == 'file') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#file'+field_id).slideDown('slow');
		} else if(value == 'drop_down') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
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
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
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
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
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
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
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
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#date'+field_id).slideDown('slow');
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
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#time'+field_id).slideDown('slow');
		} else if(value == 'simple_checkbox') {

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
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#simple_checkbox'+field_id).slideDown('slow');
		} else if(value == 'simple_radio') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#simple_radio'+field_id).slideDown('slow');
		} else if(value == 'google_font') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#google_font'+field_id).slideDown('slow');
		} else if(value == 'google_map') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
			jQuery('#range_picker'+field_id).slideUp('slow');

			jQuery('#google_map'+field_id).slideDown('slow');
		} else if(value == 'range_picker') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#file'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#radio'+field_id).slideUp('slow');
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#checkbox'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');
			jQuery('#date'+field_id).slideUp('slow');
			jQuery('#time'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#simple_radio'+field_id).slideUp('slow');
			jQuery('#google_map'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');

			jQuery('#range_picker'+field_id).slideDown('slow');
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
			jQuery('#simple_checkbox'+field_id).slideUp('slow');
			jQuery('#color'+field_id).slideUp('slow');
			jQuery('#google_font'+field_id).slideUp('slow');
		}
	}

</script>

<script type="text/javascript">
	function savedata(btn) {
		
		jQuery(btn).css('pointer-events', 'none');
		var data2 = jQuery('#savefields').serialize();
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';

		jQuery.ajax({
		    type: 'POST',
		    url: ajaxurl,
		    data: data2 + '&action=save_global_options',
		    success: function(response) {
		    	// console.log('response' + response);
		        window.location = '<?php echo admin_url("admin.php?page=eo-product-global-custom-options&msg=success") ?>';
		    }
		});
	}

	jQuery("#search-submit").click(function() {

		var data2 = jQuery('#savefields').serialize();
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';

		jQuery.ajax({
		    type: 'POST',
		    url: ajaxurl,
		    data: data2 + '&action=save_global_options',
		    success: function() {
		        window.location = '<?php echo admin_url("admin.php?page=eo-product-global-custom-options&msg=success") ?>';
		    }
		});
	});
</script>