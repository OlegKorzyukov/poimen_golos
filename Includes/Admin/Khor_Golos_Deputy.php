<?php

namespace Inc\Admin;



class Khor_Golos_Deputy
{
   const ADMIN_TEMPLATES_DIR = WP_PLUGIN_DIR  . '/khor-golos/Includes/Admin/partials/';

   private static function okGetDeputyList(int $convocation)
   {
      global $wpdb;
      $table_deput = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';
      $show_deput = $wpdb->get_results("SELECT * FROM $table_deput WHERE FIND_IN_SET($convocation, ok_dp_convocation) ORDER BY `ok_dp_name`");

      return $show_deput;
   }

   public static function okDeputyPage()
   {
      require_once self::ADMIN_TEMPLATES_DIR . 'khor-golos-admin-deputy.php';
   }


   public static function okUploadDeputPhoto(int $idDeput, $value = '')
   {
      if (empty($value)) {
         $value = '/wp-content/plugins/khor-golos/assets/images/Unknown.jpg';
      }
      $showUpload = '<div class="wrap-deput-upload">
                        <form class="deput-photo-upload" method="post">
                           <div class="deput-photo-wrapper">
                              <img class="deput-golos-photo" id="' . $idDeput . '" data-src="" src="' . $value . '"/>
                              <div class="deput-photo_input_group photo-input-wrapper">
                                 <input type="hidden" name="golos_deput_photo_url" value="' . $value . '" />
                                 <input type="hidden" name="action-dp-photo" value="khor_golos_admin_change-photo-deput" />
                                 <button type="submit" class="upload_image_button button">Завантажити</button>
                                 <button type="submit" class="remove_image_button button">×</button>
                              </div>
                           </div>
                        </form>
                     </div>';

      return $showUpload;
   }

   public static function okSaveAfterChangeDeputInfo()
   {
      //validation upload photo url
      //validation save info
      global $wpdb;
      $tableName = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';

      if ($_POST['deputFraction'] && !empty($_POST['deputFraction'])) {
         $deputFraction = $_POST['deputFraction'];
      }
      if ($_POST['deputConvocation'] && !empty($_POST['deputConvocation'])) {
         $deputConvocation = $_POST['deputConvocation'];
      }
      if ($_POST['deputBirthday'] && !empty($_POST['deputBirthday'])) {
         $deputBirthday = $_POST['deputBirthday'];
      }
      if ($_POST['deputPosition'] && !empty($_POST['deputPosition'])) {
         $deputPosition = $_POST['deputPosition'];
      }
      if ($_POST['deputComission'] && !empty($_POST['deputComission'])) {
         $deputComission = $_POST['deputComission'];
      }
      if ($_POST['deputInfo'] && !empty($_POST['deputInfo'])) {
         $deputInfo = $_POST['deputInfo'];
      }
      if ($_POST['deputImg'] && !empty($_POST['deputImg'])) {
         $deputPhoto = $_POST['deputImg'];
      }
      if ($_POST['deputID'] && !empty($_POST['deputID'])) {
         $deputID = $_POST['deputID'];
      }

      self::okValidateSaveDeputRow($deputFraction, $deputConvocation, $deputBirthday, $deputPosition, $deputComission, $deputInfo, $deputPhoto, $deputID);

      $saveQuery = $wpdb->update(
         $tableName,
         array(
            'ok_dp_fraction' => $deputFraction,
            'ok_dp_convocation' => $deputConvocation,
            'ok_dp_birthday' =>  $deputBirthday,
            'ok_dp_position' => $deputPosition,
            'ok_dp_commission' => $deputComission,
            'ok_dp_info' => $deputInfo,
            'ok_dp_photo' => $deputPhoto
         ),
         array('ID' => $deputID)
      );

      echo 'Зміни збережено';
      die;
   }

   protected static function okValidateSaveDeputRow($dpFraction = '', $dpConvocation = '', $dpBirthday = '', $dpPosition = '', $dpComission = '', $dpInfo = '', $dpPhoto = '', $dpID = '')
   {
      $validateDpFraction = preg_match("/^[а-яА-ЯїЇіІєЄ0-9\.\#\№\'\"\\\«\»\s\–\-\(\),’:;]+$|^\s*$/u", $dpFraction);
      $validateDpConvocation = preg_match("/[0-9,]|^\s*$/u", $dpConvocation);
      $validateDpBirthday = preg_match("/[0-9-]|^\s*$/u", $dpBirthday);
      $validateDpPosition = preg_match("/^[а-яА-ЯїЇіІєЄ0-9\.\#\№\'\"\\\«\»\s\–\-\(\),’:;]+$|^\s*$/u", $dpPosition);
      $validateDpComission = preg_match("/^[а-яА-ЯїЇіІєЄ0-9\.\#\№\'\"\\\«\»\s\–\-\(\),’:;]+$|^\s*$/u", $dpComission);
      $validateDpInfo = preg_match("/^[а-яА-ЯїЇіІєЄ0-9\.\#\№\'\"\\\«\»\s\–\-\(\),’:;]+$|^\s*$/u", $dpInfo);
      $validateDpPhoto = preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]|^\s*$|\/wp-content\/([\s*\S*]*)/i", $dpPhoto);
      $validateDpID = preg_match("/[0-9]|^\s*$/u", $dpID);

      $resultValidation = [$validateDpFraction, $validateDpConvocation, $validateDpBirthday, $validateDpPosition, $validateDpComission, $validateDpInfo, $validateDpPhoto, $validateDpID];

      foreach ($resultValidation as $result) {
         if (!$result) {
            die('Помилка валідації');
         }
      }
   }
}//class Khor_Golos_Deputy
