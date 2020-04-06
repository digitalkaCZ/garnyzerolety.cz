
if(pcv_var_arguments.pcv_type == 'weight') {

    //-------------------------------
    // Variable for single dimenssion
    //-------------------------------
    jQuery(document).ready(function($) {
        jQuery('select').blur( function(){
            var variation_id = jQuery('input.variation_id').val();
            if( variation_id ) {
                getRequiredQty(variation_id);
                jQuery('#variable_product_table').css("display", "block");
            } else {
                jQuery('#variable_product_table').css("display", "none");
            }     
        });
    });
    jQuery(function () {
        jQuery("#input_qty").on('input keydown change keypress ',function (event) {
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
        });
    });
    function getRequiredQty(variation_id) {
        var qtyRequired = document.getElementById('input_qty').value;
        if(variation_id) {
            var var_id = variation_id;
        } else {
            var var_id = jQuery('input.variation_id').val();
        }
        final_ajax_callback(var_id,qtyRequired);
    }
    function final_ajax_callback(variable_id, total_value) {
        var condition = 'variabe_simple_products_condition';
        jQuery.ajax({
            url : pcv_var_arguments.vajax_url,
            type : 'post',
            data : {
                action : 'variable_simple_product_action',
                condition :condition,
                variable_id : variable_id,
                total_value : total_value,
            },
            success : function( response ) {
                var price_form = pcv_var_arguments.vcurr_pos;
                var op_price = "";
                if(price_form == 'left') {
                    
                    op_price = accounting.formatMoney(response, { 
                        symbol: "Kč",
                        format: "%s%v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                } else if(price_form == 'left_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                }
                // // attached with id
                jQuery('#totalprice').html(op_price);
            }
        });  
    }

} else if(pcv_var_arguments.pcv_type == 'area') {


    //-------------------------------
    // Variable for single dimenssion
    //-------------------------------
    jQuery(document).ready(function($) {
        jQuery('select').blur( function(){
            var variation_id = jQuery('input.variation_id').val();
            if( variation_id ) {
                getRequiredQty(variation_id);
                jQuery('#variable_product_table').css("display", "block");
            } else {
                jQuery('#variable_product_table').css("display", "none");
            }     
        });
    });
    jQuery(function () {
        jQuery("#input_qty").on('input keydown change keypress ',function (event) {
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
        });
    });
    function getRequiredQty(variation_id) {
        var qtyRequired = document.getElementById('input_qty').value;
        if(variation_id) {
            var var_id = variation_id;
        } else {
            var var_id = jQuery('input.variation_id').val();
        }
        final_ajax_callback(var_id,qtyRequired);
    }
    function final_ajax_callback(variable_id, total_value) {
        var condition = 'variabe_simple_products_condition';
        jQuery.ajax({
            url : pcv_var_arguments.vajax_url,
            type : 'post',
            data : {
                action : 'variable_simple_product_action',
                condition :condition,
                variable_id : variable_id,
                total_value : total_value,
            },
            success : function( response ) {
                var price_form = pcv_var_arguments.vcurr_pos;
                var op_price = "";
                if(price_form == 'left') {
                    
                    op_price = accounting.formatMoney(response, { 
                        symbol: "Kč",
                        format: "%s%v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                } else if(price_form == 'left_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                }
                // // attached with id
                jQuery('#totalprice').html(op_price);
            }
        });  
    }


} else if(pcv_var_arguments.pcv_type == 'volume') {


    //-------------------------------
    // Variable for single dimenssion
    //-------------------------------
    jQuery(document).ready(function($) {
        jQuery('select').blur( function(){
            var variation_id = jQuery('input.variation_id').val();
            if( variation_id ) {
                getRequiredQty(variation_id);
                jQuery('#variable_product_table').css("display", "block");
            } else {
                jQuery('#variable_product_table').css("display", "none");
            }     
        });
    });
    jQuery(function () {
        jQuery("#input_qty").on('input keydown change keypress ',function (event) {
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
        });
    });
    function getRequiredQty(variation_id) {
        var qtyRequired = document.getElementById('input_qty').value;
        if(variation_id) {
            var var_id = variation_id;
        } else {
            var var_id = jQuery('input.variation_id').val();
        }
        final_ajax_callback(var_id,qtyRequired);
    }
    function final_ajax_callback(variable_id, total_value) {
        var condition = 'variabe_simple_products_condition';
        jQuery.ajax({
            url : pcv_var_arguments.vajax_url,
            type : 'post',
            data : {
                action : 'variable_simple_product_action',
                condition :condition,
                variable_id : variable_id,
                total_value : total_value,
            },
            success : function( response ) {
                var price_form = pcv_var_arguments.vcurr_pos;
                var op_price = "";
                if(price_form == 'left') {
                    
                    op_price = accounting.formatMoney(response, { 
                        symbol: "Kč",
                        format: "%s%v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                } else if(price_form == 'left_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                }
                // // attached with id
                jQuery('#totalprice').html(op_price);
            }
        });  
    }


} else if(pcv_var_arguments.pcv_type == 'length') {


    //-------------------------------
    // Variable for single dimenssion
    //-------------------------------
    jQuery(document).ready(function($) {
        jQuery('select').blur( function(){
            var variation_id = jQuery('input.variation_id').val();
            if( variation_id ) {
                getRequiredQty(variation_id);
                jQuery('#variable_product_table').css("display", "block");
            } else {
                jQuery('#variable_product_table').css("display", "none");
            }     
        });
    });
    jQuery(function () {
        jQuery("#input_qty").on('input keydown change keypress ',function (event) {
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
        });
    });
    function getRequiredQty(variation_id) {
        var qtyRequired = document.getElementById('input_qty').value;
        if(variation_id) {
            var var_id = variation_id;
        } else {
            var var_id = jQuery('input.variation_id').val();
        }
        final_ajax_callback(var_id,qtyRequired);
    }
    function final_ajax_callback(variable_id, total_value) {
        var condition = 'variabe_simple_products_condition';
        jQuery.ajax({
            url : pcv_var_arguments.vajax_url,
            type : 'post',
            data : {
                action : 'variable_simple_product_action',
                condition :condition,
                variable_id : variable_id,
                total_value : total_value,
            },
            success : function( response ) {
                var price_form = pcv_var_arguments.vcurr_pos;
                var op_price = "";
                if(price_form == 'left') {
                    
                    op_price = accounting.formatMoney(response, { 
                        symbol: "Kč",
                        format: "%s%v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                } else if(price_form == 'left_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                }
                // // attached with id
                jQuery('#totalprice').html(op_price);
            }
        });  
    }


} else if(pcv_var_arguments.pcv_type == 'area_lw' ) {


    //-------------------------------
    // Variable for double dimenssion
    //-------------------------------
    jQuery(document).ready(function($) {
        jQuery('select').blur( function(){
            var variation_id = jQuery('input.variation_id').val();
            if( variation_id ) {
                getRequiredQty(variation_id);
                jQuery('#variable_product_table').css("display", "block");
            } else {
                jQuery('#variable_product_table').css("display", "none");
            }     
        });
    });
    jQuery(function () {
        jQuery("#vlength_qty_area, #vwidth_qty_area").on('input keydown change keypress ',function (event) {
            
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
        });
    });
    function getRequiredQty(variation_id) {
        var length = document.getElementById('vlength_qty_area').value;
        var width = document.getElementById('vwidth_qty_area').value;
        var total_value = length*width;
        jQuery('#result').html(total_value);
        jQuery('#pcv_quantity_needed').val(total_value);
        if(variation_id) {
            var var_id = variation_id;
        } else {
            var var_id = jQuery('input.variation_id').val();
        }
        final_ajax_callback_double(var_id,total_value);
    }
    function final_ajax_callback_double(variable_id, total_value) {
        var condition = 'variabe_double_products_condition';
        jQuery.ajax({
            url : pcv_var_arguments.vajax_url,
            type : 'post',
            data : {
                action : 'variable_double_product_action',
                condition :condition,
                variable_id : variable_id,
                total_value : total_value,
            },
            success : function( response ) {
                var price_form = pcv_var_arguments.vcurr_pos;
                var op_price = "";
                if(price_form == 'left') {
                    
                    op_price = accounting.formatMoney(response, { 
                        symbol: "Kč",
                        format: "%s%v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                } else if(price_form == 'left_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                }

                // attached with id
                jQuery('#ext_amount').html(op_price);
            }
        });  
    }


} else if(pcv_var_arguments.pcv_type == 'wall' ) {

    //-------------------------------
    // Variable for double dimenssion
    //-------------------------------
    jQuery(document).ready(function($) {
        jQuery('select').blur( function(){
            var variation_id = jQuery('input.variation_id').val();
            if( variation_id ) {
                getRequiredQty(variation_id);
                jQuery('#variable_product_table').css("display", "block");
            } else {
                jQuery('#variable_product_table').css("display", "none");
            }     
        });
    });
    jQuery(function () {
        jQuery("#vlength_qty_area, #vwidth_qty_area").on('input keydown change keypress ',function (event) {
            
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
        });
    });
    function getRequiredQty(variation_id) {
        var length = document.getElementById('vlength_qty_area').value;
        var width = document.getElementById('vwidth_qty_area').value;
        var total_value = length*width;
        jQuery('#result').html(total_value);
        jQuery('#pcv_quantity_needed').val(total_value);
        if(variation_id) {
            var var_id = variation_id;
        } else {
            var var_id = jQuery('input.variation_id').val();
        }
        final_ajax_callback_double(var_id,total_value);
    }
    function final_ajax_callback_double(variable_id, total_value) {
        var condition = 'variabe_double_products_condition';
        jQuery.ajax({
            url : pcv_var_arguments.vajax_url,
            type : 'post',
            data : {
                action : 'variable_double_product_action',
                condition :condition,
                variable_id : variable_id,
                total_value : total_value,
            },
            success : function( response ) {
                var price_form = pcv_var_arguments.vcurr_pos;
                var op_price = "";
                if(price_form == 'left') {
                    
                    op_price = accounting.formatMoney(response, { 
                        symbol: "Kč",
                        format: "%s%v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                } else if(price_form == 'left_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                }

                // attached with id
                jQuery('#ext_amount').html(op_price);
            }
        });  
    }


} else {


    //-------------------------------
    // Variable for volumen triple garden mulch dimenssion
    //-------------------------------
    jQuery(document).ready(function($) {
        jQuery('select').blur( function(){
            var variation_id = jQuery('input.variation_id').val();
            if( variation_id ) {
                getRequiredQty(variation_id);
                jQuery('#variable_product_table').css("display", "block");
            } else {
                jQuery('#variable_product_table').css("display", "none");
            }     
        });
    });
    jQuery(function () {
        jQuery("#vlength_qty_vol, #vwidth_qty_vol, #vheight_qty_vol").on('input keydown change keypress ',function (event) {
            
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
        });
    });
    function getRequiredQty(variation_id) {
        var length = document.getElementById('vlength_qty_vol').value;
        var width = document.getElementById('vwidth_qty_vol').value;
        var height = document.getElementById('vheight_qty_vol').value;
        
        var inch_to_feet  = height / 12;
        var total_to_three = inch_to_feet * length * width ;
        var totay_cu_yad = total_to_three / 27;
        var net_cubeyard = totay_cu_yad.toFixed(3);
        jQuery('#result').html(net_cubeyard);
        jQuery('#pcv_quantity_needed').attr('value',net_cubeyard);
        if(variation_id) {
            var var_id = variation_id;
        } else {
            var var_id = jQuery('input.variation_id').val();
        }
        final_ajax_callback_triple(var_id,net_cubeyard);
    }
    function final_ajax_callback_triple(variable_id, total_value) {
        var condition = 'variabe_vol3d_products_condition';
        jQuery.ajax({
            url : pcv_var_arguments.vajax_url,
            type : 'post',
            data : {
                action : 'variable_vol3d_product_action',
                condition :condition,
                variable_id : variable_id,
                total_value : total_value,
            },
            success : function( response ) {
                var price_form = pcv_var_arguments.vcurr_pos;
                var op_price = "";
                if(price_form == 'left') {
                    
                    op_price = accounting.formatMoney(response, { 
                        symbol: "Kč",
                        format: "%s%v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                } else if(price_form == 'left_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%s %v" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v%s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99

                } else if(price_form == 'right_space') {
                    
                    op_price = accounting.formatMoney(response, {
                        symbol: "Kč",
                        format: "%v %s" }, 
                            pcv_var_arguments.pcv_decimal,
                            pcv_var_arguments.pcv_thou_sep,
                            pcv_var_arguments.pcv_decimal_sep
                    ); // €4.999,99
                
                }

                // attached with id
                jQuery('#ext_amount').html(op_price);
            }
        });  
    }


} //end of last else




