<?php 
error_reporting(0);
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( !class_exists( 'EO_Product_Addons_Front' ) ) { 

	class EO_Product_Addons_Front extends EO_Product_Addons {

		public function __construct() {

			add_action('init', array($this, 'track_front_loading'));
			add_action( 'wp_loaded', array( $this, 'front_scripts' ) );

			//Show Options on single product page
			add_action( 'woocommerce_before_add_to_cart_button', array($this, 'ProductCustomOptions' ));
            
            if (in_array( 'Pricing Calculator/extendons-price-calculator.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                add_action( 'pc_before_add_to_cart_button', array($this, 'ProductCustomOptions'));
            }

            if (in_array( 'extendons-mix-match-bundles/extendons-mix-match-bundles.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
 
                add_action( 'mixmatch_optoins_compatible_before_add_to_cart_button', array($this, 'ProductCustomOptions' ));
            }

			//Validate Options
			add_filter ( 'woocommerce_add_to_cart_validation', array ($this,'ValidateCustomOptions' ), 10, 3 );

			//This is will change add to cart button text to select options on shop page.
			add_filter('woocommerce_loop_add_to_cart_link', array($this, 'ChangeTextAddToCartButton'), 10, 2);

			// Add item data to the cart
			add_filter( 'woocommerce_add_cart_item_data',  array($this, 'addProductToCart') , 10, 2 );

			add_filter( 'woocommerce_add_cart_item',  array($this, 'add_cart_item') , 20, 1 );

			// Load cart data per page load
			add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'get_cart_item_from_session') , 20, 2 );

			// Get item data to display
			add_filter( 'woocommerce_get_item_data',  array($this, 'get_item_data') , 20, 2 );

			// Add custom options in order
			add_action( 'woocommerce_add_order_item_meta',  array($this, 'order_item_meta') , 10, 2 );
			
            if (!in_array( 'extendons-price-calculator/extendons-price-calculator.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                add_filter( 'woocommerce_locate_template', array($this, 'load_template'), 1, 3 );
            }

            if (in_array( 'extendons-mix-match-bundles/extendons-mix-match-bundles.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
                add_filter( 'woocommerce_locate_template', array($this, 'load_template'), 1, 3 );

            }
		}

		public function load_template( $template, $template_name, $template_path ) {

     		global $woocommerce;

     		$_template = $template;
			if ( ! $template_path ) 
				$template_path = $woocommerce->template_url;

			$plugin_path = EOPA_PLUGIN_DIR.'front/woocommerce/';
			$template = locate_template(
				array(
					$template_path . $template_name,
					$template_name
				)
			);

			if(  file_exists( $plugin_path . $template_name ) )
				$template = $plugin_path . $template_name;

			if ( ! $template )
				$template = $_template;

			return $template;
		}


		public function modify_cart_product_subtotal($product_subtotal, $product, $quantity, $cart) {

			$currency_symbol = get_woocommerce_currency_symbol();
			
			if($product->get_sale_price() == '')
				$product_price = $product->get_regular_price();
			else
				$product_price = $product->get_sale_price();

			$product_price = ($product_price * $quantity) + 5;

			return $currency_symbol.$product_price;
		}
		public function display_remove_order_item_button( $item_id, $item, $order ){ ?>

			<div class="order_item_exten_view">
				<ul>

			<?php foreach ( $order->get_items() as $item_id => $item ) { ?>

				
				<li>
					<?php echo $product_name = $item['name']; ?>
				</li>
				<li>
					<?php echo $custom_field = wc_get_order_item_meta( $item_id, '<b>Image</b>', true ); ?>
				</li>
			<?php } ?>

				</ul>	
			</div>
	   	<?php  } 
    

		public function front_scripts() {
            wp_enqueue_style( 'eofront-css', plugins_url( '/css/eopa_front_style.css', __FILE__ ), false );
            wp_enqueue_script( 'eo-ui-script', '//code.jquery.com/ui/1.11.4/jquery-ui.js', array('jquery'), false );
        	wp_enqueue_script( 'eo-front-timepicker', plugins_url( '/js/jquery-ui-timepicker-addon.js', __FILE__ ), array('jquery'), false );
           	wp_enqueue_script( 'eo-fancy3-js', plugins_url( '/js/jquery.fancybox.min.js', __FILE__ ), false );
            wp_enqueue_script( 'eo-front-js', plugins_url( '/js/eo_front_js.js', __FILE__ ), false );
            wp_enqueue_style( 'eo-UI-css', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css', false );
            wp_enqueue_style( 'eo-fancy3-css', plugins_url('/css/jquery.fancybox.min.css', __FILE__ ), false );
            wp_enqueue_script( 'eo-accounting-js', plugins_url( '/js/accounting.min.js', __FILE__ ), false );
            wp_enqueue_script('eo-depends-on-js', plugins_url('/js/depends_on.js', __FILE__), false);
            wp_enqueue_style( 'eo-spectrum-css', plugins_url('/css/spectrum.css', __FILE__ ), false );
            wp_enqueue_script( 'eo-spectrum-js', plugins_url( '/js/spectrum.js', __FILE__ ), false );	
        }

        function eo_wc_add_notice($string, $type="error") {
 	
			global $woocommerce;
			if( version_compare( $woocommerce->version, 2.1, ">=" ) ) {
				wc_add_notice( $string, $type );
			} else {
			   $woocommerce->add_error ( $string );
			}
		}

		public function track_front_loading() {
			if(!get_option('fields_loaded'))
				add_option('fields_loaded', '0');
			else
				update_option('fields_loaded', '0');
		}

        function ProductCustomOptions() {

            global $product, $post, $wpdb;

            // print_r(get_option('active_plugins'));

            // extendons-price-calculator/extendons-price-calculator.php 

            // if()get_option( 'active_plugins' ) )

        	if(get_option('fields_loaded') === '1')
        		return;
        	else
        		update_option('fields_loaded', '1');
        	require  EOPA_PLUGIN_DIR . 'front/view/product_custom_options.php';
        }

        function getGlobalRules() {

        	global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_global_rule_table." WHERE rule_status = %s", 'enable'));      
            return $result;
        }

        function getGlobalOptions($rule_id) {

        	global $wpdb;

            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE option_type = %s AND global_rule_id = %d ORDER BY option_sort_order, option_sort_order", 'global', $rule_id));      
            return $result;
        }


        function getProductOptions($post_id) {

        	global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE product_id = %d ORDER BY option_sort_order, option_sort_order", $post_id));      
            return $result;
        }

        function getRowOptions($option_id) {

        	global $wpdb;
			
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_rowoption_table." WHERE option_id = %d ORDER BY option_row_sort_order, option_row_sort_order", $option_id));      
            return $result;
        }

        function getRowOptionsByName($option_id, $name) {

        	global $wpdb;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_rowoption_table." WHERE option_row_title = %s AND option_id = %d", $name, $option_id));      
            return $result;
        }

        function getGlobalRequired($post_id,$key) {

        	global $wpdb;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE option_type = %s AND global_rule_id = %d  AND option_title = %s", 'global', $post_id, $key));      
            return $result;
        }

        function getProductRequired($post_id,$key) {

        	global $wpdb;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE product_id = %d AND option_title = %s", $post_id, $key));      
            return $result;
        }

        function addProductToCart( $cart_items,$product_id ) {

        	$data = array();
        	$val_post = '';
        	$is_exclude = get_post_meta ( $product_id, '_exclude_global_options', true );

			if ( empty( $cart_items['options'] ) )
				$cart_items['options'] = array();

			$array_options = $this->getProductOptions($product_id);
			$GlobalRules = $this->getGlobalRules();

			$GlobalOptions = array();
			foreach($GlobalRules as $gRule) {
				if($gRule->applied_on == 'products') {
					$proudctIDs = explode(', ', $gRule->proids);
					if(in_array($product_id, $proudctIDs)) {
						
						if($is_exclude!='yes')
							$array_options1[] = $this->getGlobalOptions($gRule->rule_id);
						else
							$array_options1[] = 0;
					}
				} else if ($gRule->applied_on == 'categories') {
					$categoryIDs = explode(', ', $gRule->catproids);
				    if(in_array($product_id, $categoryIDs)) {
						if($is_exclude!='yes')
							$array_options1[] = $this->getGlobalOptions($gRule->rule_id);
						else
							$array_options1[] = 0;
					}
				}
			}

			if($array_options !='') {
				foreach ( $array_options as $options_key => $options ) { 

					$title = strtolower(str_replace(' ', '_', $options->option_title));
					if(isset($_POST['product_options'][$title]) && $_POST['product_options'][$title]!='')
						$val_post = $_POST['product_options'][$title]['value'];
					else
						$val_post = '';

					$price_per_char = $options->enable_price_per_char;
					$multiply_by_qty = $options->multiply_price_by_qty;
					$proprice = get_post_meta($product_id, "_price", true);

					if($options->option_price_type == 'percent')
						$OptionPrice = $proprice * $options->option_price / 100;
					else
						$OptionPrice = $options->option_price;

					if($options->option_field_type == 'file') {
						foreach ($_FILES as $files) {
							$fname = strtolower(str_replace(' ', '_', $options->option_title));
							if(isset($fname) && $fname!='')
								$name = strtolower(str_replace(' ', '_', $options->option_title));
							else
								$name = '';
							
							$filename = $files['name'];
							$file = time().$files['name'][strtolower(str_replace(' ', '_', $options->option_title))]['value'];

							$target_path = EOPA_PLUGIN_DIR.'uploads/';
							$target_path = $target_path . $file;

							$target_path2 = EOPA_URL.'uploads/';
							$target_path2 = $target_path2 . $file;	
							
							if($files['tmp_name'][$name]['value'] != '') {
								if($name!='')
									$temp = move_uploaded_file($files['tmp_name'][$name]['value'], $target_path);
								$check = "";
								if($this->is_image($file)) {
									$check = "image";
								} else {
									$check = "file";
								}
								$data[] = array(
									'name'  => $name,
									'filename' => $filename,
									'value' => $file,
									'price' => $OptionPrice,
									'check' => $check,
									'multiply_price_by_qty' => $multiply_by_qty
								);
							}
						}
					}

					if(isset($val_post) && $val_post!='') {
						if($options->option_field_type == 'multiple' || $options->option_field_type == 'checkbox') {
							
							$data[] = array(
								'name'  => $title
							);
							
							foreach ($val_post as $rowvalue) {
								$value = $rowvalue;
								$RowOption = $this->getRowOptionsByName($options->id, $rowvalue); 

								if($RowOption!='') {
									if($RowOption->option_row_price_type == 'percent') {
										$RowOptionPrice = $proprice*$RowOption->option_row_price/100;
									} else {
										$RowOptionPrice = $RowOption->option_row_price;
									}
								}

								$data[] = array(
									'name'  => '',
									'value' => $value,
									'price' => $OptionPrice,
									'option_price' => $RowOptionPrice,
									'option_type' => 'mc',
									'o_type' => 'm',
									'ex_name' => $title,
									'check' => '',
									'multiply_price_by_qty' => $multiply_by_qty
								);
							}
						} else if($options->option_field_type == 'drop_down'|| $options->option_field_type == 'radio' || $options->option_field_type == 'simple_radio') {

							$value = $val_post;
							$RowOption = $this->getRowOptionsByName($options->id, $value); 

							if($RowOption->option_row_price_type == 'percent') {
								$RowOptionPrice = $proprice * $RowOption->option_row_price/100;
							} else {
								$RowOptionPrice = $RowOption->option_row_price;
							}

							$data[] = array(
								'name'  => $title,
								'value' => $value,
								'price' => $OptionPrice,
								'option_price' => $RowOptionPrice,
								'option_type' => 'mc',
								'check' => '',
								'multiply_price_by_qty' => $multiply_by_qty
							);

						} else if($options->option_field_type == 'range_picker')  {
						
							$value = $val_post;
							$data[] = array(
								'name'  => $title,
								'value' => $value,
								'price' => $OptionPrice,
								'option_price' => '',
								'price_per_unit' => $price_per_char,
								'count_val' => strlen($value),
								'multiply_price_by_qty' => $multiply_by_qty
							);
						} else  {
							$value = $val_post;
							$data[] = array(
								'name'  => $title,
								'value' => $value,
								'price' => $OptionPrice,
								'option_price' => '',
								'price_per_char' => $price_per_char,
								'count_val' => strlen($value),
								'multiply_price_by_qty' => $multiply_by_qty
							);
						}

					} else {

					}

					$cart_items['options'] =  $data;
				}
			}

			if($array_options1!='') {

				foreach ( $array_options1 as  $option ) { 
					foreach ( $option as $options_key => $options ) { 

						$title = strtolower(str_replace(' ', '_', $options->option_title));
						if(isset($_POST['product_options'][$title]) && $_POST['product_options'][$title]!='') 
							$val_post = $_POST['product_options'][$title]['value'];
						else
							$val_post = '';

						$price_per_char = $options->enable_price_per_char;
						$multiply_by_qty = $options->multiply_price_by_qty;
						
						$proprice = get_post_meta($product_id, "_price", true);

						if($options->option_price_type == 'percent')
							$OptionPrice = $proprice*$options->option_price/100;
						else
							$OptionPrice = $options->option_price;

						if($options->option_field_type == 'file') {

							foreach ($_FILES as $files) {
								$fname = strtolower(str_replace(' ', '_', $options->option_title));
								if(isset($fname) && $fname!='')
									$name = strtolower(str_replace(' ', '_', $options->option_title));
								else
									$name = '';
								$filename = $files['name'][strtolower(str_replace(' ', '_', $options->option_title))]['value'];
								
								$file = time().$files['name'][strtolower(str_replace(' ', '_', $options->option_title))]['value'];

								$target_path = EOPA_PLUGIN_DIR.'uploads/';
								$target_path = $target_path . $file;

								$target_path2 = EOPA_URL.'uploads/';
								$target_path2 = $target_path2 . $file;
							
								if($files['tmp_name'][$name]['value'] != '') {
									if($name!='') 
										$temp = move_uploaded_file($files['tmp_name'][$name]['value'], $target_path);
									$check = "";
									if($this->is_image($file)) {
										$check = "image";
									} else {
										$check = "file";
									}

									$data[] = array(
										'name'  => $name,
										'filename' => $filename,
										'value' => $file,
										'price' => $OptionPrice,
										'check' => $check,
										'multiply_price_by_qty' => $multiply_by_qty
									);
								}	
							}
						}

						if(isset($val_post) && $val_post != '') {
							if($options->option_field_type == 'multiple' || $options->option_field_type == 'checkbox') {
								
								$data[] = array(
									'name'  => $title,
									
									);
								
								foreach ($val_post as $rowvalue) {
									$value = $rowvalue;
									$RowOption = $this->getRowOptionsByName($options->id, $rowvalue); 

									if($RowOption->option_row_price_type == 'percent') {
										$RowOptionPrice = $proprice*$RowOption->option_row_price/100;
									} else {
										$RowOptionPrice = $RowOption->option_row_price;
									}

									$data[] = array(
									'name'  => '',
									'value' => $value,
									'price' => $OptionPrice,
									'option_price' => $RowOptionPrice,
									'option_type' => 'mc',
									'o_type' => 'm',
									'ex_name' => $title,
									'check' => '',
									'multiply_price_by_qty' => $multiply_by_qty
									);
								}
							} else if($options->option_field_type == 'radio' || $options->option_field_type == 'drop_down' || $options->option_field_type == 'simple_radio') {

								$value = $val_post;
								$value = is_array($value) ? $value['value'] : $value;
								$RowOption = $this->getRowOptionsByName($options->id, $value); 

								if($RowOption->option_row_price_type == 'percent') {
									$RowOptionPrice = $proprice * $RowOption->option_row_price/100;
								} else {
									$RowOptionPrice = $RowOption->option_row_price;
								}

								$data[] = array(
									'name'  => $title,
									'value' => $value,
									'price' => $OptionPrice,
									'option_price' => $RowOptionPrice,
									'option_type' => 'mc',
									'check' => '',
									'multiply_price_by_qty' => $multiply_by_qty
								);
							} else  {
								$value = $val_post;
								$data[] = array(
									'name'  => $title,
									'value' => $value,
									'price' => $OptionPrice,
									'option_price' => '',
									'check' => '',
									'price_per_char' => $price_per_char,
									'count_val' => strlen($value),
									'multiply_price_by_qty' => $multiply_by_qty
								);
							}	
						}
						$cart_items['options'] =  $data;
					}
				}
			}
            return $cart_items;
		}

		function add_cart_item($cart_items) {
        
			if ( ! empty( $cart_items['options'] ) ) {
				$extra_cost = 0;
				foreach ( $cart_items['options'] as $options ) {

					$value = is_array($options['value']) ? $options['value']['value'] : $options['value']; 
					if(isset($value) && $value != '') {
						if ( isset($options['price']) && $options['price'] != '' ) {
							
							if(isset($options['price_per_char']) && $options['price_per_char'] == '1') {
								
								if($options['multiply_price_by_qty'] == '1')
									$extra_cost += $options['price'] * $options['count_val'];
								else
									$extra_cost += ($options['price'] * $options['count_val']) / $cart_items['quantity'];
								
							} else if(isset($options['price_per_unit']) && $options['price_per_unit'] == '1') {
								
								if($options['multiply_price_by_qty'] == '1')
									$extra_cost += $options['price'] * $value;
								else
									$extra_cost += ($options['price'] * $value) / $cart_items['quantity'];
							} else {

								if($options['multiply_price_by_qty'] == '1')
									$extra_cost += $options['price'];
								else
									$extra_cost += $options['price']/ $cart_items['quantity'];
							}

						}

						if ( isset($options['option_price']) && $options['option_price'] != '' ) {
							
							
							if($options['multiply_price_by_qty'] != '1') {
								$extra_cost += $options['option_price'] / $cart_items['quantity'];
							} else  {
								$extra_cost += $options['option_price'];
							}
						}
					}
				}
				$cart_items['data']->set_price($extra_cost + $cart_items['data']->get_price());
			}
            
			return $cart_items;
		}


		function get_cart_item_from_session($cart_items, $values) { 
			
			if ( ! empty( $values['options'] ) ) {
				$cart_items['options'] = $values['options'];
				$cart_items = $this->add_cart_item( $cart_items );
			}
			return $cart_items;
		}

		function get_item_data( $other_data, $cart_items ) {
			// echo '<pre>';
			// print_r($cart_items);
			// exit;
			if ( ! empty( $cart_items['options'] ) ) {
				global $wpdb;
				foreach ( $cart_items['options'] as $options ) {
					if(isset($options['check']) && $options['check']=='image') {
						$check = 'image';
					} else {
						$check = '';
					}
					
					$value = $options['value'];
					
					if(isset($options['option_type']) && $options['option_type'] == 'mc' ) {
						$title = '<b>'.ucwords(str_replace('_', ' ', $options['name'])).'</b>';
						if ( isset($options['option_price']) && $options['option_price'] !='' ) {

							if(isset($value) && $value != '') {

								$opvalue = $value.' (' . wc_price($this->get_product_addition_options_price($options['option_price'])) . ')';
							} else {
								$opvalue = '';
							}
						} else {
							if(isset($value) && $value!='') {
								$opvalue = $value;
							} else {
								$opvalue = '';
							}
						}


						if($opvalue!='') {
							if($title == '<b></b>') {
								$other_data[] = array(
									'name'    => '',
									'value'   => $opvalue,
									'check' => $check,
								);
							} else {
								$other_data[] = array(
									'name'    => $title,
									'value'   => $opvalue,
									'check' => $check,
								);
							}
						}
					} else if($options['value'] != '') {

						$title1 = '<b>'.ucwords(str_replace('_', ' ', $options['name'])).'</b>';
						if ( isset($options['price']) && $options['price'] !='' ) {
							
							$title1 .= ' (' . wc_price($this->get_product_addition_options_price($options['price'])) . ')';
						
						} else {
							$title1 .= '';
						}
							
						if(isset($value) && $value != '') {

							if(isset($options['check']) && $options['check'] == 'image') {

								$opvalue1 = '<img src="'.EOPA_URL.'uploads/'.$value.'" width="75">';
							} else if(isset($options['check']) && $options['check'] == 'file') {
								$opvalue1 = '<img width="25" src="'.EOPA_URL.'images/attachment_icon.png" width="75">&nbsp;&nbsp;&nbsp;'.$options['filename'];
							} else {
								$opvalue1 = $value;	
							}
						} else {
							$opvalue1 = '';
						}

						$other_data[] = array(
							'name'    => $title1,
							'value'   => $opvalue1,
							'check' => $check,

						);
					}
				}
			}
			
			return $other_data;
		}

		public function is_image($path) {
			$a = getimagesize($path);
			$image_type = $a[2];
			
			if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
			{
				return true;
			}
			return false;
		}

		function order_item_meta($item_id,$values) { 
		
			if ( ! empty( $values['options'] ) ) {
				$i =0;
				foreach ( $values['options'] as $options ) {
					if(isset($options['option_type']) && $options['option_type'] == 'mc') {
						$name = '<b>'.ucwords(str_replace('_', ' ', $options['name'])).'</b>';
					} else {

						if ( isset($options['price']) && $options['price'] !='' ) {
							$name = '<b>'.ucwords(str_replace('_', ' ', $options['name'])).'</b>'.' (' . wc_price($this->get_product_addition_options_price($options['price'])) . ')';
						} else {
							$name = '<b>'.ucwords(str_replace('_', ' ', $options['name'])).'</b>';
						}
					}

					if(isset($options['option_type']) && $options['option_type'] == 'mc') {
						
						if(isset($options['value']) && $options['value']!='') {
							if ( isset($options['option_price']) && $options['option_price'] !='' ) {
								$opval = $options['value'].' (' . wc_price($this->get_product_addition_options_price($options['option_price'])) . ')';
							} else {
								$opval = $options['value'];
							}
						} else {
							$opval = '';
						}
					} else {
						if(isset($options['value']) && $options['value']!='') {
							if(is_array($options['value']))
								$opval = $options['value'];
							else
								$opval = $options['value'];
						} else {
							$opval = '';
						}
						
					}


					if(isset($options['check']) && $options['check']=='image') {

						$img = '<img src="'.EOPA_URL.'uploads/'.$options['value'].'" width="75">';
						$opval = $img;
					}


					if($opval!='') {
						if(isset($options['o_type']) && $options['o_type'] == 'm') {
							if(isset($options['ex_name']) && $options['ex_name']) {
								$name = '<b>'.ucwords(str_replace('_', ' ', $options['ex_name'])).'</b>';

							}
						}
						wc_add_order_item_meta( $item_id, $name, $opval);
						
						global $wpdb;

						$multiple = array('drop_down', 'radio', 'simple_radio', 'checkbox', 'multiple');
						$single = array('field', 'area');
						
						if($options['name'] != '')
							$option_name = str_replace('_', " ", ucwords($options['name']));
						else {
							$option_name = str_replace('_', " ", ucwords($options['ex_name']));
							$opval = $options['value'];
						}
						update_option("test{$i}", $option_name);
						$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE option_title = %s", $option_name), ARRAY_A);

						$opt_id = $result['id'];

						if(array_search($result['option_field_type'], $multiple) !== FALSE) {
							
							if(isset($options['option_type']) && $options['option_type'] == 'mc' && $options['value'] !='' ) {
								
								$result1 = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_rowoption_table." WHERE option_id = %d AND option_row_title = %s", $opt_id, $options['value']),ARRAY_A); 
	
								$stock = ( (int)$result1['stock'] - 1 );
								$wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->eopa_rowoption_table." SET stock = %d  WHERE option_id = %d AND option_row_title = %s", $stock, $opt_id , $options['value']) );
								
							} else if($options['value']!='') {
								
								$result1 = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_rowoption_table." WHERE option_id = %d AND option_row_title = %s", $opt_id, $options['value']),ARRAY_A); 
								
								$stock = ( (int)$result1['stock'] - 1 );
								$wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->eopa_rowoption_table." SET stock = %d  WHERE option_id = %d AND option_row_title = %s", $stock, $opt_id , $options['value']) );
							}
						} else if(array_search($result['option_field_type'], $single) !== FALSE) {
							update_option("testopid{$i}", $opt_id);
							$stock = ( (int)$result['stock'] - 1 );
							$res =$wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->eopa_poptions_table." SET stock = %d  WHERE id = %d", $stock, $opt_id) );
						}
					} 
					$i++;
				}
			}
		}

        function ChangeTextAddToCartButton($button, $product) {

        	$GlobalRules = $this->getGlobalRules();
        	$is_exclude = get_post_meta ( $product->get_id(), '_exclude_global_options', true );

        	$CheckGlobalOptions = array();
			foreach($GlobalRules as $gRule) {

				if($gRule->applied_on == 'products') {
					
					$proudctIDs = explode(', ', $gRule->proids);

					if(in_array($product->get_id(), $proudctIDs)) {
						
						if($is_exclude!='yes') {
							$CheckGlobalOptions[] = $this->getGlobalOptions($gRule->rule_id);
						} else {
							$CheckGlobalOptions[] = 0;
						}

					}
				} else if ($gRule->applied_on == 'categories') {

					$categoryIDs = explode(', ', $gRule->catproids);
				    if(in_array($product->get_id(), $categoryIDs)) {
						if($is_exclude!='yes')
							$CheckGlobalOptions[] = $this->getGlobalOptions($gRule->rule_id);
						else
							$CheckGlobalOptions[] = 0;
					}
				}
			}
        	
			if (!in_array($product->get_type(), array('variable', 'grouped', 'external', 'price_calculator'))) {
		        if (count($CheckGlobalOptions) > 0) {
		            $button = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
						esc_url( get_permalink($product->get_id()) ),
						esc_attr( $product->get_id() ),
						esc_attr( $product->get_sku() ),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						esc_attr( 'variable' ),
						esc_html( __('Select options', 'woocommerce') )
					);
		 
		        }

		        $CheckProductOptions = $this->getProductOptions($product->get_id());
		        if (count($CheckProductOptions) > 0) {
		            $button = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
						esc_url( get_permalink($product->get_id()) ),
						esc_attr( $product->get_id() ),
						esc_attr( $product->get_sku() ),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						esc_attr( 'variable' ),
						esc_html( __('Select options', 'woocommerce') )
					);
		        }
		    }
 
	 		return $button;
	

        }

        function ValidateCustomOptions($eodata, $product_id, $qty) { 
        	$is_exclude = get_post_meta ( $product_id, '_exclude_global_options', true );
//var_dump($_REQUEST);die;
        	if(isset($_POST['product_options']) && $_POST['product_options']!='') {
	        	foreach ($_POST['product_options'] as $key => $option) {

	        		$value = $option['value'];
	        		$phfield = $option['phfield'];
	        		$title = ucwords(str_replace('_', ' ', $key));
	        		$GlobalRules = $this->getGlobalRules();
					$GlobalOptionReq = array();

					foreach($GlobalRules as $gRule) {
						if($gRule->applied_on == 'products') {
							$proudctIDs = explode(', ', $gRule->proids);
							if(in_array($product_id, $proudctIDs)) {
								if($is_exclude!='yes')
									$GlobalOptionReq[] = $this->getGlobalRequired($gRule->rule_id, $title);
								else
									$GlobalOptionReq[] = 0;
							}
						} else if ($gRule->applied_on == 'categories') {
							$categoryIDs = explode(', ', $gRule->catproids);
							if(in_array($product_id, $categoryIDs)) {
								if($is_exclude!='yes')
									$GlobalOptionReq[] = $this->getGlobalRequired($gRule->rule_id, $title);
								else
									$GlobalOptionReq[] = 0;
							}
						}
					}

	        		if($GlobalOptionReq!='') {
	        			foreach($GlobalOptionReq as $GlobalOptionR) {

	        				if($GlobalOptionR->option_field_type == 'file') {
	        					$value = $_FILES['product_options']['name'][$key]['value'];	
	        				}
	        				
			        		if($phfield == 'not required' && $value == '') {

			        		} else if($value == '' && $GlobalOptionR->option_is_required == 'yes') {
			        			
		        				$eodata = false;
								$error_message = sprintf ( __ ( '%s is a required field.', 'woocommerce' ), $title );
								$this->eo_wc_add_notice( $error_message );
			        		}
		        		}
	        		}

	        		$ProductOption = $this->getProductRequired($product_id, $title);

	        		if($ProductOption != '') {
	        			
	        			if($ProductOption->option_field_type == 'file') {
	        				$value = $_FILES['product_options']['name'][$key]['value'];
	        			}
	        			
	        			if($phfield == 'not required' && $value == '') {

			        	} else if($value == '' && $ProductOption->option_is_required == 'yes') {

	        				$eodata = false;
							$error_message = sprintf ( __ ( '%s is a required field.', 'woocommerce' ), $title );
							$this->eo_wc_add_notice( $error_message );
		        		}
	        		}
	        		
	        	}


	        	$CheckProductOptions = $this->getProductOptions($product_id);

	        	foreach($GlobalRules as $gRule) {
					if($gRule->applied_on == 'products') {
						$proudctIDs = explode(', ', $gRule->proids);
						if(in_array($product_id, $proudctIDs)) {
							if($is_exclude!='yes')
								$CheckGlobalOptions[] = $this->getGlobalOptions($gRule->rule_id);
							else 
								$CheckGlobalOptions[] = 0;
						}
					} else if ($gRule->applied_on == 'categories') {
						$categoryIDs = explode(', ', $gRule->catproids);
					    if(in_array($product_id, $categoryIDs)) {
							if($is_exclude!='yes')
								$CheckGlobalOptions[] = $this->getGlobalOptions($gRule->rule_id);
							else
								$CheckGlobalOptions[] = 0;
						}
					}
				}

	        	if($CheckProductOptions!='') {
		        	foreach ($CheckProductOptions as $opdata) {
		        		$title = strtolower(str_replace(' ', '_', $opdata->option_title));
		        		$phfield = $_POST['product_options'][$title]['phfield'];
		        		
		        		$ProductOption = $this->getProductRequired($product_id, $opdata->option_title);

		        		if(!array_key_exists($title, $_POST['product_options']) ) {

			        		if( ($ProductOption->option_is_required == 'yes') && ($ProductOption->option_field_type != 'file')) {
			        			if($_POST['product_options'][$title]['phfield'] == 'not required') {
			        				
			        			} else {

			        				$eodata = false;
									$error_message = sprintf ( __ ( $_POST['product_options'][$title]['phfield'].$title.'%s is a required field.', 'woocommerce' ), $opdata->option_title );
									$this->eo_wc_add_notice( $error_message );
								}
			        		}
		        		}
		        	} 
	        	}

	        	if($CheckGlobalOptions!='') {
		        	foreach ($CheckGlobalOptions as $gpdatas) {

		        		foreach ($gpdatas as $gpdata) {
		        		
			        		$title = strtolower(str_replace(' ', '_', $gpdata->option_title));
			        		$phfield = $_POST['product_options'][$title]['phfield'];
			        		$GlobalOptionreq = $this->getGlobalRequired($product_id, $gpdata->option_title);

			        		if(!array_key_exists($title, $_POST['product_options']) && $phfield != 'not required') {
				        		if($GlobalOptionreq->option_is_required == 'yes' && $GlobalOptionreq->option_field_type != 'file') {

			        				$eodata = false;
									$error_message = sprintf ( __ ( '%s is a required field.', 'woocommerce' ), $gpdata->option_title );
									$this->eo_wc_add_notice( $error_message );
				        		}
			        		}
			        	}
		        	}
	        	}
        	}
        	if(isset($_FILES['product_options']) && $_FILES['product_options']!='') {
	        	foreach ($_FILES['product_options'] as $key => $value) {
	        		foreach ($value as $key1 => $value1) {
	        			$value1 = $value1['value'];
	        			if($key == 'name') {
			        		$title = ucwords(str_replace('_', ' ', $key1));
			        		$phfield = $_POST[$key1]['phfield'];
			        		
			        		$GlobalRules = $this->getGlobalRules();
							$GlobalOptionReq = array();
							foreach($GlobalRules as $gRule) {
								if($gRule->applied_on == 'products') {
									$proudctIDs = explode(', ', $gRule->proids);
									if(in_array($product_id, $proudctIDs)) {
										if($is_exclude!='yes') 
											$GlobalOptionReq[] = $this->getGlobalRequired($gRule->rule_id, $title);
										else
											$GlobalOptionReq[] = 0;
									}
								} else if ($gRule->applied_on == 'categories') {
									$categoryIDs = explode(', ', $gRule->catproids);

								    if(in_array($product_id, $categoryIDs)) {
										if($is_exclude!='yes')
											$GlobalOptionReq[] = $this->getGlobalRequired($gRule->rule_id, $title);
										else
											$GlobalOptionReq[] = 0;
									}
								}
							}
			        		
			        		$ProductOption = $this->getProductRequired($product_id, $title);
			        		
			        		if($GlobalOptionReq!='') {
			        			foreach($GlobalOptionReq as $GlobalOptionR) {
					        		if($GlobalOptionR->option_field_type == 'file') {

					        			if($value1 == '' && $GlobalOptionR->option_is_required == 'yes' && $phfield != 'not required') {

					        				$eodata = false;
											$error_message = sprintf ( __ ( '%s is a required field.', 'woocommerce' ), $title );
											$this->eo_wc_add_notice( $error_message );
					        			}

				        				$f_type = explode(',',$GlobalOptionR->option_allowed_file_extensions);
				        				$allowed[] = '';
				        				$i = 0;
				        				while ($i < sizeof($f_type)) { 
				        					$allowed[] = $f_type[$i];
				        					$i++;
				        				}
				        				$allow =  $allowed;
										$filename = $value1;
										print_r($allowed);
										echo $ext = pathinfo($filename, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed) ) {
				        					$eodata = false;
											$error_message = sprintf ( __ ( 'This type of file extension is not allowed.', 'woocommerce' ), $title );
											$this->eo_wc_add_notice( $error_message );
										}
					        		}
				        		}
			        		}


			        		if($ProductOption!='') {
				        		if($ProductOption->option_field_type == 'file') {

				        			if($value1 == '' && $ProductOption->option_is_required == 'yes' && $phfield != 'not required') {

				        				$eodata = false;
										$error_message = sprintf ( __ ( '%s is a required field.', 'woocommerce' ), $title );
										$this->eo_wc_add_notice( $error_message );
				        			}

			        				$f_type = explode(',',$ProductOption->option_allowed_file_extensions);
			        				$allowed[] = '';
			        				$i = 0;
			        				while ($i < sizeof($f_type)) { 
			        					$allowed[] = $f_type[$i];
			        					$i++;
			        				}
			        				$allow =  $allowed;
									$filename = $value1;
									$ext = pathinfo($filename, PATHINFO_EXTENSION);
									if(!in_array($ext,$allowed) ) {
			        					$eodata = false;
										$error_message = sprintf ( __ ( 'This type of file extension is not allowed.', 'woocommerce' ), $title );
										$this->eo_wc_add_notice( $error_message );
									}
				        		} 
			        		}
		        		}
	        		}
	        	}
        	}
        	return $eodata;
        }


        function get_product_addition_options_price( $price ) {
			
			global $product;

			if ( $price === '' )
				return;

			if ( is_object( $product ) )
				$display_price    = $tax_display_mode == 'incl' ? $product->get_price( 1, $price ) : $product->get_price( 1, $price );
			else
				$display_price = $price;
			
			return $display_price;
		}
	}

	new EO_Product_Addons_Front();
}


?>