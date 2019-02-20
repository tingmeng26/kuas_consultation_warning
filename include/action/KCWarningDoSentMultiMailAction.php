<?php

class KCWarningDoSentMultiMailAction implements KuasConsultationActionListener {

    /**
     * 寄多人通知信
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $num = $event->getGet();
        $KCStudentProfileModel = new KCStudentProfileModel(); // 學生模組
        $KCWarningModel = new KCWarningModel(); // 預警模組
        $sentMail = new MailUtil();
        $subject = $num['letterSubject'];
        $sign = str_replace("\n", "<br/>", $num['letterSignName']);
        $hostUrl = "http://hospital.kuas.edu.tw";
        $num['letterMessage'] = str_replace($hostUrl, "<a href=" . $hostUrl . '>' . $hostUrl . "</a>", $num['letterMessage']);
        $message = str_replace("\n", "<br/>", $num['letterMessage']) . "<br/>" . "--<br/>" . "<font color=#898989 size=2px>" . $sign . "</font>";
        $warningStudentId = $num['warningStudentId'];
        $warningStudentName = $num['warningStudentName'];
        // 取得欲取消的 id
        $cancelId = $num['cancelIds'];
        $warningID = $num['warningId'];
        $response['success'] = false;
        $response['msg'] = "寄送失敗，請聯絡管理者。";
        $siteId = $event->getSiteId();
        // 取得平台設定系統寄件地址
        $from = get_config('sys_email', $siteId);
        // 設定寄件者顯示名稱
        $namefrom = get_config("web_name", $siteId);
        // 取得預資料編號
        $warningIDString = substr($warningID, 1, strlen($warningID) - 1);
        $warningIDArray = explode(",", $warningIDString);
        // 取得收件人 id
        $warningStudentIdString = substr($warningStudentId, 1, strlen($warningStudentId) - 1);
        $warningStudentIdArray = explode(",", $warningStudentIdString);
        // 創造索引為預警號，值為導師編號的陣列
        if ($warningIDArray) {
            foreach ($warningIDArray as $key => $data) {
                $combineArray[$data] = $warningStudentIdArray[$key];
            }
        }

        if ($cancelId) {
            // 取得被取消的導師編號
            $cancelIdString = substr($cancelId, 1, strlen($cancelId) - 1);
            $cancelIdArray = explode(",", $cancelIdString);
            if ($combineArray) {
                foreach ($combineArray as $key => $data) {
                    foreach ($cancelIdArray as $row) {
                        if ($data == $row) {
                            unset($combineArray[$key]);
                        }
                    }
                }
            }
        }
        if ($combineArray) {
            foreach ($combineArray as $key => $data) {
                $warning[] = $key;
            }
            $combineArray = array_unique($combineArray);
        }
        // 收件人姓名存成 $nameto 陣列
        $nameto = "被選取的學生預警學生之導師";

        $kcEmployeeProfileModel = new KCEmployeeProfileModel(); // 職員模組
        // 收件者 email
        if ($combineArray) {
            foreach ($combineArray as $row) {
                $teacher[] = $kcEmployeeProfileModel->getEmployeeDataByUserId($row)->kcemployee_mail;
            }
        }
        $site_id = "";
        if ($teacher) {
            foreach ($teacher as $row) {
                // 寄送通知信
                $response['success'] = $sentMail->sendSmtpMail($from, $namefrom, $row, $nameto, $subject, $message, $site_id);
            }
        }
        if ($warning) {
            foreach ($warning as $row) {
                $KCWarningModel->setConditionForEqualByUserId($row);
                //  將預警資料編號存成 array
                $adviceTime = $KCWarningModel->getAdviceTime();
                $KCWarningModel->clearCondition();
                foreach ($adviceTime as $data) {
                    // 通知次數+1
                    $KCWarningModel->setAdviceTimes($row, $data->kcwarning_advice_times + 1);
                }
            }
        }
        echo true;
    }

}

?>
