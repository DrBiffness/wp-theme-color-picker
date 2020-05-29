<?php
// $cachedOpts = [];
/**
 * @link https://gitlab.tgchosting.net/dev-team/css-project
 * @since 1.0.0
 * @package Is_Css
 *
 * @wordpress-plugin
 * Plugin Name: Feuchtfröhlich CSS Framework
 * Plugin URI: https://gitlab.tgchosting.net/dev-team
 * Description: A CSS framework plugin.
 * Version: 1.0.0
 * Author: Income Store
 * Author URI: https://gitlab.tgchosting.net/dev-team
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: is-css
 */

// Basic Functions
if (!defined('ABSPATH')) {
    die;
}

// Define constants
if (!defined('IS_CSS_PLUGIN_URL')) {
    define('IS_CSS_PLUGIN_URL', plugin_dir_url(__FILE__));
}

require_once 'is-functions.php';

class iscss
{
    public function activate()
    {
        try {
            setOptions();
        } catch (Exception $err) {
            echo $err;
        }
    }

    public function deactivate()
    {
        try {
            unsetOptions();
        } catch (Exception $err) {
            echo $err;
        }
    }

    public function uninstall()
    {
        try {
            unsetOptions();
        } catch (Exception $err) {
            echo $err;
        }
    }
}

if (class_exists('iscss')) {
    $css = new iscss();
}

// Activation
register_activation_hook(__FILE__, array($css, 'activate'));

// Deactivation
register_deactivation_hook(__FILE__, array($css, 'deactivate'));

function setOptions()
{
    $opts = getOptions();
    update_option('cpa_colors', $opts);
}

function unsetOptions()
{
    delete_option('cpa_colors');
}

function getOptions()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://nwk8s445if.execute-api.us-east-1.amazonaws.com/v1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "cache-control: no-cache",
            "x-api-key: VxSKCMh7o95qjpNqhVmZf1SiM1mGcj1T4OF4jqVY"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        return json_decode($response, true)['body'];
    }
}
