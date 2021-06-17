<?php


function fca_lpc_landing_enqueue() {
	
	//REMOVE ALL OTHER CSS
	global $wp_styles;
	$wp_styles->queue = array();
	
	wp_enqueue_script( 'jquery' );
	
	wp_enqueue_style( 'fca_lpc_tooltipster_stylesheet', FCA_LPC_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.css', array(), FCA_LPC_PLUGIN_VER );
	wp_enqueue_style( 'fca_lpc_tooltipster_borderless_stylesheet', FCA_LPC_PLUGINS_URL . '/includes/tooltipster/tooltipster-borderless.min.css', array(), FCA_LPC_PLUGIN_VER );
	wp_enqueue_script( 'fca_lpc_tooltipster_js', FCA_LPC_PLUGINS_URL . '/includes/tooltipster/tooltipster.bundle.min.js', array(), FCA_LPC_PLUGIN_VER );
	
	wp_enqueue_style( 'fca_lpc_landing_stylesheet', FCA_LPC_PLUGINS_URL . '/includes/landing/landing.min.css', array(), FCA_LPC_PLUGIN_VER );
	wp_enqueue_script( 'fca_lpc_landing_js', FCA_LPC_PLUGINS_URL . '/includes/landing/landing.min.js', array( 'fca_lpc_tooltipster_js'), FCA_LPC_PLUGIN_VER, true );
	

}

function fca_lpc_render_landing_page() {
	add_action('wp_print_styles', 'fca_lpc_landing_enqueue', 100);
	remove_action('wp_head', '_admin_bar_bump_cb');
	return load_template( FCA_LPC_PLUGIN_DIR . '/includes/landing/page_template.php' );
}

function fca_lpc_check_for_deploy( $template ) {
	
	//CHECK IF SKIPPED
	if ( !empty( $_GET['fca_lpc_skip'] ) ) {
		return $template;
	}
	
	$this_url = fca_lpc_current_url(); 

	$query = new WP_Query( array(
		'post_type' => 'landingpage',
		'post_status' => 'publish',
		'post_per_page' => -1,
		'nopaging' => true
	) );
	
	$current_post_meta = array();
	$deploy = false;
	
	if ( !empty( $_GET['landingpage'] ) ) {
		return fca_lpc_render_landing_page();
	}
		
	if ( $query->have_posts() ) {
	
		while ( $query->have_posts() ) {
			
			$query->the_post();
			$current_post_meta = get_post_meta( get_the_ID(), 'fca_lpc', true );
			$current_post_meta = empty( $current_post_meta ) ? array() : $current_post_meta;
			$mode = empty( $current_post_meta['deploy_mode'] ) ? '' : $current_post_meta['deploy_mode'];
			
			switch( $mode ) {
				
				case 'homepage':
					if ( is_front_page() ) {
						$deploy = true;
					}
					break;
				
				case 'url':
					$deploy_url = get_site_url() . '/' . $current_post_meta['deploy_url_url'];
					$deploy_url_alt = substr( $current_post_meta['deploy_url_url'], -1 ) !== '/' ? get_site_url() . '/' . $current_post_meta['deploy_url_url'] . '/' : get_site_url() . '/' . rtrim( $current_post_meta['deploy_url_url'], '/' );

					if ( $deploy_url === $this_url OR $deploy_url_alt === $this_url )   {
						$deploy = true;
					}
					break;
					
				case 'four_o_four':
					if ( function_exists('fca_lpc_404_check') && fca_lpc_404_check() )  {
						$deploy = true;
					}
					break;
					
				case 'welcome':
					if ( function_exists('fca_lpc_welcome_check') && fca_lpc_welcome_check( $current_post_meta ) && empty( $_COOKIE['fca_lpc_cookie_' . get_the_ID() ] ) ) {
						$deploy = true;
					}
					break;
			}
			
			if ( $deploy === true ) {

				add_filter( 'wp_title_parts', 'fca_lpc_title' );
				return fca_lpc_render_landing_page();
			}
		}
	
	}
	wp_reset_query();
	return $template;
}
add_filter( 'template_include', 'fca_lpc_check_for_deploy', 1 );


function fca_lpc_title ( $title_parts ) {
	$title_parts['title'] = get_the_title();
	return $title_parts;
}



