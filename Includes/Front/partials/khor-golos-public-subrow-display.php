<?php
$count = 0;
foreach ($show_info_repeat_rishennya as $value) {
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
   <tr data-convocation="<?php echo $num_convocation ?>" data-glnum="<?php echo $gl_number ?>" data-session="<?php echo $num_session ?>" data-golos="<?php echo $pdnpp ?>" class="main_row_session">
      <td class="id_session"><?php echo $pdnpp ?></td>
      <td class="name_session"><span><?php self::okClearSlashesText($pd_name);
                                       echo $idRow; ?></span>
         <?php if (self::okGetFileLink($idRow)) {
            echo "<a class='khor_golos_download_file' target='_blank' link='" . self::okGetFileLink($idRow) . "'>Переглянути <i class='fas fa-eye'></i></a>";
         }
         ?>
      </td>
      <td class="num_session"><?php echo \Inc\Khor_Golos_BaseController::okConvertToRomeDigit($num_session) ?></td>
      <td class="time_session"><?php self::okTrimDate($gl_time) ?></td>
      <td style="width: 28%;" class="result_session">
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
      <td colspan="3" class="result_rish_session <?php self::okColorText($result) ?>"><?php echo $result ?></td>
   </tr>
<?php } ?>