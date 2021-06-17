<?php 

function fca_lpc_madmimi_settings( $meta ) {
	
	$madmimi_email = empty ( $meta['madmimi_email'] ) ? '' : $meta['madmimi_email'];
	$madmimi_key = empty ( $meta['madmimi_key'] ) ? '' : $meta['madmimi_key'];
	$mailing_list = empty ( $meta['madmimi_list'] ) ? '' : $meta['madmimi_list'];
	
	$html = "<tr class='fca_lpc_madmimi_setting_row'>";

		$html .= "<th>" . __('Mad Mimi Account Email', 'landing-page-cat') . fca_lpc_spinner() . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'madmimi_email', '', $madmimi_email, 'email' );
		$html .= "</td>";

	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_madmimi_setting_row'>";

		$html .= "<th>" . __('API Key', 'landing-page-cat') . fca_lpc_tooltip( __('Enter your API key information from Mad Mimi.  Click the "Get my Mad Mimi API Key" link then click on "API" on the right-hand side and paste your API key here.', 'landing-page-cat') ) . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'madmimi_key', '', $madmimi_key, 'text' );
			$html .= fca_lpc_info_span( __('Get my Mad Mimi API Key', 'landing-page-cat'), 'https://madmimi.com/user/edit?set_api=&account_info_tabs=account_info_personal' );
		$html .= "</td>";

	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_madmimi_setting_row fca_lpc_madmimi_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'madmimi_list', $mailing_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
	
}


function fca_lpc_madmimi_subscribe( $optin_settings, $email, $name, $api_key, $username, $list_id ) {

	$email = urlencode( $email );
	
	$name = urlencode( $name );
	$args = array(
		'timeout'     => 15,
		'redirection' => 15,
		'headers'     => "Accept: application/json",
	);
		
	$url = "https://api.madmimi.com/audience_lists/$list_id/add?email=$email&username=$username&api_key=$api_key";
	
	if ( !empty ( $name ) ) {
		//add name to query
		$url .= "&first_name=$name";
	}
	
	$response = wp_remote_post( $url, $args);
	
	if( !is_wp_error( $response ) ) {
		if ( $response['response']['code'] == 200 ) {
			return true;
		}
	}
	
	return false;
}
	
//madmimi AJAX GET LISTS
function fca_lpc_get_madmimi_lists() {
	
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$api_key = empty ( $_REQUEST['api_key'] ) ? '' : esc_textarea( $_REQUEST['api_key'] );
	$email = empty ( $_REQUEST['email'] ) ? '' : esc_textarea( $_REQUEST['email'] );
	
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty ($api_key) ) {
				
		$lists_formatted = array();
		
		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
			'headers'     => "Accept: application/json",
		);
		$url = "https://api.madmimi.com/audience_lists/lists.xml?username=$email&api_key=$api_key";

		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			wp_send_json_error();
		}
		if ( !empty ( $response['body'] ) ) {
			$body = wp_remote_retrieve_body( $response );
			$xml  = simplexml_load_string( $body );
			//fuck xml
			$json = json_encode($xml);
			$array = json_decode($json, TRUE );
			
			if ( !empty ( $array['list'] )) {
				
				foreach ( $array['list'] as $list ) {
					$lists_formatted[] = array(
						'name' => $list['@attributes'][ 'name' ],
						'id' => $list['@attributes'][ 'id' ],					
					);
				}
				wp_send_json_success( $lists_formatted );
			}
			
		}

	}
	
	wp_send_json_error();

}
add_action( 'wp_ajax_fca_lpc_get_madmimi_lists', 'fca_lpc_get_madmimi_lists' );