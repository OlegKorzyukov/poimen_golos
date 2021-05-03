<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Khor_Golos
 *
 * @wordpress-plugin
 * Plugin Name:       Khor Golos
 * Description: Візуалізація поіменного голосування сесій Херсонської Обласної Ради
 * Version:           1.0.0
 * Author: Oleg Korzyukov (Комунальне підприємство "Центр електронного самоврядування")
 */

if (!defined('WPINC')) {
	die;
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname(__FILE__) . '/vendor/autoload.php';
}

use Inc\Khor_Golos_Activator;
use Inc\Khor_Golos_Deactivator;


function activate_khor_golos()
{
	Khor_Golos_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_khor_golos');

function deactivate_khor_golos()
{
	Khor_Golos_Deactivator::deactivate();
}
//register_deactivation_hook(__FILE__, 'deactivate_khor_golos');


if (class_exists('Inc\\Khor_Golos_Init')) {
	Inc\Khor_Golos_Init::register_services();
}
