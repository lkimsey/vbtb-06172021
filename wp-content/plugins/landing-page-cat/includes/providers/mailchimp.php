<?php 

function fca_lpc_mailchimp_settings( $meta ) {
	
	$mailchimp_key = empty ( $meta['mailchimp_key'] ) ? '' : $meta['mailchimp_key'];
	$mailing_list = empty ( $meta['mailchimp_list'] ) ? '' : $meta['mailchimp_list'];
	$single_optin = empty ( $meta['mailchimp_single_optin'] ) ? '' : $meta['mailchimp_single_optin'];
	
	$html = "<tr class='fca_lpc_mailchimp_setting_row'>";

		$html .= "<th>" . __('API Key', 'landing-page-cat') . fca_lpc_tooltip( __('Enter your API key information from MailChimp.  Click the "Get my MailChimp API Key" link and paste your API key here.', 'landing-page-cat') ) . fca_lpc_spinner() . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'mailchimp_key', '', $mailchimp_key, 'text' );
			$html .= fca_lpc_info_span( __('Get my MailChimp API Key', 'landing-page-cat'), 'http://admin.mailchimp.com/account/api' );
		$html .= "</td>";

	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_mailchimp_setting_row fca_lpc_mailchimp_api_settings'>";
		$html .= "<th>" . __('Single Opt-In', 'landing-page-cat') . fca_lpc_tooltip( __('Adds user to mailing list with no confirmation email.', 'landing-page-cat') ) . "</th>";
		$html .= "<td>" . fca_lpc_input( 'mailchimp_single_optin', '', $single_optin, 'checkbox' ) . "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_mailchimp_setting_row fca_lpc_mailchimp_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'mailchimp_list', $mailing_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
	
}


function fca_lpc_mailchimp_subscribe( $optin_settings, $email, $name, $api_key, $list_id ) {

	$single_opt_in = empty ( $optin_settings['mailchimp_single_optin'] ) ? false : true;
	
	$status = $single_opt_in ? 'subscribed' : 'pending'; 
	$body = array (
		'email_address' => $email,
		'status' => $status,
	);
	
	if ( !empty ( $name ) ) {
		$body['merge_fields']['FNAME'] = $name;
	}

	// MAILCHIMP GROUPS STUFF GOES HERE	
	if ( !empty ( $optin_settings['mailchimp_groups'] ) ) {
		$body['interests'] = array();
		forEach ( $optin_settings['mailchimp_groups'] as $group_id ) {
			$body['interests'][$group_id] = true;
		}
	}
	
	$args = array(
		'method' => 'PUT',
		'timeout'     => 15,
		'redirection' => 15,
		'headers'     => "Authorization: apikey $api_key",
		'body' => json_encode ( $body ),
	);
	
	//get stuff after the dash as the delivery center, for URL
	$delivery_center =  explode("-", $api_key);
	$delivery_center = $delivery_center[1];		
	
	$member_id = md5(strtolower($email));
	
	$url = 'https://' . $delivery_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . $member_id;
	$response = wp_remote_request( $url, $args);
	
	if( is_wp_error( $response ) ) {
		echo 'WP Error';
	} else {
		if ( $response['response']['code'] == 200 ) {
			return true;
		} else {
			return false;
		}
	}	
}
	
//MAILCHIMP AJAX GET LISTS
function fca_lpc_get_mailchimp_lists() {
	
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$api_key = empty ( $_REQUEST['api_key'] ) ? '' : esc_textarea( $_REQUEST['api_key'] );
	
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty ($api_key) ) {
				
		$lists_formatted = array();
		
		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
			'headers'     => "Authorization: apikey $api_key",
		);
				
		//get stuff after the dash as the delivery center, for URL
		$delivery_center =  explode("-", $api_key);
		$delivery_center = $delivery_center[1];		
		
		$url = 'https://' . $delivery_center . '.api.mailchimp.com/3.0/lists?offset=0&count=999';
		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			wp_send_json_error();
		}
		if ( !empty ( $response['body'] ) ) {
			$body = json_decode( $response['body'], true);
			
			if ( !empty ( $body['lists'] )) {
				
				foreach ( $body['lists'] as $list ) {
					$lists_formatted[] = array(
						'name' => $list[ 'name' ],
						'id' => $list[ 'id' ],					
					);
				}
			}
		}

		wp_send_json_success( $lists_formatted );
		
	} else {
		wp_send_json_error();
	}
	
}
add_action( 'wp_ajax_fca_lpc_get_mailchimp_lists', 'fca_lpc_get_mailchimp_lists' );

//MAILCHIMP AJAX GET GROUPS

function fca_lpc_get_mailchimp_groups() {
	
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$api_key = empty( $_REQUEST['api_key'] ) ? '' : $_REQUEST['api_key'];
	$list_id = empty( $_REQUEST['list_id'] ) ? '' : $_REQUEST['list_id'];
 
	$groups_formatted = array();

	// Make call and add lists if any
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty ($api_key) && !empty ($list_id) ) {

		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
			'headers'     => "Authorization: apikey $api_key",
		);
		
		//get stuff after the dash as the delivery center, for URL
		$delivery_center =  explode("-", $api_key);
		$delivery_center = $delivery_center[1];		
		
		$url = 'https://' . $delivery_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/interest-categories?offset=0&count=999';
		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			wp_send_json_error();
		}
		if ( !empty ( $response['body'] ) ) {
			$body = json_decode( $response['body'], true);
			if ( !empty ( $body['categories'] )) {
				
				foreach ( $body['categories'] as $category ) {
					$url = 'https://' . $delivery_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/interest-categories/' . $category['id'] . '/interests?offset=0&count=999';
					$response = wp_remote_get( $url, $args );
					if ( ! is_wp_error( $response ) ) {
						$body = json_decode( $response['body'], true);
						
						foreach ( $body['interests'] as $interest ) {
							$groups_formatted[] = array(
								'name' => $interest['name'],
								'id' => $interest['id'],					
							);
						}
						
					}
				}
			}
		}
	}
	// Output response and exit
	wp_send_json_success( $groups_formatted );

}
add_action( 'wp_ajax_fca_lpc_get_mailchimp_groups', 'fca_lpc_get_mailchimp_groups' );

//ADD A NEW MERGE FIELD
function fca_lpc_add_mailchimp_field( $api_key, $list_id, $name, $type = 'text' ) {
	
	$existing_fields = fca_lpc_get_mailchimp_fields( $api_key, $list_id );
	
	if ( array_key_exists( $name, $existing_fields ) ) {
		return $existing_fields[$name];
	}
	
	$body = array (
		'name' => $name,
		'type' => $type,
	);
	
	$args = array(
		'method' => 'POST',
		'timeout'     => 15,
		'redirection' => 15,
		'headers'     => "Authorization: apikey $api_key",
		'body' => json_encode ( $body ),
	);
	
	//get stuff after the dash as the delivery center, for URL
	$delivery_center =  explode("-", $api_key);
	$delivery_center = $delivery_center[1];		
	
	$url = 'https://' . $delivery_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/merge-fields';

	$response = wp_remote_request( $url, $args);
	
	if( is_wp_error( $response ) ) {
		return false;
	} else {
		if ( $response['response']['code'] == 200 ) {
			if ( !empty ( $response['body']['tag'] ) ) {
				return $response['body']['tag'];
			}
		} else {
			return false;
		}
	}
}

function fca_lpc_get_mailchimp_fields( $api_key, $list_id ) {
	$merge_fields = array();
	
	$args = array(
		'method' => 'GET',
		'timeout'     => 15,
		'redirection' => 15,
		'headers'     => "Authorization: apikey $api_key"
	);
	
	//get stuff after the dash as the delivery center, for URL
	$delivery_center =  explode("-", $api_key);
	$delivery_center = $delivery_center[1];		
	
	$url = 'https://' . $delivery_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/merge-fields';
	
	$response = wp_remote_request( $url, $args);
	
	if( is_wp_error( $response ) ) {
		return false;
	} else {
		if ( !empty ( $response['body'] ) ) {
			$body = json_decode( $response['body'], true);
			if ( !empty ( $body['merge_fields'] )) {
				forEach ( $body['merge_fields'] as $field ) {
					$merge_fields[$field['name']] = $field['tag'];
				}
			}
		}
	}
	return $merge_fields;
}
