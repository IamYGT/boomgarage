<?php 
global $wp_query;

if (have_posts()): while (have_posts()) : the_post(); 
	
	echo blog_post();

endwhile; 	

$big = 999999999; // need an unlikely integer

echo '<ul class="pagination">';

$pages = paginate_links( array(
    'base'          => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'format'        => '?paged=%#%',
    'current'       => max( 1, $paged ),
    'total'         => $wp_query->max_num_pages,
    'prev_text'     => '&laquo;',
    'next_text'     => '&raquo;',
    'type'          => 'array',
) );

if(!empty($pages)){
    foreach ( $pages as $page ) {
        echo "<li>" . $page . "</li>\n";
    }
}
echo "</ul>";

wp_reset_query(); ?>

<?php else: ?>

	<!-- article -->
	<article>
		<h2><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h2>
	</article>
	<!-- /article -->

<?php endif; ?>