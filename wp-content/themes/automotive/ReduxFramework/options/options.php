<?php
if ( !class_exists( "ReduxFramework" ) ) {
	return;
}

if ( !class_exists( "Redux_Framework_automotive_wp_c15fe4af2a5399d84d32be2" ) ) {
	class Redux_Framework_automotive_wp_c15fe4af2a5399d84d32be2 {

		public $args = array();
        public $sections = array();
        public $theme;
        public $ReduxFramework;

		public function __construct( ) {
            add_action( 'after_setup_theme', array( $this, 'loadConfig' ), 10 );            

            $this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
			
		}

		public function loadConfig() {			
			global $social_options, $wpdb;
					
			$sections = array (
		array (
			'title' => __('General Settings', 'automotive'),
			'fields' => array (
				array (
					'desc' => __('Image to display beside the url bar', 'automotive'),
					'id' => 'favicon',
					'type' => 'media',
					'title' => __('Favicon', 'automotive'),
					'url' => true,
				),
				array(
					'title' => __('Body Layout', 'automotive'),
					'desc' => __('Choose which layout the body will have', 'automotive'),
					'type' => 'button_set',
					'id'   => 'body_layout',
					'options' => array(
						'1' => 'Fullwidth',
						'2' => 'Boxed',
						'3' => 'Boxed Margin'
					),
					'default' => 1
				),
				array(
					'title' => __('Default Sidebar Option', 'automotive'),
					'desc' => __('Choose whate the default sidebar option will be when creating a new page', 'automotive'),
					'type' => 'button_set',
					'id'   => 'default_sidebar',
					'options' => array(
						'none' => 'None',
						'left' => 'Left',
						'right' => 'Right'
					),
					'default' => 'none'
				),
				array( 
				    'id'       => 'boxed_background',
				    'type'     => 'background',
				    'title'    => __('Boxed Background', 'automotive'),
				    'desc'     => __('Sets the background image for boxed layouts', 'automotive'),
				    'required' => array('body_layout', '>', 1),
			    ),
				array (
					'desc' => __('Enable or disable the social share buttons at the end of each blog post.', 'automotive'),
					'type' => 'switch',
					'on' => __('Enabled', 'automotive'),
					'off' => __('Disabled', 'automotive'),
					'id' => 'social_share_buttons',
					'title' => __('Social Share Buttons', 'automotive'),
					'default' => '1',
				),
				array (
					'desc' => __('Enable or disable the featured image showing above the blog post page.', 'automotive'),
					'type' => 'switch',
					'on' => __('Enabled', 'automotive'),
					'off' => __('Disabled', 'automotive'),
					'id' => 'featured_image_blog',
					'title' => __('Featured Image', 'automotive'),
					'default' => '0',
				),
				array (
					'desc' => __('Paste your Google Analytics (or other) tracking code here. This will be added into the footer or header based on which you select afterwards. <br><br> Please <strong>do not</strong> include the &lt;script&gt; tags.', 'automotive'),
					'id' => 'google_analytics',
					'type' => 'ace_editor',
					'title' => __('Tracking Code', 'automotive'),
					'theme' => 'chrome'
				),
				array (
					'desc' => __('Place code before &lt;/head&gt; or &lt;/body&gt;', 'automotive'),
					'id' => 'tracking_code_position',
					'on' => '&lt;/' . __('head', 'automotive') . '&gt;',
					'off' => '&lt;/' . __('body', 'automotive') . '&gt;',
					'type' => 'switch',
				),
				array (
					'id' => 'custom_sidebars',
				    'type' => 'multi_text',
				    'title' => __('Custom Sidebars', 'listings'),
				    'desc' => __('These sidebars can be chosen in the page options while editing a page.', 'automotive'),
				    'default' => array(
				    	__('Sidebar 1', 'listings')
				    )
				),
				array (
					'desc' => __('Enable or disable the theme check, can speed up admin load times.', 'automotive'),
					'type' => 'switch',
					'on' => __('Enabled', 'automotive'),
					'off' => __('Disabled', 'automotive'),
					'id' => 'theme_check',
					'title' => __('Theme Update Check', 'automotive'),
					'default' => '1',
				),
				array (
					'desc' => __('Enable or disable the responsiveness of the theme.<br>Note: You may need to disable responsiveness in the Visual Composer settings as well.', 'automotive'),
					'type' => 'switch',
					'on' => __('Enabled', 'automotive'),
					'off' => __('Disabled', 'automotive'),
					'id' => 'responsiveness',
					'title' => __('Responsiveness', 'automotive'),
					'default' => '1',
				)
			),
			'icon' => 'el-icon-cog',
		),
		array (
			'title' => __('Header Settings', 'automotive'),
			'fields' => array (
				array (
					'title' => __('Logo ', 'automotive'),
					'desc' => __('Main logo text', 'automotive'),
					'type' => 'text',
					'id' => 'logo_text',
					'default' => __('Automotive', 'automotive')
				),
				array (
					'desc' => __('Text displayed under the logo text', 'automotive'),
					'type' => 'text',
					'id' => 'logo_text_secondary',
					'default' => __('Template', 'automotive')
				),
				array (
					'desc' => 'For best results make the image 270px x 65px. This setting <strong>will</strong> take precedence over the above one.',
					'type' => 'media',
					'id' => 'logo_image',
					'url' => true,
				),	
				array (
					'id' => 'logo_link',
					'type' => 'switch',
					'title' => __("Link logo to home", 'automotive'),
					'default' => true,
					'on' => 'Enabled',
					'off' => 'Disabled'
				),
				array (
					'title' => __('Default Header Image', 'automotive'),
					'desc' => __('This image will be shown if no header image is found.', 'automotive'),
					'type' => 'media',
					'id' => 'default_header_image'
				),		
				array (
					'id' => 'toolbar_shadow',
					'type' => 'switch',
					'title' => __("Toolbar Shadow", 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),
				array (
					'id' => 'header_shadow',
					'type' => 'switch',
					'title' => __("Header Shadow", 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),	
				array (
					'title' => __('Toolbar Text', 'automotive'),
					'type'  => 'section',
					'subtitle' => __('These labels are found on the top bar above the main menu.', 'automotive'),
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'toolbar_login_show',
					'type' => 'switch',
					'title' => __("Show login", 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),
				array (
					'id' => 'toolbar_login',
					'type' => 'text',
					'title' => __("Login", 'automotive'),
					'default' => __('Login', 'automotive'),
					'required' => array('toolbar_login_show', 'equals', 1)
				),
				array (
					'id' => 'toolbar_login_link',
					'type' => 'select',
					'title' => __("Login Link", 'automotive'),
					'data' => 'pages',
					'required' => array('toolbar_login_show', 'equals', 1)
				),
				array (
					'id' => 'toolbar_language_show',
					'type' => 'switch',
					'title' => __("Show languages", 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),
				array (
					'id' => 'toolbar_languages',
					'type' => 'text',
					'title' => __("Languages", 'automotive'),
					'default' => __('Languages', 'automotive'),
					'required' => array('toolbar_language_show', 'equals', 1)
				),
				array (
					'id' => 'toolbar_search_show',
					'type' => 'switch',
					'title' => __("Show search", 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),
				array (
					'id' => 'toolbar_search',
					'type' => 'text',
					'title' => __("Search", 'automotive'),
					'default' => __('Search', 'automotive'),
					'required' => array('toolbar_search_show', 'equals', 1)
				),
				array (
					'id' => 'toolbar_phone_show',
					'type' => 'switch',
					'title' => __("Show phone", 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),
				array (
					'id' => 'toolbar_phone',
					'type' => 'text',
					'title' => __("Phone", 'automotive'),
					'default' => __('Phone', 'automotive'),
					'required' => array('toolbar_phone_show', 'equals', 1)
				),
				array (
					'id' => 'toolbar_address_show',
					'type' => 'switch',
					'title' => __("Show address", 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),
				array (
					'id' => 'toolbar_address',
					'type' => 'text',
					'title' => __("Address", 'automotive'),
					'default' => __('Address', 'automotive'),
					'required' => array('toolbar_address_show', 'equals', 1)
				),
				array (
					'id' => 'toolbar_address_link',
					'type' => 'select',
					'title' => __("Address Link", 'automotive'),
					'data' => 'pages',
					'required' => array('toolbar_address_show', 'equals', 1)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),				
				array (
					'desc' => __('Show or hide the top header area.', 'automotive'),
					'id' => 'header_top',
					'type' => 'switch',
					'title' => __('Top header display', 'automotive'),
					'default' => true,
					'on' => 'Show',
					'off' => 'Hide'
				),
				array (
					'desc' => __('If on the header will resize after scrolling, or else it will stay the same size.', 'automotive'),
					'id' => 'header_resize',
					'type' => 'switch',
					'title' => __('Header Resize', 'automotive'),
					'default' => 1,
				),
				array (
					'desc' => __('If this option is off it will not resize the header if the user is using a mobile device.', 'automotive'),
					'id' => 'header_resize_mobile',
					'type' => 'switch',
					'title' => __('Header Mobile Resize', 'automotive'),
					'default' => 1,
				),
				array (
					'desc' => __('If this option is on it will push the homepage slider underneath the header so users on mobile can see the full slideshow.', 'automotive'),
					'id' => 'push_mobile_slideshow_down',
					'type' => 'switch',
					'title' => __('Push Homepage Slider Under Header on Mobile', 'automotive'),
					'default' => 0,
				),
				array (
					'desc' => __('If blog is chosen it will show blog link in breadcrumbs, otherwise it will show all the categories the post/page is tagged in.', 'automotive'),
					'id' => 'breadcrumb_style',
					'type' => 'switch',
					'title' => __('Breadcrumb Style', 'automotive'),
					'default' => 1,
					'on' => __("Blog", "listings"),
					'off' => __("Categories", "listings")
				),
				array (
					'desc' => __('If this is enabled it will display a cart icon beside the Login label', 'automotive'),
					'id' => 'woocommerce_cart',
					'type' => 'switch',
					'title' => __('WooCommerce Cart', 'automotive') . " " . sprintf( __('(WooCommerce is <span style=\'%s\'>%s</span>)', 'automotive'),  'color: ' . (function_exists("is_woocommerce") ? "green" : "red") . '; display: inline-block;', (function_exists("is_woocommerce") ? 'Active' : 'Not Active')),
					'default' => 1,
				),
				array (
					'desc' => __('Display a dropdown of available languages in the header. Only works with WPML', 'automotive'),
					'id' => 'languages_dropdown',
					'type' => 'switch',
					'title' => sprintf( __('Languages (WPML is <span style=\'%s\'>%s</span>)', 'automotive'),  'color: ' . (function_exists("icl_get_home_url") ? "green" : "red") . '; display: inline-block;', (function_exists("icl_get_home_url") ? 'Active' : 'Not Active')),
					'default' => 1,
				),
			),
			'icon' => 'fa fa-header',
		),
		array(
			'title' => __('Footer Settings', 'automotive'),
			'fields' => array(
				array(
					'desc' => __('You can create different footer widget areas for different pages.', 'automotive'),
					'id' => 'footer_widget_spots',
					'type' => 'multi_text',
					'add_text' => __('Add another footer', 'automotive'),
					'title' => __('Multiple Footer areas', 'automotive'),
				),
				array (
					'desc' => 'If a logo here isn\'t set it will default to the one from Header Settings.',
					'type' => 'media',
					'id' => 'footer_logo_image',
					'url' => true,
					'title' => 'Footer Logo'
				),	
				array (
					'desc' => __('You can use the following shortcodes in your footer text', 'automotive') . ': {wp-link} {theme-link} {loginout-link} {blog-title} {blog-link} {the-year}',
					'id' => 'footer_text',
					'type' => 'editor',
					'title' => __('Footer Text', 'automotive'),
					'default' => 'Powered by {wp-link}. Built with {theme-link}.',
				),
				array (
					'desc' => __('Show or hide the footer icons.', 'automotive'),
					'id' => 'footer_icons',
					'type' => 'switch',
					'title' => __('Footer Icons', 'automotive'),
					'default' => true,
					'on' => __("Show", "automotive"),
					'off' => __("Hide", "automotive")
				),
				array (
					'desc' => __('Show or hide the footer menu.', 'automotive'),
					'id' => 'footer_menu',
					'type' => 'switch',
					'title' => __('Footer Menu', 'automotive'),
					'default' => true,
					'on' => __("Show", "automotive"),
					'off' => __("Hide", "automotive")
				),
				array (
					'desc' => __('Show or hide the footer widgets.', 'automotive'),
					'id' => 'footer_widgets',
					'type' => 'switch',
					'title' => __('Footer Widgets', 'automotive'),
					'default' => true,
					'on' => __("Show", "automotive"),
					'off' => __("Hide", "automotive")
				),
				array (
					'desc' => __('Show or hide the footer logo.', 'automotive'),
					'id' => 'footer_logo',
					'type' => 'switch',
					'title' => __('Footer Logo', 'automotive'),
					'default' => true,
					'on' => __("Show", "automotive"),
					'off' => __("Hide", "automotive")
				),
				array (
					'desc' => __('Show or hide the footer copyright.', 'automotive'),
					'id' => 'footer_copyright',
					'type' => 'switch',
					'title' => __('Footer Copyright', 'automotive'),
					'default' => true,
					'on' => __("Show", "automotive"),
					'off' => __("Hide", "automotive")
				),
				/*array(
					'id' => 'footer_columns',
					'type' => 'radio',
					'title' => __('Footer Columns', 'automotive'),
					'options' => array(
						'1' => '1 ' . __('Column', 'automotive'),
						'2' => '2 ' . __('Columns', 'automotive'),
						'3' => '3 ' . __('Columns', 'automotive'),
						'4' => '4 ' . __('Columns', 'automotive')
					),
					'default' => '3'
				)*/
			),
			'icon' => 'fa fa-list-alt'
		),
		array (
			'title' => __('Social Settings', 'automotive'),
			'fields' => array (
				array (
					'id' => 'social_network_links',
					'type' => 'sorter',
					'title' => __('Footer Social Icons', 'automotive'),
					'desc'    => __('Choose which social networks are displayed and edit where they link to.', 'automotive'),
					'options' => array(
						'enabled'  => $social_options,
						'disabled' => array()
					)
				),
			),
			'icon' => 'fa fa-share-alt',
		),
		array (
			'title' => __('Contact Settings', 'automotive'),
			'fields' => array (
				array (
					'desc' => __('This email will be used to forward the contact form mail to it.', 'automotive'),
					'id' => 'contact_email',
					'type' => 'text',
					'title' => __('Contact Email', 'automotive'),
					'default' => get_option('admin_email'),
					'validate' => 'email'
				)/*,
				array (
					'desc' => __('Create a form using <a href=\'http://dev.themesuite.com/brand/wp-admin/plugin-install.php?tab=plugin-information&plugin=contact-form-7&TB_iframe=true&width=640&height=855\' class=\'thickbox\'>Contact Form 7</a> and paste the shortcode here to replace the default form.', 'automotive'),
					'id' => 'contact_form_shortcode',
					'type' => 'text',
					'title' => __('Contact Form', 'automotive'),
				),
				/*array (
					'desc' => __('The title found above the google map', 'automotive'),
					'id' => 'contact_map_title',
					'type' => 'text',
					'title' => __('Map title', 'automotive'),
					'default' => __('FIND US ON THE MAP', 'automotive')
				),
				array (
					'desc' => __('The title found above the contact form', 'automotive'),
					'id' => 'contact_form_title',
					'type' => 'text',
					'title' => __('Contact Form Title', 'automotive'),
					'default' => __('CONTACT FORM', 'automotive')
				),
				array (
					'title' => __('Google Map', 'automotive'),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true,
 				    'subtitle' => __('Simply enter in the Google Map Coordinates and zoom level to adjust the map on the contact page template.', 'automotive')
				),
				array (
					'title' => __('Latitude', 'automotive'),
					'id'   => 'contact_map_latitude',
					'type' => 'text'
				),
				array (
					'title' => __('Longitude', 'automotive'),
					'id'   => 'contact_map_longitude',
					'type' => 'text'
				),
				array ( 
					'id' => 'contact_map_zoom',
					'type' => 'slider',
					'title' => __('Zoom', 'automotive'),
					'default' => 10,
					'min' => 1,
					'max' => 19,
					'display_value' => 'label'
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				)*/
			),
			'icon' => 'fa fa-envelope',
		),
		array(
		    'title'     => __('Custom Styling', 'automotive'),
		    'icon'      => 'fa fa-pencil-square-o',
		    'fields'    => array(
				array (
					'desc' => __('Pick a primary color for the theme (default: #c7081b).', 'automotive'),
					'id' => 'primary_color',
					'type' => 'color',
					'title' => __('Primary Color', 'automotive'),
					'default' => '#c7081b',
				),
				array(
					'id' => 'css_link_color',
					'type' => 'link_color',
					'title' => __('Link Color', 'automotive'),
					'default' => array(
						'regular' => '#c7081b',
						'hover'   => '#c7081b',
						'active'  => '#c7081b',
						'visited' => '#c7081b'
					)
				),
				array(
					'id' => 'css_footer_link_color',
					'type' => 'link_color',
					'title' => __('Footer Link Color', 'automotive'),
					'default' => array(
						'regular' => '#BEBEBE',
						'hover'   => '#999',
						'active'  => '#999',
						'visited' => '#BEBEBE'
					)
				),
		        array(
		            'id'            => 'theme_color_scheme',
		            'type'          => 'color_scheme',
		            'title'         => 'Color Scheme',
		            'subtitle'      => 'Save and load color schemes',
		            'desc'          => '',
		            'output'        => true,
		            'compiler'      => false,
		            'simple'        => false,
		            'options'       => array(
		                'show_input'                => true,
		                'show_initial'              => true,
		                'show_alpha'                => true,
		                'show_palette'              => true,
		                'show_palette_only'         => false,
		                'show_selection_palette'    => true,
		                'max_palette_size'          => 10,
		                'allow_empty'               => true,
		                'clickout_fires_change'     => false,
		                'choose_text'               => __('Choose', 'automotive'),
		                'cancel_text'               => __('Cancel', 'automotive'),
		                'show_buttons'              => true,
		                'use_extended_classes'      => true,
		                'palette'                   => null,  // show default
		            ),
		            'groups'        => array(
		                'Header'    	=> __('Set header colors.', 'automotive'),
		                'Secondary' 	=> __('Set secondary header colors.', 'automotive'),
		                'Body'      	=> __('Set body colors here.', 'automotive'),
		                'Footer'    	=> __('Set footer colors here.', 'automotive'),
		                'Bottom Footer' => __('Set bottom footer colors here.', 'automotive')
		            ),

		            'default'       => array(
		            	// Header
		                array(
		                    'id'        => 'site-header',
		                    'title'     => __('Header Background', 'automotive'),
		                    'color'     => '#000000', 
		                    'alpha'     => .65,
		                    'selector'  => 'header, .dropdown .dropdown-menu li.dropdown .dropdown-menu, header .navbar-nav.pull-right>li>.dropdown-menu, header .navbar-nav>li>.dropdown-menu',
		                    'mode'      => 'background-color',
		                    'group'     => 'Header'
		                ),                        
		                array(
		                    'id'        => 'toolbar-color',
		                    'title'     => __('Toolbar Text', 'automotive'),
		                    'color'     => '#929596', 
		                    'alpha'     => 1,
		                    'selector'  => '.toolbar ul li a, .toolbar .search_box, header .toolbar button, .toolbar ul li i',
		                    'mode'      => 'color',
		                    'group'     => 'Header'
		                ),                            
		                array(
		                    'id'        => 'toolbar-background',
		                    'title'     => __('Toolbar Background', 'automotive'),
		                    'color'     => '#000000', 
		                    'alpha'     => .2,
		                    'selector'  => '.toolbar',
		                    'mode'      => 'background-color',
		                    'group'     => 'Header'
		                ),    
		                array(
		                    'id'        => 'header-menu-color', 
		                    'title'     => __('Header menu text color', 'automotive'), 
		                    'color'     => '#FFFFFF', 
		                    'alpha'     => 1, 
		                    'selector'  => '.bottom-header .navbar-default .navbar-nav>.active>a, header .bottom-header .navbar-default .navbar-nav>li>a, .navbar .navbar-nav li .dropdown-menu>li>a, .dropdown .dropdown-menu li.dropdown .dropdown-menu>li>a, body .navbar-default .navbar-nav .open .dropdown-menu>li>a',  
		                    'mode'      => 'color',  
		                    'group'     => 'Header'
		                ),
		                array(
		                    'id'        => 'header-menu-active', 
		                    'title'     => __('Header menu active item', 'automotive'), 
		                    'color'     => '#c7081b', 
		                    'alpha'     => 1, 
		                    'selector'  => 'header .bottom-header .navbar-default .navbar-nav>.active>a, .dropdown-menu>.active>a',  
		                    'mode'      => 'background,background-color',  
		                    'group'     => 'Header'
		                ),
		                array(
		                    'id'        => 'header-menu-hover', 
		                    'title'     => __('Header menu hover item', 'automotive'), 
		                    'color'     => '#c7081b', 
		                    'alpha'     => 1, 
		                    'selector'  => '.bottom-header .navbar-default .navbar-nav> li> a:hover, .bottom-header .navbar-default .navbar-nav>.active>a:hover, .dropdown-menu>li>a:hover, .dropdown-menu>li.active>a:hover',  
		                    'mode'      => 'background,background-color',  
		                    'group'     => 'Header'
		                ),

		                // Secondary
		                array(
		                    'id'        => 'secondary-background',
		                    'title'     => __('Secondary Background', 'automotive'),
		                    'color'     => '#000000', 
		                    'alpha'     => 1,
		                    'selector'  => '#secondary-banner',
		                    'mode'      => 'background-color',
		                    'group'     => 'Secondary'
		                ),                             
		                array(
		                    'id'        => 'secondary-text',
		                    'title'     => __('Secondary Text', 'automotive'),
		                    'color'     => '#FFFFFF', 
		                    'alpha'     => 1,
		                    'selector'  => '#secondary-banner',
		                    'mode'      => 'color',
		                    'group'     => 'Secondary'
		                ),                         
		                array(
		                    'id'        => 'breadcrumb',
		                    'title'     => __('Breadcrumb Text', 'automotive'),
		                    'color'     => '#FFFFFF', 
		                    'alpha'     => 1,
		                    'selector'  => '.breadcrumb li, .breadcrumb li a, .breadcrumb>li+li:before',
		                    'mode'      => 'color',
		                    'group'     => 'Secondary'
		                ),

		                // Body
		                array(
		                    'id'        => 'body-background',
		                    'title'     => __('Body Background', 'automotive'),
		                    'color'     => '#FFFFFF', 
		                    'alpha'     => 1,
		                    'selector'  => 'section.content, .car-block-wrap, .welcome-wrap',
		                    'mode'      => 'background-color',
		                    'group'     => 'Body'
		                ),   
		                array(
		                    'id'        => 'body-background-input',
		                    'title'     => __('Body Background Input', 'automotive'),
		                    'color'     => '#FFFFFF', 
		                    'alpha'     => 1,
		                    'selector'  => 'body input, body select, body textarea, body input[type="text"], body textarea[name="message"], body input[type="email"], input.form-control, input[type="search"], .side-content .financing_calculator table tr td input.number',
		                    'mode'      => 'background-color',
		                    'group'     => 'Body'
		                ),    
		                array(
		                    'id'        => 'body-color-input',
		                    'title'     => __('Body Input Text', 'automotive'),
		                    'color'     => '#2D2D2D', 
		                    'alpha'     => 1,
		                    'selector'  => 'body input, body select, body textarea, input.form-control, select.form-control, textarea.form-control, input[type="search"], .side-content .financing_calculator table tr td input.number',
		                    'mode'      => 'color',
		                    'group'     => 'Body'
		                ),    
		                array(
		                    'id'        => 'inventory-background-input',
		                    'title'     => __('Inventory Dropdown Background', 'automotive'),
		                    'color'     => '#F7F7F7', 
		                    'alpha'     => 1,
		                    'selector'  => '.sbHolder, .sbOptions, .sbOptions li:hover',
		                    'mode'      => 'background-color',
		                    'group'     => 'Body'
		                ),    
		                array(
		                    'id'        => 'inventory-color-input',
		                    'title'     => __('Inventory Dropdown Text', 'automotive'),
		                    'color'     => '#333', 
		                    'alpha'     => 1,
		                    'selector'  => '.sbHolder, .sbOptions, a.sbSelector:link, a.sbSelector:visited, a.sbSelector:hover, .sbOptions a:link, .sbOptions a:visited',
		                    'mode'      => 'color',
		                    'group'     => 'Body'
		                ),     

		                // Footer
		                array(
		                    'id'        => 'footer-background',
		                    'title'     => __('Footer Background', 'automotive'),
		                    'color'     => '#3D3D3D', 
		                    'alpha'     => 1,
		                    'selector'  => 'footer',
		                    'mode'      => 'background-color',
		                    'group'     => 'Footer'
		                ),                             
		                array(
		                    'id'        => 'footer-text',
		                    'title'     => __('Footer Text', 'automotive'),
		                    'color'     => '#FFFFFF', 
		                    'alpha'     => 1,
		                    'selector'  => 'footer, footer p, footer .textwidget, footer p, footer li, footer table',
		                    'mode'      => 'color',
		                    'group'     => 'Footer'
		                ),   

		                // Bottom Footer
		                array(
		                    'id'        => 'bottom-footer-background',
		                    'title'     => __('Bottom Footer Background', 'automotive'),
		                    'color'     => '#2F2F2F', 
		                    'alpha'     => 1,
		                    'selector'  => '.copyright-wrap',
		                    'mode'      => 'background-color',
		                    'group'     => 'Bottom Footer'
		                ),                             
		                array(
		                    'id'        => 'bottom-footer-text',
		                    'title'     => __('Bottom Footer Text', 'automotive'),
		                    'color'     => '#FFFFFF', 
		                    'alpha'     => 1,
		                    'selector'  => '.copyright-wrap, .copyright-wrap p',
		                    'mode'      => 'color',
		                    'group'     => 'Bottom Footer'
		                ), 
		            )
		        ),
				array (
					'id' => 'body_font',
					'type' => 'typography',
					'desc' => __('Set the body font using googles web font service.', 'automotive'),
					'title' => __('Body Font', 'automotive'),
					'fonts' => array (),
					'default' => array(
						'font-family' => 'Open Sans',
						'font-weight' => '400',
						'font-size'   => '14px',
						'line-height' => '24px',
						'color'		  => '#2D2D2D'
					),
					'all_styles' => true,
					'subsets' => false,
					'text-align' => false
				),
				array (
					'id' => 'logo_top_font',
					'type' => 'typography',
					'desc' => __('Set the top logo font using googles web font service.', 'automotive'),
					'title' => __('Top Logo Font', 'automotive'),
					'default' => array (
						'font-family' => 'Yellowtail',
						'font-weight' => '400',
						'font-size'   => '40px',
						'line-height' => '20',
						'color'		  => '#FFF'
					),
					'subsets' => false
				),
				array (
					'id' => 'logo_bottom_font',
					'type' => 'typography',
					'desc' => __('Set the bottom logo font using googles web font service.', 'automotive'),
					'title' => __('Bottom Logo Font', 'automotive'),
					'default' => array (
						'font-family' => 'Open Sans',
						'font-weight' => '400',
						'font-size'   => '12px',
						'line-height' => '20',
						'color'		  => '#FFF'
					),
					'subsets' => false
				),
				array (
					'desc' => __('Quickly add some custom CSS to your theme.', 'automotive'),
					'id' => 'custom_css',
					'type' => 'ace_editor',
					'title' => __('Custom CSS', 'automotive'),
					'mode' => 'css',
					'theme' => 'chrome'
				),
				array (
					'desc' => __('Quickly add some custom JS to your theme.', 'automotive'),
					'id' => 'custom_js',
					'type' => 'ace_editor',
					'title' => __('Custom JS', 'automotive'),
					'mode' => 'javascript',
					'theme' => 'chrome'
				),
		    ),
		),
		array ( 
			'title' => __('Page Settings', 'automotive'),
			'fields' => array (
				array (
					'title' => __('Blog Post', 'automotive'),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'blog_primary_title',
					'type' => 'text',
					'desc' => __('This title shows up in the header section on all blog postings and the blog page.', 'automotive'),
					'title' => __('Blog Listing Titles', 'automotive'),
				),
				array (
					'id' => 'blog_secondary_title',
					'type' => 'text',
					'desc' => __('This secondary title displays under the previous title in the header on blog pages.', 'automotive'),
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),

				array (
					'title' => __('404 Page', 'automotive'),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'fourohfour_page_image',
					'type' => 'media',
					'title' => __("Header Image", 'automotive')
				),
				array (
					'id' => 'fourohfour_page_title',
					'type' => 'text',
					'title' => __("Main Title", 'automotive'),
					'default' => __('Error 404: File not found.', 'automotive')
				),
				array (
					'id' => 'fourohfour_page_secondary_title',
					'type' => 'text',
					'title' => __("Secondary Title", 'automotive'),
					'default' => __('That being said, we will give you an amazing deal for the trouble.', 'automotive')
				),
				array (
					'id' => 'fourohfour_page_breadcrumb',
					'type' => 'text',
					'title' => __("Breadcrumb", 'automotive'),
					'default' => '404'
				),
				array (
					'id' => 'fourohfour_page_sidebar',
					'type' => 'select',
					'title' => __("Sidebar", 'automotive'),
					'default' => '',
					'data' => 'sidebar'
				),
				array (
					'id' => 'fourohfour_page_sidebar_position',
					'type' => 'select',
					'title' => __("Sidebar Position", 'automotive'),
					'default' => '',
					'options' => array(
						"left" => __("Left", "automotive"),
						"right" => __("Right", "automotive")
					)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),
				
				array (
					'title' => __('Search Page', 'automotive'),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'search_page_image',
					'type' => 'media',
					'title' => __("Header Image", 'automotive')
				),
				array (
					'id' => 'search_page_title',
					'type' => 'text',
					'title' => __("Main Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of search term', 'automotive'),
					'default' => __('Search', 'automotive')
				),
				array (
					'id' => 'search_page_secondary_title',
					'type' => 'text',
					'title' => __("Secondary Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of search term', 'automotive'),
					'default' => 'Search results for: {query}'
				),
				array (
					'id' => 'search_page_breadcrumb',
					'type' => 'text',
					'title' => __("Breadcrumb", 'automotive'),
					'desc' => __('You are able to use {query} in place the of search term', 'automotive'),
					'default' => 'Search results: {query}'
				),
				array (
					'id' => 'search_page_sidebar',
					'type' => 'select',
					'title' => __("Sidebar", 'automotive'),
					'default' => '',
					'data' => 'sidebar'
				),
				array (
					'id' => 'search_page_sidebar_position',
					'type' => 'select',
					'title' => __("Sidebar Position", 'automotive'),
					'default' => '',
					'options' => array(
						"left" => __("Left", "automotive"),
						"right" => __("Right", "automotive")
					)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),
				
				
				array (
					'title' => __('Category Page', 'automotive'),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'category_page_image',
					'type' => 'media',
					'title' => __("Header Image", 'automotive')
				),
				array (
					'id' => 'category_page_title',
					'type' => 'text',
					'title' => __("Main Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of category term', 'automotive'),
					'default' => 'Category: {query}'
				),
				array (
					'id' => 'category_page_secondary_title',
					'type' => 'text',
					'title' => __("Secondary Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of category term', 'automotive'),
					'default' => 'Posts related to {query}'
				),
				array (
					'id' => 'category_page_breadcrumb',
					'type' => 'text',
					'title' => __("Breadcrumb", 'automotive'),
					'desc' => __('You are able to use {query} in place the of category term', 'automotive'),
					'default' => 'Category: {query}'
				),
				array (
					'id' => 'category_page_sidebar',
					'type' => 'select',
					'title' => __("Sidebar", 'automotive'),
					'default' => '',
					'data' => 'sidebar'
				),
				array (
					'id' => 'category_page_sidebar_position',
					'type' => 'select',
					'title' => __("Sidebar Position", 'automotive'),
					'default' => '',
					'options' => array(
						"left" => __("Left", "automotive"),
						"right" => __("Right", "automotive")
					)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),
				
				
				array (
					'title' => __('Tag Page', 'automotive'),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'tag_page_image',
					'type' => 'media',
					'title' => __("Header Image", 'automotive')
				),
				array (
					'id' => 'tag_page_title',
					'type' => 'text',
					'title' => __("Main Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of tag term', 'automotive'),
					'default' => 'Tag: {query}'
				),
				array (
					'id' => 'tag_page_secondary_title',
					'type' => 'text',
					'title' => __("Secondary Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of tag term', 'automotive'),
					'default' => 'Posts related to {query}'
				),
				array (
					'id' => 'tag_page_breadcrumb',
					'type' => 'text',
					'title' => __("Breadcrumb", 'automotive'),
					'desc' => 'You are able to use {query} in place the of tag term',
					'default' => 'Tag: {query}'
				),
				array (
					'id' => 'tag_page_sidebar',
					'type' => 'select',
					'title' => __("Sidebar", 'automotive'),
					'default' => '',
					'data' => 'sidebar'
				),
				array (
					'id' => 'tag_page_sidebar_position',
					'type' => 'select',
					'title' => __("Sidebar Position", 'automotive'),
					'default' => '',
					'options' => array(
						"left" => __("Left", "automotive"),
						"right" => __("Right", "automotive")
					)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),
				
				
				array (
					'title' => __('Woocommerce Category', 'automotive') . " " . sprintf( __('(WooCommerce is <span style=\'%s\'>%s</span>)', 'automotive'),  'color: ' . (function_exists("is_woocommerce") ? "green" : "red") . '; display: inline-block;', (function_exists("is_woocommerce") ? 'Active' : 'Not Active')),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'woo_category_page_image',
					'type' => 'media',
					'title' => __("Header Image", 'automotive')
				),
				array (
					'id' => 'woo_category_page_title',
					'type' => 'text',
					'title' => __("Main Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of tag term', 'automotive'),
					'default' => '{query}'
				),
				array (
					'id' => 'woo_category_page_secondary_title',
					'type' => 'text',
					'title' => __("Secondary Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of tag term', 'automotive'),
					'default' => ''
				),
				array (
					'id' => 'woo_category_page_breadcrumb',
					'type' => 'text',
					'title' => __("Breadcrumb", 'automotive'),
					'desc' => 'You are able to use {query} in place the of tag term',
					'default' => '{query}'
				),
				array (
					'id' => 'woo_category_page_sidebar',
					'type' => 'select',
					'title' => __("Sidebar", 'automotive'),
					'default' => '',
					'data' => 'sidebar'
				),
				array (
					'id' => 'woo_category_page_sidebar_position',
					'type' => 'select',
					'title' => __("Sidebar Position", 'automotive'),
					'default' => '',
					'options' => array(
						"left" => __("Left", "automotive"),
						"right" => __("Right", "automotive")
					)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),


				array (
					'title' => __('Woocommerce Tag', 'automotive') . " " . sprintf( __('(WooCommerce is <span style=\'%s\'>%s</span>)', 'automotive'),  'color: ' . (function_exists("is_woocommerce") ? "green" : "red") . '; display: inline-block;', (function_exists("is_woocommerce") ? 'Active' : 'Not Active')),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'woo_tag_page_image',
					'type' => 'media',
					'title' => __("Header Image", 'automotive')
				),
				array (
					'id' => 'woo_tag_page_title',
					'type' => 'text',
					'title' => __("Main Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of tag term', 'automotive'),
					'default' => '{query}'
				),
				array (
					'id' => 'woo_tag_page_secondary_title',
					'type' => 'text',
					'title' => __("Secondary Title", 'automotive'),
					'desc' => __('You are able to use {query} in place the of tag term', 'automotive'),
					'default' => ''
				),
				array (
					'id' => 'woo_tag_page_breadcrumb',
					'type' => 'text',
					'title' => __("Breadcrumb", 'automotive'),
					'desc' => 'You are able to use {query} in place the of tag term',
					'default' => '{query}'
				),
				array (
					'id' => 'woo_tag_page_sidebar',
					'type' => 'select',
					'title' => __("Sidebar", 'automotive'),
					'default' => '',
					'data' => 'sidebar'
				),
				array (
					'id' => 'woo_tag_page_sidebar_position',
					'type' => 'select',
					'title' => __("Sidebar Position", 'automotive'),
					'default' => '',
					'options' => array(
						"left" => __("Left", "automotive"),
						"right" => __("Right", "automotive")
					)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),

				array (
					'title' => __('Woocommerce Shop', 'automotive') . " " . sprintf( __('(WooCommerce is <span style=\'%s\'>%s</span>)', 'automotive'),  'color: ' . (function_exists("is_woocommerce") ? "green" : "red") . '; display: inline-block;', (function_exists("is_woocommerce") ? 'Active' : 'Not Active')),
					'type'  => 'section',
					'id' => 'section-start',
 				    'indent' => true
				),
				array (
					'id' => 'woo_shop_page_image',
					'type' => 'media',
					'title' => __("Header Image", 'automotive')
				),
				array (
					'id' => 'woo_shop_page_title',
					'type' => 'text',
					'title' => __("Main Title", 'automotive'),
					'default' => 'Shop'
				),
				array (
					'id' => 'woo_shop_page_secondary_title',
					'type' => 'text',
					'title' => __("Secondary Title", 'automotive'),
					'default' => ''
				),
				array (
					'id' => 'woo_shop_page_sidebar',
					'type' => 'select',
					'title' => __("Sidebar", 'automotive'),
					'default' => '',
					'data' => 'sidebar'
				),
				array (
					'id' => 'woo_shop_page_sidebar_position',
					'type' => 'select',
					'title' => __("Sidebar Position", 'automotive'),
					'default' => '',
					'options' => array(
						"left" => __("Left", "automotive"),
						"right" => __("Right", "automotive")
					)
				),
				array (
					'type'  => 'section',
					'id' => 'section-end',
 				    'indent' => false
				),
			),
			'icon' => 'fa fa-file-text-o'
		),
		array (
			'title' => __('Update Settings', 'automotive'),
			'fields' => array (
				array (
					'id' => 'themeforest_name',
					'type' => 'text',
					'desc' => __('Enter in your themeforest username in order to download theme updates directly on your website.', 'automotive'),
					'title' => __('ThemeForest Automatic Updates', 'automotive'),
				),
				array (
					'id' => 'themeforest_api',
					'type' => 'text',
					'desc' => __('Themeforest API key<br><br>Please note: this is <b>not</b> your purchase code.', 'automotive')
				),
			),
		),
		array(
			'title' => __("Import / Export", "automotive"),
			'class' => 'custom_import',
			'fields'    => array(
			    array(
			        'id'            => 'opt-import-export',
			        'type'          => 'import_export',
			        'title'         => __('Import Export', 'automotive'),
			        'subtitle'      => __('Save and restore your Redux options', 'automotive'),
			        'full_width'    => true,
			    ),
			),
			'icon' => 'el-icon-refresh'
		),
	);		
	
			// if(is_writable(get_stylesheet_directory() . "/style.css")){
			// 	$sections[5]['fields'][] = array(
			// 	    'id'       => 'disable_embed',
			// 	    'type'     => 'switch', 
			// 	    'title'    => __('Disable embedded CSS', 'automotive'),
			// 	    'subtitle' => __('This is only visible if your server can write files', 'automotive'),
			// 	    'default'  => true,
			// 	);
			// }
			
			// add social network urls
			foreach($social_options as $label){
				$sections[3]['fields'][] = array (
					'id'    => strtolower($label) . '_url',
					'type'  => 'text',
					'title' => ucwords($label) . ' URL',
				);
			}

			// disable if available
			if(defined("AUTOMOTIVE_VERSION") && version_compare(AUTOMOTIVE_VERSION, "5.6") != -1){
				$sections[0]['fields'][] = array(
					'desc' => __('Enable or disable the listing features of the plugin, useful if you only wish to use Automotive plugin for the widgets and shortcodes.', 'automotive'),
					'type' => 'custom_button',
					'on' => __('Enabled', 'automotive'),
					'off' => __('Disabled', 'automotive'),
					'id' => 'plugin_listings',
					'title' => __('Listing Features Deactivation', 'automotive'),
					'default' => '1',
				);
			}

			// Change your opt_name to match where you want the data saved.
			$args = array(
				"opt_name"			=> "automotive_wp", // Where your data is stored. Use a different name or use the same name as your current theme. Must match the $database_newName variable in the converter code.
				"menu_title" 		=> __("Theme Options", 'automotive'), // Title for your menu item
				"page_slug" 		=> "automotive_wp", // Make this the same as your opt_name unless you care otherwise
				'dev_mode'	 		=> false,
				"footer_credit"		=> "Automotive by Theme Suite",
				"share_icons"		=> array(
						array(
					        'url'   => 'https://www.facebook.com/ThemeSuite.Themes',
					        'title' => 'Like us on Facebook',
					        'icon'  => 'fa fa-facebook-official'
					    ),
					    array(
					        'url'   => 'https://twitter.com/themesuite',
					        'title' => 'Follow us on Twitter',
					        'icon'  => 'fa fa-twitter'
					    )
					)
			);
			// Use this section if this is for a theme. Replace with plugin specific data if it is for a plugin.
			$theme = wp_get_theme();
			$args["display_name"] = $theme->get("Name");
			$args["display_version"] = $theme->get("Version");

			$ReduxFramework = new ReduxFramework($sections, $args);
						
		}
				
	}
	new Redux_Framework_automotive_wp_c15fe4af2a5399d84d32be2();
}