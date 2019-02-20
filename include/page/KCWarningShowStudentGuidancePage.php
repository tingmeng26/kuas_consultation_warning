<?php

class KCWarningShowStudentGuidancePage extends ModuleAdminView implements KuasConsultationActionListener {

    private $tpl = null;
    private $optionModel = null;

    public function __construct() {
        parent::__construct("kuas_consultation_warning");
        $this->tpl = $this->initTemplate();
        $this->warningCommon = new KCWarningCommon();
    }

    /**
     * 取得輔導紀錄畫面 （個人  )
     * @param array  $event ( GET,  POST )
     * @return string  輔導紀錄畫面
     */
    public function actionPerformed($event) {
        $post = $event->getPost();
        $warningStudentId = $post['warningGuidanceStudentId'];
        $warningYear = $post['warningYear'];
        $warningTerm = $post['warningTerm'];
         $KCConsultationReservationModel = new KCConsultationReservationModel();
        $KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
        $KCConsultationGuidancePersonalModel = new KCConsultationGuidancePersonalModel();
        $KCEmployeeProfileModel = new KCEmployeeProfileModel();
        $KCWarningCommon = new KCWarningCommon(); // 預警共用函數
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
        $warningReservationData=$KCConsultationReservationModel->getReservationList();
        $KCConsultationReservationModel->clearCondition();
        // 取得 reservationId
        if($warningReservationData){
            foreach ($warningReservationData as $data){
                $warningReservationId=$data->kcreservation_id;
            }
        }
        $KCConsultationReservationDetailModel=new KCConsultationReservationDetailModel();
        $detailId=$KCConsultationReservationDetailModel->getReservationDetailByReservationId($warningReservationId);
     
        $guidanceId = $detailId->kcguidance_id;
        $userId = $warningStudentId;
        $KCUnitModel = new KCUnitModel();
        $KCStudentProfileModel = new KCStudentProfileModel();
        $KCConsultationModel = new KCConsultationModel();
        $getGuidanceData = $KCConsultationGuidanceModel->getGuidanceDataByGuidanceId($guidanceId);
        $KCConsultationGuidanceModel->clearCondition();
        // 取得個人輔導紀錄
        if ($getGuidanceData->kcguidance_type == "personal") {
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
            $reservationData = $KCConsultationReservationModel->getReservationDataByReservationId($warningReservationId);
            $getGuidanceData->class = $reservationData->kcclass_name;
            $getGuidanceData->studentCharacter = $studentData->kcstudent_character;
            $getGuidanceData->studentSex = $studentData->kcstudent_sex;
            $KCStudentProfileModel->clearCondition();
            $teacherData = $KCEmployeeProfileModel->getEmployeeDataByUserId($getGuidanceData->kcemployee_user_id);
            $this->optionModel = new KCConsultationGuidanceOptionModel();
            // 取得父選項 ( 編號為 0 ) 的選項
            $this->optionModel->setOptionQueId("0");
            $optionList = $this->optionModel->getKCConsultationGuidanceOptionList();
            $this->optionModel->clearCondition();
            // 取得所有選項的id（ 排列順序由第一層開始依序下去 ）
            $newQueId = array();
            $newQueId[] = 0;
            $optionArray = "";
            // 選項階層
            $step = 1;
            while (!empty($newQueId)) {
                $this->optionModel->setOptionQueIdIn($newQueId);
                $newList = $this->optionModel->getKCConsultationGuidanceOptionList();
                $this->optionModel->clearCondition();
                if (isset($newList)) {
                    unset($newQueId);
                    foreach ($newList as $row) {
                        // 取得當前一層的選項 id
                        $newQueId[] = $row->kcgoption_id;
                        if ($step > 1) {
                            // 取得每一個選項的 id
                            $optionArray.= $row->kcgoption_id . ",";
                        }
                    }
                    $step = $step + 1;
                } else {
                    break;
                }
            }
            // 取得預警型態
            $KuasConsultationCommon = new KuasConsultationCommon();
            $guidanceYearAndSemesterData = $KuasConsultationCommon->getSchoolYearAndSemester($getGuidanceData->kcguidance_add_time);
//            $warning = $this->warningCommon->getRecentlyStudentWarningType($guidanceYearAndSemesterData['schoolYear'], $guidanceYearAndSemesterData['semester'], $reservationData->kcstudent_user_id);
            $warning = $this->warningCommon->getRecentlyStudentWarningType($warningYear, $warningTerm, $warningStudentId);
            $perconalGuidance->kcgpersonal_public_comment = nl2br($perconalGuidance->kcgpersonal_public_comment);
            // 產生個人輔導紀錄畫面
            $optionArray = substr($optionArray, 0, strlen($optionArray) - 1);
            $this->tpl->assign('teacherData', $teacherData);
            $this->tpl->assign('guidanceId', $guidanceId);
            $this->tpl->assign('optionList', $optionList);
            $this->tpl->assign('optionArray', $optionArray);
            $this->tpl->assign('topic', $topic);
            $this->tpl->assign('studentId', $userId);
            $this->tpl->assign('warning', $warning);
            $this->tpl->assign('reservationId', $reservationId);
            $this->tpl->assign('perconalGuidance', $perconalGuidance);
            $this->tpl->assign('getGuidanceData', $getGuidanceData);
            $this->tpl->assign("getOptionActionUrl", $this->_getModuleFunc("getOption"));
            return $this->tpl->fetch('KCWarningShowPersonalGuidancePage.tpl');
        }
    }

    /**
     * 取得網址
     * @param string $funcName action 的 func 值
     * @return string 網址
     */
    private function _getModuleFunc($funcName) {
        return "admin.php?site_id=0&mod=kuas_consultation_warning&func=$funcName";
    }

}

?>