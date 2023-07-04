<?php get_header();

global $post;
if (have_posts()) : while (have_posts()) : the_post(); 

function portfolio_details(){
	global $post;
	$project_details = get_post_meta($post->ID, "project_details", true);
		
	if(isset($project_details) && !empty($project_details[0])){
        echo "<ul>";
		foreach($project_details as $project_detail){
			echo "<li><i class='fa fa-check-circle'></i>" . $project_detail . "</li>";
		}
        echo "</ul>";
	}
}

function portfolio_related(){
	global $post;
	
	$terms    = get_the_terms( $post->ID , 'project-type');
	$term_ids = (empty($terms) ? array() : array_values( wp_list_pluck($terms, 'term_id') ) );
	$i        = 1;

	$related_query = array(
		'post_type' 	 => 'listings_portfolio',
		'tax_query' 	 => array( ),
		'posts_per_page' => 4,
		'orderby'        => 'rand',
		'post__not_in'   => array($post->ID)
	);

	if(!empty($term_ids)){
		$related_query['tax_query'][] = array(
						  'taxonomy' => 'project-type',
						  'field'    => 'id',
						  'terms'    => $term_ids,
						  'operator' => 'IN' 
					   );
	}
  
	$second_query  = new WP_Query( $related_query );
	 
	echo "<div class=\"related_post clearfix\">";
	   
	//Loop through posts and display...
	if($second_query->have_posts()) : while ($second_query->have_posts() ) : $second_query->the_post(); 
		$format      = get_post_meta(get_the_ID(), "format", true);
		$content     = get_post_meta(get_the_ID(), "portfolio_content", true);
		$description = get_post_meta(get_the_ID(), "short_description", true);
		
		
		if($format == "image"){
			$class     = "picture-o";			
			$thumbnail = get_the_post_thumbnail(get_the_ID(), 'related_portfolio');
		} elseif($format == "video"){
			$class     = "youtube-play";
			$video_id  = youtube_video_id($content);
			$thumbnail = "<img src='http://img.youtube.com/vi/" . $video_id . "/maxresdefault.jpg' alt='' class='img-responsive'>";
		} elseif($format == "gallery"){
			$class     = "plus-square-o";
			
			$thumbnail = "<img src='" . (isset($content[0]['related']['url']) && !empty($content[0]['related']['url']) ? $content[0]['related']['url'] : auto_image($content[0], "full", true)) . "' alt='' class='img-responsive'>";
		}
	?>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="car-block"><a href="<?php echo get_permalink(get_the_ID()); ?>">
                <div class="img-flex"> <span class="align-center"><i class="fa fa-3x fa-<?php echo $class; ?>"></i></span> <?php echo $thumbnail; ?> </div>
                <div class="car-block-bottom">
                    <h2><?php the_title(); ?></h2>
                    <h4><?php echo $description; ?></h4>
                </div>
                </a> 
            </div>
        </div>
   <?php $i++; endwhile; 
   	else: 
   		echo "<span class='no_related_projects'>" . __("No related projects", "listings") . "</span>"; 
   	endif; 
	
	echo "</div>";
}

function portfolio_layout($layout){ 
	global $post, $lwp_options;
	
	$format  = get_post_meta($post->ID, "format", true);
    $content = get_post_meta($post->ID, "portfolio_content", true);
    $links   = get_post_meta($post->ID, "portfolio_links", true);
	
	if($layout == "wide"){ ?>
	<div class="container">
    	<div class="page-content">
    	<?php                                    
		switch($format){
			case "image":
				if( has_post_thumbnail() ){
					the_post_thumbnail();
				}
			break;
			
			case "video":
				$video_id = youtube_video_id($content);
				echo "<div class=\"video-container\"><iframe src=\"http://www.youtube.com/embed/" . $video_id . "?rel=0\" height=\"auto\" width=\"100%\" allowfullscreen=\"\" frameborder=\"0\" class=\"portfolio_video " . $layout . "\"></iframe></div>";
			break;
			
			case "gallery":					
				if(!empty($content)){
					echo "<!--OPEN OF SLIDER-->";
					echo "<div class=\"slider padding-left-none padding-right-none padding-bottom-40\">";
						echo "<div class=\"flexslider flexslider2\">";
							echo "<ul class=\"slides item\">";
                                $i = 0;
							    foreach($content as $image){
								    echo "<li>";
                                    echo (isset($links[$i]) && !empty($links[$i]) ? "<a href='" . $links[$i] . "'>" : "");                                                                
                                    echo "<img src='" . auto_image($image, "full", true) . "' alt=''>";
                                    echo (isset($links[$i]) && !empty($links[$i]) ? "</a>" : "");
                                    echo "</li>";
                                    $i++;
							    }
							echo "</ul>";
						echo "</div>";
					echo "</div>";
					echo "<!--CLOSE OF SLIDER-->";   
				} else {
					echo __("No images in slideshow", "listings");
				}
			break;
		} ?>
    
    
        <div class="row padding-bottom-40">
            <div class="col-lg-8 col-md-8 col-sm-8 left-content padding-left-none">
                <div class="right_site_job">
                    <div class="job  margin-top-30 sm-padding-bottom-40 xs-padding-bottom-40">
                        <h2><?php echo $lwp_options['job_description_title']; ?></h2>
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 right-content padding-right-none xs-padding-left-none">
                <div class="right_site_job">
                    <div class="project_details margin-top-30">
                        <h2><?php echo $lwp_options['project_details_title']; ?></h2>
                            <?php portfolio_details();	?>
                    </div>
                </div>
            </div>
        </div>
            
        <?php if(isset($lwp_options['show_related_projects']) && $lwp_options['show_related_projects'] == 1){ ?>
        <div class="row">
            <div class="project_wrapper clearfix">
                <h4 class="related_project_head margin-top-10 padding-bottom-15 margin-top-none"><?php echo $lwp_options['related_projects_title']; ?></h4>
                <?php portfolio_related(); ?>
            </div>
        </div>
        <?php } ?>
        </div>  
    </div>

<?php } else { ?>
        <div class="container">
        	<div class='page-content'>
        	<div class="row">
                <div class="col-lg-8 col-md-8 col-xs-12 left-content padding-left-none xs-padding-bottom-40 sm-padding-bottom-40"> 
                    <?php
                                    
                    switch($format){
                        case "image":
                            if( has_post_thumbnail()){
                                the_post_thumbnail();
                            }
                        break;
                        
                        case "video":
                            $video_id = youtube_video_id($content);
							echo "<div class=\"video-container\"><iframe src=\"http://www.youtube.com/embed/" . $video_id . "?rel=0\" height=\"auto\" width=\"100%\" allowfullscreen=\"\" frameborder=\"0\" class=\"portfolio_video " . $layout . "\"></iframe></div>";
                        break;
                        
                        case "gallery":					
							if(!empty($content)){
								echo "<!--OPEN OF SLIDER-->";
								echo "<div class=\"slider padding-left-none padding-right-none\">";
									echo "<div class=\"flexslider flexslider2\">";
										echo "<ul class=\"slides item\">";
                                            $i = 0;
                                            foreach($content as $image){
                                                echo "<li>";
                                                echo (isset($links[$i]) && !empty($links[$i]) ? "<a href='" . $links[$i] . "'>" : "");                                                                
                                                echo "<img src='" . auto_image($image, "auto_portfolio", true) . "' alt=''>";
                                                echo (isset($links[$i]) && !empty($links[$i]) ? "</a>" : "");
                                                echo "</li>";
                                                $i++;
                                            }
										echo "</ul>";
									echo "</div>";
								echo "</div>";
								echo "<!--CLOSE OF SLIDER-->";   
							} else {
								echo __("No images in slideshow", "listings");
							}
                        break;
                    } ?>
                </div>
                
                <div class="col-lg-4 col-md-4 right-content padding-right-none xs-padding-left-none sm-padding-left-none">
                    <div class="right_site_job">
                        <div class="job sm-padding-bottom-40 xs-padding-bottom-40 xs-padding-top-30">
                            <h2><?php echo $lwp_options['job_description_title']; ?></h2>
                            <?php the_content(); ?>
                        </div>
                        <div class="project_details margin-top-30">
                            <h2><?php echo $lwp_options['project_details_title']; ?></h2>
                                <?php portfolio_details();	?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if(isset($lwp_options['show_related_projects']) && $lwp_options['show_related_projects'] == 1){ ?>
            <div class="row">
                <div class="project_wrapper clearfix margin-top-30">
                    <h4 class="related_project_head margin-top-10 padding-bottom-15 margin-top-none"><?php echo $lwp_options['related_projects_title']; ?></h4>
                    <?php portfolio_related(); ?>
                </div>
            </div>
            <?php } ?>
            </div>
        </div>
<?php
	}
}

global $post;

$layout  = get_post_meta($post->ID, "layout", true);
$format  = get_post_meta($post->ID, "format", true);
$content = get_post_meta($post->ID, "portfolio_content", true);

echo "<div class='inner-page portfolio_content row'>";

portfolio_layout($layout); ?>

</div>

<?php endwhile; endif; ?>

<?php get_footer(); ?>