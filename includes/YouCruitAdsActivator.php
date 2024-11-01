<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    YouCruitAds
 * @subpackage YouCruitAds/includes
 * @author     Patrick Gilmore <p@youcruit.com>
 */
class YouCruitAdsActivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        YouCruitRestClient::installNorification();
	}
}
