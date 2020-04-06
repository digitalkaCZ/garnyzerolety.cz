<?php 

/*
 * Plugin Name:       Extendons: WooCommerce Product Add-ons - Custom Product Options Plugin (edited by CREALAB)
 * Plugin URI:        http://extendons.com
 * Description:       Woocommerce Extendons Product Addons - Custom Product Options lets your customers to personalize the products on your store by additional options like fields, area, files and more.
 * Version:           2.1.0
 * Author:            Extendons
 * Developed By:  	  Extendons Team
 * Author URI:        http://www.extendons.com/
 * Support URI:		  http://support.extendons.com/
 * Text Domain:       eopa
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Check if WooCommerce is active
 * if wooCommerce is not active Product Addons module will not work.
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	
	function my_admin_notice() {

		// Deactivate the plugin
		   deactivate_plugins(__FILE__);
			
		  echo '<div id="message" class="error">
			<p><strong>Extendons: Product Addons is inactive.</strong> The <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce plugin</a> must be active for this plugin to work. Please install &amp; activate WooCommerce »</p></div>';

}
add_action( 'admin_notices', 'my_admin_notice' );
}

if ( !class_exists( 'EO_Product_Addons' ) ) { 

	class EO_Product_Addons {

		public $module_settings = array();
		public $module_default_settings = array();

		function __construct() {

			$this->module_constants();
			$this->module_tables();

			if ( is_admin() ) {
				require_once( EOPA_PLUGIN_DIR . 'admin/class-eo-product-addons-admin.php' );
				register_activation_hook( __FILE__, array( $this, 'install_module' ) );

				// register_deactivation_hook( __FILE__, array( $this, 'exd_remove_database_tables' ));
				
			} else {
				require_once( EOPA_PLUGIN_DIR . 'front/class-eo-product-addons-front.php' );
			}

			add_action( 'wp_ajax_pasupport_extend_contact', array($this,'pasupport_extendon_callback' ));
			add_action( 'wp_ajax_nopriv_pasupport_extend_contact', array($this,'pasupport_extendon_callback' ));

			
		}


		function exd_remove_database_tables() {

	   		global $wpdb;
		     
	   		$wpdb->eopa_global_rule_table = $wpdb->prefix . 'eopa_global_rule_table';
	   		$wpdb->eopa_temp_table = $wpdb->prefix . 'eopa_temp_table';
			$wpdb->eopa_poptions_table = $wpdb->prefix . 'eopa_poptions_table';
			$wpdb->eopa_rowoption_table = $wpdb->prefix . 'eopa_rowoption_table';

		    $sql1 = "DROP TABLE IF EXISTS $wpdb->eopa_temp_table";
		    $wpdb->query($sql1);

		    $sql3 = "DROP TABLE IF EXISTS $wpdb->eopa_rowoption_table";
		    $wpdb->query($sql3);

		    $sql2 = "DROP TABLE IF EXISTS $wpdb->eopa_poptions_table";
		    $wpdb->query($sql2);

		    $sql4 = "DROP TABLE IF EXISTS $wpdb->eopa_global_rule_table";
		    $wpdb->query($sql4);
	   	}


		// support email/contact function
		function support_extendon_callback () {
			
			if(isset($_POST['condition']) && $_POST['condition'] == "paextendons_support_contact") {

					$support_fname = $_POST['suppextfname'];
					$support_lname = $_POST['suppextlname'];
					$support_email = $_POST['suppextemail'];
					$support_number = $_POST['suppextnumber'];
					$support_subject = $_POST['suppextsubj'];
					$support_message = $_POST['suppextmasg'];	

					$to = "support@extendons.com";
					$subject = $support_subject;

					$message = "
					<html>
					<head>
					<title>"._e('WooCommerce Product Addons', 'eopa')."</title>
					</head>
					<body>
					<table>
					<tr>
					<td><b>"._e('First Name:', 'eopa')."</b></td>
					<td>$support_fname</td>
					</tr>
					<tr>
					<td><b>"._e('Last Name:', 'eopa')."</b></td>
					<td>$support_lname</td>
					</tr>
					<tr>
					<td><b>"._e('Email:', 'eopa')."</b></td>
					<td>$support_email</td>
					</tr>
					<tr>
					<td><b>"._e('Phone:', 'eopa')."</b></td>
					<td>$support_number</td>
					</tr>
					<tr>
					<td><b>"._e('Subject:', 'eopa')."</b></td>
					<td>$support_subject</td>
					</tr>
					<tr>
					<td><b>"._e('Message:', 'eopa')."</b></td>
					<td>$support_message</td>
					</tr>
					</table>
					</body>
					</html>
					";
					
					$headers .= "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\n";
					// $headers .= 'From: '.$admin_email.'' . "\r\n";
					// $headers .= 'Cc: '.$admin_email.'' . "\r\n";
					
					mail($to,$subject,$message,$headers);
				
			}

			die();
		}

		



		public function module_constants() {
            
            if ( !defined( 'EOPA_URL' ) )
                define( 'EOPA_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'EOPA_BASENAME' ) )
                define( 'EOPA_BASENAME', plugin_basename( __FILE__ ) );

            if ( ! defined( 'EOPA_PLUGIN_DIR' ) )
                define( 'EOPA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        public function install_module() {
        	$this->module_tables();
        	$this->create_module_data();
        }

        private function module_tables() {
            
			global $wpdb;
			
			$wpdb->eopa_global_rule_table = $wpdb->prefix . 'eopa_global_rule_table';
			$wpdb->eopa_temp_table = $wpdb->prefix . 'eopa_temp_table';
			$wpdb->eopa_poptions_table = $wpdb->prefix . 'eopa_poptions_table';
			$wpdb->eopa_rowoption_table = $wpdb->prefix . 'eopa_rowoption_table';
		}


		public function create_module_data() {

			//$this->set_module_default_settings();
            $this->create_tables();
        }



        public function create_tables() {
            
			global $wpdb;
			
			$charset_collate = '';
		
			if ( !empty( $wpdb->charset ) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( !empty( $wpdb->collate ) )
				$charset_collate .= " COLLATE $wpdb->collate";	
				
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->eopa_global_rule_table'" ) != $wpdb->eopa_global_rule_table ) {
				$sql4 = "CREATE TABLE " . $wpdb->eopa_global_rule_table . " (
							rule_id int(25) NOT NULL auto_increment,
							rule_name varchar(255) NULL,
							rule_status varchar(255) NULL, 
							applied_on varchar(255) NULL,
							proids text NULL,
							catids text NULL,
							catproids text NULL,

							PRIMARY KEY (rule_id)
							) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql4 );
			}


			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->eopa_temp_table'" ) != $wpdb->eopa_temp_table ) {
				$sql = "CREATE TABLE " . $wpdb->eopa_temp_table . " (
							id int(25) NOT NULL auto_increment,
							field_id varchar(255) NULL,
							field_type varchar(255) NULL, 
							field varchar(255) NULL,

							PRIMARY KEY (id)
							) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}


			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->eopa_poptions_table'" ) != $wpdb->eopa_poptions_table ) {
				$sql1 = "CREATE TABLE " . $wpdb->eopa_poptions_table . " (
					id int(25) NOT NULL auto_increment,
					product_id varchar(255) NOT NULL,
					option_title varchar(500) NULL,
					option_field_type varchar(500) NULL,
					option_is_required varchar(500) NULL,
					option_sort_order varchar(500) NULL,
					option_price varchar(500) NULL,
					option_price_type varchar(500) NULL,
					option_maxchars varchar(500) NULL,
					option_allowed_file_extensions varchar(500) NULL,
					option_type varchar(500) NULL,
					global_rule_id int(25) NULL,
					enable_price_per_char varchar(25) NULL,
					showif varchar(255),
					cfield varchar(255),
					ccondition varchar(255),
					ccondition_value varchar(255),
					manage_stock varchar(25) NULL,
					stock int(25),
					min_value int(25) NULL,
					max_value int(25) NULL,
					multiply_price_by_qty tinyint(1),

					PRIMARY KEY (id),
					FOREIGN KEY (global_rule_id) REFERENCES ".$wpdb->eopa_global_rule_table." (rule_id) ON DELETE CASCADE ON UPDATE CASCADE
					) $charset_collate;";
			
			
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
				$wpdb->query( "ALTER TABLE ".$wpdb->eopa_poptions_table." AUTO_INCREMENT=1001 " );
			}

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->eopa_rowoption_table'" ) != $wpdb->eopa_rowoption_table ) {
				$sql1 = "CREATE TABLE " . $wpdb->eopa_rowoption_table . " (
									id int(25) NOT NULL auto_increment,
									option_id int(25) NOT NULL,
									option_row_title varchar(500) NULL,
									option_row_sort_order varchar(500) NULL,
									option_row_price varchar(500) NULL,
									option_row_price_type varchar(500) NULL,
									option_image varchar(500) NULL,
									option_pro_image varchar(500) NULL,
									global_rule_id varchar(500) NULL,
									stock int(25),
									 
									PRIMARY KEY (id),
					FOREIGN KEY (option_id) REFERENCES ".$wpdb->eopa_poptions_table." (id) ON DELETE CASCADE ON UPDATE CASCADE
						) $charset_collate;";

		
			
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
				$wpdb->query("ALTER TABLE ".$wpdb->eopa_rowoption_table." AUTO_INCREMENT=1001 " );
			}
		}

		public function set_module_default_settings() {
            
			$module_settings = get_option( 'eopa_settings' );
			if ( !$module_settings ) {
                update_option( 'eopa_settings', $this->module_default_settings );
			}
		}

		public function get_module_settings() {
            
            $module_settings = get_option( 'eopa_settings' );

            if ( !$module_settings ) {
                update_option( 'eopa_settings', $this->module_default_settings );
                $module_settings = $this->module_default_settings;
            }

            return $module_settings;
        }
	}

	new EO_Product_Addons();
}

?>
