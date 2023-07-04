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
                data : { action: "add_name", type: type, value: value, exact: exact },
                success: function(response) {
                    $("select#" + type).append($('<option>', {
                        value: value,
                        text: value
                    }));

                    $("select#" + type + " option:last").prop('selected', 'true');

                    var features_table = $("#tabs-2 table");

                    if(features_table.find("tr:last td").length == 3){
                        features_table.find("tr:last").after("<tr></tr>")
                    }

                    features_table.find("tr:last").append("<td><label><input type=\"checkbox\" value=\"" + value + "\" name=\"multi_options[]\" checked=\"checked\">" + value + "</label></td>");
                    

                    $("input." + type).val("");

                    $("." + type + "_sh").slideToggle(400, function(){    
                        if(type == "options"){
                            $(this).parent().parent().find(".chosen-dropdown").trigger("chosen:updated");
                        }
                    });

                    $("a[data-id='" + type + "']").slideDown();
                }
            });

            e.preventDefault();
        });
        
    });

})(jQuery);