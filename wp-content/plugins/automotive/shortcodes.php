<?php
//********************************************
//	TinyMCE Editor Button
//***********************************************************
function add_editor_button() {
   //Check if user has correct level of privileges + hook into Tiny MC methods.
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
   {
     //Check if Editor is in Visual, or rich text, edior mode.
     if (get_user_option('rich_editing')) {
        //Called when tiny MCE loads plugins - 'add_custom' is defined below.
        add_filter('mce_external_plugins', 'add_custom');
        //Called when buttons are loading. -'register_button' is defined below.
        add_filter('mce_buttons', 'register_button');
     }
   }
} 

//add action is a wordpress function, it adds a function to a specific action...
//in this case the function is added to the 'init' action. Init action runs after wordpress is finished loading!
add_action('init', 'add_editor_button');


//Add button to the button array.
function register_button($buttons) {
   //Use PHP 'array_push' function to add the columnThird button to the $buttons array 
   array_push($buttons, "shortcodebutton");
   //Return buttons array to TinyMCE
   return $buttons;
} 

//Add custom plugin to TinyMCE - returns associative array which contains link to JS file. The JS file will contain your plugin when created in the following step.
function add_custom($plugin_array) {
       $plugin_array['shortcodebutton'] = LISTING_DIR . 'js/editor.js';
       return $plugin_array;
}

// Quote
if(!function_exists("post_quote")){
	function post_quote( $atts, $content = null ) {
		extract( shortcode_atts( array( 
			'side'  => 'left',
			'color' => '#c7081b'
		), $atts ) );
		
		$return  = "<div class='quote'" . (isset($color) && $color != "#c7081b" ? " style='border-color: " . $color . "'" : "") . ">";
		$return .= do_shortcode($content);
		$return .= "</div>";
		
		return $return;
	}
}
add_shortcode('quote', 'post_quote');

// inventory shortcode
if(!function_exists("inventory_display")){
	function inventory_display( $atts, $content = null ){
		extract( shortcode_atts( array(
			'layout' => 'wide_fullwidth'
		), $atts ) );

		wp_enqueue_script( 'isotope' );

		global $lwp_options;

		ob_start();

		// determine filters
		$categories = get_listing_categories();
		$filterby   = (isset($_GET) && !empty($_GET) ? $_GET : array());

		if(!empty($categories)){
			foreach($categories as $key => $category){
				$safe = str_replace(" ", "_", mb_strtolower($category['singular']));
				$safe = ($safe == "year" ? "yr" : $safe);

				if(isset($atts[$safe]) && !empty($atts[$safe])){
					$filterby[$safe] = $atts[$safe];
				}
			}
		}

		$args	  = listing_args($filterby/*, false, $_GET*/);
	    $listings = get_posts($args[0]);

		listing_view($layout, $filterby);
		listing_filter_sort($filterby);
		
	    $container = car_listing_container($layout);
	    
	    echo "<div class='row generate_new'>" . $container['start'];
	    $i = 1;
	    foreach($listings as $listing){
	        echo inventory_listing($listing->ID, $layout, $i);
	        $i++;
	    }
		echo "<div class=\"clearfix\"></div>";
	    echo $container['end'];
		
		if($layout == "boxed_left"){
			echo "<div class=\" col-md-3 col-sm-12 col-lg-pull-9 col-md-pull-9 left-sidebar side-content listing-sidebar\">";
			dynamic_sidebar("listing_sidebar");
			echo "</div>";
		} elseif($layout == "boxed_right"){
			echo "<div class=\"inventory-sidebar col-md-3 side-content listing-sidebar\">";
			dynamic_sidebar("listing_sidebar");
			echo "</div>";
		} elseif($layout == "wide_left"){
			echo "<div class=\" col-md-3 col-lg-pull-9 col-md-pull-9 left-sidebar side-content listing-sidebar\">";
			dynamic_sidebar("listing_sidebar");
			echo "</div>";
		} elseif($layout == "wide_right"){
			echo "<div class=\"inventory-sidebar col-md-3 side-content listing-sidebar\">";
			dynamic_sidebar("listing_sidebar");
			echo "</div>";
		}

		
		echo bottom_page_box($layout, false, $filterby);
		echo "</div>"; 

		wp_reset_query();

		echo "<div id='preview_slideshow'></div>";

		echo "<div class='clearfix'></div>";
	    echo listing_youtube_video();

		return ob_get_clean();
	}
}
add_shortcode( "inventory_display", "inventory_display" );

// lists
if(!function_exists("item_list")){
	function item_list( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'style' => 'arrows',
			'extra_class' => ''
		), $atts ) );
		
		if(isset($style) && !empty($style)){
			$GLOBALS['list_icon_style'] = $style;
		}
		
		$return  = "<ul class='shortcode type-" . $style . " " . (!empty($extra_class) ? $extra_class : "") . "'>";
		$return .= do_shortcode($content);
		$return .= "</ul>";
		
		return $return;
	}
}
add_shortcode('list', 'item_list');

if(!function_exists("list_item")){
	function list_item( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'icon'  => ''
		), $atts ) );
		
		if(isset($icon) && !empty($icon)){
			$the_icon    = $icon;
			$custom_icon = true;
		} elseif(isset($GLOBALS['list_icon_style']) && $GLOBALS['list_icon_style'] == "arrows"){ 
			$the_icon = "fa fa-angle-right";
		} else {
			$the_icon = "fa fa-check";
		}
		
		if(isset($GLOBALS['list_icon_style']) && !empty($GLOBALS['list_icon_style']) && $GLOBALS['list_icon_style'] == "arrows"){
			$the_icon = "<span class=\"red_box" . (isset($custom_icon) ? " custom_icon" : "") . "\"><i class='" . $the_icon . "'></i></span>";
		} else {
			$the_icon = "<span" . (isset($custom_icon) ? " class='custom_icon'" : "") . "><i class='" . $the_icon . "'></i></span>";
		}
		
		$return = "<li>" . $the_icon . do_shortcode($content) . "</li>";
		
		return $return;
	}
}
add_shortcode('list_item', 'list_item');

// Dropcaps
if(!function_exists("dropcaps")){
	function dropcaps($atts, $content = null){	
		extract(shortcode_atts(array(
					"size"  => '68px',
					"color" => "#000"
			), $atts)); 
					
			//$return  = "<style>.dropcaps:first-letter { font-size: " . $size . "; " . ($color != "#000" ? "color: " . $color . "; " : "") . "line-height: " . ((int)$size - 3) . "px; }</style>";
			$return  = "<span class='firstcharacter'>" . do_shortcode($content) . "</span>";

			return $return;
	}
}
add_shortcode( 'dropcaps', 'dropcaps' );

// Parallax Section
if(!function_exists("parallax_section")){
	function parallax_section($atts, $content = null){
		extract(shortcode_atts(array(
			"title"    		=> "",
			"velocity"		=> "-.3",
			"offset"   		=> "0",
			"image"    		=> "",
			"overlay_color" => "rgba(255, 255, 255, .65)",
			"text_color"    => "#FFFFFF",
			'temp_height'   => '',
			'extra_class'   => ''
		), $atts));

		wp_enqueue_script( 'parallax' );

		$image = wp_get_attachment_image_src($image, 'full');
		ob_start();
		?>
		<div class="row parallax_parent <?php /* margin-top-30  padding-bottom-40*/ echo (!empty($extra_class) ? $extra_class : ""); ?>"<?php echo (!empty($temp_height) ? " style='height: " . preg_replace('/\D/', '', $temp_height) . "px;'" : ""); ?>>
	    	<div class="parallax_scroll clearfix" data-velocity="<?php echo $velocity; ?>" data-offset="<?php echo $offset; ?>" data-image="<?php echo $image[0]; ?>">                
	            <div class="overlay" style="background-color: <?php echo $overlay_color; ?>; color: <?php echo $text_color; ?>;">
	            	<div class="padding-vertical-10">
	                
	                    <?php echo (!empty($title) ? "<h1>" . $title . "</h1>" : ""); ?>
	                    
	                    <div class="row container<?php echo (empty($title) ? " margin-top-60" : ""); ?>">
	                        
	                        <?php echo do_shortcode($content); ?>
	                        
	                    </div>
	                </div>                    
	            </div>   
	        </div>
	    </div>
		<?php

		$return = ob_get_clean();

		return (function_exists("wpb_js_remove_wpautop") ? wpb_js_remove_wpautop($return) : $return);
	}
}
add_shortcode("parallax_section", "parallax_section");

// Animated Numbers
if(!function_exists("animated_numbers")){
	function animated_numbers($atts, $content = null){
		extract(shortcode_atts(array(
			"icon"   		=> "",
			"number" 		=> "10",
			"before_number" => "",
			"after_number"  => "",
			"alignment"     => "",
			'extra_class' => ''
		), $atts));

		ob_start();
		?>
			<?php echo (!empty($icon) ? '<i class="fa ' . $icon . '"></i>' : ''); ?>
	                                    
	        <span class="animate_number margin-vertical-15"<?php echo (!empty($alignment) ? " style='text-align: " . $alignment . "'" : "") . (!empty($extra_class) ? $extra_class : ""); ?>>        	
	            <?php echo (!empty($before_number) ? $before_number : ""); ?><span class="number"><?php echo $number; ?></span><?php echo (!empty($after_number) ? $after_number : ""); ?>        	
	        </span>
	        
	        <?php echo do_shortcode( $content );

		$return = ob_get_clean();

		return $return;
	}
}
add_shortcode("animated_numbers", "animated_numbers");

// Progress bars
if(!function_exists("progress_bar")){
	function progress_bar($atts, $content = null){
		extract(shortcode_atts(array(
			"color"    => "#c7081b",
			"filled"   => "100%",
			"striped"  => "no",
			"animated" => "no",
			"class"    => "",
			'extra_class' => ''
			), $atts));
			
			$return  = '<div class="progressbar' . (!empty($extra_class) ? $extra_class : "") . '">';
			$return .= '<div class="progress margin-bottom-15">';
	        $return .= '<div class="progress-bar progress-bar-danger' . (!empty($class) ? " " . $class : "") . '" style="' . (isset($color) && $color != "#c7081b" ? "background-color: " . $color . ";" : "") . ';" data-width="' . $filled . '">' . do_shortcode($content) . '</div>';
	        $return .= '</div>';
	        $return .= '</div>';
			

			return $return;
	}
}
add_shortcode('progress_bar', 'progress_bar');

// Testimonials
if(!function_exists("testimonials")){
	function testimonials($atts, $content = null){
		extract(shortcode_atts(array(
			"slide" => "horizontal",
			"speed" => 500,
			"pager" => "false",
			'extra_class' => ''
		), $atts));
		
		$return = "<div class='" . (!empty($extra_class) ? $extra_class : "") . "'>" . testimonial_slider($slide, $speed, $pager, $content) . "<div class='clearfix'></div></div>";
		
		return $return;
	}
}
add_shortcode("testimonials", "testimonials");

if(!function_exists("testimonial_quote")){
	function testimonial_quote($atts, $content = null){
		extract(shortcode_atts(array( 
			"name"  => "Theodore Isaac Rubin",
			"quote" => "Happiness does not come from doing easy work but from the afterglow of satisfaction that comes after the achievement of a difficult task that demanded our best."
		), $atts));
		
		$return = testimonial_slider_quote($name, $content);
		
		return $return;
	}
}
add_shortcode("testimonial_quote", "testimonial_quote");

// Recent Post Scroller
if(!function_exists("recent_posts_scroller")){
	function recent_posts_scroller($atts, $content = null){
		extract(shortcode_atts(array(
			"number" => 2,
			"speed" => 500,
			"pager" => "false",
			"posts" => 4,
			'extra_class' => '',
			'category' => ''
		), $atts));

		$rand = rand();

		wp_enqueue_script( 'bxslider' );
		
		$return  = "<!--Recent Posts Start-->";
		$return  = "<div class=\"arrow1 pull-right blog_post_controls_" . $rand . " " . (!empty($extra_class) ? $extra_class : "") . "\"></div>";
		$return .= "<ul class=\"recent_blog_posts\" data-controls='blog_post_controls_" . $rand . "'>";
		
		$args = array('posts_per_page' => $posts);

		if(!empty($category)){
			$args['category'] = $category;
		}

		$the_posts = get_posts($args);
		
		if(!empty($the_posts)){
			foreach($the_posts as $single){
				$post_content = preg_replace('/\[[^\]]+\]/', '', $single->post_content);
				$date = date("M d, Y", strtotime($single->post_date));
				
				$return .= "<li>";
					$return .= "<div class=\"blog-list\">";
						$return .= "<div class=\"col-lg-12 col-md-12 col-sm-12 col-xs-12 list-info\">";
							$return .= "<div class=\"thumb-image\">";
							if(has_post_thumbnail($single->ID)){
								$return .= get_the_post_thumbnail( $single->ID, array(100, 100), array('class' => 'recent_thumbnail') );
							}
							$return .= "</div>";
							$return .= "<a href='" . get_permalink($single->ID) . "'><h4>" . $single->post_title . "</h4></a>";
							$return .= "<span>" . $date . " /</span> <span class=\"text-red\">" . $single->comment_count . ($single->comment_count == 1 ? " " . __("Comment", "listings") . "" : " " . __("Comments", "listings") . "") . "</span>";
							$return .= "<p>" . substr(strip_tags($post_content), 0, 115) . " " . (strlen(strip_tags($post_content)) > 112 ? "[...]" : "") . "</p>";
						$return .= "</div>";
					$return .= "</div>";
					$return .= "<div class=\"clearfix\"></div>";
				$return .= "</li>";
			}
		}

		$return .= "</ul>";
		$return .= "<!--Recent Posts End-->";
		
		return $return;
	}
}
add_shortcode("recent_posts_scroller", "recent_posts_scroller");

/*if(!function_exists("display_blog")){
	function display_blog(){

		if (have_posts()): while (have_posts()) : the_post(); 
			
			echo blog_post();

		endwhile; 



		wp_reset_query(); ?>

		<?php else: ?>

			<!-- article -->
			<article>
				<h2><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h2>
			</article>
			<!-- /article -->

		<?php endif;

	}
}
add_shortcode("display_blog", "display_blog");*/

// Faqs
if(!function_exists("frequently_asked_questions")){
	function frequently_asked_questions($atts, $content = null){
		extract(shortcode_atts(array(
			"categories"   => "",
			"all_category" => "yes",
			"sort_text"    => "Sort FAQ By:",
			'extra_class'  => ''
		), $atts));
		
		$return  = "<div class=\"list_faq clearfix " . (!empty($extra_class) ? $extra_class : "") . "\">";
			$return .= "<h5>" . $sort_text . "</h5>";
			$return .= "<ul>";
			if(isset($categories) && !empty($categories)){
				$categories      = explode(",", $categories);
				$sort_categories = ($all_category == "yes" ? "<li><a href='#All' data-action='sort'>All</a></li>" : "");
				
				foreach($categories as $category){
					$sort_categories .= "<li><a href='#" . $category . "' data-action='sort'>" . $category . "</a></li>";
				}
				
				$return .= $sort_categories;
			}
			$return .= "</ul>";
		$return .= "</div>";
		
		$return .= "<div class=\"accodian_panel margin-top-30\"><div class=\"panel-group description-accordion faq-sort faq margin-bottom-none\" id=\"accordion\"> ";
		$return .= do_shortcode($content);
		$return .= "</div></div>";
		
		return $return;
	}
}
add_shortcode("faq", "frequently_asked_questions");

if(!function_exists("toggle_item")){
	function toggle_item($atts, $content = null){
		extract(shortcode_atts(array(
			"title"      => "Title",
			"categories" => " ",
			"state"      => "collapsed"
		), $atts));
		
		$id = random_string();
		  
		$return  = "<!--description-->";
		$return .= "<div class=\"panel panel-default padding-top-20 padding-bottom-15\" data-categories=\"" . $categories . "\">";
			$return .= "<div class=\"panel-heading padding-vertical-10 padding-horizontal-15\">";
				$return .= "<h4 class=\"panel-title padding-left-30\"> <a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse_" . $id . "\" class=\"" . $state . "\">" . $title . "</a> </h4>";
			$return .= "</div>";
			$return .= "<div id=\"collapse_" . $id . "\" class=\"panel-collapse " . ($state == "in" ? "in" : "collapse") . "\" style=\"height: " . ($state == "in" ? "auto" : "0px") . ";\">";
				$return .= "<div class=\"panel-body\"> ";
					$return .= "<!--Panel_body-->";
					$return .= "<div class=\"faq_post padding-left-40\">";
						$return .= "<div class=\"post-entry clearfix margin-top-10\">";
							$return .= do_shortcode($content);
						$return .= "</div>";
					$return .= "</div>";
					$return .= "<!--Panel_body--> ";
				$return .= "</div>";
			$return .= "</div>";
		$return .= "</div>";
		$return .= "<!--description--> ";
		  
		  return $return;
	}
}
add_shortcode("toggle", "toggle_item");


// Staff list
if(!function_exists("staff_list")){
	function staff_list($atts, $content = null){
		extract(shortcode_atts(array(
			"people" => "3"
		), $atts));
		
		$return  = "<div class='find_team staff_list' itemscope itemtype='http://schema.org/Organization'>";
			$return .= "<div class='people clearfix'>";
				$return .= "<div class='row'>";
					$return .= do_shortcode($content);
				$return .= "</div>";
			$return .= "</div>";
		$return .= "</div>";
		
		return $return;
	}
}
add_shortcode("staff_list", "staff_list");

if(!function_exists("person")){
	function person($atts, $content){
		extract(shortcode_atts(array(
			"name"       => "William Dean",
			"position"   => "Cheif Executive / CEO",
			"phone"      => "",
			"cell_phone" => "",
			"email"      => "",
			"img"        => home_url() . "/wp-content/uploads/2013/07/william-dean.png",
			"hoverimg"   => "",
			"layout"     => "3",
			"facebook"   => false,
			"twitter"    => false,
			"youtube"    => false,
			"vimeo"      => false,
			"linkedin"   => false,
			"rss"        => false,
			"flickr"     => false,
			"skype"      => false,
			"google"     => false,
			"pinterest"  => false,
			'extra_class' => ''
		), $atts));
		
		global $icons;
			
		/*if($layout == "3"){
			$class = "col-lg-4";
		} elseif($layout == "4") {
			$class = "col-lg-3";
		}*/

		wp_enqueue_script( 'jqueryfancybox' );

		$img      = wp_get_attachment_url($img);
		$hoverimg = wp_get_attachment_url($hoverimg);
		
		//$return  = "<div class=\"" . $class . "\">";
		$return = "<div class=\"team hoverimg " . (!empty($extra_class) ? $extra_class : "") . "\"> " . (!empty($hoverimg) ? "<a href=\"" . $hoverimg . "\" class=\"fancybox\">" : "") . " <img src=\"" . $img  . "\" class=\"aligncenter no_border\" alt=\"" . $name . "\" /> " . (!empty($hoverimg) ? "</a>" : "");
			$return .= "<div class=\"name_post\">";
				$return .= "<h4>" . $name . "</h4>";
				$return .= "<p>" . $position . "</p>";
			$return .= "</div>";
			$return .= "<div class=\"about_team\">";
				$return .= "<p>" . do_shortcode($content) . "</p>";
				$return .= "<ul>";
					$return .= (!empty($phone) ? "<li><i class=\"fa fa-phone\"></i>" . $phone . "</li>" : "");
					$return .= (!empty($cell_phone) ? "<li><i class=\"fa fa-mobile\"></i>" . $cell_phone . "</li>" : "");
					$return .= (!empty($email) ? "<li><i class=\"fa fa-envelope-o\"></i><a href='mailto:" . $email . "'>" . $email . "</a></li>" : "");
				$return .= "</ul>";
			$return .= "</div>";
			$return .= "<div class=\"social_team pull-left\">";
				$return .= "<ul class=\"social\">";
				foreach($icons as $icon){
					if($$icon !== false && !empty($$icon)){
						$return .= "<li class='margin-bottom-none'><a href=\"" . $$icon . "\" class=\"" . $icon . "\"></a></li>\n";
					}
				}
				$return .= "</ul>";
			$return .= "</div>";
			$return .= "<div class=\"clearfix\"></div>";
		$return .= "</div>";
		
		return $return;
		
	}
}
add_shortcode('person', 'person');

// Featured Services
if(!function_exists("featured_services")){
	function featured_services($atts, $content = null){
		extract(shortcode_atts(array( ), $atts));
		
		$return  = "<div class='featured_services width row-fluid'>";
		$return .= do_shortcode($content);
		$return .= "</div>";
		
		return $return;
	}
}
add_shortcode("featured_services", "featured_services");

if(!function_exists("featured_panel")){
	function featured_panel($atts, $content = null){
		extract(shortcode_atts(array(
			"title"           => "Featured Service",
			"icon"            => "",
			"hover_icon"      => "",
			"modal"           => false,
			"popover"         => false,
			"placement"       => "right",
			"title"           => "",
			"popover_content" => "",
			"image_link"      => "",
			'extra_class' => ''
	    ), $atts));

	    $icon       = wp_get_attachment_image_src($icon);
	    $hover_icon = wp_get_attachment_image_src($hover_icon);

		if(function_exists("vc_build_link")){
			$image_link = vc_build_link($image_link);
			$image_link = $image_link['url'];
		}

		$return  = "<div class='featured margin-top-25 " . (!empty($extra_class) ? $extra_class : "") . "'>";
	        $return .= "<h5>" . $title . "</h5>";

	        $return .= (!empty($image_link) ? "<a href='" . $image_link . "'>" : "");
	        $return .= "<img src='" . $icon[0] . "' data-hoverimg='" . $hover_icon[0] . "' alt=\"\" class=\"no_border\">";
	        $return .= (!empty($image_link) ? "</a>" : "");

	        $return .= "<p>" . do_shortcode($content) . "</p>";
	    $return .= "</div>";
		
		return $return;
	} 
}
add_shortcode("featured_panel", "featured_panel");

// Detailed Services
if(!function_exists("detailed_services")){
	function detailed_services($atts, $content = null){
		extract(shortcode_atts(array( 
			"title" => ""
		), $atts));
		
		$return  = "<div class=\"detail-service clearfix\">";
		$return .= (!empty($title) ? "<h5>" . $title . "</h5>" : "");
		$return .= do_shortcode($content);
		$return .= "</div>";
		
		$return  = remove_shortcode_extras($return);
		
		return $return;
	} 
}
add_shortcode("detailed_services", "detailed_services");

if(!function_exists("detailed_panel")){
	function detailed_panel($atts, $content = null){
		extract(shortcode_atts(array(
			"title" => "",
			"icon"  => "icon-wrench",
			'extra_class' => '',
			'link'  => '',
			'image' => ''
	    ), $atts));

		if(!empty($link)){
			if(function_exists("vc_build_link")){
				$link   = vc_build_link($link);

				$href   = $link['url'];
				$target = $link['target'];
			} else {
				$href   = $link;
			}	
		}

		if(isset($image) && !empty($image)){
			$image = wp_get_attachment_image_src( $image );
			$icon  = "<img src='" . $image[0] . "'>";
		} else {
			$icon = "<i class='" . $icon . "'></i>";
		}
		
		$return  = "<div class='detail-service " . (!empty($extra_class) ? $extra_class : "") . "'>";
		$return .= "<div class='details margin-top-25'>";

		$return .= (!empty($href) ? "<a href='" . $href . "'" . (isset($target) && !empty($target) ? " target='" . $target . "'" : "") . ">" : "") . " <h5>" . $icon . $title . "</h5>" . (!empty($href) ? "</a>" : "");
		$return .= "<p class='padding-top-10 margin-bottom-none'>" . do_shortcode($content) . "</p>";
		$return .= "</div></div>";
		
		return $return;
	}
}
add_shortcode("detailed_panel", "detailed_panel");

// Featured Brands
if(!function_exists("featured_brands")){
	function featured_brands($atts, $content = null){
		extract(shortcode_atts(array(
			'title' => '',
			'extra_class' => ''
		), $atts));

		wp_enqueue_script( 'bxslider' );
		
		$return = "<div class=\"featured-brand " . (!empty($extra_class) ? $extra_class : "") . "\">";
			$return .= (!empty($title) ? "<h3 class='margin-bottom-25'>" . $title . "</h3>" : "");
			$return .= "<div class=\"arrow2 pull-right clearfix\" id=\"slideControls\"><span class=\"next-btn\"></span><span class=\"prev-btn\"></span></div>";
			$return .= "<div class=\"carasouel-slider featured_slider\">";
				$return .= do_shortcode($content);
			$return .= "</div>";
		$return .= "</div>";
		
		return $return;
	}
}
add_shortcode("featured_brands", "featured_brands");

if(!function_exists("brand_logo")){
	function brand_logo($atts, $content = null){
		extract(shortcode_atts(array(
			"img"             => "",
			"hoverimg"        => "",
			"modal"	          => false,
			"popover"         => false,
			"placement"       => "right",
			"title"           => "",
			"popover_content" => "",
			"link"			  => "#"
	    ), $atts));

		if(function_exists("vc_build_link")){
			$link = vc_build_link($link);
			$link = $link['url'];
		}
		
		$return = "<div class='slide hoverimg'><a href='" . $link . "' style='background-image: url(" . wp_get_attachment_url($img) . ");' data-hoverimg='" . wp_get_attachment_url($hoverimg) . "'></a></div>";
		
		return $return;
	}
}
add_shortcode("brand_logo", "brand_logo");

// Portfolio
if(!function_exists("automotive_portfolio")){
	function automotive_portfolio($atts, $content = null){
		extract(shortcode_atts(array(
			"categories"   => "",
			"type"         => "details",
			"portfolio"    => 40,
			"columns"      => 3,
			"all_category" => "yes",
			"auto_resize"  => "yes",
			"sort_text"    => "Sort Portfolio By:",
			'extra_class' => ''
	    ), $atts));

	    wp_enqueue_script( 'mixit' );
	    wp_enqueue_script( 'jqueryfancybox' );
		
		switch($columns){
			case 1:
				$class    = 12;
				$length   = 245;
				$img_size = array(570, 296, true);
				break;
			
			case 2:
				$class    = 6;
				$length   = 245;
				$img_size = array(570, 296, true);
				break;
				
			case 3:
				$class    = 4;
				$length   = 155;
				$img_size = array(570, 296, true);
				break;
				
			case 4: 
				$class    = 3;
				$length   = 115;
				$img_size = array(570, 296, true);
				break;
		}
		
		$return  = "<div class='portfolio-container " . (!empty($extra_class) ? $extra_class : "") . "'>";
		$return .= "<div class=\"list_faq clearfix col-lg-12\">";
			$return .= "<h5>". $sort_text . "</h5>";
			$return .= "<ul class=\"portfolioFilter\">";
				$return .= "";
				$return .= "";
				
				if(isset($categories) && !empty($categories)){
					$categories      = explode(",", $categories);
					$sort_categories = ($all_category == "yes" ? "<li class=\"active\"><a href=\"#\" data-filter=\"*\" class=\"current filter\">" . __("All", "listings") . "</a></li>" : "");
					
					foreach($categories as $category){
						$safe_category = preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ", "_", html_entity_decode($category)) );

						$sort_categories .= "<li><a href=\"#\" class=\"filter\" data-filter=\"." .  $safe_category . "\">" . $category . " </a></li>";
					}
					
					$return .= $sort_categories;
				}
		
			$return .= "</ul>";
		$return .= "<div class='clearfix'></div></div>";
		$return .= "<div class=\"portfolioContainer portfolio_2\">";
			$args = array(
					'post_type'      => 'listings_portfolio',
					'order'          => 'ASC',
					'tax_query'      => array(
						array(
							'taxonomy' => 'portfolio_in',
							'field'    => 'term_id',
							'terms'    => $portfolio
						)
					),
					'posts_per_page' => -1,
					'orderby'        => 'date'
			);
			
			$the_query = new WP_Query( $args );
			
			if($the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();
				setup_postdata($the_query->post);
				
				$in_categories   = get_the_terms( $the_query->post->ID, "project-type");
				$categories_list = "";
				
				if(!empty($in_categories)){
					foreach($in_categories as $category){
						$categories_list .= (isset($category->name) && !empty($category->name) ? $category->name . ", " : "");
					}
				}				
				
				$format  = get_post_meta($the_query->post->ID, "format", true);
				$content = get_post_meta($the_query->post->ID, "portfolio_content", true);
				$links   = get_post_meta($the_query->post->ID, "portfolio_links", true);
				
				// determine image
				if($format == "image" && has_post_thumbnail($the_query->post->ID)){
					$image     = get_the_post_thumbnail($the_query->post->ID, $img_size, array('class' => 'portfolio image')); //"<img src='" . $image . "' alt='portfolio image'>";
					$image_id  = get_post_thumbnail_id($the_query->post->ID);
					$image_url = wp_get_attachment_url($image_id);
					
				} elseif($format == "video"){
					$video_id  = youtube_video_id($content);
					$image     = "<img src='http://img.youtube.com/vi/" . $video_id . "/hqdefault.jpg' alt='" . __("youtube thumbnail portfolio image", "listings") . "' />";
					$image_url = "http://img.youtube.com/vi/" . $video_id . "/hqdefault.jpg";	
					
				} elseif($format == "gallery"){
					$image     = "<img src='" . auto_image($content[0], "auto_slider", true) . "' alt='" . __("image", "listings") . "' class='" . __("portfolio image", "listings") . "' />";
					$image_url = wp_get_attachment_image_src($content[0], "full");

	                $image_url = $image_url[0];
	            }

	            if(isset($links[1]) && !empty($links[1])){
	                $image_url = $links[1];
	            }
				
				$the_content = get_the_content();
				$the_content = preg_replace('/\[[^\]]+\]/', '', $the_content);
				
				$got_content = strip_tags($the_content);
				$exploded = explode(", ", $categories_list);
				
				$classes = "";
				foreach($exploded as $explode){					
					$safe_category = preg_replace("/[^A-Za-z0-9 ]/", '', str_replace(" ", "_", html_entity_decode($explode)) );
					$classes .= str_replace(" ", "_", $safe_category) . " ";
				}
				
				$return .= "<div class=\"col-md-" . $class . " mix " . $classes . " " . ($type == "details" ? "margin-bottom-50" : "margin-bottom-30") . "\">";
					$return .= "<div class=\"box clearfix\"> <a class=\"fancybox\" href=\"" . $image_url . "\">" . $image . "</a>";
						if($type == "details"){
							$return .= "<div class='padding-top-25 padding-bottom-10'>";
								$return .= "<h2><a href='" . get_permalink($the_query->post->ID) . "'>" . get_the_title() . "</a></h2>";
								$return .= "<span>" . substr($categories_list, 0, -2) . " </span> </div>";
							$return .= "<p>" . (strlen($got_content) > $length ? substr($got_content, 0, ($length - 3)) . "..." : $got_content) . "</p>";
						}
					$return .= "</div>";
				$return .= "</div>";
										
				//$return .= "</div>";
				
				wp_reset_postdata();
			endwhile;
			
			endif;
			
		$return .= "</div></div>";
		
		return $return;	
	}
}
add_shortcode("portfolio", "automotive_portfolio");

// Alert
if(!function_exists("alert_shortcode")){
	function alert_shortcode($atts, $content = null){
		extract(shortcode_atts(array(
			"type"  => "info",
			"close" => "no",
			'extra_class' => ''
	    ), $atts));
		
		if($type == 0){
			$type = "danger";
		} elseif($type == 1){
			$type = "success";
		} elseif($type == 2){
			$type = "info";
		} elseif($type == 3){
			$type = "warning";
		}

		
		$return  = "<div class=\"alert alert-" . $type . " " . (!empty($extra_class) ? $extra_class : "") . "\">";
		$return .= (strtolower($close) != "no" ? "<button type=\"button\" class=\"close\" data-dismiss=\"alert\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">" . __("Close", "listings") . "</span></button>" : "");
	    $return .= do_shortcode($content);
	    $return .= "</div>";
		
		return $return;
	}
}
add_shortcode("alert", "alert_shortcode");

// Tooltip
if(!function_exists("tooltip")){
	function tooltip($atts, $content = null){
		extract(shortcode_atts(array(
			"type"      => "info",
			"close"     => "no",
			"placement" => "top",
			"title"     => "Title",
			"html"		=> "false"
	    ), $atts));
		
		$return  = "<span data-toggle='tooltip' data-title='" . $title . "' data-placement='" . $placement . "' data-html='" . $html . "' class='tooltip_js'>";
		$return .= do_shortcode($content);
		$return .= "</span>";
		
		return $return;
	}
}
add_shortcode("tooltip", "tooltip");

// pricing table
if(!function_exists("pricing_table")){
	function pricing_table($atts, $content){
		extract(shortcode_atts(array(
			"title"  => "Standard",
			"price"  => "19.99",
			"often"  => "",
			"button" => "Sign Up Now",
			"link"   => "#",
			"layout" => "3",
			'extra_class' => ''
	    ), $atts));

	    $link = (function_exists("vc_build_link") ? vc_build_link($link) : $link);
	    $link = (function_exists("vc_build_link") ? $link['url'] : $link);
		
		global $currency_symbol;
		
		if($layout == 3){
			$layout_class = "col-md-4 col-sm-4 col-xs-12";
		} elseif($layout == 4){
			$layout_class = "col-md-3 col-sm-6 col-xs-12";
		}
		
		//$return  = "<div class=\"". $layout_class . "\">";
	        $return  = "<div class=\"pricing_table " . (!empty($extra_class) ? $extra_class : "") . "\">";
	        	$return .= "<div class=\"pricing-header padding-vertical-10\"><h4>" . $title . "</h4></div>";
	        	$return .= "<div class=\"main_pricing\">";
	        
	        		$return .= "<div class=\"inside\">";
	               		$return .= "<span class=\"super\">" . (isset($lwp_options['currency_symbol']) && !empty($lwp_options['currency_symbol']) ? $lwp_options['currency_symbol'] : "") . "</span>";
						
						if(strstr($price, ".")){
							$price_exploded = explode(".", $price);
							$return .= "<span class=\"amt annual\">" . $price_exploded[0] . "</span><span class=\"sub1\">" . $price_exploded[1] . "</span>";
						} else {
							$return .= "<span class=\"amt annual\">" . $price . "</span>";	
						}
						
						$return .= (!empty($often) ? "<span class=\"slash\"><img src=\"" . LISTING_DIR . "images/slash.png\" alt=\"\" class=\"no_border\"></span>" : "");
						$return .= (!empty($often) ? "<span class=\"sub\">" . $often . "</span>" : "");
	                $return .= "</div>";
	        	$return .= "</div>";
	        	$return .= "<div class=\"category_pricing\">";
					$return .= "<ul>";
						$return .= do_shortcode($content);
					$return .= "</ul>";
	        	$return .= "</div>";
				$return .= "<div class=\"price-footer padding-top-20 padding-bottom-15\">";
					$return .= "<form method=\"post\" action=\"" . $link . "\">";
						$return .= "<input type=\"submit\" value=\"" . $button . "\" class='lg-button'>";
					$return .= "</form>";
				$return .= "</div>";
			$return .= "</div>";
		//$return .= "</div>";
		
		return $return;	
	}
}
add_shortcode("pricing_table", "pricing_table");

if(!function_exists("pricing_option")){
	function pricing_option($atts, $content = null){
		extract(shortcode_atts(array(), $atts));
		
		$return  = "<li>";
		$return .= do_shortcode($content);
		$return .= "</li>";
		
		return $return;
	}
}
add_shortcode("pricing_option", "pricing_option");

if(!function_exists("pricing_layout")){
	function pricing_layout($atts, $content = null){
		extract(shortcode_atts(array(
			"layout"  => "3"
	    ), $atts));
		
		$return  = "<div class=\"pricing_wrapper layout-" . $layout . "\">";
		$return .= do_shortcode($content);
		$return .= "</div>";
		
		return $return;	
	}
}
add_shortcode("pricing_layout", "pricing_layout");

// Feature boxes
if(!function_exists("feature_boxes")){
	function feature_boxes($atts, $content = null){ 
		$return  = "<div class='thumbnail_listings width row-fluid'>";
		$return .= do_shortcode($content);  
		$return .= "</div>";
		
		return $return;
	}
}
add_shortcode("feature_boxes", "feature_boxes");

// Featured boxes w/ icons
if(!function_exists("featured_boxes")){
	function featured_boxes($atts, $content = null){
		extract(shortcode_atts(array(
			"icon" => ""
		), $atts));
		
		$return  = "<div class=\"featured-service clearfix\">";
		$return .= do_shortcode($content);            
		$return .= "</div>";
		
		return $return;
	}
}
add_shortcode("featured_boxes", "featured_boxes");

if(!function_exists("featured_box")){
	function featured_box($atts, $content = null){
		extract(shortcode_atts(array(
			"icon"            => "fa fa-dashboard",
			"image"           => "",
			"hover_image"     => "",
			"cols"            => 3,
			"title"           => "",
			"modal"           => false,
			"popover"         => false,
			"placement"       => "right",
			"title"           => "",
			"popover_content" => "",
			"animation"		  => "",
			'extra_class' => ''
			), $atts));
					
			//$return  = "<div class=\"col-lg-" . $cols . " col-md-" . $cols . " col-sm-6 col-xs-12\">";
				$return = "<div class=\"featured " . (!empty($extra_class) ? $extra_class : "") . "\">";
					$return .= "<h5>" . $title . "</h5>";
					
					$return .= (empty($image) ? "<i class=\"" . $icon . "\"></i>" : "<img src=\"" . $image . "\" alt=\"\" " . (!empty($hover_image) ? "data-hoverimg=\"" . $hover_image . "\"" : "") . " class=\"no_border\">");
					
					$return .= "<p>" . do_shortcode($content) . "</p>";
				$return .= "</div>";
			//$return .= "</div>";

			return $return;
	}
}
add_shortcode("featured_box", "featured_box");

if(!function_exists("featured_icon_box")){
	function featured_icon_box($atts, $content = null){
		extract(shortcode_atts(array(
			"icon"  => "fa fa-dashboard",
			"title" => "",
			'extra_class' => ''
			), $atts));

		return "<span class='align-center featured_icon_box " . (!empty($extra_class) ? $extra_class : "") . "'><i class='" . $icon . " fa-6x'></i></span><h4>" . $title . "</h4><p>" . do_shortcode($content) . "</p>";

	}
}
add_shortcode("featured_icon_box", "featured_icon_box");

if(!function_exists("bolded")){
	function bolded($atts, $content = null){
		return "<span style='font-weight: 800;'>" . $content . "</span>";
	}
}
add_shortcode("bolded", "bolded");

// Search box
if(!function_exists("search_inventory_box")){
	function search_inventory_box($atts, $content = null){
		extract(shortcode_atts(array(
			"column_1" => '',
			"column_2" => '',
			"min_max"  => '',
			"page_id"  => '',
			'button_text' => __('Arama Yap', 'listings'),
			'extra_class' => ''
		), $atts));
		
		global $lwp_options;

		if(function_exists("vc_build_link")){
			$page_id = vc_build_link($page_id);
			$page_id = $page_id['url'];
		}

		//D($column_1);
		$column_1_items = (isset($column_1) && !empty($column_1) ? explode(",", $column_1) : "");
		$column_2_items = (isset($column_2) && !empty($column_2) ? explode(",", $column_2) : "");

		$return  = "<div class=\"search-form search_inventory_box row " . (!empty($extra_class) ? $extra_class : "") . " styled_input\">";
		$return .= "<form method=\"get\" action=\"" . $page_id . "\">";

			parse_str(parse_url($page_id, PHP_URL_QUERY), $result);
			$result['page_id'] = (isset($result['page_id']) && !empty($result['page_id']) ? $result['page_id'] : "");

			$return .= "<input type='hidden' name='page_id' value='" . $result['page_id'] . "'>";

			$return .= "<div class=\"col-md-6 clearfix\">";
			$return .= generate_search_dropdown($column_1_items, $min_max);
			$return .= "<div class='clearfix'></div></div>";


			$return .= "<div class=\"col-md-6 clearfix\">";
			$return .= generate_search_dropdown($column_2_items, $min_max);
			$return .= "<div class='clearfix'></div></div>";

			$return .= "<div class=\"col-md-12 clearfix search_categories\">";

			$i = 1;
			if(!empty($lwp_options['additional_categories']['value'])){
				foreach($lwp_options['additional_categories']['value'] as $category){
					if(!empty($category)){
						$return .= "<div class='form-element'><input type='checkbox' id='check_" . $i . "' name='" . str_replace(" ", "_", strtolower($category)) . "' value='1'><label for='check_" . $i . "'>" . $category . "</label></div>";
						$i++;
					}
				}
			}

			$return .= "<div class='clearfix'></div></div>";

			$return .= '<div class="form-element pull-right margin-right-10 col-md-12"><input type="submit" value="'. $button_text . '" class="find_new_vehicle pull-right"><div class="loading_results pull-right"><i class="fa fa-circle-o-notch fa-spin"></i></div></div>';
		
		$return .= "</form>";
		$return .= "</div>";

		return $return;
	}
}
add_shortcode("search_inventory_box", "search_inventory_box");

if(!function_exists("generate_search_dropdown")){
	function generate_search_dropdown($items, $min_max){
		$return = "";

		// is dropdown in the min/max part
		$min_max = explode(",", $min_max);

		if(!empty($items)){
			foreach($items as $column_item){
				$column_item = trim($column_item);
				$safe_name   = str_replace(" ", "_", mb_strtolower(addslashes($column_item)));

				$display_term = get_single_listing_category($safe_name);
				$display_term = (isset($display_term['singular']) && !empty($display_term['singular']) ? stripslashes($display_term['singular']) : "");

				// year workaround
				if(strtolower($column_item) == "year"){
					$safe_name = "yr";
				}

				if(in_array($column_item, $min_max)){
					//$return .= "min/max";

					$return .= "<div class='multiple_dropdowns'>";
						$return .= "<div class=\"my-dropdown make-dropdown\">";
							$return .= "<select name=\"" . $safe_name . "[]\" class=\"css-dropdowns\" tabindex=\"1\" >";
								$return .= "<option data-empty='true'>" . __("Min", "listings") . " " . $display_term . "</option>";
									$return .= listing_dropdown_terms($column_item, false, true);
							$return .= "</select>";
						$return .= "</div>";

						$return .= '<span class="my-dropdown-between">' . __('to', 'listings') . '</span>';

						$return .= "<div class=\"my-dropdown make-dropdown\">";
							$return .= "<select name=\"" . $safe_name . "[]\" class=\"css-dropdowns\" tabindex=\"1\" >";
								$return .= "<option data-empty='true'>" . __("Max", "listings") . " " . $display_term . "</option>";
									$return .= listing_dropdown_terms($column_item, false, true);
							$return .= "</select>";
						$return .= "</div>";
					$return .= "</div>";
				} else {
					if(strtolower($column_item) != "search"){

						$current_category = get_single_listing_category($safe_name);

						$return .= "<div class=\"my-dropdown make-dropdown\">";
							$return .= "<select name=\"" . $safe_name . "\" class=\"css-dropdowns\" tabindex=\"1\" data-sort=\"" . $safe_name . "\" " . (isset($current_category['dependancy']) && !empty($current_category['dependancy']) ? " data-dependancy='" . $current_category['dependancy'] . "'" : "") . " data-label-singular='"  . $current_category['singular'] . "' data-label-plural='"  . $current_category['plural'] . "'>";
							/*<?php echo (isset($key['dependancy']) && !empty($key['dependancy']) ? " data-dependancy='" . $key['dependancy'] . "'" : ""); ?>*/
								if(isset($current_category['dependancy']) && !empty($current_category['dependancy']) && strtolower($current_category['dependancy']) != "none"){ 
								 	$return .= "<option disabled>" . __("Choose a", "listings") . " " . (str_replace("_", " ", $current_category['dependancy'])) . "</option>"; 
	                            } else {
									$return .= "<option data-empty='true'>" . $display_term . "</option>";
									$return .= listing_dropdown_terms($column_item, true, true);
								}
							$return .= "</select>";
						$return .= "</div>";
					} else {
						$return .= "<input class='full-width' type='search' name='keywords' value='' placeholder='" . __("Refine with keywords", "listings") . "'>";
					}
				}
			}
		}

		return $return;
	}
}

// Vehicle Scroller
if(!function_exists("vehicle_scroller_shortcode")){
	function vehicle_scroller_shortcode($atts, $content = null){
		extract(shortcode_atts(array(
			"title"       => "",
			"description" => "",
			"sort"        => "",
			"listings"    => "",
			'extra_class' => '',
			"limit"		  => "1",
			"autoscroll"  => "false"
		), $atts));

		wp_enqueue_script( 'bxslider' );

		$other_options = array();

		if($autoscroll == "true"){
			$other_options['autoscroll'] = "true";
		}
		
		return vehicle_scroller($title, $description, $limit, $sort, $listings, $other_options);
	}
}
add_shortcode("vehicle_scroller", "vehicle_scroller_shortcode");

// Hours of operation
if(!function_exists("hours_of_operation")){
	function hours_of_operation($atts, $content = null){
		extract(shortcode_atts(array( ), $atts));
		
		$return  = "<div class='car-rate-block'>";
	    $return .= do_shortcode($content);
		$return .= "<div class='clearfix'></div>";
	    $return .= "</div>";
		
		return $return;
	}
}
add_shortcode("hours_of_operation", "hours_of_operation");

// icon w/ title
if(!function_exists("icon_title")){
	function icon_title($atts, $content = null){
		extract( shortcode_atts(array(
			'title' => '',
			'icon'  => 'fa fa-dashboard',
			'extra_class' => '',
			'link' 	=> '#'
		), $atts));

		if(function_exists("vc_build_link")){
			$link      = vc_build_link($link);
			$link      = $link['url'];
		}

		ob_start(); ?>

		<div class="small-block clearfix <?php echo (!empty($extra_class) ? $extra_class : ""); ?>">
	   		<h4 class="margin-bottom-25 margin-top-none"><?php echo $title; ?></h4>
	    	<a href="<?php echo $link; ?>">
	    		<span class="align-center"><i class="<?php echo $icon; ?> fa-7x"></i></span>
	    	</a> 
	   	</div>
	    <?php

	    return ob_get_clean();
	}
}
add_shortcode("icon_title", "icon_title");

// Button
if(!function_exists("auto_button_shortcode")){
	function auto_button_shortcode($atts, $content = null){
		extract(shortcode_atts(array(
			"color"           => false,
			"color_2"         => false,
			"border"          => false,
			"hover_color"     => false,
			"modal" 	      => false,
			"popover"         => false,
			"placement"       => "right",
			"title"           => "",
			"popover_content" => "",
			"size"			  => "",
			'extra_class' 	  => '',
			"href"			  => ''
		), $atts));
		
		$random_string = random_string();
		
		$remote_data = (filter_var($modal, FILTER_VALIDATE_URL) !== FALSE ? true : false);

		if(function_exists("vc_build_link")){
			$link      = vc_build_link($href);
			$target    = (isset($link['target']) && !empty($link['target']) ? $link['target'] : "");
			$link      = $link['url'];
		} else {
			$link 	   = (isset($href) && !empty($href) ? $href : "");
		}
		
		$return  = "";

		$return .= (!empty($link) ? "<a href='" . $link . "' " . (isset($target) && !empty($target) ? "target='" . $target . "' " : "") . ">" : "");
		$return .= "<button class='btn button" . (!empty($extra_class) ? $extra_class : "") . " " . (!empty($size) ? " " . $size . "-button" : "")  . "'" . ($color ? " style='background-color: " . $color . "' data-color='" . $color . "'" : "") . ($hover_color ? " data-hover='" . $hover_color . "'" : "");
		$return .= ($modal !== false ? "data-toggle='modal' data-target='#" . $modal . "'" : "");
		$return .= ($popover !== false ? "data-toggle='popover' data-placement='" . $placement . "' data-title='" . $title . "' data-content='" . $popover_content . "'" : "");
		$return .= ">" . do_shortcode($content) . "</button>";
		$return .= (!empty($link) ? "</a>" : "");
		
		return (isset($style) ? $style . "\n\n" : "") . $return;
	}
}
add_shortcode("button", "auto_button_shortcode");

// flipping card
if(!function_exists("flipping_card")){
	function flipping_card($atts, $content = null){
		extract( shortcode_atts( array( 
			'image'       => '',
			'larger_img'  => '',
			'title'       => '',
			'link'        => '',
			'extra_class' => '',
			'card_link'	  => ''
		), $atts));

		wp_enqueue_script( 'jqueryfancybox' );

		if(function_exists("vc_build_link")){
			$link      = vc_build_link($link);
			$target    = (isset($link['target']) && !empty($link['target']) ? $link['target'] : "");
			$link      = $link['url'];


			$card_link = vc_build_link($card_link);
			$card_link = $card_link['url'];

			$image      = wp_get_attachment_url( $image );
			$larger_img = wp_get_attachment_url( $larger_img );
		}

		ob_start(); ?>
		<div class="flip <?php echo (!empty($extra_class) ? $extra_class : ""); ?>" data-image="<?php echo $larger_img; ?>">
			<?php echo (!empty($card_link) ? '<a href="' . $card_link . '">' : ''); ?>
		        <div class="card">
		            <div class="face front">
		            	<img class="img-responsive no_border" src="<?php echo $image; ?>" alt="">
		            </div>
		            <div class="face back">
		                <div class='hover_title'><?php echo $title; ?></div>

		                <?php echo (!empty($link) ? '<a href="' . $link . '" ' . (isset($target) && !empty($target) ? "target='" . $target . "' " : "") . 'class="fancybox"><i class="fa fa-link button_icon"></i></a>' : ''); ?>
		                <?php echo (!empty($larger_img) ? '<a href="' . $larger_img . '" class="fancybox"><i class="fa fa-arrows-alt button_icon"></i></a>' : ''); ?>
		            </div>
		        </div>
		    <?php echo (!empty($card_link) ? '</a>' : ''); ?>
	    </div>
	    <?php

	    return ob_get_clean();
	}
}
add_shortcode("flipping_card", "flipping_card");

// contact form
if(!function_exists("auto_contact_form")){
	function auto_contact_form($atts, $content = null){ 
		extract( shortcode_atts(array(
			'name' => __("Name  (Required)", "listings"),
			'email' => __("Email  (Required)", "listings"),
			'message' => __("Your Message", "listings"),
			'button' => __("Send Message", "listings"),
			'extra_class' => ''
		), $atts));

		wp_enqueue_script( 'contact_form' );

		return '<fieldset id="contact_form" class="form_contact ' . (!empty($extra_class) ? $extra_class : "") . '">
		            <div class="contact_result"></div>
	                <input type="text" name="name" class="form-control margin-bottom-25" placeholder=" ' . $name . '" />
	                <input type="email" name="email" class="form-control margin-bottom-25" placeholder="' . $email . '" />
	                <textarea name="message" class="form-control margin-bottom-25 contact_textarea" placeholder="' . $message . '" rows="7"></textarea>
	                <input id="submit_btn" class="submit_contact_form" type="submit" value="' . $button . '">
	            </fieldset>';
	}
}
add_shortcode("auto_contact_form", "auto_contact_form");

// hours table
if(!function_exists("hours_table")){
	function hours_table($atts, $content = null){
		extract( shortcode_atts(array(
			'mon' => __("Closed", "listings"),
			'tue' => __("Closed", "listings"),
			'wed' => __("Closed", "listings"),
			'thu' => __("Closed", "listings"),
			'fri' => __("Closed", "listings"),
			'sat' => __("Closed", "listings"),
			'sun' => __("Closed", "listings"),
			'title' => __("Hours", "listings"),
			'extra_class' => ''
		), $atts));

		return '<table class="table table-bordered no-border font-12px hours_table ' . (!empty($extra_class) ? $extra_class : "") . '">
		<thead>
			<tr>
				<td colspan="2"><strong>' . $title . '</strong></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>' . __('Mon', 'listings') . ':</td>
				<td>' . $mon . '</td>
			</tr>
			<tr>
				<td>' . __('Tue', 'listings') . ':</td>
				<td>' . $tue . '</td>
			</tr>
			<tr>
				<td>' . __('Wed', 'listings') . ':</td>
				<td>' . $wed . '</td>
			</tr>
			<tr>
				<td>' . __('Thu', 'listings') . ':</td>
				<td>' . $thu . '</td>
			</tr>
			<tr>
				<td>' . __('Fri', 'listings') . ':</td>
				<td>' . $fri . '</td>
			</tr>
			<tr>
				<td>' . __('Sat', 'listings') . ':</td>
				<td>' . $sat . '</td>
			</tr>
			<tr>
				<td>' . __('Sun', 'listings') . ':</td>
				<td>' . $sun . '</td>
			</tr>
		</tbody>
		</table>';
	}
}
add_shortcode("hours_table", "hours_table");

// contact information
if(!function_exists("auto_contact_information")){
	function auto_contact_information($atts, $content = null){
		extract( shortcode_atts(array(
			'company' => '',
			'address' => '',
			'phone'   => '',
			'email'   => '',
			'web'     => '',
			'extra_class' => ''
		), $atts));

		ob_start();	?>
		<div class="address clearfix margin-right-25 padding-bottom-40 <?php echo (!empty($extra_class) ? $extra_class : ""); ?>">
		    <div class="icon_address">
		        <p><i class="fa fa-map-marker"></i><strong><?php _e("Address", "listings"); ?>:</strong></p>
		    </div>
		    <div class="contact_address">
		        <p class="margin-bottom-none"><?php echo (!empty($company) ? $company . "<br>" : ""); ?>
		            <?php echo (!empty($address) ? $address : ""); ?></p>
		    </div>
		</div>
		<div class="address clearfix address_details margin-right-25 padding-bottom-40">
		    <ul class="margin-bottom-none">
		        <?php echo (!empty($phone) ? '<li><i class="fa fa-phone"></i><strong>' . __('Phone', 'listings') . ':</strong> <span>' . $phone . '</span></li>' : ''); ?>
		        <?php echo (!empty($email) ? '<li><i class="fa fa-envelope-o"></i><strong>' . __('Email', 'listings') . ':</strong> <a href="mailto:' . $email . '">' . $email . '</a></li>' : ''); ?>
		        <?php echo (!empty($web) ? '<li class="padding-bottom-none"><i class="fa fa-laptop"></i><strong>' . __('Web', 'listings') . ':</strong> <a href="' . $web . '">' . $web . '</a></li>' : ''); ?>
		    </ul>
		</div>
		<div class="clearfix"></div>
		<?php
		$return = ob_get_clean();

		return $return;
	}
}
add_shortcode("auto_contact_information", "auto_contact_information");

// google map
if(!function_exists("auto_google_map")){
	function auto_google_map($atts, $content = null){
		extract( shortcode_atts( array(
			'longitude' => '-79.38',
			'latitude'  => '43.65',
			'zoom'      => '7',
			'height'    => '390',
			'map_style' => '',
			'scrolling' => 'true',
			'extra_class' => '',
			'parallax_disabled' => ''
		), $atts));

		wp_enqueue_script( 'google-maps' );

		if(base64_encode(base64_decode($map_style)) === $map_style){
			$map_style = urldecode(html_entity_decode(base64_decode($map_style)));
		}

		return "<div class='contact google_map_init " . (!empty($extra_class) ? $extra_class : "") . "' data-longitude='" . $longitude . "' data-latitude='" . $latitude . "' data-zoom='" . $zoom . "' style='height: " . $height . "px;'" . (!empty($map_style) ? " data-style='" . $map_style . "'" : "") . " data-scroll='" . $scrolling . "'" . (!empty($parallax_disabled) && $parallax_disabled == "disabled" ? " data-parallax='false'" : "") . "></div>";
	}
}
add_shortcode("auto_google_map", "auto_google_map");

// Modal Window
if(!function_exists("modal_window")){
	function modal_window($atts, $content = null){
		extract(shortcode_atts(array(
			"title" => "",
			"id"    => ""
		), $atts));
		
		$return  = "<!-- Modal -->";
		$return .= "<div class=\"modal fade\" id=\"" . $id . "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">";
		  $return .= "<div class=\"modal-dialog\">";
			$return .= "<div class=\"modal-content\">";
			  $return .= "<div class=\"modal-header\">";
				$return .= "<button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">" . __("Close", "listings") . "</span></button>";
				$return .= "<h4 class=\"modal-title\" id=\"myModalLabel\">" . $title . "</h4>";
			  $return .= "</div>";
			  $return .= "<div class=\"modal-body\">";
				$return .= "<div>" . do_shortcode($content) . "</div>";
			  $return .= "</div>";
			  $return .= "<div class=\"modal-footer\">";
				$return .= "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>";
				//$return .= "<button type=\"button\" class=\"btn btn-primary\">Save changes</button>";
			  $return .= "</div>";
			$return .= "</div>";
		  $return .= "</div>";
		$return .= "</div>";
		
		return $return;
	}
}
add_shortcode("modal", "modal_window");

// Tabs
if(!function_exists("tabs")){
	function tabs($atts, $content = null){
		extract(shortcode_atts(array(
			'title' => '',
			'extra_class' => ''
		), $atts));	
		$GLOBALS['tab_count'] = 0;	
		
		do_shortcode($content);
		
		if( is_array( $GLOBALS['tabs'] ) ){
			foreach( $GLOBALS['tabs'] as $tab ){
				$tabs[]  = '<li' . (!isset($tabs) ? " class='active'" : "") . '><a href="#' . strtolower(str_replace(" ", "-", $tab['title'])) . '">' . $tab['title'] . '</a></li>';
				$panes[] = '<div class="tab-pane' . (!isset($panes) ? " active" : "") . '" id="' . strtolower(str_replace(" ", "-", $tab['title'])) . '">'. do_shortcode($tab['content']) .'</div>';
			}
			
			$return  = '<ul class="nav nav-tabs tabs_shortcode ' . (!empty($extra_class) ? $extra_class : "") . '" role="tablist">'.implode( "\n", $tabs ).'</ul>';
			$return .= "<div class=\"tab-content\">";
			$return .= ''.implode( "\n", $panes );
			$return .= '</div>'."\n";
			//$return .= "</div>";
		}
			
		return $return;
	}
}
add_shortcode('tabs', 'tabs');

// Single tab
if(!function_exists("single_tab")){
	function single_tab($atts, $content = null){
		extract(shortcode_atts(array(
			'title' => ''
		), $atts));
		
		$x = $GLOBALS['tab_count'];
		$GLOBALS['tabs'][$x] = array( 'title' => sprintf( $title, $GLOBALS['tab_count'] ), 'content' =>  $content );
		$GLOBALS['tab_count']++;
	}
}
add_shortcode('tab', 'single_tab');

// Fontello
if(!function_exists("fontello_icon")){
	function fontello_icon($atts, $content = null){
		extract(shortcode_atts(array( 
			'icon'  => 'fire',
			'size'  => '',
			'color' => ''
			), $atts));
			
		$css  = (!empty($size) || !empty($color) ? " style='" : "");
		$css .= (isset($css) && !empty($size) ? "font-size: " . $size . ";" : "");
		$css .= (isset($css) && !empty($color) ? "color: " . $color . ";" : "");
		$css .= (!empty($size) || !empty($color) ? "'" : "");
			
		$the_icon  = "<i class='" . $icon . " fontello'";
		$the_icon .= (!empty($css) ? $css : "");
		$the_icon .= "></i>";
		
		return $the_icon;
	}
}
add_shortcode('fontello_icon', 'fontello_icon');

// Video
if(!function_exists("auto_video")){
	function auto_video($atts, $content = null){
		extract( shortcode_atts( array(
			'url'    => 'http://www.youtube.com/watch?v=3f7l-Z4NF70',
			'width'  => 560,
			'height' => 315
		), $atts));

		$video = listing_video($url);
		
		if($video['service'] != "unknown"){
			if($video['service'] == "youtube"){
				return '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $video['id'] . '?vq=hd720&autoplay=0&rel=0" frameborder="0" allowfullscreen></iframe>';
			} elseif($video['service'] == "vimeo") {
				return '<iframe src="//player.vimeo.com/video/' . $video['id'] . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			}
		} else {
			return "unknown url";
		}
		
	}
}
add_shortcode('auto_video', 'auto_video');

// Clearfix
if(!function_exists("clear_both")){
	function clear_both($atts, $content = null){
		return "<div class='clearfix'></div>";
	}
}
add_shortcode("clear", "clear_both");

// Line break
if(!function_exists("line_break")){
	function line_break($atts, $content = null){
		return "<br />";
	}
}
add_shortcode("br", "line_break");

// heading
if(!function_exists("heading_shortcode")){
	function heading_shortcode($atts, $content = null){
		extract( shortcode_atts( array(
			'heading' => 'h1'
		), $atts));
		
		return "<" . $heading . ">" . $content . "</" . $heading . ">";
	}
}
add_shortcode("heading", "heading_shortcode");

if(!function_exists("car_comparison_sc")){
	function car_comparison_sc($atts, $content = null){
		extract( shortcode_atts( array(
			'car_ids' => ''
		), $atts) );
		
		if(empty($car_ids)){
			$cookie = (isset($_COOKIE['compare_vehicles']) && !empty($_COOKIE['compare_vehicles']) ? $_COOKIE['compare_vehicles'] : "");
		} else {
			$cookie = $car_ids;
		}
		
		ob_start();

		if(isset($cookie) && !empty($cookie)){
			$cookie = htmlspecialchars(urldecode($cookie));
			$cookie = array_filter(explode(",", $cookie));
			$total  = count($cookie);
			
			if($total <= 1){
				_e("You must have more than 1 vehicle selected to compare it.", "listings");
			} else {
				switch($total){
					case 2:
						$class = "6";
						break;
					
					case 3:
						$class = "4";
						break;
						
					case 4:
						$class = "3";
						break;
				}
				
				echo "<div class='row total_" . $total . "'>";
							
				if($total >= 5){
					_e("Maximum 4 vehicles", "listings");
				} else {
					foreach($cookie as $car){
						echo car_comparison($car, $class);
					}
				}
				echo "</div>";
			}
		} else {
			_e("You have no vehicles selected", "listings");
		}

		$return = ob_get_clean();

		return $return;
	}
}
add_shortcode("car_comparison", "car_comparison_sc");

//********************************************
//	Shortcode Generator
//***********************************************************
function shortcode_dialog(){
	$shortcodes = array("columns" => "columns",
						"elements" => array("button"			   	=> "Button",
											"heading"			   	=> "Heading" ),				   
						"other"    => array("testimonials"         	=> "Testimonials",
										    "progress_bar"         	=> "Progress Bar",
										    "dropcaps"             	=> "Dropcaps",
										    "list"                 	=> "List",
											"tooltip"              	=> "Tooltip",
										    "quote"                	=> "Quote",
											"portfolio"            	=> "Portfolio",
											"alert"                	=> "Alert",
											"featured_boxes"       	=> "Featured Boxes",
											"search_inventory_box" 	=> "Inventory Search Box",
											"vehicle_scroller"     	=> "Vehicle Scroller",
											"hours_of_operation"   	=> "Hours Of Operation",
											"modal"				   	=> "Modal Window",
											"tabs"				   	=> "Tabs",
											"staff_list"		   	=> "Staff List",
											"detailed_services"	   	=> "Detailed Services",
											"auto_video"			=> "Video",
											"insert-clear"		   	=> "Clear Fix",
											"insert-br"			   	=> "Line Break",
											"pricing_table"		   	=> "Pricing Table",
											"faq"				   	=> "FAQ",
											"featured_brands" 	   	=> "Featured Brands",
											"insert-recent_posts_scroller" => "Recent Posts"),
						"icons" => "icons"
										   
						);
	
	$default_fontello = get_option('default_fontello_font');
	
	if(isset($default_fontello) && !empty($default_fontello)){
		$shortcodes['icons']['fontello'] = "Fontello Icons";
	}
	
	echo "<div id='shortcode-modal' style='display: none;'>";
	echo "<ul class='shortcode_list'>";
	
	ksort_deep($shortcodes);
	
	// icons
	$icons =       array("columns"  				=> "fa-columns",
						 "elements" 				=> "fa-code",
						 "icons"    				=> "fa-picture-o",
						 "other"    				=> "fa-wrench");
				   	
	$child_icons = array("quote"	          		=> "fa-quote-left",
						 "alert"	          		=> "fa-warning",
						 "list"		          		=> "fa-list",
						 "dropcaps"           		=> "fa-text-height",
						 "vehicle_scroller"   		=> "fa-truck",
						 "progress_bar"       		=> "fa-tasks",
						 "staff_list"		  		=> "fa-group",
						 "hours_of_operation" 		=> "fa-clock-o",
						 "search_inventory_box" 	=> "fa-search",
						 "portfolio"				=> "fa-folder-open-o",
						 "modal"					=> "fa-list-alt",
						 "testimonials"				=> "fa-comments-o",
						 "button"					=> "fa-certificate",
						 "featured_boxes"			=> "fa-th-large",
						 "featured_icon_boxes"  	=> "fa-th-large",
						 "tabs"						=> "fa-folder",
						 "tooltip"					=> "fa-info",
						 "detailed_services"		=> "fa-th",
						 "auto_video"				=> "fa-youtube-play",
						 "insert-br"				=> "fa-level-down",
						 "insert-clear"				=> "fa-sort-amount-asc",
						 "pricing_table"			=> "fa-usd",
						 "faq"						=> "fa-question-circle",
						 "featured_brands"			=> "fa-html5",
						 "heading"					=> "fa-font",
						 "insert-car_comparison"	=> "fa-reorder",
						 "insert-listings"			=> "fa-list-alt",
						 "insert-recent_posts_scroller"	=> "fa-indent");
	
	foreach($shortcodes as $key => $shortcode){
		echo "<li>" . (isset($icons[$key]) ? "<i class='fa " . $icons[$key] . "'></i>" : "") . " <a href='#' data-title='" . $key . "'>" . ucwords($key) . "</a>";
			if(is_array($shortcode)){
				echo "<ul class='child_shortcodes'>";
				foreach($shortcode as $key => $code){
					echo "<li>" . (isset($child_icons[$key]) ? "<i class='fa " . $child_icons[$key] . "'></i>" : "") . " <a href='#' data-shortcode='" . $key . "'>" . $code . "</a></li>";
				}
				echo "</ul>";
			}
		echo "</li>";
	}
	echo "</ul>";
	
	echo "<div class='shortcode_generator'>";
	
	echo "</div>";
	echo "<div class='column_generator'>";
	
	echo "</div>";
	echo "</div>";
}
add_action('admin_footer', 'shortcode_dialog');

function generate_shortcode(){
	$form = array();
	switch($_POST['shortcode']){
		case "progress_bar":
			$form['color']    = "color_picker";
			$form['filled']   = "text";
			$form['content']  = "text";
			$form['striped']  = array("select", array("on" => "On", "off" => "Off"));
			$form['animated'] = array("select", array("on" => "On", "off" => "Off"));
		break;
			
		case "dropcaps":
			$form['size'] = array("size", "px", "5", "250");
		break;
			
		case "list":
			$form['style']    			  = array("select", array("arrows" => "arrows", "checkboxes" => "checkboxes"));
			$form['number_of_list_items'] = array("number", "list_item", "icon");
		break;
			
		case "tooltip":
			$form['title']     = "text";
			$form['placement'] = array("select", array("top" => "top", "right" => "right", "bottom" => "bottom", "left" => "left"));
			$form['content']   = "text";
			$form['html']      = array("select", array("false" => "Off", "true" => "On"));
		break;
			
		case "quote":
			$form['color'] = "color_picker";
		break;
		
		case "testimonials":
			$form['number_of_testimonial_quote'] = array("number", "testimonial_quote", "name");
		break;	
			
		case "portfolio":
			$portfolios         = get_terms("portfolio_in");
			$categories         = get_terms("project-type");
						
			$form['categories'] = array("select", $categories, "multi");
			$form['portfolio']  = array("select", $portfolios);
			$form['type']       = array("select", array("details" => "details", "classic" => "classic"));
			$form['columns']    = array("select", array(2 => 2, 3 => 3, 4 => 4));
		break;
		
		case "alert":
			$form['type'] = array("select", array("error", "success", "warning", "info"));
		break;
			
		case "featured_boxes":
			$form['featured_box'] = array("number", "featured_box", "title,button_text,button_link,hover_title,hover_text,image");
		break;
		
		case "featured_icon_boxes":
			$form['featured_icon_box'] = array("number", "featured_icon_box", "title,icon");
		break;
		
		case "search_inventory_box":
			$all_pages = get_pages();
			$pages     = array();
			
			foreach($all_pages as $page){
				$pages[$page->ID] = $page->post_title;
			}
			
			$form['page'] = array("select", $pages);
		break;
		
		case "vehicle_scroller":
			$all_listings = get_posts( array( 'post_type' => 'listings' ) );
			$listings     = array();
			
			foreach($all_listings as $single_listing){
				$listings[$single_listing->ID] = $single_listing->post_title;
			}
			
			$form['title']       = "text";
			$form['description'] = "text";
			$form['sort']		 = array("select", array("newest" => "newest", "oldest" => "oldest", "similar" => "similar"));
			$form['listings']    = array("select", array_filter($listings), "multi");
		break;
		
		case "button":
			$form['content']     = "text";
			//$form['border']      = "color_picker";
			$form['color']       = "color_picker";
			//$form['color_2']     = "color_picker";
			$form['hover_color'] = "color_picker";
		break;
		
		case "heading":
			$form['heading'] = array("select", array("h1" => "Heading 1 (&lt;h1>)", "h2" => "Heading 2 (&lt;h2>)", "h3" => "Heading 3 (&lt;h3>)", "h4" => "Heading 4 (&lt;h4>)", "h5" => "Heading 5 (&lt;h5>)", "h6" => "Heading 6 (&lt;h6>)"));
			$form['content'] = "text";
		break;
		
		case "modal":
			$form['id']      = "text";
			$form['title']   = "text";
			$form['content'] = "text";
		break;
			
		case "tabs":
			$form['number_of_tabs'] = array("number", "tab", "title");
			break;
		
		case "staff_list":
			$form['number_of_staff'] = array("number", "person", "name,position,phone,cell_phone,email,img,facebook,twitter,youtube,vimeo,linkedin,rss,flickr,skype,google,pinterest");
		break;
		
		case "detailed_services":
			$form['number_of_services'] = array("number", "detailed_panel", "title,icon");
		break;
		
		case "auto_video":
			$form['url']    = "text";
			$form['width']  = "text";
			$form['height'] = "text";
		break;
		
		case "pricing_table":
			$form['title']  = "text";
			$form['price']  = "text";
			$form['button'] = "text";
			$form['link']   = "text";
			$form['number_of_options'] = array("number", "pricing_option", "");
		break;
		
		case "faq":
			$form['categories']      = "text";
			$form['number_of_items'] = array("number", "toggle", "title,categories");
		break;
		
		case "featured_brands":
			$form['number_of_brands'] = array("number", "brand_logo", "img,hoverimg");
		break;
				
		default: 
			$form['column_content'] = array("column_content", $_POST['shortcode']);
		break;
	}
	
	process_form($form, $_POST['shortcode']);
	
	die;
}
add_action("wp_ajax_generate_shortcode", "generate_shortcode");
add_action("wp_ajax_nopriv_generate_shortcode", "generate_shortcode");

function process_form($form, $shortcode){ ?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
        $('.color-picker').wpColorPicker();
		
		if($("select.multi-select.categories").length){
			$("select.multi-select.categories").multiSelect({
			  selectableHeader: "<div class='custom-header'><?php _e("All Categories", "listings"); ?></div>",
			  selectionHeader: "<div class='custom-header'><?php _e("Selected Categories", "listings"); ?></div>"
			});
		} else if ($("select.multi-select").length){
			$("select.multi-select").multiSelect({
			  selectableHeader: "<div class='custom-header'><?php _e("All listings", "listings"); ?></div>",
			  selectionHeader: "<div class='custom-header'><?php _e("Selected Listings", "listings"); ?></div>"
			});
		}
		
		if($("input[name='color']").length > 1){
			i = 1;
			$("input[name='color']").each( function(index, element){
				var name = $(this).data('name');
				
				if(name){
					$(this).attr('name', name);				
				} else {
					$(this).attr('name', 'color_' + i);
					i++;
				}
			});
		}
        $('.ui-dialog-title').html("<?php echo str_replace("_", " ", ucwords($shortcode)); ?>");
		//$('div.ui-dialog-titlebar.ui-widget-header.ui-corner-all.ui-helper-clearfix').append('<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only shortcode_back" role="button" aria-disabled="false"><span class="ui-button-icon-primary ui-icon ui-icon ui-icon-circle-triangle-w"></span></button>');
        $("#generateShortcode").one("click", function(){			
			var shortcode_name = "<?php echo $shortcode; ?>";
			
			shortcode          = "[" + shortcode_name;
			var content        = false;
			var no_closing     = false;
			var is_html        = false;
			var first          = shortcode_name.substr(0,1);
			
			if(shortcode_name == "hours_of_operation"){
				shortcode      = shortcode + "]<br>";
			}
			
			if($("#generateShortcode").hasClass('slider_gen')){
				var add_shortcode = "";
				$("#shortcode_options :input").not(".title, .title_toggle, .ajax_created").each( function(index, element){
					var name  = $(this).attr('name');
					var value = $(this).val();
					
					add_shortcode += " " + name + "='" + value + "'";
					
				});
				
				shortcode += add_shortcode + "]<br>"; 
			}
				
			if($.isNumeric(first)){
				is_html = true;
				switch(shortcode_name.substr(2, shortcode_name.length)){
					case "full":
						var span = 12;
						break;
					case "halfs":
						var span = 6;
						break;
					case "thirds":
						var span = 4;
						break;
					case "fourths":
						var span = 3;
						break;
					case "seconds": 
						var span = 2;
				}
				
				shortcode = "<div class='width row-fluid'>";
				$("#shortcode_options :input").not(".title, .title_toggle").each( function(index, element){
                	var value  = $(this).val();
					if($(".title_toggle:checkbox:checked").length > 0){
						var string  = String($(this).classes());
						var heading = $("select." + string + " option:selected").text();
						
						shortcode  = shortcode + "<div class='span" + span + "'><" + heading + ">" + $("input." + string).val() + "</" + heading + ">" + value + "</div>";
					} else {
						shortcode  = shortcode + "<div class='span" + span + "'>" + value + "</div>";
					}
				});
				shortcode = shortcode + "</div>";
			}
			
			// using the slider to generate shortcode
			if($("#generateShortcode").hasClass('slider_gen')){
				$(".ajax_form_slider table").each( function(index, element){
					var useloop = $(this).data('useloop');
					var content = '';
					
					shortcode += "[" + useloop + " ";
					
					$(this).find(":input").each( function( index2, element2){
						var name  = $(this).attr('name');
						var value = $(this).val();
						
						if(name == "content"){		
							content = value;				
						} else {
							shortcode += name + "='" + value + "' ";
						}
					});
					
					shortcode += "]" + (content != "" ? content : "") + "[/" + useloop + "]<br />";
				});
				
				shortcode += "[/" + shortcode_name + "]<br />";
				is_html = true;
			} else {			
				$("#shortcode_options :input").not(".wp-picker-clear").each( function(index, element){
					var name  = $(this).attr("name");
					var loop  = $(this).data('loop');
					
					if(name == "hours_of_operation"){
						var value       = 1;
						var field_value = $(this).val();
					} else {
						var value       = $(this).val();
						var field_value = null;
					}
					
					if(!is_html){
						
						if(loop){
							var loop_attr  = $(this).data('loopattr');
							
							if(loop_attr){
								var attributes = loop_attr.split(",");
								
								if(field_value == "icon"){
									var value = 2;
								}
								
								for(var i=0; i < value; i++){
									
									if(name != "hours_of_operation"){
										shortcode = (i == 0 ? shortcode + "]<br />" : shortcode) + "[" + loop;
									} else {
										shortcode = shortcode + "[" + loop;
									}
									
									if(field_value != "hours"){
									
										for(var ii=0; ii < attributes.length; ii++){
											if(name == "hours_of_operation" && attributes[ii] == "type"){
												shortcode = shortcode + " " + attributes[ii] + "='" + field_value + "'";
											} else {
												shortcode = shortcode + " " + attributes[ii] + "=''";
											}
										}
									
									} else {
										shortcode = shortcode + " type='hours'";
									}
									
									if(name == "hours_of_operation"){
										shortcode = shortcode + "]<br />";
									} else if(field_value != "hours" && name == "hours_of_operation"){
										shortcode = shortcode;
									} else {
										shortcode = shortcode + "] [/" + loop + "]<br />";
									}
								}
							} else {
								for(var i=0; i < value; i++){
									shortcode = (i == 0 ? shortcode + "]<br />" : shortcode) + "[" + loop + "] [/" + loop + "]<br />";
								}
							}
							
							no_closing = true;
						} else if(name != "content"){
							shortcode = shortcode + " " + name + "='" + value + "'";
						} else {
							content   = value;
						}
					}
				}); 	
			}
			
			getContent = tinyMCE.activeEditor.selection.getContent();
			
			if(!is_html){
				shortcode  = (no_closing !== true ? shortcode + "]" : shortcode) + (content !== false ? content : getContent) + "[/" + shortcode_name + "]";
			}
			
			//$("#shortcode-modal").dialog("close");
			$( "#shortcode-modal" ).dialog().dialog("destroy");
			$( "#shortcode-modal .shortcode_generator, #shortcode-modal .shortcode_list ul.child_shortcodes").hide();
			$( "#shortcode-modal .shortcode_list").show();
			
			//tinyMCE.execInstanceCommand('content', "mceInsertContent", false, shortcode);
 			tinyMCE.execCommand('mceInsertContent', false, shortcode);
			return false;
		});
		
		$(".shortcode_slider").each( function(index, element){
			var id      = $(this).data('id');
			var minimum = $(this).data('min');
			var maximum = $(this).data('max');
			var units   = $(this).data('unit');
			
			$(this).slider({
				min: minimum,
				max: maximum,
				slide: function( event, ui ){
					$("#" + id).val(ui.value + units);
				}
			});
		});
		
		function toTitleCase(str) {
			return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
		}
		
		function makeid() {
			var text = "";
			var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		
			for( var i=0; i < 7; i++ )
				text += possible.charAt(Math.floor(Math.random() * possible.length));
		
			return text;
		}
		
		function stringFill3(x, n) {
			var s = '';
			for (;;) {
				var random_data = makeid();
				
				if (n & 1) s += x.replace("EASTEREGG", random_data);
				n >>= 1;
				if (n) x += x.replace("EASTEREGG", random_data);
				else break;
			}
			return s;
		}
		
		if($("#number_of_slider").length){
			var name     = $("#number_of_slider").data('name');
			var loop     = $("#number_of_slider").data('loop');
			var loopattr = $("#number_of_slider").data('loopattr');
			
			$("#generateShortcode").addClass('slider_gen');
			
			$("#number_of_slider").slider({
				min: 0,
				max: 10,
				slide: function( event, ui ) {
					var generated_html = "";
										
					//$("input[name='" + name + "']").val( ui.value );
					$(".slider_number").text( ui.value );
					
					var generated_html = stringFill3($(".hidden_form").html(), ui.value );
					
					$(".ajax_form_slider").html(generated_html);
				}
			});
			
			//$("input[name='" + name + "']").val( $("#number_of_slider").slider( "value" ) );
			$(".slider_number").text( $("#number_of_slider").slider( "value" ) );
		}
    });
	</script>
	
	<?php	
	echo "<table border='0' id='shortcode_options'>";
	
	$has_toolpop = array("button", "featured_icon_box", "brand_logo", "featured_panel");
	
	foreach($form as $key => $item){
		if($key == "hours_of_operation_item"){			
			$select_menu  = "<select name='hours_of_operation' data-loop='hours_of_operation_item' data-loopattr='type,title,icon'>";
			$select_menu .= "<option value='icon'>2 " . __("Icons", "listings") . "</option>";
			$select_menu .= "<option value='hours'>" . __("Hours", "listings") . "</option>";
			$select_menu .= "</select>";
			
			echo "<tr><td style='width: 100px;'>" . __("First Group", "listings") . ": </td><td>" . $select_menu . "</td></tr>"; 
			echo "<tr><td style='width: 100px;'>" . __("Second Group", "listings") . ": </td><td>" . $select_menu . "</td></tr>"; 
			echo "<tr><td style='width: 100px;'>" . __("Third Group", "listings") . ": </td><td>" . $select_menu . "</td></tr>"; 
		} else {
			echo "<tr><td class='spacer'></td></tr>";
			echo "<tr><td style='width: 100px; vertical-align: top'>";
			if($key == "html"){
				$label = __("HTML in Tooltip", "listings");
			} elseif($key == "filled") {
				$label = $key . " (%)";
			} else {
				$label = $key;
			}
			echo str_replace("_", " ", ucwords($label));
			echo ": " . ($item[0] == "number" ? "<span class='slider_number'>0</span>" : "") . ($item[0] == "column_content" ? "<br><br> Titles: <input type='checkbox' class='title_toggle'>" : "") . "</td></tr><tr><td>"; 
			
			switch($item){
				case "color_picker":
					echo "<input type=\"text\" value=\"#c7081b\" class=\"color-picker\" name=\"color\" data-name=\"" . $key . "\" />";
					break;
					
				case "text":
					echo "<input type=\"text\" name=\"" . $key . "\" value=\"\" />";
					break;
					
				case "icon":
					$random_string = random_string();
					echo "<span class='button sc_icon_selector' data-code='" . $random_string . "'>Icon: </span>";
				break;
					
				default:
					switch($item[0]){
						case "size":
							$id = random_string();
							echo "<div data-unit=\"" . $item[1] . "\" data-min=\"" . $item[2] . "\" data-max=\"" . $item[3] . "\" data-id=\"" . $id . "\" class=\"shortcode_slider\"></div>";
							echo "<input type=\"text\" name=\"" . $key . "\" value=\"" . $item[2] . $item[1] . "\" id=\"" . $id . "\" />";
							break;
							
						case "number":
							echo "<div id='number_of_slider' data-name='" . $key . "' data-loop=\"" . $item[1] . "\" " . (isset($item[2]) &&!empty($item[2]) ? "data-loopattr=\"" . $item[2] . "\"" : "") . "></div>";
							//echo "<input type=\"text\" name=\"" . $key . "\" />";
							echo "<br>";
							
							echo "<div class='hidden_form'>";
							$atts = explode(",", $item[2]);
							
							
							echo "<div class='shortcode_boxed_item' data-label='" . ucwords(str_replace("_", " ", $item[1])) . "'>";
							echo "<span class='hidden_click_event'></span>";
							echo "<table border='0' data-useloop='" . $item[1] . "'>";
							if(!empty($item[2])){
								$i = 0;
								foreach($atts as $attr){
									if($attr == "img" || $attr == "image" || $attr == "hoverimg"){
										$images = get_all_media_images();
										$input  = "<select name='" . $attr . "' class='ajax_created'>";
										$input .= "<option value=''>" . __("None", "listings") . "</option>";
										foreach($images as $image){
											$input .= "<option value='" . $image . "'>" . $image . "</option>\n";
										}
										$input .= "</select>"; 	
									} elseif($attr == "icon"){
										$input  = "<span class='button sc_icon_selector' data-code='EASTEREGG'>" . __("Icon", "listings") . ": </span>";
										
									} else {								
										$input = "<input type='text' name='" . $attr . "' class='ajax_created'>";
									}
									
									echo "<tr><td>" . ucwords(str_replace("_", " ", $attr)) . ": </td><td> " . $input . " " . ($i == 0 ? "<i class='fa fa-collapse-o shrink no_custom'></i>" : "") . "</td></tr>";
									$i++;
								}
							}
							echo ($item[1] != "brand_logo" ? "<tr><td>" . __("Content", "listings") . ": </td><td> <textarea name='content' class='ajax_created'></textarea> " . (empty($item[2]) ? "<i class='fa fa-collapse-o shrink no_custom'></i>" : "") . "</td></tr>" : "");
							echo "</table>";
							echo "</div>";
							
							echo "</div>";
							
							echo "<div class='ajax_form_slider'></div>";
							break;		
				
						case "column_content":
							$number = $item[1][0];
							$i      = 1;
							
							while($i <= $number){
								echo "<textarea name=\"column\" style=\"width: 100%;\">" . __("Content for column", "listings") . " " . $i . "</textarea><br>";
								$i++;
							}
							break;
						
						case "select":		
							$new_item = array_values($item[1]);
											
							echo "<select name='" . $key . "'" . ($key == "style" ? " data-parentattr='" . $key . "'" : "") . (isset($item[2]) && $item[2] == "multi" ? " multiple='multiple' class='multi-select" . ($key == "categories" ? " categories" : "") . "'" : "") . ">";
														
							if(is_object($new_item[0])){
								foreach($new_item as $option){
									echo "<option value='" . ($key == "categories" ? $option->name : $option->term_id) . "'>" . $option->name . "</option>";
								}
							} else {
								foreach($item[1] as $key => $option){
									echo "<option value='" . $key . "'>" . ucwords($option) . "</option>";
								}
							}
							
							echo "</select>";
							break;
						}
					break;
			}
			echo "</td></tr>";
		}
	}
	
	echo "</table>";
	
	echo "<button id=\"generateShortcode\" class=\"button btn\" style=\"bottom: 12px; position: relative;\">" . __("Generate Shortcode", "listings") . "</button>";
	
	if(in_array($_POST['shortcode'], $has_toolpop)){
		echo "<span class='generateModal button btn'>" . __("Link to modal", "listings") . "</span> <span class='generatePopover button btn'>" . __("Add a popover", "listings") . "</span>";
	}
	
	
	echo "<div id='sc_icon_selector_dialog' style='display:none;' title='" . __("Icons", "listings") . "'>";
	echo "<input type='text' class='icon_search' style='width: 98%;' placeholder='" . __("Search Icons", "listings") . "' /><br />";
	 
	$default_fontello  = get_option('default_fontello_font');
	$fontawesome_icons = get_fontawesome_icons();
	$fontello_icons    = get_fontello_icons($default_fontello);
	
	echo "<h2>" . __("Font Awesome", "listings") . "</h2>";
	foreach($fontawesome_icons as $key => $match){
		echo "<i class='" . $key . " fa'></i>";
	} 
	
	if(is_fontello_active()){	
		echo "<style type='text/css'>";
		echo '.fontello[class^="icon-"]:before, .fontello[class*="icon-"]:before, .fontello[class^="icon-"], .fontello[class*="icon-"] { font-family: ' . $default_fontello . '; }';
		echo "</style>";
		
		echo "<h2>Fontello</h2>";
		foreach($fontello_icons as $key => $match){
			echo "<i class='" . $key . " fontello'></i>";
		}
	}

}

function is_fontello_active(){ return false; }

function generate_icons(){
	$default_fontello = get_option('default_fontello_font');
	/*$icons            = ($_POST['shortcode'] == "icons" ?  get_fontawesome_icons() : get_fontello_icons($default_fontello) );
	$class            = ($_POST['shortcode'] == "icons" ? "fontawesome" : "fontello");
	
	if($class == "fontello"){
		echo "<style type='text/css'>";
		echo "i." . $class . " { font-family: " . $default_fontello . "; }";
		echo "</style>";
	}
	
	foreach($icons as $key => $match){
		echo "<i class='" . $key . " " . $class . "'></i>";
	}*/
	echo "<input type='text' class='icon_search' style='width: 98%;' placeholder='" . __("Search Icons", "listings") . "' /><br />";

	$default_fontello  = get_option('default_fontello_font');
	$fontawesome_icons = get_fontawesome_icons();
	//$fontello_icons    = get_fontello_icons($default_fontello);
	
	echo "<h2>" . __("Font Awesome", "listings") . "</h2>";
	foreach($fontawesome_icons as $key => $match){
		echo "<i class='" . $key . " fa'></i>";
	} 
	
	if(is_fontello_active()){	
		echo "<style type='text/css'>";
		echo '.fontello[class^="icon-"]:before, .fontello[class*="icon-"]:before, .fontello[class^="icon-"], .fontello[class*="icon-"] { font-family: ' . $default_fontello . '; }';
		echo "</style>";
		
		echo "<h2>Fontello</h2>";
		foreach($fontello_icons as $key => $match){
			echo "<i class='" . $key . " fontello'></i>";
		}
	}	
	die;
}
add_action("wp_ajax_generate_icons", "generate_icons");
add_action("wp_ajax_nopriv_generate_icons", "generate_icons");

function customize_icon(){ ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$( "#shortcode-modal" ).dialog("widget").animate({
		width: '500px',
		height: '450px'
	}, {
		duration: 500,
		step: function(){
			$("#shortcode-modal").dialog("option", "position", "center");
		},
		complete: function(){
			var new_height = $(".shortcode_generator").height();
			$("#shortcode-modal").height((new_height + 213)); 
		}
	});	
	
	$('.color-picker').wpColorPicker({
		change: function(event, ui) {
			$("i.preview").css( 'color', ui.color.toString());
		}	
	});
	
	$(".shortcode_slider").each( function(index, element){
		var id      = $(this).data('id');
		var minimum = $(this).data('min');
		var maximum = $(this).data('max');
		var units   = $(this).data('unit');
		var value   = $(this).data('value');
		
		$(this).slider({
			min: minimum,
			max: maximum,
			value: value,
			slide: function( event, ui ){
				$("#" + id).val(ui.value + units);
				$("i.preview").css("font-size", ui.value);
			}
		});
	});
	
	$(".insert_effect").click( function(){
		if($(".insert_effect").is(":checked")){
			$("i.preview").addClass('threed-icon');
		} else {
			$("i.preview").removeClass('threed-icon');
		}
	
	});
	
	$(".insert_spin").click( function(){
		if($(".insert_spin").is(":checked")){
			$("i.preview").addClass('fa-spin');
		} else {
			$("i.preview").removeClass('fa-spin');
		}
	
	});
	
	$(document).one("click", "#generateShortcode", function(){
		var size  = $("i.preview").css('font-size');
		var color = $("i.preview").css('color');
		var clas  = $("i.preview").attr('class').replace("preview", "");
		var icon  = $("i.preview").data('icon');
		
		if($("#insert_class").is(":checked")){
			var icon_html = icon.replace("icon-", "");
		} else {
			var icon_html = "<i class='" + clas + "' style='color: " + color + "; font-size: " + size + ";'>&nbsp;</i>";
		}
		
		$("#shortcode-modal").dialog("close");
			
		//tinyMCE.execInstanceCommand('content', "mceInsertContent", false, icon_html);
		
		tinyMCE.execCommand('mceInsertContent', false, icon_html);
		return false;
	});
});
</script>
<?php
	if(strstr($_POST['icon'], "fontello")){
		$default_fontello = get_option('default_fontello_font');
		echo "<style type='text/css'>";
		echo "i.fontello { font-family: " . $default_fontello . "; }";
		echo "</style>";
	}

	echo "<i class='" . $_POST['icon'] . " preview' data-icon=\"" . $_POST['icon'] . "\"></i><br>";
	
	echo "<table border='0'>";
	
	echo "<tr><td>" . __("Color", "listings") . ": </td><td><input type=\"text\" value=\"#000\" class=\"color-picker\" name=\"color\" /></td></tr>";
	
	echo "<tr><td>" . __("Size", "listings") . ": </td><td><input type=\"text\" name=\"size\" value=\"18px\" id=\"icon-slider\" /></td></tr>";	
	echo "<tr><td colspan='2'><div data-unit=\"px\" data-min=\"1\" data-max=\"100\" data-id=\"icon-slider\" data-value=\"18\" class=\"shortcode_slider\" style=\"width: 280px\"></div></td></tr>";
	
	echo "<tr><td colspan='2'><input type='checkbox' class='insert_effect' id='insert_effect'> <label for='insert_effect'>" . __("Add 3-D effect", "listings") . "</label></td></tr>";
	echo "<tr><td colspan='2'><input type='checkbox' class='insert_spin' id='insert_spin'> <label for='insert_spin'>" . __("Add spin effect", "listings") . "</label></td></tr>";
	
	echo "<tr><td colspan='2'><input type='checkbox' class='insert_class' id='insert_class'> <label for='insert_class'>" . __("Insert icon as code for shortcode", "listings") . "</label></td></tr>";
	
	echo "</table>";
	
	echo "<button id=\"generateShortcode\" class=\"button btn\">" . __("Generate Shortcode", "listings"). "</button>";
	
	die;
}
add_action("wp_ajax_customize_icon", "customize_icon");
add_action("wp_ajax_nopriv_customize_icon", "customize_icon");



include("vc.php");

?>