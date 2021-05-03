<?php

namespace Inc;

class Khor_Golos_Enqueue
{
	public function register()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueueAdminStyle'));
		add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScript'));

		add_action('wp_enqueue_scripts', array($this, 'enqueuePublicStyle'));
		add_action('wp_enqueue_scripts', array($this, 'enqueuePublicScript'));
	}


	function enqueueAdminStyle()
	{
		wp_enqueue_style('bootstrap.min.css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), '', 'all');
		wp_enqueue_style('bootstrap-table.min.css', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css', array(), '', 'all');
		wp_enqueue_style('khor_golos_font_awesome', 'https://use.fontawesome.com/releases/v5.13.1/css/all.css', array(), '', 'all');
		wp_enqueue_style('khor-golos-fancybox.css', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css', array(), '', 'all');
		wp_enqueue_style('khor-golos-admin.css', plugin_dir_url(__FILE__) . '../assets/css/khor-golos-admin.css', array(), '', 'all');
	}
	function enqueueAdminScript()
	{
		wp_enqueue_script('khor_golos_tableExport', 'https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js', '', '', true);
		wp_enqueue_script('khor_golos_popperjs', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js', '', '', true);
		wp_enqueue_script('bootstrap.min.js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', '', '', true);
		wp_enqueue_script('khor_golos_table', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js', '', '', true);
		wp_enqueue_script('khor_golos_table_export', 'https://unpkg.com/bootstrap-table@1.16.0/dist/extensions/export/bootstrap-table-export.min.js', '', '', true);
		wp_enqueue_script('khor_golos_table_locale', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table-locale-all.min.js', '', '', true);
		wp_enqueue_script('khor_golos_fancybox', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js', '', '', true);
		wp_enqueue_script('khor-golos-admin.js', plugin_dir_url(__FILE__) . '../assets/js/khor-golos-admin.js', array('jquery'), '', true);
		if (!did_action('wp_enqueue_media')) {
			wp_enqueue_media();
		}
	}



	function enqueuePublicStyle()
	{
		wp_enqueue_style('bootstrap.min.css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), '', 'all');
		wp_enqueue_style('bootstrap-table.min.css', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css', array(), '', 'all');
		wp_enqueue_style('khor_golos_font_awesome', 'https://use.fontawesome.com/releases/v5.13.1/css/all.css', array(), '', 'all');
		wp_enqueue_style('khor-golos-fancybox.css', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css', array(), '', 'all');
		wp_enqueue_style('khor-golos-public.css', plugin_dir_url(__FILE__) . '../assets/css/khor-golos-public.css', array(), '', 'all');
	}
	function enqueuePublicScript()
	{
		//wp_enqueue_script('khor_golos_chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js', '', '', true);
		wp_enqueue_script('khor_golos_popperjs', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js', '', '', true);
		wp_enqueue_script('bootstrap.min.js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', '', '', true);
		wp_enqueue_script('khor_golos_tableExport',  plugin_dir_url(__FILE__) . '../assets/js/tableExport.js', array('jquery'), '', true);
		//wp_enqueue_script('khor_golos_tableExport', 'https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js', '', '', true);
		wp_enqueue_script('khor_golos_table_jspdf', 'https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF/jspdf.min.js', '', '', true);
		wp_enqueue_script('khor_golos_table_jspdf_autotable', 'https://unpkg.com/tableexport.jquery.plugin/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js', '', '', true);
		wp_enqueue_script('khor_golos_Roboto_font',  plugin_dir_url(__FILE__) . '../assets/js/Roboto-Regular-normal.js', '', '', true);
		wp_enqueue_script('khor_golos_table', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js', '', '', true);
		wp_enqueue_script('khor_golos_table_locale', 'https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table-locale-all.min.js', '', '', true);
		wp_enqueue_script('khor_golos_table_export', 'https://unpkg.com/bootstrap-table@1.16.0/dist/extensions/export/bootstrap-table-export.min.js', '', '', true);

		//wp_enqueue_script('khor_golos_table_mobile', 'https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/mobile/bootstrap-table-mobile.min.js', '', '', true);
		//wp_enqueue_script('khor_golos_table_print', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.15.4/extensions/print/bootstrap-table-print.js', '', '', true);
		//wp_enqueue_script('khor_golos_fancybox', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js', '', '', true);
		//wp_enqueue_script('khor_golos_youtube_api', 'https://www.youtube.com/iframe_api', '', '', true);
		wp_enqueue_script('khor-golos-public.js', plugin_dir_url(__FILE__) . '../assets/js/khor-golos-public.js', array('jquery'), '', true);
		wp_localize_script('khor-golos-public.js', 'khor_golos_ajax', array('url' => admin_url('admin-ajax.php')));
	}
}// class Khor_Golos_Enqueue