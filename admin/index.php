<?php

class kuas_consultation_warning extends AdminModule {

    function main() {
        
    }

    public function get_module_menu($func, $func_call) {
        // 取得使用者帳號
        $user_id = get_login_id();
        $kcEmployeeUnitModel = new KCEmployeeUnitModel();
        $kcUnitModel = new KCUnitModel();
        // 取得教務處綜合教務組編號
        $officeIdObject = $kcUnitModel->getUnitIdByUnitName("教務處綜合教務組");
        $officeId = $officeIdObject->kcunit_id;
        // 取得使用者所屬單位編號
        $identity = $kcEmployeeUnitModel->getEmployeeUnitByUserId($user_id);
        // 取得使用者的職員資料
        $officerData = $kcEmployeeUnitModel->getEmployeeUnitByUserIdAndUnitId($user_id, $officeId);
        $isRegistryManager = 'no';
        if ($officerData->kceunit_is_admin == 'yes') {
            // 判斷是否為教務處綜合教務組管理者
            $isRegistryManager = 'yes';
        }
        $arr = array(
            array(
                'menu' => "學生學習預警清單",
                'menuitem' => array(
                    array('name' => "學生學習預警清單", 'type' => "javascript", 'javascript' => "kcjs.back();")))
        );

        if ($identity) {
            foreach ($identity as $identityData) {
                // 將此登入者的單位編號存成陣列
                $userUnitIdArray[] = $identityData->kcunit_id;
            }
        }
        // 如使用者為教務處綜合教務組管理者，即有權限使用同步預警資料功能
        if (in_array($officeId, $userUnitIdArray)) {
            $arr = array(
                array(
                    'menu' => "學生學習預警清單",
                    'menuitem' => array(
                        array('name' => "學生學習預警清單", 'type' => "javascript", 'javascript' => "kcjs.back();"))),
                array(
                    'menu' => "同步預警資料",
                    'menuitem' => array(
                        array('name' => "同步預警資料", 'type' => "javascript", 'javascript' => "kcjs.kcwSync();")))
            );
        }
        return $arr;
    }

    public function get_column_content(&$ref, $args = null, $key = null) {
        $func = $args['func'];
        $actionListener = null;
        switch ($func) {
            // 首頁 grid 產生
            case 'KCWarningEchoWarningListAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningEchoWarningListAction);
                $layoutUse = false;
                break;
            // 寄送通知信
            case 'KCWarningDoSentMailAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningDoSentMailAction);
                $layoutUse = false;
                break;
            // 寄送通知信單人
            case 'KCWarningDoSentOneMailAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningDoSentOneMailAction);
                $layoutUse = false;
                break;
            // 寄送通知信多人
            case 'KCWarningDoSentMultiMailAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningDoSentMultiMailAction);
                $layoutUse = false;
                break;
            // 下載預警資料
            case 'KCWarningDoDownloadAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningDoDownloadAction);
                $layoutUse = false;
                break;
            // 判斷 grid 資料是否存在
            case 'KCWarningDoCheckDataExistAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningDoCheckDataExistAction);
                $layoutUse = false;
                break;
            // 檢視輔導記錄明細
            case 'KCWarningEchoGuidanceListAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningEchoGuidanceListAction);
                $layoutUse = false;
                break;
            // 同步預警資料
            case 'KCWarningDoSyncAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningDoSyncAction);
                $layoutUse = false;
                break;
            // 預警通知信取得簽名檔資料
            case 'KCWarningGetEmployeeDataAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningGetEmployeeDataAction);
                $layoutUse = false;
                break;
            // 獲得主機 url 
            case 'KCWarningGetHostUrlAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningGetHostUrlAction);
                $layoutUse = false;
                break;
            // 檢視學生預警輔導紀錄
            case 'KCWarningShowStudentGuidancePage':
                $actionListener = KuasConsultationCommon::loadKuasConsultationPage("kuas_consultation_warning", KCWarningShowStudentGuidancePage);
                $layoutUse = true;
                break;
            // 預警輔導紀錄填報
            case 'KCWarningReportWarningGuidancePage':
                $actionListener = KuasConsultationCommon::loadKuasConsultationPage("kuas_consultation_warning", KCWarningReportWarningGuidancePage);
                $layoutUse = true;
                break;
            // 新增個人輔導紀錄時，切換該選項的子選項
            case "KCWarningChangeOptionPage":
                $actionListener = KuasConsultationCommon::loadKuasConsultationPage("kuas_consultation_warning", KCWarningChangeOptionPage);
                $layoutUse = false;
                break;
            // 修改個人輔導紀錄時，切換該選項的子選項
            case "KCWarningUpdateChangeOptionPage":
                $actionListener = KuasConsultationCommon::loadKuasConsultationPage("kuas_consultation_warning", KCWarningUpdateChangeOptionPage);
                $layoutUse = false;
                break;
            // 新增個人預警輔導記錄 
            case 'KCWarningInsertWarningGuidanceAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningInsertWarningGuidanceAction);
                $layoutUse = false;
                break;
            // 修改個人預警輔導記錄 
            case 'KCWarningUpdateWarningGuidanceAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningUpdateWarningGuidanceAction);
                $layoutUse = false;
                break;
            // 取得輔導選項
            case 'getOption':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningGetOptionAction);
                break;
            // 輸出預警輔導記錄 pdf
            case 'KCWarningExportWarningGuidancePage':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningExportWarningGuidancePage);
                $layoutUse = true;
                break;
            // 測試 pdf 
            case 'pdfTest':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", pdfTest);
                $layoutUse = false;
                break;
            // 判斷收件人是否存在
            case 'KCWarningCheckAddresseeAction':
                $actionListener = KuasConsultationCommon::loadKuasConsultationAction("kuas_consultation_warning", KCWarningCheckAddresseeAction);
                $layoutUse = false;
                break;
            default :
                $actionListener = KuasConsultationCommon::loadKuasConsultationPage("kuas_consultation_warning", KCWarningShowWarningPage);
                $layoutUse = true;
                break;
        }
        $event = new KuasConsultationEvent($_GET, $_POST);
        $ref = $actionListener->actionPerformed($event);
        return $layoutUse;
    }

}

?>
