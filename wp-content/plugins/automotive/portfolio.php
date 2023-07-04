<?php
add_action("init", "portfolio_register", 0); 
add_action("admin_init", "add_portfolio_meta"); 
add_action("save_post", "save_details");

function add_portfolio_meta(){ 
	add_meta_box("portfolio_format-meta", "Format", "portfolio_format", "listings_portfolio", "side", "core"); 
	add_meta_box("portfolio_content-meta", "Portfolio Content", "content_box", "listings_portfolio", "normal", "core");
	add_meta_box("portfolio_project-details", "Project Details", "project_details", "listings_portfolio", "side", "core");
}   
 
function portfolio_register() {  
    $args = array(  
        'label' 		 	=> __('Portfolio Items', 'listings'),  
        'singular_label'	=> __('Project', 'listings'),  
        'public' 		 	=> true,  
        'show_ui' 			=> true,  
        'capability_type' 	=> 'post',  
        'hierarchical' 		=> false,  
		'query_var' 		=> 'portfolio',
        'supports' 			=> array('title', 'editor', 'thumbnail'),
		'labels' 			=> array('not_found' 	=> __('No portfolio items found', 'listings'),
						  			 'add_new_item' => __('Add a new portfolio item', 'listings'))  
       );  
    register_post_type( 'listings_portfolio' , $args ); 
	
	
	register_taxonomy("project-type", array("listings_portfolio"), array("hierarchical" => false, "label" => __("Categories", 'listings'), "singular_label" => __("Category", 'listings')));
	register_taxonomy("portfolio_in", array("listings_portfolio"), array("hierarchical" => false, "label" => __("Portfolios", 'listings'), "singular_label" => __("Portfolio", 'listings'), 'labels' => array('add_new_item' => __('Add new Portfolio', 'listings'))));
	
	//remove_post_type_support( 'listings_portfolio', 'editor' ); 
	//flush_rewrite_rules();
} 

function project_details(){
	global $post;
	
	$project_details   = get_post_meta($post->ID, "project_details", true);
	
	echo "<div class='project_details'>";
	
	if(isset($project_details) && !empty($project_details)){
		foreach($project_details as $project_detail){
			echo "<input type='text' name='project_details[]' class='widefat' value='" . $project_detail . "'>";
		}
	} else {
		echo "<input type='text' name='project_details[]' class='widefat'>";
	}
	echo "<div class='new_details'></div>";
	echo "<span class='button btn add_detail'>" . __("Add A Detail", "listings") . "</span>";
	echo "<span class='button btn remove_detail'>" . __("Remove Last Detail", "listings") . "</span>";
	
	echo "</div>";
}

function content_box(){
	global $post;
	
	$format = get_post_meta($post->ID, "format", true);
	
	if(isset($format) && !empty($format)){
		portfolio_editor($format);
	} else {
		echo "<h2 style='margin: 15px 0 15px 15px;'>";
		_e("Select a format form the right sidebar", "listings");
		echo "</h2>";
	}
}

function portfolio_format() { 
	global $post; 
	
	$format  = get_post_meta($post->ID, "format", true);
	$formats = array("image", "gallery", "video");
	$layout  = get_post_meta($post->ID, "layout", true); 
	
	$short_description = get_post_meta($post->ID, "short_description", true);
	
	echo "<p>" . __("Short Description", "listings");
	echo "<input type='text' name='short_description' value='" . (isset($short_description) && !empty($short_description) ? $short_description : "") . "'></p><hr style='width: 108%; margin-left: -10px; border-top-color: #fff; border-bottom-color: #dfdfdf; border-top: 1px;'>";
	?>
    <div id="post-formats-select">
    	<?php foreach($formats as $single_format){ ?>
		<input type="radio" name="portfolio_post_format" class="portfolio-post-format" id="post-format-<?php echo $single_format; ?>" value="<?php echo $single_format; ?>" <?php checked($format, $single_format); ?> /> 
        <label for="post-format-<?php echo $single_format; ?>" class="post-format-icon post-format-<?php echo $single_format; ?>"><?php echo ucwords($single_format); ?></label><br />
        <?php } ?>
	</div>
    <hr style='width: 108%; margin-left: -10px; border-top-color: #fff; border-bottom-color: #dfdfdf; border-top: 1px;'>
    <p><b><?php _e("Layout", "listings"); ?></b></p>

    <select name="layout">
        <option value='wide' <?php selected($layout, "wide"); ?>><?php _e("Wide", "listings"); ?></option>
        <option value='split' <?php selected($layout, "split"); ?>><?php _e("Split", "listings"); ?></option>
    </select>
	<?php 
}

function save_details() { 
	if( "listings_portfolio" == get_post_type() ){
		global $post; 
		
		$save_format = (isset($_POST["portfolio_post_format"]) ? $_POST["portfolio_post_format"] : null);
		//$post->ID  = (isset($post->ID) ? $post->ID : null);
		
		update_post_meta($post->ID, "format", $save_format); 
		
		if(isset($_POST['portfolio_image']) && !empty($_POST['portfolio_image'])){
			update_post_meta($post->ID, "portfolio_content", $_POST['portfolio_image']);
		} elseif(isset($_POST['portfolio_video']) && !empty($_POST['portfolio_video'])){
			update_post_meta($post->ID, "portfolio_content", $_POST['portfolio_video']);
		} elseif(isset($_POST['gallery_images']) && !empty($_POST['gallery_images'])){                                
			global $slider_thumbnails;
				
			$save_gallery_images = array();
			
			if(!empty($_POST['gallery_images'])){		
				
				foreach($_POST['gallery_images'] as $gallery_image){
                    $save_gallery_images[] = $gallery_image;

					/*$save_gallery_images[] = array(
						'thumb'       => automotive_image_resize($gallery_image, $slider_thumbnails['width'], $slider_thumbnails['height']),
						'slider'      => automotive_image_resize($gallery_image, $slider_thumbnails['slider']['width'], $slider_thumbnails['slider']['height']),	
						'related'     => automotive_image_resize($gallery_image, 270, 140),
						'full_slider' => automotive_image_resize($gallery_image, 1170, 450),
						'full'        => $gallery_image
					);*/
				}
			}
				
			update_post_meta($post->ID, "portfolio_content", $save_gallery_images);
		}
		
		if(isset($_POST['project_details']) && !empty($_POST['project_details'])){			
			update_post_meta($post->ID, "project_details", $_POST['project_details']);
		}
        
        if(isset($_POST['short_description']) && !empty($_POST['short_description'])){
            update_post_meta($post->ID, "short_description", $_POST['short_description']);
        }
        
        if(isset($_POST['portfolio_links']) && !empty($_POST['portfolio_links'])){
            update_post_meta($post->ID, "portfolio_links", $_POST['portfolio_links']);
        }
	}
}

function portfolio_editor($format = null){
	global $post;
	
	if(is_ajax()){
		$format  = $_POST['format'];
		$post_id = (isset($_POST['post_id']) && !empty($_POST['post_id']) ? $_POST['post_id'] : "");
	} else {
		$post_id = $post->ID;
	}
	
    $content   = get_post_meta($post_id, "portfolio_content", true);
    $links     = get_post_meta($post_id, "portfolio_links", true);
	$og_format = get_post_meta($post_id, "format", true);
	
	switch($format){
		case "image":
			echo __("Featured post will be used", "listings") . ".";
			break;
		
		case "gallery": ?>
        <table id="gallery_images">
			<?php 
            if(isset($content) && !empty($content) && $og_format == "gallery"){
                $gallery_images = $content;

                                
                global $slider_thumbnails;
                
                $width  = $slider_thumbnails['width'];
                $height = $slider_thumbnails['height'];
                
                $i = 1;
                echo "<tbody>";
                foreach($gallery_images as $gallery_image){
					echo "<tr><td><div class='top_header'>" . __("Image", "listings") . " #{$i}</div>";
					echo "<div class='image_preview'><img src='" . auto_image($gallery_image, "auto_thumb", true) . "'></div>";
					echo "<div class='buttons'><span class='button add_image_gallery' data-id='" . $i . "'>" . __( 'Change image', 'listings' ) . "</span> ";
					echo "<span class='button make_default_image" . ($i == 1 ? " active_image" : "") . "'>" . __( 'Set default image', 'listings' ) . "</span> ";
					echo "<span class='button delete_image'>" . __( 'Delete image', 'listings' ) . "</span> ";
					echo "<span class='button move_image'>" . __( 'Move Image', 'listings' ) . "</span><input type='text' name='portfolio_links[" . $i . "]' value='" . (isset($links[$i]) && !empty($links[$i]) ? $links[$i] : "") . "' placeholder='" . __("Image link", "listings") . "'></div>";
					echo "<input type='hidden' name='gallery_images[]' value='" . $gallery_image . "'>";
					echo "</td></tr>";
					$i++;
				}
                echo "</tbody>";
            } else { ?>
            	<tr><td><div class="top_header"><?php _e("Image", "listings"); ?> #1</div><div class="image_preview"><?php _e("No Image", "listings"); ?></div><div class="buttons"><span class="button add_image_gallery" data-id="1"><?php _e("Set image", "listings"); ?></span> </div></td></tr>                
            <?php } ?>
        </table>
        <button class='add_image button button-primary'><?php _e("Add Image", "listings"); ?></button>
        
        <div class='clear'></div>
        <?php
			break;
		
		case "video":
			echo __("Video URL", "listings") . ": <input id='portfolio_video' type='text' value='" . (isset($content) && !empty($content) && $og_format == "video" ? $content : "") . "' name='portfolio_video'><br>";
			echo "<div class='video_preview'>";
			if(isset($content) && !empty($content) && $og_format == "video"){
				$video_id = youtube_video_id($content);
				echo "<br><iframe src=\"http://www.youtube.com/embed/" . $video_id . "?rel=0\" height=\"315\" width=\"560\" allowfullscreen=\"\" frameborder=\"0\"></iframe>";
			}
			echo "</div>";
			break;
	}
	
	if(is_ajax()){
		die;
	}
}
add_action('wp_ajax_portfolio_editor', 'portfolio_editor');
add_action('wp_ajax_nopriv_portfolio_editor', 'portfolio_editor'); 

?>