<?php 
    woocommerce_wp_checkbox( array(
    	'id' 			=> 	'_ext_volume_price',
    	'label' 		=> __( 'Show Product Price Per Unit', 'extendons-price-calculator' ),
    	'description' 	=> __('Check this box to display product pricing per unit on the frontend. Also Enable calculated price Fields', 'extendons-price-calculator'),
    	'value' => get_post_meta($post->ID, '_ext_volume_price_meta', true)
    ));

    echo '<hr>';

    woocommerce_wp_text_input( array(
    	'id'			=> '_ext_volume_label',
    	'label'			=> __( 'Volume Pricing Label', 'extendons-price-calculator' ),
    	'desc_tip'		=> 'true',
    	'description'	=> __( 'Label to display next to the product price (defaults to pricing unit)', 'extendons-price-calculator' ),
    	'type' 			=> 'text',
    	'value'			=> get_post_meta($post->ID, '_ext_volume_label_meta', true)
    ));

    woocommerce_wp_text_input( array(
    	'id'			=> '_ext_volume_field',
    	'label'			=> __( 'Volume Label', 'extendons-price-calculator' ),
    	'desc_tip'		=> 'true',
    	'description'	=> __( 'Volume input field label to display on the frontend', 'extendons-price-calculator' ),
    	'type' 			=> 'text',
    	'value'			=> get_post_meta($post->ID, '_ext_volume_field_meta', true)
    ));

    woocommerce_wp_select( array( 
    	'id'      		=> '_ext_volume_unit',
    	'label'   		=> __( 'Volume Unit', 'extendons-price-calculator' ), 
    	'desc_tip'		=> 'true',
    	'description'	=> __( 'Unit to define pricing in', 'extendons-price-calculator' ),
    	'options' 		=> array(
    		'' 			=> __('None','extendons-price-calculator'),
    		'ml' 		=> __('ml','extendons-price-calculator'),
    		'l'   		=> __( 'l', 'extendons-price-calculator' ),
    		'cu_m'   	=> __( 'cu m', 'extendons-price-calculator' ),
    		'cup'   	=> __( 'cup', 'extendons-price-calculator' ),
    		'pt'   		=> __( 'pt', 'extendons-price-calculator' ),
    		'qt'   		=> __( 'qt', 'extendons-price-calculator' ),
    		'gal'   	=> __( 'gal', 'extendons-price-calculator' ),
    		'fl_oz'   	=> __( 'fl. oz.', 'extendons-price-calculator' ),
    		'cu_in'   	=> __( 'cu. in.', 'extendons-price-calculator' ),
    		'cu_ft'   	=> __( 'cu. ft.', 'extendons-price-calculator' ),
    		'cu_yd'   	=> __( 'cu. yd.', 'extendons-price-calculator' )
    	),
    	'value'			=> get_post_meta($post->ID, '_ext_volume_unit_meta', true)
    ));

?>
<p class="rest-pc-product">
    <button class="resetProducToSimple btn" data-id="<?php echo $post->ID; ?>" data-type="<?php echo get_post_meta($post->ID, '_pc_measurement_type', true); ?>" type="button" data-toggle="tooltip" data-placement="top" title="<?php _e('By Clicking this you can rest this product to normal..!',''); ?>">
        <i class="fa fa-refresh fa-spin"></i>
        <?php _e(' Reset to Normal', 'extendons-price-calculator')?>
    </button>
</p>
