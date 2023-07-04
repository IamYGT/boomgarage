<?php get_header(); 

global $awp_options; ?>
    
    <?php if (have_posts()): while (have_posts()) : the_post(); 

        $secondary_title = get_post_meta(get_the_ID(), "secondary_title", true); 
        $sidebar       = get_post_meta(get_current_id(), "sidebar", true); 
        
        $classes       = content_classes($sidebar);

        $content_class = $classes[0];
        $sidebar_class = (isset($classes[1]) && !empty($classes[1]) ? $classes[1] : ""); ?>
    
		<div class="inner-page blog-post row is_sidebar">
            <div class="blog-content">

            <?php 
            if( has_post_thumbnail() && isset($awp_options['featured_image_blog']) && $awp_options['featured_image_blog'] == 1 ){
                $url = wp_get_attachment_image_src(get_post_thumbnail_id(), "full");

                echo "<a href='" . $url[0] . "' class='fancybox'>";
                the_post_thumbnail( "large_featured_image", 
                    array(
                        "class" => "attachment-large_featured_image margin-bottom-40"
                    ) 
                );
                echo "</a>";
            } ?>

            <div class="page-content <?php echo $content_class; ?>">
				<div class="blog-title">
                    <h2<?php echo (empty($secondary_title) ? " class='margin-bottom-25'" : ""); ?>><?php the_title(); ?></h2>
                    <?php echo (!empty($secondary_title) ? "<strong class='margin-top-5 margin-bottom-25'>" . $secondary_title. "</strong>" : ""); ?>
                </div>
				<ul class="margin-top-10">
                    <li class="fa fa-calendar"><span class="theme_font"><?php the_time('F j, Y') ?></span></li>
                    <li class="fa fa-folder-open">
                    <?php
                    $categories      = get_the_category();
                    $categories_list = $tooltip_cats = "";
                    $cat_inc         = 0;

                    if($categories) {
                        foreach($categories as $category) {
                            if($cat_inc < 4){
                                $categories_list .= "<a href='" . get_category_link($category->term_id ) . "'>" . $category->cat_name . "</a>, ";
                            } else {                                
                                $tooltip_cats .= "<a href='" . get_category_link($category->term_id ) . "'>" . $category->cat_name . "</a><br>";
                            }
                                        
                            $cat_inc++;
                        }
                    }

                    echo (isset($categories_list) && !empty($categories_list) ? substr($categories_list, 0, -2) : __("Not categorized", "automotive"));

                    // if more than 5
                    if(!empty($tooltip_cats)){
                        echo ", <a class='' data-toggle=\"popover\" data-placement=\"top\" data-content=\"" . $tooltip_cats . "\" data-html=\"true\">" . __("More Categories", "automotive") . "...</a>";
                    }
                    ?>
                    </li>
                    <li class="fa fa-user"><span class="theme_font"><?php _e("Posted by", "automotive"); ?></span> <?php the_author_posts_link(); ?> </li>
                    <li class="fa fa-comments"><?php comments_popup_link( __( 'Leave your thoughts', 'automotive' ), __( '1 Comment', 'automotive' ), __( '% Comments', 'automotive' )); ?></li>
				</ul>
				<div class="post-entry clearfix"> 
                	<?php the_content(); ?>
					<div class="blog-end margin-top-20">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 read-more">
                            <?php if(isset($awp_options['social_share_buttons']) && $awp_options['social_share_buttons'] == 1){ ?>
                        	<ul class="social-likes blog_social" data-url="<?php echo get_permalink(); ?>" data-title="<?php the_title(); ?>">
                                <li class="facebook" title="<?php _e("Share link on Facebook", "automotive"); ?>"></li>
                                <li class="plusone" title="<?php _e("Share link on Google+", "automotive"); ?>"></li>
                                <li class="pinterest" title="<?php _e("Share image on Pinterest", "automotive"); ?>"></li>
                                <li class="twitter" title="<?php _e("Share link on Twitter", "automotive"); ?>"></li>
                            </ul>
                            <?php } ?>
                        </div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                        	<span class="fa fa-tags tags">
								<?php
                                $posttags = get_the_tags();
								$tags     = $tooltip_tags = "";
                                $tag_inc  = 0;

                                if ($posttags) {
                                    foreach($posttags as $tag) {
                                        if($tag_inc < 4){
                                            $tags .= "<a href='" . get_tag_link($tag->term_id) . "' title='" . $tag->name . " " . __("Tag", "automotive") . "'>" . $tag->name . "</a>, ";
                                        } else {
                                            $tooltip_tags .= "<a href='" . get_tag_link($tag->term_id) . "' title='" . $tag->name . " " . __("Tag", "automotive") . "'>" . $tag->name . "</a><br>";
                                        }
                                        
                                        $tag_inc++;
                                    }							
									echo substr($tags, 0, -2);

                                    // if more than 5
                                    if(!empty($tooltip_tags)){
                                        echo ", <a class='' data-toggle=\"popover\" data-placement=\"top\" data-content=\"" . $tooltip_tags . "\" data-html=\"true\">" . __("More Tags", "automotive") . "...</a>";
                                    }
                                }
                                ?>
                            </span>
                        </div>
					</div>
                </div>

                <?php wp_link_pages( array('before' => '<p class="margin-top-20">' . __( 'Pages:' ), 'after' => '</p>') ); ?>

				<div class="clearfix"></div>
				<div class="comments margin-top-30 margin-bottom-40">
                    <?php comments_template(); ?>
				</div>
			</div>
                
            <?php // sidebar 
                $default_sidebar = get_post_meta( $post->ID, "sidebar_area", true );

                if(isset($sidebar) && !empty($sidebar) && $sidebar != "none" && isset($default_sidebar) && !empty($default_sidebar)){
                    echo "<div class='" . $sidebar_class . " sidebar-widget side-content'>";
                    dynamic_sidebar($default_sidebar);
                    echo "</div>";
                }                   
            ?>
			<div class="clearfix"></div>
		</div>
        
	</div>
        
    <?php endwhile; ?>

	<?php else: ?>

		<!-- article -->
		<article>

			<h1><?php _e( 'Sorry, nothing to display.', 'automotive' ); ?></h1>

		</article>
		<!-- /article -->

	<?php endif; ?>

<?php get_footer(); ?>
