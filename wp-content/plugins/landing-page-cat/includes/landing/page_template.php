<?php 

	global $post;
	if ( !empty ( $_GET['landingpage'] ) ) {
		$post = get_post( $_GET['landingpage'] );
	}
	
	$is_404 = is_404();
	
	if ( $is_404 ) {
		global $wp_query;
		$wp_query->is_404 = false;		
		status_header(200);
	}
	
	$meta = get_post_meta( $post->ID, 'fca_lpc', true );
	$meta = empty( $meta ) ? array() : $meta;
	
	$mode = empty ( $meta['deploy_mode'] ) ? 'disabled' : $meta['deploy_mode'];
	$title = get_the_title( $post->ID );
	$bg_alpha = !isSet( $meta['bg_alpha'] ) ? 0.6 : $meta['bg_alpha'];
	$bg_alpha = $meta['background_image'] === '' ? 0 : $meta['bg_alpha'];
	$bg_color = empty( $meta['background_image'] ) ? $meta['bg_color'] : $meta['background_image'];
	$button_color = empty( $meta['button_copy_color'] ) ? '#ffffff' : $meta['button_copy_color'];
	$header_target_blank = $meta['header_target_blank'] === 'on' ? '_blank' : '_self';
	$header_copy = empty( $meta['header_copy'] ) ? '' : $meta['header_copy'];
	$header_copy_color = empty( $meta['header_copy_color'] ) ? '#000000' : $meta['header_copy_color'];
	$header_logo = empty( $meta['header_logo'] ) ? '' : $meta['header_logo'];
	$header_enabled = empty( $meta['header_enabled'] ) ? 'off' : $meta['header_enabled'];
	$header_post = empty( $meta['header_post'] ) ? 'home' : $meta['header_post'];
	$header_url = empty( $meta['header_url'] ) ? '' : $meta['header_url'];
	$header_redirect = empty( $meta['header_mode'] ) ? $header_url : $header_post;
	$header_bg_color = empty( $meta['header_bg_color'] ) ? '#ffffff' : $meta['header_bg_color'];
	$countdown_enabled = empty( $meta['countdown_enabled'] ) ? 'off' : $meta['countdown_enabled'];
	$countdown_date = empty( $meta['countdown_date'] ) ? '' : $meta['countdown_date'];
	$countdown_style = empty( $meta['countdown_style'] ) ? 'circles' : $meta['countdown_style'];
	$countdown_color = empty( $meta['countdown_color'] ) ? '#01a3a4' : $meta['countdown_color'];
	$button_target_blank = $meta['button_target_blank'] === 'on' ? '_blank' : '_self';
	$gdpr_checkbox = fca_lpc_show_gdpr_checkbox();
	$consent_headline = stripslashes_deep( get_option( 'fca_lpc_consent_headline' ) );
	$consent_msg = stripslashes_deep( get_option( 'fca_lpc_consent_msg' ) );
	$default_social_image = FCA_LPC_PLUGINS_URL . '/assets/cup-of-coffee-1920.jpg';
	$wrapper_top = $header_enabled === 'on' ? '0px' : '90px';
	
	$JSON = htmlspecialchars( wp_json_encode ( 
		array(

			'mode' => $mode,
			'nonce' => wp_create_nonce( 'fca_lpc_landing_page_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'subscribe_message' => $meta['subscribe_message'],
			'thanks_message' => $meta['thanks_message'],
			'required_message' => $meta['required_message'],
			'invalid_message' => $meta['invalid_message'],
			'post_id' => $post->ID,
			'redirect_on' => !empty( $meta['success_redirect'] ) && $meta['success_redirect'] === 'redirect' ? true : false,
			'redirect_url' => fca_lpc_redirect_url($meta, $post, 'success'),
			'page_title' => $title,
			'particles_enabled' => !empty( $meta['particles_enabled'] ),
			'countdown_enabled' => $countdown_enabled,
			'countdown_date' => $countdown_date,
			'event_tracking' => empty( $meta['event_tracking'] ) ? false : true,
			'do_cookie' => !empty( $meta['success_cookie'] ) && $mode === 'welcome' ? true : false,
			'gdpr_checkbox' =>  $gdpr_checkbox,

		) ),
		ENT_QUOTES,
		'UTF-8'
	);
?>

<!doctype html>
<html>
	<head>
		<?php wp_head(); 
		if ( $meta['background_image'] ) { 
			echo '<meta property="og:image" content="' . $meta['background_image'] . '" />';
		} else { 
			echo '<meta property="og:image" content="' . $default_social_image . '" />';
		};
		?>
		<meta name='viewport' content='width=device-width, initial-scale=1'>
		<style type='text/css'>#fca-lpc-optin-button:hover{ background-color: <?php echo $meta['button_hover_color'] ?> !important}</style>
		<?php if ( !empty( $meta['custom_css'] ) ) {
			echo '<style type="text/css">' . $meta['custom_css'] . '</style>';
		} ?>
	</head>
	<body>
		<?php
		if ( $header_enabled === 'on' ){
			if ( $header_logo !== '' || $header_copy !== '' ){
				?><div id='fca-lpc-header' style='color: <?php echo $header_copy_color ?>; background-color: <?php echo $header_bg_color ?>;'><?php 
				if ( $header_logo !== '' ){ ?>
					<span id='fca-lpc-header_logo'><a href='<?php echo fca_lpc_redirect_url($meta, $post, 'header') ?>' target='<?php echo $header_target_blank ?>'><img src='<?php echo $header_logo ?>'></a></span><?php 
				};
				if ( $header_copy !== '' ){ ?>
					<span id='fca-lpc-header_copy'><a href='<?php echo fca_lpc_redirect_url($meta, $post, 'header') ?>' target='<?php echo $header_target_blank ?>'><?php echo $header_copy ?></a></span>
				<?php } ?>
				</div> 
			<?php };
		} ?>
		<div id='fca-lpc-wrapper'  style='top: <?php echo $wrapper_top ?>; background-color: <?php echo $bg_color ?>; background-image: url("<?php echo $meta['background_image'] ?>"); background-image: linear-gradient(to bottom, rgba(45,45,45,<?php echo $bg_alpha?>) 0%,rgba(45,45,45,<?php echo $bg_alpha?>) 100%), url("<?php echo $meta['background_image'] ?>");'>
			<?php do_action('fca_lpc_template_body', $meta ); ?>
			<div id='fca-lpc-main'>
				<div id='fca-lpc-inner'>
					<input id='fca-lpc-data' type='hidden' value='<?php echo $JSON ?>'>
					<div id='fca-lpc-headline' style='color:<?php echo $meta['headline_color'] ?>'><?php echo $meta['headline_copy'] ?></div>
					
					<?php if ( ( $countdown_enabled ) === 'on' ) { ?>
						<div id='<?php echo 'fca-lpc-countdown-' . $countdown_style ?>' style='color:<?php echo $meta['countdown_color'] ?>'>
							<div>
								<span class="fca-lpc-countdown" id="day"></span> 
								<div class="fca-lpc-countdown-text"><?php _e( 'DAYS', 'landing-page-cat') ?></div> 
							</div>
							<div>
								<span class="fca-lpc-countdown" id="hour"></span> 
								<div class="fca-lpc-countdown-text"><?php _e( 'HOURS', 'landing-page-cat') ?></div> 
							</div>
							<div>
								<span class="fca-lpc-countdown" id="minute"></span> 
								<div class="fca-lpc-countdown-text"><?php _e( 'MINUTES', 'landing-page-cat') ?></div> 
							</div>
							<div>
								<span class="fca-lpc-countdown" id="second"></span> 
								<div class="fca-lpc-countdown-text"><?php _e( 'SECONDS', 'landing-page-cat') ?></div> 
							</div>
						</div>
					<?php } ?>	

					<div id='fca-lpc-subheadline' style='color:<?php echo $meta['subheadline_color'] ?>'><?php echo do_shortcode( apply_filters( 'the_content', $meta['subheadline_copy'] ) ) ?></div>
					<?php if ( $gdpr_checkbox ) { ?>						
						<div id='fca-lpc-gdpr' style='display:none; color:<?php echo $meta['subheadline_color'] ?>'>
							<div><?php echo $consent_headline ?></div>
							<label>
								<input id="fca-lpc-gdpr-consent" title='<?php echo esc_attr( $meta['required_message'] ) ?>' value="" type="checkbox"></input>
								<?php echo $consent_msg ?>
							</label>
						</div>
					<?php }?>
					<?php if ( empty( $meta['call_to_action'] ) OR $meta['call_to_action'] === 'optin' ) { ?>
						<div id='fca-lpc-optin'>
							<?php if ( $meta['show_name'] ) { ?>
								<input name='name' title='<?php echo esc_attr( $meta['required_message'] ) ?>' type='text' class='fca-lpc-input-element' id='fca-lpc-name-input' placeholder='<?php echo esc_attr( $meta['name_placeholder'] ) ?>' />
							<?php } ?>
							<input name='email' title='<?php echo esc_attr(  $meta['required_message'] ) ?>' type='email' class='fca-lpc-input-element' id='fca-lpc-email-input' placeholder='<?php echo esc_attr( $meta['email_placeholder'] ) ?>' />
							<button type='button' data-mode='optin' title='<?php echo esc_attr( $meta['thanks_message'] ) ?>' class='fca-lpc-input-element' id='fca-lpc-optin-button' style='color:<?php echo $button_color ?>; background-color:<?php echo $meta['button_color'] ?>; border-bottom-color:<?php echo $meta['button_border_color'] ?>'><?php echo $meta['button_copy'] ?></button>
						</div>
					<?php } else if ( $meta['call_to_action'] === 'button' ) {?>
						<button type='button' data-mode='button' onclick='window.open("<?php echo fca_lpc_redirect_url($meta, $post, 'button') ?>","<?php echo $button_target_blank?>")' class='fca-lpc-input-element' id='fca-lpc-optin-button' style='color:<?php echo $button_color ?>; background-color:<?php echo $meta['button_color'] ?>; border-bottom-color:<?php echo $meta['button_border_color'] ?>'><?php echo $meta['button_copy'] ?></button>
					<?php }
						if ( !empty( $meta['footer_copy'] ) ) {	?>
							<div id='fca-lpc-after-button' style='color:<?php echo $meta['footer_copy_color'] ?>'><?php echo $meta['footer_copy'] ?></div>
						<?php }
						if ( !empty( $meta['show_skip'] ) && !$is_404 ) {	?>
							<a id='fca-lpc-skip-link' href='<?php echo add_query_arg( 'fca_lpc_skip', $post->ID ) ?>' style='color: <?php echo $meta['skip_color'] ?>;'><?php echo $meta['skip_copy'] ?></a>
					<?php } ?>	
				
				</div>
			</div>
		</div>
		<div id='fca-lpc-footer' style='display: none;'>
			<?php wp_footer(); ?> 
		</div>
	</body>
</html>