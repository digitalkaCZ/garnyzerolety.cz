checkConfiguratorRequiredFieldsAreValid = function() {
    var form = jQuery('form.pc_add_to_cart_form');
    var isValid = true;

    if (jQuery('#minimum').length > 0 && jQuery('#maximum').length > 0 && jQuery('#pc_quantity_needed').length > 0) {
        var min = jQuery('#minimum').val();
        var max = jQuery('#maximum').val();
        var current = jQuery('#pc_quantity_needed').val();

        if (parseInt(min) < parseInt(current) && parseInt(max) > parseInt(current)) {
        } else {
            isValid = false;
        }

        if (getPcCalculatedPrice() === 0) {
            isValid = false;
        }
    }

    if (jQuery('#length_qty_area').length > 0 && jQuery('#width_qty_area').length > 0) {
        var area_length = jQuery('#length_qty_area').val();
        var area_width = jQuery('#width_qty_area').val();

        if (area_length === '' || area_width === '') {
            isValid = false;
        }

        if (getPcCalculatedPrice() === 0) {
            isValid = false;
        }
    }

    if (jQuery('#length_qty_max').length > 0 && jQuery('#width_qty_max').length > 0) {
        var max_length = jQuery('#length_qty_max').val();
        var max_width = jQuery('#width_qty_max').val();

        if (max_length === '' || max_width === '') {
            isValid = false;
        }

        if (getPcCalculatedPrice() === 0) {
            isValid = false;
        }
    }

    var inputGroups = form.find('.eocustomgroup > label .required').closest('.eocustomgroup');
    if(inputGroups.length > 0) {
        jQuery.each(inputGroups, function (key, inputGroup) {
            if (jQuery(inputGroup).attr('disabled') !== 'disabled') {
                var radioInputs = jQuery(inputGroup).find(':input:radio:checked[name$="[value]"]');
                var checkboxInputs = jQuery(inputGroup).find(':input:checkbox:checked[name$="[value]"]');
                var textInputs = jQuery(inputGroup).find(':input[type="text"][name$="[value]"]');
                var textareaInputs = jQuery(inputGroup).find(':input[type="textarea"][name$="[value]"]');
                var selectInputs = jQuery(inputGroup).find(':input[type="select"][name$="[value]"]').not(':selected');
                if (radioInputs.length === 0 &&
                    checkboxInputs.length === 0 &&
                    (textInputs.length === 0 || (textInputs.val() !== 'undefined' && textInputs.val() === '')) &&
                    (textareaInputs.length === 0 || (textareaInputs.val() !== 'undefined' && textareaInputs.val() === '')) &&
                    (selectInputs.length === 0 || (selectInputs.val() !== 'undefined' && selectInputs.val() === ''))
                ) {
                    isValid = false;
                }
            }
        });
    }

    return isValid;
};

makeDelay = function(ms) {
    var timer = 0;
    return function(callback){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
};

runRequiredFieldsChecker = function() {
    var isValid = checkConfiguratorRequiredFieldsAreValid();
    if( !isValid ){
        jQuery('.single_add_to_cart_button').attr("disabled", "disabled");
    } else {
        jQuery('.single_add_to_cart_button').removeAttr("disabled");
    }
};

checkConfiguratorRequiredFieldsAreValidOnFormChange = function() {
    var product_options_fields = jQuery(':input[name^="product_options"]');
    if (product_options_fields.length > 0) {
         product_options_fields.on('change paste keyup', function (event) {
             makeDelay(250)(runRequiredFieldsChecker);
        });
    }
    var calculator_fields = jQuery('#pc_product_type_table :input[type="text"]');
    if (calculator_fields.length > 0) {
        calculator_fields.on('change paste keyup', function (event) {
            makeDelay(250)(runRequiredFieldsChecker);
        });
    }
};
checkConfiguratorRequiredFieldsAreValidOnFormChange();

updatePcCalculatedPrice = function(price) {
    jQuery('input[name="pc_calculated_price"]').val(price);
    makeDelay(250)(runRequiredFieldsChecker);
};

getPcCalculatedPrice = function() {
    var price = jQuery('input[name="pc_calculated_price"]').val();
    if (price === '' || isNaN(price) ) {
        price = 0;
    }
    return parseFloat(price);
};

getOptionPrice = function() {
    var price = jQuery('input[name="mmoptionprice"]').val();
    if (price === '' && isNaN(price) ) {
        price = 0;
    }
    return parseFloat(price);
};

getBaseProductPrice = function() {
    var price = jQuery('#product_options_total').attr('product-price');
    if (price === '' && isNaN(price) ) {
        price = 0;
    }
    return parseFloat(price);
};

finalTotalPriceExists = function() {
    return (jQuery('.finalprice').length > 0);
};

optionPriceExists = function() {
    return (jQuery('.optionprice').length > 0);
};

updateFinalTotalPrice = function() {
    var final_total = 0;
    if (finalTotalPriceExists() && optionPriceExists()) {
        var pc_calculated_price = getPcCalculatedPrice();
        var base_product_price = getBaseProductPrice();
        var options_price = getOptionPrice();
        final_total = parseFloat(base_product_price) + parseFloat(options_price) + parseFloat(pc_calculated_price);
    }

    var price_form = pc_var_arguments.curr_pos;
    var op_price = "";
    if(price_form == 'left') {

        op_price = accounting.formatMoney(pc_calculated_price, {
                symbol: "Kč",
                format: "%s%v" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

        fi_price = accounting.formatMoney(final_total, {
                symbol: "Kč",
                format: "%s%v" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

    } else if(price_form == 'left_space') {

        op_price = accounting.formatMoney(pc_calculated_price, {
                symbol: "Kč",
                format: "%s %v" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

        fi_total = accounting.formatMoney(final_total, {
                symbol: "Kč",
                format: "%s %v" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

    } else if(price_form == 'right') {

        op_price = accounting.formatMoney(pc_calculated_price, {
                symbol: "Kč",
                format: "%v%s" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

        fi_total = accounting.formatMoney(final_total, {
                symbol: "Kč",
                format: "%v%s" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

    } else if(price_form == 'right_space') {

        op_price = accounting.formatMoney(pc_calculated_price, {
                symbol: "Kč",
                format: "%v %s" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

        fi_total = accounting.formatMoney(final_total, {
                symbol: "Kč",
                format: "%v %s" },
            pc_var_arguments.pc_decimal,
            pc_var_arguments.pc_thou_sep,
            pc_var_arguments.pc_decimal_sep
        ); // €4.999,99

    }

    // attached with id
    jQuery('#ext_amount').html(op_price);
    jQuery('.finalprice').html(fi_total);
};

jQuery(function () {
    jQuery("#calculate").on('click',function (event) {
        var min= jQuery('#minimum').val();
        var max= jQuery('#maximum').val();
        var current= jQuery('#pc_quantity_needed').val();

        // if( parseInt(min) < parseInt(current) && parseInt(max) > parseInt(current) && checkConfiguratorRequiredFieldsAreValid() ){
        if( checkConfiguratorRequiredFieldsAreValid() ){
            jQuery('.single_add_to_cart_button').removeAttr("disabled");
        } else {
            jQuery('.single_add_to_cart_button').attr("disabled", "disabled");
        }

        // if first enter period prevent and empty the textbox
        // if(jQuery(this).val() == '.'){
        //    jQuery(this).val('');  
        // }

        // // only allow one period at a time
        // if(jQuery(this).val().indexOf('.') !== -1 && event.which == 190)
        //     event.preventDefault();

        // // only allow to enter numbers and period
        // if ((event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which == 8 || event.which == 9 || event.which == 37 || event.which == 39 || event.which == 46 || event.which == 190) {
        // } else {
        //     event.preventDefault();
        // }

        // else get the value and pass it to function for calculation
        var value = jQuery('#pc_quantity_needed').val();
        jQuery('#ext_amount').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        
        // call the calculation function
        simple_measurement_calculation_callback(value.replace(",", "."));
         
    });
});

// ajax call for calculation
function simple_measurement_calculation_callback(quantity) { 
    var weight_product_id = jQuery("#pc_against_postid").val();
    var condition = 'weight_base_condition'; 
    jQuery.ajax({
        url:  pc_var_arguments.ajax_url, 
        type : 'post',
        dataType: 'json',
        data : {
            action : 'weight_action_ajax',
            condition :condition,
            quantity : quantity,
            weight_product_id : weight_product_id,
        },
        success : function(response) {
            updatePcCalculatedPrice(response);// (7.2.2020)
            updateFinalTotalPrice(); // (7.2.2020)
            // (7.2.2020)
            /*var price_form = pc_var_arguments.curr_pos;
            var op_price = "";
            if(price_form == 'left') {
                
                op_price = accounting.formatMoney(response, { 
                    symbol: "Kč",
                    format: "%s%v" },

                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99
            
            } else if(price_form == 'left_space') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%s %v" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v%s" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right_space') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v %s" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99
            
            }

            // attached with id
            jQuery('#ext_amount').html(op_price.replace(".", ","));*/
        }
    }); 
};


// ---------------------------------------
// --------------- - - - Area box by tiles 
// ---------------------------------------
jQuery(function () {
    jQuery("#length_qty, #width_qty").on('input keydown change keypress',function (event) {

        // disabled the input if its boxtype product
        jQuery(".qty").prop('disabled', true);
        // getting the area with length and width
        var area_length = document.getElementById('length_qty').value;    
        var area_width = document.getElementById('width_qty').value;

        // if first enter period prevent and empty the textbox
        if(jQuery(this).val() == '.'){
           jQuery(this).val('');  
        }

        // only allow one period at a time
        if(jQuery(this).val().indexOf('.') !== -1 && event.which == 190)
            event.preventDefault();

        // only allow to enter numbers and period
        if ((event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which == 8 || event.which == 9 || event.which == 37 || event.which == 39 || event.which == 46 || event.which == 190) {
        } else {
            event.preventDefault();
        }

        // total required area
        var box_required_are = area_length * area_width;
        // total box area
        var total_area = document.getElementById('_ext_box_area').value;
        
        // calcualting the number of box required
        if( box_required_are < total_area ) {
            box_required_are = total_area;
        } else if (box_required_are > total_area) {
            var reminder = box_required_are/total_area;
            rminde = reminder.toString().split(".")[0]; 
            var once = total_area * rminde;
            box_required_are = +once + +total_area;
            box_required_are = box_required_are.toFixed(2);
        }
        // get the totla box numbers
        var qtytoi = Math.round(box_required_are/total_area);
        jQuery(".qty").add(qtytoi);
        jQuery(".qty").val(qtytoi);
        jQuery('#result').html(box_required_are);
        jQuery('#pc_quantity_needed').attr('value',qtytoi);
        jQuery('#ext_amount').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        get_item_quantity_box(qtytoi);

    });
});

function get_item_quantity_box(quantity) {
    var product_id = jQuery("#pc_against_postid").val();
    var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
    var condition = 'adv_boxtiles_product_condition';
    jQuery.ajax({
        url : pc_var_arguments.ajax_url,
        type : 'post',
        data : {
            action : 'boxtiles_action_ajax',
            condition :condition,
            quantity : quantity,
            product_id : product_id,
        },
        success : function( response ) {
            updatePcCalculatedPrice(response);// (7.2.2020)
            updateFinalTotalPrice(); // (7.2.2020)
            // (7.2.2020)
            /*var price_form = pc_var_arguments.curr_pos;
            var op_price = "";
            if(price_form == 'left') {
                
                op_price = accounting.formatMoney(response, { 
                    symbol: "Kč",
                    format: "%s%v" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99
            
            } else if(price_form == 'left_space') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%s %v" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v%s" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right_space') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v %s" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99
            
            }

            // // attached with id
            jQuery('#ext_amount').html(response);*/
        }
    });  
    
}



// ---------------------------------------
// --------------- - - - Area length*width,,,
// ---------------------------------------
jQuery(function () {
    jQuery("#calculatee").on('click',function (event) {
        // getting the area with length and width
        var area_length = document.getElementById('length_qty_area').value.replace(",", ".");    
        var area_width = document.getElementById('width_qty_area').value.replace(",", ".");

       // if first enter period prevent and empty the textbox
        // if(jQuery(this).val() == '.'){
        //    jQuery(this).val('');  
        // }

        // // only allow one period at a time
        // if(jQuery(this).val().indexOf('.') !== -1 && event.which == 190)
        //     event.preventDefault();

        // // only allow to enter numbers and period
        // if ((event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which == 8 || event.which == 9 || event.which == 37 || event.which == 39 || event.which == 46 || event.which == 190) {
        // } else {
        //     event.preventDefault();
        // }

        // if(area_length != "" && area_width != "" && checkConfiguratorRequiredFieldsAreValid()){
        if( checkConfiguratorRequiredFieldsAreValid() ){
            jQuery('.single_add_to_cart_button').removeAttr("disabled");
        }else  {
            jQuery('.single_add_to_cart_button').attr("disabled", "disabled");
        }

        var total_l = area_length.replace(",", ".");
        var total_w = area_width.replace(",", ".");
        var total_area= total_l * total_w;
        jQuery('#result').html(total_area);
        jQuery('#pc_quantity_needed').attr('value',total_area);
        var quantity = jQuery("#pc_quantity_needed").val();
        jQuery('#ext_amount').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        get_item_quantity(quantity); 


    });
});

function get_item_quantity(quantity) {
    var product_id = jQuery("#pc_against_postid").val();
    var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
    var condition = 'area_lw_product_condition';
    jQuery.ajax({
        url : pc_var_arguments.ajax_url,
        type : 'post',
        data : {
            action : 'arealw_action_ajax',
            condition :condition,
            quantity : quantity,
            product_id : product_id,
        },
        success : function( response ) {
            updatePcCalculatedPrice(response);// (7.2.2020)
            updateFinalTotalPrice(); // (7.2.2020)
            // (7.2.2020)
            /*var price_form = pc_var_arguments.curr_pos;
            var op_price = "";
            if(price_form == 'left') {
                
                op_price = accounting.formatMoney(response, { 
                    symbol: "Kč",
                    format: "%s%v" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99
            
            } else if(price_form == 'left_space') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%s %v" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v%s" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right_space') {
                
                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v %s" }, 
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99
            
            }
            // attached with id
            jQuery('#ext_amount').html(op_price);*/
        }
    });  
    
}


// ---------------------------------------
// --------------- - - - Room Walls length*width,,,
// ---------------------------------------
jQuery(function () {
    jQuery("#length_qty_wall, #width_qty_wall").on('input keydown change keypress',function (event) {
        
        // getting the area with length and width
        var wall_length = document.getElementById('length_qty_wall').value;    
        var wall_width = document.getElementById('width_qty_wall').value;

        // if first enter period prevent and empty the textbox
        if(jQuery(this).val() == '.'){
           jQuery(this).val('');  
        }

        // only allow one period at a time
        if(jQuery(this).val().indexOf('.') !== -1 && event.which == 190)
            event.preventDefault();

        // only allow to enter numbers and period
        if ((event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which == 8 || event.which == 9 || event.which == 37 || event.which == 39 || event.which == 46 || event.which == 190) {
        } else {
            event.preventDefault();
        }

        var total_area = wall_length * wall_width;
        jQuery('#result').html(total_area);
        jQuery('#pc_quantity_needed').attr('value',total_area);
        var quantity = jQuery("#pc_quantity_needed").val();
        jQuery('#ext_amount').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        get_item_quantity(quantity);

    });
});

function get_item_quantity(quantity) {
    var product_id = jQuery("#pc_against_postid").val();
    var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
    var condition = 'roomwall_product_condition';
    jQuery.ajax({
        url : pc_var_arguments.ajax_url,
        type : 'post',
        data : {
            action : 'roomwall_action_ajax',
            condition :condition,
            quantity : quantity,
            product_id : product_id,
        },
        success : function( response ) {
            updatePcCalculatedPrice(response);// (7.2.2020)
            updateFinalTotalPrice(); // (7.2.2020)
            // (7.2.2020)
            /*var price_form = pc_var_arguments.curr_pos;
            var op_price = "";
            if(price_form == 'left') {

                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%s%v" },
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'left_space') {

                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%s %v" },
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right') {

                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v%s" },
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right_space') {

                op_price = accounting.formatMoney(response, {
                    symbol: "Kč",
                    format: "%v %s" },
                        pc_var_arguments.pc_decimal,
                        pc_var_arguments.pc_thou_sep,
                        pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            }
            // attached with id
            jQuery('#ext_amount').html(op_price);*/
        }
    });

}


// ---------------------------------------
// --------------- - - - volume advanced mulch product,,,
// ---------------------------------------
jQuery(function () {
    jQuery("#length_qty_vol, #width_qty_vol, #height_qty_vol").on('input keydown change keypress',function (event) {

        // getting the area with length and width
        var length_voladv = document.getElementById('length_qty_vol').value;    
        var width_voladv = document.getElementById('width_qty_vol').value;
        var height_voladv = document.getElementById('height_qty_vol').value;

        // if first enter period prevent and empty the textbox
        if(jQuery(this).val() == '.'){
           jQuery(this).val('');  
        }

        // only allow one period at a time
        if(jQuery(this).val().indexOf('.') !== -1 && event.which == 190)
            event.preventDefault();

        // only allow to enter numbers and period
        if ((event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which == 8 || event.which == 9 || event.which == 37 || event.which == 39 || event.which == 46 || event.which == 190) {
        } else {
            event.preventDefault();
        }


        var inch_to_feet  = height_voladv / 12;

        var total_to_three = inch_to_feet * width_voladv * length_voladv ;
        var totay_cu_yad = total_to_three / 27;
        var net_cubeyard = totay_cu_yad.toFixed(3);
        jQuery('#result').html(net_cubeyard);
        jQuery('#pc_quantity_needed').attr('value',net_cubeyard);
        var quantity = jQuery("#pc_quantity_needed").val();
        jQuery('#ext_amount').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        get_item_quantity(quantity);

    });
});

function get_item_quantity(quantity) {
    var product_id = jQuery("#pc_against_postid").val();
    var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
    var condition = 'volumed_product_condition';
    jQuery.ajax({
        url : pc_var_arguments.ajax_url,
        type : 'post',
        data : {
            action : 'volumed_action_ajax',
            condition :condition,
            quantity : quantity,
            product_id : product_id,
        },
        success : function( response ) {
            updatePcCalculatedPrice(response);// (7.2.2020)
            updateFinalTotalPrice(); // (7.2.2020)
            // (7.2.2020)
            /*var price_form = pc_var_arguments.curr_pos;
            var op_price = "";
            if(price_form == 'left') {

                op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s%v" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'left_space') {

                op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right') {

                op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right_space') {

                op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            }
            // attached with id
            jQuery('#ext_amount').html(op_price);*/
        }
    });

}


// ---------------------------------------
// --------------- - - - Max length & width,,,
// ---------------------------------------
jQuery(function () {
    jQuery("#calculate_max_dimessions").on('click',function (event) {
        // getting the area with length and width
        var max_length = document.getElementById('length_qty_max').value.replace(",", ".");
        var max_width = document.getElementById('width_qty_max').value.replace(",", ".");

        // if first enter period prevent and empty the textbox
        // if(jQuery(this).val() == '.'){
        //    jQuery(this).val('');
        // }

        // // only allow one period at a time
        // if(jQuery(this).val().indexOf('.') !== -1 && event.which == 190)
        //     event.preventDefault();

        // // only allow to enter numbers and period
        // if ((event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which == 8 || event.which == 9 || event.which == 37 || event.which == 39 || event.which == 46 || event.which == 190) {
        // } else {
        //     event.preventDefault();
        // }

        // if(max_length !== "" && max_width !== "" && checkConfiguratorRequiredFieldsAreValid()){
        if( checkConfiguratorRequiredFieldsAreValid() ){
            jQuery('.single_add_to_cart_button').removeAttr("disabled");
        }else  {
            jQuery('.single_add_to_cart_button').attr("disabled", "disabled");
        }

        var total_l = max_length.replace(",", ".");
        var total_w = max_width.replace(",", ".");
        var total_area= total_l * total_w;
        jQuery('#result').html(total_area);
        jQuery('#pc_quantity_needed').attr('value',total_area);
        var quantity = jQuery("#pc_quantity_needed").val();
        jQuery('#ext_amount').html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i>');
        get_item_quantity_max_lw(quantity);
    });
});

jQuery("#length_qty_max, #width_qty_max").on('keyup keydown keypress', function(){
    replace_comma_with_dot(jQuery(this));
});

function replace_comma_with_dot(element) {
    var val = element.val();
    val = val.replace(/,/g, '.');
    element.val(val);
}

function get_item_quantity_max_lw(quantity) {
    var product_id = jQuery("#pc_against_postid").val();

    replace_comma_with_dot(jQuery("#length_qty_max"));
    replace_comma_with_dot(jQuery("#width_qty_max"));

    var length = jQuery("#length_qty_max").val();
    var width = jQuery("#width_qty_max").val();

    jQuery("#length_qty_max").parent().find('span.error').remove();
    jQuery("#width_qty_max").parent().find('span.error').remove();

    var condition = 'max_lw_product_condition';
    jQuery.ajax({
        url : pc_var_arguments.ajax_url,
        type : 'post',
        dataType: 'json',
        data : {
            action : 'maxlw_action_ajax',
            condition :condition,
            quantity : quantity,
            product_id : product_id,
            length : length,

            width : width,
        },
        success : function( response ) {

            jQuery('.single_add_to_cart_button').removeAttr("disabled");

            if (typeof response.error_length !== 'undefined') {
                jQuery("#length_qty_max").parent().append('<span class="error">'+response.error_length+'</span>');
            }

            if (typeof response.error_width !== 'undefined') {
                jQuery("#width_qty_max").parent().append('<span class="error">'+response.error_width+'</span>');
            }

            if (typeof response.error_length !== 'undefined' || typeof response.error_width !== 'undefined' || !checkConfiguratorRequiredFieldsAreValid()) {
                jQuery('.single_add_to_cart_button').attr("disabled", "disabled");
            }

            //jQuery('input[name="pc_max_lw_price"]').val(response.price); // (7.2.2020)
            updatePcCalculatedPrice(response.price); // (7.2.2020)
            updateFinalTotalPrice(); // (7.2.2020)
            // (7.2.22020)
            /*var price_form = pc_var_arguments.curr_pos;
            var op_price = "";
            if(price_form == 'left') {

                op_price = accounting.formatMoney(response.price, {
                        symbol: "Kč",
                        format: "%s%v" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

                fi_price = accounting.formatMoney(final_total, {
                        symbol: "Kč",
                        format: "%s%v" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'left_space') {

                op_price = accounting.formatMoney(response.price, {
                        symbol: "Kč",
                        format: "%s %v" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

                fi_total = accounting.formatMoney(final_total, {
                        symbol: "Kč",
                        format: "%s %v" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right') {

                op_price = accounting.formatMoney(response.price, {
                        symbol: "Kč",
                        format: "%v%s" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

                fi_total = accounting.formatMoney(final_total, {
                        symbol: "Kč",
                        format: "%v%s" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            } else if(price_form == 'right_space') {

                op_price = accounting.formatMoney(response.price, {
                        symbol: "Kč",
                        format: "%v %s" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

                fi_total = accounting.formatMoney(final_total, {
                        symbol: "Kč",
                        format: "%v %s" },
                    pc_var_arguments.pc_decimal,
                    pc_var_arguments.pc_thou_sep,
                    pc_var_arguments.pc_decimal_sep
                ); // €4.999,99

            }
            // attached with id
            jQuery('#ext_amount').html(op_price);
            jQuery('.finalprice').html(fi_total);*/
        }
    });

}