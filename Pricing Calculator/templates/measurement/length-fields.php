<?php
	woocommerce_wp_checkbox( array(
		'id' 			=> '_ext_length_price',
		'label' 		=> __( 'Show Product Price Per Unit', 'extendons-price-calculator', 'extendons-price-calculator' ),
		'description' 	=> __('Check this box to display product pricing per unit on the frontend. Also Enable calculated price Fields', 'extendons-price-calculator'),
		'value' 		=> get_post_meta($post->ID, '_ext_length_price_meta', true)
	));

	echo '<hr>';

	woocommerce_wp_text_input( array(
		'id'			=> '_ext_length_label',
		'label'			=> __( 'Length Pricing Label', 'extendons-price-calculator' ),
		'desc_tip'		=> 'true',
		'description'	=> __( 'Label to display next to the product price (defaults to pricing unit)', 'extendons-price-calculator' ),
		'type' 			=> 'text',
		'value'			=> get_post_meta($post->ID, '_ext_length_label_meta', true)
	));

	woocommerce_wp_text_input( array(
		'id'			=> '_ext_length_field',
		'label'			=> __( 'Length Label', 'extendons-price-calculator' ),
		'desc_tip'		=> 'true',
		'description'	=> __( 'Length input field label to display on the frontend', 'extendons-price-calculator' ),
		'type' 			=> 'text',
		'value'			=> get_post_meta($post->ID, '_ext_length_field_meta', true)
	));	

	woocommerce_wp_select( array( 
		'id'      		=> '_ext_length_unit', 
		'label'   		=> __( 'Length Unit', 'extendons-price-calculator' ), 
		'desc_tip'		=> 'true',
		'description'	=> __( 'Unit to define pricing in', 'extendons-price-calculator' ),
		'options' 		=> array(
			'' 			=> __('None','extendons-price-calculator'),
			'mm' 		=> __('mm','extendons-price-calculator'),
			'cm'   		=> __( 'cm', 'extendons-price-calculator' ),
			'm'   		=> __( 'm', 'extendons-price-calculator' ),
			'km'   		=> __( 'km', 'extendons-price-calculator' ),
			'in'   		=> __( 'in', 'extendons-price-calculator' ),
			'ft'   		=> __( 'ft', 'extendons-price-calculator' ),
			'yd'   		=> __( 'yd', 'extendons-price-calculator' ),
			'mi'   		=> __( 'mi', 'extendons-price-calculator' )
		),
		'value'			=> get_post_meta($post->ID, '_ext_length_unit_meta', true)
	));

?>
<p class="rest-pc-product">
    <button class="resetProducToSimple btn" data-id="<?php echo $post->ID; ?>" data-type="<?php echo get_post_meta($post->ID, '_pc_measurement_type', true); ?>" type="button" data-toggle="tooltip" data-placement="top" title="<?php _e('By Clicking this you can rest this product to normal..!',''); ?>">
        <i class="fa fa-refresh fa-spin"></i>
        <?php _e(' Reset to Normal', 'extendons-price-calculator')?>
    </button>
</p>
