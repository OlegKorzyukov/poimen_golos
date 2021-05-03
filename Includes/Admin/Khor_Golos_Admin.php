<?php

namespace Inc\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Khor_Golos
 * @subpackage Khor_Golos/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Khor_Golos
 * @subpackage Khor_Golos/admin
 * @author     Your Name <email@example.com>
 */
class Khor_Golos_Admin
{

    const TARGET_DIR = WP_PLUGIN_DIR  . '/khor-golos/files/';
    const ADMIN_TEMPLATES_DIR = WP_PLUGIN_DIR  . '/khor-golos/Includes/Admin/partials/';

    public function __construct()
    {
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_rm') {
            $khorGolosRemoveFile = new Khor_Golos_Remove_File();
            add_action('wp_ajax_khor_golos_rm', array($khorGolosRemoveFile, 'removeAll'));
        }
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_admin_subtable') {
            add_action('wp_ajax_khor_golos_admin_subtable',  Khor_Table_Constructor::okAdminSubtableGolos());
        }
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_admin_subtable_get_meta_info') {
            add_action('wp_ajax_khor_golos_admin_subtable_get_meta_info',  Khor_Table_Constructor::okBeforeTableMetaInfo());
        }
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_admin_subtable_save_row') {
            add_action('wp_ajax_khor_golos_admin_subtable_save_row',  Khor_Table_Constructor::okAdminSubtableSave());
        }
        if (isset($_POST["sendType"]) && $_POST["sendType"] == 'khor_golos_admin_subtable_ajax_click_action') {
            add_action('khor_golos_admin_subtable_ajax_click_action',  Khor_Table_Constructor::okAdminSubtableMetaClickShow());
        }
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_change_video_solutions') {
            add_action('wp_ajax_khor_golos_change_video_solutions',  Khor_Table_Constructor::okAdminSaveVideoSolutions());
        }
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_admin_change_select_upload_result_file') {
            add_action('wp_ajax_khor_golos_admin_change_select_upload_result_file',   Khor_Table_Constructor::okGetNumberSession());
        }
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_admin_deputy_save_row') {
            add_action('wp_ajax_khor_golos_admin_deputy_save_row',  Khor_Golos_Deputy::okSaveAfterChangeDeputInfo());
        }
        if (isset($_POST["action"]) && $_POST["action"] == 'khor_golos_admin_add_new_deput') {
            add_action('wp_ajax_khor_golos_admin_add_new_deput',   new Khor_Golos_Add_Deput($_POST['dataNewDeput']));
        }
    }

    public function register()
    {
        add_action('admin_menu', array($this, 'add_plugin_menu'));
    }

    //Add link on admin menu 
    public static function add_plugin_menu()
    {
        if (function_exists('add_menu_page')) {
            add_menu_page(
                'Поіменне голосування',
                'Поіменне голосування',
                'manage_options',
                'khor_golos_plugin',
                array($this, 'admin_page'),
                '',
                '8'
            );
            add_submenu_page(
                'khor_golos_plugin',
                'Поіменне голосування',
                'Поіменне голосування',
                'manage_options',
                'khor_golos_plugin'
            );

            add_submenu_page(
                'khor_golos_plugin',
                'Депутати',
                'Депутати',
                'manage_options',
                'khor_golos_plugin_deputy',
                array(Khor_Golos_Deputy::class, 'okDeputyPage')
            );
        }
    }

    public function admin_page()
    {
        require_once self::ADMIN_TEMPLATES_DIR . 'khor-golos-admin-display.php';
    }
} //class Khor_Golos_Admin