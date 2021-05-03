<?php

namespace Inc;

class Khor_Golos_BaseController
{
	public static function register()
	{
		//add_filter('the_title', array(__CLASS__, 'okAddBreadcrumbInTitlePageGolos'));
	}

	public static function okConvertToRomeDigit($digit)
	{
		$num = intval($digit);
		if (!$num) {
			return ($digit);
			die;
		}
		if ($num >= 4000) {
			return ($digit);
			die;
		}

		$data = array(
			"0" => array(
				"1" => "I", //1
				"2" => "II",
				"3" => "III",
				"4" => "IV",
				"5" => "V", //5
				"6" => "VI",
				"7" => "VII",
				"8" => "VIII",
				"9" => "IX",
				"0" => ""
			),

			"1" => array(
				"1" => "X", //10
				"2" => "XX",
				"3" => "XXX",
				"4" => "XL",
				"5" => "L", //50
				"6" => "LX",
				"7" => "LXX",
				"8" => "LXXX",
				"9" => "XC",
				"0" => ""
			),

			"2" => array(
				"1" => "C", //100
				"2" => "CC",
				"3" => "CCC",
				"4" => "CD",
				"5" => "D", //500
				"6" => "DC",
				"7" => "DCC",
				"8" => "DCCC",
				"9" => "CM",
				"0" => ""
			),

			"3" => array(
				"1" => "M", //1000
				"2" => "MM",
				"3" => "MMM"
			)

		);

		$numlen = strlen($num);
		$digit = "";
		for ($nums = 0; $nums < $numlen; $nums++) {
			$pos = $nums + 1;
			$num_interval = @substr($num, -$pos, 1);
			$digit = @strtr($num_interval, $data[$nums]) . $digit;
		}
		return ($digit);
	}

	public static function okGetAllSessionInThisConvocation(int $convocation)
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
		$showResult = $wpdb->get_results("SELECT COUNT(*) as count FROM $table_name WHERE `ok_num_convocation` = $convocation");

		return $showResult[0]->count;
	}

	public static function okGetAllQuestionInThisConvocation(int $convocation)
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$showResult = $wpdb->get_results("SELECT COUNT(*) as count FROM $table_name WHERE `ok_num_convocation` = $convocation");

		return $showResult[0]->count;
	}

	public static function okGetAcceptQuestionInThisConvocation(int $convocation)
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$showResult = $wpdb->get_results("SELECT COUNT(*) as count FROM $table_name WHERE `ok_num_convocation` = $convocation AND `ok_result` = 'РІШЕННЯ ПРИЙНЯТО'");

		return $showResult[0]->count;
	}

	public static function okGetDeclineQuestionInThisConvocation(int $convocation)
	{
		$showResult = self::okGetAllQuestionInThisConvocation($convocation) - self::okGetAcceptQuestionInThisConvocation($convocation);
		return $showResult;
	}

	public static function okGetQuestionWithoutRepeatInThisConvocation(int $convocation)
	{
		global $wpdb;

		$table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$show_info_rishennya = $wpdb->get_results("SELECT DISTINCT ok_num_session FROM $table_name_rishennya WHERE ok_num_convocation = $convocation");

		$sum = 0;
		foreach ($show_info_rishennya as $value) {
			$result = $wpdb->get_results("SELECT COUNT(*) as count FROM $table_name_rishennya tt INNER JOIN (SELECT ok_pdnpp, ok_num_session, ok_num_convocation, ok_show, MAX(ok_gl_number) AS MaxGlNum FROM $table_name_rishennya WHERE ok_num_session = $value->ok_num_session AND ok_num_convocation = $convocation AND ok_show = 1 GROUP BY ok_pdnpp) groupedtt ON tt.ok_pdnpp = groupedtt.ok_pdnpp AND tt.ok_gl_number = groupedtt.MaxGlNum AND tt.ok_num_session = groupedtt.ok_num_session AND tt.ok_num_convocation = groupedtt.ok_num_convocation AND tt.ok_show = groupedtt.ok_show");
			$sum += $result[0]->count;
		}

		return $sum;
	}

	public static function okGetAcceptQuestionWithoutRepeatInThisConvocation(int $convocation)
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$show_info_rishennya = $wpdb->get_results("SELECT DISTINCT ok_num_session FROM $table_name WHERE ok_num_convocation = $convocation");

		$sum = 0;
		foreach ($show_info_rishennya as $value) {
			$result = $wpdb->get_results("SELECT COUNT(*) as count FROM $table_name tt INNER JOIN (SELECT ok_pdnpp, ok_num_session, ok_num_convocation, ok_result, ok_show, MAX(ok_gl_number) AS MaxGlNum FROM $table_name WHERE ok_num_session = $value->ok_num_session AND ok_num_convocation = $convocation AND ok_show = 1 GROUP BY ok_pdnpp) groupedtt ON tt.ok_pdnpp = groupedtt.ok_pdnpp AND tt.ok_gl_number = groupedtt.MaxGlNum AND tt.ok_num_session = groupedtt.ok_num_session AND tt.ok_num_convocation = groupedtt.ok_num_convocation AND tt.ok_result = 'РІШЕННЯ ПРИЙНЯТО' AND tt.ok_show = groupedtt.ok_show");
			$sum += $result[0]->count;
		}

		return $sum;
	}

	public static function okGetDeclineQuestionWithoutRepeatInThisConvocation(int $convocation)
	{
		$showResult = self::okGetQuestionWithoutRepeatInThisConvocation($convocation) - self::okGetAcceptQuestionWithoutRepeatInThisConvocation($convocation);
		return $showResult;
	}



	public static function okGetAverageDeputPresence(int $convocation)
	{
		global $wpdb;
		$allDeput = 64;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$showDeputPresence = $wpdb->get_results("SELECT AVG(ok_total_count) as avgCount FROM $table_name WHERE `ok_num_convocation` = $convocation AND `ok_pdnpp` = 0.2 AND `ok_gl_number` = 1");

		$averagePresence = round((($showDeputPresence[0]->avgCount / $allDeput) * 100), 1);

		return $averagePresence;
	}

	public static function okAddBreadcrumbInTitlePageGolos()
	{
		global $post;
		if ($post->post_parent) {
			$ancestors = get_post_ancestors($post->ID);
			sort($ancestors);
			$breadcrumb = "<div class='ok-breadcrumb-page-golos'>";
			$separator = "";
			if (count($ancestors) > 1) {
				$separator = "<span class='ok-breadcrumb-golos-separator'><i class='fas fa-angle-right'></i></span>";
			}
			foreach ($ancestors as $key => $ancestor) {
				$pageUrl = get_page_link($ancestor);
				$pageTitle = get_the_title($ancestor);
				$breadcrumb .= "<a class='ok-breadcrumb-page-golos-link ok-breadcrumb-link-{$key}' href='{$pageUrl}'>{$pageTitle}</a>{$separator}";
			}
			$breadcrumb .= "</div>";
			echo ($breadcrumb);
		}
	}

	public static function okGetDateInConvocationSession($convocation)
	{
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$showResult = $wpdb->get_row("SELECT YEAR(MAX(ok_gl_time)) as maxDate, YEAR(MIN(ok_gl_time)) as minDate FROM $table_name WHERE `ok_num_convocation` = $convocation AND YEAR(`ok_gl_time`) > 2000 ");
		return (array)$showResult;
	}
} // class Khor_Golos_BaseController
