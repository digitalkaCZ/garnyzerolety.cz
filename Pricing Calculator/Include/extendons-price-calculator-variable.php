<?php 
// Variable Product Support
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}// Exit if accessed directly

// MAIN CLASS PRICE CALCULATOR
class EXTENDONS_PRICE_CALCULATOR_VARIABLE_SUPPORT {
    
    // constructor main class
    public function __construct() {

        add_action( 'wp', array($this,'variable_scripts_sytles_enqueue' ));
        // woocomemrce ready starts from here
        add_filter( 'woocommerce_add_cart_item_data', array($this,'pcv_addProductToCart' ), 10, 2 );
        add_filter( 'woocommerce_add_cart_item', array($this,'pcv_add_cart_item' ), 20, 1);
        add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'pcv_get_cart_item_from_session' ), 10, 2);
        add_filter( 'woocommerce_get_item_data', array($this,'pcv_getting_car_item_data' ), 10, 2);  
        add_filter( 'woocommerce_cart_item_price', array($this,'pcv_filter_woocommerce_cart_item_price' ), 10, 3 );
        add_filter( 'woocommerce_add_to_cart_validation', array($this, 'pcv_add_to_cart_quantity_validation'), 10, 5 );
        add_action( 'woocommerce_add_order_item_meta',  array($this, 'pcv_add_order_item_meta') , 10, 2 );
        // showing input fields on variabel with price calculator
        add_action( 'woocommerce_before_add_to_cart_button', array($this,'pc_extendons_vairable_support_callback' ));
        // hide tab of pricing table tab if its variable product selected
        add_action( 'admin_footer', array($this,'pcv_select_tabs_show' ));
    }

    public function pcv_addProductToCart( $cart_item_data, $product_id ) {

        $_product = wc_get_product($product_id);

        if( $_product->is_type( 'variable' ) ) {

            $mearurement_type = get_post_meta($product_id, '_pc_measurement_type', true);

            if(isset($mearurement_type) && $mearurement_type !='none') {

                // simple products
                $cart_item_data[ 'pcv_product_type' ] = $_REQUEST['pcv_product_type'];
                $cart_item_data[ 'pcv_quantity_needed' ] = $_REQUEST['pcv_quantity_needed'];

                // area length into width
                if(isset($_REQUEST['vlength_qty_area']) && $_REQUEST['vlength_qty_area'] !=''){
                    $cart_item_data[ 'vlength_measurement' ] = $_REQUEST['vlength_qty_area'];
                }
                if(isset($_REQUEST['vwidth_qty_area']) && $_REQUEST['vwidth_qty_area'] !=''){
                    $cart_item_data[ 'vwidth_measurement' ] = $_REQUEST['vwidth_qty_area'];
                }

                // for volume advanced
                if(isset($_REQUEST['vlength_qty_vol']) && $_REQUEST['vlength_qty_vol'] !=''){
                    $cart_item_data[ 'vvlength_measurement' ] = $_REQUEST['vlength_qty_vol'];
                }
                if(isset($_REQUEST['vwidth_qty_vol']) && $_REQUEST['vwidth_qty_vol'] !=''){
                    $cart_item_data[ 'vvwidth_measurement' ] = $_REQUEST['vwidth_qty_vol'];
                }
                if(isset($_REQUEST['vheight_qty_vol']) && $_REQUEST['vheight_qty_vol'] !=''){
                    $cart_item_data[ 'vvheight_measurement' ] = $_REQUEST['vheight_qty_vol'];
                }

                return $cart_item_data;
            }

            return $cart_item_data;
        }

        return $cart_item_data;
    }

    // setting product price
    public function pcv_add_cart_item($cart_items) {

        $product_id = $cart_items['product_id'];
            
        $_product = wc_get_product($product_id);

        if( $_product->is_type( 'variable' ) ) {

            $measurement_type =  get_post_meta($product_id, '_pc_measurement_type', true);

            if(isset($measurement_type) && $measurement_type !='none') {

                $total_qty = $cart_items['pcv_quantity_needed'];

                $variable_product = new WC_Product_Variation($cart_items['variation_id']);
            
                // getting the regular and sale prices
                $regular_price = $variable_product->get_Regular_price();
                $sales_price = $variable_product->get_Sale_price();

                // setting the product price
                if(empty($sales_price)) {
                    $price = $regular_price;
                } else {
                    $price = $sales_price;
                }

                $total_price = $price * $total_qty;

                $cart_items['data']->set_price($total_price); 

                return $cart_items;
            }
        }
        
        return $cart_items;
    }

    public function pcv_get_cart_item_from_session($cart_items, $values) {
        
        $cart_items = $this->pcv_add_cart_item($cart_items);

        return $cart_items;
    }


    public function pcv_getting_car_item_data( $cart_data, $carti = null ) {

        $var_custom_items = array();
        
        if( !empty( $cart_data ) ) {
        
            $var_custom_items = $cart_data;
        }
        // weight variable base measurement
        if( isset( $carti['pcv_product_type']) && $carti['pcv_product_type'] == "pcv_weight_type" ) { 
            // getting weight unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_weight_unit_meta', true);
            // adding weight unit meta
            $var_custom_items[] = array(
                                'name' => __('Required Weight in '.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pcv_quantity_needed'].' '.$asked_unit
                            );
        }
        // area variable base measurement
        if( isset( $carti['pcv_product_type']) && $carti['pcv_product_type'] == "pcv_area_type" ) { 
            // getting area unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_area_unit_meta', true);
            // adding area unit meta
            $var_custom_items[] = array(
                                'name' => __('Required Area in '.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pcv_quantity_needed'].' '.$asked_unit
                            );
        }
        // length variable base measurement
        if( isset( $carti['pcv_product_type']) && $carti['pcv_product_type'] == "pcv_length_type" ) { 
            // getting length unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_length_unit_meta', true);
            // adding length unit meta
            $var_custom_items[] = array(
                                'name' => __('Požadovaná délka v '.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pcv_quantity_needed'].' '.$asked_unit
                            );
        }
        // volume variable base measurement
        if( isset( $carti['pcv_product_type']) && $carti['pcv_product_type'] == "pcv_volume_type" ) { 
            // getting volume unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_volume_unit_meta', true);
            // adding volume unit meta
            $var_custom_items[] = array(
                                'name' => __('Required Volume in '.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pcv_quantity_needed'].' '.$asked_unit
                            );
        }
        // variable area length/width measurment
        if( isset( $carti['pcv_product_type']) && $carti['pcv_product_type'] == "pcv_area_lw_type" ) { 
            // getting weight unit   _ext_area_lw_unit_meta
            $asked_unit = get_post_meta($carti['product_id'], '_ext_area_lw_unit_meta', true);
            $area_length = get_post_meta($carti['product_id'], '_ext_area_lw_length_unit_meta', true);
            $area_width = get_post_meta($carti['product_id'], '_ext_area_lw_width_unit_meta', true);
            
            // Area Length Required
            $var_custom_items[] = array(
                                'name' => __('Area Length', 'extendons-price-calculator' ),
                                'value' => $carti['vlength_measurement'].' '.$area_length
                            );
            // Area Width Required
            $var_custom_items[] = array(
                                'name' => __('Area Width', 'extendons-price-calculator' ),
                                'value' => $carti['vwidth_measurement'].' '.$area_width
                            );

            // Total area of boxtiles
            $var_custom_items[] = array(
                                'name' => __('Total Area', 'extendons-price-calculator' ),
                                'value' => $carti['pcv_quantity_needed'].' '.$asked_unit
                            );

        }
        // room walls measurment
        if( isset( $carti['pcv_product_type']) && $carti['pcv_product_type'] == "pcv_wall_type" ) { 
            // getting weight unit   _ext_area_lw_unit_meta
            $asked_unit = get_post_meta($carti['product_id'], '_ext_wall_unit_meta', true);
            $warea_length = get_post_meta($carti['product_id'], '_ext_wall_length_unit_meta', true);
            $warea_width = get_post_meta($carti['product_id'], '_ext_wall_width_unit_meta', true);
            
            // Area Length Required
            $var_custom_items[] = array(
                                'name' => __('Area Length', 'extendons-price-calculator' ),
                                'value' => $carti['vlength_measurement'].' '.$warea_length
                            );
            // Area Width Required
            $var_custom_items[] = array(
                                'name' => __('Area Width', 'extendons-price-calculator' ),
                                'value' => $carti['vwidth_measurement'].' '.$warea_width
                            );

            // Total area of boxtiles
            $var_custom_items[] = array(
                                'name' => __('Total Wall Area', 'extendons-price-calculator' ),
                                'value' => $carti['pcv_quantity_needed'].' '.$asked_unit
                            );

        }
        // room walls measurment
        if( isset( $carti['pcv_product_type']) && $carti['pcv_product_type'] == "pcv_volumeadv_type" ) { 

            $asked_unit = get_post_meta($carti['product_id'], '_ext_volumeadv_unit_meta', true);
            $varea_length = get_post_meta($carti['product_id'], '_ext_volumeadv_length_unit_meta', true);
            $varea_width = get_post_meta($carti['product_id'], '_ext_volumeadv_width_unit_meta', true);
            $varea_height = get_post_meta($carti['product_id'], '_ext_volumeadv_height_unit_meta', true);
            
             // Area Length Required
            $var_custom_items[] = array(
                                'name' => __('Volume Length', 'extendons-price-calculator' ),
                                'value' => $carti['vvlength_measurement'].' '.$varea_length
                            );
            // Area Width Required
            $var_custom_items[] = array(
                                'name' => __('Volume Width', 'extendons-price-calculator' ),
                                'value' => $carti['vvwidth_measurement'].' '.$varea_width
                            );
            // Area height Required
            $var_custom_items[] = array(
                                'name' => __('Volume height', 'extendons-price-calculator' ),
                                'value' => $carti['vvheight_measurement'].' '.$varea_height
                            );

            // Total area of boxtiles
            $var_custom_items[] = array(
                                'name' => __('Total Area', 'extendons-price-calculator' ),
                                'value' => $carti['pcv_quantity_needed'].' '.$asked_unit
                            );

        }

        return $var_custom_items;
    }

    public function pcv_filter_woocommerce_cart_item_price( $sale_item_price, $cart_item, $cart_item_key ) {

        $product_id = $cart_item['product_id'];
        $_product = wc_get_product( $product_id );

        if($_product->get_type() == 'variable') {

            $measurement_type =  get_post_meta($product_id, '_pc_measurement_type', true);

            if(isset($measurement_type) && $measurement_type !='none') {

                // we can also pass specific variation qty if needed
                $required_qty = $cart_item['pcv_quantity_needed'];

                $cart_item['line_total'];

                $cart_item['quantity'];

                $item_sale_price_own = $cart_item['line_total'] / $required_qty;

                $sale_item_price  = wc_price($item_sale_price_own / $cart_item['quantity']);

                return $sale_item_price;
            }
        }

        return $sale_item_price;
                            
    }

    public function pcv_add_to_cart_quantity_validation( $passed,$product_id ) { 

        // variation id
        if(isset($_REQUEST['variation_id']) && $_REQUEST['variation_id'] !=''){
            $variation_id = $_REQUEST['variation_id'];
        }
        
        $variation_obj = new WC_Product_variation($variation_id);
        $productStock = $variation_obj->get_stock_quantity();

        if(!empty($productStock)){

            if($_REQUEST['pcv_quantity_needed'] > $productStock) {

                wc_add_notice( __( 'Stock not available please enter quantity less then '.$productStock, 'extendons-price-calculator' ), 'error' );
                
                $passed = false;

                return $passed;
            }

        }

        // product id
        $_product = wc_get_product( $product_id );

        if('variable' == $_product->get_type()) {

            $min_value = get_post_meta($variation_id, '_pcv_minimum_value', true);
            $max_value = get_post_meta($variation_id, '_pcv_maximum_value', true);
            
            if(!empty($min_value) && !empty($max_value)) {
            
                if($_REQUEST['pcv_quantity_needed'] < $min_value || $_REQUEST['pcv_quantity_needed'] > $max_value) {

                    wc_add_notice( __( 'Allowed quatity must between '.$min_value.' and '.$max_value, 'extendons-price-calculator' ), 'error' );
                
                    $passed = false;

                    return $passed; 
                }
            
            } else if(isset($min_value) && !empty($min_value)) {

                if($_REQUEST['pcv_quantity_needed'] < $min_value) {

                    wc_add_notice( __( 'You should buy minimum '.$min_value.' quantity!', 'extendons-price-calculator' ), 'error' );
                
                    $passed = false;

                    return $passed;            
                }

            } else if(isset($max_value) && !empty($max_value)) {

                 if($_REQUEST['pcv_quantity_needed'] > $max_value) {

                    wc_add_notice( __( 'Maximum quantity should not be greater then '.$max_value, 'extendons-price-calculator' ), 'error' );
                
                    $passed = false;

                    return $passed;
                }

            } else {

                wc_add_notice( __( 'Price Calculator Product Successfully added in your cart', 'extendons-price-calculator' ), 'success' );
                
                $passed = true;

                return $passed; 
            }

            return $passed;
        }
        
        return $passed;
    }

    public function pcv_add_order_item_meta($item_id, $cart_item) {

        // product id
        $_product = wc_get_product( $cart_item['product_id']);

        if('variable' == $_product->get_type()) {

            if(isset($cart_item['pcv_quantity_needed'])) {

                $required_weight = $cart_item['pcv_quantity_needed'];
                // for weight
                if(isset($cart_item['pcv_product_type']) && $cart_item['pcv_product_type'] == 'pcv_weight_type') {

                    $unites = get_post_meta($cart_item['product_id'], '_ext_weight_unit_meta', true);
                    wc_add_order_item_meta($item_id, __( 'Required Weight '.$unites, 'extendons-price-calculator' ), $required_weight);

                    // reduce stock for current variation
                    $product_stock = get_post_meta($cart_item['variation_id'], '_stock', true);
                    $actual_stock_now = $product_stock - $required_weight+1;
                    update_post_meta($cart_item['variation_id'], '_stock', $actual_stock_now);
                }
                 // for area
                if(isset($cart_item['pcv_product_type']) && $cart_item['pcv_product_type'] == 'pcv_area_type') {

                    $unites = get_post_meta($cart_item['product_id'], '_ext_area_unit_meta', true);
                    wc_add_order_item_meta($item_id, __( 'Celková plocha '.$unites, 'extendons-price-calculator' ), $required_weight);

                    // reduce stock for current variation
                    $product_stock = get_post_meta($cart_item['variation_id'], '_stock', true);
                    $actual_stock_now = $product_stock - $required_weight+1;
                    update_post_meta($cart_item['variation_id'], '_stock', $actual_stock_now);
                }
                 // for length
                if(isset($cart_item['pcv_product_type']) && $cart_item['pcv_product_type'] == 'pcv_length_type') {

                    $unites = get_post_meta($cart_item['product_id'], '_ext_length_unit_meta', true);
                    wc_add_order_item_meta($item_id, __( 'Délka '.$unites, 'extendons-price-calculator' ), $required_weight);

                    // reduce stock for current variation
                    $product_stock = get_post_meta($cart_item['variation_id'], '_stock', true);
                    $actual_stock_now = $product_stock - $required_weight+1;
                    update_post_meta($cart_item['variation_id'], '_stock', $actual_stock_now);
                }
                 // for volume
                if(isset($cart_item['pcv_product_type']) && $cart_item['pcv_product_type'] == 'pcv_volume_type') {

                    $unites = get_post_meta($cart_item['product_id'], '_ext_volume_unit_meta', true);
                    wc_add_order_item_meta($item_id, __( 'Required Volume '.$unites, 'extendons-price-calculator' ), $required_weight);

                    // reduce stock for current variation
                    $product_stock = get_post_meta($cart_item['variation_id'], '_stock', true);
                    $actual_stock_now = $product_stock - $required_weight+1;
                    update_post_meta($cart_item['variation_id'], '_stock', $actual_stock_now);
                }
                // for area lw
                if(isset($cart_item['pcv_product_type']) && $cart_item['pcv_product_type'] == 'pcv_area_lw_type') {

                    $unites = get_post_meta($cart_item['product_id'], '_ext_area_lw_unit_meta', true);
                    $area_length = get_post_meta($cart_item['product_id'], '_ext_area_lw_length_unit_meta', true);
                    $area_width = get_post_meta($cart_item['product_id'], '_ext_area_lw_width_unit_meta', true);

                    wc_add_order_item_meta($item_id, __( 'Celková plocha '.$unites, '' ), $required_weight);
                    wc_add_order_item_meta($item_id, __( 'Šířka '.$area_length, '' ), $cart_item['vlength_measurement']);
                    wc_add_order_item_meta($item_id, __( 'Výška '.$area_width, '' ), $cart_item['vwidth_measurement']);

                    // reduce stock for current variation
                    $product_stock = get_post_meta($cart_item['variation_id'], '_stock', true);
                    $actual_stock_now = $product_stock - $required_weight+1;
                    update_post_meta($cart_item['variation_id'], '_stock', $actual_stock_now);
                }
                // for roomwalls
                if(isset($cart_item['pcv_product_type']) && $cart_item['pcv_product_type'] == 'pcv_wall_type') {

                    $unites = get_post_meta($cart_item['product_id'], '_ext_wall_unit_meta', true);
                    $warea_length = get_post_meta($cart_item['product_id'], '_ext_wall_length_unit_meta', true);
                    $warea_width = get_post_meta($cart_item['product_id'], '_ext_wall_width_unit_meta', true);

                    wc_add_order_item_meta($item_id, __( 'Total Wall Area '.$unites, 'extendons-price-calculator' ), $required_weight);
                    wc_add_order_item_meta($item_id, __( 'Délka '.$warea_length, 'extendons-price-calculator' ), $cart_item['vlength_measurement']);
                    wc_add_order_item_meta($item_id, __( 'Required Width '.$warea_width, 'extendons-price-calculator' ), $cart_item['vwidth_measurement']);

                    // reduce stock for current variation
                    $product_stock = get_post_meta($cart_item['variation_id'], '_stock', true);
                    $actual_stock_now = $product_stock - $required_weight+1;
                    update_post_meta($cart_item['variation_id'], '_stock', $actual_stock_now);
                }
                // for volume advanced
                if(isset($cart_item['pcv_product_type']) && $cart_item['pcv_product_type'] == 'pcv_volumeadv_type') {

                    $unites = get_post_meta($cart_item['product_id'], '_ext_volumeadv_unit_meta', true);
                    $varea_length = get_post_meta($cart_item['product_id'], '_ext_volumeadv_length_unit_meta', true);
                    $varea_width = get_post_meta($cart_item['product_id'], '_ext_volumeadv_width_unit_meta', true);
                    $varea_height = get_post_meta($cart_item['product_id'], '_ext_volumeadv_height_unit_meta', true);

                    wc_add_order_item_meta($item_id, __( 'Total Required Area '.$unites, 'extendons-price-calculator' ), $required_weight);
                    wc_add_order_item_meta($item_id, __( 'Délka '.$varea_length, 'extendons-price-calculator' ), $cart_item['vvlength_measurement']);
                    wc_add_order_item_meta($item_id, __( 'Required Width '.$varea_width, 'extendons-price-calculator' ), $cart_item['vvwidth_measurement']);
                    wc_add_order_item_meta($item_id, __( 'Required Height '.$varea_height, 'extendons-price-calculator' ), $cart_item['vvheight_measurement']);

                    // reduce stock for current variation
                    $product_stock = get_post_meta($cart_item['variation_id'], '_stock', true);
                    $actual_stock_now = $product_stock - $required_weight+1;
                    update_post_meta($cart_item['variation_id'], '_stock', $actual_stock_now);
                }
            }
        }
    }

    public function pc_extendons_vairable_support_callback() {
        
        global $post, $product;

        // GET PRIE CALCULATOR PRODUCT METAS
        
        // get measurement type
        $measurement_type = get_post_meta($post->ID, '_pc_measurement_type', true);
        // get measurment unit
        $measurement_unit = get_post_meta($post->ID, '_ext_'.$measurement_type.'_unit_meta', true);

        /*---------------------------------------------*/
        /*-------- Getting product information --------*/
        /*---------------------------------------------*/
            $price_meta = get_post_meta($post->ID, '_ext_'.$measurement_type.'_price_meta', true);
            $label_meta = get_post_meta($post->ID, '_ext_'.$measurement_type.'_label_meta', true);
            $field_meta = get_post_meta($post->ID, '_ext_'.$measurement_type.'_field_meta', true);

            // box by tile lables info
            $length_meta = get_post_meta($post->ID, '_ext_'.$measurement_type.'_length_label_meta', true);
            $width_meta = get_post_meta($post->ID, '_ext_'.$measurement_type.'_width_label_meta', true);
            $height_meta = get_post_meta($post->ID, '_ext_'.$measurement_type.'_height_label_meta', true);
            $box_area = get_post_meta($post->ID, '_ext_boxtiles_totalarea_covered', true);

        /*---------------------------------------------*/
        /*-------- Getting product information --------*/
        /*---------------------------------------------*/

        // checking if tis variable product
        if( $product->is_type( 'variable' ) && $price_meta == 'yes') {

            switch ($measurement_type) {
                
                // for weight
                case 'weight':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_single.php');
                break;
                // for area
                case 'area':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_single.php');
                break;
                // for max length & width
                case 'max_lw':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_single.php');
                break;
                // for length
                case 'length':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_single.php');
                break;
                // for volume
                case 'volume':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_single.php');
                break;
                // for area lw
                case 'area_lw':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_double.php');
                break;
                // for room wall
                case 'wall':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_double.php');
                break;

                case 'volumeadv':
                    require_once(pc_pcalculator_invoices_dir.'templates/variable/pcv_triple.php');
                break;

                default:
                    echo "Please Fill all data to display your Price Calculator Product";
                    break;
            }

        }
    }

    public function pcv_select_tabs_show () {

         if ('product' != get_post_type()):
            return;
        
        endif; ?>

        <script type='text/javascript'>
            //hidding the ranges if its variable product
            jQuery(document).ready(function() {

                // first apply on load if its variable hide ranges
                if('variable' == jQuery("#product-type").val()){
                    jQuery("#price_calculator_option ul li:nth-child(2)").css('display','none');
                    jQuery("#_select_measurement option[value='boxtiles']").remove();
                }

                // then in the other hand hide with onchage if its variable
                jQuery(document).on("change", "#product-type", function() {
                    if('variable' == jQuery(this).val()){
                        jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','none');
                        jQuery("#_select_measurement option[value='boxtiles']").remove();
                    }else {
                        jQuery("#price_calculator_option ul li:nth-child(2)").show();
                        $("#_select_measurement").append('<option value="boxtiles">Area Box Tiles</option>');
                    }
                });
            });
        </script>

    <?php }

    public function variable_scripts_sytles_enqueue() { 

        global $post;

        $currency = get_woocommerce_currency();
        $measurement_type = get_post_meta($post->ID, '_pc_measurement_type', true);

        wp_enqueue_script('jquery'); 

        wp_enqueue_script( 'pc-var-frontend-js', plugins_url( '../Scripts/front-var.js', __FILE__ ), false );

        wp_localize_script( 'pc-var-frontend-js', 'pcv_var_arguments', array(
                'woopb_nonce' => wp_create_nonce('woopb_nonce'),
                'vajax_url' => admin_url('admin-ajax.php'),
                'vcurr_pos' => get_option('woocommerce_currency_pos'),
                'vcurr_string' => get_woocommerce_currency_symbol($currency),
                'pcv_decimal' => wc_get_price_decimals(),
                'pcv_thou_sep' => wc_get_price_thousand_separator(),
                'pcv_decimal_sep' => wc_get_price_decimal_separator(),
                'pcv_type' => $measurement_type
            )
        );

    }   

} new EXTENDONS_PRICE_CALCULATOR_VARIABLE_SUPPORT();