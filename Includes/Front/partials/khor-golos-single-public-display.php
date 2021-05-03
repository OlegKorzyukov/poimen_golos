<?php





/**

 * Provide a public-facing view for the plugin

 *

 * This file is used to markup the public-facing aspects of the plugin.

 *

 * @link       http://example.com

 * @since      1.0.0

 *

 * @package    Khor_Golos

 * @subpackage Khor_Golos/public/partials

 */

?>




<?php $giveFromDB = self::okGiveFromDB($atts) ?>



<div class="khor-golos-public-table-wrapper" data-nonce="<?php echo self::okNonceFieldBuilder($atts['session']) ?>">

   <?php if (self::okGetLinkVideo($atts)) echo self::okGetLinkVideo($atts); ?>

   <?php if (self::okGetLinkSolution($atts)) echo self::okGetLinkSolution($atts);  ?>

   <?php Inc\Khor_Golos_BaseController::okAddBreadcrumbInTitlePageGolos(); ?>

   <div class='head-info-table'>

      <div class='head-info-table-part session-head-num'>

         <div class="golos-head-title">Сесія</div>

         <div class="golos-head-result"><span><?php echo \Inc\Khor_Golos_BaseController::okConvertToRomeDigit($atts['session']) ?></span></div>

      </div>

      <div class='head-info-table-part convocation-head-num'>

         <div class="golos-head-title">Скликання</div>

         <div class="golos-head-result"><span><?php echo \Inc\Khor_Golos_BaseController::okConvertToRomeDigit($atts['convocation']) ?></span></div>

      </div>

      <div class='head-info-table-part question-head-num'>

         <div class="golos-head-title">Всього питань</div>

         <div class="golos-head-result"><span><?php echo self::okGetAllQuestion($giveFromDB) ?></span></div>

      </div>

      <div class='head-info-table-part question-head-deput'>

         <div class="golos-head-title">Присутність</div>

         <div class="golos-head-result"><span><?php echo self::okGetTableHeaderDeputPresence($atts) ?></span></div>

      </div>

      <div class='head-info-table-part question-head-accept'>

         <div class="golos-head-title">Прийнято питань</div>

         <div class="golos-head-result"><span><?php echo self::okGetAcceptQuestion($giveFromDB) ?></span></div>

      </div>

      <div class='head-info-table-part question-head-decline'>

         <div class="golos-head-title">Не прийнято питань</div>

         <div class="golos-head-result"><span><?php echo self::okGetDeclineQuestion($giveFromDB) ?></span></div>

      </div>

   </div>



   <div class="main_pkhor_golos_content">

      <table data-locale="uk-UA" data-toggle="table" data-sort-class="khor_golos_sorted" data-search="true" data-show-columns="true" data-mobile-responsive="true" data-resizable="true" data-advanced-search="true" data-detail-view="true" data-detail-formatter="detailFormatter" data-show-export="true" data-pagination="true" data-trim-on-search="false" data-page-list="[10, 25, 50, 100]" data-pagination-v-align="both" data-show-pagination-switch="true" data-export-types="['pdf','doc','excel']" class="khor_golos_table">

         <thead>

            <tr>

               <th data-sortable="true" data-field="id">№</th>

               <th data-sortable="true" class="name_question" data-field="name">Назва питання</th>

               <th data-field="num_session">Сесія</th>

               <th data-field="date_session">Дата</th>

               <th data-field="result_golos">Голосування</th>

               <th data-sortable="true" data-field="result_session">Результат</th>

            </tr>

         </thead>

         <tbody>

            <?php

            $count = 0;

            foreach ($giveFromDB as $value) {

               $idRow = $value->id;

               $num_session = $value->ok_num_session;

               $num_convocation = $value->ok_num_convocation;

               $gl_number = $value->ok_gl_number;

               $pdnpp = $value->ok_pdnpp;

               $pd_name = $value->ok_pd_name;

               $gl_time = $value->ok_gl_time;

               $yes_count = $value->ok_yes_count;

               $no_count = $value->ok_no_count;

               $utr_count = $value->ok_utr_count;

               $ng_count = $value->ok_ng_count;

               $total = $value->ok_total_count;

               $result = $value->ok_result;

            ?>

               <tr data-id="<?php echo $idRow ?>" data-glnum="<?php echo $gl_number ?>" data-convocation="<?php echo $num_convocation ?>" data-session="<?php echo $num_session ?>" data-golos="<?php echo $pdnpp ?>" class="main_row_session <?php if ($gl_number == 1) echo 'without-subrow'; ?>">

                  <td class="id_session"><?php echo $pdnpp ?></td>

                  <td class="name_session"><span><?php self::okClearSlashesText($pd_name);

                                                   echo $idRow ?></span>

                     <?php if (self::okGetFileLink($idRow)) {

                        echo "<a class='khor_golos_download_file' target='_blank' link='" . self::okGetFileLink($idRow) . "'>Переглянути <i class='fas fa-eye'></i></a>";

                     }

                     ?>

                  </td>

                  <td class="num_session"><?php echo \Inc\Khor_Golos_BaseController::okConvertToRomeDigit($num_session) ?></td>

                  <td class="time_session"><?php self::okTrimDate($gl_time) ?></td>

                  <td class="result_session">

                     <div class="row result_session_main">

                        <div class="row result_session_row_yes" data-golos="golos-yes" <?php self::okCheckEmptyValueSubtable($yes_count) ?>>

                           <div class="col-8">За</div>

                           <div class="col-4 result_count"><?php echo $yes_count ?></div>

                        </div>

                        <div class="row result_session_row_no" data-golos="golos-no" <?php self::okCheckEmptyValueSubtable($no_count) ?>>

                           <div class="col-8">Проти</div>

                           <div class="col-4 result_count"><?php echo $no_count ?></div>

                        </div>

                        <div class="row result_session_row_ng" data-golos="golos-ng" <?php self::okCheckEmptyValueSubtable($utr_count) ?>>

                           <div class="col-8">Не голосували</div>

                           <div class="col-4 result_count"><?php echo $utr_count ?></div>

                        </div>

                        <div class="row result_session_row_utr" data-golos="golos-utr" <?php self::okCheckEmptyValueSubtable($ng_count) ?>>

                           <div class="col-8">Утримались</div>

                           <div class="col-4 result_count"><?php echo $ng_count ?></div>

                        </div>

                        <div class="row result_session_row_all" data-golos="golos-all" <?php self::okCheckEmptyValueSubtable($total) ?>>

                           <div class="col-8">Всього</div>

                           <div class="col-4 result_count"><?php echo $total ?></div>

                        </div>

                     </div>

                  </td>

                  <td class="result_rish_session <?php self::okColorText($result) ?>"><?php echo $result ?></td>

               </tr>

            <?php } ?>



         </tbody>

      </table>

   </div>



</div>