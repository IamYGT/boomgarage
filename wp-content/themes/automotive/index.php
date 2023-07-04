<?php get_header();

$sidebar       = get_post_meta(get_queried_object_id(), "sidebar", true); 

$classes       = content_classes($sidebar);

$content_class = $classes[0];
$sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>

    <div class="inner-page wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?> blog-container">
       	<div class="<?php echo $content_class; ?> page-content">

			<?php get_template_part('loop'); ?>
	        <?php get_template_part('pagination'); ?>

	    </div>
                
        <?php // sidebar 
			if(isset($sidebar) && !empty($sidebar) && $sidebar != "none"){
				echo "<div class='" . $sidebar_class . " sidebar-widget side-content'>";
				dynamic_sidebar('blog-widget');
				echo "</div>";
			}					
		?>
    </div>

<?php get_footer(); ?>
