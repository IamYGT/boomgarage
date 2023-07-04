<?php
//********************************************
//	Custom meta boxes
//***********************************************************
if(!function_exists("add_custom_boxes")){
	function add_custom_boxes(){
		$post_types = get_post_types();//array("post", "page");
		
		foreach($post_types as $post_type){
			add_meta_box( "secondary_title", __("Secondary Title", "automotive"), "secondary_title", $post_type, "advanced", "high", null);
			add_meta_box( "page_options", __("Page Options", "automotive"), "page_options", $post_type, "side", "core", null );
		}
	}
}

function page_options(){
	global $post, $awp_options;
	
	$sidebar            = get_post_meta($post->ID, "sidebar", true);
    $header_image       = get_post_meta($post->ID, "header_image", true);
    $no_header          = get_post_meta($post->ID, "no_header", true);
	
	$action_toggle      = get_post_meta($post->ID, "action_toggle", true);
	$action_text        = get_post_meta($post->ID, "action_text", true);
	$action_button_text = get_post_meta($post->ID, "action_button_text", true);
    $action_link        = get_post_meta($post->ID, "action_link", true);
    $action_class       = get_post_meta($post->ID, "action_class", true);

    // default sidebar for new pages
    if(empty($sidebar)){
        $sidebar = (isset($awp_options['default_sidebar']) && !empty($awp_options['default_sidebar']) ? $awp_options['default_sidebar'] : "");
    }
	?>
    <p><b><?php _e("Sidebar Position", "automotive"); ?></b></p>
    
    <select name="sidebar">
    	<option value='none'>None</option>
        <option value='left' <?php selected($sidebar, "left"); ?>>Left</option>
        <option value='right' <?php selected($sidebar, "right"); ?>>Right</option>
    </select>
    
    <hr>

    <p><b><?php _e("Header Image", "automotive"); ?></b></p>
    
    <button class="choose_image button button-primary" data-uploader-title="<?php _e("Select a header image", "automotive"); ?>" data-uploader-button-text="<?php _e("Select Image", "automotive"); ?>"><?php _e("Choose Header Image", "automotive"); ?></button>
    <input type="hidden" class="header_image_input" name="header_image" value="<?php echo trim($header_image); ?>" <?php echo (isset($header_image) && !empty($header_image) ? "data-id='" . auto_image_id($header_image) . "'" : ""); ?>>

    <div class='header_preview_area'>
    	<?php
		if(isset($header_image) && !empty($header_image)){
            $full   = wp_get_attachment_image_src($header_image, "full");
            $medium = wp_get_attachment_image_src($header_image, "medium");

			echo "<a href='" . $full[0] . "' target='_blank'><img src='" . $medium[0] . "' style='width: 100%; margin-top: 8px;'></a>";
		}
		?>
    </div>

    <br>

    <label><?php _e("No header area", "automotive"); ?>: <input type="checkbox" value="no_header" name="no_header"<?php echo (isset($no_header) && !empty($no_header) && $no_header == "no_header" ? " checked='checked'" : ""); ?>></label>
    
    <hr>

    <p><b><?php _e("Sidebar", "automotive"); ?></b></p>
    <select name="sidebar_area" style="width:100%;">
        <?php $custom_sidebars = (isset($awp_options['custom_sidebars']) && !empty($awp_options['custom_sidebars']) ? $awp_options['custom_sidebars'] : "");
            $default_sidebar = get_post_meta( $post->ID, "sidebar_area", true );
            echo "<option value='blog-sidebar'" . selected($default_sidebar, "blog-sidebar", false) . ">" . __("Blog Sidebar", "automotive") . "</option>";
            if(!empty($custom_sidebars)){
                foreach($custom_sidebars as $area){
                    echo "<option value='" . $area . "' " . selected($default_sidebar, $area, false) . ">" . $area . "</option>\n";
                }
            } ?>
    </select>

    <hr>

    <p><b><?php _e("Footer Area", "automotive"); ?></b></p>
    <select name="footer_area" style="width:100%;">
        <?php $footer_areas = (isset($awp_options['footer_widget_spots']) && !empty($awp_options['footer_widget_spots']) ? $awp_options['footer_widget_spots'] : "");
            $default_footer = get_post_meta( $post->ID, "footer_area", true );
            echo "<option value='default-footer'" . selected($default_footer, "default-footer", false) . ">" . __("Default Footer", "automotive") . "</option>";
            echo "<option value='no-footer'" . selected($default_footer, "no-footer", false) . ">" . __("No Footer", "automotive") . "</option>";
            if(!empty($footer_areas)){
                foreach($footer_areas as $area){
                    echo "<option value='" . $area . "' " . selected($default_footer, $area, false) . ">" . $area . "</option>\n";
                }
            } ?>
    </select>
    
    <hr>
    
    <p><b><?php _e("Call to action", "automotive"); ?></b> <input type='checkbox' class='call_to_action' name='call' value='action' <?php echo (isset($action_toggle) && $action_toggle == "on" ? " checked='checked'" : ""); ?> /></p>
    
    <div class='call_to_action_form'<?php echo (isset($action_toggle) && $action_toggle == "on" ? " style='display: block;'" : " style='display: none;'"); ?>>
    	<table border='0'>
        	<tr><td><?php _e("Text", "automotive"); ?>: </td><td><input type='text' name='action_text' value="<?php echo htmlspecialchars($action_text); ?>" /></td></tr>
            <tr><td><?php _e("Button Text", "automotive"); ?>: </td><td><input type='text' name='action_button_text' value='<?php echo $action_button_text; ?>' /></td></tr>
            <tr><td><?php _e("Button Link", "automotive"); ?>: </td><td><input type='text' name='action_link' value='<?php echo $action_link; ?>' /></td></tr>
            <tr><td><?php _e("Button Class", "automotive"); ?>: </td><td><input type='text' name='action_class' value='<?php echo $action_class; ?>' /></td></tr>
        </table>
    </div>

    <?php if(is_plugin_active( 'revslider/revslider.php' )){ ?>
	    <hr>

	    <p><b><?php _e("Slideshow", "automotive"); ?></b></p>
	    <select name="page_slideshow" style="width:100%;">
	    	<?php 
	    	global $wpdb;

	    	$default_slideshow = get_post_meta($post->ID, "page_slideshow", true);

	    	// Get Revolution Sliders			
			$rev_sliders = array();
			$rev_sliders['none'] = "No Slideshow";
			
			if($wpdb->get_var("SHOW TABLES LIKE '" . get_table_prefix() . "revslider_sliders'") == get_table_prefix() . "revslider_sliders") {
				$rev_sliders_query = $wpdb->get_results("SELECT title, alias FROM " . get_table_prefix() . "revslider_sliders");
				
				foreach($rev_sliders_query as $slider){
					$rev_sliders[$slider->alias] = stripslashes($slider->title);
				}
			}

			foreach($rev_sliders as $alias => $slider){
				echo "<option value='" . $alias . "' " . selected($default_slideshow, $alias, false) . ">" . $slider . "</option>\n";
			} ?>
	    </select>
	<?php
	}
}

add_action( 'add_meta_boxes', 'add_custom_boxes' );

if(!function_exists("add_after_editor")){
	function add_after_editor(){
		global $post, $wp_meta_boxes;
		
		do_meta_boxes(get_current_screen(), 'advanced', $post);
		
		$post_types = get_post_types();
		
		foreach($post_types as $post_type){
			unset($wp_meta_boxes[$post_type]['advanced']);
		}
	}
}

add_action("edit_form_after_title", "add_after_editor");

if(!function_exists("secondary_title")){
	function secondary_title(){
		global $post;
		
		$secondary_title = get_post_meta($post->ID, "secondary_title", true);
		echo "<input type='text' value='" . $secondary_title . "' name='secondary_title' style='width:100%;'/>";
	}
}

if(!function_exists("register_menu_pages")){
	function register_menu_pages(){
		global $post_metas;
		global $filterable;
		
		add_theme_page( 'edit.php?post_type=listings', "Options", "Options", 'manage_options', 'options', 'my_custom_submenu_page_callback');
	} 
}
?>