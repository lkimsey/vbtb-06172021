<?php 

function fca_lpc_convertkit_settings( $meta ) {
	
	$convertkit_key = empty ( $meta['convertkit_key'] ) ? '' : $meta['convertkit_key'];
	$mailing_list = empty ( $meta['convertkit_list'] ) ? '' : $meta['convertkit_list'];
	
	$html = "<tr class='fca_lpc_convertkit_setting_row'>";

		$html .= "<th>" . __('API Key', 'landing-page-cat') . fca_lpc_tooltip( __('Enter your API key information from ConvertKit.  Click the "Get my ConvertKit API Key" and paste your API key here.', 'landing-page-cat') ) . fca_lpc_spinner() .  "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'convertkit_key', '', $convertkit_key, 'text' );
			$html .= fca_lpc_info_span( __('Get my ConvertKit API Key', 'landing-page-cat'), 'https://app.convertkit.com/account/edit' );
		$html .= "</td>";

	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_convertkit_setting_row fca_lpc_convertkit_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'convertkit_list', $mailing_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
	
}

function fca_lpc_convertkit_subscribe( $optin_settings, $email, $name, $api_key, $list_id, $tags = false ) {

	$body = array (
		'api_key' => $api_key,
		'email' => $email,
	);
	
	if ( !empty ( $name ) ) {
		$body['first_name'] = $name;
	}
	if ( !empty ( $tags ) ) {
		$body['tags'] = $tags;
	}

	$args = array(
		'method' => 'POST',
		'timeout'     => 15,
		'redirection' => 15,
		'body' => $body,
	);
	$url = "https://api.convertkit.com/v3/courses/$list_id/subscribe";

	$response = wp_remote_request( $url, $args );

	if( !is_wp_error( $response ) ) {
		if ( $response['response']['code'] == 200 ) {
			return true;
		}
	}	
	
	return false;
}
	
//CONVERTKIT AJAX GET LISTS
function fca_lpc_get_convertkit_lists() {
	
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$api_key = empty ( $_REQUEST['api_key'] ) ? '' : esc_textarea( $_REQUEST['api_key'] );
	
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty ($api_key) ) {
				
		$lists_formatted = array();
		
		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
		);
		
		$url = "https://api.convertkit.com/v3/sequences?api_key=$api_key";
		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			wp_send_json_error();
		}
		if ( !empty ( $response['body'] ) ) {
			$body = json_decode( $response['body'], true);
			
			if ( !empty ( $body['courses'] )) {
				
				foreach ( $body['courses'] as $list ) {
					$lists_formatted[] = array(
						'name' => $list[ 'name' ],
						'id' => $list[ 'id' ],					
					);
				}
				
				wp_send_json_success( $lists_formatted );
			}
		}

	} else {
		wp_send_json_error();
	}
	
}
add_action( 'wp_ajax_fca_lpc_get_convertkit_lists', 'fca_lpc_get_convertkit_lists' );
