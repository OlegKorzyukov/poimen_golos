<?php

namespace Inc\Admin;

use \Inc\Admin\Khor_Golos_Upload_File;
use \Inc\Khor_Golos_BaseController;

class Khor_Table_Constructor
{

   public static function okGetFromDb($table_name)
   {
      global $wpdb;
      if ($table_name == 'ok_khor_all_deput') {
         $table = $wpdb->get_blog_prefix() . $table_name;
         $product = $wpdb->get_results("SELECT DISTINCT `ok_dp_convocation` FROM $table ORDER BY ok_dp_convocation");
         foreach ($product as $key => $value) {
            $checkData = preg_match("/[,]/u", $value->ok_dp_convocation);
            if ($checkData) {
               unset($product[$key]);
            }
         }
      } else {
         $table = $wpdb->get_blog_prefix() . $table_name;
         $product = $wpdb->get_results("SELECT DISTINCT `ok_num_convocation` FROM $table ORDER BY ok_num_convocation");
      }
      return $product;
   }

   private static function okAdminGetTableHeaderDeputPresence($session, $convocation)
   {
      global $wpdb;
      $table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
      $showDeputPresence = $wpdb->get_row("SELECT AVG(`ok_total_count`) as totalCount FROM $table_name_rishennya WHERE	`ok_num_session` = $session AND `ok_num_convocation` = $convocation");

      return (int) $showDeputPresence->totalCount;
   }

   private static function okAdminGetAllQuestion($data): int
   {
      return count($data);
   }

   private static function okAdminGetAcceptQuestion($data): int
   {
      $acceptQuestion = 0;
      foreach ($data as $parts) {
         if ($parts->ok_result === 'РІШЕННЯ ПРИЙНЯТО') {
            $acceptQuestion++;
         }
      }
      return $acceptQuestion;
   }

   private static function okAdminGetDeclineQuestion($data): int
   {
      return self::okAdminGetAllQuestion($data) - self::okAdminGetAcceptQuestion($data);
   }

   public static function okGetNumberConvocation()
   {
      global $wpdb;
      $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
      $result = $wpdb->get_results("SELECT DISTINCT `ok_num_convocation` as allConvocation FROM $table_name ORDER BY `ok_num_convocation`");
      if (!empty($result) && $result !== NULL) {
         foreach ($result as $value) {
            echo '<option value="' . $value->allConvocation . '">' . $value->allConvocation . '</option>';
         }
      } else {
         return false;
      }
   }

   public static function okGetNumberSession()
   {
      $convocation = $_POST['valueSelected'];
      $check_input_convocation =  preg_match('/^[1-9][0-9]*$/', $convocation);
      if (!$check_input_convocation) {
         die('Помилка в номері скликання');
      }
      global $wpdb;
      $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
      $result = $wpdb->get_results("SELECT `ok_num_session` as allSession FROM $table_name WHERE `ok_num_convocation` = $convocation ORDER BY `ok_num_session`");

      if (!empty($result) && $result !== NULL) {
         echo json_encode($result);
      } else {
         return false;
      }
      die;
   }

   public static function okBeforeTableMetaInfo()
   {
      global $wpdb;

      $session  = $_POST['data_session'];
      $convocation  = $_POST['data_convocation'];

      $validationNumSession =  preg_match('/^[1-9][0-9]*$/', $session);
      if (!$validationNumSession) {
         die;
      }
      $validationNumConvocation =  preg_match('/^[1-9][0-9]*$/', $convocation);
      if (!$validationNumConvocation) {
         die;
      }

      $table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
      $show_info_rishennya = $wpdb->get_results("SELECT * FROM $table_name_rishennya WHERE `ok_num_session` = $session AND `ok_show` = 1 AND `ok_num_convocation` = $convocation");

      $headBeforeTable = "<tr style='display: flex;text-align: center;' class='head-info-table'>
                              <td style='width: 25%;' class='head-info-table-part question-head-num'>
                                 <div class='golos-head-title'>Всього питань</div>
                                 <div class='golos-head-result'>" . self::okAdminGetAllQuestion($show_info_rishennya) . "</div>
                              </td>
                              <td style='width: 25%;' class='head-info-table-part question-head-deput'>
                                 <div class='golos-head-title'>Присутність</div>
                                 <div class='golos-head-result'>" . self::okAdminGetTableHeaderDeputPresence($session, $convocation) . "</div>
                              </td>
                              <td style='width: 25%;' class='head-info-table-part question-head-accept'>
                                 <div class='golos-head-title'>Прийнято питань</div>
                                 <div class='golos-head-result'>" . self::okAdminGetAcceptQuestion($show_info_rishennya) . "</div>
                              </td>
                              <td style='width: 25%;' class='head-info-table-part question-head-decline'>
                                 <div class='golos-head-title'>Не прийнято питань</div>
                                 <div class='golos-head-result'>" . self::okAdminGetDeclineQuestion($show_info_rishennya) . "</div>
                              </td>
                        </tr>";

      echo $headBeforeTable;
      die;
   }

   private static function okNonceFieldCreate()
   {
      $nonce = wp_create_nonce('khor_golos_backend_table');
      return $nonce;
   }

   private static function okNonceValidation($nonce, $nonceKey)
   {
      $checkNonce = wp_verify_nonce($nonce, $nonceKey);
      return $checkNonce;
   }

   private static function okGetDbInfoByConvocation($convocation)
   {
      global $wpdb;
      $table = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
      $product = $wpdb->get_results("SELECT * FROM {$table} WHERE `ok_num_convocation` = $convocation ORDER BY `ok_num_session`");

      return $product;
   }

   // Show info from history upload file session
   public static function okShowUploadHistoryTable()
   {
      $rowcount = 0;
      $tableNameFiles = 'ok_khor_golos_files';
      foreach (self::okGetFromDb($tableNameFiles) as $key => $meta) {
         $adminTable = '<div class="show_upload_table">
               <h3>' . Khor_Golos_BaseController::okConvertToRomeDigit($meta->ok_num_convocation) . ' Скликання</h3>
               <table
                 data-locale="uk-UA"
                 data-toggle="table"
                 data-sort-class="khor_golos_sorted"
                 data-search="true"
                 data-mobile-responsive="true"
                 data-show-columns="true"
                 data-show-print="true"
                 data-resizable="true"
                 data-advanced-search="true"
                 data-show-columns-toggle-all="true"
                 data-detail-view="true"
                 data-detail-formatter="detailFormatter"
                 data-show-export="true"
                 data-trim-on-search="false"
                 data-nonce="' . self::okNonceFieldCreate() . '"
                 id="khor_golos_admin_table-' . $key . '">
                 <thead>
                   <tr>
                     <th data-sortable="true" data-field="id-' . $key . '">№</th>
                     <th data-sortable="true" class="filename-' . $key . '" data-field="name">Назва файлу</th>
                     <th data-sortable="true" data-field="num_session-' . $key . '">Сесія</th>
                     <th data-sortable="true" data-field="num_convocation-' . $key . '">Скликання</th>
                     <th data-sortable="true" data-field="filesize-' . $key . '">Розмір файлу (KB)</th>
                     <th data-sortable="true" data-field="date_upload-' . $key . '">Дата завантаження</th>
                     <th data-field="date_video_session-' . $key . '">Відео сесії</th>
                     <th data-field="date_solution_link-' . $key . '">Рішення сесії</th>
                     <th data-events="operateEvents" data-field="formatter">Редагувати | Видалити</th>
                   </tr>
                 </thead>
                 <tbody>';

         foreach (self::okGetDbInfoByConvocation($meta->ok_num_convocation) as $metaSingle) {
            $filesize = $metaSingle->ok_filesize / 1024; //convert to KB
            $rowcount++;

            $linkfile = Khor_Golos_Upload_File::okDownloadFile($metaSingle->ok_filename);
            $nonce = wp_create_nonce('khor_golos_table_file_' . $metaSingle->id);
            $videoLink = '';
            $solutionLink = '';
            if ($metaSingle->ok_video_url) {
               $videoLink = "<a target='_blank' href='" . $metaSingle->ok_video_url . "'>Переглянути</a>";
            }
            if ($metaSingle->ok_solution_link) {
               $solutionLink = "<a target='_blank' href='" . $metaSingle->ok_solution_link . "'>Переглянути</a>";
            }

            $adminTable .=
               "<tr id='khor_golos_{$metaSingle->id}' class='admin_subrow_session' data-filename='{$metaSingle->ok_filename}' data-docid='{$metaSingle->id}' data-nonce='{$nonce}' data-numses = '{$metaSingle->ok_num_session}' data-convocation = '{$metaSingle->ok_num_convocation}'>
                       <td>" . $rowcount . "</td>
                       <td><a href='{$linkfile}' download>" . $metaSingle->ok_filename . "</a></td>
                       <td>" . Khor_Golos_BaseController::okConvertToRomeDigit($metaSingle->ok_num_session) . " (" . (int) $metaSingle->ok_num_session . ")</td>
                       <td>" . Khor_Golos_BaseController::okConvertToRomeDigit($metaSingle->ok_num_convocation) . " (" . (int) $metaSingle->ok_num_convocation . ")</td>
                       <td>" . (int) $filesize . "</td>
                       <td>" . $metaSingle->ok_date_upload . "</td>
                       <td><div class='ok-admin-video-url-link'>{$videoLink}</div></td>
                       <td><div class='ok-admin-solution-link'>{$solutionLink}</div></td>
                       <td>
                           <div class='ok-control-table-files'>
                              <a class='change' href='javascript:void(0)' title='Редагувати'>
                              <i class='fas fa-edit'></i>
                              </a>
                              <a class='remove' href='javascript:void(0)' title='Видалити'>
                                 <i class='fa fa-trash'></i>
                              </a>
                           </div>
                           <div class='ok-control-table-files-second-group' style='display: none;'> 
                              <a class='ok-control-table-files-save-button' href='javascript:void(0)' title='Зберегти'>
                                 <i class='fas fa-save'></i>
                              </a>
                              <a class='ok-control-table-files-cancel-button' href='javascript:void(0)' title='Відмінити'>
                                 <i class='fas fa-times'></i>
                              </a>
                           </div>
                       </td>
                   </tr>
                   ";
         }

         $adminTable .= '</tbody>
                           </table>
                       </div>';

         echo $adminTable;
      }
   }

   public static function okAdminSaveVideoSolutions()
   {
      global $wpdb;
      $videoLink = $_POST['data_video_url'];
      $solutionsLink = $_POST['data_solutions_url'];
      $rowID = $_POST['data_id'];

      $inputData = ['Відео сесії' => $videoLink, 'Рішенні сесії' => $solutionsLink, 'ID' => $rowID];
      $accept = 0;
      foreach ($inputData as $key => $value) {
         if (!empty($value)) {
            if ($key == 'ID') {
               $validateLink = preg_match('/^[1-9][0-9]*$/', $value);
            } else {
               $validateLink = preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value);
            }
            if ($validateLink !== 1) {
               $accept = 1;
               $message = ['message' => 'Помилка в посиланні на ' . $key, 'code' => 'error'];
               echo json_encode($message);
               die;
            }
         }
      }
      if ($accept == 0) {
         $tableName = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
         $changeRowUpdate = $wpdb->update($tableName, array('ok_video_url' => $videoLink, 'ok_solution_link' => $solutionsLink), array('ID' => $rowID));

         if ($changeRowUpdate === false) {
            $message = ['message' => 'Помилка при збереженні', 'code' => 'error'];
            echo json_encode($message);
         } else {
            $message = ['message' => 'Зміни збережено', 'code' => 'accept'];
            echo json_encode($message);
         }
      }
      die;
   }

   public static function okAdminSubtableGolos()
   {
      global $wpdb;
      $numSession = $_POST['data_session'];
      $numConvocation = $_POST['data_convocation'];
      $validationNumSession =  preg_match('/^[1-9][0-9]*$/', $numSession);
      if (!$validationNumSession) {
         die;
      }
      $validationNumConvocation =  preg_match('/^[1-9][0-9]*$/', $numConvocation);
      if (!$validationNumConvocation) {
         die;
      }
      if (isset($_POST['doc_id'])) {
         $docID = $_POST['doc_id'];
         $validateDoc_ID = preg_match("/[0-9]/u", $docID);
         if (!$validateDoc_ID) {
            die('Помилка валідації (document id)');
         }
      }

      //$nonce = $_POST['nonce'];
      //$nonceKey = 'khor_golos_table_file_' . $_POST['doc_id'];
      //self::okNonceValidation($nonce, $nonceKey)


      $tableName = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
      $dbAdminSubtable = $wpdb->get_results("SELECT * FROM $tableName  WHERE `ok_num_session` = $numSession AND `ok_num_convocation` = $numConvocation");
      $rowcount = 0;
      $adminSubtable = '';
      foreach ($dbAdminSubtable as $valueSubtable) {
         $pdnpp = $valueSubtable->ok_pdnpp;
         $pdName = stripslashes($valueSubtable->ok_pd_name);
         $idRow = $valueSubtable->id;
         $glName = stripslashes($valueSubtable->ok_gl_text);
         $glNum = $valueSubtable->ok_gl_number;
         $glLink = $valueSubtable->ok_link;
         $glYesGolos = $valueSubtable->ok_yes_count;
         $glNoGolos = $valueSubtable->ok_no_count;
         $glUtrGolos = $valueSubtable->ok_utr_count;
         $glNgGolos = $valueSubtable->ok_ng_count;
         $glAllGolos = $valueSubtable->ok_total_count;
         $glResultGolos = $valueSubtable->ok_result;
         $rowcount++;

         $adminSubtable .=  '<tr data-id = ' . $idRow . ' class="admin_subtable">
                               <td>' . $rowcount . '</td>
                               <td colspan="8" class="admin_subtable-sessionnum">' . $pdnpp . '</td>
                               <td class="admin_subtable-golosname">
                                   <div class="pdname_content">' . $pdName . '</div</td>
                               <td class="admin_subtable-golossubname">
                                   <div class="glname_content">' . $glName . '</div></td>
                               <td class="admin_subtable-golossublink">'
            . self::okAdminSubtableAddFileGolos($glLink) .
            '</td>
                               <td class="admin_subtable-active-button">
                                   <a class="admin_subtable-trigger" href="javascript:void(0)" title="Змінити">
                                       <i class="fas fa-edit"></i>
                                   </a>
                                   <a class="admin_subtable-see-button" data-url="' . $glLink . '" href="javascript:void(0)" title="Переглянути">
                                       <i class="fas fa-eye"></i>
                                   </a>
                                   <div class="stack-button-active"> 
                                   <a class="admin_subtable-save-button" href="javascript:void(0)" title="Зберегти">
                                       <i class="fas fa-save"></i>
                                   </a>
                                   <a class="admin_subtable-cancel-button" href="javascript:void(0)" title="Відмінити">
                                       <i class="fas fa-times"></i>
                                   </a>
                                   </div>
                               </td>
                           </tr>
                           <tr class="admin_subtable-meta" data-gl="' . $glNum . '" data-pd="' . $pdnpp . '" data-ses="' . $numSession . '">
                               <td colspan="13">
                                   <div class="admin_subtable-info-wrpapper">
                                       <div class="admin_subtable-info__link admin_subtable-info__link-yes" data-class="admin_subtable-yes-golos"' . self::okCheckEmptyValueSubtable($glYesGolos) . '><span>За: </span>' . $glYesGolos . '</div>
                                       <div class="admin_subtable-info__link admin_subtable-info__link-no" data-class="admin_subtable-no-golos"' . self::okCheckEmptyValueSubtable($glNoGolos) . '><span>Проти: </span>' . $glNoGolos . '</div>
                                       <div class="admin_subtable-info__link admin_subtable-info__link-utr" data-class="admin_subtable-utr-golos"' . self::okCheckEmptyValueSubtable($glUtrGolos) . '><span>Утримались: </span>' . $glUtrGolos . '</div>
                                       <div class="admin_subtable-info__link admin_subtable-info__link-ng" data-class="admin_subtable-ng-golos"' . self::okCheckEmptyValueSubtable($glNgGolos) . '><span>Не голосували: </span>' . $glNgGolos . '</div>
                                       <div class="admin_subtable-info__link admin_subtable-info__link-all" data-class="admin_subtable-all-golos"' . self::okCheckEmptyValueSubtable($glAllGolos) . '><span>Всього голосів: </span>' . $glAllGolos . '</div>
                                       <div class="admin_subtable-info__link admin_subtable-info__link-result" data-class="admin_subtable-result-golos">' . $glResultGolos . '</div>
                                   </div>
                           </tr>';
      }
      echo $adminSubtable;
      die();
   }

   public static function okCheckEmptyValueSubtable($value)
   {
      if ($value == 0) {
         return 'data-empty = 1';
      } else {
         return 'data-empty = 0';
      }
   }

   public static function okAdminSubtableSave()
   {
      global $wpdb;
      $editPdname = addslashes($_POST['pdname']);
      $editGlname = addslashes($_POST['glname']);
      $editGllink = $_POST['gllink'];
      $editRowId = $_POST['row_id'];

      self::okAdminSubtableValidation($editPdname, $editGlname, $editGllink, $editRowId);

      $tableName = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
      $changeRowUpdate = $wpdb->update($tableName, array('ok_pd_name' => $editPdname, 'ok_gl_text' => $editGlname, 'ok_link' => $editGllink), array('ID' => $editRowId));

      if ($changeRowUpdate !== false) {
         echo 'Зміни збережено';
      } else {
         echo 'Помилка при збереженні';
      }

      die();
   }

   private static function okAdminSubtableValidation($pdName, $glName, $glLink, $rowID)
   {
      if (!empty($pdName)) {
         $validatePdName = preg_match("/^[а-яА-ЯїЇіІєЄ0-9\.\#\№\'\"\\\«\»\s\–\-\(\)VCIMLXD,’:]+$/u", $pdName);
         if ($validatePdName !== 1) {
            die('Недопустимі символи в назві голосування');
         }
      } else {
         die('Введіть назву голосування');
      }

      if (!empty($glName)) {
         $validateGlName = preg_match("/^[а-яА-ЯїЇіІєЄ0-9\.\#\№\'\"\\\«\»\s\–\-\(\)VCIMLXD,’:]+$/u", $glName);
         if ($validateGlName !== 1) {
            die('Недопустимі символи в скороченій назві голосування');
         }
      } else {
         die('Введіть скорочену назву голосування');
      }

      if (!empty($glLink)) {
         $validateLink = preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $glLink);
         if ($validateLink !== 1) {
            die('Некоректні символи в посиланні');
         }
      }

      if (!empty($rowID)) {
         $validateRowID = preg_match("/[0-9]/", $rowID);
         if ($validateRowID !== 1) {
            die('Некоректні символи в id');
         }
      } else {
         die('Помилка, не передані id');
      }
   }

   public static function okAdminSubtableMetaClickShow()
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

         if (!$validationAjaxSubtableMetaSession || !$validationAjaxSubtableMetaPdName || !$validationAjaxSubtableMetaGlName || !$validationAjaxSubtableMetaConvocation) {
            die;
         }
      } else {
         die;
      }

      $tableAllDeput = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';
      $tableGolosDeput = $wpdb->get_blog_prefix() . 'ok_khor_golos_deput';

      $queryDb = "SELECT $tableAllDeput.`ok_dp_name`, $tableGolosDeput.`ok_dp_golos` FROM $tableAllDeput INNER JOIN $tableGolosDeput ON $tableGolosDeput.`ok_dp_id` = $tableAllDeput.`id` AND $ajaxSubtableMetaSession = $tableGolosDeput.`ok_num_session` AND $ajaxSubtableMetaConvocation = $tableGolosDeput.`ok_num_convocation` AND $ajaxSubtableMetaPdName = $tableGolosDeput.`ok_num_rishennya` AND $ajaxSubtableMetaGlName = $tableGolosDeput.`ok_gl_number` AND $tableGolosDeput.`ok_dp_golos` LIKE ";

      switch ($ajaxActionTypeGolos) {
         case 'admin_subtable_ajax_yes_golos':
            $typeGolos = 'ЗА';
            $adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
            $jsonConvert = json_encode($adminSubtableAjaxGolos);
            print_r($jsonConvert);
            break;
         case 'admin_subtable_ajax_no_golos':
            $typeGolos = 'ПРОТИ';
            $adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
            $jsonConvert = json_encode($adminSubtableAjaxGolos);
            print_r($jsonConvert);
            break;
         case 'admin_subtable_ajax_utr_golos':
            $typeGolos = 'УТРИМАВСЯ';
            $adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
            $jsonConvert = json_encode($adminSubtableAjaxGolos);
            print_r($jsonConvert);
            break;
         case 'admin_subtable_ajax_ng_golos':
            $typeGolos = 'НЕ ГОЛОСУВАВ';
            $adminSubtableAjaxGolos = $wpdb->get_results("$queryDb" . "'%$typeGolos%'");
            $jsonConvert = json_encode($adminSubtableAjaxGolos);
            print_r($jsonConvert);
            break;
         case 'admin_subtable_ajax_all_golos':
            $adminSubtableAjaxGolos = $wpdb->get_results("SELECT $tableAllDeput.`ok_dp_name`, $tableGolosDeput.`ok_dp_golos` FROM $tableAllDeput INNER JOIN $tableGolosDeput ON $tableGolosDeput.`ok_dp_id` = $tableAllDeput.`id` AND $ajaxSubtableMetaSession = $tableGolosDeput.`ok_num_session` AND $ajaxSubtableMetaConvocation = $tableGolosDeput.`ok_num_convocation` AND $ajaxSubtableMetaPdName = $tableGolosDeput.`ok_num_rishennya` AND $ajaxSubtableMetaGlName = $tableGolosDeput.`ok_gl_number`");
            $jsonConvert = json_encode($adminSubtableAjaxGolos);
            print_r($jsonConvert);
            break;
         default:
            echo "Помилка при відображенні";
      }
      die;
   }

   public static function okAdminSubtableAddFileGolos($link)
   {
      $showUpload = '<div class="golos-file-upload-form" method="post">
                        <input type="hidden" name="action_golos_upload_file" value="khor_golos_admin_file_upload" />
                        <div class="admin_subtable_link_media_wrapper">
                           <div class="admin_subtable_link_media_icon">+</div>
                           <input class="admin_subtable_link_media" type="button" name="file_link_upload_from_media">
                        </div> 
                        <input disabled class="admin_subtable_link" type="url" name="file_link" placeholder="Посилання" value="' . $link . '">
                        <button class="remove_file_golos_button button">×</button>
                     </div>';

      return $showUpload;
   }
}
