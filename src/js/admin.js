jQuery(document).ready(function($) {

	$( ".genesis_simple_share_general_size, .genesis_simple_share_general_appearance" ).change( function() {
		
		var size = "share-" + $( ".genesis_simple_share_general_size" ).val();
		var appearance = "share-" + $( ".genesis_simple_share_general_appearance" ).val();
		
		$( "#share-preview-preview" ).removeClass();
		$( "#share-preview-preview" ).addClass( "share-preview " + size + " " + appearance );
		
	} );
	
	$( '.genesis_simple_share_general_disable_count' ).change( function() {
		
		if ( $(this).prop('checked') ) {
			$( '.share-preview .box' ).addClass( 'no-count' );
		} else {
			$( '.share-preview .sharrre:not(.twitter, .googlePlus ) .box' ).removeClass( 'no-count' );
		}
		
	} );
	
	setTimeout( function(){
		if ( $('.genesis_simple_share_general_disable_count').prop('checked') ) {
			$( '.share-preview .box' ).addClass( 'no-count' );
		} else {
			$( '.share-preview .sharrre:not(.twitter, .googlePlus ) .box' ).removeClass( 'no-count' );
		}
	}, 2000 );

} );
