<?php
/**
 * Verhant DPP Display
 *
 * Renders the DPP badge on WooCommerce product pages
 * and adds the DPP column to the admin products list.
 *
 * @package Verhant_DPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Verhant_DPP
 */
class Verhant_DPP {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'render_dpp_badge' ), 25 );
		add_action( 'woocommerce_product_meta_end', array( $this, 'render_dpp_link' ) );
		add_filter( 'manage_edit-product_columns', array( $this, 'add_product_columns' ) );
		add_action( 'manage_product_posts_custom_column', array( $this, 'render_product_column' ), 10, 2 );
	}

	/**
	 * Render the DPP badge after the product summary.
	 */
	public function render_dpp_badge(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		$dpp_url = get_post_meta( $product->get_id(), '_verhant_dpp_url', true );

		if ( empty( $dpp_url ) ) {
			return;
		}

		?>
		<div class="verhant-dpp-badge" style="margin: 20px 0; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; display: inline-flex; align-items: center; gap: 12px; background: #f9fafb;">
			<a href="<?php echo esc_url( $dpp_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: #1a1a1a;">
				<img src="<?php echo esc_url( VERHANT_DPP_PLUGIN_URL . 'assets/verhant-logo.svg' ); ?>" alt="<?php esc_attr_e( 'Digital Product Passport', 'verhant-dpp' ); ?>" width="28" height="28" />
				<span>
					<strong><?php esc_html_e( 'Digital Product Passport', 'verhant-dpp' ); ?></strong><br />
					<small style="color: #666;"><?php esc_html_e( 'ESPR compliant', 'verhant-dpp' ); ?></small>
				</span>
			</a>
		</div>
		<?php
	}

	/**
	 * Render a small DPP link in the product meta area.
	 */
	public function render_dpp_link(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		$dpp_url = get_post_meta( $product->get_id(), '_verhant_dpp_url', true );

		if ( empty( $dpp_url ) ) {
			return;
		}

		?>
		<span class="verhant-dpp-meta">
			<a href="<?php echo esc_url( $dpp_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'View Digital Product Passport', 'verhant-dpp' ); ?>
			</a>
		</span>
		<?php
	}

	/**
	 * Add the DPP column to the products list in WP Admin.
	 *
	 * @param array $columns Existing columns.
	 * @return array Modified columns.
	 */
	public function add_product_columns( array $columns ): array {
		$columns['verhant_dpp'] = __( 'DPP', 'verhant-dpp' );
		return $columns;
	}

	/**
	 * Render the DPP column content in the products list.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Product post ID.
	 */
	public function render_product_column( string $column, int $post_id ): void {
		if ( 'verhant_dpp' !== $column ) {
			return;
		}

		$dpp_url = get_post_meta( $post_id, '_verhant_dpp_url', true );
		$dpp_uid = get_post_meta( $post_id, '_verhant_dpp_uid', true );

		if ( ! empty( $dpp_url ) ) {
			printf(
				'<span style="color: #16a34a;" title="%s">&#10003; %s</span>',
				esc_attr( $dpp_url ),
				esc_html__( 'Generated', 'verhant-dpp' )
			);
		} elseif ( ! empty( $dpp_uid ) ) {
			printf(
				'<span style="color: #d97706;">&#9675; %s</span>',
				esc_html__( 'Pending', 'verhant-dpp' )
			);
		} else {
			printf(
				'<span style="color: #9ca3af;">&mdash; %s</span>',
				esc_html__( 'Not synced', 'verhant-dpp' )
			);
		}
	}
}
