<?php

namespace Inc\Admin;

class Khor_Golos_Upload_Result_File
{
   const TARGET_DIR = WP_PLUGIN_DIR  . '/khor-golos/files/PublicDoc/';
   const TARGET_URL = WP_PLUGIN_URL . '/khor-golos/files/PublicDoc/';

   protected $filename;
   protected $filetype;
   protected $filetmpname;
   protected $fileerror;
   protected $filesize;
   public $numsession;
   public $convocation;

   public function init()
   {
      $this->filename = $_FILES["golos_upload_result_file"]["name"];
      $this->filetype = $_FILES["golos_upload_result_file"]['type'];
      $this->filetmpname = $_FILES["golos_upload_result_file"]['tmp_name'];
      $this->fileerror = $_FILES["golos_upload_result_file"]['error'];
      $this->filesize = $_FILES["golos_upload_result_file"]['size'];
      $this->numsession = $_POST['result_golos_num_session'];
      $this->convocation = $_POST['result_golos_num_convocation'];

      $this->okPostDataValidation($this->filename, $this->filetype, $this->filetmpname, $this->fileerror, $this->filesize, $this->numsession, $this->convocation);
      self::okUzipResultFile($this->filetmpname, $this->convocation, $this->numsession);
      self::okAllData($this->convocation, $this->numsession);
   }

   protected function okPostDataValidation($filename, $filetype, $filetmpname, $fileerror, $filesize, $numsession, $convocation)
   {
      $dataValidation = new \WP_Error;

      //Check size upload file (> 50MB)
      if ($filesize > 52428800) {
         $dataValidation->add('max_size_error', 'Розмір файла перевищує допустимий (50 MB)');
      }

      // Check nonce field for security
      $checkNonce = wp_verify_nonce($_POST['golos_upload_file'], 'action_upload_file');
      if (!$checkNonce) {
         $dataValidation->add('nounce_error', 'Помилка в nounce_field');
      }

      // Check right user for upload
      $checkUserRight = current_user_can('upload_files');
      if (!$checkUserRight) {
         $dataValidation->add('user_right_error', 'Помилка в правах користувача');
      }

      // Check type upload file
      if ($filetype != 'application/x-zip-compressed') {
         $dataValidation->add('type_file_error', 'Помилка в типі файла (повинен бути *zip)');
      }

      //Check convocation number
      $check_input_convocation =  preg_match('/^[1-9][0-9]*$/', $convocation);
      if (!$check_input_convocation) {
         $dataValidation->add('not_allow_symbol_error', 'Недопустимі символи в номері скликання');
      }

      //Check input number session (only digit)
      $check_input_session =  preg_match('/^[1-9][0-9]*$/', $numsession);
      if (!$check_input_session) {
         $dataValidation->add('not_allow_symbol_error', 'Недопустимі символи в номері сесії');
      }

      //Show error
      if ($dataValidation->get_error_code()) {
         foreach ($dataValidation->get_error_messages() as $error) {
            echo 'Перевірка не пройдена, файл не завантажено' . '<br/>';
            echo $error;
         }
         die;
      }
   }

   private static function okUzipResultFile($tmpname, $convocation, $numsession)
   {
      $zip = new \ZipArchive;
      if ($zip->open($tmpname) === TRUE) {
         if (is_writable(self::TARGET_DIR)) {
            $zip->extractTo(self::TARGET_DIR . $convocation . '/' . $numsession);
            $zip->close();
            echo 'Файли розпаковані';
         } else {
            die('Помилка завантаження (немає прав для запису)');
         }
      } else {
         echo 'Помилка розпаковки файлів';
      }
   }

   private static function okGetRishennyaTable($convocation, $numsession)
   {
      global $wpdb;
      $tableName = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
      $query = $wpdb->get_results("SELECT id, ok_pdnpp, ok_link FROM $tableName WHERE `ok_num_convocation` = $convocation AND `ok_num_session` = $numsession ORDER BY cast(ok_pdnpp as float)");
      if (!empty($query)) {
         return $query;
      } else {
         self::okRecursiveRemoveDir(self::TARGET_DIR . $convocation);
         die("Помилка (перевірте номер сесії та скликання)");
      }
   }

   private static function okRecursiveRemoveDir($dir)
   {
      $includes = new \FilesystemIterator($dir);
      foreach ($includes as $include) {
         if (is_dir($include) && !is_link($include)) {
            self::okRecursiveRemoveDir($include);
         } else {
            unlink($include);
         }
      }
      rmdir($dir);
   }


   private static function okAllData($convocation, $numsession)
   {
      $pathFile = self::TARGET_DIR . $convocation . '/' . $numsession;
      foreach (self::okGetRishennyaTable($convocation, $numsession) as $key => $value) {
         if ($handle = opendir($pathFile)) {
            while (false !== ($file = readdir($handle))) {
               $idFile = self::okPregMatchResultFileName($file)['id_file'];
               $fileName = self::okPregMatchResultFileName($file)['file_name'];
               if ($idFile == ($key + 1)) {
                  $link = self::okGetLinkDownloadFile($fileName, $convocation, $numsession);
                  $ID = $value->id;
                  self::okInsertLinkFileToDB($link, $ID);
               }
            }
            closedir($handle);
         }
      }
   }

   private static function okPregMatchResultFileName($filename)
   {
      $searchFileName = preg_match("/(Gol)(?<countFile>[0-9]+)_p(?<glNumFile>[0-9]*\.?[0-9]+).(?<extensionFile>\w+)/u", $filename, $group);

      if ($searchFileName) {
         $validNameCount['id_file'] = $group['countFile'];
         $validNameCount['file_name'] = $filename;
         return $validNameCount;
      }
   }

   public static function okGetLinkDownloadFile($filename, $convocation, $numsession)
   {
      $downloadfile = self::TARGET_URL . $convocation . '/' . $numsession . '/' . $filename;
      if (filter_var($downloadfile, FILTER_VALIDATE_URL)) {
         return $downloadfile;
      } else {
         die('Помилковий url файла');
      }
   }

   private static function okInsertLinkFileToDB($link, $ID)
   {
      global $wpdb;
      $tableName = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
      $updateQuery = $wpdb->update(
         $tableName,
         array(
            'ok_link' => $link,
         ),
         array('ID' => $ID)
      );
   }
}//Khor_Golos_Upload_Result_File