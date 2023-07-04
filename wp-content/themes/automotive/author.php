<?php get_header(); ?>

		<?php if (have_posts()): the_post(); 

		$sidebar       = get_post_meta(get_current_id(), "sidebar", true); 
		
		$classes       = content_classes($sidebar);

		$content_class = $classes[0];
		$sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>
    
        <div class="inner-page row wp_page<?php echo (isset($sidebar) && !empty($sidebar) ? " is_sidebar" : " no_sidebar"); ?>">
        	<!-- <div class="<?php echo $content_class; ?> page-content"> -->
        	<div id="post-<?php the_ID(); ?>" <?php echo post_class($content_class . " page-content post-entry"); ?>>

				<h3 class="margin-bottom-40"><?php _e( 'Author Archives for ', 'automotive' ); echo get_the_author(); ?></h3>

				<?php if ( get_the_author_meta('description')) : ?>

				<?php echo get_avatar(get_the_author_meta('user_email')); ?>

					<h3 class="margin-bottom-40"><?php _e( 'About ', 'automotive' ); echo get_the_author() ; ?></h3>

					<?php echo wpautop( get_the_author_meta('description') ); ?>

				<?php endif; ?>

				<?php get_template_part("loop"); ?>

			</div>
		</div>

		<?php else: ?>

			<!-- article -->
			<article>

				<h3><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h3>

			</article>
			<!-- /article -->

		<?php endif; ?>

<?php get_footer(); ?>
