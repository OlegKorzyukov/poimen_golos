<div class="ok-add-new-deput-show">
   <div class="ok-add-new-deput-form-wrapper">
      <form action="" method="POST" name="add-new-deput" class="ok-add-new-deput-form-window">
         <div class="ok-add-new-deput-form-group-input">
            <div class="wrap-deput-upload">
               <div class="deput-photo-wrapper">
                  <div>Фото</div>
                  <img class="deput-golos-photo" id="1" data-src="" src="">
                  <div class="deput-photo_input_group photo-input-wrapper" style="display: block;">
                     <button type="submit" class="upload_image_button button">+</button>
                     <button type="submit" class="remove_image_button button">Очистити</button>
                  </div>
               </div>
            </div>
            <input type="text" required name="add-new-deput_name" placeholder="ПІБ">
            <input type="text" name="add-new-deput_fraction" placeholder="Фракція">
            <input type="text" required name="add-new-deput_convocation" placeholder="Скликання">
            <input type="date" name="add-new-deput_birthday" placeholder="Дата народження">
            <input type="text" name="add-new-deput_position" placeholder="Посада">
            <input type="text" name="add-new-deput_commission" placeholder="Участь в комісії">
            <textarea maxlength="1000" name="add-new-deput_info" placeholder="Інформація"></textarea>
            <input type="button" class="ok-add-new-deput-form-window_submit" value="Зберегти">
         </div>
      </form>
   </div>
</div>

<div class="show_deput_table">
   <div class="ok-add-new-deput">
      <button>Додати нового депутата</button>
   </div>
   <?php
   $dataConvocation = Inc\Admin\Khor_Table_Constructor::okGetFromDb('ok_khor_all_deput');
   foreach ($dataConvocation as $key => $meta) {
   ?>
      <div class="ok-deput-table-admin">
         <div class="ok-deput-title-wrapper">
            <div class="ok-show-table">
               <h3 class="ok-title-deput-convocation">Депутати <?php print_r($meta->ok_dp_convocation) ?> скликання</h3>
               <div class="ok-title-arrow"><img src="../wp-content/plugins/khor-golos/assets/images/down-arrow.svg" alt=""></div>
            </div>
         </div>
         <table data-locale="uk-UA" data-toggle="table" data-sort-class="khor_golos_sorted" data-search="true" data-mobile-responsive="true" data-show-columns="true" data-show-print="true" data-resizable="true" data-advanced-search="true" data-show-columns-toggle-all="true" data-detail-view="false" data-pagination="true" data-detail-formatter="detailFormatter" data-show-export="true" data-trim-on-search="false" id="khor_golos_deput_table">
            <thead>
               <tr>
                  <th data-sortable="true" data-field="dp_id">№</th>
                  <th data-sortable="true" data-field="dp_name">ПІБ</th>
                  <th data-sortable="true" data-field="dp_fraction">Фракція</th>
                  <th data-field="dp_convocation">Скликання</th>
                  <th data-sortable="true" data-field="dp_birthday">Дата народження</th>
                  <th data-field="dp_position">Посада</th>
                  <th data-sortable="true" data-field="dp_comission">Участь в комісії</th>
                  <th data-events="operateEvents" data-field="dp_info">Інформація</th>
                  <th data-events="operateEvents" data-field="dp_photo">Фото</th>
                  <th data-events="operateEvents" data-field="dp_change">Змінити</th>
               </tr>
            </thead>
            <tbody>
               <?php $rowcount = 0; ?>
               <?php foreach (self::okGetDeputyList($meta->ok_dp_convocation) as $deputy) {
                  $rowcount++;
               ?>
                  <tr id='khor_golos_<?php echo $deputy->id ?>' class='admin_deputy'>
                     <td class="deput-count"><?php echo  $rowcount ?></td>
                     <td class="deput-name"><?php echo  $deputy->ok_dp_name ?></td>
                     <td class="deput-fraction"><?php echo  $deputy->ok_dp_fraction ?></td>
                     <td class="deput-convocation"><?php echo  $deputy->ok_dp_convocation ?></td>
                     <td class="deput-birthday"><input disabled type="date" name="add-new-deput_birthday" placeholder="Дата народження" value="<?php echo  $deputy->ok_dp_birthday ?>"></td>
                     <td class="deput-position"><?php echo  $deputy->ok_dp_position ?></td>
                     <td class="deput-comission"><?php echo  $deputy->ok_dp_commission ?></td>
                     <td class="deput-main-info"><?php echo  $deputy->ok_dp_info ?></td>
                     <td class=""><?php echo  self::okUploadDeputPhoto($deputy->id, $deputy->ok_dp_photo) ?></td>
                     <td class="admin_subtable-active-button">
                        <a class="deput-change-button" href="javascript:void(0)" title="Змінити">
                           <i class="fas fa-edit"></i>
                        </a>
                        <div class="stack-button-active-deputy">
                           <a class="admin_deputy-save-button" href="javascript:void(0)" title="Зберегти">
                              <i class="fas fa-save"></i>
                           </a>
                           <a class="admin_deputy-cancel-button" href="javascript:void(0)" title="Відмінити">
                              <i class="fas fa-times"></i>
                           </a>
                        </div>
                     </td>
                  </tr>
               <?php } ?>
            </tbody>
         </table>
      </div>
   <?php } ?>
</div>