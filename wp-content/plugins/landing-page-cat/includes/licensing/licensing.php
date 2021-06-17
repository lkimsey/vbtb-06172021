<?php

/************************************
Licensing & Automatic Updates - implementation. Built on top of EDD-SL (https://easydigitaldownloads.com/extensions/software-licensing/) 
http://docs.easydigitaldownloads.com/article/383-automatic-upgrades-for-wordpress-plugins
 ************************************* */

define( 'FCA_LPC_PLUGIN_NAME', 'Landing Page Cat Premium: ' . FCA_LPC_PLUGIN_PACKAGE );
 
// load our custom updater
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include FCA_LPC_PLUGIN_DIR . '/includes/licensing/EDD_SL_Plugin_Updater.php';
}

function fca_lpc_license() {
	
	$license_key = get_option( 'fca_lpc_license_key' );
	
	$edd_updater = new EDD_SL_Plugin_Updater( 'https://fatcatapps.com/', FCA_LPC_PLUGIN_FILE, array(
			'version'	=> FCA_LPC_PLUGIN_VER,
			'license'	=> $license_key,
			'item_name' => FCA_LPC_PLUGIN_NAME,
			'author'	=> 'Fatcat Apps',
			'url'		=> home_url()
		)
	);
	
}
add_action( 'admin_init', 'fca_lpc_license' );

//register setting sub page   
function fca_lpc_license_menu() {
	add_submenu_page(
		'edit.php?post_type=landingpage',
		__('Settings', 'landing-page-cat'),
		__('Settings', 'landing-page-cat'),
		'manage_options',
		'fca_lpc_license_page',
		'fca_lpc_license_page'
	);
}
add_action( 'admin_menu', 'fca_lpc_license_menu' );

function fca_lpc_license_page() {
	
	wp_enqueue_style( 'fca_lpc_settings_stylesheet', FCA_LPC_PLUGINS_URL . '/includes/licensing/licensing.css', array(), FCA_LPC_PLUGIN_VER );
	wp_enqueue_script( 'fca_lpc_settings_js', FCA_LPC_PLUGINS_URL . '/includes/licensing/licensing.js', array('jquery'), FCA_LPC_PLUGIN_VER, true );

	$error_msg = fca_lpc_activate_license();
	$error_msg .= fca_lpc_deactivate_license();
	$error_msg .= fca_lpc_save_gdpr_settings();
	$license  = get_option( 'fca_lpc_license_key' );
	$status	  = get_option( 'fca_lpc_license_status', 'inactive' );
	
	$gdpr_checkbox = get_option( 'fca_lpc_gdpr_checkbox' );
	$gdpr_locale = get_option( 'fca_lpc_gdpr_locale' );
	$consent_headline = stripslashes_deep( get_option( 'fca_lpc_consent_headline', "In order to comply with privacy regulations in the European Union we'll need you to provide consent before confirming you to our email list:" ) );
	$consent_msg = stripslashes_deep( get_option( 'fca_lpc_consent_msg' ) );
		
	?>
	<div class="wrap">
		<form style='display:none' method="post" id='fca_lpc_settings_form'>
			<?php if ( $error_msg ) {
				echo	"<div class='notice error'>
							<p>$error_msg</p>
						</div>";
			} ?>
			<?php wp_nonce_field( 'fca_lpc_license_nonce', 'fca_lpc_license_nonce' ); ?>
			<?php if ( FCA_LPC_PLUGIN_PACKAGE !== 'Free' ) { ?>
			<h3>
				<?php _e('License', 'landing-page-cat'); ?>
				<?php if( $status == 'valid' ) { ?>
					<span style="color: #fff; background: #7ad03a; font-size: 13px; padding: 4px 6px 3px 6px; margin-left: 5px;"><?php _e('ACTIVE', 'landing-page-cat'); ?></span>
				<?php } elseif($status == 'expired' ) { ?>
					<span style="color: #fff; background: #dd3d36; font-size: 13px; padding: 4px 6px 3px 6px; margin-left: 5px;"><?php _e('EXPIRED', 'landing-page-cat'); ?></span>
				<?php } else { ?>
					<span style="color: #fff; background: #dd3d36; font-size: 13px; padding: 4px 6px 3px 6px; margin-left: 5px;"><?php _e('INACTIVE.', 'landing-page-cat'); ?></span>
				<?php } ?>
			</h3>
			<?php } //END LICENSE AREA FOR PREMIUM ?>
			<table class='form-table'>				
			<?php if ( FCA_LPC_PLUGIN_PACKAGE !== 'Free' ) { ?>
				<tr>
					<th>
						<?php _e('License Key', 'landing-page-cat'); ?>
					</th>
					<td>
						<input id="fca_lpc_license_key" name="fca_lpc_license_key" type="text" class="regular-text" value="<?php echo $license ?>" /><br/>
						<label class="description" for="fca_lpc_license_key"><?php _e('Enter your license key', 'landing-page-cat'); ?></label>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e('Activate License', 'landing-page-cat'); ?>
					</th>
					<td>
						<?php if( $status == 'valid' ) { ?>
							<input type="submit" class="button-secondary" name="fca_lpc_license_deactivate" value="<?php _e('Deactivate License', 'landing-page-cat'); ?>"/>
						<?php } else { ?>
							<input type="submit" class="button-secondary" name="fca_lpc_license_activate" value="<?php _e('Activate License', 'landing-page-cat'); ?>"/>
						<?php } ?>
					</td>
				</tr>
				<tr>
			<?php } //END LICENSE AREA FOR PREMIUM ?>
					<th colspan='2'>
						<p style='font-size: 1.3em'><?php _e('EU GDPR Compliance', 'landing-page-cat') ?></p>
						<p style='font-weight: normal;'><?php _e('If you are collecting data on people in the EU, the GDPR requires consent for any marketing activities, and will store that consent.
						Enabling this setting below will let your subscribers give explicit consent to receive marketing emails.', 'landing-page-cat' ); ?>
						<br>
						<?php _e('Note that turning this feature on is only part of making your business GDPR compliant. We recommend consulting with a lawyer.')?></p>
					</th>
				</tr>
				<tr>
					<th><?php _e( 'EU GDPR Checkbox', 'landing-page-cat') ?></th>
					<td>
						<?php echo fca_lpc_input( 'fca_lpc_gdpr_checkbox', '', $gdpr_checkbox, 'checkbox' ) ?>
						
						<?php _e( 'Enabling this will:', 'landing-page-cat') ?><br><br>
						1. <?php _e( 'Add a checkbox, which is unchecked by default, to all existing & new forms. To increase conversions, the checkbox will show after your subscriber submits his email.', 'landing-page-cat') ?><br>
						2. <?php _e( 'Log consent in the Subscribers table.', 'landing-page-cat') ?><br><br>
						<?php _e( 'Note: Your subscribers will only be sent onwards to your email provider, if they check the consent checkbox.', 'landing-page-cat') ?>
					</td>
				</tr>
				<tr class='gdpr-setting'>
					<th><?php _e( 'Consent Headline', 'landing-page-cat') ?></th>
					<td>
						<?php echo fca_lpc_input( 'fca_lpc_consent_headline', '', $consent_headline, 'simpleeditor' ) ?>
					</td>
				</tr>
				<tr class='gdpr-setting'>
					<th><?php _e( 'Consent Message', 'landing-page-cat') ?></th>
					<td>
						<?php echo fca_lpc_input( 'fca_lpc_consent_msg', 'I have read and agree to the terms and conditions', $consent_msg, 'simpleeditor', 'id="fca_lpc_consent_msg"' ) ?>
					</td>
				</tr>
				<tr class='gdpr-setting'>
					<th><?php _e( "Only show checkbox if subscriber's browser registers to the EU", 'landing-page-cat') ?></th>
					<td>
						<?php echo fca_lpc_input( 'fca_lpc_gdpr_locale', '', $gdpr_locale, 'checkbox' ) ?>
						<?php _e( "Will only show the consent checkbox if your subscriber's browser's location setting is set to the EU. Note, this can't 100% guarantee that all EU residents will be caught, so use with caution.", 'landing-page-cat' ) ?>
					</td>
				</tr>	
			</table>
			<button class='button button-primary' name='fca_lpc_save' type='submit'><?php _e( 'Save', 'landing-page-cat' ) ?></button>
		</form>
	</div>
	<?php
}

//SAVE GDPR Settings
function fca_lpc_save_gdpr_settings() {
	if( isset( $_POST['fca_lpc_save'] ) ) {
		if( !check_admin_referer( 'fca_lpc_license_nonce', 'fca_lpc_license_nonce' ) ) {
			return; // invalid nonce
		}
	
		$gdpr_checkbox = !empty( $_POST['fca_lpc']['fca_lpc_gdpr_checkbox'] ) ? true : false;
		update_option( 'fca_lpc_gdpr_checkbox', $gdpr_checkbox );
		
		$gdpr_locale = !empty( $_POST['fca_lpc']['fca_lpc_gdpr_locale'] ) ? true : false;
		update_option( 'fca_lpc_gdpr_locale', $gdpr_locale );
			
		$consent_headline = !empty( $_POST['fca_lpc']['fca_lpc_consent_headline'] ) ? fca_lpc_kses( $_POST['fca_lpc']['fca_lpc_consent_headline'] ) : false;
		update_option( 'fca_lpc_consent_headline', $consent_headline );
		
		$consent_msg = !empty( $_POST['fca_lpc']['fca_lpc_consent_msg'] ) ? fca_lpc_kses( $_POST['fca_lpc']['fca_lpc_consent_msg'] ) : false;
		update_option( 'fca_lpc_consent_msg', $consent_msg );
	}
}

/************************************
*	 Activating the license		   * 
************************************* */
function fca_lpc_activate_license() {
	if( isset( $_POST['fca_lpc_license_activate'] ) && isset( $_POST['fca_lpc_license_key'] )  ) {
		// run a quick security check 
		if( !check_admin_referer( 'fca_lpc_license_nonce', 'fca_lpc_license_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}
		
		$license = fca_lpc_sanitize_license( $_POST[ 'fca_lpc_license_key' ] );
		
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license'	=> $license,
			'item_name' => urlencode( FCA_LPC_PLUGIN_NAME ), // the name of our product in EDD
			'url'		=> home_url()
		);

		// Call the API.
		$response = wp_remote_post( 'https://fatcatapps.com/', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
		} else {

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
			
				switch( $license_data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$message = __( 'Your license key has been disabled.' );
						break;
					case 'missing' :
						$message = __( 'Invalid license.' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'Your license is not active for this URL.' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), FCA_LPC_PLUGIN_NAME );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.' );
						break;
					default :
						$message = __( 'An error occurred, please try again.' );
						break;
				}

			}
		
		}
				
		// $license_data->license will be either "valid" or "invalid"
		update_option( 'fca_lpc_license_status', $license_data->license );
		update_option( 'fca_lpc_license_key', $license );
		
		// Check if anything passed on a message constituting a failure
		if ( !empty( $message ) ) {
			return $message;
		}
	}
	
	return false;

}
	
/************************************
* Deactivating the license
************************************/

function fca_lpc_deactivate_license() {

	if( isset( $_POST['fca_lpc_license_deactivate'] ) ) {

		// run a quick security check 
		if( ! check_admin_referer( 'fca_lpc_license_nonce', 'fca_lpc_license_nonce' ) ) {
			return; // get out if we didn't click the Activate button
		}

		// retrieve the license from the database
		$license = get_option( 'fca_lpc_license_key' );

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license'	=> $license,
			'item_name' => FCA_LPC_PLUGIN_NAME, // the name of our product in EDD
			'url'		=> home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( 'https://fatcatapps.com/', array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
		
		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
		}
		

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if( $license_data->license == 'deactivated' || $license_data->license == 'failed' ) {
			delete_option( 'fca_lpc_license_status' );
			delete_option( 'fca_lpc_license_key' );
		}
		if ( !empty( $message ) ) {
			return $message;
		}
	}
	
	return false;
}

function fca_lpc_sanitize_license( $key ) {
	$old = get_option( 'fca_lpc_license_key' );
	if( $old && $old != $key ) {
		delete_option( 'fca_lpc_license_status' ); // new license has been entered, so must reactivate
	}
	return htmlentities( trim($key) );
}

//LICENSE CHECK
function fca_lpc_check_license() {
	
	$store_url = 'https://fatcatapps.com/';
	$item_name = FCA_LPC_PLUGIN_NAME;
	$license = get_option( 'fca_lpc_license_key' );
	$api_params = array(
		'edd_action' => 'check_license',
		'license' => $license,
		'item_name' => urlencode( $item_name ),
		'url' => home_url()
	);
	
	$response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
	
  	if ( is_wp_error( $response ) ) {
		return false;
  	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );
	
	update_option( 'fca_lpc_license_status', $license_data->license );
	
}

add_action('fca_lpc_license_check', 'fca_lpc_check_license');

if( !wp_next_scheduled( 'fca_lpc_license_check' ) ) {
	wp_schedule_event(time(), 'daily', 'fca_lpc_license_check');
}