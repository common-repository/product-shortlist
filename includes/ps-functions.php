<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'ced_ps_add_shop_shortlist_icon' ) ) {

	/**
	 * Get the shortlist icon for the shop loop according to saved setting.
	 * If shop page is chosen in setting then the shortlist icone will be shown at shop page.
	 *
	 * @category function
	 * @author CedCommerce <http://cedcommerce.com>
	 */
	function ced_ps_add_shop_shortlist_icon() 
	{
		global $product;
		$pages = 0;
		$c = 0;
		$current_url = $_SERVER['REQUEST_URI'];
		$arr = explode('/', $current_url);
		$arr = array_filter( $arr );
		$arr = array_values( $arr );
		$current_page = trim($arr[count($arr)-1]);
		$current_page = preg_replace('/[^A-Za-z0-9]/', '', $current_page);
		
		$saved_general_settings = get_option( 'ced_ps_general_settings' );
		$saved_general_settings = json_decode( $saved_general_settings );
		if ( ! empty( $saved_general_settings ) ) {
			if ( ! empty( $saved_general_settings->selected_pages ) ) {				
				
				foreach ($saved_general_settings->selected_pages as $key => $value) {
					if($current_page == $value)
					{
						$pages = 1;
					}
				}
				if ($pages != 1) {
					if (in_array('single', $saved_general_settings->selected_pages)) {
						$pages = is_product();
					}
				}
									
			}				
		
			if ( $pages  ) {
				$productObj = new WC_Product( $product->id );
				$size = 'shop_thumbnail';
				$product_img_src = get_the_post_thumbnail( $product->id, $size );
				if (!empty($product_img_src)) 
				{
					$product_img_src = explode( 'src="', $product_img_src );
					$product_img_src = explode( '"', $product_img_src[1] )[0];
				}
				else{
					$product_img_src = CED_PS_PLUGIN_URL.'assets/images/placeholder.png' ;
				}				
				$shortlist_html = sprintf(
						'<a rel="nofollow" class="ced_ps_add_to_shortlist" id="%s" href="javascript:void(0);" data-atc_url="%s" data-quantity="%s" data-title="%s" data-id="%s" data-sku="%s" data-atc_text="%s" data-price="%s" data-type="%s" data-user_id="%s" data-guid="%s" title="%s" data-image="%s" >',
						esc_html( 'ps_product-'.$product->id ),
						esc_url( $product->add_to_cart_url() ),
						esc_attr( isset( $quantity ) ? $quantity : 1 ),
						esc_attr( $product->post->post_title ),
						esc_attr( $product->id ),
						esc_attr( $product->get_sku() ),
						esc_html( $product->add_to_cart_text() ),
						esc_attr( $productObj->price ),
						esc_attr( $product->product_type ),
						esc_attr( get_current_user_id() ),
						esc_url( $product->get_permalink() ),
						esc_html( __('Add to Shortlist', 'product-shortlist' ) ),
						esc_url( $product_img_src )
						);
				
				$shortlist_html .= '<span class="ced_ps_shortlist_icon">';
				
				$shortlist_html .= '<svg width="35px" height="35px" viewBox="0 0 300 275" xmlns="http://www.w3.org/2000/svg" version="1.1">';
				$shortlist_html .= '<polygon fill="#ffffff" stroke="#555555" stroke-width="15" points="150,25  179,111 269,111 197,165, 223,251  150,200 77,251  103,165, 31,111 121,111"/>';
				$shortlist_html .= '</svg>';
				$shortlist_html .= '</span>';
				$shortlist_html .= '</a>';

				echo $shortlist_html;
			}
		}
	}
}
add_action( 'woocommerce_before_shop_loop_item', 'ced_ps_add_shop_shortlist_icon', 9.999 );


if ( ! function_exists( 'ced_ps_add_single_item_shortlist_icon' ) ) {

	/**
	 * Get the shortlist icon for the single product according to saved setting.
	 * If shop page is chosen in setting then the shortlist icone will be shown at shop page.
	 *
	 * @category function
	 * @author CedCommerce <http://cedcommerce.com>
	 */
	function ced_ps_add_single_item_shortlist_icon() {
		global $product;
		$pages = 0;
		$c = 0;
		$saved_general_settings = get_option( 'ced_ps_general_settings' );
		$saved_general_settings = json_decode( $saved_general_settings );
		$current_url = $_SERVER['REQUEST_URI'];
		$arr = explode('/', $current_url);
		$arr = array_filter( $arr );
		$arr = array_values( $arr );
		$current_page = trim($arr[count($arr)-1]);
		$current_page = preg_replace('/[^A-Za-z0-9]/', '', $current_page);
		if ( ! empty( $saved_general_settings ) ) {
			if ( ! empty( $saved_general_settings->selected_pages ) ) {				
				
				foreach ($saved_general_settings->selected_pages as $key => $value) {
					if($current_page == $value)
					{
						$pages = 1;
					}
				}
				if ($pages != 1) {
					if (in_array('single', $saved_general_settings->selected_pages)) {
						$pages = is_product();
					}
				}
			}
				
			if ( $pages ) {
				$productObj = new WC_Product( $product->id );
				$size = 'shop_thumbnail';
				$product_img_src = get_the_post_thumbnail( $product->id, $size );
				if (!empty($product_img_src)) 
				{
					$product_img_src = explode( 'src="', $product_img_src );
					$product_img_src = explode( '"', $product_img_src[1] )[0];
				}
				else{
					$product_img_src = CED_PS_PLUGIN_URL.'assets/images/placeholder.png'  ;
				}
				$shortlist_html = sprintf(
						'<a rel="nofollow" class="ced_ps_add_to_shortlist" id="%s" href="javascript:void(0);" data-atc_url="%s" data-quantity="%s" data-title="%s" data-id="%s" data-sku="%s" data-atc_text="%s" data-price="%s" data-type="%s" data-user_id="%s" data-guid="%s" title="%s" data-image="%s" >',
						esc_html( 'ps_product-'.$product->id ),
						esc_url( $product->add_to_cart_url() ),
						esc_attr( isset( $quantity ) ? $quantity : 1 ),
						esc_attr( $product->post->post_title ),
						esc_attr( $product->id ),
						esc_attr( $product->get_sku() ),
						esc_html( $product->add_to_cart_text() ),
						esc_attr( $productObj->price ),
						esc_attr( $product->product_type ),
						esc_attr( get_current_user_id() ),
						esc_url( $product->get_permalink() ),
						esc_html( __('Add to Shortlist', 'product-shortlist' ) ),
						esc_url( $product_img_src )
				);
				$shortlist_html .= '<span class="ced_ps_shortlist_icon">';
				
				$shortlist_html .= '<svg width="35px" height="35px" viewBox="0 0 300 275" xmlns="http://www.w3.org/2000/svg" version="1.1">';
				$shortlist_html .= '<polygon fill="#ffffff" stroke="#555555" stroke-width="15" points="150,25  179,111 269,111 197,165, 223,251  150,200 77,251  103,165, 31,111 121,111"/>';
				$shortlist_html .= '</svg>';
				$shortlist_html .= '</span>';
				$shortlist_html .= '</a>';
				echo $shortlist_html;
			}
		}
	}
}
add_action( 'woocommerce_before_single_product', 'ced_ps_add_single_item_shortlist_icon', 9.999 );

if ( ! function_exists( 'ced_ps_add_shortlist_section' ) ) {
	
	/**
	 * Adds shortlist section at shop page or single product page.
	 *
	 * @category function
	 * @author CedCommerce <http://cedcommerce.com>
	 */
	function ced_ps_add_shortlist_section() {
	
		$pages = 0;
		$c = 0;
		$saved_general_settings = get_option( 'ced_ps_general_settings' );
		$saved_general_settings = json_decode( $saved_general_settings );
		$current_url = $_SERVER['REQUEST_URI'];
		$arr = explode('/', $current_url);
		$arr = array_filter( $arr );
		$arr = array_values( $arr );
		$current_page = trim($arr[count($arr)-1]);
		$current_page = preg_replace('/[^A-Za-z0-9]/', '', $current_page);
		if ( ! empty( $saved_general_settings ) ) {
			if ( ! empty( $saved_general_settings->selected_pages ) ) {				
				
				foreach ($saved_general_settings->selected_pages as $key => $value) {
					if($current_page == $value)
					{
						$pages = 1;
					}
				}
				if ($pages != 1) {
					if (in_array('single', $saved_general_settings->selected_pages)) {
						$pages = is_product();
					}
				}
			}
			if(!empty($saved_general_settings->removeall_text)){
				$removeall_text = $saved_general_settings->removeall_text;
			}
			else{
				$removeall_text = 'REMOVE All';
			}
			if(!empty($saved_general_settings->shortlist_text)){
				$shortlist_text = $saved_general_settings->shortlist_text;
			}
			else{
				$shortlist_text = 'SHORTLIST';
			}
			if(!empty($saved_general_settings->shortlist_removeall_background_color)){
				$shortlist_removeall_background_color = $saved_general_settings->shortlist_removeall_background_color;
			}
			else{
				$shortlist_removeall_background_color = 'red';
			}
			
			if ( $pages ) {
				$shortlist_html = sprintf( '<div class="%s"><div class="%s %s" data-expanded="%s"><p class="%s">%s</P><button id="%s" class="%s" style="background : '.$shortlist_removeall_background_color.' !important;">%s</button></div><span class="%s" id="%s" rel="nofollow"><span id="ced_ps_shortlisted_qty">%s</span><span class="%s">%s</span></span></div>',
						esc_html( 'ced_ps-'. $saved_general_settings->selected_position .'-shortlist-section-wrapper' ),
						esc_html( 'ced_ps-shortlist-section' ),
						esc_html( $saved_general_settings->selected_position .'-shortlist-section' ),
						esc_html( 'no' ),
						esc_html( 'ced_ps_empty_section_text' ),
						__( 'Shortlist your wished items and add them here by clicking the <b>STAR</b> button.', 'product-shortlist' ),
						esc_html( 'ced_ps_remove_all' ),
						esc_html( 'button' ),
						esc_html( __( $removeall_text, 'product-shortlist' ) ),
						esc_html( 'ced_ps_'. $saved_general_settings->selected_position .'_shortlist_span' ),
						esc_html( 'ced_ps_shortlist_text' ),
						esc_html( 0 ),
						esc_html( __( 'ced_ps_shortlist_btn' ) ),
						esc_html( __( $shortlist_text, 'product-shortlist' ) )
				);
				echo $shortlist_html;
			}
		}
	}
}
add_action('wp_head', 'ced_ps_add_shortlist_section');