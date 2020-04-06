jQuery(document).ready(function (jQuery) {

    // selection of specific measurement unit
    jQuery(document).ready(function() {
        jQuery("#_select_measurement").change(function(){
            jQuery(this).find("option:selected").each(function(){
                if(jQuery(this).attr("value")=="weight"){
                    jQuery(".box").not(".weight").hide();
                    jQuery(".weight").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else if(jQuery(this).attr("value")=="area"){
                    jQuery(".box").not(".area").hide();
                    jQuery(".area").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else if(jQuery(this).attr("value")=="max_lw"){
                    jQuery(".box").not(".max_lw").hide();
                    jQuery(".max_lw").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else if(jQuery(this).attr("value")=="length"){
                    jQuery(".box").not(".v").hide();
                    jQuery(".length").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else if(jQuery(this).attr("value")=="volume"){
                    jQuery(".box").not(".volume").hide();
                    jQuery(".volume").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else if(jQuery(this).attr("value")=="volumeadv"){
                    jQuery(".box").not(".volumeadv").hide();
                    jQuery(".volumeadv").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else if(jQuery(this).attr("value")=="area_lw"){
                    jQuery(".box").not(".area_lw").hide();
                    jQuery(".area_lw").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else if(jQuery(this).attr("value")=="boxtiles"){
                    jQuery(".box").not(".boxtiles").hide();
                    jQuery(".boxtiles").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','none');
                    jQuery("p._pc_minimum_price_field").hide();
                }
                else if(jQuery(this).attr("value")=="wall"){
                    jQuery(".box").not(".wall").hide();
                    jQuery(".wall").show();
                    jQuery("#price_calculator_option ul li:nth-child(2) a").css('display','');
                    jQuery("p._pc_minimum_price_field").show();
                }
                else{
                    jQuery(".box").hide();
                }
            });
        }).change();
    });

    // adding row entries
    var ext_rang_count = 0;
    jQuery("#ext_add_new_rule_btn").click(function(){
        ext_rang_count ++;
        var newrow ="<tr id='pc_pricing_range_"+ext_rang_count+"'><td width='30%'><div class='form-group'><input step='any' min='0.001' name='price_table["+ext_rang_count+"][start_rang]' type='number' class='form-control' id='start_range' placeholder='Start Range'><input step='any' min='0.001' name='price_table["+ext_rang_count+"][end_rang]'type='number' class='form-control' id='end_range' placeholder='End Range'></div></td><td width='30%'><div class='form-group'><input name='price_table["+ext_rang_count+"][price_per_unit]' step='any' min='0.001' type='number' class='form-control' id='regular_price' placeholder='Regular Price'></div></td><td width='30%'><div class='form-group'><input name='price_table["+ext_rang_count+"][sale_price_per_unit]' step='any' min='0.001' type='number' class='form-control' id='sale_price' placeholder='Sale Price'></div></td><td width='10%'><div class='form-group'><button type='button' id='pc_pricing_range_"+ext_rang_count+"' onclick='unsaved_delete_range_row(this.id);' class='btn btn-danger btn-block'><i class='fa fa-trash' aria-hidden='true'></i> Delete</button></div></td></tr>";
        jQuery("table tbody.new_ext_row").append(newrow);
    });

    // tooltip
    jQuery(function () {
        jQuery('[data-toggle="tooltip"]').tooltip()
    });

    //reset the product
    jQuery('.resetProducToSimple').click(function(){
        var productID = jQuery(this).data('id');
        var producType = jQuery(this).data('type');
        var condition = 'resetProductNormal';
        jQuery.ajax({
            url : ajaxurl,
            type : 'post',
            data : {
                action : 'resetProductNormalCallback',
                condition :condition,
                productID : productID,
                producType : producType,
            },
            success : function( response ) {
                location.reload();
            }
        });  
    });

});

