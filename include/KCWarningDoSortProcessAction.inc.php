<?php

class KCWarningDoSortProcessAction {

    /**
     * 依照 grid 可排序欄位排序
     * @param string $sort grid sort
     * @return array 排序順序及排序欄位
     */
    public function processSort($sort) {
        // 將 grid 透過以欄位為基準排序
        $sortType = "ASC";
        $sortField = null;
        $sortLength = strlen($sort);
        if (substr($sort, 0, 1) == "-") {
            $sortType = "DESC";
            $sortField = substr($sort, 1, $sortLength - 1);
        } else {
            $sortField = $sort;
        }
        return array(
            "sortType" => $sortType,
            "sortField" => $sortField
        );
    }

}

?>
