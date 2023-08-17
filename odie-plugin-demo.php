<?php
/**
 *
 * Plugin Name: Odie Plugin Demo
 * Plugin URI: https://wordpress.org/plugins/odie-plugin-demo
 * Description: Odie in a WP plugin!.
 * Version: 0.3.1-alpha
 * Author: Automattic
 * Author URI: https://jetpack.com/
 * License: GPLv2 or later
 * Text Domain: odie-plugin-demo
 *
 * @package automattic/odie-plugin-demo
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ODIE_PLUGIN_DEMO_DIR', plugin_dir_path( __FILE__ ) );
define( 'ODIE_PLUGIN_DEMO_ROOT_FILE', __FILE__ );
define( 'ODIE_PLUGIN_DEMO_ROOT_FILE_RELATIVE_PATH', plugin_basename( __FILE__ ) );
define( 'ODIE_PLUGIN_DEMO_SLUG', 'odie-plugin-demo' );
define( 'ODIE_PLUGIN_DEMO_NAME', 'Odie Plugin Demo' );
define( 'ODIE_PLUGIN_DEMO_URI', 'https://jetpack.com/' );
define( 'ODIE_PLUGIN_DEMO_FOLDER', dirname( plugin_basename( __FILE__ ) ) );

// Jetpack Autoloader.
$jetpack_autoloader = ODIE_PLUGIN_DEMO_DIR . 'vendor/autoload_packages.php';
if ( is_readable( $jetpack_autoloader ) ) {
	require_once $jetpack_autoloader;
	if ( method_exists( \Automattic\Jetpack\Assets::class, 'alias_textdomains_from_file' ) ) {
		\Automattic\Jetpack\Assets::alias_textdomains_from_file( ODIE_PLUGIN_DEMO_DIR . 'jetpack_vendor/i18n-map.php' );
	}
} else { // Something very unexpected. Error out gently with an admin_notice and exit loading.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			__( 'Error loading autoloader file for Jetpack Odie Plugin plugin', 'odie-plugin-demo' )
		);
	}

	add_action(
		'admin_notices',
		function () {
			?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php
				printf(
					wp_kses(
						/* translators: Placeholder is a link to a support document. */
						__( 'Your installation of Odie Plugin Demo is incomplete. If you installed Jetpack Odie Plugin from GitHub, please refer to <a href="%1$s" target="_blank" rel="noopener noreferrer">this document</a> to set up your development environment. Jetpack Odie Plugin must have Composer dependencies installed and built via the build command.', 'odie-plugin-demo' ),
						array(
							'a' => array(
								'href'   => array(),
								'target' => array(),
								'rel'    => array(),
							),
						)
					),
					'https://github.com/Automattic/jetpack/blob/trunk/docs/development-environment.md#building-your-project'
				);
				?>
			</p>
		</div>
			<?php
		}
	);

	return;
}

// Redirect to plugin page when the plugin is activated.
add_action( 'activated_plugin', 'odie_plugin_demo_activation' );

/**
 * Redirects to plugin page when the plugin is activated
 *
 * @param string $plugin Path to the plugin file relative to the plugins directory.
 */
function odie_plugin_demo_activation( $plugin ) {
	if (
		ODIE_PLUGIN_DEMO_ROOT_FILE_RELATIVE_PATH === $plugin &&
		\Automattic\Jetpack\Plugins_Installer::is_current_request_activating_plugin_from_plugins_screen( ODIE_PLUGIN_DEMO_ROOT_FILE_RELATIVE_PATH )
	) {
		wp_safe_redirect( esc_url( admin_url( 'admin.php?page=odie-plugin-demo' ) ) );
		exit;
	}
}

// Add "Settings" link to plugins page.
add_filter(
	'plugin_action_links_' . ODIE_PLUGIN_DEMO_FOLDER . '/odie-plugin-demo.php',
	function ( $actions ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=odie-plugin-demo' ) ) . '">' . __( 'Settings', 'odie-plugin-demo' ) . '</a>';
		array_unshift( $actions, $settings_link );

		return $actions;
	}
);

require_once( 'updater.php' );
if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
	$config = array(
		'slug' => ODIE_PLUGIN_DEMO_SLUG, // this is the slug of your plugin
		'proper_folder_name' => ODIE_PLUGIN_DEMO_NAME, // this is the name of the folder your plugin lives in
		'api_url' => 'https://api.github.com/repos/Automattic/odie-plugin-demo', // the GitHub API url of your GitHub repo
		'raw_url' => 'https://raw.github.com/Automattic/odie-plugin-demo/trunk', // the GitHub raw url of your GitHub repo
		'github_url' => 'https://github.com/Automattic/odie-plugin-demo', // the GitHub url of your GitHub repo
		'zip_url' => 'https://github.com/Automattic/odie-plugin-demo/zipball/trunk', // the zip url of the GitHub repo
		'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
		'requires' => '3.0', // which version of WordPress does your plugin require?
		'tested' => '6.3', // which version of WordPress is your plugin tested up to?
		'readme' => 'README.md', // which file to use as the readme for the version number
		'access_token' => '', // Access private repositories by authorizing under Plugins > GitHub Updates when this example plugin is installed
	);
	// TODO: Maybe get this to work. 
	// new WP_GitHub_Updater($config);
}

register_deactivation_hook( __FILE__, array( 'Odie_Plugin_Demo', 'plugin_deactivation' ) );

// Main plugin class.
new Odie_Plugin_Demo();
