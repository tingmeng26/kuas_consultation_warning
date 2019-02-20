<?php

class KCWarningGetOptionAction implements KuasConsultationActionListener {

    public function __construct() {
        
    }

    /**
     * 取得選項資料
     * @return array 回傳選項資料陣列
     */
    private function getOption($optionId) {
        $this->optionModel->setOptionQueId($optionId);
        $optionList = $this->optionModel->getKCConsultationGuidanceOptionList();
        $this->optionModel->clearCondition();
        return $optionList;
    }
    
    /**
     * 設定該選項被選取
     * @param string 選項型態
     * @return string 該選項為選取狀態
     */
    private function setOptionIsChecked($type) {
        if ($type == "checkbox" || $type = "radio") {
            return "checked";
        }
    }
    
    /**
     * 取得輔導選項
     * @param array  $event ( GET,  POST )
     * @return array 輔導選項資料陣列
     */
    public function actionPerformed($event) {
        $post = $event->getPost();
        $guidanceId = $post['guidanceId'];
        $optionArray = $post['optionArray'];
        $this->optionModel = new KCConsultationGuidanceOptionModel();
        $items = array();
        $optionArray = explode(",", $optionArray);
        // 取得第一層選項的 id
        $option = $this->getOption("0");
        $optionString="";
        if (isset($option)) {
            foreach ($option as $row) {
                $optionString.=$row->kcgoption_id . ",";
            }
            $optionString = substr($optionString, 0, -1);
            $optionString = explode(",", $optionString);
        }
        if (isset($optionArray)) {
            foreach ($optionArray as $optionId) {
                // 取得該選項資料
                $option = $this->optionModel->getOptionDataByOptionId($optionId);
                if (isset($option)) {
                    $KCConsultationGuidanceOptionResultModel = new KCConsultationGuidanceOptionResultModel();
                    $KCConsultationGuidanceOptionResultModel->setConditionForEqualByGuidanceId($guidanceId);
                    $resultOptionData = $KCConsultationGuidanceOptionResultModel->getKCConsultationGuidanceOptionResultByOptionId($option->kcgoption_id);
                    $KCConsultationGuidanceOptionResultModel->clearCondition();
                    // 判斷當前選項是否在此輔導紀錄有被選取
                    if (isset($resultOptionData)) {
                        $checked = $this->setOptionIsChecked($option->kcgoption_type);
                        $option->checkType = "";
                        $resultOptionData->kcgoresult_text=nl2br($resultOptionData->kcgoresult_text);
                        // 若 kcgoption_type 為 textbox 則去判斷同一父選項下其他選項的選項型態為 radio or checkbox
                        if ($option->kcgoption_type == "textbox") {
                            $this->optionModel->setOptionQueId($option->kcgoption_que_id);
                            $optionData = $this->optionModel->getKCConsultationGuidanceOptionList();
                            $this->optionModel->clearCondition();
                            $option->checkType = $optionData[0]->kcgoption_type;
                            if ($optionData[0]->kcgoption_type == "checkbox") {
                                $option->checkType = "checkbox";
                                $checked = $this->setOptionIsChecked($option->checkType);
                            }
                            if ($optionData[0]->kcgoption_type == "radio") {
                                $option->checkType = "radio";
                                $checked = $this->setOptionIsChecked($option->checkType);
                            }
                        }
                    } else {
                        $checked = "";
                        $option->checkType = "";
                        // 若 kcgoption_type 為  textbox 則去判斷同階的選項型態為 radio or checkbox
                        if ($option->kcgoption_type == "textbox") {
                            $this->optionModel->setOptionQueId($option->kcgoption_que_id);
                            $optionData = $this->optionModel->getKCConsultationGuidanceOptionList();
                            $this->optionModel->clearCondition();
                            $option->checkType = $optionData[0]->kcgoption_type;
                            if ($optionData[0]->kcgoption_type == "checkbox") {
                                $option->checkType = "checkbox";
                            }
                            if ($optionData[0]->kcgoption_type == "radio") {
                                $option->checkType = "radio";
                            }
                        }
                    }
                    // 判斷選項父類別是否有被選取
                    $KCConsultationGuidanceOptionResultModel->setConditionForEqualByGuidanceId($guidanceId);
                    $queCount = $KCConsultationGuidanceOptionResultModel->getKCConsultationGuidanceOptionResultCountByOptionId($option->kcgoption_que_id);
                    $KCConsultationGuidanceOptionResultModel->clearCondition();
                    if ($queCount != "0") {
                        $queChecked = "checked";
                    } else {
                        $queChecked = "";
                        // 判斷該選項的父選項是否為第一層（ 因為父選項為第一層時，第一層型態為 span 須作額外判斷 ）
                        if (in_array($option->kcgoption_que_id, $optionString)) {
                            $queChecked = "checked";
                        }
                    }
                    $items[] = array(
                        'id' => $option->kcgoption_id,
                        'queId' => $option->kcgoption_que_id,
                        'name' => $option->kcgoption_text,
                        'type' => $option->kcgoption_type,
                        'desc' => $resultOptionData->kcgoresult_text,
                        'queChecked' => $queChecked,
                        'checked' => $checked,
                        'checkType' => $option->checkType,
                    );
                }
            }
        }
        echo json_encode($items);
    }
}
?>

