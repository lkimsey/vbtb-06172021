<?php

////////////////////////////
// FUNCTIONS
////////////////////////////

function fca_lpc_kses( $raw ) {
	$allowed_tags = wp_kses_allowed_html( 'post' );	
	//ADD YOUTUBE EMBED SUPPORT
	$allowed_tags['iframe'] = array( 'src' => true, 'width' => true, 'height' => true, 'frameborder' => true );
	
	return wp_kses( $raw, $allowed_tags );
}

//GET THE URL WE SHOULD USE BASED ON THE SETTINGS
function fca_lpc_redirect_url( $meta, $post, $mode ) {

	$redirect_mode = empty( $meta[ $mode . '_mode'] ) ? 'page' : $meta[ $mode . '_mode'];

	if ( $redirect_mode === 'page' ) {
		$success_post = empty( $meta[ $mode . '_post'] ) ? '' : $meta[ $mode . '_post'];
		if ( $success_post === 'blog' ) {
			return add_query_arg( 'fca_lpc_skip', $post->ID, get_permalink( get_option( 'page_for_posts' ) ) );
		} else if ( $success_post === 'home' ) {
			return add_query_arg( 'fca_lpc_skip', $post->ID, get_home_url() );
		} else if ( $success_post === 'original' ) {
			return add_query_arg( 'fca_lpc_skip', $post->ID );
		} else {
			return add_query_arg( 'fca_lpc_skip', $post->ID . '&' . $success_post, get_home_url() );
		}
	}
	
	return empty( $meta[ $mode . '_url'] ) ? '' : $meta[ $mode . '_url'];

}

//DETECT BOTS
function fca_lpc_is_bot() {
	return isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|facebookexternalhit|slurp|spider/i', $_SERVER['HTTP_USER_AGENT']);
}

//GET CURRENT URL
function fca_lpc_current_url() {
	$http = 'https';
	if ( !isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ) {
		$http = 'http';
	}
	$url_parts = parse_url( "$http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" );
	return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path']; 
}

//TOOLTIP
function fca_lpc_tooltip( $text = 'Tooltip', $icon = 'dashicons dashicons-editor-help' ) {
	return "<span class='$icon fca_lpc_tooltip' title='" . htmlspecialchars( $text, ENT_QUOTES ) . "'></span>";
}	

//DELETE ICONS
function fca_lpc_delete_icons() {
	ob_start(); ?>
		<span class='dashicons dashicons-trash fca_delete_icon fca_delete_button'></span>
		<span class='dashicons dashicons-no fca_delete_icon fca_delete_icon_cancel' style='display:none;'></span>
		<span class='dashicons dashicons-yes fca_delete_icon fca_delete_icon_confirm' style='display:none;'></span>
	<?php
	return ob_get_clean();
}
	
//RETURN GENERIC INPUT HTML
function fca_lpc_input ( $name, $placeholder = '', $value = '', $type = 'input', $atts = '' ) {

	$name = esc_attr( $name );
	$placeholder = esc_attr( $placeholder );
	$value = esc_attr( $value );
	$type = esc_attr( $type );
	$html = "<div class='fca-lpc-field fca-lpc-field-$type'>";
	
		switch ( $type ) {
			
			case 'checkbox':
				$checked = !empty( $value ) ? "checked='checked'" : '';
				
				$html .= "<div class='onoffswitch'>";
					$html .= "<input $atts style='display:none;' type='checkbox' id='fca_lpc[$name]' class='onoffswitch-checkbox fca-lpc-input-$type fca-lpc-$name' name='fca_lpc[$name]' $checked>"; 
					$html .= "<label class='onoffswitch-label' for='fca_lpc[$name]'><span class='onoffswitch-inner' data-content-on='ON' data-content-off='OFF'><span class='onoffswitch-switch'></span></span></label>";
				$html .= "</div>";
				break;
				
			case 'textarea':
				$html .= "<textarea $atts placeholder='$placeholder' class='fca-lpc-input-$type fca-lpc-$name' name='fca_lpc[$name]'>$value</textarea>";
				break;
				
			case 'color':
				$html .= "<input $atts type='text' placeholder='$placeholder' class='fca-lpc-input-$type fca-lpc-$name' name='fca_lpc[$name]' value='$value'>";
				break;
			case 'editor':
				wp_enqueue_style('fca_lpc_wysi_css', FCA_LPC_PLUGINS_URL . '/includes/wysi/wysi.css', array(), FCA_LPC_PLUGIN_VER );		
				wp_enqueue_script('fca_lpc_wysi_js_main', FCA_LPC_PLUGINS_URL . '/includes/wysi/wysihtml.min.js', array(), FCA_LPC_PLUGIN_VER, true );		
				wp_enqueue_script('fca_lpc_wysi_js', FCA_LPC_PLUGINS_URL . '/includes/wysi/wysi.js', array( 'jquery', 'fca_lpc_wysi_js_main' ), FCA_LPC_PLUGIN_VER, true );		
				
				$admin_data = array (
					'stylesheet' => FCA_LPC_PLUGINS_URL . '/includes/wysi/wysi.css',
					'editor' => 'full'
				);
				wp_localize_script( 'fca_lpc_wysi_js', 'fcaLpcAdminData', $admin_data );
				
				$html = '';
				$html .= "<div class='fca-wysiwyg-nav' style='display:none'>";
					$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-text-group">';
						$html .= '<button type="button" data-wysihtml5-command="bold" class="fca-nav-bold fca-nav-rounded-left" ><span class="dashicons dashicons-editor-bold"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="italic" class="fca-nav-italic fca-nav-no-border" ><span class="dashicons dashicons-editor-italic"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="underline" class="fca-nav-underline fca-nav-rounded-right" ><span class="dashicons dashicons-editor-underline"></span></button>';
					$html .= "</div>";
					$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-alignment-group">';
						$html .= '<button type="button" data-wysihtml5-command="justifyLeft" class="fca-nav-justifyLeft fca-nav-rounded-left" ><span class="dashicons dashicons-editor-alignleft"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="justifyCenter" class="fca-nav-justifyCenter fca-nav-no-border" ><span class="dashicons dashicons-editor-aligncenter"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="justifyRight" class="fca-nav-justifyRight fca-nav-rounded-right" ><span class="dashicons dashicons-editor-alignright"></span></button>';
					$html .= "</div>";
					
					$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-link-group">';
						$html .= '<button type="button" data-wysihtml5-command="createLink" style="border-right: 0;" class="fca-wysiwyg-link-group fca-nav-rounded-left"><span class="dashicons dashicons-admin-links"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="unlink" class="fca-wysiwyg-link-group fca-nav-rounded-right"><span class="dashicons dashicons-editor-unlink"></span></button>';
					$html .= "</div>";

					$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-image-group">';
						$html .= '<button type="button" class="fca-wysiwyg-insert-image" data-wysihtml5-command="insertImage"><span class="dashicons dashicons-format-image"></span></button>';
					$html .= "</div>";
					
					$html .= '<div class="fca-wysiwyg-url-dialog" data-wysihtml5-dialog="createLink" style="display: none">';
						$html .= '<input data-wysihtml5-dialog-field="href" value="http://">';
						$html .= '<a class="button button-secondary" data-wysihtml5-dialog-action="cancel">' . __('Cancel', 'quiz-cat') . '</a>';
						$html .= '<a class="button button-primary" data-wysihtml5-dialog-action="save">' . __('OK', 'quiz-cat') . '</a>';
					$html .= "</div>";

					$html .= '<button class="fca-wysiwyg-view-html action" type="button" data-wysihtml5-action="change_view">HTML</button>';
			
				$html .= "</div>";
				$html .= "<textarea $atts class='fca-wysiwyg-html fca-lpc-input-wysi fca-lpc-$name' name='fca_lpc[$name]' placeholder='$placeholder'>$value</textarea>";
				break;

			case 'simpleeditor':
				wp_enqueue_style('fca_lpc_wysi_css', FCA_LPC_PLUGINS_URL . '/includes/wysi/wysi.css', array(), FCA_LPC_PLUGIN_VER );		
				wp_enqueue_script('fca_lpc_wysi_js_main', FCA_LPC_PLUGINS_URL . '/includes/wysi/wysihtml.min.js', array(), FCA_LPC_PLUGIN_VER, true );		
				wp_enqueue_script('fca_lpc_wysi_js', FCA_LPC_PLUGINS_URL . '/includes/wysi/wysi.js', array( 'jquery', 'fca_lpc_wysi_js_main' ), FCA_LPC_PLUGIN_VER, true );		
				
				$admin_data = array (
					'stylesheet' => FCA_LPC_PLUGINS_URL . '/includes/wysi/wysi.css',
					'editor' => 'simple'
				);
				wp_localize_script( 'fca_lpc_wysi_js', 'fcaLpcAdminData', $admin_data );
				
				$html = '';
				$html .= "<div class='fca-wysiwyg-nav' style='display:none'>";
					$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-text-group">';
						$html .= '<button type="button" data-wysihtml5-command="bold" class="fca-nav-bold fca-nav-rounded-left" ><span class="dashicons dashicons-editor-bold"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="italic" class="fca-nav-italic fca-nav-no-border" ><span class="dashicons dashicons-editor-italic"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="underline" class="fca-nav-underline fca-nav-rounded-right" ><span class="dashicons dashicons-editor-underline"></span></button>';
					$html .= "</div>";
					$html .= '<div class="fca-wysiwyg-group fca-wysiwyg-link-group">';
						$html .= '<button type="button" data-wysihtml5-command="createLink" style="border-right: 0;" class="fca-wysiwyg-link-group fca-nav-rounded-left"><span class="dashicons dashicons-admin-links"></span></button>';
						$html .= '<button type="button" data-wysihtml5-command="unlink" class="fca-wysiwyg-link-group fca-nav-rounded-right"><span class="dashicons dashicons-editor-unlink"></span></button>';
					$html .= "</div>";
				
					$html .= '<div class="fca-wysiwyg-url-dialog" data-wysihtml5-dialog="createLink" style="display: none">';
						$html .= '<input data-wysihtml5-dialog-field="href" value="http://">';
						$html .= '<a class="button button-secondary" data-wysihtml5-dialog-action="cancel">' . __('Cancel', 'quiz-cat') . '</a>';
						$html .= '<a class="button button-primary" data-wysihtml5-dialog-action="save">' . __('OK', 'quiz-cat') . '</a>';
					$html .= "</div>";

					$html .= '<button class="fca-wysiwyg-view-html action" type="button" data-wysihtml5-action="change_view">HTML</button>';
			
				$html .= "</div>";
				$html .= "<textarea $atts class='fca-wysiwyg-html fca-lpc-input-wysi fca-lpc-$name' name='fca_lpc[$name]' placeholder='$placeholder'>$value</textarea>";
				break;
					
			case 'datepicker':
				$html .= "<input $atts type='text' placeholder='$placeholder' class='fca-lpc-input-$type fca-lpc-$name' name='fca_lpc[$name]' value='$value'>";
				break;
			
			default: 
				$html .= "<input $atts type='$type' placeholder='$placeholder' class='fca-lpc-input-$type fca-lpc-$name' name='fca_lpc[$name]' value='$value'>";
		}
	
	$html .= '</div>';
	
	return $html;
	
}

//SINGLE-SELECT
function fca_lpc_select( $name, $selected = '', $options = array() ) {
	$html = "<div class='fca-lpc-field fca-lpc-field-select'>";
		$html .= "<select name='fca_lpc[$name]' class='fca-lpc-input-select fca-lpc-$name'>";
			if ( empty( $options ) && !empty ( $selected ) ) {
				$html .= "<option selected='selected' value='$selected'>" . __('Loading...', 'landing-page-cat') . "</option>";
			} else {
				forEach ( $options as $key => $text ) {
					$sel = $selected === $key ? 'selected="selected"' : '';
					$html .= "<option $sel value='$key'>$text</option>";
				}
			}
		$html .= '</select>';
	$html .= '</div>';
	
	return $html;
}


function fca_lpc_get_redirect_links () {

	$redirect_links = array( 
		'home' => __( 'Home Page', 'landing-page-cat' ),
		'blog' => __( 'Blog Page', 'landing-page-cat' ),
		'original' => __( 'Original Page (Welcome Gate)', 'landing-page-cat' ),
	);

	forEach ( get_pages() as $p ) {
		$redirect_links['p=' . $p->ID] = __('Page', 'landing-page-cat') . " $p->ID - " . get_the_title( $p->ID );
	}

	forEach ( get_posts( array('posts_per_page' => -1 ) ) as $p ) {
		$redirect_links['p=' . $p->ID] = __('Post', 'landing-page-cat') . " $p->ID - " . get_the_title( $p->ID );
	}

	return $redirect_links;

}


//LOADING SPINNER
function fca_lpc_spinner() {
	return "<span style='display: none;' class='fca_lpc_icon dashicons dashicons-image-rotate fca_lpc_spin'></span>";
}

//INFO SPAN
function fca_lpc_info_span( $text = '', $link = '' ) {
	if ( empty( $link ) ) {
		return "<span class='fca_lpc_info_span'>$text</span>";
	} else {
		return "<span class='fca_lpc_info_span'><a class='fca_lpc_api_link' href='$link' target='_blank'>$text</a></span>";
	}
}

function fca_lpc_sanitize_text_array( $array ) {
	if ( !is_array( $array ) ) {
		return sanitize_text_field ( $array );
	}
	foreach ( $array as $key => &$value ) {
		if ( is_array( $value ) ) {
			$value = fca_sp_sanitize_text_array( $value );
		} else {
			$value = sanitize_text_field( $value );
		}
	}

	return $array;
}

function fca_lpc_do_tag_div ( $provider, $tags ) {
	$html = "<tr class='fca_lpc_" . $provider . "_setting_row fca_lpc_" . $provider . "_api_settings' style='display:none'>";

		$html .= "<th>";
			$html .= "<label class='fca_lpc_admin_label fca_lpc_admin_settings_label' for='fca_lpc_" . $provider . "_tag_input'>" . __('Tags (Optional)', 'quiz-cat') . "</label>";
		$html .= "</th>";
		
		$html .= "<td>";
			$html .= "<input type='text' class='fca_lpc_input_wide fca_lpc_tag_input' id='fca_lpc_" . $provider . "_tag_input' name='fca_lpc_" . $provider . "_tag_input'><button class='button button-secondary fca_lpc_add_tag_btn' type='button' id='" . $provider . "_add_tag' >" . __( 'Add', 'quiz-cat' ) . "</button><br>";
			$html .= "<input type='hidden' id='" . $provider . "_tag_hidden_input' class='fca_lpc_tag_hidden_input' name='fca_lpc_" . $provider . "_tags' value='$tags'>";
			$html .= '<div id="' . $provider . '-tag-div" class="tag-div"></div>';
			$html .= "<p class='fca_lpc_tag_info'><em>" . __( 'Add one or more tags, separated by commas.', 'quiz-cat' ) . "</em></p>";
		$html .= "</td>";
	
	$html .= "</tr>";	
	
	return $html;
}