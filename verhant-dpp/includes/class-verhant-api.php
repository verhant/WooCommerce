<?php
/**
 * Verhant API Client
 *
 * Handles all HTTP communication with api.verhant.com.
 *
 * @package Verhant_DPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Verhant_API
 */
class Verhant_API {

	/**
	 * API token.
	 *
	 * @var string
	 */
	private string $api_token;

	/**
	 * Base URL for the Verhant API.
	 *
	 * @var string
	 */
	private string $base_url = VERHANT_DPP_API_URL;

	/**
	 * Constructor.
	 *
	 * @param string $token API token.
	 */
	public function __construct( string $token ) {
		$this->api_token = $token;
	}

	/**
	 * Validate the API token.
	 *
	 * @return array|WP_Error Account info array with 'email' and 'plan', or WP_Error.
	 */
	public function validate_token(): array|WP_Error {
		return $this->request( 'GET', '/me' );
	}

	/**
	 * Bulk import products to Verhant.
	 *
	 * @param array $products Array of product data.
	 * @return array|WP_Error Response with imported, updated, errors counts, or WP_Error.
	 */
	public function bulk_import( array $products ): array|WP_Error {
		return $this->request( 'POST', '/products/bulk-import', array( 'products' => $products ) );
	}

	/**
	 * Get products status from Verhant.
	 *
	 * @return array|WP_Error Array of products with dpp_status, or WP_Error.
	 */
	public function get_products_status(): array|WP_Error {
		return $this->request( 'GET', '/products?status=all' );
	}

	/**
	 * Export products from Verhant.
	 *
	 * @return array|WP_Error Array of exported products, or WP_Error.
	 */
	public function export_products(): array|WP_Error {
		return $this->request( 'GET', '/products/export' );
	}

	/**
	 * Get the DPP link for a specific SKU.
	 *
	 * @param string $sku Product SKU.
	 * @return string|WP_Error DPP URL string, or WP_Error.
	 */
	public function get_dpp_link( string $sku ): string|WP_Error {
		$result = $this->request( 'GET', '/shopify/dpp-link?sku=' . rawurlencode( $sku ) );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! empty( $result['dpp_url'] ) ) {
			return $result['dpp_url'];
		}

		return new WP_Error( 'verhant_no_dpp', __( 'No DPP link found for this SKU.', 'verhant-dpp' ) );
	}

	/**
	 * Make an HTTP request to the Verhant API.
	 *
	 * @param string $method HTTP method (GET or POST).
	 * @param string $endpoint API endpoint path.
	 * @param array  $body     Request body for POST requests.
	 * @return array|WP_Error Decoded response body, or WP_Error.
	 */
	private function request( string $method, string $endpoint, array $body = array() ): array|WP_Error {
		$url = $this->base_url . $endpoint;

		$args = array(
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_token,
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
			),
		);

		if ( 'POST' === $method ) {
			$args['body'] = wp_json_encode( $body );
			$response     = wp_remote_post( $url, $args );
		} else {
			$response = wp_remote_get( $url, $args );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( $code < 200 || $code >= 300 ) {
			$message = isset( $data['message'] ) ? $data['message'] : __( 'Unknown API error.', 'verhant-dpp' );
			return new WP_Error(
				'verhant_api_error',
				/* translators: 1: HTTP status code, 2: error message */
				sprintf( __( 'Verhant API error (%1$d): %2$s', 'verhant-dpp' ), $code, $message ),
				array( 'status' => $code )
			);
		}

		if ( ! is_array( $data ) ) {
			return new WP_Error( 'verhant_invalid_response', __( 'Invalid response from Verhant API.', 'verhant-dpp' ) );
		}

		return $data;
	}
}
