<?php/** * Theme Activation *//******************************************** * AFTER ACTIVATION ********************************************//** * After Theme Activation * * Themes can request certain things to be done after activation: * *		add_theme_support( 'ctfw-after-activation', array( *			'flush_rewrite_rules'	=> true, *			'replace_notice'		=> sprintf( __( 'Please follow the <a href="%s">Next Steps</a> now that the theme has been activated.', 'your-theme-textdomain' ), 'http://churchthemes.com/guides/user/next-steps-after-activation/' ) *   	) ); * * This does not affect the Customizer preview. */add_action( 'load-themes.php', 'ctfw_after_activation' );	function ctfw_after_activation() {	global $wp_rewrite;	// Theme was just activated	if ( ! empty( $_GET['activated'] ) ) {		// Does theme support this?		if ( $support = get_theme_support( 'ctfw-after-activation' ) ) {			// What to do			$activation_tasks = isset( $support[0] ) ? $support[0] : array();			// Update .htaccess to make sure friendly URL's are in working order			if ( ! empty( $activation_tasks['flush_rewrite_rules'] ) ) {				flush_rewrite_rules();			}			// Show notice to user			if ( ! empty( $activation_tasks['notice'] ) ) {				add_action( 'admin_notices', 'ctfw_activation_notice', 5 ); // show above other notices				// Hide default notice				if ( ! empty( $activation_tasks['hide_default_notice'] ) ) {					add_action( 'admin_head', 'ctfw_hide_default_activation_notice' );				}				// Remove other notices when showing activation notice -- keep it simple				remove_action( 'admin_notices', 'ctfw_edd_license_notice', 7 ); // Theme License				remove_action( 'admin_notices', 'ctfw_ccm_plugin_notice' ); // Church Content Manager			}		}	}	}/******************************************** * NOTICES ********************************************//** * Message to show to user after activation * * Hooked in ctfw_after_activation(). */function ctfw_activation_notice() {	// Get notice if supported by theme	$support = get_theme_support( 'ctfw-after-activation' );	$notice = ! empty( $support[0]['notice'] ) ? $support[0]['notice'] : '';	// Show notice if have it	if ( $notice ) {		?>		<div id="ctfw-activation-notice" class="updated">			<p>				<?php echo $notice; ?>			</p>		</div>		<?php	}}/** * Hide default activation notice */function ctfw_hide_default_activation_notice() {	echo '<style>#message2{ display: none; }</style>';}