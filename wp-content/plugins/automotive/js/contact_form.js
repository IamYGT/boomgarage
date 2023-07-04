(function($) {
  "use strict";

 jQuery(document).ready(function($) {
    $(".submit_contact_form").click(function(e) { 
        e.preventDefault();

        var parent = $(this).parent();

        //collect input field values
        var user_name       = parent.find("input[name='name']").val(); 
        var user_email      = parent.find("input[name='email']").val();
        var user_message    = parent.find("textarea[name='message']").val();
        
        //simple validation at client's end
        //we simply change border color to red if empty field using .css()
        var proceed = true;
        if(user_name==""){ 
            parent.find("input[name='name']").css("border", "1px solid red"); 
            proceed = false;
        } else {
			parent.find("input[name='name']").removeAttr("style");
		}
        if(user_email==""){ 
            parent.find("input[name='email']").css("border", "1px solid red"); 
            proceed = false;
        } else {
			parent.find("input[name='email']").removeAttr("style");
		}
        if(user_message=="") {  
            parent.find("textarea[name='message']").css("border", "1px solid red"); 
            proceed = false;
        } else {
			parent.find("textarea[name='message']").removeAttr("style");
		}
        
        //everything looks good! proceed...
        if(proceed) {
            //data to be sent to server
            var post_data = {'userName':user_name, 'userEmail':user_email, 'userMessage':user_message, 'action':'send_contact_form'};
            
            //Ajax post data to server
            $.post(ajax_variables.ajaxurl, post_data, function(data){  
                
                //load success massage in #result div element, with slide effect.     
                if(data.success == "yes"){  
                    parent.find(".contact_result").hide().html('<div class="success">'+data.message+'</div>').slideDown();
                    
                    //reset values in all input fields
                    parent.find('input[type="text"]').val('');
    				parent.find('input[type="email"]').val(''); 
                    parent.find('textarea').val(''); 
                } else {
                    parent.find(".contact_result").hide().html('<div class="error">'+data.message+'</div>').slideDown();                    
                }
                
            }).fail(function(err) {  //load any error data
                parent.find(".contact_result").hide().html('<div class="error">'+err.statusText+'</div>').slideDown();
            });
        }

        setTimeout(function(){
            parent.find("input, textarea").css('border-color',''); 
            parent.find(".contact_result").slideUp();
        }, 3000);
    });
    
    //reset previously set border colors and hide all message on .keyup()
    /*$("#contact_form input, #contact_form textarea").keyup(function() { 
        $("#contact_form input, #contact_form textarea").css('border-color',''); 
        $("#result").slideUp();
    });*/
    
});
})(jQuery);