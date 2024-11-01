<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    YouCruitAds
 * @subpackage YouCruitAds/admin
 * @author     Patrick Gilmore <p@youcruit.com>
 */
class YouCruitAdsAdmin
{
    const YOUCRUIT_ADS_SETTING_ADMIN = 'youcruit-ads-setting-admin';
    const YOUCRUIT_API_KEY = 'youcruit_api_key';
    const YOUCRUIT_API_ENDPOINT = 'youcruit_api_endpoint';
    const YOUCRUIT_APPLY_BEFORE = 'youcruit_text_apply_before';
    const YOUCRUIT_APPLY_HERE = 'youcruit_text_apply_here';
    const YOUCRUIT_APPLY_NOTHING = 'youcruit_text_apply_no_candidates';
    const YOUCRUIT_COLOR= 'youcruit_color';

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
     * @param      string $pluginName The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($pluginName, $version)
    {
        $this->pluginName = $pluginName;
        $this->version = $version;
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));

    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'YouCruit',
            'manage_options',
            self::YOUCRUIT_ADS_SETTING_ADMIN,
            array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        if (YouCruitRestClient::isCurlInstalled()) {
            ?>
            <div class="wrap youcruit-admin-wrapper">
                <form method="post" action="options.php">
                    <?php
                    settings_fields(self::YOUCRUIT_ADS_SETTING_ADMIN);
                    do_settings_sections(self::YOUCRUIT_ADS_SETTING_ADMIN);
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        } else {
            ?>
            <div class="wrap youcruit-admin-wrapper">
                You need curl activated in order to use this plugin.
                <a href="http://php.net/manual/en/book.curl.php">http://php.net/manual/en/book.curl.php</a>
            </div>
            <?php
        }
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(self::YOUCRUIT_ADS_SETTING_ADMIN, self::YOUCRUIT_API_KEY);
        register_setting(self::YOUCRUIT_ADS_SETTING_ADMIN, self::YOUCRUIT_API_ENDPOINT);
        register_setting(self::YOUCRUIT_ADS_SETTING_ADMIN, self::YOUCRUIT_APPLY_HERE);
        register_setting(self::YOUCRUIT_ADS_SETTING_ADMIN, self::YOUCRUIT_APPLY_BEFORE);
        register_setting(self::YOUCRUIT_ADS_SETTING_ADMIN, self::YOUCRUIT_APPLY_NOTHING);
        register_setting(self::YOUCRUIT_ADS_SETTING_ADMIN, self::YOUCRUIT_COLOR);

        add_settings_section(
            'youcruit-setting-section',
            __('YouCruit Ads Settings', 'youcruit-job-listings'),
            array($this, 'print_section_info'),
            self::YOUCRUIT_ADS_SETTING_ADMIN
        );

        add_settings_field(
            self::YOUCRUIT_API_KEY,
            __('API Key', 'youcruit-job-listings'),
            array($this, 'api_key_callback'),
            self::YOUCRUIT_ADS_SETTING_ADMIN,
            'youcruit-setting-section'
        );

        add_settings_field(
            self::YOUCRUIT_APPLY_BEFORE,
            __('Apply before', 'youcruit-job-listings'),
            array($this, 'apply_text_before'),
            self::YOUCRUIT_ADS_SETTING_ADMIN,
            'youcruit-setting-section'
        );

        add_settings_field(
            self::YOUCRUIT_APPLY_HERE,
            __('Apply here', 'youcruit-job-listings'),
            array($this, 'apply_text_here'),
            self::YOUCRUIT_ADS_SETTING_ADMIN,
            'youcruit-setting-section'
        );

        add_settings_field(
            self::YOUCRUIT_APPLY_NOTHING,
            __('No active job postings', 'youcruit-job-listings'),
            array($this, 'apply_text_nothing'),
            self::YOUCRUIT_ADS_SETTING_ADMIN,
            'youcruit-setting-section'
        );

        add_settings_field(
            self::YOUCRUIT_COLOR,
            __('Accent color', 'youcruit-job-listings'),
            array($this, 'accent_color_callback'),
            self::YOUCRUIT_ADS_SETTING_ADMIN,
            'youcruit-setting-section'
        );
    }

    public function print_section_info()
    {
        _e('To use the plugin, simply insert the shorthand [youcruit_ads] into any page or post.', 'youcruit-job-listings');
    }

    public function api_endpoint_callback()
    {
        printf(
            '<input type="text" id="youcruit_api_endpoint" name="youcruit_api_endpoint" value="%s" />',
            get_option(self::YOUCRUIT_API_ENDPOINT) != null ? esc_attr(get_option(self::YOUCRUIT_API_ENDPOINT)) : 'https://api.youcruit.com/'
        );
    }

    public function api_key_callback()
    {
        printf(
            '<input type="text" id="youcruit_api_key" name="youcruit_api_key" class="regular-text" value="%s" />',
            get_option(self::YOUCRUIT_API_KEY) != null ? esc_attr(get_option(self::YOUCRUIT_API_KEY)) : ''
        );
        $label = __("You need the API key to connect this plugin with your YouCruit account. Log in to YouCruit, copy the API key and paste it into this field.", 'youcruit-job-listings');
        $linkCaption = __("Get YouCruit API key", 'youcruit-job-listings');
        printf("<p class=\"description\">{$label}</p><p class=\"description\"><a href='https://www.youcruit.com/#!advanced' target=\"_blank\">{$linkCaption}</a></p>");
    }

    public function apply_text_before()
    {
        printf(
            '<input type="text" id="youcruit_text_apply_before" name="youcruit_text_apply_before" class="regular-text" value="%s" />',
            get_option(self::YOUCRUIT_APPLY_BEFORE) != null ? esc_attr(get_option(self::YOUCRUIT_APPLY_BEFORE)) : 'Apply before'
        );
        $label = __("Apply before translation", 'youcruit-job-listings');
        printf("<p class=\"description\">{$label}</p>");
    }

    public function apply_text_here()
    {
        printf(
            '<input type="text" id="youcruit_text_apply_here" name="youcruit_text_apply_here" class="regular-text" value="%s" />',
            get_option(self::YOUCRUIT_APPLY_HERE) != null ? esc_attr(get_option(self::YOUCRUIT_APPLY_HERE)) : 'Apply here'
        );
        $label = __("Apply here translation", 'youcruit-job-listings');
        printf("<p class=\"description\">{$label}</p>");
    }

    public function apply_text_nothing()
    {
        printf(
            '<input type="text" id="youcruit_text_apply_no_candidates" name="youcruit_text_apply_no_candidates" class="regular-text" value="%s" />',
            get_option(self::YOUCRUIT_APPLY_NOTHING) != null ? esc_attr(get_option(self::YOUCRUIT_APPLY_NOTHING)) : 'We don\'t have any job openings available at the moment.'
        );
        $label = __("No positions translation", 'youcruit-job-listings');
        printf("<p class=\"description\">{$label}</p>");
    }

    public function accent_color_callback()
    {
        printf(
            '<input type="color" id="youcruit_color" name="youcruit_color" class="regular-text" value="%s" />',
            get_option(self::YOUCRUIT_COLOR) != null ? esc_attr(get_option(self::YOUCRUIT_COLOR)) : '#fe8341'
        );
        $label = __("Accent color", 'youcruit-job-listings');
        printf("<p class=\"description\">{$label}</p>");
    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->pluginName, plugin_dir_url(__FILE__) . 'css/youCruitPositions-admin.css', array(), $this->version, 'all');
    }
}
