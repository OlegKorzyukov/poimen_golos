<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Khor_Golos
 * @subpackage Khor_Golos/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Khor_Golos
 * @subpackage Khor_Golos/public
 * @author     Oleg Korzyukov <o.korzyukov@gmail.com>
 */

namespace Inc\Front;


class Khor_Golos_Public
{

	public function __construct()
	{
		if (isset($_POST["sendType"]) && $_POST["sendType"] == 'khor_golos_public_meta_table_ajax_click_action') {
			add_action('wp_ajax_nopriv_khor_golos_public_meta_table_ajax_click_action', Khor_Golos_Shortcode::okDeputGolos());
		}
		if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_subtable_repeat_question') {
			add_action('wp_ajax_nopriv_khor_golos_subtable_repeat_question', Khor_Golos_Shortcode::okGiveRepeatQuestion());
		}
	}

	public function register()
	{
		add_shortcode('khor_golos_single', ['Inc\Front\Khor_Golos_Shortcode', 'okShowShortcodeSinglePage']);
		add_shortcode('khor_all_golos_page', ['Inc\Front\Khor_Golos_Shortcode', 'okShowAllSessionGolosPage']);
		add_shortcode('khor_golos_convocation', ['Inc\Front\Khor_Golos_Shortcode', 'okShowConvocationSessionPage']);
	}
} // class Khor_Golos_Public
