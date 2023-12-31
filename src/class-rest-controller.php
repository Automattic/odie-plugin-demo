<?php
/**
 * REST API Endpoints
 *
 * @package automattic/odie-plugin-demo
 */

namespace Automattic\Jetpack\Odie_Plugin_Demo;

use Automattic\Jetpack\Connection\Client;

/**
 * Makes REST API Endpoints for Chat
 *
 * @package Automattic\Jetpack\Chat
 */
class REST_Controller {

	/**
	 * Namespace.
	 *
	 * @var string $namespace The namespace for the REST API.
	 */
	public static $namespace = 'jetpack/v4/chat';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Set up REST API routes.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Register REST API
	 */
	public function register_rest_routes() {
		// Get migration status from source site.
		register_rest_route(
			static::$namespace,
			'/odie/start_chat',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'start_chat' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		// Get migration status from source site.
		register_rest_route(
			static::$namespace,
			'/odie/get_or_create_xmpp_user_creds',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_or_create_xmpp_user_creds' ),
				'permission_callback' => array( $this, 'permissions_check' ),
			)
		);

		// Serve JSON data with the bot configuration.
		register_rest_route( 'jetpack-odie-plugin/v1', '/bot-config', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_odie_bot_config' ),
			'permission_callback' => '__return_true'
		) );
	}

	/**
	 * Pass custom config to the bot.
	 * This is mostly a @todo. 
	 */
	public function get_odie_bot_config() {
		$odie_bot_config = array(
			"botName" => "Jetpack Odie",
			"botNameSlug" => "jetpack-odie",
			"errorMessage" => "There was an error processing your message, please try again later.",
			"minimized" => array(
				"height" => "64px"
			),
			"expanded" => array(
				"width" => "60%",
				"height" => "100%"
			),
			"nudgeAnimationSource" => "container",
			"loadingAnimationSource" => "container",
			"defaultWidth" => "500px",
			"defaultHeight" => "calc(100vh - 256px)"
		);

		return rest_ensure_response( $odie_bot_config );
	}

	/**
	 * Initiates a chat session.
	 *
	 * GET `jetpack/v4/chat/odie/get_or_create_xmpp_user_creds`
	 */
	public function get_or_create_xmpp_user_creds() {
		$response = Client::wpcom_json_api_request_as_user(
			'/odie/get_or_create_xmpp_user_creds',
			'2',
			array( 'method' => 'POST' ),
			null,
			'wpcom'
		);

		return rest_ensure_response( $this->make_proper_response( $response ) );
	}

	/**
	 * Initiates a chat session.
	 *
	 * GET `jetpack/v4/chat/odie/start_chat`
	 *
	 * @param WP_REST_Request $req The REST request object.
	 */
	public function start_chat( $req ) {
		$response = Client::wpcom_json_api_request_as_user(
			'/odie/start_chat',
			'2',
			array( 'method' => 'POST' ),
			$req->get_body(),
			'wpcom'
		);

		return rest_ensure_response( $this->make_proper_response( $response ) );
	}

	/**
	 * Permissions check for start_chat.
	 *
	 * @return bool|WP_Error True if permission granted.
	 */
	public function permissions_check() {
		// TODO: How are site visitors going to chat?
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$error_msg = esc_html__(
			'You are not allowed to perform this action.',
			'odie-plugin-demo'
		);

		return new \WP_Error( 'rest_forbidden', $error_msg, array( 'status' => rest_authorization_required_code() ) );
	}

	/**
	 * Forward remote response to client with error handling.
	 *
	 * @param array|WP_Error $response - Response from WPCOM.
	 */
	private function make_proper_response( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body        = json_decode( wp_remote_retrieve_body( $response ), true );
		$status_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $status_code ) {
			return $body;
		}

		return new \WP_Error(
			isset( $body['error'] ) ? 'remote-error-' . $body['error'] : 'remote-error',
			isset( $body['message'] ) ? $body['message'] : 'unknown remote error',
			array( 'status' => $status_code )
		);
	}
}
