/* jshint asi: true */

//////////////////
//WYSIWYG EDITOR
//////////////////


jQuery(document).ready(function($){
	fca_attach_wysiwyg()
})

function detectIE() {
	var ua = window.navigator.userAgent
	var msie = ua.indexOf('MSIE ')
	if (msie > 0) {
		// IE 10 or older
		return true
	}
	var trident = ua.indexOf('Trident/')
	if (trident > 0) {
		// IE 11
		return true
	}
	
	if (document.documentMode || /Edge/.test(navigator.userAgent)) {
		//EDGE
		return true
	}
	// other browser
	return false
}
var usingIE = detectIE()

var wysihtmlParserRules = {
	"classes": {
		"wysiwyg-text-align-center": 1,
		"wysiwyg-text-align-left": 1,
		"wysiwyg-text-align-right": 1
	},
	"attributes": {
		"style": "any",
		"class": "any",
		"data-*": "any",
		"target": "any",
		"rel": "any",
	},
	
	"tags": {
		"strong":	1,
		"b":		1,
		"i":		1,
		"u":		1,
		"div":		1,
		"span":		1,
		"ul":		1,
		"li":		1,
		"ol":		1,
		"p":		1,
		"br":		1,
		"a": {
			"check_attributes": {
				"href":	"href", // important to avoid XSS
			}
		},
		"img": {
			"check_attributes": {
				"alt": "alt",
				"src": "url"
			}
		},
		"iframe": {
            "check_attributes": {
                "src": "url",
				"width": "numbers",
				"height": "numbers",
				"frameborder": "numbers",
				
            }
        },
	}
}
if ( fcaLpcAdminData.editor === 'simple' ) {
	wysihtmlParserRules = {
		"attributes": {
			"style": "any",
			"class": "any",
			"data-*": "any",
			"target": "any"
		},
		
		"tags": {
			"strong":	1,
			"b":		1,
			"i":		1,
			"u":		1,
			"div":		1,
			"span":		1,
			"ul":		1,
			"li":		1,
			"ol":		1,
			"p":		1,
			"br":		1,
			"a": {
				"check_attributes": {
					"href":	"url", // important to avoid XSS
				},
				"set_attributes": {
					"target": "_blank"
				}
			}
		}
	}
}
//console.log( wysihtmlParserRules )
function fca_attach_wysiwyg() {
	var $ = jQuery
	if ( !usingIE ) {
		$('.fca-wysiwyg-html').not('.editorActive').each(function (index, element) {
			var editor = new wysihtml5.Editor( element, { // element
				toolbar:	  $(element).siblings('.fca-wysiwyg-nav')[0], // toolbar element
				parserRules:  wysihtmlParserRules, // defined in parser rules set
				stylesheets: [fcaLpcAdminData.stylesheet],
				useLineBreaks:  false
			})
			$(element).siblings('.fca-wysiwyg-nav').find('.fca-wysiwyg-view-html').click(function(){
				$(this).siblings('.fca-wysiwyg-group').toggle()
			})
			$(element).siblings('.fca-wysiwyg-nav').find('.fca-wysiwyg-insert-image').click(function(e){
				e.stopPropagation()
				var wpmedia = wp.media( {
					frame: "post",
					multiple: false
				}).open()
					.on('insert', function(){
						//GET VALUE FROM WP MEDIA UPLOAD THING
						var image = wpmedia.state().get('selection').first()
						var display = wpmedia.state().display( image ).toJSON()
						image = image.toJSON()
						//Do something with attachment.id and/or attachment.url here
						var imgurl = image.sizes[display.size].url
			
						//ASSIGN VALUE
						if ( imgurl ) {
							editor.composer.commands.exec( "insertImage", { 
								src: imgurl,
								alt: image.caption
							})		
						}					
					})
			})
			$(element).addClass('editorActive')
			
		})
	} else {
		//DISABLE FOR IE
		$('.fca-wysiwyg-html').not('.editorActive').each(function (index, element) {
			$(element).addClass('editorActive')
			$(element).siblings('.fca-wysiwyg-group').hide()
		})
		
	}
}
