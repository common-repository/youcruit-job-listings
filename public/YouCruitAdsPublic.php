<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    YouCruitAds
 * @subpackage YouCruitAds/public
 * @author     Patrick Gilmore <p@youcruit.com>
 */
class YouCruitAdsPublic
{
    const YOUCRUIT_ETAG_CACHE_KEY = "youcruit-etag-option";
    const YOUCRUIT_ADS_CACHE_KEY = "youcruit-etag-cache-option";

    private $accentColor;
    private $applyHereText;
    private $applyBeforeText;
    private $nothingFoundText;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $pluginName The ID of this plugin.
     */
    private $pluginName;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $pluginName The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($pluginName, $version)
    {
        $this->pluginName = $pluginName;
        $this->version = $version;
        $this->register_shortcode();
    }

    private function register_shortcode()
    {
        add_shortcode('youcruit_ads', array("YouCruitAdsPublic", "fetch_ads"));
    }

    public static function fetch_ads()
    {
        $apiKey = get_option(YouCruitAdsAdmin::YOUCRUIT_API_KEY);
        $currentETag = get_option(YouCruitAdsPublic::YOUCRUIT_ETAG_CACHE_KEY, null);
        $response = YouCruitRestClient::fetchAds($currentETag, $apiKey);
        if ($response === true) {
            $jsonAds = get_option(YouCruitAdsPublic::YOUCRUIT_ADS_CACHE_KEY);
        }  else if($response['data'] != null && !empty($response['data'])) {
            $jsonAds = $response['data'];
            $eTag = $response['etag'];
            update_option(YouCruitAdsPublic::YOUCRUIT_ADS_CACHE_KEY, $jsonAds);
            update_option(YouCruitAdsPublic::YOUCRUIT_ETAG_CACHE_KEY, $eTag);
        } else {
            return self::render_ads("[]");
        }
        return self::render_ads($jsonAds);
    }

    public static function render_ads($jsonAds) {
        $ads = json_decode($jsonAds);
        $applyBeforeText = get_option(YouCruitAdsAdmin::YOUCRUIT_APPLY_BEFORE, 'Apply before');
        $applyHereText = get_option(YouCruitAdsAdmin::YOUCRUIT_APPLY_HERE, 'Apply here');
        $nothingFoundText = get_option(YouCruitAdsAdmin::YOUCRUIT_APPLY_NOTHING, 'We don\'t have any job openings available at the moment.');
        $result = self::get_style();
        $result .= '<div class="youcruit-listing">';
        if(sizeof($ads) > 0) {
            foreach ($ads as &$job) {
                $result .= '<div class="youcruit-listing-item">';
                $result .= ' <div class="youcruit-listing-item-slide-toggle">';
                $result .= vsprintf('<div class="youcruit-listing-item-title">%s</div>', $job->title);
                $result .= vsprintf('<div class="youcruit-listing-item-location">%s</div>', $job->city);
                $result .= vsprintf('<div class="youcruit-listing-item-application-date"><span class="">%s</span> %s</div>', array($applyBeforeText, $job->lastApplicationDate));
                $result .= '</div>';
                $result .= '<div class="youcruit-listing-item-slide-content">';
                $description = "";
                foreach ($job->texts as &$body) {
                    if ($body->type == "BODY_1") {
                        $description = $body->body;
                    }
                }
                $result .= vsprintf('<div class="youcruit-listing-item-body">%s</div>', $description);
                $applyUrl = YouCruitAds::get_base_ad_url() . $job->prettyUrl;
                $result .= vsprintf('<a class="youcruit-listing-apply-button" href="%s" target="_blank">%s</a>', array($applyUrl, $applyHereText));
                $result .= '</div>';
                $result .= '</div>';
            }
        } else {
            $result .= "<div class=\“youcruit-listing-item-no-positions\”>{$nothingFoundText}</div>";
        }
        $result .= '</div>';
        return $result;
    }

    public static function get_style() {
        $accentColor = get_option(YouCruitAdsAdmin::YOUCRUIT_COLOR, '#fe8341');
        $result = '<style>';
        $result .= vsprintf('.youcruit-listing-item-slide-toggle:hover .youcruit-listing-item-title { color: %s!important}',$accentColor);
        $result .= vsprintf('.youcruit-listing-item-title:after{ border-left-color: %s!important }', $accentColor);
        $result .= vsprintf('a.youcruit-listing-apply-button, a.youcruit-listing-apply-button:active, a.youcruit-listing-apply-button:focus { background-color: %s!important; }', $accentColor);
        $result .= vsprintf('.youcruit-listing-item.youcruit-listing-item-active .youcruit-listing-item-slide-toggle, .youcruit-listing-item.youcruit-listing-item-active .youcruit-listing-item-slide-toggle:hover { border-left-color: %s!important }', $accentColor);
        $result .= vsprintf('.youcruit-listing-item-slide-content { border-left-color: %s!important}', $accentColor);
        $result .= '</style>';
        return $result;
    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->pluginName, plugin_dir_url(__FILE__) . 'css/youCruitPositions-public.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->pluginName, plugin_dir_url(__FILE__) . 'js/youCruitPositions-public.min.js', array('jquery'), $this->version, false);
    }
}
