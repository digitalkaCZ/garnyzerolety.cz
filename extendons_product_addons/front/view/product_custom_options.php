<?php
	$api_key = get_option('eopa_google_map_api_key');
	$default_lat = get_option('eopa_default_lat');
	$default_long = get_option('eopa_default_long');
	$default_zoom = get_option('eopa_default_zoom');

	$json = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBYOdotYH-_VsNoj-Uh37qVx5tVM6rpnI4');
	
	$obj = json_decode($json);
	$fontNames = array();
	foreach($obj->items as $item) {
		array_push($fontNames, $item->family);
	}
	 
	require_once EOPA_PLUGIN_DIR . 'front/class-eo-product-addons-front.php';
	$custom_options = new EO_Product_Addons_Front();

	$GlobalRules = $custom_options->getGlobalRules();

	//check if global options exclude for the current product
	$ProductOptions = $custom_options->getProductOptions($post->ID);

	$currency = get_woocommerce_currency();
	$string = get_woocommerce_currency_symbol( $currency );
	$proprice = get_post_meta($post->ID, "_price", true);
	$product_image =get_the_post_thumbnail($post->ID);


	$dependencies_data = array();
	$global_dependencies_data = array();
?>
<div class="custom_options">
	<!-- Product Options Start-->

<?php 
if($ProductOptions!='') { 
	foreach ($ProductOptions as $global_option) { 
		$dependencies_data[$global_option->id] = array(
			'showif' => $global_option->showif,
			'cfield' => $global_option->cfield,
			'ccondition' => $global_option->ccondition,
			'ccondition_value' => $global_option->ccondition_value
			// 'option_field_type' => $global_option->option_field_type
			);

		
		$c_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE id = %d", $global_option->cfield)); 
		
		$dependencies_data[$global_option->id]['option_field_type'] = $c_field->option_field_type;

		if($c_field->option_field_type == 'checkbox') {
			
			$RowOptions = $this->getRowOptions($c_field->id);
			$dependencies_data[$global_option->id]['rows'] = array();
			foreach($RowOptions as $option_row) { 
				array_push($dependencies_data[$global_option->id]['rows'], $option_row->option_row_title);
			}
		} else if($c_field->option_field_type == 'radio' || $c_field->option_field_type == 'simple_radio') {
			
			$dependencies_data[$global_option->id]['option_field_type'] = $c_field->option_field_type;
			$dependencies_data[$global_option->id]['option_title'] = $c_field->option_title;
		}

		$title = strtolower(str_replace(' ', '_', $global_option->option_title));
		if(isset($_POST['product_options'][$title]['value']) && $_POST['product_options'][$title]['value'] !='' )
			$val_post = $_POST['product_options'][$title]['value'];
		else
			$val_post = '';

		$stock_status = false;
		$multiple = array('drop_down', 'radio', 'simple_radio', 'checkbox', 'multiple');
		$single = array('field', 'area', 'color');

		if(array_search($global_option->option_field_type, $multiple) !== FALSE) {
			
			if($global_option->manage_stock == 'yes') {
				$RowOptions = $this->getRowOptions($global_option->id);
				
				foreach($RowOptions as $option_row) { 

					if($option_row->stock > 0) {
						$stock_status = true;
						break;
					}
				}
			} else {
				$stock_status = true;
			}
				
		} else if($global_option->option_field_type == 'file') {
			$stock_status = true;
		} else {
			if($global_option->manage_stock == 'yes' && $global_option->stock > 0)
				$stock_status = true;
			else if($global_option->manage_stock == 'no')
				$stock_status = true;
		}
?>

	<div class="eocustomgroup" id="field<?php echo $global_option->id?>_wrapper">	
<?php if($stock_status) { ?>
		<label>
			<?php echo $global_option->option_title; ?> 
			<?php if($global_option->option_is_required == 'yes') { ?>
				<span class="required">*</span>
			<?php } ?>
			:-
			<?php if($global_option->option_price != '') { ?>
				<?php if($global_option->option_price_type == 'percent') { ?>
					<span class="price">(
					<?php
						echo wc_price($proprice*$global_option->option_price/100, array(
						    'ex_tax_label'       => false,
						    'currency'           => '',
						    'decimal_separator'  => wc_get_price_decimal_separator(),
						    'thousand_separator' => wc_get_price_thousand_separator(),
						    'decimals'           => wc_get_price_decimals(),
						    'price_format'       => get_woocommerce_price_format()
						) );

						if($global_option->enable_price_per_char == 1) {
							echo _e(' Price per character', 'eopa');
						}

					?>
					
					)</span>
				<?php } else { ?>
					<span class="price">(
					<?php 


						echo wc_price($global_option->option_price, array(
						    'ex_tax_label'       => false,
						    'currency'           => '',
						    'decimal_separator'  => wc_get_price_decimal_separator(),
						    'thousand_separator' => wc_get_price_thousand_separator(),
						    'decimals'           => wc_get_price_decimals(),
						    'price_format'       => get_woocommerce_price_format()
						) );

						if($global_option->enable_price_per_char == 1) {
							echo _e(' Price per character', 'eopa');
						}


					 ?>)</span>
				<?php } ?>
			<?php } ?>
		</label>
		<?php if($global_option->option_field_type == 'field') { ?>

			<input data-char="<?php echo $global_option->enable_price_per_char;  ?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop eoinput" type="text" maxlength="<?php echo $global_option->option_maxchars; ?>" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>">
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'area') { ?>

			<textarea type="textarea" data-char="<?php echo $global_option->enable_price_per_char;  ?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop eoinput" maxlength="<?php echo $global_option->option_maxchars; ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>"><?php if( ! empty($val_post) ){ echo $val_post; } ?></textarea>

			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'simple_checkbox') { ?>

			<input type="checkbox" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop eoinput" value="<?php echo $global_option->option_maxchars; ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>" /> <?php echo $global_option->option_title; ?>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'date') { ?> 

			<input data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop datepick eoinput" type="text" maxlength="<?php echo $global_option->option_maxchars; ?>" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>">
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'time') { ?> 

			<input data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop timepick" type="text" maxlength="<?php echo $global_option->option_maxchars; ?>" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>">
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'color') { ?> 

			<input type="text" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop colorpick" id="field<?php echo $global_option->id?>" />
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />
			<script>
			jQuery("#field<?php echo $global_option->id?>").spectrum({
				    preferredFormat: "rgb",
				    showInput: true,
				    className: "full-spectrum",
				    showInitial: true,
				    showPalette: true,
				    showSelectionPalette: true,
				    maxSelectionSize: 10,
					    palette: [
					        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
					        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
					        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
					        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
					        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
					        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
					        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
					        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
					        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
					        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
					        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
					        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
					        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
					        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
				    	]
				});
			</script>

		<?php } else if($global_option->option_field_type == 'google_font') { ?> 

			<select name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]"  id="field<?php echo $global_option->id?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop fontpick">
				<option value="">Select Font</option>
				<?php
					foreach($fontNames as $fontName) { ?>
					<option <?php selected($val_post, $fontName);?>><?php echo $fontName;?></option>
				<?php } ?>
			</select>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'google_map') { ?> 

			<input type="hidden" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" id="field<?php echo $global_option->id?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop mappick" />
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

			
			<div id="map" style="height: 300px;width: 100%;"></div>

			<script>
				var map, defaultZoom = parseInt('<?php echo $default_zoom;?>');
				var fieldId = 'field<?php echo $global_option->id?>';
		        function initMap() {                            
		            var latitude = parseFloat('<?php echo $default_lat?>'); 
		            var longitude = parseFloat('<?php echo $default_long?>'); 
		            
		            var defaultLoc = {lat: latitude, lng: longitude};
		            
		            map = new google.maps.Map(document.getElementById('map'), {
		              center: defaultLoc,
		              zoom: defaultZoom,
		              disableDoubleClickZoom: true 
		            });
		                  
		            var marker = new google.maps.Marker({
		              position: defaultLoc,
		              map: map,
		              draggable: true,
		              title: latitude + ', ' + longitude 
		            });   

		            marker.addListener('drag', function(event) {            
		              	document.getElementById(fieldId).value = event.latLng.lat().toFixed(7) + ', ' + event.latLng.lng().toFixed(7);
		              	if (typeof yourFunctionName == 'function') { 
			              	mapCoordsChanged(event.latLng.lat().toFixed(7) + ', ' + event.latLng.lng().toFixed(7));
			            }
		            });
		        }
			</script>
		<?php } else if($global_option->option_field_type == 'range_picker') { ?> 

			<span class="range_picker_min_value"><?php echo $global_option->min_value?></span>
			<input type="range" id="field<?php echo $global_option->id?>" min="<?php echo $global_option->min_value?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop rangepick" max="<?php echo $global_option->max_value?>"  value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" />
			<span class="range_picker_max_value"><?php echo $global_option->max_value?></span>
			<span id="range<?php echo $global_option->id?>" class="range_val"></span>
			
			<input type="hidden" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" id="range_picker<?php echo $global_option->id?>" />
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

			<script>
				document.getElementById('field<?php echo $global_option->id?>').addEventListener('change', function(e) {
					document.getElementById('range_picker<?php echo $global_option->id?>').value = e.target.value;
					document.getElementById('range<?php echo $global_option->id?>').innerHTML= ' (' + e.target.value + ' )';
					if (typeof ProductCustomOptions == 'function') { 
		              	// rangeChanged(e.target.value);
		              	ProductCustomOptions();
		            }
				});
			</script>

		<?php } else if($global_option->option_field_type == 'drop_down') { ?> 

			<select type="select" class="eoa eoop eoinput" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>">
				<option value=""><?php echo _e('--- Prosím zvolte možnost ---'); ?></option>
				<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
				<?php foreach($RowOptions as $option_row) { 
					if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
					<option <?php selected($val_post,$option_row->option_row_title); ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" value="<?php echo $option_row->option_row_title; ?>">
					<?php echo $option_row->option_row_title; ?>   
					<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
							-  <span class="price">(
							<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );


							 ?>)</span>
						<?php } else { ?>
							-  <span class="price">(<?php 



									echo wc_price($option_row->option_row_price, array(
									    'ex_tax_label'       => false,
									    'currency'           => '',
									    'decimal_separator'  => wc_get_price_decimal_separator(),
									    'thousand_separator' => wc_get_price_thousand_separator(),
									    'decimals'           => wc_get_price_decimals(),
									    'price_format'       => get_woocommerce_price_format()
									) );


							?>)</span>
						<?php } ?>
					<?php } ?>
					</option>
				<?php } } ?>
			</select>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'multiple') { ?> 

			<select type="mselect" multiple = "multiple" class="fmm eoop eoinput multi" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>"  data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" >
				<option value=""><?php echo _e('--- Zvolte prosím jednu z možností ').$global_option->option_title; ?> ---</option>
				<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
				<?php foreach($RowOptions as $option_row) {
					if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
					<?php 
						$title = strtolower(str_replace(' ', '_', $global_option->option_title));

						if(isset($_POST['product_options'][$title]['value']) && $_POST['product_options'][$title]['value']!='') {
							$val_post2 = $_POST['product_options'][$title]['value'];
						} else {
							$val_post2 = '';
						}

					?>
					<option <?php if($val_post2!='') { foreach ($val_post2 as $valp) { selected($valp,$option_row->option_row_title); } } ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" value="<?php echo $option_row->option_row_title; ?>">
					<?php echo $option_row->option_row_title; ?>   
					<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
							-  <span class="price">(
								
								<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );


							 ?>

							)</span>
						<?php } else { ?>
							-  <span class="price">(<?php 



									echo wc_price($option_row->option_row_price, array(
									    'ex_tax_label'       => false,
									    'currency'           => '',
									    'decimal_separator'  => wc_get_price_decimal_separator(),
									    'thousand_separator' => wc_get_price_thousand_separator(),
									    'decimals'           => wc_get_price_decimals(),
									    'price_format'       => get_woocommerce_price_format()
									) );


							?>)</span>
						<?php } ?>
					<?php } ?>
					</option>
				<?php } } ?>
			</select>

			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'radio') { ?> 

			<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
			<input data-price="0" checked = "true" class="eoop" style="display:none" value="" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>" >
			<?php foreach($RowOptions as $option_row) { 
				if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
				<div class="radio">
					<span class="rowTitle">
						<?php echo $option_row->option_row_title; ?>
						<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
						<span class="price">(
						<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );


							 ?>)</span>
						<?php } else { ?>
						<span class="price">(
							<?php 



									echo wc_price($option_row->option_row_price, array(
									    'ex_tax_label'       => false,
									    'currency'           => '',
									    'decimal_separator'  => wc_get_price_decimal_separator(),
									    'thousand_separator' => wc_get_price_thousand_separator(),
									    'decimals'           => wc_get_price_decimals(),
									    'price_format'       => get_woocommerce_price_format()
									) );


							?>
						)</span>
						<?php } ?>
						<?php } ?>
					</span>
					<label>
						<input id="rb11" <?php checked($val_post,$option_row->option_row_title); ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop" value="<?php echo $option_row->option_row_title; ?>" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]">
						<?php if($option_row->option_image == '') { ?>
							<img onClick="changeimage('<?php echo EOPA_URL ?>/images/no_image.png')" src="<?php echo EOPA_URL ?>/images/no_image.png" width="75" alt="<?php echo $option_row->option_row_title; ?>" class="img-thumbnail" />
						<?php } else { ?>
							<img onClick="changeimage('<?php echo $option_row->option_pro_image; ?>')" src="<?php echo $option_row->option_image; ?>" width="75" alt="<?php echo $option_row->option_row_title; ?>" class="img-thumbnail" />
						<?php } ?>
					</label>
				</div>
				<?php } } ?>

			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />
				
		<?php } else if($global_option->option_field_type == 'simple_radio') { ?>

			<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
			<input data-price="0" checked = "true" class="eoop" style="display:none" value="" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][]" id="field<?php echo $global_option->id?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>"  >
			<?php foreach($RowOptions as $option_row) { 
				if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
				<div class="sim_radio_button">


					<input <?php checked($val_post,$option_row->option_row_title); ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop sim_radio" value="<?php echo $option_row->option_row_title; ?>" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" style="display: block;">

					<span class="rowTitle">
						<?php echo $option_row->option_row_title; ?>
						<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
						<span class="price">(
						<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

							?>
						)</span>
						<?php } else { ?>
						<span class="price">(<?php 

								echo wc_price($option_row->option_row_price, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

								 ?>)</span>
						<?php } ?>
						<?php } ?>
					</span>
					
						
						
					
				</div>
				<?php } } ?>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />
				
		<?php }  else if($global_option->option_field_type == 'checkbox') { ?> 

			<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
			<input data-price="0" checked = "true" class="eoop" style="display:none" value="" type="checkbox" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][]" id="field<?php echo $global_option->id?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" >
			<?php foreach($RowOptions as $option_row) { 
				if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>

				<?php 
					$title = strtolower(str_replace(' ', '_', $global_option->option_title));

					if(isset($_POST['product_options'][$title]['value']) && $_POST['product_options'][$title]['value']!='') {
						$val_post2 = $_POST['product_options'][$title]['value'];
					} else {
						$val_post2 = '';
					}

				?>

				<div class="checkbox">
					
					<label>
						<input <?php if($val_post2!='') { foreach ($val_post2 as $valp) { checked($valp,$option_row->option_row_title); } } ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" class="eoop" value="<?php echo $option_row->option_row_title; ?>" type="checkbox" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]">
						<?php if($option_row->option_image == '') { ?>
							
						<?php } else { ?>
							<img onClick="changeimage('<?php echo $option_row->option_pro_image; ?>')" src="<?php echo $option_row->option_image; ?>" width="75" alt="<?php echo $option_row->option_row_title; ?>" class="img-thumbnail" />
						<?php } ?>
					</label>
					<span class="rowTitle">
						<?php echo $option_row->option_row_title; ?>
						<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
						<span class="price">(<?php 
								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

							 ?>)</span>
						<?php } else { ?>
						<span class="price">(<?php 



									echo wc_price($option_row->option_row_price, array(
									    'ex_tax_label'       => false,
									    'currency'           => '',
									    'decimal_separator'  => wc_get_price_decimal_separator(),
									    'thousand_separator' => wc_get_price_thousand_separator(),
									    'decimals'           => wc_get_price_decimals(),
									    'price_format'       => get_woocommerce_price_format()
									) );


							?>)</span>
						<?php } ?>
						<?php } ?>
					</span>
				</div>
				<?php } } ?>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'file') { ?>

				<input data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" class="eoinput" type="file" value="" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>">
				<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } ?>
		
	</div>
	<?php } } ?>
	<?php } ?>

	<!-- Product Options End-->
		
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key;?>&callback=initMap"></script>

<?php 
	$i = 1000;
	foreach($dependencies_data as $option_id => $option_dependency) {

		if($option_dependency['showif'] != '') {
			if($option_dependency['showif'] == 'Hide') { 
				if($option_dependency['ccondition'] == 'is equal to') {
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: false
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		
					  		jQuery('#field<?php echo $option_id;?>_wrapper').show();
					  		jQuery('#field<?php echo $option_dependency["cfield"];?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');

					  			if(jQuery(values).not(selectVals).get().length === 0) {
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');

							    }
					  		});
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							var conditionValue = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() == conditionValue) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['<?php echo $option_dependency["ccondition_value"];?>']
				    			}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php 				}
				} else if($option_dependency['ccondition'] == 'is not equal to') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: true
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  		jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');


							    if(jQuery(values).not(selectVals).get().length === 0) {
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});
						</script>
<?php 				}  else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function(){
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() != checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').show();
							jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
								if(jQuery(this).val() == '<?php echo $option_dependency["ccondition_value"];?>') {
									jQuery('#field<?php echo $option_id;?>_phfield').val('');
									jQuery('#field<?php echo $option_id;?>_wrapper').show();
								} else {
									jQuery('#field<?php echo $option_id;?>_wrapper').hide();
									jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
								}
							});
						</script>
<?php				}
				} else if($option_dependency['ccondition'] == 'is not empty') {
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>') {
									jQuery(this).on('change', function() {
										if(jQuery(this).is(':checked')) {
											jQuery('#field<?php echo $option_id;?>_wrapper').hide();
											jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
										} else {
											jQuery('#field<?php echo $option_id;?>_phfield').val('');
											jQuery('#field<?php echo $option_id;?>_wrapper').show();
										}
									});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'google_map') { ?>
						
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							function mapCoordsChanged(val) {
								
								if(val != '') {
									jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
		    						jQuery('#field<?php echo $option_id;?>_wrapper').hide();
		    					} else {
		    						jQuery('#field<?php echo $option_id;?>_wrapper').show();
		    						jQuery('#field<?php echo $option_id;?>_phfield').val('');
		    					}
							}
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'multiple'){ ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').show();
							jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
								
					  			if(jQuery(this).val().length !== 0 && jQuery(this).val()[0] !== '') {
					  				jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  				jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    }
					  		});

						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'file') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				values: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {
			    					
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'color_picker') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				values: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {
			    					
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function(){
			    				
			    				if(jQuery(this).is(':checked')) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else  {

			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				values: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {

			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php				} 			
				} else if($option_dependency['ccondition'] == 'is checked') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>') {
									jQuery(this).on('change', function() {
										if(jQuery(this).is(':checked')) {
											jQuery('#field<?php echo $option_id;?>_wrapper').hide();
											jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
										} else {
											jQuery('#field<?php echo $option_id;?>_phfield').val('');
											jQuery('#field<?php echo $option_id;?>_wrapper').show();
										}
									});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script type="text/javascript">
			    			var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function(){
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() == checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} 
				}
/**************** Show ******************/
			} else { 

				if($option_dependency['ccondition'] == 'is equal to') {
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: true
							    	}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
								}
							});javascript:void(0);
<?php 
		$i++;
	}
?>
					</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  		jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');
					  			
							    if(jQuery(values).not(selectVals).get().length === 0) {
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function(){
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() == checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				values: ['<?php echo $option_dependency["ccondition_value"];?>']
				    			}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php 				} ?>
<?php			} else if($option_dependency['ccondition'] == 'is not equal to') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: false
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
					</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  		jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');


							    if(jQuery(values).not(selectVals).get().length !== 0) {
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function(){
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() != checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				}
			    			});
						</script>
<?php				} else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['<?php echo $option_dependency["ccondition_value"];?>']
				    			}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php				}
				} else if($option_dependency['ccondition'] == 'is not empty') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() != '') {
									
									jQuery(this).on('change', function() {
										var res = jQuery('input[type="checkbox"]:checked').filter(function(index) {
											return jQuery(this).val() != '';
										});

										if(res.length > 0) {
											jQuery('#field<?php echo $option_id;?>_phfield').val('');
											jQuery('#field<?php echo $option_id;?>_wrapper').show();
										} else {
											jQuery('#field<?php echo $option_id;?>_wrapper').hide();
											jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
										}
									});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'file') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['']
				    		}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'color_picker') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['']
				    		}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'multiple'){ ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
								
					  			if(jQuery(this).val().length !== 0 && jQuery(this).val()[0] !== '') {
					  				jQuery('#field<?php echo $option_id;?>_phfield').val('');
					  				jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});

						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>

				    		jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    		
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function(){
			    				
			    				if(jQuery(this).is(':checked') === true) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    				}
			    			});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'google_map') { ?>
						
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							function mapCoordsChanged(val) {
								
								if(val != '') {
									jQuery('#field<?php echo $option_id;?>_phfield').val('');
		    						jQuery('#field<?php echo $option_id;?>_wrapper').show();
		    					} else {
		    						jQuery('#field<?php echo $option_id;?>_wrapper').hide();
		    						jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
		    					}
							}
						</script>
<?php 				} else { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {
			    					
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php				} ?>					
<?php			} else if($option_dependency['ccondition'] == 'is checked') { 

					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
						
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: true
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
					</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    			var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';

			    			jQuery("input[name='product_options[<?php echo strtolower($option_dependency["option_title"])?>][value]']").on('change', function(){
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() == checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				}
			    			});
						</script>
<?php 				} 
			 	}
			}
		}
	}
?>


	<!-- Global Option Start-->
	<?php 

	$is_exclude = get_post_meta ( $post->ID, '_exclude_global_options', true );
	
	$GlobalOptions = array();
	foreach($GlobalRules as $gRule) {

		if($gRule->applied_on == 'products') {
			
			$proudctIDs = explode(', ', $gRule->proids);


			if(in_array($post->ID, $proudctIDs)) {
				
				if($is_exclude!='yes') {
					$GlobalOptions[] = $custom_options->getGlobalOptions($gRule->rule_id);
				} else {
					$GlobalOptions[] = 0;
				}

			}
		} else if ($gRule->applied_on == 'categories') {


			$categoryproIDs = explode(', ', $gRule->catproids);


		    if(in_array($post->ID, $categoryproIDs)) {
				
				if($is_exclude!='yes') {
					$GlobalOptions[] = $custom_options->getGlobalOptions($gRule->rule_id);
				} else {
					$GlobalOptions[] = 0;
				}

			}
		}
	}

	if($GlobalOptions!=0) {

	foreach ($GlobalOptions as $global_options) {?>
		<?php 

		foreach ($global_options as $global_option) {
			
			$global_dependencies_data[$global_option->id] = array(
			'showif' => $global_option->showif,
			'cfield' => $global_option->cfield,
			'ccondition' => $global_option->ccondition,
			'ccondition_value' => $global_option->ccondition_value
			);

			
			$c_field = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE id = %d", $global_option->cfield)); 
			
			$global_dependencies_data[$global_option->id]['option_field_type'] = $c_field->option_field_type;
			
			$stock_status = false;
			$multiple = array('drop_down', 'radio', 'simple_radio', 'checkbox', 'multiple');
			$single = array('field', 'area');

			if(array_search($global_option->option_field_type, $multiple) !== FALSE) {
				
				if($global_option->manage_stock == 'yes') {
					$RowOptions = $this->getRowOptions($global_option->id);
					
					foreach($RowOptions as $option_row) { 

						if($option_row->stock > 0) {
							$stock_status = true;
							break;
						}

					}
				}else if($global_option->manage_stock == 'no')
					$stock_status = true;
					
			} else {
				if($global_option->manage_stock == 'yes' && $global_option->stock > 0)
					$stock_status = true;
				else if($global_option->manage_stock == 'no')
					$stock_status = true;
			}

			if($c_field->option_field_type == 'checkbox') {
				
				$RowOptions = $this->getRowOptions($c_field->id);
				$global_dependencies_data[$global_option->id]['rows'] = array();
				foreach($RowOptions as $option_row) { 
					array_push($global_dependencies_data[$global_option->id]['rows'], $option_row->option_row_title);
				}
			} else if($c_field->option_field_type == 'radio' || $c_field->option_field_type == 'simple_radio') {
				
				$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE id = %d", $global_option->cfield));

				$global_dependencies_data[$global_option->id]['option_field_type'] = $c_field->option_field_type;
				$global_dependencies_data[$global_option->id]['option_title'] = $result->option_title;
			}
			
			$title = strtolower(str_replace(' ', '_', $global_option->option_title));
			if(isset($_POST['product_options'][$title]['value']) && $_POST['product_options'][$title]['value']!='') {
				$val_post = $_POST['product_options'][$title]['value'];
			} else {
				$val_post = '';
			}
	?>

	<div class="eocustomgroup" id="field<?php echo $global_option->id?>_wrapper">	
<?php if($stock_status) { ?>
		<label>
			<?php echo $global_option->option_title; ?> 
			<?php if($global_option->option_is_required == 'yes') { ?>
				<span class="required">*</span>
			<?php } ?>
			<?php if($global_option->option_price != '') { ?>
				<?php if($global_option->option_price_type == 'percent') { ?>
					<span class="price">(
					<?php
						echo wc_price($proprice*$global_option->option_price/100, array(
						    'ex_tax_label'       => false,
						    'currency'           => '',
						    'decimal_separator'  => wc_get_price_decimal_separator(),
						    'thousand_separator' => wc_get_price_thousand_separator(),
						    'decimals'           => wc_get_price_decimals(),
						    'price_format'       => get_woocommerce_price_format()
						) );

						if($global_option->enable_price_per_char == 1) {
							echo _e(' Price per character', 'eopa');
						}

					?>
					)
					</span>
				<?php } else { ?>
					<span class="price">(
					<?php 


						echo wc_price($global_option->option_price, array(
						    'ex_tax_label'       => false,
						    'currency'           => '',
						    'decimal_separator'  => wc_get_price_decimal_separator(),
						    'thousand_separator' => wc_get_price_thousand_separator(),
						    'decimals'           => wc_get_price_decimals(),
						    'price_format'       => get_woocommerce_price_format()
						) );

						if($global_option->enable_price_per_char == 1) {
							echo _e(' Price per character', 'eopa');
						}


					 ?>
					
					)</span>
				<?php } ?>
			<?php } ?>
		</label>
		<?php if($global_option->option_field_type == 'field') { ?>
			<input data-char="<?php echo $global_option->enable_price_per_char;  ?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop eoinput" type="text" maxlength="<?php echo $global_option->option_maxchars; ?>" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>" />
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'area') { ?>
			<textarea type="textarea" data-char="<?php echo $global_option->enable_price_per_char;  ?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop eoinput" maxlength="<?php echo $global_option->option_maxchars; ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>"><?php if( ! empty($val_post) ){ echo $val_post; } ?></textarea>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'simple_checkbox') { ?>

			<input type="checkbox" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop eocheck" value="<?php echo $global_option->option_maxchars; ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]"  id="field<?php echo $global_option->id?>"/> <?php echo $global_option->option_allowed_file_extensions; ?>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'date') { ?> 
			<input data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop datepick eoinput" type="text" maxlength="<?php echo $global_option->option_maxchars; ?>" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>">
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'time') { ?> 
			<input data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop timepick eoinput" type="text" maxlength="<?php echo $global_option->option_maxchars; ?>" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>">
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'color') { ?> 

			<input type="text" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" id="field<?php echo $global_option->id?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop" />
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />
			<script>
			jQuery("#field<?php echo $global_option->id?>").spectrum({
				    preferredFormat: "rgb",
				    showInput: true,
				    className: "full-spectrum",
				    showInitial: true,
				    showPalette: true,
				    showSelectionPalette: true,
				    maxSelectionSize: 10,
					    palette: [
					        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
					        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
					        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
					        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
					        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
					        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
					        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
					        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
					        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
					        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
					        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
					        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
					        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
					        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
				    	]
				});
			</script>

		<?php } else if($global_option->option_field_type == 'google_font') { ?> 

			<select name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]"  id="field<?php echo $global_option->id?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop" >
				<option value="">Select Font</option>
				<?php
					foreach($fontNames as $fontName) { ?>
					<option <?php selected($val_post, $fontName);?>><?php echo $fontName;?></option>
				<?php } ?>
			</select>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'google_map') { ?> 

			<input type="hidden" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" id="field<?php echo $global_option->id?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop" />
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

			
			<div id="map" style="height: 300px;width: 100%;"></div>

			<script>
				var map, defaultZoom = parseInt('<?php echo $default_zoom;?>');
				var fieldId = 'field<?php echo $global_option->id?>';
		        function initMap() {                            
		            var latitude = parseFloat('<?php echo $default_lat?>'); 
		            var longitude = parseFloat('<?php echo $default_long?>'); 
		            
		            var defaultLoc = {lat: latitude, lng: longitude};
		            
		            map = new google.maps.Map(document.getElementById('map'), {
		              center: defaultLoc,
		              zoom: defaultZoom,
		              disableDoubleClickZoom: true 
		            });
		                  
		            var marker = new google.maps.Marker({
		              position: defaultLoc,
		              map: map,
		              draggable: true,
		              title: latitude + ', ' + longitude 
		            });   

		            marker.addListener('drag', function(event) {            
		              	document.getElementById(fieldId).value = event.latLng.lat().toFixed(7) + ', ' + event.latLng.lng().toFixed(7);
		              	if (typeof yourFunctionName == 'function') { 
			              	mapCoordsChanged(event.latLng.lat().toFixed(7) + ', ' + event.latLng.lng().toFixed(7));
			            }
		            });
		        }
			</script>
		<?php } else if($global_option->option_field_type == 'range_picker') { ?> 

			<span class="range_picker_min_value"><?php echo $global_option->min_value?></span>
			<input type="range" id="field<?php echo $global_option->id?>" min="<?php echo $global_option->min_value?>" data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop rangepick" max="<?php echo $global_option->max_value?>"  value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" />
			<span class="range_picker_max_value"><?php echo $global_option->max_value?></span>
			<span id="range<?php echo $global_option->id?>" class="range_val"></span>
			<input type="hidden" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" id="range_picker<?php echo $global_option->id?>" />
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

			<script>
				document.getElementById('field<?php echo $global_option->id?>').addEventListener('change', function(e) {
					document.getElementById('range_picker<?php echo $global_option->id?>').value = e.target.value;
					document.getElementById('range<?php echo $global_option->id?>').innerHTML =' (' + e.target.value + ' )';
					ProductCustomOptions();
					if (typeof ProductCustomOptions == 'function') { 
		              	// rangeChanged(e.target.value);
		              	ProductCustomOptions();
		            }
				});
			</script>
		<?php } else if($global_option->option_field_type == 'drop_down') { ?> 
			<select type="select" class="eoa eoop eoinput" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>">
				<option value=""><?php echo _e('--- Zvolte prosím jednu z možností ').$global_option->option_title; ?> ---</option>
				<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
				<?php foreach($RowOptions as $option_row) { 
					if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
					?>

					<option <?php selected($val_post,$option_row->option_row_title); ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" value="<?php echo $option_row->option_row_title; ?>">
					<?php echo $option_row->option_row_title; ?>   
					<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
							-  <span class="price">(
							<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );


							 ?>
							 )</span>
						<?php } else { ?>
							-  <span class="price">(
							<?php 



									echo wc_price($option_row->option_row_price, array(
									    'ex_tax_label'       => false,
									    'currency'           => '',
									    'decimal_separator'  => wc_get_price_decimal_separator(),
									    'thousand_separator' => wc_get_price_thousand_separator(),
									    'decimals'           => wc_get_price_decimals(),
									    'price_format'       => get_woocommerce_price_format()
									) );


							?>
							
							)</span>
						<?php } ?>
					<?php } ?>
					</option>
				<?php } } ?>
			</select>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'multiple') { ?> 
			<select type="mselect" multiple = "multiple" class="fmm eoop eoinput multi" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop" >
			
			<option value=""><?php echo _e('--- Zvolte prosím jednu z možností ').$global_option->option_title; ?> ---</option>
				<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
				<?php foreach($RowOptions as $option_row) { 
					if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
					<?php 
						$title = strtolower(str_replace(' ', '_', $global_option->option_title));
						if(isset($_POST['product_options'][$title]['value']) && $_POST['product_options'][$title]['value'] !='' ) {
							$val_post2 = $_POST['product_options'][$title]['value'];
						} else {
							$val_post2 = '';
						}

					?>
					<option <?php if($val_post2!='') { foreach ($val_post2 as $valp) { selected($valp,$option_row->option_row_title); } } ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" value="<?php echo $option_row->option_row_title; ?>">
					<?php echo $option_row->option_row_title; ?>   
					<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
							-  <span class="price">(
							<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

							?>
							
							)</span>
						<?php } else { ?>
							-  <span class="price">(
							<?php 

								echo wc_price($option_row->option_row_price, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

								 ?>)</span>
						<?php } ?>
					<?php } ?>
					</option>
				<?php } } ?>
			</select>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'radio') { ?> 
			<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
			<input data-price="0" checked = "true" class="eoop" style="display:none" value="" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][]" id="field<?php echo $global_option->id?>">
			<?php foreach($RowOptions as $option_row) {
				if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
				<div class="radio">
					<span class="rowTitle">
						<?php echo $option_row->option_row_title; ?>
						<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
						<span class="price">(
						<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

							?>
						)</span>
						<?php } else { ?>
						<span class="price">(<?php 

								echo wc_price($option_row->option_row_price, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

								 ?>)</span>
						<?php } ?>
						<?php } ?>
					</span>
					<label>
						<input <?php checked($val_post,$option_row->option_row_title); ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop" value="<?php echo $option_row->option_row_title; ?>" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]">
						<?php if($option_row->option_image == '') { ?>
							<img onClick="changeimage('<?php echo EOPA_URL ?>/images/no_image.png')" src="<?php echo EOPA_URL ?>/images/no_image.png" width="75" alt="<?php echo $option_row->option_row_title; ?>" class="img-thumbnail" />
						<?php } else { ?>
							<img onClick="changeimage('<?php echo $option_row->option_pro_image; ?>')" src="<?php echo $option_row->option_image; ?>" width="75" alt="<?php echo $option_row->option_row_title; ?>" class="img-thumbnail" />
						<?php } ?>
					</label>
				</div>
				<?php } } ?>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'simple_radio') { ?>
			<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
			<input data-price="0" checked = "true" class="eoop" style="display:none" value="" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][]" id="field<?php echo $global_option->id?>">
			<?php foreach($RowOptions as $option_row) {
				if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
				<div class="sim_radio_button">


					<input <?php checked($val_post,$option_row->option_row_title); ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop sim_radio" value="<?php echo $option_row->option_row_title; ?>" type="radio" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" style="display: block;">

					<span class="rowTitle">
						<?php echo $option_row->option_row_title; ?>
						<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
						<span class="price">(
						<?php 
								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

							?>
						)</span>
						<?php } else { ?>
						<span class="price">(<?php 

								echo wc_price($option_row->option_row_price, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

								 ?>)</span>
						<?php } ?>
						<?php } ?>
					</span>
				</div>
				<?php } } ?>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />

		<?php } else if($global_option->option_field_type == 'checkbox') { ?> 
			<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
			<input data-price="0" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" checked = "true" class="eoop" style="display:none" value="" type="checkbox" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][]" id="field<?php echo $global_option->id?>">
			<?php foreach($RowOptions as $option_row) {
				if(($global_option->manage_stock != 'yes') || ($global_option->manage_stock == 'yes' && $option_row->stock > 0) ) { ?>
			<?php 
				$title = strtolower(str_replace(' ', '_', $global_option->option_title));
				if(isset($_POST['product_options'][$title]['value']) && $_POST['product_options'][$title]['value']!='') {
					$val_post2 = $_POST['product_options'][$title]['value'];
				} else {
					$val_post2 = '';
				}
			?>
				<div class="checkbox">
					
					<label>
						<input <?php if($val_post2!='') { foreach ($val_post2 as $valp) { checked($valp,$option_row->option_row_title); } } ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" class="eoop" value="<?php echo $option_row->option_row_title; ?>" type="checkbox" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]">
						<?php if($option_row->option_image == '') { ?>
							
						<?php } else { ?>
							<img onClick="changeimage('<?php echo $option_row->option_pro_image; ?>')" src="<?php echo $option_row->option_image; ?>" width="75" alt="<?php echo $option_row->option_row_title; ?>" class="img-thumbnail" />
						<?php } ?>
					</label>
					<span class="rowTitle">
						<?php echo $option_row->option_row_title; ?>
						<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
						<span class="price">(<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

							?>)</span>
						<?php } else { ?>
						<span class="price">(<?php 

								echo wc_price($option_row->option_row_price, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

								 ?>)</span>
						<?php } ?>
						<?php } ?>
					</span>
				</div>
				<?php } } ?>
			<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />


		<?php } else if($global_option->option_field_type == 'file') { ?>
				<input data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" data-multiply="<?php echo $global_option->multiply_price_by_qty;?>" class="eoop" class="eoinput" type="file" value="" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][value]" id="field<?php echo $global_option->id?>">
				<input name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][phfield]" id="field<?php echo $global_option->id?>_phfield" type="hidden" />
		<?php } ?>
		
	</div>
	<?php }} } ?>
	<?php } ?>
	<!-- Global Options End -->
	<?php 
	foreach($global_dependencies_data as $option_id => $option_dependency) {
		if($option_dependency['showif'] != '') {
			if($option_dependency['showif'] == 'Hide') { 
				if($option_dependency['ccondition'] == 'is equal to') {
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: false
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		
					  		jQuery('#field<?php echo $option_id;?>_wrapper').show();
					  		jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');

					  			if(jQuery(values).not(selectVals).get().length === 0) {
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    }
					  		});
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() == checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['<?php echo $option_dependency["ccondition_value"];?>']
				    			}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php 				}
				} else if($option_dependency['ccondition'] == 'is not equal to') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: true
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  		jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');


							    if(jQuery(values).not(selectVals).get().length === 0) {
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});
						</script>
<?php 				}  else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() != checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').show();
							jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
								if(jQuery(this).val() == '<?php echo $option_dependency["ccondition_value"];?>') {
									jQuery('#field<?php echo $option_id;?>_phfield').val('');
									jQuery('#field<?php echo $option_id;?>_wrapper').show();
								} else {
									jQuery('#field<?php echo $option_id;?>_wrapper').hide();
									jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
								}
							});
						</script>
<?php				}
				} else if($option_dependency['ccondition'] == 'is not empty') {
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>') {
									
									jQuery(this).on('change', function() {
										if(jQuery(this).is(':checked')) {
											jQuery('#field<?php echo $option_id;?>_wrapper').hide();
											jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
										} else {
											jQuery('#field<?php echo $option_id;?>_phfield').val('');
											jQuery('#field<?php echo $option_id;?>_wrapper').show();
										}
									});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				}  else if($option_dependency['option_field_type'] == 'multiple'){ ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').show();
							jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
								
					  			if(jQuery(this).val().length !== 0 && jQuery(this).val()[0] !== '') {
					  				jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  				jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    }
					  		});

						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'file') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				values: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {
			    					
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked')) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				values: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {
			    					
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php				} 			
				} else if($option_dependency['ccondition'] == 'is checked') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>') {
									
									jQuery(this).on('change', function() {
										if(jQuery(this).is(':checked')) {
											jQuery('#field<?php echo $option_id;?>_wrapper').hide();
											jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
										} else {
											jQuery('#field<?php echo $option_id;?>_phfield').val('');
											jQuery('#field<?php echo $option_id;?>_wrapper').show();
										}
									});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script type="text/javascript">
			    			var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() == checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} 
				}
/**************** Show ******************/
			} else {
				if($option_dependency['ccondition'] == 'is equal to') { ?>
				<?php 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: true
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
					</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  		jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');
					  			
							    if(jQuery(values).not(selectVals).get().length === 0) {
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() == checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				}
			    			});
						</script>
<?php 				}  else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				values: ['<?php echo $option_dependency["ccondition_value"];?>']
				    			}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php 				} ?>
<?php			} else if($option_dependency['ccondition'] == 'is not equal to') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: false
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
					</script>
<?php				} else if($option_dependency['option_field_type'] == 'multiple') { ?>
						<script type="text/javascript">
					  		jQuery('#field<?php echo $option_id;?>_wrapper').hide();
					  		jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
					  			
					  			var selectVals = jQuery(this).val();
					  			var values = '<?php echo $option_dependency["ccondition_value"];?>'.split(',');


							    if(jQuery(values).not(selectVals).get().length !== 0) {
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('');
							    	jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked') && jQuery(this).val() != checkVal) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['<?php echo $option_dependency["ccondition_value"];?>']
				    			}}, {
				    				onDisable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
				    				},
				    				onEnable: function() {
				    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
				    				}
				    			});
						</script>
<?php				}
				} else if($option_dependency['ccondition'] == 'is not empty') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() != '') {
									
									jQuery(this).on('change', function() {
										var res = jQuery('input[type="checkbox"]:checked').filter(function(index) {
											return jQuery(this).val() != '';
										});

										if(res.length > 0) {
											jQuery('#field<?php echo $option_id;?>_phfield').val('');
											jQuery('#field<?php echo $option_id;?>_wrapper').show();
										} else {
											jQuery('#field<?php echo $option_id;?>_wrapper').hide();
											jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
										}
									});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'file') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {
			    					
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'multiple'){ ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							jQuery('#field<?php echo $option_dependency["cfield"]?>').on('change', function() {
								
					  			if(jQuery(this).val().length !== 0 && jQuery(this).val()[0] !== '') {
					  				jQuery('#field<?php echo $option_id;?>_phfield').val('');
					  				jQuery('#field<?php echo $option_id;?>_wrapper').show();
							    } else { 
							    	jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							    	jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							    }
					  		});

						</script>
<?php 				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script>
							jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
							var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo str_replace(' ', '_',strtolower($option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked') === true) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				}
			    			});
						</script>
<?php 				} else { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
				    			'#field<?php echo $option_dependency["cfield"]?>': {
				    				not: ['']
				    		}}, {
			    				onDisable: function() {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				},
			    				onEnable: function() {
			    					
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    				}
			    			});
						</script>
<?php				} ?>					
<?php			} else if($option_dependency['ccondition'] == 'is checked') { 
					if($option_dependency['option_field_type'] == 'checkbox') { ?>
						<script type="text/javascript">
<?php

	foreach($option_dependency['rows'] as $option_row_title) { ?>
							jQuery('input[type="checkbox"]').each(function() {
								if(jQuery(this).val() == '<?php echo $option_row_title?>' 
									&& '<?php echo $option_dependency["ccondition_value"];?>' == '<?php echo $option_row_title?>') {
									jQuery(this).attr('id', '<?php echo "chkbox".$i;?>');
									jQuery('#field<?php echo $option_id;?>_wrapper').dependsOn({
							    		'#<?php echo "chkbox".$i;?>': {
							    		checked: true
							    	}}, {
					    				onDisable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
					    				},
					    				onEnable: function() {
					    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
					    				}
					    			});
								}
							});
<?php 
		$i++;
	}
?>
						</script>
<?php				} else if($option_dependency['option_field_type'] == 'radio' || $option_dependency['option_field_type'] == 'simple_radio') { ?>
						<script type="text/javascript">
							jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    			var checkVal = '<?php echo $option_dependency["ccondition_value"]?>';
			    			jQuery("input[name='product_options[<?php echo strtolower(str_replace(' ', '_', $option_dependency["option_title"]));?>][value]']").on('change', function() {
			    				
			    				if(jQuery(this).is(':checked')) {
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('');
			    					jQuery('#field<?php echo $option_id;?>_wrapper').show();
			    				} else {
			    					jQuery('#field<?php echo $option_id;?>_wrapper').hide();
			    					jQuery('#field<?php echo $option_id;?>_phfield').val('not required');
			    				}
			    			});
						</script>
<?php 				} 
			 	}
			}
		}
	}
?>
</div>

<?php 
				
			$product = wc_get_product( $post->ID );
				
			if ( is_object( $product ) ) {
				
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
				
				$d_price    = $tax_display_mode == 'incl' ? $product->get_price() : $product->get_price();
			
			} else {
				
				$d_price    = '';
				
			}

?>
<div class="price_total">
	<div id="product_options_total" product-type="<?php echo $product->get_type();  ?>" product-price="<?php echo $d_price; ?>"></div>
</div>

<script type="text/javascript">
	
	jQuery(document).ready(function() {
		jQuery(this).on( 'change', 'input:text, select, textarea, input:radio, input:checkbox, input:file, input.qty, input.range_pick', function(e) {
			ProductCustomOptions('cli');
		});
	});
	
	ProductCustomOptions();
	function ProductCustomOptions(a) { 
	
		var option_total = 0;
		var product_price = jQuery('#product_options_total').attr( 'product-price' );
        var calculated_price = getPcCalculatedPrice();
        var product_total_price = 0;
		var final_total = 0;
		let multiplyPriceByQty = true;
		var qty = jQuery('input.qty').val();

		jQuery('.eoop').each( function() {
			var option_price = 0;
			var data_char = 0;

			if(jQuery(this).attr('type') == 'radio') {
				
				if(jQuery(this).is(':checked')) {
					
					option_price = jQuery(this).attr('data-price');
					if(typeof jQuery(this).attr('id') === "undefined" && jQuery(this).data('multiply') == '1') {
						option_price = option_price * qty;
					}

					if(jQuery(this).parents('.radio').prevAll('.eoop').length > 0 && jQuery(this).parents('.radio').prevAll('.eoop').attr('id') != '') {

						if(jQuery(jQuery(this).parents('.eocustomgroup').find('.eoop')[0]).data('multiply') == '1')
							option_price = option_price * qty;
					}
				}
			} else if(jQuery(this).attr('type') == 'checkbox') {
				if(jQuery(this).is(':checked') && typeof jQuery(this).attr('id') === 'undefined') {
					
					option_price = jQuery(this).attr('data-price');
					
					if(jQuery(this).parents('.checkbox').prevAll('.eoop').length > 0 && jQuery(this).parents('.checkbox').prevAll('.eoop').attr('id') != '') {

							if(jQuery(jQuery(this).parents('.eocustomgroup').find('.eoop')[0]).data('multiply') == '1') 
								option_price = option_price * qty;
					}
				}
			} else if(jQuery(this).attr('type') == 'select') {

				option_price = jQuery("option:selected", this).attr('data-price');
				if(jQuery(this).data('multiply') == '1')
					option_price *= qty;

			} else if(jQuery(this).attr('type') == 'mselect') {
					
				var sum = option_price;
			    jQuery( "option:selected", this ).each(function() {
			      str = parseFloat(jQuery( this ).attr('data-price'));
			      sum = str + sum;
			    });
			    option_price = sum;

			    if(jQuery(this).data('multiply') == '1')
					option_price *= qty;

			} else if(jQuery(this).attr('type') == 'text') {
				
				data_char = jQuery(this).attr('data-char');

				if(data_char == 1) {
					opp_price = jQuery(this).attr('data-price');
					option_price = opp_price * jQuery(this).val().length;
				} else {
					option_price = jQuery(this).attr('data-price');
				}

				if(jQuery(this).data('multiply') == '1')
					option_price *= qty;
			} else if(jQuery(this).attr('type') == 'textarea') {
				data_char = jQuery(this).attr('data-char');
				if(data_char == 1) {
					opp_price = jQuery(this).attr('data-price');
					option_price = opp_price * jQuery(this).val().length;

				} else {
					option_price = jQuery(this).data('price');
				}

				if(jQuery(this).data('multiply') == '1')
					option_price *= qty;
			} else if(jQuery(this).attr('type') == 'range') {

				let rangePicker = jQuery('#'+jQuery(this).attr('id').replace('field', 'range_picker'));
				if(rangePicker.val() != '') {
					option_price = jQuery(this).attr('data-price');
					if(jQuery(this).data('multiply') == '1')
						option_price *= qty;
				}
			} else {
				option_price = jQuery(this).attr('data-price');
				if(jQuery(this).data('multiply') == '1')
					option_price *= qty;
			}

			if(option_price == '')
				var newprice = 0;
			else
				var newprice = option_price;
			
			var value_entered =  jQuery(this).val();
			
			if(value_entered != '' && !isNaN(newprice) )
				option_total = parseFloat( option_total ) + parseFloat( newprice );
		});
		
		// if ( option_total > 0 && qty > 0 ) { // (7.2.2020)

            // (7.2.2020)
			if(multiplyPriceByQty) {
				// option_total = parseFloat( option_total * qty );
                calculated_price = parseFloat(calculated_price * qty);
            } else {
				// option_total = parseFloat( option_total );
                calculated_price = parseFloat( calculated_price );
            }

            if ( product_price ) {
                if(multiplyPriceByQty) {
                    product_total_price = parseFloat(product_price * qty);
                } else {
                    product_total_price = parseFloat( product_price );
                }
            }

			var price_form = "<?php echo get_option( 'woocommerce_currency_pos' ); ?>";
			var op_price = '';
			
			if(price_form == 'left') {
				op_price = accounting.formatMoney(option_total, { symbol: "<?php echo $string; ?>",  format: "%s%v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'left_space') {
				op_price = accounting.formatMoney(option_total, { symbol: "<?php echo $string; ?>",  format: "%s %v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right') {
				op_price = accounting.formatMoney(option_total, { symbol: "<?php echo $string; ?>",  format: "%v%s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right_space') {
				op_price = accounting.formatMoney(option_total, {symbol: "<?php echo $string; ?>", format: "%v %s"}, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>");
			}

            // (7.2.2020)
            final_total = parseFloat(option_total) + parseFloat(product_total_price) + parseFloat(calculated_price);

			var fi_price = '';
			
			if(price_form == 'left') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%s%v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'left_space') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%s %v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%v%s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right_space') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%v %s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			}

			html = '';
			// op_price= option_total+',00 Kč';
			html = html + '<div class="tprice"><input type="hidden" name="mmoptionprice" value="'+option_total+'" /><div class="leftprice"><?php echo _e("Options Total:","eopa") ?></div><div class="rightprice optionprice">'+op_price+'</div></div>';

			// if ( final_total ) { // (7.2.2020)
				html = html + '<div class="tprice"><div class="leftprice"><?php echo _e("Final Total:","eopa") ?></div><div class="rightprice finalprice">'+fi_price+'</div></div>';
			// } // (7.2.2020)

			html = html + '</dl>';
			jQuery('#product_options_total').html( html );
		// } else { // (7.2.2020)
		// 	jQuery('#product_options_total').html( '' ); // (7.2.22020)
		// } // (7.2.2020)
	}

checkConfiguratorRequiredFieldsAreValidOnFormChange();

function changeimage(image) {

    // if( checkConfiguratorRequiredFieldsAreValid() ){
    //     jQuery('.single_add_to_cart_button').removeAttr("disabled");
    // } else {
    //     jQuery('.single_add_to_cart_button').attr("disabled", "disabled");
    // }
    // setTimeout(checkConfiguratorRequiredFieldsAreValid, 250);
	
    if (image == '') {
        return;
    }

	jQuery('.flex-active').attr('onClick', 'changeSRC(this);');
	jQuery('.zoomImg').attr('src', image);
	// jQuery('.wp-post-image').attr('src', image);
	jQuery('.flex-active-slide').attr('data-thumb', image);
	jQuery('.flex-active-slide img').attr('src', image);
	jQuery('.flex-active-slide img').attr('data-src', image);
	jQuery('.flex-active-slide img').attr('srcset', image);
	jQuery('.flex-active-slide img').attr('data-large_image', image);
	jQuery('.flex-active-slide a').attr('href', image);
}

function changeSRC(picsrc) {

	var text = picsrc.src;
	var nsrc = text.replace('-180x180', '');

	jQuery('.flex-active-slide').attr('data-thumb', nsrc);
	jQuery('.flex-active-slide img').attr('src', nsrc);
	jQuery('.flex-active-slide img').attr('data-src', nsrc);
	jQuery('.flex-active-slide img').attr('srcset', nsrc);
	jQuery('.flex-active-slide img').attr('data-large_image', nsrc);
	jQuery('.flex-active-slide a').attr('href', nsrc);
}


jQuery('.eocustomgroup').on('change', function(){
    setTimeout(function() {
        jQuery.each(jQuery(".eocustomgroup"), function(){
            if (jQuery(this).is(":hidden")) {
                // console.log(jQuery(this).find(':input')
                //     .not(':button, :submit, :reset, :hidden'));

                jQuery(this).find('input[type="radio"], input[type="checkbox"]').removeAttr('checked');
            }
        });
    }, 200);
});

</script>

<script>
    var URL = "<?php echo EOPA_URL; ?>";
</script>