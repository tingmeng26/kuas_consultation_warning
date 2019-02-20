<?php

class KCWarningUpdateWarningGuidanceAction implements KuasConsultationActionListener {

    protected $optionResultModel = null;
    protected $optionModel = null;

    public function __construct() {
        
    }

    /**
     * 新增個人輔導
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $post = $event->getPost();
        $kccommon = new KCConsultationCommon();
         $KCWarningCommon = new KCWarningCommon();
        // 新增選項明細
        $optionId = $post['optionId'];
        $optionId = db_escape($optionId);
        $optionId = substr($optionId, 0, strlen($optionId) - 1);
        $optionId = explode(",", $optionId);
        $optionNote = $post['optionNote'];
        $optionNote = substr($optionNote, 0, strlen($optionNote) - 1);
        $optionNote = db_escape($optionNote);
        $optionNote = explode(",", $optionNote);

        $studentId = $post['kcstudentUseId'];

        $KCConsultationReservationModel = new KCConsultationReservationModel();
        $KCConsultationGuidanceModel = new KCConsultationGuidanceModel();
        $KCConsultationGuidancePersonalModel = new KCConsultationGuidancePersonalModel();
        // 在 reservation 表，設定學生帳號
        $KCConsultationReservationModel->setConditionForEqualInStudentUserId((array) $studentId);
        // 在 reservation 表，設定為已出席
        $KCConsultationReservationModel->setConditionForEqualByReservationAttendance("attend");
        // 開始時間，從預警共用函數
        $startDate = $KCWarningCommon->getStartTime($post['reportYear'], $post['reportTerm']);
        // 結束時間，從預警共用函數
        $endDate =$KCWarningCommon->getEndTime($post['reportYear'], $post['reportTerm']);
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
        $KCConsultationReservationModel->setConditionForEqualByStudentUserId($studentId);
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
       $kccommon->addWarningGuidance($guidanceId, $studentId, $post['kcunitId'], $post['kcteacherUserId'], $post['kcguidanceTopic'], $post['kcguidanceTotalHours'], $post['kcguidanceAddress'], $post['kcguidanceStartTime'], $post['kcguidanceEndTime'], $post['kcguidanceType'], $post['kcguidanceFileLevel'], $post['kcgpersonalPublicComment'], $post['kcgpersonalPrivateComment'], $optionId, $optionNote, $post['reportType']);
       echo true;
       
            }

}

?>
