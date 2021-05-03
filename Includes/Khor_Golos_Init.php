<?php

/**
 * @package  AlecadddPlugin
 */

namespace Inc;

use Inc\Admin\Khor_Golos_Admin;
use Inc\Front\Khor_Golos_Public;

final class Khor_Golos_Init
{
	/**
	 * Store all the classes inside an array
	 * @return array Full list of classes
	 */
	public static function get_services()
	{
		return [
			Khor_Golos_Admin::class,
			Khor_Golos_Enqueue::class,
			Khor_Golos_Public::class,
			Khor_Golos_BaseController::class
		];
	}

	/**
	 * Loop through the classes, initialize them, 
	 * and call the register() method if it exists
	 * @return
	 */
	public static function register_services()
	{
		foreach (self::get_services() as $class) {
			$service = self::instantiate($class);
			if (method_exists($service, 'register')) {
				$service->register();
			}
		}
	}

	/**
	 * Initialize the class
	 * @param  class $class    class from the services array
	 * @return class instance  new instance of the class
	 */
	private static function instantiate($class)
	{
		$service = new $class();

		return $service;
	}
}