<?php

// translation my (friend||pengyou||vriend||ven||ami||freund||dost||amico||jingu||prijatel||amigo||arkadas) 
load_theme_textdomain('automotive', get_template_directory() . '/languages');

// in case someone wants the versions added
if(!function_exists("automotive_remove_script_version")){
    function automotive_remove_script_version( $src ){
        return remove_query_arg( 'ver', $src );
    }
    add_filter( 'script_loader_src', 'automotive_remove_script_version', 15, 1 );
    add_filter( 'style_loader_src', 'automotive_remove_script_version', 15, 1 );
}

if(!function_exists('auto_theme_register_custom_extension_loader')) :
    function auto_theme_register_custom_extension_loader($ReduxFramework) {
        $path = dirname( __FILE__ ) . '/ReduxFramework/extensions/';

        if(is_dir($path)){
            $folders = scandir( $path, 1 );
            if(!empty($folders)){
                foreach ( $folders as $folder ) {
                    if ( $folder === '.' or $folder === '..' or ! is_dir( $path . $folder ) ) {
                        continue;
                    }
                    $extension_class = 'ReduxFramework_Extension_' . $folder;
                    if ( ! class_exists( $extension_class ) ) {
                        // In case you wanted override your override, hah.
                        $class_file = $path . $folder . '/extension_' . $folder . '.php';
                        $class_file = apply_filters( 'redux/extension/' . $ReduxFramework->args['opt_name'] . '/' . $folder, $class_file );
                        if ( $class_file ) {
                            require_once( $class_file );
                        }
                    }
                    if ( ! isset( $ReduxFramework->extensions[ $folder ] ) ) {
                        $ReduxFramework->extensions[ $folder ] = new $extension_class( $ReduxFramework );
                    }
                }
            }
        }
    }
    add_action("redux/extensions/automotive_wp/before", 'auto_theme_register_custom_extension_loader', 0);
endif;

if ( !class_exists( 'ReduxFramework' ) && file_exists( get_template_directory() . '/ReduxFramework/ReduxCore/framework.php' ) ) {
    require_once( get_template_directory() . '/ReduxFramework/ReduxCore/framework.php' );
}
if ( !isset( $redux_demo ) && file_exists( get_template_directory() . '/ReduxFramework/options/options.php' ) ) {
    require_once( get_template_directory() . '/ReduxFramework/options/options.php' );
}

$awp_options = get_option("automotive_wp");

add_filter('widget_text', 'do_shortcode'); 

if (!isset($content_width)) {
    $content_width = 1170;
}

if (function_exists('add_theme_support')) {
    // Add Menu Support
    add_theme_support( 'menus' );

    // Add Thumbnail Theme Support
    add_theme_support( 'post-thumbnails' );

    add_image_size( 'large_featured_image', 1170, 250, true );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'woocommerce' );
}

// disable visual composer nagging
function disable_vc_notifications() {
    vc_set_as_theme();
}
add_action( 'vc_before_init', 'disable_vc_notifications' );

// social icons 
$social_icons = $social_options = array("facebook"  => "Facebook", 
                                        "twitter"   => "Twitter", 
                                        "youtube"   => "Youtube", 
                                        "vimeo"     => "Vimeo", 
                                        "linkedin"  => "Linkedin", 
                                        "rss"       => "RSS", 
                                        "flickr"    => "Flickr", 
                                        "skype"     => "Skype", 
                                        "google"    => "Google", 
                                        "pinterest" => "Pinterest",
                                        "instagram" => "Instagram",
                                        "yelp"      => "Yelp"
                                );

//********************************************
//	Include Files
//***********************************************************
require_once(get_template_directory() . "/meta_boxes.php");
require_once(get_template_directory() . "/save.php");
require_once(get_template_directory() . "/ajax-functions.php");

require_once(get_template_directory() . "/classes/class-tgm-plugin-activation.php");
require_once(get_template_directory() . "/included_plugins.php");
require_once(get_template_directory() . "/update-notifier.php");

function automotive_theme_editor_styles() {
    add_editor_style( get_template_directory() . '/css/style.css' );
    add_editor_style( get_template_directory() . '/css/font-awesome.min.css' );
}
add_action( 'init', 'automotive_theme_editor_styles' );

//********************************************
//  Modify Main Query
//***********************************************************
function auto_change_filter($query) {
    if(!is_admin() && $query->is_main_query()){
        if($query->is_search || $query->is_author){
            $post_types = array('post', 'chip-tunning-detay');

            if(function_exists("is_woocommerce")){
                $post_types[] = "product";
            }

            $query->set('post_type', $post_types);
        }
    }
}

add_action('pre_get_posts','auto_change_filter');

//********************************************
//  Functions
//***********************************************************

if(!function_exists("get_current_id")){
    function get_current_id(){
        if(function_exists("is_shop") && is_shop()){
            return get_option("woocommerce_shop_page_id");
        } else {
            return get_queried_object_id();
        }
    }
}

function suppress_filters_all($query) {
    $query->set( 'suppress_filters', false );
    return $query;
}
add_filter( 'pre_get_posts', 'suppress_filters_all' );

if(!function_exists("automotive_styles")){
    function automotive_styles() {
        if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
            global $awp_options;

    		$css_dir   = get_template_directory_uri() . "/css/";		
    		$css_files = array("bootstrap.min", "font-awesome.min", "flexslider", "jquery.fancybox", "jquery.selectbox", "style", "ts", "mobile", "settings", "wp", "social-likes");
    		
            if(isset($awp_options['responsiveness']) && $awp_options['responsiveness'] == 0){
                $css_files[] = "no_responsive";
            }

    		foreach($css_files as $file){		
    			$label = str_replace(".", "", $file);
    		
    			wp_register_style($label, $css_dir . $file . '.css', array(), '1.1', 'all');
    			wp_enqueue_style($label);
    		}

            
            
            // child theme
            if(is_child_theme()){
                wp_enqueue_style( "child-style", get_stylesheet_uri() );
            }

            // rtl support
            if( is_rtl() ){
                wp_register_style("rtl_style", $css_dir . 'rtl.css', array(), '1.0', 'all');
                wp_enqueue_style("rtl_style");
            }

            // custom styling
            $custom_css  = "";


            // custom color
            $custom_css .= get_auto_custom_css();

            // if user has custom css
            if(isset($awp_options['custom_css']) && !empty($awp_options['custom_css'])){
                $custom_css .= "\n\n" . $awp_options['custom_css'];
            }

            // if server can write files, do so.
            // if(!empty($custom_css) && isset($awp_options['disable_embed']) && $awp_options['disable_embed'] == 1){
            //     wp_enqueue_style("custom_style", $css_dir . "custom_style.css");
            // } elseif(!empty($custom_css)){
                wp_add_inline_style( 'style', $custom_css );
            // }
        }
    }
}

// disable responsiveness
function disable_responsiveness_class($classes) {
    global $awp_options;

    if(isset($awp_options['responsiveness']) && $awp_options['responsiveness'] == 0){
        $classes[] = 'no_responsive';
    }

    return $classes;
}
add_filter( 'body_class', 'disable_responsiveness_class' );

// generate stylesheet
// if(!function_exists("generate_dynamic_stylesheet")){
//     function generate_dynamic_stylesheet(){
//         global $awp_options;

//         $page             = (isset($_GET['page']) && !empty($_GET['page']) ? $_GET['page'] : "");
//         $settings_updated = (isset($_GET['settings-updated']) && !empty($_GET['settings-updated']) ? $_GET['settings-updated'] : "");

//         $custom_css  = "";

//         // custom color
//         $custom_css .= get_auto_custom_css();

//         // if user has custom css
//         if(isset($awp_options['custom_css']) && !empty($awp_options['custom_css'])){
//             $custom_css .= "\n\n" . $awp_options['custom_css'];
//         }


//         if($page == "automotive_wp" && $settings_updated == "true" && $awp_options['disable_embed'] == 1){
//             @file_put_contents(get_template_directory() . "/css/custom_style.css", $custom_css);        
//         }
//     }
// }
// add_action("init", "generate_dynamic_stylesheet");

if(!function_exists("automotive_scripts")){
    function automotive_scripts() {
    	wp_enqueue_script('jquery');
    	
    	$js_dir = get_template_directory_uri() . "/js/";
    	
    	$js_files = array("bootstrap", "retina", "wow", "main", "jquery.fancybox", "jquery.flexslider", "jquery.selectbox-0.2", "jquery.mousewheel", "jquery.easing", "social-likes.min");
    	
    	foreach($js_files as $file){
    		$label = str_replace(".", "", $file);
    		
    		wp_register_script($label, $js_dir . $file . '.js', array('jquery'), '1.0.0', true);
    		wp_enqueue_script($label);
    	}
    	wp_localize_script( 'main', 'ajax_variables', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'template_url' => get_template_directory_uri() ));
    }
}

/*************************************************************************************
 *  Automatic Theme Update
 *************************************************************************************/
function themeforest_themes_update($updates) {
    global $awp_options;

    $awp_options['themeforest_name'] = (isset($awp_options['themeforest_name']) && !empty($awp_options['themeforest_name']) ? $awp_options['themeforest_name'] : "");
    $awp_options['themeforest_api']  = (isset($awp_options['themeforest_api']) && !empty($awp_options['themeforest_api']) ? $awp_options['themeforest_api'] : "");

    if (isset($updates->checked) && !empty($awp_options['themeforest_name']) && !empty($awp_options['themeforest_api'])) {
        require_once("classes/themes-updater/class-pixelentity-themes-updater.php");

        $updater = new Pixelentity_Themes_Updater($awp_options['themeforest_name'], $awp_options['themeforest_api']);
        $updates = $updater->check($updates);
        
        return $updates;
    }
    
    return $updates;
}
add_filter("pre_set_site_transient_update_themes", "themeforest_themes_update");

if(isset($awp_options['theme_check']) && $awp_options['theme_check'] != 0){
    // add_action('admin_init', 'theme_update_available');
}

// admin
if(!function_exists("admin_scripts")){
    function admin_scripts($hook_suffix){
    	wp_enqueue_script( 'jquery' );
    	wp_enqueue_script( 'jquery-ui' );
    	wp_enqueue_script( 'jquery-ui-core' );
    	wp_enqueue_script( 'jquery-ui-sortable' );
    	wp_enqueue_script( 'jquery-ui-tabs' );
    	wp_enqueue_script( 'jquery-ui-slider' );
    	wp_enqueue_script( 'jquery-ui-widget' );
    	wp_enqueue_script( 'jquery-ui-dialog' );
    	wp_enqueue_script( 'jquery-effects-core' );
    	wp_enqueue_script( 'jquery-ui-size' );
    	wp_enqueue_script( 'iris' );
    	

        if(is_singular('listings') || $hook_suffix == "edit.php" || $hook_suffix == "post.php"){
            wp_register_script( 'admin_script', get_template_directory_uri() . "/js/admin.js");
            wp_enqueue_script( 'admin_script' );
            
            wp_register_script( 'google-maps', "https://maps.googleapis.com/maps/api/js?key&sensor=false");
            wp_enqueue_script( 'google-maps' );
            
            wp_register_script( 'jquery-admin', get_template_directory_uri() . '/js/jquery.admin.js' );  
            wp_localize_script( 'jquery-admin', 'ajax_variables', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
            wp_enqueue_script( 'jquery-admin' );
            
        }
    }
}

function str_to_px($input){
    return preg_replace( '/[^0-9]/', '', $input ) . "px";
}

if(!function_exists("get_auto_custom_css")){
    function get_auto_custom_css(){
        global $awp_options;

        $custom_css = "";

        if(isset($awp_options['primary_color'])){
            $primary_color = $awp_options['primary_color'];

            $custom_css .= 'a, a:hover, a:focus, .firstcharacter, .list-info span.text-red, .car-block-wrap h4 a, .welcome-wrap h4, .small-block:hover h4, .small-block:hover a i, .recent-vehicles h5, .flip .card .back i.button_icon:hover:before, .about-us h3, .blog-container h3, .blog-post h3, .side-content h3, .services h3, .list_faq ul li.active a, .list_faq ul li a:hover, .right_faq .side-widget h3, .side-content .side-blog strong, .side-content .list ul li:last-child a, .side-content .list ul li span, .main_pricing h3 b, .layout-3 .main_pricing .inside span.amt, .layout-4 .main_pricing .inside span.amt, .layout-3 .main_pricing .inside span.sub1, .layout-4 .main_pricing .inside span.sub1, #features ul li .fa-li, .left_inventory h2, .side-content .list h3, .side-content .financing_calculator h3, .left_inventory h2, .side-content .list h3, .side-content .financing_calculator h3, .featured-service h2, .featured-service h2 strong, .detail-service h2, .detail-service h2 strong, .find_team h2, .find_team h2, .find_team h2, .our_inventory h4, .our_inventory span, .year_wrapper span, .right_site_job .project_details ul li i, .read-more a, .comment-data .comment-author a, .find_map h2, .information_head h3, .address ul li span.compayWeb_color, .porche .car-detail .option-tick-list ul li:before, .detail-service .details h5:before, .services .right-content ul li:before, .alternate-font, .left_inventory h3, .no_footer .logo-footer a span, .page-content h3, .page-content h4, .page-content .small-block:hover h4, .pricing_table .main_pricing .inside span.amt, .pricing_table .main_pricing .inside span.sub1, .wp_page .page-content h2, .detail-service .details h5 i, body ul.shortcode.type-checkboxes li i, .comments h3#comments-number {
                                color: ' . $primary_color . ';
                            }

                            .pagination>li>a:hover, .pagination>li>span:hover, .pagination>li>a:focus, .pagination>li>span:focus, .progressbar .progress .progress-bar-danger, .bottom-header .navbar-default .navbar-nav>.active>a, .bottom-header .navbar-default .navbar-nav>.active>a:hover, .bottom-header .navbar-default .navbar-nav>.active>a:focus, .bottom-header .navbar-default .navbar-nav> li> a:hover, header .nav .open>a, header .nav .open>a:hover, header .nav .open>a:focus, header .navbar-default .navbar-nav>.open>a, header .navbar-default .navbar-nav>.open>a:hover, header .navbar-default .navbar-nav>.open>a:focus, .dropdown-menu>li>a:hover, .dropdown-menu>li>a:focus, .dropdown-menu>.active>a, .dropdown-menu>.active>a:hover, .dropdown-menu>.active>a:focus, .navbar-default .navbar-nav .open .dropdown-menu>.active>a, .navbar-default .navbar-nav .open .dropdown-menu>.active>a:hover, .car-block:hover .car-block-bottom, .controls .left-arrow:hover, .controls .right-arrow:hover, .back_to_top:hover, .flip .card .back, .description-accordion .panel-title a:after, .layout-3 .pricing-header, .layout-4 .pricing-header, .porche .porche-header, .featured-service .featured:hover, .featured-service .featured .caption, .flexslider2 .flex-direction-nav li a:hover, .default-btn, .default-btn:hover, .default-btn:focus, .form-element input[type=submit], .side-content form input[type=submit], .side-content form input[type=submit]:hover, input[type="reset"], input[type="reset"]:hover, input[type="submit"], input[type="button"], input[type="submit"]:hover, input[type="button"]:hover, .btn-inventory, .btn-inventory:hover, .porche-footer input[type="submit"], .porche-footer input[type="button"], .porche-footer input[type="submit"]:active, .porche-footer input[type="button"]:active, .leave-comments form input[type=submit], .leave-comments form input[type=submit]:active, .choose-list ul li:before, .woocommerce span.onsale, .woocommerce-page span.onsale, .current_page_parent   {
                                background: ' . $primary_color . ';
                                background-color: ' . $primary_color . ';
                            }

                            #wp-calendar td#today, body ul.shortcode li .red_box, button, .pricing_table .pricing-header, .featured:hover, button:hover, .arrow1 a:hover, .arrow2 a:hover, .arrow3 a:hover {
                                background-color: ' . $primary_color . ';
                            }  

                            .post-entry blockquote {
                                border-left-color: ' . $primary_color . ';
                            }

                            .angled_badge.theme_color:before { border-left-color: ' . $primary_color . '; border-right-color: ' . $primary_color . ';}
                            .listing-slider .angled_badge.theme_color:before { border-color: ' . $primary_color . '  rgba(0, 0, 0, 0); }';
        }

        // custom font
        if(isset($awp_options['body_font']) && !empty($awp_options['body_font']['font-family'])){
            $body_font = $awp_options['body_font'];

            $custom_css .= "
                            body, p, table, ul, li, .theme_font, .textwidget, .recent-vehicles p, .post-entry table, .list-info p, .blog-list span, .blog-content strong, .post-entry, .pricing_table .category_pricing ul li, .inventory-heading em, body ul.shortcode.type-checkboxes li, .about-us p, .blog-container p, .blog-post p, .address ul li strong, .address ul li span { 
                                font-family: " . $body_font['font-family'] . ";
                                font-size: " . str_to_px($body_font['font-size']) . ";
                                color: " . $body_font['color'] . ";
                                line-height: " . str_to_px($body_font['line-height']) . ";
                                font-weight: " . $body_font['font-weight'] . ";
                            }";

            $custom_css .= "
                            .small-block h4, .page-content .small-block h4, .small-block a, page-template-404 .error, .content h2.error, .content h2.error i.exclamation, .blog-list h4, .page-content .blog-list h4, .panel-heading .panel-title>a, .wp_page .page-content h2, .featured-service .featured h5, .detail-service .details h5, .name_post h4, .page-content .name_post h4, .portfolioContainer .box>div>span, .blog-content ul li, .comments > ul >li, .blog-content ul li a, .portfolioContainer .mix .box a, .project_wrapper h4.related_project_head, .post-entry span.tags a, .post-entry span.tags, .side-content .list ul li, .wp_page .page-content h2 a, .blog-content .post-entry h5, .blog-content h2, .address ul li i, .address ul li strong, .address ul li span, .icon_address p i, .listing-view ul.ribbon-item li a, .select-wrapper span.sort-by, .inventory-heading h2, .inventory-heading span, .inventory-heading .text-right h2, .woocommerce div.product .product_title, .woocommerce #content div.product .product_title, .woocommerce-page div.product .product_title, .woocommerce-page #content div.product .product_title, .woocommerce ul.products li.product .price, .woocommerce-page ul.products li.product .price, .woocommerce-page div.product p.price, .woocommerce div.product p.price, .woocommerce div.product .product_title, .woocommerce #content div.product .product_title, .woocommerce-page div.product .product_title, .woocommerce-page #content div.product .product_title, .parallax_parent .parallax_scroll h4 {
                                color: " . $body_font['color'] . ";
                            }";
        }

        // logo fonts
        if(isset($awp_options['logo_top_font']) || isset($awp_options['logo_bottom_font'])){
            $logo_top_font    = $awp_options['logo_top_font'];
            $logo_bottom_font = $awp_options['logo_bottom_font'];

            $custom_css .= "
                            header .bottom-header .navbar-default .navbar-brand .logo .primary_text, .no_footer .logo-footer a h1, .logo-footer a h1 {                                
                                font-family: " . $logo_top_font['font-family'] . ";
                                font-size: " . str_to_px($logo_top_font['font-size']) . ";
                                color: " . $logo_top_font['color'] . ";
                                line-height: " . str_to_px($logo_top_font['line-height']) . ";
                            }

                            header .bottom-header .navbar-default .navbar-brand .logo .secondary_text, .no_footer .logo-footer a span, .logo-footer a span {                      
                                font-family: " . $logo_bottom_font['font-family'] . ";
                                font-size: " . str_to_px($logo_bottom_font['font-size']) . ";
                                color: " . $logo_bottom_font['color'] . ";
                                line-height: " . str_to_px($logo_bottom_font['line-height']) . ";
                            }
            ";
        }

        // link color
        if(isset($awp_options['css_link_color']) && !empty($awp_options['css_link_color'])){
            $link_color = $awp_options['css_link_color'];

            $custom_css .= "
                            a { color: " . $link_color['regular'] . "; }
                            a:hover { color: " . $link_color['hover'] . "; }
                            a:active { color: " . $link_color['active'] . "; }
            ";
        }

        // link color
        if(isset($awp_options['css_footer_link_color']) && !empty($awp_options['css_footer_link_color'])){
            $footer_link_color = $awp_options['css_footer_link_color'];

            $custom_css .= "
                            footer a { color: " . $footer_link_color['regular'] . "; }
                            footer a:hover { color: " . $footer_link_color['hover'] . "; }
                            footer a:active { color: " . $footer_link_color['active'] . "; }
            ";
        }

        // boxed background
        if(isset($awp_options['boxed_background']) && !empty($awp_options['boxed_background']) && isset($awp_options['body_layout']) && $awp_options['body_layout'] > 1){
            $background_options = array("background-color", "background-image", "background-repeat", "background-position", "background-size", "background-attachment");

            $custom_css .= "
                body {
                    ";
                    foreach($background_options as $option){
                        $value = ($option == "background-image" ? "url(" . $awp_options['boxed_background'][$option] . ")" : $awp_options['boxed_background'][$option]);

                        $custom_css .= (isset($awp_options['boxed_background'][$option]) && !empty($awp_options['boxed_background'][$option]) ? $option . ": " . $value . ";\n" : "");
                    }
            $custom_css .= "
                }
            ";
        }

        // slideshow push 
        if(isset($awp_options['push_mobile_slideshow_down']) && $awp_options['push_mobile_slideshow_down'] == 1){
            $custom_css .= "
            @media only screen and (max-width: 768px){
                .home .header_rev_slider_container {
                    margin-top:105px !important;
                }
            }";
        }

        // Theme Scheme CSS
        if(isset($awp_options['theme_color_scheme']) && !empty($awp_options['theme_color_scheme'])){
            $scheme = (isset($awp_options['theme_color_scheme']['color_scheme_name']) && !empty($awp_options['theme_color_scheme']['color_scheme_name']) ? $awp_options['theme_color_scheme']['color_scheme_name'] : "Theme Styling");
            $custom_css .= "

            /* " . $scheme . " */

            ";

            if(!empty($awp_options['theme_color_scheme'])){
                foreach($awp_options['theme_color_scheme'] as $key => $item){
                    if(is_array($item) && !empty($item['selector']) && !empty($item['mode'])){
                        $custom_css .= $item['selector'] . " { ";

                        if(strstr($item['mode'], ",")){
                            $modes = explode(",", $item['mode']);

                            if(!empty($modes)){
                                foreach($modes as $mode){
                                    $custom_css .= $mode . ": " . $item['color'] . ";
                                    ";
                                }
                            }
                        } else {
                            $custom_css .= $item['mode'] . ": " . $item['color'] . ";";
                        }

                        $custom_css .= " }

                        ";

                    }
                }
            }


        }

        return $custom_css;
    }
}

if(!function_exists("D")){
    function D($vars){
    	echo "<pre>";
    	print_r($vars);
    	echo "</pre>";
    }
}

if(!function_exists("auto_image_id")){
    function auto_image_id($image_url) {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
        
        return (isset($attachment[0]) && !empty($attachment[0]) ? $attachment[0] : "");
    }
}

if(!function_exists("automotive_google_analytics_code")){
    function automotive_google_analytics_code($location){
    	global $awp_options;
    	$saved_location = (isset($awp_options['tracking_code_position']) && !empty($awp_options['tracking_code_position']) ? $awp_options['tracking_code_position'] : "");
    	
    	if(!empty($awp_options['google_analytics'])){
    		if($location == "head" && $saved_location == 1) {
                echo "<script type='text/javascript'>"; 
    			echo $awp_options['google_analytics'];
                echo "</script>";

    		} elseif($location == "body" && empty($saved_location)){
                echo "<script type='text/javascript'>"; 
    			echo $awp_options['google_analytics'];
                echo "</script>";
    		}	
    		
    	}
    }
}
add_action( 'admin_enqueue_scripts', 'admin_scripts' );

// Load conditional scripts
if(!function_exists("automotive_conditional_scripts")){
    function automotive_conditional_scripts() {
        if (is_page_template('contact-template.php')) {
            wp_register_script('google_maps', 'https://maps.googleapis.com/maps/api/js?key&amp;sensor=false', array('jquery'), '1.0.0');
            wp_enqueue_script('google_maps');
        }

        if (is_singular() && comments_open() && (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
        
    }  
}

if(!function_exists("languages_dropdown_menu")){
    function languages_dropdown_menu(){
        global $awp_options;

        if(function_exists("icl_get_home_url") && isset($awp_options['languages_dropdown']) && $awp_options['languages_dropdown'] == 1){
            $languages = icl_get_languages('skip_missing=0&orderby=id&order=asc&link_empty_to=#');

            if(!empty($languages)){
                echo "<ul class='languages'>";
                foreach($languages as $l){
                    echo "<li>";
                    if(!$l['active']) echo '<a href="'.$l['url'].'">';
                    echo '<img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" />' . icl_disp_language($l['native_name'], $l['translated_name']);
                    if(!$l['active']) echo '</a>';
                    echo '</li>';
                }
                echo "</ul>";
            }
        }
    }
}

if(!function_exists("woocommerce_shopping_cart")){
    function woocommerce_shopping_cart(){
        if(function_exists("is_woocommerce")){
            global $woocommerce;

            echo "<ul class='cart_dropdown'>";
            echo "<li>" . sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count) . ", Total of " . $woocommerce->cart->get_cart_total() . " <span class='padding-horizontal-5'>|</span> <a href='" . $woocommerce->cart->get_cart_url() . "'>" . __("Checkout", "automotive") . "</a></li>";
            echo "</ul>";
        } else {
            echo "<ul class='cart_dropdown'>";
            echo "<li>" . __("Please enable WooCommerce", "automotive") . "</li>";
            echo "</ul>";
        }
    }
}

// blog post
if(!function_exists("blog_post")){
    function blog_post(){ 
    	global $post;
    	
    	$secondary_title = get_post_meta($post->ID, "secondary_title", true);
    	
    	ob_start(); ?>
    	<div class="blog-content margin-bottom-40<?php echo (is_sticky() ? " sticky_post" : ""); ?>">
    		<div class="blog-title">
    			<h2<?php echo (empty($secondary_title) ? " class='margin-bottom-25'" : ""); ?>><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    			<?php echo (!empty($secondary_title) ? "<strong class='margin-top-5 margin-bottom-25'>" . $secondary_title. "</strong>" : ""); ?>
            </div>
    		<ul class="margin-top-10 margin-bottom-15">
    			<li class="fa fa-calendar"><a href="#"><?php the_time('F j, Y') ?></a></li>
    			<li class="fa fa-folder-open">
    			<?php
                $categories      = get_the_category();
                $categories_list = $tooltip_cats = "";
                $cat_inc         = 0;

                if($categories) {
                    foreach($categories as $category) {
                        if($cat_inc < 4){
                            $categories_list .= "<a href='" . get_category_link($category->term_id ) . "'>" . $category->cat_name . "</a>, ";
                        } else {                                
                            $tooltip_cats .= "<a href='" . get_category_link($category->term_id ) . "'>" . $category->cat_name . "</a><br>";
                        }
                                    
                        $cat_inc++;
                    }
                }

                echo (isset($categories_list) && !empty($categories_list) ? substr($categories_list, 0, -2) : __("Not categorized", "automotive"));

                // if more than 5
                if(!empty($tooltip_cats)){
                    echo ", <a class='' data-toggle=\"popover\" data-placement=\"top\" data-content=\"" . $tooltip_cats . "\" data-html=\"true\">" . __("More Categories", "automotive") . "...</a>";
                }
                ?>
    			</li>
    			<li class="fa fa-user"><span class="theme_font"><?php _e("Posted by", "automotive"); ?></span> <?php the_author_posts_link(); ?></li>
    			<li class="fa fa-comments"><?php comments_popup_link( __( 'No comments yet', 'automotive' ), __( '1 Comment', 'automotive' ), __( '% Comments', 'automotive' )); ?></li>
    		</ul>
    		<div class="post-entry clearfix">
                <?php
                // blog thumbnail
                if ( has_post_thumbnail() ) {
                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
                    echo '<div class="featured_blog_post_image"><a href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                    echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); 
                    echo '</a></div>';
                } elseif(get_post_type($post) == "listings" && function_exists("auto_image")){
                    global $lwp_options;

                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
                    $gallery_images  = get_post_meta( $post->ID, "gallery_images", true );

                    if(!empty($gallery_images) && !empty($gallery_images[0])){
                        echo '<div class="featured_blog_post_image"><a href="' . get_permalink( $post->ID ) . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                        echo auto_image( $gallery_images[0], 'thumbnail' ); 
                        echo '</a></div>';
                    } elseif(empty($gallery_images) && isset($lwp_options['not_found_image']['url'])) {
                        echo '<div class="featured_blog_post_image"><a href="' . get_permalink( $post->ID ) . '" title="' . the_title_attribute( 'echo=0' ) . '" >';
                        echo wp_get_attachment_image_src( $lwp_options['not_found_image']['id'], 'thumbnail' ); 
                        echo '</a></div>';                        
                    }
                } ?>

    		    <?php echo get_the_excerpt() ?>

                <div class="clearfix"></div>

    			<div class="blog-end margin-top-20">
    				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 read-more"><a href="<?php echo get_permalink($post->ID); ?>"><?php _e("Read More", "automotive"); ?>...</a></div>
    				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right post-tags"><span class="fa fa-tags tags">
    				<?php
                    $posttags = get_the_tags();
                    $tags     = $tooltip_tags = "";
                    $tag_inc  = 0;

                    if ($posttags) {
                        foreach($posttags as $tag) {
                            if($tag_inc < 4){
                                $tags .= "<a href='" . get_tag_link($tag->term_id) . "' title='" . $tag->name . " " . __("Tag", "automotive") . "'>" . $tag->name . "</a>, ";
                            } else {
                                $tooltip_tags .= "<a href='" . get_tag_link($tag->term_id) . "' title='" . $tag->name . " " . __("Tag", "automotive") . "'>" . $tag->name . "</a><br>";
                            }
                            
                            $tag_inc++;
                        }                           
                        echo substr($tags, 0, -2);

                        // if more than 5
                        if(!empty($tooltip_tags)){
                            echo ", <a class='' data-toggle=\"popover\" data-placement=\"top\" data-content=\"" . $tooltip_tags . "\" data-html=\"true\">" . __("More Tags", "automotive") . "</a>";
                        }
                    }
                    ?>
    				</span></div>
    				<div class="clearfix"></div>
    			</div>
    		</div>
    	</div>
        <?php
    	
    	$return = ob_get_contents();
    	ob_end_clean();
    	
    	return $return;
    }
}

// Register HTML5 Blank Navigation
if(!function_exists("register_automotive_menu")){
    function register_automotive_menu() {
        register_nav_menus(array(
            'header-menu' => __('Header Menu', 'automotive'),
            'footer-menu' => __('Footer Menu', 'automotive'),
            'mobile-menu' => __('Mobile Menu', 'automotive')
        ));
    }
}

if(!function_exists("browser_body_class")){
    function browser_body_class($classes) {
        global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

        if($is_lynx) $classes[] = 'lynx';
        elseif($is_gecko) $classes[] = 'gecko';
        elseif($is_opera) $classes[] = 'opera';
        elseif($is_NS4) $classes[] = 'ns4';
        elseif($is_safari) $classes[] = 'safari';
        elseif($is_chrome) $classes[] = 'chrome';
        elseif($is_IE) $classes[] = 'ie';
        else $classes[] = 'unknown';

        if($is_iphone) $classes[] = 'iphone';
        return $classes;
    }
}
add_filter('body_class','browser_body_class');

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar')) {
    function automotive_sidebars(){
        global $awp_options;
        
        // custom footers
        if(!empty($awp_options['footer_widget_spots'])){
            foreach($awp_options['footer_widget_spots'] as $footer){
                // Define Sidebar Widget Area $i
                if(!empty($footer)){
                    register_sidebar(array(
                        'name' => $footer,
                        'id' => 'footer-widget-' . str_replace(" ", "-", strtolower($footer)),
                        'before_widget' => '<div class="">',
                        'after_widget' => '</div>',
                        'before_title' => '<h4>',
                        'after_title' => '</h4>'
                    ));
                }
            }
        }

        // custom sidebars
        if(!empty($awp_options['custom_sidebars'])){
            foreach($awp_options['custom_sidebars'] as $sidebar){
                // Define Sidebar Widget Area $i
                if(!empty($sidebar)){
                    $safe_name = str_replace(" ", "-", strtolower($sidebar));

                    register_sidebar(array(
                        'name' => $sidebar,
                        'id' => $safe_name,
                        'before_widget' => '<div class="side-widget padding-bottom-40 list col-xs-12">',
                        'after_widget' => '</div>',
                        'before_title' => '<h3 class="side-widget-title margin-bottom-25">',
                        'after_title' => '</h3>'
                    ));
                }
            }
        }
        
        // Define Sidebar Widget Area 5
        register_sidebar(array(
            'name' => __('Default Footer', 'automotive'),
            'id' => 'default-footer',
            'before_widget' => '<div class="list col-xs-12">',
            'after_widget' => '</div>',
            'before_title' => '<h4>',
            'after_title' => '</h4>'
        ));
        
        // Define Sidebar Widget Area 5
        register_sidebar(array(
            'name' => __('Blog Sidebar', 'automotive'),
            'id' => 'blog-widget',
            'before_widget' => '<div class="side-widget padding-bottom-40 list col-xs-12">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="side-widget-title margin-bottom-25">',
            'after_title' => '</h3>'
        ));
    }

    add_action("widgets_init", "automotive_sidebars");
}

if(!function_exists("auto_bottom_sidebar_params")){
    function auto_bottom_sidebar_params($params) {

        $sidebar_id = $params[0]['id'];

        if ( strpos($sidebar_id, 'footer-widget') === 0 || $sidebar_id == 'default-footer'){

            $total_widgets = wp_get_sidebars_widgets();
            $sidebar_widgets = count($total_widgets[$sidebar_id]);

            // add padding
            foreach($total_widgets[$sidebar_id] as $key => $name){
                if($params[0]['widget_id'] == $name){
                    $current_index = $key;
                }
            }

            // for single item stuff
            $md_sm     = ($sidebar_widgets == 1 ? 12 : 6);

            $new_class = "class=\"col-lg-" . floor(12 / $sidebar_widgets) . " col-md-" . $md_sm . " col-sm-" . $md_sm . " col-lg-12 ";

            if($sidebar_widgets == 1){
                $new_class .= " padding-left-none padding-right-none ";
            } elseif($current_index == 0){
                $new_class .= " padding-left-none md-padding-left-none sm-padding-left-15 xs-padding-left-15 ";
            } elseif($current_index == ($sidebar_widgets - 1)){
                $new_class .= " padding-right-none md-padding-right-none sm-padding-right-15 xs-padding-right-15 ";            
            }

            $params[0]['before_widget'] = str_replace('class="', $new_class, $params[0]['before_widget']);
        }

        return $params;
    }
}
add_filter('dynamic_sidebar_params','auto_bottom_sidebar_params');

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
if(!function_exists("automotive_pagination")){
    function automotive_pagination($current_query = '') {
        wp_reset_query();
        global $wp_query;

        if(is_page_template("blog-template.php")){
            query_posts( array( 'posts_per_page' => get_option('posts_per_page'), 'paged' => get_query_var('paged') ) );
        }

        $big = 999999999; // need an unlikely integer
        $pages = paginate_links( array(
                'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'       => '?paged=%#%',
                'current'      => max( 1, get_query_var('paged') ),
                'total'        => $wp_query->max_num_pages,
                'prev_next'    => false,
                'type'  	   => 'array',
                'prev_next'    => TRUE,
    			'prev_text'    => '&laquo;',
    			'next_text'    => '&raquo;',
            ) );

            if( is_array( $pages ) ) {
                $paged = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
                echo '<ul class="pagination">';
                foreach ( $pages as $page ) {
                    echo "<li>" . $page . "</li>\n";
                }
               echo '</ul>';
            }
    }
}

// Custom Comments Callback
if(!function_exists("automotive_comments")){
    function automotive_comments($comment, $args, $depth) {
    	$GLOBALS['comment'] = $comment;
    	extract($args, EXTR_SKIP);

    	if ( 'div' == $args['style'] ) {
    		$tag = 'div';
    		$add_below = 'comment';
    	} else {
    		$tag = 'li';
    		$add_below = 'div-comment';
    	} ?>
    	<li>
        <div class="comment-profile clearfix margin-top-30 div-comment-<?php echo $comment->comment_ID; ?>" id="div-comment-<?php echo $comment->comment_ID; ?>">
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 threadauthor"> <?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, 180 ); ?> </div>
            <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                <div class="comment-data">
                    <div class="comment-author clearfix"><strong><?php echo get_comment_author_link(); ?></strong>| <small><?php printf( __('%1$s at %2$s', 'automotive'), get_comment_date(),  get_comment_time()) ?></small><span class="pull-right"><?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?> <?php edit_comment_link(__('(Edit)', 'automotive'), ' ','' ); ?></span></div>
                    <div class="comment-text">
                        <?php comment_text(); ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- </li> -->
<?php }
}

if(!function_exists("automotive_commentform")){
    function automotive_commentform(){
    	global $user_identity;
    	
    	$commenter     = wp_get_current_commenter();
    	$req           = get_option( 'require_name_email' );
    	$aria_req      = ( $req ? " aria-required='true'" : '' );
    	$required_text = "*";
    	
    	get_currentuserinfo();
    	
    	$args = array(
    	  'id_form'           => 'commentform',
    	  'id_submit'         => 'submit',
    	  'title_reply'       => __( 'Leave comments', 'automotive' ),
    	  'title_reply_to'    => __( 'Leave a reply to %s', 'automotive' ),
    	  'cancel_reply_link' => __( 'Cancel Reply', 'automotive' ),
    	  'label_submit'      => __( 'Submit Comment', 'automotive' ),
    	
    	  'comment_field' =>  '<textarea class="form-control" placeholder="' . __('Your comments', 'automotive') . '" rows="7" name="comment" id="comment"></textarea>',
    	
    	  'must_log_in' => '<p class="must-log-in">' .
    		sprintf(
    		  __( 'You must be <a href="%s">logged in</a> to post a comment.' ),
    		  wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
    		) . '</p>',
    	
    	  'logged_in_as' => '<p class="logged-in-as">' .
    		sprintf(
    		__( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ),
    		  admin_url( 'profile.php' ),
    		  $user_identity,
    		  wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
    		) . '</p>',
    	
    	  'comment_notes_before' => '<p class="comment-notes">' .
    		__( 'Your email address will not be published.', 'automotive' ) . ( $req ? $required_text : '' ) .
    		'</p>',
    	
    	  'comment_notes_after' => '<p class="form-allowed-tags">' .
    		sprintf(
    		  __( '<br><br>You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s' ),
    		  ' <code>' . allowed_tags() . '</code>'
    		) . '</p>',
    	
    	  'fields' => apply_filters( 'comment_form_default_fields', array(
    	
    		'author' =>
    		  '<input type="text" class="form-control" placeholder="' . __("Name (Required)", "automotive") . '" name="author">',
    	
    		'email' =>
    		  '<input type="text" class="form-control" placeholder="' . __("Email (Required)", "automotive") . '" autocomplete="off" name="email">',
    	
    		'url' =>
    		  '<input type="text" class="form-control" placeholder="' . __("Website", "automotive") . '" name="url">'
    		)
    	  ),
    	);
    	
    	echo "<div class='leave-comments clearfix' id='respond'>";
    	comment_form( $args, get_current_id() );
    	echo "</div>";
    }
}

if(!function_exists("get_page_title_and_desc")){
    function get_page_title_and_desc(){
    	global $post, $awp_options;
    	
    	if(is_404()){
    		$desc  = __("That being said, we will give you an amazing deal for the trouble", "automotive");	
    		$title = __("Error 404: File not found.", "automotive");
    	} else {
    		if(function_exists("is_woocommerce") && (is_shop() || is_checkout() || is_account_page())){
    			if(is_shop()){
    				$page_id = get_option('woocommerce_shop_page_id');		
    			} elseif(is_checkout()) {
    				$page_id = get_option('woocommerce_pay_page_id'); 
    			} elseif(is_account_page()) {
    				$page_id = get_option('woocommerce_myaccount_page_id'); 
    			} elseif(is_account_page()) {
    				$page_id = get_option('woocommerce_edit_address_page_id'); 
    			} elseif(is_account_page()) {
    				$page_id = get_option('woocommerce_view_order_page_id'); 
    			}
    			
    			$page  = get_post( $page_id );
    			
    			$desc  = get_post_meta($page->ID, "secondary_title", true);
    			$title = get_the_title($page->ID);
    		} elseif(function_exists("is_product") && is_product()){		
    			$desc  = get_post_meta(get_queried_object_id(), "secondary_title", true);
    			$title = get_the_title(get_queried_object_id());		
    		} elseif(function_exists("is_product_category") && (is_product_category() || is_product_tag())){
    			global $wp_query;
    			
    			$cat   = $wp_query->get_queried_object();
    			$desc  = $cat->description;		
    			$title = $cat->name;
    		} elseif(is_page()){
                global $post;
        
                $desc  = get_post_meta($post->ID, "secondary_title", true);
                $title = get_the_title($post->ID);
    		} elseif(is_home()){
    			$id      = get_option('page_for_posts');
    			
    			$secondary_title = get_post_meta($id, "secondary_title",  true);
    			
    			$desc    = (isset($secondary_title) && !empty($secondary_title) ? $secondary_title : get_bloginfo('description'));
    			$title   = ($id == 0 ? get_bloginfo('name') : get_the_title($id));
    			$classes = "blog_page";
    		} elseif(is_category()){
    			$cat     = get_category(get_query_var('cat'),false);
    			
    			$desc    = "";
    			$title   = __("Category Archive", "automotive") . ": " . $cat->name;
    		} elseif(is_author()){
                $desc    = "";
                $title   = __("Author Archive", "automotive") . ": " . get_the_author();                
            } elseif(is_tag()){
    			$desc    = "";
    			$title   = __("Tag Archive", "automotive") . ": " . single_tag_title("", false);
    		} elseif(is_search()){
    			$desc    = "";
    			$title   = __("Search term", "automotive") . ": " . get_search_query();
    		} elseif(is_singular("listings")){
                global $lwp_options;
                
    			$desc    = (isset($lwp_options['inventory_secondary_title']) && !empty($lwp_options['inventory_secondary_title']) ? $lwp_options['inventory_secondary_title'] : "");
    			$title   = (isset($lwp_options['inventory_primary_title']) && !empty($lwp_options['inventory_primary_title']) ? $lwp_options['inventory_primary_title'] : "");
    		} elseif(is_singular("listings_portfolio")){
    			$desc    = get_post_meta($post->ID, "secondary_title", true);
    			$title   = get_the_title($post->ID);
    		} elseif(is_single()){
    			$desc    = (isset($awp_options['blog_secondary_title']) && !empty($awp_options['blog_secondary_title']) ? $awp_options['blog_secondary_title'] : __("Latest Industry News", "automotive"));
    			$title   = (isset($awp_options['blog_primary_title']) && !empty($awp_options['blog_primary_title']) ? $awp_options['blog_primary_title'] : __("Blog", "automotive"));
    		} else {
    			global $post;
    	
    			$desc  = get_post_meta($post->ID, "secondary_title", true);
    			$title = get_the_title($post->ID);
    		}
    	}
    	
    	return array($title, $desc);
    }
}

//********************************************
//	The breadcrumb
//***********************************************************
if( !function_exists("the_breadcrumb") ){
	function the_breadcrumb($last_text) {
		
		$character_limit = 75;
		
		if(isset($last_text) && !empty($last_text)){
			$breadcrumb_text = (strlen($last_text) > $character_limit ? substr($last_text, 0, $character_limit) . "..." : $last_text);
		} 
		
		if (!is_front_page()) {
			global $post, $awp_options, $lwp_options;
			
            if(function_exists("is_woocommerce") && is_woocommerce()){
                woocommerce_breadcrumb( array(
                        "wrap_before" => "<ul class='breadcrumb woocommerce_breadcrumb'>",
                        "wrap_after"  => "</ul>",
                        "delimiter"   => "&nbsp;&nbsp;/&nbsp;&nbsp;"
                    ) 
                );
            } else {
    			$breadcrumb  = "<ul class='breadcrumb'>";
    			$breadcrumb .= '<li><a href="' . home_url() . '">' . __("Anasayfa", "automotive") . '</a></li>';
    			
    			if(isset($post) && !empty($post) && trim($post->post_parent) != "" && $post->post_parent != 0){
    				$parent_post = get_post($post->post_parent);
    				$breadcrumb .= "<li><a href='" . get_permalink($post->post_parent) . "'>" . $parent_post->post_title . "</a></li>";
    			}
    			
    			if(is_404() || is_page_template("404.php")){
    				$breadcrumb .= " <li class='current_crumb'>" . (isset($last_text) && !empty($last_text) ? $last_text : "404") . "</li>";
    			} elseif(is_search()){
    				$breadcrumb .= " <li class='current_crumb'>" . (isset($breadcrumb_text) ? $breadcrumb_text : __("Search", "automotive") . ": " . get_search_query()) . "</li>";
    			} elseif(is_single()){
    				if(is_singular('listings')){

                        if(isset($lwp_options['inventory_page']) && !empty($lwp_options['inventory_page'])){
                            $inventory_link  = get_permalink($lwp_options['inventory_page']);
                            $inventory_title = get_the_title($lwp_options['inventory_page']);

                            $breadcrumb .= "<li><a href='" . $inventory_link . "'>" . $inventory_title . "</a></li>";
                        }

    				} elseif(is_singular("listings_portfolio")){
                        $cats = wp_get_object_terms( $post->ID, "project-type" );
                        if(!empty($cats)){
                            foreach($cats as $cat){
                                $breadcrumb .= "<li><a href='" . get_category_link($cat->term_id) . "'>" . $cat->name . "</a></li>";
                            }
                        }
    				} elseif(function_exists("is_product") && is_product()){
    					$shop_id     = get_option('woocommerce_shop_page_id');					
    					$page        = get_post( $shop_id );
    					
    					$breadcrumb .= "<li><a href='" . get_permalink($shop_id) . "'>" . get_the_title($page->ID) . "</a></li>";
    				} else {
                        $breadcrumb_style = (isset($awp_options['breadcrumb_style']) && !empty($awp_options['breadcrumb_style']) ? $awp_options['breadcrumb_style'] : "");

                        if($breadcrumb_style == 0){
                            $cats = wp_get_post_categories( $post->ID );
                            if(!empty($cats)){
                                foreach($cats as $cat){
                                    $cat = get_category($cat);
                                    $breadcrumb .= "<li><a href='" . get_permalink($cat->term_id) . "'>" . $cat->name . "</a></li>";
                                }
                            }
                        } else {
            				$posts_page = get_option('page_for_posts');
            				
            				if(isset($posts_page) && !empty($posts_page)){
            					$breadcrumb .= "<li><a href='" . get_category_link($posts_page) . "'>" . get_the_title($posts_page) . "</a></li>";
            				}
                        }
    				}
    							
    				$breadcrumb .= " <li class='current_crumb'>" . (strlen(get_the_title()) > $character_limit ? substr(get_the_title(), 0, $character_limit) . "..." : get_the_title()) . "</li>";
    			} elseif(is_archive()){
    				if ( is_category() && !isset($breadcrumb_text) ) {
    					$breadcrumb .= "<li><a href='#'>" . (isset($breadcrumb_text) ? $breadcrumb_text : __("Category Archives", "automotive")) . "</a></li>";
    					$text = single_cat_title( '', false );
    	 
    				} elseif ( is_tag() && !isset($breadcrumb_text) ) {
    					$breadcrumb .= "<li><a href='#'>" . (isset($breadcrumb_text) ? $breadcrumb_text : __("Tag Archives", "automotive")) . "</a></li>";
    					$text = single_tag_title( '', false );
    	 
    				} elseif ( is_author() ) {
    					/* Queue the first post, that way we know
    					 * what author we're dealing with (if that is the case).
    					*/
    					the_post();
                        $text = sprintf( __('Author Archives: %s', 'automotive'), get_the_author());
    					// $text = sprintf( __( 'Author Archives: %s', 'automotive'), '<li class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( "ID" ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></li>' );
    					/* Since we called the_post() above, we need to
    					 * rewind the loop back to the beginning that way
    					 * we can run the loop properly, in full.
    					 */
    					rewind_posts();
    	 
    				} elseif ( is_day() ) {
    					$breadcrumb .= "<li><a href='#'>" . __("Daily Archives", "automotive") . "</a></li>";
    					$text = get_the_date();
    	 
    				} elseif ( is_month() ) {
    					$breadcrumb .= "<li><a href='#'>" . __("Monthly Archives", "automotive") . "</a></li>";
    					$text = get_the_date( 'F Y' );
    	 
    				} elseif ( is_year() ) {
    					$breadcrumb .= "<li><a href='#'>" . __("Yearly Archives", "automotive") . "</a></li>";
    					$text = get_the_date( 'Y' );
    	 
    				} elseif( function_exists("is_shop") && is_shop() ){
    					$text = get_the_title(get_option('woocommerce_shop_page_id'));
    				} elseif( function_exists("is_product_category") && (is_product_category() || is_product_tag())){
    					global $wp_query;
    					
    					$cat  = $wp_query->get_queried_object();
    					$text = $cat->name;
    				} else {
    					$text = __( 'Archives', 'automotive');
    	 
    				}
    				
    				$breadcrumb .= " <li class='current_crumb'>";
    				if(isset($last_text) && !empty($last_text)){
    					$breadcrumb .= (strlen($last_text) > $character_limit ? substr($last_text, 0, $character_limit) . "..." : $last_text);
    				} else {					
    					$breadcrumb .= (strlen($text) > $character_limit ? substr($text, 0, $character_limit) . "..." : $text);
    				}
    				$breadcrumb .= "</li>";
    			} else {
    				$title = get_the_title(get_queried_object_id());
    				
    				$breadcrumb .= " <li class='current_crumb'>";				
    				if(isset($last_text) && !empty($last_text)){
    					$breadcrumb .= (strlen($last_text) > $character_limit ? substr($last_text, 0, $character_limit) . "..." : $last_text);
    				} else {					
    					$breadcrumb .= (strlen($title) > $character_limit ? substr($title, 0, $character_limit) . "..." : $title);
    				}
    				$breadcrumb .= "</li>";
    			}
    			
    			$breadcrumb .= "</ul>";
			 
                echo $breadcrumb;
            }
		}
	}
}

if(!function_exists("random_string")){
	function random_string($length = 10) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
}

if(!function_exists("get_table_prefix")){
	function get_table_prefix() {
		global $wpdb;
		return $wpdb->prefix;
	}
}


//********************************************
//	Editor Styles
//***********************************************************
if(!function_exists("theme_editor_styles")){
    function theme_editor_styles() {
    	add_editor_style( "css/wp.css" );
    	add_editor_style( "css/bootstrap.min.css" );
    	add_editor_style( "css/style.css" );
    	add_editor_style( "css/font-awesome.css" );
    	add_editor_style( "css/custom.css" );
    }
}

add_action( 'init', 'theme_editor_styles' );


//********************************************
//  Actions + Filters
//***********************************************************
add_action('wp_print_scripts', 'automotive_conditional_scripts');
add_action('wp_enqueue_scripts', 'automotive_styles'); 
add_action('wp_enqueue_scripts', 'automotive_scripts');
add_action('init', 'register_automotive_menu'); 
add_action('init', 'automotive_pagination'); 

class wp_bootstrap_navwalker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul role=\"menu\" class=\" dropdown-menu\">\n";
	}

	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		} else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		} else {

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

			if ( $args->has_children ){
				$class_names .= ' dropdown';
            }

			if ( in_array( 'current-menu-item', $classes ) || in_array( 'current-menu-ancestor', $classes ) )
				$class_names .= ' active';

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title )	? $item->attr_title	: '';
			$atts['target'] = ! empty( $item->target )	? $item->target	: '';
			$atts['rel']    = ! empty( $item->xfn )		? $item->xfn	: '';

			// If item has_children add atts to a.
			if ( $args->has_children && $depth === 0 ) {
				//$atts['href']   		= '#';
				//$atts['data-toggle']	= 'dropdown';
				$atts['class']			= '';
				$atts['aria-haspopup']	= 'true';
			} else {
				//$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			}
            
            $atts['href'] = ! empty( $item->url ) ? $item->url : '';

            if( $args->has_children ){
                //$atts['data-toggle']    = 'dropdown';
                $class_names .= '';
            }

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}
            
			$item_output = $args->before;

			// if ( ! empty( $item->attr_title ) )
			// 	$item_output .= '<a'. $attributes .'><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;';
			// else
		    $item_output .= '<a'. $attributes .'>';

			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= ( $args->has_children && 0 === $depth ) ? ' <b class="caret"></b></a>' : '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element )
            return;

        $id_field = $this->db_fields['id'];

        // Display this element.
        if ( is_object( $args[0] ) )
           $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {

			extract( $args );

			$fb_output = null;

			if ( $container ) {
				$fb_output = '<' . $container;

				if ( $container_id )
					$fb_output .= ' id="' . $container_id . '"';

				if ( $container_class )
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $menu_id )
				$fb_output .= ' id="' . $menu_id . '"';

			if ( $menu_class )
				$fb_output .= ' class="' . $menu_class . '"';

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">' . __("Add a menu", "automotive") . '</a></li>';
			$fb_output .= '</ul>';

			if ( $container )
				$fb_output .= '</' . $container . '>';

			echo $fb_output;
		}
	}
}

class wp_bootstrap_navwalker_mobile extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul role=\"menu\" class=\" dropdown-menu\">\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
            $output .= $indent . '<li role="presentation" class="divider">';
        } else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
            $output .= $indent . '<li role="presentation" class="divider">';
        } else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
            $output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
        } else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
            $output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
        } else {

            $class_names = $value = '';

            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes[] = 'menu-item-' . $item->ID;

            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

            if ( $args->has_children ){
                $class_names .= ' dropdown';
            }

            if ( in_array( 'current-menu-item', $classes ) || in_array( 'current-menu-ancestor', $classes ) )
                $class_names .= ' active';

            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

            $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
            $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

            $output .= $indent . '<li' . $id . $value . $class_names .'>';

            $atts = array();
            $atts['title']  = ! empty( $item->title )   ? $item->title  : '';
            $atts['target'] = ! empty( $item->target )  ? $item->target : '';
            $atts['rel']    = ! empty( $item->xfn )     ? $item->xfn    : '';

            // If item has_children add atts to a.
            if ( $args->has_children && $depth === 0 ) {
                $atts['href']           = '#';
                $atts['class']          = '';
                $atts['aria-haspopup']  = 'true';
            } else {
                $atts['href'] = ! empty( $item->url ) ? $item->url : '';
            }
            
            $atts['href'] = ! empty( $item->url ) ? $item->url : '';

            if( $args->has_children ){
                $atts['data-toggle'] = 'dropdown';
                $class_names .= '';
            }

            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $item_output = $args->before;

            if ( ! empty( $item->attr_title ) )
                $item_output .= '<a'. $attributes .'><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;';
            else
                $item_output .= '<a'. $attributes .'>';

            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
            $item_output .= ( $args->has_children && 0 === $depth ) ? ' <b class="caret"></b></a>' : '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }
    }

    public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element )
            return;

        $id_field = $this->db_fields['id'];

        // Display this element.
        if ( is_object( $args[0] ) )
           $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    public static function fallback( $args ) {
        if ( current_user_can( 'manage_options' ) ) {

            extract( $args );

            $fb_output = null;

            if ( $container ) {
                $fb_output = '<' . $container;

                if ( $container_id )
                    $fb_output .= ' id="' . $container_id . '"';

                if ( $container_class )
                    $fb_output .= ' class="' . $container_class . '"';

                $fb_output .= '>';
            }

            $fb_output .= '<ul';

            if ( $menu_id )
                $fb_output .= ' id="' . $menu_id . '"';

            if ( $menu_class )
                $fb_output .= ' class="' . $menu_class . '"';

            $fb_output .= '>';
            $fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">' . __("Add a menu", "automotive") . '</a></li>';
            $fb_output .= '</ul>';

            if ( $container )
                $fb_output .= '</' . $container . '>';

            echo $fb_output;
        }
    }
}

//********************************************
//	Action Area
//***********************************************************
if(!function_exists("action_area")){
    function action_area($action){
    	global $post;
    	
    	if(isset($post) && !empty($post) && isset($action) && $action == "on"){
    		$action_text        = get_post_meta($post->ID, "action_text", true);
    		$action_button_text = get_post_meta($post->ID, "action_button_text", true);
            $action_link        = get_post_meta($post->ID, "action_link", true);
            $action_class       = get_post_meta($post->ID, "action_class", true); 

            $heading_class = (!empty($action_button_text) ? "col-lg-9 col-md-8 col-sm-12 col-xs-12 xs-padding-left-15" : "col-lg-12 col-md-12 col-sm-12 col-xs-12 xs-padding-left-15"); ?>
    		
    		<section class="message-wrap">
    			<div class="container">
    				<div class="row">
    					<h2 class="<?php echo $heading_class; ?>"><?php echo $action_text; ?></h2>
                        <?php if(!empty($action_button_text)){ ?>
    					<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 xs-padding-right-15"> <a href="<?php echo $action_link; ?>" class="default-btn pull-right action_button<?php echo (isset($action_class) && !empty($action_class) ? " " . $action_class : ""); ?>"><?php echo ($action_button_text); ?></a> </div>
    				    <?php } ?>
                    </div>
    			</div>
    			<div class="message-shadow"></div>
    		</section>
    	<?php
    	}
    }
}

//********************************************
//	Add custom active class
//***********************************************************
add_filter( 'nav_menu_css_class', 'add_menu_active_class', 10, 2 );

if(!function_exists("add_menu_active_class")){
    function add_menu_active_class( $classes = array(), $menu_item = false ) {
        if ( in_array( 'current-menu-parent', $classes ) ) {
            $classes[] = 'active';
        }
        return $classes;
    }
}

if(!function_exists("redux_font_awesome_icons")){
    function redux_font_awesome_icons() { 
        wp_register_style( 'redux-font-awesome', get_template_directory_uri() . '/css/font-awesome.css', array() );  
        wp_enqueue_style( 'redux-font-awesome' );
    }
}
add_action( 'redux/page/automotive_wp/enqueue', 'redux_font_awesome_icons' );
add_action( 'admin_enqueue_scripts', 'redux_font_awesome_icons' );

//********************************************
//	Sidebar classes
//***********************************************************
if(!function_exists("content_classes")){
    function content_classes($sidebar){
        // determine classes
        if($sidebar == "left"){
            $return = array("col-lg-9 col-lg-push-3 col-md-push-3 col-md-9 col-sm-7 col-sm-push-5 col-xs-12 padding-right-none padding-left-15 md-padding-left-15 md-padding-right-none sm-padding-left-15 sm-padding-right-none xs-padding-left-none xs-padding-right-none", "col-lg-3 col-lg-pull-9 col-md-pull-9 col-md-3 col-sm-5 col-sm-pull-7 col-xs-12 padding-left-none padding-right-15 md-padding-left-none md-padding-right-15 sm-padding-right-15 sm-padding-left-none xs-padding-left-none xs-padding-right-none xs-padding-top-20");
        } else if($sidebar == "right"){
            $return = array("col-lg-9 col-md-9 col-sm-9 col-xs-12 padding-right-none padding-left-15 md-padding-right-15 md-padding-left-none sm-padding-right-15 sm-padding-left-none xs-padding-left-none xs-padding-right-none", "col-lg-3 col-md-3 col-sm-3 col-xs-12 padding-left-none padding-right-15 md-padding-right-none md-padding-left-15 sm-padding-left-15 sm-padding-right-none xs-padding-left-none xs-padding-right-none xs-padding-top-20");
        } else {
            $return = array("col-lg-12 col-md-12 col-sm-12 col-xs-12");
        }

        // 0 = content class
        // 1 = sidebar class

        return $return;
    }
}

//********************************************
//  Woocommerce stuffz
//***********************************************************
function woocommerce_remove_breadcrumb(){
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
add_action('woocommerce_before_main_content', 'woocommerce_remove_breadcrumb');

function woocommerce_custom_breadcrumb(){
    woocommerce_breadcrumb();
}

add_action( 'woo_custom_breadcrumb', 'woocommerce_custom_breadcrumb' );

add_filter( 'woocommerce_show_page_title' , 'woo_hide_page_title' );
function woo_hide_page_title() {
    
    return false;
    
}

//********************************************
//  Visual Composer Templates
//***********************************************************
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if( is_plugin_active('automotive/index.php')){
    add_filter( 'vc_load_default_templates', 'vc_contact_template' );
    add_filter( 'vc_load_default_templates', 'vc_about_template' );
    add_filter( 'vc_load_default_templates', 'vc_faq_template' );
    add_filter( 'vc_load_default_templates', 'vc_our_team' );
    add_filter( 'vc_load_default_templates', 'vc_services_template' );
    add_filter( 'vc_load_default_templates', 'vc_pricing_tables' );
    add_filter( 'vc_load_default_templates', 'vc_homepage_template' );


    function vc_contact_template($data) {
        $template               = array();
        $template['name']       = __( '[Automotive] Contact Page', 'automotive' );
        $template['content']    = <<<CONTENT
            [vc_row][vc_column width="1/1"][vc_column_text]
    <h3>FIND US ON THE MAP</h3>
    [/vc_column_text][auto_google_map longitude="-79.38" latitude="43.65" zoom="7" height="390"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_column_text]
    <h3>CONTACT INFORMATION</h3>
    [/vc_column_text][auto_contact_information company="Company Name" address="1234 Street Name
    City Name, AB 12345
    United States" phone="1-800-123-4567" email="sales@company.com" web="www.company.com"][vc_column_text]
    <h3>BUSINESS HOURS</h3>
    [/vc_column_text][hours_table title="Sales Department" mon="8:00am - 5:00pm" tue="8:00am - 9:00pm" wed="8:00am - 5:00pm" thu="8:00am - 9:00pm" fri="8:00am - 6:00pm" sat="9:00am - 5:00pm" sun="Closed"][hours_table title="Service Department" mon="8:00am - 5:00pm" tue="8:00am - 9:00pm" wed="8:00am - 5:00pm" thu="8:00am - 9:00pm" fri="8:00am - 6:00pm" sat="9:00am - 5:00pm" sun="Closed"][hours_table title="Parts Department" mon="8:00am - 5:00pm" tue="8:00am - 9:00pm" wed="8:00am - 5:00pm" thu="8:00am - 9:00pm" fri="8:00am - 6:00pm" sat="9:00am - 5:00pm" sun="Closed"][/vc_column][vc_column width="1/2"][vc_column_text]
    <h3>CONTACT FORM</h3>
    [/vc_column_text][auto_contact_form name="Name (Required)" email="Email (Required)" message="Message" button="Send Message"][/vc_column][/vc_row]
CONTENT;
        array_unshift($data, $template);
        return $data;
    }

    function vc_about_template($data) {
        $template               = array();
        $template['name']       = __( '[Automotive] About Us Page', 'automotive' );
        $template['content']    = <<<CONTENT
            [vc_row css=".vc_custom_1410354226977{margin-bottom: 60px !important;}"][vc_column width="8/12"][vc_column_text]
    <h3>OUR MISSION IS SIMPLE</h3>
    [/vc_column_text][vc_column_text][dropcaps]C[/dropcaps]obem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.

    <img class="alignleft wp-image-1370 size-full" src="http://dev.themesuite.com/automotive/wp-content/uploads/2014/09/img-display.jpg" alt="img-display" width="370" height="192" />Sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, eta rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis. Lorem ipsum dolor sit amet,

    Consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Et Donec pretium quis sem quam felis, ultricies nec, pellentesque eu, aenean massa et a pretium quis, sem. Cobem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.[/vc_column_text][/vc_column][vc_column width="4/12"][vc_column_text]
    <h3>WHAT WE SPECIALIZE IN</h3>
    [/vc_column_text][progress_bar color="#c7081b" filled="100%"]WordPress[/progress_bar][progress_bar color="#c7081b" filled="90%"]HTML / CSS[/progress_bar][progress_bar color="#c7081b" filled="80%"]PHP[/progress_bar][progress_bar color="#c7081b" filled="70%"]Javascript[/progress_bar][progress_bar color="#c7081b" filled="60%"]Photoshop[/progress_bar][progress_bar color="#c7081b" filled="50%"]MySQL[/progress_bar][progress_bar color="#c7081b" filled="40%"]jQuery[/progress_bar][progress_bar color="#c7081b" filled="30%"]Joomla[/progress_bar][progress_bar color="#c7081b" filled="20%"]XML[/progress_bar][/vc_column][/vc_row][vc_row css=".vc_custom_1410354195826{margin-bottom: 60px !important;}"][vc_column width="4/12"][vc_column_text]
    <h3>WHY CHOOSE US?</h3>
    [/vc_column_text][list style="arrows"][list_item]Integrated inventory management system[/list_item][list_item]Fully responsive and ready for all mobile devices[/list_item][list_item]Simple to use and extremely easy to customize[/list_item][list_item]Search engine optimized out of the box (SEO ready)[/list_item][list_item]Includes a license for Revolution Slider ($15 value)[/list_item][list_item]Tons of shortcodes for easy and functional add-ons[/list_item][list_item]Completely backed by our dedicated support staff[/list_item][list_item]Fully featured Option Panel for quick &amp; easy setup[/list_item][/list][/vc_column][vc_column width="3/12"][vc_column_text]
    <h3>TESTIMONIALS</h3>
    [/vc_column_text][testimonials slide="horizontal" speed="500"][testimonial_quote name="Theodore Isaac Rubin"]Happiness does not come from doing easy work but from the afterglow of satisfaction that comes after the achievement of a difficult task that demanded our best.[/testimonial_quote][testimonial_quote name="Theodore Isaac Rubin"]Happiness does not come from doing easy work but from the afterglow of satisfaction that comes after the achievement of a difficult task that demanded our best.[/testimonial_quote][/testimonials][/vc_column][vc_column width="5/12"][vc_column_text]
    <h3>LATEST AUTOMOTIVE NEWS</h3>
    [/vc_column_text][recent_posts_scroller number="2" speed="500" foo="3"][/vc_column][/vc_row][vc_row css=".vc_custom_1410354200578{margin-bottom: 60px !important;}"][vc_column width="1/1"][vc_column_text]
    <h3>SOME OF OUR FEATURED BRANDS</h3>
    [/vc_column_text][featured_brands][brand_logo img="1425" hoverimg="1424"][/brand_logo][brand_logo img="1421" hoverimg="1420"][/brand_logo][brand_logo img="1427" hoverimg="1426"][/brand_logo][brand_logo img="1423" hoverimg="1422"][/brand_logo][brand_logo img="1431" hoverimg="1430"][/brand_logo][brand_logo img="1429" hoverimg="1428"][/brand_logo][brand_logo img="1425" hoverimg="1424"][/brand_logo][brand_logo img="1421" hoverimg="1420"][/brand_logo][brand_logo img="1427" hoverimg="1426"][/brand_logo][brand_logo img="1423" hoverimg="1422"][/brand_logo][brand_logo img="1431" hoverimg="1430"][/brand_logo][brand_logo img="1429" hoverimg="1428"][/brand_logo][/featured_brands][/vc_column][/vc_row][vc_row el_class="fullwidth_element bottom_element"][vc_column width="1/1"][auto_google_map longitude="-79.38" latitude="43.65" zoom="8" height="390" map_style="JTVCJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJsYW5kc2NhcGUlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscyUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJ0cmFuc2l0JTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9mZiUyMiU3RCU1RCU3RCUyQyU3QiUyMmZlYXR1cmVUeXBlJTIyJTNBJTIycG9pJTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9mZiUyMiU3RCU1RCU3RCUyQyU3QiUyMmZlYXR1cmVUeXBlJTIyJTNBJTIyd2F0ZXIlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscyUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJyb2FkJTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMuaWNvbiUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyc3R5bGVycyUyMiUzQSU1QiU3QiUyMmh1ZSUyMiUzQSUyMiUyM0YwRjBGMCUyMiU3RCUyQyU3QiUyMnNhdHVyYXRpb24lMjIlM0EtMTAwJTdEJTJDJTdCJTIyZ2FtbWElMjIlM0EyLjE1JTdEJTJDJTdCJTIybGlnaHRuZXNzJTIyJTNBMTIlN0QlNUQlN0QlMkMlN0IlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMnJvYWQlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscy50ZXh0LmZpbGwlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9uJTIyJTdEJTJDJTdCJTIybGlnaHRuZXNzJTIyJTNBMjQlN0QlNUQlN0QlMkMlN0IlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMnJvYWQlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmdlb21ldHJ5JTIyJTJDJTIyc3R5bGVycyUyMiUzQSU1QiU3QiUyMmxpZ2h0bmVzcyUyMiUzQTU3JTdEJTVEJTdEJTVE"][/vc_column][/vc_row]
CONTENT;
        array_unshift($data, $template);
        return $data;
    }

    function vc_faq_template($data) {
        $template               = array();
        $template['name']       = __( '[Automotive] FAQ Page', 'automotive' );
        $template['content']    = <<<CONTENT
            [vc_row][vc_column width="1/1"][faq categories="Electrical,Engine,Mechanical,Navigation,Sunroof,Stereo,Wiring" sort_text="Sort FAQ by:"][toggle title="Nam sollicitudin neque eu nibh pharetra mollis mauris in nisi rhoncus?" categories="Electrical,Navigational,Wiring"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Onvallis odio nulla vulputate orci ut libero suscipit condimentum nunc nibh?" categories="Engine,Sunroof,Wiring"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Porta auctor adipiscing massa maecenas sem mi, vestibulum id lectus non?" categories="Electrical,Mechanical,Wiring"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Mauris in nisi elit maecenas at metus rhoncus, facilisis tellus at, quis felis pretium orci?" categories="Mechanical,Sunroof,Navigational"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Donec adipiscing tincidunt rutrum iaculis sapien nec porta ment yehu?" categories="Navigational,Stereo,Sunroof"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa maecenas sem mi?" categories="Engine,Navigational,Stereo"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Duis facilisis dapibus enim, ac venenatis nibh mattis in cras eu condimentum lacus?" categories="Engine,Navigational,Navigational"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Quisque posuere tincidunt convallis ut viverra neque non diam tempor, id tinciunt mauris cursus?" categories="Electrical,Sunroof,Wiring"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][toggle title="Suscipit mattis viverra cras sit amet odio sit amet dui aliquam tempus a ultrices felis?" categories="Mechanical,Engine,Stereo"]Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh libero,
    consequat sit amet nisl vitae, suscipit gravida mi. Nam auctor viverra
    sodales. Quisque posuere tincidunt convallis. Ut viverra neque non diam
    tempor, id tincidunt mauris cursus. Donec suscipit mattis viverra. Cras sit
    amet odio sit amet dui aliquam tempus a ultrices felis. Proin sed imperdiet
    ipsum, ultrices posuere leo.

    Duis facilisis dapibus enim, ac venenatis nibh mattis in. Cras eu
    condimentum lacus, ac ultricies leo. Nunc sodales ipsum a suscipit.

    Mauris tincidunt rutrum auctor. <a href="#">Vivamus a nunc ac augue scelerisque dapibus</a> ut sed augue. Pellentesque fermentum orci in
    velit pharetra, non lobortis sapien suscipit. Aenean sem nulla, dignissim et bibendum et, consequat in nibh.

    Nam sollicitudin neque eu nibh pharetra mollis. Mauris in nisi elit. Maecenas at metus rhoncus, facilisis tellus at, pretium orci.
    Vivamus consectetur sem eget neque dignissim, sit amet sodales urna mattis. Vivamus ut semper dolor. Suspendisse tempus,
    dolor vel eleifend vestibulum, nulla eros elementum ligula, ac bibendum mi ipsum quis felis. Donec adipiscing iaculis sapien
    nec porta. Aliquam tellus leo, posuere ut magna porta, auctor adipiscing massa. Maecenas sem mi, vestibulum id lectus non,
    placerat rhoncus dui.[/toggle][/faq][/vc_column][/vc_row]
CONTENT;
        array_unshift($data, $template);
        return $data;
    }

    function vc_our_team($data) {
        $template               = array();
        $template['name']       = __( '[Automotive] Our Team Page', 'automotive' );
        $template['content']    = <<<CONTENT
            [vc_row css=".vc_custom_1410358708453{margin-bottom: 10px !important;}"][vc_column width="1/1"][vc_column_text]
<h3>MEET THE MANAGEMENT</h3>
[/vc_column_text][/vc_column][/vc_row][vc_row css=".vc_custom_1410359107639{margin-bottom: 70px !important;}"][vc_column width="1/3"][person name="William Dean" position="Chief Executive Officer / CEO" phone="1-800-123-4567 - Extension 114" cell_phone="1-902-361-7714" email="william@automotivetemplate.com" facebook="#" twitter="#" linkedin="#" flickr="#" google="#" img="109" hoverimg="108"]Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor aenean massa. Cum sociis numquasa mode tempora posuere feugiat.[/person][/vc_column][vc_column width="1/3"][person name="Leah Jennings" position="Chief Financial Officer / CEO" phone="1-800-123-4567 - Extension 107" cell_phone="1-902-342-0864" email="leah@automotivetemplate.com" facebook="#" twitter="#" linkedin="#" img="111" hoverimg="110"]Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor aenean massa. Cum sociis numquasa mode tempora posuere feugiat.[/person][/vc_column][vc_column width="1/3"][person name="Zachary Hale" position="Lead Sales Manager" phone="1-800-123-4567 - Extension 119" cell_phone="1-902-832-3702" email="zachary@automotivetemplate.com" facebook="#" twitter="#" linkedin="#" google="#" img="113" hoverimg="112"]Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor aenean massa. Cum sociis numquasa mode tempora posuere feugiat.[/person][/vc_column][/vc_row][vc_row css=".vc_custom_1410358730115{margin-bottom: 10px !important;}"][vc_column width="1/1"][vc_column_text]
<h3>MEET OUR SALES TEAM</h3>
[/vc_column_text][/vc_column][/vc_row][vc_row css=".vc_custom_1410359126831{margin-bottom: 70px !important;}"][vc_column width="1/4"][person name="Luca Sanderson" position="Sales Representative" phone="1-800-123-4567 - Extension 105" cell_phone="1-902-544-4415" email="luca@automotivetemplate.com" facebook="#" twitter="#" linkedin="#" google="#" img="115" hoverimg="114"]Lorem ipsum dolor sit amet, paleotousia consectetuer adipiscing elit. Aenean com.[/person][/vc_column][vc_column width="1/4"][person name="Abby Myers" position="Sales Representative" phone="1-800-123-4567 - Extension 123" cell_phone="1-902-361-7267" email="abby@automotivetemplate.com" facebook="#" twitter="#" linkedin="#" google="#" flickr="#" img="117" hoverimg="116"]Lorem ipsum dolor sit amet, paleotousia consectetuer adipiscing elit. Aenean com.[/person][/vc_column][vc_column width="1/4"][person name="Connor Wyatt" position="Sales Representative" phone="1-800-123-4567 - Extension 111" cell_phone="1-902-544-4415" email="connor@automotivetemplate.com" facebook="#" twitter="#" linkedin="#" flickr="#" img="119" hoverimg="118"]Lorem ipsum dolor sit amet, paleotousia consectetuer adipiscing elit. Aenean com.[/person][/vc_column][vc_column width="1/4"][person name="Sarah Thomas" position="Sales Representative" phone="1-800-123-4567 - Extension 108" cell_phone="1-902-544-4415" email="sarah@automotivetemplate.com" facebook="#" twitter="#" linkedin="#" img="121" hoverimg="120"]Lorem ipsum dolor sit amet, paleotousia consectetuer adipiscing elit. Aenean com.[/person][/vc_column][/vc_row][vc_row css=".vc_custom_1410447322744{margin-bottom: 60px !important;}"][vc_column width="1/1"][vc_column_text]
<h4 style="color: #c7081b; font-size: 24px;"><span style="font-weight: 800;">SEARCH</span> OUR INVENTORY</h4>
[/vc_column_text][search_inventory_box][/vc_column][/vc_row][vc_row el_class="fullwidth_element bottom_element"][vc_column width="1/1"][auto_google_map height="390" map_style="JTVCJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJsYW5kc2NhcGUlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscyUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJ0cmFuc2l0JTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9mZiUyMiU3RCU1RCU3RCUyQyU3QiUyMmZlYXR1cmVUeXBlJTIyJTNBJTIycG9pJTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9mZiUyMiU3RCU1RCU3RCUyQyU3QiUyMmZlYXR1cmVUeXBlJTIyJTNBJTIyd2F0ZXIlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscyUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJyb2FkJTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMuaWNvbiUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyc3R5bGVycyUyMiUzQSU1QiU3QiUyMmh1ZSUyMiUzQSUyMiUyM0YwRjBGMCUyMiU3RCUyQyU3QiUyMnNhdHVyYXRpb24lMjIlM0EtMTAwJTdEJTJDJTdCJTIyZ2FtbWElMjIlM0EyLjE1JTdEJTJDJTdCJTIybGlnaHRuZXNzJTIyJTNBMTIlN0QlNUQlN0QlMkMlN0IlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMnJvYWQlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscy50ZXh0LmZpbGwlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9uJTIyJTdEJTJDJTdCJTIybGlnaHRuZXNzJTIyJTNBMjQlN0QlNUQlN0QlMkMlN0IlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMnJvYWQlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmdlb21ldHJ5JTIyJTJDJTIyc3R5bGVycyUyMiUzQSU1QiU3QiUyMmxpZ2h0bmVzcyUyMiUzQTU3JTdEJTVEJTdEJTVE"][/vc_column][/vc_row]
CONTENT;
        array_unshift($data, $template);
        return $data;
    }

    function vc_pricing_tables($data) {
        $template               = array();
        $template['name']       = __( '[Automotive] Pricing Tables Page', 'automotive' );
        $template['content']    = <<<CONTENT
            [vc_row el_class="margin-bottom-none"][vc_column width="1/1"][vc_column_text]
<div class="pricing_dept margin-bottom-50">
<h2 class="margin-bottom-25"><span style="color: #2d2d2d;">Choose the pricing option that best suits your business</span></h2>
Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Proin condimentum felis ut ultrices congue. Quisque in lacus condimentum, fringilla nisi commodo, faucibus velit. Integer fermentum mauris adipiscing faucibus tristique. Praesent iaculis sed tellus quis porta. Nulla porta tincidunt libero. Ut nec purus ut lectus convallis pellentesque ac non enim. Etiam suscipit eleifend tincidunt. Praesent volutpat, tortor ac molestie imperdiet, nisi quam imperdiet elit, id dapibus lacus felis sed massa. Cras ultrices enim in sagittis posuere. Vestibulum ac ipsum vitae lectus pretium vestibulum ac rutrum felis. Donec consequat lacus eu mi porta ornare. Duis eget velit ac felis sollicitudin sagittis.

</div>
<div class="pricing_wrapper">
<h3 class="margin-top-20 margin-bottom-30"><span style="color: #2d2d2d;">3 Column Pricing Layout</span></h3>
</div>
[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/3"][pricing_table title="Standard" price="299.99" often="mo" button="Order Now"][pricing_option]Manual Transmission[/pricing_option][pricing_option]4 Cylinder Engine[/pricing_option][pricing_option]60 MPG[/pricing_option][pricing_option]6 Seats[/pricing_option][pricing_option]3 Year Warranty[/pricing_option][/pricing_table][/vc_column][vc_column width="1/3"][pricing_table title="Professional" price="399.99" often="mo" button="Order Now"][pricing_option]Manual Transmission[/pricing_option][pricing_option]6 Cylinder Engine[/pricing_option][pricing_option]45 MPG[/pricing_option][pricing_option]5 Seats[/pricing_option][pricing_option]4 Year Warranty[/pricing_option][/pricing_table][/vc_column][vc_column width="1/3"][pricing_table title="Premium" price="499.99" often="mo" button="Order Now"][pricing_option]Automatic Transmission[/pricing_option][pricing_option]8 Cylinder Engine[/pricing_option][pricing_option]30 MPG[/pricing_option][pricing_option]4 Seats[/pricing_option][pricing_option]5 Year Warranty[/pricing_option][/pricing_table][/vc_column][/vc_row][vc_row][vc_column width="1/1"][vc_column_text]
<div class="pricing_wrapper">
<h3 class="margin-bottom-none margin-top-30"><span style="color: #2d2d2d;">4 Column Pricing Layout</span></h3>
</div>
[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/4"][pricing_table title="Standard" price="299.99" often="mo" button="Order Now"][pricing_option]Manual Transmission[/pricing_option][pricing_option]4 Cylinder Engine[/pricing_option][pricing_option]60 MPG[/pricing_option][pricing_option]6 Seats[/pricing_option][pricing_option]3 Year Warranty[/pricing_option][/pricing_table][/vc_column][vc_column width="1/4"][pricing_table title="Professional" price="399.99" often="mo" button="Order Now"][pricing_option]Manual Transmission[/pricing_option][pricing_option]6 Cylinder Engine[/pricing_option][pricing_option]45 MPG[/pricing_option][pricing_option]5 Seats[/pricing_option][pricing_option]4 Year Warranty[/pricing_option][/pricing_table][/vc_column][vc_column width="1/4"][pricing_table title="Premium" price="499.99" often="mo" button="Order Now"][pricing_option]Automatic Transmission[/pricing_option][pricing_option]8 Cylinder Engine[/pricing_option][pricing_option]30 MPG[/pricing_option][pricing_option]4 Seats[/pricing_option][pricing_option]5 Year Warranty[/pricing_option][/pricing_table][/vc_column][vc_column width="1/4"][pricing_table title="Platinum" price="599.99" often="mo" button="Order Now"][pricing_option]Automatic Transmission[/pricing_option][pricing_option]12 Cylinder Engine[/pricing_option][pricing_option]15 MPG[/pricing_option][pricing_option]2 Seats[/pricing_option][pricing_option]7 Year Warranty[/pricing_option][/pricing_table][/vc_column][/vc_row]
CONTENT;
        array_unshift($data, $template);
        return $data;
    }

    function vc_services_template($data) {
        $template               = array();
        $template['name']       = __( '[Automotive] Service Page', 'automotive' );
        $template['content']    = <<<CONTENT
            [vc_row css=".vc_custom_1410361334750{margin-bottom: 70px !important;}"][vc_column width="2/3"][vc_column_text]
    <h3>WHAT CAN WE DO FOR YOU?</h3>
    [/vc_column_text][vc_column_text][dropcaps]R[/dropcaps]obem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.

    Sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_column_text]
    <h3>THINGS TO CONSIDER</h3>
    [/vc_column_text][list style="checkboxes"][list_item]Fully responsive and ready for all mobile devices[/list_item][list_item]Integrated inventory management system[/list_item][list_item]Simple option panel and very easy to customize[/list_item][list_item]Search engine optimization (SEO) is 100% built-in[/list_item][list_item]Revolution Slider is included for product marketing[/list_item][list_item]Tons of shortcodes for quick and easy add-ons[/list_item][list_item]Fully backed by our dedicated support team[/list_item][/list][/vc_column][/vc_row][vc_row][vc_column width="1/1"][vc_column_text]
    <h2 class="margin-top-none" style="letter-spacing: -1.5px;"><span style="color: #c7081b;">Highlight Your <strong>Featured Services</strong></span></h2>
    [/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/4"][featured_panel title="Mobile Enhanced" icon="1463" hover_icon="1462"]Sed ut perspiciatis unde om natus error sit volup atem aperiam, eaque ipsa quae[/featured_panel][/vc_column][vc_column width="1/4"][featured_panel title="Platform Tested" icon="1465" hover_icon="1464"]Sed ut perspiciatis unde om natus error sit volup atem aperiam, eaque ipsa quae[/featured_panel][/vc_column][vc_column width="1/4"][featured_panel title="Social Ready" icon="1467" hover_icon="1466"]Sed ut perspiciatis unde om natus error sit volup atem aperiam, eaque ipsa quae[/featured_panel][/vc_column][vc_column width="1/4"][featured_panel title="Video Integration" icon="1469" hover_icon="1468"]Sed ut perspiciatis unde om natus error sit volup atem aperiam, eaque ipsa quae[/featured_panel][/vc_column][/vc_row][vc_row el_class="fullwidth_element" css=".vc_custom_1410362510858{margin-bottom: 30px !important;}"][vc_column width="1/1"][parallax_section title="Dealership Statistics" velocity="-.3" offset="0" image="1475" overlay_color="rgba(255,255,255,0.65)" text_color="#2d2d2d"]
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 margin-vertical-60 xs-margin-vertical-20"><i class="fa fa-car"></i><span class="animate_number margin-vertical-15"><span class="number">2,000</span>
    </span>Cars Sold</div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 margin-vertical-60 xs-margin-vertical-20"><i class="fa fa-shopping-cart"></i><span class="animate_number margin-vertical-15">$<span class="number">750,000</span>
    </span>Amount Sold</div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 margin-vertical-60 xs-margin-vertical-20"><i class="fa fa-users"></i><span class="animate_number margin-vertical-15"><span class="number">100</span>%
    </span>Customer Satisfaction</div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 margin-vertical-60 xs-margin-vertical-20"><i class="fa fa-tint"></i><span class="animate_number margin-vertical-15"><span class="number">3,600</span>
    </span>Oil Changes</div>
    [/parallax_section][/vc_column][/vc_row][vc_row css=".vc_custom_1410362518410{margin-bottom: 20px !important;}"][vc_column width="1/1"][vc_column_text]
    <h2 class="margin-top-none" style="letter-spacing: -1.5px;"><span style="color: #c7081b;">Easily Layout Your <strong>Detailed Services</strong></span></h2>
    [/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/3"][detailed_panel title="Highly Customizable" icon="fa fa-wrench"]Sociis natoque penatibus et magnis dis parturient etah montes, nascetur ridiculus mus. Donec quam felis, A ultricies nec, pellentesque eu, pretium quis, sem. Cum sociis natoque penatibus et magnis dis parturient nas.[/detailed_panel][/vc_column][vc_column width="1/3"][detailed_panel title="Award Winning" icon="fa fa-trophy"]Sociis natoque penatibus et magnis dis parturient etah montes, nascetur ridiculus mus. Donec quam felis, A ultricies nec, pellentesque eu, pretium quis, sem. Cum sociis natoque penatibus et magnis dis parturient nas.[/detailed_panel][/vc_column][vc_column width="1/3"][detailed_panel title="Music To Your Ears" icon="fa fa-music"]Sociis natoque penatibus et magnis dis parturient etah montes, nascetur ridiculus mus. Donec quam felis, A ultricies nec, pellentesque eu, pretium quis, sem. Cum sociis natoque penatibus et magnis dis parturient nas.[/detailed_panel][/vc_column][/vc_row][vc_row css=".vc_custom_1410366970608{margin-bottom: 70px !important;}"][vc_column width="1/3"][detailed_panel title="Easy To Work With" icon="fa fa-coffee"]Sociis natoque penatibus et magnis dis parturient etah montes, nascetur ridiculus mus. Donec quam felis, A ultricies nec, pellentesque eu, pretium quis, sem. Cum sociis natoque penatibus et magnis dis parturient nas.[/detailed_panel][/vc_column][vc_column width="1/3"][detailed_panel title="Ultra Responsive" icon="fa fa-truck"]Sociis natoque penatibus et magnis dis parturient etah montes, nascetur ridiculus mus. Donec quam felis, A ultricies nec, pellentesque eu, pretium quis, sem. Cum sociis natoque penatibus et magnis dis parturient nas.[/detailed_panel][/vc_column][vc_column width="1/3"][detailed_panel title="Flexible Framework" icon="fa fa-cog"]Sociis natoque penatibus et magnis dis parturient etah montes, nascetur ridiculus mus. Donec quam felis, A ultricies nec, pellentesque eu, pretium quis, sem. Cum sociis natoque penatibus et magnis dis parturient nas.[/detailed_panel][/vc_column][/vc_row][vc_row][vc_column width="1/1"][vc_column_text]
    <h3>SOME OF OUR FEATURED BRANDS</h3>
    [/vc_column_text][featured_brands][brand_logo img="1425" hoverimg="1424"][/brand_logo][brand_logo img="1421" hoverimg="1420"][/brand_logo][brand_logo img="1427" hoverimg="1426"][/brand_logo][brand_logo img="1423" hoverimg="1422"][/brand_logo][brand_logo img="1431" hoverimg="1430"][/brand_logo][brand_logo img="1429" hoverimg="1428"][/brand_logo][brand_logo img="1425" hoverimg="1424"][/brand_logo][brand_logo img="1421" hoverimg="1420"][/brand_logo][brand_logo img="1427" hoverimg="1426"][/brand_logo][brand_logo img="1423" hoverimg="1422"][/brand_logo][brand_logo img="1431" hoverimg="1430"][/brand_logo][brand_logo img="1429" hoverimg="1428"][/brand_logo][/featured_brands][/vc_column][/vc_row]
CONTENT;
        array_unshift($data, $template);
        return $data;
    }

    function vc_homepage_template($data) {
        $template               = array();
        $template['name']       = __( '[Automotive] Homepage', 'automotive' );
        $template['content']    = <<<CONTENT
            [vc_row el_class="padding-bottom-40 margin-bottom-none"][vc_column width="1/3"][flipping_card image="316" larger_img="315" title="Race Ready" link="url:http%3A%2F%2Fdev.themesuite.com%2Fautomotive%2F%3Fpage_id%3D36|title:About%20Us|"][vc_column_text css=".vc_custom_1410531577112{margin-top: 20px !important;}"]
<h3 class="margin-bottom-10">FACTORY READY FOR TRACK DAY</h3>
<p class="margin-bottom-none">Sea veniam lucilius neglegentur ad, an per sumo volum voluptatibus. Qui cu everti repudiare. Eam ut cibo nobis aperiam, elit qualisque at cum. Possit antiopam id est. Illud delicata ea mel, sed novum mucius id. Nullam qua.</p>
[/vc_column_text][/vc_column][vc_column width="1/3"][flipping_card image="788" larger_img="787" title="Family Oriented" link="url:http%3A%2F%2Fdev.themesuite.com%2Fautomotive%2F%3Fpage_id%3D36|title:About%20Us|"][vc_column_text css=".vc_custom_1410531618564{margin-top: 20px !important;}"]
<h3 class="margin-bottom-10">A SPORT UTILITY FOR THE FAMILY</h3>
<p class="margin-bottom-none">Cum ut tractatos imperdiet, no tamquam facilisi qui. Eum tibique consectetuer in, an legimus referrentur vis, vocent deseruisse ex mel. Sed te idque graecis. Vel ne libris dolores, in mel graece dolorum.</p>
[/vc_column_text][/vc_column][vc_column width="1/3"][flipping_card image="790" larger_img="789" title="Race Ready" link="url:http%3A%2F%2Fdev.themesuite.com%2Fautomotive%2F%3Fpage_id%3D36|title:About%20Us|"][vc_column_text css=".vc_custom_1410531639450{margin-top: 20px !important;}"]
<h3 class="margin-bottom-10">MAKE AN EXECUTIVE STATEMENT</h3>
<p class="margin-bottom-none">Te inermis cotidieque cum, sed ea utroque atomorum sadipscing. Qui id oratio everti scaevola, vim ea augue ponderum vituperatoribus, quo adhuc abhorreant omittantur ad. No his fierent perpetua consequat, et nis.</p>
[/vc_column_text][/vc_column][/vc_row][vc_row el_class="fullwidth_element margin-top-30 padding-bottom-40 margin-bottom-none"][vc_column width="1/1"][parallax_section velocity="-.3" offset="0" image="99" overlay_color="rgba(240,240,240,0.95)" text_color="#2d2d2d"][vc_row_inner el_class="margin-bottom-60"][vc_column_inner width="1/4"][featured_icon_box title="Results Driven" icon="fa fa-bar-chart-o"]Sed ut perspiciatis unde om nis natus error sit volup atem accusant dolorem que laudantium. Totam aperiam, eaque ipsa quae ai.[/featured_icon_box][/vc_column_inner][vc_column_inner width="1/4"][featured_icon_box title="Proven Technology" icon="fa fa-road"]Sed ut perspiciatis unde om nis natus error sit volup atem accusant dolorem que laudantium. Totam aperiam, eaque ipsa quae ai.[/featured_icon_box][/vc_column_inner][vc_column_inner width="1/4"][featured_icon_box title="Winning Culture" icon="fa fa-flag-checkered"]Sed ut perspiciatis unde om nis natus error sit volup atem accusant dolorem que laudantium. Totam aperiam, eaque ipsa quae ai.[/featured_icon_box][/vc_column_inner][vc_column_inner width="1/4"][featured_icon_box title="Top Performance" icon="fa fa-dashboard"]Sed ut perspiciatis unde om nis natus error sit volup atem accusant dolorem que laudantium. Totam aperiam, eaque ipsa quae ai.[/featured_icon_box][/vc_column_inner][/vc_row_inner][/parallax_section][/vc_column][/vc_row][vc_row el_class="margin-top-30 padding-bottom-40 margin-bottom-none"][vc_column width="1/2"][vc_column_text]
<h4 class="margin-top-none">[bolded]WELCOME[/bolded] TO YOUR NEW WEBSITE</h4>
[/vc_column_text][vc_column_text el_class="padding-bottom-40"]Lorem ipsum dolor sit amet, falli tollit cetero te eos. Ea ullum liber aperiri mi, impetus ate philosophia ad duo, quem regione ne ius. Vis quis lobortis dissentias ex, in du aft philosophia, malis necessitatibus no mei. Volumus sensibus qui ex, eum duis doming ad. Modo liberavisse eu mel, no viris prompta sit. Pro labore sadipscing et. Ne peax egat usu te mel <span class="alternate-font">vivendo scriptorem</span>. Pro labore sadipscing et. Ne pertinax egat usu te mel vivendo scriptorem.

Cum ut tractatos imperdiet, no tamquam facilisi qui. Eum tibique onsectetuer in, an referrentur vis, vocent deseruisse ex mel. Sed te <span class="alternate-font">idque graecis</span>. Vel ne libris dolores, mel graece mel vivendo scriptorem dolorum.[/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]
<h4 class="margin-top-none">[bolded]SEARCH[/bolded] OUR INVENTORY</h4>
[/vc_column_text][search_inventory_box column_1="Year,Make,Model,Body Style" column_2="Mileage,Transmission,Price,Search" min_max="Year,Mileage,Price" page_id="url:http%3A%2F%2Fdev.themesuite.com%2Fautomotive%2F%3Fpage_id%3D191|title:Wide%20Fullwidth|"][/vc_column][/vc_row][vc_row css=".vc_custom_1410982144071{margin-bottom: 0px !important;}" el_class="margin-top-30 padding-bottom-40 margin-bottom-none"][vc_column width="1/1"][vehicle_scroller title="Recent Vehicles" description="Browse through the vast selection of vehicles that have recently been added to our inventory." sort="Newest"][/vc_column][/vc_row][vc_row el_class="fullwidth_element margin-top-30 padding-bottom-40 margin-bottom-none"][vc_column width="1/1"][auto_google_map height="390" map_style="JTVCJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJsYW5kc2NhcGUlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscyUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJ0cmFuc2l0JTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9mZiUyMiU3RCU1RCU3RCUyQyU3QiUyMmZlYXR1cmVUeXBlJTIyJTNBJTIycG9pJTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9mZiUyMiU3RCU1RCU3RCUyQyU3QiUyMmZlYXR1cmVUeXBlJTIyJTNBJTIyd2F0ZXIlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscyUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyZmVhdHVyZVR5cGUlMjIlM0ElMjJyb2FkJTIyJTJDJTIyZWxlbWVudFR5cGUlMjIlM0ElMjJsYWJlbHMuaWNvbiUyMiUyQyUyMnN0eWxlcnMlMjIlM0ElNUIlN0IlMjJ2aXNpYmlsaXR5JTIyJTNBJTIyb2ZmJTIyJTdEJTVEJTdEJTJDJTdCJTIyc3R5bGVycyUyMiUzQSU1QiU3QiUyMmh1ZSUyMiUzQSUyMiUyM0YwRjBGMCUyMiU3RCUyQyU3QiUyMnNhdHVyYXRpb24lMjIlM0EtMTAwJTdEJTJDJTdCJTIyZ2FtbWElMjIlM0EyLjE1JTdEJTJDJTdCJTIybGlnaHRuZXNzJTIyJTNBMTIlN0QlNUQlN0QlMkMlN0IlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMnJvYWQlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmxhYmVscy50ZXh0LmZpbGwlMjIlMkMlMjJzdHlsZXJzJTIyJTNBJTVCJTdCJTIydmlzaWJpbGl0eSUyMiUzQSUyMm9uJTIyJTdEJTJDJTdCJTIybGlnaHRuZXNzJTIyJTNBMjQlN0QlNUQlN0QlMkMlN0IlMjJmZWF0dXJlVHlwZSUyMiUzQSUyMnJvYWQlMjIlMkMlMjJlbGVtZW50VHlwZSUyMiUzQSUyMmdlb21ldHJ5JTIyJTJDJTIyc3R5bGVycyUyMiUzQSU1QiU3QiUyMmxpZ2h0bmVzcyUyMiUzQTU3JTdEJTVEJTdEJTVE" longitude="-79.38" latitude="43.65" zoom="12" scrolling="false"][/vc_column][/vc_row][vc_row el_class="margin-top-30 padding-bottom-40 margin-bottom-none"][vc_column width="1/2" offset="vc_col-lg-2 vc_col-md-2 vc_col-xs-12"][icon_title title="Financing." icon="fa fa-tag"][/vc_column][vc_column width="1/2" offset="vc_col-lg-2 vc_col-md-2 vc_col-xs-12"][icon_title title="Warranty." icon="fa fa-cogs"][/vc_column][vc_column width="1/1" offset="vc_col-lg-4 vc_col-md-4 vc_col-xs-12" el_class="text-center"][vc_column_text]
<div class="small-block">
<h4 class="margin-bottom-25 margin-top-none">What are our Hours of Operation?</h4>
</div>
[/vc_column_text][hours_table title="Sales Department" mon="8:00am - 5:00pm" tue="8:00am - 9:00pm" wed="8:00am - 5:00pm" thu="8:00am - 9:00pm" fri="8:00am - 6:00pm" sat="9:00am - 5:00pm" sun="Closed"][hours_table title="Service Department" mon="8:00am - 5:00pm" tue="8:00am - 9:00pm" wed="8:00am - 5:00pm" thu="8:00am - 9:00pm" fri="8:00am - 6:00pm" sat="9:00am - 5:00pm" sun="Closed"][/vc_column][vc_column width="1/2" offset="vc_col-lg-2 vc_col-md-2 vc_col-xs-12"][icon_title title="About Us." icon="fa fa-users"][/vc_column][vc_column width="1/2" offset="vc_col-lg-2 vc_col-md-2 vc_col-xs-12"][icon_title title="Find Us." icon="fa fa-map-marker"][/vc_column][/vc_row][vc_row el_class="fullwidth_element bottom_element margin-top-30"][vc_column width="1/1"][parallax_section velocity="-.3" offset="-300" image="100" overlay_color="rgba(0,0,0,0.65)" text_color="#ffffff"][vc_row_inner][vc_column_inner width="1/4"][animated_numbers icon="fa fa-car" number="2000" alignment="center"][vc_column_text]
<p style="text-align: center;"><span style="color: #ffffff;">Cars Sold</span></p>
[/vc_column_text][/vc_column_inner][vc_column_inner width="1/4"][animated_numbers icon="fa fa-money" number="750000" before_number="$" alignment="center"][vc_column_text]
<p style="text-align: center;"><span style="color: #ffffff;">Amount Sold</span></p>
[/vc_column_text][/vc_column_inner][vc_column_inner width="1/4"][animated_numbers icon="fa fa-users" number="100" after_number="%" alignment="center"][vc_column_text]
<p style="text-align: center;"><span style="color: #ffffff;">Customer Satisfaction</span></p>
[/vc_column_text][/vc_column_inner][vc_column_inner width="1/4"][animated_numbers icon="fa fa-tint" number="3600" alignment="center"][vc_column_text]
<p style="text-align: center;"><span style="color: #ffffff;">Oil Changes</span></p>
[/vc_column_text][/vc_column_inner][/vc_row_inner][/parallax_section][/vc_column][/vc_row]
CONTENT;
        array_unshift($data, $template);
        return $data;
    }
}

if(!function_exists("pretty_excerpt")){
    function pretty_excerpt($text) {
        $raw_excerpt = $text;
        if ( '' == $text ) {
            $text = get_the_content('');
            $text = strip_shortcodes( $text );
            $text = apply_filters('the_content', $text);
            $text = str_replace(']]>', ']]&gt;', $text);
            $text = strip_tags($text, '<p><br><em><strong><i><b><img><ul><ol><li>');
            $excerpt_length = apply_filters('excerpt_length', 155);
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
            $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
            if ( count($words) > $excerpt_length ) {
                array_pop($words);
                $text = implode(' ', $words);
                $text = $text . $excerpt_more;
            } else {
                $text = implode(' ', $words);
            }

            $text = force_balance_tags( $text );
        }
        return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
    }
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'pretty_excerpt');
?>