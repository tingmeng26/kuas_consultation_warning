<?php

class KCWarningDoSentMailAction implements KuasConsultationActionListener {

    /**
     * 寄送通知信
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $num = $event->getGet();
        $KCStudentProfileModel = new KCStudentProfileModel(); // 學生模組
        $KCEmployeeProfileModel = new KCEmployeeProfileModel(); // 職員模組
        $KCUnitModel = new KCUnitModel();   // 單位模組
        $KCWarningModel = new KCWarningModel(); // 預警模組
        $sentMail = new MailUtil();
        $KCWarningCommon = new KCWarningCommon();
        $kcConsultationReservationModel = new KCConsultationReservationModel(); // 輔導記錄模組
        $kcConsultationReservationDetailModel = new KCConsultationReservationDetailModel;
        // 取得學年度 select 資料，從今年 ~ 95
        $year = new KuasConsultationCommon(); //取得所有年
        $yearData = array_reverse($year->getYears());
        // 是否點選已輔導 checkbox ， yes 為點選，空值為無
        $isGuidance = empty($num['isGuidance']) ? "" : $num['isGuidance'];
        // 是否點選未輔導 checkbox ， yes 為點選，空值為無
        $isntGuidance = empty($num['isntGuidance']) ? "" : $num['isntGuidance'];
        // 是否點選已通知 checkbox ， yes 為點選，空值為無
        $isNotice = empty($num['isNotice']) ? "" : $num['isNotice'];
        // 是否點選未通知checkbox ， yes 為點選，空值為無
        $isntNotice = empty($num['isntNotice']) ? "" : $num['isntNotice'];
        // 系所管理者登入會傳入此系 id
        $departmentAdminUnitId = empty($num['departmentAdminUnitId']) ? "" : $num['departmentAdminUnitId'];
        // 班導登入會傳入此人帳號
        $userId = empty($num['userId']) ? "" : $num['userId'];
        // 只找出預警科目有資料的值
        $KCWarningModel->setConditionForEqualByChooseType();
        // 判斷是否登入帳號為系所管理者，如是，則設定查詢只能是該帳號登入者的系
        if (!empty($departmentAdminUnitId)) {
            $KCWarningModel->setConditionForEqualByUnitId($departmentAdminUnitId);
        }
        // 取得學年 select 資料，如第一次進頁面則為今年
        $thisYear = empty($num['thisYear']) ? $yearData['0'] : $num['thisYear'];
        // 取得學生或老師 select 資料
        $searchByName = empty($num['searchByName']) ? "" : $num['searchByName'];
        // 取得單位 select 資料，為處室管理者才能使用，其他皆為空
        $searchByUnit = empty($num['searchByUnit']) ? "" : $num['searchByUnit'];
        // 取得學期，空為全學期，1 為上學期，2 為下學期
        $searchByTerm = empty($num['searchByTerm']) ? "" : $num['searchByTerm'];
        // 取得預警型態，0 為全部，1 為期中預警，2 為課業不佳
        $searchByType = empty($num['searchByType']) ? "0" : $num['searchByType'];

        // 取得預警學生以及老師編號   
        $warningStudentIdArray = $KCWarningCommon->getWarningStudentId();
        $warningTeacherIdArray = $KCWarningCommon->getWarningTeacherId();

        // 查學生模組資料表是否有該名學生的名字
        if ($searchByName) {
            $KCStudentProfileModel->setConditionForStudentName($searchByName);
            $KCStudentProfileModel->setConditionForStudentsId($warningStudentIdArray);
            $stuDate = $KCStudentProfileModel->getStudentList();
            $KCStudentProfileModel->clearCondition();
        }
        // 判斷登入者是否為班導，如是，則設定查詢只能為該登入者
        if ($userId) {
            $KCWarningModel->setConditionForEqualByTeacherUserId($userId);
        }
        // 查老師模組資料表是否有該名老師的名字
        if ($searchByName) {
            $KCEmployeeProfileModel->setConditionForEmployeeName($searchByName);
            $KCEmployeeProfileModel->setConditionForUsersId($warningTeacherIdArray);
            $teaDate = $KCEmployeeProfileModel->getEmployeeProfileList();
            $KCEmployeeProfileModel->clearCondition();
        }
        // 查單位模組資料表是否有該單位的名字
        if ($searchByUnit) {
            $unitId = $KCUnitModel->getUnitIdByUnitName($searchByUnit);
            $KCWarningModel->setConditionForEqualByUnitId($unitId->kcunit_id);
        }
        // 設定查詢學期資料
        if ($searchByTerm) {
            $KCWarningModel->setConditionForEqualByTerm($searchByTerm);
        }

        // 當學生資料表(查學生名字)有 like 此關鍵字的資料時，取出該筆資料的帳號存成 $usersId 陣列
        if ($stuDate) {
            foreach ($stuDate as $stuIds) {
                $usersId[] = $stuIds->kcstudent_user_id;
            }
        }
        // 當職員資料表(查老師名字)有 like 關鍵字的資料時，取出該筆資料的帳號存成 $teachersId 陣列
        if ($teaDate) {
            foreach ($teaDate as $teaIds) {
                $teachersId[] = $teaIds->kcemployee_user_id;
            }
        }
        // 將學生帳號 array 存成以逗點，隔開的字串
        if ($usersId) {
            $userIdString = implode("','", $usersId);
            // 如果教師帳號 array 存在，sql 為 AND 學生帳號 IN() OR 教師帳號 IN() 型態
            if ($teachersId) {
                $KCWarningModel->setConditionForEqualById($userIdString);
            } else {
                // 如果教師帳號 array 不存在，sql 為 AND 學生帳號 IN() 型態
                $KCWarningModel->setConditionForEqualByNoTeacherDataId($userIdString);
            }
        }
        // 將老師帳號 array 存成以逗點，隔開的字串
        if ($teachersId) {
            $teacherIdString = implode("','", $teachersId);
            // 如果學生 array 存在，sql 為 AND 學生帳號 IN() OR 教師帳號 IN() 型態
            if ($usersId) {
                $KCWarningModel->setConditionForEqualByTeacherId($teacherIdString);
            } else {
                // 如果學生帳號 array 不存在，sql 為 AND 教師帳號 IN() 型態
                $KCWarningModel->setConditionForEqualByNoStudentDataTeacherId($teacherIdString);
            }
        }
        // 設定預警型態  1 為 21預警 或 32預警
        if ($searchByType == '1') {
            $KCWarningModel->setConditionForEqualByChooseType();
        }
        // 設定預警型態 2 為課業不佳
        if ($searchByType == '2') {
            $KCWarningModel->setConditionForEqualByChoosePoorType();
        }
        // 如學生或老師名字查詢皆無資料，則不用執行 sql ，結果為空
        if ($searchByName != "" && !$usersId && !$teachersId) {
            $results = null;
        } else {
            // 設定學年度
            $KCWarningModel->setConditionForEqualByYear($thisYear);
            $allWarningData = $KCWarningModel->getKCWarningList();
        }
        if ($allWarningData) {
            foreach ($allWarningData as $row) {
                // 將學生帳號存成 $warningStudentIdArray 陣列
                $studentIdArray[] = $row->kcstudent_user_id;
                $termIds[] = $row->kcwarning_sem_term;
            }
            if ($studentIdArray) {
                foreach ($studentIdArray as $key => $data) {
                    // 判斷學生是否輔導，將已輔導的學生編號存成陣列
                    if ($KCWarningCommon->decideStudentTutor($data, $thisYear, $termIds[$key])) {
                        $isGuidanceStudentIdArray[] = $data;
                    } else {
                        $isntGuidanceStudentIdArray[] = $data;
                    }
                }
            }
            if ($isGuidanceStudentIdArray) {
                $isGuidanceStudentIdString = implode("','", $isGuidanceStudentIdArray);
            }
            if ($isntGuidanceStudentIdArray) {
                $isntGuidanceStudentIdString = implode("','", $isntGuidanceStudentIdArray);
            }
            // 勾選已輔導：資料表 reservation -> reservation_datail -> guidance (取得開輔教師,地點,時間)
            //							   \ -> option_result (判斷是否為已輔導)  
            if ($isGuidanceStudentIdArray) {
                // 判斷是否勾選已輔導，並且沒有勾選未輔導
                if ($isGuidance == "yes" && $isntGuidance != "yes") {
                    $KCWarningModel->setConditionForEqualByNoTeacherDataId($isGuidanceStudentIdString);
                }
                // 判斷是否為勾選未輔導，並且沒有勾選已輔導
                if ($isntGuidance == "yes" && $isGuidance != "yes") {
                    $KCWarningModel->setConditionForEqualByNoTeacherDataId($isntGuidanceStudentIdString);
                }
            }

            // 勾選已通知，並且沒有勾選未通知
            if ($isNotice == "yes" && $isntNotice != "yes") {
                $KCWarningModel->setConditionForEqualByAdviceTime();
            }
            // 勾選未通知，並且沒有勾選已通知
            if ($isntNotice == "yes" && $isNotice != "yes") {
                $KCWarningModel->setConditionForEqualByZeroAdviceTime();
            }
            // 取得符合條件資料總數
            $numrows = $KCWarningModel->getKCWarningCount();

            // 取得查詢結果
            $results = $KCWarningModel->getKCWarningList();
            $KCWarningModel->clearCondition();
        }
        $sum = 0;
        // 取得有輔導的筆數
        if ($results) {
            foreach ($results as $i => $data) {
                if (($KCWarningCommon->decideStudentTutor($data->kcstudent_user_id, $thisYear, $data->kcwarning_sem_term))) {

                    $sum++;
                }
            }
        }
        $l = 0;
        // 判斷是否為勾選已輔導
        if ($isGuidance == "yes" && $isntGuidance != "yes") {
            if ($results) {
                foreach ($results as $i => $data) {
                    if (($KCWarningCommon->decideStudentTutor($data->kcstudent_user_id, $thisYear, $data->kcwarning_sem_term))) {
                        $tmpResults[$i] = $results[$i];
                        $l++;
                    }
                }
            }
            $numrows = $l;
        }
        // 判斷是否為勾選未輔導，並且沒有勾選已輔導
        if ($isntGuidance == "yes" && $isGuidance != "yes") {
            if ($results) {
                foreach ($results as $i => $data) {
                    if ((!$KCWarningCommon->decideStudentTutor($data->kcstudent_user_id, $thisYear, $data->kcwarning_sem_term))) {
                        $tmpResults[$i] = $results[$i];
                    }
                }
            }
            $numrows-=$sum;
        }



        if ($tmpResults) {
            $results = $tmpResults;
        }
        // 判斷不符合已輔導則結果為空
        if ($isGuidance == "yes" && $isntGuidance != "yes" && empty($isGuidanceStudentIdArray)) {
            $results = null;
        }
        // 有搜詢結果時執行寄信動作
        if ($results) {
            foreach ($results as $result) {
                // 透過學生帳號找資料
                $KCStudentProfileModel->setConditionForStudentId($result->kcstudent_user_id);
                $stuLetterData = $KCStudentProfileModel->getStudentList();
                $KCStudentProfileModel->clearCondition();
                if ($stuLetterData) {
                    foreach ($stuLetterData as $row) {
                        $result->stu_name = $row->kcstudent_name;
                        $result->stu_email = $row->kcstudent_mail;
                    }
                }
            }
        }
        if ($results) {
            foreach ($results as $data) {
                if ($data->kcemployee_user_id) {
                    $teacherIdArray[] = $data->kcemployee_user_id;
                }
            }
        }
        $kcEmployeeProfileModel = new KCEmployeeProfileModel(); // 職員模組
        $teacherIdArray = array_unique($teacherIdArray);
        if ($teacherIdArray) {
            $hostUrl = "http://hospital.kuas.edu.tw";
            $num['letterMessage'] = str_replace($hostUrl, "<a href=" . $hostUrl . '>' . $hostUrl . "</a>", $num['letterMessage']);
            foreach ($teacherIdArray as $data) {
                $nameto = "被選取的學生預警學生之導師";
                $subject = $num['letterSubject'];
                $sign = str_replace("\n", "<br/>", $num['letterSignName']);
                $to = $kcEmployeeProfileModel->getEmployeeDataByUserId($data)->kcemployee_mail;
                $message = str_replace("\n", "<br/>", $num['letterMessage']) . "<br/>" . "--<br/>" . "<font color=#898989 size=2px>" . $sign . "</font>";
                $siteId = $event->getSiteId();
                $from = get_config('sys_email', $siteId); // 取得平台設定系統寄件地址
                $namefrom = get_config("web_name", $siteId); // 設定寄件者顯示名稱
                $site_id = "";
                $response['success'] = false;
                $response['msg'] = "寄送失敗，請聯絡管理者。";
                if ($to) {
                    $response['success'] = $sentMail->sendSmtpMail($from, $namefrom, $to, $nameto, $subject, $message, $site_id);
                }
            }
            if ($response['success']) {
                foreach ($results as $data) {
                    if ($data->kcemployee_user_id) {
                        $id = $data->kcwarning_id;
                        $KCWarningModel->setConditionForEqualByUserId($id);
                        $adviceTime = $KCWarningModel->getAdviceTime();
                        $KCWarningModel->clearCondition();
                        foreach ($adviceTime as $row) {
                            //  輔導次數+1
                            $KCWarningModel->setAdviceTimes($id, $row->kcwarning_advice_times + 1);
                        }
                    }
                }
            }
        }
        echo true;
    }

}

?>
