<?php



namespace Inc\Admin;



use Inc\Admin\Khor_Golos_Upload_File;



class Khor_Golos_Parse_File

{

    private $numsession;

    private $convocation;



    public function __construct($numsession, $convocation)

    {

        $this->numsession = $numsession;

        $this->convocation = $convocation;

        $this->okParseGolosFile();

    }



    // Parse poimen golos file 

    private function okParseGolosFile()

    {

        $path = Khor_Golos_Upload_File::TARGET_DIR . Khor_Golos_Upload_File::okRenameUploadFile($this->numsession, $this->convocation);



        if (!file_exists($path) || !is_writable($path)) {

            exit;

        }



        $file = file_get_contents($path);

        $date_events = json_decode($file, TRUE, JSON_UNESCAPED_UNICODE);



        $docTime = $date_events['DocTime'];

        $this->okSaveDocTimeParseFile($docTime);

        foreach ($date_events as $value) {



            if (is_array($value) || is_object($value)) {

                foreach ($value as $key => $pd_value) {



                    $pdnpp = $pd_value['PDNPP'];

                    $pdName = $pd_value['PDName'];

                    $pdName = addslashes($pdName);



                    foreach ($pd_value as $gllist) {

                        if (is_array($gllist) || is_object($gllist)) {



                            foreach ($gllist as $glvalue) {



                                if (is_array($glvalue) || is_object($glvalue)) {



                                    $glNumber = $glvalue['GLNumber'];

                                    $glType = $glvalue['GLType'];

                                    $glTime = $glvalue['GLTime'];

                                    $glText = $glvalue['GL_Text'];

                                    $glText = addslashes($glText);

                                    $glText = trim($glText);

                                    $glResultType = $glvalue['GL_ResultType'];

                                    $yesCnt = $glvalue['YESCnt'];

                                    $noCnt = $glvalue['NOCnt'];

                                    $utrCnt = $glvalue['UTRCnt'];

                                    $ngCnt = $glvalue['NGCnt'];

                                    $totalCnt = $glvalue['TotalCnt'];

                                    $result = $glvalue['RESULT'];

                                    $result = trim($result);



                                    $this->okSaveGLParseFile($this->numsession, $this->convocation, $glNumber, $glType, $glTime, $glText, $glResultType, $yesCnt, $noCnt, $utrCnt, $ngCnt, $totalCnt, $result, $pdnpp, $pdName);



                                    foreach ($glvalue as $dpvalue) {

                                        if (is_array($dpvalue) || is_object($dpvalue)) {

                                            foreach ($dpvalue as $dpinfo) {



                                                $dpName = $dpinfo['DPName'];

                                                $dpGolos = $dpinfo['DPGolos'];

                                                $this->okSaveDPParseFile($dpName, $dpGolos, $pdnpp, $glNumber);

                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

    }



    private function okSaveDocTimeParseFile($docTime)

    {

        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_files';

        $save_doc_time_parse = $wpdb->update(

            $table_name,

            array(

                'ok_file_create' => $docTime,

            ),

            array('ok_filename' => Khor_Golos_Upload_File::okRenameUploadFile($this->numsession, $this->convocation)),

            array('%s')

        );

        /* if (!$save_doc_time_parse) {

            echo "Перевірте номер сесії";

        }*/

    }



    private function okSaveGLParseFile($numsession, $convocation, $glNumber, $glType, $glTime, $glText, $glResultType, $yesCnt, $noCnt, $utrCnt, $ngCnt, $totalCnt, $result, $pdnpp, $pdName)

    {

        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_rishennya';



        $save_gl_parse = $wpdb->insert(

            $table_name,

            array(

                'ok_num_session' => $numsession,

                'ok_num_convocation' => $convocation,

                'ok_pdnpp' => $pdnpp,

                'ok_pd_name' => $pdName,

                'ok_gl_number' => $glNumber,

                'ok_gl_type' => $glType,

                'ok_gl_result_type' => $glResultType,

                'ok_gl_text' => $glText,

                'ok_gl_time' => self::okConvertGLDateToSQLDate($glTime),

                'ok_yes_count' => $yesCnt,

                'ok_no_count' => $noCnt,

                'ok_utr_count' => $utrCnt,

                'ok_ng_count' => $ngCnt,

                'ok_total_count' => $totalCnt,

                'ok_result' => $result,

            ),

            array('%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%s')

        );

    }



    private function okSaveDPParseFile(string $dpName, string $dpGolos, string $pdnpp, int $glNumber)

    {

        global $wpdb;

        $table_name = $wpdb->get_blog_prefix() . 'ok_khor_golos_deput';

        $save_dp_parse = $wpdb->insert(

            $table_name,

            array(

                'ok_num_session' => $this->numsession,

                'ok_num_convocation' => $this->convocation,

                'ok_num_rishennya' => $pdnpp,

                'ok_dp_id' => self::okGetDeputID($dpName),

                'ok_dp_golos' => $dpGolos,

                'ok_gl_number' => $glNumber

            ),

            array('%d', '%d', '%s', '%d', '%s', '%d')

        );

    }



    private static function okGetDeputID(string $dpName)

    {

        global $wpdb;

        $dpnameSlashes = addslashes($dpName);

        $table_name_deput = $wpdb->get_blog_prefix() . 'ok_khor_all_deput';

        $table_old_name = $wpdb->get_blog_prefix() . 'ok_khor_old_name_deput';

        $id_deput = $wpdb->get_results(" SELECT $table_name_deput.`id` FROM $table_name_deput INNER JOIN $table_old_name ON $table_name_deput.`ok_dp_name` LIKE '%$dpnameSlashes%' OR ($table_old_name.`ok_dp_old_name` LIKE '%$dpnameSlashes%' AND $table_name_deput.`id` = $table_old_name.`ok_dp_id`)");



        if ($id_deput) {

            return $id_deput[0]->id;

        } else {

            $wpdb->insert(

                $table_name_deput,

                array('ok_dp_name' => $dpName),

                array('%s')

            );

            return $wpdb->insert_id;

        }

    }



    private static function okConvertGLDateToSQLDate($date)

    {

        $format = 'Y-m-d H-i-s';

        $objDate = new \DateTime($date);

        return $objDate->format($format);

    }

} //class Khor_Golos_Parse_File 