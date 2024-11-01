<?php
class YouCruitRestClient {

    static function fetchAds($etag, $apiKey) {
        if(self::isCurlInstalled()) {
            try {
                $headers = array(
                    "x-youcruit-api-access-token: {$apiKey}",
                );
                if ($etag != null) {
                    array_push($headers, "If-None-Match: W/\"{$etag}\"");
                }
                $endPoint = YouCruitAds::get_base_url() . "/public/api/company/positionopening/OPEN";
                $ch = curl_init();
                $siteUrl = get_site_url();
                $siteEmail = get_bloginfo('admin_email');
                $wpVersion = get_bloginfo('version');
                curl_setopt($ch, CURLOPT_URL, $endPoint);

                curl_setopt($ch, CURLOPT_USERAGENT, "YouCruit/WP plugin 1.2.20 (site: {$siteUrl}, email: {$siteEmail}, wp_version: {$wpVersion})");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                $data = curl_exec($ch);
                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($responseCode == 304) {
                    return true;
                } else if ($responseCode >= 200 && $responseCode < 300) {
                    list($headers, $response) = explode("\r\n\r\n", $data, 2);
                    $headers = explode("\n", $headers);
                    return array('etag' => self::parseEtagHeader($headers), 'data' => $response);
                }
                return false;
            } catch (Exception $e) {
                echo "Failed to fetch data from server : " . $e->getMessage();
            }
        }
    }

    static function installNorification() {
        if(self::isCurlInstalled()) {
            $endPoint = YouCruitAds::get_base_url() . "/public/api/company/wp/install";
            $ch = curl_init();
            $siteUrl = get_site_url();
            $siteEmail = get_bloginfo('admin_email');
            $wpVersion = get_bloginfo('version');
            try {
                $headers = array(
                    "siteUrl: {$siteUrl}",
                    "siteEmail: {$siteEmail}",
                    "wpVersion: {$wpVersion}",
                    "pluginVersion: 1.2.20 "
                );
                curl_setopt($ch, CURLOPT_URL, $endPoint);
                curl_setopt($ch, CURLOPT_USERAGENT, "YouCruit/WP plugin 1.2.20 (site: {$siteUrl}, email: {$siteEmail}, wp_version: {$wpVersion})");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                curl_exec($ch);
                curl_close($ch);
            } catch (Exception $e) {
                echo "Failed to activate plugin with exception : " . $e->getMessage();
            }
        }
    }

    static function unInstallNorification() {
        if(self::isCurlInstalled()) {
            $endPoint = YouCruitAds::get_base_url() . "/public/api/company/wp/uninstall";
            $ch = curl_init();
            $siteUrl = get_site_url();
            $siteEmail = get_bloginfo('admin_email');
            $wpVersion = get_bloginfo('version');
            try {
                $headers = array(
                    "siteUrl: {$siteUrl}",
                    "siteEmail: {$siteEmail}",
                    "wpVersion: {$wpVersion}",
                    "pluginVersion: 1.2.20 "
                );
                curl_setopt($ch, CURLOPT_URL, $endPoint);
                curl_setopt($ch, CURLOPT_USERAGENT, "YouCruit/WP plugin 1.2.20 (site: {$siteUrl}, email: {$siteEmail}, wp_version: {$wpVersion})");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_exec($ch);
                curl_close($ch);
            } catch (Exception $e) {
                echo "Failed to activate plugin with exception : " . $e->getMessage();
            }
        }
    }

    private static function parseEtagHeader($headers) {
        foreach($headers as $header) {
            if (stripos($header, 'etag:') !== false) {
                $etag = substr($header, stripos($header, '/')+1);
                $pattern = "/([a-zA-Z0-9]+)/";
                preg_match($pattern, $etag, $matches);
                return $matches[0];
            }
        }
        return null;
    }

    public static function isCurlInstalled() {
        if  (in_array  ('curl', get_loaded_extensions())) {
            return true;
        }
        else {
            return false;
        }
    }
}