<?php
///// start session
if (!session_id()) {
    session_start();
}

///// register page
function register_csv_menu_item() {
    add_submenu_page( 'edit.php?post_type=listings', __('Automotive Listings Import', 'listings'), __('File Import', 'listings'), 'manage_options', 'file-import', 'automotive_file_import' ); 
}
add_action('admin_menu', 'register_csv_menu_item');


/* errors
/* * * * */
function admin_errors_auto(){
    $error = (isset($_GET['error']) && !empty($_GET['error']) ? sanitize_text_field($_GET['error']) : "");

    if(!empty($error)){
        echo "<div class='error'><span class='error_text'>";

        if($error == "file"){
            _e("The file uploaded wasn't a valid XML or CSV file, please try again.", "listings");
        } elseif($error == "url"){
            _e("The URL submitted wasn't a valid URL, please try again.", "listings");
        } elseif($error == "int_file"){
            _e("The file must not contain numbers as the column title.", "listings");
        }

        echo "</span></div>";
    }
}
add_action( 'admin_notices', 'admin_errors_auto' );

function file_type_check_auto() {
    $good_to_go = true;

    if(isset($_POST['import_submit_auto'])){
        
        $uploaded_file  = $_FILES['import_upload'];
        $file_location  = $uploaded_file['name'];
        $file_type      = strtolower(pathinfo($file_location, PATHINFO_EXTENSION));

        if($file_type == "csv"){
            $rows = parse_csv_local($uploaded_file['tmp_name']);
        }

        if(isset($rows) && !empty($rows)){
            foreach($rows[0] as $key => $val){
                if(is_int($key)){
                    $good_to_go = false;
                }
            }
        }

        if(!$good_to_go){            
            header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=int_file' ));
        } elseif(isset($file_type) && ($file_type != "csv" && $file_type != "xml")) {
            header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=file' ));
        }


    } elseif(isset($_POST['url_automotive'])){
        $url = sanitize_text_field( $_POST['url_automotive'] );

        if(filter_var($url, FILTER_VALIDATE_URL)){
            $file_content = wp_remote_get( $url );
            $file_type    = strtolower($file_content['headers']['content-type']);

            $file_content = $file_content['body'];
            // $file_type    = substr($file_type, -3);
            // new way to detect file types            

            // generate temp file to process
            $temp_file  = tmpfile();
            fwrite($temp_file, $file_content);
            fseek($temp_file, 0);

            // grab file location
            $meta           = stream_get_meta_data($temp_file);
            $file_location  = $meta['uri'];

            if(strpos($file_type, "csv") !== false){
                $rows = parse_csv_local($file_location);
            } elseif(strpos($file_type, "xml") !== false){
                $good_to_go = true;
            }

            if(isset($rows) && !empty($rows)){
                foreach($rows[0] as $key => $val){
                    if(is_int($key)){
                        $good_to_go = false;
                    }
                }
            }

            if(!$good_to_go){            
                header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=int_file' ));
            } elseif(isset($file_type) && (strpos($file_type, "csv") === false && strpos($file_type, "xml") === false && strpos($file_type, "octet-stream") === false)) {
                header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=file' ));
            }
        } else {
                header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=url' ));
        }
    }

}
add_action( 'init', 'file_type_check_auto' );

function automotive_file_import() { 
    $is_xml = $load_csv = false;

    if(isset($_POST['import_submit_auto'])){
        
        $uploaded_file  = $_FILES['import_upload'];
        $file_location  = $uploaded_file['name'];
        $file_type      = strtolower(pathinfo($file_location, PATHINFO_EXTENSION));

        if(isset($file_type) && $file_type == "csv"){
            $rows = parse_csv_local($uploaded_file['tmp_name']);

            $load_csv = true;
        } elseif(isset($file_type) && $file_type == "xml") {
            $rows = parse_xml_local($uploaded_file['tmp_name']);

            $is_xml = true;
            //die;
        }


    } elseif(isset($_POST['url_automotive'])){
        $url = sanitize_text_field( $_POST['url_automotive'] );

        if(filter_var($url, FILTER_VALIDATE_URL)){
            $file_content = wp_remote_get( $url );
            $file_type    = strtolower($file_content['headers']['content-type']);

            $file_content = $file_content['body'];
            // $file_type    = substr($file_type, -3);

            // generate temp file to process
            $temp_file  = tmpfile();
            fwrite($temp_file, $file_content);
            fseek($temp_file, 0);

            // grab file location
            $meta           = stream_get_meta_data($temp_file);
            $file_location  = $meta['uri'];

            if(isset($file_type) && (strpos($file_type, "csv") !== false || strpos($file_type, "octet-stream") !== false)){
                $rows = parse_csv_local($file_location);

                $load_csv = true;
            } elseif(isset($file_type) && strpos($file_type, "xml") !== false) {
                $rows = parse_xml_local($file_location);

                $is_xml = true;
            } else {
                header("Location: " . admin_url( 'edit.php?post_type=listings&page=file-import&error=file' ));
            }
        } else {
            echo __("Please submit a valid URL.", "listings");
        }
    }

    // xml parse
    if(isset($_SESSION['auto_xml']['file_contents']) && isset($_GET['xml'])){
        $xml = $_SESSION['auto_xml']['file_contents'];

        $load_xml    = true;
        $xml_start   = key_get_parents($_GET['xml'], $xml);
        $xml_start[] = (isset($_GET['xml']) && !empty($_GET['xml']) ? sanitize_text_field($_GET['xml']) : "");
            
        $items       = $xml;

        foreach($xml_start as $ndx){
            $items = $items[$ndx];
        }

        $rows          = $items;
        $return_result = insert_listings($rows);
    }

    // csv parse 
    if(isset($_SESSION['auto_csv']['file_contents']) && isset($_POST['csv']) && $_POST['csv']){
        $rows = $_SESSION['auto_csv']['file_contents'];

        $return_result = insert_listings($rows);
    }

    ?>
    
    <div class="wrap auto_import">
        <h2 style="display: inline-block;"><?php _e("File Import", "listings"); ?></h2> 

        <?php echo ((isset($_POST['file']) && !empty($_POST['file']) && $_POST['file'] == "uploaded" && ( isset($load_csv) && $load_csv || isset($load_xml))) || isset($load_xml) && $load_xml && isset($_GET['xml']) && !empty($_GET['xml']) ? '<button class="submit_csv button button-primary" style="vertical-align: super">' . __("Import Listings", "listings") . '</button>' : ""); ?>

        <br><br>

        <?php 
        if(isset($return_result) && !empty($return_result) && !is_null($return_result)){
            echo $return_result;

            // clear sesh
            if(isset($_SESSION['auto_csv']['file_contents']) && !empty($_SESSION['auto_csv']['file_contents'])){
                unset($_SESSION['auto_csv']['file_contents']);
            }

            if(isset($_SESSION['auto_xml']['file_contents']) && !empty($_SESSION['auto_xml']['file_contents'])){
                unset($_SESSION['auto_xml']['file_contents']);
            }
        } elseif($is_xml){ 
            $xml_options = multiarray_keys($rows); ?>
            
            <div class="upload-plugin">

                <p class="install-help"><?php _e("Choose which XML node contains each listings information.", "listings"); ?></p>

                <form method="get" class="wp-upload-form" action="">
                    <input type="hidden" name="post_type" value="listings">
                    <input type="hidden" name="page" value="file-import">

                    <select name='xml'>
                    <?php
                    foreach($xml_options as $option){
                        echo "<option value='" . $option . "'>" . $option . "</option>";
                    } ?>
                    </select>

                    <button onclick="jQuery(this).closest('form').submit()" class="button"><?php _e("Import Now", "listings"); ?></button>  
                </form>

            </div>

        <?php } elseif(!isset($_POST['file']) && !isset($load_xml)){ ?>
            <div class="upload-plugin">
                <p class="install-help"><?php _e("If you have a listing data in a .csv or .xml file format, you may import it by uploading it here.", "listings"); ?></p>

                <form method="post" enctype="multipart/form-data" class="wp-upload-form" action="<?php echo remove_query_arg( "error" ); ?>" name="import_upload">
                    <input type="hidden" name="post_type" value="listings">
                    <input type="hidden" name="page" value="file-import">
                    <input type="hidden" name="file" value="uploaded">

                    <label class="screen-reader-text" for="import_upload"><?php _e("Listing file", "listings"); ?></label>
                    <input type="file" id="import_upload" name="import_upload">
                    <input type="submit" name="import_submit_auto" id="install-plugin-submit" class="button" value="<?php _e("Import Now", "listings"); ?>" disabled="">
                </form>
            </div>


            <div class="upload-plugin">
                <p class="install-help"><?php _e("If you have a link to a .csv or .xml listing file, you may import it by pasting the URL it here.", "listings"); ?></p>

                <form method="post" class="wp-upload-form" action="<?php echo remove_query_arg( "error" ); ?>" name="import_url">
                    <input type="hidden" name="post_type" value="listings">
                    <input type="hidden" name="page" value="file-import">
                    <input type="hidden" name="file" value="uploaded">

                    <label class="screen-reader-text" for="pluginzip"><?php _e("Listing file", "listings"); ?></label>
                    <input type="text" name="url_automotive" placeholder="<?php _e("URL to xml or csv", "listings"); ?>" style="width: 70%;">
                    <button onclick="jQuery(this).closest('form').submit()" class="button"><?php _e("Import Now", "listings"); ?></button>                
                </form>
            </div>
        <?php } elseif((isset($_POST['file']) && !empty($_POST['file']) && $_POST['file'] == "uploaded") || $load_xml){ ?>

            <?php if(isset($rows) && count($rows) > 100){ ?>
            <div class="error">
                <span class="error_text"><?php _e("Please consider breaking the import file into multiple files, large imports may not import fully depending on your server's settings.", "listings"); ?></span>
            </div>
            <?php } ?>

            <?php 
            $assoc_val           = get_option("file_import_associations");
            $associations        = ($assoc_val ? $assoc_val : array());
            $duplicate_check_val = (isset($associations['duplicate_check']) && !empty($associations['duplicate_check']) ? $associations['duplicate_check'] : "");
            $overwrite_existing_val = (isset($associations['overwrite_existing']) && !empty($associations['overwrite_existing']) ? $associations['overwrite_existing'] : ""); ?>

            <ul id="csv_items" class="connectedSortable">
                <?php

                function recursive_sortable($loop, $rows, $associations){
                    foreach($loop as $key => $row){
                        if( !isset($associations['csv'][$key]) ){
                        if(!is_array($row)){
                            echo "<li class='ui-state-default'><input type='hidden' name='csv[" . (is_null(key_get_parents($key, $rows)) ? $key : implode("|", key_get_parents($key, $rows)) . "|" . $key ) . "]' > " . $key . "</li>";
                        } else {
                                recursive_sortable($row, $rows, $associations);
                            }
                        }
                    }
                }
                
                if((isset($is_xml) && $is_xml == true) || isset($load_xml)){
                    recursive_sortable($rows[0], $rows[0], $associations); 
                } else {
                    if(isset($_SESSION['auto_csv']['titles']) && !empty($_SESSION['auto_csv']['titles'])){
                        $titles = $_SESSION['auto_csv']['titles'];

                        foreach($titles as $value){
                            echo "<li class='ui-state-default'><input type='hidden' name='csv[" . $value .  "]' > " . $value . "</li>";

                        }
                    }
                }
                ?>
            </ul>

            <form method="post" id="csv_import">
             
                <?php foreach(get_listing_categories() as $key => $option){ 
                    $needle         = str_replace(" ", "_", strtolower($option['singular']));
                    $is_association = (isset($associations['csv']) && is_array($associations['csv']) && array_search($needle, $associations['csv']) ? true : false);
                    ?>
                    <fieldset class="category">
                        <legend><?php echo $option['singular']; ?></legend>

                        <ul class="listing_category connectedSortable" data-limit="1" data-name="<?php echo $key; ?>">
                            <?php if($is_association){
                                $safe_val = str_replace(" ", "_", strtolower($option['singular']));
                                $values   = array_keys($associations['csv'], $safe_val);

                                if( (isset($rows[0][$values[0]]) || array_search($values[0], $titles) !== false) ){
                                    echo '<li class="ui-state-default ui-sortable-handle"><input type="hidden" name="csv[' . $values[0] . ']" value="' . $safe_val . '"> ' . $values[0] . '</li>';
                                }
                            } ?>
                        </ul>
                    </fieldset>
                <?php } 

                // extra spots
                $extra_spots = array(__("Title", "listings") => 1, __("Vehicle Overview", "listings") => 0, __("Technical Specifications", "listings") => 0, __("Other Comments", "listings") => 0, __("Gallery Images", "listings") => 0, __("Price", "listings") => 1, __("Original Price", "listings") => 1, __("City MPG", "listings") => 1, __("Highway MPG", "listings") => 1, __("Video", "listings") => 1, __("Features and Options", "listings") => 1);
                foreach($extra_spots as $key => $option){ 
                    $needle         = str_replace(" ", "_", strtolower($key));
                    $is_association = (isset($associations['csv']) && is_array($associations['csv']) && array_search($needle, $associations['csv']) ? true : false); 
                    ?>
                    <fieldset class="category">
                        <legend><?php echo $key . ($option == 0 ? " <i class='fa fa-bars'></i>" : ""); ?></legend>

                        <ul class="listing_category connectedSortable" data-limit="<?php echo $option; ?>" data-name="<?php echo str_replace(" ", "_", strtolower($key)); ?>">
                            <?php if($is_association){
                                $safe_val = str_replace(" ", "_", strtolower($key));
                                $values   = array_keys($associations['csv'], $safe_val);

                                foreach($values as $val_key => $val_val){
                                    if( (isset($rows[0][$val_val])  || array_search($val_val, $titles) !== false) ){
                                        echo '<li class="ui-state-default ui-sortable-handle"><input type="hidden" name="csv[' . $val_val . ']" value="' . $safe_val . '"> ' . $val_val . '</li>';
                                    }
                                }
                            } ?>
                        </ul>
                    </fieldset>
                <?php } ?>

                <br><br>

                <?php _e("Check for duplicate listings using", "listings"); ?>: 
                <select name="duplicate_check">
                    <option value="none"><?php _e("None", "listings"); ?></option>
                    <option value="title" <?php selected( "title", $duplicate_check_val, true ) ?>><?php _e("Title", "listings"); ?></option>
                    <?php
                    foreach(get_listing_categories() as $key => $option){
                        $val = str_replace(" ", "_", strtolower($option['singular']));
                        echo "<option value='" . $val . "' " . selected( $val, $duplicate_check_val, false ) . ">" . $option['singular'] . "</option>";
                    } ?>
                </select>&nbsp;&nbsp;&nbsp;

                <input type="checkbox" name="overwrite_existinging" value="on" <?php echo (!empty($overwrite_existing_val) ? "checked='checked'" : ""); ?>> <?php _e("Overwrite duplicate listings with new data", "listings"); ?>

                <br><br>

                * <i class="fa fa-bars"></i> <?php _e("Categories with this symbol can contain multiple values", "listings"); ?>

                <br><br>

                <button class="save_import_categories button button-primary"><?php _e("Save the above associations", "listings"); ?></button>

            </form>
        <?php } ?>

    </div>

    <script>
    jQuery(document).ready( function($){
        $( "#csv_items, .listing_category" ).sortable({
            connectWith: ".connectedSortable",
            placeholder: "ui-state-highlight",
            forcePlaceholderSize: true,
            start: function(e, ui){
                ui.placeholder.height(ui.item.height());
            },
            receive: function(event, ui) {
                var $this = $(this);
                if ($this.data("limit") == 1 && $this.children('li').length > 1 && $this.attr('id') != "csv_items") {
                    alert('<?php _e("Only one per list!", "listings"); ?>');
                    $(ui.sender).sortable('cancel');
                }

                // set val
                var name = $this.data('name');
                ui.item.find('input[type="hidden"]').val(name);
            },
            stop: function (event, ui){
                var $this = $(this);
            }
        }).disableSelection();

        $(".submit_csv").click( function(){
            $("#csv_import").submit();
        });
    });
    </script>

    <style>
    .wrap.auto_import ul {
        list-style: disc;
        padding-left: 40px;
    }

    #csv_import {
        width: 80%;
        float: right;
    }

    #csv_items, .wrap.auto_import ul.listing_category {
        width: 150px;
        min-height: 20px;
        list-style-type: none;
        margin: 0;
        padding: 5px 0 10px 0;
        float: left;
        margin-right: 10px;
    }

    #csv_items {
        width: 19%;
    }

    #csv_items li, ul.listing_category li {
        margin: 0 5px 5px 5px;
        padding: 5px;
        font-size: 1.2em;
        width: 128px;
        cursor: move;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #csv_items li:hover, .listing_category li:hover {
        overflow: visible;
        text-overflow: clip;
    }

    li.no_value {
        border: 1px solid #F00;
    }

    fieldset.category {
        border: 1px solid #CCC;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .error_text {
        padding: 10px 0;
        display: block;
    }
    </style>
<?php } 

function array_remove_empty($haystack) {
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            $haystack[$key] = array_remove_empty($haystack[$key]);
        }

        if (empty($haystack[$key])) {
            unset($haystack[$key]);
        }
    }

    return $haystack;
}

function get_upload_image($image_url){
    $image = $image_use = $image_url;
    $get   = wp_remote_get( $image );
    $type  = wp_remote_retrieve_header( $get, 'content-type' );

    $allowed_images = array("image/jpg", "image/jpeg", "image/png", "image/gif");

    if (!$type && in_array($type, $allowed_images)){
        return false;
    }

    $extension = pathinfo($image, PATHINFO_EXTENSION);

    if(empty($extension) && !empty($type)){
        $content_type = $type;

        if($content_type == "image/jpg" || $content_type == "image/jpeg"){
            $image_use = $image . ".jpg";
        } elseif($content_type == "image/png"){
            $image_use = $image . ".png";
        } elseif($content_type == "image/gif"){
            $image_use = $image . ".gif";
        }
    }

    $mirror = wp_upload_bits(  basename( $image_use ), '', wp_remote_retrieve_body( $get ) );

    $attachment = array(
        'post_title'=> basename( $image ),
        'post_mime_type' => $type
    );

    if(isset($mirror) && !empty($mirror)){
        $attach_id = wp_insert_attachment( $attachment, $mirror['file'] );

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata( $attach_id, $mirror['file'] );

        wp_update_attachment_metadata( $attach_id, $attach_data );
    } else {
        $attach_id = "";
    }

    return $attach_id;
}

function key_get_parents($subject, $array){

    foreach ($array as $key => $value){

        if (is_array($value)){
            if (in_array($subject, array_keys($value), true)){
                return array($key);
            } else {
                $chain = key_get_parents($subject, $value);

                if (!is_null($chain)){
                    return array_merge(array($key), $chain);
                }
            }
        }
    }

  return null;
}

function parse_xml_local($file_location){
    $xml    = simplexml_load_file($file_location);
    $json   = json_encode($xml);
    $rows   = json_decode($json, TRUE);

    $_SESSION['auto_xml']['file_contents'] = $rows;

    return $rows;
}

function multiarray_keys($ar) { 
            
    foreach($ar as $k => $v) { 
        if(is_array($v)){
            $keys[] = $k; 
        }

        if (is_array($ar[$k]) && is_array(multiarray_keys($ar[$k]))) {
            $keys = array_merge($keys, multiarray_keys($ar[$k])); 
        }
    } 
    return (isset($keys) && !empty($keys) ? $keys : ""); 
} 

function parse_csv_local($file_location){
    if(!class_exists("parseCSV")){
        include(LISTING_HOME . "/classes/" . "parsecsv.lib.php"); 
    }
    // parse CSV
    $csv  = new parseCSV($file_location);
    $csv->delimiter = apply_filters("file_import_delimiter", ",");
    // $csv->auto($file_location);
    $rows = array_values(array_remove_empty($csv->data));
    $titles = $csv->titles;

    $_SESSION['auto_csv']['file_contents'] = $rows;
    $_SESSION['auto_csv']['titles']        = $titles;

    return $rows;
}

function search_array_keys($array, $term, $references){
    $count  = array_count_values($references);
    $return = "";

    // if variable has more than a single value
    if(isset($count[$term]) && $count[$term] > 2){
        $keys = array_keys($references, $term);

        foreach($keys as $key){
            if(strpos($key, "|") !== false){
                $paths = explode("|", $key); 
                $items = $array;

                foreach($paths as $ndx){
                    $items = $items[$ndx];
                }

                $return .= $items . "<br>";        
            } else {
                $return .= (isset($array[$key]) && !empty($array[$key]) ? $array[$key] : "") . "<br>";
            }
        }
    } else {
        $key = array_search($term, $references);

        if(strpos($key, "|") !== false){
            $paths = explode("|", $key); 
            $items = $array;

            foreach($paths as $ndx){
                $items = $items[$ndx];
            }

            $return .= $items;        
        } else {
            $return .= (isset($array[array_search($term, $references)]) && !empty($array[array_search($term, $references)]) ? $array[array_search($term, $references)] : "");
        }
    }

    return $return;
}

function insert_listings($rows){

     // if form submitted
    if(isset($_POST['csv']) && !empty($_POST['csv'])){

        $csv                     = (isset($_POST['csv']) && !empty($_POST['csv']) ? $_POST['csv'] : "");
        $duplicate_check         = (isset($_POST['duplicate_check']) && !empty($_POST['duplicate_check']) ? $_POST['duplicate_check'] : "");
        $listing_categories_safe = $listing_categories = get_listing_categories(true);

        $imported_listings       = array();

        if(!empty($csv)){
            // duplicate check outside listings
            $current_listings = get_posts( array( "post_type" => "listings", "posts_per_page" => -1 ) );

            $current_check = array();
            $current_ids   = array();
            $i = 0;

            foreach($current_listings as $listing){
                $post_meta       = get_metadata("post", $listing->ID);

                if(isset($post_meta[$duplicate_check]) && is_array($post_meta[$duplicate_check]) && !empty($post_meta[$duplicate_check])){
                    $current_check[$listing->ID] = (isset($post_meta[$duplicate_check][0]) && !empty($post_meta[$duplicate_check][0]) ? $post_meta[$duplicate_check][0] : "");
                    // $current_ids[$i]   = $listing->ID;
                    $i++;
                } elseif(isset($post_meta[$duplicate_check]) && !is_array($post_meta[$duplicate_check]) && !empty($post_meta[$duplicate_check])) {
                    $current_check[$listing->ID] = (isset($post_meta[$duplicate_check]) && !empty($post_meta[$duplicate_check]) ? $post_meta[$duplicate_check] : "");
                    // $current_ids[$i]   = $listing->ID;
                }
            }

            //D($rows);
            foreach($rows as $key => $row){
                $post_title     = search_array_keys($row, "title", $csv);
                $post_content   = search_array_keys($row, "vehicle_overview", $csv);

                $insert_info    = array(
                                    'post_type'     => "listings",
                                    'post_title'    => $post_title,
                                    'post_content'  => $post_content,
                                    'post_status'   => "publish"
                                );

                if($duplicate_check == "none"){
                    $no_check = true;
                } elseif($duplicate_check != "title"){
                    $search_value  = search_array_keys($row, $duplicate_check, $csv);
                } else {
                    $current_check = wp_list_pluck( $current_listings, "post_title" );
                    $search_value  = $post_title;
                }

                if( (isset($current_check) && isset($search_value) && !in_array($search_value, $current_check) ) || (isset($no_check) && $no_check)){
                    $insert_id      = wp_insert_post( $insert_info );

                    /* Record inserted posts */
                    $imported_listings[$insert_id] = $post_title;

                    // listing categories
                    $listing_categories['Technical Specifications'] = array("multiple" => true);
                    $listing_categories['Other Comments']           = array("multiple" => true);

                    foreach($listing_categories as $key => $option){
                        if(isset($option['multiple'])){
                            // contains multiple values, concatanate them
                            $key   = str_replace(" ", "_", strtolower($key));
                            $value = search_array_keys($row, $key, $csv);
                        } else {
                            $value = search_array_keys($row, $key, $csv);

                            // numbers
                            if(isset($option['compare_value']) && $option['compare_value'] != "="){
                                $value = preg_replace('/\D/', '', $value);
                            }

                            //link_value
                            if(isset($option['link_value']) && !empty($option['link_value'])){
                                if($option['link_value'] == "price"){
                                    $linked_price_value = $value;
                                }
                            }

                            // add value if not already added
                            $terms = (isset($listing_categories_safe[$key]['terms']) && !empty($listing_categories_safe[$key]['terms']) ? $listing_categories_safe[$key]['terms'] : array());
                            //compare_value
                            if(is_array($terms) && !in_array($value, $terms) && !empty($value) && isset($option['compare_value']) && $option['compare_value'] == "="){
                                $listing_categories_safe[$key]['terms'][] = $value;                                
                            }
                        }
                        
                        update_post_meta( $insert_id, $key, $value );
                    }

                    // gallery images
                    $values         = search_array_keys($row, "gallery_images", $csv);
                    $gallery_images = array();

                    if(!empty($values)){
                        if(strstr($values, ",")){
                            $dynamite = ",";
                        } elseif(strstr($values, "<br>")){
                            $dynamite = "<br>";
                        } elseif(strstr($values, "|")){
                            $dynamite = "|";
                        } elseif(strstr($values, ";")){
                            $dynamite = ";";
                        }

                        if(isset($dynamite) && !empty($dynamite)){
                            $values   = explode($dynamite, $values);

                            foreach($values as $val){
                                $val = auto_add_http(trim($val));
                                if(filter_var($val, FILTER_VALIDATE_URL)){
                                    $val = preg_replace('/\?.*/', '', $val);
                                    $gallery_images[] = get_upload_image($val);
                                }
                            }
                        } else {
                            $values = auto_add_http(trim($values));
                            if(filter_var($values, FILTER_VALIDATE_URL)){
                                $values = preg_replace('/\?.*/', '', $values);
                                $gallery_images[] = get_upload_image($values);
                            }
                        }
                    }

                    if(!empty($gallery_images)){
                        update_post_meta($insert_id, "gallery_images", $gallery_images);
                    }

                    // Features & Options    
                    $values = search_array_keys($row, "features_and_options", $csv);
                    $features_and_options = array();
                    $dynamite = "";

                    if(!empty($values)){
                        if(strstr($values, ",")){
                            $dynamite = ",";
                        } elseif(strstr($values, "<br>")){
                            $dynamite = "<br>";
                        } elseif(strstr($values, "|")){
                            $dynamite = "|";
                        } elseif(strstr($values, ";")){
                            $dynamite = ";";
                        }

                        if(isset($dynamite) && !empty($dynamite)){
                            $values   = explode($dynamite, $values);

                            foreach($values as $val){
                                $features_and_options[] = $val;
                            }
                        } else {
                            $features_and_options[] = $values;                        
                        }
                    }

                    if(!empty($features_and_options)){
                        update_post_meta($insert_id, "multi_options", $features_and_options);

                        $options = $listing_categories_safe['options']['terms'];

                        foreach($features_and_options as $option){
                            $option = trim($option);
                            $option = preg_replace('/\x{EF}\x{BF}\x{BD}/u', '', @iconv(mb_detect_encoding($option), 'UTF-8', $option));

                            if(!in_array($option, $options)){
                                $listing_categories_safe['options']['terms'][] = $option;
                            }
                        }
                    }

                    global $lwp_options;

                    // additional detail
                    if(!empty($lwp_options['additional_categories']['value'])){
                        foreach($lwp_options['additional_categories']['value'] as $key => $additional_category){
                            if(isset($lwp_options['additional_categories']['check'][$key]) && $lwp_options['additional_categories']['check'][$key] == "on"){
                                $safe_category = str_replace(" ", "_", strtolower($additional_category));

                                update_post_meta($insert_id, $safe_category, 1);
                            }
                        }
                    }

                    // post options (city, hwy, video)
                    $post_options = array(
                        "video" => search_array_keys($row, "video", $csv),
                        "price" => array(
                            "text"  => (isset($lwp_options['default_value_price']) && !empty($lwp_options['default_value_price']) ? $lwp_options['default_value_price'] : __("Price", "listings")),
                            "value" => (isset($linked_price_value) ? $linked_price_value : preg_replace('/\D/', '', search_array_keys($row, "price", $csv)))
                        ),
                        "city_mpg" => array(
                            "text"  => (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : __("City MPG", "listings")),
                            "value" => preg_replace('/\D/', '', search_array_keys($row, "city_mpg", $csv))
                        ),
                        "highway_mpg" => array(
                            "text"  => (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : __("Highway MPG", "listings")),
                            "value" => preg_replace('/\D/', '', search_array_keys($row, "highway_mpg", $csv))
                        )
                    );
                    
                    update_post_meta($insert_id, "listing_options", serialize($post_options));

                    // default history image
                    if(isset($lwp_options['default_vehicle_history']['on']) && $lwp_options['default_vehicle_history']['on'] == "1"){
                        update_post_meta( $insert_id, "verified", "yes" );
                    }
                } else {
                    global $wpdb;

                    // $results = $wpdb->get_results( "SELECT post_id, meta_key FROM $wpdb->postmeta WHERE meta_value = '" . $search_value . "'", ARRAY_A );
                    // $duplicate_id = $results[0]['post_id'];

                    $duplicate_ids = array_keys($current_check, $search_value);   

                    if(!empty($duplicate_ids)){

                        foreach($duplicate_ids as $duplicate_id){

                            $update_post = "";
                            $update_post = get_post($duplicate_id, ARRAY_A);

                            if(!empty($update_post)){
                                $post_title     = search_array_keys($row, "title", $csv);
                                $post_content   = search_array_keys($row, "vehicle_overview", $csv);


                                $imported_listings['duplicate'][$duplicate_id] = $post_title;

                                // update post title and content
                                $update_post['post_title']   = $post_title;
                                $update_post['post_content'] = $post_content;

                                $insert_id = $update_post['ID'];

                                wp_update_post( $update_post );

                                // update old information
                                $listing_categories['Technical Specifications'] = array("multiple" => true);
                                $listing_categories['Other Comments']           = array("multiple" => true);

                                foreach($listing_categories as $key => $option){
                                    $key   = str_replace(" ", "_", strtolower($key));

                                    if(isset($option['multiple'])){
                                        // contains multiple values, concatanate them
                                        $value = search_array_keys($row, $key, $csv);
                                    } else {
                                        $value = search_array_keys($row, $key, $csv);

                                        // numbers
                                        if(isset($option['compare_value']) && $option['compare_value'] != "="){
                                            $value = preg_replace('/\D/', '', $value);
                                        }

                                        //link_value
                                        if(isset($option['link_value']) && !empty($option['link_value'])){
                                            if($option['link_value'] == "price"){
                                                $linked_price_value = $value;
                                            }
                                        }

                                        // add value if not already added
                                        $terms = (isset($listing_categories_safe[$key]['terms']) && !empty($listing_categories_safe[$key]['terms']) ? $listing_categories_safe[$key]['terms'] : array());
                                        //compare_value
                                        if(is_array($terms) && !in_array($value, $terms) && !empty($value) && isset($option['compare_value']) && $option['compare_value'] == "="){
                                            $listing_categories_safe[$key]['terms'][] = $value;                                
                                        }
                                    }
                                    
                                    update_post_meta( $insert_id, $key, $value );
                                }

                                // gallery images
                                $values         = search_array_keys($row, "gallery_images", $csv);
                                $gallery_images = array();

                                if(!empty($values)){
                                    if(strstr($values, ",")){
                                        $dynamite = ",";
                                    } elseif(strstr($values, "<br>")){
                                        $dynamite = "<br>";
                                    } elseif(strstr($values, "|")){
                                        $dynamite = "|";
                                    } elseif(strstr($values, ";")){
                                        $dynamite = ";";
                                    }

                                    if(isset($dynamite) && !empty($dynamite)){
                                        $values   = explode($dynamite, $values);

                                        foreach($values as $val){
                                            $val = auto_add_http(trim($val));
                                            if(filter_var($val, FILTER_VALIDATE_URL)){
                                                $val = preg_replace('/\?.*/', '', $val);
                                                $gallery_images[] = get_upload_image($val);
                                            }
                                        }
                                    } else {
                                        $values = auto_add_http(trim($values));
                                        if(filter_var($values, FILTER_VALIDATE_URL)){
                                            $values = preg_replace('/\?.*/', '', $values);
                                            $gallery_images[] = get_upload_image($values);
                                        }
                                    }
                                }

                                if(!empty($gallery_images)){
                                    update_post_meta($insert_id, "gallery_images", $gallery_images);
                                }

                                // Features & Options    
                                $values = search_array_keys($row, "features_and_options", $csv);
                                $features_and_options = array();
                                $dynamite = "";

                                if(!empty($values)){
                                    if(strstr($values, ",")){
                                        $dynamite = ",";
                                    } elseif(strstr($values, "<br>")){
                                        $dynamite = "<br>";
                                    } elseif(strstr($values, "|")){
                                        $dynamite = "|";
                                    }

                                    if(isset($dynamite) && !empty($dynamite)){
                                        $values   = explode($dynamite, $values);

                                        foreach($values as $val){
                                            $features_and_options[] = $val;
                                        }
                                    } else {
                                        $features_and_options[] = $values;                        
                                    }
                                }
                                
                                if(!empty($features_and_options)){
                                    update_post_meta($insert_id, "multi_options", $features_and_options);

                                    $options = $listing_categories_safe['options']['terms'];

                                    foreach($features_and_options as $option){
                                        if(!in_array($option, $options)){
                                            $listing_categories_safe['options']['terms'][] = $option;
                                        }
                                    }
                                }

                                global $lwp_options;

                                // additional detail
                                if(!empty($lwp_options['additional_categories']['value'])){
                                    foreach($lwp_options['additional_categories']['value'] as $key => $additional_category){
                                        if(isset($lwp_options['additional_categories']['check'][$key]) && $lwp_options['additional_categories']['check'][$key] == "on"){
                                            $safe_category = str_replace(" ", "_", strtolower($additional_category));

                                            update_post_meta($insert_id, $safe_category, 1);
                                        }
                                    }
                                }

                                // post options (city, hwy, video)
                                $post_options = array(
                                    "video" => search_array_keys($row, "video", $csv),
                                    "price" => array(
                                        "text"  => (isset($lwp_options['default_value_price']) && !empty($lwp_options['default_value_price']) ? $lwp_options['default_value_price'] : __("Price", "listings")),
                                        "value" => (isset($linked_price_value) ? $linked_price_value : preg_replace('/\D/', '', search_array_keys($row, "price", $csv)))
                                    ),
                                    "city_mpg" => array(
                                        "text"  => (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : __("City MPG", "listings")),
                                        "value" => preg_replace('/\D/', '', search_array_keys($row, "city_mpg", $csv))
                                    ),
                                    "highway_mpg" => array(
                                        "text"  => (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : __("Highway MPG", "listings")),
                                        "value" => preg_replace('/\D/', '', search_array_keys($row, "highway_mpg", $csv))
                                    )
                                );
                                
                                update_post_meta($insert_id, "listing_options", serialize($post_options));
                            }
                        }


                        // if overwrite is enabled
                        if(isset($_POST['overwrite_existinging']) && $_POST['overwrite_existinging'] == "on"){
                            $duplicates = (isset($imported_listings['duplicate']) && !empty($imported_listings['duplicate']) ? $imported_listings['duplicate'] : "");                   
                        }
                    }
                }

                update_option( 'listing_categories', $listing_categories_safe );
            }

            $return = "";

            $duplicates = (isset($imported_listings['duplicate']) && !empty($imported_listings['duplicate']) ? $imported_listings['duplicate'] : "");
            unset($imported_listings['duplicate']);

            if(!empty($imported_listings)){
                $return .= __("Successfully imported these listings", "listings") . ":<br>";

                $return .= "<ul>";
                foreach($imported_listings as $key => $listing){
                    if($key != "duplicate"){
                        $return .= "<li><a href='" . get_permalink($key) . "'>" . $listing . "</a></li>";
                    }
                }
                $return .= "</ul>";
            }

            if(!empty($duplicates)){
                if(isset($_POST['overwrite_existinging']) && $_POST['overwrite_existinging'] == "on"){
                    $return .= __("These listings were updated with new information from the imported file", "listings") . ":<br>";
                } else {
                    $return .= __("These listings weren't imported because a duplicate listing was detected", "listings") . ":<br>";
                }

                $return .= "<ul>";
                foreach($duplicates as $listing){
                    $return .= "<li>" . $listing . "</li>";
                }
                $return .= "</ul>";            
            }

            $return .= "<a href='" . admin_url("edit.php?post_type=listings&page=file-import") . "'><button class='button button-primary'>" . __("Import more listings", "listings") . "</button></a>";

            return $return;
        }
    } 
}

function auto_add_http($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

function save_import_categories(){

    if(isset($_POST['form']) && !empty($_POST['form'])){
        parse_str($_POST['form'], $form);

        update_option("file_import_associations", $form);

        echo "Saved";
    }

    die;
}
add_action("wp_ajax_save_import_categories", "save_import_categories");
add_action("wp_ajax_nopriv_save_import_categories", "save_import_categories");

function automotive_import_scripts() {
    wp_enqueue_script( 'jquery-ui' );
    wp_enqueue_script( 'jquery-ui-sortable' );
}

add_action( 'wp_enqueue_scripts', 'automotive_import_scripts' ); ?>