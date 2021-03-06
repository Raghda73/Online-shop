<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_REST_Connect_Services_Controller' ) ) {
	return;
}

class WC_REST_Connect_Services_Controller extends WC_REST_Connect_Base_Controller {

	protected $method = 'POST';
	protected $rest_base = 'connect/services/(?P<id>[a-z_]+)\/(?P<instance>[\d]+)';

	/**
	 * @var WC_Connect_Service_Schemas_Store
	 */
	protected $service_schemas_store;

	public function __construct( WC_Connect_API_Client $api_client, WC_Connect_Service_Settings_Store $settings_store, WC_Connect_Logger $logger, WC_Connect_Service_Schemas_Store $schemas_store ) {
		parent::__construct( $api_client, $settings_store, $logger );
		$this->service_schemas_store  = $schemas_store;
	}

	/**
	 * Attempts to update the settings on a particular service and instance
	 */
	public function run( $request ) {
		$request_params = $request->get_params();

		$id = array_key_exists( 'id', $request_params ) ? $request_params['id'] : '';
		$instance = array_key_exists( 'instance', $request_params ) ? absint( $request_params['instance'] ) : false;

		if ( empty( $id ) ) {
			$error = new WP_Error( 'service_id_missing',
				__( 'Unable to update service settings. Form data is missing service ID.', 'woocommerce-services' ),
				array( 'status' => 400 )
			);
			$this->logger->debug( $error, __CLASS__ );
			return $error;
		}

		$settings = ( object ) $request->get_json_params();

		if ( empty( $settings ) ) {
			$error = new WP_Error( 'bad_form_data',
				__( 'Unable to update service settings. The form data could not be read.', 'woocommerce-services' ),
				array( 'status' => 400 )
			);
			$this->logger->debug( $error, __CLASS__ );
			return $error;
		}

		$validation_result = $this->settings_store->validate_and_possibly_update_settings( $settings, $id, $instance );

		if ( is_wp_error( $validation_result ) ) {
			$error = new WP_Error( 'validation_failed',
				sprintf(
					__( 'Unable to update service settings. Validation failed. %s', 'woocommerce-services' ),
					$validation_result->get_error_message()
				),
				array_merge(
					array( 'status' => 400 ),
					$validation_result->get_error_data()
				)
			);
			$this->logger->debug( $error, __CLASS__ );
			return $error;
		}

		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

}
