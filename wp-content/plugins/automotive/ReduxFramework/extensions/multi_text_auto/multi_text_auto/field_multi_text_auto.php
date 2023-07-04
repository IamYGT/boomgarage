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
if( !class_exists( 'ReduxFramework_multi_text_auto' ) ) {

    /**
     * Main ReduxFramework_multi_text_auto class
     *
     * @since       1.0.0
     */
    class ReduxFramework_multi_text_auto extends ReduxFramework {
    
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

            $this->add_text   = ( isset( $this->field['add_text'] ) ) ? $this->field['add_text'] : __( 'Add More', 'redux-framework' );
            $this->show_empty = ( isset( $this->field['show_empty'] ) ) ? $this->field['show_empty'] : true;

            echo '<ul id="' . $this->field['id'] . '-ul" class="redux-multi-text">';

            // migrate the old info to new
            if(!isset($this->value['value']) && !empty($this->value) && isset($this->value[0])){
                foreach($this->value as $key => $term){
                    $this->value['value'][] = $term;
                    unset($this->value[$key]);
                }
            }

            if ( isset( $this->value['value'] ) && is_array( $this->value['value'] ) ) {
                $this->value['value'] = array_values(array_filter($this->value['value']));

                // D($this->value);

                foreach ( $this->value['value'] as $k => $value ) {
                    if ( $value != '' ) {
                        echo '<li><input type="checkbox" name="' . $this->field['name'] . $this->field['name_suffix'] . '[check][' . $k . ']' . '" ' . checked( (isset($this->value['check'][$k]) && !empty($this->value['check'][$k]) ? "on" : ""), "on", false ) . '> <input type="text" id="' . $this->field['id'] . '-' . $k . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[value][' . $k . ']' . '" value="' . esc_attr( $value ) . '" class="regular-text ' . $this->field['class'] . '" /> <a href="javascript:void(0);" class="deletion redux-multi-text-remove">' . __( 'Remove', 'redux-framework' ) . '</a></li>';
                    }
                }
            } elseif ( $this->show_empty == true ) {
                echo '<li><input type="checkbox" name="' . $this->field['name'] . $this->field['name_suffix'] . '[check][]' . '"> <input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[value][]' . '" value="" class="regular-text ' . $this->field['class'] . '" /> <a href="javascript:void(0);" class="deletion redux-multi-text-remove">' . __( 'Remove', 'redux-framework' ) . '</a></li>';
            }

            echo '<li style="display:none;"><input type="checkbox" name="' . $this->field['name'] . $this->field['name_suffix'] . '[check][]' . '"> <input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '[value][]' . '" value="" class="regular-text" /> <a href="javascript:void(0);" class="deletion redux-multi-text-remove">' . __( 'Remove', 'redux-framework' ) . '</a></li>';

            echo '</ul>';
            $this->field['add_number'] = ( isset( $this->field['add_number'] ) && is_numeric( $this->field['add_number'] ) ) ? $this->field['add_number'] : 1;
            echo '<a href="javascript:void(0);" class="button button-primary redux-multi-text-add" data-add_number="' . $this->field['add_number'] . '" data-id="' . $this->field['id'] . '-ul" data-name="' . $this->field['name'] . $this->field['name_suffix'] . '[value][]' . '">' . $this->add_text . '</a><br/>';
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

            $extension = ReduxFramework_extension_multi_text_auto::getInstance();

            wp_enqueue_script(
                'redux-field-multi-text-auto-js',
                // ReduxFramework::$_url . 'inc/fields/multi_text/field_multi_text' . Redux_Functions::isMin() . '.js',
                LISTING_DIR . "/ReduxFramework/extensions/multi_text_auto/field_multi_text.js",
                array( 'jquery', 'redux-js' ),
                time(),
                true
            );
        
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
