(function($) {
  "use strict";

 jQuery(document).ready( function($){
	
	if($("#meta_tabs, #listing_tabs").length){ 
		$("#meta_tabs, #listing_tabs").tabs();

		// var dis_index = $("#listing_tabs li[data-action='options']").index();

		// if(dis_index != -1){
		// 	$("#listing_tabs").tabs( "option", "disabled", [dis_index] );
		// } 
	}
	
	// Functions
	function reset_rows(){
		var i = 1;	
		
		$("#gallery_images tr td .top_header").each(function(index) {
			$(this).text("Image #" + i);
			
			i++;
		});
	}

	var map = "";
	
	function first_run(){		 
		var latitude  = $(".location_value[data-location='latitude']").val();
		var longitude = $(".location_value[data-location='longitude']").val();
		var zoom      = parseInt($( ".zoom_level" ).val());
		 
		var myLatlng = new google.maps.LatLng(latitude, longitude);
		
		var myOptions = {
			zoom: zoom,
			center: myLatlng,
			popup: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		
		map = new google.maps.Map(document.getElementById("google-map"), myOptions);
		
		var marker = new google.maps.Marker({
			position: myLatlng, 
			map: map,
			title: "Our Location"
		});
		
		google.maps.event.addListener(marker, 'click', function() {
			map.setZoom(myLatlng);
		});
		
		google.maps.event.addListener(map, "rightclick", function(event) {
			var lat = event.latLng.lat();
			var lng = event.latLng.lng();
			
			$(".location_value[data-location='latitude']").val(lat);
			$(".location_value[data-location='longitude']").val(lng);
			
			marker.setMap(null);
			
			newLatlng = new google.maps.LatLng(lat, lng);
			
			marker = new google.maps.Marker({
				position: newLatlng, 
				map: map,
				title: "Our Location"
			});
			
			google.maps.event.addListener(marker, 'click', function() {
				map.setZoom(zoom);
			});
		});
		
		google.maps.event.addListener(map, 'zoom_changed', function() {
		  var zoomLevel = map.getZoom();
		  
		  $( ".zoom_level" ).val( zoomLevel );
		  $( ".zoom_level_text" ).html( zoomLevel );		  
		  $( "#slider-vertical" ).slider( "value", zoomLevel );
		});
		
	 }
	 if($("#slider-vertical").length){
		 $( "#slider-vertical" ).slider({
		    orientation: "vertical",
		    range: "min",
		    min: 0,
		    max: 19,
		    slide: function( event, ui ) {
		    	$( ".zoom_level" ).val( ui.value );
			  	$( ".zoom_level_text" ).html( ui.value );

			  	map.setZoom(parseInt( ui.value ));
		    }
	    });

	    $( ".zoom_level" ).val( $( "#slider-vertical" ).slider( "value" ) );
		$( ".zoom_level_text" ).html( $( "#slider-vertical" ).slider( "value" ) );
	}
	 
	 $(".chosen-dropdown").chosen();
	 $('.info').tooltip({container: 'body'});
	
	 if($("select.multi").length){
	 	$("select.multi").chosen();

	 	/*$("select.multi").on("change", function(evt, params){
	 		var name = $(this).attr("name");

	 		$("select[name^='dependancies'].multi").each( function(index, element){

	 			// if select
	 			if($(this).attr("name") != name){
		 			if(typeof params.selected != "undefined"){
			 			$(this).find("option").eq(params.selected).prop('disabled', true);
			 		} else {
			 			$(this).find("option").eq(params.deselected).prop('disabled', false);
			 		}
			 	}
	 		});

	 		$("select.multi").trigger("chosen:updated");
	 	});*/
	 }

	// Adding images
	// $(document).on("click", ".add_image", function(e){
	// 	e.preventDefault();
		
	// 	var image_number = parseInt($("#gallery_images tr:last td:first .top_header").text().match(/\d{1,}/g)) + 1;
		
	// 	var html = "<tr><td><div class='top_header'>Image #" + image_number + "</div>";
	// 		html    += "<div class='image_preview'>No Image</div>";
	// 		html    += "<div class='buttons'><span class='button add_image_gallery' data-id='" + image_number + "'>Change image</span> <input type='text' name='portfolio_links[" + image_number + "]' value='' placeholder='Image link'></div>";
	// 		html    += "</td></tr>";
		
	// 	$("#gallery_images tr:last").after(html);
	// 	$("#gallery_images tr:last").fadeIn();
	// });
	
	$("#gallery_images").on("click", ".make_default_image", function(e){
		e.preventDefault();
		
		var the_row = $(this).closest("tr");
		
		$(".active_image").removeClass("active_image");
		
		$(this).addClass("active_image");
		
		var html = the_row.clone();
		
		the_row.remove();
		
		$(html).prependTo("#gallery_images tbody");
		reset_rows();
	});
	
	$("#gallery_images").on("click", ".delete_image", function(e){
		e.preventDefault();
		
		var handle = $(this).closest("tr");
	
		handle.fadeOut(300, function(){
			handle.remove();
			
			reset_rows();
		});		
	});
	
	 // portfolio upload
	 // Prepare the variable that holds our custom media manager.
	 var media_frame;
	 var formlabel = 0;
	 
	 // Bind to our click event in order to open up the new media experience.
	 $(document.body).on('click.mojoOpenMediaManager', '.media-upload', function(e){ 
	 
		 e.preventDefault();
		 
		 if($(this).data('imageholder').length){
			formlabel = $("#" + $(this).data('imageholder'));
		 } else {
		 	formlabel = jQuery(this).parent();
		 }
		 
		 if ( media_frame ) {
			media_frame.open();
			return;
		 }
		 media_frame = wp.media.frames.media_frame = wp.media({
		 
		 
		 className: 'media-frame add-image-gallery',
		 frame: 'select', 
		 multiple: false, 
		 library: {
			type: 'image' 
		 },
		 });
		 media_frame.on('select', function(){
			 
			 var media_attachment = media_frame.state().get('selection').first().toJSON();
			 
			 var html = "<img src='" + media_attachment.url + "' class='gallery_thumbnail'><br>";
			 //html    += "<span class='button add_image_gallery'>Change image</span> ";
			 //html    += "<span class='button delete_image'>Delete image</span> ";
			 html    += "<input type='hidden' name='portfolio_image' value='" + media_attachment.url + "'>";
			 
			 formlabel.html(html);
			 
		 });
	 
	 media_frame.open();
	 });

	// 
	$(document.body).on('click.mojoOpenMediaManager', '.pick_pdf_brochure', function(e){ 
	 
		e.preventDefault();
		 
		if ( media_frame ) {
			media_frame.open();
			return;
		}

		media_frame = wp.media.frames.media_frame = wp.media({		 
			className: 'media-frame add-pdf-gallery',
			frame: 'select', 
			multiple: false, 
			library: { },
		});
		 media_frame.on('select', function(){
			 
			 var media_attachment = media_frame.state().get('selection').first().toJSON();
			 
			 $(".pdf_brochure_input").val(media_attachment.id);
			 $(".pdf_brochure_label").text(media_attachment.url);
			 
		 });
	 
	 media_frame.open();
	 });

	$(document).on("click", ".remove_pdf_brochure", function(e){
		e.preventDefault();

		$(".pdf_brochure_label").html("");
		$(".pdf_brochure_input").val("");
	});
	 
	 // add detail
	 $(".add_detail").click( function(){
		 $(".new_details").append("<input type='text' name='project_details[]' class='widefat' style='display: none'>");
		 $("input[name='project_details[]']").last().slideDown();
	 });
	 
	 // remove detail
	 $(".remove_detail").click( function(){
		 $("input[name='project_details[]']").last().slideUp(400, function(){
			 $(this).remove();
		 });
	 });
	
	// Return a helper with preserved width of cells
	var fixHelper = function(e, tr) {
		var $originals = tr.children();
		var $helper = tr.clone();
		$helper.children().each(function(index)
		{
		  $(this).width($originals.eq(index).width())
		});
		return $helper;
	};
	 
	if($("#gallery_images").length){
		$("#gallery_images tbody").sortable({
			helper: fixHelper,
			handle: ".move_image",
			placeholder: "ui-state-highlight",
			stop: function(){
				reset_rows();
				$(".active_image").removeClass("active_image");
				$(".make_default_image:first").addClass("active_image");
			}
		}).disableSelection();
	}
	
	// Media manager for adding images
	// Prepare the variable that holds our custom media manager.
	 var media_frame;
	 var formlabel = 0;
	 
	 // Bind to our click event in order to open up the new media experience.
	 $(document.body).on('click.mojoOpenMediaManager', '.add_image'/*_gallery'*/, function(e){ 
	 
		e.preventDefault();
		 
		// if($("body").hasClass("post-type-listings_portfolio")){
		// 	formlabel = jQuery(this).parent().parent().parent();
		// } else {
		// 	formlabel = jQuery(this).parent().parent().parent().parent();
			formlabel = $("#gallery_images tbody");
		// }

		 var id = ($("#gallery_images tbody tr").length + 1);//$(this).data('id');
		 
		 if ( media_frame ) {
			media_frame.open();
			return;
		 }
		 media_frame = wp.media.frames.media_frame = wp.media({
		 
		 className: 'media-frame add-image-gallery',
		 frame: 'select', 
		 multiple: true, 
		 library: {
			type: 'image' 
		 },
		 });
		 media_frame.on('select', function(){
			 
			var media_attachment = media_frame.state().get('selection').toJSON();
			
			// $("#gallery_images tr > td:last").remove();

			$.each( media_attachment, function(i, val){
				var html = "";
			
				var theImage = new Image();
				theImage.src = val.url;
				
				var imageWidth = theImage.width;
				
				if(imageWidth > 167){
					var divider    = (imageWidth / 167);
					var imageWidth = (imageWidth / divider);
				} else if(imageWidth == 0){
					var imageWidth = 167;
				}
				
				 
			   	html 	 += "<tr><td><div class='top_header'>Image #" + id + "</div>";
				html    += "<div class='image_preview'><img src='" + val.url + "' style='width: " + imageWidth + "px; height: auto;'></div>";
				html    += "<div class='buttons'><span class='button add_image_gallery' data-id='" + id + "'>Change image</span> ";
				html    += "<span class='button make_default_image'>Set default image</span> ";
				html    += "<span class='button delete_image'>Delete image</span> ";
				html    += "<span class='button move_image'>Move Image</span>";
				html 	+= ($("body").hasClass("post-type-listings_portfolio") ? '<input type="text" name="portfolio_links[2]" value="" placeholder="Image link">' : '');
				html    += "</div><input type='hidden' name='gallery_images[]' value='" + val.id + "'>";
				html    += "</td></tr>";console.log(html);
				
				formlabel.append(html);
				id++;

			});
		});
	 
	 media_frame.open();
	 });
	 
	 // check for valid video 
	 $("#listing_video_input").bind("keyup input paste", function(){
		 var url = $(this).val();
		 
		 var youtubeUrl = url.match(/watch\?v=([a-zA-Z0-9\-_]+)/);
		 var vimeoUrl = url.match(/https?:\/\/(?:www\.)?vimeo.com\/(?:channels\/|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/);
		 
		 if( youtubeUrl ){
			 var output = '<br><br><iframe src="http://www.youtube.com/embed/'+youtubeUrl[1]+'?rel=0" height="400" width="644" allowfullscreen="" frameborder="0" style="display: none;" class="listing-video-preview"></iframe>';
			 $("#listing_video").html(output);
			 $(".listing-video-preview").slideDown();
		 } else if( vimeoUrl ){
			 var output = '<br><br><iframe src="http://player.vimeo.com/video/'+vimeoUrl[3]+'" height="400" width="644" allowfullscreen="" frameborder="0" style="display: none;" class="listing-video-preview"></iframe>';
			 $("#listing_video").html(output);
			 $(".listing-video-preview").slideDown();			 
		 } else {
			 var output = '<br><br><span class="listing-video-preview text-error" style="display: none;">URL isn\'t recognized, please either past in a Vimeo URL or YouTube URL</span>';
			 
			 if(!$(".listing-video-preview").hasClass('text-error')){
			 	$("#listing_video").html(output);
			 	$(".listing-video-preview").fadeIn();	
			 }
		 }
	 });
	 
	 
	 // shortcode
	 function shortcode_generator_back(){
		 $(".shortcode_generator").hide({ effect: "fold", duration: 600 }).html("");
		 $(".shortcode_list").show({ effect: "fold", duration: 600 });
		 $(".shortcode_back").fadeOut(400, function(){
			 $(this).remove();
		 });
        $('.ui-dialog-title').html("Shortcode Manager");
	 }
	 
	 $(document).on("click", ".shortcode_back", function(){
		 shortcode_generator_back();
	 });
	 
	 $(document).on("click", "div.mce-shortcodebutton", function(){
		 $( "#shortcode-modal" ).dialog({
			height: 500,
			width: 550,
			resizable: false,
			modal: false,
			title: 'Shortcode Manager',
			open : function(event, ui) { 
				$("body").css({ overflow: 'hidden' });
			},
			close: function(){
				 $( "#shortcode-modal" ).dialog( "destroy" );
				 $(".column_generator, .shortcode_generator").hide();
				 $("ul.shortcode_list").show();
			},
		    beforeClose: function(event, ui) {
				$("body").css({ overflow: 'inherit' });
		    }
		  });
	 });
	
	
	 
	 $(document).on("click", ".column_generator .column_display_container.insert", function(){
		 var number = $(this).data('number');
		 var left   = $("#full_column").data('number');
		 var html   = "";
		 
		 if((number + left) < 13){			 
			 $("#full_column div.empty").each( function(i, element){
				 //alert("i: "+(i+1)+"\nNumber: "+number);
				 if((i + 1) == number){ 
				 	var full_width = (((number - 1) * 5) + (29 * number));
				 
				 	html = "<div class='full' style='width: " + full_width + "px;' data-spaces='" + number + "'><i class='fa fa-times'></i></div>";
				 	
				 	$(this).before(html);
				 }
				 
				 $(this).remove();//removeClass('empty').addClass('full');
				 
				 if((i + 1) == number){
				 	 return false; 
				 }
			 });
			 
			 $("#full_column").data('number', (number + left));
			 
			 if($("#full_column").data('number') == 12){
				 $(".generate_columns").fadeIn();
			 }
		 } else {
			 //alert('no room');
		 }
	 });
	 
	 $(document).on("click", ".column_generator .column_display_container div i", function(){
		 var spaces = parseInt($(this).parent().data('spaces'));
		 var number = $("#full_column").data('number');
		 
		 $("#full_column").data('number', (number - spaces));
		 
		 $(this).parent().hide().remove();
		 
		 for(var i=0; i<spaces; i++){
		 	 $("#full_column").append("<div class='empty one'></div>");
		 } 
			 
		 if($("#full_column").data('number') <= 11) {
			 $(".generate_columns").fadeOut();
		 }
	 });
	 
	 $(document).on("click", ".generate_columns", function(){	 
	  	 var column_code = "<div class='row'>";
			 
		 $("#full_column div").each( function(index, element){
			 var space = $(this).data('spaces');
			 
			 column_code = column_code + "<div class='col-lg-" + space + " col-md-" + space + "'>column content</div>";
		 });
		 
		 column_code = column_code + "</div><br>&nbsp;";
		 
		 
		 $( "#shortcode-modal" ).dialog().dialog("destroy");
		 $( ".column_generator, #shortcode-modal .shortcode_list ul.child_shortcodes").hide();
		 $( "#shortcode-modal .shortcode_list").show();
		 
		 tinyMCE.execCommand('mceInsertContent', false, column_code);
		 return false;
	 });
	 
	 $(document).on("click", ".shortcode_boxed_item .hidden_click_event", function(){
		 this_boxed = $(this).closest('.shortcode_boxed_item');
		 var current_height = this_boxed.height();
		 
		 if(!this_boxed.hasClass('open')){
			 this_boxed.addClass('open');
			 
			 var clone = this_boxed.clone();
			 clone.css({ position: "absolute", top: "-1000px", left: "-1000px" }).attr('id', 'cloned_div');
			 $('body').append(clone);
			 
			 $("#cloned_div").css("height", "auto").addClass('open');
			 
			 var autoHeight = $("#cloned_div").outerHeight();
			 
			 this_boxed.animate({height: autoHeight}, 400);
			 
			 $("#cloned_div").remove();
		 } else {
			 this_boxed.removeClass('open');
			 
			 var clone = this_boxed.clone();
			 clone.css({ position: "absolute", top: "-1000px", left: "-1000px" }).attr('id', 'cloned_div');
			 $('body').append(clone);
			 
			 $("#cloned_div").removeAttr('style');
			 
			 var autoHeight = $("#cloned_div").height();
			 
			 this_boxed.animate({height: "30px"/*autoHeight*/}, 400);
			 
			 $("#cloned_div").remove();
		 }
	 });
	 
	 /*$(document).on("click", ".shortcode_boxed_item table i.shrink", function(){
	 	 //$(this).closest('.shortcode_boxed_item').addClass('just_closed').removeClass('open').removeAttr('style');
		 
		 this_boxed = $(this).closest('.shortcode_boxed_item');
		 var current_height = this_boxed.height();
		 
		 if(this_boxed.hasClass('open')){
			 this_boxed.removeClass('open');
			 
			 var clone = this_boxed.clone();
			 clone.css({ position: "absolute", top: "-1000px", left: "-1000px" }).attr('id', 'cloned_div');
			 $('body').append(clone);
			 
			 $("#cloned_div").removeAttr('style');
			 
			 var autoHeight = $("#cloned_div").height();
			 
			 this_boxed.animate({height: autoHeight}, 400);
			 
			 $("#cloned_div").remove();
		 }
	 });*/
	 
	 
	 $(document).on("click", ".icon_selector", function(e){
		 var id = $(this).data('id');
		 
		 $( "#icon_selector_dialog" ).dialog({
			resizable: true,
			height: 600,
			width: 500,
			modal: true,
			buttons: {
			  "Use Icon": function() {
				  var id    = $("#icon_selector_dialog").data('container');
				  var value = $("#icon_selector_dialog i.enlarge").attr('class').replace(" enlarge", "");				  
				  
				  var type  = (value.indexOf("fa") != -1 ? "fa" : "fontello");
				  
				  $(".icon_selector[data-id='" + id + "']").html("Icon: <i class='" + value + "' data-no_custom='js'></i>");
				  $("input[name*='[icon]'][data-input='" + id + "']").val(value.replace(type, "").trim());
				  $("input[name*='[type]'][data-input='" + id + "']").val(type);
				  
				  $( this ).dialog( "close" );
			  },
			  Cancel: function() {
				  $( this ).dialog( "close" );
			  }
			},
		    open: function(){
				  $( this ).data('container', id);
				  $("body").css({ overflow: 'hidden' });
			},
			close: function(){
				$("body").css({ overflow: 'inherit' });
				if($("#icon_selector_dialog i.enlarge").length){
					$("#icon_selector_dialog i.enlarge").each( function(index, element){
						$(this).removeClass("enlarge");
					});
				}
			}
		  });
	 });
	 
	 $(document).on("click", ".sc_icon_selector", function(e){
		 var id = $(this).data('code');
		 var this_selector = $(this);
		 
		 $( "#sc_icon_selector_dialog" ).dialog({
			resizable: true,
			height: 600,
			width: 500,
			modal: true,
			buttons: {
			  "Use Icon": function() {
				  var id    = $("#sc_icon_selector_dialog").data('container');
				  var value = $("#sc_icon_selector_dialog i.enlarge").attr('class').replace(" enlarge", "");				  
				  
				  var type  = (value.indexOf("fa") != -1 ? "fa" : "fontello");
				  
				  this_selector.after("<input type='hidden' name='icon' value='" + value + "' class='ajax_created'>");
				  this_selector.html("Icon: <i class='" + value + "' data-no_custom='js'></i>");
				  
				  $( this ).dialog( "close" );
			  },
			  Cancel: function() {
				  $( this ).dialog( "close" );
			  }
			},
		    open: function(){
				  $( this ).data('container', id);
			},
			close: function(){
				if($("#sc_icon_selector_dialog i.enlarge").length){
					$("#sc_icon_selector_dialog i.enlarge").each( function(index, element){
						$(this).removeClass("enlarge");
					});
				}
			}
		  });
	 });
	 
	 $(document).on("click", "#icon_selector_dialog i", function(){
		 if($("#icon_selector_dialog i.enlarge").length){
			 $("#icon_selector_dialog i.enlarge").each( function(index, element){
				 $(this).removeClass("enlarge");
			 });
		 }
		 
		 if($(this).hasClass("enlarge")){
			 $(this).removeClass("enlarge");
		 } else {
			 $(this).addClass('enlarge');
		 }
	 });
	 
	 $(document).on("click", "#sc_icon_selector_dialog i", function(){
		 if($("#sc_icon_selector_dialog i.enlarge").length){
			 $("#sc_icon_selector_dialog i.enlarge").each( function(index, element){
				 $(this).removeClass("enlarge");
			 });
		 }
		 
		 if($(this).hasClass("enlarge")){
			 $(this).removeClass("enlarge");
		 } else {
			 $(this).addClass('enlarge');
		 }
	 });
	 
	 function get_icon_prefix(the_class){
		 return (the_class.substring(0, 2) == "fa" ? "fa" : "icon");
	 }
	 
	 $(".icon_search").keyup( function(){
		 var text    = $(this).val();
		 var letters = text.length;
		 
		 //if(text == ""){
			 $("#icon_selector_dialog i.no_result").each( function(index, element){
				 $(this).removeClass('no_result');
			 });
		 //}
			 
		 //alert("text: " + text + "\nletters: " + letters);
		 
		 $("#icon_selector_dialog i").each( function(index, element){
			 var the_class = $(this).attr('class');
			 var icon  = $(this).attr('class').replace(get_icon_prefix(the_class) + "-", "");
			 
			 if (icon.substring(0, letters) != text) {
				 $(this).addClass('no_result');
			 }
		 });
	 });
	 
	 $(document).on('keyup', '.icon_search', function(){
		 var text    = $(this).val();
		 var letters = text.length;
		 
		$(".shortcode_generator i.no_result").each( function(index, element){
		    $(this).removeClass('no_result');
		});
		 
			 
		 //alert("text: " + text + " letters: " + letters);
		 
		 $(".shortcode_generator i").each( function(index, element){
			 var the_class = $(this).attr('class');
			 var icon  = $(this).attr('class').replace(get_icon_prefix(the_class) + "-", "");
			 
			 if (icon.substring(0, letters) != text) {
				 $(this).addClass('no_result');
			 }
		 });
	 });
	 
	 $(document).on("click", ".generateModal", function(){
		 if(!$("#shortcode_options").hasClass('modal_form')){
		 	 var html = '<tr class="modal_row"><td>Modal ID: </td><td> <input type="text" name="modal" /></td></tr>';
		 	 $("#shortcode_options tr:last").after(html);
		 	 $("#shortcode_options").addClass('modal_form');
			 $(this).addClass('active');
		 } else {
		 	 $("#shortcode_options").removeClass('modal_form');
			 $(this).removeClass('active');
			 $("#shortcode_options tr.modal_row").hide().remove();
		 }
	 });
	 
	 $(document).on("click", ".generatePopover", function(){
		 if(!$("#shortcode_options").hasClass('popover_form')){
			 var html  = '<tr style="display: none" class="popover_form"><td></td><td><input type="hidden" name="popover" value="true"></td></tr>';
				 html += '<tr class="popover_form"><td>Placement: </td><td> <select name="placement"><option value="top">Top</option><option value="bottom">Bottom</option><option value="right">Right</option><option value="left">Left</option></select></td></tr>';
				 html += '<tr class="popover_form"><td>Title: </td><td> <input type="text" name="title"></td></tr>';
				 html += '<tr class="popover_form"><td>Content: </td><td> <input type="text" name="popover_content"></td></tr>';
				 
			 $("#shortcode_options tr:last").after(html);
			 $("#shortcode_options").addClass('popover_form');
			 $(this).addClass('active');
		 } else {
			 $("#shortcode_options").removeClass('popover_form');
			 $(this).removeClass('active');
			 $("#shortcode_options tr.popover_form").hide().remove();
		 }
	 });
	 
	 
	 
	 $(document).on("click", ".shortcode_generator i", function(){ 
	 	if(!$(this).hasClass('no_custom') && !$(this).data('no_custom')){
			var icon = $(this).attr('class');
					
			jQuery.ajax({
			   type : "post",
			   url : myAjax.ajaxurl,
			   data : {action: "customize_icon", icon: icon},
			   success: function(response) {
				  $(".shortcode_generator").html(response).fadeIn();
			   }
			});
		}
	 });
	 	 
	 // $("li[data-action='options']").click( function(e){
		//  e.preventDefault();
		 
		//  var effects = ["wobble", "flash", "bounce", "tada", "shake", "swing"];
		//  var rand    = effects[Math.floor(Math.random() * effects.length)];
		 
		//  $("#options").addClass('animated ' + rand).one('webkitAnimationEnd mozAnimationEnd oAnimationEnd animationEnd', function(){
		// 	 $(this).removeClass('animated ' + rand);
		//  });
		 
		//  /*setTimeout( function(){
		// 	 $("#options").removeClass('animated ' + rand);
		//  }, 1500);*/
	 // });
	 
	  // add new term from new listing page
	 $(".add_new_name").click( function(e){
		 var id = $(this).data('id');
		 
		 $("a[data-id='" + id + "']").slideUp(400, function(){

			 $("." + id + "_sh").slideToggle(400, function(){
			 	if($(this).is(":visible")){
			 		$(this).css("display", "inline-block");
			 	}
			 });
			 $("." + id + "_sh").find("input").focus();

		});
		 e.preventDefault();
	 });
	 
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

				if(type == "options"){
					var features_table = $("#tabs-2 table");

					if(features_table.find("tr:last td").length == 3){
						features_table.find("tr:last").after("<tr></tr>")
					}

					features_table.find("tr:last").append("<td><label><input type=\"checkbox\" value=\"" + value + "\" name=\"multi_options[]\" checked=\"checked\">" + value + "</label></td>");

					$("input." + type).val("");
				}

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
	 
	 
	 $("li[data-action='map']").click( function(){		 
		 first_run();
	 });
	 
	 $(".location_value").keydown( function(){
		 first_run();
	 });
	 
	 if($("#google-map").data('longitude') && $("#google-map").data('latitude')){
		 first_run();
	 }
	 
	 // Add more prices to the price thing
	 $(".add_price").click( function() {
		 var html = $("#section-prices_range .controls .html").html();
		 
		 $("#section-prices_range .controls .update_area").append("<span class='extra_price' style='display: none'>" + html + "</span>");
		 $(".extra_price").not(":visible").slideDown().css("display", "block");
		 
		 $(".extra_price").last().find("input").each( function(){
			 var name   = $(this).attr('name');
			 var number = parseInt($("input[type='pricing']").length / 2);
			 
			 if(name.substring(16, 21) == "start"){
				 $(this).attr("name", "prices_range[" + number + "][start]");
			 } else {
				 $(this).attr("name", "prices_range[" + number + "][end]");
			 }
		 });
	 });
	 
	 $(".remove_price").click( function() {
	      $("#section-prices_range .controls .extra_price").last().slideUp().remove();
	 });
	 
	 // Add more fuel ranges
	 $(".add_fuel").click( function() {
		 var html = $("#section-fuel_economy_range .controls .html").html();
		 
		 $("#section-fuel_economy_range .controls .fuel_update_area").append("<span class='extra_fuel' style='display: none'>" + html + "</span>");
		 $(".extra_fuel").not(":visible").slideDown().css("display", "block");
		 
		 $(".extra_fuel").last().find("input").each( function(){
			 var name   = $(this).attr('name');
			 var number = parseInt($("input[type='fuel_economy']").length / 2);
			 
			 if(name.substring(22, 27) == "start"){
				 $(this).attr("name", "fuel_economy_range[" + number + "][start]");
			 } else {
				 $(this).attr("name", "fuel_economy_range[" + number + "][end]");
			 }
		 });
	 });
	 
	 $(".remove_fuel").click( function() {
	      $("#section-fuel_economy_range .controls .extra_fuel").last().slideUp().remove();
	 });
	 
	 // Add more mileage ranges
	 $(".add_mileage").click( function() {
		 var html = $("#section-mileage_range .controls .html").html();
		 
		 $("#section-mileage_range .controls .mileage_update_area").append("<span class='extra_mileage' style='display: none'>" + html + "</span>");
		 $(".extra_mileage").not(":visible").slideDown().css("display", "block");
		 
		 $(".extra_mileage").last().find("input").each( function(){
			 var name   = $(this).attr('name');
			 var number = parseInt($("input[type='mileage']").length / 2);
			 
			 if(name.substring(17, 22) == "start"){
				 $(this).attr("name", "mileage_range[" + number + "][start]");
			 } else {
				 $(this).attr("name", "mileage_range[" + number + "][end]");
			 }
		 });
	 });
	 
	 $(".remove_mileage").click( function() {
	      $("#section-mileage_range .controls .extra_mileage").last().slideUp().remove();
	 });
	 
	
	 
	 // auto update labels
	 $(".auto_update_label").keyup( function(){
		 var name  = $(this).attr('name');
		 var name  = name.replace('[s_label]', '');
		 
		 var value = $(this).val();
		 
		 $("span." + name).text(value);
	 });
	 
	 // add custom category
	 $(".add_category").click( function(){
	   if($("input[name^='custom_categories']").length){		   
	   
		  var last_name   = $("input[name^='custom_categories']").last().attr("name");
		  var last_number = last_name.match(/[0-9]+/g);
		  
		  jQuery.ajax({
			 type : "post",
			 url : myAjax.ajaxurl,
			 data : { action: "single_custom_category", last_number: last_number },
			 success: function(response) {
				$("#custom_categories_list").append(response);
				$("#custom_categories_list .option:last").slideDown();
			 }
		  });
	   } else {
		   var id = random_string();
		   
		   $("#custom_categories_list").append('<div class="option" style="display:none" id="' + id + '">\
			  <div class="controls">\
				  <table border="0" style="font-size: 12px;">\
					  <tbody>\
						  <tr>\
							  <td>\
								  <span style="display:inline-block">Singular Label:</span> \
							  </td>\
							  <td>\
								  <input class="of-input no-bottom" name="custom_categories[0][s_label]" type="text" value="" style="width:200px; margin-left: 10px;">\
							  </td>\
							  <td rowspan="2">\
							  	  <span class="delete_category" data-id="' + id + '">X</span>\
							  </td>\
						 </tr>\
						 <tr>\
						 	<td>\
								<span style="display:inline-block">Plural Label:</span> \
							</td>\
							<td>\
								<input class="of-input " name="custom_categories[0][p_label]" type="text" value="" style="width:200px; margin-left: 10px;">\
							</td>\
						 </tr>\
					 </tbody>\
				  </table>\
			  </div>\
			  \
			  <div class="clear"></div>\
		  </div>').promise().done( function(){
			  $("#custom_categories_list .option").slideDown();
		  });
	   }
	 });
	 
	 // delete custom category
	 $("#custom_categories_list").on("click", ".delete_category", function(){
		 var id = $(this).data('id');
		 
		 $("#" + id).slideUp( function(){
			 $(this).remove();
		 });
	 });
	 
	 //$('.color-picker').wpColorPicker();
	 
	 // header preview area
	 $("select[name='header_image']").change( function(){
		 var image = $(this).val();
		 
		 $(".header_preview_area").slideUp( function(){
			 $(this).hide();
			 $(this).html("<a href='" + image + "' target='_blank'><img src='" + image + "' style='width: 100%; margin-top: 8px;'></a>");
			 $(this).slideDown();
		 });
	 });
	 
	 
	 function random_string() {
			 return (((10+Math.random())*0x10000)|0).toString(16).substring(1);
	  }
	 
	 $(document).on("click", ".title_toggle", function(){
		 $("#shortcode_options :input").not(".title_toggle").each( function(index, element){
			 var random_class = random_string();
			 
			 $(this).addClass(random_class);
			 $(this).before("Title: <input type='text' name='title' class='title " + random_class + "'><select style='width: 100px;' class='title " + random_class + "'><option>h1</option><option>h2</option><option>h3</option><option>h4</option><option>h5</option><option>h6</option><br>");
		 });
	 });
	 
	 $(document).on("click", ".shortcode_generator i", function(){ 
	 	if(!$(this).hasClass('no_custom') && !$(this).data('no_custom')){
			var icon = $(this).attr('class');
					
			jQuery.ajax({
			   type : "post",
			   url : myAjax.ajaxurl,
			   data : {action: "customize_icon", icon: icon},
			   success: function(response) {
				  $(".shortcode_generator").html(response).fadeIn();
			   }
			});
		}
	 });
	 
	 // Testimonial Modal
	 $(document).on("click", ".edit_testimonials", function(){
		 var id    = $(this).data("id");
		 var value = $("#" + id).val();
		 
		 $( "#testimonial_window" ).dialog({
			resizable: true,
			height: "auto",
			width: 400,
			modal: true,
			open: function(){
				jQuery.ajax({
				   type : "post",
				   url : myAjax.ajaxurl,
				   data : {action: "testimonial_widget_fields", value: value},
				   success: function(response) {
					   $("#testimonial_window .load").html(response).fadeIn();
				 
					$(".remove_jquery_button_class").removeClass("ui-widget ui-state-default");
				   }
				});
			},
			buttons: [
				{
					text: "Add Testimonial",
					"class": "button-primary remove_jquery_button_class",
					click: function(){					
						  var number = parseInt($("#testimonial_window textarea:last").attr("name").slice(-1)) + 1;
						  var html = "<tr><td>Name:</td><td> <input type='text' name='testimonial_name_" + number + "'>&nbsp; <i class='fa fa-times remove_testimonial'></i></td></tr><tr><td>Text: </td><td> <textarea name='testimonial_text_" + number + "'></textarea></td></tr>"; 
						  
						  $("#testimonial_window .load tr:last").after(html);
					}
				},
				{
					text: "Finish",
					"class": "button-primary remove_jquery_button_class",
					click: function(){					
						  var serialized = $("#testimonial_form").serialize();
						  $(".testimonial_fields").val(serialized);
						  
						  $( this ).dialog( "close" );
					}
				},
				{
					text: "Cancel",
					"class": "button-primary remove_jquery_button_class",
					click: function(){					
				 		  $( this ).dialog( "close" );
					}
				},
			]
		  });
	 });
	 
	 $(document).on("click", ".remove_testimonial", function(){
		 var row1 = $(this).closest('td').parent()[0].sectionRowIndex;
		 var row2 = row1 + 1;
		 
		 $("#testimonial_window .load tr").eq(row2).fadeOut( function(){ $(this).remove(); } );
		 $("#testimonial_window .load tr").eq(row1).fadeOut( function(){ $(this).remove(); } );	
	 });
	 
	 // List Item Modal
	 $(document).on("click", ".edit_list", function(){
		 var id    = $(this).data("id");
		 var value = $("#" + id).val();
		 
		 $( "#list_window" ).dialog({
			resizable: true,
			height: "auto",
			width: 320,
			modal: true,
			open: function(){
				jQuery.ajax({
				   type : "post",
				   url : myAjax.ajaxurl,
				   data : {action: "list_widget_fields", value: value},
				   success: function(response) {
					   $("#list_window .load").html(response).fadeIn();
				   }
				});
				
				$(".remove_jquery_button_class").removeClass("ui-widget ui-state-default");
			},
			buttons:
			[
				{
					text: "Add List Item",
					"class": "button-primary remove_jquery_button_class",
					click: function(){					
						var html = "<tr><td>List Item: </td><td> <input type='text' name='list_item'>&nbsp; <i class='fa fa-times remove_list_item'></i></td></tr>"; 
						
						$("#list_window .load tr:last").after(html);
					}
				},
				{
					text: "Finish",
					"class": "button-primary remove_jquery_button_class",
					click: function(){					
						var serialized = $("#list_form").serialize();
						$(".list_fields").val(serialized);
						
						$( this ).dialog( "close" );
					}
				},
				{
					text: "Cancel",
					"class": "button-primary remove_jquery_button_class",
					click: function(){
				  		$( this ).dialog( "close" );
					}
				},
			]
		  });
	 });
	 
	 $(document).on("click", ".remove_list_item", function(){
		 var row = $(this).closest('td').parent()[0].sectionRowIndex;
		 
		 $("#list_window .load tr").eq(row).fadeOut( function(){ $(this).remove(); } );		 
	 });
	 
	 // portfolio format
	 $(".portfolio-post-format").click( function(){
		 var format       = $(this).val();
		 var content_area = $("#portfolio_content-meta .inside");
		 
		 
		 jQuery.ajax({
			 type : "post",
			 url : myAjax.ajaxurl,
			 data : { action: "portfolio_editor", format: format, post_id: myAjax.post_id },
			 success: function(response) {
				content_area.html(response);				
			 }
		  });
	 });
	 
	 var sortable_options = {
		 helper: function(e, tr) {
					var $originals = tr.children();
					var $helper = tr.clone();
					$helper.children().each(function(index) {
					    // Set helper cell sizes to match the original sizes
					    $(this).width($originals.eq(index).width());
					});
					return $helper;
				 },
		  handle: $(".detail_handle"),
		  items: 'tr:not(:last)',
		  stop: function(event, ui){
			  $(".addition_details tr:not(:last)").each( function(index, element){							
					$(this).find("input").each( function(iindex, eelement){
							var name = $(this).attr("name");
							
							$(this).attr("name", name.replace(/(\d+)/g, index));
					});
			  }); 
		  }
	 };
	 
	 $(document).on("click", ".add_itional_details", function(e){
		e.preventDefault();
		
		var index = ($(".addition_details tr").length - 1);
		var label = $(".addition_details tr td input[name$='[label]']").attr("placeholder");
		var value = $(".addition_details tr td input[name$='[value]']").attr("placeholder");
		
		var html = "<tr><td><input type='text' name='additional_details[" + index + "][label]' placeholder='" + label + "'> </td> <td>: <input type='text' name='additional_details[" + index + "][value]' placeholder='" + value + "'> <i class='fa fa-times delete_detail'></i> <i class='fa fa-arrows detail_handle'></i></td></tr>";
	 
	 	$(".addition_details tr:last").before(html);
		$(".addition_details").sortable(sortable_options);
	 });
	 
	 if($(".additional_details").length){
	 	$(".addition_details").sortable(sortable_options);
	 }

	 $(document).on("click", ".delete_detail", function(e){
		e.preventDefault();
		
		$(this).closest("tr").fadeOut(200, function(){
			$(this).remove();
		});
	 });

	 /* listing categories */
	 // Return a helper with preserved width of cells
	var fixHelper = function(e, ui) {
	    ui.children().each(function() {
	        $(this).width($(this).width());
	    });
	    return ui;
	};

	if($("table.listing_categories tbody").length){
	 $("table.listing_categories tbody").sortable({
	 	helper: fixHelper,
	 	handle: '.handle',
	 	containment: "parent"
	 }).disableSelection();
	}

	 $(".badge_color").change( function(){
	 	if($(this).val() == "custom"){
	 		$(".badge_hint").fadeIn();
	 	} else {
	 		$(".badge_hint").fadeOut();
	 	}
	 });

	$(".import_listing_categories").click( function(e){
		var thisel = $(this);
		e.preventDefault();
		
		jQuery.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: { action: 'import_listing_categories' },
			success: function(response){
				alert(response);
				thisel.closest("tr").hide();				
			}
		});
		
	});

	// $(document).on("change", ".use-listing-categories", function(){
	// 	$(".choose_listing_container").slideToggle();
	// });

	function progress(percent, $element) {
	    var progressBarWidth = percent * $element.width() / 100;
	    $element.find('div').animate({ width: progressBarWidth }, 500);//.html(percent + "%&nbsp;");
	}
	
	function increment_installer_step(){
		var current_step = parseInt($(".progress_steps .current_step").text());
		var total_steps  = parseInt($(".total_steps").text());

		var new_value    = parseInt(current_step + 1);

		$(".progress_steps .current_step").text(new_value);

		progress(((new_value / total_steps) * 100), $( "#progressbar" ));
	}

	function hide_installer_progress(){
		$("#progressbar, .progress_steps").slideUp();
		$(".loading_icon_spinner").fadeOut();
		$(".progress_steps .current_step").text(1);

		progress(0, $( "#progressbar" ));
	}

	$(document).on("click", ".install_automotive_demo_content", function(e){
		e.preventDefault();

		$(".loading_icon_spinner").fadeIn();

		$( "#progressbar, .progress_steps" ).slideDown(200, function(){
			$( "#progressbar, .progress_steps" ).css("display", "block");

			$( "#progressbar" ).progressbar({
		      	value: 0
		    });
		});

		// step 1
		jQuery.ajax({
			beforeSend : function (){
				increment_installer_step();
			},
			url: myAjax.ajaxurl,
			type: 'POST',
			data: { action: 'automotive_demo_content_installer', step: 1 },
			success: function(response){
				if(response == "success"){

					// run step 2
					jQuery.ajax({
						beforeSend : function (){
							increment_installer_step();
						},
						url: myAjax.ajaxurl,
						type: 'POST',
						data: { action: 'automotive_demo_content_installer', step: 2 },
						success: function(response){							
							if(response == "success"){
								
								// run step 3
								jQuery.ajax({
									beforeSend : function (){
										increment_installer_step();
									},
									url: myAjax.ajaxurl,
									type: 'POST',
									data: { action: 'automotive_demo_content_installer', step: 3 },
									success: function(response){							
										if(response == "success"){

											// run step 4
											jQuery.ajax({
												beforeSend : function (){
													increment_installer_step();
												},
												url: myAjax.ajaxurl,
												type: 'POST',
												data: { action: 'automotive_demo_content_installer', step: 4 },
												success: function(response){							
													if(response == "success"){

														// run step 5
														jQuery.ajax({
															beforeSend : function (){
																increment_installer_step();
															},
															url: myAjax.ajaxurl,
															type: 'POST',
															data: { action: 'automotive_demo_content_installer', step: 5 },
															success: function(response){							
																if(response == "success"){

																	// run step 6
																	jQuery.ajax({
																		beforeSend : function (){
																			increment_installer_step();
																		},
																		url: myAjax.ajaxurl,
																		type: 'POST',
																		data: { action: 'automotive_demo_content_installer', step: 6 },
																		success: function(response){							
																			if(response == "success"){

																				// run step 7
																				jQuery.ajax({
																					beforeSend : function (){
																						increment_installer_step();
																					},
																					url: myAjax.ajaxurl,
																					type: 'POST',
																					data: { action: 'automotive_demo_content_installer', step: 7 },
																					success: function(response){							
																						if(response == "success"){

																							// run step 8
																							jQuery.ajax({
																								beforeSend : function (){
																									increment_installer_step();
																								},
																								url: myAjax.ajaxurl,
																								type: 'POST',
																								data: { action: 'automotive_demo_content_installer', step: 8 },
																								success: function(response){							
																									if(response == "success"){

																										// run step 9
												                                                        jQuery.ajax({
												                                                            beforeSend : function (){
												                                                                increment_installer_step();
												                                                            },
												                                                            url: myAjax.ajaxurl,
												                                                            type: 'POST',
												                                                            data: { action: 'automotive_demo_content_installer', step: 9 },
												                                                            success: function(response){                            
												                                                                if(response == "success"){

												                                                                    $(".import_complete").slideDown();
												                                                                    hide_installer_progress();

												                                                                } else {
												                                                                    hide_installer_progress();
												                                                                    alert("Error on step 9");
												                                                                }

												                                                            }
												                                                        });
																										
																									} else {
																										hide_installer_progress();
																										alert("Error on step 8");
																									}

																								}

																							});
																							
																						} else {
																							hide_installer_progress();
																							alert("Error on step 7");
																						}

																					}

																				});
																				
																			} else {
																				hide_installer_progress();
																				alert("Error on step 6");
																			}

																		}
																	
																	});

																} else {
																	hide_installer_progress();
																	alert("Error on step 5");
																}

															}

														});
														
													} else {
														hide_installer_progress();
														alert("Error on step 4");
													}

												}
												
											});
										} else {
											hide_installer_progress();
											alert("Error on step 3");
										}

									}
								});
							} else {
								hide_installer_progress();
								alert("Error on step 2");
							}

						}
					});
				} else {
					hide_installer_progress();
					alert("Error on step 1");
				}	
			},
			error: function(){
				alert("Seems like your server quit the one click installer process, try importing the demo content using the files in the download package.")
			}
		});
	});

	$(document).on("click", ".toggle_seo_options", function(e){
		e.preventDefault();

		$("form[name='seo_listing']").slideToggle();

		if($("table.listing_categories").hasClass("seo_active")){
			$("table.listing_categories").removeClass("seo_active");
		} else {
			$("table.listing_categories").addClass("seo_active");
		}
	});

	$(document).on("click", "table.seo_active i", function(e){
		e.preventDefault();

		$(".seo_string_holder").val($(".seo_string_holder").val() + "%" + $(this).data("name") + "% ");
	});

	$(".remove_option_categories").click( function(){
		var thisel = $(this);

		jQuery.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: { action: 'hide_import_listing_categories' },
			success: function(){
				thisel.closest("tr").hide();
			}
		});
	});

	$(document).on("click", ".save_import_categories", function(e){

		e.preventDefault();

		jQuery.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: { action: 'save_import_categories', form: $("#csv_import").serialize() },
			success: function(response){
				alert(response);
			}
		});
	});

	$(document).on("click", ".save_vin_import_categories", function(e){

		e.preventDefault();

		jQuery.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: { action: 'save_vin_import_categories', form: $("#vin_import_form").serialize() },
			success: function(response){
				alert(response);
			}
		});
	});

	$(document).on("click", ".toggle_listing_features", function(e){
		e.preventDefault();

		jQuery.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: { action: 'toggle_listing_features' },
			success: function(response){
				if(response == "disabled"){
					$(".toggle_listing_features").text("Enable Listing Features");
				} else if(response == "enabled"){
					$(".toggle_listing_features").text("Disabled Listing Features");
				}
			}
		});
	});
});
})(jQuery);
