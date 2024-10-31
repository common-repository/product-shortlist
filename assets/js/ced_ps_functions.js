jQuery(document).ready(function($){
		
		/**
		 * Applies select2 in In pages options
		 */
		$( '#ced_ps_select_pages' ).select2( {
			placeholder: global.page_placeholder,
			allowClear: true
		} );
		
		/**
		 * Applies select2 in In position options
		 */
		$( '#ced_ps_select_position' ).select2( {
			placeholder: global.position_placeholder,
			allowClear: true
		} );

		/**
		 * Applies select2 in In discount type options
		 */
		$( '#ced_ps_select_discount_type' ).select2( {
			placeholder: global.position_placeholder,
			allowClear: true
		} );
		
		/**
		 * Expiry time field validation
		 */
		$( "#ced_ps_expiry_time" ).on( 'keypress', function (e) {
			//if the letter is not digit then display error and don't type anything
			if ( e.which != 8 && e.which != 0 && ( e.which < 48 || e.which > 57 ) ) {
		    $( '#ced_ps_exp_error' ).html( global.digits_only_text );
				setTimeout( function(){ jQuery( '#ced_ps_exp_error' ).html( '' ); }, 3000 );
				return false;
		    }
		});
		
		$( document ).on( 'click', 'button.notice-dismiss', function() {
			$( this ).parents( 'div#message' ).remove();
		});

		/**
		 * Save General settings
		 */
		$( '#ced_ps_save_general_settings' ).on( 'click', function(){
			
			var selected_pages = jQuery('#ced_ps_select_pages').val(),
			selected_position = jQuery('#ced_ps_select_position option:selected').attr('data'),
			discount_type = jQuery('#ced_ps_select_discount_type option:selected').attr('data'),
			exp_time = jQuery('#ced_ps_expiry_time').val(),
			shortlist_background_color = jQuery('#shortlist-background-color').val(),
			shortlist_addtocart_background_color = jQuery('#shortlist-addtocart-background-color').val(),
			shortlist_font_color = jQuery('#shortlist-font-color').val(),
			shortlist_addtocart_font_color = jQuery('#shortlist-addtocart-font-color').val(),
			shortlist_text = jQuery('#ced_ps_save_shortlist_text').val(),
			removeall_text = jQuery('#ced_ps_save_removeall_text').val(),
			removeall_font_color = jQuery('#removeall-font-color').val(),
			shortlist_removeall_background_color = jQuery('#shortlist-removeall-color').val(),
			notice_html = '';	

			if (shortlist_text == '') 
			{
				shortlist_text = 'SHORTLIST';
			}
			if (removeall_text == '') 
			{
				removeall_text = 'REMOVE ALL';
			}
			if( selected_pages == '' ||  selected_pages == null || discount_type == null || typeof selected_pages == 'undefined') {
				$( '#ced_ps_pages_error' ).html( global.empty_pages_selection );
				setTimeout( function(){ jQuery( '#ced_ps_pages_error' ).html( '' ); }, 3000 );
			} else if( selected_position == '' || selected_position == null || discount_type == null || typeof selected_position == 'undefined' || shortlist_background_color == null || shortlist_text == '' || shortlist_font_color == null || shortlist_addtocart_font_color == null || shortlist_addtocart_background_color == null || shortlist_removeall_background_color == null || removeall_text == '' || removeall_font_color == null) {
				$( '#ced_ps_position_error' ).html( global.empty_position_selection );
				setTimeout( function(){ jQuery( '#ced_ps_position_error' ).html( '' ); }, 3000 );
			} else if( exp_time == '' || exp_time == null ) {
				$( '#ced_ps_exp_error' ).html( global.invalid_exp );
				setTimeout( function(){ jQuery( '#ced_ps_exp_error' ).html( '' ); }, 3000 );
			} else if( exp_time == 0 || exp_time == '0' ) {
				$( '#ced_ps_exp_error' ).html( global.exp_greater_than_zero );
				setTimeout( function(){ jQuery( '#ced_ps_exp_error' ).html( '' ); }, 3000 );
			} else if( exp_time < 0 ) {
				$( '#ced_ps_exp_error' ).html( global.exp_not_negative );
				setTimeout( function(){ jQuery( '#ced_ps_exp_error' ).html( '' ); }, 3000 );
			} else {
				$( '.ced_ps_content_wrapper' ).css( 'opacity', '0.6' );
				$( '#ced_ps_loading_wrapper' ).show();
				jQuery.post(
					global.ajaxurl,
				    {
				        'action'							:	'save_general_settings',
				        'security_check'					:	global.ced_ps_nonce,
				        'selected_pages'					:	selected_pages,
				        'selected_position'					:	selected_position,
				        'discount_type'					    :	discount_type,
				        'shortlist_background_color'		:   shortlist_background_color,
				        'shortlist_addtocart_background_color'		:   shortlist_addtocart_background_color,
				        'shortlist_font_color'				:   shortlist_font_color,
				        'shortlist_addtocart_font_color'			:   shortlist_addtocart_font_color,
				        'shortlist_text'					: 	shortlist_text,
				        'removeall_text'                    :   removeall_text,
				        'removeall_font_color'				:   removeall_font_color,
				        'shortlist_removeall_background_color': shortlist_removeall_background_color,
				        'exp_time'							:	exp_time,
				    },
				    function( data ) {				    	
				    	$( '#ced_ps_loading_wrapper' ).hide();
				    	$( '.ced_ps_content_wrapper' ).css( 'opacity', '1' );
				    	if ($.trim(data) == 'success') {
				    		notice_html = '<div class="updated notice is-dismissible" id="message">';
				    			notice_html += '<p>'+ global.setting_saved_text +'</p>';
				    			notice_html += '<button class="notice-dismiss" type="button">';
				    				notice_html += '<span class="screen-reader-text">Dismiss this notice.</span>';
				    			notice_html += '</button>';
				    		notice_html += '</div>';
				    		$( "#ced_ps_message" ).html( notice_html );
				    		setTimeout( function(){ window.location.href = window.location.href; }, 5000 );
				    	} else {
				    		notice_html = '<div class="updated error notice is-dismissible" id="message">';
				    			notice_html += '<p>'+ global.setting_failed_text +'</p>';
				    			notice_html += '<button class="notice-dismiss" type="button">';
				    				notice_html += '<span class="screen-reader-text">Dismiss this notice.</span>';
				    			notice_html += '</button>';
				    		notice_html += '</div>';
				    		$( "#ced_ps_message" ).html( notice_html );
				    	}
				    }
				);
			}
		});	

		/**
		 * Delete product from admin end list
		 */
		$(document).on('click' , '#delete_admin_side' , function(){
			var pid = $(this).data('pid');
			var user_id = $(this).data('userid');
			$('#ced_ps_loading_wrapper_'+user_id+pid).show();		
			$.ajax({
				url		: 	global.ajaxurl,
				type	: 	'post',
				data	: 	{
					'action' 		 : 'delete_products_admin_side',									
					'product_id'     : pid,
					'user_id'        : user_id,
					'date'			 : $(this).data('date'),
					'security_check' : global.ced_ps_nonce,
				},
				success : 	function(e)	{
					console.log($(document).find('#shortlisted_product_'+user_id+pid));
					$(document).find('#shortlisted_product_'+user_id+pid ).remove();
					removeCookieById( pid , user_id );	
					$('#ced_ps_loading_wrapper_'+user_id+pid).hide();	
					notice_html = '<div class="updated notice is-dismissible" id="message">';
		    			notice_html += '<p>Product Deleted successfully</p>';
		    			notice_html += '<button class="notice-dismiss" type="button">';
		    				notice_html += '<span class="screen-reader-text">Dismiss this notice.</span>';
		    			notice_html += '</button>';
		    		notice_html += '</div>';
		    		$( "#ced_ps_success_message" ).html( notice_html );			
				}
			});				
		});

		/**
		 * Provide discount on shortlisted products
		 */
		$(document).on('blur' , '#product_discount_admin_side' , function(){
			var pid 		= $(this).data('pid');
			var user_id 	= $(this).data('userid');
			var date 		= $(this).data('date');
			var row_id 		= $(this).data('rowid');
			var discount 	= $(this).val();
			$('#ced_ps_loading_wrapper_'+user_id+pid).show(); 
			$.ajax({
				url		: 	global.ajaxurl,
				type	: 	'post',
				data	: 	{
					'action' 		 : 'add_discount_product_admin_side',									
					'product_id'     : pid,
					'user_id'        : user_id,
					'date'			 : date,
					'row_id'		 : row_id,
					'discount'		 : discount,
					'security_check' : global.ced_ps_nonce,
				},
				success : 	function(e){
					if (e == 1 && (discount != '' || discount != 0)) 
					{
						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').show();
						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').removeClass('not_available_discount');
						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').addClass('available_discount');

						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').attr( 'data-discount' , discount );									
					}
					else if(discount == 0 || discount == '')
					{
						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').hide();
						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').removeClass('available_discount');
						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').addClass('not_available_discount');

						$('#shortlisted_product_'+user_id+pid).find('#admin-send-mail').attr( 'data-discount' , 0 );									
					}
					$('#ced_ps_loading_wrapper_'+user_id+pid).hide();
					notice_html = '<div class="updated notice is-dismissible" id="message">';
		    			notice_html += '<p>Discount provided successfully</p>';
		    			notice_html += '<button class="notice-dismiss" type="button">';
		    				notice_html += '<span class="screen-reader-text">Dismiss this notice.</span>';
		    			notice_html += '</button>';
		    		notice_html += '</div>';
		    		$( "#ced_ps_success_message" ).html( notice_html );
				}
			});
		});

		/**
		 * Send Mail
		 */
		$(document).on('click' , '#admin-send-mail' , function(){
			var pid 		= $(this).data('pid');
			var user_id 	= $(this).data('userid');
			var discount 	= $(this).data('discount');
			$('#ced_ps_loading_wrapper_'+user_id+pid).show();
			$.ajax({
				url		: 	global.ajaxurl,
				type	: 	'post',
				data	: 	{
					'action' 		 : 'send_mail_to_user',									
					'product_id'     : pid,
					'user_id'        : user_id,
					'discount'		 : discount,
					'security_check' : global.ced_ps_nonce,
				},
				success : 	function(e){
					if (e == 1) 
					{
						var l ='<td><input type="button" class="button button-primary" id="admin-send-mail" value="Send Mail"></td>';
						$('td#prod_action').show();
						$('tr#shortlisted_product_'+pid).append(l);	
					}
					$('#ced_ps_loading_wrapper_'+user_id+pid).hide();
					notice_html = '<div class="updated notice is-dismissible" id="message">';
		    			notice_html += '<p>Email sent successfully</p>';
		    			notice_html += '<button class="notice-dismiss" type="button">';
		    				notice_html += '<span class="screen-reader-text">Dismiss this notice.</span>';
		    			notice_html += '</button>';
		    		notice_html += '</div>';
		    		$( "#ced_ps_success_message" ).html( notice_html );
				}
			});
		});

	
		$('.ced-ps-color-picker').wpColorPicker();
		
	});


/**
 * Removes Cookie after deleting the product form admin end
 */
function removeCookieById( product_id , user_id ) 
{
	document.cookie = "ps_product_"+user_id+"-"+ product_id +"=; " + [ "Thu, 18 Dec 2013 12:00:00 UTC; path="+ global.base_path ].join('');	
}

/**
 * Perform bulk actions
 */
function ced_ps_do_bulk_actions()
{
	if(jQuery('select[name=action]').val() == 'delete')
	{
		var url = window.location.href;
		var val = [];
		var user = [];
        jQuery.each(jQuery("input[name='shortlist_product[]']:checked"), function(){            

        	val.push(jQuery(this).data('key'));
        	user.push(jQuery(this).data('userid'));

        });
        jQuery.ajax({
       	url : global.ajaxurl,
       	type: 'post',
       	data: 	{
       		'action' 	: 'bulk_delete',
       		'ids' 		: val,
       		'user'		: user
       	},
       	success : function(e){
       		for( var i = 0 ; i <= val.length ; i++ )
       		{
       			removeCookieById( val[i] , user[i] );
       		}
       		window.location.assign(url);
       	}
       });
	}
}	
