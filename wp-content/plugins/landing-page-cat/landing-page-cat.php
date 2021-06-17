<?php
/*
	Plugin Name: Landing Page Cat Free
	Plugin URI: https://fatcatapps.com/landing-page-cat
	Description: Provides an easy way to create landing pages
	Text Domain: landing-page-cat
	Domain Path: /languages
	Author: Fatcat Apps
	Author URI: https://fatcatapps.com/
	License: GPLv2
	Version: 1.7.2
*/

// BASIC SECURITY
defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );



if ( !defined('FCA_LPC_PLUGIN_DIR') ) {
	
	//DEFINE SOME USEFUL CONSTANTS
	define( 'FCA_LPC_PLUGIN_VER', '1.7.2' );
	define( 'FCA_LPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'FCA_LPC_PLUGINS_URL', plugins_url( '', __FILE__ ) );
	define( 'FCA_LPC_PLUGIN_FILE', __FILE__ );
	define( 'FCA_LPC_PLUGIN_PACKAGE', 'Free' ); //DONT CHANGE THIS, IT WONT ADD FEATURES, ONLY BREAKS UPDATER AND LICENSE

	define( 'FCA_LPC_PLUGINS_BASENAME', plugin_basename(__FILE__) );
		
	//LOAD CORE
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/functions.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/api.php' );
	
	//LOAD MODULES
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/subscribers.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/landing/landing.php' );
	include_once( FCA_LPC_PLUGIN_DIR . '/includes/editor/editor.php' );
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/editor/editor-premium.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/editor/editor-premium.php' );
	}
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/landing/landing-premium.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/landing/landing-premium.php' );
	}
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/editor/sidebar.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/editor/sidebar.php' );
	}
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/upgrade.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/upgrade.php' );
	}	
	if ( file_exists ( FCA_LPC_PLUGIN_DIR . '/includes/licensing/licensing.php' ) ) {
		include_once( FCA_LPC_PLUGIN_DIR . '/includes/licensing/licensing.php' );
	}
	
	//ACTIVATION HOOK
	function fca_lpc_activation() {
		fca_lpc_api_action( 'Activated Landing Page Cat Free' );
		fca_lpc_set_bg_image_file_paths();
	}
	register_activation_hook( FCA_LPC_PLUGIN_FILE, 'fca_lpc_activation' );
	
	//DEACTIVATION HOOK
	function fca_lpc_deactivation() {
		fca_lpc_api_action( 'Deactivated Landing Page Cat Free' );
	}
	register_deactivation_hook( FCA_LPC_PLUGIN_FILE, 'fca_lpc_deactivation' );
	
	////////////////////////////
	// SET UP POST TYPE
	////////////////////////////

	//REGISTER CPT
	function fca_lpc_register_post_type() {
		
		$labels = array(
			'name' => _x('Landing Pages','landing-page-cat'),
			'singular_name' => _x('Landing Page','landing-page-cat'),
			'add_new' => _x('Add New','landing-page-cat'),
			'all_items' => _x('All Landing Pages','landing-page-cat'),
			'add_new_item' => _x('Add New Landing Page','landing-page-cat'),
			'edit_item' => _x('Edit Landing Page','landing-page-cat'),
			'new_item' => _x('New Landing Page','landing-page-cat'),
			'view_item' => _x('View Landing Page','landing-page-cat'),
			'search_items' => _x('Search Landing Pages','landing-page-cat'),
			'not_found' => _x('Landing Page not found','landing-page-cat'),
			'not_found_in_trash' => _x('No Landing Pages found in trash','landing-page-cat'),
			'parent_item_colon' => _x('Parent Landing Page:','landing-page-cat'),
			'menu_name' => _x('Landing Pages','landing-page-cat')
		);
			
		$args = array(
			'labels' => $labels,
			'description' => "",
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'menu_position' => 101,
			'menu_icon' => FCA_LPC_PLUGINS_URL . '/icons/icon.png',
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title','thumbnail'),
			'has_archive' => false,
			'rewrite' => false,
			'query_var' => true,
			'can_export' => true
		);
		
		register_post_type( 'landingpage', $args );
	}
	add_action ( 'init', 'fca_lpc_register_post_type' );
	
	//CHANGE CUSTOM 'UPDATED' MESSAGES FOR OUR CPT
	function fca_lpc_post_updated_messages( $messages ){
		
		$post = get_post();
		$preview_url = get_site_url() . '?landingpage=' . $post->ID;
		$messages['landingpage'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Landing Page updated. %sView Preview%s','landing-page-cat'), "<a href='$preview_url' target='_blank'>", '</a>' ),
			2  => sprintf( __( 'Landing Page updated. %sView Preview%s','landing-page-cat'), "<a href='$preview_url' target='_blank'>", '</a>' ),
			3  => __( 'Landing Page deleted.','landing-page-cat'),
			4  => sprintf( __( 'Landing Page updated.  %sView Preview%s.','landing-page-cat'), "<a href='$preview_url' target='_blank'>", '</a>' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Landing Page restored to revision from %s','landing-page-cat'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Landing Page published.' ,'landing-page-cat'),
			7  => __( 'Landing Page saved.' ,'landing-page-cat'),
			8  => __( 'Landing Page submitted.' ,'landing-page-cat'),
			9  => sprintf(
				__( 'Landing Page scheduled for: <strong>%1$s</strong>.','landing-page-cat'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Landing Page draft updated.' ,'landing-page-cat'),
		);

		return $messages;
	}
	add_filter('post_updated_messages', 'fca_lpc_post_updated_messages' );
	
	function fca_lpc_remove_screen_options_tab ( $show_screen, $screen ) {
		if ( $screen->id == 'landingpage' ) {
			return false;
		}
		return $show_screen;
	}	
	add_filter('screen_options_show_screen', 'fca_lpc_remove_screen_options_tab', 10, 2);
	
	//DEACTIVATION SURVEY
	function fca_lpc_admin_deactivation_survey( $hook ) {
		if ( $hook === 'plugins.php' ) {
			
			ob_start(); ?>
			
			<div id="fca-deactivate" style="position: fixed; left: 232px; top: 191px; border: 1px solid #979797; background-color: white; z-index: 9999; padding: 12px; max-width: 669px;">
				<h3 style="font-size: 14px; border-bottom: 1px solid #979797; padding-bottom: 8px; margin-top: 0;"><?php _e( 'Sorry to see you go', 'landing-page-cat' ) ?></h3>
				<p><?php _e( 'Hi, this is David, the creator of Landing Page Cat. Thanks so much for giving my plugin a try. I’m sorry that you didn’t love it.', 'landing-page-cat' ) ?>
				</p>
				<p><?php _e( 'I have a quick question that I hope you’ll answer to help us make Landing Page Cat better: what made you deactivate?', 'landing-page-cat' ) ?>
				</p>
				<p><?php _e( 'You can leave me a message below. I’d really appreciate it.', 'landing-page-cat' ) ?>
				</p>
				<p><b><?php _e( 'If you\'re upgrading to Landing Page Cat Premium and have questions or need help, click <a href=' . 'https://fatcatapps.com/article-categories/gen-getting-started/' . ' target="_blank">here</a></b>', 'landing-page-cat' ) ?>
				</p>

				<p><textarea style='width: 100%;' id='fca-lpc-deactivate-textarea' placeholder='<?php _e( 'What made you deactivate?', 'landing-page-cat' ) ?>'></textarea></p>
				
				<div style='float: right;' id='fca-deactivate-nav'>
					<button style='margin-right: 5px;' type='button' class='button button-secondary' id='fca-lpc-deactivate-skip'><?php _e( 'Skip', 'landing-page-cat' ) ?></button>
					<button type='button' class='button button-primary' id='fca-lpc-deactivate-send'><?php _e( 'Send Feedback', 'landing-page-cat' ) ?></button>
				</div>
			
			</div>
			
			<?php
				
			$html = ob_get_clean();
			
			$data = array(
				'html' => $html,
				'nonce' => wp_create_nonce( 'fca_lpc_uninstall_nonce' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);
						
			wp_enqueue_script('fca_lpc_deactivation_js', FCA_LPC_PLUGINS_URL . '/includes/deactivation.min.js', false, FCA_LPC_PLUGIN_VER, true );
			wp_localize_script( 'fca_lpc_deactivation_js', "fca_lpc", $data );
		}
		
		
	}	
	add_action( 'admin_enqueue_scripts', 'fca_lpc_admin_deactivation_survey' );
	
	function fca_lpc_post_type_url( $url, $post ) {
		if ( get_post_type( $post ) === 'landingpage' ) {
			return home_url( '/?landingpage=' . $post->ID );
		}
		return $url;
	}
	add_filter( 'post_type_link', 'fca_lpc_post_type_url', 10, 2 );
	
	function fca_lpc_add_plugin_action_links( $links ) {
		
		$support_url = FCA_LPC_PLUGIN_PACKAGE === 'Free' ? 'https://wordpress.org/support/plugin/landing-page-cat' : 'https://fatcatapps.com/support';
		
		$new_links = array(
			'support' => "<a target='_blank' href='$support_url' >" . __('Support', 'quiz-cat' ) . '</a>'
		);
		
		$links = array_merge( $new_links, $links );
	
		return $links;
		
	}
	add_filter( 'plugin_action_links_' . FCA_LPC_PLUGINS_BASENAME, 'fca_lpc_add_plugin_action_links' );

}

function fca_lpc_set_bg_image_file_paths() {
	$args = array(
		'post_type' => 'landingpage',
		'post_per_page' => -1,
	);
	$posts_updated = 0;
	forEach ( get_posts ( $args ) as $post ) {
		$meta = get_post_meta ( $post->ID, 'fca_lpc', true );
		
		//THE LANDING PAGE PATH CHANGED IF YOU UPGRADE TO PREMIUM
		$search_path = plugins_url() . '/landing-page-cat';
		
		if ( stripos( $meta['background_image'], $search_path ) !== false ) {
			
			$pos = strrpos( $meta['background_image'], '/');
			$meta['background_image'] = $pos === false ? '' : FCA_LPC_PLUGINS_URL . '/assets/' . substr( $meta['background_image'], $pos + 1);
			
			if ( !isSet( $meta['bg_alpha'] ) ) {
				$meta['bg_alpha'] = !empty( $meta['background_image'] ) ? 0.6 : 0;
			}
			
			if ( !empty( $meta['deploy_url'] ) && !empty( $meta['deploy_url_url'] ) ) {
				$meta['deploy_mode'] = 'url';
				unset ( $meta['deploy_url'] );
			} else if ( empty( $meta['deploy_mode'] ) ) {
				$meta['deploy_mode'] = 'homepage';
			}
		}
		
		if ( update_post_meta( $post->ID, 'fca_lpc', $meta )  ) {
			$posts_updated++;
		}
	}
	
	update_option( 'fca_lpc_meta_version', '1.2.0' );
	return $posts_updated;

}

//Customize CPT table columns
function fca_lpc_add_new_post_table_columns($columns) {
	$new_columns = array();
	$new_columns['cb'] = '<input type="checkbox" />';
	$new_columns['title'] = _x('Title', 'column name', 'landing-page-cat');
	$new_columns['state'] = __('Current state', 'landing-page-cat');
	$new_columns['date'] = _x('Date', 'column name', 'landing-page-cat');
 
	return $new_columns;
}
add_filter('manage_edit-landingpage_columns', 'fca_lpc_add_new_post_table_columns', 10, 1 );

function fca_lpc_manage_post_table_columns($column_name, $id) {

	$meta = get_post_meta ( $id, 'fca_lpc', true );
	if ( $meta['deploy_mode'] !== 'disabled' ){
		$behavior = 'Enabled';
	} else {
		$behavior = 'Disabled';
	}
	switch ($column_name) {
		case 'state':
			echo $behavior;
				break;
	 
		default:
		break;
	} // end switch
}
add_action('manage_landingpage_posts_custom_column', 'fca_lpc_manage_post_table_columns', 10, 2);

//UPGRADE NOTICE FOR 1.2
function fca_lpc_admin_notice() {

	if ( isset ( $_GET['fca_lpc_run_upgrade'] ) ) {
		//RUN ACTIVATION HOOK WHICH CONTAINS BACKWARD COMPATIBILITY STUFF
		$posts_updated = fca_lpc_set_bg_image_file_paths();
		
		echo '<div class="notice notice-info">';
			echo "<p>$posts_updated " . __('Landing Pages Updated.', 'landing-page-cat' ) . '</p>';
		echo '</div>';	
	} else if ( get_option( 'fca_lpc_meta_version' ) !== '1.2.0' ) {
		echo '<div class="notice notice-info">';
			echo '<img height="120" width="120" style="float:left;" src="' . FCA_LPC_PLUGINS_URL . '/icons/icon-128x128.png' . '">';
			echo '<p><strong>' . __('Landing Page Cat 1.2 Update', 'landing-page-cat' ) . '</strong></p>';
			echo '<p>' . __("Landing Page Cat needs to update any previous landing pages for compatibility with the new version.", 'landing-page-cat' );
			echo '<p>' . __("This should only take a moment.", 'landing-page-cat' );
			echo "<p><a class='button button-primary' href='" . esc_url( add_query_arg( 'fca_lpc_run_upgrade', 'true' ) ) . "'>" . __('OK', 'landing-page-cat' ) . '</a></p>';
		echo '</div>';
	}	

	if ( FCA_LPC_PLUGIN_PACKAGE === 'Free' ){

		if ( isSet( $_GET['fca_lpc_leave_review'] ) ) {

			$review_url = 'https://wordpress.org/support/plugin/landing-page-cat/reviews/?filter=5';
			update_option( 'fca_lpc_show_review_notice', false );
			wp_redirect($review_url);
			exit;

		}

		$show_review_option = get_option( 'fca_lpc_show_review_notice', 'not-set' );

		if ( $show_review_option === 'not-set' && !wp_next_scheduled( 'fca_lpc_schedule_review_notice' )  ) {

			wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_lpc_schedule_review_notice' );

		}

		if ( isSet( $_GET['fca_lpc_postpone_review_notice'] ) ) {

			$show_review_option = false;
			update_option( 'fca_lpc_show_review_notice', $show_review_option );
			wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_lpc_schedule_review_notice' );

		}

		if ( isSet( $_GET['fca_lpc_forever_dismiss_notice'] ) ) {

			$show_review_option = false;
			update_option( 'fca_lpc_show_review_notice', $show_review_option );

		}

		$review_url = add_query_arg( 'fca_lpc_leave_review', true );
		$postpone_url = add_query_arg( 'fca_lpc_postpone_review_notice', true );
		$forever_dismiss_url = add_query_arg( 'fca_lpc_forever_dismiss_notice', true );

		if ( $show_review_option && $show_review_option !== 'not-set' ){

			$plugin_name = 'landing-page-cat';

			echo '<div id="fca-pc-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
				echo '<p>' . __( "Hi! You've been using Landing Page Cat for a while now, so who better to ask for a review than you? Would you please mind leaving us one? It really helps us a lot!", $plugin_name ) . '</p>';
				echo "<a href='$review_url' class='button button-primary' style='margin-top: 2px;'>" . __( 'Leave review', $plugin_name) . "</a> ";
				echo "<a style='position: relative; top: 10px; left: 7px;' href='$postpone_url' >" . __( 'Maybe later', $plugin_name) . "</a> ";
				echo "<a style='position: relative; top: 10px; left: 16px;' href='$forever_dismiss_url' >" . __( 'No thank you', $plugin_name) . "</a> ";
				echo '<br style="clear:both">';
			echo '</div>';

		}
	}

}
add_action( 'admin_notices', 'fca_lpc_admin_notice' );	

function fca_lpc_enable_review_notice(){
	update_option( 'fca_lpc_show_review_notice', true );
	wp_clear_scheduled_hook( 'fca_lpc_schedule_review_notice' );
}

add_action ( 'fca_lpc_schedule_review_notice', 'fca_lpc_enable_review_notice' );

