<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Adds shortlist section at shop page or single product page.
 *
 * @category function
 * @author CedCommerce <http://cedcommerce.com>
 * @return void
 */
function ced_ps_setting_html() {
	global $wpdb;
	$f = 0;
	$saved_general_settings = get_option( 'ced_ps_general_settings' );
	$saved_general_settings = json_decode( $saved_general_settings );
	
	/****************************** 
	 * Initializing variables 
	 * ***************************/
	$exp_time = '';
	$selected_pages = ''; $selected_position = '';
	$single_selected = '';
	$top_selected = ''; $bottom_selected = '';
	$left_selected = ''; $right_selected = '';
	$discount_type = ''; $fixed_selected = '';
	$percentage_selected = '';
	$shortlist_addtocart_background_color = 'red';$shortlist_addtocart_font_color='white';
	$shortlist_background_color = 'red';$shortlist_font_color='white';
	$shortlist_text = 'SHORTLIST';$removeall_text='REMOVE ALL';
	$removeall_font_color = 'white';$shortlist_removeall_background_color='red';
	/******************************************
	 * Assignment of settings to variables 
	 * ****************************************/
	if ( ! empty( $saved_general_settings ) ) {
		$selected_pages 	= $saved_general_settings->selected_pages;
		$selected_position 	= $saved_general_settings->selected_position;
		$exp_time 			= $saved_general_settings->exp_time;
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

	/******************************************
	 * Assigns selected values in page setting 
	 * ****************************************/
	if (! empty( $selected_pages ) ) {
		if ( is_array( $selected_pages ) || is_object( $selected_pages ) ) {			 
			if ( in_array( 'single', $selected_pages ) ) {
				$single_selected = 'selected="selected"';
			}
		}
	}
	
	/***********************************************
	 * Assigns selected values in positions setting 
	 * ********************************************/
	if (! empty( $selected_position ) ) {
		if ( $selected_position == 'top' ) {
			$top_selected = 'selected="selected"';
		}
		elseif ( $selected_position == 'bottom' ) {
			$bottom_selected = 'selected="selected"';
		}
		elseif ( $selected_position == 'left' ) {
			$left_selected = 'selected="selected"';
		}
		elseif ( $selected_position == 'right' ) {
			$right_selected = 'selected="selected"';
		}
	}

	/***********************************************
	 * Assigns selected values in positions setting 
	 * ********************************************/
	if (! empty( $saved_general_settings->discount_type ) ) {
		$discount_type 		= $saved_general_settings->discount_type;
		if ( $discount_type == 'fixed' ) {
			$fixed_selected = 'selected="selected"';
		}
		elseif ( $discount_type == 'rate' ) {
			$percentage_selected = 'selected="selected"';
		}		
	}

	if( $shortlist_background_color != '' || $shortlist_addtocart_background_color != '' || $shortlist_font_color != '' || $shortlist_addtocart_font_color != '' || $shortlist_text != '' || $removeall_text != '' || $removeall_font_color != '' || $shortlist_removeall_background_color != '' )
	{
		$f = 1;
	}
	if ($selected_pages == '') {
		$selected_pages = array();
	}
	?>
	<div class="<?php echo CED_PS_PREFIX; ?>_container">
		<div id="<?php echo CED_PS_PREFIX; ?>_message"></div>
		<img id="<?php echo CED_PS_PREFIX; ?>_loading_wrapper" src="<?php echo CED_PS_PLUGIN_URL.'assets/images/loading.gif';?>" width="40" height="40">
		<!-- 	Content Wrapper Starts Here -->
		<table class=" <?php echo CED_PS_PREFIX; ?>_content_wrapper form-table">
			
			<!-- Title Wrapper Starts Here -->
			<tr>
				<td colspan="2"><h2><?php echo _e('Product Shortlist Setting', 'product-shortlist');?></h2>
				<p class="<?php echo CED_PS_PREFIX; ?>_note"><i>	<?php echo _e('Shortlist your WooCommerce products', 'product-shortlist');?></p></td>
			</tr>
			<!-- Title Wrapper Ends Here -->
			
			<tr>
				<th>
					<b><?php echo _e('Page Setting', 'product-shortlist');?></b>
				</th>
				<td class="<?php echo CED_PS_PREFIX; ?>_field">
				
					<select id="<?php echo CED_PS_PREFIX; ?>_select_pages" multiple="multiple">
						<?php 
						$pages = $wpdb->get_results( "SELECT option_value , option_name FROM $wpdb->options WHERE option_name LIKE 'woocommerce%' AND option_name LIKE '%_page_id' " );
						foreach ($pages as $key => $value) 
						{
							if($value->option_value != '')
							{
								$name = explode( '_', $value->option_name);
								$pagename = get_the_title($value->option_value);
								?>
								<option value="<?php echo $name[1]; ?>" <?php if(in_array($name[1], $selected_pages)){ echo "selected" ; } ?>><?php echo _e( $pagename.' Page', 'product-shortlist');?></option>
								<?php
							}							
						}
						?>						
						<option value="single" <?php echo $single_selected;?>><?php echo _e( 'Product Detail Page', 'product-shortlist');?></option>
					</select>
					
					<p class="<?php echo CED_PS_PREFIX; ?>_note"><i><?php _e('Select page where you want to apply shortlist section','product-shortlist') ?></i></p>
					
					<span id="<?php echo CED_PS_PREFIX; ?>_pages_error" class="<?php echo CED_PS_PREFIX; ?>_error"></span>
				</td>
			</tr>
			
			<tr>
				<th>
					<b><?php echo _e('Position Setting', 'product-shortlist');?></b>
				</th>
				<td  class="<?php echo CED_PS_PREFIX; ?>_field">
					<select id="<?php echo CED_PS_PREFIX; ?>_select_position">
						<option data="top" <?php echo $top_selected;?>><?php echo _e('Top', 'product-shortlist');?></option>
						<option data="bottom" <?php echo $bottom_selected;?>><?php echo _e('Bottom', 'product-shortlist');?></option>
						<option data="left" <?php echo $left_selected;?>><?php echo _e('Left', 'product-shortlist');?></option>
						<option data="right" <?php echo $right_selected;?>><?php echo _e('Right', 'product-shortlist');?></option>
					</select>
					<p class="<?php echo CED_PS_PREFIX; ?>_note"><i><?php _e('Position where shortlist section will show','product-shortlist') ?></i></p>
					
					<span id="<?php echo CED_PS_PREFIX; ?>_position_error" class="<?php echo CED_PS_PREFIX; ?>_error"></span>
				</td>
			</tr>
			
			<tr>
				<th>
					<b><?php echo _e('Discount Type', 'product-shortlist');?></b>
				</th>
				<td  class="<?php echo CED_PS_PREFIX; ?>_field">
					<select id="<?php echo CED_PS_PREFIX; ?>_select_discount_type">
						<option data="fixed" <?php echo $fixed_selected;?>><?php echo _e('Fixed Discount', 'product-shortlist');?></option>
						<option data="rate" <?php echo $percentage_selected;?>><?php echo _e('Percentage Discount', 'product-shortlist');?></option>			
					</select>
					<p class="<?php echo CED_PS_PREFIX; ?>_note"><i><?php _e('Select type of discount to be provided','product-shortlist') ?></i></p>
				</td>
			</tr>

			<tr>
				<th>
					<b><?php echo _e('Time Period', 'product-shortlist');?></b>
				</th>
				<td  class="<?php echo CED_PS_PREFIX; ?>_field">
					<input type="number" id="<?php echo CED_PS_PREFIX; ?>_expiry_time" min="1" step="1" value="<?php if (! empty( $exp_time ) ) { echo $exp_time; } else{ echo '1'; }?>">
					<span><?php echo _e( 'In Days', 'product-shortlist' );?></span>
					<p class="<?php echo CED_PS_PREFIX; ?>_note"><i><?php _e('Expiry time(days) to show shortlisted products','product-shortlist') ?></i></p>
					<span id="<?php echo CED_PS_PREFIX; ?>_exp_error" class="<?php echo CED_PS_PREFIX; ?>_error"></span>
				</td>
			</tr>

			<tr>
				<th>
					<b><?php echo _e('Shortcode for Shortlist Section', 'product-shortlist');?></b>
				</th>
				<td  class="<?php echo CED_PS_PREFIX; ?>_field">
					<input type="text" class="regular-text" value="[ced_ps_product_shortlist_section]" readonly></input>
					<p class="<?php echo CED_PS_PREFIX; ?>_note"><i><?php _e('Copy & Paste this shortcode on any page for the product shorlist section ','product-shortlist') ?></i></p>
				</td>
			</tr>
		
			<tr>
				<th>
					<b><?php echo _e('Text for Shortlist Section' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="regular-text" maxlength="15" id="ced_ps_save_shortlist_text" value="<?php echo $shortlist_text; ?>"></input>
				</td>
			</tr>
			<tr>
				<th>
					<b><?php echo _e('Choose Shortlist Buttton Font Color' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="ced-ps-color-picker" id="shortlist-font-color" value="<?php echo $shortlist_font_color; ?>"></input>
				</td>
			</tr>
			<tr>
				<th>
					<b><?php echo _e('Choose Background Color for shortlist section' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="ced-ps-color-picker" id="shortlist-background-color" value="<?php echo $shortlist_background_color; ?>"></input>
				</td>
			</tr>

			<tr>
				<th>
					<b><?php echo _e('Choose Add to Cart Button Font Color' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="ced-ps-color-picker" id="shortlist-addtocart-font-color" value="<?php echo $shortlist_addtocart_font_color; ?>"></input>
				</td>
			</tr>
			<tr>
				<th>
					<b><?php echo _e('Choose Background Color for Add to Cart Button' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="ced-ps-color-picker" id="shortlist-addtocart-background-color" value="<?php echo $shortlist_addtocart_background_color; ?>"></input>
				</td>
			</tr>
			
			<tr>
				<th>
					<b><?php echo _e('Text for Remove all Button' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="regular-text" maxlength="15" id="ced_ps_save_removeall_text" value="<?php echo $removeall_text; ?>"></input>
				</td>
			</tr>
			<tr>
				<th>
					<b><?php echo _e('Choose Remove All Button Font Color' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="ced-ps-color-picker" id="removeall-font-color" value="<?php echo $removeall_font_color; ?>"></input>
				</td>
			</tr>
			<tr>
				<th>
					<b><?php echo _e('Choose Background Color for Remove All Button' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="ced-ps-color-picker" id="shortlist-removeall-color" value="<?php echo $shortlist_removeall_background_color; ?>"></input>
				</td>
			</tr>
			
			<tr>
				<td>
					<input type="button" value="Save Changes" class="button button-primary" id="<?php echo CED_PS_PREFIX; ?>_save_general_settings">
				</td>
			</tr>
		</table>			
		<!-- 	Content Wrapper Ends Here -->
		
	</div>
<?php 
}
add_action( 'ced_ps_setting_html', 'ced_ps_setting_html', 10);

/**
 * Add a list of shortlisted products to the shorlist product tab in setting page
 *
 * @category function
 * @author CedCommerce <http://cedcommerce.com>
 * @return void
 */
function ced_ps_admin_shortlisted_products()
{
	global $wpdb;
	$user_id = get_current_user_id();
	$flag = 0 ;
	$f = 0;
	$table = $wpdb->prefix.'ps_shortlist_log';
	$user_result = $wpdb->get_results( " SELECT * FROM $table where user_id != '' ");
	?>
	<h2><?php _e( 'Shortlisted Products' , 'product-shortlist' ) ?></h2>
	<div id="<?php echo CED_PS_PREFIX; ?>_success_message"></div>
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<label class="screen-reader-text" for="bulk-action-selector-top">Select bulk action</label>

			<select id="bulk-action-selector-top actions" name="action" >
			<option value="-1"><?php _e('Bulk Actions' , 'product-shortlist'); ?></option>
				<option class="hide-if-no-js" value="delete"><?php _e('Delete' , 'product-shortlist'); ?></option>				
			</select>
			<input type="button" value="Apply" name="bulk_action" class="button action" id="do-subscriber-action" onclick="ced_ps_do_bulk_actions()">
			<?php if(!empty($user_result)){
				foreach ($user_result as $key => $value) {
					if (!empty(json_decode($value->shortlisted_products))) {
						$f = 1;
						break;
					}
				}	
			} ?>
			<?php if($f == 1){
				?>
				<a target="_blank" name="ced_ps_export_csv" class="button-primary ced_ps_export_csv" id="ced_ps_export_csv" href="edit.php?post_type=product&amp;page=ced-ps-settings&amp;tab=ced_ps_shortlisted_products&amp;ced_ps_export=true">Export CSV</a>				
				<?php
				} ?>
			
		</div>
	</div>
	<table class="wp-list-table widefat fixed striped posts ced-ps-shortlisted-products">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column">
					<input id="cb-select-all-1" type="checkbox">
				</td>
				<th id="thumb">
					<span class="wc-image tips"><?php _e('Image' , 'product-shortlist'); ?></span>
				</th>
				<th id="name">
					<span><?php _e('Name' , 'product-shortlist'); ?></span>
				</th>
				<th id="price">
					<span><?php _e('Price' , 'product-shortlist'); ?></span>
				</th>
				<th id="username">
					<span><?php _e('Username' , 'product-shortlist'); ?></span>
				</th>
				<th id="discount">
					<span><?php _e('Discount' , 'product-shortlist'); ?></span>
				</th>
				<th id="date">
					<span><?php _e('Date' , 'product-shortlist'); ?></span>
				</th>
				<td id="prod_action">
					<span><?php _e('Action' , 'product-shortlist'); ?></span>
				</td>
			</tr>
		</thead>
		<tbody id="the-list">
			<?php 		
			foreach ($user_result as $key => $value) 
			{
				$shortlisted_products = json_decode($value->shortlisted_products);
				$discount_price       = json_decode($value->discount);
				if ( !empty( $shortlisted_products ) ) 
				{
					foreach ($shortlisted_products as $key1 => $value1) 
					{
						$flag = 0;
						?>
						<tr id="<?php echo 'shortlisted_product_'.$value->user_id.$value1 ?>">
							<th class="check-column">
								<input id="<?php echo 'cb_Select_'.$value1 ; ?>" type="checkbox" value="<?php echo $value1; ?>" data-key="<?php echo $value1; ?>" data-userid="<?php echo $value->user_id; ?>" name="shortlist_product[]">
							</th>
							<td data-colname="Image" class="thumb column-thumb">
								<?php 
								set_post_thumbnail_size(50 , 50);
								if ( !get_the_post_thumbnail($value1) ) {
									?>
									<img class="attachment-post-thumbnail size-post-thumbnail wp-post-image" width="50px" height="50px" src="<?php echo CED_PS_PLUGIN_URL.'assets/images/placeholder.png'; ?>">
									<?php
								}
								else{
									echo get_the_post_thumbnail($value1) ;
								}
								?>
							</td>
							<td class="name column-name has-row-actions column-primary" data-colname="Name">
								<strong>
									<?php echo get_the_title($value1); ?>
								</strong>
								<div class="row-actions">
									<span class="id">ID: <?php echo $value1;  ?> | </span>	
									<span class="trash">
										<a aria-label="<?php echo 'Delete'.get_the_title($value1); ?>” to the Trash" class="submitdelete" href="javascript:void(0)" id="delete_admin_side" data-userid="<?php echo $value->user_id; ?>" data-date="<?php echo $value->date; ?>" data-pid="<?php echo $value1; ?>" ><?php _e('Delete' , 'product-shortlist'); ?>
										</a>
									</span>
								</div>								
							</td>
							<td data-colname="Price" class="price column-price">					
									<span class="woocommerce-Price-amount amount">
										<span class="woocommerce-Price-currencySymbol">$</span><?php $_product = wc_get_product( $value1 );
										if($_product->price != ''){
											echo $_product->price;
										}
										else{
											echo '0';
										}
										?>
								</span>								
							</td>
							<td class="name column-name has-row-actions column-primary" data-colname="Name">
								<strong>
									<?php $user = get_user_by( 'id' , $value->user_id );
									echo $user->user_email; ?>
								</strong>
							</td>
							<td class="name column-name has-row-actions column-primary" data-colname="Name">
								<input type="text" data-pid="<?php echo $value1; ?>" data-userid="<?php echo $value->user_id; ?>" data-date="<?php echo $value->date; ?>" data-rowid="<?php echo $value->id ?>" id="product_discount_admin_side" class="product_discount_admin_side" value="<?php if(!empty($discount_price)){
										foreach ($discount_price as $key2 => $value2) {
											if ($value2->id == $value1 && ($value2->discount != '' || $value->discount != 0)) {
												$flag = 1; 
												echo $value2->discount;
											}
										}
									} ?>" ></input>
								<div class="row-actions">
									<span class="id"><?php _e('Enter Discount' , 'product-shortlist'); ?></span>
								</div>
							</td>
							<td data-colname="Date" class="date column-date">					<br>
								<abbr title="<?php echo $value->date; ?>"><?php echo $value->date; ?></abbr>
							</td>
							<td>								
							<?php if($flag == 1 ){
								?>
									<input type="button" class="button button-primary available_discount" data-userid="<?php echo $user_id; ?>" data-pid="<?php echo $value1; ?>" id="admin-send-mail" value="Send Mail">
								<?php }else{
									?>
									<input type="button" class="button button-primary not_available_discount" data-userid="<?php echo $user_id; ?>" data-pid="<?php echo $value1; ?>" id="admin-send-mail" value="Send Mail">
									<?php
									} ?>
								<img id="<?php echo CED_PS_PREFIX.'_loading_wrapper_'.$value->user_id.$value1; ?>" style="display:none;" src="<?php echo CED_PS_PLUGIN_URL.'assets/images/loading.gif';?>" width="20" height="20">
							</td>
						</tr>
						<?php
					}
				}
			}
			
			 ?>
		</tbody>
	</table>
	<?php
}
add_action( 'ced_ps_admin_shortlisted_products' , 'ced_ps_admin_shortlisted_products' , 10);

/**
 * Adds a smtp setting page under smtp setting tab
 *
 * @category function
 * @author CedCommerce <http://cedcommerce.com>
 * @return void
 */
function ced_ps_email_smtp_settings()
{
	$data = get_option('ced_ps_smtp_settings');
	?>
	<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")) ?>">
		<input type="hidden" name="action" value="save_smtp_settings" />
		<input type="hidden" name="current_url" value="<?php echo $_SERVER['REQUEST_URI']?>" />
		<table class="form-table">			
			<tr>
				<th>
					<label><?php _e('Sender' , 'product-shortlist'); ?></label>					
				</th>
				<td>
					<input type="email" name="sender_email" id="sender_email" class="regular-text" value="<?php echo $data['from'] ?>"></input>
					<p class="description"><?php _e('Senders Email-Id' , 'product-shortlist'); ?></p>
				</td>
			</tr>

			<tr>
				<th>
					<label><?php _e('Host' , 'product-shortlist'); ?></label>					
				</th>
				<td>
					<input type="text" name="host" value="<?php echo $data['host'] ?>" id="host" class="regular-text"></input>
					<p class="description"><?php _e('SMTP Host Server' , 'product-shortlist'); ?></p>
				</td>
			</tr>

			<tr>
				<th>
					<label><?php _e('Port' , 'product-shortlist'); ?></label>					
				</th>
				<td>
					<input type="text" name="port" value="<?php echo $data['port'] ?>" id="port" class="regular-text"></input>
					<p class="description"><?php _e('SMTP port for the server' , 'product-shortlist'); ?></p>
				</td>
			</tr>

			<tr>
				<th>
					<label><?php _e('Username' , 'product-shortlist'); ?></label>					
				</th>
				<td>
					<input type="text" name="uname" value="<?php echo $data['username'] ?>" id="uname" class="regular-text"></input>
					<p class="description"></p>
				</td>
			</tr>

			<tr>
				<th>
					<label><?php _e('Password' , 'product-shortlist'); ?></label>					
				</th>
				<td>
					<input type="text" name="password" value="<?php echo $data['password'] ?>" id="password" class="regular-text"></input>
					<p class="description"></p>
				</td>
			</tr>

			 <tr>
				<th>
					<label><?php _e('Set SMTP Secure' , 'product-shortlist'); ?></label>
				</th>
				<td>
					<select class="postform"  id="smtp_secure" name="smtp_secure" >
						<option value="tls" <?php if($data['secure']=='tls'){ ?>selected="selected"<?php } ?>><?php _e('tls' , 'product-shortlist'); ?></option>
						<option value="ssl" <?php if($data['secure']=='ssl'){ ?>selected="selected"<?php } ?>><?php _e('ssl' , 'product-shortlist'); ?></option>
						
					</select>
				</td>
			</tr>
		</table>
		<input type="submit" name="save_smtp_settings" value="Save Settings" id="save_smtp_settings" class="button button-primary"></input>
	</form>
	<?php
}
add_action( 'ced_ps_email_smtp_settings' , 'ced_ps_email_smtp_settings' , 10);

/**
 * Adds email template setting page for template editing
 *
 * @category function
 * @author CedCommerce <http://cedcommerce.com>
 * @return void
 */
function ced_ps_email_template_settings()
{
	$content = '';	
	$data = get_option('ced_ps_email_template_settings');
	$data = json_decode($data);
	if ( isset($data->content) && $data->content != '' ) {
		$content = $data->content;
	}
	else{
		$ced_ps_obj = new Product_Shortlist();
		$content = $ced_ps_obj->ced_ps_email_template_design();
	}
	if(isset($data->subejct) && $data->subject != '')
	{
		$subject = $data->subject;
	}else{
		$subject = 'Great offer for your shortlisted products';
	}
	?>
	<form method="post" action="<?php echo esc_url(admin_url("admin-post.php")) ?>">
		<input type="hidden" name="action" value="save_email_template_settings" />
		<input type="hidden" name="email_current_url" value="<?php echo $_SERVER['REQUEST_URI']?>" />
		<table class="form-table">			
			<tr>
				<th>
					<b><?php echo _e('Subject of the mail' , 'product-shortlist') ?></b>
				</th>
				<td>
					<input type="text" class="ced-ps-mail-subject regular-text" id="ced_ps_mail_subejct" value="<?php echo $subject ?>" name="ced_ps_mail_subejct"></input>
				</td>
			</tr>
			<tr>
				<th>
					<b><?php echo _e('Email Content' , 'product-shortlist') ?></b>
				</th>
				<td>
					<?php 
					remove_action( 'media_buttons', 'media_buttons' );
					wp_editor(stripslashes($content) , 'ced_ps_maileditor' , array()) ?>
					<p class="description"><?php _e('Message of the Email' , 'product-shortlist'); ?></p>
				</td>
			</tr>
		</table>
		<input type="submit" name="save_email_template_settings" value="Save Settings" id="save_email_template_settings" class="button button-primary"></input>
	</form>
	<?php
}

add_action( 'ced_ps_email_template_settings' , 'ced_ps_email_template_settings' , 10 );

?>