<?php
/**
 * Plugin Name: Product Shortlist
 * Plugin URI: http://cedcommerce.com
 * Description: A WooCommerce extension to shortlist the desired products of WooCommerce .
 * Version: 1.0.4
 * Author: CedCommerce
 * Author URI: http://cedcommerce.com
 * Requires at least: 4.3
 * Tested up to: 5.2.0
 *
 * Text Domain: product-shortlist
 * Domain Path: /languages/
 *
 * @author CedCommerce
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Checks if current site is multisite
 *
 * @author CedCommerce <http://cedcommerce.com>
 */
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * Checkes if WooCommerce id active
 * 
 * @author CedCommerce <http://cedcommerce.com>
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/***************************************
	 * Creating constants to use them later 
	 * *************************************/
	define( 'CED_PS_PLUGIN_URL', plugin_dir_url(__FILE__) );
	define( 'CED_PS_PLUGIN_DIR', plugin_dir_path(__FILE__) );
	define( 'CED_PS_PREFIX', 'ced_ps' );
	define( 'CED_PS_PLUGIN_VER' , '1.0.3');
	if ( ! function_exists( 'ced_ps_doc_settings' ) ) {
		/**
		 * Adding links of setting
		 *
		 * @param array $actions
		 * @param static $plugin_file
		 * @author CedCommerce
		 */
		function ced_ps_doc_settings( $actions, $plugin_file ) {
			static $plugin;
			if (!isset($plugin)) 
			{
				$plugin = plugin_basename(__FILE__);
			}
			if ( $plugin == $plugin_file ) 
			{
				$setting = array('setting' => '<a href="'.site_url().'/wp-admin/edit.php?post_type=product&page=ced-ps-settings" target="_blank">' . __('Settings', 'product-shortlist') . '</a>');
				$actions = array_merge( $setting, $actions );
			}
			return $actions;
		}
	}
	add_filter( 'plugin_action_links', 'ced_ps_doc_settings', 10, 5 );
	
	if ( ! function_exists( 'ced_ps_custom_plugin_row_meta' ) ) {
		/**
		 * Add links of demo and documentation
		 * 
		 * @param array $links
		 * @param string $file
		 * @author CedCommerce <http://cedcommerce.com>
		 */
		function ced_ps_custom_plugin_row_meta( $links, $file ) {
		
			if ( strpos( $file, 'product-shortlist/product-shortlist.php' ) !== false ) {
				$new_links = array(
						'doc' => '<a href="http://demo.cedcommerce.com/woocommerce/product-shortlist/doc/index.html" target="_blank">' . __( 'Documentation', 'product-shortlist' ) . '</a>',
						'demo' => '<a href="http://demo.cedcommerce.com/woocommerce/product-shortlist/wp-admin/edit.php?post_type=product&page=ced-ps-settings" target="_blank">' . __( 'Live Demo', 'product-shortlist' ) . '</a>'
				);
		
				$links = array_merge( $links, $new_links );
			}
		
			return $links;
		}
	}
	add_filter( 'plugin_row_meta', 'ced_ps_custom_plugin_row_meta', 10, 2 );
	
	/**
	 * Creates a table at activation
	 *
	 * @category function
	 * @author CedCommerce <http://cedcommerce.com>
	 * @return void
	 */
	function ced_ps_create_table() {
		global $wpdb;
			
		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'ps_shortlist_log';
			
		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,		
		date date DEFAULT '0000-00-00' NOT NULL,
		ip varchar(55) DEFAULT '' NOT NULL ,
		user_id varchar(55) DEFAULT '' , 
		shortlisted_products TEXT ,
		discount TEXT,
		UNIQUE KEY id (id)
		) $charset_collate;";
			
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		update_option('ced_ps_plugin_version' , CED_PS_PLUGIN_VER);
	}
	register_activation_hook( __FILE__, 'ced_ps_create_table' );

	/*******************************************
	 * Includes product-shortlist-class.php file 
	 ******************************************/
	include_once 'includes/product-shortlist-class.php';
	
	/*********************************
	 * Includes ps-functions.php file 
	 * ******************************/
	include_once 'includes/ps-functions.php';
	
	if (! function_exists( 'ced_ps_load_text_domain' ) ) {
		/**
		 * Loads text domain
		 * @name ced_ps_load_text_domain()
		 * @author CedCommerce <http://cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_ps_load_text_domain()	{
		
			$domain = "product-shortlist";
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			load_textdomain( $domain, CED_PS_PLUGIN_DIR .'languages/'.$domain.'-' . $locale . '.mo' );
			$var = load_plugin_textdomain( 'product-shortlist', false, plugin_basename( dirname(__FILE__) ) . '../languages' );
		}
		add_action( 'plugins_loaded', 'ced_ps_load_text_domain' );
	}
	/**
	* Condition to manage previous versions database
	*/
	if ( in_array('product-shortlist/product-shortlist.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
	{		
		if (get_option('ced_ps_plugin_version') != CED_PS_PLUGIN_VER) 
		{
			global $wpdb;
			$table = $wpdb->prefix.'ps_shortlist_log';
			$user_result = $wpdb->get_results( "SELECT * FROM $table" );		
			update_option('ced_ps_plugin_version' , CED_PS_PLUGIN_VER);
			$wpdb->query("DROP TABLE $table");
			ced_ps_create_table();
			if (!empty($user_result)) 
			{
				foreach ($user_result as $key => $value) 
				{
					$data = array();
					foreach ($user_result as $key1 => $value1) 
					{
						if ($value1->ip == $value->ip) 
						{
							$data[] = array('product_id'=>$value1->product_id , 'date'=>$value1->date);
						}
					}					
					$result = $wpdb->insert( $table, array( 'date' => date('Y-m-d') , 'ip' => $value->ip , 'shortlisted_products' => json_encode($data)  ) );
				}
			}												
		}
		else{
			update_option('ced_ps_plugin_version' , CED_PS_PLUGIN_VER);
		}	
	}
	
} else {
	if (! function_exists( 'ced_ps_plugin_error_notice' ) ) {
		
		/**
		 * Shows error notice if extension is being activated without WooCommerce
		 *
		 * @category 	function
		 * @author		CedCommerce <http://cedcommerce.com>
		 * 
		 */
		function ced_ps_plugin_error_notice() {
		?>
			<div class="error notice is-dismissible">
				<p>
					<?php _e( 'It seems WooCommerce is not active. Please install WooCommerce first and activate, to use the Prodcut Shortlist extension !!!', 'product-shortlist' ); ?>
				</p>
			</div>
			
  
		<?php
		}
	}
	
	add_action( 'admin_init', 'ced_ps_plugin_deactivate' );
		
	if (! function_exists( 'ced_ps_plugin_deactivate' ) ) {
		
		/**
		 * Deactivates the extension if WooCommerce is not installed
		 *
		 * @category 	function
		 * @author		CedCommerce <http://cedcommerce.com>
		 */
		function ced_ps_plugin_deactivate() {
			
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action( 'admin_notices', 'ced_ps_plugin_error_notice' );
			
		}
	}	
			
}