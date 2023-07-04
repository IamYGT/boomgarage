(function($) {
  "use strict";

 jQuery(document).ready( function($){
	$.fn.evenElements = function() {	
		var heights  = [];
		
		$(this).removeAttr("style").height('auto');
		
		this.each( function() {
			if($(this).css('transition-duration')){
				transition_duration = (typeof transition_duration == "undefined" ? $(this).css('transition-duration') : transition_duration);
				$(this).css('transition-duration', '0s');
			}
			
			var height = $(this).height('auto').outerHeight();
			
			heights.push(height);
		});	
		
		var largest = Math.max.apply(Math, heights);
	
		return this.each(function() {
            $(this).height(largest);
				
			$(this).css('transition-duration', transition_duration);
        });
	}
	
	jQuery.fn.extend({
	  	renameAttr: function( name, newName, removeData ) {
			var val;
			return this.each(function() {
			  	val = jQuery.attr( this, name );
		  		jQuery.attr( this, newName, val );
		  		jQuery.removeAttr( this, name );
		  		// remove original data
		  		if (removeData !== false){
					jQuery.removeData( this, name.replace('data-','') );
		  		}
			});
	  	}
	});
	
	// empty paragraphs
	$('p:empty').remove();
	
	$(".portfolioFilter li a").click( function(){
		$(".portfolioFilter li.active").removeClass("active");
		$(this).parent().addClass('active');
	});
	
	$(document).on({
		mouseenter: function(){
			var elm = $('ul:first', this);
			var off = elm.offset();
			
			if(typeof off != "undefined"){	
				var l = off.left;
				var w = elm.width();
				var docW = $("section.content").outerWidth(true);
			
				var isEntirelyVisible = (l+ w <= docW);
			
				if ( ! isEntirelyVisible ) {
					$(this).addClass('other_side');
				}
			}
		},
		
		mouseleave: function(){
			if($(this).hasClass('other_side')){
				$(this).removeClass('other_side');	
			}
		}
	}, ".dropdown li");
	
	// featured service hover
	if($(".featured-service .featured").length){
		$('.featured-service .featured').hover( function() {
			var image = $(this).find('img');
			
			image.data('img', image.attr('src'));
			image.attr('src', image.data('hoverimg'));
		}, function(){
			var image = $(this).find('img');
			
			image.attr('src', image.data('img'));
		});
	}	
	
	// google map
	// function init_google_map(){
	// 	if($("#google-map-listing").length){
	// 		var latitude     = $("#google-map-listing").data('latitude');
	// 		var longitude    = $("#google-map-listing").data('longitude');
	// 		var zoom         = $("#google-map-listing").data('zoom');
	// 		var scroll_wheel = $("#google-map-listing").data('scroll');
	// 		var style        = $("#google-map-listing").data('style');
	// 		var parallax     = $("#google-map-listing").data('parallax');
			
	// 		if(latitude && longitude){				
	// 			var myLatlng = new google.maps.LatLng(latitude, longitude);
	// 			var myOptions = {
	// 				zoom: zoom,
	// 				center: myLatlng,
	// 				popup: true,
	// 				mapTypeId: google.maps.MapTypeId.ROADMAP
	// 			}

	// 			if(parallax != false && typeof parallax == "undefined"){
	// 				myOptions.scroll = {
	// 					x:$(window).scrollLeft(),
	// 					y:$(window).scrollTop()
	// 				}
	// 			}
				
	// 			if(scroll_wheel == false && typeof scroll_wheel != "undefined"){
	// 				myOptions.scrollwheel = false;
	// 			}
				
	// 			if(typeof style != "undefined"){
	// 				myOptions.styles = style;
	// 			}				
				
	// 			var map = new google.maps.Map(document.getElementById("google-map-listing"), myOptions);
				
	// 			var marker = new google.maps.Marker({
	// 				position: myLatlng, 
	// 				map: map,
	// 				title: "Our Location"
	// 			});


	// 			if(parallax != false && typeof parallax == "undefined"){
	// 				var offset = $("#google-map-listing").offset();
	// 			    map.panBy(((myOptions.scroll.x-offset.left)/3),((myOptions.scroll.y-offset.top)/3));
				      
	// 			    google.maps.event.addDomListener(window, 'scroll', function(){
	// 				    var scrollY = $(window).scrollTop(),
	// 				        scrollX = $(window).scrollLeft(),
	// 				        scroll  = map.get('scroll');
					    
	// 				    if(scroll){
	// 						map.panBy(-((scroll.x-scrollX)/3),-((scroll.y-scrollY)/3));
	// 				    }

	// 				    map.set('scroll',{
	// 				    	x:scrollX,
	// 				    	y:scrollY
	// 				    });
	// 				});
	// 			}

	// 			google.maps.event.addListener(marker, 'click', function() {
	// 				map.setZoom(zoom);
	// 			});
	// 		}
	// 	}
	// }
	
	// init_google_map();
			
	// $('#myTab a').click(function (e) {
	// 	e.preventDefault();
	// 	$(this).tab('show');
		
	// 	var index = $(this).parent().index();
		
	// 	if(index == 3){
	// 		setTimeout( function(){
	// 			init_google_map();
	// 		}, 500);
	// 	}
	// });

	// social likes
	if($('.social-likes.blog_social').length){
		$('.social-likes.blog_social').socialLikes({
			zeroes: 'yes'
		});
	}
	
	// fancy box
	if($(".fancybox").length){
		$("a.fancybox").fancybox();
	}
	
	// dropdown menu
	if($('.mobile_dropdown_menu .dropdown .dropdown').length){
		$('.mobile_dropdown_menu .dropdown .dropdown').each(function(){
			var $self = $(this);
			var handle = $self.children('[data-toggle="dropdown"]');
			$(handle).on("click touchstart", function(){
				var submenu = $self.children('.dropdown-menu').eq(0);
				$(submenu).toggle();
				return false;
			});
		});
	}
	
	// grid switch
	if($('#grid-switch-control li a').length){
		$('#grid-switch-control li a').click(function(e) {
			e.preventDefault();
			var _sidebar = $(this).attr('data-sidebar');
			var _boxview = $(this).attr('data-boxview');
			$('#grid-switch').removeClass('no-sidebar').removeClass('left-sidebar').removeClass('right-sidebar').removeClass('list-view').removeClass('grid-view');
			$('#grid-switch').addClass(_sidebar).addClass(_boxview);
		});
	}
	
	$(".flip").on({
		mouseenter: function(){	
			if($(this).css('border-top-color') != 'rgb(0, 255, 0)'){
				$(this).find('.card').addClass('flipped');
			}
		},
		mouseleave: function(){
			if($(this).css('border-top-color') != 'rgb(0, 255, 0)'){
				$(this).find('.card').removeClass('flipped');
			}
		}
	});
	
	function flip_card_size(){
		$(".flip").each( function(index, element){	
			var frontHeight = $(this).find('.front img').outerHeight();
			var frontWidth  = $(this).find('.front img').outerWidth();
			
			$(this).find('.flip, .back, .front, .card').height(frontHeight);
			$(this).height(frontHeight);
			
			$(this).find('.flip, .back, .front, .card').width(frontWidth);
			$(this).width(frontWidth);
		});
	}
	
	function flip_card_reset_size(){
		$(".flip").each( function(index, element){				
			$(this).find('.flip, .back, .front, .card').removeAttr("style").css("width", "auto");
			$(this).removeAttr("style").css("width", "auto"); 
		});
	}
	
	if($(".back_to_top").length){
		$(".back_to_top").click(function() {
		   $("html, body").animate({ scrollTop: 0 }, "slow");
		   return false;
		});
		
		$(window).scroll(function() {
			var height = $(window).scrollTop();
		
			if(height > 300) {
				$(".back_to_top").fadeIn();
			} else {
				$(".back_to_top").fadeOut();
			}
		});
	}

	$(document).on("click", ".ajax_login", function(){
		var nonce        = $(this).data("nonce");
		var username     = $(this).parent().find(".username_input");
		var password     = $(this).parent().find(".password_input");
		var empty_fields = false;

		if(!username.val()){
			empty_fields = true;
			username.css("border", "1px solid #F00");
		} else {
			username.removeAttr("style");
		}

		if(!password.val()){
			empty_fields = true;
			password.css("border", "1px solid #F00");
		} else {
			password.removeAttr("style");
		}

		if(!empty_fields){

			jQuery.ajax({
				url: ajax_variables.ajaxurl,
				type: 'POST',
				data: { action: 'ajax_login', username: username.val(), password: password.val(), nonce: nonce },
				success: function(response){
					if("success" == response){
						username.removeAttr("style");
						password.removeAttr("style");

						location.reload();
					} else {
						username.css("border", "1px solid #F00");
						password.css("border", "1px solid #F00");
					}
				}
			});			
		}
	});

	$("*[data-toggle='popover']").popover({
		html: true,
        trigger: 'manual',
        container: $(this).attr('id'),
        content: function () {
            $return = '<div class="hover-hovercard"></div>';
        }
	}).on("mouseenter", function () {
        var _this = this;
        $(this).popover("show");
        $(this).siblings(".popover").on("mouseleave", function () {
            $(_this).popover('hide');
        });
    }).on("mouseleave", function () {
        var _this = this;
        setTimeout(function () {
            if (!$(".popover:hover").length) {
                $(_this).popover("hide")
            }
        }, 100);
    });
	
	// if(typeof WOW == 'function'){
		// var WOW =  WOW({
	 //      boxClass:     'wow',      
	 //      animateClass: 'animated', 
	 //      offset:       0,          
	 //      mobile:       true,       
	 //      live:         true        
	 //  });
	  // WOW.init();
	// }

	// new WOW().init();

	// if wow exists
	if(typeof WOW == 'function'){
		WOW = new WOW({
				boxClass: 'auto_animate',
				offset: 15
			});
		
		WOW.init();
	}
	
});
})(jQuery);