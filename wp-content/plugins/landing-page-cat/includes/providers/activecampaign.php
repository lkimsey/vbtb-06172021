<?php 

function fca_lpc_activecampaign_settings( $meta ) {

	$activecampaign_url = empty ( $meta['activecampaign_url'] ) ? '' : $meta['activecampaign_url'];
	$activecampaign_key = empty ( $meta['activecampaign_key'] ) ? '' : $meta['activecampaign_key'];
	
	$activecampaign_list = empty ( $meta['activecampaign_list'] ) ? '' : $meta['activecampaign_list'];
	
	$html = "<tr class='fca_lpc_activecampaign_setting_row'>";
		$html .= "<th>" . __('URL', 'landing-page-cat') . fca_lpc_tooltip( __("Log in to ActiveCampaign, then click your profile in the top right-hand side. Click My Settings, then Developer and paste your API Access URL here.", 'quiz-cat') ) . fca_lpc_spinner() . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'activecampaign_url', '', $activecampaign_url, 'text' );
		$html .= "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_activecampaign_setting_row'>";
		$html .= "<th>" . __('Key', 'landing-page-cat') . fca_lpc_tooltip( __("Log in to ActiveCampaign, then click your profile in the top right-hand side. Click My Settings, then Developer and paste your API Access Key here.", 'quiz-cat') ) . "</th>";
		$html .= "<td>";
			$html .= fca_lpc_input( 'activecampaign_key', '', $activecampaign_key, 'text' );
			$html .= fca_lpc_info_span( __('Log in to ActiveCampaign', 'landing-page-cat'), 'http://www.activecampaign.com/login/' );
		$html .= "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_activecampaign_setting_row fca_lpc_activecampaign_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'activecampaign_list', $activecampaign_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
	
}


function fca_lpc_activecampaign_subscribe( $api_key, $api_url, $list, $tags, $email, $name ) {
	
	$body = array (
		'email' => $email,
		"p[$list]" => $list,
	);
	
	if ( !empty ( $name ) ) {
		$body['first_name'] = $name;
	}
	
	if ( !empty ( $tags ) ) {
		$body['tags'] = html_entity_decode( $tags );
	}
	
	$args = array(
		'method' => 'POST',
		'timeout'     => 15,
		'redirection' => 15,
		'headers'     => "Content-Type: application/x-www-form-urlencoded",
		'body' => $body,
	);
	
	$url = $api_url . "/admin/api.php?api_action=contact_sync&api_output=json&api_key=$api_key";
	$response = wp_remote_request( $url, $args);

	if( is_wp_error( $response ) ) {
		return false;
	} else {
		if ( $response['response']['code'] == 200 ) {
			return true;
		} else {
			return false;
		}
	}	
}
	
//activecampaign AJAX GET LISTS
function fca_lpc_get_activecampaign_lists() {
	
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$api_key = empty ( $_REQUEST['api_key'] ) ? '' : esc_textarea( $_REQUEST['api_key'] );
	$api_url = empty ( $_REQUEST['api_url'] ) ? '' : esc_textarea( $_REQUEST['api_url'] );
	
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty ($api_key) ) {

		$lists_formatted = array();
		
		$args = array(
					'timeout'     => 15,
					'redirection' => 15,
					'headers'     => "Content-Type: application/x-www-form-urlencoded",
				);
		

		
		$url = $api_url . "/admin/api.php?api_action=list_list&api_output=json&ids=all&api_key=$api_key";
		$response = wp_remote_get( $url, $args );
			
		if ( is_wp_error( $response ) ) {
			wp_send_json_error(); 
		}
		if ( !empty ( $response['body'] ) ) {
			
			$lists = json_decode( $response['body'], true);
			
			if ( is_array ( $lists ) ) {
				unset ( $lists['result_code'] );
				unset ( $lists['result_message'] );
				unset ( $lists['result_output'] );

				foreach (  $lists as $list ) {
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
add_action( 'wp_ajax_fca_lpc_get_activecampaign_lists', 'fca_lpc_get_activecampaign_lists' );
