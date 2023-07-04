<?php
$allowed_widget_tags = "<br><p><b><u><i><div><span><img>";

//********************************************
//	Loan Calculator 
//***********************************************************
if(!class_exists("Loan_Calculator")){
    class Loan_Calculator extends WP_Widget {

    	public function Loan_Calculator() {
    		$widget_ops = array( 'classname' => 'loan_calculator', 'description' => __('A widget that displays a calculator able to calculate loan payments', 'listings') );  
            $control_ops = array( 'id_base' => 'loan-calculator-widget' );  
            $this->WP_Widget( 'loan-calculator-widget', __('[LISTINGS] Loan Calculator', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
            $title        = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Financing Calculator', 'listings' ));
            $price        = (isset($instance[ 'price' ]) && !empty($instance[ 'price' ]) ? $instance[ 'price' ] : 10000 );
            $rate         = (isset($instance[ 'rate' ]) && !empty($instance[ 'rate' ]) ? $instance[ 'rate' ] : 7 );
            $down_payment = (isset($instance[ 'down_payment' ]) && (!empty($instance[ 'down_payment' ]) || $instance['down_payment'] == 0 ) ? $instance[ 'down_payment' ] : 1000 );
            $loan_years   = (isset($instance[ 'loan_years' ]) && (!empty($instance[ 'loan_years' ]) || $instance['loan_years'] == 0) ? $instance[ 'loan_years' ] : 5 );
            $text_below   = (isset($instance[ 'text_below' ]) && (!empty($instance[ 'text_below' ]) || $instance['text_below'] == 0) ? $instance[ 'text_below' ] : '' );

            $title        = apply_filters("widget_title", $title);

            //WMPL
            /**
             * retreive translations
             */
            if (function_exists ( 'icl_translate' )){
                $text_below = icl_translate('Widgets', 'Automotive Widget Loan Calculator Text Below Field', $text_below);
            }
    		
    		global $lwp_options;
    		
    		$currency_symbol = (isset($lwp_options['currency_symbol']) && !empty($lwp_options['currency_symbol']) ? $lwp_options['currency_symbol'] : "");
    		
    		echo $before_widget;
    		echo "<div class=\"financing_calculator\">";
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title; ?>
                <div class="table-responsive">
                    <table class="table no-border no-margin">
                        <tbody>
                            <tr>
                                <td><?php _e("Cost of Vehicle", "listings"); ?> (<?php echo $currency_symbol; ?>):</td>
                                <td><input type="text" class="number cost" value="<?php echo $price; ?>"></td>
                            </tr>
                            <tr>
                                <td><?php _e("Down Payment", "listings"); ?> (<?php echo $currency_symbol; ?>):</td>
                                <td><input type="text" class="number down_payment" value="<?php echo $down_payment; ?>"></td>
                            </tr>
                            <tr>
                                <td><?php _e("Annual Interest Rate", "listings"); ?> (%):</td>
                                <td><input type="text" class="number interest" value="<?php echo $rate; ?>"></td>
                            </tr>
                            <tr>
                                <td><?php _e("Term of Loan in Years", "listings"); ?>:</td>
                                <td><input type="text" class="number loan_years" value="<?php echo $loan_years; ?>"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bi_weekly clearfix">
                    <div class="pull-left"><?php _e("Frequency of Payments", "listings"); ?>:</div>
                    <?php $default_frequency = (isset($lwp_options['default_frequency']) && !empty($lwp_options['default_frequency']) ? $lwp_options['default_frequency'] : ""); ?>
                    <div class="styled pull-right">
                        <select class="frequency css-dropdowns">
                            <option value='0'<?php selected( 1, $default_frequency); ?>><?php _e("Bi-Weekly", "listings"); ?></option>
                            <option value='1'<?php selected( 2, $default_frequency); ?>><?php _e("Weekly", "listings"); ?></option>
                            <option value='2'<?php selected( 3, $default_frequency); ?>><?php _e("Monthly", "listings"); ?></option>
                        </select>
                    </div>
                </div>
                <a class="btn-inventory pull-right calculate"><?php _e("Calculate My Payment", "listings"); ?></a>
                <div class="clear"></div>
                <div class="calculation">
                    <div class="table-responsive">
                        <table>
                            <tbody><tr>
                                <td><strong><?php _e("NUMBER OF PAYMENTS", "listings"); ?>:</strong></td>
                                <td><strong class="payments">60</strong></td>
                            </tr>
                            <tr>
                                <td><strong><?php _e("PAYMENT AMOUNT", "listings"); ?>:</strong></td>
                                <td><strong class="payment_amount"><?php echo $currency_symbol; ?> 89.11</strong></td>
                            </tr>
                        </tbody></table>
                    </div>
                </div>
                
                <?php if(isset($text_below) && !empty($text_below)){
                    echo "<p>" . $text_below . "</p>";
                } ?>
    		</div>
    		<?php
    		echo $after_widget;
    	}

     	public function form( $instance ) { 
            $title        = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Financing Calculator', 'listings' ));
            $price        = (isset($instance[ 'price' ]) && !empty($instance[ 'price' ]) ? $instance[ 'price' ] : 10000);
            $down_payment = (isset($instance[ 'down_payment' ]) && !empty($instance[ 'down_payment' ]) ? $instance[ 'down_payment' ] : 1000);
            $rate         = (isset($instance[ 'rate' ]) && !empty($instance[ 'rate' ]) ? $instance[ 'rate' ] : 7);
            $loan_years   = (isset($instance[ 'loan_years' ]) && !empty($instance[ 'loan_years' ]) ? $instance[ 'loan_years' ] : 5);
            $text_below   = (isset($instance[ 'text_below' ]) && !empty($instance[ 'text_below' ]) ? $instance[ 'text_below' ] : ''); ?>
            <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_name( 'price' ); ?>"><?php _e( 'Price:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'price' ); ?>" name="<?php echo $this->get_field_name( 'price' ); ?>" type="text" value="<?php echo esc_attr( $price ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_name( 'down_payment' ); ?>"><?php _e( 'Down Payment:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'down_payment' ); ?>" name="<?php echo $this->get_field_name( 'down_payment' ); ?>" type="text" value="<?php echo esc_attr( $down_payment ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_name( 'rate' ); ?>"><?php _e( 'Rate:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'rate' ); ?>" name="<?php echo $this->get_field_name( 'rate' ); ?>" type="text" value="<?php echo esc_attr( $rate ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_name( 'loan_years' ); ?>"><?php _e( 'Loan Years:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'loan_years' ); ?>" name="<?php echo $this->get_field_name( 'loan_years' ); ?>" type="text" value="<?php echo esc_attr( $loan_years ); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_name( 'text_below' ); ?>"><?php _e( 'Text Below Calculator:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'text_below' ); ?>" name="<?php echo $this->get_field_name( 'text_below' ); ?>" type="text" value="<?php echo esc_attr( $text_below ); ?>" />
            </p>
        <?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
            $instance["title"]        = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
            $instance["price"]        = ( !empty( $new_instance["price"] ) ) ? strip_tags( $new_instance["price"], $allowed ) : '';
            $instance["down_payment"] = ( !empty( $new_instance["down_payment"] ) ) ? strip_tags( $new_instance["down_payment"], $allowed ) : '';
            $instance["rate"]         = ( !empty( $new_instance["rate"] ) ) ? strip_tags( $new_instance["rate"], $allowed ) : '';
            $instance["loan_years"]   = ( !empty( $new_instance["loan_years"] ) ) ? strip_tags( $new_instance["loan_years"], $allowed ) : '';
            $instance["text_below"]   = ( !empty( $new_instance["text_below"] ) ) ? strip_tags( $new_instance["text_below"], $allowed ) : '';

            //WMPL
            /**
             * register strings for translation
             */
            if(function_exists('icl_register_string')){
                icl_register_string('Widgets', 'Automotive Widget Loan Calculator Text Below Field', $instance['text_below']);
            }
    		
    		return $instance;
    	}

    }
}

//********************************************
//	Listing Filter
//***********************************************************
if(!class_exists("Filter_Listings")){
    class Filter_Listings extends WP_Widget {

    	public function Filter_Listings() {
    		$widget_ops = array( 'classname' => 'filter_listings', 'description' => __('A widget that can filter/search the listings being currently displayed', 'listings') );  
            $control_ops = array( 'id_base' => 'filter-listings-widget' );  
            $this->WP_Widget( 'filter-listings-widget', __('[LISTINGS] Filter Listings', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );

    		$title = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Search Our Listings', 'listings' ));
            $title = apply_filters("widget_title", $title);
    		
    		unset($instance['title']);
    		
    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title;
    		
    		echo "<div class='dropdowns select-form'>";
    		foreach($instance as $post_meta => $value){
    			if($value == 1){
    				$key     = get_single_listing_category($post_meta);

    				$meta    = str_replace("_", "-", strtolower((isset($key['singular']) && !empty($key['singular']) ? $key['singular'] : "")));
    				$text    = str_replace("_", " ", (isset($key['singular']) && !empty($key['singular']) ? $key['singular'] : "")); 
                    $text    = wpml_category_translate($key['singular'], "singular", $key['singular']);

                    $new_key = (isset($key['singular']) && strtolower($key['singular']) == "year" ? "yr" : $key['singular']);

                    $prefix_text = __("Bir Seçim Yapın: ", "listings");
                    ?>

    	            <div class="my-dropdown max-dropdown">
    	                <select name="<?php echo str_replace(" ", "_", strtolower($new_key)); ?>" class="listing_filter sidebar_widget_filter" data-sort="<?php echo str_replace(" ", "_", mb_strtolower($key['singular'])); ?>" data-label-plural="<?php echo $key['plural']; ?>" data-label-singular="<?php echo $key['singular']; ?>" tabindex="1" <?php echo (isset($key['compare_value']) && $key['compare_value'] != "=" ? " data-compare-value='" . $key['compare_value'] . "' " : ""); ?> <?php echo (isset($key['dependancy']) && !empty($key['dependancy']) ? " data-dependancy='" . $key['dependancy'] . "'" : ""); ?> data-prefix="<?php echo $prefix_text; ?>">
    	                    <?php if(isset($key['dependancy']) && !empty($key['dependancy']) && mb_strtolower($key['dependancy']) != "none"){ 

                                $dependancy_category = get_single_listing_category($key['dependancy']);
                                $dependancy_singular = $dependancy_category['singular'];
                                $dependancy_safe     = str_replace(" ", "_", strtolower($dependancy_singular));

                                if(isset($_GET[$dependancy_safe]) && !empty($_GET[$dependancy_safe]) && in_array($correct_case, $dependancy_category['terms'])){
                                    $correct_case        = get_category_correct_case($dependancy_singular, $_GET[$dependancy_safe]);

                                    echo "<option>" . $prefix_text . " " . $key['plural'] . "</option>";

                                    $term_id = array_search($correct_case, $dependancy_category['terms']);

                                    // D($key['dependancies'][$term_id]);
                                    if(isset($key['dependancies'][$term_id]) && !empty($key['dependancies'][$term_id])){
                                        // foreach option
                                        foreach($key['dependancies'][$term_id] as $new_key){
                                            if(!is_null($key['terms'][$new_key])){
                                                echo "<option value='" . $key['terms'][$new_key] . "'>" . $key['terms'][$new_key] . "</option>";
                                            }
                                        }
                                    }
                                } else {
                                    echo "<option>" . __("Choose a", "listings") . " " . $dependancy_singular . "</option>";
                                }
                            } else { 
                                echo '<option value="">' . $prefix_text . ' ' . $text . '</option>';
        	                    if(!empty($key['terms'])){

                                    if(isset($key['sort_terms']) && $key['sort_terms'] == "desc"){
                                        arsort($key['terms']);
                                    } else {
                                        asort($key['terms']);
                                    }

        	                        foreach($key['terms'] as $term_key => $term){
                                        $on_select = wpml_term_translate($key['singular'], $term, $term_key);
                                        $term      = wpml_term_translate($key['singular'], $term, $term_key);

        	                            if(isset($key['currency']) && $key['currency'] == 1){
        	                            	$on_select = format_currency($on_select);
        	                            } elseif($key['compare_value'] != "="){
        	                            	$on_select = $key['compare_value'] . " " . $on_select;
        	                            }

                                        $get_value = (isset($_GET[str_replace(" ", "-", strtolower($new_key))]) && !empty($_GET[str_replace(" ", "-", strtolower($new_key))]) ? $_GET[str_replace(" ", "-", strtolower($new_key))] : "");

        	                        	echo "<option value='" . $term . "'" . selected( $get_value, str_replace(" ", "-", strtolower($term)), false ) . " data-key='" . $term_key . "'>" . $on_select . "</option>";
        	                        }
        	                    } else {
        		                        echo "<option>" . __("No options", "listings") . "</option>";
        	                    }
                            }
    	                    ?>
    	                </select>
    	            </div>
    	            <?php
    	        }
    		}
    		
    		echo "</div>";
    		echo "<button class='btn button reset_widget_filter md-button margin-top-10 margin-bottom-none btn-inventory'>" . __("Arama Kriterlerini Temizle", "listings") . "</button>";
    		echo "<div class='clearfix'></div>";
    		
    		echo $after_widget;
    	}

     	public function form( $instance ) {
     		$filterable = get_filterable_listing_categories();
    		
    		$title = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Search Our Inventory', 'listings' ));?>
            <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
            <table>
            <?php
    		foreach($filterable as $filter){ 
    			$spost_meta = str_replace(" ", "_", strtolower($filter['singular']));
    			$value      = (isset($instance[$spost_meta]) && $instance[$spost_meta] == 1 ? "checked='checked' " : null); ?>
    			<tr><td><label for="<?php echo $this->get_field_name( $spost_meta ); ?>"><?php echo $filter['singular']; ?></label> </td>
                <td><input id="<?php echo $this->get_field_id( $spost_meta ); ?>" name="<?php echo $this->get_field_name( $spost_meta ); ?>" type="checkbox" value="1" <?php echo $value; ?>/></td></tr>
                
    		<?php 
    		}
    		
    		echo "</table></p>";
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

     		$filterable = get_filterable_listing_categories();
    		
    		$instance   = array();
    		$allowed    = $allowed_widget_tags;
    		
    		foreach($filterable as $filter){
    			$spost_meta = str_replace(" ", "_", strtolower($filter['singular']));
    			$instance[$spost_meta] = ( !empty( $new_instance[$spost_meta] ) ) ? strip_tags( $new_instance[$spost_meta], $allowed ) : '';
    		}
    		
    		$instance["title"] = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}

//********************************************
//	Single Filter
//***********************************************************
if(!class_exists("Single_Filter")){
    class Single_Filter extends WP_Widget {

    	public function Single_Filter() {
    		$widget_ops = array( 'classname' => 'single_filter', 'description' => __('A widget that can filter/search listings and shows a custom amount of options', 'listings') );  
            $control_ops = array( 'id_base' => 'single-filter-widget' );  
            $this->WP_Widget( 'single-filter-widget', __('[LISTINGS] Single Filter', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
            global $post;

    		extract( $args );
    		
    		$title  = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Search Our Listings', 'listings' ));
    		$number = (isset($instance[ 'number' ]) && !empty($instance[ 'number' ]) ? $instance[ 'number' ] : __( 10, 'listings' ));
    		$filter = (isset($instance[ 'filter' ]) && !empty($instance[ 'filter' ]) ? $instance[ 'filter' ] : "");
            $title  = apply_filters("widget_title", $title);
    		
    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title;
    						
    			$options = get_single_listing_category($filter);
                $sort    = (isset($options['sort_terms']) && !empty($options['sort_terms']) ? $options['sort_terms'] : "");
    			$options = (isset($options['terms']) && !empty($options['terms']) ? $options['terms'] : "");
    				
    			if(isset($options) && !empty($options)){
    				$i = 0;
    				echo "<ul class='single_filter margin-bottom-none'>";

                    $url = (isset($_REQUEST['page_id']) && !empty($_REQUEST['page_id']) ? get_permalink($_REQUEST['page_id']) : get_permalink( $post->ID ));

                    if(isset($sort) && $sort == "desc"){
                        arsort($options);
                    } else {
                        asort($options);
                    }

    				foreach($options as $option){
    					  echo "<li><a href='" . str_replace("&", "&amp;", add_query_arg( (strtolower($filter) == "year" ? "yr" : /*str_replace("_", "-",*/ $filter/*)*/), urlencode(str_replace(" ", "-", strtolower($option) )), $url) ) . "'>" . $option . " (" . get_total_meta($filter, $option) . ")</a></li>\n";
    					  $i++;
    					  
    					  if($i == $number){
    						  break;
    					  }
    				}
    				echo "</ul>";
    			}
    			
    		echo "<div class='clearfix'></div>";
    		echo $after_widget;
    	}

     	public function form( $instance ) {		
    		$title   = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Filter', 'listings' ));
    		$number  = (isset($instance[ 'number' ]) && !empty($instance[ 'number' ]) ? $instance[ 'number' ] : __( '10', 'listings' ));
    		$cfilter = (isset($instance[ 'filter' ]) && !empty($instance[ 'filter' ]) ? $instance[ 'filter' ] : __( 'years', 'listings' )); ?>
            <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'number' ); ?>"><?php _e( 'Number of terms to display:', 'listings' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'filter' ); ?>"><?php _e( 'Filter:', 'listings' ); ?></label> 
            <select id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>" class="widefat">
            <?php		
     		$filterable = get_filterable_listing_categories();

    		foreach($filterable as $filter){
    			$spost_meta = str_replace(" ", "_", strtolower($filter['singular']));
    			echo "<option value='" . $spost_meta . "' " . selected($spost_meta, $cfilter) . ">" . $filter['singular'] . "</option>";
    		}
    		?>
            </select>
            <?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]  = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["number"] = ( !empty( $new_instance["number"] ) ) ? strip_tags( $new_instance["number"], $allowed ) : '';
    		$instance["filter"] = ( !empty( $new_instance["filter"] ) ) ? strip_tags( $new_instance["filter"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}


//********************************************
//	Contact Us
//***********************************************************
if(!class_exists("Contact_Us")){
    class Contact_Us extends WP_Widget {

    	public function Contact_Us() {
    		$widget_ops = array( 'classname' => 'contact_us', 'description' => __('A widget that displays contact information ', 'listings') );  
            $control_ops = array( 'id_base' => 'contact-us-widget' );  
            $this->WP_Widget( 'contact-us-widget', __('[LISTINGS] Contact Us', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		$title   = apply_filters( 'widget_title', $instance['title'] );
    		$phone   = apply_filters( 'widget_phone', $instance['phone'] );
    		$address = apply_filters( 'widget_address', $instance['address'] );
    		$email   = apply_filters( 'widget_email', $instance['email'] );

            //WMPL
            /**
             * retreive translations
             */
            if (function_exists ( 'icl_translate' )){
                $email = icl_translate('Widgets', 'Automotive Widget Contact Us Email Field', $email);
                $phone = icl_translate('Widgets', 'Automotive Widget Contact Us Phone Field', $phone);
                $address = icl_translate('Widgets', 'Automotive Widget Contact Us Address Field', $address);
            }

    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title; ?>
                
                <div class="footer-contact xs-margin-bottom-60">
                    <ul>
                        <li><i class="fa fa-map-marker"></i> <strong><?php _e("Address", "listings"); ?>:</strong> <?php echo $address; ?></li>
                        <li><i class="fa fa-phone"></i> <strong><?php _e("Phone", "listings"); ?>:</strong> <?php echo $phone; ?></li>
                        <li><i class="fa fa-envelope-o"></i> <strong><?php _e("Email", "listings"); ?>:</strong><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></li>
                    </ul>

                    <i class="fa fa-location-arrow back_icon"></i>
                </div>
            <?php
    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title   = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Contact Us', 'listings' ));
    		$phone   = (isset($instance[ 'phone' ]) && !empty($instance[ 'phone' ]) ? $instance[ 'phone' ] : "");
    		$address = (isset($instance[ 'address' ]) && !empty($instance[ 'address' ]) ? $instance[ 'address' ] : "");
    		$email   = (isset($instance[ 'email' ]) && !empty($instance[ 'email' ]) ? $instance[ 'email' ] : "");
    		?>
    		<p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
    		<label for="<?php echo $this->get_field_name( 'phone' ); ?>"><?php _e( 'Phone:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'phone' ); ?>" name="<?php echo $this->get_field_name( 'phone' ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'address' ); ?>"><?php _e( 'Address:', 'listings' ); ?></label> 
    		<textarea class="widefat" id="<?php echo $this->get_field_id( 'address' ); ?>" name="<?php echo $this->get_field_name( 'address' ); ?>"><?php echo esc_attr( $address ); ?></textarea>
            <br />
            <label for="<?php echo $this->get_field_name( 'email' ); ?>"><?php _e( 'Email:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>" />
    		</p>
    		<?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance['title']   = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'], $allowed ) : '';
    		$instance['phone']   = ( !empty( $new_instance['phone'] ) ) ? strip_tags( $new_instance['phone'], $allowed  ) : '';
    		$instance['address'] = ( !empty( $new_instance['address'] ) ) ? strip_tags( $new_instance['address'], $allowed  ) : '';
    		$instance['email']   = ( !empty( $new_instance['email'] ) ) ? strip_tags( $new_instance['email'], $allowed  ) : '';

            //WMPL
            /**
             * register strings for translation
             */
            if(function_exists('icl_register_string')){
                icl_register_string('Widgets', 'Automotive Widget Contact Us Email Field', $instance['email']);
                icl_register_string('Widgets', 'Automotive Widget Contact Us Phone Field', $instance['phone']);
                icl_register_string('Widgets', 'Automotive Widget Contact Us Address Field', $instance['address']);
            }
    		
    		return $instance;
    	}

    }
}

//********************************************
//	Business Hours
//***********************************************************
if(!class_exists("Business_Hours")){
    class Business_Hours extends WP_Widget {
    	public function Business_Hours() {
    		$widget_ops = array( 'classname' => 'business_hours', 'description' => __('A widget that displays a companies business hours', 'listings') );  
            $control_ops = array( 'id_base' => 'business-hours-widget' );  
            $this->WP_Widget( 'business-hours-widget', __('[LISTINGS] Business Hours', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		$title   = apply_filters( 'widget_title', $instance['title'] );
    		
    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title;
    			
    			$week = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
    			
    			echo "<table border='0'>";
    			foreach($week as $day){
    				$value = apply_filters( 'widget_value', $instance[$day] );
    		
    				echo "<tr><td>" . sprintf( __( '%s', 'listings' ), ucwords($day)) . ": </td><td> " . $value . "</td></tr>";
    			}
    			echo "</table>";
    			
    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$week = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
    		
    		echo "<p>"; 
    		$title = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Business Hours', 'listings' ));
    		?>
            <label for="<?php echo $this->get_field_name( "title" ); ?>"><?php _e( 'Title', 'listings' ); ?>:</label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( "title" ); ?>" name="<?php echo $this->get_field_name( "title" ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <?php
    		foreach($week as $day){ 
    			$$day = (isset($instance[$day]) && !empty($instance[$day]) ? $instance[$day] : "");
    			?>
    	        <label for="<?php echo $this->get_field_name( $day ); ?>"><?php echo sprintf( __( '%s', 'listings' ), ucwords($day)); ?>:</label> 
    			<input class="widefat" id="<?php echo $this->get_field_id( $day ); ?>" name="<?php echo $this->get_field_name( $day ); ?>" type="text" value="<?php echo esc_attr( $$day ); ?>" />
    	        <br />
    	        <?php
    		}
    		echo "</p>";
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$week     = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
    		$instance = array();	
    		$allowed  = $allowed_widget_tags;	
    		
    		foreach($week as $day){
    			$instance[$day] = ( !empty( $new_instance[$day] ) ) ? strip_tags( $new_instance[$day], $allowed ) : '';
    		}
    		
    		$instance["title"] = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}

//********************************************
//	Google Maps
//***********************************************************
if(!class_exists("Google_Map")){
    class Google_Map extends WP_Widget {

    	public function Google_Map() {
    		$widget_ops = array( 'classname' => 'google_map', 'description' => __('A widget that displays a google map of a location', 'listings') );  
            $control_ops = array( 'id_base' => 'google-map-widget' );  
            $this->WP_Widget( 'google-map-widget', __('[LISTINGS] Google Map', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) { 
    	extract( $args );
    		$title     = apply_filters( 'widget_title', $instance['title'] );
    		$type      = apply_filters( 'widget_type', $instance['type'] );
    		$zoom      = apply_filters( 'widget_type', $instance['zoom'] );
    		$latitude  = apply_filters( 'widget_latitude', $instance['latitude'] );
    		$longitude = apply_filters( 'widget_longitude', $instance['longitude'] );
    		$rand_id   = random_string();

    		wp_enqueue_script( 'google-maps' );
    		
    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title; ?>
        <script>
    	jQuery(document).ready( function($) {
    		var map;
    		var latlng = new google.maps.LatLng(<?php echo (isset($latitude) && !empty($latitude) ? $latitude : "-34.397"); ?>, <?php echo (isset($longitude) && !empty($longitude) ? $longitude : "150.644"); ?>);
    		
    		function initialize() {
    		  var mapOptions = {
    			zoom: <?php echo $zoom; ?>,
    			center: latlng,
    			mapTypeId: google.maps.MapTypeId.<?php echo (isset($type) && !empty($type) ? strtoupper($type) : "ROADMAP"); ?>
    		  };
    		  map = new google.maps.Map(document.getElementById('<?php echo $rand_id; ?>'),
    			  mapOptions);
    			  
    		  var marker = new google.maps.Marker({
    			  position: latlng,
    			  map: map
    		  });
    		}
    		
    		google.maps.event.addDomListener(window, 'load', initialize);
    		$("#<?php echo $rand_id; ?>").height($("#<?php echo $rand_id; ?>").width());
    	});
        </script>
        <div id="<?php echo $rand_id; ?>" class='map-canvas'></div>
        <?php
    	echo $after_widget;
    	}

     	public function form( $instance ) { 
    		$map_types = array("roadmap", "satellite", "hybrid", "terrain");
    		
    		$title     = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Location', 'listings' ));
    		$latitude  = (isset($instance[ 'latitude' ]) && !empty($instance[ 'latitude' ]) ? $instance[ 'latitude' ] : "");
    		$longitude = (isset($instance[ 'longitude' ]) && !empty($instance[ 'longitude' ]) ? $instance[ 'longitude' ] : "");
    		$type      = (isset($instance[ 'type' ]) && !empty($instance[ 'type' ]) ? $instance[ 'type' ] : "");
    		$zoom      = (isset($instance[ 'zoom' ]) && !empty($instance[ 'zoom' ]) ? $instance[ 'zoom' ] : "8");		
    	?>
        <p>
        <label for="<?php echo $this->get_field_name( "title" ); ?>"><?php _e("Title", "listings"); ?>:</label> 
        <input class="widefat" id="<?php echo $this->get_field_id( "title" ); ?>" name="<?php echo $this->get_field_name( "title" ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <br />
        <label for="<?php echo $this->get_field_name( "latitude" ); ?>"><?php _e("Latitude", "listings"); ?>:</label> 
        <input class="widefat" id="<?php echo $this->get_field_id( "latitude" ); ?>" name="<?php echo $this->get_field_name( "latitude" ); ?>" type="text" value="<?php echo esc_attr( $latitude ); ?>" />
        <br />
        <label for="<?php echo $this->get_field_name( "longitude" ); ?>"><?php _e("Longitude", "listings"); ?>:</label> 
        <input class="widefat" id="<?php echo $this->get_field_id( "longitude" ); ?>" name="<?php echo $this->get_field_name( "longitude" ); ?>" type="text" value="<?php echo esc_attr( $longitude ); ?>" />
        <br />
        <label for="<?php echo $this->get_field_name( "zoom" ); ?>"><?php _e("Zoom", "listings"); ?>: <span class='zoom_level'><?php echo $zoom; ?></span></label> 
        <input id="<?php echo $this->get_field_id( "zoom" ); ?>" name="<?php echo $this->get_field_name( "zoom" ); ?>" type="hidden" value="<?php echo esc_attr( $zoom ); ?>" class="zoom_text" />
        <div class="zoom_slider"></div>
        <script type="text/javascript">
    	jQuery(document).ready( function($) {
    		$(".zoom_slider").slider({
    			max: 21,
    			min: 0,
    			value: <?php echo $zoom; ?>,
    			slide: function( event, ui ) {
    				$( ".zoom_text" ).val( ui.value );
    				$( ".zoom_level" ).text( ui.value );
    			}
    		});
    	});
    	</script>
        <br />
        <label for="<?php echo $this->get_field_name( "type" ); ?>"><?php _e("Map Type", "listings"); ?>: </label>
        <select id="<?php echo $this->get_field_id( "type" ); ?>" name="<?php echo $this->get_field_name( "type" ); ?>" class="widefat">
        <?php
    	foreach($map_types as $map_type){
    		echo ($map_type != $type ? "<option value='" . $map_type . "'>" . ucwords($map_type) . "</option>" : "<option value='" . $map_type . "' selected='selected'>" . ucwords($map_type) . "</option>");
    	}
    	?>
        </select>
        <br />
        
        </p>
        <?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;	
    		
    		$instance["title"]     = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["latitude"]  = ( !empty( $new_instance["latitude"] ) ) ? strip_tags( $new_instance["latitude"], $allowed ) : '';
    		$instance["longitude"] = ( !empty( $new_instance["longitude"] ) ) ? strip_tags( $new_instance["longitude"], $allowed ) : '';
    		$instance["type"]      = ( !empty( $new_instance["type"] ) ) ? strip_tags( $new_instance["type"], $allowed ) : '';
    		$instance["zoom"]      = ( !empty( $new_instance["zoom"] ) ) ? strip_tags( $new_instance["zoom"], $allowed ) : '';
    		
    		return $instance;
    	}

    }
}

//********************************************
//	MailChimp Newsletter
//***********************************************************
if(!class_exists("Mail_Chimp")){
    class Mail_Chimp extends WP_Widget {

    	public function Mail_Chimp() {
    		$widget_ops = array( 'classname' => 'mail_chimp', 'description' => __('A widget that displays a form for users to register for a mailchimp newsletter', 'listings') );  
            $control_ops = array( 'id_base' => 'mail-chimp-widget' );  
            $this->WP_Widget( 'mail-chimp-widget', __('[LISTINGS] Mail Chimp', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {				
    		extract( $args );
    		$title       = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Newsletter', 'listings' ));
    		$description = (isset($instance[ 'description' ]) && !empty($instance[ 'description' ]) ? $instance[ 'description' ] : "");
    		$list        = (isset($instance[ 'list' ]) && !empty($instance[ 'list' ]) ? $instance[ 'list' ] : "");
            $title       = apply_filters("widget_title", $title);

            //WMPL
            /**
             * retreive translations
             */
            if (function_exists ( 'icl_translate' )){
                $description = icl_translate('Widgets', 'Automotive Widget MailChimp Description Field', $description);
            }
    		
    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo "<div class='newsletter'>";
    			echo $before_title . $title . $after_title;
    			
    			if ( ! empty( $description ) )
    				echo "<p class='description margin-bottom-20'>" . $description . "</p>";
    				
    			echo "<div class='form_contact'>";
    			echo "<input type='text' class='email margin-bottom-15' placeholder='" . __("Email Address", "listings") . "'><button class='add_mailchimp button pull-left md-button' data-list='" . $list . "'>" . __("Subscribe", "listings") . "</button><br><span class='response'></span>";
    			echo "</div>";
    			
    		echo "</div>";
    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title       = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Newsletter', 'listings' ));
    		$description = (isset($instance[ 'description' ]) && !empty($instance[ 'description' ]) ? $instance[ 'description' ] : "" );
    		$list        = (isset($instance[ 'list' ]) && !empty($instance[ 'list' ]) ? $instance[ 'list' ] : "" );
    		?>
    		<p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'description' ); ?>"><?php _e( 'Description:', 'listings' ); ?></label> 
    		<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" ><?php echo esc_attr( $description ); ?></textarea>
            <br />
            <label for="<?php echo $this->get_field_name( 'list' ); ?>"><?php _e( 'List:', 'listings' ); ?></label> 
    		<select id="<?php echo $this->get_field_id( 'list' ); ?>" name="<?php echo $this->get_field_name( 'list' ); ?>" class="widefat">
            <?php require_once(LISTING_HOME . "/classes/mailchimp/MCAPI.class.php");
    	
    		global $lwp_options;
    		
    		$api_key   = $lwp_options['mailchimp_api_key'];
    		$api       = new MCAPI($api_key);
    		$list_list = $api->lists();
    		
    		if(!empty($list_list['data'])){
    			foreach($list_list['data'] as $lists){
    				echo ($lists['id'] == $list ? "<option value='" . $lists['id'] . "' selected='selected'>" . $lists['name'] . "</option>" : "<option value='" . $lists['id'] . "'>" . $lists['name'] . "</option>");
    			}
    		}
    		?>
            </select>
            <br />
            </p>
            <?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]       = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["description"] = ( !empty( $new_instance["description"] ) ) ? strip_tags( $new_instance["description"], $allowed ) : '';
    		$instance["list"]        = ( !empty( $new_instance["list"] ) ) ? strip_tags( $new_instance["list"], $allowed ) : '';
    		
            //WMPL
            /**
             * register strings for translation
             */
            if(function_exists('icl_register_string')){
                icl_register_string('Widgets', 'Automotive Widget MailChimp Description Field', $instance['description']);
            }

    		return $instance;
    	}

    }
}

//********************************************
//	Twitter
//***********************************************************
if(!class_exists("Twitter_Feed")){
    class Twitter_Feed extends WP_Widget {

    	public function Twitter_Feed() {
    		$widget_ops = array( 'classname' => 'twitter_feed', 'description' => __('A widget that displays a feed from your twitter', 'listings') );  
            $control_ops = array( 'id_base' => 'twitter-feed-widget' );  
            $this->WP_Widget( 'twitter-feed-widget', __('[LISTINGS] Twitter Feed', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		$title    = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Newsletter', 'listings' ));
    		$username = (isset($instance[ 'username' ]) && !empty($instance[ 'username' ]) ? $instance[ 'username' ] : "themesuite" );
    		$tweets   = (isset($instance[ 'tweets' ]) && !empty($instance[ 'tweets' ]) ? $instance[ 'tweets' ] : 2 );
            $title    = apply_filters("widget_title", $title);
    		
    		wp_enqueue_script('twitter_feed');
    		wp_enqueue_script('twitter_tweet');

    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title;
    			
    			echo "<div class='twitterfeed'></div>";
    			?>
                <script type="text/javascript">
    			jQuery(document).ready( function($){
    				$('.twitterfeed').tweet({
    					modpath: '<?php echo JS_DIR . "twitter/"; ?>', 					
    					count: <?php echo $tweets; ?>,
    					loading_text: '<?php _e("Loading twitter feed", "listings"); ?>...',
    					username: '<?php echo $username; ?>'
    				});
    			});
    			</script>
                <?php
    			echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title    = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Twitter Feed', 'listings' ));
    		$username = (isset($instance[ 'username' ]) && !empty($instance[ 'username' ]) ? $instance[ 'username' ] : "" );
    		$tweets   = (isset($instance[ 'tweets' ]) && !empty($instance[ 'tweets' ]) ? $instance[ 'tweets' ] : "" );
    		?>
    		<p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'username' ); ?>"><?php _e( 'Username:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'tweets' ); ?>"><?php _e( 'Number of tweets:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'tweets' ); ?>" name="<?php echo $this->get_field_name( 'tweets' ); ?>" type="text" value="<?php echo esc_attr( $tweets ); ?>" />
            </p>
            
            <?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]    = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["username"] = ( !empty( $new_instance["username"] ) ) ? strip_tags( $new_instance["username"], $allowed ) : '';
    		$instance["tweets"] = ( !empty( $new_instance["tweets"] ) ) ? strip_tags( $new_instance["tweets"], $allowed ) : '';
    		
    		return $instance;
    	}

    }
}

//********************************************
//	Flickr Widget
//***********************************************************
if(!class_exists("Flickr_Pictures")){
    class Flickr_Pictures extends WP_Widget {

    	public function Flickr_Pictures() {
    		$widget_ops = array( 'classname' => 'flickr_pictures', 'description' => __('A widget that displays photos from a flickr feed', 'listings') );  
            $control_ops = array( 'id_base' => 'flickr-pictures-widget' );  
            $this->WP_Widget( 'flickr-pictures-widget', __('[LISTINGS] Flickr Pictures', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		$title     = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Twitter Feed', 'listings' ));
    		$flickr_id = (isset($instance[ 'flickr_id' ]) && !empty($instance[ 'flickr_id' ]) ? $instance[ 'flickr_id' ] : "998875@N22" );
            $title     = apply_filters("widget_title", $title);
    		
    		echo $before_widget;
    		if ( ! empty( $title ) )
    			echo $before_title . $title . $after_title;
    			
    			echo "<div class='flickr_feed' data-id='" . $flickr_id . "'></div>";
    			
    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title     = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Flickr Feed', 'listings' ));
    		$flickr_id = (isset($instance[ 'flickr_id' ]) && !empty($instance[ 'flickr_id' ]) ? $instance[ 'flickr_id' ] : "998875@N22" );
    		?>
    		<p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'flickr_id' ); ?>"><?php _e( 'Flickr ID:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'flickr_id' ); ?>" name="<?php echo $this->get_field_name( 'flickr_id' ); ?>" type="text" value="<?php echo esc_attr( $flickr_id ); ?>" />
            </p>
            
            <?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]     = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["flickr_id"] = ( !empty( $new_instance["flickr_id"] ) ) ? strip_tags( $new_instance["flickr_id"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}

//********************************************
//	Custom recent posts
//***********************************************************
if(!class_exists("Recent_Posts")){
    class Recent_Posts extends WP_Widget {

    	public function Recent_Posts() {
    		$widget_ops = array( 'classname' => 'recent_posts', 'description' => __('A widget that can displays your posts with the featured image.', 'listings') );  
            $control_ops = array( 'id_base' => 'recent-posts-widget' );  
            $this->WP_Widget( 'recent-posts-widget', __('[LISTINGS] Recent Posts', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		
    		$title = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Recent Posts', 'listings' ));
    		$posts = (isset($instance[ 'posts' ]) && !empty($instance[ 'posts' ]) ? $instance[ 'posts' ] : __( 5, 'listings' ));
            $title = apply_filters("widget_title", $title);
    		
    		echo $before_widget;
    		echo $before_title . $title . $after_title;
    		
    		$post_args = array("posts_per_page" => $posts,
    						   "order"			=> "DESC",
    						   "orderby"		=> "date");
    		
    		$posts = get_posts( $post_args );
    		
    		echo "<div class='recent_posts_container'>";
    		foreach($posts as $single_post){			
    			echo "<div class=\"side-blog\">";
    			if(has_post_thumbnail( $single_post->ID )){
    				echo "<a href='" . get_permalink($single_post->ID) . "'>" . get_the_post_thumbnail($single_post->ID, array(50,50), array('class' => 'alignleft')) . "</a>";
    			} else if(get_first_post_image($single_post)){
    				echo "<a href='" . get_permalink($single_post->ID) . "'><img src='" . get_first_post_image($single_post) . "' class='alignleft wp-post-image'></a>";
    			}
    			echo "<strong><a href='" . get_permalink($single_post->ID) . "'>" . get_the_title($single_post) . "</a></strong>";
    				echo "<p>" . substr(strip_shortcodes(strip_tags($single_post->post_content)), 0, 55) . " " . (strlen(strip_shortcodes(strip_tags($single_post->post_content))) > 55 ? "..." : "") . "</p>";
    			echo "</div>";
    		}
    		echo "</div>";
    		
    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Recent Posts', 'listings' ));
    		$posts = (isset($instance[ 'posts' ]) && !empty($instance[ 'posts' ]) ? $instance[ 'posts' ] : __( 5, 'listings' ));
    		
    		?>
            <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'posts' ); ?>"><?php _e( 'Number of posts:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" type="text" value="<?php echo esc_attr( $posts ); ?>" />
            </p>
            <?php
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"] = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["posts"] = ( !empty( $new_instance["posts"] ) ) ? strip_tags( $new_instance["posts"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}


//********************************************
//	Recent Listings Widget
//***********************************************************
if(!class_exists("Recent_Listings")){
    class Recent_Listings extends WP_Widget {

    	public function Recent_Listings() {
    		$widget_ops = array( 'classname' => 'recent_listings', 'description' => __('A widget that can show a custom amount of options', 'listings') );  
            $control_ops = array( 'id_base' => 'recent-listings-widget' );  
            $this->WP_Widget( 'recent-listings-widget', __('[LISTINGS] Recent Listings', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		
    		$title  = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Recent Listings', 'listings' ));
    		$number = (isset($instance[ 'number' ]) && !empty($instance[ 'number' ]) ? $instance[ 'number' ] : 3);
            $title  = apply_filters("widget_title", $title);

    		echo $before_widget;
    		echo $before_title . $title . $after_title;


    		$listings = get_posts(array("post_type" => "listings", "posts_per_page" => $number, "orderby" => "post_date", "order" => "DESC"));

    		if(!empty($listings)){
    			foreach($listings as $listing){
    				$options   = unserialize(get_post_meta($listing->ID, "listing_options", true));
    				$gallery   = (array)(get_post_meta($listing->ID, "gallery_images", true));

    				$image     = auto_image($gallery[0], "auto_thumb", true);

    				echo '<div class="car-block recent_car">         
    			            <div class="car-block-bottom">
    			            	<div class="img-flex"> 
    				            	<a href="' . get_permalink($listing->ID) . '">
    				            		<span class="align-center"><i class="fa fa-2x fa-plus-square-o"></i></span>
    				            	</a> 
    				            	<img src="' . $image . '" alt="" class="img-responsive"> 
    				            </div>
    			                <h6><strong>' . $listing->post_title . '</strong></h6>
    			                ' . (!empty($options['short_desc']) ? '<h6>' . $options['short_desc']. '</h6>' : '') . '
    			                <h5>' . format_currency($options['price']['value']) . '</h5>
    			            </div>
    			        </div>';
    			}
    		}

    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title  = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Recent Listings', 'listings' ));
    		$number = (isset($instance[ 'number' ]) && !empty($instance[ 'number' ]) ? $instance[ 'number' ] : 2); ?>

    		<p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

    		<p>
            <label for="<?php echo $this->get_field_name( 'number' ); ?>"><?php _e( 'Number of Listings:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
            </p>
            <?php 
    		
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]  = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["number"] = ( !empty( $new_instance["number"] ) ) ? strip_tags( $new_instance["number"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}

//********************************************
//	 Contact Form
//***********************************************************
if(!class_exists("Contact_Form")){
    class Contact_Form extends WP_Widget {

    	public function Contact_Form() {
    		$widget_ops = array( 'classname' => 'contact_form', 'description' => __('A widget that displays a contact form and emails it to the email specified in the Contact Settings (under the Theme Options).', 'listings') );  
            $control_ops = array( 'id_base' => 'contact-form-widget' );  
            $this->WP_Widget( 'contact-form-widget', __('[LISTINGS] Contact Form', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract($args);

    		wp_enqueue_script( 'contact_form' );
    		
    		$title   = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Get In Touch', 'listings' ));
    		$name    = (isset($instance[ 'name' ]) && !empty($instance[ 'name' ]) ? $instance[ 'name' ] : __('Name', 'listings')); 
    		$email   = (isset($instance[ 'email' ]) && !empty($instance[ 'email' ]) ? $instance[ 'email' ] : __('Email', 'listings')); 
    		$message = (isset($instance[ 'message' ]) && !empty($instance[ 'message' ]) ? $instance[ 'message' ] : __('Message', 'listings'));
    		$button  = (isset($instance[ 'button' ]) && !empty($instance[ 'button' ]) ? $instance[ 'button' ] : __('Send', 'listings'));
            $title   = apply_filters("widget_title", $title);

            //WMPL
            /**
             * retreive translations
             */
            if (function_exists ( 'icl_translate' )){
                $name = icl_translate('Widgets', 'Automotive Widget Contact Form Name Field', $name);
                $email = icl_translate('Widgets', 'Automotive Widget Contact Form Email Field', $email);
                $message = icl_translate('Widgets', 'Automotive Widget Contact Form Message Field', $message);
                $button = icl_translate('Widgets', 'Automotive Widget Contact Form Button Field', $button);
            }

    		echo $before_widget;
    		echo $before_title . $title . $after_title;
    		?>
    		<form method="post" action="" class="form_contact">
    			<div class="contact_result"></div>

                <input type="text" value="" name="name" placeholder="<?php echo $name; ?>">
                <input type="text" value="" name="email" placeholder="<?php echo $email; ?>">

                <textarea name="message" placeholder="<?php echo $message; ?>"></textarea>
                <input type="submit" value="<?php echo $button; ?>" class="md-button submit_contact_form">
            </form>
    	<?php

    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title   = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Get In Touch', 'listings' ));
    		$name    = (isset($instance[ 'name' ]) && !empty($instance[ 'name' ]) ? $instance[ 'name' ] : __('Name', 'listings')); 
    		$email   = (isset($instance[ 'email' ]) && !empty($instance[ 'email' ]) ? $instance[ 'email' ] : __('Email', 'listings')); 
    		$message = (isset($instance[ 'message' ]) && !empty($instance[ 'message' ]) ? $instance[ 'message' ] : __('Message', 'listings'));
    		$button  = (isset($instance[ 'button' ]) && !empty($instance[ 'button' ]) ? $instance[ 'button' ] : __('Send', 'listings')); ?>	

    		<p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>

    		<p>
            <label for="<?php echo $this->get_field_name( 'name' ); ?>"><?php _e( 'Name Placeholder:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>" />
            </p>

    		<p>
            <label for="<?php echo $this->get_field_name( 'email' ); ?>"><?php _e( 'Email Placeholder:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>" />
            </p>

    		<p>
            <label for="<?php echo $this->get_field_name( 'message' ); ?>"><?php _e( 'Message Placeholder:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'message' ); ?>" name="<?php echo $this->get_field_name( 'message' ); ?>" type="text" value="<?php echo esc_attr( $message ); ?>" />
            </p>

    		<p>
            <label for="<?php echo $this->get_field_name( 'button' ); ?>"><?php _e( 'Button Text:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'button' ); ?>" name="<?php echo $this->get_field_name( 'button' ); ?>" type="text" value="<?php echo esc_attr( $button ); ?>" />
            </p>
    	<?php	
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]   = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["name"]    = ( !empty( $new_instance["name"] ) ) ? strip_tags( $new_instance["name"], $allowed ) : '';
    		$instance["email"]   = ( !empty( $new_instance["email"] ) ) ? strip_tags( $new_instance["email"], $allowed ) : '';
    		$instance["message"] = ( !empty( $new_instance["message"] ) ) ? strip_tags( $new_instance["message"], $allowed ) : '';
    		$instance["button"]  = ( !empty( $new_instance["button"] ) ) ? strip_tags( $new_instance["button"], $allowed ) : '';
            
            //WMPL
            /**
             * register strings for translation
             */
            if(function_exists('icl_register_string')){
                icl_register_string('Widgets', 'Automotive Widget Contact Form Name Field', $instance['name']);
                icl_register_string('Widgets', 'Automotive Widget Contact Form Email Field', $instance['email']);
                icl_register_string('Widgets', 'Automotive Widget Contact Form Message Field', $instance['message']);
                icl_register_string('Widgets', 'Automotive Widget Contact Form Button Field', $instance['button']);
            }
    		
    		return $instance;
    	}
    }
}

//********************************************
//	Testimonial Widget
//***********************************************************
if(!class_exists("Testimonial_Slider")){
    class Testimonial_Slider extends WP_Widget {

    	public function Testimonial_Slider() {
    		$widget_ops = array( 'classname' => 'testimonial_slider', 'description' => __('A widget that can slide through customer testimonials', 'listings') );  
            $control_ops = array( 'id_base' => 'testimonial-slider-widget' );  
            $this->WP_Widget( 'testimonial-slider-widget', __('[LISTINGS] Testimonial Slider', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		
    		$title  = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Testimonials', 'listings' ));
    		$fields = (isset($instance[ 'fields' ]) && !empty($instance[ 'fields' ]) ? $instance[ 'fields' ] : "");
            $title  = apply_filters("widget_title", $title);
    		
    		echo $before_widget;
    		echo $before_title . $title . $after_title;
    		
    		$field_and_value = explode("&", $fields);
    		$field_and_value = array_chunk($field_and_value, 2);
    		
    		$widget = array();
    		
    		if(!empty($field_and_value) && !empty($field_and_value[0]) && !empty($field_and_value[0][0])){
    			foreach($field_and_value as $values){
    				$explode  = explode("=", $values[0]);
    				$explode2 = explode("=", $values[1]);
    				
    				$name = $explode[1];
    				$text = $explode2[1];
    				
    				array_push($widget, array('name' => urldecode($name), 'content' => urldecode($text)));
    			}
    		
    			echo testimonial_slider("horizontal", 500, "false", "", $widget);
    		}
    		
    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title  = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'Testimonials', 'listings' ));
    		$fields = (isset($instance[ 'fields' ]) && !empty($instance[ 'fields' ]) ? $instance[ 'fields' ] : "");
    		
    		$id = random_string();
    		?>
            <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <input type="hidden" value="<?php echo $fields; ?>" name="<?php echo $this->get_field_name( 'fields' ); ?>" class='testimonial_fields' id="<?php echo $id; ?>" />
            <br />
            </p>
            
            <span class='edit_testimonials btn button' data-id="<?php echo $id; ?>"><?php _e("Edit Testimonials", "listings"); ?></span>
                    
            <?php
    		
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]  = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["fields"] = ( !empty( $new_instance["fields"] ) ) ? strip_tags( $new_instance["fields"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}

//********************************************
//	^ Modal for Testimonial Widget ^
//***********************************************************
function testimonial_window(){
	echo "<div id='testimonial_window' title='" . __("Testimonials", "listings") . "'>";
	echo "<form id='testimonial_form'>";
	echo "<table class='load' style='border: 0; width: 100%;'>";
	
	echo "</table>";
	echo "</form>";
	echo "</div>";
}
add_action( 'admin_footer', 'testimonial_window');


//********************************************
//	Process Fields
//***********************************************************
function testimonial_widget_fields(){
	$value = $_POST['value'];
	
	if(isset($value) && !empty($value)){		
		$field_and_value = explode("&", $value);
		$field_and_value = array_chunk($field_and_value, 2);
		
		$widget = array();
		$i      = 1;
		
		foreach($field_and_value as $values){
			$explode  = explode("=", $values[0]);
			$explode2 = explode("=", $values[1]);
			
			$name = $explode[1];
			$text = $explode2[1];
			
			echo "<tr><td>Name: </td><td> <input type='text' name='testimonial_name_" . $i . "' value='" . urldecode($name) . "'>&nbsp; <i class='fa fa-times remove_testimonial'></i></td></tr>";
			echo "<tr><td>Text: </td><td> <textarea name='testimonial_text_" . $i . "'>" . urldecode($text) . "</textarea></td></tr>";
			$i++;
		}
	} else {
		echo "<tr><td>Name: </td><td> <input type='text' name='testimonial_name_1'></td></tr>";
		echo "<tr><td>Text: </td><td> <textarea name='testimonial_text_1'></textarea></td></tr>";
	}
	
	die;
}
add_action("wp_ajax_testimonial_widget_fields", "testimonial_widget_fields");
add_action("wp_ajax_nopriv_testimonial_widget_fields", "testimonial_widget_fields");

//********************************************
//	List Item Shortcode
//***********************************************************
if(!class_exists("List_Items")){
    class List_Items extends WP_Widget {

    	public function List_Items() {
    		$widget_ops = array( 'classname' => 'list_items', 'description' => __('A widget that can create a list from a bunch of items', 'listings') );  
            $control_ops = array( 'id_base' => 'list-items-widget' );  
            $this->WP_Widget( 'list-items-widget', __('[LISTINGS] List Items', 'listings'), $widget_ops, $control_ops );
    	}

    	public function widget( $args, $instance ) {
    		extract( $args );
    		
    		$title  = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'List', 'listings' ));
    		$style  = trim(isset($instance[ 'style' ]) && !empty($instance[ 'style' ]) ? $instance[ 'style' ] : "");
    		$fields = (isset($instance[ 'fields' ]) && !empty($instance[ 'fields' ]) ? $instance[ 'fields' ] : "");
            $title  = apply_filters("widget_title", $title);
    		
    		echo $before_widget;
    		echo $before_title . $title . $after_title;
    		
    		echo "<ul class='icons-ul shortcode type-" . $style . "'>";
    		$field_and_value = explode("&", $fields);

    		if(!empty($field_and_value) && !empty($field_and_value[0])){
    			foreach($field_and_value as $values){
    				$explode  = explode("=", $values);
    				$text     = $explode[1];
    				
    				switch($style){
    					case "checkboxes";
    						$icon = "<i class=\"fa fa-check\"></i>";	
    						break;
    					default:
    						$icon = "<span class=\"red_box\"><i class=\"fa fa-angle-right fa-light\"></i></span>";	
    						break;
    				}
    							
    				echo "<li>" . $icon . urldecode($text) . "</li>";
    			}
    		}
    		echo "</ul>";
    		
    		echo $after_widget;
    	}

     	public function form( $instance ) {
    		$title  = (isset($instance[ 'title' ]) && !empty($instance[ 'title' ]) ? $instance[ 'title' ] : __( 'List', 'listings' ));
    		$style  = (isset($instance[ 'style' ]) && !empty($instance[ 'style' ]) ? $instance[ 'style' ] : "");
    		$fields = (isset($instance[ 'fields' ]) && !empty($instance[ 'fields' ]) ? $instance[ 'fields' ] : "");
    		
    		$id = random_string(); ?>
            <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'listings' ); ?></label> 
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            <br />
            <label for="<?php echo $this->get_field_name( 'style' ); ?>"><?php _e( 'Style:', 'listings' ); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
            <?php $styles = array("arrows", "checkboxes");
    		foreach($styles as $single_style){
    			echo "<option value='" . $single_style . " " . selected($style, $single_style) . "'>" . ucwords($single_style) . "</option>";
    		}
    		?>
            </select>
            <input type="hidden" value="<?php echo $fields; ?>" name="<?php echo $this->get_field_name( 'fields' ); ?>" class='list_fields' id="<?php echo $id; ?>" />
            <br />
            </p>
            
            <span class='edit_list btn button' data-id="<?php echo $id; ?>"><?php _e("Edit List", "listings"); ?></span>
                    
            <?php
    		
    	}

    	public function update( $new_instance, $old_instance ) {
            global $allowed_widget_tags;

    		$instance = array();
    		$allowed  = $allowed_widget_tags;
    		
    		$instance["title"]  = ( !empty( $new_instance["title"] ) ) ? strip_tags( $new_instance["title"], $allowed ) : '';
    		$instance["style"]  = ( !empty( $new_instance["style"] ) ) ? strip_tags( $new_instance["style"], $allowed ) : '';
    		$instance["fields"] = ( !empty( $new_instance["fields"] ) ) ? strip_tags( $new_instance["fields"], $allowed ) : '';
    		
    		return $instance;
    	}
    }
}

//********************************************
//	^ Modal for List Widget ^
//***********************************************************
function list_window(){
	echo "<div id='list_window' title='List'>";
	echo "<form id='list_form'>";
	echo "<table class='load'>";
	
	echo "</table>";
	echo "</form>";
	echo "</div>";
}
add_action( 'admin_footer', 'list_window');

//********************************************
//	Process Fields
//***********************************************************
function list_widget_fields(){
	$value = $_POST['value'];
	
	if(isset($value) && !empty($value)){		
		$field_and_value = explode("&", $value);
		
		foreach($field_and_value as $values){
			$explode  = explode("=", $values);
			
			$text     = $explode[1];
						
			echo "<tr><td>" . __("List Item", "listings") . ": </td><td> <input type='text' name='list_item' value='" . urldecode($text) . "'>&nbsp; <i class='fa fa-times remove_list_item'></i></td></tr>";
			$i++;
		}
	} else {
		echo "<tr><td>" . __("List Item", "listings") . ": </td><td> <input type='text' name='list_item'>&nbsp; <i class='fa fa-times remove_list_item'></i></td></tr>";
	}
	
	die;
}
add_action("wp_ajax_list_widget_fields", "list_widget_fields");
add_action("wp_ajax_nopriv_list_widget_fields", "list_widget_fields");


class Extended_Categories extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'extended_widget_categories', 'description' => __( "A list or dropdown of categories.", 'listings' ) );
		parent::__construct('extended_categories', __('[LISTINGS] Categories', 'listings'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Categories', 'listings' ) : $instance['title'], $instance, $this->id_base );

		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$h = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h, 'hide_empty' => 0);

		if ( $d ) {
			$cat_args['show_option_none'] = __('Select Category', 'listings');

			/**
			 * Filter the arguments for the Categories widget drop-down.
			 *
			 * @since 2.8.0
			 *
			 * @see wp_dropdown_categories()
			 *
			 * @param array $cat_args An array of Categories widget drop-down arguments.
			 */
			wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );
?>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo home_url(); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
		} else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';

		/**
		 * Filter the arguments for the Categories widget.
		 *
		 * @since 2.8.0
		 *
		 * @param array $cat_args An array of Categories widget options.
		 */
		wp_list_categories( apply_filters( 'widget_categories_args', $cat_args ) );
?>
		</ul>
<?php
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'listings' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e( 'Display as dropdown', 'listings' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts', 'listings' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e( 'Show hierarchy', 'listings' ); ?></label></p>
<?php
	}

}


//********************************************
//	 stem widget (still very controversial) 
//***********************************************************
class My_Widget extends WP_Widget {

	public function My_Widget() {
		$widget_ops = array( 'classname' => 'single_filter', 'description' => __('A widget that can filter/search listings and shows a custom amount of options', 'listings') );  
        $control_ops = array( 'id_base' => 'single-filter-widget' );  
        $this->WP_Widget( 'single-filter-widget', __('[LISTINGS] Single Filter', 'listings'), $widget_ops, $control_ops );
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
	}

 	public function form( $instance ) {
		
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}

function register_listing_widgets(){
	global $lwp_options;
	
	register_widget( 'Loan_Calculator' );
	register_widget( 'Filter_Listings' );
	register_widget( 'Single_Filter' );

	register_widget( 'Contact_Us' );
	//register_widget( 'Business_Hours' );
	register_widget( 'Google_Map' );
	register_widget( 'Mail_Chimp' );
	(isset($lwp_options['twitter_switch']) && $lwp_options['twitter_switch'] == 1 ? register_widget( 'Twitter_Feed' ) : "");
	//register_widget( 'Flickr_Pictures' );
	register_widget( 'Recent_Posts' );
	register_widget( 'Recent_Listings' );
	register_widget( 'Contact_Form' );
	register_widget( 'Testimonial_Slider' );
	register_widget( 'List_Items' );
	register_widget( 'Extended_Categories' );
}

add_action( 'widgets_init', 'register_listing_widgets' );

?>