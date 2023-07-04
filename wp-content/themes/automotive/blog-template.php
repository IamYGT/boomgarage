<?php 
/* Template name: Blog Page Template */
get_header(); ?>
    
    <?php 
    	$sidebar       = get_post_meta(get_the_ID(), "sidebar", true); 
		
		$classes       = content_classes($sidebar);

		$content_class = $classes[0];
		$sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>
    
            <div class="inner-page row wp_page">
            	<div class="<?php echo $content_class; ?> page-content">
                	
            		<?php get_template_part("loop"); ?>
                    
            	</div>
                
                <?php // sidebar 
					if(isset($sidebar) && !empty($sidebar) && $sidebar != "none"){
						echo "<div class='" . $sidebar_class . " sidebar-widget side-content'>";
						dynamic_sidebar('blog-widget');
						echo "</div>";
					}					
				?>
            </div>
        <!--container ends--> 

<?php get_footer(); ?>
