<?php 
// Variable Product Support
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}// Exit if accessed directly

// MAIN CLASS PRICE CALCULATOR
class EXTENDONS_PRICE_CALCULATOR_VARIABLE_CALLBACK extends EXTENDONS_PRICE_CALCULATOR_VARIABLE_SUPPORT {
    
    // constructor main class
    public function __construct() {

        // min max fields for variable products and saving
        add_action( 'woocommerce_product_after_variable_attributes', array($this,'pcv_add_minmax_to_variations'), 10, 3 ); 
        add_action( 'woocommerce_save_product_variation', array($this,'pcv_save_minmax_variations'), 10, 2 );

        add_action( 'wp_ajax_variable_simple_product_action', array($this,'variable_simple_callback' ));
        add_action( 'wp_ajax_nopriv_variable_simple_product_action', array($this,'variable_simple_callback' ));

        add_action( 'wp_ajax_variable_double_product_action', array($this,'variable_double_callback' ));
        add_action( 'wp_ajax_nopriv_variable_double_product_action', array($this,'variable_double_callback' ));
        
        add_action( 'wp_ajax_variable_vol3d_product_action', array($this,'variable_vol3d_callback' ));
        add_action( 'wp_ajax_nopriv_variable_vol3d_product_action', array($this,'variable_vol3d_callback' ));

    }

    public function pcv_add_minmax_to_variations( $loop, $variation_data, $variation ) {
        
        // Min value of variation if calculator
        woocommerce_wp_text_input( array( 
            'id' => '_pcv_minimum_value[' . $variation->ID . ']', 
            'desc_tip'    => 'true',
            'placeholder' => __( 'Enter Minimum Quantity', 'extendons-price-calculator' ),
            'description' => __('Minimum Value Allowed to by for this variation','extendons-price-calculator'),
            'label' => __( 'Minimum Value', 'extendons-price-calculator' ),
            'value' => get_post_meta( $variation->ID, '_pcv_minimum_value', true )
        ));  
        
        // Max value of variation if calculator
        woocommerce_wp_text_input( array( 
            'id' => '_pcv_maximum_value[' . $variation->ID . ']', 
            'desc_tip'    => 'true',
            'placeholder' => __( 'Enter Maximum Quantity', 'extendons-price-calculator' ),
            'description' => __('Maximum Value Allowed to by for this variation','extendons-price-calculator'),
            'label' => __( 'Maximum Value', 'extendons-price-calculator' ),
            'value' => get_post_meta( $variation->ID, '_pcv_maximum_value', true )
        ));   
  
    }

    public function pcv_save_minmax_variations( $post_id ) {
        
        $minimum_value = $_POST['_pcv_minimum_value'][ $post_id ];
        if( ! empty( $minimum_value ) ) {
            update_post_meta( $post_id, '_pcv_minimum_value', esc_attr( $minimum_value ) );
        }

        $maximum_value = $_POST['_pcv_maximum_value'][ $post_id ];
        if( ! empty( $maximum_value ) ) {
            update_post_meta( $post_id, '_pcv_maximum_value', esc_attr( $maximum_value ) );
        }

    }

    // for single variable
    public function variable_simple_callback() {

        if(isset($_POST['condition']) && $_POST['condition'] == "variabe_simple_products_condition") {

            $total_quantity = $_POST['total_value'];

            $variable_product = new WC_Product_Variation($_POST['variable_id']);
            $regular_price = $variable_product->get_regular_price();
            $sales_price = $variable_product->get_sale_price();

            if(empty($sales_price)) {

                echo $price = $regular_price * $total_quantity;

            } else {

                echo $price = $sales_price * $total_quantity;

            }

        } die();
    }

    // for double variable
    public function variable_double_callback() {

        if(isset($_POST['condition']) && $_POST['condition'] == "variabe_double_products_condition") {

            $total_quantity = $_POST['total_value'];

            $variable_product = new WC_Product_Variation($_POST['variable_id']);
            $regular_price = $variable_product->get_regular_price();
            $sales_price = $variable_product->get_sale_price();

            if(empty($sales_price)) {

                echo $price = $regular_price * $total_quantity;

            } else {

                echo $price = $sales_price * $total_quantity;

            }

        } die();
    }

    // for vol3d variable
    public function variable_vol3d_callback() {

        if(isset($_POST['condition']) && $_POST['condition'] == "variabe_vol3d_products_condition") {

            $total_quantity = $_POST['total_value'];

            $variable_product = new WC_Product_Variation($_POST['variable_id']);
            $regular_price = $variable_product->get_regular_price();
            $sales_price = $variable_product->get_sale_price();

            if(empty($sales_price)) {

                echo $price = $regular_price * $total_quantity;

            } else {

                echo $price = $sales_price * $total_quantity;

            }

        } die();
    }

    public function main_scripts_sytles_enqueue() { 

        wp_enqueue_script('jquery');

    }   

} new EXTENDONS_PRICE_CALCULATOR_VARIABLE_CALLBACK();