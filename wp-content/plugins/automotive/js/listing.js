(function($) {
  "use strict";

jQuery.noConflict();

(function() {
    var blockPopstateEvent = true;
    window.addEventListener("load", function() {
        setTimeout(function() {
            blockPopstateEvent = false;
        },0);
    }, false);
    window.addEventListener("popstate", function(evt) {
        if (blockPopstateEvent && document.readyState=="complete") {
            evt.preventDefault();
            evt.stopImmediatePropagation();
        }
    }, false);
})();

jQuery(document).ready(function($){	
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
	
	//********************************************
	//	Plugin Calls
	//***********************************************************
	$("#video_box").modal({ show: false });
	
	$(document).on({
		mouseleave: function(){
			$(this).stop(true, true).toggle( "scale", function(){
				$(this).remove();
			});
		}
	}, '.preview_image');

	function masonry_boxed(){
		if($(".car_listings.boxed").length){
			// $('.car_listings.boxed').masonry({
			//   itemSelector: '.col-xs-12'
			// });
			$(".car_listings.boxed").isotope({
				itemSelector: '.col-xs-12',
				sortBy: 'order-order',
				layoutMode: 'fitRows'
			});
		}
	}

	function masonry_destroy(){
		if($(".car_listings.boxed").length){
			// $('.car_listings.boxed').masonry('destroy');
			$(".car_listings.boxed").isotope('destroy');
		}
	}

	masonry_boxed();	
			
	function commaSeparateNumber(val){
		while (/(\d+)(\d{3})/.test(val.toString())){
		  val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		}
		return val;
	}
	
	function animate_number(el, value){		
		var original = value;
		    value    = parseInt(value);
	
		$({ someValue: 0 }).animate({ someValue: value }, {
			duration: 3000,
			easing: 'easeOutExpo', 
			step: function() { 
				el.text(commaSeparateNumber(Math.round(this.someValue)));
			},
			complete: function(){
				// ensure correct number appears after animate, wierd bugs in cause larger numbers to lose a few digits //
				el.text(commaSeparateNumber(Math.round(original)));
			}
		});
	}
	
	// animate numbers
	if($(".animate_number").length){
		$(".animate_number").each( function(){					
			var el 	  = $(this).find(".number");
			
			el.data('value', el.text());
			el.text(0);
			
			$(this).one('inview', function(event, isInView, visiblePartX, visiblePartY){
				var value = el.data('value').replace(/[^0-9]/gi, '');					
				
				if(isInView){
					setTimeout( function(){
						animate_number(el, value);
					}, 500);
				}
				
			});
		});
	}
	
	$(window).on('resize', function(){
		if($("div.inventory").length){
			//$("div.inventory").evenElements();
		}
	});
	
	function reset_even_elements(element){
		$(element).removeAttr("style").css('transition-duration', 0);
	}
	
	// portfolio filter
	if($('#portfolio_grid').length){
		$('#portfolio_grid').mixItUp();
	}

	$(".portfolioFilter li a").click(function(e){ e.preventDefault(); })
	
	// portfolio sorting
	if($(".portfolioContainer").length){
		var portfolio_options = {load:{}};

		if(!$(".portfolioFilter li.active").length){
			var sort_by = $(".portfolioFilter li:first a").data("filter");
		
			$(".portfolioFilter li:first").addClass("active");

			portfolio_options.load.filter = sort_by;
		}

		$(".portfolioContainer").mixItUp(portfolio_options);
	}
	
		
	$("button[data-hover]").on({
		mouseenter: function() {
			$(this).css({
				"backgroundColor": $(this).data("hover"),
				"background":      $(this).data("hover")
			});
		}, 
		mouseleave: function() {
			$(this).css({
				"backgroundColor": $(this).data("color"),
				"background":      $(this).data("color"),
			});
		}
	});
	
	function resize_chosen_dropdowns(){
		if($(".listing_sidebar_widget .dropdowns .chosen-container").length && $(".listing_sidebar_widget .dropdowns .chosen-container").css('width') != "100%"){
			$(".listing_sidebar_widget .dropdowns .chosen-container").css('width', '100%');
		}
	}
	
	function remove_class_later(class_name){
		var t = setTimeout(function(){
			$("div.inventory").each(function(index, element) {
				if($(this).hasClass(class_name)){
					$(this).removeClass(class_name);
				}
						
				if($(this).hasClass("undefined")){
					$(this).removeClass("undefined");
				}
			});
		}, 1300);
	}
	
	
	$(".recent_listings li.even_elements .desc").evenElements();
		
	$(".recent_listings li").hover( function(){
		$(this).find(".layer").slideDown("fast");
	},
	function(){
		$(this).find(".layer").slideUp("fast");
	});

	function scroll_to_top_page(){
		var $ = jQuery;
		$("html, body").animate({ scrollTop: "300px" });
	}
	
	// select view buttons
	$(document).on("click", ".page-view li", function(e){
		e.preventDefault();
		
		var layout = $(this).data("layout");

		$(".select_view").data("layout", layout);
		
		$(".page-view li.active").removeClass('active');
		$(this).addClass('active');

		masonry_destroy();

		var params = getQueryStringAsObject();

		$("ul.filter li").each(function(index, element) {
			var type  = $(this).data("type");
			var value = encodeURIComponent($(this).find("span").text());
			
			params[type] = value;		
		});


		params = JSON.stringify(params);

		$.ajax({
			type: "post",
			url: listing_ajax.ajaxurl,
			data: { action: "generate_new_view", layout: layout, params: params, page: $(".page_of").data("page"), page_id: listing_ajax.post_id },
			dataType: "json",
			success: function(response) {				
				$(".generate_new").slideUp(400, function(){
					$(this).html(response.html);
					$(this).slideDown(400, function(){
						masonry_boxed();

						if(layout == "wide_left" || layout == "boxed_left"){
							var pagination_classes = "col-lg-9 col-md-9 col-sm-12 col-xs-12 col-lg-offset-3";
						} else if(layout == "wide_right" || layout == "boxed_right"){
							var pagination_classes = "col-lg-9 col-md-9 col-sm-12 col-xs-12";
						} else {
							var pagination_classes = "col-lg-12 col-md-12 col-sm-12 col-xs-12";
						}

						// pagination bottom
						$(this).append("<div class='" + pagination_classes + " pagination_container'>" + response.bottom_page + "</div>");
					});

					push_url_vars($(".page_of").data("page"));

					// pagination top
					$(".page_of").parent().html(response.top_page);

					init_listing_filters();
				});
			}
		});
	});
	
	if($(".faq").length){
		// faq shortcode
		if(window.location.hash) {
			var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
			
			$(".sort_container a").each( function(index, element){
				if($(this).text().indexOf(hash) !== -1){
					//$(this).css('font-weight', 'bold');
				}
			});
			
			if(hash != "All"){
				$(".faq .accordion-group").each(function(index, element) {
					var in_categories = $(this).data('categories');
					
					if(in_categories.indexOf(hash) === -1){
						$(this).hide({effect: "fold", duration: 600});
					} else {
						$(this).show({effect: "fold", duration: 600});
					}
				});
			}
		} else {
			// No hash found
		}
		
		$(".faq .accordion-toggle").click( function(){
			href = $(this).attr('href');
			
			if($("a[href='" + href + "'] .gray_button i").hasClass("fa-minus")){			
				$("a[href='" + href + "'] .gray_button i").removeClass("fa-minus").addClass("fa-plus");
			} else {
				$(".faq .gray_button i.fa-minus").removeClass("fa-minus").addClass("fa-plus");
				$("a[href='" + href + "'] .gray_button i").removeClass("fa-plus").addClass("fa-minus");
			}
		});
		
		$("a[data-action='sort']").click( function(){
			var category = $(this).attr("href").replace("#", "");
			var faqs     = $(".faq .accordion-group");
			
			$(".sort_container a").each( function(index, element){
				$(this).css('font-weight', 'normal');
			});
			
			if(category == "All"){
				faqs.each(function(index, element) {
					$(this).show({effect: "fold", duration: 600});
				});
			} else {
				faqs.each(function(index, element) {
					var in_categories = $(this).data('categories');
					
					if(in_categories.indexOf(category) === -1){
						$(this).hide({effect: "fold", duration: 600});
					} else {
						$(this).show({effect: "fold", duration: 600});
					}
				});
			}
		});
	}
	
	// Inventory Listings Filter
	$(".dropdowns select, .dropdowns_mobile select").change( function(){
		var items = $("ul.filter li").length;
		var chose = $(this).val();
		var type  = ($(this).attr("name") == "year" ? "yr" : $(this).attr("name"));
		var id    = $(this).attr("id");
		var slug  = $("#" + id + " option:selected").data("slug");
		
		var no_options = $("select[name='" + type + "'] option:selected").data('no-options');
		
		if(!!no_options){
			return;
		}
		
		// If no filters are set
		if(items == 1 && $("ul.filter li").eq(0).data("filter") == "All"){
			$("ul.filter li").eq(0).remove();
		}
				
		if($("ul.filter li[data-type='" + type + "']").length){
			$("ul.filter li[data-type='" + type + "']").html("<li data-type='" + type + "' data-slug='" + slug + "'><a href=''><i class='fa fa-times-circle'></i> " + chose + "</a></li>").fadeIn();
		} else {
			$("<li data-type='" + type + "' data-slug='" + slug + "'><a href=''><i class='fa fa-times-circle'></i> " + chose + "</a></li>").appendTo("ul.filter").hide().fadeIn();
		}
		
		$("select[name='" + type + "']").val(chose);
				
		update_results(1);
	});
	
	// Deselect Vehicles
	$(".top_buttons").on("click", ".deselect", function(e){
		e.preventDefault();
		
		$('input:checkbox').removeAttr('checked');
		
		$.removeCookie('compare_vehicles', { path: '/' });
		compare_vehicles();
	});
	
	// tooltip
	$('.tooltip_js').tooltip();
	
	$("*[data-toggle='popover']").each( function(index, element){	
		$(this).popover();
	});
	
	$("*[data-toggle='popover']").click( function(e){
		e.preventDefault();
	});
	
	$('.tabs_shortcode a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})
	
	// reset filters
	$(".top_buttons").on("click", ".reset", function(e){
		e.preventDefault();
		
		$("ul.filter li").each( function(){
			$(this).fadeOut(400, function(){
				var name = ($(this).data("type") == "year" ? "yr" : $(this).data("type")).replace(/-/g,'_');
				var sb   = $(".listing_select select[name='" + name + "']").attr("sb");
				
				$(".listing_select select[name='" + name + "']").prop("selectedIndex", 0);
				$("#sbSelector_" + sb).text($(".listing_select select[name='" + name + "'] option:selected").text());
				
				// if sidebar 
				if($(".listing-sidebar select[name='" + name + "']").length){
					var sb = $(".listing-sidebar select[name='" + name + "']").attr("sb");

					$(".listing-sidebar select[name='" + name + "']").prop("selectedIndex", 0);
					$("#sbSelector_" + sb).text($(".listing-sidebar select[name='" + name + "'] option:selected").text());
				}

				// if has dependancy
				if($(".listing_filter[data-dependancy='" + name + "']").length){
					$("select[data-dependancy='" + name + "']").html("<option>Choose a " + name + "</option>");
					$("select[data-dependancy='" + name + "']").selectbox('detach').selectbox('attach');
				}
				
				$(this).remove();
			});
		});
		
		$("ul.filter").html("<li data-type='All' data-filter='All'>All Listings</li>");
		
		update_results(1);
		$(".current_page").text("1");
	});
	
	function push_url_vars(current_page_number){
		// init data
		if(!$(".page_of").data("page") && $(".page_of").attr("data-page")){
			$(".page_of").data("page", $(".page_of").attr("data-page"));
		}

		var query_vars = window.location.search;
		
		// if permalinks are set
		if(listing_ajax.permalink_set == "true"){
			var parameters = "?";
		} else {
			var parameters = (query_vars.charAt(0) == "?" ? "&" : "?");
		}

		var current_page = parseInt($(".page_of").data("page"));
		var total        = 0;
		
		// category parameters
		$("ul.filter li").each( function(index, element){
			var type 			  = $(this).data('type');

			var span 			  = $(this).find("span");
			var translated_option = span.data("val");

			if(translated_option){
				var text = translated_option;
			} else {
				var text = span.text();
			}

			// var text = (type == "certified" || type == "verified" || type == "brand_new" ? "on" : );
			
			if($(this).data('filter') != "All"){
				total += 1;

				var type = $.trim(type).replace(/\s+/g, '-').toLowerCase();

				var text = $.trim(text).replace(/-/g, '--').toLowerCase();
				text = $.trim(text).replace(/\s+/g, '-').toLowerCase();
				text = encodeURIComponent(text);

				if(type.slice(-2) == "[]"){
					//var values = text.split("-");
					var min = $(this).data('min');
					var max = $(this).data('max');

					parameters += type + "=" + $.trim(min) + "&" + type + "=" + $.trim(max) + "&";
				} else if(type == "keywords"){
					parameters += type + "=" + $.trim(text) + "&";
				} else {
					text.replace(/\s+/g, '-').toLowerCase();
					parameters += (type == "year" ? "yr" : type) + "=" + text + "&";
				}
			}
		});
		
		// order parameter
		if($("select[name='price_order']").val()){
			var order_category 	= $("select[name='price_order']").val();
			var selected_index  = $("select[name='price_order']")[0].selectedIndex;
			var orderby 		= $("select[name='price_order'] option").eq(selected_index).data("orderby");

			if(order_category != "none"){
				parameters += "order=" + $("select[name='price_order']").val() + "&";
				total += 1;
			}
		}
		
		// page parameter
		parameters += (current_page == 1 ? "" : "paged=" + current_page + "&");
				
		history.pushState('', '', listing_ajax.current_url + parameters.slice(0, -1));		
	}	

	var getQueryStringAsObject = function() {
	    var b, cv, e, k, ma, sk, v, r = {},
	        d = function (v) { return decodeURIComponent(v).replace(/\+/g, " "); }, 
	        q = window.location.search.substring(1),
	        s = /([^&;=]+)=?([^&;]*)/g;

	    ma = function(v) {
	        if (typeof v != "object") {
	            cv = v;
	            v = {};
	            v.length = 0;

	            if (cv) { Array.prototype.push.call(v, cv); }
	        }
	        return v;
	    };

	    while (e = s.exec(q)) { 
	        b = e[1].indexOf("[");
	        v = d(e[2]);

	        if (b < 0) { 
	            k = d(e[1]);

	            if (r[k]) {
	                r[k] = ma(r[k]);
	                Array.prototype.push.call(r[k], v);
	            } else {
	                r[k] = v;
	            }
	        } else {
	            k = d(e[1].slice(0, b));
	            sk = d(e[1].slice(b + 1, e[1].indexOf("]", b)));

	            r[k] = ma(r[k]);

	            if (sk) { 
	            	r[k][sk] = v; 
	            } else { 
	            	Array.prototype.push.call(r[k], v); 
	            }
	        }
	    }

	    // remove page id
	    delete r['page_id'];
	    delete r['paged'];
	    
	    return r;
	};
	
	function arraysEqual(a, b) {
	  if (a === b) return true;
	  if (a == null || b == null) return false;
	  if (a.length != b.length) return false;
	
	  // If you don't care about the order of the elements inside
	  // the array, you should sort both arrays here.
	
	  for (var i = 0; i < a.length; ++i) {
		if (a[i] !== b[i]) return false;
	  }
	  return true;
	}
	
	var the_parameters = (typeof the_parameters == "undefined" ?  getQueryStringAsObject() : the_parameters);
	
	if($(".car_listings").length){
		// This event is triggered when you visit a page in the history
		// like when you push the "back" button
		$(window).on('popstate', function(e){
			e.preventDefault();

			var new_parameters  = getQueryStringAsObject();
			var keep_parameters = {};

			if(!$.isEmptyObject(new_parameters)){
				$("ul.filter li").each(function(index, element) {
					var type  = $(this).data("type");
					var span  = $(this).find("span");

					type = (type == "year" ? "yr" : type);

					if(!(type in new_parameters)){
						$(this).remove();
					}
				});
			} else {
				$("ul.filter li").each(function(index, element) { 
					$(this).remove();
				});
			}
			
			if(arraysEqual(the_parameters, new_parameters)){						
				update_ajax_results();
			}
		});
	}
		
	// check box vehicles
	$("body").on("click", ".compare_vehicle", function(){
		
		if($(this).attr("checked")){
			var action = "checked";
		} else {
			var action = "unchecked";
		}
				
		var cookie  = decodeURIComponent($.cookie('compare_vehicles'));
		var cookiet = $.cookie('compare_vehicles');
		
		if(typeof cookiet == "undefined" || !cookiet){
			
			var vehicles = new Array();
			
			$(".compare_vehicle:checked").each( function(index, element){
				var id = $(this).data('id');
				
				vehicles.push(id);
			});
			
			var vehicles_safe = encodeURIComponent(vehicles);//.join(','));
		} else {		
		
			var ids = cookie.split(",");
			
			if(action == "checked"){
				ids.push($(this).data('id'));
			} else {
				var index = ids.indexOf(String($(this).data('id')));
				ids.splice(index, 1);
			}
			
			var vehicles_safe = encodeURIComponent(ids.join(','));
		}
							
		$.cookie('compare_vehicles', vehicles_safe, { path: '/' });
		compare_vehicles();
		compare_button_link($(this).data('id'));
	});
	
	// check checkboxes if vehicles are checked
	var compare_cookie = decodeURIComponent($.cookie('compare_vehicles'));
	
	if(typeof compare_cookie != "undefined"){
		var ids = compare_cookie.split(",");
	}
	
	compare_button_link();
	
	// compare vehicles button
	function compare_button_link(car_id){
		car_id = car_id || decodeURIComponent($.cookie('compare_vehicles'));
		
		var number = $(".number_of_vehicles").html();
		
		if(number == 1){
			jQuery.ajax({
			   type : "post",
			   url : listing_ajax.ajaxurl,
			   data : { action: "get_single_permalink", id : car_id },
			   success: function(response) {
				   	var compare_link = "<a href='" + response + "'></a>";				 
		
					if($(".top_buttons li > a.compare").length){
						$(".top_buttons li > a.compare").attr("href", response);
					} else {
						$(".top_buttons .compare").wrap(compare_link);
					}
			   }
			});
		} else if(number >= 2 && typeof listing_ajax.compare != "undefined"){
			if(listing_ajax.compare.length){
				$(".top_buttons a.compare").attr("href", listing_ajax.compare);
			}
		}
	}
	
	// remove a filter
	$("ul.filter").on("click", "li", function(e){				
		e.preventDefault();
		
		if($(this).data('type') == "All"){
			return false;
		}
		
		$(this).fadeOut(400, function(){
			var name = $(this).data("type");

			// min max
			if(name.slice(-2) == "[]"){

			} else {
				var sb   = $(".listing_select select[name='" + name + "']").attr("sb");
				
				$(".listing_select select[name='" + name + "']").prop("selectedIndex", 0);
				$("#sbSelector_" + sb).text($(".listing_select select[name='" + name + "'] option:selected").text());
				
				// if sidebar 
				if($(".listing-sidebar select[name='" + name + "']").length){
					var sb = $(".listing-sidebar select[name='" + name + "']").attr("sb");

					$(".listing-sidebar select[name='" + name + "']").prop("selectedIndex", 0);
					$("#sbSelector_" + sb).text($(".listing-sidebar select[name='" + name + "'] option:selected").text());
				}

				// if has dependancy
				if($(".listing_filter[data-dependancy='" + name + "']").length){
					$("select[data-dependancy='" + name + "']").html("<option>Choose a " + name + "</option>");
					$("select[data-dependancy='" + name + "']").selectbox('detach').selectbox('attach');
				}
			}

			$(this).remove();
			update_results();
		});
	});
	
	// pagination
	$(document).on("click", ".page_of .right-arrow", function(e){
		e.preventDefault();
		
		var current_page = parseInt($(".page_of").data("page"));
		var total_pages  = parseInt($(".page .total_pages").text());
		
		if($(this).hasClass("disabled")){
			return false;
		}
		
		if(current_page < total_pages){			
			update_results("next");
			$(".page_of .current_page").text(current_page + 1);
		}
	});
	
	$(document).on("click", ".page_of .left-arrow", function(e) {
		e.preventDefault();
		
		var current_page = parseInt($(".page_of").data("page"));
		var total_pages  = parseInt($(".page_of .total_pages").text());
		
		if($(this).hasClass("disabled")){
			return false;
		}
		
		update_results("prev");
		$(".page_of .current_page").text(current_page - 1);
	});
	
	$(document).on("click", ".bottom_pagination li[data-page]", function() {
		if(!$(this).hasClass("nojs")){
			var page         = $(this).data("page");
			var current_page = parseInt($(".page_of").data("page"));
			var total_pages  = parseInt($(".total_pages").text());

			if(page == "next" && (current_page == total_pages)){
				return false;
			}


			if(page != "next" && page != "previous"){
				update_results(page);
				$(".current_page").html(page);
			} else {			
				if(page == "next"){
					update_results(current_page + 1);
				} else if(page == "previous") {
					update_results(current_page - 1);
				}
			}
		}
	});

	$(document).on("click", ".bottom_pagination li[data-page] a", function(e){
		e.preventDefault();
	});
	
	function compare_vehicles() {
		var vehicles = decodeURIComponent($.cookie("compare_vehicles"));
		var vehicles = vehicles.split(",").length;
		
		if(typeof($.cookie("compare_vehicles")) == "undefined" || (typeof($.cookie("compare_vehicles")) == "string" && !$.cookie("compare_vehicles"))){
			var vehicles = 0;
		}
		
		if(vehicles == 1){
			$(".compare_grammar").html(listing_ajax.singular_vehicles);
		} else {
			$(".compare_grammar").html(listing_ajax.plural_vehicles);
		}
		
		$(".number_of_vehicles").html(vehicles);
	}
	
	function filter_results(page){
		var parameters = "action=filter_listing";
		
		// if layout is set
		parameters = parameters + "&layout=" + $(".select_view").data("layout");
		
			
		$("ul.filter li").each(function(index, element) {
			var type  = $(this).data("type");
			var span  = $(this).find("span");

			var translated_option = span.data("val");

			if(translated_option){
				var value = translated_option;
			} else {
				var value = span.text();
			}

			value = encodeURIComponent(value);
			
			if(type.slice(-2) == "[]"){
				var min = $(this).data('min');
				var max = $(this).data('max');

				parameters = parameters + "&" + type + "=" + min + "&" + type + "=" + max;
			} else {
				parameters = parameters + "&" + type + "=" + value;
			}
		});
		
		if(page !== false){
			parameters = parameters + "&paged=" + page;
			
			// set new page
			$(".page_of").data("page", page);
			$(".current_page").text(page);
		}

		// order by params
		var order    	= $("select[name='price_order']").val();
		var numeric 	= '';
		var order_param = "&order=" + order;
		
		return parameters + order_param + numeric;
	}

	$(document).on("click", ".find_new_vehicle", function(){
		$(".find_new_vehicle .loading_results").css("display", "inline-block");
	});
	
	function update_results(next_page){		
		var next_page = (typeof next_page === "undefined") ? false : next_page;

		$(".loading_results").css("display", "inline-block");
		
		if(next_page == "prev"){
			next_page = parseInt($(".page_of").data("page"))-1;
		} else if(next_page == "next"){
			next_page = parseInt($(".page_of").data("page"))+1;
		}
		// update listings
		jQuery.ajax({
         	type : "post",
         	url : listing_ajax.ajaxurl,
         	data : filter_results(next_page),
			dataType: "json",
         	success: function(response) {
             	$(".car_listings").slideUp(400, function(){
             		scroll_to_top_page();
					$(this).html(response.content).slideDown(400, function(){
						// update number of listings
						var listings = response.number;
						var grammar  = (listings == 1 ? listing_ajax.singular_vehicles : listing_ajax.plural_vehicles);
						
						$(".number_of_listings").html(listings);
						$(".listings_grammar").html(grammar);
				
						var current_page_number = $(".current_page").text();

						$(".page_of").data("page", current_page_number);

						push_url_vars(current_page_number);
						
						var current_page_on = $(".page_of").data("page");

						// pagination top
						$(".page_of").parent().html(response.top_page);

						// pagination bottom
						$("div.pagination_container").html(response.bottom_page);
						masonry_destroy();
						masonry_boxed();

						$(".loading_results").hide();
					});
				});
				
         	},
		 	error: function(response) {
				console.log(response);
		 	}
      	});
	}
	
	function update_ajax_results(){
		$(".loading_results").css("display", "inline-block");

		var query_string = getQueryStringAsObject();
		var next_page    = (typeof query_string['paged'] == "undefined" ? 1 : query_string['paged']);

		var additional_params = "";

		var new_parameters = JSON.stringify(getQueryStringAsObject());

		// update listings
		jQuery.ajax({
         	type : "post",
         	url : listing_ajax.ajaxurl,
         	data : filter_results(next_page)/* + "&filter_params=" + new_parameters*/,
			dataType: "json",
         	success: function(response) {
             	$(".car_listings").slideUp(400, function(){
             		scroll_to_top_page();
					$(this).html(response.content).slideDown(400, function(){
						// update number of listings
						var listings = response.number;
						var grammar  = (listings == 1 ? "Vehicle" : "Vehicles");
						
						$(".number_of_listings").html(listings);
						$(".listings_grammar").html(grammar);
				
						var current_page_number = $(".current_page").text();
						
						$(".page_of").data("page", current_page_number);

						if(typeof response.filter != "undefined"){
							$.each(response.filter, function(index, value){
								if(value.singular && value.value){
									if(typeof value.value == "object"){
										$("<li data-type='" + index + "' data-slug='" + value.value + "'><a href=''><i class='fa fa-times-circle'></i> " + value.singular + ": " + value.value[0] + " - " + value.value[1] + "</a></li>").appendTo("ul.filter").hide().fadeIn();
									} else {
										$("<li data-type='" + index + "' data-slug='" + value.value + "'><a href=''><i class='fa fa-times-circle'></i> " + value.singular + ": " + value.value + "</a></li>").appendTo("ul.filter").hide().fadeIn();
									}
								}
							});	
						}

						// pagination top
						$(".page_of").parent().html(response.top_page);

						// pagination bottom
						$("div.pagination_container").html(response.bottom_page);

						masonry_destroy();
						masonry_boxed();

						$(".loading_results").hide();
					});
				});
				
         	},
		 	error: function() {
			 	alert("error");
		 	}
      	});
	}
	
	$(document).delegate(".view-video", "click", function(){
		var ele = $(this);
		
		$.fancybox({
				'href'       : '#youtube_video',
				'height'     : '320',
				'width'      : '560',
				'fitToView'  : false,
				'autoSize'   : false,
				'maxWidth'	 : '90%',
				'beforeLoad' : function(){
					var http_prefix = (listing_ajax.is_ssl ? "https" : "http");

					if(ele.data("video")){
						var video_url = http_prefix + '://player.vimeo.com/video' + ele.data("youtube-id");
					} else {
						var video_url = http_prefix + '://www.youtube.com/embed/' + ele.data("youtube-id") + '?vq=hd720&autoplay=1&rel=0';
					}

					$("#youtube_video iframe").attr("src", video_url);
				},
				'afterClose' : function(){
					$("#youtube_video iframe").attr("src", "");
				}
			});
	});	
	
	// Single Listing Tabs
	$(".listing_content").not(":first").hide();
	$("ul.listing_tabs li").click( function() {
		var datab = $(this).data('tab');
		
		$("ul.listing_tabs li.current").removeClass("current");		
		$(this).addClass("current");
		
		var tab = $(this).index();
		$(".listing_content:visible").fadeOut(400, function(){
			$(".listing_content").eq(tab).fadeIn(400, function(){
				if(typeof datab != "undefined" && datab == "map"){
					initialize_google_map();
				}
			});	
		});
	});
	
	function initialize_google_map(){
		jQuery(".google_map_init").each(function(index, element){
			var latitude     = $(this).data('latitude');
			var longitude    = $(this).data('longitude');
			var zoom         = $(this).data('zoom');
			var scroll_wheel = $(this).data('scroll');
			var style        = $(this).data('style');
			
			if(latitude && longitude){				
				var myLatlng = new google.maps.LatLng(latitude, longitude);
				var myOptions = {
					zoom: zoom,
					center: myLatlng,
					popup: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					scroll:{
						x:$(window).scrollLeft(),
						y:$(window).scrollTop()
					}
				}
				
				if(scroll_wheel == false && typeof scroll_wheel != "undefined"){
					myOptions.scrollwheel = false;
				}
				
				if(typeof style != "undefined"){
					myOptions.styles = style;
				}				
				
				var map = new google.maps.Map(this, myOptions);
				
				var marker = new google.maps.Marker({
					position: myLatlng, 
					map: map,
					title: "Our Location"
				});

				var offset = $(this).offset();
			    map.panBy(((myOptions.scroll.x-offset.left)/3),((myOptions.scroll.y-offset.top)/3));
			      
			    google.maps.event.addDomListener(window, 'scroll', function(){
				    var scrollY = $(window).scrollTop(),
				        scrollX = $(window).scrollLeft(),
				        scroll  = map.get('scroll');
				    
				    if(scroll){
						map.panBy(-((scroll.x-scrollX)/3),-((scroll.y-scrollY)/3));
				    }

				    map.set('scroll',{
				    	x:scrollX,
				    	y:scrollY
				    });
				});

				google.maps.event.addListener(marker, 'click', function() {
					map.setZoom(zoom);
				});
			}
		});
	}
	
	initialize_google_map();
	
	if($(".portfolio_flexslider").length){
		$('.portfolio_flexslider').flexslider({
		  animation: "slide",
		  controlNav: false,
		  prevText: "", 
		  nextText: "",  
		  rtl: $("body").hasClass("rtl") 
		});
	}

	if($(".flexslider_thumb").length){		
		 $('.flexslider_thumb').flexslider({
			animation: "slide",
			controlNav: false,
			directionNav:true,
			animationLoop: false,
			slideshow: false,
			itemWidth: 167,
			itemMargin: 5,
			asNavFor: '.flexslider_slider',
			prevText: '',
			nextText: '',
			rtl: $("body").hasClass("rtl")
		});	
		
		$('.flexslider_slider').flexslider({
			animation: "slide",
			controlNav: false,
			directionNav:false,
			animationLoop: false,
			slideshow: false,
			sync: ".flexslider_thumb",
			rtl: $("body").hasClass("rtl")
		});
	}
	
	if($('.carasouel-slider3').length){
		$(".carasouel-slider3").each( function(){

			var bx_options = {
				slideWidth: 167,
				minSlides: 1,
				maxSlides: 6,
				slideMargin: 33,
				infiniteLoop:false,
				pager:false,
				prevSelector: jQuery(this).closest(".recent-vehicles-wrap").find('#slideControls3>.prev-btn'),
				nextSelector: jQuery(this).closest(".recent-vehicles-wrap").find('#slideControls3>.next-btn'),
			};

			if($(this).data('autoscroll') == true){
				bx_options.infiniteLoop = true;
				bx_options.auto = true;
				bx_options.autoStart = true;
			}

			$(this).bxSlider(bx_options);

		});
	}

	function calculate_func(calculator){
		var cost         = calculator.find(".cost").val();
		var down_payment = calculator.find(".down_payment").val();
		var interest     = calculator.find(".interest").val();
		var loan_years   = calculator.find(".loan_years").val();
		var frequency    = calculator.find(".frequency").val();
		
		if( !cost || !down_payment || !interest || !loan_years  || isNaN(cost) || isNaN(down_payment) || isNaN(interest) || isNaN(loan_years) ){
			if(!cost || isNaN(cost)){
				calculator.find(".cost").addClass("error");
			} else { 
				calculator.find(".cost").removeClass("error");
			}
			
			if(!down_payment || isNaN(down_payment)){
				calculator.find(".down_payment").addClass("error");
			} else { 
				calculator.find(".down_payment").removeClass("error");
			}	
				
			if(!interest || isNaN(interest)){
				calculator.find(".interest").addClass("error");
			} else { 
				calculator.find(".interest").removeClass("error");
			}	
					
			if(!loan_years || isNaN(loan_years)){
				calculator.find(".loan_years").addClass("error");
			}	 else { 
				calculator.find(".loan_years").removeClass("error");
			}	
							
			return;
		}
		
		calculator.find("input").removeClass("error");
		
		switch(frequency) {
			case "0":
				var frequency_rate = 26;
				break;
			case "1":
				var frequency_rate = 52;
				break;
			case "2":
				var frequency_rate = 12;
				break;			
		}
		
		var interest_rate = (interest) / 100;
		var rate          = interest_rate / frequency_rate;
		var payments      = loan_years * frequency_rate;
		var difference    = cost - down_payment;
	
		var payment = Math.floor((difference*rate)/(1-Math.pow((1+rate),(-1*payments)))*100)/100;


		
		if(typeof listing_ajax.currency_separator != "undefined" && typeof payment != "undefined"){
			// payment = payment.toString().replace(".", listing_ajax.currency_separator);
			payment = parseInt( payment ).toLocaleString();
		}

		var currency_symbol = (typeof listing_ajax.currency_symbol != "undefined" ? listing_ajax.currency_symbol : "$");

		calculator.find(".payments").text(payments);
		calculator.find(".payment_amount").text(currency_symbol + payment);
	}

	// Financing Calculator
	$(document).on("click", '.financing_calculator .calculate', function() {		
		var calculator = $(this).closest(".financing_calculator");
		
		calculate_func(calculator);
	});

	calculate_func($(".financing_calculator"));
	
	$(".toggle_dropdowns").click( function(){
		$("#mobile_dropdowns").stop().slideToggle();
	});
	
	$("ul.action_tabs li").click( function(){
		$("ul.action_tabs li.current").removeClass('current');
		$(this).addClass('current');
	});
	
	$(".actions_menu").click( function(){
		$(".mobile_actions").slideToggle();
		
		if($("#action_button_content").is(":visible")){
			$("#action_button_content").slideUp();
		}
	});
	
	// widget
	$(document).on("click", ".reset_widget_filter", function() {
		$(".listing_sidebar_widget select").each(function(index, element) {
            var id = $(this).attr('id');

			$('#' + id).find('option:first-child').prop('selected', true).end().trigger('liszt:updated');
        });

        var count = $("ul.filter li").length;
		
		$("ul.filter li").each( function(){
			$(this).fadeOut(400, function(){
				var name = $(this).data("type");
				var id   = $("select[name='" + name + "']").attr("id");
				var text = $("select[name='" + name + "']").data("placeholder");

				var name = ($(this).data("type") == "year" ? "yr" : $(this).data("type")).replace(/-/g,'_');
				var sb   = $("select[name='" + name + "'].sidebar_widget_filter").attr("sb");
				
				// top
				var sb2   = $(".listing_select select[name='" + name + "']").attr("sb");
				
				$(".listing_select select[name='" + name + "']").prop("selectedIndex", 0);
				$("#sbSelector_" + sb2).text($(".listing_select select[name='" + name + "'] option:selected").text());

				// sidebar
				$(".listing-sidebar select[name='" + name + "'].sidebar_widget_filter").prop("selectedIndex", 0);
				$("#sbSelector_" + sb).text($(".listing-sidebar select[name='" + name + "'].sidebar_widget_filter option:selected").text());
				
				
				$("#" + id + "_chzn").find(".chzn-single span").text(text);
				
				$(this).remove();

				if (!--count) update_results();
			});
		});
	});
	
	function print_tabs(){
		if(!$(".print_tabs").length){
			// generate google map
			var longitude  = $(".google_map_init").data("longitude");
			var latitude   = $(".google_map_init").data("latitude");
			var zoom       = $(".google_map_init").data("zoom");

			var http_prefix = (listing_ajax.is_ssl ? "https" : "http");
			
			var google_map = "<img src='" + http_prefix + "://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=" + zoom + "&size=700x200&markers=color:blue|label:S|" + latitude + "," + longitude + "&sensor=false'>";
			
			$(".example-tabs").each( function() {		
				var tabs_html = "";			
				$(this).find(".nav-tabs li").each( function(index, element) {			
					tabs_html += "<div class='" + $(this).find("a").attr("href").replace("#", "") + "'><h2>" + $(this).text() + "</h2><br />";			
					
					tabs_html += ($(this).find("a").attr("href") == "#location" ? google_map : $(".tab-content .tab-pane[id='" + $(this).find("a").attr("href").replace("#", "")  + "']").html()) + "</div><br />";			
				});
				
				$(".inner-page.inventory-listing").append("<div class='print_friendly print_tabs'>" + tabs_html + "</div>");		
			});
		}
	}
	
	function print_header(){
		if(!$(".print_header").length){
			var header_html = "";
			
			header_html += $(".logo").html();
			header_html += $(".company_info").html();
			
			$(".inner-page.inventory-listing").prepend("<div class='print_friendly print_header'>" + header_html + "</div>");
			
			$(".inventory-heading").append("<div style='clear: both;'></div>");
		}
	}
	
	function print_images(){
		if(!$(".print_image").length){
			var images_html = "";
			
			$("#home-slider-thumbs li").slice(0, 6).each( function(index, element){
				images_html += $(this).html() + (index == 1 || index == 3 ? "<br>" : "");
			});
			
			var car_info = $(".car-info").clone().html();
			
			$(".print_tabs").prepend("<div class='print_friendly print_image'>" + images_html + "<br></div><div class='car-info'>" + car_info + "</div><div style='clear: both;'></div>");
		}
	}
	
	$(".add_mailchimp").click( function(){
		var email  = $(".email").val();
		var list   = $(this).data('list');
		var thisel = $(this);
		
		$.ajax({
			 type: "POST",
			 url: listing_ajax.ajaxurl,
			 data: { action: "add_mailchimp", email: email, list: list },
			 success:function(data){
				thisel.parent().find(".response").hide().html(data).fadeIn();
				thisel.parent().find(".email").val("");
			 }
		});
	});
	
	// email to a friend
	$(document).on("click", ".send_email", function(){
		$("#action_button_content form input.error").removeClass("error");
		
		var fields  = ["your_name", "your_email", "friends_email", "message"];
		email_error = false;
		
		for(var i=0; i<fields.length; i++){
			var value = (fields[i] == "message" ? $("textarea[name='" + fields[i] + "']").val() : $("input[name='" + fields[i] + "']").val());
			
			if( !value ){
				(fields[i] == "message" ? $("textarea[name='" + fields[i] + "']") : $("input[name='" + fields[i] + "']")).addClass('error');
				email_error = true;
			}
		}
		
		if(email_error !== true){
			var name    = $("input[name='" + fields[0] + "']").val();		
			var email   = $("input[name='" + fields[1] + "']").val();
			var friend  = $("input[name='" + fields[2] + "']").val();
			var message = $("textarea[name='" + fields[3] + "']").val();	
			
			if($("#send_copy").is(":checked")){
				var checked = true;
			} else {
				var checked = false;
			}
			
			jQuery.ajax({
			   type : "post",
			   url : listing_ajax.ajaxurl,
			   data : { action: "send_listing_email", name: name, email: email, friend: friend, message: message, checked: checked, id: listing_ajax.listing_id },
			   success: function(response) {
				   $("#action_button_content").slideUp(400, function(){
					   $(this).html(response).slideDown().delay(2000).slideUp();
					   $("button[data-action='email'] i").removeClass("fa-times").addClass($("button[data-action='email']").data('icon'));
				   });
			   }
			});
		}
	});
	
	// schedule test drive
	$(document).on("click", ".schedule_test_drive", function(){
		$("#action_button_content form input.error").removeClass("error");
		
		var fields  = ["first_name", "last_name", "contact_method", "date", "time"];
		email_error = false;
		
		var checked_method  = $("input[name='contact_method']:checked");
		var prefered_method = (typeof checked_method.attr('id') != "undefined" && checked_method.attr('id') == "email" ? "email" : "phone");
		fields.push(prefered_method);
		
		for(var i=0; i<fields.length; i++){
			var value = $("input[name='" + fields[i] + "']").val();
			
			if( !value ){
				$("input[name='" + fields[i] + "']").addClass('error');
				email_error = true;
			} else if(value && $("input[name='" + fields[i] + "']").hasClass('error')) {
				$("input[name='" + fields[i] + "']").removeClass('error');
			}
		}
		
		if(email_error !== true){
			var first_name     = $("input[name='" + fields[0] + "']").val();
			var last_name      = $("input[name='" + fields[1] + "']").val();
			var contact_method = $("input[name='" + fields[2] + "']").val();
			var email          = $("input[name='" + fields[3] + "']").val();
			var phone          = $("input[name='" + fields[4] + "']").val();
			var date           = $("input[name='" + fields[5] + "']").val();
			var time           = $("input[name='" + fields[6] + "']").val();
			
			jQuery.ajax({
			   type : "post",
			   url : listing_ajax.ajaxurl,
			   data : { action: "schedule_test_drive", first_name: first_name, last_name: last_name, contact_method: contact_method, email: email, phone: phone, date: date, time: time, id: listing_ajax.listing_id },
			   success: function(response) {
				   $("#action_button_content").slideUp(400, function(){
					   $(this).html(response).slideDown().delay(2000).slideUp();
				   });
			   }
			});
		}
	});
	
	// request_info
	$(document).on("click", ".request_info", function(){
		$("#action_button_content form input.error").removeClass("error");
		
		var fields  = ["first_name", "last_name", "contact_method"];
		email_error = false;
		
		var checked_method  = $("input[name='contact_method']:checked");
		var prefered_method = (typeof checked_method.attr('id') != "undefined" && checked_method.attr('id') == "email" ? "email" : "phone");
		fields.push(prefered_method);

		for(var i=0; i<fields.length; i++){
			var value = $("input[name='" + fields[i] + "']").val();
			
			if( !value ){
				$("input[name='" + fields[i] + "']").addClass('error');
				email_error = true;
			} else if(value && $("input[name='" + fields[i] + "']").hasClass('error')) {
				$("input[name='" + fields[i] + "']").removeClass('error');
			}
		}
		
		if(email_error !== true){
			var first_name     = $("input[name='" + fields[0] + "']").val();
			var last_name      = $("input[name='" + fields[1] + "']").val();
			var contact_method = $("input[name='" + fields[2] + "']").val();
			var email          = $("input[name='" + fields[3] + "']").val();
			var phone          = $("input[name='" + fields[4] + "']").val();
			
			jQuery.ajax({
			   type : "post",
			   url : listing_ajax.ajaxurl,
			   data : { action: "request_info", first_name: first_name, last_name: last_name, contact_method: contact_method, email: email, phone: phone, id: listing_ajax.listing_id },
			   success: function(response) {
				   $("#action_button_content").slideUp(400, function(){
					   $(this).html(response).slideDown().delay(2000).slideUp();
				   });
			   }
			});
		}
	});
	
	// trade in
	$(document).on("click", ".trade_in_submit", function(){
		$("#action_button_content form input.error").removeClass("error");
		
		var form    = $("#tradein").serialize();
		form_errors = false;
		
		var checked_method  = $("input[name='contact_method']:checked");
		var prefered_method = (typeof checked_method.attr('id') != "undefined" && checked_method.attr('id') == "email" ? "email" : "phone");
		
		
		if($("input[name='" + prefered_method + "']").is(":empty")){
			$("input[name='" + prefered_method + "']").addClass('error');
		}
		
		$(".trade-in label.required").each(function(index, element) {
            var value = $(this).find("input").val();
			
			if(value == ""){
				form_errors = true;
				$(this).find("input").addClass('error');
			} else if($(this).find("input").hasClass('error') && value != ""){
				$(this).find("input").removeClass('error');
			}
        });		
		
		if(form_errors == false){
			jQuery.ajax({
			   type : "post",
			   url : listing_ajax.ajaxurl,
			   data : { action: "trade_in_action", form_data: form, listing_id: listing_ajax.listing_id},
			   success: function(response) {
					$("#action_button_content").slideUp(400, function(){
					   $(this).html(response).slideDown().delay(2000).slideUp();
				   });
			   }
			});
		}
	});
	
	// make an offer
	$(document).on("click", ".make_offer_submit", function(){
		$("#action_button_content form input.error").removeClass("error");
		
		var form_data = $(".offer").serialize();
		form_errors   = false;
		
		var checked_method  = $("input[name='contact_method']:checked");
		var prefered_method = (typeof checked_method.attr('id') != "undefined" && checked_method.attr('id') == "email" ? "email" : "phone");
		
		
		if($("input[name='" + prefered_method + "']").is(":empty")){
			$("input[name='" + prefered_method + "']").addClass('error');
		}
		
		$(".offer label.required").each(function(index, element) {
            var value = $(this).find("input").val();
			
			if(value == ""){
				form_errors = true;
				$(this).find("input").addClass('error');
			} else if($(this).find("input").hasClass('error') && value != ""){
				$(this).find("input").removeClass('error');
			}
        });		
		
		if(form_errors == false){
			jQuery.ajax({
			   type : "post",
			   url : listing_ajax.ajaxurl,
			   data : { action: "offer_action", form_data: form_data, listing_id: listing_ajax.listing_id },
			   success: function(response) {
					$("#action_button_content").slideUp(400, function(){
					   $(this).html(response).slideDown().delay(3000).slideUp();
				   });
			   }
			});
		}		
	});

	/*$(document).on("click", ".find_new_vehicle", function(e){
		e.preventDefault();

		$(this).closest("form").find("select").each( function(){
			// $(this).attr("name", $(this).attr("name").replace(/_/g, "-"));
		});

		$(this).closest("form").submit();
	});*/
    
	function init_listing_filters(){
		$(".listing_filter").selectbox({
			onChange: function(val, inst){
				if($(this).attr("name") != "price_order"){
					var items   = $("ul.filter li").length;
					var chose   = $(this).val();
					var type    = $(this).data("sort");
					var compare = $(this).data("compare-value");
					var translate_term = $(this).data("translate-term");

					var key     = $(this).find("option:selected").data("key");
					var orig_name = ($(this).attr("name") == "yr" ? "year" : $(this).attr("name"));

					// check for dependancies
					$(".listing_filter[data-dependancy='" + orig_name + "']").each( function(index, element){
						var name = $(this).attr("name");

						// if something selected, remove it
						if($("ul.filter li[data-type='" + name + "']").length){
							$("ul.filter li[data-type='" + name + "']").remove();

							var label = $("select[name='" + $(this).data("dependancy") + "']").data("label-singular");

							if($(".listing_filter[name='" + name + "']").length){
								$("select[name='" + name + "']").html("<option>Choose a " + label + "</option>");
								$("select[name='" + name + "']").selectbox('detach').selectbox('attach');
							}
							
							init_listing_filters();
						}

						jQuery.ajax({
							type: "post",
							url: listing_ajax.ajaxurl,
						    dataType: 'json',
							data: { action: "get_child_categories", value: val, name: name, key: key },
							success: function(response) {
								var options     = response;
								var look_depend = $("select[sb='" + inst.uid + "']").data("sort");

								$("select[data-dependancy='" + look_depend + "']").each( function(index, element){
									var this_el		= $(this);
									var plural 		= $(this).data("label-plural");

									$(this).html("<option>" + plural + "</option>");
									$(this).selectbox('detach');

									$.each(options, function(key, value) {  
										if(value != null){ 
										    this_el.append($("<option></option>").attr("value", value.term).text(value.term).data("key", value.key)); 
										}
									});
								});

								//$("select[data-dependancy='" + look_depend + "']").selectbox('attach');
								init_listing_filters();
							}
				        });
					});
					
					// If no filters are set
					if(items == 1 && $("ul.filter li").eq(0).data("filter") == "All"){
						$("ul.filter li").eq(0).remove();
					}

					var safe_type = $.trim(type.replace(/ /g, "-"));
					safe_type = (safe_type == "year" ? "yr" : safe_type);
					
					var select_index = inst.input.context.selectedIndex;

					if(select_index != 0){
						var span = " <span>" + chose + "</span>";
						

						if($("li[data-type='" + safe_type + "']").length){
							$("ul.filter li[data-type='" + safe_type + "']").html("<a href=''><i class='fa fa-times-circle'></i> " + type.replace(/_/g, " ").replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) + ": " + (typeof compare != "undefined" && compare != "=" ? compare + " " : "") + span + "</a>").fadeIn();
						} else {
							$("<li data-type='" + safe_type + "'><a href=''><i class='fa fa-times-circle'></i> " + type.replace(/_/g, " ").replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) + ": " + (typeof compare != "undefined" && compare != "=" ? compare + " " : "") + span + "</a></li>").appendTo("ul.filter").hide().fadeIn();
						}
											
						update_results(1);
					} else {
						$("ul.filter li[data-type='" + safe_type + "']").hide(function(){
							$(this).remove().then(update_results(1));
						});
					}

					if($(this).hasClass('sidebar_widget_filter')){
						var name = $(this).data("sort");
						var sb   = $(".listing_select select[data-sort='" + name + "']").attr("sb");
						
						$(".listing_select select[name='" + name + "']").prop("selectedIndex", 0);
						$("#sbSelector_" + sb).text((select_index != 0 ? val : $(".listing_select select[data-sort='" + name + "'] option:first").text()));
					} else {
						var name = $(this).data("sort");
						var sb   = $(".listing-sidebar select[data-sort='" + name + "']").attr("sb");						

						$(".listing-sidebar select[name='" + name + "']").prop("selectedIndex", 0);
						$("#sbSelector_" + sb).text((select_index != 0 ? val : $(".listing-sidebar select[data-sort='" + name + "'] option:first").text()));
					}
				} else {
					update_results(1);
				}
			},
			onOpen: function(inst){
				var width = 0;
				$("#sbOptions_" + inst.uid + " li").each( function(){
					width = (width < $(this).outerWidth() ? $(this).outerWidth() : width);
				});

				$("#sbOptions_" + inst.uid).width((width + 15));
			} 
		});
	}

	// select box
	if($(".listing_filter").length){		
		//  filter
		init_listing_filters();
	}

	// $("option[data-empty]").each( function(index, element){
	// 	$(this).attr('disabled', true);
	// });

	// even out all comparison table rows for easier viewing
	if($(".comparison").length){
		// title block
		var sizes = Array();
		
		$(".comparison .porche-header").each(function(index, element) {
			title_height = $(this).height();
			
            sizes.push(title_height);
        });
		
		var biggest_height = Math.max.apply(Math, sizes);
		
		$(".title").height(biggest_height);
		
		// table rows
		var tables = $(".comparison").length;
		var rows   = (parseInt($(".comparison:eq(0) tr").length) - 1);
		
		for(var i=0;i<rows;i++){
			var sizes = Array();
			
			for(var ii=0;ii<tables;ii++){
				var row_height = $(".comparison").eq(ii).find("tr").eq(i).height();
				
				sizes.push(row_height);
			}
			
			var biggest_height  = Math.max.apply(Math, sizes);
			
			$("table.comparison").each(function(index, element) {
                $(this).find("tr").eq(i).height(biggest_height);
            });
		}
		
		if($(".option-tick-list").length){
			$(".option-tick-list").evenElements();
		}
	}
	
	// animate progress bars
	$(".progress-bar[data-width]").each( function(){
		$(this).css("width", $(this).data('width').replace("%", "") + "%");
	});
	
	if($('.featured_slider').length){
		$('.featured_slider').bxSlider({
			slideWidth: 155,
			minSlides: 1,
			maxSlides: 6,
			slideMargin: 30,
			infiniteLoop:false,
			pager:false,
			nextSelector:jQuery('#slideControls>.prev-btn'),
			prevSelector:jQuery('#slideControls>.next-btn')
		});
	}
	
	function bxSlider_responsive_slides(){			
		if($(".featured_brands").length){
			$(".featured_brands").each( function( index, element) {
				var pager      = $(this).data('pager');
				var slides     = $(this).data('minslides');
				var next       = $(this).data('next');
				var nexttext   = $(this).data('nexttext');
				var prev       = $(this).data('prev');
				var prevtext   = $(this).data('prevtext');
				var slidewidth = $(this).data('slidewidth');
				
				$(".featured_brands").bxSlider({
					pager: false,
					nextSelector: next,
					nextText: nexttext,
					prevSelector: prev,
					prevText: prevtext,
					slideWidth: slidewidth
				});
			});
		}
	}	
	
	bxSlider_responsive_slides();
	
	
	function shuffle(o){ //v1.0
		for(var j, x, i = o.length; i; j = Math.floor(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
		return o;
	}
	
	function random_effect(){
		var effects = new Array("fadeIn", "fadeInUp", "fadeInDown", "fadeInLeft", "fadeInRight", "fadeInUpBig", "fadeInDownBig", "fadeInLeftBig", "fadeInRightBig", "slideInDown", "slideInLeft", "slideInRight", "bounceIn", "bounceInDown", "bounceInUp", "bounceInLeft", "bounceInRight");
		effects = shuffle(effects);
		effects = shuffle(effects);
		return effects[Math.floor(Math.random()*effects.length)];
	}

	
	//********************************************
	//	Preview Slideshow
	//***********************************************************
	$(document).on("click", "img.preview[data-id]", function(e){
		e.preventDefault();

		var id = $(this).data("id");

		$.ajax({
			url: listing_ajax.ajaxurl,
			type: 'POST',
			dataType: 'html',
			data: { action: 'preview_slideshow_ajax', id: id },
			success: function(response){

				$("#preview_slideshow").html(response).waitForImages( function(){

					$('#home-slider-thumbs').flexslider({
						animation: "slide",
						controlNav: false,
						directionNav:true,
						animationLoop: false,
						slideshow: false,
						itemWidth: 171,
						itemMargin: 10,
						asNavFor: '#home-slider-canvas',
						rtl: $("body").hasClass("rtl") 
					}); 

					$('#home-slider-canvas').flexslider({
						animation: "slide",
						controlNav: false,
						directionNav:false,
						animationLoop: false,
						slideshow: false,
						smoothHeight: true,
						sync: "#home-slider-thumbs",
						start: function(slider){
							slider.removeClass("loading");
						},
						rtl: $("body").hasClass("rtl") 
					});
					
			    });

			    $("#preview_slideshow").addClass("open");
			}
		});
	});

	$(document).on("click", "#preview_slideshow.open", function(e){
		var id = e.target.id;

		if(id == "preview_slideshow" || id == "close_preview_area"){
			$(this).removeClass("open");
		} else {
			return false;
		}
	});

	$(document).keyup(function(e) {
	  	if (e.keyCode == 27 && $("#preview_slideshow").hasClass("open")){ 
	  		$("#preview_slideshow").removeClass("open");
	  	}  
	});
	
	//********************************************
	//	Inview
	//***********************************************************
	$('i.fa[data-animated]').css('opacity', 0);
	
	var effects = new Array("fadeIn", "fadeInUp", "fadeInDown", "fadeInLeft", "fadeInRight", "fadeInUpBig", "fadeInDownBig", "fadeInLeftBig", "fadeInRightBig", "slideInDown", "slideInLeft", "slideInRight", "bounceIn", "bounceInDown", "bounceInUp", "bounceInLeft", "bounceInRight");
	
	$('i.fa[data-animated]').live('inview', function(event, isInView) {
		if (!isInView) { return; }
		
		var string = $.inArray($(this).data('animated'), effects) > -1;
		
		var icon   = $(this);
		var rand   = (string ? $(this).data('animated') : random_effect());
		
		icon.addClass('animated ' + rand);
		icon.css('opacity', 1);
		icon.removeAttr('data-animated');
	});
	
	if($(".fancybox_div").length){
		$(".fancybox_div").each( function(index, element){
			$(this).fancybox({
				'width' : '620',
				'autoDimensions':false
			});
		});
	}
	
	
	
	// testimonial slider
	if($(".testimonial_slider").length){
		$('.testimonial_slider').bxSlider({
			mode: 'horizontal',
			slideMargin: 3,
			minSlides: 1,
			maxSlides: 1,
			auto: true,
			autoHover: true,
			speed: 500,
			pager: false,
			controls: false
		});
	}
	
	
  	if($('.recent_blog_posts').length){
  		$('.recent_blog_posts').each( function(){
  			var controls = $(this).data('controls');

			$(this).bxSlider({
				mode: 'vertical',
				moveSlides: 1,
				auto: false,
				speed: 500,
				pager: false,
				minSlides: 2,
				maxSlides: 2,
				nextSelector: '.' + controls,
				prevSelector: '.' + controls,
				nextText: '<i class="fa fa-chevron-up"></i>',
				prevText: '<i class="fa fa-chevron-down"></i>',
				adaptiveHeight: true
			});
  		});
	}
	
	if($('.flexslider2').length){
		$('.flexslider2').flexslider({
			animation: "slide",
			directionNav:true,
			controlNav: false,
			prevText:"",
			nextText:""		
		});
	}
	
	
	// faq sort
	if($(".faq-sort").length){
		// faq shortcode
		if(window.location.hash) {
			var hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
			
			$(".list_faq a").each( function(index, element){
				if($(this).text().indexOf(hash) !== -1){
					$(this).parent().addClass("active");
				}
			});
			
			if(hash != "All"){
				$(".faq-sort div.panel").each(function(index, element) {
					var in_categories = $(this).data('categories');
					
					if(in_categories.indexOf(hash) === -1){
						$(this).hide({effect: "fold", duration: 600});
					} else {
						$(this).show({effect: "fold", duration: 600});
					}
				});
			}
		} else {
			$(".list_faq li").first().addClass("active");
		}
		
		
	}
	
	if($(".faq").length){
		$("a[data-action='sort']").click( function(e){
			e.preventDefault();
			
			var category = $(this).attr("href").replace("#", "");
			var faqs     = $(".faq div.panel");
												
			$(".list_faq li.active").removeClass("active");
			
			$(this).parent().addClass("active");
			
			if(category == "All"){
				faqs.each(function(index, element) {
					$(this).show({effect: "fold", duration: 600});
				});
			} else {
				faqs.each(function(index, element) {
					var in_categories = $(this).data('categories');
					
					if(in_categories.indexOf(category) === -1){
						$(this).hide({effect: "fold", duration: 600});
					} else {
						$(this).show({effect: "fold", duration: 600});
					}
				});
			}
		});
	}
	
	// social likes
	if($('.social-likes.listing_share').length){
		$('.social-likes.listing_share').socialLikes({
			zeroes: 'yes'
		});
	}
	
	// select box
	function init_search_listing_filters(){
		if($(".css-dropdowns").length){
			$(".css-dropdowns").selectbox({
				onChange: function(val, inst){
					if($(this).attr("name") != "price_order"){
						var items   = $("ul.filter li").length;
						var chose   = $(this).val();
						var type    = $(this).data("sort");
						var compare = $(this).data("compare-value");

						var key     = $(this).find("option:selected").data("key");
						var orig_name = ($(this).attr("name") == "yr" ? "year" : $(this).attr("name"));

						// check for dependancies
						$(".css-dropdowns[data-dependancy='" + orig_name + "']").each( function(index, element){
							var name   = $(this).attr("name");
							var thisel = $(this);

							jQuery.ajax({
								type: "post",
								url: listing_ajax.ajaxurl,
							    dataType: 'json',
								data: { action: "get_child_categories", value: val, name: name, key: key },
								success: function(response) {
									var options     = response;
									var look_depend = $("select[sb='" + inst.uid + "']").data("sort");
									var plural 		= thisel.data("label-plural");

									$("select[data-dependancy='" + look_depend + "']").html("<option>" + plural + "</option>");
									$("select[data-dependancy='" + look_depend + "']").selectbox('detach');

									$.each(options, function(key, value) {
										if(value.term != null){
										    $("select[data-dependancy='" + look_depend + "']")
										        .append($("<option></option>")
										        .attr("value",value.term.toLowerCase().replace(/\s+/g, '-'))
										        .text(value.term)
										        .data("key", value.key)); 
										}
									});

									//$("select[data-dependancy='" + look_depend + "']").selectbox('attach');
									init_search_listing_filters();
								}
					        });
						});					
					}
				},
				onOpen: function(inst){
					var width = 0;
					$("#sbOptions_" + inst.uid + " li").each( function(){
						width = (width < $(this).outerWidth() ? $(this).outerWidth() : width);
					});

					$("#sbOptions_" + inst.uid).width((width + 15));
				} 
			});
		}
	}
	init_search_listing_filters();
	
	// my tab
	if($('#myTab a').length){
		$('#myTab a').click(function (e) {
			e.preventDefault()
			$(this).tab('show');
		});

		jQuery("#myTab a:first").tab('show');
	}
	
	function convertImgToBase64(url, callback, outputFormat){
		var canvas = document.createElement('CANVAS'),
			ctx = canvas.getContext('2d'),
			img = new Image;
		img.crossOrigin = 'Anonymous';
		img.onload = function(){
			var dataURL;
			canvas.height = img.height;
			canvas.width = img.width;
			ctx.drawImage(img, 0, 0);
			dataURL = canvas.toDataURL(outputFormat);
			callback.call(this, dataURL);
			canvas = null;
		};
		img.src = url;
	}
	
	// pregenerate base images
	if($(".google_map_init").length){
		var latitude  = $(".google_map_init").data("latitude");
		var longitude = $(".google_map_init").data("longitude");
		var zoom      = $(".google_map_init").data("zoom");

		var http_prefix = (listing_ajax.is_ssl ? "https" : "http");
		
		convertImgToBase64(http_prefix + "://maps.google.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=" + zoom + "&size=700x170&maptype=roadmap&sensor=false&language=&markers=color:red|label:S|" + latitude + "," + longitude, function(base_64){
			$(".google_map_init").data("base_64", base_64);
		});
	}

	if($(".logo > img").length == 1){
		convertImgToBase64($(".logo > img").attr("src"), function(base_64){
			$(".logo > img").data("base_64", base_64);
		});
	}
	
	$(".home-slider-thumbs ul li[data-thumb]").each( function(index, element){
		var this_i = $(this);
		var image  = this_i.data("thumb");
		
		convertImgToBase64(image, function(base_64){
			this_i.data("base_64", base_64);
		});
		
	});
		
	// generate pdf
	$(".generate_pdf").click( function(e){
		e.preventDefault();
		
		// generate map		
		var doc = new jsPDF();
		
		// properties
		doc.setProperties({
			title: 'Inventory Listing',
			creator: 'MrHunter'
		});
		
		// get elements
		if($("header .logo > img").length == 1){
			var image_logo  = $(".logo img").data("base_64");
			var logo_width  = $(".logo img").width();
			var logo_height = $(".logo img").height();
		} else {
			var title      = $(".logo .primary_text").text();
			var secondary  = $(".logo .secondary_text").text();
		}
		var telephone = $.trim($(".company_info li:first a").text());
		var address   = $.trim($(".company_info li:eq(1) a").text());

		var car_title = $(".inventory-heading h2:first").text();
		var car_price = $(".inventory-heading h2:eq(1)").text();

		// car_title length
		if(car_title.length > 43){
			car_title = car_title.substr(0, 43) + "...";
		}
		
		var car_secondary = $(".inventory-heading span").text();
		var car_taxes     = $(".inventory-heading em").text();
		
		var left_margin   = 10;
		var right_margin  = 10;

		var top_space_int = 20;
		
		// write header
		if(typeof image_logo == "undefined"){
			doc.text(left_margin, 15, title + " " + secondary);
		} else {
			var imag_logo = doc.addImage(image_logo, 'PNG', left_margin, 15);
			top_space_int += (parseInt($(".logo > img").height()) * 0.26458333333333);
		}
		
		doc.setFontSize(12);
		doc.text(left_margin, top_space_int, telephone);

		top_space_int += 5;

		doc.text(left_margin, top_space_int, address);
		
		// set bigger font for listing title & price
		doc.setFontSize(20);
		top_space_int += 15;
		doc.setFontStyle("bold");
		doc.text(left_margin, top_space_int, car_title);

		doc.setFontStyle("bold");
		doc.myText(car_price, { align: "right" }, right_margin, top_space_int);
		
		// smaller text for under
		doc.setFontStyle("normal");
		doc.setFontSize(10);
		top_space_int += 8;
		doc.text(left_margin, top_space_int, car_secondary);
		doc.myText(car_taxes, { align: "right" }, right_margin, top_space_int);
		
		top_space_int += 15;

		// get first 6 images
		var i    = 0;
		var inc  = 0;
		var top  = top_space_int;
		var table_top_spacing = top_space_int;
		var left = left_margin;
		
		var left_spacing = 38;
		var top_spacing  = 0;
		
		$(".home-slider-thumbs ul li[data-thumb]").each( function(index, element){
			var base_64 = $(this).data("base_64");
						
			doc.addImage(base_64, 'PNG', left + (inc * left_spacing), (top + top_spacing), 36, 26);
		
			inc = (inc == 1 ? 0 : inc + 1);
			
			if(inc == 0){
				top_spacing += 28;
			}
			
			if(index == 5){
				return false;
			}
		}); 
		
		// get table
		var height = table_top_spacing;
		
		$(".car-info:first table tr").each( function(index, element){
			var first  = $(this).find("td:first").text();
			var second = $(this).find("td:eq(1)").text();
			
			doc.setFontStyle("bold");
			doc.text(120, height, first);
			
			doc.setFontStyle("normal");
			doc.myText(second, { align: "right" }, right_margin, height);
			
			height += 5;

			return index<17;
		});
				
		// process vehicle info tabs
		var height = (top_space_int += 100);  
		var page_height  = doc.internal.pageSize.height; 
		var page_width   = doc.internal.pageSize.width; 
		
        $(".nav-tabs li").each( function(index, element) {  
			var the_link     = $(this).find("a").attr("href");
			       
            var title 		 = $.trim($(this).text());
			var desc  		 = $.trim($(".tab-content .tab-pane").eq(index).text());
			 
			var text_dim     = doc.getTextDimensions(title);
			var text_height  = text_dim.h;
			
			var split_desc   = doc.splitTextToSize(desc, 190);
			var desc_dim     = doc.getTextDimensions(split_desc);
			var desc_height  = desc_dim.h;
			
			
			var desc_di      = doc.getTextDimensions(desc);
			
			if(the_link == "#technical" && $("#technical table").length){
				// 159
				var total_lines = 0;
				$("#technical table").each( function(i, e){
					total_lines += $(this).find("tr").length;
				});

				desc_height = (total_lines * 4.5);

			} else if(the_link != "#location"){
				var total_lines = doc.splitTextToSize(desc, 190);
				var line_height = doc.internal.getLineHeight();
				var calc		= (total_lines.length * 4.5);
				
				desc_height = calc;
			}
			
			var to_be_height = (height + text_height + desc_height);
			
			
			// if goes off of page, make new one
			if(to_be_height >= page_height){
				doc.addPage();
				height = 20;
			}
			
			doc.setFontSize(20);
			doc.text(left_margin, height, title);
					
			height += 5;
			
			doc.setFontSize(10);
			if(the_link == "#features"){
				desc = $("#features ul").data("list");
				split_desc = doc.splitTextToSize(desc, 190);
			}  else if(the_link == "#technical" && $("#technical table").length){
				var csv = "";

				$("#technical table").each( function(i, e){
					$(this).find("tr").each( function(ii, ee){
						$(this).find("td").each( function(iii, eee){
							csv += $(this).text() + ", ";
						});

						csv = csv.slice(0, -2);
						csv += "\n";
					});


					csv += "\n\n";
				});				
	                    
				split_desc = doc.splitTextToSize(csv, 190);
			}

			if(the_link != "#location"){
				doc.text(left_margin, height, split_desc);
			} else {
				if($(".google_map_init").length){			
					doc.addImage($(".google_map_init").data("base_64"), 'PNG', left_margin, height, (page_width - (right_margin * 2)), 50);
					desc_height = 60;
				}
			}
			
			height += desc_height + (10);
        });
		
		doc.save('listing.pdf');
	});

	$(document).on("submit", ".ajax_form", function(e){
		e.preventDefault();

		$(".loading_icon_form").fadeIn();
		
		var empty_input = false;
		
		$(this).find("*:not(input[type='submit'])").filter(":input").each( function(index, element){
			if(!$(this).val()){
				empty_input = true;
				
				$(this).css("border", "1px solid #F00");
			} else {
				$(this).attr("style", "");
			}
		});
			
		//check recaptcha
		if($("#recaptcha_area").length){
			var recaptcha_challenge_field = $(this).find("input[name='recaptcha_challenge_field']").val();
			var recaptcha_response_field  = $(this).find("input[name='recaptcha_response_field']").val();

			$.ajax({
				type: "POST",
				url: listing_ajax.ajaxurl,
				data: { "action": "recaptcha_check", recaptcha_challenge_field: recaptcha_challenge_field, recaptcha_response_field: recaptcha_response_field },
				success: function(data){
					if(data != "success"){
						if(!$(".recaptcha_message").length){
							$(".recaptcha_holder").after( "<div class='recaptcha_message'>" + data + "</div>" );
						} else {
							$(".recaptcha_message").html(data);
						}
						return false;
					}
				}
			});
		}
		
		if(empty_input == false){
			var form_values  = $(this).serialize();
			var form_name    = $(this).attr("name");
			var current_form = $(this);
			
			$.ajax({
				type: "POST",
				url: listing_ajax.ajaxurl,
				dataType: "json",
				data: form_values + "&id=" + listing_ajax.listing_id + "&form=" + form_name + "&action=listing_form",
				success: function(data){
					if(data.status == "success"){
						$.fancybox.close();				
					} else {
						if($(".error_list").length){
							$(".error_list").remove();
						}
						
						current_form.prepend(data.message);
					}

					$(".loading_icon_form").fadeOut();
				}
			});
		}
	});

	function init_recaptcha(element){
		grecaptcha.render(element, {
        	'sitekey': listing_ajax.recaptcha_public,
            'theme': "red",
            'callback': grecaptcha.focus_response_field
        });
	}

	$(document).on("click", ".action_button", function(){
		if($(this).hasClass("fancybox_div")){
			var element = $(this).attr("href").replace("#", "") + "_recaptcha";
			if($("#" + element).length){
				init_recaptcha(element);
			}
		}
	});
	
	$(".content-nav .gradient_button").click( function(e){
		if(!$(this).hasClass("print")){
			var element = $(this).find("a").attr("href").replace("#", "") + "_recaptcha";
			if($("#" + element).length){			
	        	init_recaptcha(element);
			}
	    } else {
	    	e.preventDefault();
	    	
			window.print();
	    }
	});

	// generate print before click
	if($(".content-nav .gradient_button.print")){
    	print_tabs();
		print_header();
		print_images();
	}


	// hover image stuff. 
	var image_swap = function(){
		var $this = $(this);
        var newSource = $this.data('hoverimg');
        $this.data('hoverimg', $this.css('background-image'));
        $this.css('background-image', (newSource.indexOf("url(") > -1 ? newSource : 'url(' + newSource + ')'));
	}

	$(".hoverimg [data-hoverimg]").hover(image_swap, image_swap);

	// featured panels needs a different hover
	var featured_swap = function(){
		var $this = $(this).find("[data-hoverimg]");
        var newSource = $this.data('hoverimg');
        $this.data('hoverimg', $this.attr('src'));
        $this.attr('src', newSource);
	}

	$(".featured").hover(featured_swap, featured_swap);

});

// have to wait until DOM is fully loaded (images too)
jQuery(window).load(function($){ 

	var $ = jQuery;
	// parallax effect
	if($(".parallax_scroll").length){
		$(".parallax_scroll").parallax({ speed: 0.15 });

		$(".parallax_parent").each( function(){
			$(this).height($(this).find(".parallax_scroll").height());
		});
	}	

	// fullwidth content
	if($(".fullwidth_element").length){
		$(".fullwidth_element").each( function(){
			$(this).height(($(this).hasClass("bottom_element") ? ($(this).find(">:first-child").height() - 70) : $(this).find(">:first-child").height()));
		});
	}

	// throttle for parallax resize
	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};

	var resize_parallax = debounce(function() {
		$(".parallax_parent").each( function(){
	    	$(this).height($(this).find(".parallax_scroll").height());
	    });

	    // also, for fullwidth elements
	    $(".fullwidth_element").each( function(){
			$(this).height(($(this).hasClass("bottom_element") ? ($(this).find(">:first-child").height() - 70) : $(this).find(">:first-child").height()));
		});
	}, 250);

	window.addEventListener('resize', resize_parallax);
	
	// inventory listing slider
	if($('#home-slider-canvas').length){
		$('#home-slider-canvas').flexslider({
			animation: "slide",
			controlNav: false,
			directionNav:false,
			animationLoop: false,
			slideshow: false,
			smoothHeight: true,
			sync: "#home-slider-thumbs",
			rtl: $("body").hasClass("rtl"),
			start: function(slider){
				slider.removeClass("loading");
			}
		});
	}
	
	if($('#home-slider-thumbs').length){
		$('#home-slider-thumbs').flexslider({
			animation: "slide",
			controlNav: false,
			directionNav:true,
			animationLoop: false,
			slideshow: false,
			itemWidth: 171,
			itemMargin: 10,
			asNavFor: '#home-slider-canvas',
			rtl: $("body").hasClass("rtl"),
			start: function(slider){
			}
		}); 
	}

	$('#home-slider-canvas ul li img').each(function(){
        $(this).wrap('<a rel="gallery1" class="fancybox fancybox_listing_link" href="' + $(this).data('full-image') + '"></a>');
        $(".fancybox").fancybox();
    });


	var map;

	// google map
	function init_google_map(){		
		jQuery(".google_map_init").each(function(index, element){
			var latitude     = $(this).data('latitude');
			var longitude    = $(this).data('longitude');
			var zoom         = $(this).data('zoom');
			var scroll_wheel = $(this).data('scroll');
			var style        = $(this).data('style');
			var parallax 	 = $(this).data('parallax');
			
			if(latitude && longitude){				
				var myLatlng = new google.maps.LatLng(latitude, longitude);
				var myOptions = {
					zoom: zoom,
					center: myLatlng,
					popup: true,
					mapTypeId: google.maps.MapTypeId.ROADMAP					
				}

				if(parallax != false && typeof parallax == "undefined"){
					myOptions.scroll = {
						x:$(window).scrollLeft(),
						y:$(window).scrollTop()
					}
				}
				
				if(scroll_wheel == false && typeof scroll_wheel != "undefined"){
					myOptions.scrollwheel = false;
				}
				
				if(typeof style != "undefined"){
					myOptions.styles = style;
				}				
				
				var map = new google.maps.Map(this, myOptions);
				
				var marker = new google.maps.Marker({
					position: myLatlng, 
					map: map,
					title: "Our Location"
				});

				if(parallax != false && typeof parallax == "undefined"){
					var offset = $(this).offset();
				    map.panBy(((myOptions.scroll.x-offset.left)/3),((myOptions.scroll.y-offset.top)/3));
				      
				    google.maps.event.addDomListener(window, 'scroll', function(){
					    var scrollY = $(window).scrollTop(),
					        scrollX = $(window).scrollLeft(),
					        scroll  = map.get('scroll');
					    
					    if(scroll){
							map.panBy(-((scroll.x-scrollX)/3),-((scroll.y-scrollY)/3));
					    }

					    map.set('scroll',{
					    	x:scrollX,
					    	y:scrollY
					    });
					});
				}

				google.maps.event.addListener(marker, 'click', function() {
					map.setZoom(zoom);
				});
			}
		});
	}
			
	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
		
		if($(this).attr("href") == "#location"){
			setTimeout( function(){
				init_google_map();
				google.maps.event.trigger(map, 'resize');
			}, 500);
		}
	});
	
	init_google_map();

	// preload images
	$(".hoverimg [data-hoverimg]").each( function(index, element){
		$('<img/>')[0].src = $(this).data('hoverimg');
	});



});
})(jQuery);

/*! waitForImages jQuery Plugin 2015-02-25 */
!function(a){var b="waitForImages";a.waitForImages={hasImageProperties:["backgroundImage","listStyleImage","borderImage","borderCornerImage","cursor"],hasImageAttributes:["srcset"]},a.expr[":"].uncached=function(b){if(!a(b).is('img[src][src!=""]'))return!1;var c=new Image;return c.src=b.src,!c.complete},a.fn.waitForImages=function(){var c,d,e,f=0,g=0,h=a.Deferred();if(a.isPlainObject(arguments[0])?(e=arguments[0].waitForAll,d=arguments[0].each,c=arguments[0].finished):1===arguments.length&&"boolean"===a.type(arguments[0])?e=arguments[0]:(c=arguments[0],d=arguments[1],e=arguments[2]),c=c||a.noop,d=d||a.noop,e=!!e,!a.isFunction(c)||!a.isFunction(d))throw new TypeError("An invalid callback was supplied.");return this.each(function(){var i=a(this),j=[],k=a.waitForImages.hasImageProperties||[],l=a.waitForImages.hasImageAttributes||[],m=/url\(\s*(['"]?)(.*?)\1\s*\)/g;e?i.find("*").addBack().each(function(){var b=a(this);b.is("img:uncached")&&j.push({src:b.attr("src"),element:b[0]}),a.each(k,function(a,c){var d,e=b.css(c);if(!e)return!0;for(;d=m.exec(e);)j.push({src:d[2],element:b[0]})}),a.each(l,function(c,d){var e,f=b.attr(d);return f?(e=f.split(","),void a.each(e,function(c,d){d=a.trim(d).split(" ")[0],j.push({src:d,element:b[0]})})):!0})}):i.find("img:uncached").each(function(){j.push({src:this.src,element:this})}),f=j.length,g=0,0===f&&(c.call(i[0]),h.resolveWith(i[0])),a.each(j,function(e,j){var k=new Image,l="load."+b+" error."+b;a(k).one(l,function m(b){var e=[g,f,"load"==b.type];return g++,d.apply(j.element,e),h.notifyWith(j.element,e),a(this).off(l,m),g==f?(c.call(i[0]),h.resolveWith(i[0]),!1):void 0}),k.src=j.src})}),h.promise()}}(jQuery);