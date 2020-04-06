<?php
/**
 * Simple custom product
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $product;

// action to adding things before add to cart
do_action( 'pc_before_add_to_cart_form' );
// display product title
do_action('pc_showing_price_after_title'); 

do_action('pc_showing_min_price');
?>
<form class="cart pc_add_to_cart_form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
    <?php

    do_action( 'pc_before_add_to_cart_button' ); 
    
    if ( !$product->is_sold_individually() )
            woocommerce_quantity_input( array(
                'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
                'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ))); ?>

    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

</form>

