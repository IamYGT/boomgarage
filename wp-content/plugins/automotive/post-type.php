<?php

if(!function_exists("register_listing_post_type")){
    function register_listing_post_type() {
    	//********************************************
    	//	Register the post type
    	//***********************************************************

    	$labels = array(
    	  'name'          	    => __('Chip Tunning', 'listings'),
    	  'singular_name'		=> __('Chip Tunning', 'listings'),
    	  'add_new'			 	=> __('Yeni Ekle', 'listings'),
    	  'add_new_item'		=> __('Yeni Ekle', 'listings'),
    	  'edit_item'			=> __('Düzenle', 'listings'),
    	  'new_item'			=> __('Yeni Eklenenler', 'listings'),
    	  'all_items'			=> __('Bütün Eklenenler', 'listings'),
    	  'view_item' 		 	=> __('Görüntüle', 'listings'),
    	  'search_items'		=> __('Arama Yap', 'listings'),
    	  'not_found'          	=> __('Chip Tunning Bulunamadı', 'listings'),
    	  'not_found_in_trash' 	=> __('Çöpte Chip Tunning Bulunamadı',  'listings'),
    	  'menu_name'			=> __('Chip Tunning', 'listings')
    	);
      
    	$args = array(
    	  'labels'              => $labels,
    	  'public'              => true,
    	  'publicly_queryable' 	=> true,
    	  'show_ui'            	=> true, 
    	  'show_in_menu' 	    => true, 
    	  'query_var'          	=> true,
    	  'rewrite' 	        => array( 'slug' => 'chip-tunning-detay' ),
    	  'capability_type'    	=> 'post',
    	  'has_archive'        	=> true, 
    	  'hierarchical'       	=> false,
    	  'taxonomies' 			=> array('listing_category'), 
    	  'menu_position'      	=> null,
    	  'supports'			=> array('title', 'editor', 'comments')
    	); 
      
    	register_post_type( 'listings', $args );
      
    }
    add_action( 'init', 'register_listing_post_type', 0 );
}


/* Custom Columns */
function add_new_listings_columns($columns) {
    $new_columns['cb'] = '<input type="checkbox" />';
     
    $new_columns['title'] = __('Title', 'listings');

    $column_categories = get_column_categories();

    if(!empty($column_categories)){
	    foreach($column_categories as $column){
	    	$safe = str_replace(" ", "_", strtolower($column['singular']));
	    	$new_columns[$safe] = wpml_category_translate($column['singular'], "singular", $column['singular']);
	    }
	}
 
    $new_columns['date'] = __('Date', 'listings');
 
    return $new_columns;
}
add_filter('manage_edit-listings_columns', 'add_new_listings_columns');

 
function manage_listings_columns($column_name, $id) {
    $return = get_post_meta($id, $column_name, true);

    echo (isset($return) && !empty($return) ? $return : "");
}  
add_action('manage_listings_posts_custom_column', 'manage_listings_columns', 10, 2);


function order_column_register_sortable($columns){
    $column_categories = get_column_categories();

    if(!empty($column_categories)){
	    foreach($column_categories as $column){
	    	$safe = str_replace(" ", "_", strtolower($column['singular']));

	    	$columns[$safe] = $safe;
	    }
	}

  return $columns;
}
add_filter('manage_edit-listings_sortable_columns','order_column_register_sortable');


function custom_listings_orderby( $query ) {
    if( ! is_admin() )
        return;
 
    $orderby = $query->get( 'orderby');
    
    $column_categories = get_column_categories();

    if(!empty($column_categories)){
	    foreach($column_categories as $column){ 
	    	$safe = str_replace(" ", "_", strtolower($column['singular']));

		    if( $safe == $orderby ) {
		        $query->set('meta_key', $safe);
		        $query->set('orderby', ($column['compare_value'] != "=" ? 'meta_value_num' : 'meta_value') );
		    }

	    	$columns[$safe] = $safe;
	    }
	}
}
add_action( 'pre_get_posts', 'custom_listings_orderby' ); ?>