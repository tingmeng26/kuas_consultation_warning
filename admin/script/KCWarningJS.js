dojo.require("dijit.form.Button");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.FilteringSelect");
dojo.require("dijit.form.Select");
dojo.require("dijit.Dialog");
dojo.require("dijit.Toolbar");
dojo.require("dijit.form.SimpleTextarea");
dojo.require("dijit.layout.ContentPane");
dojo.require("dojox.grid.DataGrid");
dojo.require("dojox.grid.Selection");
dojo.require("dojo.io.iframe");
dojo.require("dijit.form.ValidationTextBox");
dojo.require("dojox.form.Uploader");
dojo.require("dojox.data.QueryReadStore");
dojo.require("dijit.form.Form");
dojo.require("dojox.widget.Standby");
dojo.require("dijit.form.CheckBox");
dojo.require("dojo.fx");
dojo.require("dijit.MenuItem");
dojo.require("dijit.form.DropDownButton");
dojo.require("dijit.CheckedMenuItem");
dojo.require("dijit.form.RadioButton");
dojo.require("dojo.ready");
dojo.require("dijit.form.DateTextBox");
dojo.require("dijit.form.TimeTextBox");
dojo.require("dijit.form.NumberSpinner");
dojo.require("dojo.io.iframe");

/**
 * 判斷是否有按鍵觸發 Enter = 搜尋 Esc = 重置
 * @returns (Boolean) 是否完成執行
 */
dojo.ready(function() {
    dojo.connect(dijit.byId("searchByName"), 'onKeyDown', function(e) {
        if (e.keyCode == 13) {
            kcjs.conditionSelect();
        } else if (e.keyCode == 27) {
            kcjs.kcwReset();
        }
    });
    return true;
});
function KCWarningJS() {

    /**
     * 產生 grid 項目編號
     * @param {int} index 索引值
     * @return {int} 項目編號
     */
    this.showNumber = function(value, index) {
        return index + 1;
    }

    /**
     * 欄位為空顯示 -
     * @param {type} value
     * @returns {string} -
     */
    this.emptySign = function(value, index) {
        if (value == "") {
            return "-";
        } else {
            return value;
        }
    }

    /**
     * 課業不佳顯示 "v"
     * @param {string} value 是否課業不佳 yes 或 空值
     * @return {string} 勾勾的圖片 或 空值
     */
    this.checkSchoolWork = function(value) {
        if (value == '21' || value == '32') {
            return "<div style='text-align:center;'><img  src='./modules/kuas_consultation_warning/admin/tmpl/images/ok.png'></div>";
        } else {
            return "<div style='text-align:center;'>-</div>";
        }
    }

    /**
     * 判斷學期 1 為上學期 2 為下學期
     * @param {string} value 學期 
     * @return {string} 1 為上學期 2 為下學期
     */
    this.checkTerm = function(value) {
        if (value == '1') {
            return "上";
        }
        if (value == "2") {
            return "下";
        }
    }

    /**
     * 檢視輔導明細，有期中預警項目以 "v" 顯示
     * @param {string} value 是否期中預警
     * @return {string} 期中預警則回傳勾勾圖片
     */
    this.gridanceMiddle = function(value) {
        var checkStr = '期中預警';
        if (value.indexOf(checkStr) == -1) {
            return "<div style='text-align:center;'>-</div>";
        } else {
            return "<div style='text-align:center;'><img  src='./modules/kuas_consultation_warning/admin/tmpl/images/ok.png'></div>";
        }
    }

    /**
     * 合併欄位
     * @param {type} value
     * @returns {string|String}
     */
    this.mergeField = function(value, index) {
        return "";
    }

    /**
     * 檢視輔導明細，有課業不佳項目以 "v" 顯示
     * @param {string} value 是否課業不佳
     * @return {string} 課業不佳回傳勾勾圖片
     */
    this.gridancePoorWork = function(value) {
        var checkStr = '課業不佳';
        if (value.indexOf(checkStr) == -1) {
            return "<div style='text-align:center;'>-</div>";
        } else {
            return "<div style='text-align:center;'><img  src='./modules/kuas_consultation_warning/admin/tmpl/images/ok.png'></div>";
        }
    }

    /**
     * 分割時間
     * @param {date} date 時間區間
     * @return {string} 開始時間與結束時間
     */
    this.sliceDate = function(date) {
        var start = date.slice(0, 19);
        var end = date.slice(19);
        return start + "<br/> 至 " + end;
    }

    /**
     * 計算期中預警
     * @param {int} value 期中預警的次數
     */
    this.coutMiterm = function(value) {
        dojo.byId("guidanceNote").innerHTML = value;
    }

    /**
     * 計算課業不佳
     * @param {int} value 課業不佳次數
     */
    this.countSchool = function(value) {
        dojo.byId("guidanceSchoolNote").innerHTML = value;
    }

    /**
     * 檢查點選要排序的欄位
     * @param {int} index 欄位的標號
     * @return {boolean} 可排序的欄位
     */
    this.fieldCheck = function(index) {
        index = Math.abs(index)
        if (index == 6 || index == 15) {
            return true;
        }
        return false;
    }

    /**
     * 檢查預警型態，有 21 預警或 32 預警 以 "v" 顯示
     * @param {string} value 預警型態
     * @return {string} 為 21預警 或 32預警 回傳勾勾圖片
     */
    this.checkWarning = function(value) {
        if (value == '21' || value == '32') {
            return "<div style='text-align:center;'><img  src='./modules/kuas_consultation_warning/admin/tmpl/images/ok.png'></div>";
        }
        else {
            return "<div style='text-align:center;'>-</div>";
        }
    }

    /**
     * 按下檢視輔導明細的 dialog
     */
    this.ShowGuidance = function() {
        var grid = dijit.byId("getKCWarningIndexGrid");
        var items = grid.selection.getSelected().length;
        if (items == 0) {
            kcjs._showMessage("請選擇一筆資料", "檢視輔導明細");
        } else {
            var items = grid.selection.getSelected();
            if (items == "") {
                kcjs._showMessage("請選擇一筆資料", "檢視輔導明細");
                return false;
            }
            // 學生帳號
            var kcstudentUserId = grid.store.getValue(items[0], 'kcstudent_user_id');
            // 學生姓名
            var kcstudentName = grid.store.getValue(items[0], 'kcstudent_name');
            // 學年度
            var kcwarningSemYear = grid.store.getValue(items[0], 'kcwarning_sem_year');
            // 學期
            var kcwarningSemTerm = grid.store.getValue(items[0], 'kcwarning_sem_term');
            // 檢視輔導明細標題
            dijit.byId("dialogGuidance").attr("title", "輔導明細" + " " + kcwarningSemYear + "  學年度 第  " + kcwarningSemTerm + " 學期");
            dojo.byId("guidanceName").innerHTML = kcstudentName;
            // 期中與課業不佳次數預設為 0
            dojo.byId("guidanceNote").innerHTML = 0;
            dojo.byId("guidanceSchoolNote").innerHTML = 0;

            var store = new dojox.data.QueryReadStore({
                url: dojo.byId('kcWarningShowGuidanceUrl').value + "&kcstudentUserId=" + kcstudentUserId + "&kcwarningSemYear=" + kcwarningSemYear + "&kcwarningSemTerm=" + kcwarningSemTerm,
                requestMethod: "GET"
            })
            dijit.byId("getKCWarningGuidanceGrid").setStore(store);
            dijit.byId("dialogGuidance").show();
        }
    }

    /**
     * 按下寄發通知信顯示 dialog
     */
    this.ShowLetter = function() {
        // 學年度
        var thisYear = dijit.byId("semYear").getValue();
        // 學期
        var searchByTerm = dijit.byId("searchByTerm").getValue();
        var grid = dijit.byId("getKCWarningIndexGrid");
        var items = grid.selection.getSelected().length;
        // 沒有選擇學生為全部寄送
        if (items == 0) {
            var from = "letter";
            kcjs.checkDataExist(from);
        }
        // 選擇單名學生
        if (items == 1) {
            dijit.byId("sentMailBtnOne").set("disabled", false);
            var items = grid.selection.getSelected();
            if (items == "") {
                kcjs._showMessage("請選擇一筆資料", "檢視輔導明細");
                return false;
            }
            // 導師帳號
            var kcstudentUserId = grid.store.getValue(items[0], 'kcemployee_user_id');
            // 該筆預警資料編號
            var kcwarning_id = grid.store.getValue(items[0], 'kcwarning_id');
            // 導師姓名
            var kcstudentName = grid.store.getValue(items[0], 'kcteacher_name');
            if (kcstudentName == '-') {
                dojo.byId("warningStudentName").innerHTML = "無";
                dijit.byId("sentMailBtnOne").set("disabled", true);
            } else {
                dojo.byId("warningStudentName").innerHTML = kcstudentName;
            }
            dojo.byId("warningStudentNameHidden").value = kcstudentName;
            dojo.byId("warningStudentId").value = kcstudentUserId;
            dojo.byId("warningId").value = kcwarning_id;
            dojo.xhrGet({
                url: dojo.byId("kcWarningHostUrl").value,
                load: function(url) {
                    dijit.byId("uploadedfileformOne").reset();
                    // 產生主旨預設格式
                    kcjs.defaultSubject("letterSubjectOne", thisYear, searchByTerm);
                    // 產生內容預設格式
                    kcjs.defaultContent("letterMessageOne", url);
                    // 產生簽名檔格式
                    kcjs.defaultSignName("letterSignNameOne");
                    dijit.byId("dialogKCLetterOne").show();
                },
                error: function() {
                    console.log("error");
                }
            });

        }
        // 選擇多名學生
        if (items > 1) {
            dijit.byId("sentMailBtnMulti").set("disabled", false);
            dojo.empty('warningStudentNames');
            dojo.byId("cancelIds").value = "";
            var kcstudentUserId = "";
            var kcwarning_id = "";
            var kcstudentName = "";
            var items = grid.selection.getSelected();
            var sum = 0;
            var noName = 0;
            var length = grid.selection.getSelected().length;
            dojo.forEach(items, function(node) {
                sum++;
                // 導師帳號
                var userId = grid.store.getValue(node, 'kcemployee_user_id');
                // 該筆預警資料的編號
                var warningId = grid.store.getValue(node, 'kcwarning_id');
                // 導師姓名
                var studentName = grid.store.getValue(node, 'kcteacher_name');
                if (studentName != '-') {
                    // 導師帳號串成 string
                    kcstudentUserId = kcstudentUserId + "," + userId;
                    // 預警資料編號串成 string
                    kcwarning_id = kcwarning_id + "," + warningId;
                    if (kcstudentName.indexOf(studentName) == -1) {
                        // 導師姓名串成 string
                        kcstudentName = kcstudentName + "," + studentName;
                        kcjs.createAddresseeDiv(userId, studentName, length, sum);
                    }
                } else {
                    sum--;
                    noName++;
                }
            });
            if (length == noName) {
                dojo.byId('warningStudentNames').innerHTML = "無";
                dijit.byId("sentMailBtnMulti").set("disabled", true);
            }
            var strLength = kcstudentName.length;
            kcstudentName = kcstudentName.substr(1, strLength - 1);
            dojo.byId("warningStudentNameHiddens").value = kcstudentName;
            dojo.byId("warningStudentIds").value = kcstudentUserId;
            dojo.byId("warningIds").value = kcwarning_id;
            dojo.xhrGet({
                url: dojo.byId("kcWarningHostUrl").value,
                load: function(url) {
                    dijit.byId("uploadedfileformMulti").reset();
                    // 產生主旨預設格式
                    kcjs.defaultSubject("letterSubjectMulti", thisYear, searchByTerm);
                    // 產生內容預設格式
                    kcjs.defaultContent("letterMessageMulti", url);
                    // 產生簽名檔格式
                    kcjs.defaultSignName("letterSignNameMulti");
                    dijit.byId("dialogKCLetterMulti").show();
                },
                error: function() {
                    console.log("error");
                }
            });


        }
    }

    /**
     * 產生收件人 div 
     */
    this.createAddresseeDiv = function(id, name, length, sum) {
        var content = dojo.byId('warningStudentNames');
        if (sum == 1) {
            var a = dojo.create("a", {
                id: id,
                innerHTML: name,
                value: id
            }, content);
        } else {
            var a = dojo.create("a", {
                id: id,
                innerHTML: ' , ' + name,
                value: id
            }, content);
        }
        a.setAttribute("class", "assignUserGroupDiv");
        dojo.connect(a, "onclick", function() {
            kcjs.removeSelected(id);
        });
    }

    /**
     * 移除收件人 div
     */
    this.removeSelected = function(id) {
        var cancelId = dojo.byId("cancelIds").value;
        dojo.destroy(dojo.byId(id));
        cancelId = cancelId + "," + id;
        dojo.byId("cancelIds").value = cancelId;
        var warningId = dojo.byId('warningStudentIds').value;

        dojo.xhrPost({
            url: dojo.byId("KCWarningCheckAddresseeAction").value,
            content: {
                cancelId: cancelId,
                warningId: warningId
            },
            load: function(result) {
                if (result == false) {
                    dijit.byId("sentMailBtnMulti").set("disabled", true);
                    dojo.byId('warningStudentNames').innerHTML = "無";
                    return false;
                }
            },
            error: function() {
                console.log("error");
            }
        });

    }

    /**
     * 執行下載或寄送通知信前檢查資料是否存在
     * @param {string} from 判斷是由下載或寄送通知信觸發
     */
    this.checkDataExist = function(from) {
        var privilege = dojo.byId("privilege").value;
        // 學期
        var searchByTerm = dijit.byId("searchByTerm").getValue();
        // 姓名
        var searchByName = dijit.byId("searchByName").getValue();
        // 種類
        var searchByType = "";
        if (dijit.byId("isWarningBox").attr("checked")) {
            searchByType = "1";
        }
        if (dijit.byId("isPoorBox").attr("checked")) {
            searchByType = "2";
        }
        if (dijit.byId("allTypeBox").attr("checked")) {
            searchByType = "0";
        }

        var departmentAdminUnitId = "";
        var searchByUnit = "";
        var userId = "";
        // 學年度
        var thisYear = dijit.byId("semYear").getValue();
        // 處室管理者才能選擇系不同系所
        if (privilege == 'OfficeAdmin') {
            searchByUnit = dijit.byId("searchByUnit").getValue();
        }
        // 為系所管理者則設定只能查該系的資料
        if (privilege == 'DepartmentAdmin') {
            departmentAdminUnitId = dojo.byId("departmentAdminUnitId").value;
        }
        // 為班導則設定只能查該名班導的資料
        if (privilege == 'Tutor') {
            userId = dojo.byId("userId").value;
        }
        var isGuidanceBox = dijit.byId("isGuidanceBox").attr("checked");
        var isntGuidanceBox = dijit.byId("isntGuidanceBox").attr("checked");
        var isNoticeBox = dijit.byId("isNoticeBox").attr("checked");
        var isntNoticeBox = dijit.byId("isntNoticeBox").attr("checked");

        var allGuidanceBox = dijit.byId("allGuidanceBox").attr("checked");
        var allNoticeBox = dijit.byId("allNoticeBox").attr("checked");

        var isGuidance = "";
        var isntGuidance = "";
        var isNotice = "";
        var isntNotice = "";

        // 判斷勾選全部，則相關設定為代表勾選
        if (allGuidanceBox) {
            isGuidance = "yes";
            isntGuidance = "yes";
        }
        if (allNoticeBox) {
            isNotice = "yes";
            isntNotice = "yes";
        }
        // 判斷是否勾選已輔導
        if (isGuidanceBox == true) {
            isGuidance = "yes";
        }
        // 判斷是否勾選未輔導
        if (isntGuidanceBox == true) {
            isntGuidance = "yes";
        }
        // 判斷是否勾選已通知
        if (isNoticeBox == true) {
            isNotice = "yes";
        }
        // 判斷是否勾選未通知
        if (isntNoticeBox == true) {
            isntNotice = "yes";
        }
        dojo.xhrGet({
            url: dojo.byId("kcWarningDoCheckDataExistActionUrl").value,
            handleAs: "json",
            content: {
                thisYear: thisYear,
                searchByName: searchByName,
                searchByTerm: searchByTerm,
                searchByUnit: searchByUnit,
                searchByType: searchByType,
                departmentAdminUnitId: departmentAdminUnitId,
                userId: userId,
                isGuidance: isGuidance,
                isntGuidance: isntGuidance,
                isNotice: isNotice,
                isntNotice: isntNotice,
                from: from
            },
            load: function(check) {
                if (from == "letter") {
                    if (check.response == false) {
                        kcjs._showMessage("預警清單無資料,無法使用通知功能", "提示");
                    } else {
                        kcjs.formatMail(thisYear, searchByTerm);
                    }
                }
                if (from == "download") {
                    if (check.response == false) {
                        kcjs._showMessage("預警清單無資料,無法使用下載功能", "提示");
                    } else {
                        kcjs.downloadWarningData();
                    }
                }
                if (from == 'export') {
                    if (check.response == false || check.isGuidanceNum == 0) {
                        kcjs._showMessage("目前尚無學生有輔導記錄,無法使用輔導記錄表輸出功能", "提示");
                        warningDataStandby.hide();
                        return false;
                    } else {
                        dojo.byId("isGuidanceStudentId").value = check.studentId;
                        dojo.byId("isGuidanceNum").value = check.isGuidanceNum;
                        dojo.byId("exportContent").innerHTML = "共 " + check.isGuidanceNum + " 名學生之輔導記錄表，將會統一合併成 PDF 檔後輸出，是否確定輸出 ?";
                        dijit.byId("exportDialog").show();
                        warningDataStandby.hide();
                        return true;
                    }
                }
            },
            error: function() {
                console.log("error");
            }
        });
    }

    /**
     * 產生寄送所有人通知信預設介面
     */
    this.formatMail = function(thisYear, searchByTerm) {
        dojo.xhrGet({
            url: dojo.byId("kcWarningHostUrl").value,
            load: function(url) {
                dijit.byId("uploadedfileform").reset();
                // 產生主旨預設格式
                kcjs.defaultSubject("letterSubject", thisYear, searchByTerm);
                // 產生內容預設格式
                kcjs.defaultContent("letterMessage", url);
                // 產生簽名檔格式
                kcjs.defaultSignName("letterSignName");
                dijit.byId("dialogKCLetter").show();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    /**
     * 查詢不同條件下的結果
     */
    this.conditionSelect = function() {
        if (!dijit.byId("semYear").validate())
        {
            dijit.byId("semYear").focus();
            return false;
        }
        if (!dijit.byId("searchByTerm").validate())
        {
            dijit.byId("searchByTerm").focus();
            return false;
        }

        var isWarning = "none";
        var isPoor = "none";
        var isGuidance = "none";
        var isntGuidance = "none";
        var isNotice = "none";
        var isntNotice = "none";

        var isWarningBox = dijit.byId("isWarningBox").attr("checked");
        var isPoorBox = dijit.byId("isPoorBox").attr("checked");
        var isGuidanceBox = dijit.byId("isGuidanceBox").attr("checked");

        var isntGuidanceBox = dijit.byId("isntGuidanceBox").attr("checked");
        var isNoticeBox = dijit.byId("isNoticeBox").attr("checked");
        var isntNoticeBox = dijit.byId("isntNoticeBox").attr("checked");

        var allTypeBox = dijit.byId("allTypeBox").attr("checked");
        var allGuidanceBox = dijit.byId("allGuidanceBox").attr("checked");
        var allNoticeBox = dijit.byId("allNoticeBox").attr("checked");

        // 判斷勾選全部，則相關設定為代表勾選
        if (allTypeBox) {
            isWarning = "yes";
            isPoor = "yes";
        }
        if (allGuidanceBox) {
            isGuidance = "yes";
            isntGuidance = "yes";
        }
        if (allNoticeBox) {
            isNotice = "yes";
            isntNotice = "yes";
        }

        // 期中預警設為 yes 代表勾選
        if (isWarningBox) {
            isWarning = "yes";
        }
        // 課業不佳設為 yes 代表勾選
        if (isPoorBox) {
            isPoor = "yes";
        }
        // 已輔導設為 yes 代表勾選
        if (isGuidanceBox) {
            isGuidance = "yes";
        }
        // 未輔導設為 yes 代表勾選
        if (isntGuidanceBox) {
            isntGuidance = "yes";
        }
        // 已通知設為 yes 代表勾選
        if (isNoticeBox) {
            isNotice = "yes";
        }
        // 未通知設為 yes 代表勾選
        if (isntNoticeBox) {
            isntNotice = "yes";
        }

        var privilege = dojo.byId("privilege").value;
        // 學期
        var searchByTerm = dijit.byId("searchByTerm").getValue();
        // 姓名
        var searchByName = dijit.byId("searchByName").getValue();
        var departmentAdminUnitId = "";
        var searchByUnit = "";
        var userId = "";
        var thisYear = dijit.byId("semYear").getValue();
        // 是否為處室管理者
        if (privilege == 'OfficeAdmin') {
            if (!dijit.byId("searchByUnit").validate())
            {
                dijit.byId("searchByUnit").focus();
                return false;
            }
            // 為處室管理者才可以選擇不同系所
            searchByUnit = dijit.byId("searchByUnit").getValue();
        }
        if (privilege == 'DepartmentAdmin') {
            // 為系所管理者則設定只可以查詢該系
            departmentAdminUnitId = dojo.byId("departmentAdminUnitId").value;
        }
        if (privilege == 'Tutor') {
            // 為班導則設定只能查詢該名導師
            userId = dojo.byId("userId").value;
        }
        dijit.byId("getKCWarningIndexGrid").setQuery({
            thisYear: thisYear,
            searchByTerm: searchByTerm,
            searchByName: searchByName,
            searchByUnit: searchByUnit,
            userId: userId,
            departmentAdminUnitId: departmentAdminUnitId,
            isWarning: isWarning,
            isPoor: isPoor,
            isGuidance: isGuidance,
            isntGuidance: isntGuidance,
            isNotice: isNotice,
            isntNotice: isntNotice,
            start: 0,
            count: 15
        });
        dijit.byId('getKCWarningIndexGrid').selection.clear();
        warningDataStandby.show();
        dojo.xhrPost({
            url: dojo.byId("kcWarningDataGridUrl").value + "&thisYear=" + thisYear + "&searchByTerm=" + searchByTerm + "&&searchByUnit=" + searchByUnit + "&userId=" + userId + "&departmentAdminUnitId=" + departmentAdminUnitId + "&isWarning=" + isWarning + "&isPoor=" + isPoor + "&isGuidance=" + isGuidance + "&isntGuidance=" + isntGuidance + "&isNotice=" + isNotice + "&isntNotice=" + isntNotice,
            handleAs: "json",
            content: {
                searchByNamePost: searchByName,
                searchByTerm: searchByTerm
            },
            load: function(data) {
                var searchByTermMsg = "";
                var typeMsg = "";
                var guidanceMsg = "";
                var noticeMsg = "";
                if (searchByTerm == 1) {
                    searchByTermMsg = "上學期";
                } else if (searchByTerm == 2) {
                    searchByTermMsg = "下學期";
                } else if (searchByTerm == 0) {
                    searchByTermMsg = "上、下學期";
                }
                if (isGuidanceBox) {
                    guidanceMsg = "，經過輔導";
                }
                if (isntGuidanceBox) {
                    guidanceMsg = "，尚未經過輔導";
                }
                if (isNoticeBox) {
                    noticeMsg = "，已通知學生";
                }
                if (isntNoticeBox) {
                    noticeMsg = "，未通知學生";
                }
                if (isWarningBox) {
                    typeMsg = "，期中預警類型";
                }
                if (isPoorBox) {
                    typeMsg = "，課業不佳類型";
                }
                if (isGuidanceBox && isNoticeBox) {
                    guidanceMsg = "，經過輔導";
                    noticeMsg = "並且通知學生"
                }
                if (isGuidanceBox && isntNoticeBox) {
                    guidanceMsg = "，經過輔導";
                    noticeMsg = "但是尚未通知學生"
                }
                if (isntGuidanceBox && isntNoticeBox) {
                    guidanceMsg = "，尚未經過輔導";
                    noticeMsg = "和通知學生"
                }
                if (isntGuidanceBox && isNoticeBox) {
                    guidanceMsg = "，經過輔導";
                    noticeMsg = "但是尚未通知學生"
                }
                var unitMsg = ""
                if (privilege == 'OfficeAdmin') {
                    if (dijit.byId("searchByUnit").getValue() == "0") {
                        unitMsg = "，全部科系"
                    } else {
                        unitMsg = "，" + dijit.byId("searchByUnit").getValue();
                    }
                }
                var msg = thisYear + " 學年度" + searchByTermMsg + unitMsg + typeMsg + guidanceMsg + noticeMsg + "，共 " + data.numRows + " 筆資料";

                dojo.byId("warningMsg").innerHTML = msg;
                warningDataStandby.hide();
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    /**
     * 寄送通知信
     */
    this.sentLetter = function() {
        var letterMessage = dijit.byId("letterMessage").getValue();
        if (letterMessage == "") {
            kcjs._showMessage("請輸入預警通知信內容", "提示");
        } else {
            var isGuidance = "";
            var isntGuidance = "";
            var isNotice = "";
            var isntNotice = "";
            var isGuidanceBox = dijit.byId("isGuidanceBox").attr("checked");
            var isntGuidanceBox = dijit.byId("isntGuidanceBox").attr("checked");
            var isNoticeBox = dijit.byId("isNoticeBox").attr("checked");
            var isntNoticeBox = dijit.byId("isntNoticeBox").attr("checked");

            var allGuidanceBox = dijit.byId("allGuidanceBox").attr("checked");
            var allNoticeBox = dijit.byId("allNoticeBox").attr("checked");

            // 判斷勾選全部，則相關設定為代表勾選
            if (allGuidanceBox) {
                isGuidance = "yes";
                isntGuidance = "yes";
            }
            if (allNoticeBox) {
                isNotice = "yes";
                isntNotice = "yes";
            }
            if (isGuidanceBox == true) {
                isGuidance = "yes";
            }
            if (isntGuidanceBox == true) {
                isntGuidance = "yes";
            }

            if (isNoticeBox == true) {
                isNotice = "yes";
            }
            if (isntNoticeBox == true) {
                isntNotice = "yes";
            }
            // 種類
            var searchByType = "";
            if (dijit.byId("isWarningBox").attr("checked")) {
                searchByType = "1";
            }
            if (dijit.byId("isPoorBox").attr("checked")) {
                searchByType = "2";
            }
            if (dijit.byId("allTypeBox").attr("checked")) {
                searchByType = "0";
            }

            // 權限
            var privilege = dojo.byId("privilege").value;
            // 學期
            var searchByTerm = dijit.byId("searchByTerm").getValue();
            // 姓名
            var searchByName = dijit.byId("searchByName").getValue();
            var departmentAdminUnitId = "";
            var searchByUnit = "";
            var userId = "";
            var thisYear = dijit.byId("semYear").getValue();
            if (privilege == 'OfficeAdmin') {
                // 為處室管理者才可以選擇不同系所
                searchByUnit = dijit.byId("searchByUnit").getValue();
            }
            if (privilege == 'DepartmentAdmin') {
                // 為系所管理者則設定只可以查詢該系
                departmentAdminUnitId = dojo.byId("departmentAdminUnitId").value;
            }

            if (privilege == 'Tutor') {
                // 為班導則設定只能查詢該名導師
                userId = dojo.byId("userId").value;
            }
            var letterSubject = dijit.byId("letterSubject").getValue();
            var letterSignName = dijit.byId("letterSignName").getValue();
            if (!dijit.byId("uploadedfileform").validate())
            {
                return false;
            }
            basicStandbyLetter.show();
            dojo.xhrGet({
                url: dojo.byId("kcWarningSentFullMailUrl").value,
                content: {
                    letterMessage: letterMessage,
                    letterSubject: letterSubject,
                    letterSignName: letterSignName,
                    thisYear: dijit.byId("semYear").getValue(),
                    searchByName: dijit.byId("searchByName").getValue(),
                    searchByTerm: dijit.byId("searchByTerm").getValue(),
                    searchByUnit: searchByUnit,
                    departmentAdminUnitId: departmentAdminUnitId,
                    userId: userId,
                    searchByType: searchByType,
                    isGuidance: isGuidance,
                    isntGuidance: isntGuidance,
                    isNotice: isNotice,
                    isntNotice: isntNotice,
                },
                load: function(newContent) {
                    if (newContent) {
                        dijit.byId("dialogKCLetter").hide();
                        dijit.byId("getKCWarningIndexGrid").setQuery({
                            thisYear: thisYear,
                            searchByTerm: searchByTerm,
                            searchByName: searchByName,
                            searchByUnit: searchByUnit,
                            userId: userId,
                            searchByType: searchByType,
                            isGuidance: isGuidance,
                            isntGuidance: isntGuidance,
                            isNotice: isNotice,
                            isntNotice: isntNotice,
                            departmentAdminUnitId: departmentAdminUnitId
                        });
                        basicStandbyLetter.hide();
                    }
                },
                error: function() {
                }
            });
        }
    }

    /**
     * 寄送單人通知信
     */
    this.sentLetterOne = function() {
        // 通知信內容
        var letterMessage = dijit.byId("letterMessageOne").getValue();
        if (letterMessage == "") {
            kcjs._showMessage("請輸入預警通知信內容", "提示");
        } else {
            // 主旨
            var letterSubject = dijit.byId("letterSubjectOne").getValue();
            // 收件人
            var letterSignName = dijit.byId("letterSignNameOne").getValue();
            var warningStudentName = dojo.byId("warningStudentNameHidden").value;
            // 該筆預警資料編號
            var warningId = dojo.byId("warningId").value;
            // 收件人 id
            var warningStudentId = dojo.byId("warningStudentId").value;
            if (!dijit.byId("uploadedfileformOne").validate())
            {
                return false;
            }
            basicStandbyOneLetter.show();
            dojo.xhrGet({
                url: dojo.byId("kcWarningSentSingleMailUrl").value,
                content: {
                    letterMessage: letterMessage,
                    letterSubject: letterSubject,
                    letterSignName: letterSignName,
                    warningStudentName: warningStudentName,
                    warningId: warningId,
                    warningStudentId: warningStudentId,
                },
                load: function(newContent) {
                    if (newContent) {
                        dijit.byId("dialogKCLetterOne").hide();
                        var isGuidance = "";
                        var isntGuidance = "";
                        var isNotice = "";
                        var isntNotice = "";
                        var isGuidanceBox = dijit.byId("isGuidanceBox").attr("checked");
                        var isntGuidanceBox = dijit.byId("isntGuidanceBox").attr("checked");
                        var isNoticeBox = dijit.byId("isNoticeBox").attr("checked");
                        var isntNoticeBox = dijit.byId("isntNoticeBox").attr("checked");

                        var allGuidanceBox = dijit.byId("allGuidanceBox").attr("checked");
                        var allNoticeBox = dijit.byId("allNoticeBox").attr("checked");

                        // 判斷勾選全部，則相關設定為代表勾選
                        if (allGuidanceBox) {
                            isGuidance = "yes";
                            isntGuidance = "yes";
                        }
                        if (allNoticeBox) {
                            isNotice = "yes";
                            isntNotice = "yes";
                        }

                        // 是否勾選已輔導
                        if (isGuidanceBox == true) {
                            isGuidance = "yes";
                        }
                        // 是否勾選未輔導
                        if (isntGuidanceBox == true) {
                            isntGuidance = "yes";
                        }
                        // 是否勾選已通知
                        if (isNoticeBox == true) {
                            isNotice = "yes";
                        }
                        // 是否勾選未通知
                        if (isntNoticeBox == true) {
                            isntNotice = "yes";
                        }
                        // 種類
                        var searchByType = "";
                        if (dijit.byId("isWarningBox").attr("checked")) {
                            searchByType = "1";
                        }
                        if (dijit.byId("isPoorBox").attr("checked")) {
                            searchByType = "2";
                        }
                        if (dijit.byId("allTypeBox").attr("checked")) {
                            searchByType = "0";
                        }
                        // 權限
                        var privilege = dojo.byId("privilege").value;
                        // 學期
                        var searchByTerm = dijit.byId("searchByTerm").getValue();
                        // 姓名
                        var searchByName = dijit.byId("searchByName").getValue();
                        var departmentAdminUnitId = "";
                        var searchByUnit = "";
                        var userId = "";
                        var thisYear = dijit.byId("semYear").getValue();
                        if (privilege == 'OfficeAdmin') {
                            // 為處室管理者才可以選擇不同系所
                            searchByUnit = dijit.byId("searchByUnit").getValue();
                        }
                        if (privilege == 'DepartmentAdmin') {
                            // 為系所管理者則設定只可以查詢該系
                            departmentAdminUnitId = dojo.byId("departmentAdminUnitId").value;
                        }

                        if (privilege == 'Tutor') {
                            // 為班導則設定只能查詢該名導師
                            userId = dojo.byId("userId").value;
                        }

                        dijit.byId("getKCWarningIndexGrid").setQuery({
                            thisYear: thisYear,
                            searchByTerm: searchByTerm,
                            searchByName: searchByName,
                            searchByUnit: searchByUnit,
                            userId: userId,
                            searchByType: searchByType,
                            isGuidance: isGuidance,
                            isntGuidance: isntGuidance,
                            isNotice: isNotice,
                            isntNotice: isntNotice,
                            departmentAdminUnitId: departmentAdminUnitId
                        });
                        basicStandbyOneLetter.hide();
                    }
                },
                error: function() {
                }
            });
        }
    }

    /**
     * 寄送多人通知信
     */
    this.sentLetterMulti = function() {
        // 通知信內容
        var letterMessage = dijit.byId("letterMessageMulti").getValue();
        if (letterMessage == "") {
            kcjs._showMessage("請輸入預警通知信內容", "提示");
        } else {
            // 主旨
            var letterSubject = dijit.byId("letterSubjectMulti").getValue();
            // 收件人
            var letterSignName = dijit.byId("letterSignNameMulti").getValue();
            var warningStudentName = dojo.byId("warningStudentNameHiddens").value;
            // 該筆預警資料編號
            var warningId = dojo.byId("warningIds").value;
            // 收件人 id
            var warningStudentId = dojo.byId("warningStudentIds").value;
            // 取消 id
            var cancelIds = dojo.byId("cancelIds").value;
            if (!dijit.byId("uploadedfileformMulti").validate())
            {
                return false;
            }
            dojo.byId("cancelIds").value = "";
            basicStandbyMultiLetter.show();
            dojo.xhrGet({
                url: dojo.byId("kcWarningSentMultiMailUrl").value,
                content: {
                    letterMessage: letterMessage,
                    letterSubject: letterSubject,
                    letterSignName: letterSignName,
                    warningStudentName: warningStudentName,
                    warningId: warningId,
                    warningStudentId: warningStudentId,
                    cancelIds: cancelIds,
                },
                load: function(newContent) {
                    if (newContent) {
                        dijit.byId("dialogKCLetterMulti").hide();
                        var isGuidance = "";
                        var isntGuidance = "";
                        var isNotice = "";
                        var isntNotice = "";
                        var isGuidanceBox = dijit.byId("isGuidanceBox").attr("checked");
                        var isntGuidanceBox = dijit.byId("isntGuidanceBox").attr("checked");
                        var isNoticeBox = dijit.byId("isNoticeBox").attr("checked");
                        var isntNoticeBox = dijit.byId("isntNoticeBox").attr("checked");

                        var allGuidanceBox = dijit.byId("allGuidanceBox").attr("checked");
                        var allNoticeBox = dijit.byId("allNoticeBox").attr("checked");

                        // 判斷勾選全部，則相關設定為代表勾選
                        if (allGuidanceBox) {
                            isGuidance = "yes";
                            isntGuidance = "yes";
                        }
                        if (allNoticeBox) {
                            isNotice = "yes";
                            isntNotice = "yes";
                        }

                        // 是否勾選已輔導
                        if (isGuidanceBox == true) {
                            isGuidance = "yes";
                        }
                        // 是否勾選未輔導
                        if (isntGuidanceBox == true) {
                            isntGuidance = "yes";
                        }
                        // 是否勾選已通知
                        if (isNoticeBox == true) {
                            isNotice = "yes";
                        }
                        // 是否勾選未通知
                        if (isntNoticeBox == true) {
                            isntNotice = "yes";
                        }
                        // 種類
                        // 種類
                        var searchByType = "";
                        if (dijit.byId("isWarningBox").attr("checked")) {
                            searchByType = "1";
                        }
                        if (dijit.byId("isPoorBox").attr("checked")) {
                            searchByType = "2";
                        }
                        if (dijit.byId("allTypeBox").attr("checked")) {
                            searchByType = "0";
                        }
                        // 權限
                        var privilege = dojo.byId("privilege").value;
                        // 學期
                        var searchByTerm = dijit.byId("searchByTerm").getValue();
                        // 姓名
                        var searchByName = dijit.byId("searchByName").getValue();
                        var departmentAdminUnitId = "";
                        var searchByUnit = "";
                        var userId = "";
                        var thisYear = dijit.byId("semYear").getValue();
                        if (privilege == 'OfficeAdmin') {
                            // 為處室管理者才可以選擇不同系所
                            searchByUnit = dijit.byId("searchByUnit").getValue();
                        }
                        if (privilege == 'DepartmentAdmin') {
                            // 為系所管理者則設定只可以查詢該系
                            departmentAdminUnitId = dojo.byId("departmentAdminUnitId").value;
                        }
                        if (privilege == 'Tutor') {
                            // 為班導則設定只能查詢該名導師
                            userId = dojo.byId("userId").value;
                        }
                        dijit.byId("getKCWarningIndexGrid").setQuery({
                            thisYear: thisYear,
                            searchByTerm: searchByTerm,
                            searchByName: searchByName,
                            searchByUnit: searchByUnit,
                            userId: userId,
                            searchByType: searchByType,
                            isGuidance: isGuidance,
                            isntGuidance: isntGuidance,
                            isNotice: isNotice,
                            isntNotice: isntNotice,
                            departmentAdminUnitId: departmentAdminUnitId
                        });
                        basicStandbyMultiLetter.hide();
                        dijit.byId('getKCWarningIndexGrid').selection.clear();
                    }
                },
                error: function() {
                }
            });
        }
    }

    /**
     * 點選下載 button 檢查資料存在是否
     */
    this.clickWarningData = function() {
        var from = "download";
        kcjs.checkDataExist(from);
    }

    /**
     * 下載動作，在檢查資料是否存在後
     */
    this.downloadWarningData = function() {
        warningDataStandby.show();
        // 權限
        var privilege = dojo.byId("privilege").value;
        // 學期
        var searchByTerm = dijit.byId("searchByTerm").getValue();
        // 姓名
        var searchByName = dijit.byId("searchByName").getValue();
        var departmentAdminUnitId = "";
        var searchByUnit = "";
        var userId = "";
        var isGuidance = "";
        var isntGuidance = "";
        var isNotice = "";
        var isntNotice = "";

        // 已輔導未輔導已通知未通知的勾選情況
        var isGuidanceBox = dijit.byId("isGuidanceBox").attr("checked");
        var isntGuidanceBox = dijit.byId("isntGuidanceBox").attr("checked");
        var isNoticeBox = dijit.byId("isNoticeBox").attr("checked");
        var isntNoticeBox = dijit.byId("isntNoticeBox").attr("checked");

        var allGuidanceBox = dijit.byId("allGuidanceBox").attr("checked");
        var allNoticeBox = dijit.byId("allNoticeBox").attr("checked");

        // 判斷勾選全部，則相關設定為代表勾選
        if (allGuidanceBox) {
            isGuidance = "yes";
            isntGuidance = "yes";
        }
        if (allNoticeBox) {
            isNotice = "yes";
            isntNotice = "yes";
        }
        // 是否已勾選已輔導
        if (isGuidanceBox == true) {
            isGuidance = "yes";
        }
        // 是否已勾選未輔導
        if (isntGuidanceBox == true) {
            isntGuidance = "yes";
        }
        // 是否已勾選已通知
        if (isNoticeBox == true) {
            isNotice = "yes";
        }
        // 是否已勾選未通知
        if (isntNoticeBox == true) {
            isntNotice = "yes";
        }
        // 種類
        var searchByType = "";
        if (dijit.byId("isWarningBox").attr("checked")) {
            searchByType = "1";
        }
        if (dijit.byId("isPoorBox").attr("checked")) {
            searchByType = "2";
        }
        if (dijit.byId("allTypeBox").attr("checked")) {
            searchByType = "0";
        }
        // 學年
        var thisYear = dijit.byId("semYear").getValue();
        if (privilege == 'OfficeAdmin') {
            // 為處室管理者才可以選擇不同系所
            searchByUnit = dijit.byId("searchByUnit").getValue();
        }
        if (privilege == 'DepartmentAdmin') {
            // 為系所管理者只能選擇該系
            departmentAdminUnitId = dojo.byId("departmentAdminUnitId").value;
        }
        if (privilege == 'Tutor') {
            // 為班導只能選擇該名導師
            userId = dojo.byId("userId").value;
        }
        dojo.xhrGet({
            url: dojo.byId("kcWarningDoDownloadActionUrl").value,
            content: {
                thisYear: dijit.byId("semYear").getValue(),
                searchByName: dijit.byId("searchByName").getValue(),
                searchByTerm: dijit.byId("searchByTerm").getValue(),
                searchByUnit: searchByUnit,
                departmentAdminUnitId: departmentAdminUnitId,
                userId: userId,
                searchByType: searchByType,
                isGuidance: isGuidance,
                isntGuidance: isntGuidance,
                isNotice: isNotice,
                isntNotice: isntNotice
            },
            load: function(newContent) {
                warningDataStandby.hide();
                location.href = "./admin.php?site_id=0&mod=kuas_consultation_warning&func=KCWarningDoDownloadAction&searchByUnit=" + searchByUnit + "&searchByName=" + searchByName + "&thisYear=" + thisYear + "&searchByTerm=" + searchByTerm + "&departmentAdminUnitId=" + departmentAdminUnitId + "&userId=" + userId + "&searchByType=" + searchByType + "&isGuidance=" + isGuidance + "&isntGuidance=" + isntGuidance + "&isNotice=" + isNotice + "&isntNotice=" + isntNotice
            },
            error: function() {
                alert("error");
            }
        });
    }

    /**
     * 重新整理
     */
    this.kcwReset = function() {
        var privilege = dojo.byId("privilege").value;
        if (privilege == 'OfficeAdmin') {
            dijit.byId("searchByUnit").reset();
            var searchByUnit = dijit.byId("searchByUnit").getValue();
            dijit.byId('getKCWarningIndexGrid').selection.clear();
        } else {
            searchByUnit = "";
        }
        var searchByTerm =dojo.byId("term").value;
        if (searchByTerm == "1") {
             dijit.byId("searchByTerm").attr('value', "1");
        }
        if (searchByTerm == "2") {
              dijit.byId("searchByTerm").attr('value', "2");
        }
        dijit.byId('semYear').reset();
        dijit.byId('searchByName').reset();
      
        dijit.byId("allTypeBox").set("checked", true);
        dijit.byId("allGuidanceBox").set("checked", true);
        dijit.byId("allNoticeBox").set("checked", true);
        dijit.byId('getKCWarningIndexGrid').selection.clear();
        this.conditionSelect();
    };

    /**
     * 同步預警資料
     */
    this.kcwSync = function() {
        warningDataStandby.show();
        dojo.byId("loadingContent").innerHTML = "預警資料同步中，請稍待片刻...";
        dijit.byId("loadingDialog").show();
        dojo.xhrGet({
            url: dojo.byId("kcIndexSync").value,
            content: {
            },
            load: function(response) {
                if (response == "0") {
                    warningDataStandby.hide();
                    dijit.byId("loadingDialog").hide();
                    kcjs.kcwReset();
                } else {
                    warningDataStandby.hide();
                    dijit.byId("loadingDialog").hide();
                    kcjs._showMessage("同步預警資料時發生不可預期之錯誤，請聯絡系統管理者協助解決此問題。", "同步預警錯誤");
                }
            },
            error: function() {
                console.log("error");
            }
        });
    }

    /**
     * 預警通知信主旨預設格式
     * @param {string} id 標籤 id
     * @param {string} thisYear 學年度
     * @param {string} searchByTerm 學期
     */
    this.defaultSubject = function(id, year, term) {
        // 判斷學期中文
        var chineseTerm = "";
        switch (term) {
            case '0':
                chineseTerm = "全";
                break;
            case'1':
                chineseTerm = "上";
                break;
            case'2':
                chineseTerm = "下";
                break;
        }
        dojo.byId(id).value = "學習預警輔導提醒通知";
    }

    /**
     * 預警通知信內容通知格式
     * @param {string} id 標籤 id
     * @param {string} url 預約網址
     */
    this.defaultContent = function(id, url) {
        dojo.byId(id).value = "親愛的導師：\n您好! 請至學習門診平台針對班上被預警的學生進行輔導, \n適當地與學生溝通並進行輔導，能有效增進學生們學習的成效!\n\n請至以下網址登入「學習門診中心平台」：" + " http://hospital.kuas.edu.tw" + "\n於登入後點選「學習預警填報」功能按鈕進行填寫輔導記錄!\n感謝老師的配合! 謝謝!!";
    }

    /*
     * 預警通知信簽名檔格式
     * @param {string} id 標籤 id
     */
    this.defaultSignName = function(id) {
        dojo.byId(id).value = "國立高雄應用科技大學\n教務處綜合教務組\nE-mail: caoffice01@kuas.edu.tw\nTel: 07-3814526 ext.2326"
    }

    /**
     * 判斷輔導狀況
     * @param {string} value 是否已輔導
     */
    this.decideTutor = function(value) {
        if (value == 'yes') {
            return "<font color='green'>已輔導</font>";
        } else {
            return  "<font color='red'>未輔導</font>";
        }
    }

    /**
     * 顯示訊息 Dialog
     * @param {string} message 內容
     * @param {string} title 標題
     */
    this._showMessage = function(message, title) {
        dojo.byId("msgContent").innerHTML = message;
        dijit.byId("msgDialog").attr("title", title);
        dijit.byId("msgDialog").show();
    }

    /**
     * 預警輔導記錄填報 
     * @return {Boolean} 是否執行成功
     */
    this.reportWarningGuidance = function() {
        var term = dojo.byId("term").value;
        var year = dojo.byId("year").value;
        var grid = dijit.byId("getKCWarningIndexGrid");
        var items = grid.selection.getSelected();
        if (items.length != 1) {
            kcjs._showMessage("請選擇一筆資料", "提示");
            return false;
        }
        var teacherId = grid.store.getValue(items[0], 'kcemployee_user_id');
        var loginId = dojo.byId("loginId").value;
        var semTerm = grid.store.getValue(items[0], 'kcwarning_sem_term');
        var semYear = grid.store.getValue(items[0], 'kcwarning_sem_year');
        if (semTerm != term || semYear != year) {
            kcjs._showMessage("請針對本學期之預警學生進行填報", "提示");
            return false;
        }
        if (teacherId != loginId) {
            kcjs._showMessage("您非該班導師，無法使用預警輔導記錄填報功能", "提示");
            return false;
        }
        else {
            var studentId = grid.store.getValue(items[0], 'kcstudent_user_id');
            var studentClass = grid.store.getValue(items[0], 'kcclass_name');
            var teacherName = grid.store.getValue(items[0], 'kcteacher_name');
            var studentUnitId = grid.store.getValue(items[0], 'kcunit_id');
            var condition = grid.store.getValue(items[0], 'kcwarning_have_counseling');
            var year = grid.store.getValue(items[0], 'kcwarning_sem_year');
            var term = grid.store.getValue(items[0], 'kcwarning_sem_term');
            if (condition == 'no') {
                var type = 'insert';
            } else {
                var type = 'update';
            }
        }
        dojo.byId("reportStudentId").value = studentId;
        dojo.byId("reportStudentClass").value = studentClass;
        dojo.byId("reportTeacherId").value = teacherId;
        dojo.byId("reportStudentUnitId").value = studentUnitId;
        dojo.byId("reportTeacherName").value = teacherName;
        dojo.byId("reportType").value = type;
        dojo.byId("reportYear").value = year;
        dojo.byId("reportTerm").value = term;
        document.reportForm.submit();
        return true;
    }

    /**
     * 取得輔導填報初始畫面
     * @returns {Boolean} 是否執行完成
     */
    this.getOptionView = function() {
        var newQueId = dojo.byId("optionIdString").value;
        var guidance = dojo.byId("kcguidanceId").value;
        dojo.xhrPost({
            url: dojo.byId("KCWarningChangeOptionPage").value,
            handleAs: "json",
            content: {
                newQueId: newQueId,
                guidance: guidance
            },
            load: function(data) {
                var li = 0
                for (li = 0; li < data.optionView.length; li++) {
                    kcjs.newOptionLi(data.optionView[li]);
                }
                personalGuidanceStandby.hide();
            },
            error: function(error, ioArgs) {
                console.log(error);
            }
        });
        return true;
    }

    /**
     * 取得輔導填報修改畫面
     * @returns {Boolean} 是否執行完成
     */
    this.getOptionUpdateView = function() {
        var newQueId = dojo.byId("optionIdString").value;
        var guidance = dojo.byId("kcguidanceId").value;
        dojo.xhrPost({
            url: dojo.byId("KCWarningUpdateChangeOptionPage").value,
            handleAs: "json",
            content: {
                newQueId: newQueId,
                guidance: guidance
            },
            load: function(data) {
                var li = 0
                for (li = 0; li < data.optionView.length; li++) {
                    kcjs.newOptionLi(data.optionView[li]);
                }
                personalGuidanceStandby.hide();
            },
            error: function(error, ioArgs) {
                console.log(error);
            }
        });
        return true;
    }

    /**
     * 產生新節點
     * @param {array} data  項目明細資料
     * @returns {Boolean} 是否執行完成
     */
    this.newOptionLi = function(data) {
        var li = dojo.create("li", {id: "li" + data.id, style: "list-style-type: none;margin-top:10px;", value: data.id}, "ol" + data.queId);
        this.createOptionLabel(data, li);
        return true;
    }

    /**
     * 產生節點底下的內容
     * @param {array} data 項目明細資料
     * @param {String} li 節點
     * @returns {Boolean}   是否執行完成
     */
    this.createOptionLabel = function(data, li) {
        var node = dojo.create("div", {}, li);
        switch (data.type) {
            case 'dijit.form.RadioButton':
                new dijit.form.RadioButton({
                    id: "optionId" + data.id,
                    name: "optionName" + data.queId,
                    value: data.id,
                    checked: data.checked,
                    readOnly: data.readonly,
                    onChange: function() {
                        kcjs.changeOption(data.id);
                    }
                }, node);
                break;
            case 'dijit.form.CheckBox':
                new dijit.form.CheckBox({
                    id: "optionId" + data.id,
                    name: "optionName" + data.queId,
                    value: data.id,
                    checked: data.checked,
                    readOnly: data.readonly,
                    onChange: function() {
                        kcjs.changeOption(data.id);
                    }
                }, node);
                break;
            case 'dijit.form.SimpleTextarea':
                new dijit.form.SimpleTextarea({
                    id: "optionId" + data.id,
                    name: "optionName" + data.queId,
                    value: data.content,
                    style: "width:98%; resize: none;",
                    onChange: function() {
                        kcjs.changeOption(data.id);
                    }
                }, node);
                break;
        }
        if (data.type != 'dijit.form.SimpleTextarea') {
            dojo.create("label", {"innerHTML": data.name, "for": "optionId" + data.id}, li);
        }
        var spanDisplay = "display:none;";
        var olDisplay = "display:none;";
        if (data.checked) {
            spanDisplay = "";
            olDisplay = "";
        }
        var span = dojo.create("span", {id: "optionSpan" + data.id, style: spanDisplay}, li)
        var div = dojo.create("div", {}, span);
        if (data.tran == true) {
            if (data.desc == "") {
                var desc = "請輸入其他內容";
            } else {
                var desc = "請輸入" + data.desc;
            }
            new dijit.form.ValidationTextBox({
                type: "text",
                id: "optionNote" + data.id,
                placeHolder: desc,
                value: data.content,
                style: "margin-left:5px;width:100px "
            }, div);
        } else {
            new dijit.form.TextBox({
                id: "optionNote" + data.id,
                style: "margin-left:5px; ",
                type: "hidden",
            }, div);
        }
        var ol = dojo.create("ol", {id: "ol" + data.id, style: "list-style-type: none;" + olDisplay + "margin-buttom:5px;"}, li);
        dojo.create("input", {type: "hidden", id: "limit" + data.id, value: data.limit}, ol);
        dojo.create("input", {type: "hidden", id: "name" + data.id, value: data.name}, ol);
        dojo.create("input", {type: "hidden", name: "queId", id: "queId" + data.id, value: data.id}, ol);
        return true;
    }

    /**
     * 選取選進行判斷是否開啟 ol 子選項
     * @param {String} id ol 編號
     * @returns {Boolean} 是否執行完成
     */
    this.changeOption = function(id) {
        if (dijit.byId("optionId" + id).checked == true) {
            dojo.style("ol" + id, "display", "");
            dojo.byId("optionSpan" + id).style.display = "";
            dijit.byId("optionNote" + id).attr("required", true);
        } else {
            dojo.style("ol" + id, "display", "none");
            dijit.byId("optionNote" + id).setValue("");
            dojo.style("optionNote" + id, "required", "false")
            dojo.byId("optionSpan" + id).style.display = "none";
            dijit.byId("optionNote" + id).attr("required", false);
            this.closeli(id);
        }
        return true;
    }

    /**
     * 關閉 ol 以下節點
     * @param {String} id ol 編號
     * @returns {Boolean} 是否執行完成
     */

    this.closeli = function(id) {
        dojo.query('#ol' + id + '>' + 'li').forEach(function(node, index, arr) {
            dijit.byId("optionId" + node.value).set("checked", false);
        });
        return true;
    }

    /**
     * 修改總時間影響結束時間
     * @returns {Boolean} 是否執行完成
     */
    this.changeTotalTime = function() {
        var time = (dijit.byId("kcguidanceTotalHours").getValue()) * 60000;
        var startDate = dojo.byId("kcguidanceStartDate").value;
        var startTime = dijit.byId("kcguidanceStartTime").toString().replace(/T/, '');
        var start = startDate + " " + startTime;
        var date = new Date(start);
        var endTimeSec = new Date(date.getTime() + time);
        dijit.byId("kcguidanceEndTime").setValue(endTimeSec);
        dijit.byId("kcguidanceEndDate").setValue(endTimeSec);
        return true;
    }

    /**
     * 修改時間影響總時間
     * @returns {Boolean} 是否執行完成
     */
    this.changeTotalHours = function() {
        var startDate = dojo.byId("kcguidanceStartDate").value;
        var endDate = dojo.byId("kcguidanceEndDate").value;
        var startTime = dijit.byId("kcguidanceStartTime").toString().replace(/T/, '');
        var endTime = dijit.byId("kcguidanceEndTime").toString().replace(/T/, '');
        var start = new Date(startDate + " " + startTime);
        var end = new Date(endDate + " " + endTime);
        dijit.byId("kcguidanceTotalHours").setValue((end - start) / 60000);
        return true;
    }

    /**
     * 新增個人預警輔導紀錄 
     * @returns {Boolean} 是否執行完成
     */
    this.insertWarningGuidance = function() {
        var queId = document.getElementsByName("queId");
        // 判斷有選項未填則提示
        var len = queId.length;
        for (i = 0; i < len; i++)
        {
            if (dijit.byId("optionId" + queId[i].value).get("checked")) {
                var optionName = document.getElementsByName("optionName" + queId[i].value);
                var len2 = optionName.length;
                var checked = false;
                var check = 0;
                for (j = 0; j < len2; j++)
                {
                    if (optionName[j].checked == true) {
                        check = check + 1;
                    } else if (optionName[j].type == "textarea") {
                        if (optionName[j].value) {
                            check = check + 1;
                        }
                    }
                }
                if (check < dojo.byId("limit" + queId[i].value).value) {
                    kcjs._showMessage(dojo.byId("name" + queId[i].value).value + "選項未填", "資料遺漏");
                    return false;
                }
            }
        }
        // 輔導紀錄資料
        var kcunitId = dojo.byId("kcunitId").value;
        var kcstudentUseId = dojo.byId("kcstudentUseId").value;
        var kcteacherUserId = dojo.byId("kcteacherUserId").value;
        var kcguidanceTopic = "學習成績期中預警";
        var kcguidanceTotalHours = dijit.byId("kcguidanceTotalHours").getValue();
        var kcguidanceAddress = "教師研究室";
        var startTime = dijit.byId("kcguidanceStartTime").toString().replace(/T/, '');
        var endTime = dijit.byId("kcguidanceEndTime").toString().replace(/T/, '');
        var kcguidanceStartTime = dijit.byId("kcguidanceStartDate") + " " + startTime;
        var kcguidanceEndTime = dijit.byId("kcguidanceEndDate") + " " + endTime;
        var kcguidanceType = dojo.byId("kcguidanceType").value;
        var reportType = dojo.byId("reportType").value;


        // 判斷開始時間不要大於結束時間
        if (kcguidanceStartTime > kcguidanceEndTime) {
            kcjs._showMessage("請確認開始時間小於結束時間", "時間錯誤");
            return false;
        }
        var kcgpersonalPublicComment = dijit.byId("kcgpersonalPublicComment").getValue();
        var kcgpersonalPrivateComment = dijit.byId("kcgpersonalPrivateComment").getValue();
        var kcguidanceFileLevel = dijit.byId("kcguidanceFileLevel").getValue();
        // 有資料未填則返回
        if (!dijit.byId("personalGuidanceInsertForm").validate()) {
            return false;
        }
        // 判斷勾選哪些選項
        var optionId = "";
        var optionNote = "";
        var queId = document.getElementsByName("queId");
        var len = queId.length;
        for (i = 0; i < len; i++)
        {
            var id = queId[i].value;
            if (typeof (dijit.byId("optionNote" + id)) != "undefined") {
                if (dijit.byId("optionId" + id).get("checked")) {
                    optionId += id + ",";
                    if (dijit.byId("optionNote" + id).getValue() != "") {
                        optionNote += dijit.byId("optionNote" + id).getValue() + ",";
                    }
                } else if (dijit.byId("optionId" + id).type == "text") {
                    optionId += id + ",";
                    optionNote += dijit.byId("optionId" + id).value + ",";
                }
            }
        }
        dijit.byId("insertBtn").set("disabled", true);
        personalGuidanceStandby.show();
        dojo.xhrPost({
            url: dojo.byId("KCWarningInsertWarningGuidanceAction").value,
            content: {
                kcunitId: kcunitId,
                kcstudentUseId: kcstudentUseId,
                kcteacherUserId: kcteacherUserId,
                kcguidanceTopic: kcguidanceTopic,
                kcguidanceTotalHours: kcguidanceTotalHours,
                kcguidanceAddress: kcguidanceAddress,
                kcguidanceStartTime: kcguidanceStartTime,
                kcguidanceEndTime: kcguidanceEndTime,
                kcguidanceType: kcguidanceType,
                kcgpersonalPublicComment: kcgpersonalPublicComment,
                kcgpersonalPrivateComment: kcgpersonalPrivateComment,
                kcguidanceFileLevel: kcguidanceFileLevel,
                optionId: optionId,
                optionNote: optionNote,
                reportType: reportType
            },
            load: function() {
                kcjs.back();
            },
            error: function(error, ioArgs) {
                console.log(error);
            }
        });
        return true;
    }

    /**
     * 修改個人預警輔導紀錄 
     * @returns {Boolean} 是否執行完成
     */
    this.updateWarningGuidance = function() {
        var queId = document.getElementsByName("queId");
        // 判斷有選項未填則提示
        var len = queId.length;
        for (i = 0; i < len; i++)
        {
            if (dijit.byId("optionId" + queId[i].value).get("checked")) {
                var optionName = document.getElementsByName("optionName" + queId[i].value);
                var len2 = optionName.length;
                var checked = false;
                var check = 0;
                for (j = 0; j < len2; j++)
                {
                    if (optionName[j].checked == true) {
                        check = check + 1;
                    } else if (optionName[j].type == "textarea") {
                        if (optionName[j].value) {
                            check = check + 1;
                        }
                    }
                }
                if (check < dojo.byId("limit" + queId[i].value).value) {
                    kcjs._showMessage(dojo.byId("name" + queId[i].value).value + "選項未填", "資料遺漏");
                    return false;
                }
            }
        }
        // 輔導紀錄資料
        var kcunitId = dojo.byId("kcunitId").value;
        var kcstudentUseId = dojo.byId("kcstudentUseId").value;
        var kcteacherUserId = dojo.byId("kcteacherUserId").value;
        var kcguidanceTopic = "學習成績期中預警";
        var kcguidanceTotalHours = dijit.byId("kcguidanceTotalHours").getValue();
        var kcguidanceAddress = "教師研究室";
        var startTime = dijit.byId("kcguidanceStartTime").toString().replace(/T/, '');
        var endTime = dijit.byId("kcguidanceEndTime").toString().replace(/T/, '');
        var kcguidanceStartTime = dijit.byId("kcguidanceStartDate") + " " + startTime;
        var kcguidanceEndTime = dijit.byId("kcguidanceEndDate") + " " + endTime;
        var kcguidanceType = dojo.byId("kcguidanceType").value;
        var reportType = dojo.byId("reportType").value;
        var reportYear = dojo.byId("reportYear").value;
        var reportTerm = dojo.byId("reportTerm").value;

        // 判斷開始時間不要大於結束時間
        if (kcguidanceStartTime > kcguidanceEndTime) {
            kcjs._showMessage("請確認開始時間小於結束時間", "時間錯誤");
            return false;
        }
        var kcgpersonalPublicComment = dijit.byId("kcgpersonalPublicComment").getValue();
        var kcgpersonalPrivateComment = dijit.byId("kcgpersonalPrivateComment").getValue();
        var kcguidanceFileLevel = dijit.byId("kcguidanceFileLevel").getValue();
        // 有資料未填則返回
        if (!dijit.byId("personalGuidanceUpdateForm").validate()) {
            return false;
        }
        // 判斷勾選哪些選項
        var optionId = "";
        var optionNote = "";
        var queId = document.getElementsByName("queId");
        var len = queId.length;
        for (i = 0; i < len; i++)
        {
            var id = queId[i].value;
            if (typeof (dijit.byId("optionNote" + id)) != "undefined") {
                if (dijit.byId("optionId" + id).get("checked")) {
                    optionId += id + ",";
                    if (dijit.byId("optionNote" + id).getValue() != "") {
                        optionNote += dijit.byId("optionNote" + id).getValue() + ",";
                    }
                } else if (dijit.byId("optionId" + id).type == "text") {
                    optionId += id + ",";
                    optionNote += dijit.byId("optionId" + id).value + ",";
                }
            }
        }
        dijit.byId("updateBtn").set("disabled", true);
        personalGuidanceStandby.show();
        dojo.xhrPost({
            url: dojo.byId("KCWarningUpdateWarningGuidanceAction").value,
            content: {
                kcunitId: kcunitId,
                kcstudentUseId: kcstudentUseId,
                kcteacherUserId: kcteacherUserId,
                kcguidanceTopic: kcguidanceTopic,
                kcguidanceTotalHours: kcguidanceTotalHours,
                kcguidanceAddress: kcguidanceAddress,
                kcguidanceStartTime: kcguidanceStartTime,
                kcguidanceEndTime: kcguidanceEndTime,
                kcguidanceType: kcguidanceType,
                kcgpersonalPublicComment: kcgpersonalPublicComment,
                kcgpersonalPrivateComment: kcgpersonalPrivateComment,
                kcguidanceFileLevel: kcguidanceFileLevel,
                optionId: optionId,
                optionNote: optionNote,
                reportType: reportType,
                reportTerm: reportTerm,
                reportYear: reportYear
            },
            load: function() {
                kcjs.back();
            },
            error: function(error, ioArgs) {
                console.log(error);
            }
        });
        return true;
    }

    /**
     * 回到學生學習預警首頁       
     * @returns {Boolean} 是否執行完成  
     */
    this.back = function() {
        document.location.href = "./admin.php?site_id=0&mod=kuas_consultation_warning";
        return true;
    }

    /**
     * 檢視學生個人預警輔導記錄
     * @type {Boolean} 是否執行完成  
     */
    this.showWarningGuidance = function() {
        var grid = dijit.byId("getKCWarningIndexGrid");
        var items = grid.selection.getSelected();
        if (items.length != 1) {
            kcjs._showMessage("請選擇一筆資料", "提示");
            return false;
        }
        var condition = grid.store.getValue(items[0], 'kcwarning_have_counseling');
        if (condition == 'no') {
            kcjs._showMessage("目前尚無輔導記錄", "提示");
            return false;
        }
        else {
            var studentId = grid.store.getValue(items[0], 'kcstudent_user_id');
            var studentClass = grid.store.getValue(items[0], 'kcclass_name');
            var teacherId = grid.store.getValue(items[0], 'kcemployee_user_id');
            var studentUnitId = grid.store.getValue(items[0], 'kcunit_id');
            var year = grid.store.getValue(items[0], 'kcwarning_sem_year');
            var term = grid.store.getValue(items[0], 'kcwarning_sem_term');
        }
        dojo.byId("warningGuidanceStudentId").value = studentId;
        dojo.byId("warningGuidanceStudentClass").value = studentClass;
        dojo.byId("warningGuidanceTeacherId").value = teacherId;
        dojo.byId("warningGuidanceStudentUnitId").value = studentUnitId;
        dojo.byId("warningYear").value = year;
        dojo.byId("warningTerm").value = term;
        document.warningGuidancePageForm.submit();
        return true;
    }

    /**
     * 取得輔導項目 ( 個人輔導紀錄畫面 )
     * @returns {Boolean} 是否執行成功
     */
    this.getOptionAction = function() {
        var url = dojo.byId("getOptionUrl").value;
        var optionArray = dojo.byId("optionArray").value;
        var guidanceId = dojo.byId("guidanceId").value;
        dojo.xhrPost({
            url: url,
            handleAs: "json",
            content: {
                guidanceId: guidanceId,
                optionArray: optionArray
            },
            load: function(msg) {
                kcjs.diplayArray(msg);
            },
            error: function(error, ioArgs) {
                console.log(error);
            }
        });
        return true;
    }

    /**
     * 排列選項 ( 個人輔導紀錄 )
     * @param {array} msg 輔導選項陣列
     * @returns {Boolean} 是否執行成功
     */
    this.diplayArray = function(msg) {
        for (var i = 0; i < msg.length; i++) {
            var queOl = dojo.byId("ol" + msg[i].queId);
            // 產生 li 放到父選項的 ol
            var li = dojo.create("li", {
                style: "list-style-type: none ;margin-top:10px ;"
            }, queOl);
            // 產生 div 放進該選項的 li
            var div = dojo.create("div", {
                id: "option" + msg[i].id,
                style: "text-align: left ; width:100% ;"
            }, li);
            var optionType = msg[i].type;
            // radio 和 checkbox 判斷式流程
            // 1. 先判斷型態
            // 2. 判斷父選項是否為 checked
            // 3. 判斷自己是否為 checked
            if (optionType == "checkbox") {
                //若父選項為 checked 則顯示
                if (msg[i].queChecked == "checked") {
                    if (msg[i].checked == "checked") {
                        var check = true;
                    } else {
                        var check = false;
                    }
                    var checkbox = new dijit.form.CheckBox({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: check
                    });
                    dojo.byId("option" + msg[i].id).appendChild(checkbox.domNode);
                    dojo.create("span", {
                        innerHTML: msg[i].name
                    }, div);
                } else if (msg[i].queChecked != "checked") {
                    var checkbox = new dijit.form.CheckBox({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: false,
                        style: {
                            display: "none"
                        }
                    });
                    li.style.cssText = "display:none";
                    dojo.byId("option" + msg[i].id).appendChild(checkbox.domNode);
                }
                // 若當前選項型態為 radio
            } else if (optionType == "radio") {
                // 若父選項為 checked 則顯示
                if (msg[i].queChecked == "checked") {
                    if (msg[i].checked == "checked") {
                        var check = true;
                    } else {
                        var check = false;
                    }
                    var radioTextBox = new dijit.form.RadioButton({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: check
                    });
                    dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                    dojo.create("span", {
                        innerHTML: msg[i].name
                    }, div);
                } else if (msg[i].queChecked != "checked") {
                    var radioTextBox = new dijit.form.RadioButton({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: false,
                        style: {
                            display: "none"
                        }
                    });
                    li.style.cssText = "display :none ;";
                    dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                }
            }
            // 判斷型態為 textbox 時執行判斷同階的第一筆資料為 radio or checkbox
            else if (optionType == "textbox") {
                // 判斷當前選項是否被勾選
                if (msg[i].checked == "checked") {
                    if (msg[i].queChecked == "checked") {
                        if (msg[i].checkType == "radio") {
                            if (msg[i].checked == "checked") {
                                var check = true;
                            } else {
                                var check = false;
                            }
                            var radioTextBox = new dijit.form.RadioButton({
                                id: msg[i].id,
                                name: msg[i].queId,
                                value: msg[i].checked,
                                readOnly: true,
                                checked: check
                            });
                            // 將 RadioButton 放進父選項的 div
                            dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name + " "
                            }, div);
                            var textbox = new dijit.form.TextBox({
                                id: "text" + msg[i].id,
                                readOnly: true,
                                value: msg[i].desc
                            });
                            dojo.byId("option" + msg[i].id).appendChild(textbox.domNode);
                        }
                    }
                    // 判斷是否型態為 checkbox
                    if (msg[i].checkType == "checkbox") {
                        if (msg[i].queChecked == "checked") {
                            // 判斷當前選項是否被勾選
                            if (msg[i].checked == "checked") {
                                var check = true;
                            } else {
                                var check = false;
                            }
                            var checkboxTextBox = new dijit.form.CheckBox({
                                id: msg[i].id,
                                readOnly: true,
                                checked: check
                            });
                            dojo.byId("option" + msg[i].id).appendChild(checkboxTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name + " "
                            }, div);
                            var textbox = new dijit.form.TextBox({
                                id: "text" + msg[i].id,
                                readOnly: true,
                                value: msg[i].desc
                            });
                            dojo.byId("option" + msg[i].id).appendChild(textbox.domNode);
                        }
                    }
                    // 判斷當前選項是否無資料
                } else if (msg[i].checked != "checked") {
                    // 該選項的父選項被選取時
                    if (msg[i].queChecked == "checked") {
                        if (msg[i].checkType == "radio") {
                            var radioTextBox = new dijit.form.RadioButton({
                                id: msg[i].id,
                                checked: false,
                                readOnly: true
                            });
                            dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name
                            }, div);
                        }
                    }
                    else {
                        li.style.cssText = "display :none ;";
                    }
                    if (msg[i].checkType == "checkbox") {
                        // 該選項的父選項被選取時
                        if (msg[i].queChecked == "checked") {
                            var checkboxTextBox = new dijit.form.CheckBox({
                                id: msg[i].id,
                                checked: false,
                                readOnly: true
                            });
                            dojo.byId("option" + msg[i].id).appendChild(checkboxTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name
                            }, div);
                        }
                    }
                    var textbox = new dijit.form.TextBox({
                        id: "text" + msg[i].id,
                        readOnly: true,
                        style: {
                            display: "none"
                        },
                        value: msg[i].desc
                    });
                    dojo.byId("option" + msg[i].id).appendChild(textbox.domNode);
                }
            } else if (optionType == "textarea") {
                var textDiv = dojo.create("div", {
                    style: {
                        "overflow-y": "auto",
                        height: "auto",
                        "max-height": "77px"
                    }
                }, div);
                dojo.create("span", {
                    innerHTML: msg[i].desc
                }, textDiv);
            }
            // 產生 ol 放進該選項的 div
            var ol = dojo.create("ol", {
                id: "ol" + msg[i].id,
                style: "list-style-type: none ;dispaly ;"
            }, div);
        }
        return true;
    }

    /**
     * 排列選項 ( 個人輔導紀錄 )
     * @param {array} msg 輔導選項陣列
     * @returns {Boolean} 是否執行成功
     */
    this.diplayArray = function(msg) {
        for (var i = 0; i < msg.length; i++) {
            var queOl = dojo.byId("ol" + msg[i].queId);
            // 產生 li 放到父選項的 ol
            var li = dojo.create("li", {
                style: "list-style-type: none ;margin-top:10px ;"
            }, queOl);
            // 產生 div 放進該選項的 li
            var div = dojo.create("div", {
                id: "option" + msg[i].id,
                style: "text-align: left ; width:100% ;"
            }, li);
            var optionType = msg[i].type;
            // radio 和 checkbox 判斷式流程
            // 1. 先判斷型態
            // 2. 判斷父選項是否為 checked
            // 3. 判斷自己是否為 checked
            if (optionType == "checkbox") {
                //若父選項為 checked 則顯示
                if (msg[i].queChecked == "checked") {
                    if (msg[i].checked == "checked") {
                        var check = true;
                    } else {
                        var check = false;
                    }
                    var checkbox = new dijit.form.CheckBox({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: check
                    });
                    dojo.byId("option" + msg[i].id).appendChild(checkbox.domNode);
                    dojo.create("span", {
                        innerHTML: msg[i].name
                    }, div);
                } else if (msg[i].queChecked != "checked") {
                    var checkbox = new dijit.form.CheckBox({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: false,
                        style: {
                            display: "none"
                        }
                    });
                    li.style.cssText = "display:none";
                    dojo.byId("option" + msg[i].id).appendChild(checkbox.domNode);
                }
                // 若當前選項型態為 radio
            } else if (optionType == "radio") {
                // 若父選項為 checked 則顯示
                if (msg[i].queChecked == "checked") {
                    if (msg[i].checked == "checked") {
                        var check = true;
                    } else {
                        var check = false;
                    }
                    var radioTextBox = new dijit.form.RadioButton({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: check
                    });
                    dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                    dojo.create("span", {
                        innerHTML: msg[i].name
                    }, div);
                } else if (msg[i].queChecked != "checked") {
                    var radioTextBox = new dijit.form.RadioButton({
                        id: msg[i].id,
                        name: msg[i].queId,
                        value: msg[i].checked,
                        readOnly: true,
                        checked: false,
                        style: {
                            display: "none"
                        }
                    });
                    li.style.cssText = "display :none ;";
                    dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                }
            }
            // 判斷型態為 textbox 時執行判斷同階的第一筆資料為 radio or checkbox
            else if (optionType == "textbox") {
                // 判斷當前選項是否被勾選
                if (msg[i].checked == "checked") {
                    if (msg[i].queChecked == "checked") {
                        if (msg[i].checkType == "radio") {
                            if (msg[i].checked == "checked") {
                                var check = true;
                            } else {
                                var check = false;
                            }
                            var radioTextBox = new dijit.form.RadioButton({
                                id: msg[i].id,
                                name: msg[i].queId,
                                value: msg[i].checked,
                                readOnly: true,
                                checked: check
                            });
                            // 將 RadioButton 放進父選項的 div
                            dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name + " "
                            }, div);
                            var textbox = new dijit.form.TextBox({
                                id: "text" + msg[i].id,
                                readOnly: true,
                                value: msg[i].desc
                            });
                            dojo.byId("option" + msg[i].id).appendChild(textbox.domNode);
                        }
                    }
                    // 判斷是否型態為 checkbox
                    if (msg[i].checkType == "checkbox") {
                        if (msg[i].queChecked == "checked") {
                            // 判斷當前選項是否被勾選
                            if (msg[i].checked == "checked") {
                                var check = true;
                            } else {
                                var check = false;
                            }
                            var checkboxTextBox = new dijit.form.CheckBox({
                                id: msg[i].id,
                                readOnly: true,
                                checked: check
                            });
                            dojo.byId("option" + msg[i].id).appendChild(checkboxTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name + " "
                            }, div);
                            var textbox = new dijit.form.TextBox({
                                id: "text" + msg[i].id,
                                readOnly: true,
                                value: msg[i].desc
                            });
                            dojo.byId("option" + msg[i].id).appendChild(textbox.domNode);
                        }
                    }
                    // 判斷當前選項是否無資料
                } else if (msg[i].checked != "checked") {
                    // 該選項的父選項被選取時
                    if (msg[i].queChecked == "checked") {
                        if (msg[i].checkType == "radio") {
                            var radioTextBox = new dijit.form.RadioButton({
                                id: msg[i].id,
                                checked: false,
                                readOnly: true
                            });
                            dojo.byId("option" + msg[i].id).appendChild(radioTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name
                            }, div);
                        }
                    }
                    else {
                        li.style.cssText = "display :none ;";
                    }
                    if (msg[i].checkType == "checkbox") {
                        // 該選項的父選項被選取時
                        if (msg[i].queChecked == "checked") {
                            var checkboxTextBox = new dijit.form.CheckBox({
                                id: msg[i].id,
                                checked: false,
                                readOnly: true
                            });
                            dojo.byId("option" + msg[i].id).appendChild(checkboxTextBox.domNode);
                            dojo.create("span", {
                                innerHTML: msg[i].name
                            }, div);
                        }
                    }
                    var textbox = new dijit.form.TextBox({
                        id: "text" + msg[i].id,
                        readOnly: true,
                        style: {
                            display: "none"
                        },
                        value: msg[i].desc
                    });
                    dojo.byId("option" + msg[i].id).appendChild(textbox.domNode);
                }
            } else if (optionType == "textarea") {
                var textDiv = dojo.create("div", {
                    style: {
                        "overflow-y": "auto",
                        "height": "auto",
                        "max-height": "77px"
                    }
                }, div);
                dojo.create("span", {
                    innerHTML: msg[i].desc
                }, textDiv);
            }
            // 產生 ol 放進該選項的 div
            var ol = dojo.create("ol", {
                id: "ol" + msg[i].id,
                style: "list-style-type: none ;dispaly ;"
            }, div);
        }
        return true;
    }

    /*
     * 點擊匯出預警輔導記錄為 pdf 按鈕
     * @param {array} msg 輔導選項陣列
     * @returns {Boolean} 是否執行成功
     */
    this.exportWarningGuidanceBtn = function() {
        warningDataStandby.show();
        var grid = dijit.byId("getKCWarningIndexGrid");
        var items = grid.selection.getSelected();
        // 無選取特定學生，取全部有輔導的資料
        if (items.length == 0) {
            var from = "export";
            kcjs.checkDataExist(from);
            return false;
        }
        if (items.length == 1) {
            var condition = grid.store.getValue(items[0], 'kcwarning_have_counseling');
            if (condition == 'no') {
                kcjs._showMessage("您選取的學生目前尚無輔導記錄，無法使用輔導記錄表輸出功能", "提示");
                warningDataStandby.hide();
                return false;
            } else {
                dojo.byId("exportContent").innerHTML = "共 1 名學生之輔導記錄表，將會統一合併成 PDF 檔後輸出，是否確定輸出 ?";
                dijit.byId("exportDialog").show();
                return false;
            }
        }
        if (items.length > 1) {
            var num = 0;
            var exportNum = 0;
            var studentIds = "";
            var grid = dijit.byId("getKCWarningIndexGrid");
            var items = grid.selection.getSelected();
            dojo.forEach(items, function(node) {
                var condition = grid.store.getValue(node, 'kcwarning_have_counseling');
                if (condition == 'no') {
                    num++;
                } else {
                    var studentId = grid.store.getValue(node, 'kcstudent_user_id');
                    var year = grid.store.getValue(items[0], 'kcwarning_sem_year');
                    var term = grid.store.getValue(items[0], 'kcwarning_sem_term');
                    studentIds = studentIds + "," + studentId;
                    exportNum++;
                }
            });
            if (num == items.length) {
                kcjs._showMessage("您選取的學生目前尚無輔導記錄，無法使用輔導記錄表輸出功能", "提示");
                warningDataStandby.hide();
                return false;
            }
            dojo.byId("exportContent").innerHTML = "共 " + exportNum + " 名學生之輔導記錄表，將會統一合併成 PDF 檔後輸出，是否確定輸出 ?";
            dijit.byId("exportDialog").show();
            return false;


        }
    }

    /*
     * 匯出預警輔導記錄為 pdf 
     * @param {array} msg 輔導選項陣列
     * @returns {Boolean} 是否執行成功
     */
    this.exportWarningGuidance = function() {
        warningDataStandby.show();
        dijit.byId("exportDialog").hide();
        dojo.byId("loadingContent").innerHTML = "檔案合併中，請稍待片刻...";
        dijit.byId("loadingDialog").show();
        var grid = dijit.byId("getKCWarningIndexGrid");
        var items = grid.selection.getSelected();
        // 無選取特定學生，取全部有輔導的資料
        if (items.length == 0) {
            var year = dijit.byId("semYear").getValue();
            var term = dijit.byId("searchByTerm").getValue();
            var isGuidanceStudentId = dojo.byId("isGuidanceStudentId").value;
            var isGuidanceNum = dojo.byId("isGuidanceNum").value;
            dojo.byId("exportGuidanceStudentId").value = isGuidanceStudentId;
            dojo.byId("exportGuidanceStudentClass").value = studentClass;
            dojo.byId("exportGuidanceTeacherId").value = teacherId;
            dojo.byId("exportGuidanceStudentUnitId").value = studentUnitId;
            dojo.byId("exportYear").value = year;
            dojo.byId("exportTerm").value = term;
            dojo.xhrPost({
                form: "exportWarningGuidancePageForm",
                content: {
                },
                load: function(data) {
                    warningDataStandby.hide();
                    dijit.byId("loadingDialog").hide();
					document.exportWarningGuidancePageForm.submit();
                },
                error: function(error) {
                    console.log(error);
                }
            });
            return true;
        }
        if (items.length == 1) {
            var condition = grid.store.getValue(items[0], 'kcwarning_have_counseling');
            if (condition == 'no') {
                kcjs._showMessage("您選取的學生目前尚無輔導記錄，無法使用輔導記錄表輸出功能", "提示");
                return false;
            } else {
                var studentId = grid.store.getValue(items[0], 'kcstudent_user_id');
                var studentClass = grid.store.getValue(items[0], 'kcclass_name');
                var teacherId = grid.store.getValue(items[0], 'kcemployee_user_id');
                var studentUnitId = grid.store.getValue(items[0], 'kcunit_id');
                var year = grid.store.getValue(items[0], 'kcwarning_sem_year');
                var term = grid.store.getValue(items[0], 'kcwarning_sem_term');
            }
            dojo.byId("exportGuidanceStudentId").value = studentId;
            dojo.byId("exportGuidanceStudentClass").value = studentClass;
            dojo.byId("exportGuidanceTeacherId").value = teacherId;
            dojo.byId("exportGuidanceStudentUnitId").value = studentUnitId;
            dojo.byId("exportYear").value = year;
            dojo.byId("exportTerm").value = term;
            var termArray = "";
            dojo.xhrPost({
                form: "exportWarningGuidancePageForm",
                content: {
                },
                load: function(data) {
                    warningDataStandby.hide();
                    dijit.byId("loadingDialog").hide();
					document.exportWarningGuidancePageForm.submit();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
        if (items.length > 1) {
            var num = 0;
            var studentIds = "";
            var year = "";
            var term = "";
            var terms = "";
            var grid = dijit.byId("getKCWarningIndexGrid");
            var items = grid.selection.getSelected();
            dojo.forEach(items, function(node) {
                var condition = grid.store.getValue(node, 'kcwarning_have_counseling');
                if (condition == 'no') {
                    num++;
                } else {
                    studentId = grid.store.getValue(node, 'kcstudent_user_id');
                    year = grid.store.getValue(node, 'kcwarning_sem_year');
                    term = grid.store.getValue(node, 'kcwarning_sem_term');
                    studentIds = studentIds + "," + studentId;
                    terms = terms + "," + term;
                }
            });
            if (num == items.length) {
                kcjs._showMessage("您選取的學生目前尚無輔導記錄，無法使用輔導記錄表輸出功能", "提示");
                return false;
            }
            dojo.byId("exportGuidanceStudentId").value = studentIds;
            dojo.byId("exportYear").value = year;
            dojo.byId("exportTerm").value = term;
            dojo.byId("termArray").value = terms;
            dojo.xhrPost({
                form: "exportWarningGuidancePageForm",
                content: {
                },
                load: function(data) {
                    warningDataStandby.hide();
                    dijit.byId("loadingDialog").hide();
					document.exportWarningGuidancePageForm.submit();
                },
                error: function(error) {
                    console.log(error);
                }
            });
            return true;

        }
    }

    /*
     * 關閉 dialog
     */
    this.closeDialog = function() {
        warningDataStandby.hide();
    }

}

var kcjs = new KCWarningJS();

