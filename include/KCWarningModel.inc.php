<?php

class KCWarningModel {

    public $db = null;
    private $condition = "";
    private $sortStartCondition = "";
    private $sortCondition = "";

    public function __construct() {
        $this->db = init_db();
    }

    /**
     * 取得 kuas_learning_warning results
     * @return array 預警資料
     */
    function getKCWarningList() {
        $sql = "SELECT `kcwarning_id`,`kcunit_id`,`kcemployee_user_id`,`kcstudent_user_id`,`kcwarning_sem_year`,`kcwarning_sem_term`,`kcclass_name`,`kcwarning_type`,`kcwarning_poor_schoolwork`,`kcwarning_advice_times`,`kcwarning_poor_schoolwork_subject`,`kcwarning_subject` FROM `mod_kuas_consultation_warning` WHERE 1  ";
        $sql.=$this->condition;
        $sql.=$this->sortStartCondition;
        return $this->db->get_results($sql);
    }

    /**
     * 取得有預警資料的學年度
     * @return array 有預警資料的學年度
     */
    function getSelectYear() {
        $sql = "SELECT distinct `kcwarning_sem_year` FROM `mod_kuas_consultation_warning` order by `kcwarning_sem_year` desc";
        return $this->db->get_results($sql);
    }

    /**
     * 取得有預警資料單位編號
     * @return array 有預警資料單位編號
     */
    function getSelectUnit() {
        $sql = "SELECT distinct `kcunit_id` FROM `mod_kuas_consultation_warning` group by `kcunit_id`desc";
        return $this->db->get_results($sql);
    }

    /**
     * 取得寄通知信需要的資料
     * @return array 寄通知信需要的資料
     */
    function getLetterValue() {
        $sql = "SELECT `kcwarning_id`,`kcstudent_user_id` FROM `mod_kuas_consultation_warning` WHERE 1 ";
        $sql.=$this->condition;
        return $this->db->get_results($sql);
    }

    /**
     * 取得匯出 excel 的資料
     * @return array 匯出 excel 的資料
     */
    function getExcelValue() {
        $sql = "SELECT `kcwarning_id`,`kcunit_id`,`kcemployee_user_id`,`kcstudent_user_id`,`kcwarning_sem_year`,`kcwarning_sem_term`,`kcclass_name`,`kcwarning_type`,`kcwarning_poor_schoolwork`,`kcwarning_advice_times`,`kcwarning_poor_schoolwork_subject`,`kcwarning_subject` FROM `mod_kuas_consultation_warning` WHERE 1 ";
        $sql.=$this->condition;
        $sql.=$this->sortCondition;
        return $this->db->get_results($sql);
    }

    /**
     * 取得通知次數
     * @return array 通知次數
     */
    function getAdviceTime() {
        $sql = "SELECT `kcwarning_advice_times` FROM `mod_kuas_consultation_warning` WHERE 1 ";
        $sql.=$this->condition;
        return $this->db->get_results($sql);
    }

    /**
     * 取得資料筆數
     * @return string 資料筆數
     */
    function getKCWarningCount() {
        $sql = "SELECT COUNT(*)  FROM `mod_kuas_consultation_warning` WHERE 1 ";
        $sql.=$this->condition;
        return $this->db->get_var($sql);
    }

    /**
     * 設定 sort start count
     * @param string $sortType (ASC or DESC)
     * @param string $sortField (欄位名稱)
     * @param string $start 開始的筆數
     * @param string $count 總筆數
     */
    function setSortStartCount($sortType, $sortField, $start, $count) {
        $sortType = db_escape($sortType);
        $sortField = db_escape($sortField);
        $start = db_escape($start);
        $count = db_escape($count);
        $this->sortStartCondition.="ORDER BY  `$sortField` $sortType LIMIT $start , $count ";
    }

    /**
     * 設定 sort
     * @param $sortType  (ASC or DESC)
     * @param $sortField (欄位名稱)
     */
    function setSort($sortType, $sortField) {
        $sortType = db_escape($sortType);
        $sortField = db_escape($sortField);
        $this->sortCondition.="ORDER BY  `$sortField` $sortType ";
    }

    /**
     * 修改通知次數
     * @param $id 預警清單id
     * @param $times 該筆資料通知次數+1
     * @return boolean 是否修改成功
     */
    function setAdviceTimes($id, $times) {
        $id = db_escape($id);
        $times = db_escape($times);
        $sql = "UPDATE `mod_kuas_consultation_warning` SET  `kcwarning_advice_times` =  '$times' 
	    WHERE `kcwarning_id` ='$id'";
        return $this->db->query($sql);
    }

    /**
     * 設定學期
     * @param $term 學期 上為 1 下為 2
     */
    function setConditionForEqualByTerm($term) {
        $term = db_escape($term);
        $this->condition.="AND `kcwarning_sem_term` = $term ";
    }

    /**
     * 設定條件
     * @param $thisYear 選擇的學年度
     */
    public function setConditionForEqualByYear($thisYear) {
        $thisYear = db_escape($thisYear);
        $this->condition.= "AND `mod_kuas_consultation_warning`.`kcwarning_sem_year` = '$thisYear' ";
    }

    /**
     * 設定學生 ID for IN 語法  
     * 教師查詢名字有資料時
     * @param $id 學生 id 用逗號隔開的字串
     */
    public function setConditionForEqualById($id) {
        $this->condition.= "AND (`kcstudent_user_id` in('$id') ";
    }

    /**
     * 設定學生 ID for IN 語法   
     * 教師為空值時
     * @param $id 學生 id 用逗號隔開的字串
     */
    public function setConditionForEqualByNoTeacherDataId($id) {
        $this->condition.= "AND `kcstudent_user_id` in('$id') ";
    }

    /**
     * 設定學生 ID for IN 語法 
     * @param $id 學生 id 用逗號隔開的字串
     */
    public function setConditionForEqualByNotGuidanceId($id) {
        $this->condition.= "AND `kcstudent_user_id` Not In('$id') ";
    }

    /**
     * 設定老師 ID for IN 語法
     * @param $id 老師 id 用逗號隔開的字串
     */
    public function setConditionForEqualByTeacherId($id) {
        $this->condition.= "or `kcemployee_user_id` in('$id'))";
    }

    /**
     * 設定老師 ID for IN 語法
     * 學生名字查詢為空值時
     * @param $id 老師 id 用逗號隔開的字串
     */
    public function setConditionForEqualByNoStudentDataTeacherId($id) {
        $this->condition.= "AND `kcemployee_user_id` in('$id')";
    }

    /**
     * 設定預警 ID  
     * @param $id 預警清單 id
     */
    public function setConditionForEqualByUserId($id) {
        $id = db_escape($id);
        $this->condition.= "AND `kcwarning_id` = '$id' ";
    }

    /**
     * 設定單位 id
     * @param $id 單位 id
     */
    public function setConditionForEqualByUnitId($id) {
        $id = db_escape($id);
        $this->condition.="AND `kcunit_id` = '$id' ";
    }

    /**
     * 設定老師 user_id
     * @param $user_id 給班導權限
     */
    public function setConditionForEqualByTeacherUserId($user_id) {
        $id = db_escape($id);
        $this->condition.="AND `kcemployee_user_id` = '$user_id' ";
    }

    /**
     * 設定通知次數
     */
    public function setConditionForEqualByAdviceTime() {
        $this->condition.="AND `kcwarning_advice_times` >0 ";
    }

    /**
     * 設定未通知
     */
    public function setConditionForEqualByZeroAdviceTime() {
        $this->condition.="AND `kcwarning_advice_times` =0 ";
    }

    /**
     * 設定預警型態
     * @param string $value 預警型態
     */
    public function setConditionForEqualByWarningType($value) {
        $value = db_escape($value);
        $this->condition.="AND `kcwarning_type` = '$value' ";
    }

    /**
     * 設定課業不佳的型態
     * @param string $value 型態
     */
    public function setConditionForEqualByPoorSchoolWork($value) {
        $this->condition.="AND `kcwarning_poor_schoolwork` = '$value' ";
    }

    /**
     * 設定預警型態為 21 或 32
     */
    public function setConditionForEqualByChooseType() {
        $this->condition.="AND (`kcwarning_type`='21' OR `kcwarning_type`='32') ";
    }

    /**
     * 設定預警型態為課業不佳
     */
    public function setConditionForEqualByChoosePoorType() {
        $this->condition.="AND (`kcwarning_poor_schoolwork`='21' || `kcwarning_poor_schoolwork`='32') ";
    }

    /**
     * 回傳單筆學生預警資料
     * @param string $year 學年
     * @param string $term 學期
     * @param string $id 使用者帳號
     * @return array 預警資料
     */
    public function getRecentlyStudentWarning($year,$term, $id) {
        $year = db_escape($year);
	$term = db_escape($term);
        $id = db_escape($id);
        $sql = "SELECT `kcwarning_id`,`kcunit_id`,`kcemployee_user_id`,`kcstudent_user_id`,`kcwarning_sem_year`,`kcwarning_sem_term`,`kcclass_name`,`kcwarning_type`,`kcwarning_poor_schoolwork`,`kcwarning_advice_times`,`kcwarning_poor_schoolwork_subject`,`kcwarning_subject` FROM `mod_kuas_consultation_warning` WHERE  `kcwarning_sem_year`='$year' AND `kcwarning_sem_term`='$term' AND `kcstudent_user_id`='$id'";
        return $this->db->get_row($sql);
    }

    /**
     * 根據學生編號和學年和學期回傳預警資料
     * @param string $year 學年
     * @param string $term 學期
     * @param string $id 使用者帳號
     * @return array 預警資料
     */
    public function getWarningByStudentIdAndYearAndTerm($year, $term, $id) {
        $year = db_escape($year);
        $term = db_escape($term);
        $id = db_escape($id);
        $sql = "SELECT `kcwarning_id`,`kcunit_id`,`kcemployee_user_id`,`kcstudent_user_id`,`kcwarning_sem_year`,`kcwarning_sem_term`,`kcclass_name`,`kcwarning_type`,`kcwarning_poor_schoolwork`,`kcwarning_advice_times`,`kcwarning_poor_schoolwork_subject`,`kcwarning_subject` FROM `mod_kuas_consultation_warning` WHERE  `kcwarning_sem_year`='$year' AND `kcwarning_sem_term`='$term' AND `kcstudent_user_id`='$id'";
        return $this->db->get_row($sql);
    }
    
    /*
     * 只找出期中預警科目有值的資料
     */
    public function setConditionForWarningSubjectNotEmpty(){
        $this->condition.="AND `kcwarning_subject` !='' ";
    }

	/**
	 * 新增預警資料
	 * @param int $kcunit_id 單位編號
	 * @param string $kcemployee_user_id 職員編號
	 * @param string $kcstudent_user_id 學生編號
	 * @param int $kcwarning_sem_year 學年
	 * @param int $kcwarning_sem_term 學期
	 * @param string $kcclass_name 班級名稱
	 * @param string $kcwarning_type 預警類型
	 * @param string $kcwarning_poor_schoolwork 是否課業不佳
	 */
	public function addWarning($kcunit_id, $kcemployee_user_id, $kcstudent_user_id, $kcwarning_sem_year, $kcwarning_sem_term, $kcclass_name, $kcwarning_type, $kcwarning_poor_schoolwork, $kcwarning_poor_schoolwork_subject = null, $kcwarning_subject = null) {
		$kcunit_id = db_escape($kcunit_id);
		$kcemployee_user_id = db_escape($kcemployee_user_id);
		$kcstudent_user_id = db_escape($kcstudent_user_id);
		$kcwarning_sem_year = db_escape($kcwarning_sem_year);
		$kcwarning_sem_term = db_escape($kcwarning_sem_term);
		$kcclass_name = db_escape($kcclass_name);
		$kcwarning_type = db_escape($kcwarning_type);
		$kcwarning_poor_schoolwork = db_escape($kcwarning_poor_schoolwork);
		$kcwarning_poor_schoolwork_subject = db_escape($kcwarning_poor_schoolwork_subject);
		$kcwarning_subject = db_escape($kcwarning_subject);
		$sql = "INSERT INTO mod_kuas_consultation_warning (kcunit_id,kcemployee_user_id,kcstudent_user_id,kcwarning_sem_year,kcwarning_sem_term,kcclass_name,kcwarning_type,kcwarning_poor_schoolwork,kcwarning_poor_schoolwork_subject,kcwarning_subject)
			VALUES ('$kcunit_id', '$kcemployee_user_id', '$kcstudent_user_id', '$kcwarning_sem_year', '$kcwarning_sem_term', '$kcclass_name', '$kcwarning_type', '$kcwarning_poor_schoolwork','$kcwarning_poor_schoolwork_subject', '$kcwarning_subject')";
		$this->db->query($sql);
	}

	/**
	 * 更新學生
	 * @param int $kcunit_id 單位編號
	 * @param string $kcemployee_user_id 職員編號
	 * @param string $kcstudent_user_id 學生編號
	 * @param int $kcwarning_sem_year 學年
	 * @param int $kcwarning_sem_term 學期
	 * @param string $kcclass_name 班級名稱
	 * @param string $kcwarning_type 預警類型
	 * @param string $kcwarning_poor_schoolwork 課業不佳
	 * @param string $kcwarning_poor_schoolwork_subject 課業不佳預警科目
	 * @param string $kcwarning_subject 期中預警科目
	 * @return boolean true=更新成功/false=更新失敗
	 */
	public function setWarning($kcunit_id, $kcemployee_user_id, $kcstudent_user_id, $kcwarning_sem_year, $kcwarning_sem_term, $kcclass_name, $kcwarning_type, $kcwarning_poor_schoolwork, $kcwarning_poor_schoolwork_subject = null, $kcwarning_subject = null) {
		if (!isset($kcstudent_user_id) || !isset($kcwarning_sem_year) || !isset($kcwarning_sem_term))
			return false;
		$set = '';
		if (isset($kcunit_id))
			$set .= empty($set) ? " kcunit_id = '" . db_escape($kcunit_id) . "' " : " ,kcunit_id = '" . db_escape($kcunit_id) . "' ";
		if (isset($kcemployee_user_id))
			$set .= empty($set) ? " kcemployee_user_id = '" . db_escape($kcemployee_user_id) . "' " : " ,kcemployee_user_id = '" . db_escape($kcemployee_user_id) . "' ";
		if (isset($kcclass_name))
			$set .= empty($set) ? " kcclass_name = '" . db_escape($kcclass_name) . "' " : " ,kcclass_name = '" . db_escape($kcclass_name) . "' ";
		if (isset($kcwarning_type))
			$set .= empty($set) ? " kcwarning_type = '" . db_escape($kcwarning_type) . "' " : " ,kcwarning_type = '" . db_escape($kcwarning_type) . "' ";
		if (isset($kcwarning_poor_schoolwork))
			$set .= empty($set) ? " kcwarning_poor_schoolwork = '" . db_escape($kcwarning_poor_schoolwork) . "' " : " ,kcwarning_poor_schoolwork = '" . db_escape($kcwarning_poor_schoolwork) . "' ";
		if (isset($kcwarning_poor_schoolwork_subject))
			$set .= empty($set) ? " kcwarning_poor_schoolwork_subject = '" . db_escape($kcwarning_poor_schoolwork_subject) . "' " : " ,kcwarning_poor_schoolwork_subject = '" . db_escape($kcwarning_poor_schoolwork_subject) . "' ";
		if (isset($kcwarning_subject))
			$set .= empty($set) ? " kcwarning_subject = '" . db_escape($kcwarning_subject) . "' " : " ,kcwarning_subject = '" . db_escape($kcwarning_subject) . "' ";
		if (empty($set))
			return false;
		$sql = "UPDATE mod_kuas_consultation_warning SET $set WHERE kcstudent_user_id = '" . db_escape($kcstudent_user_id) . "' AND kcwarning_sem_year = '" . db_escape($kcwarning_sem_year) . "' AND kcwarning_sem_term = '" . db_escape($kcwarning_sem_term) . "' ";
		$res = $this->db->query($sql);
		return (!$res) ? false : true;
	}

    /**
     * 清空條件
     */
    function clearCondition() {
        $this->condition = " ";
        $this->sortStartCondition = " ";
        $this->sortCondition = " ";
    }

}

?>
