<?php

class KCWarningDoSentOneMailAction implements KuasConsultationActionListener {

    /**
     * 寄送單人通知信
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $num = $event->getGet();
        $KCWarningModel = new KCWarningModel(); // 預警模組
        $sentMail = new MailUtil();
        // 主旨
        $subject = $num['letterSubject'];
        // 簽名檔
        $sign = str_replace("\n", "<br/>", $num['letterSignName']);
        // 內容
        $hostUrl = "http://hospital.kuas.edu.tw";
        $num['letterMessage'] = str_replace($hostUrl, "<a href=" . $hostUrl . '>' . $hostUrl . "</a>", $num['letterMessage']);
        $message = str_replace("\n", "<br/>", $num['letterMessage']) . "<br/>" . "--<br/>" . "<font color=#898989 size=2px>" . $sign. "</font>";
        $warningStudentId = $num['warningStudentId'];
        $response['success'] = false;
        $response['msg'] = "寄送失敗，請聯絡管理者。";
        $siteId = $event->getSiteId();
        $from = get_config('sys_email', $siteId); // 取得平台設定系統寄件地址
        $namefrom = get_config("web_name", $siteId); // 設定寄件者顯示名稱
        $kcEmployeeProfileModel = new KCEmployeeProfileModel(); // 職員模組
        // 收件者 email
        $to = $kcEmployeeProfileModel->getEmployeeDataByUserId($warningStudentId)->kcemployee_mail;
        // 收件人姓名
        $nameto = "被選取的學生預警學生之導師";
        $site_id = "";
        // 寄送通知信
        if ($to) {
            $response['success'] = $sentMail->sendSmtpMail($from, $namefrom, $to, $nameto, $subject, $message, $site_id);
        }
        // 預警資料的編號
        $warningID = $num['warningId'];
        $KCWarningModel->setConditionForEqualByUserId($warningID);
        $adviceTime = $KCWarningModel->getAdviceTime();
        $KCWarningModel->clearCondition();
        if ($response['success']) {
            foreach ($adviceTime as $row) {
                // 輔導次數+1
                $KCWarningModel->setAdviceTimes($warningID, $row->kcwarning_advice_times + 1);
            }
        }
        echo true;
    }

}

?>
