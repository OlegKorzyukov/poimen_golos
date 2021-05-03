<?php

/* -------------------------------------------------------------------------- */
/*                      Shortcode [khor_golos_single session='']                     */
/* -------------------------------------------------------------------------- */

namespace Inc\Front;

class Khor_Golos_Shortcode
{
	const PUBLIC_TEMPLATES_DIR = WP_PLUGIN_DIR  . '/khor-golos/Includes/Front/partials/';

	public static function okShowShortcodeSinglePage($atts)
	{
		ob_start();
		require_once self::PUBLIC_TEMPLATES_DIR . 'khor-golos-single-public-display.php';
		return ob_get_clean();
	}

	public static function okShowAllSessionGolosPage()
	{
		ob_start();
		require_once self::PUBLIC_TEMPLATES_DIR . 'khor-golos-all-page-display.php';
		return ob_get_clean();
	}

	public static function okShowConvocationSessionPage($atts)
	{
		ob_start();
		require_once self::PUBLIC_TEMPLATES_DIR . 'khor-golos-convocation-page-display.php';
		return ob_get_clean();
	}

	private static function okNonceFieldBuilder($session)
	{
		$nonce = wp_create_nonce('khor_golos_frontend_' . $session);

		return $nonce;
	}

	private static function okNonceValidation($nonce, $session)
	{
	}

	private static function okGiveFromDB($atts)
	{
		global $wpdb;
		$atts = shortcode_atts(array(
			'session' => '',
			'convocation' => '',
		), $atts);

		$atributeSesion = $atts['session'];
		$atributeConvocation = $atts['convocation'];

		$table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$show_info_rishennya = $wpdb->get_results("SELECT tt.* FROM $table_name_rishennya tt INNER JOIN (SELECT ok_pdnpp, ok_num_session, ok_num_convocation, ok_show, MAX(ok_gl_number) AS MaxGlNum FROM $table_name_rishennya WHERE ok_num_session = $atributeSesion AND ok_num_convocation = $atributeConvocation AND ok_show = 1 GROUP BY ok_pdnpp) groupedtt ON tt.ok_pdnpp = groupedtt.ok_pdnpp AND tt.ok_gl_number = groupedtt.MaxGlNum AND tt.ok_num_session = groupedtt.ok_num_session AND tt.ok_num_convocation = groupedtt.ok_num_convocation AND tt.ok_show = groupedtt.ok_show");

		return $show_info_rishennya;
	}

	public static function okGiveRepeatQuestion()
	{
		global $wpdb;
		$numSession = $_POST['data_session'];
		$numGolos = $_POST['data_num_golos'];
		$numConvocation = $_POST['data_convocation'];
		$dataID = $_POST['data_id'];

		$validationNumSession =  preg_match('/^[1-9][0-9]*$/', $numSession);
		if (!$validationNumSession) {
			die;
		}
		$validationNumConvocation =  preg_match('/^[1-9][0-9]*$/', $numConvocation);
		if (!$validationNumConvocation) {
			die;
		}
		$validationNumPdnpp = preg_match("/(0+.[0-9]|[0-9])/u",  $numGolos);
		if (!$validationNumPdnpp) {
			die;
		}
		$validationIDSession =  preg_match('/^[1-9][0-9]*$/', $dataID);
		if (!$validationIDSession) {
			die;
		}

		$table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';

		$getText = $wpdb->get_results("SELECT ok_pd_name FROM $table_name_rishennya WHERE id = $dataID");
		$getText = &$getText[0]->ok_pd_name;

		$show_info_repeat_rishennya = $wpdb->get_results("SELECT * FROM $table_name_rishennya WHERE MATCH (ok_pd_name) AGAINST ('$getText') AND ok_gl_number < (SELECT MAX(ok_gl_number) FROM $table_name_rishennya) AND ok_num_session = $numSession AND ok_num_convocation = $numConvocation AND ok_show = 1 AND ok_pdnpp = $numGolos ORDER BY ok_gl_number");

		array_pop($show_info_repeat_rishennya);
		require_once self::PUBLIC_TEMPLATES_DIR . 'khor-golos-public-subrow-display.php';
		die;
	}

	private static function okGetTableHeaderDeputPresence($atts)
	{
		global $wpdb;
		$session = $atts['session'];
		$convocation = $atts['convocation'];
		$table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';

		$showDeputPresence = $wpdb->get_results("SELECT AVG(`ok_total_count`) as totalCount FROM $table_name_rishennya WHERE YEAR(`ok_gl_time`) > 2000 AND `ok_num_session` = $session AND `ok_num_convocation` = $convocation");

		return (int)$showDeputPresence[0]->totalCount;
	}

	private static function okGetTimeSession($atts)
	{
		global $wpdb;
		$session = $atts['session'];
		$convocation = $atts['convocation'];
		$table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$showDeputPresence = $wpdb->get_row("SELECT `ok_gl_time` FROM $table_name_rishennya WHERE `ok_num_session` = $session AND `ok_num_convocation` = $convocation AND `ok_pdnpp` = 0.2 AND `ok_gl_number` = 1");
		$date = new \DateTime($showDeputPresence->ok_gl_time);
		echo $date->format('d.m.Y');
	}

	public static function okCheckEmptyValueSubtable($value)
	{
		if ($value == 0) {
			$attr = 'data-empty = 1';
		} else {
			$attr = 'data-empty = 0';
		}

		echo $attr;
	}

	private static function okGetAllQuestion($data): int
	{
		return count($data);
	}

	private static function okGetAcceptQuestion($data): int
	{
		$acceptQuestion = 0;
		foreach ($data as $parts) {
			if ($parts->ok_result === 'РІШЕННЯ ПРИЙНЯТО') {
				$acceptQuestion++;
			}
		}
		return $acceptQuestion;
	}

	private static function okGetDeclineQuestion($data): int
	{
		return self::okGetAllQuestion($data) - self::okGetAcceptQuestion($data);
	}

	private static function okColorText($text)
	{
		if ($text === 'РІШЕННЯ ПРИЙНЯТО') {
			$color = 'accept_color';
		} else {
			$color = 'forbid_color';
		}
		echo $color;
	}

	private static function okTrimDate($date)
	{
		echo date("d.m.Y", strtotime($date));
	}

	private static function okClearSlashesText($text)
	{
		echo stripslashes($text);
	}

	private static function okGetFileLink($id)
	{
		global $wpdb;
		$table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
		$show_link_golos = $wpdb->get_row("SELECT ok_link FROM $table_name_rishennya WHERE id=$id ");

		if ($show_link_golos) {
			return $show_link_golos->ok_link;
		} else {
			return false;
		}
	}

	private static function okGetLinkVideo($data)
	{
		global $wpdb;
		$session = $data['session'];
		$convocation = $data['convocation'];

		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
		$link = $wpdb->get_row("SELECT ok_video_url FROM $table_name WHERE ok_num_session = $session AND ok_num_convocation = $convocation ");
		if ($link->ok_video_url) {
			$tagLink = "<div class='ok-public-video-wrapper'>
			<div class='ok-input-video'></div>
			<a href='{$link->ok_video_url}'>
			<div class='ok-video-wrapper__bg'></div>
			<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 294.843 294.843'>
  			<path class='ok-svg-a' d='M278.527 79.946c-10.324-20.023-25.38-37.704-43.538-51.132a6.001 6.001 0 00-7.135 9.649c16.687 12.34 30.521 28.586 40.008 46.983 9.94 19.277 14.98 40.128 14.98 61.976 0 74.671-60.75 135.421-135.421 135.421S12 222.093 12 147.421 72.75 12 147.421 12a6 6 0 000-12C66.133 0 0 66.133 0 147.421s66.133 147.421 147.421 147.421 147.421-66.133 147.421-147.421c0-23.444-5.641-46.776-16.315-67.475z'/>
  			<path class='ok-svg-b' d='M109.699 78.969a6.002 6.002 0 00-3.035 5.216v131.674a6 6 0 0012 0V94.74l88.833 52.883-65.324 42.087a6 6 0 106.5 10.087l73.465-47.333a6 6 0 00-.181-10.2L115.733 79.029a6 6 0 00-6.034-.06z'/>
			</svg>
			<span>Переглянути відеозапис сесії</span>
			</a>
			</div>";
			return $tagLink;
		} else {
			return false;
		}
	}

	private static function okGetLinkSolution($data)
	{
		global $wpdb;
		$session = $data['session'];
		$convocation = $data['convocation'];

		$table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
		$link = $wpdb->get_row("SELECT ok_solution_link FROM $table_name WHERE ok_num_session = $session AND ok_num_convocation = $convocation ");

		if ($link->ok_solution_link) {
			$tagLink = "<div class='ok-public-solution-wrapper'>
			<a class='ok-public-solution-link' target='_blank' href='{$link->ok_solution_link}'>
			<span>Переглянути рішення сесії</span>
			<img src='/wp-content/plugins/khor-golos/assets/images/link.svg' alt=''>
			</a>
			</div>
			<svg style='visibility: hidden; position: absolute;' width='0' height='0' xmlns='http://www.w3.org/2000/svg' version='1.1'>
				<defs>
					<filter id='goo'><feGaussianBlur in='SourceGraphic' stdDeviation='10' result='blur' />    
							<feColorMatrix in='blur' mode='matrix' values='1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9' result='goo' />
							<feComposite in='SourceGraphic' in2='goo' operator='atop'/>
					</filter>
				</defs>
			</svg>";

			return $tagLink;
		} else {
			return false;
		}
	}

	public static function okDeputGolos()
	{
		global $wpdb;
		if (isset($_POST['action'])) {
			$ajaxActionTypeGolos = $_POST['action'];
			$validationAjaxActionTypeGolos = preg_match("/\w/u", $ajaxActionTypeGolos);
			if ($validationAjaxActionTypeGolos !== 1) {
				die('Помилка відображення (action type)');
			}
		} else {
			die;
		}

		if (isset($_POST['dataSend'])) {
			$ajaxSubtableMetaConvocation = $_POST['dataSend']['data_convocation'];
			$ajaxSubtableMetaSession = $_POST['dataSend']['data_session'];
			$ajaxSubtableMetaPdName = $_POST['dataSend']['data_pdname'];
			$ajaxSubtableMetaGlName = $_POST['dataSend']['data_glnum'];

			$validationAjaxSubtableMetaConvocation = preg_match("/[0-9]/u", $ajaxSubtableMetaConvocation);
			$validationAjaxSubtableMetaSession = preg_match("/[0-9]/u", $ajaxSubtableMetaSession);
			$validationAjaxSubtableMetaPdName = preg_match("/(0+.[0-9]|[0-9])/u",  $ajaxSubtableMetaPdName);
			$validationAjaxSubtableMetaGlName = preg_match("/[0-9]/u", $ajaxSubtableMetaGlName);

			$validationResult = [$validationAjaxSubtableMetaSession, $validationAjaxSubtableMetaPdName, $validationAjaxSubtableMetaGlName, $validationAjaxSubtableMetaConvocation];

			foreach ($validationResult as $result) {
				if (!$result) {
					die("Помилка валідації");
				}
			}
		} else {
			die;
		}


		$tableAllDeput = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';
		$tableGolosDeput = $wpdb->get_blog_prefix() . 'ok_khor_golos_deput';
		$queryDb = "SELECT $tableAllDeput.`ok_dp_name`, $tableGolosDeput.`ok_dp_golos` FROM $tableAllDeput INNER JOIN $tableGolosDeput ON $tableGolosDeput.`ok_dp_id` = $tableAllDeput.`id` AND $ajaxSubtableMetaSession = $tableGolosDeput.`ok_num_session` AND $ajaxSubtableMetaConvocation = $tableGolosDeput.`ok_num_convocation`  AND $ajaxSubtableMetaPdName = $tableGolosDeput.`ok_num_rishennya` AND $ajaxSubtableMetaGlName = $tableGolosDeput.`ok_gl_number` AND $tableGolosDeput.`ok_dp_golos` LIKE ";

		switch ($ajaxActionTypeGolos) {
			case 'public_table_meta_golos-yes':
				$typeGolos = 'ЗА';
				$adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
				$jsonConvert = json_encode($adminSubtableAjaxGolos);
				print_r($jsonConvert);
				break;
			case 'public_table_meta_golos-no':
				$typeGolos = 'ПРОТИ';
				$adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
				$jsonConvert = json_encode($adminSubtableAjaxGolos);
				print_r($jsonConvert);
				break;
			case 'public_table_meta_golos-ng':
				$typeGolos = 'УТРИМАВСЯ';
				$adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
				$jsonConvert = json_encode($adminSubtableAjaxGolos);
				print_r($jsonConvert);
				break;
			case 'public_table_meta_golos-utr':
				$typeGolos = 'НЕ ГОЛОСУВАВ';
				$adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
				$jsonConvert = json_encode($adminSubtableAjaxGolos);
				print_r($jsonConvert);
				break;
			case 'public_table_meta_golos-all':
				$adminSubtableAjaxGolos = $wpdb->get_results("SELECT $tableAllDeput.`ok_dp_name`, $tableGolosDeput.`ok_dp_golos` FROM $tableAllDeput INNER JOIN $tableGolosDeput ON $tableGolosDeput.`ok_dp_id` = $tableAllDeput.`id` AND $ajaxSubtableMetaSession = $tableGolosDeput.`ok_num_session` AND $ajaxSubtableMetaConvocation = $tableGolosDeput.`ok_num_convocation` AND $ajaxSubtableMetaPdName = $tableGolosDeput.`ok_num_rishennya` AND $ajaxSubtableMetaGlName = $tableGolosDeput.`ok_gl_number`");
				$jsonConvert = json_encode($adminSubtableAjaxGolos);
				print_r($jsonConvert);
				break;
			default:
				echo "Помилка при відображенні";
		}
		die;
	}
}//class Khor_Golos_Shortcode