<?php

class KCWarningReportWarningGuidancePage extends ModuleAdminView implements KuasConsultationActionListener {

    private $tpl = null;
    private $optionModel = null;

    public function __construct() {
        parent::__construct("kuas_consultation_warning");
        $this->tpl = $this->initTemplate();
    }

    /**
     * 填報預警輔導畫面 （個人  )
     * @param array  $event ( GET,  POST )
     * @return string  預警輔導畫面
     */
    public function actionPerformed($event) {
        $post = $event->getPost();
        // 選取的學生 id
        $reportStudentId = $post['reportStudentId'];
        // 取得學生的班級
        $reportStudentClass = $post['reportStudentClass'];
        // 取得老師 id
        $reportTeacherId = $post['reportTeacherId'];
        // 取得學生的單位 id
        $reportStudentUnitId = $post['reportStudentUnitId'];
        // 取得老師姓名
        $reportTeacherName=$post['reportTeacherName'];
        // 填報記錄的型態 insert/update
        $reportType = $post['reportType'];
        // 填報學年
        $reportYear = $post['reportYear'];
        // 填報學期
        $reportTerm = $post['reportTerm'];
        // 取得學生資料
        $KCStudentProfileModel = new KCStudentProfileModel();
        $this->optionModel = new KCConsultationGuidanceOptionModel();
        $this->warningCommon = new KCWarningCommon();
        $KuasConsultationCommon = new KuasConsultationCommon();
        $this->common = new KCConsultationCommon();
        $this->employeeModel=new KCEmployeeProfileModel();
        if ($reportType == 'insert') {
            $studentData = $KCStudentProfileModel->getStudentDataByUserId($reportStudentId);
            $studentSex = $this->common->getStudentsex($studentData->kcstudent_sex);

            // 取得班級、系所、學院資料
            try {
                $KCUnitModel = new KCUnitModel();
                $classRow = $KCUnitModel->getUnitByUnitId($studentData->kcunit_id);
                $departmentRow = $KCUnitModel->getUnitByUnitId($classRow->kcunit_parent);
                $collegeRow = $KCUnitModel->getUnitByUnitId($departmentRow->kcunit_parent);
                $department = $departmentRow->kcunit_name;
                $college = $collegeRow->kcunit_name;
            } catch (Exception $exc) {
                $department = '-';
                $college = '-';
            }
            // 取得預警型態

            $year = $KuasConsultationCommon->getSchoolYearAndSemester();

            $warning = $this->warningCommon->getRecentlyStudentWarningType($year['schoolYear'], $reportTerm, $reportStudentId);
           
            // 取得第 0 階以外的選項
            $newQueId = array();
            $newQueId[] = 0;
            $newQueIdString = "";
            $optionStep = 1;
            while (!empty($newQueId)) {
                $this->optionModel->setOptionQueIdIn($newQueId);
                $newList = $this->optionModel->getKCConsultationGuidanceOptionListByEnable();
                $this->optionModel->clearCondition();
                if (isset($newList)) {
                    unset($newQueId);
                    foreach ($newList as $row) {
                        $newQueId[] = $row->kcgoption_id;
                        if ($optionStep > 0) {
                            $newQueIdString.= $row->kcgoption_id . ",";
                        }
                    }
                    $optionStep = $optionStep + 1;
                } else {
                    break;
                }
            }
            $optionIdString = substr($newQueIdString, 0, strlen($newQueIdString) - 1);

            // 取得項目第一層
            $this->optionModel->setOptionQueId("0");
            $list = $this->optionModel->getKCConsultationGuidanceOptionListByEnable();
            $item = array();
            if (isset($list)) {
                foreach ($list as $row) {
                    $item[] = array(
                        'id' => $row->kcgoption_id,
                        'name' => $row->kcgoption_text,
                        'desc' => $row->kcgoption_desc,
                        'limit' => $row->kcgoption_limit,
                        'queId' => $row->kcgoption_que_id,
                    );
                }
            }
            // 取得今日日期為預警輔導日期
            $preDate = date('Y-m-d');
            // 預設預警輔導時間
            $startClass = '00:00:00';
            $endClass = '00:00:00';
            // 總輔導時間
            $totalTime = 0;
            $this->tpl->assign('reportStudentClass', $reportStudentClass);
            $this->tpl->assign('reportStudentId', $reportStudentId);
            $this->tpl->assign('reportTeacherId', $reportTeacherId);
            $this->tpl->assign('reportStudentUnitId', $reportStudentUnitId);
            $this->tpl->assign('reportTeacherName', $reportTeacherName);
            $this->tpl->assign('department', $department);
            $this->tpl->assign('college', $college);
            $this->tpl->assign('studentSex', $studentSex);
            $this->tpl->assign('studentData', $studentData);
            $this->tpl->assign('warning', $warning);
            $this->tpl->assign('totalTime', $totalTime);
            $this->tpl->assign('optionIdString', $optionIdString);
            $this->tpl->assign("KCWarningChangeOptionPage", $this->_getModuleFunc("KCWarningChangeOptionPage"));
            $this->tpl->assign('item', $item);
            $this->tpl->assign('startClass', $startClass);
            $this->tpl->assign('endClass', $endClass);
            $this->tpl->assign('preDate', $preDate);
            $this->tpl->assign("KCWarningInsertWarningGuidanceAction", $this->_getModuleFunc("KCWarningInsertWarningGuidanceAction"));
            return $this->tpl->fetch('KCWarningShowReportWarningGuidancePage.tpl');
        } else {
            $KCConsultationReservationModel = new KCConsultationReservationModel();
            $KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
            $KCConsultationGuidancePersonalModel = new KCConsultationGuidancePersonalModel();
            $KCWarningCommon = new KCWarningCommon(); // 預警共用函數
            // 在 reservation 表，設定學生帳號
            $KCConsultationReservationModel->setConditionForEqualInStudentUserId((array) $reportStudentId);
            // 在 reservation 表，設定為已出席
            $KCConsultationReservationModel->setConditionForEqualByReservationAttendance("attend");
            // 開始時間，從預警共用函數
            $startDate = $KCWarningCommon->getStartTime($reportYear, $reportTerm);
            // 結束時間，從預警共用函數
            $endDate = $KCWarningCommon->getEndTime($reportYear, $reportTerm);
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
        $KCConsultationReservationModel->setConditionForEqualByStudentUserId($reportStudentId);
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

            // 取得預約紀錄資料
            $reservationRow = $KCConsultationReservationModel->getReservationDataByReservationId($warningReservationId);
            // 取得個人輔導紀錄資料
            $guidanceRow = $KCConsultationGuidanceModel->getGuidanceDataByGuidanceId($guidanceId);
            $guidancePersonalRow = $KCConsultationGuidancePersonalModel->getPersonalGuidanceDataByGuidanceId($guidanceRow->kcguidance_id);
            // 取得學生資料
            $KCStudentProfileModel = new KCStudentProfileModel();
            $studentData = $KCStudentProfileModel->getStudentDataByUserId($reservationRow->kcstudent_user_id);
            $studentSex = $this->common->getStudentSex($studentData->kcstudent_sex);
            // 取得門診資料
            $KCConsultationModel = new KCConsultationModel();
            $consultationData = $KCConsultationModel->getConsultationDataByConsultationId($guidanceRow->kcconsultation_id);

            // 取得班級、系所、學院資料
            try {
                $KCUnitModel = new KCUnitModel();
                $classRow = $KCUnitModel->getUnitByUnitId($studentData->kcunit_id);
                $departmentRow = $KCUnitModel->getUnitByUnitId($classRow->kcunit_parent);
                $collegeRow = $KCUnitModel->getUnitByUnitId($departmentRow->kcunit_parent);
                $department = $departmentRow->kcunit_name;
                $college = $collegeRow->kcunit_name;
            } catch (Exception $exc) {
                $department = '-';
                $college = '-';
            }

            $startDate = substr($guidanceRow->kcguidance_start_time, 0, 10);
            $endDate = substr($guidanceRow->kcguidance_end_time, 0, 10);
            $startClass = substr($guidanceRow->kcguidance_start_time, 11, 8);
            $endClass = substr($guidanceRow->kcguidance_end_time, 11, 8);

            // 取得第 0 階以外的選項
            $newQueId = array();
            $newQueId[] = 0;
            $newQueIdString = "";
            $optionStep = 1;
            while (!empty($newQueId)) {
                $this->optionModel->setOptionQueIdIn($newQueId);
                $newList = $this->optionModel->getKCConsultationGuidanceOptionListByEnable();
                $this->optionModel->clearCondition();
                if (isset($newList)) {
                    unset($newQueId);
                    foreach ($newList as $row) {
                        $newQueId[] = $row->kcgoption_id;
                        if ($optionStep > 0) {
                            $newQueIdString.= $row->kcgoption_id . ",";
                        }
                    }
                    $optionStep = $optionStep + 1;
                } else {
                    break;
                }
            }
            $optionIdString = substr($newQueIdString, 0, strlen($newQueIdString) - 1);
            $optionResultString = substr($optionResultString, 0, strlen($optionResultString) - 1);

            // 取得項目第一層
            $this->optionModel->setOptionQueId("0");
            $list = $this->optionModel->getKCConsultationGuidanceOptionListByEnable();
            if (isset($list)) {
                foreach ($list as $row) {
                    $item[] = array(
                        'id' => $row->kcgoption_id,
                        'name' => $row->kcgoption_text,
                        'desc' => $row->kcgoption_desc,
                        'limit' => $row->kcgoption_limit,
                        'queId' => $row->kcgoption_que_id,
                    );
                }
            }
            
            $KuasConsultationCommon = new KuasConsultationCommon();
            $year = $KuasConsultationCommon->getSchoolYearAndSemester($guidanceRow->kcguidance_add_time);
            
            $employeeProFileData = $this->employeeModel->getEmployeeDataByUserId($guidanceRow->kcemployee_user_id);


            // 取得預警型態
            $warning = $this->warningCommon->getRecentlyStudentWarningType($reportYear, $reportTerm, $reportStudentId);
           

            $searchType = $post['searchType'];
            $searchTeacher = $post['searchTeacher'];
            $searchYear = $post['searchYear'];
            $searchDate = $post['searchDate'];
            $searchMonth = $post['searchMonth'];
            $this->tpl->assign('searchType', $searchType);
            $this->tpl->assign('searchTeacher', $searchTeacher);
            $this->tpl->assign('searchYear', $searchYear);
            $this->tpl->assign('searchDate', $searchDate);
            $this->tpl->assign('searchMonth', $searchMonth);
            $this->tpl->assign('reportStudentClass', $reportStudentClass);
            $this->tpl->assign('reportStudentId', $reportStudentId);
            $this->tpl->assign('reportTeacherId', $reportTeacherId);
            $this->tpl->assign('reportStudentUnitId', $reportStudentUnitId);
            $this->tpl->assign('warning', $warning);
            $this->tpl->assign('employeeProFileData', $employeeProFileData);
            $this->tpl->assign('optionResultString', $optionResultString);
            $this->tpl->assign('optionIdString', $optionIdString);
            $this->tpl->assign('item', $item);
            $this->tpl->assign('department', $department);
            $this->tpl->assign('college', $college);
            $this->tpl->assign('showCalendarPage', $this->common->_getModuleFuncUrl("KCConsultationShowCalendarIndexPage"));
            $this->tpl->assign('showConsultationPage', $this->common->_getModuleFuncUrl("KCConsultationShowConsultationPage"));
            $this->tpl->assign('showConsultationPageUrl', $this->common->_getModuleFuncUrl("KCConsultationShowConsultationPage"));
            $this->tpl->assign('KCWarningUpdateWarningGuidanceAction', $this->_getModuleFunc("KCWarningUpdateWarningGuidanceAction"));
             $this->tpl->assign("KCWarningUpdateChangeOptionPage", $this->_getModuleFunc("KCWarningUpdateChangeOptionPage"));
            $this->tpl->assign('startClass', $startClass);
            $this->tpl->assign('endClass', $endClass);
            $this->tpl->assign('endDate', $endDate);
            $this->tpl->assign('startDate', $startDate);
            $this->tpl->assign('reservationRow', $reservationRow);
            $this->tpl->assign('studentData', $studentData);
            $this->tpl->assign('studentSex', $studentSex);
            $this->tpl->assign('mean', "學生學習輔導紀錄表");
            $this->tpl->assign('guidanceRow', $guidanceRow);
            $this->tpl->assign('consultationData', $consultationData);
            $this->tpl->assign('guidancePersonalRow', $guidancePersonalRow);
            $this->tpl->assign('reportYear', $reportYear);
            $this->tpl->assign('reportTerm', $reportTerm);
            $this->tpl->assign('guidancePersonalRow', $guidancePersonalRow);
            return $this->tpl->fetch('KCWarningShowUpdateReportWarningGuidancePage.tpl');
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