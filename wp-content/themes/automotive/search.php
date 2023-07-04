<?php get_header();

    global $awp_options;

    $sidebar            = (isset($awp_options['search_page_sidebar_position']) && !empty($awp_options['search_page_sidebar_position']) ? $awp_options['search_page_sidebar_position'] : "");
    $default_sidebar    = (isset($awp_options['search_page_sidebar']) && !empty($awp_options['search_page_sidebar']) ? $awp_options['search_page_sidebar'] : "");     
    $classes            = content_classes($sidebar);

    $content_class      = $classes[0];
    $sidebar_class      = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>

	<div class="container">
        <div class="inner-page row wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?>">
            <div class="page-content<?php echo (!empty($content_class) ? " " . $content_class : ""); ?>">

				<?php get_template_part('loop'); ?>    

			</div>

            <?php // sidebar 
                if(isset($sidebar) && !empty($sidebar) && $sidebar != "none" && isset($default_sidebar) && !empty($default_sidebar)){
                    echo "<div class='" . $sidebar_class . " sidebar-widget side-content'>";
                    dynamic_sidebar($default_sidebar);
                    echo "</div>";
                }                   
            ?>
        </div>
    </div>

<?php get_footer(); ?>
