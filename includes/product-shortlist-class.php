<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Checks if Product_Shortlist class has been defined
 *
 * @author CedCommerce <http://cedcommerce.com>
 */
if( ! class_exists( 'Product_Shortlist' ) ) {
	
	/**
	 * Creates the class named Product_Shortlist
	 *
	 * @category class
	 * @author CedCommerce
	 */
	class Product_Shortlist {
	
		/**
		 * Initializes the settings over here
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @access public
		 */
		public function __construct() {
			global $woocommerce;
			$this->id 				= 'ced-ps';			
			
			add_action( 'admin_enqueue_scripts', array ( $this, 'ced_ps_admin_enque_scripts' ) );
			
			add_action( 'wp_enqueue_scripts', array ( $this, 'ced_ps_wp_enque_scripts' ) );
			
			add_action( 'admin_menu', array( $this, 'ced_ps_add_settings_page' ) );
			
			add_action( 'wp_ajax_save_general_settings', array ( $this, 'ced_ps_save_general_settings' ) );
			
			add_action( 'wp_ajax_update_shortlisted_records', array ( $this, 'ced_ps_update_shortlisted_records' ) );
			
			add_action( 'wp_ajax_nopriv_update_shortlisted_records', array ( $this, 'ced_ps_update_shortlisted_records' ) );
			
			add_action( 'wp_ajax_get_shortlisted_products' , array( $this , 'ced_ps_get_shortlisted_products' ) );
			
			add_action( 'wp_ajax_nopriv_get_shortlisted_products' , array( $this , 'ced_ps_get_shortlisted_products' ) );
			
			add_action( 'wp_ajax_delete_shortlisted_product' , array( $this , 'ced_ps_delete_shortlisted_product' ) );
			
			add_action( 'wp_ajax_delete_all_shortlisted_products' , array( $this , 'ced_ps_delete_shortlisted_product' ) );
			
			add_action( 'wp_ajax_delete_products_admin_side' , array( $this , 'ced_ps_delete_products_admin_side' ) );
			
			add_action( 'wp_ajax_add_discount_product_admin_side' , array( $this , 'ced_ps_add_discount_product_admin_side' ) );
			
			add_action( 'wp_ajax_send_mail_to_user' , array( $this , 'ced_ps_send_mail_to_user' ) );
			
			add_action('phpmailer_init' , array($this , 'ced_ps_phpmailer_init'));
			
			add_action('admin_post_save_smtp_settings' , array($this , 'ced_ps_save_smtp_settings'));
			
			add_action('admin_post_nopriv_save_smtp_settings' , array($this , 'ced_ps_save_smtp_settings'));

			add_action( 'wp_ajax_bulk_delete' , array( $this , 'ced_ps_bulk_delete' ) );

			add_shortcode('ced_ps_product_shortlist_section',  array( $this , 'ced_ps_add_shortlist_section_shortcode'));

			add_action( 'woocommerce_before_calculate_totals', array($this , 'ced_ps_show_discount_price_in_cart') );
			
			add_action('admin_post_save_email_template_settings' , array($this , 'ced_ps_save_email_tpl_settings'));
			
			add_action('admin_post_nopriv_save_email_template_settings' , array($this , 'ced_ps_save_email_tpl_settings'));
			
			add_action('admin_head', array($this , 'ced_ps_add_mce_button'));
			
			add_action('init',array($this,'ced_ps_export_csv'));

			add_action('wp_ajax_ced_ps_send_mail',array(&$this,'ced_ps_send_mail'));

			add_action("after_setup_theme",array(&$this,"ced_ps_close_email_img"));

		}

		function ced_ps_close_email_img()
		{
			//print_r($_GET);
			if(isset($_GET["ced_ps_close"]) && $_GET["ced_ps_close"]==true)
			{
				//die("seema");
				unset($_GET["ced_ps_close"]);
				if(!session_id())
					session_start();
				$_SESSION["ced_ps_hide_email"]=true;
			}
		}


		function ced_ps_send_mail()
		{
			if(isset($_POST["flag"]) && $_POST["flag"]==true && !empty($_POST["emailid"]))
			{
				$to = "support@cedcommerce.com";
				$subject = "Wordpress Org Know More";
				$message = 'This user of our woocommerce extension "Product Shortlist" wants to know more about marketplace extensions.<br>';
				$message .= 'Email of user : '.$_POST["emailid"];
				$headers = array('Content-Type: text/html; charset=UTF-8');
				$flag = wp_mail( $to, $subject, $message);	
				if($flag == 1)
				{
					echo json_encode(array('status'=>true,'msg'=>__('Soon you will receive the more details of this extension on the given mail.',"product-shortlist")));
				}
				else
				{
					echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occurred.Please try again.',"product-shortlist")));
				}
			}
			else
			{
				echo json_encode(array('status'=>false,'msg'=>__('Sorry,an error occurred.Please try again.',"product-shortlist")));
			}
			wp_die();
		}

		/**
		 * Enqueues the scripts and styles at admin panel
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		public function ced_ps_admin_enque_scripts() {

			if ( preg_match( '/ced-ps-settings/', $_SERVER[ 'REQUEST_URI' ] ) ) {
				
				$ajax_nonce = wp_create_nonce( "ced-ps-ajax-seurity-string" );
				
				// Enqueues css files
				wp_enqueue_style( 'ced-ps-select2-style', CED_PS_PLUGIN_URL.'assets/css/ced_ps-select2.min.css', array( 'jquery-ui-style' ) , CED_PS_PLUGIN_VER );
				wp_enqueue_style( 'ced-ps-admin-mailer-style', CED_PS_PLUGIN_URL.'assets/css/ced_ps_admin_mailer.css', array() , CED_PS_PLUGIN_VER );
				wp_enqueue_style( 'ced-ps-style', CED_PS_PLUGIN_URL.'assets/css/ced_ps_admin-style.min.css', array( 'jquery-ui-style', 'ced-ps-select2-style' ) , CED_PS_PLUGIN_VER  );
				wp_enqueue_script( 'ced-ps-admin-mailer', CED_PS_PLUGIN_URL.'assets/js/ced_ps_admin_mailer.js',array('jquery'), CED_PS_PLUGIN_VER , TRUE  );
				wp_localize_script('ced-ps-admin-mailer','ajax_url',admin_url('admin-ajax.php'));
					
				// Enqueues js files
				wp_enqueue_script( 'editor' );	
				if(strpos($_SERVER['REQUEST_URI'], "&page=ced-ps-settings"))
				{					
					wp_enqueue_script( 'ced_ps-select2', CED_PS_PLUGIN_URL.'assets/js/ced_ps-select2.min.js', '' , CED_PS_PLUGIN_VER , TRUE  );
				}

				wp_enqueue_script( 'ced-ps-js', CED_PS_PLUGIN_URL.'assets/js/ced_ps_function.min.js', array( 'jquery', 'jquery-ui-datepicker', 'ced_ps-select2', 'wp-color-picker' ), CED_PS_PLUGIN_VER , TRUE  );
				$domain = $_SERVER[ 'SERVER_NAME' ];
				$path = str_replace( $domain, '', str_replace( 'http://', '', site_url() ) ).'/';
				$translation = array(
						'base_url' 					=> home_url(),
						'ajaxurl' 					=> admin_url('admin-ajax.php'),
						'page_placeholder'			=> __( 'Select pages', 'product-shortlist' ),
						'position_placeholder'		=> __( 'Choose a position', 'product-shortlist' ),
						'digits_only_text'			=> __( 'You can enter only digits in this field!', 'product-shortlist' ),
						'setting_saved_text'		=> __( 'All the settings are saved successfully !', 'product-shortlist' ),
						'setting_failed_text'		=> __( 'It seems no changes were selected !', 'product-shortlist' ),
						'exp_not_negative'			=> __( 'Expiry time mustn\'t be negative !', 'product-shortlist' ),
						'exp_greater_than_zero'		=> __( 'Expiry time must be greater than zero !', 'product-shortlist' ),
						'invalid_exp'				=> __( 'Please enter a valid expiry time, till when shortlisted products will be saved !', 'product-shortlist' ),
						'empty_pages_selection'		=> __( 'Please choose some pages first!', 'product-shortlist' ),
						'empty_position_selection'	=> __( 'Please choose a position first!', 'product-shortlist' ),
						'ced_ps_nonce'				=> $ajax_nonce,
						'base_path'					=> $path
				);
				wp_localize_script('ced-ps-js', 'global', $translation);
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
			}
		}
		
		
		/**
		 * Enqueues the scripts and styles at front-end
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_wp_enque_scripts() 
		{	
			global $wpdb;					
			$cookieExpTime = ''; $currentPage = '';
			$shortlistSectionPosition = '';
			$shortlist_addtocart_background_color='';
			$shortlist_background_color ='';$shortlist_font_color="";
			$shortlist_addtocart_font_color='';$shortlist_text='';
			$removeall_text='';$removeall_font_color='';
			$shortlist_removeall_background_color='';
			$user_logged_in = is_user_logged_in();
			$current_user_id = is_user_logged_in() ? get_current_user_id() : null ; 
			$saved_general_settings = get_option( 'ced_ps_general_settings' );
			$saved_general_settings = json_decode( $saved_general_settings );
			if ( ! empty( $saved_general_settings ) ) {
				if (! empty( $saved_general_settings->exp_time ) ) {
					$cookieExpTime = $saved_general_settings->exp_time;
				}
				if (! empty( $saved_general_settings->selected_position )) {
					$shortlistSectionPosition = $saved_general_settings->selected_position;
					if (isset($saved_general_settings->shortlist_background_color)) 
					{
						$shortlist_background_color = $saved_general_settings->shortlist_background_color;
					}
					if (isset($saved_general_settings->shortlist_addtocart_background_color)) 
					{
						$shortlist_addtocart_background_color = $saved_general_settings->shortlist_addtocart_background_color;
					}
					if (isset($saved_general_settings->shortlist_font_color)) 
					{
						$shortlist_font_color = $saved_general_settings->shortlist_font_color;
					}
					if (isset($saved_general_settings->shortlist_addtocart_font_color)) 
					{
						$shortlist_addtocart_font_color = $saved_general_settings->shortlist_addtocart_font_color;
					}
					if (isset($saved_general_settings->shortlist_text)) 
					{
						$shortlist_text = $saved_general_settings->shortlist_text;
					}
					if (isset($saved_general_settings->removeall_text)) 
					{
						$removeall_text = $saved_general_settings->removeall_text;
					}
					if (isset($saved_general_settings->removeall_font_color)) 
					{
						$removeall_font_color = $saved_general_settings->removeall_font_color;
					}
					if (isset($saved_general_settings->shortlist_removeall_background_color)) 
					{
						$shortlist_removeall_background_color = $saved_general_settings->shortlist_removeall_background_color;
					}

				}
				if (!empty( $saved_general_settings->discount_type )) {
					$discount_type = $saved_general_settings->discount_type;
				}
			}
			if ($user_logged_in) 
			{
				$table = $wpdb->prefix.'ps_shortlist_log';
				$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id = $current_user_id" );
				if (!empty($user_result)) 
				{
					foreach ($user_result as $key => $value) 
					{
						if (!empty(json_decode($value->discount))) 
						{
							$discount = json_decode($value->discount);
							foreach ($discount as $key1 => $value1) 
							{
								$discount_arr[$value1->id] = $value1->discount;
							}
						}
					}
				}				
			}
			if( empty($discount_arr) )
			{
				$discount_arr = '';
			}


			/**
			 * Checks the expiry time of the shortlisted products
			 *
			 * @category function
			 * @author CedCommerce <http://cedcommerce.com>			 
			 */
			$table = $wpdb->prefix.'ps_shortlist_log';
			$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id != '' " );
			if ( !empty($user_result) ) 
			{
				foreach ($user_result as $key => $value) 
				{
					$shortlist_date = $value->date;
					$saved_general_settings = get_option( 'ced_ps_general_settings' );
					$saved_general_settings = json_decode( $saved_general_settings );
					$expiry_time = $saved_general_settings->exp_time;
					if ( strtotime(date('Y-m-d')) > strtotime(date('Y-m-d', strtotime('+'.$expiry_time.' day', strtotime($shortlist_date)))) ) 
					{					
						$wpdb->delete( $table, array( 'id' => $value->id ) );
						if(!empty(json_decode($value->shortlisted_products)))
						{
							$shortlisted_products = json_decode($value->shortlisted_products);
							foreach ($shortlisted_products as $key1 => $value1) {
								$cookie_name = 'ps_product_'.$value->user_id.'-'.$value1;
								$cookie_delete[] = $cookie_name;
							}
						}
					}
				}
									
			}
			if( empty($cookie_delete) )
			{
				$cookie_delete = '';
			}
				
			$domain = $_SERVER[ 'SERVER_NAME' ];
			$path = str_replace( $domain, '', str_replace( 'http://', '', site_url() ) ).'/';
			
			if ( is_shop() ) {
				$currentPage = 'shop';
			} elseif ( is_single() ) {
				$currentPage = 'single';
			} else {
				$currentPage = '';
			}
			
			$ajax_nonce = wp_create_nonce( "ced-ps-ajax-seurity-string" );
			
			// Enqueues css files
			wp_enqueue_style( 'ced-ps-style', CED_PS_PLUGIN_URL.'assets/css/ced_ps_frontend-style.min.css' , array() , CED_PS_PLUGIN_VER  );
			
			// Enqueues js files
			wp_enqueue_script( 'ced-ps-js', CED_PS_PLUGIN_URL.'assets/js/ced_ps_frontend.min.js', array( 'jquery' ) , CED_PS_PLUGIN_VER , TRUE );
			wp_enqueue_script( 'ced_ps-select3', CED_PS_PLUGIN_URL.'assets/js/jquery-ui.js', TRUE );			
			$translation = array(
					'base_url' 					=> site_url(),
					'plugin_url'				=> CED_PS_PLUGIN_URL,
					'ajaxurl' 					=> admin_url('admin-ajax.php'),
					'cookie_exp_time'			=> $cookieExpTime,
					'user_logged_in'			=> $user_logged_in,
					'adminr_bar'				=> is_admin_bar_showing(),
					'current_user_id'			=> $current_user_id,
					'shortlist_section_position'=> $shortlistSectionPosition,
					'shortlist_background_color'=> $shortlist_background_color,
					'shortlist_addtocart_background_color'=> $shortlist_addtocart_background_color,
					'shortlist_font_color'		=> $shortlist_font_color,
					'shortlist_addtocart_font_color'		=> $shortlist_addtocart_font_color,
					'shortlist_text'			=> $shortlist_text,

					'removeall_text'			=> $removeall_text,
					'shortlist_removeall_background_color' => $shortlist_removeall_background_color,
					'removeall_font_color'      => $removeall_font_color,
					'discount_type'				=> $discount_type,
					'discount_arr'				=> json_encode($discount_arr),
					'wc_currency'				=> get_woocommerce_currency_symbol(),
					'cookie_names'				=> $cookie_delete,					
					'current_page'				=> $currentPage,
					'base_path'					=> $path,
					'ced_ps_nonce'				=> $ajax_nonce,
					'crossImageUrl'				=> CED_PS_PLUGIN_URL.'assets/images/close.png'				
			);
			wp_localize_script('ced-ps-js', 'global', $translation);

		}
		
		
		
		/**
		 * Adds a setting submenu tab named Product Shortlists to te Product menu
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		public function ced_ps_add_settings_page() {
		
			add_submenu_page( 'edit.php?post_type=product', __("Products Shortlists", 'product-shortlist'), __("Products Shortlists", 'product-shortlist'), 'manage_options', $this->id.'-settings', array($this, 'ced_ps_render_settings'));
		}

		/**
		 * Adds different tabs to the setting page
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		public function ced_ps_render_settings( $active_tab = '' ) 
		{
			if( isset( $_GET[ 'tab' ] ) ) {
            	$active_tab = $_GET[ 'tab' ];
        	}
        	else{
        		$active_tab = 'ced_ps_general_settings';
        	}
			?>
			<div class="ced-ps-main-wrapper">
				<div class="ced-ps-main-content">
					<div class="nav-wrapper ced-ps-setting-tabs">
						<h2 class="nav-tab-wrapper">
							<a href="?post_type=product&page=ced-ps-settings&tab=ced_ps_general_settings" class="nav-tab <?php if( $active_tab == 'ced_ps_general_settings' ) { echo 'nav-tab-active' ; } ?>"><?php _e( 'General Settings' , 'product-shortlist' ) ?></a>
							<a href="?post_type=product&page=ced-ps-settings&tab=ced_ps_shortlisted_products" class="nav-tab <?php if( $active_tab == 'ced_ps_shortlisted_products' ) { echo 'nav-tab-active' ; } ?>"><?php _e( 'Shortlisted Products' , 'product-shortlist' ) ?></a>
							<a href="?post_type=product&page=ced-ps-settings&tab=ced_ps_smtp_settings" class="nav-tab <?php if( $active_tab == 'ced_ps_smtp_settings' ) { echo 'nav-tab-active' ; } ?>"><?php _e( 'SMTP Settings' , 'product-shortlist' ) ?></a> 
							<a href="?post_type=product&page=ced-ps-settings&tab=ced_ps_email_template" class="nav-tab <?php if( $active_tab == 'ced_ps_email_template' ) { echo 'nav-tab-active' ; } ?>"><?php _e( 'Email Template Settings' , 'product-shortlist' ) ?></a>				
						</h2>
					</div>
					<?php
					include_once 'ps-settings.php';
					if ($active_tab == 'ced_ps_general_settings') {
						do_action( 'ced_ps_setting_html' );
					}
					elseif ($active_tab == 'ced_ps_shortlisted_products') {
						do_action( 'ced_ps_admin_shortlisted_products' );
					}
					elseif ($active_tab == 'ced_ps_smtp_settings'){
						do_action( 'ced_ps_email_smtp_settings' );
					}
					elseif ($active_tab == 'ced_ps_email_template'){
						do_action( 'ced_ps_email_template_settings' );
					}			
					else{
						do_action( 'ced_ps_setting_html' );
					}
					?>
				</div>
				<?php
				if(!session_id())
					session_start();
				if(!isset($_SESSION["ced_ps_hide_email"])):
					$actual_link = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					$urlvars = parse_url($actual_link);
					$url_params = $urlvars["query"];
				?>
				<div class="ced_ps_img_email_image">
					<div class="ced_ps_email_main_content">
						<div class="ced_ps_cross_image">
						<a class="button-primary ced_ps_cross_image" href="?<?php echo $url_params?>&ced_ps_close=true"></a>
						</div>
						<input type="text" value="" class="ced_ps_img_email_field" placeholder="<?php _e("enter your e-mail address","product-shortlist")?>"/> 
						<a id="ced_ps_img_send_email" href=""><?php _e("Know More","product-shortlist")?></a>
						<p></p>
						<div class="hide"  id="ced_ps_loader">	
							<img id="product-autoshare-loading-image" src="<?php echo plugins_url().'/product-shortlist/assets/images/ajax-loader.gif'?>" >
						</div>
						<div class="ced_ps_banner">
						<a target="_blank" href="https://cedcommerce.com/offers#woocommerce-offers"><img src="<?php echo plugins_url().'/product-shortlist/assets/images/ebay.jpg'?>"></a>
						</div>
					</div>
				</div>
				<?php endif;?>
			</div>
		<?php			
		}
		
		/**
		 * Handles ajax request to save general settings
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_save_general_settings() {
			$check_ajax = check_ajax_referer( 'ced-ps-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				$arg = $_POST;
				
				foreach ( $arg as $k => $val ) {
					if (is_array ( $val )) {
						foreach ( $val as $key => $v ) {
							$args [$k] [$key] = sanitize_text_field ( $v );
						}
					} else {
						$args [$k] = sanitize_text_field ( $val );
					}
				}
				
				if ( empty( $args['selected_pages']) || empty($args['selected_position'] ) || empty( $args['exp_time'] ) ) {
					echo _e('Please fill all the fields first !');
				} else if ( $args['exp_time'] == '0' || $args['exp_time'] == 0 ) {
					echo _e('Expiry Time mustn\'t be zero');
				} else {
					$result = update_option('ced_ps_general_settings', json_encode( $args ) );
					if ( $result ) {
						echo 'success';
						exit();
					} else {
						echo 'failed';
						exit();
					}
				}
			}
		}
		
		/**
		 * Handles ajax request to update tht shortlisted products user wise
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_update_shortlisted_records() {
			$check_ajax = check_ajax_referer( 'ced-ps-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) {
				global $wpdb;
				$shortlisted_products = array();
				$product_id 	= sanitize_text_field( $_POST['product_id'] );
				$user_ip		= sanitize_text_field( $_SERVER['REMOTE_ADDR'] );
				$user_id 		= get_current_user_id();
				$table = $wpdb->prefix.'ps_shortlist_log';
				if (! empty( $product_id ) && $user_id ) {
					$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id = $user_id" );
					
					if (!empty( $user_result )) 
					{
						foreach ($user_result as $key => $value) 
						{							
							if ( $value->date == date('Y-m-d') ) 
							{
								$shortlisted_products = json_decode($value->shortlisted_products);
								if (!in_array($product_id, $shortlisted_products)) 
								{
									$shortlisted_products[] = $product_id;
								}									
								$result = $wpdb->update( $table, array( 'date' => current_time( 'mysql' ), 'ip' => $user_ip , 'user_id' => $user_id, 'shortlisted_products' => json_encode($shortlisted_products) ) , array( 'id' => $value->id ) );
								if ($result) 
								{
									echo 'updated';
									die;
								}
							}
						}
						
						$shortlisted_products[] = $product_id;
						$table = $wpdb->prefix.'ps_shortlist_log';
						$result = $wpdb->insert( $table, array( 'date' => date('Y-m-d') , 'ip' => $user_ip , 'user_id' => $user_id, 'shortlisted_products' => json_encode($shortlisted_products)  ) );
						if ($result) 
						{
							echo 'inserted';
						}
																		
					}
					else
					{
						$shortlisted_products[] = $product_id;
						$table = $wpdb->prefix.'ps_shortlist_log';
						$result = $wpdb->insert( $table, array( 'date' => date('Y-m-d') , 'ip' => $user_ip , 'user_id' => $user_id, 'shortlisted_products' => json_encode($shortlisted_products)  ) );						
						if ($result) {
							echo 'inserted';
						}
					}						
				}
				wp_die();
			}
		}

		/**
		 * Handles ajax request to fetch the shortlisted products according to user in the front end
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return json(products)
		 */
		function ced_ps_get_shortlisted_products()
		{
			$shortlisted_products = array();
			$check_ajax = check_ajax_referer( 'ced-ps-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) 
			{
				global $wpdb;
				$user_id = $_POST['user_id'];
				$table = $wpdb->prefix.'ps_shortlist_log';
				$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id = $user_id" );
				if ( !empty($user_result) ) 
				{
					foreach ($user_result as $key => $value) 
					{
						$arr = json_decode($value->shortlisted_products);
						if ( !empty($arr) ) 
						{
							foreach ($arr as $key1 => $value1) 
							{
								$productObj = wc_get_product( $value1 );
								$size = 'shop_thumbnail';
								$product_type = $productObj->get_type();
								$atc_url = $productObj->add_to_cart_url();
								$atc_text = $productObj->add_to_cart_text();							
								$product_img_src = get_the_post_thumbnail( $value1, $size );
								$product_img_src = explode( 'src="', $product_img_src );
								$product_img_src = explode( '"', $product_img_src[1] )[0];								
							 	$shortlisted_products[$value1] = array('id' => $value1 , 'guid' => get_the_permalink($value1) ,
							 	 'image' => $product_img_src , 'title' => $productObj->post->post_title ,							 	
							 	'price' => $productObj->price , 'sku' => get_post_meta( $value1, '_sku') , 
							 	'atc_url' =>$atc_url , 'atc_text' => $atc_text , 'type' => $product_type); 
							}
						}							
					}					
					echo json_encode($shortlisted_products);			
				}
				else
				{
					echo "no shortlisted products";
				}				
			}
			wp_die();
		}

		/**
		 * Handles ajax request to remove shortlisted product
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_delete_shortlisted_product()
		{
			$check_ajax = check_ajax_referer( 'ced-ps-ajax-seurity-string', 'security_check' );
			if ( $check_ajax ) 
			{
				global $wpdb;
				$table = $wpdb->prefix.'ps_shortlist_log';
				if ($_POST['action'] == 'delete_shortlisted_product') 
				{					
					$user_id 	= $_POST['user_id'];
					$product_id = $_POST['product_id'];				
					$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id = $user_id" );
					foreach ($user_result as $key => $value) 
					{
						$arr = json_decode($value->shortlisted_products);
						$discount_arr = json_decode($value->discount);
						if ( !empty( $arr ) ) 
						{						
							foreach ($arr as $key1 => $value1) 
							{
								if ($value1 == $product_id) 
								{
									unset($arr[$key1]);
									break;
								}
							}
							$arr = array_values($arr);
							$result = $wpdb->update( $table, array( 'shortlisted_products' => json_encode($arr) ) , array( 'id' => $value->id ) );
						}
						if ( !empty( $discount_arr ) ) 
						{
							foreach ($discount_arr as $key2 => $value2) 
							{
								if( $value2->id == $product_id )
								{
									unset($discount_arr[$key2]);									
								}
							}
							$discount_arr = array_values($discount_arr);
							$result = $wpdb->update( $table, array( 'discount' => json_encode($discount_arr) ) , array( 'id' => $value->id ) );
						}
					}
				}
				else if($_POST['action'] == 'delete_all_shortlisted_products')
				{					
					$user_id 	= $_POST['user_id'];
					$result = $wpdb->update( $table, array( 'shortlisted_products' => '' , 'discount'=>'' ) , array( 'user_id' => $user_id ) );			
				}					
			}
			wp_die();
		}

		/**
		 * Handles ajax request to delete shortlisted products from admin side listing
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_delete_products_admin_side()
		{
			$user_id = $_POST['user_id'];
			$product_id = $_POST['product_id'];
			$date = $_POST['date'];			
			global $wpdb;
			$table = $wpdb->prefix.'ps_shortlist_log';
			$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id = $user_id" );
			foreach ($user_result as $key => $value) 
			{
				if ($value->date == $date) 
				{
					$arr = json_decode($value->shortlisted_products);
					$discount_arr = json_decode($value->discount);					
					if ( !empty($arr) ) {
						foreach ($arr as $key1 => $value1) 
						{							
							if ( $value1 == $product_id ) 
							{
								unset($arr[$key1]);
							}
						}
						$arr = array_values($arr);
						
						$result = $wpdb->update( $table, array( 'shortlisted_products' => json_encode($arr) ) , array( 'id' => $value->id ) );				
					}
					if ( !empty( $discount_arr ) ) 
					{
						foreach ($discount_arr as $key2 => $value2) 
						{							
							if( $value2->id == $product_id )
							{
								unset($discount_arr[$key2]);									
							}
						}
						$discount_arr = array_values($discount_arr);
						$result = $wpdb->update( $table, array( 'discount' => json_encode($discount_arr) ) , array( 'id' => $value->id ) );
					}
				}
			}
			echo $result;
			wp_die();
		}

		/**
		 * Handles ajax request provide discount to each product individually and user wise from admin end
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return string(success message)
		 */
		function ced_ps_add_discount_product_admin_side()
		{
			$user_id 	= $_POST['user_id'];
			$product_id = $_POST['product_id'];
			$date 		= $_POST['date'];	
			$row_id 	= $_POST['row_id'];	
			$discount 	= $_POST['discount'];
			$flag = 0;		
			global $wpdb;
			$table = $wpdb->prefix.'ps_shortlist_log';
			$user_result = $wpdb->get_results( "SELECT * FROM $table where id = $row_id and user_id != '' " );
			$arr = json_decode($user_result[0]->shortlisted_products);

			if (in_array($product_id, $arr)) 
			{
				$discount_array = array( 'id'=>$product_id , 'discount'=>$discount );
				$discount_price = json_decode($user_result[0]->discount);
				if (!empty($discount_price)) 
				{
					foreach ($discount_price as $key => $value) 
					{						
						if ( $value->id == $product_id ) 
						{
							$discount_price[$key] = array('id'=>$product_id , 'discount'=>$discount);
							$flag = 1;							
							break;
						}						
					}
					if( $flag == 0 )
					{
						$discount_array = array('id'=>$product_id , 'discount'=>$discount);
						$discount_price[] = $discount_array;
						
					}

				}
				else{
					$discount_array = array('id'=>$product_id , 'discount'=>$discount);
					$discount_price[] = $discount_array;
				}					
				$result = $wpdb->update( $table, array( 'discount' => json_encode($discount_price) ) , array( 'id' => $row_id ) );
				echo $result;
			}	
			wp_die();	
		}

		/**
		 * Function to save the smtp settings for mailing 
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_save_smtp_settings()
		{
			if (isset($_POST['save_smtp_settings'])) 
			{

				$uname = $_POST['uname'];
				$password = $_POST['password'];
				$sender_email = $_POST['sender_email'];
				$host = $_POST['host'];
				$port = $_POST['port'];
				$secure = $_POST['smtp_secure'];
				$url = esc_url($_POST['current_url']);
				$data = array('username'=>$uname , 'password'=>$password , 'from'=>$sender_email , 'host'=>$host , 'port'=>$port , 'secure'=>$secure);
				update_option('ced_ps_smtp_settings' , $data);
				wp_redirect($url);
				exit;
			}
		}

		/**
		 * Function to set the smtp settings for phpMailer
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_phpmailer_init( PHPMailer $phpmailer ) 
		{
			$data = get_option('ced_ps_smtp_settings');
			if( !empty($data) )
			{
				$phpmailer->IsSMTP();
			    $phpmailer->Host = $data['host'];
			    $phpmailer->Port = $data['port']; // could be different
			    $phpmailer->Username = $data['username']; // if required
			    $phpmailer->Password = $data['password']; // if required
			    $phpmailer->SMTPAuth = true; // if required
			    $phpmailer->SMTPSecure = $data['secure']; // enable if required, 'tls' is another possible value
				$phpmailer->From = $data['from'];		   		
			}
		}

		/**
		 * Handles ajax request to send mail to the users from admin end only when discount is offered
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return int(status)
		 */
		function ced_ps_send_mail_to_user()
		{
			$user_id = $_POST['user_id'];
			$product_id = $_POST['product_id'];
			$discount = $_POST['discount'];
			$user = get_user_by('id' , $user_id);			
			$to = $user->data->user_email;
			$data = get_option('ced_ps_email_template_settings');
			$data = json_decode($data);
			if($data->subject == '')
			{
				$subject = 'Great offer for your shortlisted products';				
			}
			else{
				$subject = $data->subject;
			}
			if($data->content == '')
			{
				$content = $this->ced_ps_email_template_design();				
			}
			else
			{
				$content = $data->content;
			}
			$content = stripslashes($content);
			$content = str_replace('%user%' , $user->data->display_name , $content);
			$content = str_replace('%product_name%' , get_the_title($product_id) , $content);
			$content = str_replace('%permalink%' , get_the_permalink($product_id) , $content);
			$content = str_replace('%site_url%' , site_url() , $content);
			$headers = array('Content-Type: text/html; charset=UTF-8');			
			$status = wp_mail($to, $subject, $content , $headers);
			return $status;
			wp_die();
		}

		/**
		 * Deletes multiple users at a time form admin end listing
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>		
		 */
		function ced_ps_bulk_delete()
		{
			global $wpdb;
			$product_id = $_POST['ids'];
			$user = $_POST['user'];
			$table = $wpdb->prefix.'ps_shortlist_log';
			foreach ($user as $key => $value) 
			{
				$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id = $value" );
				foreach ($user_result as $key1 => $value1) 
				{
					if (!empty(json_decode($value1->shortlisted_products))) 
					{
						$products = json_decode($value1->shortlisted_products);					
						foreach ($products as $key3 => $value3) 
						{
							if ($value3 == $product_id[$key]) {
								unset($products[$key3]);
							}								
						}
						$products = array_values($products);
						$result = $wpdb->update( $table, array( 'shortlisted_products' => json_encode($products) ) , array( 'id' => $value1->id ) );
					}
					if (!empty(json_decode($value1->discount))) 
					{
						$discount_arr = json_decode($value1->discount);				
						foreach ($discount_arr as $key4 => $value4) 
						{
							if ($value4->id == $product_id[$key]) {
								unset($discount_arr[$key4]);
							}								
						}
						$discount_arr = array_values($discount_arr);
						$result = $wpdb->update( $table, array( 'discount' => json_encode($discount_arr) ) , array( 'id' => $value1->id ) );
					}
				}
			}
			wp_die();			
		}

		/**
		 * Adds shortlist section on the page where the shortcode is placed
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return html(shortlisted section)
		 */
		function ced_ps_add_shortlist_section_shortcode() 
		{	
			$pages = 0;
			$saved_general_settings = get_option( 'ced_ps_general_settings' );
			$saved_general_settings = json_decode( $saved_general_settings );		
			$shortlist_html = sprintf( '<div class="%s"><div class="%s"><p class="%s">%s</P><button id="%s" class="%s">%s</button></div><span class="%s" id="%s" rel="nofollow"><span id="ced_ps_shortlisted_qty">%s</span><span class="%s">%s</span></span></div>',
					esc_html( 'ced_ps-'. $saved_general_settings->selected_position .'-shortlist-section-wrapper' ),
					esc_html( $saved_general_settings->selected_position .'-shortlist-section' ),
					esc_html( 'ced_ps_empty_section_text' ),
					__( 'Shortlist your wished items and add them here by clicking the <b>STAR</b> button.', 'product-shortlist' ),
					esc_html( 'ced_ps_remove_all' ),
					esc_html( 'button' ),
					esc_html( __( 'Remove All', 'product-shortlist' ) ),
					esc_html( 'ced_ps_'. $saved_general_settings->selected_position .'_shortlist_span' ),
					esc_html( 'ced_ps_shortlist_text' ),
					esc_html( 0 ),
					esc_html( __( 'ced_ps_shortlist_btn' ) ),
					esc_html( __( 'SHORTLIST', 'product-shortlist' ) )
			);
			echo $shortlist_html;	
		}

		/**
		 * Function to modify the product price after discount is provided
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>		 
		 */
		function ced_ps_show_discount_price_in_cart( $cart_object ) 
		{
		    $user_logged_in = is_user_logged_in();
			$current_user_id = is_user_logged_in() ? get_current_user_id() : null ; 
			if ($user_logged_in) 
			{
				$saved_general_settings = get_option( 'ced_ps_general_settings' );
				$saved_general_settings = json_decode( $saved_general_settings );
				$discount_type = $saved_general_settings->discount_type;
				global $wpdb;
				$table = $wpdb->prefix.'ps_shortlist_log';
				$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id = $current_user_id" );
				if (!empty($user_result)) 
				{
					foreach ($user_result as $key => $value) 
					{
						if (!empty(json_decode($value->discount))) 
						{
							$discount = json_decode($value->discount);
							foreach ( $cart_object->cart_contents as $key2 => $value2 ) 
						    {
						    	foreach ($discount as $key1 => $value1) 
								{
									if( $value1->id == $value2['product_id'] && $value1->discount != '' )
									{
										if ($discount_type == 'rate') 
							    		{
							    			$value2['data']->price = $value2['data']->price - $value1->discount*$value2['data']->price/100;
							    		}
								    	else
								    	{
											$value2['data']->price = $value2['data']->price - $value1->discount;						
										}
									}
								}						    	
						    }							
						}
					}
				}
			}					   
		}

		/**
		 * Simple Email template for the admin  to send as a mail
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 */
		function ced_ps_email_template_design()
		{
			$data = get_option('ced_ps_email_template_settings');
			$email_tpl = '<body style="margin: 0; padding: 0;">
							<table  align="center" border="1" cellpadding="0" cellspacing="0" width="600" style="border: none;">
								<tr style="text-align: center;">
									<td style="padding-bottom: 10px; border: none; padding-bottom: 20px; padding-top: 20px; padding-right: 20px; padding-left: 20px; color: #ffffff; background-color: #4073b5; border: none; font-size: 28px;">
										Discount on %product_name%
										
									</td>
								</tr>								
								<tr style="text-align: center;">
									<td style="
									  padding-top: 40px; padding-bottom: 25px; border: 
									none; font-size: 20px; padding-right: 20px; padding-left: 20px;">
										Hey %user%,
										You have been given a dicount on this product.
									</td>
								</tr>
								<tr style="text-align: center;">
									<td style="color: #ffffff; padding-top: 20px; padding-bottom: 50px; border: 
									none; font-size: 20px;">
										<a style="  background-color: #4073b5;
									    border: medium none;
									    border-radius: 6px;
									    color: #ffffff;
									    font-size: 20px;
									    padding: 20px; padding-left: 25px; padding-right: 25px;" href="%permalink%">Login to avail Exciting Offers</a>
									</td>
								</tr>
								</tr>
									<tr style="text-align: center;">
									<td style=" color: #ffffff; padding-bottom: 30px; padding-right: 20px; padding-top: 30px; padding-left: 20px; background-color: #4073b5; border: none; font-size: 20px;">
										Sent by %site_url%
										
									</td>
								</tr>
							</table>
						</body>';
			return $email_tpl;
		}

		/**
		 * Function to save the email template settings 
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 * @return void
		 */
		function ced_ps_save_email_tpl_settings()
		{
			if (isset($_POST['save_email_template_settings'])) 
			{				
				$content = $_POST['ced_ps_maileditor'];
				$subject = sanitize_text_field($_POST['ced_ps_mail_subejct']);				
				$url = $_POST['email_current_url'];
				$data = array('content'=>$content , 'subject'=>$subject);
				update_option('ced_ps_email_template_settings' , json_encode($data));
				wp_redirect($url);
				exit;
			}
		}

		/**
		 * Function for enabling more editing options in the tinymce editor
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 */
		function ced_ps_enable_more_buttons($buttons) {

			$buttons[] = 'fontselect';
			$buttons[] = 'fontsizeselect';
			$buttons[] = 'styleselect';
			$buttons[] = 'backcolor';
			$buttons[] = 'newdocument';
			$buttons[] = 'cut';
			$buttons[] = 'copy';
			$buttons[] = 'hr';		
			return $buttons;
		}

		/**
		 * Function for enabling buttons and toolbars in the tinymce editor
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 */
		function ced_ps_add_mce_button() 
		{

			// check user permissions
			if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
				return;
			}
			// check if WYSIWYG is enabled
			if ( 'true' == get_user_option( 'rich_editing' ) ) {
				add_filter( 'mce_external_plugins', array($this , 'ced_ps_add_tinymce_plugin' ));
				add_filter( 'mce_buttons', array($this , 'ced_ps_register_mce_button' ));

				add_filter( 'mce_buttons_3' , array( $this , 'ced_ps_enable_more_buttons' ) );
			}
		}
		
		/**
		 * Includes the js file for tinymce button
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 */		
		function ced_ps_add_tinymce_plugin( $plugin_array ) 
		{
			$plugin_array['ced_ps_mce_button'] = plugins_url( '../assets/js/ced_ps_editor.min.js',__FILE__ );
			return $plugin_array;
		}

		/**
		 * Function for adding custom button to the tinymce button
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 */
		function ced_ps_register_mce_button( $buttons ) 
		{
			array_push( $buttons, 'ced_ps_mce_button' );
			return $buttons;
		}

		/**
		 * Function for exporting all shortlisted products as a csv report
		 *
		 * @category function
		 * @author CedCommerce <http://cedcommerce.com>
		 */
		function ced_ps_export_csv()
		{
			global $wpdb;
			$table = $wpdb->prefix.'ps_shortlist_log';
			if(strpos($_SERVER['REQUEST_URI'], "&page=ced-ps-settings&tab=ced_ps_shortlisted_products&ced_ps_export=true"))
			{			
				$user_result = $wpdb->get_results( "SELECT * FROM $table where user_id != '' " );	
				if ( !empty($user_result) ) 
				{
					foreach ( $user_result as $key => $value ) 
					{
						if ( !empty(json_decode($value->shortlisted_products)) ) 
						{
							$shortlisted_products = json_decode($value->shortlisted_products);
							$discount_arr = json_decode($value->discount);
							$user = get_user_by('id' , $value->user_id);			
							foreach ( $shortlisted_products as $key1 => $value1 ) 
							{
								$c = 0 ;
								if (isset($discount_arr)) {
									foreach ($discount_arr as $key2 => $value2) 
									{
										$l = count($discount_arr);
										if($value1 == $value2->id)
										{
											$c++ ;
											if( $value2->discount != '' )
											{
												$data[] = array('user_id'=>$value->user_id , 'user_email'=>$user->data->user_email , 'product_id'=>$value1 , 'product_name'=>get_the_title($value1) , 'discount'=>$value2->discount );
											}
											else
											{
												$data[] = array('user_id'=>$value->user_id , 'user_email'=>$user->data->user_email , 'product_id'=>$value1 , 'product_name'=>get_the_title($value1) , 'discount'=>0 );
											}
											break;	
										}
																				
									}
									if($c == 0){										
										$data[] = array('user_id'=>$value->user_id , 'user_email'=>$user->data->user_email , 'product_id'=>$value1 , 'product_name'=>get_the_title($value1) , 'discount'=>0 );
									}
								}
								else{
									$data[] = array('user_id'=>$value->user_id , 'user_email'=>$user->data->user_email , 'product_id'=>$value1 , 'product_name'=>get_the_title($value1) , 'discount'=>0 );
								}
							}
						}
					}
					
					$csv_fields=array();
					if (!empty($data)) 
					{
						foreach ($data as $key => $value) {
							foreach ($value as $key1 => $value1) {
								$csv_fields[] = $key1;
							}
							break;
						}						
						$filename = 'Shortlst_Report.csv';
						$output_handle = fopen('php://output', 'w');
						 
						header("Content-type: text/csv");
						header("Content-Disposition: attachment; filename=$filename");
						header("Pragma: no-cache");
						header("Expires: 0");

						// Insert header row
						fputcsv( $output_handle, $csv_fields );

						// Parse results to csv format
						foreach ($data as $key=>$value) 
						{
							$leadArray = (array) $value; // Cast the Object to an array
							// Add row to file
							fputcsv( $output_handle, $leadArray );
						}
						die();
					}					
				}
			}
		}				
	}
	new Product_Shortlist ();
}
?>