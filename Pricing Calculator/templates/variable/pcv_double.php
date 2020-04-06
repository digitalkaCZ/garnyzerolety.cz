<div class="container bootstrap-iso">         
  <table id="variable_product_table" style="display: none;" class="table borderless">
    <tbody>
        <tr>
            <td>
                <div class="form-group">
                    <label><?php _e($length_meta, 'extendons-price-calculator'); ?></label>
                </div>    
            </td>
            <td class="price_calculation">
                <div class="form-group">
                    <input name="vlength_qty_area" oninput="getRequiredQty()" class="form-control pc_calculator_item_input" id="vlength_qty_area" required type="text">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="form-group">
                    <label><?php _e($width_meta, 'extendons-price-calculator'); ?></label>
                </div>   
            </td>
            <td class="price_calculation">
                <div class="form-group">
                    <input name="vwidth_qty_area" oninput="getRequiredQty()" class="form-control pc_calculator_item_input" id="vwidth_qty_area" required type="text">
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    <?php _e('Total Area', 'extendons-price-calculator'); ?>
                </label>
            </td>
            <td class="price_calculation">
                <span id="result" class="ext_amount"></span>
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    <?php _e('Product Price', 'extendons-price-calculator'); ?>
                </label>
            </td>
            <td class="price_calculation">        
                <span class="ext_amount">
                    <span id="ext_amount">
                    </span>
                </span>
                <input name="pcv_product_type" type="hidden" value="pcv_<?php echo $measurement_type; ?>_type" />   
                <input name="pcv_quantity_needed" type="hidden" id="pcv_quantity_needed" />  
            </td>
        </tr> 
    </tbody>
  </table>
</div>

<?php do_action('pc_show_short_description'); ?>


