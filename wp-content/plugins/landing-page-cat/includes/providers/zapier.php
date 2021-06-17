<?php 

function fca_lpc_zapier_settings( $meta ) {
	
	$zapier_url = empty( $meta['zapier_url'] ) ? '' : $meta['zapier_url'];
	
	$html = "<tr class='fca_lpc_zapier_setting_row'>";

		$html .= "<th>" . __('Zapier Webhook URL', 'landing-page-cat') . fca_lpc_tooltip( __('Click the "Make a new Zap" link and paste your Zapier webhook URL here.', 'landing-page-cat') ) . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'zapier_url', '', $zapier_url, 'text' );
			$html .= fca_lpc_info_span( __('Make a new Zap', 'landing-page-cat'), 'https://zapier.com/app/editor' );
		$html .= "</td>";

	$html .= "</tr>";
	
	return $html;
	
}
	
function fca_lpc_zapier_subscribe( $zapier_url, $email, $name ) {
	// Headers
	$headers = array(
		'Accept: application/json',
		'Content-Type: application/json'
	);
	$body = array (
		'email_address' => $email,
		'time' => date('r'),
	);
	if ( !empty ( $name ) ) {
		$body['name'] = $name;
	}

	$response = wp_remote_post(
			$zapier_url, array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $headers,
				'body' => $body,
				'cookies' => array()
			)
	);
	
	if ( is_wp_error($response) ) {
		return false;
	} else {
		return true;
	}
	
}
	