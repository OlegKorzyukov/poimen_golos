<?php

namespace Inc\Admin;

use \Inc\Admin\Khor_Golos_Upload_File;

class Khor_Golos_Remove_File
{
    private $docID;
    private $filenameTable;
    private $numSes;
    private $numConvocation;

    public function removeAll()
    {
        $this->okValidateRemove();
        $this->okRemoveUploadFile();
        $this->okRemoveSinglePagePoimenGolos();
        $this->okRemoveParentConvocation();
        $this->okRemoveTableRow();
        $this->okRemoveParseFileDB();
    }

    public function okValidateRemove()
    {
        //$dataValidation = new \WP_Error;

        if (isset($_POST['filename'])) {
            $this->filenameTable = $_POST['filename'];
            $validateFilename = preg_match("/[0-9]+(_session_)+([0-9]+_)+([0-9]{4})-+([0-9]{2})-+([0-9]{2})\s+([0-9]{2})-+([0-9]{2})-+([0-9]{2}).+(json)/u", $this->filenameTable);
            if (!$validateFilename) {
                die('Помилка валідації (filename)');
            }
        }
        if (isset($_POST['doc_id'])) {
            $this->docID = $_POST['doc_id'];
            $validateDoc_ID = preg_match("/[0-9]/u", $this->docID);
            if (!$validateDoc_ID) {
                die('Помилка валідації (document id)');
            }
        }
        if (isset($_POST['num_ses'])) {
            $this->numSes = $_POST['num_ses'];
            $validateNumSes = preg_match("/[0-9]/u", $this->numSes);
            if (!$validateNumSes) {
                die('Помилка валідації (number session)');
            }
        }
        if (isset($_POST['convocation'])) {
            $this->numConvocation = $_POST['convocation'];
            $validateNumConvocation = preg_match("/[0-9]/u", $this->numConvocation);
            if (!$validateNumConvocation) {
                die('Помилка валідації (number convocation)');
            }
        }

        $checkNonceRemove = wp_verify_nonce($_POST['nonce'], 'khor_golos_table_file_' . $_POST['doc_id']);
        if (!$checkNonceRemove) {
            die('Помилка верифікації (nonce field)');
        }
    }

    protected function okRemoveUploadFile()
    {
        $url_path = Khor_Golos_Admin::TARGET_DIR . $this->filenameTable;

        if (file_exists($url_path)) {
            unlink($url_path);
            $message = 'Файл видалено->';
        } else {
            $message = 'Помилка при видаленні (файл не знайдено)';
        }
        echo ($message);
    }

    protected function okRemoveTableRow()
    {
        global $wpdb;
        $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
        $delete_table_files = $wpdb->delete($table_name, array('ID' => $this->docID));

        if ($delete_table_files) {
            $message = 'Запис з таблиці golos_files видалено->';
        } else {
            $message = 'Помилка при видаленні данних з таблиці golos_files';
        }
        echo ($message);
    }

    protected function okRemoveParseFileDB()
    {
        global $wpdb;
        $table_name_rishennya = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';
        $table_name_deput = $wpdb->get_blog_prefix() . 'ok_khor_golos_deput';

        $delete_table_rishennya = $wpdb->delete($table_name_rishennya, array('ok_num_session' => $this->numSes, 'ok_num_convocation' => $this->numConvocation));
        $delete_table_deput = $wpdb->delete($table_name_deput, array('ok_num_session' => $this->numSes, 'ok_num_convocation' => $this->numConvocation));

        if ($delete_table_rishennya) {
            $message = 'Запис з таблиці golos_rishennya видалено->';
        } else {
            $message = 'Помилка при видаленні данних з таблиці golos_rishennya';
        }
        echo ($message);

        if ($delete_table_deput) {
            $message = 'Запис з таблиці golos_deput видалено';
        } else {
            $message = 'Помилка при видаленні данних з таблиці golos_deput';
        }
        echo ($message);
    }

    private function okRemoveSinglePagePoimenGolos()
    {
        $parentID = get_page_by_path('all-poimen-golos/sklykannia-golos-' . $this->numConvocation . '/single-poimen-golos-' . $this->numSes);
        $deleted = wp_delete_post($parentID->ID, $force_delete = true);
        if ($deleted != false && $deleted != null) {
            return true;
        }
    }

    public function okRemoveParentConvocation()
    {
        if (Khor_Golos_Upload_File::okCheckConvocationCount($this->numConvocation)) {
            $parentID = get_page_by_path('all-poimen-golos/sklykannia-golos-' . $this->numConvocation);
            $deleted = wp_delete_post($parentID->ID, $force_delete = true);
            if ($deleted != false && $deleted != null) {
                return true;
            }
        }
    }
}//class Khor_Golos_Remove_File