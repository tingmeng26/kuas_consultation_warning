<link rel="stylesheet" type="text/css" href="./modules/kuas_consultation_common/admin/css/KCCommonCSS.css" />
<link rel="stylesheet" type="text/css" href="./modules/kuas_consultation_warning/admin/css/KCWarningCSS.css" />
<link rel="stylesheet" type="text/css" href="./script/dojo/dojox/grid/resources/tundraGrid.css" />
<script src="./modules/kuas_consultation_warning/admin/script/KCWarningJS.js"></script>
<script type="text/javascript">
	dojo.addOnLoad(function() {
                var display = dojo.byId("display").value;
		if (display == 'display') {
			show_warning();
		}

		if (display == 'none') {
			dojo.byId("smtpDiv").style.display = "none";

		}
		var store = new dojox.data.QueryReadStore({
			url: dojo.byId("kcWarningDataGridUrl").value,
			requestMethod: "GET"
		})
		dijit.byId("getKCWarningIndexGrid").setStore(store);        
                kcjs.conditionSelect();
	});
	function show_warning() {
		var fadeIn = dojo.fadeIn({node: "smtpDiv", duration: 1000});
		var wipeIn = dojo.fx.wipeIn({node: "smtpDiv",duration: 1000});
		var anim = dojo.fx.combine([fadeIn, wipeIn]);
		anim.play();
	}


</script>
<!--填報預警輔導 -->
<form action="<{$KCWarningReportWarningGuidancePage}>" method="post" name="reportForm" style="display:none">
    <input type="text" name="reportStudentId" id="reportStudentId"/>
    <input type="text" name="reportStudentClass" id="reportStudentClass"/>
    <input type="text" name="reportTeacherId" id="reportTeacherId"/>
    <input type="text" name="reportStudentUnitId" id="reportStudentUnitId"/>
    <input type="text" name="reportTeacherName" id="reportTeacherName"/>
      <input type="text" name="reportType" id="reportType"/>
      <input type="text" name="reportYear" id="reportYear"/>
      <input type="text" name="reportTerm" id="reportTerm"/>
</form>

<form action="<{$KCWarningShowStudentGuidancePage}>" method="post" name="warningGuidancePageForm" style="display:none">
 <input type="text" name="warningGuidanceStudentId" id="warningGuidanceStudentId"/>
    <input type="text" name="warningGuidanceStudentClass" id="warningGuidanceStudentClass"/>
    <input type="text" name="warningGuidanceTeacherId" id="warningGuidanceTeacherId"/>
    <input type="text" name="warningGuidanceStudentUnitId" id="warningGuidanceStudentUnitId"/>   
    <input type="text" name="warningYear" id="warningYear"/> 
    <input type="text" name="warningTerm" id="warningTerm"/> 
</form>

<form id="exportWarningGuidancePageForm" action="<{$KCWarningExportWarningGuidancePage}>" method="post" name="exportWarningGuidancePageForm"  style="display:none">
 <input type="text" name="exportGuidanceStudentId" id="exportGuidanceStudentId"/>
    <input type="text" name="exportGuidanceStudentClass" id="exportGuidanceStudentClass"/>
    <input type="text" name="exportGuidanceTeacherId" id="exportGuidanceTeacherId"/>
    <input type="text" name="exportGuidanceStudentUnitId" id="exportGuidanceStudentUnitId"/>   
    <input type="text" name="exportYear" id="exportYear"/> 
    <input type="text" name="exportTerm" id="exportTerm"/> 
    <input type="text" name="termArray" id="termArray"/>  
</form>
<input id="kcWarningDataGridUrl"type="hidden" value="<{$kcWarningDataGridUrl}>">
<input id="display" type="hidden" value="<{$display}>">
<input id="privilege" type="hidden" value='<{$privilege}>'>
<input id="loginId" type="hidden" value='<{$userId}>'>
<br/>
<div id="smtpDiv" style=" overflow: hidden; height: 0px; opacity: 0; display:">
    <fieldset style="padding: 10px">
		<legend style ="color:red; font-weight: bold;">!!! 注意 !!!</legend>
		<div style="text-align: left; color:red"><b>此模組含自動寄送郵件功能，請系統管理者務必至系統組態進行郵件相關設定:</b></div>
		<{foreach from=$required_close_file item="file"}>
		<ul>
			<div style ="color:red"> <li><{$file}></li> </div>
		</ul>
		<{/foreach}>


    </fieldset>

    <br/>
</div>
<input type="hidden" id="term" value="<{$term}>"/>
<input type="hidden" id="year" value="<{$year}>"/>
<input type="hidden" id="kcWarningDataGridUrl" value="<{$kcWarningDataGridUrl}>"/>
<input type="hidden" id="kcWarningSentFullMailUrl" value="<{$kcWarningSentFullMailUrl}>"/>
<input type="hidden" id="kcWarningDoDownloadActionUrl" value="<{$kcWarningDoDownloadActionUrl}>"/>
<input type="hidden" id="kcWarningDoCheckDataExistActionUrl" value="<{$kcWarningDoCheckDataExistActionUrl}>"/>
<input type="hidden" id="departmentAdminUnitId" value="<{$departmentAdminUnitId}>"/>
<input type="hidden" id="userId" value="<{$userId}>"/>  
<input type="hidden" id="kcWarningShowGuidanceUrl" value="<{$kcWarningShowGuidanceUrl}>"/>  
<input type="hidden" id="kcWarningSentSingleMailUrl" value="<{$kcWarningSentSingleMailUrl}>"/>  
<input type="hidden" id="kcWarningSentMultiMailUrl" value="<{$kcWarningSentMultiMailUrl}>"/>
<input type="hidden" id="kcIndexSync" value="<{$kcIndexSync}>"/>  
<input type="hidden" id="kcWarningHostUrl" value="<{$kcWarningHostUrl}>"/>  
<input type="hidden" id="isGuidanceStudentId" value="<{$isGuidanceStudentId}>"/>  
<input type="hidden" id="isGuidanceNum" value="<{$isGuidanceNum}>"/>  
<input type="hidden" id="KCWarningExportWarningGuidancePage" value="<{$KCWarningExportWarningGuidancePage}>"/>  
<input type="hidden" id="KCWarningCheckAddresseeAction" value="<{$KCWarningCheckAddresseeAction}>"/> 

<input type="hidden" id="KCWarningExportWarningGuidancePage" value="<{$KCWarningExportWarningGuidancePage}>"/>  
<div jsId="warningDataStandby" dojoType="dojox.widget.Standby" target="forStandBy"></div>
<div id="forStandBy">
    <fieldset>
		<table style="width: 100%">
			<legend>
				<b>
					查詢條件
				</b>
			</legend>
                        <tr>
                            <th class="menuTh">
                                學年/學期
                            </th>
                            <td class="menuTd">
                                <select id="semYear" style="width: 60px"  dojoType="dijit.form.FilteringSelect" pageSize="5" required="true" onChange="kcjs.conditionSelect();"  >
					<{foreach item=selectYear from=$selectYear}>
                                            <option value="<{$selectYear}>"><{$selectYear}></option>
					<{/foreach}>
				</select>
                            </td>
                            <{if $term == '1'}>
                            <td class="menuTd">
                                <select id="searchByTerm" style="width: 60px"   dojoType="dijit.form.FilteringSelect" required="true"  onChange="kcjs.conditionSelect();"  >
					<option value="0" >全部</option>
					<option value="1" selected>上</option>
					<option value="2" >下</option>
				</select>
                            </td>
                            <{/if}>
                            <{if $term == '2'}>
                            <td class="menuTd">
                                <select id="searchByTerm" style="width: 60px"   dojoType="dijit.form.FilteringSelect" required="true"  onChange="kcjs.conditionSelect();"  >
					<option value="0" >全部</option>
					<option value="1" >上</option>
					<option value="2" selected>下</option>
				</select>
                            </td>
                            <{/if}>
                        </tr>
                        <{if $privilege=='OfficeAdmin'}>
                            <tr>
                                <th class="menuTh">
                                系所
                                </th>
                                <td class="menuTd" colspan="3">
                                    <select id="searchByUnit" style="width:130px"  dojoType="dijit.form.FilteringSelect" pageSize="5" required="true" onChange="kcjs.conditionSelect();" >
                                        <option value="0" selected>全部科系</option>
                                        <{foreach item=selectUnit from=$selectUnit}>
                                            <{if $selectUnit->kcunit_name!=""}>
                                                <option value="<{$selectUnit->kcunit_name}>"><{$selectUnit->kcunit_name}></option>
                                            <{/if}>
					<{/foreach}>
                                    </select> 
                                </td>				                           
                            </tr>     
                        <{/if}>
                        <tr>
                            <th class="menuTh">
                                預警類型
                            </th>
                            <td class="menuTd">
				<input type="radio" dojoType="dijit.form.RadioButton" id="allTypeBox" name="typeRdo" checked  onChange="kcjs.conditionSelect();" >
                                <label for="allTypeBox">全部</label>                                
                            </td>
                            <td class="menuTd">
                                <input type="radio" dojoType="dijit.form.RadioButton" id="isWarningBox" name="typeRdo" onChange="kcjs.conditionSelect();" >
                                <label for="isWarningBox">期中預警</label>
                            </td>
                            <td>
				<input type="radio" dojoType="dijit.form.RadioButton" id="isPoorBox" name="typeRdo" onChange="kcjs.conditionSelect();" >
                                <label for="isPoorBox">課業不佳</label>                                
                            </td>                            
                        </tr>
                        <tr>
                            <th>
                                輔導狀況
                            </th>
                            <td class="menuTd">
				<input type="radio" dojoType="dijit.form.RadioButton" id="allGuidanceBox" name="guidanceRdo" checked onChange="kcjs.conditionSelect();" >
                                <label for="allGuidanceBox">全部</label>                                
                            </td>
                            <td class="menuTd">
                                <input type="radio" dojoType="dijit.form.RadioButton" id="isGuidanceBox" name="guidanceRdo" onChange="kcjs.conditionSelect();" >
                                <label for="isGuidanceBox">已輔導</label>
                            </td>
                            <td>
				<input type="radio" dojoType="dijit.form.RadioButton" id="isntGuidanceBox" name="guidanceRdo" onChange="kcjs.conditionSelect();" >
                                <label for="isntGuidanceBox">未輔導</label>                                
                            </td>                            
                        </tr>
                        <tr>
                            <th class="menuTh">
                                通知狀況
                            </th>
                            <td class="menuTd">
				<input type="radio" dojoType="dijit.form.RadioButton" id="allNoticeBox" name="noticeRdo" checked onChange="kcjs.conditionSelect();" >
                                <label for="allNoticeBox">全部</label> 
                            </td>
                            <td class="menuTd">
                                <input type="radio" dojoType="dijit.form.RadioButton" id="isNoticeBox" name="noticeRdo" onChange="kcjs.conditionSelect();" >
                                <label for="isNoticeBox">已通知</label>
                            </td>
                            <td>
				<input type="radio" dojoType="dijit.form.RadioButton" id="isntNoticeBox" name="noticeRdo" onChange="kcjs.conditionSelect();" >
                                <label for="isntNoticeBox">未通知</label> 
                            </td>                            
                        </tr>
                        <tr>
                            <th class="menuTh">
                                關鍵字
                            </th>
                            <td colspan="3" class="menuTd">
                                <input id="searchByName" style="width: 220px" dojoType="dijit.form.ValidationTextBox" placeHolder="請輸入學生姓名或導師姓名" >
				<button type="button" dojoType="dijit.form.Button" iconclass="seachButton" onclick="kcjs.conditionSelect();">查詢</button>
				<button type="button" dojoType="dijit.form.Button" iconclass="resetButtonIcon" onclick="kcjs.kcwReset();">  重置</button>
                            </td>
                        </tr>			
		</table>
    </fieldset>
    <br/>
    <div id="kcToorBarForUnit" dojoType="dijit.Toolbar" style="height:25px">
        <div style="float:left;margin-top:7px;font-weight: bold;" id="warningMsg" class="warningMsg"></div>
		<div style="text-align: right;float:right">
			<span >
                <{if $isTutor == 'yes'}>
                <button  type="button" dojoType="dijit.form.Button" iconclass="editButton" onclick="kcjs.reportWarningGuidance()">預警輔導記錄填報</button>
                <{/if}>
                <button  type="button" dojoType="dijit.form.Button" iconclass="seachButton" onclick="kcjs.showWarningGuidance()">檢視輔導記錄</button>
				<button  type="button" dojoType="dijit.form.Button" iconclass="excelButton" onClick="kcjs.clickWarningData();">預警名單下載</button>
				<button  type="button" dojoType="dijit.form.Button" iconclass="pdfButton" onClick="kcjs.exportWarningGuidanceBtn();">輔導記錄表輸出</button>
                <!-- 教務處綜合教務組管理者  -->
                <{if $isRegistryManager == 'yes'}>
				<button  type="button" dojoType="dijit.form.Button" iconclass="mailButton" onclick="kcjs.ShowLetter();">寄發預警輔導通知</button>
                <{/if}>
			</span>
		</div>
</div>

    
    <!-- end toolbar -->  
    <div  dojoType="dojox.data.QueryReadStore" url="<{$kcWarningDataGridUrl}>" requestMethod='GET' jsId="getKCWarningIndexStore"　id="getKCWarningIndexStoreid" ></div>
    <table id="getKCWarningIndexGrid" dojoType="dojox.grid.DataGrid" closable='true'   noDataMessage='目前沒有資料'  errorMessage= '查無此資料'loadingMessage='載入中...' autoHeight="15" rowsPerPage="15" canSort=""  style="width: 100%; " selectable="true" >
		<thead>
			<tr >
				<th field="row_id" width="20px" hidden="true" formatter="kcjs.showNumber" rowspan="2">#</th>
				<th field="kcwarning_id" hidden="true"  rowspan="2">學習預警編號</th>
				<th field="kcemployee_user_id"hidden="true"  rowspan="2">導師編號</th>
				<th field="kcunit_id" hidden="true"  rowspan="2" >單位編號</th>
				<th field="kcwarning_sem_year" width="50px" hidden="true" rowspan="2" >學年度</th>
				<th field="kcwarning_sem_term" formatter="kcjs.checkTerm" width="30px"  rowspan="2">學期</th>
				<th field="kcunit_name" width="auto" formatter="kcjs.emptySign"  rowspan="2">系所</th>
				<th field="kcclass_name" width="80px" formatter="kcjs.emptySign"  rowspan="2">班級</th>
				<th field="kcstudent_user_id" width="80px"  formatter="kcjs.emptySign"  rowspan="2" >學號</th>
				<th field="kcstudent_name"width="60px"  rowspan="2" >學生名稱</th>
				<th field="kcteacher_name" width="55px" rowspan="2" >導師名稱</th>
				<th colspan="2" formatter='kcjs.mergeField' styles="text-align: center;width:150px">上學期課業不佳</th>
				<th field="kcwarning_poor_schoolwork_subject"width="120px" formatter="kcjs.emptySign" rowspan="2">上學期不及格科目</th>
				<th colspan="2" formatter='kcjs.mergeField' styles="text-align: center;width:150px">本學期期中預警</th>
				<th field="kcwarning_subject"width="120px" formatter="kcjs.emptySign" rowspan="2">本學期期中預警科目</th>
				<th field="kcwarning_advice_times" width="60px" rowspan="2">通知次數</th> 
				<th field="kcwarning_have_counseling"width="60px" formatter='kcjs.decideTutor' rowspan="2">輔導狀況</th>
                </tr>
			<tr>
				<th field="kcwarning_half_poor_schoolwork" formatter="kcjs.checkWarning" styles="text-align: center;width:75px">1/2</th> 
				<th field="kcwarning_three_quarters_poor_schoolwork"formatter="kcjs.checkWarning" styles="text-align: center;width:75px">2/3</th>
				<th field="kcwarning_half" formatter="kcjs.checkWarning" styles="text-align: center;width:75px"> 1/2 預警</th> 
				<th field="kcwarning_three_quarters"formatter="kcjs.checkWarning" styles="text-align: center;width:75px"> 2/3 預警</th>

			</tr>
		</thead>
    </table >
</div>
<!--通知dialog-->
<div id ="dialogKCLetter" dojoType="dijit.Dialog" title="寄發預警輔導通知" style="width: auto;"> 

    <form  method="post" id="uploadedfileform" enctype="multipart/form-data" dojoType="dijit.form.Form" >

		<table style="width: auto;"> 
			<input id="noticeName" type="hidden" >
			<tr>
				<th width= "60px">主旨</th>
				<td><input id="letterSubject" dojoType="dijit.form.ValidationTextBox" style="width: 300px;" value="" required></td>

			</tr>
			<tr>
				<th>對象</th>
				<td>所有預警學生之導師</td>

			</tr>
			<tr>
				<th>內容</th>
				<td><input id="letterMessage" dojoType="dijit.form.SimpleTextarea" value="" style="height: 100px; width: 300px;max-width: 300px; max-height: 300px"  required></td>

			</tr>
			<tr>
				<th>簽名檔</th>
				<td><input id="letterSignName" dojoType="dijit.form.SimpleTextarea" value=""  style="height: 100px; width: 300px;max-width: 300px; max-height: 300px" required></td>
			</tr>



		</table>

		<div class="dijitDialogPaneActionBar">
			<button style="text-align: left;" id="sentMailBtn" type="button" dojoType="dijit.form.Button" onClick=" kcjs.sentLetter();" iconclass="mailButton">寄發
			</button >
			<button type="button" dojoType="dijit.form.Button" onClick=" dijit.byId('dialogKCLetter').hide();" iconclass="cancelButton">取消
			</button>
		</div>
    </form>    
</div>
<div jsId="basicStandbyLetter" dojoType="dojox.widget.Standby" target="dialogKCLetter"></div>
<!--單筆通知dialog-->
<div id ="dialogKCLetterOne" dojoType="dijit.Dialog" title="寄發預警輔導通知" style="width: auto;"> 

    <form  method="post" id="uploadedfileformOne" enctype="multipart/form-data" dojoType="dijit.form.Form" >
		<input type="hidden" id="warningId" value="">
		<input type="hidden" id="warningStudentId" value="">
		<input type="hidden" id="warningStudentNameHidden" value="">
		<input type="hidden" id="warningIds" value="">
		<input type="hidden" id="warningStudentIds" value="">
		<input type="hidden" id="warningStudentNameHiddens" value="">
		<table style="width: auto;"  > 
			<input id="noticeNameOne" type="hidden" >
			<tr>
				<th width= "60px">主旨</th>
				<td><input id="letterSubjectOne" dojoType="dijit.form.ValidationTextBox" style="width: 300px;" required></td>

			</tr>
			<tr>
				<th>對象</th>
				<td><span id="warningStudentName" ></span></td>

			</tr>
			<tr>
				<th>內容</th>
				<td><input id="letterMessageOne" dojoType="dijit.form.SimpleTextarea" style="height: 100px; width: 300px;max-width: 300px; max-height: 300px"  required></td>

			</tr>
			<tr>
				<th>簽名檔</th>
				<td><input id="letterSignNameOne" dojoType="dijit.form.SimpleTextarea" value=""  style="height: 100px; width: 300px;max-width: 300px; max-height: 300px" required></td>
			</tr>



		</table>

		<div class="dijitDialogPaneActionBar">
			<button style="text-align: left;" id="sentMailBtnOne" type="button" dojoType="dijit.form.Button" onClick=" kcjs.sentLetterOne();" iconclass="mailButton">寄發
			</button >
			<button type="button" dojoType="dijit.form.Button" onClick=" dijit.byId('dialogKCLetterOne').hide();" iconclass="cancelButton">取消
			</button>
		</div>
    </form>    
</div>
<div jsId="basicStandbyOneLetter" dojoType="dojox.widget.Standby" target="dialogKCLetterOne"></div>
<!--多筆通知dialog-->
<div id ="dialogKCLetterMulti" dojoType="dijit.Dialog" title="寄發預警輔導通知" style="width: 400px;"> 

    <form  method="post" id="uploadedfileformMulti" enctype="multipart/form-data" dojoType="dijit.form.Form" >
		<input type="hidden" id="warningIds" value="">
		<input type="hidden" id="warningStudentIds" value="">
		<input type="hidden" id="warningStudentNameHiddens" value="">
        <input type="hidden" id="cancelIds" value="">
		<table style="width: auto; " > 
			<input id="noticeNameMulti" type="hidden" >
			<tr>
				<th width= "60px">主旨</th>
				<td style="width: 300px;"><input id="letterSubjectMulti" dojoType="dijit.form.ValidationTextBox" style="width: 300px;" required></td>

			</tr>
			<tr>
				<th>對象</th>
				<td><div id="warningStudentNames" style=" height:auto;max-height: 54px;overflow-y: auto;" ></div></td>

			</tr>
			<tr>
				<th>內容</th>
				<td><input id="letterMessageMulti" dojoType="dijit.form.SimpleTextarea" style="height: 100px; width: 300px;max-width: 300px; max-height: 300px"  required></td>

			</tr>
			<tr>
				<th>簽名檔</th>
				<td><input id="letterSignNameMulti" dojoType="dijit.form.SimpleTextarea" value=""  style="height: 100px; width: 300px;max-width: 300px; max-height: 300px" required></td>
			</tr>



		</table>

		<div class="dijitDialogPaneActionBar">
			<button style="text-align: left;" id="sentMailBtnMulti" type="button" dojoType="dijit.form.Button" onClick=" kcjs.sentLetterMulti();" iconclass="mailButton">寄發
			</button >
			<button type="button" dojoType="dijit.form.Button" onClick=" dijit.byId('dialogKCLetterMulti').hide();" iconclass="cancelButton">取消
			</button>
		</div>
    </form>    
</div>
<div jsId="basicStandbyMultiLetter" dojoType="dojox.widget.Standby" target="dialogKCLetterMulti"></div>



<!--提示 -->
<div data-dojo-type="dijit.Dialog" id="msgDialog" data-dojo-props="title:'提示'" style="width:auto;max-width:450px;min-width:180px;">
    
            <table style="max-width: 450px; min-width: 180px; " >
        <tr>
            <th style="vertical-align: middle; margin-left: 20px; "></th>
            <td>
                <img style="vertical-align: middle; text-align: center; margin-right: 5px;" src="./modules/kuas_consultation_warning/admin/tmpl/images/warning.png">
            </td>
            <td>
                <span  id="msgContent" ></span>
            </td>
        </tr>
    </table>
    <div class="dijitDialogPaneActionBar">
		<button type="button" data-dojo-type="dijit.form.Button" iconclass="cancelButton" onClick="dijit.byId('msgDialog').hide();"> 關閉</button>
    </div>
</div>
<!--匯出提示 -->
<div data-dojo-type="dijit.Dialog" id="exportDialog" data-dojo-props="title:'提示'" style="width:auto;max-width:450px;min-width:180px;" onHide="kcjs.closeDialog();">
      <table style="max-width: 450px; min-width: 180px; ">
        <tr>
            <th style="vertical-align: middle; margin-left: 20px; "></th>
            <td>
                <img style="vertical-align: middle; text-align: center; margin-right: 5px;" src="./modules/kuas_consultation_warning/admin/tmpl/images/warning.png">
            </td>
            <td>
                <span  id="exportContent" ></span>
            </td>
        </tr>
    </table>
    <div class="dijitDialogPaneActionBar">
		<button type="button" data-dojo-type="dijit.form.Button"  iconclass="okButton" onClick="kcjs.exportWarningGuidance();"> 輸出</button>
        <button type="button" data-dojo-type="dijit.form.Button" iconclass="cancelButton" onClick="dijit.byId('exportDialog').hide();"> 關閉</button>
 </div>
</div>
<!--loading -->
<div data-dojo-type="dijit.Dialog" id="loadingDialog" data-dojo-props="title:'提示'" style="width:auto;max-width:450px;min-width:180px;" >
    <div style="vertical-align: middle;margin-right: 5px;">
		<table><td><img style="vertical-align: middle;text-align: center;" src="./modules/kuas_consultation_warning/admin/tmpl/images/bar_circle.gif" style="margin-right: 5px;"></td>
			<td><span  id="loadingContent" ></span></td></table>
    </div>
</div>



