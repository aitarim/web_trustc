<?php
namespace Elex\RequestAQuote\TemplateSetting;

use Elex\RequestAQuote\TemplateSetting\Models\TemplateModel;
use Elex\RequestAQuote\FormSetting\Models\FormSettings;
use Elex\RequestAQuote\FormSetting\FormSettingController;
use Elex\RequestAQuote\Settings\SettingsController;


class TemplateController {

	public static $settings = null;

	public static function init() {
		add_action( 'req_settings_tab_template', array( self::class, 'load_template' ) );

		add_filter( 'settings_saving_template', array( self::class, 'save_template' ) );

	}

	/**
	 * Function to get the template settings stored
	 *
	 * @param boolean $reload
	 * @return void
	 */
	public static function get_settings( $reload = false ) {

		$settings = TemplateModel::load();
		$settings = $settings->to_array();

		self::$settings = apply_filters( 'request_a_quote_settings', $settings );

		return  self::$settings;

	}

	public static function load_template() {

		$settings      = self::get_settings();
		$form_settings = FormSettingController::get_settings();
		$form_settings = FormSettingController::converToArray( $form_settings );
		$form_fields   = $form_settings['fields'];
		
		SettingsController::show_saved_toast();

		include VIEW_PATH . 'settings/template.php';
	}

	public static function save_template( TemplateModel $settings ) {
	   check_admin_referer( 'save_settings', 'req_settings_nonce' );
	   
		$new_settings = array();
		
		$new_settings['template_id']         = isset( $_POST['template_id'] ) ? sanitize_text_field( $_POST['template_id'] ) : 1;
		$new_settings['predefined_template'] = false;
		$new_settings['company_logo']        = '';
		$new_settings['is_terms_enabled']    = isset( $_POST['is_terms_enabled'] ) ? true : false;
		$new_settings['terms_conditions']    = isset( $_POST['terms_conditions'] ) ? wp_kses_post( sanitize_text_field( $_POST['terms_conditions'] ) ) : '';

		$new_settings['sent_to_admin']['quote_requested_email_template']['subject'] = isset( $_POST['quote_requested_mail_subject_to_admin'] ) ? sanitize_text_field( $_POST['quote_requested_mail_subject_to_admin'] ) : 'Quote Request';
		$new_settings['sent_to_admin']['quote_requested_email_template']['heading'] = isset( $_POST['quote_requested_mail_heading_to_admin'] ) ? sanitize_text_field( $_POST['quote_requested_mail_heading_to_admin'] ) : 'Your Quote Request has been received.';
		$new_settings['sent_to_admin']['quote_requested_email_template']['body']    = isset( $_POST['quote_requested_mail_body_to_admin'] ) ? wp_kses_post( $_POST['quote_requested_mail_body_to_admin'] ) : '';
	
		$new_settings['sent_to_admin']['quote_requested_sms_chat_template']['body'] = isset( $_POST['quote_requested_sms_chat_body_to_admin'] ) ? wp_kses_post( $_POST['quote_requested_sms_chat_body_to_admin'] ) : '';

		$new_settings['sent_to_customer']['quote_requested_email_template']['subject'] = isset( $_POST['quote_requested_mail_sub_to_cust'] ) ? sanitize_text_field( $_POST['quote_requested_mail_sub_to_cust'] ) : 'Quote Received';
		$new_settings['sent_to_customer']['quote_requested_email_template']['heading'] = isset( $_POST['quote_requested_mail_heading_to_cust'] ) ? sanitize_text_field( $_POST['quote_requested_mail_heading_to_cust'] ) : 'Your Quote Request has been received.';
		$new_settings['sent_to_customer']['quote_requested_email_template']['body']    = isset( $_POST['quote_requested_mail_body_to_cust'] ) ? wp_kses_post( $_POST['quote_requested_mail_body_to_cust'] ) : '';
	
		$new_settings['sent_to_customer']['quote_approved_email_template']['subject'] = isset( $_POST['quote_approved_mail_subject_to_cust'] ) ? sanitize_text_field( $_POST['quote_approved_mail_subject_to_cust'] ) : 'Quote Approved';
		$new_settings['sent_to_customer']['quote_approved_email_template']['heading'] = isset( $_POST['quote_approved_mail_heading_to_cust'] ) ? sanitize_text_field( $_POST['quote_approved_mail_heading_to_cust'] ) : 'Your Quote Request has been approved.';
		$new_settings['sent_to_customer']['quote_approved_email_template']['body']    = isset( $_POST['quote_approved_mail_body_to_cust'] ) ? wp_kses_post( $_POST['quote_approved_mail_body_to_cust'] ) : '';
		
		$new_settings['sent_to_customer']['quote_rejected_email_template']['subject'] = isset( $_POST['quote_rejected_mail_subject_to_cust'] ) ? sanitize_text_field( $_POST['quote_rejected_mail_subject_to_cust'] ) : 'Quote Rejected';
		$new_settings['sent_to_customer']['quote_rejected_email_template']['heading'] = isset( $_POST['quote_rejected_mail_heading_to_cust'] ) ? sanitize_text_field( $_POST['quote_rejected_mail_heading_to_cust'] ) : 'Your Quote has been rejected.';
		$new_settings['sent_to_customer']['quote_rejected_email_template']['body']    = isset( $_POST['quote_rejected_mail_body_to_admin'] ) ? wp_kses_post( $_POST['quote_rejected_mail_body_to_admin'] ) : '';
		$_SESSION['saved_settings_data'] = true;

		return $settings->merge( $new_settings );
	}
}
