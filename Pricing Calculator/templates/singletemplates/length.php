<div class="container bootstrap-iso">         
  <table id="pc_product_type_table" class="table borderless">
    <tbody>
        <tr>
            <td>
                <div class="form-group">
                    <label for="pc_quantity_needed"><?php _e($field_meta, 'extendons-price-calculator'); ?></label>
                    <div class="form-group omezeni">
                    <i style="font-size: 11px;">Minimální délka: <?php echo get_post_meta( $post->ID, '_pc_minimum_quantity', true );?> cm <br> Maximální délka: <?php echo get_post_meta( $post->ID, '_pc_maximum_quantity', true );?> cm </i>
                </div> </div>   
            </td>
            <td>
                <div class="form-group">
                    <input class="form-control pc_calculator_item_input" name="pc_quantity_needed" id="pc_quantity_needed" required type="text">
                    <?php if(get_post_meta( $post->ID, '_pc_minimum_quantity', true ) !="" &&  get_post_meta( $post->ID, '_pc_maximum_quantity', true ) != ""){ ?>
                    <?php }?>
                    <input name="product_id" type="hidden" id="pc_against_postid"  value="<?php echo $post->ID; ?>">
                     <input name="minimum" type="hidden" id="minimum"  value="<?php echo get_post_meta( $post->ID, '_pc_minimum_quantity', true ); ?>">
                      <input name="maximum" type="hidden" id="maximum"  value="<?php echo get_post_meta( $post->ID, '_pc_maximum_quantity', true ); ?>">
                    <input name="pc_product_type" type="hidden" value="pc_<?php echo $measurement_type; ?>_product" / >
                    <input name="pc_calculated_price" type="hidden"  value=""><!-- (7.2.2020) -->
                </div>
            </td>
        </tr>
        <tr>
            
            <td colspan="2" style="text-align: right;">
                 <button type="button" id="calculate">Vypočítat</button> 
            </td>
        </tr>
        <tr>
            <td>
                <label>
                    <?php _e('Cena produktu', 'extendons-price-calculator'); ?>
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
<script type="text/javascript">
    jQuery(document).ready(function(){
         jQuery('.single_add_to_cart_button').attr("disabled", "disabled");
    });
</script>