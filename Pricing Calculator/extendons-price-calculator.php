<?php 
/*
Plugin Name: Extendons Woocommerce Measurement Price Calculator (edited by CREALAB)
Plugin URI: http://extendons.com
Description: For the purpose of calculating price based on customer measurements.
Author: Extendons
Version: 1.9.0
Developed By: Extendons
Author URI: http://extendons.com/
Support: http://support@extendons.com
textdomain: extendons-price-calculator
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}// Exit if accessed directly
	

if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function pc_ifwoocommerce_not_active() {

		deactivate_plugins(__FILE__);

		$error_message = __('<div class="error notice"><p>This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be installed and active!</p></div>', 'extendons-price-calculator');

		die($error_message);
	}

	add_action( 'admin_notice', 'pc_ifwoocommerce_not_active' );
}

// MAIN CLASS PRICE CALCULATOR
class EXTENDONS_PRICE_CALCULATOR_MAIN {
	
    // constructor main class
	public function __construct() {

		$this->module_constant();
		
        // deleting save ragnes
        add_action( 'wp_ajax_pc_deleting_saved_field', array($this,'pc_save_delete_price_row' ));
		add_action( 'wp_ajax_nopriv_pc_deleting_saved_field', array($this,'pc_save_delete_price_row' ));

        // reset product to normal
        add_action( 'wp_ajax_resetProductNormalCallback', array($this,'productResetCallback' ));
        add_action( 'wp_ajax_nopriv_resetProductNormalCallback', array($this,'productResetCallback' ));
		
        // including the classes
        require_once( pc_pcalculator_invoices_dir.'extendons-price-calculator-admin.php');
		require_once( pc_pcalculator_invoices_dir.'Include/extendons-price-calculator-variable.php');
        require_once( pc_pcalculator_invoices_dir.'Include/extendons-price-calculator-variable-callback.php');
        // variable product support
        require_once( pc_pcalculator_invoices_dir.'extendons-price-calculator-front.php');
		
        add_action('wp_loaded', array( $this, 'main_scripts_sytles_enqueue'));
		
        add_filter( 'woocommerce_is_purchasable', array($this, 'is_product_purchasable_measurement'), 10, 2 );

        // all single, simple products populated from this ajax call
        add_action( 'wp_ajax_weight_action_ajax', array($this,'weight_ajax_function' ));
        add_action( 'wp_ajax_nopriv_weight_action_ajax', array($this,'weight_ajax_function' ));

        // box by tile product type
        add_action( 'wp_ajax_boxtiles_action_ajax', array($this,'boxtiles_ajax_function' ));
        add_action( 'wp_ajax_nopriv_boxtiles_action_ajax', array($this,'boxtiles_ajax_function' ));

        // area length into width
        add_action( 'wp_ajax_arealw_action_ajax', array($this,'area_lw_ajax_function' ));
        add_action( 'wp_ajax_nopriv_arealw_action_ajax', array($this,'area_lw_ajax_function' ));

        // max length & width
        add_action( 'wp_ajax_maxlw_action_ajax', array($this,'max_lw_ajax_function' ));
        add_action( 'wp_ajax_nopriv_maxlw_action_ajax', array($this,'max_lw_ajax_function' ));

        // room walls
        add_action( 'wp_ajax_roomwall_action_ajax', array($this,'roomwalls_ajax_function' ));
        add_action( 'wp_ajax_nopriv_roomwall_action_ajax', array($this,'roomwalls_ajax_function' ));

        // room walls
        add_action( 'wp_ajax_volumed_action_ajax', array($this,'volumeadv_ajax_function' ));
        add_action( 'wp_ajax_nopriv_volumed_action_ajax', array($this,'volumeadv_ajax_function' ));
		
	} //the equalizer 2018, 

    /**
     * ajax callback for single and simple products
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function weight_ajax_function() {
        
        global $wpdb;
            
            if(isset($_POST['condition']) && $_POST['condition'] == "weight_base_condition") {
                
                $quantiti_weight = (float) $_POST['quantity'];
                
                $product_id = $_POST['weight_product_id'];

                $ranges_table = get_post_meta($product_id, '_pc_product_price_ranges', true);
                $minimum_price = get_post_meta($product_id, '_pc_minimum_price', true);
                $table_Check=  get_post_meta($product_id, '_checkbox_cal', true);

                $flag = 0;
                if($table_Check == 'yes'){
                    foreach ($ranges_table as $ranges) {
     
                        if($quantiti_weight >= $ranges['start_rang'] && $quantiti_weight <= $ranges['end_rang'] ) { 
              
                            if($ranges['sale_price_per_unit'] != "") {
                               
                                $pc_price = $ranges['sale_price_per_unit'];
                               
                                $flag = 1;

                            } else {
                               
                                $pc_price = $ranges['price_per_unit'];
                               
                                $flag = 1;
                            }
                        }
                    }
                    if ($flag == 1) {
                   
                        echo $pc_price;
                
                    } else {
                    
                        echo $pc_price = $minimum_price;
                    
                    }
                }
                else{
                    foreach ($ranges_table as $ranges) {
     
                        if($quantiti_weight >= $ranges['start_rang'] && $quantiti_weight <= $ranges['end_rang'] ) { 
              
                            if($ranges['sale_price_per_unit'] != "") {
                               
                                $pc_price = $quantiti_weight * $ranges['sale_price_per_unit'];
                               
                                $flag = 1;

                            } else {
                               
                                $pc_price = $quantiti_weight * $ranges['price_per_unit'];
                               
                                $flag = 1;
                            }
                        }
                    }
                    if ($flag == 1) {
                   
                       echo $pc_price;
                    
                    } else {
                    
                       echo $pc_price = $quantiti_weight * $minimum_price;
                    
                    }
                }
                

                

        } die();
        
    }

    /**
     * ajax callback for boxtiles
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function boxtiles_ajax_function() {

        global $wpdb;
            
            if(isset($_POST['condition']) && $_POST['condition'] == "adv_boxtiles_product_condition") {
                
                $quantiti_weight = $_POST['quantity'];
                
                $product_id = $_POST['product_id'];

                $per_sq_ft  = get_post_meta($product_id, '_ext_boxtiles_persqft', true);

                $area_covered = get_post_meta($product_id, '_ext_boxtiles_totalarea_covered', true);

                $total = $per_sq_ft * $area_covered;

                $total_box_price =  $total * $quantiti_weight;

               echo number_format((float)$total_box_price, 2, '.', '');


        } die();
    }

    /**
     * ajax callback for area length * width
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function area_lw_ajax_function() {
        
        global $wpdb;
            
            if(isset($_POST['condition']) && $_POST['condition'] == "area_lw_product_condition") {
                
                $quantiti_weight = $_POST['quantity'];
                
                $product_id = $_POST['product_id'];

                $ranges_table = get_post_meta($product_id, '_pc_product_price_ranges', true);
                $minimum_price = get_post_meta($product_id, '_pc_minimum_price', true);

                $flag = 0;
                
                foreach ($ranges_table as $ranges) {
     
                    if($quantiti_weight >= $ranges['start_rang'] && $quantiti_weight <= $ranges['end_rang'] ) { 
          
                        if($ranges['sale_price_per_unit'] != "") {
                           
                            $pc_price = $quantiti_weight * $ranges['sale_price_per_unit'];
                           
                            $flag = 1;

                        } else {
                           
                            $pc_price = $quantiti_weight * $ranges['price_per_unit'];
                           
                            $flag = 1;
                        }
                    }
                }

                if ($flag == 1) {
                   
                   echo $pc_price;
                
                } else {
                
                   echo $pc_price = $quantiti_weight * $minimum_price;
                
                }

        } die();
        
    }

    /**
     * ajax callback for max length & width
     *
     * @access public
     * @since  1.0.7
     * @author CREALAB <gabriel.vojtko@crealab.sk>
    */
    public function max_lw_ajax_function() {

        global $wpdb;
        $return_data = array(
            'price' => 0,
        );
        $lengths = array();
        $widths = array();
        $pricing = array();

        if(isset($_POST['condition']) && $_POST['condition'] == "max_lw_product_condition") {

            $quantity_weight = $_POST['quantity'];
            $product_id = $_POST['product_id'];
            $length = $_POST['length'];
            $width = $_POST['width'];

            $ranges_table = get_post_meta($product_id, '_pc_product_price_ranges', true);
            $minimum_price = get_post_meta($product_id, '_pc_minimum_price', true);

            foreach ($ranges_table as $ranges) {
                if ((float) $ranges['start_rang'] > 0) {
                    $lengths[] = (float) $ranges['start_rang'];
                }

                if ((float) $ranges['end_rang'] > 0) {
                    $widths[] = (float) $ranges['end_rang'];
                }

                $pricing[$ranges['start_rang']][$ranges['end_rang']]['price_per_unit'] = $ranges['price_per_unit'];
                $pricing[$ranges['start_rang']][$ranges['end_rang']]['sale_price_per_unit'] = $ranges['sale_price_per_unit'];
            }

            $length_min = min($lengths);
            $length_max = max($lengths);
            $width_min = min($widths);
            $width_max = max($widths);

            if ($length < $length_min) {
                $return_data['error_length'] = 'Minimální rozměr je ' . $length_min . '!';
            } else if ($length > $length_max) {
                $return_data['error_length'] = 'Maximální rozměr je ' . $length_max . '!';
            }

            if ($width < $width_min) {
                $return_data['error_width'] = 'Minimální rozměr je ' . $width_min . '!';
            } else if ($width > $width_max) {
                $return_data['error_width'] = 'Maximální rozměr je ' . $width_max . '!';
            }

            if (empty($return_data['error_width']) && empty($return_data['error_length'])) {
                $length_real = 0;
                $width_real = 0;

                if (in_array($length, $lengths)) {
                    $length_real = $length;
                } else {
                    $lengths[] = (float)$length;
                    sort($lengths);

                    foreach ($lengths as $key => $length_value) {
                        if ($length_value == $length) {
                            break;
                        }
                    }

                    $key = (int) $key;
                    $length_real = $lengths[$key+1];
                }

                if (in_array($width, $widths)) {
                    $width_real = $width;
                } else {
                    $widths[] = (float)$width;
                    sort($widths);

                    foreach ($widths as $key => $width_value) {
                        if ($width_value == $width) {
                            break;
                        }
                    }

                    $key = (int) $key;
                    $width_real = $widths[$key+1];
                }

                $price = (!empty($pricing[$length_real][$width_real]['sale_price_per_unit']) ? $pricing[$length_real][$width_real]['sale_price_per_unit'] : $pricing[$length_real][$width_real]['price_per_unit']);

                if ($price == 'x') {
                    foreach ($pricing[$width_real] as $max_entered_width_ => $value) {
                        if ($value == 'x') {
                            break;
                        }
                        $max_entered_width = $max_entered_width_;
                    }

                    $return_data['error_width'] = 'Hodnota při zadané šířce je nejvíc ' . $max_entered_width . '!';
                } else {
                    $price = (float) $price;
                }

                $return_data['price'] = $price;
            }

           echo json_encode($return_data);

        } die();
    }

    /**
     * ajax callback for room walls
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function roomwalls_ajax_function() {
        
        global $wpdb;
            
            if(isset($_POST['condition']) && $_POST['condition'] == "roomwall_product_condition") {
                
                $quantiti_weight = $_POST['quantity'];
                
                $product_id = $_POST['product_id'];

                $ranges_table = get_post_meta($product_id, '_pc_product_price_ranges', true);
                $minimum_price = get_post_meta($product_id, '_pc_minimum_price', true);

                $flag = 0;
                
                foreach ($ranges_table as $ranges) {
     
                    if($quantiti_weight >= $ranges['start_rang'] && $quantiti_weight <= $ranges['end_rang'] ) { 
          
                        if($ranges['sale_price_per_unit'] != "") {
                           
                            $pc_price = $quantiti_weight * $ranges['sale_price_per_unit'];
                           
                            $flag = 1;

                        } else {
                           
                            $pc_price = $quantiti_weight * $ranges['price_per_unit'];
                           
                            $flag = 1;
                        }
                    }
                }

                if ($flag == 1) {
                   
                   echo $pc_price;
                
                } else {
                
                   echo $pc_price = $quantiti_weight * $minimum_price;
                
                }

        } die();
        
    }


    /**
     * ajax callback for volume advanced
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function volumeadv_ajax_function() {
        
        global $wpdb;
            
            if(isset($_POST['condition']) && $_POST['condition'] == "volumed_product_condition") {
                
                $quantiti_weight = $_POST['quantity'];
                $product_id = $_POST['product_id'];

                $ranges_table = get_post_meta($product_id, '_pc_product_price_ranges', true);
                $minimum_price = get_post_meta($product_id, '_pc_minimum_price', true);

                $flag = 0;
                
                foreach ($ranges_table as $ranges) {
                    if($quantiti_weight >= $ranges['start_rang'] && $quantiti_weight <= $ranges['end_rang'] ) {
                        if($ranges['sale_price_per_unit'] != "") {
                            $pc_price = $quantiti_weight * $ranges['sale_price_per_unit'];
                            $flag = 1;
                        } else {
                            $pc_price = $quantiti_weight * $ranges['price_per_unit'];
                            $flag = 1;
                        }
                    }
                }

                if ($flag == 1) {
                   echo $pc_price;
                } else {
                   echo $pc_price = $quantiti_weight * $minimum_price;
                }

        } die();
    }


    /**
     * Allowing the price calcualtor product to purchasable
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
	public function is_product_purchasable_measurement( $purchasable, $product ){
    
        if( $product->get_type() == 'price_calculator')
    
            $purchasable = true;
    
        return $purchasable;
    }

	/**
     * remove the save price ranges
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
	public function pc_save_delete_price_row() {

		if(isset($_POST['condition']) && $_POST['condition'] == "pc_delete_saved_row") {

			$row_delete_id = $_POST['id'];
			echo $array_key = $row_delete_id[5];
			$saved_ranges = get_post_meta($_POST['p_id'], '_pc_product_price_ranges', true);
			unset($saved_ranges[$array_key]);
            $reindex = array_values($saved_ranges);
			update_post_meta( $_POST['p_id'], '_pc_product_price_ranges', $reindex);
		}
		
		die();
	}

    public function productResetCallback() {

        if(isset($_POST['condition']) && $_POST['condition'] == "resetProductNormal") {

            global $wpdb;

            $product_id = $_POST['productID'];
            $product_type = $_POST['producType'];

            if(isset($product_type) && $product_type == 'weight' || $product_type == 'area' || $product_type == 'length' || $product_type == 'volume' ) {

                // deleting the post meta
                delete_post_meta($product_id, '_ext_'.$product_type.'_price_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_label_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'t_field_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_unit_meta');
                delete_post_meta($product_id, '_pc_minimum_quantity');
                delete_post_meta($product_id, '_pc_maximum_quantity');
                delete_post_meta($product_id, '_pc_minimum_price');
                delete_post_meta($product_id, '_pc_product_price_ranges');
                delete_post_meta($product_id, '_pc_measurement_type');
                // set product to normal
                wp_set_object_terms( $product_id, null, 'product_type' );

            } else if(isset($product_type) && $product_type == 'area_lw' || $product_type == 'wall') {

                // deleting the post meta
                delete_post_meta($product_id, '_ext_'.$product_type.'_price_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_label_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'t_field_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_unit_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_length_label');
                delete_post_meta($product_id, '_ext_'.$product_type.'_length_unit');
                delete_post_meta($product_id, '_ext_'.$product_type.'_width_label');
                delete_post_meta($product_id, '_ext_'.$product_type.'_width_unit');
                delete_post_meta($product_id, '_pc_minimum_quantity');
                delete_post_meta($product_id, '_pc_maximum_quantity');
                delete_post_meta($product_id, '_pc_minimum_price');
                delete_post_meta($product_id, '_pc_product_price_ranges');
                delete_post_meta($product_id, '_pc_measurement_type');
                // set product to normal
                wp_set_object_terms( $product_id, null, 'product_type' );

            } else if(isset($product_type) && $product_type == 'boxtiles') {

                // deleting the post meta
                delete_post_meta($product_id, '_ext_'.$product_type.'_price_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_label_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'t_field_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_unit_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_length_label');
                delete_post_meta($product_id, '_ext_'.$product_type.'_length_unit');
                delete_post_meta($product_id, '_ext_'.$product_type.'_width_label');
                delete_post_meta($product_id, '_ext_'.$product_type.'_width_unit');
                delete_post_meta($product_id, '_ext_'.$product_type.'_persqft');
                delete_post_meta($product_id, '_ext_'.$product_type.'_totalarea_covered');
                delete_post_meta($product_id, '_pc_minimum_quantity');
                delete_post_meta($product_id, '_pc_maximum_quantity');
                delete_post_meta($product_id, '_pc_minimum_price');
                delete_post_meta($product_id, '_pc_product_price_ranges');
                delete_post_meta($product_id, '_pc_measurement_type');
                // set product to normal
                wp_set_object_terms( $product_id, null, 'product_type' );

            } else if(isset($product_type) && $product_type == 'volumeadv') {

                // deleting the post meta
                delete_post_meta($product_id, '_ext_'.$product_type.'_price_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_label_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'t_field_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_unit_meta');
                delete_post_meta($product_id, '_ext_'.$product_type.'_length_label');
                delete_post_meta($product_id, '_ext_'.$product_type.'_length_unit');
                delete_post_meta($product_id, '_ext_'.$product_type.'_width_label');
                delete_post_meta($product_id, '_ext_'.$product_type.'_width_unit');
                delete_post_meta($product_id, '_ext_'.$product_type.'_height_label');
                delete_post_meta($product_id, '_ext_'.$product_type.'_height_unit');
                delete_post_meta($product_id, '_pc_minimum_quantity');
                delete_post_meta($product_id, '_pc_maximum_quantity');
                delete_post_meta($product_id, '_pc_minimum_price');
                delete_post_meta($product_id, '_pc_product_price_ranges');
                delete_post_meta($product_id, '_pc_measurement_type');
                // set product to normal
                wp_set_object_terms( $product_id, null, 'product_type' );
            }

        } die();
    }

    /**
     * module constant for price calculator
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
	public function module_constant() {

		if ( !defined( 'pc_pcalculator_invoices_url' ) )
	    define( 'pc_pcalculator_invoices_url', plugin_dir_url( __FILE__ ) );

	    if ( !defined( 'pc_pcalculator_invoices_basename' ) )
	    define( 'pc_pcalculator_invoices_basename', plugin_basename( __FILE__ ) );

	    if ( ! defined( 'pc_pcalculator_invoices_dir' ) )
	    define( 'pc_pcalculator_invoices_dir', plugin_dir_path( __FILE__ ) );

		if ( !defined( 'pc_pcalculator_template_path' ) ) 
    	define( 'pc_pcalculator_template_path', pc_pcalculator_invoices_dir . 'templates' );
	}

    /**
     * enqueue the styles for plugin
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
	public function main_scripts_sytles_enqueue() { 

		wp_enqueue_script('jquery');	
		
        wp_enqueue_script('pc_accounting-js', plugins_url( 'Scripts/accounting.min.js', __FILE__ ), false );
		
        wp_enqueue_style('pc_backend-css', plugins_url( '/Styles/backend.css', __FILE__ ), false );
		
        wp_enqueue_style( 'pc-bootstrap-admin-css', plugins_url( '/Styles/bootstrap-iso.css', __FILE__ ), false );
		
        wp_enqueue_script('pc-bootstrap-js', plugins_url( 'Scripts/bootstrap.min.js', __FILE__ ), false );

        wp_enqueue_style('pc-font-awesome-css', plugins_url( '/Styles/font-awesome.min.css', __FILE__ ), false );
		
        if ( function_exists('load_plugin_textdomain') )
		load_plugin_textdomain( 'extendons-price-calculator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	} 	

} new EXTENDONS_PRICE_CALCULATOR_MAIN();