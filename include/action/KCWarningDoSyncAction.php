<?php

class KCWarningDoSyncAction implements KuasConsultationActionListener {

    /**
     * 預警同步
     * @param array $event ( GET, POST )
     */
    public function actionPerformed($event) {
        // 取得學年度 select 資料，從今年 ~ 95
        $year = new KuasConsultationCommon(); //取得所有年
        $yearData = array_reverse($year->getYears());
        $thisMonth = date('m');
        // 從 2/1 ~ 7/31始算下學期
        $monthArray = array('02', '03', '04', '05', '06', '07');
        $term = '1';
        foreach ($monthArray as $row) {
            if ($thisMonth == $row) {
                $term = '2';
            }
        }
        try {
            $sync = new KCSyncWarningData();
            $sync->doSyncWarningData(0, "$yearData[0]", "$term");
            $sync->doSyncWarningData(1, "$yearData[0]", "$term");
        } catch (Exception $exc) {
            echo"1";
        }
        echo "0";
    }

}

?>
