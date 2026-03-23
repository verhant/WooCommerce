<?php
/**
 * Verhant DPP Uninstall
 *
 * Removes all plugin data when the plugin is uninstalled.
 *
 * @package Verhant_DPP
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove plugin options.
delete_option( 'verhant_api_token' );
delete_option( 'verhant_last_sync' );
delete_option( 'verhant_sync_count' );
delete_option( 'verhant_auto_publish_dpp' );
delete_option( 'verhant_auto_sync' );

// Remove post meta from all products.
delete_post_meta_by_key( '_verhant_dpp_url' );
delete_post_meta_by_key( '_verhant_dpp_uid' );