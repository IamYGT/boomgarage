<?php



class Listing {

    public function __construct(){
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts_styles'));
    }

    public function enqueue_scripts_styles(){
        wp_register_script('wpml_class', JS_DIR . "wpml.class.js");
        wp_register_script('wpml_admin_class', JS_DIR . "wpml.admin.class.js");

        if(is_admin()){
            wp_enqueue_script('wpml_admin_class');
        } else {
            wp_enqueue_script('wpml_class');
        }
    }

    // construct function for converting the data on first run
    private function convert_listing_categories_wpml(){
        /* Added to Convert Listing Categories to Taxonomies! */
        $listing_categories             = get_option("listing_categories");
        $listing_categories_converted   = get_option("listing_categories_converted");

        // D($listing_categories);
        // D($listing_categories_converted);
        // die;

        if(isset($listing_categories) && !empty($listing_categories)){

            if(!empty($listing_categories) && empty($listing_categories_converted)){
                // set options
                if(isset($listing_categories['options']['terms']) && !empty($listing_categories['options']['terms'])){
                    $listing_options = $listing_categories['options']['terms'];
                    unset($listing_categories['options']);

                    foreach($listing_options as $key => $option){
                        wp_insert_term( $option, "listing_options" );
                    }
                }

                // convert categories
                foreach($listing_categories as $key => $category){
                    $singular       = (isset($category['singular'])       && !empty($category['singular'])       ? $category['singular'] : "");
                    $plural         = (isset($category['plural'])         && !empty($category['plural'])         ? $category['plural'] : "");
                    $filterable     = (isset($category['filterable'])     && !empty($category['filterable'])     ? "on" : "");
                    $use_on_listing = (isset($category['use_on_listing']) && !empty($category['use_on_listing']) ? "on" : "");
                    $column         = (isset($category['column'])         && !empty($category['column'])         ? "on" : "");
                    $compare_value  = (isset($category['compare_value'])  && !empty($category['compare_value'])  ? htmlentities($category['compare_value']) : "");
                    $currency       = (isset($category['currency'])       && !empty($category['currency'])       ? "on" : "");
                    $link_value     = (isset($category['link_value'])     && !empty($category['link_value'])     ? $category['link_value'] : "");
                    $sort_terms     = (isset($category['sort_terms'])     && !empty($category['sort_terms']) && $category['sort_terms'] == "ascending" ? "ascending" : "descending");
                    $terms          = (isset($category['terms'])          && !empty($category['terms'])          ? $category['terms'] : "");

                    $info_array = array(
                        "singular"          => $singular,
                        "plural"            => $plural,
                        "filterable"        => $filterable,
                        "use_on_listing"    => $use_on_listing,
                        "column"            => $column,
                        "compare_value"     => $compare_value,
                        "currency"          => $currency,
                        "link_value"        => $link_value,
                        "sort_terms"        => $sort_terms,
                    );

                    // insert parent category
                    $parent    = wp_insert_term( $singular, "listing_categories" );

                    if(!is_wp_error($parent)){
                        $parent_id = $parent['term_id'];

                        // set meta options
                        update_option("tax_meta_" . $parent_id, $info_array );

                        // child terms
                        if(!empty($terms)){
                            foreach($terms as $term){
                                wp_insert_term( $term, "listing_categories", array("parent" => $parent_id) );
                            }
                        }
                    }
                }

                // convert listings
                $listings = get_posts( array("post_type" => "listings", "posts_per_page" => -1) );

                if(!empty($listings) && !empty($listing_categories)){
                    foreach($listings as $key => $the_post){

                        // foreach old listing categories
                        $old_categories = array();

                        foreach($listing_categories as $key => $category){
                            $post_meta = get_post_meta( $the_post->ID, str_replace(" ", "_", mb_strtolower($category['singular'])), true );

                            if(isset($post_meta) && !empty($post_meta) && mb_strtolower($post_meta) != "none"){
                                $old_categories[$category['singular']] = $post_meta;
                            }
                        }

                        // foreach new listing categories
                        foreach($this->get_listing_categories() as $key => $category){
                            if(isset($old_categories[$category->options['singular']]) && !empty($old_categories[$category->options['singular']])){
                                $post_meta_term_id = "";

                                // get term id
                                foreach($category->terms as $key => $single_term){
                                    if($old_categories[$category->options['singular']] == $single_term->name){
                                        $post_meta_term_id = $single_term->term_id;
                                        break;
                                    }
                                }

                                if(!empty($post_meta_term_id)){
                                    update_post_meta( $the_post->ID, "listing_category_" . $category->term_id, $post_meta_term_id );
                                }
                            }
                        }

                        // convert multi options
                        $multi_options = get_post_meta( $the_post->ID, "multi_options", true );

                        if(!empty($multi_options)){
                            $new_multi_options = array();
                            $all_multi_options = $this->get_multi_options();
                            // $found_new_option  = "";

                            foreach($multi_options as $option){

                                // try and find term
                                if(!empty($all_multi_options)){
                                    foreach($all_multi_options as $single_option){
                                        if($option == $single_option->name){
                                            $new_multi_options[] = $single_option->term_id;
                                        }
                                    }
                                }
                            }

                            update_post_meta( $the_post->ID, "multi_options", $new_multi_options );
                        }
                    }
                }

                update_option( "listing_categories_converted", "true" );
            }
        }
    }

    // init listing categories page
    public function init_listing_categories_page(){
        //********************************************
        //  Register the categories taxonomy
        //***********************************************************
        $labels = array(
            'name'              => _x( 'Listing Categories', 'listings' ),
            'singular_name'     => _x( 'Listing Category', 'listings' ),
            'search_items'      => __( 'Search Listing Categories', 'listings' ),
            'all_items'         => __( 'All Listing Categories', 'listings' ),
            'edit_item'         => __( 'Edit Listing Category', 'listings' ),
            'update_item'       => __( 'Update Listing Category', 'listings' ),
            'add_new_item'      => __( 'Add New Listing Category', 'listings' ),
            'new_item_name'     => __( 'New Listing Category', 'listings' ),
            'menu_name'         => __( 'Listing Categories', 'listings' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'listing_categories' ),
        );

        register_taxonomy( 'listing_categories', array( 'listings' ), $args );

        //********************************************
        //  Register the options taxonomy
        //***********************************************************
        $labels = array(
            'name'              => _x( 'Options', 'listings' ),
            'singular_name'     => _x( 'Option', 'listings' ),
            'search_items'      => __( 'Search Options', 'listings' ),
            'all_items'         => __( 'All Options', 'listings' ),
            'edit_item'         => __( 'Edit Option', 'listings' ),
            'update_item'       => __( 'Update Option', 'listings' ),
            'add_new_item'      => __( 'Add New Option', 'listings' ),
            'new_item_name'     => __( 'New Option', 'listings' ),
            'menu_name'         => __( 'Vehicle Options', 'listings' ),
        );

        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'listing_options' ),
        );

        register_taxonomy( 'listing_options', array( 'listings' ), $args );

        //********************************************
        //  Taxonomy Meta
        //***********************************************************
        require_once(LISTING_HOME . "classes/taxonomy/Tax-meta-class.php");

        $config = array(
           'id'             => 'demo_meta_box',      
           'title'          => 'Listing Options',   
           'pages'          => array('listing_categories'),
           'context'        => 'normal',        
           'fields'         => array(),          
           'local_images'   => false,      
           'use_with_theme' => false     
        );

        $listing_meta = new Tax_Meta_Class($config);

        $listing_meta->addText('singular', 
            array('name'  => __('Singular', 'listings'), 
            )
        );

        $listing_meta->addText('plural', 
            array('name' => __('Plural', 'listings')
            )
        );

        $listing_meta->addSelect('compare_value', 
            array( htmlentities('=') =>'=',
                   htmlentities('<') =>'<', 
                   htmlentities('<=') =>'<=', 
                   htmlentities('>') =>'>', 
                   htmlentities('>=') =>'>='
            ),
            
            array('name' => __('Compare Value', 'listings'), 
                  'desc' => __('Change the way the value is compared, useful for numbers (mileage, fuel economy).', 'listings')
            )
        );

        $listing_meta->addCheckbox('filterable', 
            array('name' => __('Filterable', 'listings'), 
                  'desc' => __('Make this category display in filterable spots.', 'listings')
            )
        );

        $listing_meta->addCheckbox('currency', 
            array('name' => __('Currency', 'listings'), 
                  'desc' => __('Check this box if the current category is a currency or price.', 'listings')
            )
        );

        $listing_meta->addCheckbox('use_on_listing', 
            array('name' => __('Use on listing', 'listings'), 
                  'desc' => __('Make this category show on the listing information.', 'listings')
            )
        );

        $listing_meta->addCheckbox('column', 
            array('name' => __('Show Column', 'listings'), 
                  'desc' => __('Use this category as a searchable column under "All Listings"', 'listings')
            )
        );

        $listing_meta->addSelect('link_value', 
            array('none' => __('None', 'listings'),
                  'price' => __('Price', 'listings'),
                  'mpg' => __('MPG', 'listings')
            ),

            array('name' => __('Link Value', 'listings'), 
                  'desc' => __('Link this value to another default value to avoid entering information in twice.', 'listings')
            )
        );

        $listing_meta->addSelect('sort_terms', 
            array('ascending' => __('Ascending', 'listings'),
                  'descending' => __('Descending', 'listings')
            ),

            array('name' => __('Sort Terms', 'listings'), 
                  'desc' => __('Change the way the terms are sorted.', 'listings')
            )
        );

        $listing_meta->addCheckbox('default_orderby', 
            array('name' => __('Default Orderby', 'listings'), 
                  'desc' => __('Use this category as the default orderby on the listings page. Can only support a single category.', 'listings')
            )
        );

        $listing_meta->addTextarea('default_terms', 
            array('name' => __('Default Terms', 'listings'), 
                  'desc' => __('If you would rather display these terms in the dropdowns rather than the entered values, only works with numbers.<br>Enter each term on a new line', 'listings')
            )
        );

        $listing_meta->Finish();

        // Listing Options Options
        $config = array(
           'id'             => 'demo_meta_box',      
           'title'          => 'Listing Options',   
           'pages'          => array('listing_options'),
           'context'        => 'normal',        
           'fields'         => array(),          
           'local_images'   => false,      
           'use_with_theme' => false     
        );

        $options_meta = new Tax_Meta_Class($config);

        $options_meta->addCheckbox('default', 
            array('name' => 'Default', 
                  'desc' =>'Enable this option to be selected by default when creating new listings.'
            )
        );

        $options_meta->Finish();

        $this->convert_listing_categories_wpml();
    }

    // get option
    public function get_listing_categories_option(){
        // return get_option("listing_categories");
    }

    public function get_value($category, $value){
        $first_level = array("term_id", "name", "slug", "term_group", "term_taxonomy_id", "taxonomy", "description", "parent", "count", "terms");

        if(in_array($value, $first_level)){
            return (isset($category->$value) && !empty($category->$value) ? $category->$value : "");
        } else {
            return (isset($category->options[$value]) && !empty($category->options[$value]) ? $category->options[$value] : "");
        }
    }

    public function auto_quickSort(&$array) {
        $cur = 1;
        $stack[1]['l'] = 0;
        $stack[1]['r'] = count($array) - 1;
        do {
            $l = $stack[$cur]['l'];
            $r = $stack[$cur]['r'];
            $cur--;
            do {
                $i = $l;
                $j = $r;
                $tmp = $array[(int)(($l + $r) / 2) ];

                // partion the array in two parts.
                // left from $tmp are with smaller values,
                // right from $tmp are with bigger ones

                do {
                    while ($array[$i]->name < $tmp->name) $i++;
                    while ($tmp->name < $array[$j]->name) $j--;

                    // swap elements from the two sides

                    if ($i <= $j) {
                        $w = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $w;
                        $i++;
                        $j--;
                    }
                }

                while ($i <= $j);
                if ($i < $r) {
                    $cur++;
                    $stack[$cur]['l'] = $i;
                    $stack[$cur]['r'] = $r;
                }

                $r = $j;
            }

            while ($l < $r);
        }

        while ($cur != 0);
    }

    public function auto_set_term_ids($terms){
        $new_terms = new stdClass;
        foreach ($terms as $key => $term) {
            $new_terms->{$term->term_id} = $term;
        }

        return $new_terms;
    }

    // get all listing categories
    public function get_listing_categories($multi_options = false){
        $taxonomy_name = "listing_categories";

        global $sitepress;
        if(isset($sitepress) && !empty($sitepress) && $lang){
            $sitepress->switch_lang($lang, true);
        }

        $listing_categories = get_terms( $taxonomy_name, array( 'hide_empty' => false, 'parent' => 0 ) );

        if(!empty($listing_categories) && !is_wp_error($listing_categories)){
            foreach($listing_categories as $key => $term){
                $listing_categories[$key]->options = get_option("tax_meta_" . $term->term_id);

                // check for options
                $term_children = get_term_children( $term->term_id, $taxonomy_name );
                if(!empty($term_children)){
                    $listing_categories[$key]->terms = new stdClass;
                    $temp_term_array = array();

                    foreach($term_children as $child_term){
                        $child_term_val = get_term_by( 'id', $child_term, $taxonomy_name );
                        // $listing_categories[$key]->terms->$child_term = $child_term_val;
                        $temp_term_array[] = $child_term_val;
                    }

                    $this->auto_quickSort($temp_term_array);

                    if($term->options['sort_terms'] == "descending"){
                        $temp_term_array = array_reverse($temp_term_array);
                    }

                    $listing_categories[$key]->terms = (object)$this->auto_set_term_ids($temp_term_array);
                }
                
            }
        } else {
            $listing_categories = array();
        }

        return (array)$listing_categories;
    }

    public function get_multi_options(){
        $listing_options = get_terms( "listing_options", array( 'hide_empty' => false, 'parent' => 0 ) );

        foreach($listing_options as $key => $value){
            $options[$value->term_id] = $value;

            $default = get_option("tax_meta_" . $value->term_id);

            if(isset($default['default']) && !empty($default['default'])){
                $options[$value->term_id]->default = $default['default'];
            }
        }

        return $options;
    }

    // get single instance of listing category
    public function get_single_listing_category($category_id){
        $categories = $this->get_listing_categories();
        $return     = "";

        if(!empty($categories)){
            foreach($categories as $key => $category){
                if($category->term_id == $category_id){
                    $return = $category;
                    break;
                }
            }
        }

        return (array)$return;
    }

    // get all filterable listing categories
    public function get_filterable_listing_categories($lang = false){
        $filterable_categories = array();
        $all_categories        = $this->get_listing_categories(false, $lang);

        if(!empty($all_categories)){
            foreach($all_categories as $key => $category){
                if(isset($category->options['filterable']) && $category->options['filterable'] == "on"){
                    $filterable_categories[] = $category;
                }
            }
        }

        return (array)$filterable_categories;
    }

    // get listing categories used on listing
    public function get_use_on_listing_categories(){    
        $all_categories  = $this->get_listing_categories();
        $use_on_listings = array();

        if(!empty($all_categories)){
            foreach($all_categories as $key => $category){
                if(isset($category->options['use_on_listing']) && $category->options['use_on_listing'] == "on"){
                    $use_on_listings[] = $category;
                }
            }
        }

        // limit 10
        if(count($use_on_listings) > 10){
            $use_on_listings = array_slice($use_on_listings, 0, 10);
        }

        return $use_on_listings;
    }

    // get the values from the URL
    public function get_url_listing_categories(){
        $return_array = array();

        $get_holder = $_GET;
        $filterable = $this->get_filterable_listing_categories((isset($get_holder['wpml_lang']) && !empty($get_holder['wpml_lang']) ? $get_holder['wpml_lang'] : false));

        foreach($filterable as $key => $filter){
            $slug     = ($filter->slug == "year" ? "yr" : $filter->slug);
            $singular = $filter->options['singular'];
            $plural   = $filter->options['plural'];
            $compare  = $filter->options['compare_value'];
            $terms    = $filter->terms;
            $default_terms = array();

            $get_val  = (isset($get_holder[$slug]) && !empty($get_holder[$slug]) ? $get_holder[$slug] : "");

            $look_for_val = (isset($filter->options['default_terms']) && !empty($filter->options['default_terms']) ? true : false);

            if($look_for_val){
                $default_terms = explode("\n", $filter->options['default_terms']);
            }

            // look for term
            if(!empty($get_val)){

                if(!empty($terms)){
                    if($look_for_val){

                        if(in_array((int)$get_val, $default_terms)){
                            $current_term = $get_val;
                            $filter->selected_slug = $term->slug;
                            $filter->selected_name = $term->name;
                        }
                    } else {

                        foreach($terms as $key => $term){
                            if(is_array($get_val)){
                                foreach($get_val as $single_get_val){
                                    if($term->slug == $single_get_val){
                                        $current_term[] = $term;
                                        $filter->selected_slug = $term->slug;
                                        $filter->selected_name = $term->name;
                                    }
                                }
                            } else {                   
                                if($term->slug == $get_val){
                                    // set term
                                    $current_term = $term;
                                    $filter->selected_slug = $term->slug;
                                    $filter->selected_name = $term->name;
                                    break;
                                }                                   
                            }
                        }
                    }
                }

                // set labels
                if(is_array($current_term) && $look_for_val){
                    $item_label_1 = $current_term[0];
                    $item_label_2 = $current_term[1];
                } elseif(is_array($current_term)){
                    $item_label_1 = $current_term[0]->name;
                    $item_label_2 = $current_term[1]->name;                         
                } elseif($look_for_val){
                    $item_label   = $current_term;
                } else {                            
                    $item_label   = $current_term->name;
                }

                // format currency
                if(isset($filter->options['currency']) && $filter->options['currency'] == "on" && is_array($current_term) && $look_for_val){
                    $item_label_1 = format_currency($item_label_1);
                    $item_label_2 = format_currency($item_label_2);
                } elseif(isset($filter->options['currency']) && $filter->options['currency'] == "on" && is_array($current_term)){
                    $item_label_1 = format_currency($item_label_1);
                    $item_label_2 = format_currency($item_label_2);
                } elseif(isset($filter->options['currency']) && $filter->options['currency'] == "on" && $look_for_val){
                    $item_label = format_currency($item_label);
                } elseif(isset($filter->options['currency']) && $filter->options['currency'] == "on"){
                    $item_label = format_currency($item_label);
                }

                // if(isset($current_term) && !empty($current_term) && is_array($current_term) && $look_for_val){
                //     $filters .= "<li data-type='" . $slug . "'><a href=''><i class='fa fa-times-circle'></i> " . $singular . ": <span data-val='" . $current_term[0] . "'>" . $item_label_1 . "</span> - <span data-val='" . $current_term[1] . "'>" . $item_label_2 . "</span></a></li>";
                // } elseif(isset($current_term) && !empty($current_term) && is_array($current_term)){
                //     $filters .= "<li data-type='" . $slug . "'><a href=''><i class='fa fa-times-circle'></i> " . $singular . ": " . ($compare != "=" ? $compare . " " : "") . " <span data-val='" . $current_term[0]->slug . "'>" . $item_label_1 . "</span>" . ($compare != "=" ? $compare . " " : "") . " - <span data-val='" . $current_term[1]->slug . "'>" . $item_label_2 . "</span></a></li>";
                // } elseif(isset($current_term) && !empty($current_term) && $look_for_val){
                //     $filters .= "<li data-type='" . $slug . "'><a href=''><i class='fa fa-times-circle'></i> " . $singular . ": " . ($compare != "=" ? $compare . " " : "") . " <span data-val='" . $current_term . "'>" . $item_label . "</span></a></li>";
                // } elseif(isset($current_term) && !empty($current_term)){
                //     $filters .= "<li data-type='" . $slug . "'><a href=''><i class='fa fa-times-circle'></i> " . $singular . ": " . ($compare != "=" ? $compare . " " : "") . " <span data-val='" . $current_term->slug . "'>" . $item_label . "</span></a></li>";
                // }


                $return_array[$key] = $filter;
            }
        }

        // /*echo "<pre>";
        // print_r($this->get_filterable_listing_categories());
        // echo "</pre>";*/

        return $return_array;
    }

    public function get_top_filter_listing_categories(){

        $url_listing_categories = $this->get_url_listing_categories();
        $return_array           = array();

        if(!empty($url_listing_categories)){
            foreach($url_listing_categories as $key => $category){
                $return_array[] = array(
                    "label"     => $category->options['singular'],
                    "url_safe"  => $category->slug,
                    "compare"   => html_entity_decode($category->options['compare_value']),
                    "value"     => $category->selected_name
                );
            }
        }

        return $return_array;
    }

    private function listing_category_key_to_term($array){
        if(!empty($array)){
            foreach($array as $key => $value){
                $array[$value->name] = $value;
                unset($array[$key]);
            }
        }

        return $array;
    }
    
    // takes entire listing category, not just terms
    //
    // needs data-key attr
    public function get_listing_category_terms($listing_category){
        // var_dump($listing_category);
        $return_array   = array();
        $selected_terms = $this->listing_category_key_to_term($this->get_url_listing_categories());

        $terms          = $this->get_value($listing_category, "terms");
        $sort_terms     = $this->get_value($listing_category, "sort_terms");
        $singular       = $this->get_value($listing_category, "singular");

        if(!empty($listing_category) && !empty($terms)){
            foreach($terms as $key => $text) {
                $return_array[] = array(
                    "text"      => $text->name,
                    "data-key"  => $text->term_id,
                    "value"     => $text->slug,
                    "selected"  => (isset($selected_terms[$singular]->selected_slug) && $selected_terms[$singular]->selected_slug == $text->name ? "true" : "false")
                );
            }
        } else {
            $return_array[] = array("text" => __("No options", "listings"));
        }

        return $return_array;
    }

    public function listing_args($get_or_post, $all = false, $ajax_array = false){
        global $lwp_options, $post;
        
        if(is_array($ajax_array)){
                $get_or_post = array_merge($get_or_post, $ajax_array);

                foreach($get_or_post as $key => $value){
                    if(strstr($key, "_")){
                        $get_or_post[str_replace("_", "-", $key)] = $value;
                        unset($get_or_post[$key]);
                    }

                    if($key == "paged"){
                        $_REQUEST['paged'] = $value;
                    }
                }
            }

        
        // $paged      = (isset($_POST['page']) && !empty($_POST['page']) ? $_POST['page'] : (isset($_POST['paged']) && !empty($_POST['paged']) ? $_POST['paged'] : ""));
        $paged      = (isset($_REQUEST['paged']) && !empty($_REQUEST['paged']) ? $_REQUEST['paged'] : 1);
        $lwp_options['listings_amount'] = (isset($lwp_options['listings_amount']) && !empty($lwp_options['listings_amount']) ? $lwp_options['listings_amount'] : "");
        $sort_items = array();

        // order by
        // $orderby = listing_orderby();
        $default_orderby = (isset($lwp_options['sortby_default']) && $lwp_options['sortby_default'] == 0 ? "DESC" : "ASC");

        if(isset($get_or_post['order']) && !empty($get_or_post['order'])){
            $ordering = explode("|", $get_or_post['order']);
        } elseif(!empty($listing_orderby)) {
            $selected = reset($listing_orderby);
            $selected = key($listing_orderby);
            
            $ordering[0] = $selected;
            $ordering[1] = $default_orderby;
        }

        $args = array(
                  'post_type'           => 'listings',
                  'meta_query'          => array(),
                  'paged'               => (isset($paged) && !empty($paged) ? $paged : get_query_var('paged')),
                  'posts_per_page'      => ($lwp_options['listings_amount']),
                  'order'               => (isset($ordering[1]) && !empty($ordering[1]) && $ordering[1] != "undefined" ? $ordering[1] : $default_orderby),
                  'suppress_filters'    => false
                );

        // keywords
        if(isset($_REQUEST['keywords']) && !empty($_REQUEST['keywords'])){
            $args['s'] = sanitize_text_field($_REQUEST['keywords']);
        }
                
        $data = array();    

        // if(isset($orderby) && !empty($orderby) && isset($lwp_options['sortby']) && $lwp_options['sortby'] != 0){
        //     $args['meta_key'] = $orderby['key'];
        //     $args['orderby']  = $orderby['type'];
        // }

        if(isset($lwp_options['sortby']) && $lwp_options['sortby'] != 0){

            if(!empty($ordering[0]) && !empty($ordering[1])){

                $listing_orderby = get_option("listing_orderby");

                $args['meta_key'] = $ordering[0];
                $args['orderby']  = $listing_orderby[$ordering[0]];
            } else {
                if(!empty($listing_orderby)){
                    $selected = reset($listing_orderby);
                    $selected = key($listing_orderby);

                    $args['meta_key'] = $selected;
                    $args['orderby']  = $listing_orderby[$selected];
                }
            }
        }

        $filterable_categories = $this->get_filterable_listing_categories((isset($get_or_post['wpml_lang']) && !empty($get_or_post['wpml_lang']) ? $get_or_post['wpml_lang'] : false));

        foreach($filterable_categories as $filter){
            $slug        = ($filter->slug == "year" ? "yr" : $filter->slug);
            $terms       = $filter->terms; 
            $current_val = (isset($get_or_post[$slug]) && !empty($get_or_post[$slug]) ? $get_or_post[$slug] : "");

            if(!empty($current_val)){

                // default terms
                $look_for_val = (isset($filter->options['default_terms']) && !empty($filter->options['default_terms']) ? true : false);

                if($look_for_val){
                    $terms = explode("\n", $filter->options['default_terms']);
                }

                // find term id
                if(!empty($terms)){

                    foreach($terms as $key => $term){
                        if(is_array($current_val) && $look_for_val){
                            if(in_array((int)$term, $current_val)){
                                $between_array[] = $term;
                            }
                        } elseif(is_array($current_val) && in_array($term->slug, $current_val)){
                            $between_array[] = $term->term_id;
                        } else {
                            if($term->slug == $current_val || $look_for_val){
                                // set data since found
                                $current_data = array("key"   => "listing_category_" . ($look_for_val ? "value_" : "") . $filter->term_id,
                                                      "value" => trim($look_for_val ? $current_val : $term->term_id));

                                if(isset($filter->options['compare_value']) && $filter->options['compare_value'] != "="){
                                    $current_data['compare'] = html_entity_decode($filter->options['compare_value']);
                                    $current_data['type']    = "numeric";
                                }

                                $data[] = $current_data;

                                break;
                            }
                        }
                    }

                    if(is_array($current_val)){
                        // min/max
                        $data[] = array(
                            'key'     => "listing_category_" . ($look_for_val ? "value_" : "") . $filter->term_id,
                            'value'   => $between_array,
                            'type'    => 'numeric',
                            'compare' => 'BETWEEN'
                        );
                    }
                }
            }
        }

        // filter params
        if(isset($get_or_post['filter_params']) && !empty($get_or_post['filter_params'])){
            $filter_params = json_decode(stripslashes($get_or_post['filter_params']));

            // no page id for me
            unset($filter_params->page_id);

            foreach($filter_params as $index => $param){
                unset($param->length);

                $min = $param->{0};
                $max = $param->{1};

                $data[] = array(
                    'key'     => str_replace(" ", "_", mb_strtolower($index)),
                    'value'   => array($min, $max),
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN'
                );
            }
        }

        // additional categories
        if(isset($lwp_options['additional_categories']['value']) && !empty($lwp_options['additional_categories']['value'])){
            foreach($lwp_options['additional_categories']['value'] as $additional_category){
                $check_handle = str_replace(" ", "_", mb_strtolower($additional_category));

                // in url
                if(isset($get_or_post[$check_handle]) && !empty($get_or_post[$check_handle])){
                    $data[] = array("key" => $check_handle, "value" => 1);
                }
            }
        }

        // hide sold vehicles
        if(isset($_GET['show_only_sold'])){
            $data[] = array("key"   => "car_sold",
                            "value" => "1");
        } elseif(empty($lwp_options['inventory_no_sold']) && !isset($_GET['show_sold'])){
            $data[] = array("key"     => "car_sold",
                                "value" => "2");
        }

        // order by
        if(isset($get_or_post['order_by']) && isset($get_or_post['order'])){
            $args['orderby'] = $get_or_post['order_by'];
            $args['order']   = $get_or_post['order'];
        }
        
        if(!empty($data)){
            $args['meta_query'] = $data;
        }
        
        // D($get_or_post);
        // D($args);

        // $args = apply_filters( "listing_args", $args );
        
        return array($args);//, $sort_items);
    }

    public function single_listing_details_sidebar($post_meta){
        global $post;
        $listing_categories = $this->get_listing_categories();

        foreach($listing_categories as $key => $category){
            $current_val  = get_post_meta( $post->ID, "listing_category_" . $category->term_id, true );

            if(isset($current_val) && !empty($current_val) && $current_val != "none"){
                $current_term = (isset($category->terms->{$current_val}->name) && !empty($category->terms->{$current_val}->name) ? $category->terms->{$current_val}->name : __("None", "listings"));
                $current_term = (isset($category->options['currency']) && $category->options['currency'] == "on" ? format_currency($current_term) : $current_term);

                echo "<tr><td>" . $category->name . ": </td><td>" . $current_term . "</td></tr>";
            }
        } 
    }

    public function meta_boxes(){
        global $post;

        $listing_categories = $this->get_listing_categories();

        // D($listing_categories);

        foreach($listing_categories as $category){
            if(empty($category->options['link_value']) || $category->options['link_value'] == "none"){
                echo "<tr>";

                echo "<td>" . $category->name . "</td>";

                // dropdown
                echo "<td><select name='listing_category_" . $category->term_id . "' style='width:100%;'>";
                echo "<option value='none'>" . __("None", "listings") . "</option>";
                foreach($category->terms as $key => $term){
                    $current_val = get_post_meta( $post->ID, "listing_category_" . $category->term_id, true );
                    echo "<option value='" . $term->term_id . "'" . selected( $term->term_id, $current_val, false ) . ">" . $term->name . "</option>";
                }
                echo "</select></td>";

                echo "<td>";

                echo "<span class='add_new_tax_term' data-parent-id='" . $category->term_id . "'>+ " . __("Add New Term", "listings") . "</span>";

                // add new term div
                echo "<div class='new_term_box' data-show-id='" . $category->term_id . "'>";
                echo "<input class='term_input_val' type='text' style='margin-left: 0;' />
                        <button class='button submit_new_term'  data-exact=''>" . __("Add New Term", "listings") . "</button>
                      </div>";
                echo "</div>";

                echo "</td>";

                echo "</tr>";
            }
        }
    }

    // delete term
    public function delete_listing_category_term($category, $term_id){    
        // $listing_categories = $this->get_listing_categories(true);
        // $current_category   = (isset($listing_categories[$category]) && !empty($listing_categories[$category]) ? $listing_categories[$category] : "");

        // // update the var
        // $listing_categories[$category] = $current_category;
        
        // unset($listing_categories[$category]['terms'][$term_id]);

        // update_option('listing_categories', $listing_categories);
    }

    public function meta_box_options(){
        global $post;

        $single_category = $this->get_single_listing_category('options');
        $options         = $this->get_multi_options();

        $selected = get_post_meta($post->ID, 'options', true);

        /* Default Options */
        $default_options = get_option("options_default_auto");
            
        $multi_options = get_post_meta($post->ID, "multi_options", true);

        // natcasesort($options);
        
        $i = 0;
        $last_option = end($options);
        echo "<table>";
        foreach($options as $option_id => $option){
            $option_name = stripslashes($option->name);
            
            echo ($i == 0 ? "<tr>" : "");

            echo "<td><label><input type='checkbox' value='" . $option_id . "' name='multi_options[]'" . (is_array($multi_options) && (in_array($option_id, $multi_options)) || (is_edit_page('new') && is_array($default_options) && in_array($option_id, $default_options)) ? " checked='checked'" : "") . ">" . $option_name . "</label></td>\n";
            
            $i++;

            if($i == 3 || $option == $last_option){
                $i = 0;
                echo "</tr>\n";
            }
        }
        echo "</table>";
    }

    public function save_listing_categories($post_id){
        $listing_categories = $this->get_listing_categories();

        // D(get_post_meta_all($post_id));
        // die;

        if(!empty($listing_categories)){
            foreach($listing_categories as $category){
                $category_post = (isset($_POST['listing_category_' . $category->term_id]) && !empty($_POST['listing_category_' . $category->term_id]) ? $_POST['listing_category_' . $category->term_id] : "");

                if(!empty($category_post)){
                    update_post_meta( $post_id, 'listing_category_' . $category->term_id, $_POST['listing_category_' . $category->term_id]);

                    // if(isset($category->options['default_terms']) && !empty($category->options['default_terms'])){
                        $value = (isset($category->terms->{$_POST['listing_category_' . $category->term_id]}->name) && !empty($category->terms->{$_POST['listing_category_' . $category->term_id]}->name) ? $category->terms->{$_POST['listing_category_' . $category->term_id]}->name : __("None", "listings"));
                        update_post_meta( $post_id, 'listing_category_value_' . $category->term_id, $value );
                    // }
                }

                if(isset($_POST['options']) && !empty($_POST['options'])){
                    if(!empty($category->options['link_value']) && $category->options['link_value'] != "none"){
                        if($category->options['link_value'] == "price"){
                            $option_value = preg_replace('/\D/', '', $_POST['options']['price']['value']);
                        } else if($category->options['link_value'] == "mpg"){
                            $option_value = $_POST['options']['city_mpg']['value'] . " " . $_POST['options']['city_mpg']['text'] . " / " . $_POST['options']['highway_mpg']['value'] . " " . $_POST['options']['highway_mpg']['text'];
                        }

                        if(isset($option_value) && !empty($option_value)){
                            // make array to check for existing term
                            $check_array = array();
                            foreach($category->terms as $key => $term){
                                $check_array[$term->term_id] = $term->name;
                            }

                            if(!in_array((int)$option_value, $check_array)){
                                $inserted = wp_insert_term( $option_value, "listing_categories", array("parent" => $category->term_id) );

                                if(!is_wp_error($inserted)){
                                    $option_value_id = $inserted['term_id'];
                                }
                            } else {
                                $option_value_id = array_search($option_value, $check_array);
                            }

                            update_post_meta( $post_id, 'listing_category_' . $category->term_id, $option_value_id);

                            // default terms
                            if(isset($category->options['default_terms']) && !empty($category->options['default_terms'])){
                                update_post_meta( $post_id, 'listing_category_value_' . $category->term_id, $option_value );
                            }
                        }
                    }
                }
            }
        }
    }

    public function car_comparison($all_post_meta){
        $listing_categories = $this->get_listing_categories();
        
        foreach($listing_categories as $category){
            $value = (isset($all_post_meta['listing_category_' . $category->term_id]) && !empty($all_post_meta['listing_category_' . $category->term_id]) ? $all_post_meta['listing_category_' . $category->term_id] : "");
            $value = (isset($category->terms->{$value}) && !empty($category->terms->{$value}) ? $category->terms->{$value}->name : "" );

            if(isset($category->options['currency']) && $category->options['currency'] == "on"){
                $value = format_currency($value);
            }

            echo "<tr><td>" . $category->options['singular'] . ": </td><td>" . (!empty($value) ? $value : __("None", "listings") ) . "</td></tr>";
        }
    }

    public function multi_option_term_value($multi_options){
        $return_array       = array();
        $multi_option_terms = $this->get_multi_options();

        if(!empty($multi_options)){
            foreach($multi_options as $option){
                $value = (isset($multi_option_terms[$option]->name) && !empty($multi_option_terms[$option]->name) ? $multi_option_terms[$option]->name : "");

                if(!empty($value)){
                    $return_array[$option] = $value;
                }
            }
        }
    
        return $return_array;
    }

    public function add_new_listing_category_term($parent_id, $value){
        $return    = array(
            "response" => "success"
        );

        // run check
        if(!empty($parent_id) && !empty($value)){

            $insert_term = wp_insert_term( $value, 'listing_categories', array("parent" => $parent_id) );

            if(!is_wp_error($insert_term)){
                $return['term_id']  = $insert_term['term_id'];
            } else {
                $return['response'] = "failed";
            }

        } else {
            $return['response'] = "failed";
        }

        echo json_encode($return);
    }

    /*public function car_comparison_options($multi_options, $class){
        $multi_option_terms = $this->get_multi_options();
        $multi_options      = $this->multi_option_term_value($multi_options);

        switch($class){
            case 6:
                $columns = 3;
                $column_class = 4;
                break;
            
            case 4:
                $columns = 2;
                $column_class = 6;
                break;
                
            case 3:
                $columns = 1;
                $column_class = 12;
                break;
        }
        
        $amount = ceil(count($multi_options) / $columns); 
        $new    = array_chunk($multi_options, $amount);
        
        echo "<div class='row'>";
        foreach($new as $section){
            echo "<ul class='options col-lg-" . $column_class . "'>";
            foreach($section as $option){
                // $option = (isset($multi_option_terms[$option]->name) && !empty($multi_option_terms[$option]->name) ? $multi_option_terms[$option]->name : "");

                echo "<li>" . $option . "</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    }*/
}

?>