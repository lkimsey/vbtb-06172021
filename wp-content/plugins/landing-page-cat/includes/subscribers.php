<?php

function fca_lpc_table_name() {
	global $wpdb;
	return $wpdb->prefix . "fca_lpc_subscribers";
}

function fca_lpc_gdpr_update() {
	global $wpdb;
		
	//CREATE THE PEOPLE TABLE
	$table_name = fca_lpc_table_name();
	
	$sql = "CREATE TABLE $table_name (
		`id` INT NOT NULL AUTO_INCREMENT,
		`email` LONGTEXT,
		`name` LONGTEXT,
		`time` DATETIME,
		`ip` LONGTEXT,
		`campaign_id` INT,
		`status` LONGTEXT,
		`consent_granted` LONGTEXT,
		`consent_msg` LONGTEXT,
		PRIMARY KEY  (id)
	);";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	//MOVE ANY OLD OPTINS TO SHINY NEW TABLE
	$args = array(
		'post_type' => 'landingpage',
		'post_per_page' => -1,
	);
	$posts_updated = 0;
	forEach ( get_posts ( $args ) as $post ) {
		$rows_created = 0;
		$optins = get_post_meta( $post->ID, 'fca_lpc_optin' );
		forEach ( $optins as $optin ) {
			
			$rows_created += $wpdb->insert( fca_lpc_table_name(), array(
				'campaign_id'   => $post->ID,
				'email'      => $optin['email'],
				'name'      => $optin['name'],
				'status' => '',
				'time' => $optin['time'],
				'ip' => $optin['ip'],
				'consent_granted' => 'unknown',
				'consent_msg' => '',
			), array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
					
		}
		if ( $rows_created == count ( $optins ) ) {
			delete_post_meta( $post->ID, 'fca_lpc_optin' );
			$posts_updated++;
		}
	}
	
	update_option( 'fca_lpc_gdpr_compatible', true );
}
if ( get_option( 'fca_lpc_gdpr_compatible' ) !== true ) {
	fca_lpc_gdpr_update();
}

function fca_lpc_is_gdpr_country( $accept_language = '' ) {
	$accept_language = empty( $accept_language ) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : $accept_language;
	$gdpr_countries = array(
		"AT",
		"BE",
		"BG",
		"CY",
		"CZ",
		"DE",
		"DK",
		"EE",
		"EL",
		"ES",
		"FI",
		"FR",
		"HR",
		"HU",
		"IE",
		"IT",
		"LT",
		"LU",
		"LV",
		"MT",
		"NL",
		"PL",
		"PT",
		"RO",
		"SE",
		"SI",
		"SK",
		"UK",
		"GL",
		"GF",
		"PF",
		"TF",
		"GP",
		"MQ",
		"YT",
		"NC",
		"RE",
		"BL",
		"MF",
		"PM",
		"WF",
		"AW",
		"AN",
		"BV",
		"AI",
		"BM",
		"IO",
		"VG",
		"KY",
		"FK",
		"FO",
		"GI",
		"MS",
		"PN",
		"SH",
		"GS",
		"TC",
	);
		
	$code = '';

	//in some cases like "fr" or "hu" the language and the country codes are the same
	if ( strlen( $accept_language ) === 2 ){
		$code = strtoupper( $accept_language ); 
	} else if ( strlen( $accept_language ) === 5 ) {          
		$code = strtoupper( substr( $accept_language, 3, 5 ) ); 
	} 
	if ( in_array( $code, $gdpr_countries ) ) {
		return true;
	}
	
	if ( strlen( $accept_language ) > 5 ) {
		
		for ( $i=0; $i+2 < strlen( $accept_language ); $i++ ){
			$code = strtoupper( substr( $accept_language, $i, $i+2 ) );
			if ( in_array( $code, $gdpr_countries ) ) {
				return true;
			}
		}
	}
	return false;
}


function fca_lpc_show_gdpr_checkbox(){
	$gdpr_checkbox = get_option( 'fca_lpc_gdpr_checkbox' );
	if ( !empty( $gdpr_checkbox ) ) {
		$gdpr_locale = get_option( 'fca_lpc_gdpr_locale' );
		if ( empty( $gdpr_locale ) ) {
			return true;
		}
		return fca_lpc_is_gdpr_country();
	}
	
	return false;
}


function fca_lpc_register_data_exporter( $exporters ) {
	$exporters['landing-page-cat'] = array(
		'exporter_friendly_name' => __( 'Landing Page Cat' ),
		'callback' => 'fca_lpc_data_exporter',
	);
	return $exporters;
}
add_filter(	'wp_privacy_personal_data_exporters', 'fca_lpc_register_data_exporter' );

function fca_lpc_data_exporter( $email, $page = 1 ) {
	
	global $wpdb;
	$table_name = fca_lpc_table_name();
	$number = 500; // Limit us to avoid timing out
	$page = (int) $page;
	$offset = ( $page - 1 ) * $number;
	$subscribers = $wpdb->get_results( "SELECT * FROM $table_name WHERE `email` = '$email' LIMIT $number OFFSET $offset" );
	$export_items = array();
	$fields = array(
		'email',
		'name',
		'time',
		'ip',
		'camapgin_id',
		'status',
		'consent_granted',
		'consent_msg',		
	);
	
	
	if ( count( $subscribers ) )  {
		forEach ( $subscribers as $subscriber ) {
			$item = array(
				'group_id' => 'subscriber',
				'group_label' => 'Subscriber',
				'item_id' => 'subscriber-' . $subscriber->id,
				'data' => array(),
			);
			
			forEach ( $fields as $field ) {
				if ( isSet( $subscriber->$field ) && $subscriber->$field !== '' ) {
					$item['data'][] = array(
						'name' => $field,
						'value' => $subscriber->$field
					);
					
				}
			}
					
			$export_items[] = $item;
		}
	}
	return array(
		'data' => $export_items,
		'done' => count( $export_items ) < $number,
	 );			 
}

function fca_lpc_register_data_eraser( $erasers ) {
	$erasers['landing-page-cat'] = array(
		'eraser_friendly_name' => __( 'Landing Page Cat' ),
		'callback' => 'fca_lpc_data_eraser',
	);
	return $erasers;
}
add_filter(	'wp_privacy_personal_data_erasers', 'fca_lpc_register_data_eraser' );

function fca_lpc_data_eraser( $email, $page = 1 ) {
	global $wpdb;
	$table_name = fca_lpc_table_name();
	$number = 500; // Limit us to avoid timing out
	$page = (int) $page;
	$offset = ( $page - 1 ) * $number;
	$subscribers = $wpdb->get_results( "SELECT * FROM $table_name WHERE `email` = '$email' LIMIT $number OFFSET $offset" );
	$rows_deleted = 0;
			
	if ( count( $subscribers ) )  {
		forEach ( $subscribers as $subscriber ) {
			$rows_deleted += $wpdb->delete( $table_name, array( 'id' => $subscriber->id ), array( '%d' ) );
			
		}			
	}
	
	return array(
		'done' => $rows_deleted < $number,
		'items_removed' => $rows_deleted,
		'items_retained' => false,
		'messages' => array(), 
	 );			 
}



function fca_lpc_add_subscriber( $post_id, $email, $name = '', $success ) {
	global $wpdb;
	$post_meta = get_post_meta( $post_id, 'fca_lpc', true );
	$provider = empty ( $post_meta['provider'] ) ? '' : $post_meta['provider'];
	$time = current_time( 'mysql', 1 );
	$consent_granted = isSet( $_POST['consent_granted'] ) ? sanitize_text_field( $_POST['consent_granted'] ) : 'unknown';
	$consent_msg = $consent_granted === 'true' && get_option( 'fca_lpc_consent_msg' ) ? get_option( 'fca_lpc_consent_msg' ) : '';
	
	if ( $success === true ) {
		$status_msg = "added to $provider";
	} else if ( $status ) {
		$status_msg = "failed to add to $provider [$status]";
	} else {
		$status_msg = "failed to add to $provider";
	}
	
	$wpdb->insert( fca_lpc_table_name(), array(
		'campaign_id'   => $post_id,
		'email'      => $email,
		'name'      => $name,
		'status' => $status_msg,
		'time' => $time,
		'ip' => $_SERVER['REMOTE_ADDR'],
		'consent_granted' => $consent_granted,
		'consent_msg' => $consent_msg,
	), array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
			
}	
add_action( 'fca_lpc_after_submission', 'fca_lpc_add_subscriber', 10, 4 ); 
	
function fca_lpc_register_subscribers_page() {
	add_submenu_page(
		'edit.php?post_type=landingpage',
		__('Subscribers', 'landing-page-cat'),
		__('Subscribers', 'landing-page-cat'),
		'manage_options',
		'fca_lpc_subscribers_page',
		'fca_lpc_subscribers_page'
	);
}
add_action( 'admin_menu', 'fca_lpc_register_subscribers_page' );

function fca_lpc_subscribers_page() {

	global $wpdb;
	$table_name = fca_lpc_table_name();
	$where = '';
	$search_text = '';
	$post_limit = 50;
	$page = empty( $_GET['paged'] ) ? 0 : intVal( $_GET['paged'] );
	$offset = $page * $post_limit;
	
	if (  isSet( $_POST['_wpnonce'] ) ) {
		$verified = wp_verify_nonce( sanitize_text_field( $_POST['_wpnonce'] ) );
		$search_text = sanitize_text_field( $_POST['search_text'] );
		
		if ( $verified && $search_text ) {
			if ( $search_text ) {
				$where = "WHERE ( `email` LIKE '%$search_text%' OR `name` LIKE '%$search_text%' )";
				
			}
		}
	}
	
	$subscribers = $wpdb->get_results( "SELECT * FROM $table_name $where ORDER BY `time` DESC LIMIT $post_limit OFFSET $offset" );
			
	ob_start(); ?>
	<div style='padding-right: 32px;'>
		<h1>Subscribers</h1>
		<p>List of people that opted in since you've updated to Landing Page Cat 1.5 (released May 2018)</p>
		<form method='post' id='fca_lpc_subscribers' >
			<?php wp_nonce_field(); ?>
			<input size='35' type='text' name='search_text' value='<?php echo $search_text ?>' ></input>
			<button class='button button-secondary' name='search' type='submit'>Search subscribers</button>
			<a href='<?php echo add_query_arg( array( 'fca_lpc_export' => true, '_wpnonce' => wp_create_nonce() ) ) ?>' class='button button-secondary' style='float:right;' download >Download CSV</a>
		</form>
		<br>
			<table class='wp-list-table widefat fixed striped'>
				<tr>			
					<th style='display:none;'>ID</th>
					<th>Email</th>
					<th>Name</th>
					<th>Time</th>
					<th>IP</th>
					<th>Landing Page</th>
					<th>Status</th>
					<th>Consent granted</th>
					<th>Consent message</th>
				</tr>
				<?php forEach ( $subscribers as $p ) { 
					$form_title = get_the_title( $p->campaign_id );
					$form_link = admin_url( "post.php?post=$p->campaign_id&action=edit" );
					
					if ( empty( $form_title ) ) {
						$form_title = '(no title)';
					}
					echo "<tr>
							<td style='display:none;'>$p->id</td>
							<td>$p->email</td>
							<td>$p->name</td>
							<td>$p->time</td>
							<td>$p->ip</td>
							<td><a href='$form_link'>$form_title</a></td>
							<td>$p->status</td>
							<td>$p->consent_granted</td>
							<td>$p->consent_msg</td>
					</tr>";				
				} ?>
			</table>
			<br>
		<?php
		if ( $page ) {
			$prev_page_link = add_query_arg( 'paged', $page - 1 );
			echo "<a href='$prev_page_link'>Previous</a>";
		}
		if ( count( $subscribers ) >= $post_limit ) {
			$next_page_link = add_query_arg( 'paged', $page + 1 );
			echo "<a style='float:right;' href='$next_page_link'>Next</a>";
		}
		?>
	</div>
	
	<?php		
	echo ob_get_clean();
}
	
function fca_lpc_export_subscribers() {
	if ( is_user_logged_in() && current_user_can('manage_options') && isset( $_GET['fca_lpc_export'] ) && isset( $_GET['_wpnonce'] ) ) {
		if( !wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ) ) ) {
			echo 'Authentication failure.  Please log in and try again.';
			die();
		}
	
		global $wpdb;
		$table_name = fca_lpc_table_name();
			
		$subscribers = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
	
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=subscribers.csv');
		if ( empty( $subscribers ) ) {
			echo 'No results found.';
			die();	
		}
		$headings = array( 'id', 'email', 'name', 'time', 'ip', 'campaign_id', 'status', 'consent_granted', 'consent_message' );
		//$customers = array_map( 'array_values', $customers );
		$out = fopen('php://output', 'w');
		fputcsv( $out, $headings );
		foreach ( $subscribers as $fields ) {
			fputcsv( $out, $fields );
		}
		fclose( $out );
		die();			 
	}
}
add_action( 'plugins_loaded', 'fca_lpc_export_subscribers' );