jQuery(document).ready(function(){
	
	jQuery( function( $ ) {
		
		if (! global.shortlist_section_position ) {
			global.shortlist_section_position = 'left';
		}
		if (! global.shortlist_background_color ) 
		{
			global.shortlist_background_color = 'red';
		}
		if (! global.shortlist_font_color ) 
		{
			global.shortlist_font_color = 'white';
		}
		if (! global.shortlist_addtocart_font_color ) 
		{
			global.shortlist_addtocart_font_color = 'white';
		}
		if (! global.shortlist_addtocart_background_color ) 
		{
			global.shortlist_addtocart_background_color = 'red';
		}
		if (! global.shortlist_removeall_background_color ) 
		{
			global.shortlist_removeall_background_color = 'red';
		}
		if (! global.removeall_text ) 
		{
			global.removeall_text = 'Remove All';
		}
		if (! global.removeall_font_color ) 
		{
			global.removeall_font_color = 'white';
		}

		$('.ced_ps_'+global.shortlist_section_position+'_shortlist_span').css( 'background' , global.shortlist_background_color );
		$('span.ced_ps_shortlist_btn').css('color' , global.shortlist_font_color);		
		// $('#ced_ps_remove_all').css('background' , global.shortlist_removeall_background_color+" !important");
		// $('#ced_ps_remove_all').css('color' , global.removeall_font_color);
		getProductCookies();
		
		
		if ( global.user_logged_in || global.user_logged_in != '' ) {
			
			$( '#ced_ps_shortlist_text' ).addClass( 'ced_ps_user_logged-in' );
		}
		$( document ).on( 'click', '#ced_ps_shortlist_text', function() {
			toggleExpand( $( this ), global.shortlist_section_position );		
		});

		var toggleExpand = function( $this, position ) {
			var max_width = $( document ).find( '.ced_ps-shortlist-section' ).css( 'max-width' ),
			width = '',
			expansion_sign = '+',
			expanded = $this.siblings( '.ced_ps-shortlist-section' ).data( 'expanded' ),
			expand_to = 'yes';
			if ( expanded == 'yes' ) {
				expansion_sign = '-';
				expand_to = 'no';
			}
			

			if ( position == 'top' || position == 'bottom'  ) {
				$this.siblings( '.' + global.shortlist_section_position + '-shortlist-section' ).slideToggle( 'slow' );
			} else if ( position == 'left' || position == 'right' ) {
				width = max_width ? max_width : '150px';
				$this.siblings('.'+ global.shortlist_section_position +'-shortlist-section').toggle('slide' , 'right' , 'slow');
				
				if ( position == 'left' ) {
					$this.animate({ 
						left: expansion_sign + "=" + width,
					}, 'slow' );
				} else {
					$this.animate({ 
						right: expansion_sign + "=" + width,
					}, 'slow' );
				}
				$this.siblings( '.ced_ps-shortlist-section' ).data( 'expanded', expand_to );
			}

		}
		
		/**************************
		 * Add items to shortlist
		 * 
		 * @author CedCommerce
		**************************/
		$( document ).on( 'click', '.ced_ps_add_to_shortlist', function() {
			var atc_url =	$( this ).data( 'atc_url' ),
			item_title	=	$( this ).data( 'title' ),
			product_id	=	$( this ).data( 'id' ),
			item_sku	=	$( this ).data( 'sku' ),
			atc_text	=	$( this ).data( 'atc_text' ),
			item_price	=	$( this ).data( 'price' ),
			item_guid	=	$( this ).data( 'guid' ),
			item_image	=	$( this ).data( 'image' ),
			user_id     =   $( this ).data( 'user_id' ),
			product_type = $( this ).data( 'type' ),
			itemCounter = 	$( '#ced_ps_shortlisted_qty' ).html(), 
			splitCookie, productHtml = '', currentDate = new Date(), priceWithCurrency = '';
			if (user_id == '') {
				user_id = 'guest';
			}
			if (! item_image || typeof item_image == 'undefined' || item_image == '' || item_image == null ) {
				item_image = global.plugin_url+'assets/images/placeholder.png'
			}
			
			if ( item_price == '' || item_price == null || typeof item_price == 'undefined' ) {
				priceWithCurrency = '';
			} else {
				priceWithCurrency = global.wc_currency + item_price;
			}
			
			/*********************
			 * * Checks if product does not exist in shortlist section and does not exists into cookie also * *
			 * * *******************/
			if( $( '#ced_ps_product-'+ product_id ).length == 0 && ! checkCookie( "ps_product_"+user_id+"-"+product_id ) ) {
				/********************** Product informations to save in the cookie ************************/
				var cvalue = [ atc_url, item_title, product_id, item_sku, atc_text, item_image, item_price, item_guid , product_type ];
				
				/********************** Calculation of expiry Time ************************/
				currentDate.setTime( currentDate.getTime() + ( global.cookie_exp_time*24*60*60*1000 ) );
			    var expires = "expires="+ currentDate.toUTCString();

			    /********** Set cookie of products over here **********/
			    document.cookie = "ps_product_"+user_id+"-"+product_id + "=" + cvalue + "; " + [ expires +"; path="+ global.base_path ].join();
			    
			    itemCounter = parseInt(itemCounter) + 1;
			    
			    updateShortListedRecords( product_id );
			    
			    /************* Creating html *************/
			    productHtml = '<div class="ced_ps_product_info" id="ced_ps_product-'+ product_id +'" title="'+item_title+'">';
			    	productHtml += '<a href="javascript:void(0);" class="ced_ps_item_remove"><img src="'+ global.crossImageUrl +'"></a>';
			    	productHtml += '<a href="'+ item_guid +'" class="ced_ps_item_img"><img src="'+ item_image +'" class="attachment-shop_catalog size-shop_catalog wp-post-image ced_ps_product_image" width="55" height="55"></a>';
				    productHtml += '<a href="'+ item_guid +'"><span class="ced_ps_item_title">'+ item_title +'</span></a>'; 
				    productHtml += '<span class="ced_ps_item_price">'+ priceWithCurrency +'</span>';
				    if( product_type == 'variable' )
				    {
				    	productHtml += '<a class="button product_type_variable add_to_cart_button ced_ps_atc_btn" data-product_sku="'+item_sku+'" data-product_id="'+product_id+'" data-quantity="1" href="'+atc_url+'" rel="nofollow" style="background-color: '+ global.shortlist_addtocart_background_color +' !important'+'; color: '+ global.shortlist_addtocart_font_color +' !important'+'">'+ atc_text +'</a>';
				    }
				    else {
				    	productHtml += '<a class="button product_type_simple add_to_cart_button ajax_add_to_cart ced_ps_atc_btn" id="ced_ps_addtocart_button" data-product_sku="'+ item_sku +'" data-product_id="'+ product_id +'" data-quantity="1" href="'+ atc_url +'" rel="nofollow" style="background-color: '+ global.shortlist_addtocart_background_color +' !important'+'; color: '+ global.shortlist_addtocart_font_color +' !important'+'">'+ atc_text +'</a>';
				    }
			    productHtml += '</div>';
			    
			    $( '.'+ global.shortlist_section_position +'-shortlist-section' ).prepend( productHtml );
			    $( '#ced_ps_shortlisted_qty' ).html( itemCounter );
			    addShortlistIcon( product_id );
			    $( '#ced_ps_remove_all' ).show();
			    $( '.ced_ps_empty_section_text' ).hide();
			} else if ( $( '#ced_ps_product-'+ product_id ).length != 0 && $.cookie( "ps_product_"+user_id+"-"+product_id ) ){
				$( '#ced_ps_product-'+ product_id ).remove();
				itemCounter = parseInt(itemCounter) - 1;
				removeCookieById( product_id ,user_id );
				$( '#ced_ps_shortlisted_qty' ).html( itemCounter );
				$( this ).find( 'polygon' ).attr( 'stroke', '#555555' );
				$( this ).find( 'polygon' ).attr( 'fill', '#ffffff' );
				if ( itemCounter  == 0 ) {
					clearAll();
				}
				if (global.current_user_id != null) 
				{
					$.ajax({
						url		: 	global.ajaxurl,
						type	: 	'post',
						data	: 	{
							'action' 		 : 'delete_shortlisted_product',
							'product_id'	 : product_id,
							'user_id'		 : global.current_user_id,
							'security_check' : global.ced_ps_nonce,
						},
						success : 	function(e)
						{

						}
					});					
				}
			}
		});
		
		/***************************************
		 * Get all the cookies saved in browser
		 * 
		 * @author CedCommerce
		 * @category function
		 ***************************************/
		function getCookie( cname ) {
			
		    var name = cname + "=";
		    var carr = document.cookie.split(';');
		    for( var i = 0; i < carr.length; i++ ) {
		        var cookie = carr[i];
		        while ( cookie.charAt(0) == ' ') {
		        	cookie = cookie.substring(1);
		        }
		        if ( cookie.indexOf(name) == 0) {
		            return cookie.substring( name.length, cookie.length );
		        } 
		    }
		    return "";
		} 
		
		/***********************************
		 * Checks if cookie exist in browser
		 * 
		 * @author CedCommerce
		 * @category function
		 ***********************************/
		function checkCookie( cookieName ) {
		    var cookieName = getCookie( cookieName );
		    if ( cookieName != "") {
		    	return true;
		    } else {
		    	return false;
		    }
		}
		
		/*****************************************************************************
		 * Fetches all the products from  the cookie, and filters the required cookies
		 * 
		 * @author CedCommerce
		 *******************************************************************************/
		function getProductCookies() 
		{
			if (global.cookie_names != null) 
			{
				for(var i =0 ; i < global.cookie_names.length ; i++)
				{
					removeCookieByName(global.cookie_names[i]);
				}
			}
			if ( global.current_user_id == null ) {
				var user_id = 'guest';
			}
			else{
				var user_id = global.current_user_id;
			}
			if ( user_id == 'guest' ) 
			{
				var cookies, counter = 0, cookie_name = '', atc_url = '', priceWithCurrency = '',
				title = '', item_sku = '', atc_text = '', item_img = '', item_price = 0.0, product_id = '', counter = 0;
				cookies = document.cookie.split(/; */);
				var matchStr = 'ps_product_'+user_id+'-';
				var matchStr1 = 'ps_product-';
				$.each( cookies, function()  {
					var splitCookie = this.split('=')
					
					if( splitCookie[0].indexOf( matchStr ) != -1 ) {
					    
					    cookie_name =  splitCookie[0];
					    
					    var productCookie = $.cookie(cookie_name);
						    productCookie = productCookie.split(',');
						    
						    atc_url 	=  productCookie[0];
						    title		=  productCookie[1];
						    product_id	=  productCookie[2];
						    sku			=  productCookie[3];
						    atc_text	=  productCookie[4];
						    item_img	=  productCookie[5];
						    item_price	=  productCookie[6];
						    item_guid	=  productCookie[7];
						    product_type=  productCookie[8];
						    
					    if ( item_price == '' || item_price == null || typeof item_price == 'undefined' ) {
							priceWithCurrency = '';
						} else {
							priceWithCurrency = global.wc_currency + item_price;
						}
						if (item_img == '') 
						{
							item_img = global.plugin_url+'/assets/images/placeholder.png';
						}
					    if( $( '#ced_ps_product-'+ product_id ).length == 0 && checkCookie( cookie_name ) ) {
					    	counter++;
					    	
					    	productHtml = '<div class="ced_ps_product_info" id="ced_ps_product-'+ product_id +'" title="'+title+'">';
					    		productHtml += '<a href="javascript:void(0);" class="ced_ps_item_remove"><img src="'+ global.crossImageUrl +'"></a>';
						    	productHtml += '<a href="'+ item_guid +'" class="ced_ps_item_img"><img src="'+ item_img +'" class="attachment-shop_catalog size-shop_catalog wp-post-image ced_ps_product_image" width="55" height="55"></a>';
							    productHtml += '<a href="'+ item_guid +'"><span class="ced_ps_item_title">'+ title +'</span></a>'; 
							    productHtml += '<span class="ced_ps_item_price">'+ priceWithCurrency +'</span>';
							    if( product_type == 'variable' )
							    {
							    	productHtml += '<a class="button product_type_variable add_to_cart_button ced_ps_atc_btn" data-product_sku="'+item_sku+'" data-product_id="'+product_id+'" data-quantity="1" href="'+atc_url+'" rel="nofollow" style="background-color: '+ global.shortlist_addtocart_background_color +' !important'+'; color: '+ global.shortlist_addtocart_font_color +' !important'+'">'+ atc_text +'</a>';
							    }
							    else {
							    	productHtml += '<a class="button product_type_simple add_to_cart_button ajax_add_to_cart ced_ps_atc_btn" id="ced_ps_addtocart_button" data-product_sku="'+ item_sku +'" data-product_id="'+ product_id +'" data-quantity="1" href="'+ atc_url +'" rel="nofollow" style="background-color: '+ global.shortlist_addtocart_background_color +' !important'+'; color: '+ global.shortlist_addtocart_font_color +' !important'+'">'+ atc_text +'</a>';
							    }
						    productHtml += '</div>';
						    $( '.'+ global.shortlist_section_position +'-shortlist-section' ).prepend( productHtml );
						    $( '#ced_ps_shortlisted_qty' ).html( counter );
						    addShortlistIcon( product_id );
						    $( '#ced_ps_remove_all' ).show();
						    $( '.ced_ps_empty_section_text' ).hide();
					    }
					}					
					else 
					{
						var item_count = itemCounter( product_id , user_id );
					    if ( item_count == 0 ){
					    	$( '.ced_ps_empty_section_text' ).show();
					    }
					}
				});
			} else {
				$.ajax({
					url		: 	global.ajaxurl,
					type	: 	'post',
					data	: 	{
						'action' 	 		: 'get_shortlisted_products',
						'user_id'	 		: user_id,
						'security_check'	: global.ced_ps_nonce,
					},
					success : 	function(e){									
						if ( e == 'no shortlisted products' ) 
						{
							$( '.ced_ps_empty_section_text' ).show();
						}
						else
						{					
							try {
								discount_arr = JSON.parse(global.discount_arr);														
								var itemCounter = 0;
								var products = JSON.parse(e);
								
								var productHtml = '';
								for ( i in products ) {
									itemCounter++;
									var product_id = products[i].id,
									value = products[i],
									image = '',
									f = 0;
									if ( value.image == '' ) {
										image = global.plugin_url+'/assets/images/placeholder.png';
									} else {
										image = value.image
									}
									productHtml = '<div class="ced_ps_product_info" id="ced_ps_product-'+ product_id +'" title="'+value.title+'">';
							    		productHtml += '<a href="javascript:void(0);" class="ced_ps_item_remove"><img src="'+ global.crossImageUrl +'"></a>';
								    	productHtml += '<a href="'+ value.guid +'" class="ced_ps_item_img"><img src="'+ image +'" class="attachment-shop_catalog size-shop_catalog wp-post-image ced_ps_product_image" width="55" height="55"></a>';
									    productHtml += '<a href="'+ value.guid +'"><span class="ced_ps_item_title">'+ value.title +'</span></a>'; 
									    if ( discount_arr != '' || discount_arr != null ) {
									    	for ( d in discount_arr ) {
										    	if( discount_arr[d].id == product_id && value1 != '' ) {
										    		if ( global.discount_type == 'rate' ) {
										    			price = value.price - value1*value.price/100;
											    		price = global.wc_currency + price;
											    		f = 1;
										    		} else {
											    		price = value.price - value1;
											    		price = global.wc_currency + price;
											    		f = 1;
											    	}	
										    	}
									    	}
								    	}
										   
										if( f == 0 && value.price != '' ) {
											price = global.wc_currency + value.price;
											productHtml += '<span class="ced_ps_item_price">'+ price +'</span>';
										} else if( value.price !='' ) {
											productHtml += '<span class="ced_ps_item_price"><strike>'+global.wc_currency + value.price+'</strike>   '+ price +'</span>';
										} else {
											productHtml += '<span class="ced_ps_item_price">  </span>';	
										}
									    
									    if( value.type == 'variable' ) {									    	
									    	productHtml += '<a class="button product_type_variable add_to_cart_button ced_ps_atc_btn" data-product_sku="'+ value.sku +'" data-product_id="'+ product_id +'" data-quantity="1" href="'+value.atc_url+'" rel="nofollow" style="background-color: '+ global.shortlist_addtocart_background_color +' !important'+'; color: '+ global.shortlist_addtocart_font_color +' !important'+'">'+ value.atc_text +'</a>';
									    } else {
									    	productHtml += '<a class="button product_type_simple add_to_cart_button ajax_add_to_cart ced_ps_atc_btn" id="ced_ps_addtocart_button" data-product_sku="'+ value.sku +'" data-product_id="'+ product_id +'" data-quantity="1" href="'+ value.atc_url +'" rel="nofollow" style="background-color: '+ global.shortlist_addtocart_background_color +' !important'+'; color: '+ global.shortlist_addtocart_font_color +' !important'+'">'+ value.atc_text +'</a>';
									    }
								    productHtml += '</div>';
									
									$( '.'+ global.shortlist_section_position +'-shortlist-section' ).prepend( productHtml );
									$( '#ced_ps_shortlisted_qty' ).html( counter );

								    addShortlistIcon( product_id );
								    $( '#ced_ps_remove_all' ).show();
								    $( '.ced_ps_empty_section_text' ).hide();
								    if ( ! checkCookie( 'ps_product_' + user_id + '-' + product_id ) ) {
								    	var currentDate = new Date();
								    	var cvalue = [ value.atc_url, value.title, product_id, value.sku, value.atc_text, value.image, price, value.guid , value.type ];
										
										/********************** Calculation of expiry Time ************************/
										currentDate.setTime( currentDate.getTime() + ( global.cookie_exp_time*24*60*60*1000 ) );
									    var expires = "expires=" + currentDate.toUTCString();

									    /********** Set cookie of products over here **********/
									    document.cookie = "ps_product_" + user_id + "-" + product_id + "=" + cvalue + "; " + [ expires +"; path="+ global.base_path ].join();
								    }
								};								
								if ( itemCounter == 0 ) {
									$( '.ced_ps_empty_section_text' ).show();
								} else {
									$( '#ced_ps_shortlisted_qty' ).html( itemCounter );										
								}
							} catch( error ) {
								
							}
						}
					}
				});
			}
		}
		
		/****************************************
		 * Counts the matched item in the cookie
		 * 
		 * @author CedCommerce
		 * @category function
		 ****************************************/
		function itemCounter( product_id , user_id ) {
			cookies = document.cookie.split(/; */);
			var matchStr = 'ps_product_'+user_id, counter = 0, productCookie = '';
			$.each( cookies, function()  {
				var splitCookie = this.split('=');
				
				if( splitCookie[0].indexOf( matchStr ) != -1 ) {
				    
				    cookie_name =  splitCookie[0];
				    
				    productCookie = $.cookie(cookie_name).split(',');
					    
				    if( checkCookie( cookie_name ) ) {
				    	counter++;
				    }
				}
			});
			return counter;
		}
		
		/**************************
		 * Removes Cookie by ID
		 * 
		 * @author CedCommerce
		 * @category function
		 * @param string product_id
		 ***************************/
		function removeCookieById( product_id , user_id ) {
			document.cookie = "ps_product_"+user_id+"-"+ product_id +"=; " + [ "Thu, 18 Dec 2013 12:00:00 UTC; path="+ global.base_path ].join('');
			removeShortlistIcon( product_id );
		}

		/**************************
		 * Removes Cookie by name
		 * 
		 * @author CedCommerce
		 * @category function
		 * @param string cookie name
		 ***************************/
		function removeCookieByName( cookiename ) {
			document.cookie = cookiename +"=; " + [ "Thu, 18 Dec 2013 12:00:00 UTC; path="+ global.base_path ].join('');
			var product_id = cookiename.substring( cookiename.indexOf('-') + 1 , cookiename.length );
			removeShortlistIcon( product_id );
		}
		
		/**************************
		 * Deletes all the Cookies
		 * 
		 * @author CedCommerce
		 * @category function
		 ***************************/
		function removeAllCookie() {
			if ( global.current_user_id == null ) {
				var user_id = 'guest';
			}
			else{
				var user_id = global.current_user_id;
			}
			cookies = document.cookie.split(/; */);
			var matchStr = 'ps_product_'+user_id, 
			count = 0, 
			productCookie = '';
			$.each( cookies, function()  {
				var splitCookie = this.split('=');
				if( splitCookie[0].indexOf( matchStr ) != -1 ) {
				    
				    cookie_name =  splitCookie[0];
				    
				    productCookie = $.cookie(cookie_name).split(',');
				    if( checkCookie( cookie_name ) ) {
				    	product_id	=  productCookie[2];
				    	removeCookieById( product_id , user_id);
				    	$( '#ced_ps_shortlisted_qty' ).html( 0 );
				    }
				}
			});
		}
		
		/********************************************
		 * Fills the shotlisted icons with the color
		 * 
		 * @author CedCommerce
		 * @category function
		 * @param string product_id
		 *********************************************/
		function addShortlistIcon( product_id ) {
			$( '#ps_product-'+product_id ).find( 'polygon' ).attr( 'stroke', '#0BB1AF' );
		    $( '#ps_product-'+product_id ).find( 'polygon' ).attr( 'fill', '#0BB1AF' );
		}
		
		/*****************************************
		 * Removes the color of shortlisted icons
		 * 
		 * @author CedCommerce
		 * @category function
		 * @param string product_id
		 *****************************************/
		function removeShortlistIcon( product_id ) {
			$( '#ps_product-'+product_id ).find( 'polygon' ).attr( 'stroke', '#555555' );
			$( '#ps_product-'+product_id ).find( 'polygon' ).attr( 'fill', '#ffffff' );
		}
		
		/*****************************************
		 * Clear all the other fields required 
		 * 
		 * @author CedCommerce
		 * @category function
		 *****************************************/
		function clearAll() {
			$( '#ced_ps_remove_all' ).hide();
			$( '.ced_ps_empty_section_text' ).show( 'slow' );
		}
		
		/***************************************
		 * Adds and updates shortlisted records
		 * 
		 * @author CedCommerce
		 * **************************************/
		function updateShortListedRecords( product_id ) {
			$.post(
				global.ajaxurl,
			    {
			        'action'			:	'update_shortlisted_records',
			        'security_check'	:	global.ced_ps_nonce,
			        'product_id'		:	product_id
			    },
			    function( data ) {
			    	
			    }
			);
		}
		
		/****************************************
		 * Remove items on click of cross button
		 * 
		 * @author CedCommerce
		 * ***************************************/
		$( document ).on( 'click', '.ced_ps_item_remove', function() 
		{
			if ( global.current_user_id == null ) {
				var user_id = 'guest';
			}
			else{
				var user_id = global.current_user_id;
			}
			var product_id = $( this ).siblings( 'a.ced_ps_atc_btn' ).data( 'product_id' ),
			itemCount = '';
			itemCount = itemCounter( product_id , user_id );
			removeCookieById( product_id , user_id );
			$( this ).parent( 'div.ced_ps_product_info' ).remove();
			$( '#ced_ps_shortlisted_qty' ).html( itemCount - 1 );
			if ( itemCount  == 1 ) {
				clearAll();
			}
			if (global.current_user_id != null) 
			{
				$.ajax({
					url		: 	global.ajaxurl,
					type	: 	'post',
					data	: 	{
						'action' 		 : 'delete_shortlisted_product',
						'product_id'	 : product_id,
						'user_id'		 : global.current_user_id,
						'security_check' : global.ced_ps_nonce,
					},
					success : 	function(e)
					{

					}
				});					
			}
		});
		
		/****************************************************************
		 * Remove All items from shortlist by click on Remove All button
		 * 
		 * @author CedCommerce
		 * *************************************************************/
		$( document ).on( 'click', '#ced_ps_remove_all', function() 
		{
			removeAllCookie();
			$( '.ced_ps_product_info' ).remove();
			clearAll();
			if (global.current_user_id != null) 
			{
				$.ajax({
					url		: 	global.ajaxurl,
					type	: 	'post',
					data	: 	{
						'action' 		 : 'delete_all_shortlisted_products',									
						'user_id'		 : global.current_user_id,
						'security_check' : global.ced_ps_nonce,
					},
					success : 	function(e)
					{

					}
				});					
			}
		});
	});
});