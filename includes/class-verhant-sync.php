<?php
/**
 * Verhant Sync
 *
 * Handles import/export logic between WooCommerce and Verhant.
 *
 * @package Verhant_DPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Verhant_Sync
 */
class Verhant_Sync {

	/**
	 * Constructor. Registers auto-sync hook if enabled.
	 */
	public function __construct() {
		if ( get_option( 'verhant_auto_sync', false ) ) {
			add_action( 'woocommerce_update_product', array( $this, 'sync_single_product' ) );
			add_action( 'woocommerce_new_product', array( $this, 'sync_single_product' ) );
		}
	}

	/**
	 * Import all published WooCommerce products to Verhant.
	 *
	 * @return array|WP_Error Results with imported, updated, errors counts.
	 */
	public function run_import(): array|WP_Error {
		$token = get_option( 'verhant_api_token', '' );

		if ( empty( $token ) ) {
			return new WP_Error( 'verhant_no_token', __( 'API token is not configured.', 'verhant-dpp' ) );
		}

		$products = wc_get_products(
			array(
				'status' => 'publish',
				'limit'  => -1,
			)
		);

		if ( empty( $products ) ) {
			return new WP_Error( 'verhant_no_products', __( 'No published products found.', 'verhant-dpp' ) );
		}

		$formatted = array();

		foreach ( $products as $product ) {
			$formatted[] = $this->format_product( $product );
		}

		$api    = new Verhant_API( $token );
		$result = $api->bulk_import( $formatted );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		update_option( 'verhant_last_sync', current_time( 'mysql' ) );
		update_option( 'verhant_sync_count', count( $formatted ) );

		return $result;
	}

	/**
	 * Export DPP data from Verhant and save to WooCommerce products.
	 *
	 * @return array|WP_Error Results with updated, skipped counts.
	 */
	public function run_export(): array|WP_Error {
		$token = get_option( 'verhant_api_token', '' );

		if ( empty( $token ) ) {
			return new WP_Error( 'verhant_no_token', __( 'API token is not configured.', 'verhant-dpp' ) );
		}

		$api      = new Verhant_API( $token );
		$products = $api->get_products_status();

		if ( is_wp_error( $products ) ) {
			return $products;
		}

		$updated = 0;
		$skipped = 0;

		foreach ( $products as $product_data ) {
			if ( empty( $product_data['sku'] ) ) {
				++$skipped;
				continue;
			}

			if ( empty( $product_data['dpp_status'] ) || 'generated' !== $product_data['dpp_status'] ) {
				++$skipped;
				continue;
			}

			$product_id = wc_get_product_id_by_sku( $product_data['sku'] );

			if ( ! $product_id ) {
				++$skipped;
				continue;
			}

			if ( ! empty( $product_data['dpp_url'] ) ) {
				update_post_meta( $product_id, '_verhant_dpp_url', esc_url_raw( $product_data['dpp_url'] ) );
			}

			if ( ! empty( $product_data['dpp_uid'] ) ) {
				update_post_meta( $product_id, '_verhant_dpp_uid', sanitize_text_field( $product_data['dpp_uid'] ) );
			}

			++$updated;
		}

		return array(
			'updated' => $updated,
			'skipped' => $skipped,
		);
	}

	/**
	 * Sync a single product to Verhant.
	 *
	 * @param int $product_id WooCommerce product ID.
	 * @return bool True on success, false on failure.
	 */
	public function sync_single_product( int $product_id ): bool {
		$token = get_option( 'verhant_api_token', '' );

		if ( empty( $token ) ) {
			return false;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product || 'publish' !== $product->get_status() ) {
			return false;
		}

		$api    = new Verhant_API( $token );
		$result = $api->bulk_import( array( $this->format_product( $product ) ) );

		return ! is_wp_error( $result );
	}

	/**
	 * Format a WooCommerce product for the Verhant API.
	 *
	 * @param WC_Product $product WooCommerce product.
	 * @return array Formatted product data.
	 */
	private function format_product( WC_Product $product ): array {
		return array(
			'name'              => $product->get_name(),
			'brand'             => get_post_meta( $product->get_id(), '_brand', true ),
			'sku'               => $product->get_sku(),
			'barcode'           => get_post_meta( $product->get_id(), '_barcode', true ),
			'product_type'      => $this->map_product_type( $product ),
			'description'       => wp_strip_all_tags( $product->get_description() ),
			'country_of_origin' => get_post_meta( $product->get_id(), '_origin', true ),
			'materials'         => array(),
			'images'            => $this->get_product_images( $product ),
		);
	}

	/**
	 * Map WooCommerce product categories to Verhant verticals.
	 *
	 * @param WC_Product $product WooCommerce product.
	 * @return string Verhant product type.
	 */
	private function map_product_type( WC_Product $product ): string {
		$categories = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );

		if ( is_wp_error( $categories ) || empty( $categories ) ) {
			return 'textiles';
		}

		$cat_string = strtolower( implode( ' ', $categories ) );

		$map = array(
			'footwear'    => array( 'shoes', 'footwear', 'sneakers', 'boots', 'sandals' ),
			'furniture'   => array( 'furniture', 'home', 'sofa', 'chair', 'table' ),
			'electronics' => array( 'electronics', 'tech', 'gadget', 'device', 'computer' ),
			'batteries'   => array( 'batteries', 'battery' ),
			'tyres'       => array( 'tires', 'tyres', 'tire', 'tyre' ),
		);

		foreach ( $map as $type => $keywords ) {
			foreach ( $keywords as $keyword ) {
				if ( str_contains( $cat_string, $keyword ) ) {
					return $type;
				}
			}
		}

		return 'textiles';
	}

	/**
	 * Get product images formatted for the Verhant API.
	 *
	 * @param WC_Product $product WooCommerce product.
	 * @return array Array of image data with url and alt.
	 */
	private function get_product_images( WC_Product $product ): array {
		$images     = array();
		$image_id   = $product->get_image_id();
		$gallery_ids = $product->get_gallery_image_ids();

		if ( $image_id ) {
			$url = wp_get_attachment_url( $image_id );
			$alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			if ( $url ) {
				$images[] = array(
					'url' => $url,
					'alt' => $alt ? $alt : $product->get_name(),
				);
			}
		}

		foreach ( $gallery_ids as $gallery_id ) {
			$url = wp_get_attachment_url( $gallery_id );
			$alt = get_post_meta( $gallery_id, '_wp_attachment_image_alt', true );
			if ( $url ) {
				$images[] = array(
					'url' => $url,
					'alt' => $alt ? $alt : $product->get_name(),
				);
			}
		}

		return $images;
	}
}
