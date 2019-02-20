<?php

class KCWarningEchoWarningListAction implements KuasConsultationActionListener {

    /**
     * 預警資料清單
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $num = $event->getGet();
        $post = $event->getPost();
        $start = $num['start'];
        $count = $num['count'];
        $processSort = new KCWarningDoSortProcessAction();
        $kcStudentProfileModel = new KCStudentProfileModel(); // 學生模組
        $kcEmployeeProfileModel = new KCEmployeeProfileModel(); // 職員模組
        $kcUnitModel = new KCUnitModel();   // 單位模組
        $kcWarningModel = new KCWarningModel(); // 預警模組
        $KCWarningCommon = new KCWarningCommon(); // 預警共用函數
        $kcConsultationReservationModel = new KCConsultationReservationModel(); // 預約記錄模組
        $kcConsultationReservationDetailModel = new KCConsultationReservationDetailModel; // 預約明細模組
        // 取得學年度 select 資料，從今年 ~ 95
        $year = new KuasConsultationCommon(); //取得所有年
        $yearData = array_reverse($year->getYears());

        // 是否點選期中預警 checkbox ，yes 為點選，空值為無
        $isWarning = empty($num['isWarning']) ? "" : $num['isWarning'];
        // 是否點選課業不佳 checkbox ，yes 為點選，空值為無
        $isPoor = empty($num['isPoor']) ? "" : $num['isPoor'];
        // 是否點選已輔導 checkbox ，yes 為點選，空值為無
        $isGuidance = empty($num['isGuidance']) ? "" : $num['isGuidance'];
        // 是否點選未輔導 checkbox ，yes 為點選，空值為無
        $isntGuidance = empty($num['isntGuidance']) ? "" : $num['isntGuidance'];
        // 是否點選已通知 checkbox ，yes 為點選，空值為無
        $isNotice = empty($num['isNotice']) ? "" : $num['isNotice'];
        // 是否點選未通知 checkbox ，yes 為點選，空值為無
        $isntNotice = empty($num['isntNotice']) ? "" : $num['isntNotice'];
        // 點選 grid 欄位排序，預設為用 id 排序或者為選取欄位名字
        $sort = empty($num['sort']) ? "kcwarning_id" : $num['sort'];
        // 系所管理者登入會傳入此系 id
        $departmentAdminUnitId = empty($num['departmentAdminUnitId']) ? "" : $num['departmentAdminUnitId'];
        // 班導登入會傳入此人帳號
        $tutorName = empty($num['tutorName']) ? "" : $num['tutorName'];
        // 只找出預警科目有資料的值
        $kcWarningModel->setConditionForEqualByChooseType();
        // 判斷是否登入帳號為系所管理者，如是，則設定查詢只能是該帳號登入者的系
        if (!empty($departmentAdminUnitId)) {
            $kcWarningModel->setConditionForEqualByUnitId($departmentAdminUnitId);
        }
        // 取得排序欄位及方式 asc 或 desc
        $sortData = $processSort->processSort($sort);
        // 取得學年 select 資料，如第一次進頁面則為空，抓取自己資料表有資料之學年度最上面一筆
        $thisYear = empty($num['thisYear']) ? $yearData['0'] : $num['thisYear'];
        // 取得學生或老師 select 資料
        $searchByName = empty($num['searchByName']) ? "" : $num['searchByName'];
        $searchByNamePost = empty($post['searchByNamePost']) ? "" : $post['searchByNamePost'];
        // 取得單位 select 資料，為處室管理者才能使用，其他皆為空
        $searchByUnit = empty($num['searchByUnit']) ? "" : $num['searchByUnit'];
        $searchByTerm = $num['searchByTerm'];
        if ($searchByTerm == '0') {
            $searchByTerm = "";
        } elseif ($searchByTerm == '1') {
            $searchByTerm = '1';
        } else {
            //依當日判斷現有學期
            $years = date(Y) - 1911;
            $month = date(m);
            if ($month >= '02' && $month < '08') {
                $term = '2';
                $years = $years - 1;
            } else {
                $term = '1';
            }
            $searchByTerm = $term;
        }

        // 取得預警學生以及老師編號   
        $warningStudentIdArray = $KCWarningCommon->getWarningStudentId();
        $warningTeacherIdArray = $KCWarningCommon->getWarningTeacherId();

        // 查學生模組資料表是否有該名學生的名字
        if ($searchByNamePost) {
            $kcStudentProfileModel->setConditionForStudentName($searchByNamePost);
            $kcStudentProfileModel->setConditionForStudentsId($warningStudentIdArray);
            $stuDate = $kcStudentProfileModel->getStudentList();
            $kcStudentProfileModel->clearCondition();
        }
        if ($searchByName) {
            $kcStudentProfileModel->setConditionForStudentName($searchByName);
            $kcStudentProfileModel->setConditionForStudentsId($warningStudentIdArray);
            $stuDate = $kcStudentProfileModel->getStudentList();
            $kcStudentProfileModel->clearCondition();
        }
        // 判斷登入者是否為班導，如是，則設定查詢只能為該登入者
        if ($tutorName) {
            $kcWarningModel->setConditionForEqualByTeacherUserId($tutorName);
        }
        // 查老師模組資料表是否有該名老師的名字
        if ($searchByNamePost) {
            $kcEmployeeProfileModel->setConditionForEmployeeName($searchByNamePost);
            $kcEmployeeProfileModel->setConditionForUsersId($warningTeacherIdArray);
            $teaDate = $kcEmployeeProfileModel->getEmployeeProfileList();
            $kcEmployeeProfileModel->clearCondition();
        }
        if ($searchByName) {
            $kcEmployeeProfileModel->setConditionForEmployeeName($searchByName);
            $kcEmployeeProfileModel->setConditionForUsersId($warningTeacherIdArray);
            $teaDate = $kcEmployeeProfileModel->getEmployeeProfileList();
            $kcEmployeeProfileModel->clearCondition();
        }

        // 查單位模組資料表是否有該單位的名字
        if ($searchByUnit) {
            $unitId = $kcUnitModel->getUnitIdByUnitName($searchByUnit);
            $kcWarningModel->setConditionForEqualByUnitId($unitId->kcunit_id);
        }
        // 設定查詢學期資料
        if ($searchByTerm) {
            $kcWarningModel->setConditionForEqualByTerm($searchByTerm);
        }

        // 當學生資料表(查學生名字)有 like 此關鍵字的資料時，取出該筆資料的帳號存成 $usersId 陣列
        if ($stuDate) {
            $usersId = array();
            foreach ($stuDate as $stuIds) {
                $usersId[] = $stuIds->kcstudent_user_id;
            }
        }
        // 當職員資料表(查老師名字)有 like 關鍵字的資料時,取出該筆資料的帳號存成 $teachersId 陣列
        if ($teaDate) {
            $teachersId = array();
            foreach ($teaDate as $teaIds) {
                $teachersId[] = $teaIds->kcemployee_user_id;
            }
        }
        // 將學生帳號 array ，存成以逗點隔開的字串        
        if ($usersId) {
            $userIdString = implode("','", $usersId);
            // 如果教師帳號 array 存在， sql 為 AND 學生帳號 IN() OR 教師帳號 IN() 型態
            if ($teachersId) {
                $kcWarningModel->setConditionForEqualById($userIdString);
            } else {
                // 如果教師帳號 array 不存在， sql 為 AND 學生帳號 IN() 型態
                $kcWarningModel->setConditionForEqualByNoTeacherDataId($userIdString);
            }
        }
        // 將老師帳號 array ，存成以逗點隔開的字串
        if ($teachersId) {
            $teacherIdString = implode("','", $teachersId);
            //如果學生 array 存在，sql 為 AND 學生帳號 IN() OR 教師帳號 IN() 型態
            if ($usersId) {
                $kcWarningModel->setConditionForEqualByTeacherId($teacherIdString);
            } else {
                //如果學生帳號 array 不存在，sql 為 AND 教師帳號 IN() 型態
                $kcWarningModel->setConditionForEqualByNoStudentDataTeacherId($teacherIdString);
            }
        }
        // 設定預警型態， 1 為 21預警 或 32預警
        if ($isWarning == "yes" && $isPoor != "yes") {
            $kcWarningModel->setConditionForEqualByChooseType();
        }
        // 設定預警型態，2 為課業不佳
        if ($isPoor == "yes" && $isWarning != "yes") {
            $kcWarningModel->setConditionForEqualByChoosePoorType();
        }
        // 如學生或老師名字查詢皆無資料，則不用執行 sql，結果為空
        if ($searchByName != "" && $usersId == null && $teachersId == null) {
            $results = null;
        } else if ($searchByNamePost != "" && $usersId == null && $teachersId == null) {
            $numrows = 0;
            $results = null;
        } else {
            // 設定學年度
            $kcWarningModel->setConditionForEqualByYear($thisYear);
            $allWarningData = $kcWarningModel->getKCWarningList();
           
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
                    $kcWarningModel->setConditionForEqualByNoTeacherDataId($isGuidanceStudentIdString);
                }
                // 判斷是否為勾選未輔導，並且沒有勾選已輔導
                if ($isntGuidance == "yes" && $isGuidance != "yes") {
                    $kcWarningModel->setConditionForEqualByNoTeacherDataId($isntGuidanceStudentIdString);
                }
            }

            // 勾選已通知，並且沒有勾選未通知
            if ($isNotice == "yes" && $isntNotice != "yes") {
                $kcWarningModel->setConditionForEqualByAdviceTime();
            }
            // 勾選未通知，並且沒有勾選已通知
            if ($isntNotice == "yes" && $isNotice != "yes") {
                $kcWarningModel->setConditionForEqualByZeroAdviceTime();
            }
            // 取得符合條件資料總數
            $numrows = $kcWarningModel->getKCWarningCount();
             // 設定 start count
            if ($count == "") {
                $start = 0;
                $count = 15;
            }
            $kcWarningModel->setSortStartCount($sortData['sortType'], $sortData['sortField'], $start, $count);
            // 取得查詢結果
            $results = $kcWarningModel->getKCWarningList();
            $kcWarningModel->clearCondition();
        }

        // 當已通知條件下無資料，結果為空
        if ($isGuidance == "yes" && $isntGuidance != "yes" && empty($isGuidanceStudentIdArray)) {
            $results = null;
            $numrows = 0;
        }
        if ($results) {
            foreach ($results as $data) {
                // 設定學生帳號取得學生名字，透過 KCStudentProfileModel 的 function
                $kcStudentProfileModel->setConditionForStudentId($data->kcstudent_user_id);
                $stu = $kcStudentProfileModel->getStudentList();

                // 透過教師帳號取得教師名字，透過 KCEmployeeProfileModel 的 function
                $tea = $kcEmployeeProfileModel->getEmployeeDataByUserId($data->kcemployee_user_id);
                $kcStudentProfileModel->clearCondition();
                // 因 function 方式為 get_results
                if ($stu) {
                    foreach ($stu as $stuArray) {
                        $data->stu = $stuArray->kcstudent_name;
                    }
                }
                $data->tea = $tea->kcemployee_name;
                if ($data->stu == "") {
                    $data->stu = "-";
                }
                if ($data->tea == "") {
                    $data->tea = "-";
                }
                // 透過單位編號取得單位名字
                $unit = $kcUnitModel->getUnitName($data->kcunit_id);
                $data->unit_name = $unit->kcunit_name;
                // 透過自己資料表的預警型態欄位設定要勾選 21 或 32 預警
                if ($data->kcwarning_type == '21') {
                    $data->kcwarning_half = '21';
                    $data->kcwarning_three_quarters = "-";
                } if ($data->kcwarning_type == '32') {
                    $data->kcwarning_half = '-';
                    $data->kcwarning_three_quarters = "32";
                }
                // 透過自己資料表的課業不佳型態欄位設定要勾選 21 或 32 預警
                if ($data->kcwarning_poor_schoolwork == '21') {
                    $data->kcwarning_half_poor_schoolwork = '21';
                    $data->kcwarning_three_quarters_poor_schoolwork = "";
                } if ($data->kcwarning_poor_schoolwork == '32') {
                    $data->kcwarning_half_poor_schoolwork = '';
                    $data->kcwarning_three_quarters_poor_schoolwork = "32";
                }
                $decide = $KCWarningCommon->decideStudentTutor($data->kcstudent_user_id, $thisYear, $data->kcwarning_sem_term);
                if ($decide) {
                    $data->kcwarning_have_counseling = 'yes';
                } else {
                    $data->kcwarning_have_counseling = 'no';
                }
            }
        }
        $items = array();
        if ($results) {
            foreach ($results as $result) {
                $items[] = array(
                    'row_id' => "",
                    'kcwarning_id' => $result->kcwarning_id,
                    'kcemployee_user_id' => $result->kcemployee_user_id,
                    'kcunit_id' => $result->kcunit_id,
                    'kcwarning_sem_year' => $result->kcwarning_sem_year,
                    'kcwarning_sem_term' => $result->kcwarning_sem_term,
                    'kcunit_name' => $result->unit_name,
                    'kcclass_name' => $result->kcclass_name,
                    'kcstudent_user_id' => $result->kcstudent_user_id,
                    'kcstudent_name' => $result->stu,
                    'kcteacher_name' => $result->tea,
                    'kcwarning_half_poor_schoolwork' => $result->kcwarning_half_poor_schoolwork,
                    'kcwarning_three_quarters_poor_schoolwork' => $result->kcwarning_three_quarters_poor_schoolwork,
                    'kcwarning_poor_schoolwork_subject' => $result->kcwarning_poor_schoolwork_subject,
                    'kcwarning_half' => $result->kcwarning_half,
                    'kcwarning_three_quarters' => $result->kcwarning_three_quarters,
                    'kcwarning_subject' => $result->kcwarning_subject,
                    'kcwarning_advice_times' => $result->kcwarning_advice_times,
                    'kcwarning_have_counseling' => $result->kcwarning_have_counseling,
                );
            }
        }
        $data = array(
            'identifier' => "kcwarning_id",
            'label' => null,
            'items' => $items,
            'numRows' => intval($numrows),
        );
        echo json_encode($data);
    }

}

?>
