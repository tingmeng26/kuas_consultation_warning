<?php

class KCWarningShowWarningPage extends ModuleAdminView implements KuasConsultationActionListener {

    private $smarty = null;

    public function __construct() {
        parent::__construct("kuas_consultation_warning");
        $this->smarty = $this->initTemplate();
    }

    /**
     * 取得預警資料列表頁面
     * @param array $event ( GET, POST )
     * @return string 預警資料列表頁面
     */
    public function actionPerformed($event) {
        // 檢查 smtp 設定
        $response = KuasConsultationCommon::authMailServer();
        if ($response['result'] == false) {
            foreach ($response['msg'] as $row) {
                $required_close_file[] = $row;
            }
            $display = 'display';
            $this->smarty->assign(display, $display);
            $this->smarty->assign(required_close_file, $required_close_file);
        }
        if ($response['result']) {
            $none = "none";
            $this->smarty->assign(display, $none);
        }

        $kcEmployeeUnitModel = new KCEmployeeUnitModel();
        $kcUnitModel = new KCUnitModel();
        $competence = 0;
        // 取得使用者帳號
        $user_id = get_login_id();
        // 取得教務處綜合教務組編號
        $officeIdObject = $kcUnitModel->getUnitIdByUnitName("教務處綜合教務組");
        $officeId = $officeIdObject->kcunit_id;
        // 取得使用者的職員資料
        $officerData = $kcEmployeeUnitModel->getEmployeeUnitByUserIdAndUnitId($user_id, $officeId);
        $isRegistryManager = 'no';
        if ($officerData->kceunit_is_admin == 'yes') {
            // 判斷是否為教務處綜合教務組管理者
            $isRegistryManager = 'yes';
        }


        $kcMemberProfileModel = new KCMemberProfileModel();
        // 導師資料
        $tutorData = $kcMemberProfileModel->getMemberDataByUserId($user_id);
        $isTutor = 'no';
        // 將班導身份辦認資料設為 $privilegeArray 陣列
        if ($tutorData) {
            foreach ($tutorData as $tutor) {
                $tutorEx = explode(",", $tutor->kcmember_position);
                if (in_array("tutor", $tutorEx)) {
                    // 抓出為班導的資料
                    $privilegeArray = array(
                        'privilege' => 'Tutor',
                        'userId' => $user_id,
                    );
                    $competence = 1;
                    $isTutor = 'yes';
                }
            }
        }

        $identity = $kcEmployeeUnitModel->getEmployeeUnitByUserId($user_id);

        // 將系所與處室管理者的單位編號存成 $unitData 陣列
        if ($identity) {
            foreach ($identity as $identityData) {
                // 判斷是否為管理者
                if ($identityData->kceunit_is_admin == 'yes') {
                    $unitData[] = $kcUnitModel->getUnitByUnitId($identityData->kcunit_id);
                }
            }
        }

        // 設定辦認管理者身份的 $privilege 陣列
        if ($unitData) {
            foreach ($unitData as $rightData) {
                $rightType[] = $rightData->kcunit_type;
            }
            // 系所管理者
            if (in_array("department", $rightType)) {
                foreach ($unitData as $rightData) {
                    $department = $rightData->kcunit_id;
                }
                $privilegeArray = array(
                    'privilege' => 'DepartmentAdmin',
                    'departmentAdminUnitId' => $department
                );
                $competence = 1;
            }
            // 處室管理者
            if (in_array("office", $rightType)) {
                $privilegeArray = array(
                    'privilege' => 'OfficeAdmin'
                );
                $competence = 1;
            }
        }
        // 當不為可進入模組的系所管理者，處室管理者，班導身份
        if ($competence != 1) {
            $strMeta = "<meta http-equiv='content-type' content='text/html; charset=UTF-8'>";
            echo $strMeta . "<script>alert ('你沒有權限進入此模組!'); window.history.go(-1);</script>";
            exit(0);
        }
        $kcwmodel = new KCWarningModel();
        // 取得年
        $year = new KuasConsultationCommon();
        $yearData = array_reverse($year->getYears());
        // 取得自己資料表有資料的單位 id
        $unitData = $kcwmodel->getSelectUnit();
        if ($unitData != null) {
            foreach ($unitData as $unitArray) {
                $unitId[] = $unitArray->kcunit_id;
            }
            // 為用逗號隔開之字串，用做 sql IN
            $selectUnit = $kcUnitModel->getUnitNames($unitId);
        } else {
            $selectUnit = "";
        }

        $privilege = $privilegeArray['privilege'];
        // 權限為系所管理者
        if ($privilege == 'DepartmentAdmin') {
            $departmentAdminUnitId = $privilegeArray['departmentAdminUnitId'];
            $this->smarty->assign("kcWarningDataGridUrl", $this->_getModuleFunc("KCWarningEchoWarningListAction") . "&departmentAdminUnitId=" . $departmentAdminUnitId);
            // 放入系所編號，查詢限制為只有該系所
            $this->smarty->assign(departmentAdminUnitId, $departmentAdminUnitId);
        }
        // 權限為處室管理者
        if ($privilege == 'OfficeAdmin') {
            $this->smarty->assign("kcWarningDataGridUrl", $this->_getModuleFunc("KCWarningEchoWarningListAction"));
        }
        // 權限為班導
        if ($privilege == 'Tutor') {
            $userId = $privilegeArray['userId'];

            $this->smarty->assign("kcWarningDataGridUrl", $this->_getModuleFunc("KCWarningEchoWarningListAction") . "&tutorName=" . $userId);
            // 放入導師編號，查詢限制為只有該名導師
            $this->smarty->assign(userId, $userId);
        }
         $years=date(Y)-1911;
        $month=date(m);
        if($month >= '02' && $month < '08' ){
            $term='2';
            $years=$years-1;
        }else{
            $term='1';
        }
       
        
        // 登入者帳號
        $this->smarty->assign("userId", $user_id);
        $this->smarty->assign("term", $term);
         $this->smarty->assign("year", $years);
        $this->smarty->assign("isTutor", $isTutor);
        $this->smarty->assign("isRegistryManager", $isRegistryManager);
        $this->smarty->assign("privilege", $privilege);
        $this->smarty->assign("selectYear", $yearData);
        $this->smarty->assign("selectUnit", $selectUnit);
        $this->smarty->assign("kcWarningSentFullMailUrl", $this->_getModuleFunc("KCWarningDoSentMailAction"));
        $this->smarty->assign("kcWarningDoDownloadActionUrl", $this->_getModuleFunc("KCWarningDoDownloadAction"));
        $this->smarty->assign("kcWarningSentSingleMailUrl", $this->_getModuleFunc("KCWarningDoSentOneMailAction"));
        $this->smarty->assign("kcWarningSentMultiMailUrl", $this->_getModuleFunc("KCWarningDoSentMultiMailAction"));
        $this->smarty->assign("kcIndexSync", $this->_getModuleFunc("KCWarningDoSyncAction"));
        $this->smarty->assign("kcWarningDoCheckDataExistActionUrl", $this->_getModuleFunc("KCWarningDoCheckDataExistAction"));
        $this->smarty->assign("kcWarningShowGuidanceUrl", $this->_getModuleFunc("KCWarningEchoGuidanceListAction"));
        $this->smarty->assign("kcWarningHostUrl", $this->_getModuleFunc("KCWarningGetHostUrlAction"));
        $this->smarty->assign("KCWarningShowStudentGuidancePage", $this->_getModuleFunc("KCWarningShowStudentGuidancePage"));
        $this->smarty->assign("KCWarningReportWarningGuidancePage", $this->_getModuleFunc("KCWarningReportWarningGuidancePage"));
         $this->smarty->assign("KCWarningExportWarningGuidancePage", $this->_getModuleFunc("KCWarningExportWarningGuidancePage"));
         $this->smarty->assign("KCWarningCheckAddresseeAction", $this->_getModuleFunc("KCWarningCheckAddresseeAction"));



        return $this->smarty->fetch('KCWarningShowWarningPage.tpl');
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
