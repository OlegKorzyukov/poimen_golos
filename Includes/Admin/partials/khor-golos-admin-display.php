<div class="ok-wrap">
    <div class="ok-head-selector-upload-file">
        <span class="ok-head-selector-upload-file__poimen-golos ok-active" show="ok-upload_file">Завантажити поіменне голосування</span>
        <span class="ok-head-selector-upload-file__upload-docs" show="ok-upload-result-file">Завантажити документи голосування</span>
    </div>
    <div class="ok-upload_file ok-toggle-active">
        <h3>Завантаження поіменного голосування (*JSON)</h3>
        <form action="<?php /*echo esc_url(admin_url('admin-post.php'));*/ ?>" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('action_upload_file', 'golos_upload_file'); ?>
            <input type="hidden" name="action" value="golos_upload_file" />
            <div class="row upload-input-wrapper">
                <div class="upload-file-input-wrapper">
                    <input class="upload-file-input" type="file" name="golos_upload_file" accept='.json' required />
                    <div class="upload-file-text">Натисніть або перемістіть файл для завантаження</div>
                </div>
                <div class="field-num-session-wrapper">
                    <div class="field-num-convocation-wrapper">
                        <input class="field-num-session" type="text" name="golos_num_session" placeholder="№ Сесії" required />
                        <input class="field-num-convocation" type="text" name="golos_num_convocation" placeholder="№ Скликання" required />
                    </div>
                    <input class="submit-file-upload" type="submit" value="Завантажити файл" />
                </div>
            </div>
        </form>
    </div>
    <div class="ok-upload-result-file">
        <h3>Завантажити документи результатів голосування (*ZIP)</h3>
        <form action="<?php /*echo esc_url(admin_url('admin-post.php'));*/ ?>" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('action_upload_file', 'golos_upload_file');
            ?>
            <input type="hidden" name="action" value="golos_upload_result_file" />
            <div class="row upload-input-wrapper">
                <div class="upload-file-input-wrapper">
                    <input class="upload-file-input" type="file" name="golos_upload_result_file" accept='application/zip' required />
                    <div class="upload-file-text">Натисніть або перемістіть файл для завантаження</div>
                </div>
                <div class="field-num-session-wrapper">

                    <select class="golos_upload_result_field-num-convocation" name="result_golos_num_convocation" required>
                        <option value="" disabled selected>№ Скликання</option>
                        <?php Inc\Admin\Khor_Table_Constructor::okGetNumberConvocation() ?>
                    </select>
                    <select disabled class="golos_upload_result_field-num-session" name="result_golos_num_session" required>
                        <option value="" disabled selected>№ Сесії</option>
                    </select>
                    <input class="submit-file-upload" type="submit" value="Завантажити файл" />
                </div>
            </div>
        </form>
    </div>
    <?php
    if (isset($_POST["action"]) && $_POST["action"] == 'golos_upload_file') {
        //add_action('admin_post_golos_upload_file', new Inc\Admin\Khor_Golos_Upload_File());
        $uploadFile =  new Inc\Admin\Khor_Golos_Upload_File();
        $uploadFile->init();
    }
    if (isset($_POST["action"]) && $_POST["action"] == 'golos_upload_result_file') {
        $uploadFile =  new Inc\Admin\Khor_Golos_Upload_Result_File();
        $uploadFile->init();
    }
    Inc\Admin\Khor_Table_Constructor::okShowUploadHistoryTable();
    ?>

</div>