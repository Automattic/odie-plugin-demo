<?php
/**
 * Primary class file for the Odie Plugin Demo.
 *
 * @package automattic/odie-plugin-demo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\Jetpack\Admin_UI\Admin_Menu;
use Automattic\Jetpack\Connection\Manager as Connection_Manager;
use Automattic\Jetpack\Connection\Rest_Authentication as Connection_Rest_Authentication;
use Automattic\Jetpack\My_Jetpack\Initializer as My_Jetpack_Initializer;
use Automattic\Jetpack\Odie_Plugin_Demo\REST_Controller as Odie_Rest_Controller;

/**
 * Class Odie_Plugin_Demo
 */
class Odie_Plugin_Demo {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Set up the REST authentication hooks.
		Connection_Rest_Authentication::init();

		$page_suffix = Admin_Menu::add_menu(
			__( 'Odie Plugin Demo', 'odie-plugin-demo' ),
			_x( 'Odie Plugin', 'The name of the plugin', 'odie-plugin-demo' ),
			'manage_options',
			'odie-plugin-demo',
			array( $this, 'plugin_settings_page' ),
			99
		);
		add_action( 'load-' . $page_suffix, array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		if ( ! is_admin() ) {
			// add_action( 'wp_footer', array( $this, 'inject_odie_widget_root' ) );
			// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		}

		// Init Jetpack packages
		add_action(
			'plugins_loaded',
			function () {
				$config = new Automattic\Jetpack\Config();
				// Connection package.
				$config->ensure(
					'connection',
					array(
						'slug'     => ODIE_PLUGIN_DEMO_SLUG,
						'name'     => ODIE_PLUGIN_DEMO_NAME,
						'url_info' => ODIE_PLUGIN_DEMO_URI,
					)
				);
				// Sync package.
				$config->ensure( 'sync' );

				// Identity crisis package.
				$config->ensure( 'identity_crisis' );
			},
			1
		);

		My_Jetpack_Initializer::init();

		add_action( 'rest_api_init', array( new Odie_Rest_Controller(), 'register_rest_routes' ) );
	}

	/**
	 * Initialize the admin resources.
	 */
	public function admin_init() {
		add_action( 'admin_notices', array( $this, 'maybe_connect_first_notice' ) );
		add_action( 'admin_notices', array( $this, 'inject_odie_widget_root' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	public function inject_odie_widget_root() {
		?>
			<div id="jetpack-odie-root"></div>
		<?php
	}

	/**
	 * Displays a notice to the user to connect to WordPress.com first.
	 */
	public static function maybe_connect_first_notice() {
		$connection = new Connection_Manager();
		if ( $connection->is_user_connected() ) {
			return;
		}
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php esc_html_e( 'Odie Plugin Demo requires a wordpress.com connection.', 'odie-plugin-demo' ); ?></p>
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=my-jetpack#/connection' ) ); ?>"><?php esc_html_e( 'Connect to chat', 'odie-plugin-demo' ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Enqueue plugin admin scripts and styles.
	 */
	public function enqueue_admin_scripts() {
		$connection = new Connection_Manager();
		if ( ! $connection->is_connected() ) {
			return;
		}

		// Build the user JID based on the connected wpcom user login.
		$connected_user = $connection->get_connected_user_data();
		if ( $connected_user['login'] ) {
			$userJid = $connected_user['login'] . '@xmpp.jetpacksandbox.com';
		} else {
			// Non-connected wpcom users are not yet supported.
			return;
		}

		wp_enqueue_script( 'wpcom-odie-widget', '//widgets.wp.com/odie/widget.js', array(), time(), true );
		wp_localize_script( 'wpcom-odie-widget', 'wpcomOdieWidget', array(
			'isRunningInJetpack' => true,
			'jetpackXhrParams'   => array(
				'apiRoot'     => esc_url_raw( rest_url() ),
				'headerNonce' => wp_create_nonce( 'wp_rest' ),
			),
			'authToken'          => 'wpcom-proxy-request',
			'botJids'            => array( 'wapuu-bot@xmpp.jetpacksandbox.com' ),
			'siteId'             => $connection::get_site_id(),
			'service'            => 'wss://xmpp.jetpacksandbox.com:5443/ws',
			'userJid'            => $userJid,
			// 'botConfigUrl'       => esc_url_raw( rest_url( '/odie-plugin-demo/v1/bot-config' ) ),
		) );
		wp_enqueue_script( 
			'jetpack-odie-js',
			plugins_url( '/src/js/odie.js', ODIE_PLUGIN_DEMO_ROOT_FILE_RELATIVE_PATH ),
			array( 'wpcom-odie-widget' ),
			time(),
			array( 'in_footer' => true )
		);
	}

	/**
	 * Main plugin settings page.
	 */
	public function plugin_settings_page() {
		?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Odie Plugin Demo', 'odie-plugin-demo' ); ?></h1>
				<p><?php esc_html_e( 'This is the main plugin settings page.', 'odie-plugin-demo' ); ?></p>
				<p><?php esc_html_e( 'This is a test.', 'odie-plugin-demo' ); ?></p>
				<div id="jetpack-odie-root"></div>
			</div>
		<?php
	}

	/**
	 * Removes plugin from the connection manager
	 * If it's the last plugin using the connection, the site will be disconnected.
	 *
	 * @access public
	 * @static
	 */
	public static function plugin_deactivation() {
		$manager = new Connection_Manager( 'odie-plugin-demo' );
		$manager->remove_connection();
	}
}
