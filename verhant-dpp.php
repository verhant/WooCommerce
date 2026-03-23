<?php
/**
 * Plugin Name:       Verhant — Digital Product Passport (ESPR)
 * Plugin URI:        https://verhant.com/integrations/woocommerce
 * Description:       Generate ESPR-compliant Digital Product Passports for your WooCommerce products and publish DPP links automatically.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Tested up to:      6.7
 * Author:            Verhant
 * Author URI:        https://verhant.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       verhant-dpp
 * Domain Path:       /languages
 *
 * WC requires at least: 7.0
 * WC tested up to:      9.5
 *
 * @package Verhant_DPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VERHANT_DPP_VERSION', '1.0.0' );
define( 'VERHANT_DPP_API_URL', 'https://api.verhant.com/v1' );
define( 'VERHANT_DPP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VERHANT_DPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Check if WooCommerce is active.
 *
 * @return bool
 */
function verhant_dpp_woocommerce_is_active(): bool {
	return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true );
}

/**
 * Show admin notice if WooCommerce is not active.
 */
function verhant_dpp_woocommerce_missing_notice(): void {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: WooCommerce plugin name */
				esc_html__( '%s requires WooCommerce to be installed and activated.', 'verhant-dpp' ),
				'<strong>Verhant DPP</strong>'
			);
			?>
		</p>
	</div>
	<?php
}

if ( ! verhant_dpp_woocommerce_is_active() ) {
	add_action( 'admin_notices', 'verhant_dpp_woocommerce_missing_notice' );
	return;
}

// Include plugin classes.
require_once VERHANT_DPP_PLUGIN_DIR . 'includes/class-verhant-api.php';
require_once VERHANT_DPP_PLUGIN_DIR . 'includes/class-verhant-admin.php';
require_once VERHANT_DPP_PLUGIN_DIR . 'includes/class-verhant-sync.php';
require_once VERHANT_DPP_PLUGIN_DIR . 'includes/class-verhant-dpp.php';

/**
 * Plugin activation.
 */
function verhant_dpp_activate(): void {
	add_option( 'verhant_api_token', '' );
	add_option( 'verhant_last_sync', '' );
	add_option( 'verhant_sync_count', 0 );
	add_option( 'verhant_auto_publish_dpp', false );
	add_option( 'verhant_auto_sync', false );
}

/**
 * Plugin deactivation.
 */
function verhant_dpp_deactivate(): void {
	// Nothing to clean up on deactivation; cleanup happens in uninstall.php.
}

register_activation_hook( __FILE__, 'verhant_dpp_activate' );
register_deactivation_hook( __FILE__, 'verhant_dpp_deactivate' );

/**
 * Load plugin text domain.
 */
function verhant_dpp_load_textdomain(): void {
	load_plugin_textdomain( 'verhant-dpp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'verhant_dpp_load_textdomain' );

/**
 * Initialize plugin classes.
 */
function verhant_dpp_init(): void {
	new Verhant_Admin();
	new Verhant_DPP();
}
add_action( 'plugins_loaded', 'verhant_dpp_init' );