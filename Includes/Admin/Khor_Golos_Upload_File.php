<?php

namespace Inc\Admin;

use \Inc\Admin\Khor_Table_Constructor;
use \Inc\Khor_Golos_BaseController;

class Khor_Golos_Upload_File
{
    const TARGET_DIR = WP_PLUGIN_DIR  . '/khor-golos/files/';
    const TARGET_URL = WP_PLUGIN_URL . '/khor-golos/files/';

    protected $filename;
    protected $filetype;
    protected $filetmpname;
    protected $fileerror;
    protected $filesize;
    public $numsession;
    public $convocation;

    public function init()
    {
        $this->filename = $_FILES["golos_upload_file"]["name"];
        $this->filetype = $_FILES["golos_upload_file"]['type'];
        $this->filetmpname = $_FILES["golos_upload_file"]['tmp_name'];
        $this->fileerror = $_FILES["golos_upload_file"]['error'];
        $this->filesize = $_FILES["golos_upload_file"]['size'];
        $this->numsession = $_POST['golos_num_session'];
        $this->convocation = $_POST['golos_num_convocation'];

        $this->okPostDataValidation($this->filename, $this->filetype, $this->filetmpname, $this->fileerror, $this->filesize, $this->numsession, $this->convocation);
        $this->okSaveFile($this->filetmpname, $this->numsession, $this->convocation);
        $this->okInsertUploadHisoryDB($this->numsession, $this->filesize, $this->convocation);

        new Khor_Golos_Parse_File($this->numsession, $this->convocation);

        $this->okCreateConvocationNumberPage();
        $this->okCreateSinglePageForUploadGolos();
    }

    private static function okTimeUploadFileUTC()
    {
        $timestamp = time();
        return $timestamp;
    }

    private static function okTimeUploadFile()
    {
        $tz = 'Europe/Kiev';
        $timeobject = new \DateTime("@" . self::okTimeUploadFileUTC());
        $timeobject->setTimezone(new \DateTimeZone($tz));
        $timeshow = $timeobject->format('Y-m-d H-i-s');
        return $timeshow;
    }

    public static function okRenameUploadFile($numsession, $convocation)
    {
        $filerename = $numsession . '_session_' . $convocation . '_' . self::okTimeUploadFile() . '.json';
        return $filerename;
    }

    //URL Path for upload files
    public static function okDownloadFile($filename)
    {
        $downloadfile = self::TARGET_URL . $filename;
        return $downloadfile;
    }

    private function okPostDataValidation($filename, $filetype, $filetmpname, $fileerror, $filesize, $numsession, $convocation)
    {

        $dataValidation = new \WP_Error;

        //Check size upload file (> 10MB)
        if ($filesize > 10485760) {
            $dataValidation->add('max_size_error', 'Розмір файла перевищує допустимий (10 MB)');
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
        if ($filetype != 'application/json') {
            $dataValidation->add('type_file_error', 'Помилка в типі файла (повинен бути json)');
        }

        // Check for repeat number session
        foreach (glob(self::TARGET_DIR . $numsession . "_session_" . $convocation . "*.json") as $docFile) {
            if ($docFile) {
                $dataValidation->add('repeat_session_error', 'Такий номер сесії вже був завантажений (видаліть стару версію)');
                break;
            }
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

    // Save upload file session in directory
    protected function okSaveFile($filetmpname, $numsession, $convocation)
    {
        if (is_writable(self::TARGET_DIR)) {
            $move = move_uploaded_file($filetmpname, self::TARGET_DIR . self::okRenameUploadFile($numsession, $convocation));
            if (!$move) {
                echo 'Помилка при завантаженні файлу' . '<br>';
                die;
            } else {
                echo 'Файл був успішно завантажений' . '<br>';
            }
        } else {
            echo 'Не існує директорії для збереження файлу';
        }
    }

    // Save info for upload file in DB
    private function okInsertUploadHisoryDB($numsession, $filesize, $convocation)
    {
        global $wpdb;
        $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
        $save_info_upload = $wpdb->insert(
            $table_name,
            array(
                'ok_filename' => self::okRenameUploadFile($numsession, $convocation),
                'ok_num_session' => $numsession,
                'ok_num_convocation' => $convocation,
                'ok_filesize' => $filesize,
                'ok_date_upload' => self::okTimeUploadFile(),
                'ok_date_upload_utc' => self::okTimeUploadFileUTC(),
            ),
            array('%s', '%d', '%d', '%d', '%s', '%d')
        );

        if ($save_info_upload) {
            $message = 'Дані додані до таблиці';
        } else {
            $message = 'Помилка додавання даних до таблиці';
        }
        echo $message;
    }

    //Create single page for convocation number
    private function okCreateConvocationNumberPage()
    {
        if (self::okCheckConvocationCount($this->convocation)) {
            $new_page_title = Khor_Golos_BaseController::okConvertToRomeDigit($this->convocation) . ' Скликання';
            $new_page_content = '[khor_golos_convocation convocation=' . $this->convocation . ']';
            $new_page_slug = 'sklykannia-golos-' . $this->convocation;
            $new_page_template = ''; //templates page

            $parentID = get_page_by_path('all-poimen-golos');

            if (isset($parentID->ID)) {
                $page_check = get_page_by_path($new_page_slug);
                $new_page = array(
                    'comment_status' => 'closed',
                    'post_type' => 'page',
                    'post_name'      => $new_page_slug,
                    'post_title' => $new_page_title,
                    'post_content' => $new_page_content,
                    'post_status' => 'publish',
                    'post_parent'    => $parentID->ID,
                    'post_author' => 1,
                    'meta_input'     => [
                        'ok_golos_convocation' => $this->convocation,
                    ],
                );

                if (!isset($page_check->ID)) {
                    $new_page_id = wp_insert_post(wp_slash($new_page));

                    if (!empty($new_page_template)) {
                        update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                    }
                }
            }
        }
    }

    //Create single page for loading session golos
    private function okCreateSinglePageForUploadGolos()
    {
        $new_page_title = 'Поіменне голосування ' . Khor_Golos_BaseController::okConvertToRomeDigit($this->numsession) . ' сесії';
        $new_page_content = '[khor_golos_single convocation=' . $this->convocation . ' session=' . $this->numsession . ']';
        $new_page_slug = 'single-poimen-golos-' . $this->numsession;
        $new_page_template = ''; //templates page

        $parentID = get_page_by_path('all-poimen-golos/sklykannia-golos-' . $this->convocation);

        if (isset($parentID->ID)) {
            $page_check = get_page_by_path($new_page_slug);
            $new_page = array(
                'comment_status' => 'closed',
                'post_type' => 'page',
                'post_name'      => $new_page_slug,
                'post_title' => $new_page_title,
                'post_content' => $new_page_content,
                'post_status' => 'publish',
                'post_parent'    => $parentID->ID,
                'post_author' => 1,
                'meta_input'     => ['ok_poimen_golos_session' => $this->numsession],
            );

            if (!isset($page_check->ID)) {
                $new_page_id = wp_insert_post(wp_slash($new_page));

                self::okUpdateMetaInPostWhenAdd($parentID->ID, $this->convocation);

                if (!empty($new_page_template)) {
                    update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                }
            }
        }
    }

    public static function okCheckConvocationCount($convocation)
    {
        global $wpdb;
        $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';
        $resultCount =  $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE `ok_num_convocation` = $convocation");
        $resultCount = $resultCount - 1;

        if ($resultCount == 0) {
            return true;
        } else {
            return false;
        }
    }

    private static function okUpdateMetaInPostWhenAdd($id, $convocation)
    {
        $metaKey = [
            'ok_golos_convocation_all_session' =>  Khor_Golos_BaseController::okGetAllSessionInThisConvocation($convocation),
            'ok_golos_convocation_all_session_question' => Khor_Golos_BaseController::okGetQuestionWithoutRepeatInThisConvocation($convocation),
            'ok_golos_convocation_accept_session_question' => Khor_Golos_BaseController::okGetAcceptQuestionWithoutRepeatInThisConvocation($convocation),
            'ok_golos_convocation_decline_session_question' => Khor_Golos_BaseController::okGetDeclineQuestionWithoutRepeatInThisConvocation($convocation),
            'ok_golos_convocation_average_deput_presence' => Khor_Golos_BaseController::okGetAverageDeputPresence($convocation),
            'ok_golos_convocation_date_min' => Khor_Golos_BaseController::okGetDateInConvocationSession($convocation)['minDate'],
            'ok_golos_convocation_date_max' => Khor_Golos_BaseController::okGetDateInConvocationSession($convocation)['maxDate']
        ];
        foreach ($metaKey as $key => $func) {
            update_post_meta($id, $key, $func);
        }
    }
}//class Khor_Golos_Upload_File
