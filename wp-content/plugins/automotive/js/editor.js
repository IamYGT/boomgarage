(function($) {
  "use strict";

 jQuery(document).ready( function($) {
(function() {
    tinymce.create('tinymce.plugins.shortcodebutton', {
        init : function(ed, url) {
            ed.addButton('shortcodebutton', {
                title : 'Add an element',
                image : url + '/../images/icon.png',
				classes : 'widget btn shortcodebutton',
                onclick : function() {
					
					$("ul.shortcode_list > li").not("ul.shortcode_list li ul.child_shortcodes li").unbind().on('click', function(e){
						e.preventDefault();
												
						var title = $(this).find("a").data("title");						
						/*if($(this).find("ul.child_shortcodes").is(":visible")){
							$(this).find("ul.child_shortcodes").slideUp();
						} else {
							$(this).find("ul.child_shortcodes").slideDown();						
						}*/
						
						/**/
						
						if(title == "columns"){
							$(".shortcode_list").hide({effect: "fold", duration: 600});
							
							jQuery.ajax({
							   type : "post",
							   url : myAjax.ajaxurl,
							   data : { action: "column_maker" },
							   success: function(response) {
								  $(".column_generator").html(response).fadeIn();
								  $( "#shortcode-modal" ).dialog('option', 'title', 'Column Generator');
							      $( "#shortcode-modal" ).dialog("widget").animate({
									  width: '450px',
									  height: '500px'
								  }, {
									  duration: 500,
									  step: function(){
										  $("#shortcode-modal").dialog("option", "position", "center");
									  }
								  });
								  
								  var new_height = $(".column_generator").height();
								  $("#shortcode-modal").height((new_height + 12));
							   }
							});
						} else if(title == "icons"){
							$(".shortcode_list").hide({effect: "fold", duration: 600});
							
							jQuery.ajax({
							   type : "post",
							   url : myAjax.ajaxurl,
							   data : { action: "generate_icons" },
							   success: function(response) {
								  $(".shortcode_generator").html(response).fadeIn(400, function(){
									  $("#shortcode-modal").height(440); 
								  });								
								  
								  $( "#shortcode-modal" ).dialog('option', 'title', 'Icon Customizer');
								  $( "#shortcode-modal" ).dialog("widget").animate({
									  width: '800px',
									  height: '500px'
								  }, {
									  duration: 500,
									  step: function(){
										  $("#shortcode-modal").dialog("option", "position", "center");
									  }
								  });
							   }
							});
						} else {						
							var display = $(this).find("ul.child_shortcodes").css('display');
							
							if(display == "none"){
								$(this).find("ul.child_shortcodes").slideDown();
							} else {
								$(this).find("ul.child_shortcodes").slideUp();
							}
							//$(this).find("ul.child_shortcodes").slideToggle();
						}
					});
					
					$("ul.child_shortcodes li").click( function(e){
						e.preventDefault();
						
						var shortcode = $(this).find("a").data("shortcode");
						
						if(shortcode.substring(0, 6) == "insert"){
							$( "#shortcode-modal" ).dialog('close');
							tinyMCE.execCommand('mceInsertContent', false, "[" + shortcode.substring(7) + "]");
							return false;
						} else {		
							$(".shortcode_list").hide({effect: "fold", duration: 600});
											
							jQuery.ajax({
							   type : "post",
							   url : myAjax.ajaxurl,
							   data : {action: "generate_shortcode", shortcode: shortcode},
							   success: function(response) {
								  $(".shortcode_generator").html(response).fadeIn();
							   }
							});
						}
						
					});				
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('shortcodebutton', tinymce.plugins.shortcodebutton);
})();
});
})(jQuery);