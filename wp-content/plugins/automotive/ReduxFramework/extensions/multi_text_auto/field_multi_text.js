/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.multi_text_auto  = redux.field_objects.multi_text_auto  || {};

    $( document ).ready(
        function() {
            redux.field_objects.multi_text_auto .init();
        }
    );

    redux.field_objects.multi_text_auto .init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.redux-container-multi_text_auto:visible' );
        }

        console.log(selector);

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }
                el.find( '.redux-multi-text-remove' ).live(
                    'click', function() {
                        redux_change( $( this ) );
                        $( this ).prev( 'input[type="text"]' ).val( '' );
                        $( this ).parent().slideUp(
                            'medium', function() {
                                $( this ).remove();           
                    
                                // update checkbox numbers
                                var i=0;
                                $("#additional_categories-ul > li").filter(":visible").each( function(index, element){
                                    $(this).find("input[type='checkbox']").attr("name", "listing_wp[additional_categories][check][" + i + "]");
                                    i++;
                                });
                            }
                        );
                    }
                );

                el.find( '.redux-multi-text-add' ).click(
                    function() {
                        var number = parseInt( $( this ).attr( 'data-add_number' ) );
                        var id = $( this ).attr( 'data-id' );
                        var name = $( this ).attr( 'data-name' );
                        for ( var i = 0; i < number; i++ ) {
                            var new_input = $( '#' + id + ' li:last-child' ).clone();
                            el.find( '#' + id ).append( new_input );
                            el.find( '#' + id + ' li:last-child' ).removeAttr( 'style' );
                            el.find( '#' + id + ' li:last-child input[type="text"]' ).val( '' );
                            el.find( '#' + id + ' li:last-child input[type="text"]' ).attr( 'name', name );

                            el.find( '#' + id + ' li:last-child input[type="text"]' ).attr("name", "listing_wp[additional_categories][value][" + ($("#additional_categories-ul > li").length - 2) + "]");

                            el.find( '#' + id + ' li:last-child' ).find("input[type='checkbox']").attr("name", "listing_wp[additional_categories][check][" + ($("#additional_categories-ul > li").length - 2) + "]");
                            //"listing_wp[additional_categories][check][" + $(".additional_categories-ul > li").length + "]"
                        }
                    }
                );
            }
        );
    };
})( jQuery );