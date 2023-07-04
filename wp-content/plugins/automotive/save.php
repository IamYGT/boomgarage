<?php
//********************************************
//	Save custom meta fields
//***********************************************************
function plugin_save_custom_meta($post_id){
	// page/post options
	global $lwp_options;
	
	$post_types = get_post_types();
	
	unset($post_types['listings']);
	
	$layout = (isset($_POST['layout']) && !empty($_POST['layout']) ? $_POST['layout'] : "");
	if(isset($layout) && !empty($layout)){
		update_post_meta((int)$post_id, "layout", (string)$layout);
	}
	
	if(get_post_type() == "listings"){
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		   return $post_id;
		} else {
			if(isset($_POST['location_map']) && !empty($_POST['location_map'])){
				update_post_meta((int)$post_id, "location_map", $_POST['location_map']);
			}
			
			if(isset($_POST['other_comments']) && !empty($_POST['other_comments'])){
				update_post_meta((int)$post_id, "other_comments", $_POST['other_comments']);
			}
			
			if(isset($_POST['technical_specifications']) && !empty($_POST['technical_specifications'])){
				update_post_meta((int)$post_id, "technical_specifications", $_POST['technical_specifications']);
			}
			
			if(isset($_POST['verified']) && !empty($_POST['verified'])){
				update_post_meta((int)$post_id, "verified", $_POST['verified']);
			} else {
				delete_post_meta((int)$post_id, "verified" );
			}
			
			if(isset($_POST['additional_details']) && !empty($_POST['additional_details'])){
				update_post_meta((int)$post_id, "additional_details", $_POST['additional_details']);
			}
			
			// secondary title
			if(isset($_POST['secondary_title']) && !empty($_POST['secondary_title'])){
				update_post_meta((int)$post_id, "secondary_title", $_POST['secondary_title']);
			}
			
			// custom post meta
			$listing_categories = get_listing_categories();

			foreach($listing_categories as $category){
				$skey = strtolower(str_replace(array(" ", "."), "_", $category['singular']));
				
				$value = $org_key = (isset($_POST[$skey]) && !empty($_POST[$skey]) ? $_POST[$skey] : "");
				
				//if(!empty($value)){
				if(empty($value) && $category['compare_value'] != "="){
					$value = 0;
				} elseif(empty($value)) {
					$value = __("None", "listings");
				}

				// linked values
				$category['link_value'] = (isset($category['link_value']) && !empty($category['link_value']) ? $category['link_value'] : "");

				if(isset($_POST['options']) && !empty($_POST['options'])){
					if(!empty($category['link_value']) && $category['link_value'] != "none"){
						if($category['link_value'] == "price"){
							$value = $_POST['options']['price']['value'];
							update_post_meta((int)$post_id, $skey, $value); // because $org_key wont be set
						} else if($category['link_value'] == "mpg"){
							$value = $_POST['options']['city_mpg']['value'] . " " . $_POST['options']['city_mpg']['text'] . " / " . $_POST['options']['highway_mpg']['value'] . " " . $_POST['options']['highway_mpg']['text'];
							update_post_meta((int)$post_id, $skey, $value); // because $org_key wont be set
						}
					}
				}

				if(!empty($org_key)){
					update_post_meta((int)$post_id, $skey, $value);
				}
			}

			if(!empty($lwp_options['additional_categories']['value'])){
				foreach($lwp_options['additional_categories']['value'] as $category){
					$safe_category = str_replace(" ", "_", strtolower($category));
					$value         = (isset($_POST['additional_categories']['value'][$safe_category]) && !empty($_POST['additional_categories']['value'][$safe_category]) ? $_POST['additional_categories']['value'][$safe_category] : "");

					update_post_meta($post_id, $safe_category, $value);
				}
			}

			// car sold
			$car_sold = (isset($_POST['car_sold']) && !empty($_POST['car_sold']) ? $_POST['car_sold'] : "");
			if(!empty($car_sold)){
				update_post_meta($post_id, "car_sold", $car_sold);
			} else {
				update_post_meta($post_id, "car_sold", 2);
			}

			// pdf_brochure_input
			if(isset($_POST['pdf_brochure_input'])){
				update_post_meta($post_id, "pdf_brochure_input", $_POST['pdf_brochure_input']);
			}
			
			$multi_options = (isset($_POST['multi_options']) && !empty($_POST['multi_options']) ? $_POST['multi_options'] : "");
			update_post_meta($post_id, "multi_options", $multi_options);
			

			$_POST['options']['price']['value']       = (isset($_POST['options']['price']['value']) && !empty($_POST['options']['price']['value']) ? preg_replace('/\D/', '', $_POST['options']['price']['value']) : "");
			$_POST['options']['price']['original']    = (isset($_POST['options']['price']['original']) && !empty($_POST['options']['price']['original']) ? preg_replace('/\D/', '', $_POST['options']['price']['original']) : "");
			// $_POST['options']['city_mpg']['value']    = (isset($_POST['options']['city_mpg']['value']) && !empty($_POST['options']['city_mpg']['value']) ? preg_replace('/\D/', '', $_POST['options']['city_mpg']['value']) : "");
			// $_POST['options']['highway_mpg']['value'] = (isset($_POST['options']['highway_mpg']['value']) && !empty($_POST['options']['highway_mpg']['value']) ? preg_replace('/\D/', '', $_POST['options']['highway_mpg']['value']) : "");
						
			$post_options = (isset($_POST['options']) ? serialize($_POST['options']) : null);
			
			update_post_meta($post_id, "listing_options", $post_options);
			
						
			if( isset($_POST['gallery_images']) && !empty($_POST['gallery_images']) ){
				$images = $_POST['gallery_images'];
				
				$gallery_images = (isset($images) ? serialize($images) : null);
			   
				if(!empty($_POST['gallery_images'])){					
					global $slider_thumbnails;
					
					$save_gallery_images = array();
					
					foreach($_POST['gallery_images'] as $gallery_image){
						$save_gallery_images[] = $gallery_image;

						/*array(
							'thumb'   => automotive_image_resize($gallery_image, $slider_thumbnails['width'], $slider_thumbnails['height']),
							'slider'  => automotive_image_resize($gallery_image, $slider_thumbnails['slider']['width'], $slider_thumbnails['slider']['height']),			
							'listing' => automotive_image_resize($gallery_image, $slider_thumbnails['listing']['width'], $slider_thumbnails['listing']['height']),				
							'full'    => $gallery_image
						);*/
					}
										
					update_post_meta($post_id, "gallery_images", $save_gallery_images);					
				} else {
					// update_post_meta($post_id, "gallery_images", "");
				}
			} else {
				// update_post_meta($post_id, "gallery_images", "");
			}
		}
	}
}

add_action('save_post', 'plugin_save_custom_meta'); ?>