<?php
if (!class_exists("Pixelentity_Themes_Updater")) {
	class Pixelentity_Themes_Updater {
			
		protected $username;
		protected $apikey;

		public function __construct($username = null,$apikey = null,$authors = null) {

			$this->username = $username;
			$this->apikey = $apikey;
			$this->authors = $authors;

		}

		public function check($updates) {

			$this->username = apply_filters("pixelentity_themes_update_username",$this->username);
			$this->apikey = apply_filters("pixelentity_themes_updater_apikey",$this->apikey);
			$this->authors = apply_filters("pixelentity_themes_updater_authors",$this->authors);

			if (isset($this->authors) && !is_array($this->authors)) {
				$this->authors = array($this->authors);
			}

			if (!isset($this->username) || !isset($this->apikey) || !isset($updates->checked)) return $updates;

			if (!class_exists("Envato_Protected_API")) {
				require_once("class-envato-protected-api.php");
			}

			
			$api = new Envato_Protected_API($this->username,$this->apikey);
			add_filter("http_request_args",array(&$this,"http_timeout"),10,1);
			$purchased = $api->wp_list_themes(true);
			
			if(is_array($purchased) && !in_array("Automotive", $purchased)){
				global $error_updating;
				$error_updating = 'error';
			}

			$installed = wp_get_themes();
			$filtered  = array();
			
			if(!empty($installed)){
				foreach ($installed as $theme) {
					if ($this->authors && !in_array($theme->{'Author Name'},$this->authors)) continue;
					$filtered[$theme->Name] = $theme;
				}
			}

			if(!empty($purchased)){
				foreach ($purchased as $theme) {
					if (isset($theme->theme_name) && isset($filtered[$theme->theme_name])) {
						// gotcha, compare version now
						$current = $filtered[$theme->theme_name];
						if (version_compare($current->Version, $theme->version, '<')) {
							// bingo, inject the update
							if ($url = $api->wp_download($theme->item_id)) {
								$update = array(
												"url" => "http://support.themesuite.com/version/changelog.php?t=automotive-wp",
												"new_version" => $theme->version,
												"package" => $url
												);

								$updates->response[$current->Stylesheet] = $update;

							}
						}
					}
				}
			}

			remove_filter("http_request_args",array(&$this,"http_timeout"));

			return $updates;
		}

		public function http_timeout($req) {
			// increase timeout for api request
			$req["timeout"] = 300;
			return $req;
		}

	}
}