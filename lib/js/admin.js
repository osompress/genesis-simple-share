jQuery( function( $ ){

	$( ".genesis_simple_share_general_size, .genesis_simple_share_general_appearance" ).change( function() {
		
		var size = "share-" + $( ".genesis_simple_share_general_size" ).val();
		var appearance = "share-" + $( ".genesis_simple_share_general_appearance" ).val();
		
		$( "#share-preview-" ).hide();
		
		$( "#share-preview-" ).removeClass();
		$( "#share-preview-" ).addClass( "share-preview " + size + " " + appearance );
		
		$( "#share-preview-" ).fadeIn();
		
	} );

} );