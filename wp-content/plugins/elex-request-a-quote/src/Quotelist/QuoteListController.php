<?php

namespace Elex\RequestAQuote\Quotelist;

use Elex\RequestAQuote\Migrate;
use Elex\RequestAQuote\Quotelist\Models\QuoteListModel;
use Elex\RequestAQuote\Settings\Models\GeneralSettings;
use Elex\RequestAQuote\Settings\SettingsController;
use Elex\RequestAQuote\FormSetting\FormSettingController;
use Elex\RequestAQuote\FormSetting\Models\FormSettings;


use Elex\RequestAQuote\Quotelist\Models\ListPageSettings;
use Elex\RequestAQuote\Widget\WidgetController;


class QuoteListController {

	const SESSION_KEY_COOKIE_NAME = 'request_a_quote_user_coockie';
	public static $api_namespace  = 'elex-raq';
	public static $api_version    = 'v1';

	
	public static function init() {
		
		add_action( 'init', array( self::class, 'set_guest_user_cookie' ) );

		add_action( 'wp_ajax_elex_raq_submit_form', array( self::class, 'elex_place_order' ) );
		add_action( 'wp_ajax_nopriv_elex_raq_submit_form', array( self::class, 'elex_place_order' ) );

		add_action( 'wp_ajax_elex_raq_add_to_quote', array( self::class, 'elex_raq_add_to_quote' ) );
		add_action( 'wp_ajax_nopriv_elex_raq_add_to_quote', array( self::class, 'elex_raq_add_to_quote' ) );

		add_action( 'wp_ajax_elex_raq_update_quantity', array( self::class, 'elex_raq_update_quantity' ) );
		add_action( 'wp_ajax_nopriv_elex_raq_update_quantity', array( self::class, 'elex_raq_update_quantity' ) );


		add_action( 'wp_ajax_elex_raq_delete_item', array( self::class, 'eleX_raq_delete_item' ) );
		add_action( 'wp_ajax_nopriv_elex_raq_delete_item', array( self::class, 'eleX_raq_delete_item' ) );

		add_action( 'wp_ajax_elex_raq_clear_list', array( self::class, 'elex_raq_clear_list' ) );
		add_action( 'wp_ajax_nopriv_elex_raq_clear_list', array( self::class, 'elex_raq_clear_list' ) );

		add_action( 'wp_ajax_elex_raq_update_quote_list', array( self::class, 'update_list' ) );
		add_action( 'wp_ajax_nopriv_elex_raq_update_quote_list', array( self::class, 'update_list' ) );


		add_action( 'wp_ajax_get_the_quote_list', array( self::class, 'get_the_quote_list' ) );
		add_action( 'wp_ajax_nopriv_get_the_quote_list', array( self::class, 'get_the_quote_list' ) );

		add_shortcode( 'elex_quote_request_list', array( self::class, 'elex_quote_request_list_shortcode' ) );
		add_shortcode( 'elex_quote_received_page', array( self::class, 'elex_quote_received_page_shortcode' ) );

		//To make Compatible with Avada Astra theme
		if ( in_array( 'fusion-builder/fusion-builder.php', get_option( 'active_plugins' ) ) ) {
			add_action( 'awb_after_woo_add_to_cart_content', array( self::class, 'add_button_to_product_page' ) );
		} else {
	   //To make it compatible with Elementor "woocommerce_product_meta_start" has been replaced with "woocommerce_after_add_to_cart_form" hook.
		add_action( 'woocommerce_after_add_to_cart_form', array( self::class, 'add_button_to_product_page' ) );
		}
		add_action( 'woocommerce_after_shop_loop_item', array( self::class, 'add_button_to_shop_page' ) );

		add_action( 'woocommerce_before_single_variation', array( self::class, 'elex_single_variation_selected' ) );

		add_filter( 'woocommerce_loop_add_to_cart_link', array( self::class, 'show_or_hide_add_to_cart' ), 10, 2 );

		//To make it compatible with elementor use woocommerce_before_add_to_cart_form hook
		add_filter( 'woocommerce_before_add_to_cart_form', array( self::class, 'show_or_hide_add_to_cart_on_product_page' ) );
		
		add_action( 'woocommerce_single_product_summary', array( self::class, 'show_or_hide_add_to_cart_on_product_page' ) );
		
		add_action( 'add_meta_boxes', array( self::class, 'elex_raq_add_meta_box_to_order' ) );

		// registering 
		add_action( 'rest_api_init', array( self::class, 'elex_raq_order_routes_register' ) );


	}

	/** Rest API routes register callback*/
	public static function elex_raq_order_routes_register() {
		register_rest_route(
			self::$api_namespace . '/' . self::$api_version,
			'/request-quote',
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => array( QuoteListModel::class, 'elex_raq_place_order' ),
				'args'                => array(
					'address' => array(
						'required'          => true,
						'validate_callback' => array( QuoteListModel::class, 'elex_raq_address_validation' ),
					),
					'user_id' => array(
						'default'           => 0,
						'required'          => true,
						'validate_callback' => array( QuoteListModel::class, 'elex_raq_user_exists' ),
					),
				),
				'permission_callback' => array( QuoteListModel::class, 'elex_raq_validate_token' ),
			) 
		);
	}


	public static function elex_quote_received_page_shortcode() {

			$form_settings = FormSettingController::get_settings();
			$form_settings = FormSettingController::converToArray( $form_settings );
			$message       = $form_settings['success_message'];
		if ( '' !== $message ) {

			return '<h3>' . __( $message, 'elex_request_a_quote' ) . '</h3>';
		   
		} else {
		   
			return '<h3>' . __( 'Your request has been sent successfully', 'elex_request_a_quote' ) . '</h3>';
		   
		}   
	}
		/** Custom Meta box feature */
	public static function elex_raq_add_meta_box_to_order() {
		
		global $post;
		if ( ! $post ) {
			return;
		}
		if ( ! in_array( $post->post_type, array( 'shop_order' ) ) ) {
			return;
		}
			
		$order    = wc_get_order( $post->ID );
		$order_id = $order->get_id();
	
		if ( ! empty( $order ) ) {
	
			$elex_raq_unqiue_key =  $order->get_meta( '_elex_raq_unique_order_key' );
			if ( 'elex_raq_' . $order_id === $elex_raq_unqiue_key ) {
	
				add_meta_box( 'woocommerce-order-meta-elex-raq', __( 'ELEX Quote Request Details', '' ), array( QuoteListModel::class, 'elex_raq_meta_box' ), 'shop_order', 'normal', 'low' );
			}
		}   
	}

	
	public static function elex_place_order() {
		check_ajax_referer( 'request-a-quote-ajax-nonce', 'ajax_raq_nonce' );

		$post_data = map_deep( $_POST , 'sanitize_text_field' );

		if ( isset( $_POST['cart_item'] ) ) {
			$cart_items = map_deep( $_POST['cart_item'] , 'sanitize_text_field' );

		}

		parse_str( $cart_items, $formdata );// This will convert the string to array.
		
		foreach ( $formdata as $key => $value ) {

			if ( array_key_exists( $key , $post_data ) ) {
				$formdata[ $key ] = $post_data[ $key ];
			}
		}
	
		$address = QuoteListModel::elex_raq_create_order_address( $formdata, $_FILES );
		
		$elex_raq_default_type_count = FormSettingController::get_custom_fields_count();

		$order           = wc_create_order();
		$current_user_id = get_current_user_id();
		if ( 0 !== $current_user_id ) {
			$order->set_customer_id( $current_user_id );
		}
	
			$order->set_address( $address, 'billing' );
			$order->set_address( $address, 'shipping' );
			$order_id = $order->get_id();

			//Add comments to orders
			QuoteListModel::elex_raq_add_order_comments( $order, $formdata );
			QuoteListModel::elex_raq_add_custom_fields_meta( $order, $formdata, $_FILES, $elex_raq_default_type_count );

			$quote_list_id   = QuoteListModel::get_the_quote_list_id( $current_user_id );
			$quote_list_data = QuoteListModel::get_the_quote_list( $quote_list_id );
		if ( empty( $quote_list_data['items'] ) ) {
			wp_send_json_error( array( 'msg' => 'No item in the quote List' ) );
			die();
		}
			
			$form_settings = FormSettingController::get_settings();
			$form_settings = FormSettingController::converToArray( $form_settings );
			$form_fields   = $form_settings['fields'];

			$order->update_meta_data( '_elex_raq_default_form_details', $form_fields );
			$order->update_meta_data( '_elex_raq_unique_order_key', 'elex_raq_' . $order_id );

			$order->update_meta_data( 'elex_quote_list_id', $quote_list_id );
			$order->update_meta_data( 'elex_quote_data', $quote_list_data );

			$order->save(); 

			QuoteListModel::elex_raq_add_products_order( $order, $quote_list_data['items'] );
			QuoteListModel::update_the_quote_list( $quote_list_id, QuoteListModel::$quote_status['quote_requested'] );
			
			wp_send_json_success(
				array(
					'message'         => $form_settings['success_message'],
					'redirection_url' => $form_settings['redirection_url'],
				)
			);
			
	}



	public  static function set_guest_user_cookie() {
		
		if ( ! headers_sent() && ! isset( $_COOKIE[ self::SESSION_KEY_COOKIE_NAME ] ) && ( empty( $_COOKIE[ self::SESSION_KEY_COOKIE_NAME ] ) ) ) {
			QuoteListModel::setCoockie_for_guest_user();

		}

	}


	public static function elex_raq_add_to_quote() {

		check_ajax_referer( 'request-a-quote-ajax-nonce', 'ajax_raq_nonce' );

		if ( ! isset( $_POST['data'] ) || empty( $_POST['data'] ) ) {
			wp_send_json_error( array( 'msg' => 'select product' ) );
			die();
		}
		$product_data = ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) ? map_deep( $_POST['data'], 'sanitize_text_field' ) : array();
		
		
		$product_data_temp = array();

		$user_id = get_current_user_id();

		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );

		$check_if_product_exist = QuoteListModel::find_product_in_quote( $quote_list_id, $product_data );

		if ( null === $check_if_product_exist ) {

			QuoteListModel::add_products_to_quote( $quote_list_id, $product_data );
			
		}
		if ( !empty( $check_if_product_exist ) ) {

			QuoteListModel::update_variation_quantity( $quote_list_id, $product_data );
		}

			ob_start();
			$product_data = QuoteListModel::get_the_product_details( $product_data );
			self::add_quote_button( $product_data );
			$html_content = ob_get_clean();
			wp_send_json_success(
				array(
					'success_toast'   => self::get_success_toast(),
					'html'            => $html_content,
					'quote_list_data' => self::get_the_quote_list_data(),
				)
			);


	}

	public static function get_success_toast() {
		$settings        = SettingsController::get_settings();
		$success_message = $settings['general']['add_to_quote_success_message'];
		ob_start();
		include VIEW_PATH . 'quote/success_toast.php';
		return ob_get_clean();

	}
	public static function elex_raq_update_quantity() {

		check_ajax_referer( 'request-a-quote-ajax-nonce', 'ajax_raq_nonce' );
		if ( ! isset( $_POST['product_id'] ) || empty( $_POST['product_id'] ) ) {
			wp_send_json_error( array( 'msg' => 'select product to to update' ) );
			die();
		}

		if ( ! isset( $_POST['quantity'] ) ) {
			wp_send_json_error( array( 'msg' => 'could not update' ) );
			die();
		}

		
		$product_id   = isset( $_POST['product_id'] ) && ! empty( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
		$quantity     = isset( $_POST['quantity'] ) && ( null !== $_POST['quantity'] ) ? sanitize_text_field( $_POST['quantity'] ) : '';
		$variation_id = isset( $_POST['variation_id'] ) && ( ! empty( $_POST['variation_id'] ) ) ? sanitize_text_field( $_POST['variation_id'] ) : 0;

		$user_id       = get_current_user_id();
		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );
		$result        = QuoteListModel::update_quantity( $quote_list_id , $product_id, $quantity, $variation_id );
		
		if ( $result ) {
			wp_send_json_success(
				array(
					'msg'             => 'update',
					'quote_list_data' => self::get_the_quote_list_data(),
				)
			);
		}

	}

	public static function update_list() {


		check_ajax_referer( 'request-a-quote-ajax-nonce', 'ajax_raq_nonce' );

		$product_data = ( isset( $_POST['data'] ) && ( ! empty( $_POST['data'] ) ) ) ? map_deep( $_POST['data'] , 'sanitize_text_field' ) : array();
		
		if ( empty( $product_data ) ) {
			wp_send_json_error( array( 'msg' => 'No Products' ) );
			die();
		}
	
		$user_id       = get_current_user_id();
		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );
		$result        = QuoteListModel::update_list( $quote_list_id , $product_data );
		
		if ( $result ) {
			wp_send_json_success(
				array(
					'msg'             => 'update',
					'quote_list_data' => self::get_the_quote_list_data(),
				)
			);
		}


	}
	public static function eleX_raq_delete_item() {

		check_ajax_referer( 'request-a-quote-ajax-nonce', 'ajax_raq_nonce' );

		if ( ! isset( $_POST['product_id'] ) || empty( $_POST['product_id'] ) ) {
			wp_send_json_error( array( 'msg' => 'select product to to delete' ) );
			die();
		}
		
		$product_id   = isset( $_POST['product_id'] ) && ! empty( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
		$variation_id = isset( $_POST['variation_id'] ) && ! empty( $_POST['variation_id'] ) ? sanitize_text_field( $_POST['variation_id'] ) : 0;

		
		$user_id       = get_current_user_id();
		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );

		$result = QuoteListModel::delete_item_in_quote( $quote_list_id , $product_id , $variation_id );


		if ( $result ) {
			wp_send_json_success(
				array(
					'msg'             => 'deleted',
					'quote_list_data' => self::get_the_quote_list_data(),
				)
			);
		}

		
	}
	public static function elex_raq_clear_list() {

		check_ajax_referer( 'request-a-quote-ajax-nonce', 'ajax_raq_nonce' );

		
		$user_id       = get_current_user_id();
		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );

		$result = QuoteListModel::clear_list( $quote_list_id );
		if ( $result ) {
			wp_send_json_success(
				array(
					'msg'             => 'clear',
					'quote_list_data' => self::get_the_quote_list_data(),
				)
			);
		}
		
	}
	public static function get_the_quote_list_data() {


		$quote_data    = array();
		$user_id       = get_current_user_id();
		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );

		$quote_data['quote_list']          = QuoteListModel::get_the_quote_list( $quote_list_id );
		$quote_data['settings']            = ListPageController::get_settings( 'quote_list_page' , '' );
		$quote_data['additional_settings'] = ListPageController::get_settings( 'additional_options', '' );
		$quote_data['widget']              = WidgetController::get_settings();
		$quote_data['general']             = SettingsController::get_settings();
		$quote_data['page_url']            = $quote_data['settings']['selected_page'];
		if ( 0 === $user_id &&  ( false === QuoteListModel::is_guest_user_allowed() ) ) {
			$quote_data['page_url'] = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
		}


		// wp_send_json_success($quote_data);
		return $quote_data;

	}

	
	public static function get_the_quote_list() {


		// check_ajax_referer( 'ajax_raq_nonce', 'ajax_raq_nonce');

		$quote_data = array();
		$user_id    = get_current_user_id();

		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );

		$quote_data['quote_list']          = QuoteListModel::get_the_quote_list( $quote_list_id );
		$quote_data['settings']            = ListPageController::get_settings( 'quote_list_page' , '' );
		$quote_data['additional_settings'] = ListPageController::get_settings( 'additional_options', '' );
		$quote_data['widget']              = WidgetController::get_settings();
		$quote_data['general']             = SettingsController::get_settings();
		$quote_data['empty_image']         = ELEX_RAQ_IMAGES . 'Frontend empty Quote list illustration.svg';
		$quote_data['default_image']       = ELEX_RAQ_IMAGES . 'Dummy-Person.jpg';
		$form_settings                     = FormSettingController::get_settings();
		$quote_data['form_settings']       = FormSettingController::converToArray( $form_settings );
		$quote_data['page_url']            = $quote_data['settings']['selected_page'];
		if ( 0 === $user_id &&  ( false === QuoteListModel::is_guest_user_allowed() ) ) {
			$quote_data['page_url'] = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
		}

		wp_send_json_success( $quote_data );

	}

	public static function elex_quote_request_list_shortcode() {

		$quote_data      = ListPageController::get_settings( 'quote_list_page', '' );
		$show_powered_by = $quote_data['show_prowered_by'];
		ob_start();

		include_once VIEW_PATH . 'quote/quote_list_main.php';
		return ob_get_clean();
	}

	public static function add_button_to_shop_page() {
		

		$product_info = QuoteListModel::add_button_to_shop_product_page( 'shop' );
		

		if ( empty( $product_info ) && ! isset( $product_info ) ) {
				return;         
		}
		$product_info['success_message'] = QuoteListModel::get_add_to_quote_success_msg();

		self::add_quote_button( $product_info );

	}

		/** Single Variation Selected. */
	public static  function elex_single_variation_selected() {
		wc_enqueue_js(
			"(function($){
	
				$('form.variations_form').on('show_variation', function(event, data){
					localStorage.setItem('currently_selected_variation_id', data.variation_id);
					var item = new Array();
					var variation_form = $( this ).closest( '.variations_form' );
					var variations = variation_form.find( 'select[name^=attribute]' );
					if ( !variations.length) {
						variations = variation_form.find( '[name^=attribute]:checked' );
					}
					if ( !variations.length) {
						variations = variation_form.find( 'input[name^=attribute]' );
					}
				
					variations.each( function() {
						var tthis = $( this );
							var attributeName = tthis.attr( 'name' );
							var attributevalue = tthis.val();
							var attributeName_final=attributeName.replace('attribute_','');
							item.push({
							attribute_value:attributevalue,
							attribute_name:attributeName_final,
							});
					} );
					localStorage.setItem('selected_variation_attributes', JSON.stringify(item));
					//Enable quote button when variation is selected
					$('.add_to_quote' ).removeClass('disabled');
					$('.add_to_quote' ).css('opacity','');
					$('.add_to_quote' ).removeAttr('disabled');								
				});
				//Disable quote button when reset variation is triggered
				$('.reset_variations').click(function() {
					$('.add_to_quote').addClass('disabled');
					$('.add_to_quote').css('opacity','0.5');
					$('.add_to_quote').attr('disabled', true); 
				});
				})(jQuery);"
		);
	}

/**
 * Function is to decide whether we need to add the add to quote button to the variaable product if all the variations are added to the quote list it will return false else true.
 *
 * @param [array] $product_data
 * @param [int] $quote_list_id
 * @return boolean
 */
	public static function is_button_needed( $product_data, $quote_list_id) {

		if ('variable' == $product_data['type']) {
			$product        = wc_get_product($product_data['id']);
			$variations     = $product->get_available_variations();
			$variations_ids = wp_list_pluck( $variations, 'variation_id' );

			$variations_count_in_quotelist = QuoteListModel::find_variation_count_in_quote( $quote_list_id, $product_data);
			if ($variations_count_in_quotelist
			 < count( $variations_ids ) ) {
				return true;
			}

		}
		return false;
	}

	public static function add_quote_button( $product_data ) {

		
		$list_page_settings = ListPageSettings::load();

		$list_page_settings = $list_page_settings->to_array();
		$list_page_settings = $list_page_settings[ 'quote_list_page' ];
		$page_url           = $list_page_settings['selected_page'];

		$user_id = get_current_user_id();
	
		$quote_list_id = QuoteListModel::get_the_quote_list_id( $user_id );

		if ( 0 === $user_id && ( false === QuoteListModel::is_guest_user_allowed() ) ) {
			$page_url = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
		}
		$check_if_product_exist = QuoteListModel::find_product_in_quote( $quote_list_id, $product_data );
		
		if ( null !== $check_if_product_exist && ( 'variable' === $product_data['type'] || 'composite' === $product_data['type'] ) ) {
			include VIEW_PATH . 'quote/view_quote_list.php';
				include VIEW_PATH . 'quote/add_to_quote.php';

			return;
		} elseif ( ! empty( $check_if_product_exist ) && 'simple' === $product_data['type'] ) {
			include VIEW_PATH . 'quote/view_quote_list.php';
			return;
		} else {
			include VIEW_PATH . 'quote/add_to_quote.php';
		}
		
	}

	public static function add_button_to_product_page() {
	
		$product_data = QuoteListModel::add_button_to_shop_product_page( 'product' );

		if ( empty( $product_data ) && ! isset( $product_data ) ) {
			return;         
		}
		
		self::add_quote_button( $product_data );

	}


	public static function show_or_hide_add_to_cart( $add_to_cart_html, $product ) {

		
		$result = QuoteListModel::show_or_hide_add_to_cart( 'shop' );
		QuoteListModel::show_or_hide_price();
		
		if ( true === $result ) {
			return '';
		}
		return $add_to_cart_html;
		
	}


	public static function show_or_hide_add_to_cart_on_product_page() {
		global $post;
		$product = wc_get_product( $post->ID );


		$result = QuoteListModel::show_or_hide_add_to_cart( 'product' );
		QuoteListModel::show_or_hide_price();

		
		if ( true === $result ) {

			if ( $product->get_type() == 'variable' ) {
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
				add_action( 'woocommerce_single_variation', 'woocommerce_quantity_input', 10 );
			} else {
				wc_enqueue_js(
					"
						jQuery('button[name=add-to-cart]').remove();
						jQuery('button.single_add_to_cart_button:nth-of-type(2)').remove();
						"
				);
			}
		}
			
		
	}

	
}
