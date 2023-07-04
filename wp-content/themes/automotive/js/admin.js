(function($) {
  "use strict";

 jQuery(document).ready( function($){
	var file_frame;

	$(document).on("click", ".choose_image", function(e){
		e.preventDefault();

		// If the media frame already exists, reopen it.
	    if ( file_frame ) {
	      file_frame.open();
	      return;
	    }
	 
	    // Create the media frame.
	    file_frame = wp.media.frames.file_frame = wp.media({
	      	title: jQuery( this ).data( 'uploader-title' ),
	      	button: {
	        	text: jQuery( this ).data( 'uploader-button-text' ),
	      	},
	      	multiple: false,
	    	library : { type : 'image'},
	    });

	    // highlight selected image
	    file_frame.on('open', function(){
	    	var selection = file_frame.state().get('selection');

	    	var attachement = wp.media.attachment($(".header_image_input").data("id"));
	    	//attachement.fetch();
	    	selection.add( attachement ? [ attachement ] : [] );
	    });
	 
	    // Finally, open the modal
	    file_frame.open();	    

		file_frame.on( 'select', function() {	 
	    	var selection = file_frame.state().get('selection');
	 
	    	selection.map( function( attachment ) {	 
	      		attachment = attachment.toJSON();	 			
	 			$(".header_image_input").val(attachment.id);
	 			$(".header_image_input").data("id", attachment.id);

	 			if($(".header_preview_area img").length){
	 				$(".header_preview_area img").attr("src", attachment.url);
		    	} else {
		    		$(".header_preview_area").append("<img src='" + attachment.url + "' style='width: 100%; margin-top: 8px;'>");
		    	}
		    });

		});
	});
});
})(jQuery);