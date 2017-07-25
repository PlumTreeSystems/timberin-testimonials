function initMap() {
    var lat = jQuery('#tt_coord_lat');
    var long = jQuery('#tt_coord_long');

    var center = {lat: -25.363, lng: 131.044};
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 4,
        center: center
    });
    var marker = new google.maps.Marker({
        position: center,
        map: map,
        draggable:true
    });

    function changeLocation(location){
        marker.setPosition(location);
        map.setCenter(location);
        lat.val(location.lat);
        long.val(location.lng);
    }


    var geocoder = new google.maps.Geocoder;
    function resolve_address(address){
        geocoder.geocode({'address': address}, function (res, status) {
            if(status === 'OK'){
                var location = res[0].geometry.location;
                changeLocation(location);
            }
        });
    }
    var address_input = jQuery('#tt_address');

    if (lat.val() && long.val()){
        changeLocation({'lat' : parseFloat(lat.val()), 'lng' : parseFloat(long.val())})
    }else{
        resolve_address(address_input.val());
    }
    address_input.change(function (){
        var address = jQuery(this).val();
        resolve_address(address);
    });
    google.maps.event.addListener(marker, 'dragend', function(){
        lat.val(marker.getPosition().lat);
        long.val(marker.getPosition().lng);
    });

}
jQuery( document ).ready( function( $ ) {
    var file_frame;
    var image_attachment = $( '#image_attachment_id' );
    if(!image_attachment.length){
        return;
    }
    var set_to_post_id = image_attachment.val(); // Set this

    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    // Uploading files

    jQuery('#upload_image_button').on('click', function( event ){
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

            // Do something with attachment.id and/or attachment.url here
            $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
            image_attachment.val( attachment.id );
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
});
