<?php
///// start session
if (!session_id()) {
    session_start();
}

function register_vin_menu_item() {
    add_submenu_page( 'edit.php?post_type=listings', __('VIN Import', 'listings'), __('VIN Import', 'listings'), 'manage_options', 'vin-import', 'automotive_vin_import' ); 
}
add_action('admin_menu', 'register_vin_menu_item');

function get_vin_info($vin){
    global $lwp_options;

    $api_key     = (isset($lwp_options['edmunds_api_key']) && !empty($lwp_options['edmunds_api_key']) ? $lwp_options['edmunds_api_key'] : "");
    $api_secret  = (isset($lwp_options['edmunds_api_secret']) && !empty($lwp_options['edmunds_api_secret']) ? $lwp_options['edmunds_api_secret'] : "");

    $response    = wp_remote_get("https://api.edmunds.com/api/vehicle/v2/vins/" . $vin . "?fmt=json&api_key=" . $api_key);
    $body_encode = $response['body'];
    
    return json_decode($body_encode);
}

/* errors
/* * * * */
function admin_errors_vin(){
    $vin = (isset($_GET['vin']) && !empty($_GET['vin']) ? $_GET['vin'] : "");

    if(!empty($vin) && isset($_GET['error'])){
        $body = get_vin_info($vin);

        if(isset($body->errorType) && !empty($body->errorType)){
            echo "<div class='error'><span class='error_text'>";

            echo "Error: " . $body->message . "<br>";

            echo "</span></div>";
        }
    }
}
add_action( 'admin_notices', 'admin_errors_vin' );

function vin_test(){
    if(isset($_GET['vin']) && !empty($_GET['vin'])){    
        $vin  = (isset($_GET['vin']) && !empty($_GET['vin']) ? $_GET['vin'] : "");
        $body = get_vin_info($vin);

        if(isset($body->errorType) && !empty($body->errorType) && !isset($_GET['error'])){
            header("Location: " . add_query_arg("error", ""));
        }
    }
}
add_action( 'init', 'vin_test' );

function automotive_vin_import() { 
    global $lwp_options;

    $vin = (isset($_GET['vin']) && !empty($_GET['vin']) ? $_GET['vin'] : ""); ?>

    <div class="wrap auto_import">
        <h2 style="display: inline-block;"><?php _e("VIN Import", "listings"); ?></h2> 
        <?php if(!empty($vin) && !isset($_POST['import']) && !isset($_GET['error'])){ ?>
            <button class='button button-primary' onclick="jQuery('form[name=vin_import_form]').submit()" style="vertical-align: super;"><?php _e("Import Vehicle", "listings"); ?></button>
        <?php } ?>

        <br><br><br>

        <?php 

        if(isset($_POST['import']) && !empty($_POST['import'])){
            $post_title = "";
            if(isset($_POST['import']['title']) && !empty($_POST['import']['title'])){
                foreach($_POST['import']['title'] as $title){
                    $post_title .= sanitize_text_field($title) . " ";
                }
            }

            $post_content = "";
            if(isset($_POST['import']['vehicle_overview']) && !empty($_POST['import']['vehicle_overview'])){
                foreach($_POST['import']['vehicle_overview'] as $overview){
                    $post_content .= sanitize_text_field($overview) . " ";
                }
            }

            $post_title   = trim($post_title);
            $post_content = trim($post_content);

            // insert post, get id
            $insert_info    = array(
                                'post_type'     => "listings",
                                'post_title'    => $post_title,
                                'post_content'  => $post_content,
                                'post_status'   => "publish"
                            );

            $insert_id      = wp_insert_post( $insert_info );

            // if success :)
            if($insert_id){

                $listing_categories_safe = $listing_categories = get_listing_categories(true);

                // add
                $listing_categories['Technical Specifications'] = array();
                $listing_categories['Other Comments']           = array();

                foreach($listing_categories as $key => $category){
                    $safe_key = str_replace(" ", "_", strtolower($key));
                    $value    = (isset($_POST['import'][$safe_key]) && !empty($_POST['import'][$safe_key]) ? $_POST['import'][$safe_key] : "");

                    if(!empty($value)){
                        update_post_meta($insert_id, $safe_key, $value);
                    }

                    $terms = (isset($listing_categories_safe[$key]['terms']) && !empty($listing_categories_safe[$key]['terms']) ? $listing_categories_safe[$key]['terms'] : array());
                    //compare_value
                    if(!in_array($value, $terms) && !empty($value) && isset($category['compare_value']) && $category['compare_value'] == "="){
                        $listing_categories_safe[$key]['terms'][] = $value;                                
                    }
                }

                // gallery images
                $gallery_values = (isset($_POST['import']['gallery_image']) && !empty($_POST['import']['gallery_image']) ? $_POST['import']['gallery_image'] : "");
                $gallery_images = array();

                if(!empty($gallery_values)){
                    foreach($gallery_values as $val){
                        if(filter_var($val, FILTER_VALIDATE_URL)){
                            $gallery_images[] = get_upload_image($val);
                        }
                    }
                }
                
                if(!empty($gallery_images)){
                    update_post_meta($insert_id, "gallery_images", $gallery_images);
                }

                $video       = (isset($_POST['import']['video']) && !empty($_POST['import']['video']) ? $_POST['import']['video'] : "");
                $price       = (isset($_POST['import']['price']) && !empty($_POST['import']['price']) ? $_POST['import']['price'] : "");
                $city_mpg    = (isset($_POST['import']['city_mpg']) && !empty($_POST['import']['city_mpg']) ? $_POST['import']['city_mpg'] : "");
                $highway_mpg = (isset($_POST['import']['highway_mpg']) && !empty($_POST['import']['highway_mpg']) ? $_POST['import']['highway_mpg'] : "");

                // other categories
                $post_options = array(
                    "video" => $video,
                    "price" => array(
                        "text"  => (isset($lwp_options['default_value_price']) && !empty($lwp_options['default_value_price']) ? $lwp_options['default_value_price'] : __("Price", "listings")),
                        "value" => $price
                    ),
                    "city_mpg" => array(
                        "text"  => (isset($lwp_options['default_value_city']) && !empty($lwp_options['default_value_city']) ? $lwp_options['default_value_city'] : __("City MPG", "listings")),
                        "value" => $city_mpg
                    ),
                    "highway_mpg" => array(
                        "text"  => (isset($lwp_options['default_value_hwy']) && !empty($lwp_options['default_value_hwy']) ? $lwp_options['default_value_hwy'] : __("Highway MPG", "listings")),
                        "value" => $highway_mpg
                    )
                );
                
                update_post_meta($insert_id, "listing_options", serialize($post_options));
                update_option( 'listing_categories', $listing_categories_safe );

                _e("Congratulations, you successfully imported this listing: ", "listings");
                echo "<a href='" . get_permalink($insert_id) . "'>" . (!empty($post_title) ? $post_title : __("Untitled", "listings")) . "</a>";
            } else {
                _e("Error importing your listing", "listings");
            }
        } else { ?>

            <?php if(!empty($vin) && !isset($_GET['error'])){

                $body = get_vin_info($vin);

                    function recursive_get_all_values($array, $second=''){            
                        foreach($array as $key => $item){
                            if(!is_array($item) && !is_object($item)){
                                $words = preg_split('/(?=[A-Z])/', $key);
                                echo "<li class='ui-state-default'><i class='fa fa-times remove_element'></i><span class='title'>" . ucwords(implode(" ", $words)) . "</span> <span class='inside_value'>" . $item . "</span>  <input type='hidden' name='' value='" . $item . "' /></li>";
                            } else {
                                recursive_get_all_values($item, $key);
                            }
                        }
                    }

                    echo '<ul id="items" class="form_value ui-sortable">';
                    recursive_get_all_values($body);
                    echo '</ul>'; ?>

                    <form method="post" action="" name="vin_import_form">
                        <?php
                        $categories = get_listing_categories();

                        foreach($categories as $key => $value){
                            $safe_name = str_replace(" ", "_", strtolower($value['singular']));

                            echo "<fieldset class='category'>";
                            echo "<legend>" . wpml_category_translate($value['singular'], "singular", $value['singular']) . "</legend>";

                            echo "<ul class='listing_category form_value' data-name='" . $safe_name . "' data-limit='1'></ul>";

                            echo "</fieldset>";
                        }

                        // extra spots
                        $extra_spots = array(__("Title", "listings") => 0, __("Vehicle Overview", "listings") => 0, __("Technical Specifications", "listings") => 0, __("Other Comments", "listings") => 0, __("Gallery Images", "listings") => 0, __("Price", "listings") => 1, __("Original Price", "listings") => 1, __("City MPG", "listings") => 1, __("Highway MPG", "listings") => 1, __("Video", "listings") => 1);
                        foreach($extra_spots as $key => $option){ ?>
                            <fieldset class="category">
                                <legend><?php echo $key . ($option == 0 ? " <i class='fa fa-bars'></i>" : ""); ?></legend>

                                <ul class="listing_category form_value" data-limit="<?php echo $option; ?>" data-name="<?php echo str_replace(" ", "_", strtolower($key)); ?>"></ul>
                            </fieldset>
                        <?php } ?>

                        <br><br>

                        * <i class="fa fa-bars"></i> <?php _e("Categories with this symbol can contain multiple values", "listings"); ?>
                    </form>

                <?php } else { ?>

                    <?php                    
                    $api_key     = (isset($lwp_options['edmunds_api_key']) && !empty($lwp_options['edmunds_api_key']) ? $lwp_options['edmunds_api_key'] : "");
                    $api_secret  = (isset($lwp_options['edmunds_api_secret']) && !empty($lwp_options['edmunds_api_secret']) ? $lwp_options['edmunds_api_secret'] : "");

                    if(!empty($api_key) && !empty($api_secret)){ ?>

                        <div class="upload-plugin">
                            <form method="GET" class="wp-upload-form" action="" name="import_url">
                                <input type="hidden" name="post_type" value="listings">
                                <input type="hidden" name="page" value="vin-import">

                                <label class="screen-reader-text" for="pluginzip"><?php _e("Listing file", "listings"); ?></label>
                                <input type="text" name="vin" placeholder="<?php _e("VIN #", "listings"); ?>" style="width: 60%;">
                                <button onclick="jQuery(this).closest('form').submit()" class="button"><?php _e("Get vehicle details", "listings"); ?></button>                
                            </form>
                        </div>

                    <?php } else { ?>

                        <a href="<?php echo admin_url("admin.php?page=listing_wp&tab=8"); ?>"><?php _e("Please set both your edmunds API keys in the API Keys panel.", "listings"); ?></a>

                    <?php } ?>


                <?php } ?>

            <?php } ?>

    </div>

    <script>
    jQuery(document).ready( function($){
        var list_html;

        $( "#items, .listing_category" ).sortable({
            connectWith: ".form_value",
            placeholder: "ui-state-highlight",
            forcePlaceholderSize: false,
            create: function(e, ui){
                list_html = $("#items").html();
            },
            start: function(e, ui){
                ui.placeholder.height(ui.item.height());
            },
            receive: function(event, ui) {
                var $this = $(this);

                if ($this.data("limit") == 1 && $this.children('li').length > 1 && $this.attr('id') != "items") {
                    alert('<?php _e("Only one per list!", "listings"); ?>');
                    $(ui.sender).sortable('cancel');
                }

                // set val
                var name      = $this.data('name');

                var name_attr = ($this.data("limit") == 1 ? "import[" + name + "]" : "import[" + name + "][]");

                ui.item.find('input[type="hidden"]').attr("name", name_attr);
            },
            stop: function (event, ui){
                var $this = $(this);
                $("#items").html(list_html);
            }
        }).disableSelection();

        $(document).on("click", ".remove_element", function(){
            $(this).closest("li").remove();

            list_html = $("#items").html();
        });

        $("form[name='vin_import_form']").width(($(".auto_import").width() - $("#items").width()) + "px").show();

        $(window).resize( function(){
            $("form[name='vin_import_form']").width(($(".auto_import").width() - $("#items").width()) + "px").show();
        });

    });
    </script>

    <style>
    fieldset.category {
        border: 1px solid #CCC;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    #items, ul.listing_category {
        width: 150px;
        min-height: 20px;
        list-style-type: none;
        margin: 0;
        padding: 5px 0 10px 0;
        float: left;
        margin-right: 10px;
    }

    #items {
        max-width: 30%;
        min-width: 300px;
    }

    .ui-state-highlight { margin-bottom: 0; }

    .ui-state-default .title {
        display: block;
        text-align: center;
        border-bottom: 1px solid #2D2D2D;
        margin: 0 4px;
        color: #2D2D2D;
        margin-bottom: 6px;
        font-size: 16px;
    }

    .ui-state-default .inside_value {
        padding: 4px;
    }

    .ui-state-default .remove_element {
        position: absolute;
        right: 2px;
        top: 2px;
        cursor: pointer;
    }

    .ui-state-default .remove_element:hover {
        color: #000;
    }

    #items li, ul.listing_category li {
        margin: 0 5px 5px 5px;
        padding: 5px;
        width: 128px;
        cursor: move;
        overflow: hidden;
        text-overflow: ellipsis;
        position: relative;
    }

    #items li {
        display: inline-block;
    }

    .error_text {
        padding: 10px 0;
        display: block;
    }

    form[name='vin_import_form']{
        position: fixed;
        display: none;
        right: 0;
    }
    </style>
<?php } ?>