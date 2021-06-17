<?php

////////////////////////////
// API ENDPOINTS
////////////////////////////

//ENTRIES END POINT
function fca_lpc_add_optin_ajax() {
	
	$post_id = intval( $_REQUEST['post_id'] );
	$nonce = sanitize_text_field( $_REQUEST['nonce'] );
	$email = sanitize_email( $_REQUEST['email'] );
	$name = sanitize_text_field( $_REQUEST['name'] );		
	
	$nonceVerified = wp_verify_nonce( $nonce, 'fca_lpc_landing_page_nonce') == 1;
	$post_meta = get_post_meta( $post_id, 'fca_lpc', true );
	$provider = empty ( $post_meta['provider'] ) ? '' : $post_meta['provider'];

	if ( $nonceVerified && !empty( $email ) && !empty( $provider ) ) {
		
		switch ( $provider ) {
			case 'mailchimp':
				$list_id = empty ( $post_meta['mailchimp_list'] ) ? '' : $post_meta['mailchimp_list'];
				$api_key = empty ( $post_meta['mailchimp_key'] ) ? '' : $post_meta['mailchimp_key'];
				
				$success = fca_lpc_mailchimp_subscribe( $post_meta, $email, $name, $api_key, $list_id );
				break;
			
			case 'madmimi':
				$list_id = empty ( $post_meta['madmimi_list'] ) ? '' : $post_meta['madmimi_list'];
				$api_key = empty ( $post_meta['madmimi_key'] ) ? '' : $post_meta['madmimi_key'];
				$username = empty ( $post_meta['madmimi_email'] ) ? '' : $post_meta['madmimi_email'];
				
				$success = fca_lpc_madmimi_subscribe( $post_meta, $email, $name, $api_key, $username, $list_id );
				break;
				
			case 'convertkit':
				$list_id = empty ( $post_meta['convertkit_list'] ) ? '' : $post_meta['convertkit_list'];
				$api_key = empty ( $post_meta['convertkit_key'] ) ? '' : $post_meta['convertkit_key'];
				
				$success = fca_lpc_convertkit_subscribe( $post_meta, $email, $name, $api_key, $list_id );
				break;
				
			case 'campaignmonitor':
				$list_id = empty ( $post_meta['campaignmonitor_list'] ) ? '' : $post_meta['campaignmonitor_list'];
				$api_key = empty ( $post_meta['campaignmonitor_key'] ) ? '' : $post_meta['campaignmonitor_key'];
				$campaignmonitor_id = empty ( $post_meta['campaignmonitor_id'] ) ? '' : $post_meta['campaignmonitor_id'];
				
				$success = fca_lpc_campaignmonitor_subscribe( $post_meta, $email, $name, $api_key, $campaignmonitor_id, $list_id );
				break;
			
			case 'zapier':
				$zapier_url = empty ( $post_meta['zapier_url'] ) ? '' : $post_meta['zapier_url'];
				
				$success = fca_lpc_zapier_subscribe( $zapier_url, $email, $name );
				break;
				
			case 'getresponse':
				$getresponse_list = empty ( $post_meta['getresponse_list'] ) ? '' : $post_meta['getresponse_list'];
				$getresponse_key = empty ( $post_meta['getresponse_key'] ) ? '' : $post_meta['getresponse_key'];
				
				$success = fca_lpc_getresponse_subscribe( $post_meta, $getresponse_key, $getresponse_list, $email, $name );
				break;
				
			case 'aweber':
				$aweber_tags = empty ( $post_meta['aweber_tags'] ) ? '' : $post_meta['aweber_tags'];
				$aweber_list = empty ( $post_meta['aweber_list'] ) ? '' : $post_meta['aweber_list'];
				
				$success = fca_lpc_aweber_subscribe( $email, $name, $aweber_list, $aweber_tags );
				break;
			
			case 'activecampaign':
				$activecampaign_key = empty ( $post_meta['activecampaign_key'] ) ? '' : $post_meta['activecampaign_key'];
				$activecampaign_url = empty ( $post_meta['activecampaign_url'] ) ? '' : $post_meta['activecampaign_url'];
				$activecampaign_list = empty ( $post_meta['activecampaign_list'] ) ? '' : $post_meta['activecampaign_list'];
				$activecampaign_tags = empty ( $post_meta['activecampaign_tags'] ) ? '' : $post_meta['activecampaign_tags'];

				$success = fca_lpc_activecampaign_subscribe( $activecampaign_key, $activecampaign_url, $activecampaign_list, $activecampaign_tags, $email, $name );
				break;
				
			case 'drip':
				$drip_key = empty ( $post_meta['drip_key'] ) ? '' : $post_meta['drip_key'];
				$drip_id = empty ( $post_meta['drip_id'] ) ? '' : $post_meta['drip_id'];
				$drip_list = empty ( $post_meta['drip_list'] ) ? '' : $post_meta['drip_list'];
				$drip_tags = empty ( $post_meta['drip_tags'] ) ? '' : $post_meta['drip_tags'];

				$success = fca_lpc_drip_subscribe( $drip_key, $drip_id, $drip_list, $drip_tags, $email, $name );
				break;
				
			default:
				$success = true;
				break;
		}
		do_action( 'fca_lpc_after_submission', $post_id, $email, $name, $success );
		if ( $success ) {
			wp_send_json_success();
		}
	}
	wp_send_json_error();
}
add_action( 'wp_ajax_fca_lpc_add_optin', 'fca_lpc_add_optin_ajax' );
add_action( 'wp_ajax_nopriv_fca_lpc_add_optin', 'fca_lpc_add_optin_ajax' );


//UNINSTALL ENDPOINT
function fca_lpc_uninstall_ajax() {
	
	$msg = sanitize_text_field( $_REQUEST['msg'] );
	$nonce = sanitize_text_field( $_REQUEST['nonce'] );
	$nonceVerified = wp_verify_nonce( $nonce, 'fca_lpc_uninstall_nonce') == 1;

	if ( $nonceVerified && !empty( $msg ) ) {
		
		$url =  "https://api.fatcatapps.com/api/feedback.php";
				
		$body = array(
			'product' => 'landingpagecat',
			'msg' => $msg,		
		);
		
		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
			'body' => json_encode( $body ),	
			'blocking'    => true,
			'sslverify'   => false
		); 		
		
		$return = wp_remote_post( $url, $args );
		
		wp_send_json_success( $msg );

	}
	wp_send_json_error( $msg );

}
add_action( 'wp_ajax_fca_lpc_uninstall', 'fca_lpc_uninstall_ajax' );

//ADD AN EVENT FOR DRIP SUBSCRIBER FOR ACTIVATION/DEACTIVATION OF PLUGIN
function fca_lpc_api_action( $action = '' ) {
	$tracking = get_option( 'fca_lpc_activation_status' );
	if ( $tracking !== false ) {
		$user = wp_get_current_user();
		$url =  "https://api.fatcatapps.com/api/activity.php";
		
		$body = array(
			'user' => $user->user_email,
			'action' => $action,		
		);
		
		$args = array(
			'body' => json_encode( $body ),		
		);		
		
		$return = wp_remote_post( $url, $args );
		
		return true;
	}
	
	return false;
	
}
