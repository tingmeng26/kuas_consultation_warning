<?php

class KCWarningDoCheckDataExistAction implements KuasConsultationActionListener {

    /**
     * 確認資料是否存在
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $num = $event->getGet();
        $KCWarningCommon = new KCWarningCommon();
        $KCStudentProfileModel = new KCStudentProfileModel(); // 學生模組
        $KCEmployeeProfileModel = new KCEmployeeProfileModel(); // 職員模組
        $kcUnitModel = new KCUnitModel();   // 單位模組
        $KCWarningModel = new KCWarningModel(); // 預警模組
        $KCConsultationReservationModel = new KCConsultationReservationModel(); // 預約記錄模組
        $KCConsultationReservationDetailModel = new KCConsultationReservationDetailModel; // 預約明細模組
        // 取得來源 下載/寄信/匯出
        $source = $num['from'];
        // 取得學年度 select 資料，從今年 ~ 95
        $year = new KuasConsultationCommon(); //取得所有年
        $yearData = array_reverse($year->getYears());
        // 是否點選已輔導 checkbox ， yes 為點選，空值為無
        $isGuidance = empty($num['isGuidance']) ? "" : $num['isGuidance'];
        // 是否點選未輔導 checkbox ， yes 為點選，空值為無
        $isntGuidance = empty($num['isntGuidance']) ? "" : $num['isntGuidance'];
        // 是否點選已通知 checkbox ， yes 為點選，空值為無
        $isNotice = empty($num['isNotice']) ? "" : $num['isNotice'];
        // 是否點選未通知 checkbox ， yes 為點選，空值為無
        $isntNotice = empty($num['isntNotice']) ? "" : $num['isntNotice'];
        // 系所管理者登入會傳入此系 id
        $departmentAdminUnitId = empty($num['departmentAdminUnitId']) ? "" : $num['departmentAdminUnitId'];
        // 班導登入會傳入此人帳號
        $tutorName = empty($num['userId']) ? "" : $num['userId'];
        // 只找出預警科目有資料的值
        $KCWarningModel->setConditionForEqualByChooseType();
        // 判斷是否登入帳號為系所管理者，如是 ，則設定查詢只能是該帳號登入者的系
        if (!empty($departmentAdminUnitId)) {
            $KCWarningModel->setConditionForEqualByUnitId($departmentAdminUnitId);
        }
        // 取得學年 select 資料，如第一次進頁面則為空，抓取自己資料表有資料之學年度最上面一筆
        $thisYear = empty($num['thisYear']) ? $yearData['0'] : $num['thisYear'];
        // 取得學生或老師 select 資料
        $searchByName = empty($num['searchByName']) ? "" : $num['searchByName'];
        // 取得單位 select 資料，為處室管理者才能使用，其他皆為空
        $searchByUnit = empty($num['searchByUnit']) ? "" : $num['searchByUnit'];
        // 取得學期 select 資料
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
        if ($tutorName) {
            $KCWarningModel->setConditionForEqualByTeacherUserId($tutorName);
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
            $unitId = $kcUnitModel->getUnitIdByUnitName($searchByUnit);
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
        // 將學生帳號 array 存成以逗點隔開的 $usersId 字串
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
        // 將老師帳號 array 存成以逗點隔開的 $teacherIdString 字串
        if ($teachersId) {
            $teacherIdString = implode("','", $teachersId);
            // 如果學生 array 存在， sql 為 AND 學生帳號 IN() OR 教師帳號 IN() 型態
            if ($usersId) {
                $KCWarningModel->setConditionForEqualByTeacherId($teacherIdString);
            } else {
                // 如果學生帳號 array 不存在， sql 為 AND 教師帳號 IN() 型態
                $KCWarningModel->setConditionForEqualByNoStudentDataTeacherId($teacherIdString);
            }
        }
        // 設定預警型態 1 為 21預警 或 32預警
        if ($searchByType == '1') {
            $KCWarningModel->setConditionForEqualByChooseType();
        }
        // 設定預警型態 2 為課業不佳
        elseif ($searchByType == '2') {
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
        // 已輔導條件下無符合資料，結果為空
        if ($isGuidance == "yes" && $isntGuidance != "yes" && empty($isGuidanceStudentIdArray)) {
            $results = null;
        }
        // 學生或老師條件下無符合資料，結果為空
        if ($searchByName != "" && !$usersId && !$teachersId) {
            $results = null;
        }
        if ($results) {
            foreach ($results as $key => $data) {
                $studentId[] = $data->kcstudent_user_id;
                $termId[] = $data->kcwarning_sem_term;
                $leterArray[$key]['id'] = $data->kcemployee_user_id;
                $leterArray[$key]['name'] = $KCEmployeeProfileModel->getEmployeeDataByUserId($data->kcemployee_user_id)->kcemployee_name;
                $leterArray[$key]['warning'] = $data->kcwarning_id;
            }
        }
        $isGuidanceNum = 0;
        if ($studentId) {
            foreach ($studentId as $key => $data) {
                // 判斷學生是否輔導，將已輔導的學生編號存成陣列
                if ($KCWarningCommon->decideStudentTutor($data, $thisYear, $termId[$key])) {
                    $isGuidanceStudentId = $isGuidanceStudentId . "," . $data;
                    $isGuidanceNum++;
                }
            }
        }
        if ($source == 'letter') {
            $response = $leterArray;
            // 結果為空，傳回 false ，代表此條件下無結果，無法執行下載或寄通知信功能
            if ($results == null) {
                $leterArray['response'] = false;
            } else {
                $response['response'] = true;
            }
        } else {
            // 結果為空，傳回 false ，代表此條件下無結果，無法執行下載或寄通知信功能
            if ($results == null) {
                $response['response'] = false;
            } else {
                $response['response'] = true;
                $response['studentId'] = $isGuidanceStudentId;
                $response['isGuidanceNum'] = $isGuidanceNum;
            }
        }
        echo json_encode($response);
    }

}

?>
