<?php 
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( !class_exists( 'EO_Product_Addons_Admin' ) ) { 

	class EO_Product_Addons_Admin extends EO_Product_Addons {


		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'admin_init' ) );

			add_action('admin_menu', array($this, 'register_procustomopt_submenu_page'));

			add_action( 'add_meta_boxes', array($this, 'product_custom_options_box' ));
			add_action('save_post', array($this, 'save_product_meta'), 1, 2);
			add_action('wp_ajax_addoptionTempData', array($this, 'addoptionTemData'));
			add_action('wp_ajax_deloptionTempData', array($this, 'deloptionTempData'));
			add_action('wp_ajax_addrowTempData', array($this, 'addrowTemData'));
			add_action('wp_ajax_addmultirowTempData', array($this, 'addmultirowTemData'));
			add_action('wp_ajax_addradiorowTempData', array($this, 'addradiorowTemData'));

			add_action('wp_ajax_addsimpleradiorowTempData', array($this, 'addsimpleradiorowTemData'));

			add_action('wp_ajax_addcheckboxrowTempData', array($this, 'addcheckboxrowTemData'));
			add_action('wp_ajax_delrowTempData', array($this, 'delrowTempData'));
			
			add_action('wp_ajax_addGlobalOptionTempData', array($this, 'addGlobalOptionTempData'));
			add_action('wp_ajax_addGlobalrowTempData', array($this, 'addGlobalrowTemData'));
			add_action('wp_ajax_addGlobalmultirowTempData', array($this, 'addGlobalmultirowTemData'));
			add_action('wp_ajax_addGlobalradiorowTempData', array($this, 'addGlobalradiorowTemData'));
			add_action('wp_ajax_addGlobalSimpleradiorowTempData', array($this, 'addGlobalSimpleradiorowTemData'));


			add_action('wp_ajax_addGlobalcheckboxrowTempData', array($this, 'addGlobalcheckboxrowTemData'));

			add_action('wp_ajax__ajax_fetch_custom_list', array($this, '_ajax_fetch_custom_list_callback'));
			add_action('wp_ajax_save_global_options', array($this, 'SaveGlobalOptions'));

			add_action( 'woocommerce_before_order_itemmeta', array($this,'display_custom_fields_in_order_page'), 10, 3 );
		}


		public function display_custom_fields_in_order_page( $item_id, $item, $_product ) {

			echo $custom_field = wc_get_order_item_meta( $item_id, '<b>Image</b>', true ); 
		}

		

		public function admin_init() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );	
		}

		public function admin_scripts() {	
           
           	wp_enqueue_script('jquery');
        	wp_enqueue_style( 'eopa-admin-css', plugins_url( '/css/eopa_style.css', __FILE__ ), false );
        	wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'media-upload' ); 
			wp_enqueue_media();
			wp_enqueue_style('admin-css-woo', plugins_url('woocommerce/assets/css/admin.css?ver=2.3.11'));
        	wp_enqueue_script('datatable-js', 'https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js', false );
			wp_enqueue_style( 'datatable-css', 'https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css', false );	

			wp_enqueue_script('parsley-js', plugins_url( '/js/parsley.min.js', __FILE__ ), false );
			wp_enqueue_style('parsley-css', plugins_url( '/css/parsley.css', __FILE__ ), false );

			wp_enqueue_style('eopa-admin-select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', false );
			wp_enqueue_script( 'eopa-admin-select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', false );


        }

        

        function register_procustomopt_submenu_page() {

        	add_menu_page('product-addons', 'Product Addons', apply_filters( 'eopa_capability', 'manage_options' ), 'eo-product-global-custom-options', array( $this, 'eopa_global_meta_data' ) ,plugins_url( 'images/ext_icon.png', dirname( __FILE__ ) ), apply_filters( 'eopa_menu_position', 7 ) );

        	add_submenu_page( 'eo-product-global-custom-options', __( 'All Global Addons Rules', 'eopa' ), __( 'All Global Addons Rules', 'eopa' ), 'manage_options', 'eo-product-global-custom-options', array( $this, 'eopa_global_meta_data' ) );

        	add_submenu_page( 'eo-product-global-custom-options', __( 'Add New Rule', 'eopa' ), __( 'Add New Rule', 'eopa' ), 'manage_options', 'eo-product-global-custom-options-add-rule', array( $this, 'eopa_add_rule' ) );

        	add_submenu_page( 'eo-product-global-custom-options', __( 'Settings', 'eopa' ), __( 'Settings', 'eopa' ), 'manage_options', 'eo-product-global-custom-options-plugin-settings', array( $this, 'plugin_settings' ) );

        	add_submenu_page( 'eo-product-global-custom-options', __( 'Support', 'eopa' ), __( 'Support', 'eopa' ), 'manage_options', 'eo-product-addons-support', array( $this, 'eopa_support' ) );

		    
		}

		public function eopa_support() {
			require  EOPA_PLUGIN_DIR . 'admin/view/support.php';
		}


		public function plugin_settings() { 
			require_once(EOPA_PLUGIN_DIR.'admin/view/includes/settings-form.php');
			// require_once(EOPA_PLUGIN_DIR.'admin/view/includes/import-export-form.php');
		}


		function eopa_global_meta_data() {
			require  EOPA_PLUGIN_DIR . 'admin/view/all_global_rules.php';
			
		}


		function eopa_add_rule() {
			
			require  EOPA_PLUGIN_DIR . 'admin/view/global_form.php';
		}



		function eo_procustomopt_callback() {
		    
		    global $wpdb;
			$wpdb->query("TRUNCATE TABLE ".$wpdb->eopa_temp_table);

			require  EOPA_PLUGIN_DIR . 'admin/view/global_form.php';

		}

		function product_custom_options_box() {
    		add_meta_box( 'product_custom_optios', 'Product Custom Options', array($this, 'product_custom_options_call'), 'product', 'normal', 'high' );

		}

		function save_product_meta($post_id, $post) { 

			global $wpdb;

			// echo '<pre>';
			// print_r($_POST);
			// exit;
			$meta_value = get_post_meta( $post_id, '_exclude_global_options', true );

		  	if ( isset($_POST['exclude_global_options']) && $_POST['exclude_global_options']!='' && $meta_value == '' ) {
		    	add_post_meta( $post_id, '_exclude_global_options', $_POST['exclude_global_options'], true );
		  	} else {
		    	delete_post_meta( $post_id, '_exclude_global_options' );
		  	}

			if(isset($_POST['product_option']) && count($_POST['product_option'])!=0) {
			foreach ($_POST['product_option'] as $option_id => $product_option) {

				if(isset($product_option['option_id']) && $product_option['option_id']!='')
					$product_option_option_id = $product_option['option_id'];
				else
					$product_option_option_id = '';

				if(isset($_POST['editpro']) && $_POST['editpro'] == 'yes')
					$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->eopa_rowoption_table." WHERE option_id = %d", $product_option_option_id ) );

				$res = $wpdb->get_row("SELECT * FROM $wpdb->eopa_poptions_table WHERE id = '" . $option_id . "'", 'ARRAY_A');

			
				if(count($product_option) == 1)
					$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->eopa_poptions_table." WHERE id = %d", $option_id  ) );

				$manage_stock 	= isset($product_option['manage_stock']) ? $product_option['manage_stock'] : 'no';


				if(isset($product_option['option_type']) && $product_option['option_type'] == 'field') {

					$price_per_char = isset($product_option['field_price_per_char']) ? $product_option['field_price_per_char'] : '';
					$multiply_price_by_qty = isset($product_option['field_multiply_price_by_qty']) ? $product_option['field_multiply_price_by_qty'] : '';

					if($res && $product_option['option_title'] != '') {
						
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_maxchars=%s,enable_price_per_char=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock = %s, stock = %s, multiply_price_by_qty=%s WHERE id=%d",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['text_option_price'],
			            $product_option['text_option_price_type'],
			            $product_option['text_option_maxchars'],
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['text_stock'],
			            $multiply_price_by_qty,
			            $option_id
			            ) );
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_maxchars,enable_price_per_char,showif,cfield,ccondition,ccondition_value,manage_stock,stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['text_option_price'],
			            $product_option['text_option_price_type'],
			            $product_option['text_option_maxchars'],
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['text_stock'],
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'area') {

					$multiply_price_by_qty = isset($product_option['textarea_multiply_price_by_qty']) ? $product_option['textarea_multiply_price_by_qty'] : '';
					$price_per_char = isset($product_option['textarea_price_per_char']) ? $product_option['textarea_price_per_char'] : '';
					if($res && $product_option['option_title'] != '' ) {
						
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_maxchars=%s,enable_price_per_char=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock = %s, stock=%s, multiply_price_by_qty=%s WHERE ID=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['area_option_price'],
			            $product_option['area_option_price_type'],
			            $product_option['area_option_maxchars'],
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['area_stock'],
			            $multiply_price_by_qty,
			            $option_id
			            ) );
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_maxchars,enable_price_per_char,showif,cfield,ccondition,ccondition_value,manage_stock, stock, multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['area_option_price'],
			            $product_option['area_option_price_type'],
			            $product_option['area_option_maxchars'],
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['area_stock'],
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'file') {
					$multiply_price_by_qty = isset($product_option['file_multiply_price_by_qty']) ? $product_option['file_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_allowed_file_extensions=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['file_option_price'],
			            $product_option['file_option_price_type'],
			            $product_option['option_allowed_file_extensions'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_allowed_file_extensions,showif,cfield,ccondition,ccondition_value,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['file_option_price'],
			            $product_option['file_option_price_type'],
			            $product_option['option_allowed_file_extensions'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $multiply_price_by_qty
			            ) );
					}
				
				} else if(isset($product_option['option_type']) && 	$product_option['option_type'] == 'drop_down') {
					$multiply_price_by_qty = isset($product_option['dropdown_multiply_price_by_qty']) ? $product_option['dropdown_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock = %s, multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $multiply_price_by_qty,
			            $manage_stock,
			            $option_id
			            ));

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['drop_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,stock)
					            VALUES (%s,%s,%s,%s,%s,%s)
					            ",
					            $option_id,
					            $row_value['drop_option_row_title'],
					            $row_value['drop_option_row_price'],
					            $row_value['drop_option_row_price_type'],
					            $row_value['drop_option_row_sort_order'],
					            $row_value['stock']
					            ));
							}
						}
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,showif,cfield,ccondition,ccondition_value,manage_stock, multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $multiply_price_by_qty,
			            $manage_stock
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['drop_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,stock)
					            VALUES (%s,%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            $row_value['drop_option_row_title'],
					            $row_value['drop_option_row_price'],
					            $row_value['drop_option_row_price_type'],
					            $row_value['drop_option_row_sort_order'],
					            $row_value['stock']
					            ));
							}
						}
					}
					

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'multiple') {
					$multiply_price_by_qty = isset($product_option['ms_multiply_price_by_qty']) ? $product_option['ms_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty,
			            $option_id
			            ));

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['multi_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,stock)
					            VALUES (%s,%s,%s,%s,%s,%s)
					            ",
					            $option_id,
					            $row_value['multi_option_row_title'],
					            $row_value['multi_option_row_price'],
					            $row_value['multi_option_row_price_type'],
					            $row_value['multi_option_row_sort_order'],
					            $row_value['stock']
					            ));
							}
						}
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,showif,cfield,ccondition,ccondition_value,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['multi_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,stock)
					            VALUES (%s,%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            $row_value['multi_option_row_title'],
					            $row_value['multi_option_row_price'],
					            $row_value['multi_option_row_price_type'],
					            $row_value['multi_option_row_sort_order'],
					            $row_value['stock']
					            ));
							}
						}
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'radio') {

					$multiply_price_by_qty = isset($product_option['radio_multiply_price_by_qty']) ? $product_option['radio_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
				            $post->ID,
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $multiply_price_by_qty,
				            $option_id
			            ));

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,stock)
					            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
					            ",
					            $option_id,
					            $row_value['radio_option_row_title'],
					            $row_value['radio_option_row_price'],
					            $row_value['radio_option_row_price_type'],
					            $row_value['radio_option_row_sort_order'],
					            $row_value['radio_option_row_image_url'],
					            $row_value['radio_option_row_proimage_url'],
					            $row_value['value']
					            ));
							}
						}
					} else if($product_option['option_title'] != '') {
						
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,value)
					            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            $row_value['radio_option_row_title'],
					            $row_value['radio_option_row_price'],
					            $row_value['radio_option_row_price_type'],
					            $row_value['radio_option_row_sort_order'],
					            $row_value['radio_option_row_image_url'],
					            $row_value['radio_option_row_proimage_url'],
					            $row_value['value']
					            ));
							}
						}
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'simple_radio') {

					$multiply_price_by_qty = isset($product_option['sr_multiply_price_by_qty']) ? $product_option['sr_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%d",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty,
			            $option_id
			            ));

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {

								
								
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,stock)
					            VALUES (%s,%s,%s,%s,%s,%s)
					            ",
					            $option_id,
					            $row_value['radio_option_row_title'],
					            $row_value['radio_option_row_price'],
					            $row_value['radio_option_row_price_type'],
					            $row_value['radio_option_row_sort_order'],
					            $row_value['stock']
					            ));
							}
						}
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {

								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,stock)
					            VALUES (%s,%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            $row_value['radio_option_row_title'],
					            $row_value['radio_option_row_price'],
					            $row_value['radio_option_row_price_type'],
					            $row_value['radio_option_row_sort_order'],
					            $row_value['stock']
					            ));
							}
						}
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'checkbox') {
					$multiply_price_by_qty = isset($product_option['cb_multiply_price_by_qty']) ? $product_option['cb_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
				            $post->ID,
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $multiply_price_by_qty,
				            $option_id
			            ));

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['check_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,stock)
					            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
					            ",
					            $option_id,
					            $row_value['check_option_row_title'],
					            $row_value['check_option_row_price'],
					            $row_value['check_option_row_price_type'],
					            $row_value['check_option_row_sort_order'],
					            $row_value['check_option_row_image_url'],
					            $row_value['check_option_row_proimage_url'],
					            $row_value['stock']
					            ));
							}
						}
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['check_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,stock)
					            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            $row_value['check_option_row_title'],
					            $row_value['check_option_row_price'],
					            $row_value['check_option_row_price_type'],
					            $row_value['check_option_row_sort_order'],
					            $row_value['check_option_row_image_url'],
					            $row_value['check_option_row_proimage_url'],
					            $row_value['stock']
					            ));
							}
						}
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'date') {
					$multiply_price_by_qty = isset($product_option['date_multiply_price_by_qty']) ? $product_option['date_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['date_option_price'],
			            $product_option['date_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,showif,cfield,ccondition,ccondition_value,manage_stock=%s,multiply_price_by_qty=%s) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['date_option_price'],
			            $product_option['date_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'time') {
					$multiply_price_by_qty = isset($product_option['time_multiply_price_by_qty']) ? $product_option['time_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['time_option_price'],
			            $product_option['time_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['time_option_price'],
			            $product_option['time_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'color') {
					$multiply_price_by_qty = isset($product_option['color_multiply_price_by_qty']) ? $product_option['color_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['color_option_price'],
			            $product_option['color_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['color_option_price'],
			            $product_option['color_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'google_font') {
					$multiply_price_by_qty = isset($product_option['gf_multiply_price_by_qty']) ? $product_option['gf_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_font_option_price'],
			            $product_option['google_font_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_font_option_price'],
			            $product_option['google_font_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'google_map') {
					$multiply_price_by_qty = isset($product_option['gm_multiply_price_by_qty']) ? $product_option['gm_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_map_option_price'],
			            $product_option['google_map_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_map_option_price'],
			            $product_option['google_map_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'range_picker') {
					$multiply_price_by_qty = isset($product_option['rp_multiply_price_by_qty']) ? $product_option['rp_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,enable_price_per_char=%s, manage_stock=%s,min_value=%s,max_value=%s,multiply_price_by_qty=%s WHERE id=%s",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['range_picker_option_price'],
			            $product_option['range_picker_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $product_option['rp_price_per_char'],
			            'no',
			            $product_option['range_picker_min_value'],
			            $product_option['range_picker_max_value'],
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,showif,cfield,ccondition,ccondition_value,enable_price_per_char,manage_stock,min_value,max_value,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            $post->ID,
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['range_picker_option_price'],
			            $product_option['range_picker_option_price_type'],
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $product_option['rp_price_per_char'],
			            'no',
			            $product_option['range_picker_min_value'],
			            $product_option['range_picker_max_value'],
			            $multiply_price_by_qty
			            ) );
					}
				}
			} 
		}
			// exit;
		}

		function product_custom_options_call( $post ) { 

			global $wpdb;
			$wpdb->query("TRUNCATE TABLE ".$wpdb->eopa_temp_table );

			require  EOPA_PLUGIN_DIR . 'admin/view/product_level_form.php';
		}

		function getTempFields($id) { 
			global $wpdb;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_temp_table." WHERE field_type = %s AND  id = %d", 'option', $id), ARRAY_A);      
            return $result;
		}

		function getrowTempFields($id,$field_id) {
			global $wpdb;

            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_temp_table." WHERE field_type = %s AND field_id = %d AND id = %d", 'row', $field_id, $id));      
            return $result;
		}

		function getProductOptions($post) { 

			global $wpdb;
			
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE product_id = %d", $post));      
            return $result;
		}

		function getProductOptionRows($option_id) { 

			global $wpdb;
			
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_rowoption_table." WHERE option_id = %d", $option_id));      
            return $result;
		}


		// Add option to product form
		function addForm($id) { ?>

			<?php 

		   		$tempField = $this->getTempFields($id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

			<div class="addFormFields" id="field<?php echo $tempField['id']; ?>">
				<input onClick="delFields('<?php echo $tempField['id']; ?>')" type="button" class="btnDel button btn-danger button-large" value="<?php echo _e('Delete Option','eopa'); ?>">
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
								<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][option_title]" id="title" /></td>
								<td class="datath2">
						    		<select class="select_type inputs" name="product_option[<?php echo $tempField['id']; ?>][option_type]" id="type" onChange="showFields('<?php echo $tempField['id']; ?>',this.value)">
						    			<option value=""><?php echo _e('-- Please select --','eopa'); ?></option>
						    			<optgroup label="Text">
						    				<option value="field"><?php echo _e('Field','eopa'); ?></option>
						    				<option value="area"><?php echo _e('Area','eopa'); ?></option>
						    			</optgroup>
						    			<optgroup label="File">
						    				<option value="file"><?php echo _e('File','eopa')?></option>
						    			</optgroup>
						    			<optgroup label="Select">
						    				<option value="drop_down"><?php echo _e('Drop-down','eopa'); ?></option>
			            					<option value="radio"><?php echo _e('Radio Buttons','eopa'); ?></option>
			            					<option value="simple_radio"><?php echo _e('Simple Radio Buttons','eopa'); ?></option>
			            					<option value="checkbox"><?php echo _e('Checkbox','eopa'); ?></option>
			            					<!-- <option value="simple_checkbox"><?php //echo _e('Simple Checkbox','eopa'); ?></option> -->
			            					<option value="multiple"><?php echo _e('Multiple Select','eopa'); ?></option>
			            				</optgroup>
					            		<optgroup label="Date">
					            			<option value="date"><?php echo _e('Date','eopa'); ?></option>
					            			<option value="time"><?php echo _e('Time','eopa')?></option>
					            		</optgroup>

					            		<optgroup label="Color">
					            			<option value="color"><?php echo _e('Color Picker','eopa'); ?></option>
					            		</optgroup>
					            		<optgroup label="Google Fonts">
					            			<option value="google_font"><?php echo _e('Google Font','eopa'); ?></option>
					            		</optgroup>
					            		<optgroup label="Google Map">
				            			<option value="google_map"><?php echo _e('Google Map','eopa'); ?></option>
				            		</optgroup>
				            		<optgroup label="Range Picker Field">
				            			<option value="range_picker"><?php echo _e('Range Picker','eopa'); ?></option>
				            		</optgroup>

			            			</select>
						        </td>
								<td class="datath3">
									<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][option_is_required]" id="is_required">
				                		<option value="yes"><?php echo _e('Yes','eopa'); ?></option>
				                		<option value="no"><?php echo _e('No','eopa'); ?></option>
				                	</select>
								</td>
								<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][option_sort_order]" id="sort_order<?php echo $tempField['id']; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
							</tr>
							
						</tbody>
					</table>
				</div>

				<div class="stock_wrapper">
					<label>Manage Stock</label>
					<select name="product_option[<?php echo $tempField['id']; ?>][manage_stock]">
						<option value="no" selected="selected">No</option>
						<option value="yes">Yes</option>
					</select>
				</div>

				<div class="widd">
		  			<label for="clogic"><b><?php _e('Conditional Logic:','eopa'); ?></b></label>
		  			<div class="widd_wrapper">
			  			<div class="showf">
			  				<select name="product_option[<?php echo $tempField['id']; ?>][showif]">
			  					<option value=""><?php _e('Select','eopa'); ?></option>
			  					<option value="Show"><?php _e('Show','eopa'); ?></option>
			  					<option value="Hide"><?php _e('Hide','eopa'); ?></option>
			  				</select>
			  			</div>
			  			<div class="showf_text"><?php _e('if value of','eopa'); ?></div>
				  			<div class="showf clshowf" id="cl">
				  				<?php 
				  				global $wpdb;

					            $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE id!=%d AND global_rule_id IS NULL", $tempField['id']));      
					            ?>
					            <select name="product_option[<?php echo $tempField['id']; ?>][cfield]" class="cfields">
					            <option value=""><?php _e('Select','eopa'); ?></option>
					            <?php 
					            foreach($results as $res) { ?>
									<option value="<?php echo $res->id; ?>" ><?php echo $res->option_title; ?></option>
					            <?php } ?>
					            </select>
				  			</div>
			  			<div class="showf" id="cll">
			  				<select id="cll_select" name="product_option[<?php echo $tempField['id']; ?>][ccondition]" class="cfields">
			  					<option value=""><?php _e('Select','eopa'); ?></option>
			  					<option value="is not empty"><?php _e('is not empty','eopa'); ?></option>
			  					<option value="is equal to"><?php _e('is equal to','eopa'); ?></option>
			  					<option value="is not equal to"><?php _e('is not equal to','eopa'); ?></option>
			  					<option value="is checked"><?php _e('is checked','eopa'); ?></option>
			  					
			  				</select>
			  			</div>

			  			<div class="showf" id="clll">
			  				<input type="text" name="product_option[<?php echo $tempField['id']; ?>][ccondition_value]" class="clll_field" size="13">
			  			</div>
			  		</div>
		  		</div>

				<div class="bottom_fields">
					<div id="textField<?php echo $tempField['id']; ?>" style="display:none;">
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
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][text_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][text_option_price_type]" id="fieldprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][text_option_maxchars]" id="maxchars<?php echo $tempField['id']; ?>" onChange="MaxCharsOnlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="text" name="product_option[<?php echo $tempField['id'];?>][text_stock]" value="0" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td>
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][field_price_per_char]" value="1" />
										<?php _e('Enable Price per character', 'eopa'); ?>
									</td>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][field_multiply_price_by_qty]" value="1" checked="checked"/>
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</table>
					</div>
					<div id="textArea<?php echo $tempField['id']; ?>" style="display:none;">
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
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][area_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][area_option_price_type]" id="areaprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][area_option_maxchars]" id="maxchars<?php echo $tempField['id']; ?>" onChange="SMaxCharsonlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="number" name="product_option[<?php echo $tempField['id'];?>][area_stock]" value="0" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td>
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][textarea_price_per_char]" value="yes" />
										<?php _e('Enable Price per character', 'eopa'); ?>
									</td>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][textarea_multiply_price_by_qty]"  value="1" checked="checked" />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>

					<div id="simple_chexbox<?php echo $tempField['id']; ?>" style="display:none;">
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
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][area_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][area_option_price_type]" id="areaprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][area_option_maxchars]" id="maxchars<?php echo $tempField['id']; ?>" onChange="SMaxCharsonlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="text" name="" value="" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][scb_multiply_price_by_qty]" value="1" checked="checked" />
									<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>

					<div id="file<?php echo $tempField['id']; ?>" style="display:none;">
						<table class="datatable">
							<thead>
							    <tr>
							    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="datath1"><label><b><?php echo _e('Allowed Extensions <span>(add with comma(,) separated)</span>:','eopa'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][file_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][file_option_price_type]" id="areaprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][option_allowed_file_extensions]" id="fileallowedex" /></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][file_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div id="dropdown<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="datatable" id="POITable<?php echo $tempField['id']; ?>">
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
							<tbody class="<?php echo $tempField['id']; ?>droprowdata">

								
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--droprowdata" colspan="4"></td>-->
<!--								</tr>-->
							</tbody>
							<tfoot>
								<tr>
							   		<td>
							   			<input onClick="addNewDropRow(this, <?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>">
							   		</td> 
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id'];?>][dropdown_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<div id="multiselect<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="datatable" id="MultiTable<?php echo $tempField['id']; ?>">
							<thead>
							    <tr>
							    	<th class="width38"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
							    	<th class="width23"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="width19"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="width10"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
							    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
							    	<th></th>
							    </tr>
							</thead>
							<tbody class="<?php echo $tempField['id']; ?>multirowdata">
								
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--multirowdata" colspan="9"></td>-->
<!--								</tr>-->
							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addNewMultiRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][ms_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<div id="radio<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="datatable" id="RadioTable<?php echo $tempField['id']; ?>">
							<thead>
							    <tr>
							    	<th class="width31"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
							    	<th class="width9"><label><b>Radio Image</b></label></th>
							    	<th class="width9"><label><b>Product Image</b></label></th>
							    	<th class="width18"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="width13"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="width10"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
							    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
							    	<th></th>
							    </tr>
							</thead>
							<tbody class="<?php echo $tempField['id']; ?>radiorowdata">
								
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--radiorowdata" colspan="9"></td>-->
<!--								</tr>-->

							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addNewRadioRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][radio_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>

					<div id="simple_radio<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="datatable" id="RadioTable<?php echo $tempField['id']; ?>">
							<thead>
							    <tr>
							    	<th class="width38"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
							    	
							    	<th class="width23"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="width18"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="datath4"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
							    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
							    	<th></th>
							    </tr>
							</thead>
							<tbody class="<?php echo $tempField['id']; ?>simpleradiorowdata">
								
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--simpleradiorowdata" colspan="6"></td>-->
<!--								</tr>-->

							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addNewSimpleRadioRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][sr_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>

					<div id="checkbox<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="datatable" id="CheckboxTable<?php echo $tempField['id']; ?>">
							<thead>
							    <tr>
							    	<th style="display:none"></th>
							    	<th class="width31"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
							    	<th class="width3"><label><b>Checkbox Image</b></label></th>
							    	<th class="width9"><label><b>Product Image</b></label></th>
							    	<th class="width19"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="width15"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="width10"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
							    	<th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
							    	<th></th>
							    </tr>
							</thead>
							<tbody class="<?php echo $tempField['id']; ?>checkboxrowdata">
								
							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addNewCheckboxRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td> 
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][cb_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
<!--							<tbody>-->
<!---->
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--checkboxrowdata" colspan="9"></td>-->
<!--								</tr>-->
<!--								-->
<!--							</tbody>-->
						</table>
					</div>
					
					<div id="date<?php echo $tempField['id']; ?>" style="display:none;">
						<table class="datatable">
							<thead>
							    <tr>
							    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][date_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][date_option_price_type]" id="dateprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][date_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
						</table>
					</div>
					<div id="time<?php echo $tempField['id']; ?>" style="display:none;">
						<table class="datatable">
							<thead>
							    <tr>
							    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][time_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][time_option_price_type]" id="timeprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][time_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
						</table>
					</div>


					<div id="color<?php echo $tempField['id']; ?>" style="display:none;">
						<table class="datatable">
							<thead>
							    <tr>
							    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][color_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][color_option_price_type]" id="colorprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][color_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
						</table>
					</div>
					<div id="google_font<?php echo $tempField['id'];?>" style="display:none;">
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
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][google_font_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" />
									</td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][google_font_option_price_type]" id="google_font_price_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][gf_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
						</table>
					</div>
					<div id="google_map<?php echo $tempField['id'];?>" style="display:none;">
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
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][google_map_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" />
									</td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][google_map_option_price_type]" id="google_map_price_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr> 
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][gm_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
						</table>
					</div>
					<div id="range_picker<?php echo $tempField['id'];?>" style="display:none;">
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
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][range_picker_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" />
									</td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][range_picker_option_price_type]" id="range_picker_price_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3">
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][range_picker_min_value]" id="min_value<?php echo $tempField['id']; ?>" />
									</td>
									<td class="datath4">
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][range_picker_max_value]" id="price<?php echo $tempField['id']; ?>" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td>
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][rp_price_per_char]"  value="yes" />
										<?php _e('Enable Price per unit', 'eopa'); ?>
									</td>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][rp_multiply_price_by_qty]"  value="1" checked="checked" />
											<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

			</div>
			<?php }  ?>

		<?php }

		function addForm2($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_title]" id="dropdowntitle" /></td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
					<td class="datath5">
						<input type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" />
					</td>
					<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		   	<?php } }


		function addMultiForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_title]" id="dropdowntitle" /></td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
					<td class="datath5">
						<input type="text" class="width90px" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" />
					</td>
					<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		   	<?php } }


		 function addRadioForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>">
					<td class="datath1">
                        <input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_title]" id="dropdowntitle" />
                    </td>
					<td>
						<div class="imgdis" id="radioimgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_image_url]" id="radioimage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="radioimm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>

					<td>
						<div class="imgdis" id="radioproimgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_proimage_url]" id="radioproimage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="radioproimm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
					<td class="datath5">
						<input type="text" class="width90px" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" />
					</td>
					<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		   	<?php } }



		function addSimpleRadioForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_title]" id="dropdowntitle" /></td>
					
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
					<td class="datath5">
						<input type="text" class="width90px" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" />
					</td>

					<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')"><?php _e('Remove', 'eopa'); ?></a></td>
				</tr>

		   	<?php } }


		function addcheckboxForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_title]" id="dropdowntitle" /></td>
					<td>
						<div class="imgdis" id="checkboximgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_image_url]" id="checkboximage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="checkboximm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>

					<td>
						<div class="imgdis" id="checkboxproimgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_proimage_url]" id="checkboxproimage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="checkboxproimm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
					<td class="datath5">
						<input type="text" class="width90px" name="product_option[<?php echo $tempField->tempField; ?>][row_value][<?php echo $tempField->id; ?>][stock]" />
					</td>
					<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		   	<?php } }


		function addoptionTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_type,field)
	            VALUES (%s,%s)
	            ",
	            'option',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addForm($lastid);
			die();
			return true;

			
			

		}

		function addrowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addForm2($lastid,$_POST['field_id']);
			die();
			return true;

		}


		function addmultirowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addMultiForm($lastid,$_POST['field_id']);
			die();
			return true;

		}

		function addradiorowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addRadioForm($lastid,$_POST['field_id']);
			die();
			return true;

		}

		function addsimpleradiorowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addSimpleRadioForm($lastid,$_POST['field_id']);
			die();
			return true;

		}


		function addcheckboxrowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addcheckboxForm($lastid,$_POST['field_id']);
			die();
			return true;

		}

		function deloptionTempData() {

			$field_id = $_POST['field_id'];
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->eopa_temp_table . " WHERE id = %d", $field_id ) );
			die();
			return true;

		}

		function delrowTempData() {

			$field_id = $_POST['field_id'];
			$id = $_POST['id'];
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->eopa_temp_table . " WHERE id = %d AND field_id = %d", $id, $field_id ) );
			die();
			return true;

		}



		//Global Options Function Start
		function addGlobalOptionTempData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_type,field)
	            VALUES (%s,%s)
	            ",
	            'option',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addGlobalForm($lastid);
			die();
			return true;
		}

		function addGlobalrowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addGlobalForm2($lastid,$_POST['field_id']);
			die();
			return true;
		}

		function addGlobalmultirowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addGlobalMultiForm($lastid,$_POST['field_id']);
			die();
			return true;
		}

		function addGlobalradiorowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addGlobalRadioForm($lastid,$_POST['field_id']);
			die();
			return true;
		}

		function addGlobalSimpleradiorowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addGlobalSimpleRadioForm($lastid,$_POST['field_id']);
			die();
			return true;
		}

		function addGlobalcheckboxrowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->eopa_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            $_POST['field_id'],
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addGlobalcheckboxForm($lastid,$_POST['field_id']);
			die();
			return true;
		}


		function addGlobalForm($id) { ?>

			<?php 

		   		$tempField = $this->getTempFields($id);
		   		if(!empty($tempField)) {
		   	?>

			<div class="addFormFields" id="field<?php echo $tempField['id']; ?>">
				<input onClick="delGlobalFields('<?php echo $tempField['id']; ?>')" type="button" class="btnDel button btn-danger button-large" value="<?php echo _e('Delete Option','eopa'); ?>">
				<div class="topFields">

					

					<table class="globaldatatable">
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
								<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][option_title]" id="title" /></td>
								<td class="datath2">
						    		<select class="select_type inputs" name="product_option[<?php echo $tempField['id']; ?>][option_type]" id="type" onChange="showGlobalFields('<?php echo $tempField['id']; ?>',this.value)">
						    			<option value=""><?php echo _e('-- Please select --','eopa'); ?></option>
						    			<optgroup label="Text">
						    				<option value="field"><?php echo _e('Field','eopa'); ?></option>
						    				<option value="area"><?php echo _e('Area','eopa'); ?></option>
						    			</optgroup>
						    			<optgroup label="File">
						    				<option value="file"><?php echo _e('File','eopa')?></option>
						    			</optgroup>
						    			<optgroup label="Select">
						    				<option value="drop_down"><?php echo _e('Drop-down','eopa'); ?></option>
			            					<option value="radio"><?php echo _e('Radio Buttons','eopa'); ?></option>
			            					<option value="simple_radio"><?php echo _e('Simple Radio Buttons','eopa'); ?></option>
			            					<option value="checkbox"><?php echo _e('Checkbox','eopa'); ?></option>
			            					<!-- <option value="simple_checkbox"><?php //echo _e('Simple Checkbox','eopa'); ?></option> -->
			            					<option value="multiple"><?php echo _e('Multiple Select','eopa'); ?></option>
			            				</optgroup>
					            		<optgroup label="Date">
					            			<option value="date"><?php echo _e('Date','eopa'); ?></option>
					            			<option value="time"><?php echo _e('Time','eopa')?></option>
					            		</optgroup>
					            		<optgroup label="Color">
					            			<option value="color"><?php echo _e('Color Picker','eopa'); ?></option>
					            		</optgroup>
					            		<optgroup label="Google Fonts">
					            			<option value="google_font"><?php echo _e('Google Font','eopa'); ?></option>
					            		</optgroup>
					            		<optgroup label="Google Map">
				            				<option value="google_map"><?php echo _e('Google Map','eopa'); ?></option>
					            		</optgroup>
					            		<optgroup label="Range Picker Field">
					            			<option value="range_picker"><?php echo _e('Range Picker','eopa'); ?></option>
					            		</optgroup>
			            			</select>
						        </td>
								<td class="datath3">
									<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][option_is_required]" id="is_required">
				                		<option value="yes"><?php echo _e('Yes','eopa'); ?></option>
				                		<option value="no"><?php echo _e('No','eopa'); ?></option>
				                	</select>
								</td>
								<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][option_sort_order]" id="sort_order<?php echo $tempField['id']; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
							</tr>
							
						</tbody>
					</table>
				</div>

				<div class="stock_wrapper">
					<label>Manage Stock</label>
					<select name="product_option[<?php echo $tempField['id']; ?>][manage_stock]">
						<option value="no" selected="selected">No</option>
						<option value="yes">Yes</option>
					</select>
				</div>
				<div class="widd">
		  			<label for="clogic"><b><?php _e('Conditional Logic:','eopa'); ?></b></label>
		  			<div class="widd_wrapper">
			  			<div class="showf">
			  				<select name="product_option[<?php echo $tempField['id']; ?>][showif]">
			  					<option value=""><?php _e('Select','eopa'); ?></option>
			  					<option value="Show"><?php _e('Show','eopa'); ?></option>
			  					<option value="Hide"><?php _e('Hide','eopa'); ?></option>
			  				</select>
			  			</div>
			  			<div class="showf_text"><?php _e('if value of','eopa'); ?></div>
				  			<div class="showf clshowf" id="cl">
				  				<?php 
				  				global $wpdb;

					            $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE id!=%d AND global_rule_id!=''", $tempField['id']));      
					            ?>
					            <select name="product_option[<?php echo $tempField['id']; ?>][cfield]" class="cfields">
					            <option value=""><?php _e('Select','eopa'); ?></option>
					            <?php 
					            foreach($results as $res) { ?>
									<option value="<?php echo $res->id; ?>" ><?php echo $res->option_title; ?></option>
					            <?php } ?>
					            </select>
				  			</div>
			  			<div class="showf" id="cll">
			  				<select id="cll_select" name="product_option[<?php echo $tempField['id']; ?>][ccondition]" class="cfields">
			  					<option value=""><?php _e('Select','eopa'); ?></option>
			  					<option value="is not empty"><?php _e('is not empty','eopa'); ?></option>
			  					<option value="is equal to"><?php _e('is equal to','eopa'); ?></option>
			  					<option value="is not equal to"><?php _e('is not equal to','eopa'); ?></option>
			  					<option value="is checked"><?php _e('is checked','eopa'); ?></option>
			  					
			  				</select>
			  			</div>

			  			<div class="showf" id="clll">
			  				<input type="text" name="product_option[<?php echo $tempField['id']; ?>][ccondition_value]" class="clll_field" size="13">
			  			</div>
			  		</div>
		  		</div>


				<div class="bottom_fields">
					<div id="textField<?php echo $tempField['id']; ?>" style="display:none;">
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
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][text_option_price]" id="price<?php echo $tempField['id'] ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][text_option_price_type]" id="fieldprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][text_option_maxchars]" id="sort_order<?php echo $tempField['id']; ?>" onChange="MaxCharsonlyNumber(this.id);" /></td>
									<td class="datath5">
										<input type="text" name="product_option[<?php echo $tempField['id'];?>][text_stock]" value="0" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td>
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][field_price_per_char]"  value="yes" />
										<?php _e('Enable Price per character', 'eopa'); ?>
									</td>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][field_multiply_price_by_qty]" value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
						</table>
					</div>
					<div id="textArea<?php echo $tempField['id']; ?>" style="display:none;">
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
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][area_option_price]" id="price<?php echo $tempField['id'];?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][area_option_price_type]" id="areaprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3">
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][area_option_maxchars]" id="sort_order<?php echo $tempField['id']; ?>" onChange="MaxCharsonlyNumber(this.id);" />
									</td>
									<td class="datath5">
										<input type="text" name="product_option[<?php echo $tempField['id'];?>][area_stock]" value="0" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td>
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][textarea_price_per_char]"  value="yes" />
										<?php _e('Enable Price per character', 'eopa'); ?>
									</td>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][textarea_multiply_price_by_qty]" value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>

					<!-- <div id="simple_checkbox<?php //echo $tempField->id; ?>" style="display:none;">
						<table class="globaldatatable">
							<thead>
							    <tr>
							    	<th class="datath1" style="width:20%"><label><b><?php //echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php //echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="datath3"><label><b><?php //echo _e('Checkbox Value:','eopa'); ?></b></label></th>
							    	<th class="datath4" style="width:35%"><label><b><?php //echo _e('Checkbox Text/Title:','eopa'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath1" style="width:20%"><input class="inputs" type="text" name="product_option[<?php //echo $tempField->id; ?>][simple_checkbox_option_price]" id="price<?php //echo $tempField->id ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php //echo $tempField->id; ?>][simple_checkbox_option_price_type]" id="simple_checkboxprice_type">
					                		<option value="fixed"><?php //echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php //echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php //echo $tempField->id; ?>][simple_checkbox_option_maxchars]" id="sort_order<?php //echo $tempField->id; ?>" /></td>
									<td class="datath4" style="width:35%"><input class="inputs" type="text" name="product_option[<?php //echo $tempField->id; ?>][simple_checkbox_allowed_file_extensions]" id="simple_checkboxallowed" /></td>
								</tr>
							</tbody>
						</table>
					</div> -->

					<div id="file<?php echo $tempField['id']; ?>" style="display:none;">
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
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][file_option_price]" id="price<?php echo $tempField['id'] ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][file_option_price_type]" id="areaprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][option_allowed_file_extensions]" id="fileallowedex" /></td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][file_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div id="dropdown<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="globaldatatable" id="POITable<?php echo $tempField['id']; ?>">
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
							<tbody class="<?php echo $tempField['id']; ?>globaldroprowdata">
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--globaldroprowdata" colspan="4"></td>-->
<!--								</tr>-->
							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addGlobalNewDropRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][dropdown_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<div id="multiselect<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="globaldatatable" id="MultiTable<?php echo $tempField['id']; ?>">
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
							<tbody class="<?php echo $tempField['id']; ?>globalmultirowdata">
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--globalmultirowdata" colspan="4"></td>-->
<!--								</tr>-->
							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addGlobalNewMultiRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][ms_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td> 
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<div id="radio<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="globaldatatable" id="RadioTable<?php echo $tempField['id']; ?>">
							<thead>
							    <tr>
							    	<th class="datathpro1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
							    	<th class="datathpro2"><label><b>Radio Image</b></label></th>
							    	<th class="datathpro3"><label><b>Product Image</b></label></th>
							    	<th class="datathpro4"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datathpro5"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="datathpro6"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
                                    <th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
                                    <th class="datathpro7"></th>
							    </tr>
							</thead>
							<tbody class="<?php echo $tempField['id']; ?>globalradiorowdata">
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--globalradiorowdata" colspan="7"></td>-->
<!--								</tr>-->
							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addGlobalNewRadioRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][radio_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>

					<div id="simple_radio<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="globaldatatable" id="RadioTable<?php echo $tempField['id']; ?>">
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
							<tbody class="<?php echo $tempField['id']; ?>globalsimpleradiorowdata">
<!--								<tr>-->
<!--									<td class="--><?php //echo $tempField['id']; ?><!--globalsimpleradiorowdata" colspan="7"></td>-->
<!--								</tr>-->
							</tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addGlobalNewSimpleRadioRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][sr_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>

					<div id="checkbox<?php echo $tempField['id']; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField['id']; ?>"></div>
						<table class="globaldatatable" id="CheckboxTable<?php echo $tempField['id']; ?>">
							<thead>
							    <tr>
							    	<th class="datathpro1"><label><b><?php echo _e('Title:','eopa'); ?></b></label></th>
							    	<th class="datathpro2"><label><b>Checkbox Image</b></label></th>
							    	<th class="datathpro3"><label><b>Product Image</b></label></th>
							    	<th class="datathpro4"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datathpro5"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    	<th class="datathpro6"><label><b><?php echo _e('Sort Order:','eopa'); ?></b></label></th>
                                    <th class="datath5"><label><b><?php _e('Stock:', 'eopa');?></b></label></th>
                                    <th class="datathpro7"></th>
							    </tr>
							</thead>
                            <tbody class="<?php echo $tempField['id']; ?>globalcheckboxrowdata">
<!--                                <tr>-->
<!--                                    <td class="--><?php //echo $tempField['id']; ?><!--globalcheckboxrowdata" colspan="7"></td>-->
<!--                                </tr>-->
                            </tbody>
							<tfoot>
								<tr>
							   		<td><input onClick="addGlobalNewCheckboxRow(<?php echo $tempField['id']; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','eopa'); ?>"></td>
							   		<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][cb_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
							   	</tr>
							</tfoot>
						</table>
					</div>
					
					<div id="date<?php echo $tempField['id']; ?>" style="display:none;">
						<table class="globaldatatable">
							<thead>
							    <tr>
							    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][date_option_price]" id="price<?php echo $tempField['id'] ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][date_option_price_type]" id="dateprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][date_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div id="time<?php echo $tempField['id']; ?>" style="display:none;">
						<table class="globaldatatable">
							<thead>
							    <tr>
							    	<th class="datath3"><label><b><?php echo _e('Price:','eopa'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','eopa'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][time_option_price]" id="price<?php echo $tempField['id'] ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][time_option_price_type]" id="timeprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][time_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div id="color<?php echo $tempField['id']; ?>" style="display:none;">
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
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][color_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][color_option_price_type]" id="colorprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath5">
										<input type="number" name="product_option[<?php echo $tempField['id'];?>][color_stock]" value="0" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][color_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div id="google_font<?php echo $tempField['id'];?>" style="display:none;">
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
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][google_font_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" />
									</td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][google_font_option_price_type]" id="google_font_price_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][gf_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>

					<div id="google_map<?php echo $tempField['id'];?>" style="display:none;">
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
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][google_map_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" />
									</td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][google_map_option_price_type]" id="google_map_price_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][gm_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div id="range_picker<?php echo $tempField['id'];?>" style="display:none;">
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
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][range_picker_option_price]" id="price<?php echo $tempField['id']; ?>" onChange="PriceOnly(this.id);" />
									</td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField['id']; ?>][range_picker_option_price_type]" id="range_picker_price_type">
					                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
					                	</select>
									</td>
									<td class="datath3">
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][range_picker_min_value]" id="min_value<?php echo $tempField['id']; ?>" />
									</td>
									<td class="datath4">
										<input class="inputs" type="text" name="product_option[<?php echo $tempField['id']; ?>][range_picker_max_value]" id="price<?php echo $tempField['id']; ?>" />
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td>
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][price_per_char]"  value="yes" />
										<?php _e('Enable Price per unit', 'eopa'); ?>
									</td>
									<td colspan="4">
										<input type="checkbox" name="product_option[<?php echo $tempField['id']; ?>][rp_multiply_price_by_qty]"  value="1" checked="checked" />
										<?php _e('Multiply price by quantity', 'eopa'); ?>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

			</div>
			<?php }  ?>

		<?php }


		function addGlobalForm2($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>" class="row100">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_title]" id="dropdowntitle" /></td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datath4">
                        <input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" />
                    </td>
                    <td class="datath5">
                        <input type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" value="<?php echo $tempField->stock;?>" />
                    </td>

                    <td><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		<?php } }


		function addGlobalMultiForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>" class="row100">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_title]" id="dropdowntitle" /></td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
                    <td class="datath5">
                        <input type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" value="<?php echo $tempField->stock;?>" />
                    </td>
                    <td><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		<?php } }

		function addGlobalRadioForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>" class="row100">
					<td class="datathpro1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_title]" id="dropdowntitle" /></td>
					<td class="datathpro2">
						<div class="imgdis" id="radioimgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_image_url]" id="radioimage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="radioimm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>

					<td class="datathpro3">
						<div class="imgdis" id="radioproimgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_proimage_url]" id="radioproimage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="radioproimm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>
					<td class="datathpro4">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datathpro5">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datathpro6"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
                    <td class="datath5">
                        <input type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" value="<?php echo $tempField->stock;?>" />
                    </td>
                    <td class="datathpro7"><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		<?php } }


		function addGlobalSimpleRadioForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>" class="row100">
					<td class="datathpro1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_title]" id="dropdowntitle" /></td>
					
					<td class="datathpro4">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datathpro5">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datathpro6"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][radio_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
                    <td class="datath5">
                        <input type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" value="<?php echo $tempField->stock;?>" />
                    </td>
                    <td class="datathpro7"><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		<?php } }


		function addGlobalcheckboxForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(!empty($tempField)) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>" class="row100">
					<td class="datathpro1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_title]" id="dropdowntitle" /></td>
					<td class="datathpro2">
						<div class="imgdis" id="checkboximgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_image_url]" id="checkboximage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="checkboximm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>

					<td class="datathpro3">
						<div class="imgdis" id="checkboxproimgdisplay<?php echo $tempField->id; ?>">
							<!--<img src="<?php echo EOPA_URL; ?>images/upload.png" width="50" />-->
						</div>
						<input type="hidden" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_proimage_url]" id="checkboxproimage_url<?php echo $tempField->id; ?>" class="regular-text">
						<input onClick="checkboxproimm('<?php echo $tempField->id; ?>', '<?php echo $tempField->field_id; ?>')"   type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload">
						
					</td>
					<td class="datathpro4">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datathpro5">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','eopa'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','eopa'); ?></option>
	                	</select>
					</td>
					<td class="datathpro6"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][check_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
                    <td class="datath5">
                        <input type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][stock]" value="<?php echo $tempField->stock;?>" />
                    </td>
                    <td class="datathpro7"><a href="javascript:void(0)" onClick="deleteGlobalDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		<?php } }

		function SaveGlobalOptions() {
			// echo '<pre>';
			// print_r($_POST);
			// exit;
			global $wpdb;
			if(!empty($_POST['id'])) {
				$rid = $_POST['id'];
			} else {
				$rid = '';	
			}

			if(!empty($_POST['rule_name'])) {
				$rule_name = $_POST['rule_name'];
			} else {
				$rule_name = '';	
			}

			if(!empty($_POST['rule_status'])) {
				$rule_status = $_POST['rule_status'];
			} else {
				$rule_status = '';	
			}

			if(!empty($_POST['applied_to'])) {
				$applied_to = $_POST['applied_to'];
			} else {
				$applied_to = '';	
			}

			if(!empty($_POST['proids'])) {
				
				$proids = implode(", ", $_POST['proids']);
			} else {
				$proids = '';	
			}
			
			if(!empty($_POST['catids'])) {
				
				$catids = implode(", ", $_POST['catids']);

			} else {
				$catids = '';	
			}

			$products_IDs[] = array();
			if(!empty($_POST['catids'])) {
				foreach($_POST['catids'] as $iid) {

					$products_IDs[] = query_posts( array(
				        'post_type' => 'product',
				        'post_status' => 'publish',
				        'fields' => 'ids', 
				        'tax_query' => array(
				            array(
				                'taxonomy' => 'product_cat',
				                'field' => 'term_id',
				                'terms' => $iid,
				                'operator' => 'IN',
				            )
				        )
				    ) );


					
				}
			}


			$result1 = array();
			foreach($products_IDs as $is) {

				$result1 = array_merge($result1, $is);
			}

			$catproids = implode(", ", $result1);

			if(!empty($rid)) {
				$wpdb->query("delete from $wpdb->eopa_rowoption_table where global_rule_id = '".intval($rid)."'");

				$wpdb->query($wpdb->prepare(
					"UPDATE " .$wpdb->eopa_global_rule_table." SET rule_name = %s, rule_status = %s, applied_on = %s, proids = %s, catids = %s, catproids = %s WHERE rule_id = %d",
				    stripslashes($rule_name),
				    $rule_status,
				    $applied_to,
				    $proids,
				    $catids,
				    $catproids,
				    $rid
				));

				$rule_id = $rid;

			} else {
				$wpdb->query(
					$wpdb->prepare( 
			            "INSERT INTO $wpdb->eopa_global_rule_table
			            (rule_name, rule_status, applied_on, proids, catids, catproids)
			            VALUES (%s,%s,%s,%s,%s,%s)
			            ",
			            $rule_name,
			            $rule_status,
			            $applied_to,
			            $proids,
			            $catids,
			            $catproids
		            ) 
	            );
	            $rule_id = $wpdb->insert_id;
        	}

            if( isset($_POST['product_option']) && count($_POST['product_option'])!=0) {
			foreach ($_POST['product_option'] as $option_id => $product_option) {

				if(isset($product_option['option_id']) && $product_option['option_id']!='') {
					$product_option_option_id = $product_option['option_id'];
				} else {
					$product_option_option_id = '';
				}

				$res = $wpdb->get_row("SELECT * FROM $wpdb->eopa_poptions_table WHERE id = '" . $option_id . "'", 'ARRAY_A');

				if(count($product_option) == 1) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->eopa_poptions_table." WHERE id = %d", $option_id  ) );
				}

				$manage_stock = isset($product_option['manage_stock']) ? $product_option['manage_stock'] : '';
				

				if(isset($product_option['option_type']) && $product_option['option_type'] == 'field') {

					$price_per_char = isset($product_option['field_price_per_char']) ? $product_option['field_price_per_char'] : '';
					$multiply_price_by_qty = isset($product_option['field_multiply_price_by_qty']) ? $product_option['field_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id = %s, option_title = %s, option_field_type = %s, option_is_required = %s, option_sort_order = %s, option_price = %s, option_price_type = %s, option_maxchars = %s, option_type = %s, global_rule_id = %s, enable_price_per_char = %s, showif = %s, cfield = %s, ccondition = %s, ccondition_value = %s, manage_stock = %s, stock = %s,multiply_price_by_qty=%s WHERE id = %s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['text_option_price'],
			            $product_option['text_option_price_type'],
			            $product_option['text_option_maxchars'],
			            'global',
			            $rule_id,
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['text_stock'],
			            $multiply_price_by_qty,
			            $option_id
			            ) );
					} else if(isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_maxchars, option_type,global_rule_id,enable_price_per_char,showif,cfield,ccondition,ccondition_value, manage_stock, stock, multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['text_option_price'],
			            $product_option['text_option_price_type'],
			            $product_option['text_option_maxchars'],
			            'global',
			            $rule_id,
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['text_stock'],
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'simple_checkbox') {

					$multiply_price_by_qty = isset($product_option['scb_multiply_price_by_qty']) ? $product_option['scb_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id =%s,option_title =%s,option_field_type =%s,option_is_required =%s,option_sort_order =%s,option_price =%s,option_price_type =%s,option_maxchars =%s,option_allowed_file_extensions =%s,option_type =%s,global_rule_id =%s,showif =%s,cfield =%s,ccondition =%s,ccondition_value=%s,manage_stock=%s,stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['simple_checkbox_option_price'],
			            $product_option['simple_checkbox_option_price_type'],
			            $product_option['simple_checkbox_option_maxchars'],
			            $product_option['simple_checkbox_allowed_file_extensions'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['stock'],
			            $multiply_price_by_qty,
			            $option_id
			            ) );
					} else {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_maxchars,option_allowed_file_extensions,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['simple_checkbox_option_price'],
			            $product_option['simple_checkbox_option_price_type'],
			            $product_option['simple_checkbox_option_maxchars'],
			            $product_option['simple_checkbox_allowed_file_extensions'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['stock'],
			            $multiply_price_by_qty
			            ) );
					}
				}

				else if(isset($product_option['option_type']) && $product_option['option_type'] == 'area') {
					$price_per_char = isset($product_option['textarea_price_per_char']) ? $product_option['textarea_price_per_char'] : '';
					$multiply_price_by_qty = isset($product_option['textarea_multiply_price_by_qty']) ? $product_option['textarea_multiply_price_by_qty'] : '';

					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id = %s, option_title = %s, option_field_type = %s, option_is_required = %s, option_sort_order = %s, option_price = %s, option_price_type = %s, option_maxchars = %s, option_type = %s, global_rule_id = %s, enable_price_per_char = %s, showif = %s, cfield = %s, ccondition = %s, ccondition_value = %s, manage_stock = %s, stock = %s,multiply_price_by_qty=%s WHERE id = %s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['area_option_price'],
			            $product_option['area_option_price_type'],
			            $product_option['area_option_maxchars'],
			            'global',
			            $rule_id,
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['area_stock'],
			            $multiply_price_by_qty,
			            $option_id
			            ) );
					} else {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_maxchars,option_type,global_rule_id,enable_price_per_char, showif,cfield,ccondition,ccondition_value,manage_stock,stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['area_option_price'],
			            $product_option['area_option_price_type'],
			            $product_option['area_option_maxchars'],
			            'global',
			            $rule_id,
			            $price_per_char,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['area_stock'],
			            $multiply_price_by_qty
			            ) );
					}
				}


				 else if(isset($product_option['option_type']) && $product_option['option_type'] == 'file') {
				 	$multiply_price_by_qty = isset($product_option['file_multiply_price_by_qty']) ? $product_option['file_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_allowed_file_extensions=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock = %s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['file_option_price'],
			            $product_option['file_option_price_type'],
			            $product_option['option_allowed_file_extensions'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_allowed_file_extensions,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['file_option_price'],
			            $product_option['file_option_price_type'],
			            $product_option['option_allowed_file_extensions'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );
					}
				
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'drop_down') {
					$multiply_price_by_qty = isset($product_option['dropdown_multiply_price_by_qty']) ? $product_option['dropdown_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock = %s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty,
			            $option_id
			            ));
			            

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['drop_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order, global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,%s)
						            ",
						            $option_id,
						            $row_value['drop_option_row_title'],
						            $row_value['drop_option_row_price'],
						            $row_value['drop_option_row_price_type'],
						            $row_value['drop_option_row_sort_order'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					} else {
						$wpdb->query($wpdb->prepare( 
				            "INSERT INTO $wpdb->eopa_poptions_table 
				            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
				            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
				            '',
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            'global',
				            $rule_id,
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $multiply_price_by_qty
			            ) );
						$lastid = $wpdb->insert_id;
						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['drop_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order, global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,%s)
						            ",
						            $lastid,
						            $row_value['drop_option_row_title'],
						            $row_value['drop_option_row_price'],
						            $row_value['drop_option_row_price_type'],
						            $row_value['drop_option_row_sort_order'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'multiple') {
					$multiply_price_by_qty = isset($product_option['ms_multiply_price_by_qty']) ? $product_option['ms_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
				            "UPDATE $wpdb->eopa_poptions_table SET
				            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock = %s,multiply_price_by_qty=%s WHERE id=%s",
				            '',
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            'global',
				            $rule_id,
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $multiply_price_by_qty,
				            $option_id
			            ));
			            

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['multi_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,%s)
						            ",
						            $option_id,
						            $row_value['multi_option_row_title'],
						            $row_value['multi_option_row_price'],
						            $row_value['multi_option_row_price_type'],
						            $row_value['multi_option_row_sort_order'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					} else {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['multi_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,%s)
						            ",
						            $lastid,
						            $row_value['multi_option_row_title'],
						            $row_value['multi_option_row_price'],
						            $row_value['multi_option_row_price_type'],
						            $row_value['multi_option_row_sort_order'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'radio') {

					$multiply_price_by_qty = isset($product_option['radio_multiply_price_by_qty']) ? $product_option['radio_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock = %s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty,
			            $option_id
			            ));
			            

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
					            "INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,global_rule_id,stock)
					            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)
					            ",
					            $option_id,
					            $row_value['radio_option_row_title'],
					            $row_value['radio_option_row_price'],
					            $row_value['radio_option_row_price_type'],
					            $row_value['radio_option_row_sort_order'],
					            $row_value['radio_option_row_image_url'],
					            $row_value['radio_option_row_proimage_url'],
					            $rule_id,
					            $stock
					            ));
							}
						}
					} else {
						$wpdb->query($wpdb->prepare( 
			            "INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
					            "INSERT INTO $wpdb->eopa_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,global_rule_id,stock)
					            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            $row_value['radio_option_row_title'],
					            $row_value['radio_option_row_price'],
					            $row_value['radio_option_row_price_type'],
					            $row_value['radio_option_row_sort_order'],
					            $row_value['radio_option_row_image_url'],
					            $row_value['radio_option_row_proimage_url'],
					            $rule_id,
					            $stock
					            ));
							}
						}
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'simple_radio') {

					$multiply_price_by_qty = isset($product_option['sr_multiply_price_by_qty']) ? $product_option['sr_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock = %s,multiply_price_by_qty=%s WHERE id=%s",
				            '',
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            'global',
				            $rule_id,
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $multiply_price_by_qty,
				            $option_id
			            ));
			           
						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,%s)
						            ",
						            $option_id,
						            $row_value['radio_option_row_title'],
						            $row_value['radio_option_row_price'],
						            $row_value['radio_option_row_price_type'],
						            $row_value['radio_option_row_sort_order'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					} else {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['radio_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,stock)
						            ",
						            $lastid,
						            $row_value['radio_option_row_title'],
						            $row_value['radio_option_row_price'],
						            $row_value['radio_option_row_price_type'],
						            $row_value['radio_option_row_sort_order'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					}

				}  else if(isset($product_option['option_type']) && $product_option['option_type'] == 'checkbox') {
					$multiply_price_by_qty = isset($product_option['cb_multiply_price_by_qty']) ? $product_option['cb_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock = %s,multiply_price_by_qty=%s WHERE id=%s",
				            '',
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            'global',
				            $rule_id,
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $multiply_price_by_qty,
				            $option_id
			            ));
			            
						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['check_option_row_title'] != '') {
								$stock = isset($row_value['stock']) ? esc_html($row_value['stock']) : '';
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)
						            ",
						            $option_id,
						            $row_value['check_option_row_title'],
						            $row_value['check_option_row_price'],
						            $row_value['check_option_row_price_type'],
						            $row_value['check_option_row_sort_order'],
						            $row_value['check_option_row_image_url'],
						            $row_value['check_option_row_proimage_url'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					} else {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $multiply_price_by_qty
			            ) );

			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if($row_value['check_option_row_title'] != '') {
								$wpdb->query($wpdb->prepare( 
						            "INSERT INTO $wpdb->eopa_rowoption_table
						            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order,option_image,option_pro_image,global_rule_id,stock)
						            VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)
						            ",
						            $lastid,
						            $row_value['check_option_row_title'],
						            $row_value['check_option_row_price'],
						            $row_value['check_option_row_price_type'],
						            $row_value['check_option_row_sort_order'],
						            $row_value['check_option_row_image_url'],
						            $row_value['check_option_row_proimage_url'],
						            $rule_id,
						            $stock
					            ));
							}
						}
					}
				} else if(isset($product_option['option_type'] ) && $product_option['option_type'] == 'date') {
					$multiply_price_by_qty = isset($product_option['date_multiply_price_by_qty']) ? $product_option['date_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            	"UPDATE $wpdb->eopa_poptions_table SET product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock = %s, stock=%s,multiply_price_by_qty=%s WHERE id=%s",
				            '',
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            $product_option['date_option_price'],
				            $product_option['date_option_price_type'],
				            'global',
				            $rule_id,
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $product_option['stock'],
				            $multiply_price_by_qty,
				            $option_id
			            ));
					} else {
						$wpdb->query($wpdb->prepare( 
				            "INSERT INTO $wpdb->eopa_poptions_table 
				            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,stock,multiply_price_by_qty) 
				            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
				            '',
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            $product_option['date_option_price'],
				            $product_option['date_option_price_type'],
				            'global',
				            $rule_id,
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $product_option['stock'],
				            $multiply_price_by_qty
			            ) );
					}

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'time') {
					$multiply_price_by_qty = isset($product_option['time_multiply_price_by_qty']) ? $product_option['time_multiply_price_by_qty'] : '';
					if($res && isset($product_option['option_title']) && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,manage_stock = %s, stock = %s,multiply_price_by_qty=%s WHERE id=%s",
				            '',
				            $product_option['option_title'],
				            $product_option['option_type'],
				            $product_option['option_is_required'],
				            $product_option['option_sort_order'],
				            $product_option['time_option_price'],
				            $product_option['time_option_price_type'],
				            'global',
				            $rule_id,
				            $product_option['showif'],
				            $product_option['cfield'],
				            $product_option['ccondition'],
				            $product_option['ccondition_value'],
				            $manage_stock,
				            $product_option['stock'],
				            $multiply_price_by_qty,
				            $option_id
			            ));
					} else {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['time_option_price'],
			            $product_option['time_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $manage_stock,
			            $product_option['stock'],
			            $multiply_price_by_qty
			            ) );
					}
				}
				 else if(isset($product_option['option_type']) && $product_option['option_type'] == 'color') {
				 	$multiply_price_by_qty = isset($product_option['color_multiply_price_by_qty']) ? $product_option['color_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['color_option_price'],
			            $product_option['color_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['color_option_price'],
			            $product_option['color_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'google_font') {
					$multiply_price_by_qty = isset($product_option['gfa_multiply_price_by_qty']) ? $product_option['gf_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_font_option_price'],
			            $product_option['google_font_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_font_option_price'],
			            $product_option['google_font_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'google_map') {
					$multiply_price_by_qty = isset($product_option['gm_multiply_price_by_qty']) ? $product_option['gm_multiply_price_by_qty'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s, manage_stock=%s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_map_option_price'],
			            $product_option['google_map_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,manage_stock,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['google_map_option_price'],
			            $product_option['google_map_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            'no',
			            $multiply_price_by_qty
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'range_picker') {
					$multiply_price_by_qty = isset($product_option['rp_multiply_price_by_qty']) ? $product_option['rp_multiply_price_by_qty'] : '';
					$price_per_char = isset($product_option['rp_price_per_char']) ? $product_option['rp_price_per_char'] : '';
					if($res && $product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            UPDATE $wpdb->eopa_poptions_table SET
			            product_id=%s,option_title=%s,option_field_type=%s,option_is_required=%s,option_sort_order=%s,option_price=%s,option_price_type=%s,option_type=%s,global_rule_id=%s,showif=%s,cfield=%s,ccondition=%s,ccondition_value=%s,enable_price_per_char=%s, manage_stock=%s, min_value=%s,max_value=%s,multiply_price_by_qty=%s WHERE id=%s",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['range_picker_option_price'],
			            $product_option['range_picker_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $price_per_char,
			            'no',
			           	$product_option['range_picker_min_value'],
			            $product_option['range_picker_max_value'],
			            $multiply_price_by_qty,
			            $option_id
			            ));
					} else if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->eopa_poptions_table 
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_type,global_rule_id,showif,cfield,ccondition,ccondition_value,enable_price_per_char,manage_stock,min_value,max_value,multiply_price_by_qty) 
			            VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			            '',
			            $product_option['option_title'],
			            $product_option['option_type'],
			            $product_option['option_is_required'],
			            $product_option['option_sort_order'],
			            $product_option['range_picker_option_price'],
			            $product_option['range_picker_option_price_type'],
			            'global',
			            $rule_id,
			            $product_option['showif'],
			            $product_option['cfield'],
			            $product_option['ccondition'],
			            $product_option['ccondition_value'],
			            $price_per_char,
			            'no',
			            $product_option['range_picker_min_value'],
			            $product_option['range_picker_max_value'],
			            $multiply_price_by_qty
			            ) );
					}
				}
			} 
			
			}
            exit;
		}

		function getProductGlobalOptions($id) { 

			global $wpdb;
			
            $result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->eopa_global_rule_table." WHERE rule_id = %d", $id));      
            return $result;
		}


		function getProductGlobalOptionsData($id) {

			global $wpdb;
			
            $result = $wpdb->get_results( $wpdb->prepare("SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE global_rule_id = %d", $id));      
            return $result;

		}

		function getGlobalProIDs() {

			global $wpdb;
			
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->eopa_poptions_table." WHERE option_type = %s", 'global'));      
            return $result;

		}



		public function _ajax_fetch_custom_list_callback() {

		    $_SESSION['chkd'] = $_GET['checkval'];
		    
			$manage_products = new EOF_List_Products();
		    $manage_products->ajax_response();
		}


		
	}
	new EO_Product_Addons_Admin();
	require_once(EOPA_PLUGIN_DIR.'admin/class-eo-product-addons-import-export.php');
}