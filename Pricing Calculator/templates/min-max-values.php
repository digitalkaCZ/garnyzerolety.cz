<?php 

 woocommerce_wp_text_input(array(
    'id'            => '_pc_minimum_price',
    'label'         => __( 'Minimum Price', 'extendons-price-calculator' ),
    'desc_tip'      => 'true',
    'description'   => __( 'If your quantity not exist in ranges then the minimum price will apply', 'extendons-price-calculator' ),
    'type'          => 'number',
    'value'         => get_post_meta( $post->ID, '_pc_minimum_price', true )
));

echo '<hr>';

woocommerce_wp_text_input(array(
    'id'            => '_pc_minimum_quantity',
    'label'         => __( 'Minimum Quantity', 'extendons-price-calculator' ),
    'desc_tip'      => 'true',
    'description'   => __( 'Set Minimum Quanity if you need for this product', 'extendons-price-calculator' ),
    'value'         => get_post_meta( $post->ID, '_pc_minimum_quantity', true )

));

woocommerce_wp_text_input(array(
    'id'            => '_pc_maximum_quantity',
    'label'         => __( 'Maximum Quantity', 'extendons-price-calculator' ),
    'desc_tip'      => 'true',
    'description'   => __( 'Set Maximum Quanity if you need for this product', 'extendons-price-calculator' ),
    'value'         => get_post_meta( $post->ID, '_pc_maximum_quantity', true )
));

woocommerce_wp_checkbox( 
            array( 
                'id'            => '_checkbox_cal', 
                'label'         => __('Enable Interval pricing', 'woocommerce' ), 
                'description'   => __( 'Enable Interval pricing', 'woocommerce' ),
                'value'         => get_post_meta( $post->ID, '_checkbox_cal', true ), 
                )
            );