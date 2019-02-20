<link rel="stylesheet" type="text/css" href="./modules/kuas_consultation_common/admin/css/KCCommonCSS.css" />
<script src="./modules/kuas_consultation_warning/admin/script/KCWarningJS.js">
</script>
<script>
	var kcjs = new KCWarningJS();
	dojo.addOnLoad(
			function() {
				personalGuidanceStandby.show();
				kcjs.getOptionView();
			}
	);
</script>
<input type="hidden" id="KCWarningChangeOptionPage" value="<{$KCWarningChangeOptionPage}>">
<input type="hidden" id="KCWarningInsertWarningGuidanceAction" value="<{$KCWarningInsertWarningGuidanceAction}>">
<input type="hidden" id="kcunitId" value="<{$reportStudentUnitId}>">
<input type="hidden" id="kcstudentUseId" value="<{$reportStudentId}>">
<input type="hidden" id="kcteacherUserId" value="<{$reportTeacherId}>">
<input type="hidden" id="kcguidanceType" value="personal"/>
<input type="hidden" id="reportType" value="insert"/>
<input type="hidden" id="optionIdString" value="<{$optionIdString}>">
<input type="hidden" id="kcguidanceId" value="0">
<div jsId="personalGuidanceStandby" dojoType="dojox.widget.Standby" target="personalGuidance"></div>
<form dojoType="dijit.form.Form" id="personalGuidanceInsertForm">
	<div id="personalGuidance" style="width:100%; margin: 0 auto; ">
		<div style="width: 850px; margin: 0 auto; ">
			<center>
				<h1>新增輔導紀錄</h1>
			</center>
			<div style="font-size: 16px; text-align: center; ">
				<h2>
					學習成績期中預警
				</h2>
				<h3>
					學生學習輔導記錄表
				</h3>
			</div>

            <table cellpadding="3px" class="KCTable" style="width: 100%;  "> 
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
                    <td >
						<{$reportStudentClass}>
                    </td> 
                </tr>
                <tr>
                    <th>輔導對象</th>
                    <td >
                        <{$studentData->kcstudent_user_id}>&nbsp;-&nbsp;<{$studentData->kcstudent_name}>
                    </td>
                    <th>學生性質</th>
                    <td >
                        <{$studentData->kcstudent_character}>
                    </td>
                    <th>性別</th>
                    <td >
                        <{$studentSex}>
                    </td>
                </tr>     
                <{if $warning.type}>    
                <tr>
                    <th>預警型態</th>
                    <td colspan="5" >
                        <span><{$warning.type}></span>
                    </td>					
                </tr>
                <{/if}>
                <{if $warning.poorSchoolSubject}>   
				<tr>
                    <th>上學期課業不佳科目</th>
                    <td colspan="5" >
                        <span><{$warning.poorSchoolSubject}></span>
                    </td>					
                </tr>
                <{/if}>
                <{if $warning.warningSubject}>   
				<tr>
                    <th>本學期期中預警科目</th>
                    <td colspan="5" >
                        <span><{$warning.warningSubject}></span>
                    </td>					
                </tr>
                <{/if}>
            </table>
			<br>
			<table cellpadding="3px" class="KCTable" style="width: 100%;" >
				<tr>
					<th colspan="2" class="tableTitle">輔導內容</th>
				</tr>
				<tr>
					<th style="width: 130px;">
						輔導主題&nbsp;<span style="color:red; " >(*)</span>
					</th>
					<td >
						學習成績期中預警
					</td>
				</tr>
				<tr>
					<th>
						輔導教師    
					</th>
					<td>
						<{$reportTeacherName}>
					</td>
				</tr>
				<tr>
					<th>
						地點&nbsp;<span style="color:red; " >(*)</span>
					</th>
					<td>
						教師研究室
					</td>
				</tr>
				<tr>
					<th>
						開始日期&nbsp;<span style="color:red; " >(*)</span>
					</th>
					<td>
						<span id="kcguidanceStartDate" dojoType="dijit.form.DateTextBox" value="<{$preDate}>" style="width: 110px; " onchange="kcjs.changeTotalHours();"></span>
						<input type="text" id="kcguidanceStartTime" value="T<{$startClass}>" dojoType="dijit.form.TimeTextBox" style="width: 100px; " onchange="kcjs.changeTotalHours();" >
					</td>
				</tr>
				<tr>		
					<th>
						結束日期&nbsp;<span style="color:red; " >(*)</span>
					</th>
					<td>
						<span id="kcguidanceEndDate" dojoType="dijit.form.DateTextBox" value="<{$preDate}>" style="width: 110px; " onchange="kcjs.changeTotalHours();"></span>
						<input type="text" id="kcguidanceEndTime" value="T<{$endClass}>" dojoType="dijit.form.TimeTextBox"  style="width: 100px; " onchange="kcjs.changeTotalHours();">
						<b>&nbsp;輔導總時間&nbsp; </b>							
						<input type="text" id="kcguidanceTotalHours" dojoType="dijit.form.NumberSpinner" value="<{$totalTime}>"  smallDelta="1"  constraints="{min:0}" onclick="kcjs.changeTotalTime();" onchange="kcjs.changeTotalTime();" style="width:50px; ">&nbsp;分
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
								<ol id="ol<{$option.id}>" style="margin-left: -40px; margin-top: 10px; margin-bottom: 10px; "></ol>
							</td>
				</tr>
				<{/foreach}>
				<tr>
					<th>
						文件等級&nbsp;<span style="color:red; " >(*)</span>
					</th>
					<td>
						<select id="kcguidanceFileLevel" dojoType="dijit.form.Select">
							<option value="normal">一般</option>
							<option value="confidential">機密</option>
						</select>
					</td>
				</tr>			
				<tr>
					<th style="width:100px; text-align: center;">公開評論<br>(給學生的話)</th>
					<td>
						<span>
							<textarea id="kcgpersonalPublicComment"dojoType="dijit.form.SimpleTextarea" style="resize: none; width:98%; height:100px; "></textarea>
						</span>
					</td>
				</tr>
				<!-- 此功能在第二階段需求暫無使用，為確保以後使用，暫時先隱藏-->
				<tr style="display: none; ">
					<td>
						<fieldset>
							<legend>
								私人評論(不開放給學生檢視)
							</legend>
							<span>
								<textarea id="kcgpersonalPrivateComment"dojoType="dijit.form.SimpleTextarea" style="resize: none; width:99%; height:100px; "></textarea>
							</span>
						</fieldset>
					</td>
				</tr>
			</table>
			</form>   
			<br>
			<div align="center">
				<button type="button" id="insertBtn" dojoType="dijit.form.Button" onclick="kcjs.insertWarningGuidance();">
					<img src="./modules/kuas_consultation/admin/images/ok.png">新增</button>
				<button type="button" iconclass="cancelButton" dojoType="dijit.form.Button" onclick="kcjs.back();">取消</button>
			</div>
            <!--提示 -->
			<div data-dojo-type="dijit.Dialog" id="msgDialog" data-dojo-props="title:'訊息'" style="width:auto;max-width:450px;min-width:180px;">
				<div style="vertical-align: middle;margin-left: 20px;">
					<table><td><img style="vertical-align: middle;text-align: center;" src="./modules/kuas_consultation_warning/admin/tmpl/images/warning.png" style="margin-right: 5px;"></td>
						<td><span  id="msgContent" ></span></td></table>
				</div>
				<div class="dijitDialogPaneActionBar">
					<button type="button" data-dojo-type="dijit.form.Button" iconclass="cancelButton" onClick="dijit.byId('msgDialog').hide();"> 關閉</button>
				</div>
			</div>