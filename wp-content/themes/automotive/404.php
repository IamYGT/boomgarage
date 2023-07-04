<?php  /* Template name: 404 Page Template */
    get_header(); 

    global $awp_options;

    $sidebar            = (isset($awp_options['fourohfour_page_sidebar_position']) && !empty($awp_options['fourohfour_page_sidebar_position']) ? $awp_options['fourohfour_page_sidebar_position'] : "");
    $default_sidebar    = (isset($awp_options['fourohfour_page_sidebar']) && !empty($awp_options['fourohfour_page_sidebar']) ? $awp_options['fourohfour_page_sidebar'] : "");     
    $classes            = content_classes($sidebar);

    $content_class      = $classes[0];
    $sidebar_class      = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>

    <div class="inner-page wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?>">
        <div class="error-message<?php echo (!empty($content_class) ? " " . $content_class : ""); ?>">
            <h2 class="error padding-10 margin-bottom-30 padding-top-none"><i class="fa fa-exclamation-circle exclamation margin-right-50"></i>404</h2>
            <em><?php _e("File not found", "automotive"); ?>.</em> 
        </div>

        <?php // sidebar 
            if(isset($sidebar) && !empty($sidebar) && $sidebar != "none" && isset($default_sidebar) && !empty($default_sidebar)){
                echo "<div class='" . $sidebar_class . " sidebar-widget side-content'>";
                dynamic_sidebar($default_sidebar);
                echo "</div>";
            }                   
        ?>

        <div class="clearfix"></div>
    </div>
    <!--container ends--> 

<?php get_footer(); ?>