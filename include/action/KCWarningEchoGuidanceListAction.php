<?php

class KCWarningEchoGuidanceListAction implements KuasConsultationActionListener {
    
    /**
     * 查詢個人輔導記錄 
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
	$get = $event->getGet();
	$kcstudentUserId = $get['kcstudentUserId'];
	$kcwarningSemYear = $get['kcwarningSemYear'];
	$kcwarningSemTerm = $get['kcwarningSemTerm'];
	$KCConsultationReservationModel = new KCConsultationReservationModel();
	$KCConsultationReservationDetailModel = new KCConsultationReservationDetailModel;
	$KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
	$KCEmployeeProfileModel = new KCEmployeeProfileModel(); // 職員模組
	// 在 reservation 表，設定學生帳號
	$KCConsultationReservationModel->setConditionForEqualInStudentUserId((array) $kcstudentUserId);
	// 在 reservation 表，設定為已出席
	$KCConsultationReservationModel->setConditionForEqualByReservationAttendance("attend");
	// 取得 reservation 的資料
	$reservationData = $KCConsultationReservationModel->getReservationList();
	$KCConsultationReservationModel->clearCondition();

	if ($reservationData) {
	    // 將 reservation_id 存成 $reservationDetailData 陣列
	    foreach ($reservationData as $reservationObject) {
		$reservationDetailData[] = $KCConsultationReservationDetailModel->getReservationDetailByReservationId($reservationObject->kcreservation_id);
	    }
	}
	$KCWarningCommon = new KCWarningCommon();
	// 開始時間，從預警共用函數
	$startDate = $KCWarningCommon->getStartTime($kcwarningSemYear, $kcwarningSemTerm);
	// 結束時間，從預警共用函數
	$endDate = $KCWarningCommon->getEndTime($kcwarningSemYear, $kcwarningSemTerm);
	// 從 guidance 表取得資料
	if ($reservationDetailData) {
	    foreach ($reservationDetailData as $guidanceData) {
		if ($guidanceData) {
		    // 設定 guidance_id
		    $KCConsultationGuidanceModel->setConditionForEqualByGuidanceId($guidanceData->kcguidance_id);
		    // 設定為個人輔導
		    $KCConsultationGuidanceModel->setConditionForEqualByPersonalType();
		    // 設定時間區間
		    $KCConsultationGuidanceModel->setConditionForEqualByGuidanceDate($startDate, $endDate);
		    $guidance[] = $KCConsultationGuidanceModel->getGuidanceList();
		    $KCConsultationGuidanceModel->clearCondition();
		}
	    }
	}
	$KCConsultationGuidanceOptionResultModel = new KCConsultationGuidanceOptionResultModel();
	if ($guidance)
	    foreach ($guidance as $row) {
		if ($row)
		    foreach ($row as $result) {
			// 將 guidance_id 存成 $guidanceIdArray 陣列
			$guidanceIdArray[] = $result->kcguidance_id;
		    }
	    }
	if ($guidance)
	    foreach ($guidance as $row) {
		if ($row)
		    foreach ($row as $result) {
			$kcConsultationGuidanceOptionModel = new KCConsultationGuidanceOptionModel();
			// 取得預警輔導編號
			$optionId = $kcConsultationGuidanceOptionModel->getWarningGuidanceOptionId();
			// 取得期中預警編號
			$warningId = $kcConsultationGuidanceOptionModel->getWarningOptionId();
			// 取得課業不佳編號
			$schoolId = $kcConsultationGuidanceOptionModel->getSchoolgOptionId();

			// 設定為類型是預警輔導
			$KCConsultationGuidanceOptionResultModel->setConditionForEqualByQueId($optionId);
			// 設定為期中預警或課業不佳
			$KCConsultationGuidanceOptionResultModel->setConditionForEqualLikeMidtermOrSchoolWork($warningId, $schoolId);
			// 加入 guidanceId
			$KCConsultationGuidanceOptionResultModel->setConditionForEqualByInGuidanceId($guidanceIdArray);
			$guidanceDatas = $KCConsultationGuidanceOptionResultModel->getKCConsultationGuidanceOptionResultList();
			$KCConsultationGuidanceOptionResultModel->clearCondition();
			// 項目包含期中預警
			$KCConsultationGuidanceOptionResultModel->setConditionForEqualByInGuidanceId($guidanceIdArray);
			$KCConsultationGuidanceOptionResultModel->setConditionForEqualByOptionId($warningId);
			// 計算項目包含期中預警次數
			$countMiterm = $KCConsultationGuidanceOptionResultModel->getKCConsultationGuidanceOptionResultCount();
			$KCConsultationGuidanceOptionResultModel->clearCondition();
			// 項目包含課業不佳
			$KCConsultationGuidanceOptionResultModel->setConditionForEqualByInGuidanceId($guidanceIdArray);
			$KCConsultationGuidanceOptionResultModel->setConditionForEqualByOptionId($schoolId);
			// 計算項目包含課業不佳次數
			$countSchool = $KCConsultationGuidanceOptionResultModel->getKCConsultationGuidanceOptionResultCount();
			$KCConsultationGuidanceOptionResultModel->clearCondition();
		    }
	    }
	// 確認有預警輔導的 guidance_id
	if ($guidanceDatas) {
	    foreach ($guidanceDatas as $row) {
		// 將 guidance_id 存成 correctGuidanceIdArray 陣列
		$correctGuidanceIdArray[] = $row->kcguidance_id;
	    }
	    // 將 array 中重複的去除
	    $KCConsultationGuidanceModel->setConditionInGuidanceId(array_unique($correctGuidanceIdArray));
	    // 設定為個人輔導
	    $KCConsultationGuidanceModel->setConditionForEqualByPersonalType();
	    // 設定時間區間
	    $KCConsultationGuidanceModel->setConditionForEqualByGuidanceDate($startDate, $endDate);
	    $guidance = $KCConsultationGuidanceModel->getGuidanceList();
	    $KCConsultationGuidanceModel->clearCondition();
	}

	// 判斷個人輔導記錄是否包含期中預警或課業不佳或兩者都是
	for ($i = 0; $i < count($guidanceDatas); $i++) {
	    if ($guidanceDatas[$i]->kcgoption_id == $warningId) {
		$guidanceDatas[$i]->text = "期中預警";
	    }
	    if ($guidanceDatas[$i]->kcgoption_id == $schoolId) {
		$guidanceDatas[$i]->text = "課業不佳";
	    }
	    for ($j = 0; $j < count($guidanceDatas); $j++) {
		if ($guidanceDatas[$i]->kcguidance_id == $guidanceDatas[$j]->kcguidance_id && (($guidanceDatas[$i]->kcgoption_id == $warningId && $guidanceDatas[$j]->kcgoption_id == $schoolId ) || $guidanceDatas[$i]->kcgoption_id == $schoolId && $guidanceDatas[$j]->kcgoption_id == $warningId)) {
		    $guidanceDatas[$i]->text = "期中預警課業不佳";
		}
	    }
	}
	// 將輔導編號重覆的部分移除
	for ($i = 0; $i < count($guidanceDatas); $i++) {
	    if ($guidanceDatas[$i]) {
		if ($guidanceDatas[$i]->kcguidance_id == $guidanceDatas[$i + 1]->kcguidance_id) {
		    unset($guidanceDatas[$i + 1]);
		}
	    }
	}
	// 期中預警與課業不佳的欄位
	if ($guidance && $guidanceDatas) {
	    foreach ($guidance as $guidanceList) {
		foreach ($guidanceDatas as $row) {
		    if ($guidanceList->kcguidance_id == $row->kcguidance_id) {
			$guidanceList->kcgpersonal_middle_item = $row->text;
			$guidanceList->kcgpersonal_school_item = $row->text;
		    }
		}
	    }
	}
	// 個人輔導記錄 grid 
	if ($guidanceDatas) {
	    foreach ($guidance as $guidanceList) {
		$items[] = array(
		    'row_id' => "",
		    'kcguidance_topic' => $guidanceList->kcguidance_topic,
		    'kcemployee_user_id' => $KCEmployeeProfileModel->getEmployeeDataByUserId($guidanceList->kcemployee_user_id)->kcemployee_name,
		    'kcguidance_time' => $guidanceList->kcguidance_start_time . $guidanceList->kcguidance_end_time,
		    'kcgpersonal_type' => $guidanceDatas->kcgoresult_text,
		    'kcgpersonal_middle_item' => $guidanceList->kcgpersonal_middle_item,
		    'kcgpersonal_school_item' => $guidanceList->kcgpersonal_school_item,
		    'countMiterm' => $countMiterm,
		    'countSchool' => $countSchool
		);
	    }
	}
	$data = array(
	    'identifier' => "kcguidance_topic",
	    'label' => null,
	    'items' => $items,
	);
	echo json_encode($data);
    }

}

?>
