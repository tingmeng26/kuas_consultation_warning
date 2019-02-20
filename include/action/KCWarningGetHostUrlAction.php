<?php

class KCWarningGetHostUrlAction implements KuasConsultationActionListener {

    public function actionPerformed($event) {
	// 取得預約模組網址
	echo "http://" . URL_HOST . '/' . URL_ROOT . "/main.php?mod=kuas_consultation_reservation";
    }

}

?>
