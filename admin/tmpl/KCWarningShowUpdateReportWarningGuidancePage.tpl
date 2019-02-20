<script src="./modules/kuas_consultation_warning/admin/script/KCWarningJS.js"></script>
<link rel="stylesheet" type="text/css" href="./modules/kuas_consultation_common/admin/css/KCCommonCSS.css" />
<script>
	var kcjs = new KCWarningJS();
	dojo.addOnLoad(
			function() {
				personalGuidanceStandby.show();
				kcjs.getOptionUpdateView();
			}
	);
</script>

<div jsId="personalGuidanceStandby" dojoType="dojox.widget.Standby" target="personalGuidance"></div>
<input type="hidden" id="kcguidanceId" value="<{$guidanceRow->kcguidance_id}>">
<input type="hidden" id="kcgpersonalId" value="<{$guidancePersonalRow->kcgpersonal_id}>">
<input type="hidden" id="kcunitId" value="<{$reportStudentUnitId}>">
<input type="hidden" id="kcstudentUseId" value="<{$reportStudentId}>">
<input type="hidden" id="kcteacherUserId" value="<{$reportTeacherId}>">
<input type="hidden" id="kcguidanceType" value="personal"/>
<input type="hidden" id="reportType" value="update"/>
<input type="hidden" id="reportYear" value="<{$reportYear}>"/>
<input type="hidden" id="reportTerm" value="<{$reportTerm}>"/>
<input type="hidden" id="KCWarningUpdateWarningGuidanceAction" value="<{$KCWarningUpdateWarningGuidanceAction}>">
<input type="hidden" id="KCWarningUpdateChangeOptionPage" value="<{$KCWarningUpdateChangeOptionPage}>">
<input type="hidden" id="optionIdString" value="<{$optionIdString}>">


<div id="personalGuidance" style="width:100%; margin: 0 auto; ">
    <form dojoType="dijit.form.Form" id="personalGuidanceUpdateForm">
        <center>
            <h1>修改輔導紀錄</h1>
        </center>
        <div style="width: 850px; margin: 0 auto; ">
            <div style="font-size: 16px; text-align: center; ">
                <h2 >
                    <{$consultationData->kcconsultation_topic}>
                </h2>
                <h3>
                    <{$mean}>
                </h3>
            </div>
			<table class="KCTable" cellpadding="3px" style="width: 100%;  "> 
				<tr>
					<th colspan="6" class="tableTitle">學生資料</th> 
				</tr>
				<tr>
					<th style="width: 130px;">學院</th>
					<td style="width: 180px;">
						<{$college}>
					</td>               
					<th style="width: 130px;">系所</th>
					<td>
						<{$department}>
					</td>
					<th style="width: 130px;">班級</th>
					<td>
						<{if $reservationRow->kcclass_name == ''}>-<{else}><{$reservationRow->kcclass_name}><{/if}>
					</td> 
				</tr>
				<tr>
					<th>輔導對象</th>
					<td>
						<{$studentData->kcstudent_user_id}>&nbsp;-&nbsp;<{$studentData->kcstudent_name}>
					</td>
					<th>學生性質</th>
					<td>
						<{$studentData->kcstudent_character}>
					</td>
					<th>性別</th>
					<td>
						<{$studentSex}>
					</td>
				</tr>
				<{if $warning.type}>    
                <tr>
                    <th>預警型態</th>
                    <td colspan="5" class="personalGuidance_td">
                        <span><{$warning.type}></span>
                    </td>					
                </tr>
                <{/if}>
                <{if $warning.poorSchoolSubject}>   
				<tr>
                    <th style="text-align: center;">上學期課業不佳科目</th>
                    <td colspan="5" class="personalGuidance_td">
                        <span><{$warning.poorSchoolSubject}></span>
                    </td>					
                </tr>
                <{/if}>
				<{if $warning.warningSubject}>   
				<tr>
                    <th>本學期期中預警科目</th>
                    <td colspan="5" class="personalGuidance_td">
                        <span><{$warning.warningSubject}></span>
                    </td>					
                </tr>
                <{/if}>
			</table>
            <br>
			<table class="KCTable" cellpadding="3px" style="width: 100%; ">
				<tr>
					<th colspan="2" class="tableTitle">輔導內容</th>
				</tr>

				<tr>
					<th style="width: 130px;">輔導主題&nbsp;<span style="color:red">(*)</span></th>
					<td id="kcguidanceTopic"><{$guidanceRow->kcguidance_topic}></td>
				</tr>
				<tr>
					<th>輔導教師</th>
					<td><{$employeeProFileData->kcemployee_name}></td>
				</tr>
				<tr>
					<th>地點&nbsp;<span style="color:red; " >(*)</span></th>
					<td id="kcguidanceAddress"><{$guidanceRow->kcguidance_address}></td>
				</tr>
				<tr>
					<th>開始日期&nbsp;<span style="color:red; " >(*)</span></th>
					<td>
						<span id="kcguidanceStartDate" dojoType="dijit.form.DateTextBox" value="<{$startDate}>" style="width: 110px; " onchange="kcjs.changeTotalHours();"></span>
						<input type="text" id="kcguidanceStartTime" value="T<{$startClass}>" dojoType="dijit.form.TimeTextBox" style="width: 100px; " onchange="kcjs.changeTotalHours();" >
					</td>
				</tr>
				<tr>
					<th>結束日期&nbsp;<span style="color:red; " >(*)</span></th>
					<td>
						<span id="kcguidanceEndDate" dojoType="dijit.form.DateTextBox" value="<{$endDate}>" style="width: 110px; " onchange="kcjs.changeTotalHours();"></span>
						<input type="text" id="kcguidanceEndTime" value="T<{$endClass}>" dojoType="dijit.form.TimeTextBox"  style="width: 100px; " onchange="kcjs.changeTotalHours();">
						<b>&nbsp;輔導總時間&nbsp; </b>							
						<input type="text" id="kcguidanceTotalHours" dojoType="dijit.form.NumberSpinner" value="<{$guidanceRow->kcguidance_total_hours}>"  smallDelta="1"  constraints="{min:0}" onclick="kcjs.changeTotalTime();" onchange="kcjs.changeTotalTime();" style="width:50px">&nbsp;分
					</td>
				</tr>
				<{foreach item=option from=$item}>
				<tr>
					<th><{$option.name}>&nbsp;<span style="color:red; " >(*)
							<input name="optionName<{$option.queId}>" dojoType="dijit.form.CheckBox"  id="optionId<{$option.id}>" checked style="display:none; ">
							<input type="hidden" id="limit<{$option.id}>" value="<{$option.limit}>" >
							<input type="hidden" id="name<{$option.id}>" value="<{$option.name}>" >
							<input type="hidden" name="queId"  id="queId<{$option.id}>" value="<{$option.id}>">
							</th>
							<td>
								<ol id="ol<{$option.id}>" style="margin-left: -40px; margin-top: 10px; margin-bottom: 10px;">
								</ol>
							</td>
				</tr>
				<{/foreach}>                
				<tr>
					<th>文件等級&nbsp;<span style="color:red" >(*)</span></th>
					<td>
						<select id="kcguidanceFileLevel" dojoType="dijit.form.Select" value="<{$guidanceRow->kcguidance_file_level}>">
							<option value="normal">一般</option>
							<option value="confidential">機密</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>公開評論<br>(給學生的話)</th>
					<td>                            
						<span style="word-break:break-all;">
							<textarea id="kcgpersonalPublicComment"dojoType="dijit.form.SimpleTextarea" style="resize: none; width:98%;height:77px;max-height:77px;overflow:auto;"><{$guidancePersonalRow->kcgpersonal_public_comment}></textarea>
						</span>
					</td>
				</tr>
				<!-- 此功能在第二階段需求暫無使用，為確保以後使用，暫時先隱藏-->
				<tr style="display:none; ">
					<td>
						<fieldset>
							<legend>
								私人評論(不開放給學生檢視)
							</legend>
							<span>
								<textarea id="kcgpersonalPrivateComment"dojoType="dijit.form.SimpleTextarea" style="resize: none; width:98%;height:100px;"><{$guidancePersonalRow->kcgpersonal_private_comment}></textarea>
							</span>
						</fieldset>
					</td>
				</tr>                
			</table>
            <br>
            <div align="center">
                <button type="button" id="updateBtn" iconclass="editButton" dojoType="dijit.form.Button" onclick="kcjs.updateWarningGuidance();">修改</button>                   
                <button type="button"  iconclass="cancelButton" dojoType="dijit.form.Button" onclick="kcjs.back();">取消</button>
            </div>
        </div>
    </form> 
</div>
<div dojoType="dijit.Dialog" id="msgDialog" data-dojo-props="title:'訊息'" style="width:auto;max-width:450px;min-width:180px;" onHide="kCConsultation.closeDialog('msgContent');">
    <table style="max-width: 450px; min-width: 200px; ">
        <tr>
            <th style="vertical-align: middle; margin-left: 20px; "></th>
            <td>
                <img style="vertical-align: middle; text-align: center; margin-right: 5px;" src="./modules/kuas_consultation/admin/images/warning.png">
            </td>
            <td>
                <span  id="msgContent" ></span>
            </td>
        </tr>
    </table>
    <div class="dijitDialogPaneActionBar">
        <button type="button" dojoType="dijit.form.Button" iconclass="cancelButton" onClick="dijit.byId('msgDialog').hide();" >關閉</button>
    </div>
</div>