<?php
//********************************************
//	Save custom meta fields
//***********************************************************
function save_custom_meta($post_id){
	// page/post options
	
	$post_types = get_post_types();
	
	//unset($post_types['listings']);
	
	if(in_array(get_post_type(), $post_types)){//get_post_type() == "page" || get_post_type() == "post"){
		$secondary_title    = (isset($_POST['secondary_title']) && !empty($_POST['secondary_title']) ? $_POST['secondary_title'] : "");
		$sidebar            = (isset($_POST['sidebar']) && !empty($_POST['sidebar']) ? $_POST['sidebar'] : "");
		$header_image       = (isset($_POST['header_image']) && !empty($_POST['header_image']) ? $_POST['header_image'] : "");
		$footer_area        = (isset($_POST['footer_area']) && !empty($_POST['footer_area']) ? $_POST['footer_area'] : "");
		$sidebar_area       = (isset($_POST['sidebar_area']) && !empty($_POST['sidebar_area']) ? $_POST['sidebar_area'] : "");
		$action_text        = (isset($_POST['action_text']) && !empty($_POST['action_text']) ? $_POST['action_text'] : "");
		$action_button_text = (isset($_POST['action_button_text']) && !empty($_POST['action_button_text']) ? $_POST['action_button_text'] : "");
		$action_link        = (isset($_POST['action_link']) && !empty($_POST['action_link']) ? $_POST['action_link'] : "");
		$action_class       = (isset($_POST['action_class']) && !empty($_POST['action_class']) ? $_POST['action_class'] : "");
		$page_slideshow     = (isset($_POST['page_slideshow']) && !empty($_POST['page_slideshow']) ? $_POST['page_slideshow'] : "");
		$no_header          = (isset($_POST['no_header']) && !empty($_POST['no_header']) ? $_POST['no_header'] : "");
		
		update_post_meta((int)$post_id, "secondary_title", (string)esc_html($secondary_title));
		
		if(!empty($sidebar)){
			update_post_meta((int)$post_id, "sidebar", (string)$sidebar);
		}
		if(!empty($header_image)){
			update_post_meta((int)$post_id, "header_image", (string)$header_image);
		}
		if(!empty($footer_area)){
			update_post_meta((int)$post_id, "footer_area", (string)$footer_area);
		}
		if(!empty($sidebar_area)){
			update_post_meta((int)$post_id, "sidebar_area", (string)$sidebar_area);
		}
		if(!empty($page_slideshow)){
			update_post_meta((int)$post_id, "page_slideshow", (string)$page_slideshow);
		}	
		
		update_post_meta((int)$post_id, "no_header", (string)$no_header);
		
		
		
		if(isset($_POST['call']) && $_POST['call'] == "action"){
			update_post_meta((int)$post_id, "action_toggle", "on");
			update_post_meta((int)$post_id, "action_text", (string)$action_text);
			update_post_meta((int)$post_id, "action_button_text", (string)$action_button_text);
			update_post_meta((int)$post_id, "action_link", (string)$action_link);
			update_post_meta((int)$post_id, "action_class", (string)$action_class);
		} else {
			update_post_meta((int)$post_id, "action_toggle", "off");			
		}
	} 
}

add_action('save_post', 'save_custom_meta'); ?>