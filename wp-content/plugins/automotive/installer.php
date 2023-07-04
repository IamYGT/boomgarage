<?php

// simple installer
function automotive_demo_content_installer(){
    error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

    // import step 1 || Automotive XML
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 1){
        include_once("importer/xml_importer.php");

        $args = array(
                'file'        => LISTING_HOME . 'demo_content/1.xml',
                'map_user_id' => 1
        );


        ob_start();
            auto_import( $args );
        $return = ob_get_clean();

        echo (empty($return) ? "success" : "some_wrong<!--" . $return . "-->");
    }

    // import step 2 || Automotive XML
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 2){
        include_once("importer/xml_importer.php");

        $args = array(
                'file'        => LISTING_HOME . 'demo_content/2.xml',
                'map_user_id' => 1
        );


        ob_start();
            auto_import( $args );
        $return = ob_get_clean();

        echo (empty($return) ? "success" : "some_wrong<!--" . $return . "-->");
    }

    // import step 3 || Automotive XML
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 3){
        include_once("importer/xml_importer.php");

        $args = array(
                'file'        => LISTING_HOME . 'demo_content/3.xml',
                'map_user_id' => 1
        );


        ob_start();
            auto_import( $args );
        $return = ob_get_clean();

        echo (empty($return) ? "success" : "some_wrong<!--" . $return . "-->");
    }

    // import step 4 || Automotive XML
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 4){
        include_once("importer/xml_importer.php");

        $args = array(
                'file'        => LISTING_HOME . 'demo_content/4.xml',
                'map_user_id' => 1
        );


        ob_start();
            auto_import( $args );
        $return = ob_get_clean();

        echo (empty($return) ? "success" : "some_wrong<!--" . $return . "-->");
    }

    // import step 5 || Automotive XML
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 5){
        include_once("importer/xml_importer.php");

        $args = array(
                'file'        => LISTING_HOME . 'demo_content/5.xml',
                'map_user_id' => 1
        );


        ob_start();
            auto_import( $args );
        $return = ob_get_clean();

        echo (empty($return) ? "success" : "some_wrong<!--" . $return . "-->");
    }

    // import step 6 || Listing Panel Options
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 6){
        global $wpdb;

        // import listing categories first
        $demo_content = unserialize('a:18:{s:4:"year";a:6:{s:8:"singular";s:4:"Year";s:6:"plural";s:5:"Years";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:6:{i:0;s:4:"2014";i:1;s:4:"2013";i:2;s:4:"2012";i:3;s:4:"2010";i:4;s:4:"2009";i:5;s:4:"2015";}}s:4:"make";a:6:{s:8:"singular";s:4:"Make";s:6:"plural";s:5:"Makes";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:1:{i:0;s:7:"Porsche";}}s:5:"model";a:6:{s:8:"singular";s:5:"Model";s:6:"plural";s:6:"Models";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:5:{i:0;s:7:"Carrera";i:1;s:3:"GTS";i:2;s:7:"Cayenne";i:3;s:7:"Boxster";i:4;s:5:"Macan";}}s:10:"body_style";a:6:{s:8:"singular";s:10:"Body Style";s:6:"plural";s:11:"Body Styles";s:10:"filterable";s:1:"1";s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:3:{i:0;s:11:"Convertible";i:1;s:5:"Sedan";i:2;s:22:"Sports Utility Vehicle";}}s:7:"mileage";a:6:{s:8:"singular";s:7:"Mileage";s:6:"plural";s:8:"Mileages";s:10:"filterable";s:1:"1";s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:4:"&lt;";s:5:"terms";a:10:{i:0;s:5:"10000";i:1;s:5:"20000";i:2;s:5:"30000";i:3;s:5:"40000";i:4;s:5:"50000";i:5;s:5:"60000";i:6;s:5:"70000";i:7;s:5:"80000";i:8;s:5:"90000";i:9;s:6:"100000";}}s:12:"transmission";a:6:{s:8:"singular";s:12:"Transmission";s:6:"plural";s:13:"Transmissions";s:10:"filterable";s:1:"1";s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:8:{i:0;s:14:"6-Speed Manual";i:1;s:17:"5-Speed Automatic";i:2;s:17:"8-Speed Automatic";i:3;s:17:"6-Speed Semi-Auto";i:4;s:17:"6-Speed Automatic";i:5;s:14:"5-Speed Manual";i:6;s:17:"8-Speed Tiptronic";i:7;s:11:"7-Speed PDK";}}s:12:"fuel_economy";a:6:{s:8:"singular";s:12:"Fuel Economy";s:6:"plural";s:14:"Fuel Economies";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:4:"&lt;";s:5:"terms";a:6:{i:0;s:2:"10";i:1;s:2:"20";i:2;s:2:"30";i:3;s:2:"40";i:4;s:2:"50";i:5;s:2:"50";}}s:9:"condition";a:6:{s:8:"singular";s:9:"Condition";s:6:"plural";s:10:"Conditions";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:3:{i:0;s:9:"Brand New";i:1;s:13:"Slightly Used";i:2;s:4:"Used";}}s:8:"location";a:6:{s:8:"singular";s:8:"Location";s:6:"plural";s:9:"Locations";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:1:"=";s:5:"terms";a:1:{i:0;s:7:"Toronto";}}s:5:"price";a:8:{s:8:"singular";s:5:"Price";s:6:"plural";s:6:"Prices";s:10:"filterable";s:1:"1";s:14:"use_on_listing";i:0;s:13:"compare_value";s:4:"&lt;";s:8:"currency";s:1:"1";s:10:"link_value";s:5:"price";s:5:"terms";a:10:{i:0;s:5:"10000";i:1;s:5:"20000";i:2;s:5:"30000";i:3;s:5:"40000";i:4;s:5:"50000";i:5;s:5:"60000";i:6;s:5:"70000";i:7;s:5:"80000";i:8;s:5:"90000";i:9;s:6:"100000";}}s:10:"drivetrain";a:6:{s:8:"singular";s:10:"Drivetrain";s:6:"plural";s:11:"Drivetrains";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:4:{i:0;s:3:"AWD";i:1;s:3:"RWD";i:2;s:3:"4WD";i:3;s:14:"Drivetrain RWD";}}s:6:"engine";a:6:{s:8:"singular";s:6:"Engine";s:6:"plural";s:7:"Engines";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:9:{i:0;s:7:"3.6L V6";i:1;s:17:"4.8L V8 Automatic";i:2;s:13:"4.8L V8 Turbo";i:3;s:7:"4.8L V8";i:4;s:7:"3.8L V6";i:5;s:18:"2.9L Mid-Engine V6";i:6;s:18:"3.4L Mid-Engine V6";i:7;s:14:"3.0L V6 Diesel";i:8;s:13:"3.0L V6 Turbo";}}s:14:"exterior_color";a:6:{s:8:"singular";s:14:"Exterior Color";s:6:"plural";s:15:"Exterior Colors";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:10:{i:0;s:13:"Racing Yellow";i:1;s:23:"Rhodium Silver Metallic";i:2;s:16:"Peridot Metallic";i:3;s:17:"Ruby Red Metallic";i:4;s:5:"White";i:5;s:18:"Aqua Blue Metallic";i:6;s:23:"Chestnut Brown Metallic";i:7;s:10:"Guards Red";i:8;s:18:"Dark Blue Metallic";i:9;s:18:"Lime Gold Metallic";}}s:14:"interior_color";a:6:{s:8:"singular";s:14:"Interior Color";s:6:"plural";s:15:"Interior Colors";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:10:{i:0;s:14:"Interior Color";i:1;s:10:"Agate Grey";i:2;s:15:"Alcantara Black";i:3;s:11:"Marsala Red";i:4;s:5:"Black";i:5;s:13:"Platinum Grey";i:6;s:11:"Luxor Beige";i:7;s:13:"Platinum Grey";i:8;s:21:"Black / Titanium Blue";i:9;s:10:"Agate Grey";}}s:3:"mpg";a:7:{s:8:"singular";s:3:"MPG";s:6:"plural";s:3:"MPG";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:10:"link_value";s:3:"mpg";s:5:"terms";a:9:{i:0;s:16:"19 city / 27 hwy";i:1;s:16:"16 city / 24 hwy";i:2;s:15:"15 city /21 hwy";i:3;s:16:"15 city / 21 hwy";i:4;s:16:"18 city / 26 hwy";i:5;s:16:"16 city / 24 hwy";i:6;s:16:"20 city / 30 hwy";i:7;s:16:"20 City / 28 Hwy";i:8;s:16:"19 city / 29 hwy";}}s:12:"stock_number";a:6:{s:8:"singular";s:12:"Stock Number";s:6:"plural";s:13:"Stock Numbers";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:12:{i:0;s:6:"590388";i:1;s:6:"590524";i:2;s:6:"590512";i:3;s:6:"590499";i:4;s:6:"590435";i:5;s:6:"590421";i:6;s:6:"590476";i:7;s:6:"590271";i:8;s:6:"590497";i:9;s:5:"16115";i:10;s:6:"590124";i:11;s:6:"590562";}}s:10:"vin_number";a:6:{s:8:"singular";s:10:"VIN Number";s:6:"plural";s:11:"VIN Numbers";s:10:"filterable";i:0;s:14:"use_on_listing";s:1:"1";s:13:"compare_value";s:1:"=";s:5:"terms";a:12:{i:0;s:17:"WP0CB2A92CS376450";i:1;s:17:"WP0AB2A74AL092462";i:2;s:17:"WP1AD29P09LA73659";i:3;s:17:"WP0AB2A74AL079264";i:4;s:17:"WP0CB2A92CS754706";i:5;s:17:"WP0CA2A96AS740274";i:6;s:17:"WP0AB2A74AL060306";i:7;s:17:"WP0AB2A74AL060306";i:8;s:17:"WP1AD29P09LA65818";i:9;s:17:"WP0AB2E81EK190171";i:10;s:17:"WP0CB2A92CS377324";i:11;s:17:"WP0CT2A92CS326491";}}s:7:"options";a:1:{s:5:"terms";a:40:{i:0;s:23:"Adaptive Cruise Control";i:1;s:7:"Airbags";i:2;s:16:"Air Conditioning";i:3;s:12:"Alarm System";i:4;s:21:"Anti-theft Protection";i:5;s:15:"Audio Interface";i:6;s:25:"Automatic Climate Control";i:7;s:20:"Automatic Headlights";i:8;s:15:"Auto Start/Stop";i:9;s:19:"Bi-Xenon Headlights";i:10;s:19:"Bluetooth® Handset";i:11;s:21:"BOSE® Surround Sound";i:12;s:26:"Burmester® Surround Sound";i:13;s:18:"CD/DVD Autochanger";i:14;s:9:"CDR Audio";i:15;s:14:"Cruise Control";i:16;s:21:"Direct Fuel Injection";i:17;s:22:"Electric Parking Brake";i:18;s:10:"Floor Mats";i:19;s:18:"Garage Door Opener";i:20;s:15:"Leather Package";i:21;s:25:"Locking Rear Differential";i:22;s:20:"Luggage Compartments";i:23;s:19:"Manual Transmission";i:24;s:17:"Navigation Module";i:25;s:15:"Online Services";i:26;s:10:"ParkAssist";i:27;s:21:"Porsche Communication";i:28;s:14:"Power Steering";i:29;s:16:"Reversing Camera";i:30;s:20:"Roll-over Protection";i:31;s:12:"Seat Heating";i:32;s:16:"Seat Ventilation";i:33;s:18:"Sound Package Plus";i:34;s:20:"Sport Chrono Package";i:35;s:22:"Steering Wheel Heating";i:36;s:24:"Tire Pressure Monitoring";i:37;s:25:"Universal Audio Interface";i:38;s:20:"Voice Control System";i:39;s:14:"Wind Deflector";}}}');
        $update       = update_option("listing_categories", $demo_content);

        $theme_panel_options = 'a:99:{s:8:"last_tab";s:0:"";s:21:"vehicle_history_label";s:7:"Carfax2";s:15:"vehicle_history";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:21:"fuel_efficiency_image";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:23:"inventory_primary_title";s:17:"Inventory Listing";s:25:"inventory_secondary_title";s:46:"Powerful Inventory Marketing, Fully Integrated";s:14:"inventory_page";s:0:"";s:17:"inventory_no_sold";s:0:"";s:15:"comparison_page";s:0:"";s:15:"listings_amount";s:1:"0";s:10:"sale_value";s:0:"";s:13:"tax_label_box";s:14:"Plus Sales Tax";s:14:"tax_label_page";s:22:"Plus Taxes & Licensing";s:14:"car_comparison";s:1:"1";s:24:"inventory_listing_toggle";s:1:"1";s:19:"thumbnail_slideshow";s:1:"1";s:21:"additional_categories";a:3:{i:0;s:9:"Certified";i:1;s:15:"CARFAX Verified";i:2;s:9:"Brand New";}s:15:"currency_symbol";s:0:"";s:18:"currency_placement";s:1:"1";s:18:"currency_separator";s:0:"";s:13:"email_success";s:19:"The email was sent.";s:13:"email_failure";s:23:"The email was not sent.";s:10:"email_spam";s:52:"The email you are trying to send is considered spam.";s:14:"friend_subject";s:42:"{name} wants you to check this vehicle out";s:13:"friend_layout";s:60:"I want you check this vehicle out {table} Message: {message}";s:8:"drive_to";s:0:"";s:13:"drive_subject";s:28:"Scheduled Test Drive Request";s:12:"drive_layout";s:45:"Information
    
    {table}
    
    Vehicle: {link}";s:7:"info_to";s:0:"";s:12:"info_subject";s:19:"Information Request";s:11:"info_layout";s:53:"Request Information
    
    {table}
    
    Vehicle: {link}";s:8:"trade_to";s:0:"";s:13:"trade_subject";s:18:"Trade-In Appraisal";s:12:"trade_layout";s:28:"{table}
    
    Vehicle: {link}";s:8:"offer_to";s:0:"";s:13:"offer_subject";s:5:"Offer";s:12:"offer_layout";s:28:"{table}
    
    Vehicle: {link}";s:27:"request_info_form_shortcode";s:0:"";s:34:"schedule_test_drive_form_shortcode";s:0:"";s:25:"make_offer_form_shortcode";s:0:"";s:22:"tradein_form_shortcode";s:0:"";s:27:"email_friend_form_shortcode";s:0:"";s:17:"recaptcha_enabled";s:1:"1";s:20:"recaptcha_public_key";s:0:"";s:21:"recaptcha_private_key";s:0:"";s:20:"fuel_efficiency_show";s:1:"1";s:20:"fuel_efficiency_text";s:95:"Actual rating will vary with options, driving conditions, driving habits and vehicle condition.";s:17:"social_icons_show";s:1:"1";s:21:"display_vehicle_video";s:1:"1";s:15:"calculator_show";s:1:"1";s:23:"calculator_down_payment";s:4:"1000";s:15:"calculator_rate";s:1:"7";s:15:"calculator_loan";s:1:"5";s:20:"recent_vehicles_show";s:1:"1";s:21:"recent_vehicles_title";s:15:"Recent Vehicles";s:20:"recent_vehicles_desc";s:93:"Browse through the vast selection of vehicles that have recently been added to our inventory.";s:21:"recent_vehicles_limit";s:2:"10";s:21:"previous_vehicle_show";s:1:"1";s:22:"previous_vehicle_label";s:12:"Prev Vehicle";s:17:"request_more_show";s:1:"1";s:18:"request_more_label";s:17:"Request More Info";s:18:"schedule_test_show";s:1:"1";s:19:"schedule_test_label";s:19:"Schedule Test Drive";s:15:"make_offer_show";s:1:"1";s:16:"make_offer_label";s:13:"Make an Offer";s:12:"tradein_show";s:1:"1";s:13:"tradein_label";s:18:"Trade-In Appraisal";s:17:"pdf_brochure_show";s:1:"1";s:18:"pdf_brochure_label";s:12:"PDF Brochure";s:18:"print_vehicle_show";s:1:"1";s:19:"print_vehicle_label";s:18:"Print this Vehicle";s:17:"email_friend_show";s:1:"1";s:18:"email_friend_label";s:17:"Email to a Friend";s:17:"next_vehicle_show";s:1:"1";s:18:"next_vehicle_label";s:12:"Next Vehicle";s:9:"first_tab";s:16:"Vehicle Overview";s:10:"second_tab";s:18:"Features & Options";s:9:"third_tab";s:24:"Technical Specifications";s:10:"fourth_tab";s:16:"Vehicle Location";s:9:"fifth_tab";s:14:"Other Comments";s:16:"listing_comments";s:0:"";s:21:"job_description_title";s:15:"Job Description";s:21:"project_details_title";s:16:"Projects Details";s:22:"related_projects_title";s:16:"Related Projects";s:17:"default_value_lat";s:9:"43.653226";s:18:"default_value_long";s:11:"-79.3831843";s:18:"default_value_zoom";s:2:"10";s:19:"default_value_price";s:5:"Price";s:18:"default_value_city";s:4:"City";s:17:"default_value_hwy";s:7:"Highway";s:15:"edmunds_api_key";s:0:"";s:18:"edmunds_api_secret";s:0:"";s:17:"mailchimp_api_key";s:0:"";s:14:"twitter_switch";s:0:"";s:12:"consumer_key";s:0:"";s:19:"secret_consumer_key";s:0:"";s:12:"access_token";s:0:"";s:19:"secret_access_token";s:0:"";s:30:"import-demo-listing-categories";i:1;}';

        $query = $wpdb->query("UPDATE ". $wpdb->prefix ."options SET option_value = '" . $theme_panel_options . "' WHERE option_name = 'listing_wp';");
        
        echo ($query === false ? "some_wrong" : "success");
    }

    // import step 7 || Theme Panel Options
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 7){
        global $wpdb;

        $listing_panel_options = 'a:82:{s:13:"redux-section";s:1:"8";s:8:"last_tab";s:1:"0";s:7:"favicon";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:11:"body_layout";s:1:"1";s:16:"boxed_background";a:7:{s:16:"background-color";s:0:"";s:17:"background-repeat";s:0:"";s:15:"background-size";s:0:"";s:21:"background-attachment";s:0:"";s:19:"background-position";s:0:"";s:16:"background-image";s:0:"";s:5:"media";a:4:{s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}}s:20:"social_share_buttons";s:1:"1";s:16:"google_analytics";s:1:" ";s:22:"tracking_code_position";s:0:"";s:9:"logo_text";s:10:"Automotive";s:19:"logo_text_secondary";s:8:"Template";s:10:"logo_image";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:20:"default_header_image";a:5:{s:3:"url";s:84:"http://dev.themesuite.com/automotive/wp-content/uploads/2014/09/dynamic-header-1.jpg";s:2:"id";s:1:"8";s:6:"height";s:3:"300";s:5:"width";s:4:"2000";s:9:"thumbnail";s:92:"http://dev.themesuite.com/automotive/wp-content/uploads/2014/09/dynamic-header-1-150x150.jpg";}s:18:"toolbar_login_show";s:1:"1";s:13:"toolbar_login";s:5:"Login";s:18:"toolbar_login_link";s:0:"";s:21:"toolbar_language_show";s:1:"1";s:17:"toolbar_languages";s:9:"Languages";s:19:"toolbar_search_show";s:1:"1";s:14:"toolbar_search";s:6:"Search";s:18:"toolbar_phone_show";s:1:"1";s:13:"toolbar_phone";s:14:"1-800-567-0123";s:20:"toolbar_address_show";s:1:"1";s:15:"toolbar_address";s:41:"107 SUNSET BLVD., BEVERLY HILLS, CA 90210";s:20:"toolbar_address_link";s:3:"156";s:10:"header_top";s:1:"1";s:13:"header_resize";s:1:"1";s:20:"header_resize_mobile";s:1:"1";s:16:"breadcrumb_style";s:1:"1";s:16:"woocommerce_cart";s:1:"1";s:18:"languages_dropdown";s:1:"0";s:19:"footer_widget_spots";a:1:{i:0;s:0:"";}s:11:"footer_text";s:46:"Powered by {wp-link}. Built with {theme-link}.";s:12:"footer_icons";s:1:"1";s:11:"footer_menu";s:1:"1";s:14:"footer_widgets";s:1:"1";s:11:"footer_logo";s:1:"1";s:16:"footer_copyright";s:1:"1";s:20:"social_network_links";a:2:{s:7:"enabled";a:11:{s:7:"placebo";s:7:"placebo";s:8:"facebook";s:8:"Facebook";s:7:"twitter";s:7:"Twitter";s:7:"youtube";s:7:"Youtube";s:5:"vimeo";s:5:"Vimeo";s:8:"linkedin";s:8:"Linkedin";s:3:"rss";s:3:"RSS";s:6:"flickr";s:6:"Flickr";s:5:"skype";s:5:"Skype";s:6:"google";s:6:"Google";s:9:"pinterest";s:9:"Pinterest";}s:8:"disabled";a:1:{s:7:"placebo";s:7:"placebo";}}s:12:"facebook_url";s:1:"#";s:11:"twitter_url";s:1:"#";s:11:"youtube_url";s:1:"#";s:9:"vimeo_url";s:1:"#";s:12:"linkedin_url";s:1:"#";s:7:"rss_url";s:1:"#";s:10:"flickr_url";s:1:"#";s:9:"skype_url";s:1:"#";s:10:"google_url";s:1:"#";s:13:"pinterest_url";s:1:"#";s:13:"instagram_url";s:0:"";s:8:"yelp_url";s:0:"";s:13:"contact_email";s:0:"";s:13:"primary_color";s:7:"#c7081b";s:14:"css_link_color";a:3:{s:7:"regular";s:7:"#c7081b";s:5:"hover";s:7:"#c7081b";s:6:"active";s:7:"#c7081b";}s:21:"css_footer_link_color";a:3:{s:7:"regular";s:7:"#BEBEBE";s:5:"hover";s:4:"#999";s:6:"active";s:4:"#999";}s:9:"body_font";a:8:{s:11:"font-family";s:9:"Open Sans";s:12:"font-options";s:0:"";s:6:"google";s:1:"1";s:11:"font-weight";s:3:"400";s:10:"font-style";s:0:"";s:9:"font-size";s:4:"14px";s:11:"line-height";s:4:"24px";s:5:"color";s:7:"#2d2d2d";}s:13:"logo_top_font";a:9:{s:11:"font-family";s:10:"Yellowtail";s:12:"font-options";s:0:"";s:6:"google";s:1:"1";s:11:"font-weight";s:3:"400";s:10:"font-style";s:0:"";s:10:"text-align";s:0:"";s:9:"font-size";s:4:"40px";s:11:"line-height";s:4:"20px";s:5:"color";s:4:"#FFF";}s:16:"logo_bottom_font";a:9:{s:11:"font-family";s:9:"Open Sans";s:12:"font-options";s:0:"";s:6:"google";s:1:"1";s:11:"font-weight";s:3:"400";s:10:"font-style";s:0:"";s:10:"text-align";s:0:"";s:9:"font-size";s:4:"12px";s:11:"line-height";s:4:"20px";s:5:"color";s:4:"#FFF";}s:10:"custom_css";s:1:" ";s:9:"custom_js";s:1:" ";s:13:"disable_embed";s:1:"0";s:18:"blog_primary_title";s:0:"";s:20:"blog_secondary_title";s:0:"";s:21:"fourohfour_page_image";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:21:"fourohfour_page_title";s:26:"Error 404: File not found.";s:31:"fourohfour_page_secondary_title";s:66:"That being said, we will give you an amazing deal for the trouble.";s:26:"fourohfour_page_breadcrumb";s:3:"404";s:17:"search_page_image";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:17:"search_page_title";s:6:"Search";s:27:"search_page_secondary_title";s:27:"Search results for: {query}";s:22:"search_page_breadcrumb";s:23:"Search results: {query}";s:19:"category_page_image";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:19:"category_page_title";s:17:"Category: {query}";s:29:"category_page_secondary_title";s:24:"Posts related to {query}";s:24:"category_page_breadcrumb";s:17:"Category: {query}";s:14:"tag_page_image";a:5:{s:3:"url";s:0:"";s:2:"id";s:0:"";s:6:"height";s:0:"";s:5:"width";s:0:"";s:9:"thumbnail";s:0:"";}s:14:"tag_page_title";s:12:"Tag: {query}";s:24:"tag_page_secondary_title";s:24:"Posts related to {query}";s:19:"tag_page_breadcrumb";s:12:"Tag: {query}";s:16:"themeforest_name";s:0:"";s:15:"themeforest_api";s:0:"";s:11:"import_link";s:0:"";s:12:"redux-backup";s:1:"1";}';
        
        $query = $wpdb->query("UPDATE ". $wpdb->prefix ."options SET option_value = '" . $listing_panel_options . "' WHERE option_name = 'automotive_wp';");
        
        echo ($query === false ? "some_wrong" : "success");
    }

    // import step 9 || Revolution Slider
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 9){
        
        $slider_array = array(LISTING_HOME . "demo_content/homepage_slideshow.zip");
        $slider       = new RevSlider();

        ob_start();
            foreach($slider_array as $filepath){
                $slider->importSliderFromPost(true, true, $filepath);     
            }
        $return = ob_get_clean();

        echo (strstr($return, "imported") ? "success" : "some_wrong");
    }

    // import step 8 || Set Options, Menus & Widgets
    if(isset($_POST['action']) && !empty($_POST['action']) && $_POST['action'] == "automotive_demo_content_installer" && $_POST['step'] == 8){

        // update front/blog page
        $home_id = get_page_by_title( "Home" );
        $blog_id = get_page_by_title( "Blog" );

        update_option( "page_on_front", $home_id->ID );
        update_option( "page_for_posts", $blog_id->ID );
        update_option( "show_on_front", "page" );

        // set menus (header-menu|footer-menu)
        $footer_slug = "footer-menu";
        $header_slug = "main-menu";

        $header_id   = $footer_id = "";
        
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

        foreach($menus as $menu){
            if($menu->slug == $footer_slug){
                $footer_id = $menu->term_id;
            }

            if($menu->slug == $header_slug){
                $header_id = $menu->term_id;
            }
        }

        $locations = get_theme_mod('nav_menu_locations');
        $locations['header-menu'] = $header_id;
        $locations['footer-menu'] = $footer_id;

        set_theme_mod('nav_menu_locations', $locations);

        // Set Widgets (listing_sidebar|default-footer|blog-widget)
        $widgets = array(
            "listing_sidebar"   => array(
                "filter-listings-widget"    => array('title' => 'Search Our Inventory', 'year' => 1, 'make' => 1, 'model' => 1, 'body_style' => 1, 'mileage' => 1, 'mileage' => 1, 'transmission' => 1, 'fuel_economy' => 1, 'condition' => 1, 'location' => 1, 'price' => 1),
                "single-filter-widget"      => array('title' => 'Year', 'number' => 10, 'filter' => 'year'),
                "loan-calculator-widget"    => array('title' => 'Financing Calculator')
            ),
            "default-footer"    => array(
                "mail-chimp-widget"         => array('title' => 'Newsletter', 'description' => 'By subscribing to our company newsletter you will always be up-to-date on our latest promotions, deals and vehicle inventory!'),
                "twitter-feed-widget"       => array('title' => 'Twitter Feed', 'username' => 'themesuite', 'tweets' => 2),
                "contact-us-widget"         => array('title' => 'Contact Us', 'phone' => '1-800-123-4567', 'address' => '1234 Street Name, City Name, AB 12345', 'email' => 'sales@company.com')
            ),
            "blog-widget"       => array(
                "recent-posts"              => array('title' => 'Recent Posts', 'number' => 5, 'show_date' => false),
                "search"                    => array('title' => 'Search'),
                "archives"                  => array('title' => 'Archives', 'count' => 0, 'dropdown' => 0),
                "categories"                => array('title' => 'Categories', 'count' => 0, 'hierarchical' => 0, 'dropdown' => 0),
                "meta"                      => array('title' => 'Meta'),
                "list-items-widget"         => array('title' => 'Why Choose Us?', 'style' => 'checkboxes', 'fields' => 'list_item=Fully+responsive+and+ready+for+mobile&list_item=Integrated+inventory+management&list_item=Simple+to+use+and+easy+to+customize&list_item=Search+engine+optimized+(SEO+ready)&list_item=Slider+Revolution+%26+Visual+Composer&list_item=Tons+of+shortcodes+for+easy+add-ons&list_item=Fully+featured+Option+Panel+for+setup&list_item=Backed+by+dedicated+support+staff'),
                "testimonial-slider-widget" => array('title' => 'Testimonials', 'fields' => 'testimonial_name_1=Theodore+Isaac+Rubin&testimonial_text_1=Happiness+does+not+come+from+doing+easy+work+but+from+the+afterglow+of+satisfaction+that+comes+after+the+achievement+of+a+difficult+task+that+demanded+our+best.&testimonial_name_2=Theodore+Isaac+Rubin&testimonial_text_2=Happiness+does+not+come+from+doing+easy+work+but+from+the+afterglow+of+satisfaction+that+comes+after+the+achievement+of+a+difficult+task+that+demanded+our+best.')
            )
        );

        $active_widgets = array();
        $widget_options = array();
        $counter        = 1;

        // run through each widget area
        foreach($widgets as $widget_area => $area_widgets){

            // run through each widget
            foreach($area_widgets as $widget_title => $the_widget){
                $active_widgets[ $widget_area ][] = $widget_title . "-" . $counter;

                // add options
                $widget_options["widget_" . $widget_title][] = array($counter, $the_widget);

                $counter++;
            }

        }

        // Now save the active_widgets array.
        update_option( 'sidebars_widgets', $active_widgets );

        // update the widget details
        foreach($widget_options as $widget_name => $widget){
            $the_widget_options = array();

            foreach($widget as $id => $options){
                $the_widget_options[$options[0]] = $options[1];
            }
            
            update_option( $widget_name, $the_widget_options );
        }

        update_option( "hide_install_message", true );

        echo "success";
    }

    die;
}
add_action("wp_ajax_automotive_demo_content_installer", "automotive_demo_content_installer");
add_action("wp_ajax_nopriv_automotive_demo_content_installer", "automotive_demo_content_installer");

// installer message
function automotive_installer_message() {
    $hide_install_message = get_option( "hide_install_message" );

    if( $hide_install_message != true ){ ?>
        <div class="update-nag" style="display: block;">
            <span style="float: right;"><a href="<?php echo esc_url( add_query_arg("hide_install_message", "true") ); ?>"><i class="fa fa-times"></i></a></span>

            <?php _e( 'Would you like to run the Automotive one click installer to install the demo content?', 'listings' ); ?>

            <p><a href="<?php echo esc_url( add_query_arg("install_automotive_demo", "true") ); ?>"><button class='button button-primary install_automotive_demo_content'><?php _e("Install Demo Content", "listings"); ?></button></a> <i class="fa fa-cog fa-spin loading_icon_spinner" style="display: none; font-size: 22px;"></i></p>

            <div id="progressbar" style="display: none;"><div></div></div>
            <span class="progress_steps" style="display: none; text-align: center;"><?php _e("Step", "listings"); ?> <span class="current_step">0</span> / <span class="total_steps">9</span></span>

            <div style="display: none;" class="import_complete">
                <?php _e("Congratulations! Your site is now ready!"); ?>
            </div>
        </div>
        <?php
    }
}

// Only with automotive theme
$theme = wp_get_theme();
if($theme->Name == "Automotive" || $theme->Name == "Automotive Child Theme"){
    add_action( 'admin_notices', 'automotive_installer_message' );
}
?>