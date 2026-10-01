<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://panjerestudio.com
 * @since             1.0.0
 * @package           Anar_Woocomerce_Api
 *
 * @wordpress-plugin
 * Plugin Name:       انار 360
 * Plugin URI:        https://yourwebsite.com/your-anar-woocomerce-api
 * Plugin Signature:  AWCA
 * Description:       پلاگین سازگار با ووکامرس برای دریافت محصولات انار 360 در وبسایت کاربران
 * Version:           2.0.0
 * Author:            پنجره استودیو
 * Author URI:        https://panjerestudio.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       anar-woocomerce-api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}


// Define plugin constants.
define('ANAR_WC_API_PLUGIN_NAME', 'Anar WooCommerce API');
define('ANAR_WOOCOMERCE_API_VERSION', '2.0.0');
define('ANAR_WC_API_PLUGIN_FILE', __FILE__);
define('ANAR_WC_API_PLUGIN_DIR', plugin_dir_path(ANAR_WC_API_PLUGIN_FILE));
define('ANAR_WC_API_PLUGIN_URL', plugin_dir_url(ANAR_WC_API_PLUGIN_FILE));
define('ANAR_WC_API_ADMIN', ANAR_WC_API_PLUGIN_DIR . 'admin/');
define('ANAR_WC_API_FRONT', ANAR_WC_API_PLUGIN_DIR . 'public/');
define('ANAR_WC_API_TEXT_DOMAIN', 'anar-woocommerce-api');
define('ANAR_WC_API_LANG_DIR', trailingslashit(ANAR_WC_API_PLUGIN_DIR) . 'languages');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-anar-woocomerce-api-activator.php
 */
function activate_anar_woocomerce_api()
{
	require_once ANAR_WC_API_PLUGIN_DIR . 'includes/class-anar-woocomerce-api-activator.php';
	Anar_Woocomerce_Api_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-anar-woocomerce-api-deactivator.php
 */
function deactivate_anar_woocomerce_api()
{
	require_once ANAR_WC_API_PLUGIN_DIR . 'includes/class-anar-woocomerce-api-deactivator.php';
	Anar_Woocomerce_Api_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_anar_woocomerce_api');
register_deactivation_hook(__FILE__, 'deactivate_anar_woocomerce_api');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require ANAR_WC_API_PLUGIN_DIR . 'includes/class-anar-woocomerce-api.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_anar_woocomerce_api()
{
	$plugin = new Anar_Woocomerce_Api();
	$plugin->run();
}
run_anar_woocomerce_api();


/**
 * Includes Admin panel
 */
if (file_exists(ANAR_WC_API_PLUGIN_DIR . '/vendor/autoload.php')) {
	require_once ANAR_WC_API_PLUGIN_DIR . '/vendor/autoload.php';
}
$awca_sentry_dsn = defined('AWCA_SENTRY_DSN') ? AWCA_SENTRY_DSN : '';
if ($awca_sentry_dsn !== '' && function_exists('Sentry\\init')) {
	Sentry\init(['dsn' => $awca_sentry_dsn]);
}


include_once ANAR_WC_API_PLUGIN_DIR . '/functions.php';
