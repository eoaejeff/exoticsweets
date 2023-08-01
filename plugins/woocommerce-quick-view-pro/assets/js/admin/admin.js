( function( $, window, document, undefined ) {
    'use strict';

    $( document ).ready( function() {
        var $galleryStyleSetting = $( '#wc_quick_view_pro_settings\\[gallery_style\\]' ).closest( 'tr' );

        // Show/hide gallery thumbnail style depending on checkbox.
        $( '#wc_quick_view_pro_settings\\[enable_gallery\\]' ).change( function() {
            $galleryStyleSetting.toggle( $( this ).prop( 'checked' ) );
        } ).trigger( 'change' );

        var $productDetailsSetting = $( '#quick_view_settings input.product-details' ).closest( 'tr' ),
            $productImageSetting = $( '#quick_view_settings input.product-image' ).closest( 'tr' );

        // Show/hide lightbox settings style depending on information displayed.
        $( '#wc_quick_view_pro_settings\\[display_type\\]' ).change( function() {
            var type = $( this ).val();

            $productDetailsSetting.toggle( ( 'image_only' !== type ) );
            $productImageSetting.toggle( ( 'details_only' !== type ) );
        } ).trigger( 'change' );
    } );

} )( jQuery, window, document );