<?php 

function fca_lpc_campaignmonitor_settings( $meta ) {

	$campaignmonitor_key = empty ( $meta['campaignmonitor_key'] ) ? '' : $meta['campaignmonitor_key'];
	$campaignmonitor_id = empty ( $meta['campaignmonitor_id'] ) ? '' : $meta['campaignmonitor_id'];
	$campaignmonitor_list = empty ( $meta['campaignmonitor_list'] ) ? '' : $meta['campaignmonitor_list'];
	
	$html = "<tr class='fca_lpc_campaignmonitor_setting_row'>";
		$html .= "<th>" . __('Client ID', 'landing-page-cat') . fca_lpc_spinner() . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'campaignmonitor_id', '', $campaignmonitor_id, 'text' );
			$html .= fca_lpc_info_span( __('Where can I find my Campaign Monitor Client ID?', 'landing-page-cat'), 'https://www.campaignmonitor.com/api/getting-started/#your-client-id' );
		$html .= "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_campaignmonitor_setting_row'>";
		$html .= "<th>" . __('API Key', 'landing-page-cat') . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'campaignmonitor_key', '', $campaignmonitor_key, 'text' );
			$html .= fca_lpc_info_span( __('Where can I find my Campaign Monitor API Key?', 'landing-page-cat'), 'http://help.campaignmonitor.com/topic.aspx?t=206' );
		$html .= "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_campaignmonitor_setting_row fca_lpc_campaignmonitor_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'campaignmonitor_list', $campaignmonitor_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
	
}

function fca_lpc_campaignmonitor_load() {
	$base_class = 'CS_REST_Wrapper_Base';

	$classes = array(
		'CS_REST_Administrators' => 'csrest_administrators',
		'CS_REST_Campaigns'      => 'csrest_campaigns',
		'CS_REST_Clients'        => 'csrest_clients',
		'CS_REST_General'        => 'csrest_general',
		'CS_REST_Lists'          => 'csrest_lists',
		'CS_REST_People'         => 'csrest_people',
		'CS_REST_Segments'       => 'csrest_segments',
		'CS_REST_Subscribers'    => 'csrest_subscribers',
		'CS_REST_Templates'      => 'csrest_templates'
	);

	if ( class_exists( $base_class ) ) {
		$base_class = new ReflectionClass( $base_class );
		$base_dir   = realpath( dirname( $base_class->getFileName() ) . '/..' );

		foreach ( array( 'CS_REST_General', 'CS_REST_Clients', 'CS_REST_Subscribers' ) as $class ) {
			if ( ! class_exists( $class ) ) {
				require_once $base_dir . '/' . $classes[ $class ] . '.php';
			}
		}
	} else {
		foreach ( $classes as $class => $file ) {
			if ( ! class_exists( $class ) ) {
				require_once FCA_LPC_PLUGIN_DIR . '/includes/providers/campaignmonitor_api/' . $file . '.php';
			}
		}
	}
}


function fca_lpc_campaignmonitor_subscribe( $post_meta, $email, $name, $api_key, $campaignmonitor_id, $list_id ) {

	fca_lpc_campaignmonitor_load();

	// Subscribe user
	$auth = array( 'api_key' => $api_key );
	$wrap = new CS_REST_Subscribers( $list_id, $auth );
	$result = $wrap->add( array(
		'EmailAddress' => $email,
		'Name' => $name,
		'Resubscribe' => true,
	) );

	return $result->was_successful() ? true : false;
}

function fca_lpc_get_campaignmonitor_lists() {
	
    // Validate the API key
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : sanitize_text_field( $_REQUEST['nonce'] );
	$api_key = empty ( $_REQUEST['api_key'] ) ? '' : sanitize_text_field( $_REQUEST['api_key'] );
	$client_id = empty ( $_REQUEST['client_id'] ) ? '' : sanitize_text_field( $_REQUEST['client_id'] );
		
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && !empty ($api_key) && !empty ($client_id) ) {
	
		fca_lpc_campaignmonitor_load();
		
		$lists_formatted = array();
		$auth = array( 'api_key' => $api_key );
		$wrap = new CS_REST_Clients( $client_id, $auth );
		$results = json_decode( json_encode( $wrap->get_lists() ), true );
		if ( isset( $results[ 'response' ] ) && $results[ 'http_status_code' ] == 200 ) {
			foreach ( $results[ 'response' ] as $result ) {
				$lists_formatted[] = array( 'name' => $result['Name'], 'id' =>  $result['ListID'] );
			}
			wp_send_json_success( $lists_formatted );
		}
	}
	wp_send_json_error();

}
add_action( 'wp_ajax_fca_lpc_get_campaignmonitor_lists', 'fca_lpc_get_campaignmonitor_lists' );
