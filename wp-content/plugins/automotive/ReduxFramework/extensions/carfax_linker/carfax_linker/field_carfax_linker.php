<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_carfax_linker' ) ) {

    /**
     * Main ReduxFramework_carfax_linker class
     *
     * @since       1.0.0
     */
    class ReduxFramework_carfax_linker extends ReduxFramework {
    
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {
        
            
            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
            }    

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'options'           => array(),
                'stylesheet'        => '',
                'output'            => true,
                'enqueue'           => true,
                'enqueue_frontend'  => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );            
        
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {
        
            // HTML output goes here
            $current_url      = (isset($this->value['url']) && !empty($this->value['url']) ? $this->value['url'] : "");
            $current_category = (isset($this->value['category']) && !empty($this->value['category']) ? $this->value['category'] : "");

            echo "<input type='text' name='" . $this->field['name'] . "[url]' placeholder='" . __("URL", "listings") . "' class='regular-text' style='margin-bottom: 10px;' value='" . $current_url . "'><br>";

            echo "<select name='" . $this->field['name'] . "[category]'>";
            $categories = get_listing_categories();

            echo "<option value='none'>" . __("Choose VIN Category", "listings") . "</option>";
            foreach($categories as $key => $category){
                echo "<option value='" . $key . "'" . selected( $current_category, $key ) . ">" . wpml_category_translate($category['singular'], "singular", $category['singular']) . "</option>";
            }
            echo "</select>";
        }
    
        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public static function enqueue() {

            $extension = ReduxFramework_extension_carfax_linker::getInstance();
        
        }
        
        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */        
        public static function output() {

            if ( $this->field['enqueue_frontend'] ) {

            }
            
        }        
        
    }
}
