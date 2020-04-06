<div class="container bootstrap-iso">         
  <table id="pc_product_type_table" class="table borderless">
    <tbody>
        <tr>
            <td>
                <div class="form-group">
                    <label for="pc_quantity_needed"><?php _e($field_meta, 'extendons-price-calculator'); ?></label>
                </div>    
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control pc_calculator_item_input" name="pc_quantity_needed" id="pc_quantity_needed" required type="text">
                    <input name="product_id" type="hidden" id="pc_against_postid"  value="<?php echo $post->ID; ?>">
                    <input name="pc_product_type" type="hidden" value="pc_<?php echo $measurement_type; ?>_product" / >
                    <input name="pc_calculated_price" type="hidden"  value=""><!-- (7.2.2020) -->
                </div>
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
            </td>
        </tr> 
    </tbody>
  </table>
</div>

<?php do_action('pc_show_short_description'); ?>