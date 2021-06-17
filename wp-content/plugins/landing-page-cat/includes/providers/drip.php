<?php 

function fca_lpc_drip_settings( $meta ) {

	$drip_key = empty ( $meta['drip_key'] ) ? '' : $meta['drip_key'];
	$drip_id = empty ( $meta['drip_id'] ) ? '' : $meta['drip_id'];
	
	$mailing_list = empty ( $meta['drip_list'] ) ? '' : $meta['drip_list'];
	
	
	$html = "<tr class='fca_lpc_drip_setting_row'>";
		$html .= "<th>" . __('Account ID', 'landing-page-cat') . fca_lpc_spinner() . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'drip_id', '', $drip_id, 'text' );
			$html .= fca_lpc_info_span( __('The Drip account ID can be found under Settings → Site Setup.', 'landing-page-cat') );
		$html .= "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_drip_setting_row'>";
		$html .= "<th>" . __('API Token', 'landing-page-cat') . "</th>";
		$html .= "<td>";
			$html .= fca_lpc_input( 'drip_key', '', $drip_key, 'text' );
			$html .= fca_lpc_info_span( __('The Drip API Token can be found under Settings → My User Settings.', 'landing-page-cat') );
			$html .= '<br>' . fca_lpc_info_span( __('Click here to log in to Drip', 'landing-page-cat'), 'https://getdrip.com/signin' );
		$html .= "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_drip_setting_row fca_lpc_drip_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'drip_list', $mailing_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
	
}


function fca_lpc_drip_subscribe( $api_key, $client_id, $list, $tags, $email, $name ) {
	
	$body = array (
		'subscribers' => array (
			array( 'email' => $email ),
		)
	);
	
	if ( !empty ( $name ) ) {
		$body['subscribers'][0]['custom_fields'] = array ( 'name' => $name );
	}
	
	if ( !empty ( $tags ) ) {
		$tags = explode ( ',', $tags );
		
		$body['subscribers'][0]['tags'] = $tags;
		
	}
	
	$args = array(
		'method' => 'POST',
		'timeout'     => 15,
		'redirection' => 15,
		'headers' => "Content-Type: application/vnd.api+json",
		'body' => json_encode ( $body ),
	);
	
	$url = "https://$api_key:@api.getdrip.com/v2/$client_id/campaigns/$list/subscribers";
	
	$response = wp_remote_request( $url, $args);
	
	if( is_wp_error( $response ) ) {
		return false;
	} else {
		if ( $response['response']['code'] == 201 ) {
			return true;
		} else {
			return false;
		}
	}	
}
	
//DRIP AJAX GET LISTS
function fca_lpc_get_drip_lists() {
	
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$api_key = empty ( $_REQUEST['api_key'] ) ? '' : esc_textarea( $_REQUEST['api_key'] );
	$client_id = empty ( $_REQUEST['client_id'] ) ? '' : esc_textarea( $_REQUEST['client_id'] );
	
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty( $api_key ) && !empty( $client_id ) ) {
		
		$args = array(
			'method' => 'GET',
			'timeout'     => 15,
			'redirection' => 15,
		);
			
		$url = "https://$api_key:@api.getdrip.com/v2/$client_id/campaigns";
		
		$response = wp_remote_request( $url, $args );
				
		if ( is_wp_error( $response ) ) {
			wp_send_json_error();
		}
		
		if ( !empty ( $response['body'] ) ) {
			$body = json_decode( $response['body'], true);
			
			if ( !empty ( $body['campaigns'] )) {
				$lists_formatted = array();
				foreach ( $body['campaigns'] as $list ) {
					$lists_formatted[] = array(
						'name' => $list[ 'name' ],
						'id' => $list[ 'id' ],					
					);
				}
				wp_send_json_success( $lists_formatted );
			}
		}
	}
	wp_send_json_error();
	
}
add_action( 'wp_ajax_fca_lpc_get_drip_lists', 'fca_lpc_get_drip_lists' );
