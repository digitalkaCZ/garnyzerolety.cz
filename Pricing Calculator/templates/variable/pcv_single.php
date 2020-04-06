<div class="container bootstrap-iso"> 
    <table id="variable_product_table" style="display: none;" class="table borderless">
        <tr>
            <td>
                <div class="form-group">
                    <label><?php _e(get_post_meta($post->ID, '_ext_'.$measurement_type.'_field_meta', true), 'extendons-price-calculator'); ?></label>
                </div>
            </td>
            <td class="price_calculation">
                <div class="form-group">
                    <input class="form-control" name="pcv_quantity_needed" required oninput="getRequiredQty()" type="text" id="input_qty">
                    <input type="hidden" value="pcv_<?php echo $measurement_type; ?>_type" name="pcv_product_type">
                </div>
            </td>   
        </tr>
        <tr>
            <td>
                <label>
                    <?php echo _e('Total Price', 'extendons-price-calculator'); ?>
                </label>
            </td>
            <td class="price_calculation">
                <span class="totalprice" id="totalprice"></span>
            </td>   
        </tr>
    </table>
</div>