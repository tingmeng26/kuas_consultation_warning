<?php

class KCWarningCheckAddresseeAction implements KuasConsultationActionListener {

    /**
     * 確認資料是否存在
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        $post = $event->getPost();
        $cancelId = $post['cancelId'];
        $warningId = $post['warningId'];
        $cancelIdString = substr($cancelId, 1, strlen($cancelId) - 1);
        $cancelIdArray = explode(",", $cancelIdString);
        $warningIdString = substr($warningId, 1, strlen($warningId) - 1);
        $warningIdArray = explode(",", $warningIdString);
        $warningIdArray=  array_unique($warningIdArray);
        $result = array_diff ($warningIdArray, $cancelIdArray);
        if(empty($result)){
            echo false;
        }else{
            echo true;
        }
    }

}

?>
