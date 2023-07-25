<?php
namespace Elex\RequestAQuote;

use Elex\RequestAQuote\Settings\SettingsController;
use Elex\RequestAQuote\FormSetting\FormSettingController;
use Elex\RequestAQuote\HelpAndSupport\HelpAndSupportController;
use Elex\RequestAQuote\Quotelist\ListPageController;
use Elex\RequestAQuote\Quotelist\QuoteListController;


class RequestAQuote {


	const VERSION  = '';
	const INSTANCE = 'RAQ_BASIC';

	public $plugin_basename;

	public function with_basename( $basename ) {
		$this->plugin_basename = $basename;

		return $this;
	}

	public function boot() {

		$this->register_hooks();
	}

	public function register_hooks() {

		add_action( 'admin_init', array( $this, 'migrate' ) );

		add_action( 'init', array( $this, 'load_language_files' ) );

		add_action( 'init', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_scripts' ) , 999 );

		add_action( 'init', array( $this, 'add_menu' ) );

		add_action( 'widgets_init', array( self::class, 'wp_custom_widget' ) );

		add_action( 'wp_nav_menu_items', array( self::class, 'add_minicart_to_header' ) );
		add_action( 'elementor/widgets/register', array( self::class, 'register_elex_raq_add_to_quote_widget' ) );
	
		add_filter( 'the_title', array( self::class, 'hide_quote_list_page_title' ) , 10, 1 );
		


		$this->register_routes();
		add_action(
			'woocommerce_init',
			function() {
				wc_enqueue_js(
					"	
				jQuery('a[href*=\"quote-received-page\"]').closest('li').remove();
				//Fires whenever variation selects are changed
				jQuery( '.variations_form' ).on( 'woocommerce_variation_select_change', function () {
					// Fires when variation option isn't selected
					jQuery('form.variations_form').on('hide_variation',function(event, data){
						jQuery('.add_to_quote').addClass('disabled');
						jQuery('.add_to_quote').css('opacity','0.5');
						jQuery('.add_to_quote').attr('disabled', true); 
					});	
				});
			"
				);
			}
		);

	}

	/**
	 * Function to hide the title of the page added by woocommerce as we are adding the page title through our plugin for quote list page.
	 *
	 * @param [string] $title
	 * @return void
	 */
	public static function hide_quote_list_page_title( $title ) {
	
		if ( is_page( 'add-to-quote-product-list' ) ) { 
			$title = '';
		}
		return $title;

	}

	public static function wp_custom_widget() {

		include_once 'CustomWidget/Custom_Widget.php';
		register_widget( 'Custom_Widget' );
	}



	public static function add_minicart_to_header() {
		$items = the_widget( 'Custom_Widget', 'title=' );
		return $items;
	}

	/** Elementor Add to Quote Widget */
	public static function register_elex_raq_add_to_quote_widget( $widgets_manager ) {

		require_once  __DIR__ . '/ElementorWidget/Eraq_add_to_quote_widget.php' ;
	
		$widgets_manager->register( new \Eraq_Add_To_Quote_Widget() );
	}

	public function register_routes() {
		SettingsController::init();
		FormSettingController::init();
		HelpAndSupportController::init();
		ListPageController::init();
		QuoteListController::init();
		
		include_once 'create_order_status.php';
		
	}
	
	public function migrate() {

		Migrate::run();
	}

	public function elex_quote_request_add_navigation_menu() {

		$quote_request_page = array(
			'post_title'   => __( 'Quotes List', 'elex-request-a-quote' ),
			'post_content' => '[elex_quote_request_list]',
			'post_status'  => 'publish',
			'post_name'    => 'add-to-quote-product-list',
			'post_type'    => 'page',
		);

		wp_insert_post( $quote_request_page );

		// Create post object.
		$quote_received_page = array(
			'post_title'   => __( 'Quote Received', 'elex-request-a-quote' ),
			'post_content' => '[elex_quote_received_page]',
			'post_status'  => 'publish',
			'post_name'    => 'quote-received-page',
			'post_type'    => 'page',
		);

		// Insert the post into the database.
		wp_insert_post( $quote_received_page );


	}

	

	/** Flush data on deactivation. */
	public function elex_quote_request_flush_data() {
		$path_object_1 = get_page_by_path( '/add-to-quote-product-list' );
		wp_delete_post( $path_object_1->ID );
		$path_object_2 = get_page_by_path( '/quote-received-page' );
		wp_delete_post( $path_object_2->ID );
	}

	public function enqueue_scripts() {
		global $plugin_page;
		$tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
		if ( 'form' === $tab ) {
			wp_enqueue_script( 'request_a_quote_formsetting', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/components/form_settings.min.js' ), array( 'jquery', 'wp-element', 'wp-i18n' ), self::VERSION );
			self::form_settings_localize_script();
		}


		wp_enqueue_script( 'request_a_quote_select_2_js', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/select2-min.js' ), array( 'jquery', 'underscore' ), self::VERSION, true );
		wp_enqueue_style( 'request_a_quote_select_2_css', plugins_url( dirname( $this->plugin_basename ) . '/assets/css/select-2-min.css' ), array(), self::VERSION );
		wp_enqueue_script( 'request_a_quote_script', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/req_script.js' ), array( 'jquery' ), self::VERSION , true );
		
		wp_enqueue_script( 'request_a_quote_popper_script', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/popper.js' ), array(), self::VERSION , true );
		wp_enqueue_script( 'request_a_quote_bootstrap_script', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/bootstrap.js' ), array(), self::VERSION , true );
		wp_enqueue_script( 'request_a_quote_fontawesome', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/fontawesome.js' ), array(), self::VERSION , true );
		wp_enqueue_script( 'request_a_quote_chosen', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/settings.js' ), array(), self::VERSION , true );

		$page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
		
		//Do not Load the front end scripts in admin pages
		$pages = array( 'settings', 'product_importer', 'product_exporter' );
		if ( ! in_array( $page, $pages ) ) {
			wp_enqueue_script( 'quote_list', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/components/quote_list/quote_list.min.js' ), array( 'jquery', 'underscore', 'wp-element', 'wp-i18n' ), self::VERSION );
			wp_enqueue_script( 'mini_quote_list', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/components/mini_quote_list/render_mini_quote_list.min.js' ), array( 'jquery', 'underscore', 'wp-element', 'wp-i18n' ), self::VERSION );
			wp_enqueue_script( 'mini_quote', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/miniquote.js' ), array( 'jquery', 'underscore', 'wp-element', 'wp-i18n' ), self::VERSION );
			wp_enqueue_script( 'add_to_quote', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/components/quote_list/add_to_quote.js' ), array( 'jquery', 'underscore', 'wp-element', 'wp-i18n' ), self::VERSION , true );
		}
		wp_enqueue_style( 'request_a_quote_front_style', plugins_url( dirname( $this->plugin_basename ) . '/assets/css/app.css' ), array(), self::VERSION );  
		wp_enqueue_script( 'quote_items', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/components/quote_list/quote_items.min.js' ), array( 'jquery', 'underscore', 'wp-element', 'request_a_quote_script', 'wp-i18n' ), self::VERSION , true );

		self::quote_list_localize_script();
		self::localize_script();
		
	}
	public function enqueue_front_end_scripts() {
		if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
		wp_enqueue_script( 'request_a_quote_front_script', plugins_url( dirname( $this->plugin_basename ) . '/assets/js/front_script.min.js' ), array(), self::VERSION , true );
		}

	}

	public function form_settings_localize_script() {
		wp_localize_script(
			'request_a_quote_formsetting',
			'raq_formsetting_ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'raq-formsetting-ajax-nonce' ),
			)
		);
	}

	public function localize_script() {
		wp_localize_script(
			'request_a_quote_script',
			'request_a_quote_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'request-a-quote-ajax-nonce' ),
			)
		);
	}

	public function quote_list_localize_script() {
		wp_localize_script(
			'quote_list',
			'quote_list_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'request-a-quote-ajax-nonce' ),
			)
		);
	}

	public function load_language_files() {
		load_plugin_textdomain( 'elex-request-a-quote', false, __DIR__ . '/../lang' );
	}


	public function add_menu() {
		add_action( 'admin_menu', array( $this, 'add_admin_main_menu' ) );

	}

	public function add_admin_main_menu() {

		$parent_slug = 'elex-request-a-quote';

		add_menu_page(
			__( 'Request a Quote', 'elex-request-a-quote' ),
			__( 'Request a Quote', 'elex-request-a-quote' ),
			'manage_options',
			$parent_slug,
			array( SettingsController::class, 'load_general_settings' ),
			esc_url( plugins_url() . '/elex_request_a_quote_premium/assets/images/ELEX-grey-logo-forsidebar.svg' ),
			57
		);

		add_submenu_page(
			$parent_slug,
			__( 'Settings', 'elex-request-a-quote' ),
			__( 'Settings', 'elex-request-a-quote' ),
			'manage_options',
			'settings',
			array( SettingsController::class, 'load_view' )
		);

		add_submenu_page(
			$parent_slug,
			__( 'Customize Quote List & Form', 'elex-request-a-quote' ),
			__( 'Customize Quote List & Form', 'elex-request-a-quote' ),
			'manage_options',
			'listpage',
			array( ListPageController::class, 'load_view' )
		);
		add_submenu_page(
			$parent_slug,
			__( 'Help & Support', 'elex-request-a-quote' ),
			__( 'Help & Support', 'elex-request-a-quote' ),
			'manage_options',
			'helpandsupport',
			array( HelpAndSupportController::class, 'load_view' )
		);
		remove_submenu_page( $parent_slug, $parent_slug ); 

	}

}
