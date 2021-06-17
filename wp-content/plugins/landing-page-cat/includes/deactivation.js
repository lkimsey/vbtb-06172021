/* jshint asi: true */
jQuery(document).ready(function($){
	
	var $deactivateButton = $('#the-list tr.active').filter( function() { return $(this).data('plugin') === 'landing-page-cat/landing-page-cat.php' } ).find('.deactivate a')
		
	$deactivateButton.click(function(e){
		e.preventDefault()
		$deactivateButton.unbind('click')
		$('body').append(fca_lpc.html)
		fca_lpc_uninstall_button_handlers( $deactivateButton.attr('href') )
		
	})
}) 

function fca_lpc_uninstall_button_handlers( url ) {
	var $ = jQuery
	$('#fca-lpc-deactivate-skip').click(function(){
		$(this).prop( 'disabled', true )
		window.location.href = url
	})
	$('#fca-lpc-deactivate-send').click(function(){
		$(this).prop( 'disabled', true )
		$(this).html('...')
		$('#fca-lpc-deactivate-skip').hide()
		$.ajax({
			url: fca_lpc.ajaxurl,
			type: 'POST',
			data: {
				"action": "fca_lpc_uninstall",
				"nonce": fca_lpc.nonce,
				"msg": $('#fca-lpc-deactivate-textarea').val()
			}
		}).done( function( response ) {
			console.log ( response )
			window.location.href = url			
		})	
	})
	
}