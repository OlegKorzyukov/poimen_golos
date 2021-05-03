<?php


namespace  Inc;


/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Khor_Golos
 * @subpackage Khor_Golos/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Khor_Golos
 * @subpackage Khor_Golos/includes
 * @author     Your Name <email@example.com>
 */
class Khor_Golos_Activator
{
	const PUBLIC_TEMPLATES_DIR = WP_PLUGIN_DIR  . '/khor-golos/Includes/Front/partials/';

	public static function activate()
	{
		self::okCreateUploadFileTable();
		self::okCreateDbTableParseFile();
		self::okCreateAllDeputTable();
		self::okCreateOldNameDeputGolosTable();
		self::okCreateDeputGolosTable();
		self::okCreateAllGolosPage();
	}

	//Create Page ALL golos
	private static function okCreateAllGolosPage()
	{
		$new_page_title = 'Поіменне голосування';
		$new_page_content = '[khor_all_golos_page]';
		$new_page_slug = 'all-poimen-golos';
		$new_page_template = self::PUBLIC_TEMPLATES_DIR . 'khor-golos-all-page-display.php'; //templates page

		$page_check = get_page_by_path($new_page_slug);
		$new_page = array(
			'comment_status' => 'closed',
			'post_type' => 'page',
			'post_name'      => $new_page_slug,
			'post_title' => $new_page_title,
			'post_content' => $new_page_content,
			'post_status' => 'publish',
			'post_author' => 1,
			'meta_input'     => ['ok_session_poimen_golos_plugin' => 'ok_session_poimen_golos_plugin'],
		);

		if (!isset($page_check->ID)) {
			$new_page_id = wp_insert_post(wp_slash($new_page));
			/*if (!empty($new_page_template)) {
				add_filter('template_include', $new_page_template);
			}*/
		}
	}


	//Create table main upload file in DB
	private static function okCreateUploadFileTable()
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';

		if ($wpdb->get_var("show tables like '" . $table_name . "'") != $table_name) {

			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE {$table_name} (
		    id             bigint 				unsigned NOT NULL auto_increment,
		    ok_filename    varchar(255)     UNIQUE NOT NULL default '0',
		    ok_num_session smallint(255) 	unsigned NOT NULL default '0',
			 ok_num_convocation smallint(255) 	unsigned NOT NULL default '0',
		    ok_filesize    int(255) 			unsigned NOT NULL default '0',
			 ok_video_url	varchar(2083)		NULL,
			 ok_solution_link varchar(2083)	NULL,
		    ok_date_upload varchar(20)   	NOT NULL default '0',
		    ok_date_change varchar(20) 		NULL,
		    ok_date_upload_utc int(255) 		unsigned NOT NULL default '0',
		    ok_date_change_utc int(255)  	unsigned NULL,
		    ok_file_create varchar(20) 		NOT NULL default '0',
		    PRIMARY KEY  (id)
		    ) {$charset_collate};";

			dbDelta($sql);
		}
	}

	// Create table parse file in DB
	private static function okCreateDbTableParseFile()
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';

		if ($wpdb->get_var("show tables like '" . $table_name . "'") != $table_name) {

			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE {$table_name} (
		    id           bigint 			 	unsigned NOT NULL auto_increment,
		    ok_num_session smallint(255)  	unsigned NOT NULL default '0',
			 ok_num_convocation smallint(255)  	unsigned NOT NULL default '0',
		    ok_pdnpp   varchar(3) 			 	NOT NULL default '0',
		    ok_pd_name text COLLATE utf8mb4_bin NOT NULL default  '',
		    ok_gl_number  smallint(255) 	 	unsigned NOT NULL default '0',
		    ok_gl_type varchar(30)   		 	NOT NULL default '0',
		    ok_gl_result_type varchar(255)  NOT NULL default '0',
		    ok_gl_text text 		 		 		NOT NULL default '',
		    ok_gl_time datetime 			   NOT NULL,
		    ok_yes_count tinyint(255)  		unsigned NOT NULL default '0',
		    ok_no_count tinyint(255)   		unsigned NOT NULL default '0',
		    ok_utr_count tinyint(255)  		unsigned NOT NULL default '0',
		    ok_ng_count tinyint(255)   		unsigned NOT NULL default '0',
		    ok_total_count tinyint(255) 		unsigned NOT NULL default '0',
		    ok_result varchar(30)  		 	NOT NULL default '0',
		    ok_link varchar(255)  		  		NULL default '',
		    ok_show boolean						NOT NULL default '1',
			 FULLTEXT (ok_pd_name),
		    PRIMARY KEY  (id)
		    ) {$charset_collate};";

			dbDelta($sql);
		}
	}

	// Create table deput golos in DB
	private static function okCreateDeputGolosTable()
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_deput';

		if ($wpdb->get_var("show tables like '" . $table_name . "'") != $table_name) {

			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
			$foreign_table_name_deput = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE {$table_name} (
		    id           bigint 				unsigned NOT NULL auto_increment,
		    ok_num_session smallint(255) 	unsigned NOT NULL default '0',
			 ok_num_convocation smallint(255) 	unsigned NOT NULL default '0',
		    ok_num_rishennya  varchar(3) 	NOT NULL default '0',
		    ok_dp_id tinyint(255)  			unsigned NOT NULL default '0',
		    ok_dp_golos varchar(20)  			NOT NULL default '0',
		    ok_gl_number  smallint(255) 		unsigned NOT NULL default '0',
		    PRIMARY KEY  (id),
		    FOREIGN KEY (ok_dp_id) REFERENCES  {$foreign_table_name_deput}(id)
		    ) {$charset_collate};";

			dbDelta($sql);
		}
	}

	private static function okCreateAllDeputTable()
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';

		if ($wpdb->get_var("show tables like '" . $table_name . "'") != $table_name) {

			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE {$table_name} (
		    id         tinyint(255) 			unsigned NOT NULL auto_increment,
		    ok_dp_name varchar(255)  			UNIQUE NOT NULL default '',
			 ok_dp_convocation varchar(255) 	NOT NULL default '0',
			 ok_dp_change_name boolean			NOT NULL default 0,
			 ok_dp_fraction varchar (255) 	NOT NULL default '',
			 ok_dp_birthday date 			   NOT NULL,
			 ok_dp_position varchar (255) 	NOT NULL default '',
			 ok_dp_commission varchar (255) 	NOT NULL default '',
			 ok_dp_info text (255)				NOT NULL default '',
			 ok_dp_photo varchar (255) 		UNIQUE NOT NULL default '',
			 ok_dp_actual boolean				NOT NULL default 1,	
		    PRIMARY KEY  (id)
		    ) {$charset_collate};";

			dbDelta($sql);
		}
	}

	private static function okCreateOldNameDeputGolosTable()
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_old_name_deput';

		if ($wpdb->get_var("show tables like '" . $table_name . "'") != $table_name) {

			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
			$foreign_table_name_deput = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE {$table_name} (
		    id           int(255) 		unsigned NOT NULL auto_increment,
		    ok_dp_old_name text (255)			NOT NULL,
		    ok_dp_id tinyint(255)  			unsigned NOT NULL default '0',
		    PRIMARY KEY  (id),
			 FOREIGN KEY (ok_dp_id) REFERENCES  {$foreign_table_name_deput}(id)
		    ) {$charset_collate};";

			dbDelta($sql);
		}
	}
}// class Khor_Golos_Activator