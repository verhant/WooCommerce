<?php
/**
 * Verhant Admin
 *
 * Handles admin pages, settings, and AJAX endpoints.
 *
 * @package Verhant_DPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Verhant_Admin
 */
class Verhant_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'show_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
		add_action( 'wp_ajax_verhant_validate_token', array( $this, 'handle_ajax_validate' ) );
		add_action( 'wp_ajax_verhant_sync_import', array( $this, 'handle_ajax_import' ) );
		add_action( 'wp_ajax_verhant_sync_export', array( $this, 'handle_ajax_export' ) );
		add_action( 'wp_ajax_verhant_save_auto_sync', array( $this, 'handle_ajax_auto_sync' ) );
	}

	/**
	 * Add submenu pages under WooCommerce.
	 */
	public function add_menu_pages(): void {
		add_submenu_page(
			'woocommerce',
			__( 'Verhant DPP — Settings', 'verhant-dpp' ),
			__( 'Verhant DPP', 'verhant-dpp' ),
			'manage_woocommerce',
			'verhant-dpp-settings',
			array( $this, 'render_settings_page' )
		);

		add_submenu_page(
			'woocommerce',
			__( 'Verhant DPP — Sync', 'verhant-dpp' ),
			__( 'Verhant Sync', 'verhant-dpp' ),
			'manage_woocommerce',
			'verhant-dpp-sync',
			array( $this, 'render_sync_page' )
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'verhant-dpp' ) );
		}
		include VERHANT_DPP_PLUGIN_DIR . 'admin/views/settings-page.php';
	}

	/**
	 * Render the sync page.
	 */
	public function render_sync_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'verhant-dpp' ) );
		}
		include VERHANT_DPP_PLUGIN_DIR . 'admin/views/sync-page.php';
	}

	/**
	 * Save settings from POST form.
	 */
	public function save_settings(): void {
		if ( ! isset( $_POST['verhant_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['verhant_nonce'] ) ), 'verhant_settings' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( isset( $_POST['verhant_api_token'] ) ) {
			$token = sanitize_text_field( wp_unslash( $_POST['verhant_api_token'] ) );
			update_option( 'verhant_api_token', $token );
		}

		if ( isset( $_POST['verhant_auto_publish_dpp'] ) ) {
			update_option( 'verhant_auto_publish_dpp', true );
		} else {
			update_option( 'verhant_auto_publish_dpp', false );
		}

		add_settings_error( 'verhant_messages', 'verhant_saved', __( 'Settings saved.', 'verhant-dpp' ), 'updated' );
	}

	/**
	 * Enqueue admin scripts and styles only on plugin pages.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_scripts( string $hook ): void {
		if ( ! str_contains( $hook, 'verhant-dpp' ) ) {
			return;
		}

		wp_enqueue_style(
			'verhant-admin-css',
			VERHANT_DPP_PLUGIN_URL . 'admin/assets/verhant-admin.css',
			array(),
			VERHANT_DPP_VERSION
		);

		wp_enqueue_script(
			'verhant-admin-js',
			VERHANT_DPP_PLUGIN_URL . 'admin/assets/verhant-admin.js',
			array(),
			VERHANT_DPP_VERSION,
			true
		);

		wp_localize_script(
			'verhant-admin-js',
			'verhantDpp',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'verhant_ajax' ),
				'i18n'     => array(
					'validating'  => __( 'Validating...', 'verhant-dpp' ),
					'importing'   => __( 'Importing products...', 'verhant-dpp' ),
					'exporting'   => __( 'Exporting DPP data...', 'verhant-dpp' ),
					'success'     => __( 'Success!', 'verhant-dpp' ),
					'error'       => __( 'An error occurred.', 'verhant-dpp' ),
					'connected_as' => __( 'Connected as', 'verhant-dpp' ),
					'plan'        => __( 'Plan', 'verhant-dpp' ),
					'imported'    => __( 'imported', 'verhant-dpp' ),
					'updated'     => __( 'updated', 'verhant-dpp' ),
					'errors'      => __( 'errors', 'verhant-dpp' ),
					'products_updated' => __( 'products updated with DPP links', 'verhant-dpp' ),
					'skipped'     => __( 'skipped', 'verhant-dpp' ),
				),
			)
		);
	}

	/**
	 * Show admin notice if API token is not configured.
	 */
	public function show_admin_notice(): void {
		$token = get_option( 'verhant_api_token', '' );

		if ( ! empty( $token ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen && str_contains( $screen->id, 'verhant-dpp' ) ) {
			return;
		}

		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: settings page URL */
					wp_kses(
						__( 'Verhant DPP: Please <a href="%s">configure your API token</a> to start generating Digital Product Passports.', 'verhant-dpp' ),
						array( 'a' => array( 'href' => array() ) )
					),
					esc_url( admin_url( 'admin.php?page=verhant-dpp-settings' ) )
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * AJAX handler: validate API token.
	 */
	public function handle_ajax_validate(): void {
		check_ajax_referer( 'verhant_ajax', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'verhant-dpp' ) ) );
		}

		$token = get_option( 'verhant_api_token', '' );

		if ( empty( $token ) ) {
			wp_send_json_error( array( 'message' => __( 'API token is not configured.', 'verhant-dpp' ) ) );
		}

		$api    = new Verhant_API( $token );
		$result = $api->validate_token();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( $result );
	}

	/**
	 * AJAX handler: import products to Verhant.
	 */
	public function handle_ajax_import(): void {
		check_ajax_referer( 'verhant_ajax', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'verhant-dpp' ) ) );
		}

		$sync   = new Verhant_Sync();
		$result = $sync->run_import();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( $result );
	}

	/**
	 * AJAX handler: export DPP data from Verhant.
	 */
	public function handle_ajax_export(): void {
		check_ajax_referer( 'verhant_ajax', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'verhant-dpp' ) ) );
		}

		$sync   = new Verhant_Sync();
		$result = $sync->run_export();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( $result );
	}

	/**
	 * AJAX handler: save auto-sync setting.
	 */
	public function handle_ajax_auto_sync(): void {
		check_ajax_referer( 'verhant_ajax', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'verhant-dpp' ) ) );
		}

		$enabled = isset( $_POST['enabled'] ) && 'true' === sanitize_text_field( wp_unslash( $_POST['enabled'] ) );
		update_option( 'verhant_auto_sync', $enabled );

		wp_send_json_success( array( 'enabled' => $enabled ) );
	}
}
