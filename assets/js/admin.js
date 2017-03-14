
//rva_admin = '';

var rva_admin = (function($) {
	'use strict';
	var self = {

	'bind_media_picker' : function ( upload_btn = '#upload_image_button', preview = '', save_field = '') {
			// Uploading files
			var upload_btn_select = upload_btn;
            var img_field = save_field; //"#subscribe_magazine_image";
			var img_preview = preview;
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = 0; // Set this
			jQuery(upload_btn_select).on('click', function( event ){
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					var attachment = file_frame.state().get('selection').first().toJSON();
					console.log(attachment);
					// Do something with attachment.id and/or attachment.url here
					$( img_preview ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( img_field ).val( attachment.id );
                    
					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					// Finally, open the modal
					file_frame.open();
			});
			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		}
}

return self;
//rva_admin.display_media_picker(upload_btn, save_field);
})( window.jQuery );


