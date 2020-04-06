<?php
	woocommerce_wp_checkbox( array(
		'id' 			=> '_ext_weight_price',
		'label' 		=> __( 'Show Product Price Per Unit', 'extendons-price-calculator' ),
		'description' 	=> __('Check this box to display product pricing per unit on the frontend. Also Enable calculated price Fields', 'extendons-price-calculator'),
		'value' 		=> get_post_meta($post->ID, '_ext_weight_price_meta', true)
	));

	echo '<hr>';

	woocommerce_wp_text_input( array(
		'id'			=> '_ext_weight_label',
		'label'			=> __( 'Weight Pricing Label', 'extendons-price-calculator' ),
		'desc_tip'		=> 'true',
		'description'	=> __( 'Label to display next to the product price (defaults to pricing unit)', 'extendons-price-calculator' ),
		'type' 			=> 'text',
		'value'			=> get_post_meta( $post->ID, '_ext_weight_label_meta', true )
	));

	woocommerce_wp_text_input( array(
		'id'			=> '_ext_weight_field',
		'label'			=> __( 'Weight Label', 'extendons-price-calculator' ),
		'desc_tip'		=> 'true',
		'description'	=> __( 'Weight input field label to display on the frontend', 'extendons-price-calculator' ),
		'type' 			=> 'text',
		'value'			=> get_post_meta( $post->ID, '_ext_weight_field_meta', true )
	));	

	woocommerce_wp_select( array( 
		'id'      		=> '_ext_weight_unit', 
		'label'   		=> __( 'Weight Unit', 'extendons-price-calculator' ), 
		'desc_tip'		=> 'true',
		'description'	=> __( 'Unit to define pricing in', 'extendons-price-calculator' ),
		'options' 		=> array(
			'' 			=> __('None','extendons-price-calculator'),
			'g' 		=> __('g','extendons-price-calculator'),
			'kg'   		=> __( 'kg', 'extendons-price-calculator' ),
			'mg'   		=> __( 'mg', 'extendons-price-calculator' ),
			'oz'   		=> __( 'oz', 'extendons-price-calculator' ),
			'lb'   		=> __( 'lb', 'extendons-price-calculator' ),
			't'   		=> __( 't', 'extendons-price-calculator' )
		),
		'value'			=> get_post_meta( $post->ID, '_ext_weight_unit_meta', true )
	));
?>
    
<p class="rest-pc-product">
    <button class="resetProducToSimple btn" data-id="<?php echo $post->ID; ?>" data-type="<?php echo get_post_meta($post->ID, '_pc_measurement_type', true); ?>" type="button" data-toggle="tooltip" data-placement="top" title="<?php _e('By Clicking this you can rest this product to normal..!',''); ?>">
        <i class="fa fa-refresh fa-spin"></i>
        <?php _e(' Reset to Normal', 'extendons-price-calculator')?>
    </button>
</p>
