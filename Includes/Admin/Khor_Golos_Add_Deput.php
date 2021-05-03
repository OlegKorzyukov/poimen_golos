<?php

namespace Inc\Admin;

class Khor_Golos_Add_Deput extends Khor_Golos_Deputy
{
   private $name;
   private $fraction;
   private $convocation;
   private $birthday;
   private $position;
   private $commission;
   private $info;
   private $photo;

   public function __construct(array $deputData)
   {
      $this->photo = $deputData['photoDeput'];
      $this->name = $deputData['nameDeput'];
      $this->fraction = $deputData['fractionDeput'];
      $this->convocation = $deputData['convocationDeput'];
      $this->birthday = $deputData['birthdayDeput'];
      $this->position = $deputData['positionDeput'];
      $this->commission = $deputData['commissionDeput'];
      $this->info = $deputData['infoDeput'];

      $this->okValidateSaveDeputRow($this->fraction, $this->convocation, $this->birthday, $this->position, $this->commission,  $this->info, $this->photo, '');

      $this->okAddNewDeput($this->name, $this->convocation, $this->fraction, $this->birthday, $this->position, $this->commission, $this->info, $this->photo);
   }

   private function okAddNewDeput($dpName, $dpConvocation, $dpFraction, $dpBirthday, $dpPosition,  $dpCommission, $dpInfo, $dpPhoto)
   {
      global $wpdb;
      $dpnameSlashes = addslashes($dpName);
      $tableName = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';
      $table_old_name = $wpdb->get_blog_prefix() . 'ok_khor_old_name_deput';
      $getDeput = $wpdb->get_results(" SELECT $tableName.`id`, $tableName.`ok_dp_convocation` FROM $tableName INNER JOIN $table_old_name ON $tableName.`ok_dp_name` LIKE '%$dpnameSlashes%' OR ($table_old_name.`ok_dp_old_name` LIKE '%$dpnameSlashes%' AND $tableName.`id` = $table_old_name.`ok_dp_id`)");

      if (!empty($getDeput)) {
         $updateConvocation = $getDeput[0]->ok_dp_convocation . ',' . $dpConvocation;
         $update = $wpdb->update(
            $tableName,
            array('ok_dp_convocation' => $updateConvocation),
            array('ID' => $getDeput[0]->id)
         );
         if ($update !== false) {
            echo 'Дані оновлені';
         } else {
            echo ('Помилка оновлення');
         }
      } else {
         $insert = $wpdb->insert(
            $tableName,
            array(
               'ok_dp_name' => $dpName,
               'ok_dp_fraction' => $dpFraction,
               'ok_dp_convocation' => $dpConvocation,
               'ok_dp_birthday' => $dpBirthday,
               'ok_dp_position' => $dpPosition,
               'ok_dp_commission' => $dpCommission,
               'ok_dp_info' => $dpInfo,
               'ok_dp_photo' => $dpPhoto
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
         );
         if ($insert !== false) {
            echo 'Дані додані до таблиці';
         } else {
            echo ('Помилка збереження');
         }
      }
      die;
   }
} // class Khor_Golos_Add_Deput
