<?php

class KCWarningCommon {

    /**
     * 設定資料庫查詢日期的區間，開始時間
     * @param string $thisYear 學年
     * @param string $searchByTerm 學期
     * @return string 開始時間
     */
    public function getStartTime($thisYear, $searchByTerm) {
        // 開始時間
        $year = $thisYear + 1911;
        // 選全學期 為  今年 2/1~明年1/31
        if (empty($searchByTerm)) {
            $startDate = $year . "-08-01";
        }
        // 選上學期 為 今年8/1 ~ 明年1/31
        if ($searchByTerm == '1') {
            $startDate = $year . "-08-01";
        }
        // 選下學期 為 今年 2/1~7/31
        if ($searchByTerm == '2') {
            $year+=1;
            $startDate = $year . "-02-01";
        }
        return $startDate;
    }

    /**
     * 設定資料庫查詢日期的區間，結束時間
     * @param string $thisYear 學年
     * @param string $searchByTerm 學期
     * @return string 結束時間
     */
    public function getEndTime($thisYear, $searchByTerm) {
        // 開始時間
        $year = $thisYear + 1911;
        // 結束時間
        $endYear = $year + 1;
        // 選全學期 為  今年 2/1~明年 1/31
        if (empty($searchByTerm)) {
            $endDate = $endYear . "-07-31";
        }
        // 選上學期 為 今年8/1 ~ 明年 1/31
        if ($searchByTerm == '1') {
            $endDate = $endYear . "-01-31";
        }
        // 選下學期 為 今年 2/1~7/31
        if ($searchByTerm == '2') {
            $endDate = $endYear . "-07-31";
        }
        return $endDate;
    }

    /**
     * 判斷學生輔導狀況
     * @param string $studentId 學生帳號
     * @param string $year 學年度
     * @param string $term 學期
     */
    public function decideStudentTutor($studentId, $year, $term) {
        $KCConsultationReservationModel = new KCConsultationReservationModel();
        $KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
        $KCConsultationGuidancePersonalModel = new KCConsultationGuidancePersonalModel();
        // 在 reservation 表，設定學生帳號
        $KCConsultationReservationModel->setConditionForEqualInStudentUserId((array) $studentId);
        // 在 reservation 表，設定為已出席
        $KCConsultationReservationModel->setConditionForEqualByReservationAttendance("attend");
        // 開始時間，從預警共用函數
        $startDate = $this->getStartTime($year, $term);
        // 結束時間，從預警共用函數
        $endDate = $this->getEndTime($year, $term);
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
        // 在預約表中由門診編號取得資料
        if ($isCustomConsultationIdArray) {
            $KCConsultationGuidanceModel->setConditionForEqualInConsultationId($isCustomConsultationIdArray);
            $guidanceArray = $KCConsultationGuidanceModel->getGuidanceList();
        }
        // 取得預約編號
        if ($guidanceArray) {
            foreach ($guidanceArray as $data) {
                $guidanceIdArray[] = $data->kcguidance_id;
            }
        }
        // 將預約編號帶入個人輔導記錄表查詢資料
        if ($guidanceIdArray) {
            foreach ($guidanceIdArray as $data) {
                $guidanceResult = $KCConsultationGuidancePersonalModel->getPersonalGuidanceDataByGuidanceId($data);
            }
        }
        if ($guidanceResult) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 回傳單筆學生預警情況
     * @param string $year 學年度 
     * @param string $term 學期
     * @param string $id 使用者帳號
     * @return string 預警情況
     */
    public function getRecentlyStudentWarningType($year, $term, $id) {
        $KCWarningModel = new KCWarningModel();
        $result = $KCWarningModel->getRecentlyStudentWarning($year, $term, $id);
        $returnArray['type'] = '';
        if (empty($result)) {
            $returnArray['warningSubject'] = '';
            $returnArray['poorSchoolSubject'] = '';
            return $returnArray;
        }
        if ($result->kcwarning_type != 'no') {
            $returnArray['type'] = '期中預警';
        }
        if ($result->kcwarning_poor_schoolwork != 'no') {
            $returnArray['type'] = '課業不佳';
        }
        if (($result->kcwarning_type == '21' || $result->kcwarning_type == '32') && ($result->kcwarning_poor_schoolwork == '21' || $result->kcwarning_poor_schoolwork == '32')) {
            $returnArray['type'] = '課業不佳、期中預警';
        }

        $returnArray['warningSubject'] = $result->kcwarning_subject;
        $returnArray['poorSchoolSubject'] = $result->kcwarning_poor_schoolwork_subject;
        return $returnArray;
    }

    /**
     * 取得所有預警學生編號
     * @return array 所有預警學生編號
     */
    public function getWarningStudentId() {
        $KCWarningModel = new KCWarningModel();
        $warningData = $KCWarningModel->getKCWarningList();
        $studentIdArray = array();
        if (isset($warningData)) {
            foreach ($warningData as $row) {
                $studentIdArray[] = $row->kcstudent_user_id;
            }
        }
        return $studentIdArray;
    }

    /**
     * 取得所有參與預警老師編號
     * @return array 所有預警老師編號
     */
    public function getWarningTeacherId() {
        $KCWarningModel = new KCWarningModel();
        $warningData = $KCWarningModel->getKCWarningList();
        $teacherIdArray = array();
        if (isset($warningData)) {
            foreach ($warningData as $row) {
                $teacherIdArray[] = $row->kcemployee_user_id;
            }
        }
        return $teacherIdArray;
    }

}

?>
