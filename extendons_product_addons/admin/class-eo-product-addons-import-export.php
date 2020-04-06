<?php
// phpinfo();
if(!class_exists('EO_Product_Addons_Import_Export')) {
	class EO_Product_Addons_Import_Export extends EO_Product_Addons_Admin {

		function __construct() {
			add_action('wp_ajax_exportRules', array($this, 'export_all_rules'));
			add_action('wp_ajax_importRules', array($this, 'import_rules'));
			add_action('wp_ajax_deleteDir', array($this, 'delete_dir'));
			add_action('wp_ajax_deleteImportedfile', array($this, 'delete_imported_file'));
		}

		public function delete_imported_file() {

            $this->delTree($_POST['file']);
            die();
        }
 
		public function delete_dir() {

            $this->delTree($_POST['file']);
            die();
        }

		public function import_rules() {

			ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 6000000);
           
			$new = $_POST['is_new'];
           	foreach ($_FILES as $key => $value) {
                $file = $value['tmp_name'];
                $file_name = $value['name'];
           	}

           	$info = pathinfo($file);
            $info2 = pathinfo($file_name);
            $ext = $info2['extension'];

            if($ext == 'zip') {

                $zip = new ZipArchive();
                if($zip->open($file, ZIPARCHIVE::CREATE) !== true) throw new Exception('Unable to open zip file');

                $decompressFolder = EOPA_PLUGIN_DIR.'files/' . $info['filename'] . '/';
                $decompressFolder2 = EOPA_PLUGIN_DIR.'files/' . $info['filename'];


                if (!file_exists($decompressFolder) || (file_exists($decompressFolder) && !is_dir($decompressFolder))) {
                        if (!mkdir($decompressFolder, 0755)) throw new Exception('Unable to create folder for zip extraction');
                    }
                    
                if (!$zip->extractTo($decompressFolder)) throw new Exception('Unalbe to extract zip file');

                //check the files
                $files = scandir($decompressFolder);
                foreach ($files as $tempFile) {
                    if(in_array($tempFile, array('.', '..'))) continue;
                    if(!file_exists($decompressFolder.$tempFile) || is_dir($decompressFolder.$tempFile)) continue;
                    
                    $tempInfo = pathinfo($tempFile);

                    if($tempInfo['extension'] == 'xlsx')  {
                        $success[] = $decompressFolder . $tempFile;
                    } else {
                        echo json_encode(array('err' => 'error', 'dfile' => $decompressFolder2,));
                    }
                }
            }  else { ?>

                <div class="error extmsg"><p><?php _e('This is not a valid zip or xlsx file!','eopa'); ?></p></div>
            <?php }
            // Use the PHPExcel package from http://phpexcel.codeplex.com/
                require  EOPA_PLUGIN_DIR . 'PHPExcel/Classes/PHPExcel.php';
                require  EOPA_PLUGIN_DIR . 'PHPExcel/Classes/PHPExcel/IOFactory.php';
                require  EOPA_PLUGIN_DIR . 'PHPExcel/chunk.php';
                $count = count($success);
                
                if($success!='') {
                    foreach($success as $ss) {
                        $data = $this->extract_to_db($ss, $count, $decompressFolder2);
                    }
                    // echo json_encode($data);
                }

            die();
		}

		public function extract_to_db($filename, $count, $file) {

			global $wpdb;
            $importLimit = 1000;//$this->module_settings['import_limit'];

            $inputFileType = PHPExcel_IOFactory::identify($filename);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($filename);
            $total_rules_imported = 0;

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10 For all rows
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                
                if(1) {

                    $chunkSize = 100;//$this->module_settings['import_limit'];
                    $chunkFilter = new ChunkReadFilter();
                    $objReader->setReadFilter($chunkFilter);
                    $i = 2;
                    $j=0;


                    for ($startRow = 2; $startRow <= $highestRow; $startRow += $chunkSize) {

                        $chunkFilter->setRows($startRow, $chunkSize);
                        $objPHPExcel1 = $objReader->load($filename);

                        foreach ($objPHPExcel1->getWorksheetIterator() as $worksheet1) {

                            $worksheetTitle1 = $worksheet1->getTitle();
                            $highestRow1 = $worksheet1->getHighestRow(); // e.g. 10 For all rows
                            $highestColumn1 = $worksheet1->getHighestColumn(); // e.g 'F'
      						$highestColumnIndex1 = PHPExcel_Cell::columnIndexFromString($highestColumn1);

                            if(1) {

                                $total_pros = floor($highestRow1/4);
                                for ($row = 2; $row <= $highestRow1; ++$row) {
                                    $val=array();
                                    for ($col = 0; $col < $highestColumnIndex1; ++ $col) {
                                        $cell = $worksheet1->getCellByColumnAndRow($col, $row);
                                        $val[] = $cell->getValue();
                                    }

                                    if($val[0] != '') {

                                    }

                                }
                                $inc = 11;
                                
                                for($rr = 2; $rr <= $highestRow1; $rr+=$inc) {
                                	
                            	    $rule_name = $worksheet1->getCellByColumnAndRow(1, $rr);
                                    $rule_status = $worksheet1->getCellByColumnAndRow(2, $rr);//eopa_poptions_table

                                    $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}eopa_global_rule_table WHERE rule_name='$rule_name'");
                                    
                                    if(count($result) == 0) {
                                    	$total_rules_imported++;
                                    	$wpdb->query("INSERT INTO {$wpdb->prefix}eopa_global_rule_table (rule_name, rule_status) VALUES ('{$rule_name}', '{$rule_status}')");
                                    	$rule_id = $wpdb->insert_id;
                                    } else  {
                                    	$rule_id = $result[0]->rule_id;
                                    }
                                    
                                    $option_title = sanitize_text_field($worksheet1->getCellByColumnAndRow(3, $rr));
                                    $option_field_type = sanitize_text_field($worksheet1->getCellByColumnAndRow(4, $rr));
                                    $option_is_required = sanitize_text_field($worksheet1->getCellByColumnAndRow(5, $rr));
                                    $option_sort_order = sanitize_text_field($worksheet1->getCellByColumnAndRow(6, $rr));
                                    $option_price = sanitize_text_field($worksheet1->getCellByColumnAndRow(7, $rr));
                                    $option_price_type = sanitize_text_field($worksheet1->getCellByColumnAndRow(8, $rr));
                                    $option_maxchars = sanitize_text_field($worksheet1->getCellByColumnAndRow(9, $rr));
                                    $option_allowed_file_extensions = sanitize_text_field($worksheet1->getCellByColumnAndRow(10, $rr));
                                    $option_type = sanitize_text_field($worksheet1->getCellByColumnAndRow(11, $rr));
                                    $global_rule_id = sanitize_text_field($worksheet1->getCellByColumnAndRow(12, $rr));
                                    $enable_price_per_char = sanitize_text_field($worksheet1->getCellByColumnAndRow(13, $rr));
                                    $showif = sanitize_text_field($worksheet1->getCellByColumnAndRow(14, $rr));
                                    $cfield = sanitize_text_field($worksheet1->getCellByColumnAndRow(15, $rr));
                                    $condition = sanitize_text_field($worksheet1->getCellByColumnAndRow(16, $rr));
                                    $ccondition_value = sanitize_text_field($worksheet1->getCellByColumnAndRow(17, $rr));
                                    $manage_stock = sanitize_text_field($worksheet1->getCellByColumnAndRow(18, $rr));
                                    $stock = sanitize_text_field($worksheet1->getCellByColumnAndRow(19, $rr));
                                    $min_value = sanitize_text_field($worksheet1->getCellByColumnAndRow(20, $rr));
                                    $max_value = sanitize_text_field($worksheet1->getCellByColumnAndRow(21, $rr));
                                    $multiply_price_by_qty = sanitize_text_field($worksheet1->getCellByColumnAndRow(22, $rr));
                                    
                                    $wpdb->query("INSERT INTO {$wpdb->prefix}eopa_poptions_table (option_title, option_field_type, option_is_required, option_sort_order, option_price, option_price_type, option_maxchars, option_allowed_file_extensions, option_type, global_rule_id, enable_price_per_char, showif, cfield, ccondition, ccondition_value, manage_stock, stock, min_value, max_value, multiply_price_by_qty) VALUES ('$option_title', '$option_field_type', '$option_is_required', '$option_sort_order', '$option_price', '$option_price_type', '$option_maxchars', '$option_allowed_file_extensions', '$option_type', '$rule_id', '$enable_price_per_char', '$showif', '$cfield', '$condition', '$ccondition_value', '$manage_stock', '$stock', '$min_value', '$max_value', '$multiply_price_by_qty')");

                                  	$option_id = $wpdb->insert_id;
                                    if($worksheet1->getCellByColumnAndRow(0, $rr +1) == 'Rows Data') {

                                        for($cl = 2; $cl <= $highestColumnIndex1; $cl++) {

                                        	if($worksheet1->getCellByColumnAndRow($cl, $rr + 2) != '') {
	                                            $option_row_title = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+3));
	                                            $option_row_sort_order = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+4));
	                                            $option_row_price = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+5));
	                                            $option_row_price_type = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+6));
	                                            $option_image = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+7));
	                                            $option_pro_image = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+8));
	                                            // $global_rule_id = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+9));
	                                            $stock = sanitize_text_field($worksheet1->getCellByColumnAndRow($cl, $rr+10));

												$wpdb->query("INSERT INTO {$wpdb->prefix}eopa_rowoption_table (option_id, option_row_title, option_row_sort_order, option_row_price, option_row_price_type, option_image, option_pro_image, global_rule_id, stock) VALUES ('$option_id', '$option_row_title','$option_row_sort_order', '$option_row_price','$option_row_price_type', '$option_image','$option_pro_image', '$rule_id', '$stock')");
                                        	}
                                        }
                                        $inc = 11;
                                    } else
                                    	$inc = 1;
                                }

                            }

                            $objPHPExcel1->disconnectWorksheets(); 
                            unset($objPHPExcel1);
                        }
                    }
                }
            }

            if( $total_rules_imported === 0 ) {

                echo json_encode(array('nor' => 'no_record'));

            } else {

                echo json_encode(array('nor' => $total_rules_imported, 'file' => $file));
            }
            die();
		}

		public function export_all_rules() {

			global $wpdb;
			
			$global_rules = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'eopa_global_rule_table');
		
			ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 600000);

            if (is_dir(EOPA_PLUGIN_DIR.'files/global_rules')) {
                $this->delTree(EOPA_PLUGIN_DIR.'files/global_rules');
            }
            if(!is_dir(EOPA_PLUGIN_DIR."files"))
            	mkdir(EOPA_PLUGIN_DIR."files");
           	if(!is_dir(EOPA_PLUGIN_DIR."files/global_rules"))
            	mkdir(EOPA_PLUGIN_DIR."files/global_rules", 0700);

            global $wpdb;
            require  EOPA_PLUGIN_DIR . 'PHPExcel/Classes/PHPExcel.php';

            $workbook = new PHPExcel();

            // set some default styles
            $workbook->getDefaultStyle()->getFont()->setName('Calibri');
            $workbook->getDefaultStyle()->getFont()->setSize(10);
            $workbook->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $workbook->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $workbook->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            
            // pre-define some commonly used styles
            $box_format = array(
                'fill' => array(
                	'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                	'color' => array( 'rgb' => '05568B')
                ),
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => 'FFFFFF'),
                    'size'  => 11,
                    'name'  => 'Calibri'
           		),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER     
                )   
            );
            
            $meta_box_format = array(
	            'fill' => array(
	                'type' 	=> PHPExcel_Style_Fill::FILL_SOLID,
	                'color'	=> array( 'rgb' => '8BD7F7')
	            ),
	            'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => '000000'),
                    'size'  => 10,
                    'name'  => 'Calibri'
        		),
            	'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            	)    
            );
            
            $price_format = array(
                'numberformat' => array(
                    'code' => '######0.00'
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT  
                )
            );

            $borders = array(
	            'borders' => array(
	                'left' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_NONE,
	                ),
	                'right' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_NONE,
	                ),
	                'top' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_NONE,
	                ),
	                'bottom' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_THICK,
	                ), 
	            ),
          	);

            $borders_right = array(
	            'borders' => array(
	                'left' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_NONE,
	                ),
	                'botttm' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_NONE,
	                ),
	                'top' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_NONE,
	                ),
	                'right' => array(
	                  'style' => PHPExcel_Style_Border::BORDER_THICK,
	                ), 
	            ),
          	);

            $j = 0;
                        
            $workbook->setActiveSheetIndex(0);
            $worksheet = $workbook->getActiveSheet(0);
            $worksheet->setTitle( 'Orders' );

            //Set Columns Widths
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(strlen('rule_id')+15);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('rule_name')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('rule_status')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_title')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_field_type')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_is_required')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_sort_order')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_price')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_price_type')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_maxchars')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_allowed_file_extensions')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('option_type')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('global_rule_id')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('enable_price_per_char')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('showif')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('cfield')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('ccondition')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('ccondition_value')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('manage_stock')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('stock')+4,10)+10);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('min_value')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('max_value')+4,5)+5);
            $worksheet->getColumnDimensionByColumn($j++)->setWidth(max(strlen('multiply_price_by_qty')+4,5)+5);


            // The heading row and column styles
            $styles = array();
            $data = array();
            $i = 1;
            $j = 0;
            $data[$j++] = 'Global Rule ID';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Rule Name';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Rule Status';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Title';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Field Type';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option is Required';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Sort Order';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Price';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Price Type';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Maxchars';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Allowed File Extensions';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Option Type';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Global Rule Id';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Enable Price Per Char';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Showif';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Field';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Condition';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Condition Value';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Manage Stock';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Stock';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Min Value';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Max Value';
            $styles[$j] = &$text_format;
            $data[$j++] = 'Multiply Price by Qty';
            

            $worksheet->getRowDimension($i)->setRowHeight(30);
            $this->setCellRow( $worksheet, $i, $data, $box_format );

          	foreach ($global_rules as $key => $global_rule) {
          		$global_rule_options = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'eopa_poptions_table WHERE product_id = "" AND global_rule_id ='.$global_rule->rule_id);

          		$ai = 2;
          		foreach ($global_rule_options as $global_rule_option) {

          			$worksheet->getRowDimension($i)->setRowHeight(30);

          			$worksheet->setCellValue('A'.$ai, $global_rule->rule_id);
                    $worksheet->setCellValue('B'.$ai, $global_rule->rule_name);
                    $worksheet->setCellValue('C'.$ai, $global_rule->rule_status);
                    $worksheet->setCellValue('D'.$ai, $global_rule_option->option_title);
                    $worksheet->setCellValue('E'.$ai, $global_rule_option->option_field_type);
                    $worksheet->setCellValue('F'.$ai, $global_rule_option->option_is_required);
                    $worksheet->setCellValue('G'.$ai, $global_rule_option->option_sort_order);
                    $worksheet->setCellValue('H'.$ai, $global_rule_option->option_price);
                    $worksheet->setCellValue('I'.$ai, $global_rule_option->option_price_type);
                    $worksheet->setCellValue('J'.$ai, $global_rule_option->option_maxchars);
                    $worksheet->setCellValue('K'.$ai, $global_rule_option->option_allowed_file_extensions);
                    $worksheet->setCellValue('L'.$ai, $global_rule_option->option_type);
                    $worksheet->setCellValue('M'.$ai, $global_rule_option->global_rule_id);
                    $worksheet->setCellValue('N'.$ai, $global_rule_option->enable_price_per_char);
                    $worksheet->setCellValue('O'.$ai, $global_rule_option->showif);
                    $worksheet->setCellValue('P'.$ai, $global_rule_option->cfield);
                    $worksheet->setCellValue('Q'.$ai, $global_rule_option->ccondition);
                    $worksheet->setCellValue('R'.$ai, $global_rule_option->ccondition_value);
                    $worksheet->setCellValue('S'.$ai, $global_rule_option->manage_stock);
                    $worksheet->setCellValue('T'.$ai, $global_rule_option->stock);
                    $worksheet->setCellValue('U'.$ai, $global_rule_option->min_value);
                    $worksheet->setCellValue('V'.$ai, $global_rule_option->max_value);
                    $worksheet->setCellValue('W'.$ai, $global_rule_option->multiply_price_by_qty);

					// print_r($global_rule_option_rows);

					

                    $global_rule_option_rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."eopa_rowoption_table WHERE global_rule_id = {$global_rule->rule_id} AND option_id={$global_rule_option->id}");

                    if(count($global_rule_option_rows) > 0) {
	                    
	                    $worksheet->mergeCells("A".($ai+1).":J".($ai+1));
						$worksheet->setCellValue('A'.($ai+1), 'Rows Data');
	                    $worksheet->setCellValue('B'.($ai+2), 'Option Id');
	                    $worksheet->setCellValue('B'.($ai+3), 'Option Row Title');
	                    $worksheet->setCellValue('B'.($ai+4), 'Option Row Sort Order');
	                    $worksheet->setCellValue('B'.($ai+5), 'Option Row Price');
	                    $worksheet->setCellValue('B'.($ai+6), 'Option Row Price Type');
	                    $worksheet->setCellValue('B'.($ai+7), 'Option Image');
	                    $worksheet->setCellValue('B'.($ai+8), 'Option Product Image');
	                    $worksheet->setCellValue('B'.($ai+9), 'global_rule_id');
	                    $worksheet->setCellValue('B'.($ai+10), 'Stock');

	                    $j = 2;

	                    foreach($global_rule_option_rows as $global_rule_option_row) {

	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+2, $global_rule_option_row->option_id);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+3, $global_rule_option_row->option_row_title);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+4, $global_rule_option_row->option_row_sort_order);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+5, $global_rule_option_row->option_row_price);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+6, $global_rule_option_row->option_row_price_type);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+7, $global_rule_option_row->option_image);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+8, $global_rule_option_row->option_pro_image);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+9, $global_rule_option_row->global_rule_id);
	                    	$worksheet->setCellValueByColumnAndRow( $j, $ai+10, $global_rule_option_row->stock);
	                    	$j++;
	                    }

	                    for ($icol = $ai+1; $icol <= ($ai+10); $icol++) {
                           
	                        $worksheet->getRowDimension($icol)->setOutlineLevel(1);
	                        $worksheet->getRowDimension($icol)->setCollapsed(false);
	                        $worksheet->getRowDimension($icol)->setVisible(false);
	                    }
	                    $ai+= 11;
	                } else
	                	$ai++;
          		}
          	}
// die();
          	$workbook->setActiveSheetIndex(0);
            $datetime = date('d-m-Y');
            $filename = 'global_rules-'.$datetime;
            $filename .= '.xlsx';

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
            
            $objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');
            $objWriter->setPreCalculateFormulas(false);
            $objWriter->save(EOPA_PLUGIN_DIR.'files/global_rules/'.$filename);
        
            $max_row = $workbook->setActiveSheetIndex(0)->getHighestRow();
            $workbook->getActiveSheet(0)->removeRow(2, $max_row);
            $workbook->setActiveSheetIndex(0);

          	$this->addzip (EOPA_PLUGIN_DIR.'files/global_rules/' , EOPA_PLUGIN_DIR.'files/global_rules.zip' );

                        
            $filepath = EOPA_PLUGIN_DIR.'files/';
            $filename = 'global_rules.zip';

            // Download the created zip file
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Length: " . filesize($filepath.$filename));

            // echo json_encode(array('zip' => $filename));
        
	        $this->delTree(EOPA_PLUGIN_DIR.'files/global_rules');
	        // Clear the spreadsheet caches
	        $this->clearSpreadsheetCache();
			echo json_encode(array('status' => 'success', 'res' => $filename));
			die(); 
		}

		public static function delTree($dir) {
        	
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
              (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }

        public function addzip($source, $destination) {
            $files_to_zip = glob($source . '/*');
            $this->create_zip($files_to_zip, $destination);
        }

        // compress all files in the source directory to destination directory 
        public function create_zip($files = array(), $dest = '', $overwrite = false) {
            if (file_exists($dest) && !$overwrite) {
                return false;
            }
            if (($files)) {
                $zip = new ZipArchive();
                if ($zip->open($dest, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
                foreach ($files as $file) {

                    $filename = substr($file, strrpos($file, '/')+1);
                    $zip->addFile($file,$filename);

                    
                }
                $zip->close();

                    
                    

                return file_exists($dest);
            } else {
                return false;
            }
        }

        protected function setCellRow( $worksheet, $row/*1-based*/, $data, &$style=null ) {
            $worksheet->fromArray( $data, null, 'A'.$row, true );
            if (!empty($style)) {        
                $worksheet->getStyle( "$row:$row" )->applyFromArray( $style, false );
            }
        }

        protected function clearSpreadsheetCache() {
            $files = glob('DIR_CACHE' . 'Spreadsheet_Excel_Writer' . '*');
            
                if ($files) {
                        foreach ($files as $file) {
                                if (file_exists($file)) {
                                        @unlink($file);
                                        clearstatcache();
                                }
                        }
                }
        }

	} new EO_Product_Addons_Import_Export();
}