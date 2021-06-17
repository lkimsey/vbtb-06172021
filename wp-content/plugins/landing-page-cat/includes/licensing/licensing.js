jQuery( document ).ready(function($){
	//GDPR TOGGLE
	$('#fca_lpc\\[fca_lpc_gdpr_checkbox\\]').change( function(e){
		
		var $thisTable = $(this).closest('table')
		if ( this.checked ) {
			$thisTable.find('tr.gdpr-setting').show()
		} else {
			$thisTable.find('tr.gdpr-setting').hide()
		}
	}).change()
	
	$('#fca_lpc_settings_form').submit( function(e){
		if( $('#fca_lpc\\[fca_lpc_gdpr_checkbox\\]').attr('checked') && $('#fca_lpc_consent_msg').val() == false  ) {
			alert( 'GDPR checkbox is enabled but the consent statement is blank.  Please add a consent statement to enable this feature.' )
			return false			
		} else {
			return true
		}
		
	})
	
	$("#fca_lpc_settings_form").show()

})