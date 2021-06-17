/* jshint asi: true */
jQuery(document).ready(function($){

	//PAGE LISTING SELECT2
	$('.fca_lpc_select2').select2()		

	if ( $('.fca-lpc-custom_css').length > 0 ) {
		wp.codeEditor.initialize( $( '.fca-lpc-custom_css' ), fcaLpcData.code_editor )
	}

	//SET DATEPICKER
	$('.fca-lpc-countdown_date').datetimepicker()
	
	//DEPLOY MODE TOGGLE
	var LPCsettingsFirstRun = true

	$('.fca-lpc-deploy_mode').change(function(){
		
		$('.fca_lpc_select2').select2('destroy')
		$('.fca_lpc_original_post_option').attr('disabled', true)
		$('.fca-lpc-show-skip, #fca-lpc-redirect-url-input, #fca_pc_welcome_gate_settings').hide()
						
		//SWITCH MODE OFF WELCOME GATE UNLESS IT'S A WELCOME GATE
		if ( $(this).val()  !== 'welcome' ) {
			$('.fca_lpc_select2').each(function(){
				if ( $(this).val() === 'original' || !$(this).val()  ) {
					$(this).val('home').change()
				}
			})
		}
	
		//UNCHECK SHOW SKIP
		if ( $(this).val() !== 'welcome' ) {
			$('.fca-lpc-show_skip').attr( 'checked', false ).change()
		}
				
		switch ( $(this).val() ) {
			case 'homepage':
				break;
			case 'url':
				$('#fca-lpc-redirect-url-input').show()
				break;
			case 'four_o_four':
				break;
			case 'welcome':
				if ( LPCsettingsFirstRun === false ) {
					$('.fca_lpc_select2').val('original').change()
				}
				$('#fca_pc_welcome_gate_settings, .fca-lpc-show-skip').show()
				$('.fca_lpc_original_post_option').attr('disabled', false)
				break;
		}
		$('.fca_lpc_select2').select2()
		LPCsettingsFirstRun = false
	}).change()

	//COUNTDOWN TIMER TOGGLE
	$('.fca-lpc-countdown_enabled').change(function(){
		$('.countdown_settings').toggle( this.checked )
	}).change()

	//HEADER LOGO TOGGLE
	$('.fca-lpc-header_enabled').change(function(){
		$('.fca-lpc-settings-header').toggle( this.checked )
	}).change()

	//HEADER BUTTON REDIRECT MODE / PAGE TOGGLE
	$('.fca-lpc-header_mode').change(function(){
		if ( $(this).val() === 'url' ) {
			$('#fca_lpc_header_url_input').show()
			$('#fca_lpc_header_page_list_input').hide()
		} else {
			$('#fca_lpc_header_url_input').hide()
			$('#fca_lpc_header_page_list_input').show()
		}
	}).change()	

	//SKIP LINK TOGGLE
	$('.fca-lpc-show_skip').change(function(){
		$('.fca-lpc-skip-settings').toggle( this.checked )
	}).change()
	
	//COOKIE TOGGLE
	$('.fca-lpc-success_cookie').change(function(){
		$('#fca-lpc-cookie-duration-row').toggle( this.checked )
	}).change()

	//CALL TO ACTION TOGGLE
	$('.fca-lpc-call_to_action').change(function(e){
		$('.fca-lpc-settings-optin, .fca-lpc-settings-button' ).hide()
		$('.fca-lpc-settings-' + $(this).val() ).show()
		if ( $(this).val() === 'button' && $('.fca-lpc-button_copy').val() === 'Subscribe' ) {
			$('.fca-lpc-button_copy').val('Learn More')
		}
		if ( $(this).val() === 'optin' && $('.fca-lpc-button_copy').val() === 'Learn More' ) {
			$('.fca-lpc-button_copy').val('Subscribe')
			$('.fca-lpc-settings-redirect' ).hide()
		}

		$('.fca-lpc-show_name').change()
	}).change()
	
	//NAME TOGGLE
	$('.fca-lpc-show_name').change(function(){
		//UNCHECK NAME TOGGLE FOR BUTTONS & NO OPTINS
		if ( $('.fca-lpc-call_to_action').val() === 'none' || $('.fca-lpc-call_to_action').val() === 'button' ) {
			$(this).attr('checked', false)
		}
		$('#fca-lpc-name-placeholder').toggle( this.checked )
	}).change()
	
	//OPTIN SUCCESS MODE TOGGLE
	$('.fca-lpc-success_redirect').change(function(){
		if ( $(this).val() === 'redirect' ) {
			$('#fca_lpc_thank_you_msg_row').hide()
			$('#fca_lpc_thank_you_redirect_row').show()
		} else {
			$('#fca_lpc_thank_you_msg_row').show()
			$('#fca_lpc_thank_you_redirect_row').hide()
		}
	}).change()
	
	$('.fca-lpc-success_mode').change(function(){
		if ( $(this).val() === 'url' ) {
			$('.fca-lpc-success_url').parent().show()
			$('.fca-lpc-success_post').parent().hide()
		} else {
			$('.fca-lpc-success_url').parent().hide()
			$('.fca-lpc-success_post').parent().show()
		}
	}).change()

	//BUTTON REDIRECT MODE / PAGE TOGGLE
	$('.fca-lpc-button_mode').change(function(){
		if ( $(this).val() === 'url' ) {
			$('.fca-lpc-button_url').show()
			$('.fca-lpc-button_post').hide()
		} else {
			$('.fca-lpc-button_url').hide()
			$('.fca-lpc-button_post').show()
		}
	}).change()	
	
	//PARTICLE ON/OFF TOGGLE
	$('.fca-lpc-particles_enabled').change(function(){
		$('.fca-lpc-particles-setting').toggle( this.checked )
	}).change()
	
	//ACTIVATE COLOR PICKERS
	$('.fca-lpc-input-color').wpColorPicker({
		palettes: [ '#000', '#fff', '#42d0e1', '#fdd01f', '#ecf0f1', '#0ff1a3', '#ffe26e', '#99d7d9', '#e74c3c' ],
		change: function (event, ui) {
			if ( $( event.target ).hasClass('fca-lpc-bg_color') ) {
				$('.fca-bg-selected').removeClass('fca-bg-selected')
				$('#fca-lpc-bg-none').addClass('fca-bg-selected')
				$('#fca-lpc-bg').val( '' )
			}
		}
	})
	
	//NAV
	$('.nav-tab-wrapper a').click(function(e){
		e.preventDefault()
		$('.nav-tab-wrapper a').removeClass('nav-tab-active')
		$(this).addClass('nav-tab-active').blur()
		$('#normal-sortables').children().hide()
		$( $(this).data('target') ).show()

		//HIDE THE SETTINGS BASED ON CALL TO ACTION
		if ( $('.fca-lpc-call_to_action').val() === 'button'  ) {
			$('#fca_lpc_landing_page_settings_meta_box, #fca_lpc_landing_page_settings_text_meta_box').hide()
		}
		if ( $('.fca-lpc-call_to_action').val() === 'none' ) {
			$('#fca_lpc_landing_page_settings_meta_box, #fca_lpc_landing_page_settings_text_meta_box, #fca_lpc_google_analytics_meta_box, #fca_lpc_landing_page_cta_meta_box').hide()
		}
	}).first().click()
	
	//BG SELECTOR
	$('.fca-lpc-bg-item').click(function(){
		//SET NEW
		$('#fca-lpc-bg').val( $(this).find('img').attr('src') )
		
		if ( !$(this).hasClass('fca-bg-selected') ) {
			//STYLE
			$('#fca-lpc-image-opacity-slider').val(0).trigger('input')
			$('.fca-bg-selected').removeClass('fca-bg-selected')
			$(this).addClass('fca-bg-selected')
			$('#fca-lpc-image-opacity-slider').val(0.60).trigger('input')
		}
		if ( $(this).attr('id') === 'fca-lpc-bg-none' ) {
			$('#fca-lpc-custom-image-opacity-row').css('visibility', 'hidden')
		} else {
			$('#fca-lpc-custom-image-opacity-row').css('visibility', 'visible')
		}
	})
	//SET INITIAL OPACITY SELECTOR VISIBILITY
	if ( $('.fca-bg-selected').attr('id') === 'fca-lpc-bg-none' ) {
		$('#fca-lpc-custom-image-opacity-row').css('visibility', 'hidden')
	} else {
		$('#fca-lpc-custom-image-opacity-row').css('visibility', 'visible')
	}

	// LOGO UPLOAD
	$('.fca-lpc-logo-upload-btn, .fca-lpc-logo-change-btn').click(function(e) {
		
		e.preventDefault()
		var $this = $(this)
		var $mainDiv = $(this).closest('.fca-lpc-logo-upload')
		var image = wp.media().open()
		.on('select', function(){
			//GET VALUE FROM WP MEDIA UPLOAD THING
			var image_url = image.state().get('selection').first().toJSON().url
			//ASSIGN VALUE
			$mainDiv.find('.fca-lpc-input-logo').attr('value', image_url)
			$mainDiv.find('.fca-lpc-custom-logo').attr('src',image_url)
			$('#fca-lpc-logo').val( image_url )
			
			//SET VISIBILITY
			$mainDiv.find('.fca-lpc-logo-upload-btn').hide()
			$mainDiv.find('.fca-lpc-logo-item').show()
			$('#fca-lpc-logo-upload').css('border', '0')

			//ADD SPRINKLES
			$('.fca-logo-selected').removeClass('fca-logo-selected')
			$('#fca-lpc-logo-upload').addClass('fca-logo-selected')
		})
	})
	
	//ACTION WHEN CLICKING REMOVE LOGO
	$('.fca-lpc-logo-revert-btn').click( function(e) {
		var $mainDiv = $(this).closest('.fca-lpc-logo-upload')
		
		$mainDiv.find('.fca-lpc-input-logo').attr('value', '')
		$mainDiv.find('.fca-lpc-custom-logo').attr('src', '' )
		$mainDiv.find('.fca-lpc-logo-upload-btn').show()
		$mainDiv.find('.fca-lpc-logo-item').hide()
		
		if ( $mainDiv.find('.fca-lpc-logo-item').hasClass('fca-logo-selected') ) {
			$('.fca-lpc-logo-item').first().click()
		}
		
		$('#fca-lpc-logo-upload').css('background-color', '#fff')
		$('#fca-lpc-logo-upload').css('border', '1px solid #ccc')
		
	})
	
	//HIDE "ADD IMAGE" BUTTONS IF LOGO HAS BEEN SET
	$('.fca-lpc-custom-logo').each( function( index ){
		if ( $(this).attr('src') !== '' ) {
			$(this).siblings('.fca-lpc-logo-upload-btn').hide()
			$('#fca-lpc-logo-upload').css('border', '0')
		}
	})

	// MEDIA UPLOAD
	$('.fca-lpc-image-upload-btn, .fca-lpc-image-change-btn').click(function(e) {
		
		e.preventDefault()
		var $this = $(this)
		var $mainDiv = $(this).closest('.fca-lpc-image-upload')
		var image = wp.media().open()
		.on('select', function(){
			//GET VALUE FROM WP MEDIA UPLOAD THING
			var image_url = image.state().get('selection').first().toJSON().url
			//ASSIGN VALUE
			$mainDiv.find('.fca-lpc-input-image').attr('value', image_url)
			$mainDiv.find('.fca-lpc-custom-image').attr('src',image_url)
			$('#fca-lpc-bg').val( image_url )
			
			//SET VISIBILITY
			$mainDiv.find('.fca-lpc-image-upload-btn').hide()
			$mainDiv.find('.fca-lpc-bg-item').show()
			$('#fca-lpc-bg-upload').css('background-color', '#2d2d2d')
			$('#fca-lpc-bg-upload').css('border', '0')

			//ADD SPRINKLES
			$('.fca-bg-selected').removeClass('fca-bg-selected')
			$('#fca-lpc-bg-upload').addClass('fca-bg-selected')
		})
	})
	
	//ACTION WHEN CLICKING REMOVE IMAGE
	$('.fca-lpc-image-revert-btn').click( function(e) {
		var $mainDiv = $(this).closest('.fca-lpc-image-upload')
		
		$mainDiv.find('.fca-lpc-input-image').attr('value', '')
		$mainDiv.find('.fca-lpc-custom-image').attr('src', '' )
		$mainDiv.find('.fca-lpc-image-upload-btn').show()
		$mainDiv.find('.fca-lpc-bg-item').hide()
		
		if ( $mainDiv.find('.fca-lpc-bg-item').hasClass('fca-bg-selected') ) {
			$('.fca-lpc-bg-item').first().click()
		}
		
		$('#fca-lpc-bg-upload').css('background-color', '#fff')
		$('#fca-lpc-bg-upload').css('border', '1px solid #ccc')
		
	})
	
	//HIDE "ADD IMAGE" BUTTONS IF IMAGE HAS BEEN SET
	$('.fca-lpc-custom-image').each( function( index ){
		if ( $(this).attr('src') !== '' ) {
			$(this).siblings('.fca-lpc-image-upload-btn').hide()
			$('#fca-lpc-bg-upload').css('border', '0')
			$('#fca-lpc-bg-upload').css('background-color', '#2d2d2d')
		}
	})
	
	//IMAGE OPACITY SLIDER
	$('#fca-lpc-image-opacity-slider').on( 'input', function(){
		$('.fca-bg-selected').find('img').css( 'opacity', 1 - $(this).val() )
	}).trigger('input')
	
	//REDIRECT MODE TOGGLE
	$('.fca-lpc-welcome_redirect_mode').change(function(){
		if ( $(this).val() === 'all' ) {
			$('.fca-lpc-welcome-url-exclude, .fca-lpc-welcome-search-exclude-option').show()
			$('.fca-lpc-welcome-url-include').hide()
		} else {
			$('.fca-lpc-welcome-url-exclude, .fca-lpc-welcome-search-exclude-option').hide()
			$('.fca-lpc-welcome-url-include').show()
		}
	}).change()
	
	//ADD RULE BUTTON
	$('#fca_lpc_add_welcome_rule').click(function(){
		if ( $('.fca-lpc-welcome_redirect_mode').val() === 'all' ) {
			$('#fca-lpc-url-rule-list').append( fcaLpcTemplateData.exclude_url )
		} else {
			$('#fca-lpc-url-rule-list').append( fcaLpcTemplateData.include_url )
		}
		add_rule_event_handler()
	})	
	
	function add_rule_event_handler() {
	
		$('.fca_delete_button').unbind( 'click' )
		$('.fca_delete_button').click( function(){
			$( this ).closest( '.fca_deletable_item' ).hide( 'fast', function() {
				$( this ).remove()
			})
			
		})
	}
	add_rule_event_handler()
	
	//NO PROVIDER SET NAG
	$('#fca-lpc-no-list-cta').click(function(e){
		e.preventDefault()
		$('#nav-tab-optin').click()
		$('#fca_lpc_landing_page_settings_meta_box').css('outline', '2px solid #dc3232')
		window.setTimeout( function(){ $('#fca_lpc_landing_page_settings_meta_box').css('outline', '0') }, 2200)
		
	})
	
	//SET UP SAVE AND PREVIEW BUTTONS, THEN HIDE THE PUBLISHING METABOX
	var saveButton = '<button type="submit" class="button-primary" id="fca_lpc_submit_button">Save</buttton>'
	var previewButton = '<button type="button" class="button-secondary" id="fca_lpc_preview_button">Save & Preview</buttton>'

	$('#poststuff').append( saveButton )
	$('#fca_lpc_submit_button').click( function( e ) {
		$(window).unbind('beforeunload')
		e.preventDefault()
		
		// Add target
		var thisForm = $(this).closest('form')
		thisForm.removeAttr('target')

		// Remove preview url
		$('#fca_lpc_preview_url').val('')
		
		// Submit form
		thisForm.submit()
		
		return false
	})
	$('#poststuff').append( previewButton )
	$('#fca_lpc_preview_button').click(function(e) {
		
		e.preventDefault()
		// Add target
		var thisForm = $(this).closest('form')
		thisForm.prop('target', '_blank')
					
		// Submit form
		thisForm.submit()

		return false
	})	
	$( '#submitdiv' ).hide()
	
	//MAILCHIMP API CHANGE EVENT
	$('.fca-lpc-mailchimp_key').bind('input', function(e){
		var $this_button = $(this)
		var api_key = $this_button.val()
		var delivery_center =  api_key.split('-')
		var provider = $('.fca-lpc-provider').val()
				
		if ( api_key !== '' && delivery_center[1] && provider === 'mailchimp' ) {
			//might be valid API key, lets check
			$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this_button.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						api_key: api_key,
						nonce: fcaLpcData.nonce,
						post_id: fcaLpcData.post_id,
						action: 'fca_lpc_get_mailchimp_lists'
					}
				}).done( function( returnedData ) {

					$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'mailchimp') {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this_button.data('validated', 1)
						$('.fca_lpc_mailchimp_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-mailchimp_list') )
						//update groups list
						$('.fca-lpc-mailchimp_list').change() 
						

					} else {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this_button.data('validated', 0)
						$('.fca_lpc_mailchimp_api_settings').hide()
					}
				})
			
		}
	})
	
	//MAD MIMI API CHANGE EVENT
	$('.fca-lpc-madmimi_key, .fca-lpc-madmimi_email').bind('input', function(e){
		var $this_button = $('.fca-lpc-madmimi_email')
		var api_key = $('.fca-lpc-madmimi_key').val()
		var email = $('.fca-lpc-madmimi_email').val()
		var provider =  $('.fca-lpc-provider').val()
				
		if ( email && api_key && provider === 'madmimi' ) {
			//might be valid API key, lets check
			$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this_button.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						'api_key': api_key,
						'email': email,
						'nonce': fcaLpcData.nonce,
						'post_id': fcaLpcData.post_id,
						'action': 'fca_lpc_get_madmimi_lists'
					}
				}).done( function( returnedData ) {

					$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'madmimi' ) {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this_button.data('validated', 1)
						$('.fca_lpc_madmimi_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-madmimi_list') )
					} else {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this_button.data('validated', 0)
						$('.fca_lpc_madmimi_api_settings').hide()
					}
				})
			
		}
	})
	
	//CONVERTKIT API CHANGE EVENT
	$('.fca-lpc-convertkit_key').bind('input', function(e){
		var $this = $(this)
		var api_key = $this.val()
		var provider =  $('.fca-lpc-provider').val()
				
		if ( api_key && provider === 'convertkit' ) {
			//might be valid API key, lets check
			$this.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						'api_key': api_key,
						'nonce': fcaLpcData.nonce,
						'post_id': fcaLpcData.post_id,
						'action': 'fca_lpc_get_convertkit_lists'
					}
				}).done( function( returnedData ) {

					$this.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'convertkit' ) {
						$this.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this.data('validated', 1)
						$('.fca_lpc_convertkit_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-convertkit_list') )
					} else {
						$this.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this.data('validated', 0)
						$('.fca_lpc_convertkit_api_settings').hide()
					}
				})
			
		}
	})
	
	//CAMPAIGN MONITOR API CHANGE EVENT
	$('.fca-lpc-campaignmonitor_key, .fca-lpc-campaignmonitor_id').bind('input', function(e){
		var $this_button = $('.fca-lpc-campaignmonitor_id')
		var api_key = $('.fca-lpc-campaignmonitor_key').val()
		var campaignmonitor_id = $('.fca-lpc-campaignmonitor_id').val()
		var provider =  $('.fca-lpc-provider').val()
				
		if ( api_key && campaignmonitor_id && provider === 'campaignmonitor' ) {
			//might be valid API key, lets check
			$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this_button.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						'api_key': api_key,
						'client_id': campaignmonitor_id,
						'nonce': fcaLpcData.nonce,
						'post_id': fcaLpcData.post_id,
						'action': 'fca_lpc_get_campaignmonitor_lists'
					}
				}).done( function( returnedData ) {

					$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'campaignmonitor' ) {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this_button.data('validated', 1)
						$('.fca_lpc_campaignmonitor_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-campaignmonitor_list') )
					} else {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this_button.data('validated', 0)
						$('.fca_lpc_campaignmonitor_api_settings').hide()
					}
				})
			
		}
	})
	
	//GETRESPONSE API CHANGE EVENT
	$('.fca-lpc-getresponse_key').bind('input', function(e){
		var $this_button = $(this)
		var api_key = $this_button.val()
		var provider =  $('.fca-lpc-provider').val()
				
		if ( api_key !== '' && provider === 'getresponse' ) {
			//might be valid API key, lets check
			$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this_button.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						api_key: api_key,
						nonce: fcaLpcData.nonce,
						post_id: fcaLpcData.post_id,
						action: 'fca_lpc_get_getresponse_lists'
					}
				}).done( function( returnedData ) {

					$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'getresponse' ) {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this_button.data('validated', 1)
						$('.fca_lpc_getresponse_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-getresponse_list') )
					} else {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this_button.data('validated', 0)
						$('.fca_lpc_getresponse_api_settings').hide()
					}
				})
			
		}
	})
	
	//AWEBER API CHANGE EVENT
	$('.fca-lpc-aweber_key').bind('input', function(e){
		var $this_button = $(this)
		var api_key = $this_button.val()
		var provider =  $('.fca-lpc-provider').val()
				
		if ( api_key !== '' && provider === 'aweber'  ) {
			//might be valid API key, lets check
			$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this_button.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						api_key: api_key,
						nonce: fcaLpcData.nonce,
						post_id: fcaLpcData.post_id,
						action: 'fca_lpc_get_aweber_lists'
					}
				}).done( function( returnedData ) {

					$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'aweber' ) {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this_button.data('validated', 1)
						$('.fca_lpc_aweber_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-aweber_list') )
					} else {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this_button.data('validated', 0)
						$('.fca_lpc_aweber_api_settings').hide()
					}
				})
			
		}
	})
	
	//ACTIVECAMPAIGN API CHANGE EVENT
	$('.fca-lpc-activecampaign_url, .fca-lpc-activecampaign_key').bind('input', function(e){
		var $this_button = $('.fca-lpc-activecampaign_url')
		var api_key = $('.fca-lpc-activecampaign_key').val()
		var api_url = $this_button.val()
		var provider =  $('.fca-lpc-provider').val()
				
		if ( api_key !== '' && api_url !== '' && provider === 'activecampaign'  ) {
			//might be valid API key, lets check
			$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this_button.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						api_url: api_url,
						api_key: api_key,
						nonce: fcaLpcData.nonce,
						post_id: fcaLpcData.post_id,
						action: 'fca_lpc_get_activecampaign_lists'
					}
				}).done( function( returnedData ) {

					$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'activecampaign' ) {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this_button.data('validated', 1)
						$('.fca_lpc_activecampaign_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-activecampaign_list') )
					} else {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this_button.data('validated', 0)
						$('.fca_lpc_activecampaign_api_settings').hide()
					}
				})
			
		}
	})
	
	//DRIP API CHANGE EVENT
	$('.fca-lpc-drip_id, .fca-lpc-drip_key').bind('input', function(e){
		var $this_button = $('.fca-lpc-drip_id')
		var client_id = $this_button.val()
		var api_key = $('.fca-lpc-drip_key').val()
		var provider =  $('.fca-lpc-provider').val()
		
		if ( api_key !== '' && client_id !== '' && provider === 'drip'  ) {
			//might be valid API key, lets check
			$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-no dashicons-yes')
			$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-image-rotate fca_lpc_spin')
			$this_button.closest('tr').find('.fca_lpc_icon').css('display', 'inline-block')
			
			$.ajax({
					url: fcaLpcData.ajaxurl,
					type: 'POST',
					data: {
						client_id: client_id,
						api_key: api_key,
						nonce: fcaLpcData.nonce,
						post_id: fcaLpcData.post_id,
						action: 'fca_lpc_get_drip_lists'
					}
				}).done( function( returnedData ) {

					$this_button.closest('tr').find('.fca_lpc_icon').removeClass('dashicons-image-rotate fca_lpc_spin')
										
					if ( returnedData.success && $('.fca-lpc-provider').val() === 'drip' ) {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-yes')
						$this_button.data('validated', 1)
						$('.fca_lpc_drip_api_settings').show('fast')
						update_lists( returnedData.data, $('.fca-lpc-drip_list') )
					} else {
						$this_button.closest('tr').find('.fca_lpc_icon').addClass('dashicons-no')
						$this_button.data('validated', 0)
						$('.fca_lpc_drip_api_settings').hide()
					}
				})
			
		}
	})
	
	//PROVIDER CHANGE EVENT - HIDE UNSELECTED ONES
	$('.fca-lpc-provider').change(function(e){
		
		$('.fca_lpc_mailchimp_setting_row').hide()
		$('.fca_lpc_zapier_setting_row').hide()
		$('.fca_lpc_getresponse_setting_row').hide()
		$('.fca_lpc_aweber_setting_row').hide()
		$('.fca_lpc_activecampaign_setting_row').hide()
		$('.fca_lpc_drip_setting_row').hide()
		$('.fca_lpc_localwp_setting_row').hide()
		$('.fca_lpc_madmimi_setting_row').hide()
		$('.fca_lpc_convertkit_setting_row').hide()
		$('.fca_lpc_campaignmonitor_setting_row').hide()
		
		switch ( $(this).val() ) {
			
			case 'mailchimp':
				$('.fca_lpc_mailchimp_setting_row').show('fast')
				$('.fca-lpc-mailchimp_key').trigger('input')
				if ( $('.fca-lpc-mailchimp_key').data('validated') === 1 ) {
					$('.fca_lpc_mailchimp_api_settings').show()
				} else {
					$('.fca_lpc_mailchimp_api_settings').hide()
				}
				break;	
				
			case 'madmimi':
				$('.fca_lpc_madmimi_setting_row').show('fast')
				$('.fca-lpc-madmimi_key').trigger('input')
				if ( $('.fca-lpc-madmimi_key').data('validated') === 1 ) {
					$('.fca_lpc_madmimi_api_settings').show()
				} else {
					$('.fca_lpc_madmimi_api_settings').hide()
				}
				break;
				
			case 'convertkit':
				$('.fca_lpc_convertkit_setting_row').show('fast')
				$('.fca-lpc-convertkit_key').trigger('input')
				if ( $('.fca-lpc-convertkit_key').data('validated') === 1 ) {
					$('.fca_lpc_convertkit_api_settings').show()
				} else {
					$('.fca_lpc_convertkit_api_settings').hide()
				}
				break;
				
			case 'campaignmonitor':
				$('.fca_lpc_campaignmonitor_setting_row').show('fast')
				$('.fca-lpc-campaignmonitor_key').trigger('input')
				if ( $('.fca-lpc-campaignmonitor_key').data('validated') === 1 ) {
					$('.fca_lpc_campaignmonitor_api_settings').show()
				} else {
					$('.fca_lpc_campaignmonitor_api_settings').hide()
				}
				break;
			
			case 'zapier':
				$('.fca_lpc_zapier_setting_row').show('fast')
				break;
			
			case 'getresponse':
				$('.fca_lpc_getresponse_setting_row').show('fast')
				$('.fca-lpc-getresponse_key').trigger('input')
				if ( $('.fca-lpc-getresponse_key').data('validated') === 1 ) {
					$('.fca_lpc_getresponse_api_settings').show()
				} else {
					$('.fca_lpc_getresponse_api_settings').hide()
				}
				break;
			
			case 'aweber':
				$('.fca_lpc_aweber_setting_row').show('fast')
				$('.fca-lpc-aweber_key').trigger('input')
				if ( $('.fca-lpc-aweber_key').data('validated') === 1 ) {
					$('.fca_lpc_aweber_api_settings').show()
				} else {
					$('.fca_lpc_aweber_api_settings').hide()
				}
				break;
				
			case 'activecampaign':
				$('.fca_lpc_activecampaign_setting_row').show('fast')
				$('.fca-lpc-activecampaign_url').trigger('input')
				if ( $('.fca-lpc-activecampaign_url').data('validated') === 1 ) {
					$('.fca_lpc_activecampaign_api_settings').show()
				} else {
					$('.fca_lpc_activecampaign_api_settings').hide()
				}
				break;
				
			case 'drip':
				$('.fca_lpc_drip_setting_row').show('fast')
				$('.fca-lpc-drip_id').trigger('input')
				
				if ( $('.fca-lpc-drip_id').data('validated') === 1 ) {
					$('.fca_lpc_drip_api_settings').show()
				} else {
					$('.fca_lpc_drip_api_settings').hide()
				}
				break;
			
			case 'localwp':
				$('.fca_lpc_localwp_setting_row').show('fast')
				break;
		}
		
	}).change()
	
	//ADD/UPDATE MAILING LIST SELECT
	function update_lists( lists, $target ) {
		//lists = jQuery.parseJSON( lists )

		var selected = $target.val()
		if ( !selected ) {
			//set to 'not set' if its empty/undefined/etc
			selected = 'not-set'
		}
		$target.children('option').remove()
		
		for ( var i = 0; i < lists.length; i++ ) {
			var newOption = ''
			if ( selected.indexOf( lists[i].id ) !== -1 ) {
				newOption = "<option value='" + lists[i].id + "' selected='selected' >" + lists[i].name + "</option>";
			} else {
				newOption = "<option value='" + lists[i].id + "'>" + lists[i].name + "</option>";
			}
			$target.append(newOption)	
		}

	}
	
	// ACTIVATE TOOLTIPS
	$('.fca_lpc_tooltip').tooltipster( {trigger: 'hover', maxWidth: '100%', contentAsHTML: true, arrow: false, side: 'right', theme: ['tooltipster-borderless', 'tooltipster-landing-page-cat'] } )
	
	$('.empty-container').removeClass('empty-container')
	
	$('#side-sortables').sortable({
        disabled: true
    })

    $('.postbox .hndle').css('cursor', 'pointer')
	
	$('#postimagediv').hide()
	
	//SHOW OUR MAIN DIV AFTER WE'RE DONE WITH DOM CHANGES
	$( '#wpbody-content').show()

})