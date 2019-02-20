<?php

class KCWarningInsertWarningGuidanceAction implements KuasConsultationActionListener {

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
         // 新增選項明細
        $optionId = $post['optionId'];
        $optionId = db_escape($optionId);
        $optionId = substr($optionId, 0, strlen($optionId) - 1);
        $optionId = explode(",", $optionId);
        $optionNote = $post['optionNote'];
        $optionNote = substr($optionNote, 0, strlen($optionNote) - 1);
        $optionNote = db_escape($optionNote);
        $optionNote = explode(",", $optionNote);
        
        $response=$kccommon->addWarningGuidance("", $post['kcstudentUseId'], $post['kcunitId'], $post['kcteacherUserId'], $post['kcguidanceTopic'], $post['kcguidanceTotalHours'], $post['kcguidanceAddress'], $post['kcguidanceStartTime'], $post['kcguidanceEndTime'], $post['kcguidanceType'], $post['kcguidanceFileLevel'], $post['kcgpersonalPublicComment'], $post['kcgpersonalPrivateComment'],$optionId, $optionNote, $post['reportType']);
        echo true;
    }

}

?>
