<?php
/**
 * Theme license and automatic updates
 *
 * For use with remote install of Easy Digital Downloads Software Licensing extension.
 * Integration is based on Pippin Williamson's sample theme for the extension.
 *
 * Add support for this framework feature like this:
 *
 *		add_theme_support( 'ctfw-edd-license', array(
 *  		'store_url'					=> 'yourstore.com',					// URL of store running EDD with Software Licensing extension
 *			'updates'					=> true,							// default true; enable automatic updates
 *			'options_page'				=> true,							// default true; provide options page for license entry/activaton
 *			'options_page_message'		=> '',								// optional message to show on options page
 *			'activation_error_notice'	=> __( 'Your message', 'theme' ),	// optional notice to override default with when activation fails
 *			'inactive_notice'			=> __( 'Your message', 'theme' ),	// optional notice to override default with license is inactive
 *			'expired_notice'			=> __( 'Your message', 'theme' ),	// optional notice to override default with when license is expired
 *			'expiring_soon_notice'		=> __( 'Your message', 'theme' ),	// optional notice to override default with when license expires soon
 *			'expiring_soon_days'		=> 30,								// days before expiration to consider a license "expiring soon"
 *			'renewal_url'				=> '',								// optional URL for renewal links (ie. EDD checkout); {license_key} will be replaced with key
 *			'renewal_info_url'			=> '',								// optional URL for renewal information
 *    	) );
 *
 * This default configuration assumes download's name in EDD is same as theme name.
 * See ctfw_edd_license_config() below for other arguments and their defaults.
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013 - 2014, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * CONFIGURATION
 *******************************************/

/**
 * License feature configuration
 *
 * Return arguments specified for licensing feature.
 * If no argument passed, whole array is returned.
 *
 * @since 0.9
 * @param string $arg Optional argument to retrieve
 * @return mixed Whole config array or single argument
 */
function ctfw_edd_license_config( $arg = false ) {

	$config = array();

	// Get theme support
	$support = get_theme_support( 'ctfw-edd-license' );

	// Get arguments passed in via theme support
	if ( ! empty( $support[0] ) ) {
		$config = $support[0];
	}

	// Use defaults or values passed in via theme supprt
	$config = wp_parse_args( $config, array(
		'store_url'					=> '',						// URL of store running EDD with Software Licensing extension
		'version'					=> CTFW_THEME_VERSION,		// default is to auto-determine from theme
		'license'					=> ctfw_edd_license_key(),	// default is to use '{theme}_license_key' option
		'item_name'					=> CTFW_THEME_NAME,			// default is to use theme name; must match download name in EDD
		'author'					=> CTFW_THEME_AUTHOR,		// default is to auto-determine from theme
		'updates'					=> true,					// default true; enable automatic updates
		'options_page'				=> true,					// default true; provide options page for license entry/activaton
		'options_page_message'		=> '',						// optional message to show on options page
		'activation_error_notice'	=> __( '<strong>License key could not be activated.</strong>', 'onechurch' ),
		'inactive_notice'			=> __( '<strong>Theme License Inactive:</strong> <a href="%1$s">Activate Your Theme License</a> to enable updates for the <strong>%2$s</strong> theme.', 'onechurch' ),	// optional notice to override default with license is inactive
		'expired_notice'			=> __( '<strong>Theme License Expired:</strong> <a href="%1$s">Renew Your Theme License</a> to re-enable updates for the <strong>%2$s</strong> theme (expired on <strong>%3$s</strong>).', 'onechurch' ),	// optional notice to override default with when license is expired
		'expiring_soon_notice'		=> __( '<strong>Theme License Expiring Soon:</strong> <a href="%1$s">Renew Your Theme License</a> to continue receiving updates for the <strong>%2$s</strong> theme (expires on <strong>%3$s</strong>).', 'onechurch' ),	// optional notice to override default with when license expires soon
		'expiring_soon_days'		=> 30,						// days before expiration to consider a license "expiring soon"
		'renewal_url'				=> '',						// optional URL for renewal links (ie. EDD checkout); {license_key} will be replaced with key
		'renewal_info_url'			=> '',						// optional URL for renewal information
	) );

	// Get specific argument?
	if ( ! empty( $arg ) ) {

		// Is argument valid? Use value
		if ( isset( $config[$arg] ) ) {
			$config = $config[$arg];
		}

		// If invalid, return empty (not array)
		else {
			$config = '';
		}

	}

	// Return filtered
	return apply_filters( 'ctfw_edd_license_config', $config );

}

/*******************************************
 * AUTOMATIC UPDATES
 *******************************************/

/**
 * Theme updater
 *
 * @since 0.9
 */
function ctfw_edd_license_updater() {

	// Theme supports updates?
	if ( current_theme_supports( 'ctfw-edd-license' ) && ctfw_edd_license_config( 'updates' ) ) {

		// Include updater class
		locate_template( CTFW_CLASS_DIR . '/CTFW_EDD_SL_Theme_Updater.php', true );

		// Activate updates
		$edd_updater = new CTFW_EDD_SL_Theme_Updater( array(
			'remote_api_url' 	=> ctfw_edd_license_config( 'store_url' ), 		// Store URL running EDD with Software Licensing extension
			'version' 			=> ctfw_edd_license_config( 'version' ), 		// Current version of theme
			'license' 			=> ctfw_edd_license_key(), 						// The license key entered by user
			'item_name' 		=> ctfw_edd_license_config( 'item_name' ),		// The name of this theme
			'author'			=> ctfw_edd_license_config( 'author' )			// The author's name
		) );

	}

}

add_action( 'after_setup_theme', 'ctfw_edd_license_updater', 99 ); // after any use of add_theme_support() at 10

/*******************************************
 * OPTIONS DATA (LOCAL)
 *******************************************/

/**
 * License key option name
 *
 * Specific to the current theme.
 *
 * @since 0.9
 * @param string $append Append string to base option name
 * @return string Option name
 */
function ctfw_edd_license_key_option( $append = '' ) {

	$field = CTFW_THEME_SLUG . '_license_key';

	if ( $append ) {
		$field .= '_' . ltrim( $append, '_' );
	}

	return apply_filters( 'ctfw_edd_license_key_option', $field, $append );

}

/**
 * License key value
 *
 * @since 0.9
 * @param string $append Append string to base option name
 * @return string Option value
 */
function ctfw_edd_license_key( $append = '' ) {

	$option = trim( get_option( ctfw_edd_license_key_option( $append ) ) );

	return apply_filters( 'ctfw_edd_license_key', $option, $append );

}

/**
 * Get local license status
 *
 * Note if inactive, value is empty
 *
 * @since 1.3
 * @return string status active, expired or empty (inactive)
 */
function ctfw_edd_license_status() {

	$status = get_option( ctfw_edd_license_key_option( 'status' ) );

	return apply_filters( 'ctfw_edd_license_status', $status );

}

/**
 * License is locally active
 *
 * @since 0.9
 * @return bool True if active
 */
function ctfw_edd_license_active() {

	$active = false;

	if ( 'active' == ctfw_edd_license_status() ) {
		$active = true;
	}

	return apply_filters( 'ctfw_edd_license_active', $active );

}

/**
 * License is locally inactive
 *
 * @since 1.3
 * @return bool True if inactive
 */
function ctfw_edd_license_inactive() {

	$inactive = false;

	if ( ! ctfw_edd_license_status() ) {
		$inactive = true;
	}

	return apply_filters( 'ctfw_edd_license_inactive', $inactive );

}

/**
 * License is locally expired
 *
 * @since 1.3
 * @return bool True if expired
 */
function ctfw_edd_license_expired() {

	$expired = false;

	if ( 'expired' == ctfw_edd_license_status() ) {
		$expired = true;
	}

	return apply_filters( 'ctfw_edd_license_expired', $expired );

}

/**
 * License is expiring soon
 *
 * @since 1.3
 * @return bool True if expiring within X days
 */
function ctfw_edd_license_expiring_soon() {

	$expiring_soon = false;

	$expiration_data = ctfw_edd_license_expiration_data();

	if ( ! empty( $expiration_data['expiring_soon'] ) ) {
		$expiring_soon = true;
	}

	return apply_filters( 'ctfw_edd_license_expiring_soon', $expiring_soon );

}

/**
 * Set license expiration date locally
 *
 * Removes seconds so stored value is YYYY-MM-DD.
 *
 * @since 1.3
 * @param string $expiration Remove expiration date value
 * @return string Expiration YYYY-MM-DD
 */
function ctfw_edd_license_update_expiration( $expiration ) {

	// Only if have a value (old value better than no value)
	if ( ! empty( $expiration ) ) {

		// Remove seconds so stored value is YYYY-MM-DD
		list( $expiration ) = explode( ' ', $expiration );
		$expiration = trim( $expiration );

		// Not an invalid key?
		if ( $expiration != '1970-01-01' ) {

			// Update local value
			update_option( ctfw_edd_license_key_option( 'expiration' ), $expiration );

		}

	}

}

/**
 * Get license expiration date (local value)
 *
 * @since 1.3
 * @return string Expiration YYYY-MM-DD
 */
function ctfw_edd_license_expiration() {

	$expiration = get_option( ctfw_edd_license_key_option( 'expiration' ) );

	return apply_filters( 'ctfw_edd_license_expiration', $expiration );

}

/**
 * Show license expiration date (formatted)
 *
 * @since 1.3
 * @param string Text to show if no date found
 * @return string Expiration date formatted
 */
function ctfw_edd_license_expiration_formatted( $none_text = false ) {

	$expiration = ctfw_edd_license_expiration();

	$date = '';

	if ( $expiration ) {
		$date = date_i18n( get_option( 'date_format' ), strtotime( $expiration ) );
	} elseif ( ! empty( $none_text ) ) {
		$date = $none_text;
	}

	return apply_filters( 'ctfw_edd_license_expiration_formatted', $date );

}

/**
 * Get expiration data
 *
 * @since 1.3
 * @return array date in various formats and whether it is expiring soon or not
 */
function ctfw_edd_license_expiration_data() {

	$data = array();

	$data['expiration'] = get_option( ctfw_edd_license_key_option( 'expiration' ) );
	$data['expiration_date'] = ctfw_edd_license_expiration_formatted( _x( 'unknown date', 'license expiration', 'onechurch' ) );
	$data['expiration_ts'] = ! empty( $data['expiration'] ) ? strtotime( $data['expiration'] ) : '';
	$data['expiring_soon_days'] = ctfw_edd_license_config( 'expiring_soon_days' );
	$data['expiring_soon_ts'] = time() + ( DAY_IN_SECONDS * $data['expiring_soon_days'] );
	$data['expiring_soon'] = ( ! ctfw_edd_license_expired() && ! empty( $data['expiration_ts'] ) && $data['expiration_ts'] < $data['expiring_soon_ts'] ) ? true : false;

	return apply_filters( 'ctfw_edd_license_expiration_data', $data );

}

/*******************************************
 * OPTIONS PAGE
 *******************************************/

/**
 * Add menu item and page
 *
 * @since 0.9
 */
function ctfw_edd_license_menu() {

	// Theme supports license options page?
	if ( current_theme_supports( 'ctfw-edd-license' ) && ctfw_edd_license_config( 'options_page' ) ) {

		// Add menu item and page
		add_theme_page(
			_x( 'Theme License', 'page title', 'onechurch' ),
			_x( 'Theme License', 'menu title', 'onechurch' ),
			'manage_options',
			'theme-license',
			'ctfw_edd_license_page' // see below for output
		);

	}

}

add_action( 'admin_menu', 'ctfw_edd_license_menu' );

/**
 * Options page content
 *
 * @since 0.9
 */
function ctfw_edd_license_page() {

	$license 	= ctfw_edd_license_key();
	$status 	= ctfw_edd_license_key( 'status' ); // local status

	?>
	<div class="wrap">

		<h2><?php _ex( 'Theme License', 'page title', 'onechurch' ); ?></h2>

		<?php
		$message = ctfw_edd_license_config( 'options_page_message' );
		if ( $message ) :
		?>
		<p>
			<?php echo $message; ?>
		</p>
		<?php endif; ?>

		<form method="post" action="options.php">

			<?php settings_fields( 'ctfw_edd_license' ); ?>

			<?php wp_nonce_field( 'ctfw_edd_license_nonce', 'ctfw_edd_license_nonce' ); ?>

			<h3 class="title"><?php _ex( 'License Key', 'heading', 'onechurch' ); ?></h3>

			<table class="form-table">

				<tbody>

					<tr valign="top">

						<th scope="row" valign="top">
							<?php _e( 'License Key', 'onechurch' ); ?>
						</th>

						<td>
							<input id="<?php echo esc_attr( ctfw_edd_license_key_option() ); ?>" name="<?php echo esc_attr( ctfw_edd_license_key_option() ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
						</td>

					</tr>

				</tbody>

			</table>

			<?php submit_button( __( 'Save Key', 'onechurch' ) ); ?>

			<?php if ( $license ) : ?>

			<h3 class="title"><?php _e( 'License Status', 'onechurch' ); ?></h3>

			<table class="form-table">

				<tbody>

					<tr valign="top">

						<th scope="row" valign="top">
							<?php _e( 'License Status', 'onechurch' ); ?>
						</th>

						<td>

							<?php if ( ctfw_edd_license_active() ) : ?>

								<span class="ctfw-license-active"><?php _ex( 'Active', 'license key', 'onechurch' ); ?></span>

								<?php if ( ctfw_edd_license_expiring_soon() ) : ?>
									/ <span class="ctfw-license-expiring-soon"><?php _ex( 'Expiring Soon', 'license status', 'onechurch' ); ?></span>
								<?php endif; ?>

							<?php elseif ( ctfw_edd_license_expired() ) : ?>

								<span class="ctfw-license-expired"><?php _ex( 'Expired', 'license key', 'onechurch' ); ?></span>

							<?php else : ?>

								<span class="ctfw-license-inactive"><?php _ex( 'Inactive', 'license key', 'onechurch' ); ?></span>

							<?php endif; ?>

						</td>

					</tr>

					<?php if ( ctfw_edd_license_expiration() && ( ctfw_edd_license_active() || ctfw_edd_license_expired() ) ) : // show only if active or expired, not just if have the data ?>

						<tr valign="top">

							<th scope="row" valign="top">
								<?php _e( 'License Expiration', 'onechurch' ); ?>
							</th>

							<td>
								<?php echo esc_html( ctfw_edd_license_expiration_formatted() ); ?>
							</td>

						</tr>

					<?php endif; ?>

				</tbody>

			</table>

			<p class="submit">

				<?php if ( ! ctfw_edd_license_expired() ) : // only show renew button if expired ?>

					<?php if ( ctfw_edd_license_active() ) : ?>

						<input type="submit" class="button button-primary ctfw-license-button ctfw-license-deactivate-button" name="ctfw_edd_license_deactivate" value="<?php _e( 'Deactivate License', 'onechurch' ); ?>" />

					<?php else : ?>

						<input type="submit" class="button button-primary ctfw-license-button ctfw-license-activate-button" name="ctfw_edd_license_activate" value="<?php _e( 'Activate License', 'onechurch' ); ?>" />

					<?php endif; ?>

				<?php endif; ?>

				<?php if ( ctfw_edd_license_config( 'renewal_url' ) && ( ctfw_edd_license_active() || ctfw_edd_license_expired() ) ) : // only if URL provided ?>
					<input type="submit" id="ctfw-license-renew-button" class="button button<?php if ( ctfw_edd_license_expired() ) : ?>-primary<?php endif; ?> ctfw-license-button ctfw-license-renew-button" name="ctfw_edd_license_renew" value="<?php _e( 'Renew License', 'onechurch' ); ?>" />
				<?php endif; ?>

			</p>

			<?php endif; ?>


		</form>

	</div>
	<?php
}

/**
 * Register option
 *
 * Create setting in options table
 *
 * @since 0.9
 */
function ctfw_edd_license_register_option() {

	// If theme supports it
	if ( current_theme_supports( 'ctfw-edd-license' ) && ctfw_edd_license_config( 'options_page' ) ) {
		register_setting( 'ctfw_edd_license', ctfw_edd_license_key_option(), 'ctfw_edd_license_sanitize' );
	}

}

add_action( 'admin_init', 'ctfw_edd_license_register_option' );

/**
 * Sanitize license key
 *
 * Also unset local status and expiration if changing key.
 *
 * @since 0.9
 * @param string $new Key being saved
 * @return string Sanitized key
 */
function ctfw_edd_license_sanitize( $new ) {

	$old = ctfw_edd_license_key();

	// Unset local status as active and expiration date when changing key -- need to activate new key
	if ( $old && $old != $new ) {
		delete_option( ctfw_edd_license_key_option( 'status' ) );
		delete_option( ctfw_edd_license_key_option( 'expiration' ) );
	}

	$new = trim( $new );

	return $new;

}

/**
 * Auto-activate after saving license key
 *
 * @since 1.3
 * @param string $old_value
 * @param string $value
 */
/* This is unreliable, doesn't work on first save on a fresh install.
function ctfw_edd_license_activate_after_save( $old_value, $value ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-edd-license' ) ) {
		return;
	}

	// Different key was saved on Theme License page
	if ( $value && $old_value != $value && isset( $_POST['submit'] ) && 'Save Key' == $_POST['submit'] ) {

		// Try to activate license automatically upon saving
		ctfw_edd_license_activation( 'activate_license' );

	}

}

add_action( 'update_option_' . CTFW_THEME_SLUG . '_license_key', 'ctfw_edd_license_activate_after_save', 10, 2 );
*/

/**
 * Activate or deactivate license key
 *
 * @since 0.9
 * @param string $action Action when not executing via post
 */
function ctfw_edd_license_activation( $action = false ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-edd-license' ) ) {
		return;
	}

	// Activate or Deactivate button clicked
	// Or, action manually passed
	if ( $action || isset( $_POST['ctfw_edd_license_activate'] ) || isset( $_POST['ctfw_edd_license_deactivate'] ) ) {

		// Check post if action not passed
		if ( ! $action ) {

			// Security check
		 	if( ! check_admin_referer( 'ctfw_edd_license_nonce', 'ctfw_edd_license_nonce' ) ) {
				return;
			}

			// Activate or deactivate?
			$action = isset( $_POST['ctfw_edd_license_activate'] ) ? 'activate_license' : 'deactivate_license';

		}

		// Get license data
		$license_data = ctfw_edd_license_action( $action );

		// Call action via API
		if ( $license_data ) {

			// If activated remotely, set local status; or set local status if was already active remotely -- keep in sync
			if ( 'activate_license' == $action ) {

				// Success
				if ( 'valid' == $license_data->license || 'valid' == ctfw_edd_license_check() ) {
					update_option( ctfw_edd_license_key_option( 'status' ), 'active' );
				}

				// Failure - note error for next page load
				else {
					set_transient( 'ctfw_edd_license_activation_result', 'fail', 15 ); // will be deleted after shown or in 15 seconds
				}

			}

			// If deactivated remotely, set local status; or set local status if was already inactive remotely -- keep in sync
			elseif (
				'deactivate_license' == $action
				&& (
					'deactivated' == $license_data->license
					|| 'disabled' == $license_data->license // if disabled would return failed... (leaving this just in case)
					|| 'failed' == $license_data->license // likely means deactivastion failed because it's disabled
					|| 'inactive' == ctfw_edd_license_check()
				)
			) {
				delete_option( ctfw_edd_license_key_option( 'status' ) );
			}

			// Set current expiration locally
			// Local will be synced to remote daily in case changes
			if ( isset( $license_data->expires ) ) {
				ctfw_edd_license_update_expiration( $license_data->expires );
			}

		}

	}

}

add_action( 'admin_init', 'ctfw_edd_license_activation' );

/**
 * Show notice on activation failure
 *
 * @since 0.9
 */
function ctfw_edd_license_activation_failure_notice() {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-edd-license' ) ) {
		return;
	}

	// Only on Theme License page
	$screen = get_current_screen();
	if ( 'appearance_page_theme-license' != $screen->base ) {
		return;
	}

	// Have a result transient?
	if ( $activation_result = get_transient( 'ctfw_edd_license_activation_result' ) ) {

		// Failed
		if ( 'fail' == $activation_result && ctfw_edd_license_config( 'activation_error_notice' ) ) {

			?>
			<div id="ctfw-license-activation-error-notice" class="error">
				<p>
					<?php echo ctfw_edd_license_config( 'activation_error_notice' ); ?>
				</p>
			</div>
			<?php

		}

		// Delete transient
		delete_transient( 'ctfw_edd_license_activation_result' );

	}

}

add_action( 'admin_notices', 'ctfw_edd_license_activation_failure_notice' );

/*******************************************
 * LICENSE NOTICE
 *******************************************/

/**
 * Show inactive, expiring soon and expired license notices
 *
 * @since 0.9
 */
function ctfw_edd_license_notice() {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-edd-license' ) ) {
		return;
	}

	// User can edit theme options?
	// kKeeps notices from showing to non-admin users
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	// Show only on relevant pages as not to overwhelm the admin
	// Don't show on Theme License page -- redundant
	$screen = get_current_screen();
	if ( ! in_array( $screen->base, array( 'dashboard', 'themes', 'update-core' ) ) ) {
		return;
	}

	// Get expiration data
	$expiration_data = ctfw_edd_license_expiration_data();


	// Active But Expiring Soon
	// Show a reminder notice 30 days before expiration
	if ( ctfw_edd_license_active() && $expiration_data['expiring_soon'] ) {
		$class = 'error';
		$notice = 'expiring_soon_notice';
	}

	// Expired
	elseif ( ctfw_edd_license_expired() ) {
		$class = "error";
		$notice = 'expired_notice';
	}

	// Inactive
	elseif ( ! ctfw_edd_license_active() ) {
		$class = "error";
		$notice = 'inactive_notice';
	}

	// Show the notice
	if ( ! empty( $notice ) && ctfw_edd_license_config( $notice ) && ! empty( $class ) ) {

		?>

			<div id="ctfw-license-notice" class="<?php echo $class; ?>">

				<p>

					<?php

					printf(
						ctfw_edd_license_config( $notice ),
						admin_url( 'themes.php?page=theme-license' ),
						CTFW_THEME_NAME,
						$expiration_data['expiration_date'],
						ctfw_edd_license_renewal_url(),
						ctfw_edd_license_config( 'renewal_info_url' )
					);

					?>

				</p>

			</div>

		<?php

	}

}

add_action( 'admin_notices', 'ctfw_edd_license_notice', 7 ); // higher priority than functionality plugin notice

/*******************************************
 * LICENSE RENEWAL
 *******************************************/

/**
 * Construct license renewal URL
 *
 * Replace {license_key} with license key
 *
 * @since 1.3
 * @return string Renewal URl with license key replaced
 */
function ctfw_edd_license_renewal_url() {

	// Get raw renewal URL
	$renewal_url = ctfw_edd_license_config( 'renewal_url' );

	// Replace {license_key} with license key
	$renewal_url = str_replace( '{license_key}', ctfw_edd_license_key(), $renewal_url );

	// Return filtered
	return apply_filters( 'ctfw_edd_license_renewal_url', $renewal_url );

}

/**
 * Redirect to rewal URL when "Renew License" clicked on Theme License page
 *
 * @since 1.3
 */
function ctfw_edd_license_process_renew_button() {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-edd-license' ) ) {
		return;
	}

	// Renewal button on Theme License page clicked
	if ( isset( $_POST['ctfw_edd_license_renew'] ) ) {

		// Get renewal URL
		$renewal_url = ctfw_edd_license_renewal_url();

		// Renewal URL provided
		if ( ! empty( $renewal_url ) ) {

			// Redirect to renewal URL
			wp_redirect( $renewal_url );

			// Stop execution
			exit;

		}

	}

}

add_action( 'admin_init', 'ctfw_edd_license_process_renew_button' );

/*******************************************
 * EDD API
 *******************************************/

/**
 * Call API with specific action
 *
 * https://easydigitaldownloads.com/docs/software-licensing-api/
 * activate_license, deactivate_license or check_license
 *
 * @since 0.9
 * @param string $action EDD API action: activate_license, deactivate_license or check_license
 * @return object License data from remote server
 */
function ctfw_edd_license_action( $action ) {

	$license_data = array();

	// Theme stores local option?
	if ( ctfw_edd_license_config( 'options_page' ) ) {

		// Valid action?
		$actions = array( 'activate_license', 'deactivate_license', 'check_license' );
		if ( in_array( $action, $actions ) ) {

			// Get license
			$license = ctfw_edd_license_key();

			// Have license
			if ( $license ) {

				// Data to send in API request
				$api_params = array(
					'edd_action'	=> $action,
					'license' 		=> $license,
					'item_name'		=> urlencode( ctfw_edd_license_config( 'item_name' ) ), // name of download in EDD
					'url'			=> urlencode( home_url() ) // URL of this site activated for license
				);

				// Call the API
				$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, ctfw_edd_license_config( 'store_url' ) ) ), array( 'timeout' => 15, 'sslverify' => false ) );

				// Got a valid response?
				if ( ! is_wp_error( $response ) ) {

					// Decode the license data
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				}

			}

		}

	}

	return apply_filters( 'ctfw_edd_license_action', $license_data, $action );

}

/**
 * Get remote license data
 *
 * Get status, expiration, etc. from remote
 *
 * @since 1.3
 * @param string Optional key to get value for
 * @return array License data array or single value for key
 */
function ctfw_edd_license_check_data( $key = false ) {

	// Get remote license data
	$data = ctfw_edd_license_action( 'check_license' );

	// Convert data to array
	$data = (array) $data;

	// Get value for specific key?
	if ( isset( $data[$key] ) ) { // key is given

		// Value exists for key in object
		if ( ! empty( $data[$key] ) ) {
			$data = $data[$key];
		}

		// If key or value not found, return nothing
		// (instead of full license data from above)
		else {
			$data = '';
		}

	}

	return apply_filters( 'ctfw_edd_license_check_data', $data, $key );

}

/**
 * Check license key status
 *
 * Check if license is valid on remote end.
 *
 * @since 0.9
 * @return string Remote license status
 */
function ctfw_edd_license_check() {

	$status = ctfw_edd_license_check_data( 'license' );

	return apply_filters( 'ctfw_edd_license_check', $status );

}

/**
 * Sync remote/local status
 *
 * It's handy to run this periodically in case license has been remotely activated, renewed or deactivated.
 * An expired license could have been renewed or a site URL addded remorely.
 * The license could have been expired, refunded or the URL no longer matches (whole site move).
 *
 * This also updates the expiration date locally.
 *
 * Otherwise, they may think they are up to date when they are not.
 *
 * @since 0.9
 */
function ctfw_edd_license_sync() {

	// Theme stores local option?
	if ( ! ctfw_edd_license_config( 'options_page' ) ) {
		return;
	}

	// Get remote license data
	$license_data = ctfw_edd_license_check_data();

	// Continue only if got a response
	if ( ! empty( $license_data ) ) { // don't do anything if times out

		// Get remote status
		$status = isset( $license_data['license'] ) ? $license_data['license'] : false;

		// Active remotely
		// This will activate locally if had been inactive or expired locally
		if ( 'valid' == $status ) {

			// Activate locally
			update_option( ctfw_edd_license_key_option( 'status' ), 'active' );

		}

		// Inactive remotely
		elseif ( in_array( $status, array( 'inactive', 'site_inactive', 'disabled' ) ) ) { // status is not valid

			// Deactivate locally
			delete_option( ctfw_edd_license_key_option( 'status' ) );

		}

		// Expired remotely
		elseif ( 'expired' == $status ) {

			// Set status expired locally
			update_option( ctfw_edd_license_key_option( 'status' ), 'expired' );

		}

		// Update expiration data
		// This helps the user know when to renew
		if ( isset( $license_data['expires'] ) ) {
			ctfw_edd_license_update_expiration( $license_data['expires'] );
		}

	}

}

/**
 * Sync remote/local status automatically
 *
 * Check for remote status change periodically on relevant pages: Dashboard, Theme License, Themes, Updates
 * Check in real-time on Theme License page so if remote change was made, they see it immediately as if in account.
 *
 * Once daily is enough to keep notice on dashboard and updates up to date without hammering remote server.
 *
 * @since 0.9
 */
function ctfw_edd_license_auto_sync() {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-edd-license' ) ) {
		return;
	}

	// Admin only
	if ( ! is_admin() ) {
		return;
	}

	// Theme stores local option?
	if ( ! ctfw_edd_license_config( 'options_page' ) ) {
		return;
	}

	// Periodically in relevant areas or always on Theme License page
	$screen = get_current_screen();
	if ( in_array( $screen->base, array( 'dashboard', 'appearance_page_theme-license', 'themes', 'update-core' ) ) ) {

		// Has this been checked in last day or is it theme license page?
		if ( ! get_transient( 'ctfw_edd_license_auto_sync' ) || 'appearance_page_theme-license' == $screen->base ) {

			// Check remote status and sync both ways if necessary
			ctfw_edd_license_sync();

			// Set transient to prevent check until next day
			// Once per day is enough to keep notice on dashboard and updates pages without hammering remote server
			set_transient( 'ctfw_edd_license_auto_sync', true, DAY_IN_SECONDS );

		}

	}

}

add_action( 'current_screen', 'ctfw_edd_license_auto_sync' );
