<?php 
function fca_lpc_getresponse_settings( $meta ) {
	
	$getresponse_key = empty ( $meta['getresponse_key'] ) ? '' : $meta['getresponse_key'];
	$getresponse_list = empty ( $meta['getresponse_list'] ) ? '' : $meta['getresponse_list'];
	
	$html = "<tr class='fca_lpc_getresponse_setting_row'>";

		$html .= "<th>" . __('API Key', 'landing-page-cat') . fca_lpc_tooltip( __('Enter your API key information from GetResponse.  Click the "Get my GetResponse API Key" link and paste your API key here.', 'landing-page-cat') ) . fca_lpc_spinner() . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'getresponse_key', '', $getresponse_key, 'text' );
			$html .= fca_lpc_info_span( __('Get my GetResponse API Key', 'landing-page-cat'), 'https://app.getresponse.com/manage_api.html' );
		$html .= "</td>";

	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_getresponse_setting_row fca_lpc_getresponse_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'getresponse_list', $getresponse_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
}

	
	
function fca_lpc_getresponse_subscribe( $optin_settings, $api_key, $list, $email, $name ) {
	
	$body = array (
		'email' => $email,
		'dayOfCycle' => 0,
		'campaign' => array(
			'campaignId' => $list,
		),
	);
	
	if ( !empty ( $name ) ) {
		$body['name'] = $name;
	}
		
	$args = array(
		'method' => 'POST',
		'timeout'     => 15,
		'redirection' => 15,
		'headers' => array ( "Content-Type" => "application/json", "X-Auth-Token" => "api-key $api_key" ),
		'body' => json_encode ( $body ),
	);
	
	$url = "https://api.getresponse.com/v3/contacts";
	
	$response = wp_remote_request( $url, $args);
	
	if( is_wp_error( $response ) ) {
		return false;
	} else {
		if ( $response['response']['code'] == 202 ) {
			return true;
		} else {
			return false;
		}
	}	
}
	
//getresponse AJAX GET LISTS
function fca_lpc_get_getresponse_lists() {
	
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$api_key = empty ( $_REQUEST['api_key'] ) ? '' : esc_textarea( $_REQUEST['api_key'] );
	
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty ($api_key) ) {
		
		$lists_formatted = array();
		
		$args = array(
			'method' => 'GET',
			'timeout'     => 15,
			'redirection' => 15,
			'headers' => array ( "Content-Type" => "application/json", "X-Auth-Token" => "api-key $api_key" ),
		);
			
		$url = "https://api.getresponse.com/v3/campaigns";
		
		$response = wp_remote_request( $url, $args );
		
		if ( is_wp_error( $response ) ) {
			wp_send_json_error();
		}
		
		if ( !empty ( $response['body'] ) ) {
			$campaigns = json_decode( $response['body'], true);
			
			if ( empty ( $campaigns['message'] ) ) {
				
				foreach ( $campaigns as $list ) {
					$lists_formatted[] = array(
						'name' => $list[ 'name' ],
						'id' => $list[ 'campaignId' ],					
					);
				}
				wp_send_json_success( $lists_formatted );
			}
		}
		
	}
	wp_send_json_error();
	
}
add_action( 'wp_ajax_fca_lpc_get_getresponse_lists', 'fca_lpc_get_getresponse_lists' );


//ADD A NEW MERGE FIELD
function fca_lpc_add_getresponse_field( $api_key, $list_id, $name, $type = 'text' ) {
	
	$name = str_replace ( ' ', '_', $name );
	$name = strtolower  ( $name );
	$name = preg_replace('/[^a-zA-Z0-9_.]/', '', $name);
	
	$existing_fields = fca_lpc_get_getresponse_fields( $api_key );

	if ( array_key_exists( $name, $existing_fields ) ) {
		
		return $existing_fields[$name];
	}
	
	$body = array (
		'name' => $name,
		'type' => $type,
		'hidden' => 'false',
		'value' => array(),
	);
	
	$args = array(
		'method' => 'POST',
		'timeout'     => 15,
		'redirection' => 15,
		'headers' => array ( "Content-Type" => "application/json", "X-Auth-Token" => "api-key $api_key" ),
		'body' => json_encode ( $body ),
	);
	
	$url = "https://api.getresponse.com/v3/custom-fields";
	
	$response = wp_remote_request( $url, $args);
	if( is_wp_error( $response ) ) {
		return false;
	} else {
		if ( $response['response']['code'] == 201 ) {
			
			$body = json_decode ( $response['body'] );
			return $body->customFieldId;
			
		} else {
			return false;
		}
	}
}

function fca_lpc_get_getresponse_fields( $api_key ) {
	
	$merge_fields = array();
	
	$args = array(
		'method' => 'GET',
		'timeout'     => 15,
		'redirection' => 15,
		'headers' => array ( "Content-Type" => "application/json", "X-Auth-Token" => "api-key $api_key" ),
	);
		
	$url = "https://api.getresponse.com/v3/custom-fields";
	
	$response = wp_remote_request( $url, $args );
		
	if( is_wp_error( $response ) ) {
		return false;
	} else {
		if ( !empty ( $response['body'] ) ) {
			$body = json_decode( $response['body'], true);
			
			
			forEach ( $body as $field ) {
				$merge_fields[$field['name']] = $field['customFieldId'];
			}
			
		}
	}
	return $merge_fields;
}
