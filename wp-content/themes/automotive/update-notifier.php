<?php
/**
 * Provides a notification everytime the theme is updated
 * Original code courtesy of João Araújo of Unisphere Design - http://themeforest.net/user/unisphere
 */

function update_notifier_menu() {  
	$xml = get_latest_theme_version(86400); // This tells the function to cache the remote call for 86400 seconds (24 hours)
	if(is_child_theme()){
		global $new_theme_dir_name;

		$theme_data = wp_get_theme( (isset($new_theme_dir_name) && !empty($new_theme_dir_name) ? $new_theme_dir_name : 'automotive') );           // Get theme data from style.css (current version is what we want)
	} else {
		$theme_data = wp_get_theme();           // Get theme data from style.css (current version is what we want)
	}

	if(isset($_GET['dan'])){
		var_dump($theme_data);
		delete_option( $db_cache_field );
		delete_option( $db_cache_field_last_updated );	
	}
	
	if(isset($xml->latest) && isset($theme_data['Version']) && version_compare($theme_data['Version'], $xml->latest) == -1) {
		
		add_theme_page( 'Automotive Theme Updates', 'Automotive <span class="update-plugins count-1"><span class="update-count">New Updates</span></span>', 'administrator', 'automotive-updates', 'update_notifier' );
		add_action('admin_bar_menu', 'add_items', 10);

		function add_items($admin_bar) {
			$admin_bar->add_menu( array(
				'id'     => 'automotive-update',
				'title'  => 'Automotive <span style="background-color: #eee; color: #333; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; padding: 2px 5px; font-size: 10px; text-shadow: none; font-weight: bold;">New Update</span>',
				'href'   => get_admin_url() . "themes.php?page=automotive-updates"
			) );
		}
	}
}  

add_action('admin_menu', 'update_notifier_menu');

function update_notifier() { 
	$xml = get_latest_theme_version(86400); // This tells the function to cache the remote call for 86400 seconds (24 hours)
	if(is_child_theme()){
		global $new_theme_dir_name;

		$theme_data = wp_get_theme( (isset($new_theme_dir_name) && !empty($new_theme_dir_name) ? $new_theme_dir_name : 'automotive') );          // Get theme data from style.css (current version is what we want)
	} else {
		$theme_data = wp_get_theme();           // Get theme data from style.css (current version is what we want)
	}

	global $awp_options;
	?>
	
	<style>
		.update-nag {display: none;}
		#instructions {max-width: 800px;}
		h3.title {margin: 30px 0 0 0; padding: 30px 0 0 0; border-top: 1px solid #ddd;}
		ul { padding-left: 40px; list-style: disc; }
	</style>

	<div class="wrap">
	
		<div id="icon-tools" class="icon32"></div>
		<h2><?php _e("Automotive Theme Updates", "automotive"); ?></h2>
  	    <div id="message" class="updated below-h2"><p><strong>There is a new version of the Automotive theme available.</strong> You have version <?php echo $theme_data['Version']; ?> installed. Update to version <?php echo $xml->latest; ?>.</p></div>
        
        <img style="float: left; margin: 0 20px 20px 0; border: 1px solid #ddd; max-width: 500px;" src="<?php echo get_template_directory_uri() . '/screenshot.png'; ?>" />
        
        <div id="instructions" style="max-width: 800px;">
            <h3><?php _e("Update Download and Instructions", "automotive"); ?></h3>
            <p><strong>Please note:</strong> Make a <strong>backup</strong> of the Theme inside your WordPress installation folder <strong>/wp-content/themes/<?php echo strtolower($theme_data['Name']); ?>/</strong> and your <b>database</b></p>
            <p>To update the Theme, enter your username and API key in the theme options panel. Then click update when you have entered the correct information.</p>
            <p>If you didn't make any changes to the theme files, you are free to overwrite them with the new ones without the risk of losing theme settings, pages, posts, etc, and backwards compatibility is guaranteed.</p>
            <p><b>Please remember:</b> We always recommend using a child theme rather then changing core theme files </p>
            
            <?php			
			if(isset($awp_options['themeforest_name']) && !empty($awp_options['themeforest_name']) && isset($awp_options['themeforest_api']) && !empty($awp_options['themeforest_api'])){ ?>
            
            <?php /*<form name="update" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=automotive-updates" method="POST">
            	<input type='submit' name='update_theme' value="Update <?php echo $theme_data['Name']; ?>" class='button-primary' />
            </form> */ 
			$nonce = wp_create_nonce( 'upgrade-theme_automotive' );
			?>
            
            <a href='<?php echo admin_url("update.php?action=upgrade-theme&theme=automotive&_wpnonce=" . $nonce); ?>'><button class='btn button-primary'><?php _e("Update Automotive", "automotive"); ?></button></a>
            
            <?php } ?>
        </div>
        
            <div class="clear"></div>
	    
	    <h3 class="title">Changelog</h3>
	    <?php echo $xml->changelog; ?>

	</div>
    
<?php } 

// This function retrieves a remote xml file on my server to see if there's a new update 
// For performance reasons this function caches the xml content in the database for XX seconds ($interval variable)
function get_latest_theme_version($interval) {
	// remote xml file location
	$notifier_file_url = 'http://support.themesuite.com/version/automotive-wp/notifier.xml';
	
	$db_cache_field = 'contempo-notifier-cache';
	$db_cache_field_last_updated = 'contempo-notifier-last-updated';
	$last = get_option( $db_cache_field_last_updated );
	$now = time();
	// check the cache
	if ( !$last || (( $now - $last ) > $interval) ) {
		// cache doesn't exist, or is old, so refresh it
		if( function_exists('curl_init') ) { // if cURL is available, use it...
			$ch = curl_init($notifier_file_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$cache = curl_exec($ch);
			curl_close($ch);
		} else {
			$cache = wp_remote_get($notifier_file_url); // ...if not, use the common wp_remote_get()
		}
		
		if ($cache) {			
			// we got good results
			update_option( $db_cache_field, $cache );
			update_option( $db_cache_field_last_updated, time() );			
		}
		// read from the cache file
		$notifier_data = get_option( $db_cache_field );
	}
	else {
		// cache file is fresh enough, so read from it
		$notifier_data = get_option( $db_cache_field );
	}
	
	$xml = @simplexml_load_string($notifier_data); 
	
	return $xml;
}

?>