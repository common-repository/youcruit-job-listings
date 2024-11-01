<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    YouCruitAds
 * @subpackage YouCruitAds/includes
 * @author     Patrick Gilmore <p@youcruit.com>
 */
class YouCruitAdsDeactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        YouCruitRestClient::unInstallNorification();
	}

}
