<?php 

function fca_lpc_aweber_settings( $meta ) {

	$aweber_key = empty ( $meta['aweber_key'] ) ? '' : $meta['aweber_key'];
	$aweber_list = empty ( $meta['aweber_list'] ) ? '' : $meta['aweber_list'];
	
	$html = "<tr class='fca_lpc_aweber_setting_row'>";
		$html .= "<th>" . __('Auth Code', 'landing-page-cat') . fca_lpc_tooltip( __('Click the "Get my Aweber Auth Code" link and paste your auth code here.', 'landing-page-cat') ) . fca_lpc_spinner() . "</th>";
		
		$html .= "<td>";
			$html .= fca_lpc_input( 'aweber_key', '', $aweber_key, 'textarea' );
			$html .= fca_lpc_info_span( __('Get my Aweber Auth Code', 'landing-page-cat'), 'https://auth.aweber.com/1.0/oauth/authorize_app/799877fc' );
		$html .= "</td>";
	$html .= "</tr>";
	
	$html .= "<tr class='fca_lpc_aweber_setting_row fca_lpc_aweber_api_settings'>";
		$html .= "<th>" . __('List to subscribe to', 'landing-page-cat') . "</th>";
		$html .= "<td>" . fca_lpc_select( 'aweber_list', $aweber_list ) . "</td>";
	$html .= "</tr>";
	
	return $html;
	
}

function fca_lpc_aweber_object( $api_key = '' ) {

	if ( !class_exists( 'AWeberAPI' ) ) {
		require_once( FCA_LPC_PLUGIN_DIR . '/includes/providers/aweber_api/aweber_api.php' );
	}
	if ( empty( $api_key ) ) {
		$credentials = get_option( 'fca_lpc_aweber_credentials' );		
	}
	
	if ( !isSet( $credentials['consumerKey'],
              $credentials['consumerSecret'],
              $credentials['accessKey'], 
              $credentials['accessSecret'] ) ) {
		try {
			list( $credentials[ 'consumerKey' ], $credentials[ 'consumerSecret' ], $credentials[ 'accessKey' ], $credentials[ 'accessSecret' ] ) = AWeberAPI::getDataFromAweberID( $api_key );
		
			if ( !empty ( $credentials ) ) {
				update_option( 'fca_lpc_aweber_credentials', $credentials);
			}
		} catch ( Exception $e ) {
			return false;
		}
	}
		
	try {

		$aweber = new AWeberAPI( $credentials[ 'consumerKey' ], $credentials[ 'consumerSecret' ]	);
		$account = $aweber->getAccount( $credentials[ 'accessKey' ], $credentials[ 'accessSecret' ] );
		
	} catch ( Exception $e ) {
		return false;		
	}
	
	// Prepare lists
	$lists = array();
	foreach ( $account->lists->data[ 'entries' ] as $list ) {
		$lists[] = array( 'id' => $list[ 'id' ], 'name' => $list[ 'name' ] );
	}
	
	return array(
		'lists' => $lists,
		'account' => $account,
	);
}


function fca_lpc_aweber_subscribe( $email, $name, $list_id, $aweber_tags ) {

	$helper = fca_lpc_aweber_object();  //should load from saved credentials...

	if ( empty( $helper['account'] ) ) {
		return false;
	}
		
	$account = $helper[ 'account' ];

	try {
		$listURL = "/accounts/$account->id/lists/$list_id";
		$list = $account->loadFromUrl( $listURL );

		$list->subscribers->create( array(
			'email' => $email,
			'name' => $name,
			'tags' => array( $aweber_tags ),
		) );
		
		return true;
	} catch ( Exception $e ) {
		//check if already subscribed
		if ( stripos( $e->getMessage(), 'already subscribed') !== false ) {
			return true;
		}
		
	}
	return false;
}

function fca_lpc_get_aweber_lists() {
	
    // Validate the API key
	$nonce = empty ( $_REQUEST['nonce'] ) ? '' : esc_textarea( $_REQUEST['nonce'] );
	$new_api_key = empty ( $_REQUEST['api_key'] ) ? '' : trim( esc_textarea( $_REQUEST['api_key'] ) );
	$post_id = empty ( $_REQUEST['post_id'] ) ? '' : esc_textarea( $_REQUEST['post_id'] );
	
	if ( wp_verify_nonce ( $nonce, 'fca_lpc_admin_nonce') == 1 && $new_api_key ) {
		
		$post_meta = get_post_meta ( $post_id, 'fca_lpc', true );
		
		$old_api_key = isSet( $post_meta['aweber_key'] ) ? $post_meta['aweber_key'] : false;
		
		if ( $new_api_key === $old_api_key ) {
			$helper = fca_lpc_aweber_object();
		} else {
			$helper = fca_lpc_aweber_object( $new_api_key );
		}
		
		if ( $helper === false ) {
			wp_send_json_error();
		}

		if ( empty( $helper['lists'] ) ) {
			wp_send_json_error();
		} else {
			wp_send_json_success( $helper['lists'] );
		}

	} else {
		wp_send_json_error();
	}

}
add_action( 'wp_ajax_fca_lpc_get_aweber_lists', 'fca_lpc_get_aweber_lists' );
