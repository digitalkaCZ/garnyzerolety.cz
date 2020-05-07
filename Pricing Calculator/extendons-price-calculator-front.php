<?php  
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}// Exit if accessed directly

// FRONT END PRICE CALCULATOR CLASS
class EXTENDONS_PRICE_CALCULATOR_FRONT extends EXTENDONS_PRICE_CALCULATOR_MAIN {
	
    // constructor front class
	public function __construct() {

        add_action( 'wp', array($this,'front_scripts_sytles_enqueue' ));

        add_action( 'woocommerce_single_product_summary', array($this,'pc_calling_single_template' ));

        add_action( 'pc_before_add_to_cart_button', array($this,'pc_calculator_fields_before_add_to_cart' ));

        add_filter( 'woocommerce_product_tabs', array($this,'price_calculater_new_tab' ), 98);

        add_filter( 'pc_showing_price_after_title', array($this,'pc_displaty_product_price' ));

        add_filter( 'woocommerce_add_cart_item', array($this,'add_cart_item' ), 20, 1);

        add_filter( 'woocommerce_add_cart_item_data', array($this,'addProductToCart' ), 10, 2 );

        add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'get_cart_item_from_session' ), 10, 2);   

        add_filter( 'woocommerce_get_item_data', array($this,'getting_car_item_data' ), 10, 2);

        add_filter( 'woocommerce_cart_item_price', array($this,'filter_woocommerce_cart_item_price' ), 10, 3 );

        add_action( 'pc_show_short_description',array($this,'pc_showing_product_stock' ), 10);

        add_action( 'pc_showing_min_price', array($this, 'pc_showing_min_price_callback' )); 

        add_filter( 'woocommerce_get_price_html', array($this, 'pc_change_product_price_display_shop') );

        add_filter( 'woocommerce_add_to_cart_validation', array($this, 'pc_add_to_cart_quantity_validation'), 10, 5 );

        add_filter( 'woocommerce_loop_add_to_cart_link', array($this,'pc_shop_page_add_tocart_text' ));

        add_action( 'woocommerce_shop_loop_item_title', array($this, 'pc_show_sale_percentage_loop'), 25 );

        add_action( 'woocommerce_add_order_item_meta',  array($this, 'pc_add_order_item_meta') , 10, 2 );

        add_filter( 'woocommerce_cart_item_quantity', array( $this, 'pc_woocommerce_cart_item_quantity' ), 10, 2 );

	}


    /**
     * Showing stock quantity in frontend
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_showing_product_stock() {

        global $product;

        if ( $product->get_stock_quantity() ) {

            if ( number_format( $product->get_stock_quantity(),0,'','' ) < 3 ) {

                    printf (
                        esc_html__( '%s', 'extendons-price-calculator' ),
                        '<p class="stock in-stock">' . esc_html__( number_format($product->get_stock_quantity(),0,'','').' left in stock', '' ) . '</p>'
                    );
        
                } else {

                    printf (
                        esc_html__( '%s', 'extendons-price-calculator' ),
                        '<p class="stock in-stock">' . esc_html__( number_format($product->get_stock_quantity(),0,'','').' left in stock', '' ) . '</p>'
                    );
            }
        }
    }

    /**
     * adding product to cart
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function add_cart_item($cart_items) {

        $product_id = $cart_items['product_id'];

        $_product = wc_get_product( $product_id );

        if('price_calculator' == $_product->get_type()) {

            // -----------------------------------------------------//
            // --------------Global for all types (7.2.2020)----------------//
            // -----------------------------------------------------//
            if(isset($cart_items['pc_product_type'])) {

                // returning the price
                $cart_items['data']->set_price($cart_items['pc_calculated_price']);

                return $cart_items;
            }

            // -----------------------------------------------------//
            // --------------If its max length & width ----------------//
            // -----------------------------------------------------//
            // (7.2.2020)
            /*if(isset($cart_items['pc_product_type']) && $cart_items['pc_product_type'] == 'pc_max_lw_product') {

                // returning the price
                $cart_items['data']->set_price($cart_items['pc_max_lw_price']);

                return $cart_items;
            }*/

            // -----------------------------------------------------//
            // --------------If its boxtiles product----------------//
            // -----------------------------------------------------//
            if(isset($cart_items['pc_product_type']) && $cart_items['pc_product_type'] == 'pc_boxtiles_product') {

                // getting the product information if its box tile
                $box_area = get_post_meta($product_id, '_ext_boxtiles_totalarea_covered', true);
                $per_sqft = get_post_meta($product_id, '_ext_boxtiles_persqft', true);

                // calculating the box price
                $totalBoxPrice = $box_area * $per_sqft;

                // returning the price
                $cart_items['data']->set_price($totalBoxPrice * $cart_items['pc_quantity_needed']);

                return $cart_items;
            }


            $product_quantity_required = $cart_items['pc_quantity_needed'];

            $ranges_table = get_post_meta($product_id, '_pc_product_price_ranges', true);
            $minimum_price = get_post_meta($product_id, '_pc_minimum_price', true);
            $table_Check=  get_post_meta($product_id, '_checkbox_cal', true);

                $flag = 0;
                if($table_Check == 'yes'){
                    foreach ($ranges_table as $ranges) {
     
                        if($product_quantity_required >= $ranges['start_rang'] && $product_quantity_required <= $ranges['end_rang'] ) { 
              
                            if($ranges['sale_price_per_unit'] != "") {
                               
                                $pc_price =  $ranges['sale_price_per_unit'];
                               
                                $flag = 1;

                            } else {
                               
                                $pc_price =  $ranges['price_per_unit'];
                               
                                $flag = 1;
                            }
                        }
                    }

                    if ($flag == 1) {
                       
                        $cart_items['data']->set_price($pc_price); 
                    
                    } else {

                        $cart_items['data']->set_price( $minimum_price); 
                
                    }
                }else{
                    foreach ($ranges_table as $ranges) {
     
                        if($product_quantity_required >= $ranges['start_rang'] && $product_quantity_required <= $ranges['end_rang'] ) { 
              
                            if($ranges['sale_price_per_unit'] != "") {
                               
                                $pc_price = $product_quantity_required * $ranges['sale_price_per_unit'];
                               
                                $flag = 1;

                            } else {
                               
                                $pc_price = $product_quantity_required * $ranges['price_per_unit'];
                               
                                $flag = 1;
                            }
                        }
                    }

                    if ($flag == 1) {
                       
                        $cart_items['data']->set_price($pc_price); 
                    
                    } else {

                        $cart_items['data']->set_price($product_quantity_required * $minimum_price); 
                
                    }

                }
                
                foreach ($ranges_table as $ranges) {
     
                    if($product_quantity_required >= $ranges['start_rang'] && $product_quantity_required <= $ranges['end_rang'] ) { 
          
                        if($ranges['sale_price_per_unit'] != "") {
                           
                            $pc_price = $product_quantity_required * $ranges['sale_price_per_unit'];
                           
                            $flag = 1;

                        } else {
                           
                            $pc_price = $product_quantity_required * $ranges['price_per_unit'];
                           
                            $flag = 1;
                        }
                    }
                }

                if ($flag == 1) {
                   
                    $cart_items['data']->set_price($pc_price); 
                
                } else {

                    $cart_items['data']->set_price($product_quantity_required * $minimum_price); 
            
                }

            return $cart_items;
        }

        return $cart_items;
    }

    /**
     * Sending request variables to cart
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function addProductToCart( $cart_item_data, $product_id ) {

        $_product = wc_get_product( $product_id );

        if('price_calculator' == $_product->get_type()) {

            $cart_item_data[ 'pc_product_type' ] = self::getNumericValue($_REQUEST['pc_product_type']);
            $cart_item_data[ 'pc_quantity_needed' ] = self::getNumericValue($_REQUEST['pc_quantity_needed']);

            // (7.2.2020)
//            $cart_item_data['pc_max_lw_price'] = (isset($_REQUEST['pc_max_lw_price']) ? $_REQUEST['pc_max_lw_price'] : null);

            // (7.2.2020)
            $cart_item_data['pc_calculated_price'] = (isset($_REQUEST['pc_calculated_price']) ? self::getNumericValue($_REQUEST['pc_calculated_price']) : null);

            // for max length & width
            if(isset($_REQUEST['length_qty_max']) && $_REQUEST['length_qty_max'] !=''){
                $cart_item_data[ 'max_length_measurement' ] = self::getNumericValue($_REQUEST['length_qty_max']);
            }
            if(isset($_REQUEST['width_qty_max']) && $_REQUEST['width_qty_max'] !=''){
                $cart_item_data[ 'max_width_measurement' ] = self::getNumericValue($_REQUEST['width_qty_max']);
            }

            // for area length into width
            if(isset($_REQUEST['length_qty_area']) && $_REQUEST['length_qty_area'] !=''){
                $cart_item_data[ 'length_measurement' ] = self::getNumericValue($_REQUEST['length_qty_area']);
            }
            if(isset($_REQUEST['width_qty_area']) && $_REQUEST['width_qty_area'] !=''){
                $cart_item_data[ 'width_measurement' ] = self::getNumericValue($_REQUEST['width_qty_area']);
            }

            // for roomwalls
            if(isset($_REQUEST['length_qty_wall']) && $_REQUEST['length_qty_wall'] !=''){
                $cart_item_data[ 'wlength_measurement' ] = self::getNumericValue($_REQUEST['length_qty_wall']);
            }
            if(isset($_REQUEST['width_qty_wall']) && $_REQUEST['width_qty_wall'] !=''){
                $cart_item_data[ 'wwidth_measurement' ] = self::getNumericValue($_REQUEST['width_qty_wall']);
            }

            // for volume advanced
            if(isset($_REQUEST['length_qty_vol']) && $_REQUEST['length_qty_vol'] !=''){
                $cart_item_data[ 'vlength_measurement' ] = self::getNumericValue($_REQUEST['length_qty_vol']);
            }
            if(isset($_REQUEST['width_qty_vol']) && $_REQUEST['width_qty_vol'] !=''){
                $cart_item_data[ 'vwidth_measurement' ] = self::getNumericValue($_REQUEST['width_qty_vol']);
            }
            if(isset($_REQUEST['height_qty_vol']) && $_REQUEST['height_qty_vol'] !=''){
                $cart_item_data[ 'vheight_measurement' ] = self::getNumericValue($_REQUEST['height_qty_vol']);
            }

            $cart_item_data['unique_key'] = md5( microtime().rand() );

            return $cart_item_data;
        }
        
        return $cart_item_data;
    }
    
    /**
     * displaying and holding the product in session
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function get_cart_item_from_session($cart_items, $values) {
        
        $cart_items = $this->add_cart_item($cart_items);

        return $cart_items;
    }

    /**
     * getting cart item data for price calculator
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function getting_car_item_data( $cart_data, $carti = null ) {

       
        $custom_items = array();
        
        if( !empty( $cart_data ) ) {
        
            $custom_items = $cart_data;
        }
        
        // weight base measurement
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_weight_product" ) { 
            // getting weight unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_weight_unit_meta', true);
            // adding weight unit meta
            $custom_items[] = array(
                                'name' => __('Required Weight in '.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit
                            );
        }
        // area base measurement
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_area_product" ) { 
            // getting area unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_area_unit_meta', true);
            // adding area unit meta
            $custom_items[] = array( 
                                'name' => __('Required Area in '.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit
                            );
        }
        // length base measurement
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_length_product" ) { 
            // getting length unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_length_unit_meta', true);
            // adding length unit meta
            $custom_items[] = array( 
                                'name' => __('Délka'.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.'cm'
                            );
        }
        // volume base measurement
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_volume_product" ) { 
            // getting volume unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_volume_unit_meta', true);
            // adding volume unit meta
            $custom_items[] = array( 
                                'name' => __('Required Volume in '.$asked_unit, 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit
                            );
        }
        // boxtiles measurment
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_boxtiles_product" ) { 
            // getting weight unit
            $asked_unit = get_post_meta($carti['product_id'], '_ext_boxtiles_unit_meta', true);
            // adding weight unit meta
            $custom_items[] = array(
                                'name' => __('Total Box', 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed']
                            );
        }
        // area length/width measurment
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_area_lw_product" ) { 
            // getting weight unit   _ext_area_lw_unit_meta
            $asked_unit = get_post_meta($carti['product_id'], '_ext_area_lw_unit_meta', true);
            $area_length = get_post_meta($carti['product_id'], '_ext_area_lw_length_unit_meta', true);
            $area_width = get_post_meta($carti['product_id'], '_ext_area_lw_width_unit_meta', true);
            
            // Area Length Required
            $custom_items[] = array(
                                'name' => __('Šířka', 'extendons-price-calculator' ),
                                'value' => $carti['length_measurement'].' '.$area_length
                            );
            // Area Width Required
            $custom_items[] = array(
                                'name' => __('Výška', 'extendons-price-calculator' ),
                                'value' => $carti['width_measurement'].' '.$area_width
                            );

            // Total area of boxtiles
            $custom_items[] = array(
                                'name' => __('Celková plocha', 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit
                            );

        }
        // max length & width measurment
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_max_lw_product" ) {
            // getting weight unit   _ext_area_lw_unit_meta
            $asked_unit = get_post_meta($carti['product_id'], '_ext_max_lw_unit_meta', true);
            $area_length = get_post_meta($carti['product_id'], '_ext_max_lw_length_unit_meta', true);
            $area_width = get_post_meta($carti['product_id'], '_ext_max_lw_width_unit_meta', true);

            // Area Length Required
            $custom_items[] = array(
                                'name' => __('Montážní šířka', 'extendons-price-calculator' ),
                                'value' => $carti['max_length_measurement'].' '.$area_length
                            );
            // Area Width Required
            $custom_items[] = array(
                                'name' => __('Výška', 'extendons-price-calculator' ),
                                'value' => $carti['max_width_measurement'].' '.$area_width
                            );

            // Total area of boxtiles
            // (7.2.2020)
//            $custom_items[] = array(
//                                'name' => __('Total Area', 'extendons-price-calculator' ),
//                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit.' ('.$carti['pc_max_lw_price'].' Kč)'
//                            );

            // Total Calculated Price (7.2.2020)
            $custom_items[] = array(
                                'name' => __('Celková cena', 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit.' ('.$carti['pc_calculated_price'].' Kč)'
                            );

        }
        // room walls measurment
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_wall_product" ) { 
            // getting weight unit   _ext_area_lw_unit_meta
            $asked_unit = get_post_meta($carti['product_id'], '_ext_wall_unit_meta', true);
            $warea_length = get_post_meta($carti['product_id'], '_ext_wall_length_unit_meta', true);
            $warea_width = get_post_meta($carti['product_id'], '_ext_wall_width_unit_meta', true);
            
            // Area Length Required
            $custom_items[] = array(
                                'name' => __('Area Length', 'extendons-price-calculator' ),
                                'value' => $carti['wlength_measurement'].' '.$warea_length
                            );
            // Area Width Required
            $custom_items[] = array(
                                'name' => __('Area Width', 'extendons-price-calculator' ),
                                'value' => $carti['wwidth_measurement'].' '.$warea_width
                            );

            // Total area of boxtiles
            $custom_items[] = array(
                                'name' => __('Total Area', 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit
                            );

        }
        // volume advanced measurment
        if( isset( $carti['pc_product_type']) && $carti['pc_product_type'] == "pc_volumeadv_product" ) { 
            // getting weight unit   _ext_area_lw_unit_meta
            $asked_unit = get_post_meta($carti['product_id'], '_ext_volumeadv_unit_meta', true);
            $varea_length = get_post_meta($carti['product_id'], '_ext_volumeadv_length_unit_meta', true);
            $varea_width = get_post_meta($carti['product_id'], '_ext_volumeadv_width_unit_meta', true);
            $varea_height = get_post_meta($carti['product_id'], '_ext_volumeadv_height_unit_meta', true);
            
            // Area Length Required
            $custom_items[] = array(
                                'name' => __('Volume Length', 'extendons-price-calculator' ),
                                'value' => $carti['vlength_measurement'].' '.$varea_length
                            );
            // Area Width Required
            $custom_items[] = array(
                                'name' => __('Volume Width', 'extendons-price-calculator' ),
                                'value' => $carti['vwidth_measurement'].' '.$varea_width
                            );
            // Area height Required
            $custom_items[] = array(
                                'name' => __('Volume height', 'extendons-price-calculator' ),
                                'value' => $carti['vheight_measurement'].' '.$varea_height
                            );

            // Total area of boxtiles
            $custom_items[] = array(
                                'name' => __('Total Area', 'extendons-price-calculator' ),
                                'value' => $carti['pc_quantity_needed'].' '.$asked_unit
                            );

        }

        return $custom_items;
    }

    /**
     * setting price in price calculator in cart
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function filter_woocommerce_cart_item_price( $sale_item_price, $cart_item, $cart_item_key ) {
    
        $product_id = $cart_item['product_id'];

        $_product = wc_get_product( $product_id );

        $ranges_table = get_post_meta($product_id, '_pc_product_price_ranges', true);
        $minimum_price = get_post_meta($product_id, '_pc_minimum_price', true);


        if($_product->get_type() == 'price_calculator') {
           
            // -----------------------------------------------------//
            // --------------If its boxtiles product----------------//
            // -----------------------------------------------------//
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_boxtiles_product') {

                // getting the product information if its box tile  
                $box_area = get_post_meta($product_id, '_ext_boxtiles_totalarea_covered', true);
                $per_sqft = get_post_meta($product_id, '_ext_boxtiles_persqft', true);

                $sale_item_price = wc_price($box_area * $per_sqft);

                return $sale_item_price;
            }

            // rest of all product goes as it is
            $weight_needed = $cart_item['pc_quantity_needed'];

            // $cart_item['line_total'];
            // $cart_item['quantity'];
            // $item_sale_price_own = $cart_item['line_total'] / $weight_needed;
            // $sale_item_price  = wc_price($item_sale_price_own / $cart_item['quantity']);

            $flag = 0;
            
            foreach ($ranges_table as $ranges) {
 
                if($weight_needed >= $ranges['start_rang'] && $weight_needed <= $ranges['end_rang'] ) { 
      
                    if($ranges['sale_price_per_unit'] != "") {
                       
                        $sale_item_price = $ranges['sale_price_per_unit'];
                       
                        $flag = 1;

                    } else {
                       
                        $sale_item_price =  $ranges['price_per_unit'];
                       
                        $flag = 1;
                    }
                }
            }

            if ($flag == 1) {
               
               $sale_item_price;
            
            } else {
            
               $sale_item_price = $minimum_price;
            
            }

            return wc_price($sale_item_price);
        }

        return $sale_item_price;
                            
    }

    /**
     * adding tab for pc product ranges
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function price_calculater_new_tab( $tabs ) {
        
        global $post, $product;

        if( $product->is_type( 'price_calculator' )) {

            $measurement_type = get_post_meta($product->get_id(), '_pc_measurement_type', true);

            if(isset($measurement_type) && $measurement_type == "boxtiles") {

                return $tabs;
            
            } else {

                $tabs['product_question'] = array(
                    'title'     => __( 'Tabulka cen', 'extendons-price-calculator' ),
                    'priority'  => 50,  
                    'callback'  => array($this,'pc_ragnes_tab_callback')
                );
            }

                
        }

        return $tabs;  
    }

    /**
     * displaying data in single tab ranges
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_ragnes_tab_callback() { 

        global $post;
        // pricing table ranges
        $price_ranges = get_post_meta($post->ID, '_pc_product_price_ranges', true);
        // get measurement type
        $measurement_type = get_post_meta($post->ID, '_pc_measurement_type', true);
        // get measurment unit
        $measurement_unit = get_post_meta($post->ID, '_ext_'.$measurement_type.'_unit_meta', true); ?>

        <div id="pc_ranges_table" class="bootstrap-iso">
            <h2>
                <?php _e('Tabulka cen produktu', 'extendons-price-calculator'); ?>        
            </h2>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <?php _e('Rozměr', 'extendons-price-calculator'); ?>        
                        </th>
                        <th>
                            <?php _e('Cena', 'extendons-price-calculator'); ?>
                        </th>
                        <th>
                            <?php _e('Sleva', 'extendons-price-calculator'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($price_ranges as $key => $range): ?>
                        <tr>
                            <td>
                                <span class="pc-from-torange">
                                    <?php _e('Od', ''); ?>        
                                </span>
                                    <?php echo $range['start_rang'].' '.$measurement_unit; ?>
                                <span class="pc-from-torange">
                                    <?php _e('do', ''); ?>        
                                </span>
                                    <?php echo $range['end_rang'].' '.$measurement_unit; ?>        
                            </td>
                            <td>
                                <?php if(!empty($range['sale_price_per_unit'])) { ?>
                                    <del><?php echo wc_price($range['price_per_unit']); ?></del> 
                                <?php } else { ?>
                                    <?php echo wc_price($range['price_per_unit']); ?>
                                <?php } ?>
                            </td>
                            <td>
                                <?php echo wc_price($range['sale_price_per_unit']); ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>

    <?php } 

    /**
     * Displaying the measurement table for each measurement
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_calculator_fields_before_add_to_cart() {

        global $post, $product;
        
        // pricing table ranges
        $price_ranges = get_post_meta($post->ID, '_pc_product_price_ranges', true);
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

        if('price_calculator' == $product->get_type() && $price_meta == 'yes') {

            switch ($measurement_type) {

            case "weight":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/weight.php');
                break;

            case "area":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/area.php');
                break;

            // for max length & width
            case 'max_lw':
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/max-lw.php');
                break;

            case "length":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/length.php');
                break;

            case "volume":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/volume.php');
                break;

            case "boxtiles":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/boxtiles.php');
                break;

            case "area_lw":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/area-lw.php');
                break;

            case "wall":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/wall.php');
                break;

            case "volumeadv":
                    require_once(pc_pcalculator_invoices_dir.'templates/singletemplates/volumeadv.php');
                break;

            default:
                echo "Please Fill all data to display your Price Calculator Product";
                break;

    } } } 

    /**
     * showing price in single product page
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_displaty_product_price($price){
        
        global $product;

        $price_ranges = get_post_meta($product->get_id(), '_pc_product_price_ranges', true);
        $measurement_type = get_post_meta($product->get_id(), '_pc_measurement_type', true);
        $pricing_label = get_post_meta($product->get_id(), '_ext_'.$measurement_type.'_label_meta', true);

        if(isset($measurement_type) && $measurement_type == 'boxtiles') {

            $persqft = get_post_meta($product->get_id(), '_ext_boxtiles_persqft', true);
            $total_area_cov = get_post_meta($product->get_id(), '_ext_boxtiles_totalarea_covered', true);

            $total_box_price = $persqft * $total_area_cov;

            echo $price = '<p class="price">
                                <ins>'. wc_price($total_box_price).' '.$pricing_label.'</ins>
                            </p>';

        } else {

            if(!empty($price_ranges)) {

                $i = 0;
                $length = count($price_ranges);
                
                $first_val = '';
                $last_val = '';

                foreach ($price_ranges as $key => $value) {
                    
                    if ($i == 0) {
                        if(empty($value['price_per_unit'])){
                            $first_val = $value['price_per_unit'];
                        }else {
                            $first_val = $value['sale_price_per_unit'];
                        }
                    } else if ($i == $length - 1) {
                        if(empty($value['price_per_unit'])){
                            $last_val = $value['price_per_unit'];
                        }else {
                            $last_val = $value['sale_price_per_unit'];
                        }
                    }

                    $i++;
                }

                echo $price =   '<p class="price">
                                    <ins>'. wc_price($first_val).' '.$pricing_label.' - '.wc_price($last_val).' '.$pricing_label.'</ins>
                            </p>';

            }
        }
    }
    
    /**
     * displaying minimum price in single template
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_showing_min_price_callback() { 

        global $product;

        $min_price = get_post_meta($product->get_id(), '_pc_minimum_price', true);

        if( $product->is_type( 'price_calculator' )) {

            if(!empty($min_price)) { ?>

                <div class="pc_min_price">
                    <?php _e('Minimum Price '.wc_price($min_price), ''); ?>  
                </div>

    <?php } } }

    /**
     * setting single page tempalte for pc
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_calling_single_template() {
             
        global $product;

        if('price_calculator' == $product->get_type()) {

            wc_get_template('single-product/add-to-cart/pc-calculator-product.php', array(), '', pc_pcalculator_template_path . '/' );
        }
    }

    /**
     * displaying price on archive page
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_change_product_price_display_shop( $price ) {
            
        global $product;

        $_product = wc_get_product( $product->get_id() );

        if('price_calculator' == $product->get_type()) {

            if(is_shop()) {

                // if product type is box tiles
                $measurement_type = get_post_meta($product->get_id(), '_pc_measurement_type', true);
                
                if(isset($measurement_type) && $measurement_type == "boxtiles") {
                    
                    $box_area = get_post_meta($product->get_id(), '_ext_boxtiles_totalarea_covered', true);
                    $per_sqft = get_post_meta($product->get_id(), '_ext_boxtiles_persqft', true);

                    $pricing_label = get_post_meta($product->get_id(), '_ext_'.$measurement_type.'_field_meta', true);

                    $price = wc_price($box_area * $per_sqft).' '.$pricing_label;

                    return $price;

                } else {

                    $price_ranges = get_post_meta($product->get_id(), '_pc_product_price_ranges', true);
                
                    if(!empty($price_ranges)) {

                        $i = 0;
                        
                        $length = count($price_ranges);
                        
                        $first_val = '';
                        $last_val = '';

                        foreach ($price_ranges as $key => $value) {
                            
                            if ($i == 0) {
                                if(empty($value['price_per_unit'])){
                                    $first_val = $value['price_per_unit'];
                                }else {
                                    $first_val = $value['sale_price_per_unit'];
                                }
                            } else if ($i == $length - 1) {
                                if(empty($value['price_per_unit'])){
                                    $last_val = $value['price_per_unit'];
                                }else {
                                    $last_val = $value['sale_price_per_unit'];
                                }
                            }

                            $i++;
                        }

                        $price .= wc_price($first_val).' - '.wc_price($last_val);

                        return $price;

                    } else {

                        $price = wc_price(get_post_meta($product->get_id(), '_pc_minimum_price', true));

                        return $price;

                    }

                }
                
            }
        }

        return $price;
    }

    /**
     * enqueue the front style scripts and styles
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function front_scripts_sytles_enqueue() {

        $currency = get_woocommerce_currency();

        wp_enqueue_script('jquery');    

        wp_enqueue_style('pc_frontend-css', plugins_url( 'Styles/frontend.css', __FILE__ ), false, '1.0.0' );
        wp_enqueue_script( 'pc-frontend-js', plugins_url( 'Scripts/front-end.js', __FILE__ ), false, '1.5.9' );

        wp_localize_script( 'pc-frontend-js', 'pc_var_arguments', array(
                'woopb_nonce' => wp_create_nonce('woopb_nonce'),
                'ajax_url' => admin_url('admin-ajax.php'),
                'curr_pos' => get_option('woocommerce_currency_pos'),
                'curr_string' => get_woocommerce_currency_symbol($currency),
                'pc_decimal' => wc_get_price_decimals(),
                'pc_thou_sep' => wc_get_price_thousand_separator(),
                'pc_decimal_sep' => wc_get_price_decimal_separator()
            )
        );

    } 

    /**
     * validation for min and max values
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_add_to_cart_quantity_validation( $passed,$product_id ) { 

        $_product = wc_get_product( $product_id );

        if('price_calculator' == $_product->get_type()) {

            $min_value = get_post_meta($product_id, '_pc_minimum_quantity', true);
            $max_value = get_post_meta($product_id, '_pc_maximum_quantity', true);

            $pc_quantity_needed = self::getNumericValue($_REQUEST['pc_quantity_needed']);

            if(!empty($min_value) && !empty($max_value)) {
            
                if($pc_quantity_needed < $min_value || $pc_quantity_needed > $max_value) {

                    wc_add_notice( __( 'Allowed quatity must between '.$min_value.' and '.$max_value, 'extendons-price-calculator' ), 'error' );
                
                    $passed = false;

                    return $passed; 
                }
            
            } else if(isset($min_value) && !empty($min_value)) {

                if($pc_quantity_needed < $min_value) {

                    wc_add_notice( __( 'You should buy minimum '.$min_value.' quantity!', 'extendons-price-calculator' ), 'error' );
                
                    $passed = false;

                    return $passed;            
                }

            } else if(isset($max_value) && !empty($max_value)) {

                 if($pc_quantity_needed > $max_value) {

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


    /**
     * Display self add to cart button on woocommerce shop page
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_shop_page_add_tocart_text( $link ) {
        
        global $product;
     
        $product_id = $product->get_id();


        $_product = wc_get_product( $product_id );

        if('price_calculator' == $_product->get_type()) {

            $link ='<a href="'.get_permalink().'"class="button add_to_cart_button pc_calculator_producty">'.esc_html__( 'Calculate & Buy', 'extendons-price-calculator' ).'</a>';

            return $link;
        
        } else {

            return $link;
        }
    }

    /**
     * Showing sale badge if its in sale
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_show_sale_percentage_loop() {
         
        global $product;
         
        $product_id = $product->get_id();

        $_product = wc_get_product( $product_id );

        if('price_calculator' == $_product->get_type()) {
         
            $measurement_type = get_post_meta($product->get_id(), '_pc_measurement_type', true);
                
            if(isset($measurement_type) && $measurement_type == "boxtiles") {
                
                $box_area = get_post_meta($product_id, '_ext_boxtiles_totalarea_covered', true);
                $per_sqft = get_post_meta($product_id, '_ext_boxtiles_persqft', true);

                $sale_item_price = wc_price($box_area * $per_sqft);

                return $sale_item_price;

            } else {

                $price_ranges = get_post_meta($product_id, '_pc_product_price_ranges', true);

                $something = 0;
                
                foreach ($price_ranges as $key => $value) {
                   
                    if(!empty($value['sale_price_per_unit'])) {
                        $something = 1;
                    } else {
                        $something = 0;
                    }

                } 

                if($something == 1) {

                   echo "<span class='onsale'>".__( 'Sale!', 'extendons-price-calculator' )."</span>"; 

                }
            }
        }
    }


    /**
     * Showing stock quantity in frontend
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_add_order_item_meta($item_id, $cart_item) {

        if(isset($cart_item['pc_quantity_needed'])) {

            $required_weight = $cart_item['pc_quantity_needed'];
            // for weight
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_weight_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_weight_unit_meta', true);
                wc_add_order_item_meta($item_id, __( 'Required Weight '.$unites, 'extendons-price-calculator' ), $required_weight);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for area
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_area_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_area_unit_meta', true);
                wc_add_order_item_meta($item_id, __( 'Required Area '.$unites, 'extendons-price-calculator' ), $required_weight);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for max length & width
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_max_lw_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_max_lw_unit_meta', true);
                $max_length = get_post_meta($cart_item['product_id'], '_ext_max_lw_length_unit_meta', true);
                $max_width = get_post_meta($cart_item['product_id'], '_ext_max_lw_width_unit_meta', true);

                wc_add_order_item_meta($item_id, __( 'Celková plocha '.$unites, 'extendons-price-calculator' ), $required_weight);
                wc_add_order_item_meta($item_id, __( 'Šířka '.$max_length, 'extendons-price-calculator' ), $cart_item['max_length_measurement']);
                wc_add_order_item_meta($item_id, __( 'Výška '.$max_width, 'extendons-price-calculator' ), $cart_item['max_width_measurement']);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for length
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_length_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_length_unit_meta', true);
                wc_add_order_item_meta($item_id, __( 'Délka '.$unites, 'extendons-price-calculator' ), $required_weight);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for volume
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_volume_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_volume_unit_meta', true);
                wc_add_order_item_meta($item_id, __( 'Required Volume '.$unites, 'extendons-price-calculator' ), $required_weight);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for boxtiles
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_boxtiles_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_boxtiles_unit_meta', true);
                wc_add_order_item_meta($item_id, __( 'Required Box', 'extendons-price-calculator' ), $required_weight);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for area lw
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_area_lw_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_area_lw_unit_meta', true);
                $area_length = get_post_meta($cart_item['product_id'], '_ext_area_lw_length_unit_meta', true);
                $area_width = get_post_meta($cart_item['product_id'], '_ext_area_lw_width_unit_meta', true);

                wc_add_order_item_meta($item_id, __( 'Celková plocha '.$unites, 'extendons-price-calculator' ), $required_weight);
                wc_add_order_item_meta($item_id, __( 'Šířka '.$area_length, 'extendons-price-calculator' ), $cart_item['length_measurement']);
                wc_add_order_item_meta($item_id, __( 'Výška '.$area_width, 'extendons-price-calculator' ), $cart_item['width_measurement']);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for roomwalls
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_wall_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_wall_unit_meta', true);
                $warea_length = get_post_meta($cart_item['product_id'], '_ext_wall_length_unit_meta', true);
                $warea_width = get_post_meta($cart_item['product_id'], '_ext_wall_width_unit_meta', true);

                wc_add_order_item_meta($item_id, __( 'Celková plocha '.$unites, 'extendons-price-calculator' ), $required_weight);
                wc_add_order_item_meta($item_id, __( 'Šířka '.$warea_length, 'extendons-price-calculator' ), $cart_item['wlength_measurement']);
                wc_add_order_item_meta($item_id, __( 'Výška '.$warea_width, 'extendons-price-calculator' ), $cart_item['wwidth_measurement']);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
            // for volume advanced
            if(isset($cart_item['pc_product_type']) && $cart_item['pc_product_type'] == 'pc_volumeadv_product') {

                $unites = get_post_meta($cart_item['product_id'], '_ext_volumeadv_unit_meta', true);
                $varea_length = get_post_meta($cart_item['product_id'], '_ext_volumeadv_length_unit_meta', true);
                $varea_width = get_post_meta($cart_item['product_id'], '_ext_volumeadv_width_unit_meta', true);
                $varea_height = get_post_meta($cart_item['product_id'], '_ext_volumeadv_height_unit_meta', true);

                wc_add_order_item_meta($item_id, __( 'Celková plocha '.$unites, 'extendons-price-calculator' ), $required_weight);
                wc_add_order_item_meta($item_id, __( 'Délka '.$varea_length, 'extendons-price-calculator' ), $cart_item['vlength_measurement']);
                wc_add_order_item_meta($item_id, __( 'Required Width '.$varea_width, 'extendons-price-calculator' ), $cart_item['vwidth_measurement']);
                wc_add_order_item_meta($item_id, __( 'Required Height '.$varea_height, 'extendons-price-calculator' ), $cart_item['vheight_measurement']);
                // reduct stock quantity
                $product_stock = get_post_meta($cart_item['product_id'], '_stock', true);
                $actual_stock_now = $product_stock - $required_weight+1;
                update_post_meta($cart_item['product_id'], '_stock', $actual_stock_now);
            }
        }
    }

    /**
     * if its boxtiles remove cart quantity input
     *
     * @access public
     * @since  1.0.7
     * @author Tehseen Rajpoot <tehseen.ahmed@unitedsol.net>
    */
    public function pc_woocommerce_cart_item_quantity( $quantity, $cart_item_key ) {

        if ( isset( WC()->cart->cart_contents[$cart_item_key][ 'pc_product_type' ] ) && WC()->cart->cart_contents[$cart_item_key]['pc_product_type'] == 'pc_boxtiles_product') {
            return WC()->cart->cart_contents[$cart_item_key][ 'pc_quantity_needed' ].__( ' Box', 'extendons-price-calculator' );
        }

        return $quantity;
    }

    public static function getNumericValue($value) {
        $value = str_replace(',', '.', trim($value));
        return $value ? $value : null;
    }

// FRONT CLASS PRICE CALCULATOR
} new EXTENDONS_PRICE_CALCULATOR_FRONT();