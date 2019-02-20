<link rel="stylesheet" type="text/css" href="./modules/kuas_consultation_common/admin/css/KCCommonCSS.css" />
<script src="./modules/kuas_consultation_warning/admin/script/KCWarningJS.js">
    </script>
<script>
    var kcjs = new KCWarningJS();
    dojo.addOnLoad(function(){
    kcjs.getOptionAction();
});
</script>
<input type="hidden" id="optionArray" value="<{$optionArray}>">
<input type="hidden" id="guidanceId" value="<{$guidanceId}>">
<input type="hidden" id="getOptionUrl" value="<{$getOptionActionUrl}>">
<input type="hidden" id="reservationId" value="<{$reservationId}>">
<input type="hidden" id="userId" value="<{$studentId}>">
<form action="<{$showStudentStatusPageUrl}>" method="post" name="guidancePageForm" style="display:none">
    <input type="text" name="guidanceStudentId" id="guidanceStudentId" value="<{$studentId}>">
</form>
<div style="margin:0 auto; width:850px ">
    <span style="text-align:center;"><h1><{$topic}></h1></span>
    <span style="text-align:center;"><h2>學生學習輔導紀錄表</h2></span>
    <table class="KCTable" cellpadding="5px" >
        <tr >
            <th colspan="6" class="tableTitle">學生資料</th> 
        </tr>
            <tr>
                <th span style="width:140px;">學院</th><td style="width:180px"><{$getGuidanceData->college}></td>
                <th span style="width:130px;">系所</th><td style="width:150px"><{$getGuidanceData->unit}></td>
                <th span style="width:130px;">班級</th><td style="width:150px"><{$getGuidanceData->class}></td>
            </tr>
            <tr>
                <th>輔導對象</th>
                <td>
                    <{$studentId}> &nbsp;-&nbsp; <{$getGuidanceData->kcstudent_name}>
                </td>
                <th>學生性質</th>
                <td>
                    <{$getGuidanceData->studentCharacter}>
                </td>
                <th>性別</th>
                <td>
                    <{if $getGuidanceData->studentSex == "male"}>
                        男
                    <{/if}>
                     <{if $getGuidanceData->studentSex == "female"}>
                        女
                    <{/if}>
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
                    <th >上學期課業不佳科目</th>
                    <td colspan="5" >
                        <span><{$warning.poorSchoolSubject}></span>
                    </td>					
                </tr>
                <{/if}>
                <{if $warning.warningSubject}>   
				<tr>
                    <th >本學期期中預警科目</th>
                    <td colspan="5" >
                        <span><{$warning.warningSubject}></span>
                    </td>					
                </tr>
                <{/if}>
        </table>
            <br>
                <table class="KCTable" cellpadding="3px" >
        <tr >
            <th colspan="2" class="tableTitle">輔導內容</th>
        </tr>
        
            <tr>
                <th style="width:130px;">輔導主題</th>
                <td>
                    <{$getGuidanceData->kcguidance_topic}>
                </td>
            </tr>
             <tr>
                <th>輔導老師</th>
                <td>
                    <{$teacherData->kcemployee_name}>
                </td>
	    </tr>
            <tr>
                <th>地點</th>
                <td>
                    <{$getGuidanceData->kcguidance_address}>
                </td>
            </tr>
            <tr>
                <th>開始時間</th>
                <td>
                    <{$getGuidanceData->kcguidance_start_time}>
                </td>
            </tr>
            <tr>
                <th>結束時間</th>
                <td>
                    <{$getGuidanceData->kcguidance_end_time}>
                </td>
            </tr>
            <tr>
                <th>輔導總時間</th>
                <td>
                    <{$getGuidanceData->kcguidance_total_hours}> 分鐘
                </td>
            </tr>
            <{foreach item=item  from=$optionList}>
            <tr>
                <th>
                    <div><{$item->kcgoption_text}></div>
                </th>
            <td id="option<{$item->kcgoption_id}>" style="word-break:break-all;">
                <ol style="margin-left: -40px; margin-top: 10px; margin-bottom: 10px;" id="ol<{$item->kcgoption_id}>"></ol>
                <!-- 內部js產生畫面-->
            </td>
            </tr>
            <{/foreach}>
            <tr>
                <th>文件等級</th>
                <td>
                    <{if $getGuidanceData->kcguidance_file_level=="normal"}>
                    一般
                    <{/if}>   
                    <{if $getGuidanceData->kcguidance_file_level=="confidential"}>
                    機密
                    <{/if}>   
                </td>
            </tr>
            <tr>
                <th>公開評論<br>( 給學生的話 )</th>
                <td >
                    <div style=" word-wrap: break-word; word-break: normal; height:auto;max-height:77px;overflow:auto">
                        <{if $perconalGuidance->kcgpersonal_public_comment==""}>
                        無
                        <{else}>    
                        <{$perconalGuidance->kcgpersonal_public_comment}>
                        <{/if}>
                    </div>
                </td>
            </tr>
            <tr style="display: none;">
                <th>私人評論<br>( 不開放給學生檢視 )</th>
                <td style="height:100px">
                    <{if $perconalGuidance->kcgpersonal_private_comment==""}>
                    無
                    <{else}>    
                    <{$perconalGuidance->kcgpersonal_private_comment}>
                    <{/if}>
                </td>
            </tr>
        </table>
     </br>
    <div style="text-align: center;">
        <button type="button" iconclass="backButton" dojoType="dijit.form.Button" onClick="kcjs.back()">返回</button>
    </div>
</div>