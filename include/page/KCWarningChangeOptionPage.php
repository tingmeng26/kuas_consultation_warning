<?php

class KCWarningChangeOptionPage extends ModuleAdminView implements KuasConsultationActionListener {

    protected $common = null;
    protected $option = null;
    protected $optionResult = null;

    public function __construct() {
        parent::__construct("kuas_consultation_warning");
        $this->tpl = $this->initTemplate();
        $this->common = new KCConsultationCommon();
        $this->option = new KCConsultationGuidanceOptionModel();
        $this->optionResult = new KCConsultationGuidanceOptionResultModel();
    }

    /**
     * 切換選項資料
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $queId[] = 0;
        $post = $event->getPost();
        $newQueId = $post['newQueId'];
        $guidance = $post['guidance'];
        $newQueId = db_escape($newQueId);
        $queId = explode(",", $newQueId);
        $this->option->setOptionQueIdIn($queId);
        $list = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        // 取得項目第一層
        $this->option->setOptionQueId(0);
        $listZero = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        if (!empty($listZero)) {
            foreach ($listZero as $data) {
                // 取得輔導類別的編號
                if ($data->kcgoption_text == '輔導類別') {
                    $guidanceOptionId = $data->kcgoption_id;
                }
                if ($data->kcgoption_text == '學生來源') {
                    $sourceOptionId = $data->kcgoption_id;
                }
                if ($data->kcgoption_text == '精神狀況') {
                    $mindOptionId = $data->kcgoption_id;
                }
                if ($data->kcgoption_text == '危機狀況') {
                    $dangerousOptionId = $data->kcgoption_id;
                }
                if ($data->kcgoption_text == '輔導方式') {
                    $wayOptionId = $data->kcgoption_id;
                }
            }
        }
        // 設定輔導類別父類別編號
        $this->option->setOptionQueId($guidanceOptionId);
        $listWarning = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        if (!empty($listWarning)) {
            foreach ($listWarning as $data) {
                if ($data->kcgoption_text == '預警輔導') {
                    $warningId = $data->kcgoption_id;
                } else {
                    $warningIdArray[] = $data->kcgoption_id;
                }
            }
        }
        // 設定學生來源父類別編號
        $this->option->setOptionQueId($sourceOptionId);
        $listSource = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        if (!empty($listSource)) {
            foreach ($listSource as $data) {
                if ($data->kcgoption_text == '約談') {
                    $interviewId = $data->kcgoption_id;
                }
            }
        }
        // 設定精神狀況父類別編號
        $this->option->setOptionQueId($mindOptionId);
        $listMind = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        if (!empty($listMind)) {
            foreach ($listMind as $data) {
                if ($data->kcgoption_text == '無特殊狀況') {
                    $noSpecialConditionId = $data->kcgoption_id;
                }
            }
        }
        // 設定危機狀況父類別編號
        $this->option->setOptionQueId($dangerousOptionId);
        $listDangerous = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        if (!empty($listDangerous)) {
            foreach ($listDangerous as $data) {
                if ($data->kcgoption_text == '無危機') {
                    $noDangerousId = $data->kcgoption_id;
                }
            }
        }
        // 設定輔導方式父類別編號
        $this->option->setOptionQueId($wayOptionId);
        $listWay = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        if (!empty($listWay)) {
            foreach ($listWay as $data) {
                if ($data->kcgoption_text == '個別談話') {
                    $individualTalkId = $data->kcgoption_id;
                }
            }
        }


        // 設定父類別編號
        $this->option->setOptionQueId($warningId);
        $listWarningQue = $this->option->getKCConsultationGuidanceOptionListByEnable();
        $this->option->clearCondition();
        if (!empty($listWarningQue)) {
            foreach ($listWarningQue as $data) {
                if ($data->kcgoption_text == '期中預警') {
                    $warningMidId = $data->kcgoption_id;
                } else {
                    $warningIdArray[] = $data->kcgoption_id;
                }
            }
        }
        $tran = false;
        if (isset($list)) {
            foreach ($list as $row) {
                // 判斷型態為 textbox，則要轉換成同一階級的型態，並且紀錄轉換過 tran = true
                if ($row->kcgoption_type == "textbox") {
                    $this->option->setOptionQueId($row->kcgoption_que_id);
                    $list = $this->option->getKCConsultationGuidanceOptionListByEnable();
                    $this->option->clearCondition();
                    if (isset($list)) {
                        $type = $list[0]->kcgoption_type;
                        $tran = true;
                    } else {
                        $type = $row->kcgoption_type;
                        $tran = false;
                    }
                } else {
                    $type = $row->kcgoption_type;
                    $tran = false;
                }
                $checked = false;
                $content = "";
                $this->optionResult->setConditionForEqualByGuidanceId($guidance);
                $result = $this->optionResult->getKCConsultationGuidanceOptionResultByOptionId($row->kcgoption_id);
                // 判斷是否勾選
                if (isset($result)) {
                    $checked = true;
                    $content = $result->kcgoresult_text;
                }
                switch ($row->kcgoption_id) {
                    // 預警輔導與期中預警預設為勾不可更改
                    case $row->kcgoption_id == $warningId || $row->kcgoption_id == $warningMidId:
                        $option[] = array(
                            'id' => $row->kcgoption_id,
                            'name' => $row->kcgoption_text,
                            'type' => $this->common->getDijitType($type),
                            'desc' => $row->kcgoption_desc,
                            'limit' => $row->kcgoption_limit,
                            'tran' => $tran,
                            'queId' => $row->kcgoption_que_id,
                            'checked' => true,
                            'content' => $content,
                            'readonly' => true,
                        );
                        break;
                    // 約談、無特殊狀況、無危機、個別談話預設勾選可更改
                    case $row->kcgoption_id == $interviewId || $row->kcgoption_id == $noSpecialConditionId || $row->kcgoption_id == $individualTalkId || $row->kcgoption_id == $noDangerousId :
                        $option[] = array(
                            'id' => $row->kcgoption_id,
                            'name' => $row->kcgoption_text,
                            'type' => $this->common->getDijitType($type),
                            'desc' => $row->kcgoption_desc,
                            'limit' => $row->kcgoption_limit,
                            'tran' => $tran,
                            'queId' => $row->kcgoption_que_id,
                            'checked' => true,
                            'content' => $content,
                            'readonly' => false,
                        );
                        break;
                    // 輔導類別所有子項目不可更改
                    case in_array($row->kcgoption_id, $warningIdArray):
                        $option[] = array(
                            'id' => $row->kcgoption_id,
                            'name' => $row->kcgoption_text,
                            'type' => $this->common->getDijitType($type),
                            'desc' => $row->kcgoption_desc,
                            'limit' => $row->kcgoption_limit,
                            'tran' => $tran,
                            'queId' => $row->kcgoption_que_id,
                            'checked' => $checked,
                            'content' => $content,
                            'readonly' => true,
                        );
                        break;
                    default :
                        $option[] = array(
                            'id' => $row->kcgoption_id,
                            'name' => $row->kcgoption_text,
                            'type' => $this->common->getDijitType($type),
                            'desc' => $row->kcgoption_desc,
                            'limit' => $row->kcgoption_limit,
                            'tran' => $tran,
                            'queId' => $row->kcgoption_que_id,
                            'checked' => $checked,
                            'content' => $content,
                            'readonly' => false,
                        );
                        break;
                }
            }
        }
        $data = array();
        $data['newQueId'] = $queId;
        $data['optionView'] = $option;
        echo(json_encode($data));
    }

}
?>

