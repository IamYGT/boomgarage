<?php get_header();

    global $awp_options;

    if(is_product_category()){
        $sidebar            = (isset($awp_options['woo_category_page_sidebar_position']) && !empty($awp_options['woo_category_page_sidebar_position']) ? $awp_options['woo_category_page_sidebar_position'] : "");
        $default_sidebar    = (isset($awp_options['woo_category_page_sidebar']) && !empty($awp_options['woo_category_page_sidebar']) ? $awp_options['woo_category_page_sidebar'] : "");  
    } elseif(is_product_tag()){
        $sidebar            = (isset($awp_options['woo_tag_page_sidebar_position']) && !empty($awp_options['woo_tag_page_sidebar_position']) ? $awp_options['woo_tag_page_sidebar_position'] : "");
        $default_sidebar    = (isset($awp_options['woo_tag_page_sidebar']) && !empty($awp_options['woo_tag_page_sidebar']) ? $awp_options['woo_tag_page_sidebar'] : "");  
    } elseif(is_shop()) {
        $sidebar            = (isset($awp_options['woo_shop_page_sidebar_position']) && !empty($awp_options['woo_shop_page_sidebar_position']) ? $awp_options['woo_shop_page_sidebar_position'] : "");
        $default_sidebar    = (isset($awp_options['woo_shop_page_sidebar']) && !empty($awp_options['woo_shop_page_sidebar']) ? $awp_options['woo_shop_page_sidebar'] : "");  
    } else {
        $default_sidebar    = get_post_meta(get_current_id(), "sidebar", true); 
	}

    if(isset($sidebar) && !empty($sidebar)){
        $classes       = content_classes($sidebar);
    } 

    $content_class = (isset($classes[0]) && !empty($classes[0]) ? $classes[0] : "");
    $sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>

    <div class="inner-page row wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?>">

    	<div class="pull-right"><?php do_action('currency_switcher', array('format' => '%name (%symbol)')); ?></div>

    	<div id="post-<?php echo get_current_id(); ?>" <?php echo "class='" . $content_class . " page-content post-entry'"; ?>>
        
    		<?php woocommerce_content(); ?>
            
    	</div>
        
        <?php // sidebar 
			if(isset($sidebar) && !empty($sidebar) && $sidebar != "none"){
				echo "<div class='" . $sidebar_class . " sidebar-widget side-content'>";
				dynamic_sidebar($default_sidebar);
				echo "</div>";
			}					
		?>
    </div>

<?php get_footer(); ?>