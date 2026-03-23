<?php
/**
 * Settings page view.
 *
 * @package Verhant_DPP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$api_token         = get_option( 'verhant_api_token', '' );
$last_sync         = get_option( 'verhant_last_sync', '' );
$sync_count        = get_option( 'verhant_sync_count', 0 );
$auto_publish      = get_option( 'verhant_auto_publish_dpp', false );

settings_errors( 'verhant_messages' );
?>
<div class="wrap verhant-admin-wrap">
	<h1>
		<img src="<?php echo esc_url( VERHANT_DPP_PLUGIN_URL . 'assets/verhant-logo.svg' ); ?>" alt="Verhant" width="28" height="28" style="vertical-align: middle; margin-right: 8px;" />
		<?php esc_html_e( 'Verhant DPP — Settings', 'verhant-dpp' ); ?>
	</h1>

	<div class="verhant-card">
		<h2><?php esc_html_e( 'API Connection', 'verhant-dpp' ); ?></h2>

		<form method="post" action="">
			<?php wp_nonce_field( 'verhant_settings', 'verhant_nonce' ); ?>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="verhant_api_token"><?php esc_html_e( 'API Token', 'verhant-dpp' ); ?></label>
					</th>
					<td>
						<input type="password" id="verhant_api_token" name="verhant_api_token" value="<?php echo esc_attr( $api_token ); ?>" class="regular-text" autocomplete="off" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: Verhant settings URL */
								wp_kses(
									__( 'Find your API token at <a href="%s" target="_blank" rel="noopener noreferrer">verhant.com/settings/api</a>.', 'verhant-dpp' ),
									array(
										'a' => array(
											'href'   => array(),
											'target' => array(),
											'rel'    => array(),
										),
									)
								),
								'https://verhant.com/settings/api'
							);
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Auto-publish DPP', 'verhant-dpp' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="verhant_auto_publish_dpp" value="1" <?php checked( $auto_publish ); ?> />
							<?php esc_html_e( 'Automatically display DPP badges on product pages when available.', 'verhant-dpp' ); ?>
						</label>
					</td>
				</tr>
			</table>

			<p class="submit">
				<?php submit_button( __( 'Save Settings', 'verhant-dpp' ), 'primary', 'submit', false ); ?>
				<button type="button" id="verhant-validate-token" class="button button-secondary" style="margin-left: 10px;">
					<?php esc_html_e( 'Verify Connection', 'verhant-dpp' ); ?>
				</button>
			</p>
		</form>

		<div id="verhant-validation-result" style="display: none;"></div>
	</div>

	<div class="verhant-card">
		<h2><?php esc_html_e( 'Status', 'verhant-dpp' ); ?></h2>
		<table class="widefat striped">
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'Last synchronization', 'verhant-dpp' ); ?></strong></td>
					<td>
						<?php
						if ( ! empty( $last_sync ) ) {
							echo esc_html( $last_sync );
						} else {
							esc_html_e( 'Never', 'verhant-dpp' );
						}
						?>
					</td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Products synced', 'verhant-dpp' ); ?></strong></td>
					<td><?php echo esc_html( (string) $sync_count ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="verhant-card">
		<p>
			<a href="https://verhant.com" target="_blank" rel="noopener noreferrer" class="button">
				<?php esc_html_e( 'Open Verhant Dashboard', 'verhant-dpp' ); ?> &rarr;
			</a>
		</p>
	</div>
</div>
