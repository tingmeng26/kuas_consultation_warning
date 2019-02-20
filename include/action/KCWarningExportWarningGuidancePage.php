<?php

class KCWarningExportWarningGuidancePage extends ModuleAdminView implements KuasConsultationActionListener {

    private $tpl = null;
    private $optionModel = null;

    public function __construct() {
        parent::__construct("kuas_consultation_warning");
        $this->tpl = $this->initTemplate();
        $this->warningCommon = new KCWarningCommon();
        set_time_limit(86400);
    }

    /**
     * 取得輔導紀錄畫面 （個人  )
     * @param array  $event ( GET,  POST )
     * @return string  輔導紀錄畫面
     */
    public function actionPerformed($event) {
        $post = $event->getPost();
        $warningStudentId = $post['exportGuidanceStudentId'];
        $warningYear = $post['exportYear'];
        $warningTerm = $post['exportTerm'];
        $warningTermString = $post['termArray'];
        $num = 1;
        if (strpbrk($warningStudentId, ",")) {
            $studentIdString = substr($warningStudentId, 1);
            $warningStudentId = substr($warningStudentId, 1);
        }
        if (strpbrk($warningTermString, ",")) {
            $warningTermStrings = substr($warningTermString, 1);
        }
        $studentIdArray = explode(",", $studentIdString);
        $warningTermArray = explode(",", $warningTermStrings);
        if (count($studentIdArray) > 1) {
            $num = 2;
        }
        $KCConsultationReservationModel = new KCConsultationReservationModel();
        $KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
        $KCConsultationGuidancePersonalModel = new KCConsultationGuidancePersonalModel();
        $KCWarningCommon = new KCWarningCommon(); // 預警共用函數
        require_once('./modules/tcpdf/lib/config/lang/eng.php');
        require_once('./modules/tcpdf/include/tcpdf.inc.php');
        require_once('./modules/tcpdf/lib/tcpdf.php');
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $today = date('Y-m-d');
        // set default header data
        $pdf->SetHeaderData("", "", "" . $today, "");

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        $pdf->setLanguageArray($l);

        // ---------------------------------------------------------
        // set font
        $pdf->SetFont('msungstdlight', '', 16);
        $pdf->SetFontSize(6);

        if ($num == 1) {
            // 在 reservation 表，設定學生帳號
            $KCConsultationReservationModel->setConditionForEqualInStudentUserId((array) $warningStudentId);
            // 在 reservation 表，設定為已出席
            $KCConsultationReservationModel->setConditionForEqualByReservationAttendance("attend");
            // 開始時間，從預警共用函數
            $startDate = $KCWarningCommon->getStartTime($warningYear, $warningTerm);
            // 結束時間，從預警共用函數
            $endDate = $KCWarningCommon->getEndTime($warningYear, $warningTerm);
            // reservation 表，設定時間範圍
            $KCConsultationReservationModel->setConditionForEqualByRangeDate("$startDate", "$endDate");
            // 取得 reservation 的資料
            $reservationData = $KCConsultationReservationModel->getReservationList();
            $KCConsultationReservationModel->clearCondition();
            $kcconsultation = new KCConsultationModel();
            if ($reservationData) {
                // 將 kcconsultation_id 存成陣列
                foreach ($reservationData as $data) {
                    $consultationIdArray[] = $data->kcconsultation_id;
                }
            }
            // 將門診為非自訂門診且符合 kcconsultation_id 的資料抓出來
            if ($consultationIdArray) {
                foreach ($consultationIdArray as $data) {
                    $kcconsultation->setIsNotCustom();
                    $consultationArray[] = $kcconsultation->getConsultationDataByConsultationId($data);
                    $kcconsultation->clearCondition();
                }
            }
            // 因取資料方式為單筆，將不符合上述條件的資料去除
            if ($consultationArray) {
                foreach ($consultationArray as $data) {
                    if ($data) {
                        $isCustomConsultationIdArray[] = $data->kcconsultation_id;
                    }
                }
            }
            // 回 reservation 透過 consultationId 與 學生編號取得 reservationId
            $KCConsultationReservationModel->setConditionForEqualByConsultationId($isCustomConsultationIdArray[0]);
            $KCConsultationReservationModel->setConditionForEqualByStudentUserId($warningStudentId);
            $warningReservationData = $KCConsultationReservationModel->getReservationList();
            $KCConsultationReservationModel->clearCondition();
            // 取得 reservationId
            if ($warningReservationData) {
                foreach ($warningReservationData as $data) {
                    $warningReservationId = $data->kcreservation_id;
                }
            }
            $KCConsultationReservationDetailModel = new KCConsultationReservationDetailModel();
            $detailId = $KCConsultationReservationDetailModel->getReservationDetailByReservationId($warningReservationId);
            $guidanceId = $detailId->kcguidance_id;

            $userId = $warningStudentId;


            $this->guidanceId = $guidanceId;
            $KCUnitModel = new KCUnitModel();
            $KCStudentProfileModel = new KCStudentProfileModel();
            $KCConsultationModel = new KCConsultationModel();
            $KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
            $KCConsultationReservationModel = new KCConsultationReservationModel();
            $KCConsultationGuidanceGroupsModel = new KCConsultationGuidanceGroupsModel();
            $KCConsultationGuidancePersonalModel = new KCConsultationGuidancePersonalModel();
            $KCEmployeeProfileModel = new KCEmployeeProfileModel();
            $getGuidanceData = $KCConsultationGuidanceModel->getGuidanceDataByGuidanceId($guidanceId);
            $KCConsultationGuidanceModel->clearCondition();
            $teacherData = $KCEmployeeProfileModel->getEmployeeDataByUserId($getGuidanceData->kcemployee_user_id);
            $perconalGuidance = $KCConsultationGuidancePersonalModel->getPersonalGuidanceDataByGuidanceId($guidanceId);
            $topic = $KCConsultationModel->getConsultationTopicByKcconsultationId($getGuidanceData->kcconsultation_id)->kcconsultation_topic;
            // 取得學生資料
            $studentData = $KCStudentProfileModel->getStudentDataByUserId($userId);
            $getGuidanceData->kcstudent_name = $studentData->kcstudent_name;
            // 取得學院資料
            $classData = $KCUnitModel->getUnitByUnitId($studentData->kcunit_id);
            $KCUnitModel->clearCondition();
            $unitData = $KCUnitModel->getUnitByUnitId((int) $classData->kcunit_parent);
            $getGuidanceData->unit = $unitData->kcunit_name;
            $getGuidanceData->college = $KCUnitModel->getUnitByUnitId($unitData->kcunit_parent)->kcunit_name;
            // 取得預約紀錄內的班級名稱
            $reservationDatas = $KCConsultationReservationModel->getReservationDataByReservationId($warningReservationId);
            $getGuidanceData->class = $reservationDatas->kcclass_name;
            $getGuidanceData->studentCharacter = $studentData->kcstudent_character;
            $getGuidanceData->studentSex = $studentData->kcstudent_sex;
            $KCStudentProfileModel->clearCondition();
            $this->optionModel = new KCConsultationGuidanceOptionModel();
            // 取得被選取的選項資料
            $this->getOption();
            // 取得預警型態
            $KuasConsultationCommon = new KuasConsultationCommon();
            $guidanceYearAndSemesterData = $KuasConsultationCommon->getSchoolYearAndSemester($getGuidanceData->kcguidance_add_time);
            $warning = $this->warningCommon->getRecentlyStudentWarningType($guidanceYearAndSemesterData['schoolYear'], $guidanceYearAndSemesterData['semester'], $warningStudentId);
            $perconalGuidance->kcgpersonal_public_comment = nl2br($perconalGuidance->kcgpersonal_public_comment);
            // 產生個人輔導紀錄畫面
            $this->tpl->assign('guidanceId', $guidanceId);
            $this->tpl->assign('teacherData', $teacherData);
            $this->tpl->assign('optionList', $this->text);
            $this->tpl->assign('topic', $topic);
            $this->tpl->assign('studentId', $userId);
            $this->tpl->assign('warning', $warning);
            $this->tpl->assign('reservationId', $warningReservationId);
            $this->tpl->assign('perconalGuidance', $perconalGuidance);
            $this->tpl->assign('getGuidanceData', $getGuidanceData);
            $this->tpl->assign('guidanceJS', "./modules/kuas_consultation_guidance/admin/script/KCGuidanceJs.js");
            $this->tpl->assign('PrintGuidanceCss', "./modules/kuas_consultation_guidance/admin/tmpl/css/KCGuidanceCss.css");
            $html = $this->tpl->fetch('KCWarningShowExportWarningGuidancePage.tpl');
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, true, '');
            $pdf->lastPage();
        }
        // 要匯出多筆
        if ($num == 2) {
            if ($studentIdArray) {
                foreach ($studentIdArray as $key => $studentId) {
                    $reservationData = "";
                    $consultationIdArray = "";
                    $consultationArray = "";
                    $isCustomConsultationIdArray = "";
                    $guidanceIdArray = "";
                    $reservationId = "";
                    // 在 reservation 表，設定學生帳號
                    $KCConsultationReservationModel->setConditionForEqualInStudentUserId((array) $studentId);
                    // 在 reservation 表，設定為已出席
                    $KCConsultationReservationModel->setConditionForEqualByReservationAttendance("attend");
                    if ($warningTermArray[$key]) {
                        $warningTerm = $warningTermArray[$key];
                    }
                    // 開始時間，從預警共用函數
                    $startDate = $KCWarningCommon->getStartTime($warningYear, $warningTerm);
                    // 結束時間，從預警共用函數
                    $endDate = $KCWarningCommon->getEndTime($warningYear, $warningTerm);
                    // reservation 表，設定時間範圍
                    $KCConsultationReservationModel->setConditionForEqualByRangeDate("$startDate", "$endDate");
                    // 取得 reservation 的資料
                    $reservationData = $KCConsultationReservationModel->getReservationList();
                    $KCConsultationReservationModel->clearCondition();
                    $kcconsultation = new KCConsultationModel();
                    if ($reservationData) {
                        // 將 kcconsultation_id 存成陣列
                        foreach ($reservationData as $data) {
                            $consultationIdArray[] = $data->kcconsultation_id;
                        }
                    }
                    // 將門診為非自訂門診且符合 kcconsultation_id 的資料抓出來
                    if ($consultationIdArray) {
                        foreach ($consultationIdArray as $data) {
                            $kcconsultation->setIsNotCustom();
                            $consultationArray[] = $kcconsultation->getConsultationDataByConsultationId($data);
                            $kcconsultation->clearCondition();
                        }
                    }
                    // 因取資料方式為單筆，將不符合上述條件的資料去除
                    if ($consultationArray) {
                        foreach ($consultationArray as $data) {
                            if ($data) {
                                $isCustomConsultationIdArray[] = $data->kcconsultation_id;
                            }
                        }
                    }
                    // 回 reservation 透過 consultationId 與 學生編號取得 reservationId
                    $KCConsultationReservationModel->setConditionForEqualByConsultationId($isCustomConsultationIdArray[0]);
                    $KCConsultationReservationModel->setConditionForEqualByStudentUserId($studentId);
                    $warningReservationData = $KCConsultationReservationModel->getReservationList();
                    $KCConsultationReservationModel->clearCondition();
                    // 取得 reservationId
                    if ($warningReservationData) {
                        foreach ($warningReservationData as $data) {
                            $warningReservationId = $data->kcreservation_id;
                        }
                    }
                    $KCConsultationReservationDetailModel = new KCConsultationReservationDetailModel();
                    $detailId = $KCConsultationReservationDetailModel->getReservationDetailByReservationId($warningReservationId);
                    $guidanceId = $detailId->kcguidance_id;
                    $userId = $studentId;

                    $this->guidanceId = $guidanceId;
                    $KCUnitModel = new KCUnitModel();
                    $KCStudentProfileModel = new KCStudentProfileModel();
                    $KCConsultationModel = new KCConsultationModel();
                    $KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
                    $KCConsultationReservationModel = new KCConsultationReservationModel();
                    $KCConsultationGuidanceGroupsModel = new KCConsultationGuidanceGroupsModel();
                    $KCConsultationGuidancePersonalModel = new KCConsultationGuidancePersonalModel();
                    $KCEmployeeProfileModel = new KCEmployeeProfileModel();
                    $getGuidanceData = $KCConsultationGuidanceModel->getGuidanceDataByGuidanceId($guidanceId);
                    $KCConsultationGuidanceModel->clearCondition();
                    $teacherData = $KCEmployeeProfileModel->getEmployeeDataByUserId($getGuidanceData->kcemployee_user_id);
                    $perconalGuidance = $KCConsultationGuidancePersonalModel->getPersonalGuidanceDataByGuidanceId($guidanceId);
                    $topic = $KCConsultationModel->getConsultationTopicByKcconsultationId($getGuidanceData->kcconsultation_id)->kcconsultation_topic;
                    // 取得學生資料
                    $studentData = $KCStudentProfileModel->getStudentDataByUserId($userId);
                    $getGuidanceData->kcstudent_name = $studentData->kcstudent_name;
                    // 取得學院資料
                    $classData = $KCUnitModel->getUnitByUnitId($studentData->kcunit_id);
                    $KCUnitModel->clearCondition();
                    $unitData = $KCUnitModel->getUnitByUnitId((int) $classData->kcunit_parent);
                    $getGuidanceData->unit = $unitData->kcunit_name;
                    $getGuidanceData->college = $KCUnitModel->getUnitByUnitId($unitData->kcunit_parent)->kcunit_name;
                    // 取得預約紀錄內的班級名稱
                    $reservationDatas = $KCConsultationReservationModel->getReservationDataByReservationId($warningReservationId);
                    $getGuidanceData->class = $reservationDatas->kcclass_name;
                    $getGuidanceData->studentCharacter = $studentData->kcstudent_character;
                    $getGuidanceData->studentSex = $studentData->kcstudent_sex;
                    $KCStudentProfileModel->clearCondition();
                    $this->optionModel = new KCConsultationGuidanceOptionModel();
                    // 取得被選取的選項資料
                    $this->getOption();
                    // 取得預警型態
                    $KuasConsultationCommon = new KuasConsultationCommon();
                    $guidanceYearAndSemesterData = $KuasConsultationCommon->getSchoolYearAndSemester($getGuidanceData->kcguidance_add_time);
                    $warning = $this->warningCommon->getRecentlyStudentWarningType($guidanceYearAndSemesterData['schoolYear'], $guidanceYearAndSemesterData['semester'], $studentId);
                    $perconalGuidance->kcgpersonal_public_comment = nl2br($perconalGuidance->kcgpersonal_public_comment);
                    // 產生個人輔導紀錄畫面

                    $this->tpl->assign('guidanceId', $guidanceId);
                    $this->tpl->assign('teacherData', $teacherData);
                    $this->tpl->assign('optionList', $this->text);
                    $this->tpl->assign('topic', $topic);
                    $this->tpl->assign('studentId', $userId);
                    $this->tpl->assign('warning', $warning);
                    $this->tpl->assign('reservationId', $reservationId);
                    $this->tpl->assign('perconalGuidance', $perconalGuidance);
                    $this->tpl->assign('getGuidanceData', $getGuidanceData);
                    $this->tpl->assign('guidanceJS', "./modules/kuas_consultation_guidance/admin/script/KCGuidanceJs.js");
                    $this->tpl->assign('PrintGuidanceCss', "./modules/kuas_consultation_guidance/admin/tmpl/css/KCGuidanceCss.css");

                    $html = $this->tpl->fetch('KCWarningShowExportWarningGuidancePage.tpl');
                    $pdf->AddPage();
                    $pdf->writeHTML($html, true, false, true, false, '');
                    $pdf->lastPage();
                    $html = "";
                    $this->text = "";
                }
            }
        }
        $nowDate = date(Ymd);
        // 匯出成 pdf
        $pdf->Output($nowDate . '.pdf', 'D');
        echo json_encode(true);
    }

    /**
     * 取得網址
     * @param string $funcName action 的 func 值
     * @return string 網址
     */
    private function _getModuleFunc($funcName) {
        return "admin.php?site_id=0&mod=kuas_consultation_warning&func=$funcName";
    }

    /**
     * 取得選項資料
     * @param string $guidanceId 輔導紀錄編號
     * @return $result 所有輔導選項資料
     */
    private function getOption() {
        $optionList = "";
        $result = "";
        $this->optionModel->setOptionQueId("0");
        $optionList = $this->optionModel->getKCConsultationGuidanceOptionList();
        $this->optionModel->clearCondition();
        $result = $this->getQueOption($optionList);
        return $result;
    }

    /**
     * 取得輔導項目
     * @param array $optionList 選項陣列（父類別編號為 0）
     * @return array $item 選項陣列
     */
    public function getQueOption($optionList) {
        $optionQueList = "";
        $resultOptionData = "";
        $item = "";
        $this->step += 1;
        $firstOption = true;
        // 判斷當前括號為中括號、小括號、不加括號
        switch ($this->step) {
            case 1:
                $rightParentheses = " ";
                $leftParentheses = " ";
                break;
            case 2:
                $rightParentheses = "[";
                $leftParentheses = "]";
                break;
            default :
                $rightParentheses = "(";
                $leftParentheses = ")";
                break;
        }
        $item = array();
        $size = sizeof($optionList);
        foreach ($optionList as $key => $row) {
            if ($this->step == 1) {
                $this->que = $row->kcgoption_id;
            }
            $this->optionModel->setOptionQueId($row->kcgoption_id);
            $optionQueList = $this->optionModel->getKCConsultationGuidanceOptionList();
            $this->optionModel->clearCondition();
            $KCConsultationGuidanceOptionResultModel = new KCConsultationGuidanceOptionResultModel();
            $KCConsultationGuidanceOptionResultModel->setConditionForEqualByGuidanceId($this->guidanceId);
            $resultOptionData = $KCConsultationGuidanceOptionResultModel->getKCConsultationGuidanceOptionResultByOptionId($row->kcgoption_id);
            $KCConsultationGuidanceOptionResultModel->clearCondition();
            // 當選項被選取或為標題選項，則將該選項值儲存至陣列
            if (isset($resultOptionData) || $this->step == 1) {
                // 標題
                if ($this->step == 1) {
                    $this->text[$this->que]['text'] = $row->kcgoption_text;
                    $this->getQueOption($optionQueList);
                } else {
                    // 選項
                    if (isset($optionQueList)) {
                        $this->text[$this->que]['desc'] .= $row->kcgoption_text . " " . $rightParentheses . " ";
                        $this->getQueOption($optionQueList);
                        if ($size - 1 == $key) {
                            $this->text[$this->que]['desc'] .= $leftParentheses . " ";
                        } else {
                            $this->text[$this->que]['desc'] .=" " . $leftParentheses . " ";
                        }
                    } else {
                        if ($row->kcgoption_type == "textbox") {
                            $resultOptionData->kcgoresult_text = nl2br($resultOptionData->kcgoresult_text);
                            if (!$firstOption) {
                                $this->text[$this->que]['desc'] .= " , " . $row->kcgoption_text . " (  " . $resultOptionData->kcgoresult_text . "  ) ";
                            } else {
                                $firstOption = false;
                                $this->text[$this->que]['desc'] .= $row->kcgoption_text . " (  " . $resultOptionData->kcgoresult_text . "  ) ";
                            }
                        } elseif ($row->kcgoption_type == "textarea") {
                            $resultOptionData->kcgoresult_text = chunk_split(nl2br($resultOptionData->kcgoresult_text), 24, "");
                            $this->text[$this->que]['desc'] .= $resultOptionData->kcgoresult_text . " ";
                        } else {
                            if (!$firstOption) {
                                $this->text[$this->que]['desc'] .= " , " . $row->kcgoption_text . " ";
                            } else {
                                $firstOption = false;
                                $this->text[$this->que]['desc'] .= $row->kcgoption_text . " ";
                            }
                        }
                    }
                }
            }
        }
        $this->step -= 1;
        return $item;
    }

}

?>