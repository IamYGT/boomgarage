<?php get_header(); 

if (have_posts()) : while (have_posts()) : the_post();

    listing_content();
    
endwhile; ?>

<?php else : ?>
<?php _e("Post not found", "listings"); ?>!
<?php endif; ?>

<?php get_footer(); ?>