<?php

add_action( 'vc_after_init', 'automotive_vc_shortcodes' );
//add_action( 'wp_loaded', 'automotive_vc_shortcodes' );

function automotive_vc_shortcodes() {
   // quote
   vc_map( array(
      "name"              => __("Quote", "listings"),
      "base"              => "quote",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Text", "listings"),
            "param_name"  => "content",
            "value"       => __("Quote Text", "listings"),
            "description" => ""
         ),
         array(
            "type"        => "colorpicker",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Color", "listings"),
            "param_name"  => "color",
            "value"       => __("#c7081b", "listings"),
            "description" => __("The color to the left of the quote", "listings")
         )
      )
   ) );

   // setup inventory

   $inventory_vc = array(
      "name"              => __("Inventory", "listings"),
      "base"              => "inventory_display",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Layout", "listings"),
            "param_name"  => "layout",
            "value"       => array("Wide Fullwidth" => "wide_fullwidth", "Wide Sidebar Left" => "wide_left", "Wide Sidebar Right" => "wide_right", "Boxed Fullwidth" => "boxed_fullwidth", "Boxed Sidebar Left" => "boxed_left", "Boxed Sidebar Right" => "boxed_right"),
            "description" => __("Style of dropdown", "listings")
         ),
         /*array(
            "type"        => "dropdown",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Text", "listings"),
            "param_name"  => "search_box",
            "value"       => __("Quote Text", "listings"),
            "description" => ""
         ),*/
      )
   );

   $categories = get_listing_categories();

   if(!empty($categories)){
      foreach($categories as $category){

         $options = array(__("None", "listings") => "");

         if(!empty($category['terms'])){
            foreach($category['terms'] as $key => $term){
               $options[(isset($category['compare_value']) && $category['compare_value'] != "=" ? html_entity_decode($category['compare_value']) . " " : "") . $term] = $term;
            }
         }

         $safe = str_replace(" ", "_", strtolower($category['singular']));

         // $inventory_vc['params'][] = array(
         //    "type"        => "checkbox",
         //    "class"       => "",
         //    "heading"     => $category['plural'],
         //    "param_name"  => ($safe == "year" ? "yr" : $safe),
         //    "value"       => $options,
         //    "description" => __("Only select a single value", "listings")
         // );
         $inventory_vc['params'][] = array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => $category['plural'],
            "param_name"  => ($safe == "year" ? "yr" : $safe),
            "value"       => $options,
            // "description" => __("Only select a single value", "listings")
         );

         // $inventory_vc['params'][] = array(
         //    "type"        => "checkbox",
         //    "class"       => "",
         //    "heading"     => ,
         //    "param_name"  => ($safe == "year" ? "yr" : $safe),
         //    "value"       => $options,
         // );
      }
   }

   // inventory
   vc_map( $inventory_vc );   

   // lists
   vc_map( array(
      "name"              => __("List", "listings"),
      "base"              => "list",
      "as_parent"         => array("only" => "list_item"),
      "content_element"   => true,
      "show_settings_on_create" => false,
      "params"            => array(
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Style", "listings"),
            "param_name"  => "style",
            "value"       => array("Arrows" => "arrows", "Checkboxes" => "checkboxes"),
            "description" => __("Style of dropdown", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      ),
      "js_view"           => "VcColumnView",
      "category"          => __('Automotive Shortcodes', "listings"),
   ) );
   vc_map( array(
      "name"              => __("List Item", "listings"),
      "base"              => "list_item",
      "category"          => __('Automotive Shortcodes', "listings"),
      "as_child"          => array("only" => "list"),
      "content_element"   => true,
      "as_parent"         => array(),
      "params"            => array(
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Icon", "listings"),
            "param_name"  => "icon",
            "value"       => "",
            "description" => __("Icon used for the list items. <a href='http://fontawesome.io/icons/' target='_blank'>List of Icons</a>", "listings")
         ),
         array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Text", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => __("List item content.", "listings")
         )
      )
   ) );

   // parallax section
   vc_map( array(
      "name"              => __("Parallax", "listings"),
      "base"              => "parallax_section",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "content_element"   => true,
      "js_view"           => "VcColumnView",
      "as_parent"         => array('only' => 'vc_row'),
      "params"            => array(
         array(
            "type"        => "textarea_html",
            "class"       => "",
            "heading"     => __("Content", "listings"),
            "param_name"  => "content",
            "value"       => __("Parallax content", "listings"),
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Velocity", "listings"),
            "param_name"  => "velocity",
            "value"       => array("-.3", "-.2", "-.1", ".1", ".2", ".3"),
            "description" => __("How fast do you want the parallax to go (and which direction!)", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Offset", "listings"),
            "param_name"  => "offset",
            "value"       => __("0", "listings"),
            "description" => __("Amount of offset (px)", "listings")
         ),
         array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Image", "listings"),
            "param_name"  => "image",
            "value"       => "",
            "description" => __("Image used for parallax scrolling", "listings")
         ),
         array(
            "type"        => "colorpicker",
            "class"       => "",
            "heading"     => __("Overlay Color", "listings"),
            "param_name"  => "overlay_color",
            "value"       => "",
            "description" => __("Select an overlay color", "listings")
         ),
         array(
            "type"        => "colorpicker",
            "class"       => "",
            "heading"     => __("Text Color", "listings"),
            "param_name"  => "text_color",
            "value"       => "",
            "description" => __("Select text color to use inside the parallax content", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      ),

   ) );
   
   // animated numbers
   vc_map( array(
      "name"              => __("Animated Numbers", "listings"),
      "base"              => "animated_numbers",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Icon", "listings"),
            "param_name"  => "icon",
            "value"       => "",
            "description" => __("Icon to display above the number. <a href='http://fontawesome.io/icons/' target='_blank'>List of Icons</a>", "listings")
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Number", "listings"),
            "param_name"  => "number",
            "value"       => "",
            "description" => __("The number to animate to.", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Before Number", "listings"),
            "param_name"  => "before_number",
            "value"       => "",
            "description" => __("Display text before the number ($, €, #)", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("After Number", "listings"),
            "param_name"  => "after_number",
            "value"       => "",
            "description" => __("Display text after the number (%, !)", "listings")
         ),
         array(
            "type"        => "dropdown",
            "heading"     => __("Align", "listings"),
            "param_name"  => "alignment",
            "description" => __("Align the content", "listings"),
            "value"       => array(
               __("Left", "listings")    => "left",
               __("Center", "listings") => "center",
               __("Right", "listings")  => "right"
            )
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   // featured icon box    
   vc_map( array(
      "name"              => __("Featured Icon Box", "listings"),
      "base"              => "featured_icon_box",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textarea_html",
            "class"       => "",
            "heading"     => __("Content", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => ""
         ),         
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Title of the featured box", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Icon", "listings"),
            "param_name"  => "icon",
            "value"       => "",
            "description" => __("Icon of the featured box (fa fa-users) <a href='http://fontawesome.io/icons/' target='_blank'>List of Icons</a>.", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   // progress bar
   vc_map( array(
      "name"              => __("Progress Bar", "listings"),
      "base"              => "progress_bar",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Content", "listings"),
            "param_name"  => "content",
            "value"       => __("Text", "listings"),
            "description" => __("Text to be displayed inside the progress bar.", "listings")
         ),
         array(
            "type"        => "colorpicker",
            "class"       => "",
            "heading"     => __("Color", "listings"),
            "param_name"  => "color",
            "value"       => __("#c7081b", "listings"),
            "description" => __("Color of progress bar", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Filled", "listings"),
            "param_name"  => "filled",
            "value"       => __("90%", "listings"),
            "description" => __("The percentage of the progress bar filled with color.", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   
   // testimonials
   vc_map( array(
      "name"              => __("Testimonials", "listings"),
      "base"              => "testimonials",
      "as_parent"         => array('only' => 'testimonial_quote'),
      "content_element"   => true,
      "show_settings_on_create" => false,
      "js_view"           => 'VcColumnView',
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Text", "listings"),
            "param_name"  => "slide",
            "value"       => array("horizontal", "vertical"),
            "description" => __("This will control which way the testiomnials slide.", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Speed", "listings"),
            "param_name"  => "speed",
            "value"       => "500",
            "description" => __("This controls the speed at which the testimonials slide.", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   vc_map( array(
      "name"              => __("Testimonial Quote", "listings"),
      "base"              => "testimonial_quote",
      "content_element"   => true,
      "as_child"          => array('only' => 'testimonials'),
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Name", "listings"),
            "param_name"  => "name",
            "value"       => "",
            "description" => __("Name of person giving the testimonial", "listings")
         ),
         array(
            "type"        => "textarea_html",
            "class"       => "",
            "heading"     => __("Quote", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => __("Testimonial quote", "listings")
         )
      )
   ) );
   
   // gs
   vc_map( array(
      "name"              => __("Recent Posts Scroller", "listings"),
      "base"              => "recent_posts_scroller",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Posts to show", "listings"),
            "param_name"  => "number",
            "value"       => __("2", "listings"),
            "description" => __("Number of posts to display at a time", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Speed of scroller", "listings"),
            "param_name"  => "speed",
            "value"       => __("500", "listings"),
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Number of posts", "listings"),
            "param_name"  => "posts",
            "value"       => __("3", "listings"),
            "description" => __("Number of posts to scroll through", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   
   // FAQs
   vc_map( array(
      "name"              => __("FAQ", "listings"),
      "base"              => "faq",
      "as_parent"         => array('only' => 'toggle'),
      "content_element"   => true,
      "show_settings_on_create" => false,
      "js_view"           => 'VcColumnView',
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Categories", "listings"),
            "param_name"  => "categories",
            "value"       => "category1, category2, etc",
            "description" => __("Comma seperated list of categories.", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Sort Text", "listings"),
            "param_name"  => "sort_text",
            "value"       => "Sort FAQ by:",
            "description" => __("This text is displayed beside the categories.", "listings")
         ),
      )
   ) );
   vc_map( array(
      "name"              => __("FAQ Item", "listings"),
      "base"              => "toggle",
      "content_element"   => true,
      "as_child"          => array('only' => 'faq'),
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Title of FAQ item", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Categories", "listings"),
            "param_name"  => "categories",
            "value"       => __("category1, category2, etc", "listings"),
            "description" => __("Comma seperated list of categories item is in.", "listings")
         ),
         array(
            "type"        => "textarea_html",
            "class"       => "",
            "heading"     => __("FAQ Content", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "dropdown",
            "heading"     => __("State of item", "listings"),
            "param_name"  => "state",
            "value"       => array("Closed" => "collapsed", "Open" => "in"),
            "description" => __("Choose whether the item is open or closed", "listings")
         )
      )
   ) );

   vc_map( array(
      "name"              => __("Staff person", "listings"),
      "base"              => "person",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Name", "listings"),
            "param_name"  => "name",
            "value"       => "",
            "description" => __("Name of person", "listings")
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Position", "listings"),
            "param_name"  => "position",
            "value"       => "",
            "description" => __("Position of person", "", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Phone", "listings"),
            "param_name"  => "phone",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Cell Phone", "listings"),
            "param_name"  => "cell_phone",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Email", "listings"),
            "param_name"  => "email",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Image", "listings"),
            "param_name"  => "img",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Larger Image", "listings"),
            "param_name"  => "hoverimg",
            "value"       => "",
            "description" => ""
         ),
         array( 
            "type"        => "textarea_html",
            "class"       => "",
            "heading"     => __("Description", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => __("Description of staff member", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Facebook", "listings"),
            "param_name"  => "facebook",
            "value"       => "",
            "description" => __("URL to Facebook profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Twitter", "listings"),
            "param_name"  => "twitter",
            "value"       => "",
            "description" => __("URL to Twitter profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("YouTube", "listings"),
            "param_name"  => "youtube",
            "value"       => "",
            "description" => __("URL to YouTube profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Vimeo", "listings"),
            "param_name"  => "vimeo",
            "value"       => "",
            "description" => __("URL to Vimeo profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("LinkedIn", "listings"),
            "param_name"  => "linkedin",
            "value"       => "",
            "description" => __("URL to LinkedIn profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("RSS", "listings"),
            "param_name"  => "rss",
            "value"       => "",
            "description" => __("URL to RSS profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Flickr", "listings"),
            "param_name"  => "flickr",
            "value"       => "",
            "description" => __("URL to Flickr profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Skype", "listings"),
            "param_name"  => "skype",
            "value"       => "",
            "description" => __("URL to Skype profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Google", "listings"),
            "param_name"  => "google",
            "value"       => "",
            "description" => __("URL to Google profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Pinterest", "listings"),
            "param_name"  => "pinterest",
            "value"       => "",
            "description" => __("URL to Pinterest profile", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   
   // featured panel
   vc_map( array(
      "name"              => __("Featured Panel", "listings"),
      "base"              => "featured_panel",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Title of panel", "listings")
         ),
         array(
            "type"        => "attach_image",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Image", "listings"),
            "param_name"  => "icon",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "attach_image",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Hover Image", "listings"),
            "param_name"  => "hover_icon",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "vc_link",
            "holder"      => "",
            "class"       => "",
            "heading"     => __("Image Link", "listings"),
            "param_name"  => "image_link",
            "value"       => "",
            "description" => __("Link the image", "listings")
         ),
         array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Content", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )        
      )
   ) );

   // detailed panel
   vc_map( array(
      "name"              => __("Detailed Panel", "listings"),
      "base"              => "detailed_panel",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Title of service", "listings")
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Icon", "listings"),
            "param_name"  => "icon",
            "value"       => "",
            "description" => __("Icon to show beside the title. <a href='http://fontawesome.io/icons/' target='_blank'>List of Icons</a>", "listings")
         ),
         array(
            "type"        => "attach_image",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Image", "listings"),
            "param_name"  => "image",
            "value"       => "",
            "description" => __("This will overwrite the icon setting.", "listings")
         ),
         array(
            "type"        => "vc_link",
            "class"       => "",
            "heading"     => __("Link", "listings"),
            "param_name"  => "link",
            "value"       => "",
            "description" => __("Link for the title and icon", "listings")
         ),
         array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Content", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );  

   // Featured Brand
   vc_map( array(
      "name"              => __("Featured Brands", "listings"),
      "base"              => "featured_brands",
      "category"          => __('Automotive Shortcodes', "listings"),
      "as_parent"         => array('only' => 'brand_logo'),
      "content_element"   => true,
      "show_settings_on_create" => false,
      "js_view"           => 'VcColumnView',
      "params"            => array()
   ) );
   vc_map( array(
      "name"              => __("Brand Item", "listings"),
      "base"              => "brand_logo",
      "content_element"   => true,
      "as_child"          => array('only' => 'featured_brands'),
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "attach_image",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Image", "listings"),
            "param_name"  => "img",
            "value"       => "",
            "description" => __("The image shown on load", "listings")
         ),
         array(
            "type"        => "attach_image",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Hover Image", "listings"),
            "param_name"  => "hoverimg",
            "value"       => "",
            "description" => __("Image shown when hovering over the brand", "listings")
         ),
         array(
            "type"        => "vc_link",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Link", "listings"),
            "param_name"  => "link",
            "value"       => "",
            "description" => __("Link brand to url", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   
   $portfolio_terms = get_terms('portfolio_in');
   $portfolios      = array();

    if(!empty($portfolio_terms)){
        foreach ($portfolio_terms as $key => $value) {
            $portfolios[$value->name] = $value->term_id;
        }
    }


   $project_terms = get_terms('project-type');
   $projects      = array();

    if(!empty($project_terms)){
       foreach ($project_terms as $key => $value) {
          $projects[$value->name . "<br>"] = $value->name;
       }
    }

   // portfolio
   vc_map( array(
      "name"              => __("Portfolio", "listings"),
      "base"              => "portfolio",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "checkbox",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Categories", "listings"),
            "param_name"  => "categories",
            "value"       => $projects,
            "description" => __("Display these sortable categories", "listings")
         ),
         array(
            "type"        => "dropdown",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Type", "listings"),
            "param_name"  => "type",
            "value"       => array(__("Details", "listings") => "details", __("Classic", "listings") => "classic"),
            "description" => ""
         ),
         array(
            "type"        => "dropdown",
            "holder"      => "",
            "class"       => "",
            "heading"     => __("All Category", "listings"),
            "param_name"  => "all_category",
            "value"       => array(__("Yes", "listings") => "yes", __("No", "listings") => "no"),
            "description" => "Display the all category"
         ),
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Portfolio", "listings"),
            "param_name"  => "portfolio",
            "value"       => $portfolios,
            "description" => __("Which portfolio to display", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Sort Text", "listings"),
            "param_name"  => "sort_text",
            "value"       => "",
            "description" => __("Change the text beside the categories", "listings")
         ),
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Columns", "listings"),
            "param_name"  => "columns",
            "value"       => array(1, 2, 3, 4),
            "description" => __("Change the text beside the categories", "listings")
         )
      )
   ) );

   // alert
   vc_map( array(
      "name"              => __("Alert", "listings"),
      "base"              => "alert",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Type", "listings"),
            "param_name"  => "type",
            "value"       => array("Danger" => 0, "Success" => 1, "Info" => 2, "Warning" => 3),
            "description" => __("Type of alert", "listings")
         ),
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Close button", "listings"),
            "param_name"  => "close",
            "value"       => array("No", "Yes"),
            "description" => __("Display a close button", "listings")
         ),
         array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Close button", "listings"),
            "param_name"  => "content",
            "value"       => "Alert content",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   
   // pricing table 
   vc_map( array(
      "name"              => __("Pricing Table", "listings"),
      "base"              => "pricing_table",
      "as_parent"         => array('only' => 'pricing_option'),
      "category"          => __('Automotive Shortcodes', "listings"),
      "content_element"   => true,
      "show_settings_on_create" => true,
      "js_view"           => 'VcColumnView',
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("The title of the pricing option", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Price", "listings"),
            "param_name"  => "price",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Often", "listings"),
            "param_name"  => "often",
            "value"       => __("mo", "listings"),
            "description" => __("How often the payment is made", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Button Text", "listings"),
            "param_name"  => "button",
            "value"       => __("Sign Up Now", "listings"),
            "description" => ""
         ),
         array(
            "type"        => "vc_link",
            "class"       => "",
            "heading"     => __("Link", "listings"),
            "param_name"  => "link",
            "value"       => "",
            "description" => __("Link brand to url", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   vc_map( array(
      "name"              => __("Pricing Option", "listings"),
      "base"              => "pricing_option",
      "content_element"   => true,
      "as_child"          => array('only' => 'pricing_table'),
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textarea_html",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Option Text", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => __("An option this pricing table includes", "listings")
         ),
      )
   ) );

   $categories = get_listing_categories();

   $use_categories = $min_categories = array();
   if(!empty($categories)){
      foreach($categories as $category){
         $use_categories[$category['singular'] . "<br>"] = $category['singular'];
         $min_categories[$category['singular'] . "<br>"] = $category['singular'];
      }
   }
   // add search box
   $use_categories["Search Box"] = "Search";
   
   // search inventory box
   vc_map( array(
      "name"              => __("Search Inventory Box", "listings"),
      "base"              => "search_inventory_box",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "checkbox",
            "class"       => "",
            "heading"     => __("Column 1", "listings"),
            "param_name"  => "column_1",
            "value"       => $use_categories,
            "description" => __("Dropdowns that will be shown in the first column", "listings")
         ),
         array(
            "type"        => "checkbox",
            "class"       => "",
            "heading"     => __("Column 2", "listings"),
            "param_name"  => "column_2",
            "value"       => $use_categories,
            "description" => __("Dropdowns that will be shown in the second column", "listings")
         ),
         array(
            "type"        => "checkbox",
            "class"       => "",
            "heading"     => __("Min/Max Values", "listings"),
            "param_name"  => "min_max",
            "value"       => $min_categories,
            "description" => __("Only choose values you checked in the previous options and only for categories that use <strong>numbers</strong> as values", "listings")
         ),
         array(
            "type"        => "vc_link",
            "class"       => "",
            "heading"     => __("Form action", "listings"),
            "param_name"  => "page_id",
            "description" => __("Choose the page you want to submit the form to (must be using listing template)", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Button text", "listings"),
            "param_name"  => "button_text",
            "description" => __("Adjust the button text to submit the form.", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   // vehicle scroller
   vc_map( array(
      "name"              => __("Vehicle Scroller", "listings"),
      "base"              => "vehicle_scroller",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Title of vehicle scroller.", "listings")
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Description", "listings"),
            "param_name"  => "description",
            "value"       => "",
            "description" => __("Small description of vehicles being displayed.", "listings")
         ),
         array(
            "type"        => "dropdown",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Sort by", "listings"),
            "param_name"  => "sort",
            "value"       => array("Newest" => "newest", "Oldest" => "oldest"),
            "description" => __("Sort the vehicles.", "listings")
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Vehicles to show", "listings"),
            "param_name"  => "listings",
            "value"       => "",
            "description" => __("Comma seperated list of vehicle ID's to display.", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Vehicle limit", "listings"),
            "param_name"  => "limit",
            "value"       => "",
            "description" => __("The number of vehicles to show. (-1 to display all)", "listings")
         ),
         array(
            "type"        => "checkbox",
            "class"       => "",
            "heading"     => __("Automatic Scrolling", "listings"),
            "param_name"  => "autoscroll",
            "value"       => array("Enable" => "true"),
            "description" => __("Enable automatic scrolling on the slider.", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   // button 
   vc_map( array(
      "name"              => __("Button", "listings"),
      "base"              => "button",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Button text", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "dropdown",
            "class"       => "",
            "heading"     => __("Button size", "listings"),
            "param_name"  => "size",
            "value"       => array("Extra Small" => "xs", "Small" => "sm", "Medium" => "md", "Large" => "lg", "Extra Large" => "xl"),
            "description" => ""
         ),
         array(
            "type"        => "vc_link",
            "class"       => "",
            "heading"     => __("Link", "listings"),
            "param_name"  => "href",
            "value"       => "",
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   // tabs
   vc_map( array(
      "name"              => __("Tabs", "listings"),
      "base"              => "tabs",
      "as_parent"         => array('only' => 'tab'),
      "content_element"   => true,
      "show_settings_on_create" => false,
      "js_view"           => 'VcColumnView',
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   vc_map( array(
      "name"              => __("Single Tab", "listings"),
      "base"              => "tab",
      "content_element"   => true,
      "as_child"          => array('only' => 'tabs'),
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Tab title", "listings")
         ),
         array(
            "type"        => "textarea_html",
            "class"       => "",
            "heading"     => __("Content", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => __("Tab Content", "listings")
         ),
      )
   ) );

   // video
   vc_map( array(
      "name"              => __("YouTube/Vimeo video", "listings"),
      "base"              => "auto_video",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("URL to video", "listings"),
            "param_name"  => "url",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Width", "listings"),
            "param_name"  => "width",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Height", "listings"),
            "param_name"  => "height",
            "value"       => "",
            "description" => ""
         )
      )
   ) );

   // clear fix
   vc_map( array(
      "name"              => __("Clear Fix", "listings"),
      "base"              => "clearfix",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array()
   ) );

   // line break
   vc_map( array(
      "name"              => __("Line Break", "listings"),
      "base"              => "br",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array()
   ) );

   // heading
   vc_map( array(
      "name"              => __("Heading", "listings"),
      "base"              => "heading",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "dropdown",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Heading", "listings"),
            "param_name"  => "heading",
            "value"       => array("Heading 1" => "h1", "Heading 2" => "h2", "Heading 3" => "h3", "Heading 4" => "h4", "Heading 5" => "h5", "Heading 6" => "h6"),
            "description" => __("Size of heading", "listings")
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Text", "listings"),
            "param_name"  => "content",
            "value"       => "",
            "description" => __("Heading Text", "listings")
         )
      )
   ) );

   // car comparison
   vc_map( array(
      "name"              => __("Car Comparison", "listings"),
      "base"              => "car_comparison",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Car ID's", "listings"),
            "param_name"  => "car_ids",
            "value"       => "",
            "description" => __("Comma seperated ID's of vehicles, if no car ID's are set it will automatically grab cars checked by user in inventory.", "listings")
         )
      )
   ) );

   vc_map( array(
      "name"              => __("Contact Form", "listings"),
      "base"              => "auto_contact_form",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      'front_enqueue_js'  => JS_DIR .'/contact_form.js',
      "params"            => array(
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Name Placeholder", "automotive", "listings"),
            "param_name"  => "name",
            "description" => __("This is the text 'behind' the name textfield.", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Email Placeholder", "automotive", "listings"),
            "param_name"  => "email",
            "description" => __("This is the text 'behind' the email textfield.", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Message Placeholder", "automotive", "listings"),
            "param_name"  => "message",
            "description" => __("This is the text 'behind' the message textbox.", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Submit Button", "automotive", "listings"),
            "param_name"  => "button",
            "description" => __("This is the text used on the submit button.", "listings")
         )
      )
      
   ) );

   vc_map( array(
      "name"              => __("Hours Table", "listings"),
      "base"              => "hours_table",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Title of hours table", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Monday", "listings"),
            "param_name"  => "mon",
            "value"       => "",
            "description" => __("Monday hours", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Tuesday", "listings"),
            "param_name"  => "tue",
            "value"       => "",
            "description" => __("Tuesday hours", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Wednesday", "listings"),
            "param_name"  => "wed",
            "value"       => "",
            "description" => __("Wednesday hours", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Thursday", "listings"),
            "param_name"  => "thu",
            "value"       => "",
            "description" => __("Thursday hours", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Friday", "listings"),
            "param_name"  => "fri",
            "value"       => "",
            "description" => __("Friday hours", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Saturday", "listings"),
            "param_name"  => "sat",
            "value"       => "",
            "description" => __("Saturday hours", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Sunday", "listings"),
            "param_name"  => "sun",
            "value"       => "",
            "description" => __("Sunday hours", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );
   
   vc_map( array(
      "name"              => __("Contact Information", "listings"),
      "base"              => "auto_contact_information",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Company Name", "listings"),
            "param_name"  => "company",
            "value"       => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Address", "listings"),
            "param_name"  => "address",
            "value"       => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Phone", "listings"),
            "param_name"  => "phone",
            "value"       => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Email", "listings"),
            "param_name"  => "email",
            "value"       => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Website", "listings"),
            "param_name"  => "web",
            "value"       => ""
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   vc_map( array(
      "name"              => __("Google Map", "listings"),
      "base"              => "auto_google_map",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Latitude", "listings"),
            "param_name"  => "latitude",
            "value"       => "",
            "description" => __("Latitude of google map", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Longitude", "listings"),
            "param_name"  => "longitude",
            "value"       => "",
            "description" => __("Longitude of google map", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Zoom", "listings"),
            "param_name"  => "zoom",
            "value"       => "",
            "description" => __("Zoom of google map", "listings")
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Height", "listings"),
            "param_name"  => "height",
            "value"       => __("390", "listings"),
            "description" => __("Height of google map", "listings")
         ),
         array(
            "type"        => "dropdown",
            "heading"     => __("Scrolling to zoom", "listings"),
            "param_name"  => "scrolling",
            "value"       => array("On" => "true", "Off" => "false"),
            "description" => __("Turn off the scrolling to zoom function on google map, useful for fullscreen maps", "listings")
         ),
         array(
            "type"        => "textarea_raw_html",
            "heading"     => __("Style", "listings"),
            "param_name"  => "map_style",
            "description" => __("Style of google map, styles availiable at: <a href='http://snazzymaps.com/' target='_blank'>http://snazzymaps.com/</a>", "listings")
         ),
         array(
            "type"        => "checkbox",
            "heading"     => __("Parallax Disabled", "listings"),
            "param_name"  => "parallax_disabled",
            "value"       => array("Disabled" => "disabled"),
            "description" => __("Check this option to disable the parallax", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   vc_map( array(
      "name"              => __("Flipping Card", "listings"),
      "base"              => "flipping_card",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Image", "listings"),
            "param_name"  => "image",
            "value"       => "",
            "description" => __("This image will be shown on the front of the flipping card", "listings")
         ),
         array(
            "type"        => "attach_image",
            "class"       => "",
            "heading"     => __("Larger image", "listings"),
            "param_name"  => "larger_img",
            "value"       => "",
            "description" => __("This image will open in a fancybox", "listings")
         ),
         array(
            "type"        => "textfield",
            "holder"      => "div",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => __("Title of flipped card", "listings")
         ),
         array(
            "type"        => "vc_link",
            "class"       => "",
            "heading"     => __("Link", "listings"),
            "param_name"  => "link",
            "value"       => "",
            "description" => __("Link of link button on flipped side", "listings")
         ),
         array(
            "type"        => "vc_link",
            "class"       => "",
            "heading"     => __("Card link", "listings"),
            "param_name"  => "card_link",
            "value"       => "",
            "description" => __("Link the entire flipping card", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   vc_map( array(
      "name"              => __("Icon & Title", "listings"),
      "base"              => "icon_title",
      "class"             => "",
      "category"          => __('Automotive Shortcodes', "listings"),
      "params"            => array(
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Title", "listings"),
            "param_name"  => "title",
            "value"       => "",
            "description" => ""
         ),
         array(
            "type"        => "textfield",
            "class"       => "",
            "heading"     => __("Icon", "listings"),
            "param_name"  => "icon",
            "value"       => "",
            "description" => __("e.g. fa fa-dashboard <a href='http://fontawesome.io/icons/' target='_blank'>List of Icons</a>", "listings")
         ),
         array(
            "type"        => "vc_link",
            "class"       => "",
            "heading"     => __("Link", "listings"),
            "param_name"  => "link",
            "value"       => "",
            "description" => __("Link of the icon", "listings")
         ),
         array(
            "type"        => "textfield",
            "heading"     => __("Extra class name", "listings"),
            "param_name"  => "extra_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "listings")
         )
      )
   ) );

   if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
       class WPBakeryShortCode_List extends WPBakeryShortCodesContainer {}
       class WPBakeryShortCode_Faq extends WPBakeryShortCodesContainer {}
       class WPBakeryShortCode_Testimonials extends WPBakeryShortCodesContainer {}
       class WPBakeryShortCode_Staff_List extends WPBakeryShortCodesContainer {}
       class WPBakeryShortCode_Featured_Brands extends WPBakeryShortCodesContainer {}
       class WPBakeryShortCode_Pricing_Table extends WPBakeryShortCodesContainer {}
       class WPBakeryShortCode_Tabs extends WPBakeryShortCodesContainer {}
       class WPBakeryShortCode_Parallax_Section extends WPBakeryShortCodesContainer {}
   }
   if ( class_exists( 'WPBakeryShortCode' ) ) {
       class WPBakeryShortCode_List_Item extends WPBakeryShortCode {}
       class WPBakeryShortCode_Toggle_Item extends WPBakeryShortCode {}
       class WPBakeryShortCode_Testimonial_Quote extends WPBakeryShortCode {}
       class WPBakeryShortCode_Person extends WPBakeryShortCode {}
       class WPBakeryShortCode_Brand_Logo extends WPBakeryShortCode {}
       class WPBakeryShortCode_Pricing_Option extends WPBakeryShortCode {}
       class WPBakeryShortCode_Single_Tab extends WPBakeryShortCode {}
   }
   
   $attributes = array(
       'type'        => 'textfield',
       'heading'     => "Row Height",
       'param_name'  => 'height',
       'description' => __( "Add a row height, useful for fullwidth elements.", "listings" )
   );

   vc_add_param( 'vc_row', $attributes );

   vc_map_update( 'vc_row', array(
       'html_template' => LISTING_HOME . 'vc_row.php'
      ) 
   );
}


?>