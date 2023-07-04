(function($) {
  "use strict";

    jQuery(document).ready( function($){

        $("button.submit_new_name").click( function(e) {
            var type  = $(this).data('type');
            var exact = $(this).data('exact');
            var value = $("input." + type).val();

            if(!value){ $("." + type + "_sh").slideToggle(400, function(){ $("a[data-id='" + type + "']").slideDown(); }); return false; }
             
            jQuery.ajax({
                type : "post",
                url : myAjax.ajaxurl,
                dataType: 'json',
                data : { action: "add_name", type: type, value: value, exact: exact },
                success: function(response) {

                    $("select#" + type).append($('<option>', {
                        value: response.term_id,
                        text: value
                    }));

                    if(type == "options" && typeof $("select#" + type).val() == "object"){
                        var old_val = $("select#" + type).val();
                        old_val.push(response.term_id);

                        value = old_val;
                    }

                    $("select#" + type).val(value);

                    $("input." + type).val("");

                    $("." + type + "_sh").slideToggle(400, function(){    
                        if(type == "options"){
                            $(this).parent().parent().find(".chosen-dropdown").trigger("chosen:updated");
                        }
                    });

                    $("a[wdata-id='" + type + "']").slideDown();
                }
            });

            e.preventDefault();
         });
        
    });


    
    /* Tax Add */
    $(document).on("click", ".add_new_tax_term", function(e){
        var parent_id = $(this).data("parent-id");

        $(".new_term_box[data-show-id='" + parent_id + "']").stop(true, true).toggleClass("show");
    });

    $(document).on("click", ".submit_new_term", function(e){
        e.preventDefault();

        var parent_id = $(this).closest(".new_term_box").data("show-id");
        var input     = $(".new_term_box[data-show-id='" + parent_id + "'] .term_input_val");
        var value     = input.val();

        jQuery.ajax({
            type : "post",
            url : myAjax.ajaxurl,
            dataType: "json",
            data : { action: "add_tax_term", parent_id: parent_id, value: value },
            success: function(response) {
                
                if(response.response == "success"){
                    var term_id = response.term_id;

                    $("select[name='listing_category_" + parent_id + "']").append($('<option>', {
                        value: term_id,
                        text: value
                    })).val(term_id);

                    input.val("");
                } else {
                    alert("Error adding term");
                }

                // $("a[data-id='" + type + "']").slideDown();
            }
        });
    });

})(jQuery);