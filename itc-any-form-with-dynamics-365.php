<?php
/*
 * Plugin Name:       ITC - Dynamics 365 Integration
 * Plugin URI:        https://it-consultis.com/
 * Description:       This plugin will automatically insert a record to Dynamics 365 when there is a user who submits a form on this website, currently only support Gravity Form.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Michael T
 * Author URI:        https://it-consultis.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://it-consultis.com/
 * Text Domain:       itc-dynamics-365
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define('ITC_D365_DIR',plugin_dir_path( __FILE__ ));
define('ITC_D365_URL',plugin_dir_url( __FILE__ ));

require_once ITC_D365_DIR . 'vendor/autoload.php';

function itc_dynamics_365_activate() {
    // Add activation logic for your plugin here
}
register_activation_hook(__FILE__, 'itc_dynamics_365_activate');

// Remove menu page when plugin is deactivated
register_deactivation_hook(__FILE__, 'itc_dynamics_365_deactivate');

function itc_dynamics_365_deactivate() {

}

function itc_render_d365_template_part($template_file, $template_data = []){
    $template_file = ITC_D365_DIR . $template_file.'.php';
	if (file_exists($template_file)) {
        ob_start();
        extract($template_data); // Extract the variables from the data array
    	require_once $template_file;
        echo ob_get_clean();
    } else {
        throw new Exception("Template file not found: $template_file");
    }
}


require_once ITC_D365_DIR . 'admin/form-settings.php';
require_once ITC_D365_DIR . 'hooks/admin.php';

$dynamics_token_info = get_network_option(null, 'itc_dynamics_365_token_info');
if( !empty($dynamics_token_info)) {
    require_once ITC_D365_DIR . 'hooks/gforms.php';
}