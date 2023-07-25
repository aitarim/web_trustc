<?php
namespace Elex\RequestAQuote\Quotelist;

use Elex\RequestAQuote\Quotelist\Models\ListPageSettings;

use Elex\RequestAQuote\FormSetting\FormSettingController;
use Elex\RequestAQuote\Settings\SettingsController;
use Elex\RequestAQuote\FormSetting\Models\FormSettings;

class ListPageController {

	public static $settings = null;

	public static function init() {
		FormSettingController::init();

		add_action( 'req_settings_tab_listpage', array( self::class, 'load_quote_list_page_settings' ) );
		add_action( 'req_settings_tab_additional', array( self::class, 'load_additional_options' ) );
		

		add_filter( 'settings_saving_listpage', array( self::class, 'save_quote_list_page_settings' ) );
		add_filter( 'settings_saving_additional', array( self::class, 'save_additional_options' ) );
		add_action( 'init', array( self::class, 'update_quote_list_page_title' ) );
		

	}

	
	public static function get_settings( $key, $reload = false ) {

		$settings = ListPageSettings::load();

		$settings = $settings->to_array();
		$settings = $settings[ $key ];

		self::$settings = apply_filters( 'request_a_quote_settings', $settings );

		return  self::$settings;

	}
	public static function update_quote_list_page_title() {
		
		$settings   = self::get_settings( 'quote_list_page', false );
		$page_title = $settings['title'];

		$path_object = get_page_by_path( '/add-to-quote-product-list' );
		if ( is_object( $path_object ) ) {
			$my_post = array(
				'ID'         => $path_object->ID,
				'post_title' => $page_title,
			);
			wp_update_post( $my_post );
		}
	}

	
	public static function load_quote_list_page_settings() {
		$settings          = self::get_settings( 'quote_list_page', false );
		$settings['pages'] = self::get_available_page();
		include VIEW_PATH . 'listpage_form_setting/quote_list.php';
		SettingsController::show_saved_toast();
	}
	public static function load_additional_options() {
		$settings = self::get_settings( 'additional_options', false );



		$settings['pages'] = self::get_available_page();
		include VIEW_PATH . 'listpage_form_setting/additional_options.php';
		SettingsController::show_saved_toast();


	}
	
	public static function get_available_page() {
		global $wpdb;
		$page_list    = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_title, guid FROM $wpdb->posts WHERE post_type = %s AND post_status = %s",
				'page',
				'publish'
			)
		);
		$page_options = array();
	
		foreach ( $page_list as $k => $v ) {
			
			$page_guid = $v->guid;
			if ( 'listpage' === self::get_active_tab() ) {
				if ( ! preg_match( '/(\/quote-received-page\/|\/cart\/|\/shop\/|\/my-account\/|\/checkout\/)/i', $page_guid ) ) { // Avoid paths listed in the expression.
					$page_options[ $page_guid ] = $v->post_title;
				}
			} else {
				if ( ! preg_match( '/(\/add-to-quote-product-list\/|\/quote-received-page\/|\/cart\/|\/my-account\/|\/checkout\/)/i', $page_guid ) ) { // Avoid paths listed in the expression.
					$page_options[ $page_guid ] = $v->post_title;
				}           
			}       
		}
		return $page_options;
	}
	public static function load_view() {

		
		global $plugin_page;
		$sub_tabs    = self::get_menus();
		$active_tab  = self::get_active_tab();
		$active_page = $plugin_page;

		if ( isset( $_POST['submit'] ) ) {

			// check_admin_referer( 'save_settings', 'req_settings_nonce' );
		check_admin_referer( 'save_settings', 'req_settings_nonce' );


			$settings = ListPageSettings::load();

			if ( 'form' === self::get_active_tab() ) {
				$settings = FormSettings::load();

			}

			$settings = apply_filters( 'settings_saving_' . self::get_active_tab(), $settings );

			$settings->save();
		
		}
		include VIEW_PATH . 'settings.php';
	}
	public static function get_menus() {
		$setting_menus = array(
			array(
				'title' => __( 'Quote List Page' ),
				'slug'  => 'listpage',
			),
			
			array(
				'title' => __( 'Additional Options' ),
				'slug'  => 'additional',
		
			),
			array(
				'title' => __( 'Quote Form' ),
				'slug'  => 'form',
			),
		
		
		);
		return apply_filters( 'settings_tabs', $setting_menus );

		
	}

	public static function get_active_tab() {

		if ( isset( $_POST['_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) ) ) {
			return;
		}

	   $tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
		return ! empty( $tab ) ? $tab : self::get_default_tab();

	}

	public static function get_default_tab() {
		return apply_filters( 'request_a_quote_list_settings_default_tab', 'listpage' );
	}





	public static function save_quote_list_page_settings( ListPageSettings $stored_settings ) {
		check_admin_referer( 'save_settings', 'req_settings_nonce' );

		$new_settings = array();

		$new_settings['quote_list_page']['selected_page']                                = isset( $_POST['selected_page'] ) ? sanitize_text_field( $_POST['selected_page'] ) : '';
		$new_settings['quote_list_page']['title']                                        = isset( $_POST['page_title'] ) ? sanitize_text_field( $_POST['page_title'] ) : 'Quote List';
		$new_settings['quote_list_page']['layout']['product_on_left_form_on_right']      = isset( $_POST['layout_choice'] ) && ( 'product_on_left_form_on_right' === sanitize_text_field( $_POST['layout_choice'] ) ) ? true : false;
		$new_settings['quote_list_page']['layout']['product_on_top_form_on_bottom']      = isset( $_POST['layout_choice'] ) && ( 'product_on_top_form_on_bottom' === sanitize_text_field( $_POST['layout_choice'] ) ) ? true : false;
		$new_settings['quote_list_page']['show_if_list_empty']['illustration']           = true;
		$new_settings['quote_list_page']['show_if_list_empty']['empty_text']['enabled']  = true;
		$new_settings['quote_list_page']['show_if_list_empty']['empty_text']['text']     = __('Your Quote List is Empty' , 'elex-request-a-quote' );
		$new_settings['quote_list_page']['show_if_list_empty']['go_to_shop_page_button'] = true;
		$new_settings['quote_list_page']['show_if_list_empty']['quote_request_form']     = false;

		$new_settings['quote_list_page']['show_on_product_table']['product_image']         = true;
		$new_settings['quote_list_page']['show_on_product_table']['product_price']         = true;
		$new_settings['quote_list_page']['show_on_product_table']['quantity']              = true;
		$new_settings['quote_list_page']['show_on_product_table']['each_product_subtotal'] = true;
		$new_settings['quote_list_page']['show_on_product_table']['product_sku']           = false;
		$new_settings['quote_list_page']['show_on_product_table']['taxes']                 = false;
		$new_settings['quote_list_page']['show_prowered_by']                               = isset( $_POST['show_prowered_by'] ) ? true : false;
		$_SESSION['saved_settings_data'] = true;
		return $stored_settings->merge( $new_settings );


	}

	public static function save_additional_options( ListPageSettings $stored_settings ) {
		check_admin_referer( 'save_settings', 'req_settings_nonce' );

		$new_settings = array();
	

		$new_settings['additional_options']['update_list_button']                = isset( $_POST['update_list_button'] ) ? true : false;
		$new_settings['additional_options']['clear_list']                        = isset( $_POST['clear_list_button'] ) ? true : false;
		$new_settings['additional_options']['add_more_items_button']             = isset( $_POST['add_more_items'] ) ? true : false;
		$new_settings['additional_options']['add_more_items_button_label']       = isset( $_POST['add_more_item_btn_label'] ) ? sanitize_text_field( $_POST['add_more_item_btn_label'] ) : 'Add More Items';
		$new_settings['additional_options']['add_more_items_button_redirection'] = isset( $_POST['add_more_item_btn_redirection'] ) ? filter_var( $_POST['add_more_item_btn_redirection'], FILTER_VALIDATE_URL ) : ListPageSettings::get_page_url( 'shop' );

		$_SESSION['saved_settings_data'] = true;
		return $stored_settings->merge( $new_settings );
	}
}
