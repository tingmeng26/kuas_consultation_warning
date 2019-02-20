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
<div style="margin:0 auto; width:750px ">
    <span style="text-align:center;"><h1><{$topic}></h1></span>
    <span style="text-align:center;"><h2>學生學習輔導紀錄表</h2></span>
    <fieldset>
        <legend>
            學生資料
        </legend>
        <table class="styleTable" cellpadding="5px" style="text-align: left;">
            <tr>
                <th span style="font-weight:bold; width:60px; text-align: left;">學院</th><td style="width:200px"><{$getGuidanceData->college}></td>
                <th span style="font-weight:bold; width:60px; text-align: left;">系所</th><td style="width:150px"><{$getGuidanceData->unit}></td>
                <th span style="font-weight:bold; width:60px; text-align: left;">班級</th><td style="width:150px"><{$getGuidanceData->class}></td>
            </tr>
            <tr>
                <th span style="font-weight:bold; text-align: left;">輔導對象</th>
                <td>
                    <{$studentId}> &nbsp;-&nbsp; <{$getGuidanceData->kcstudent_name}>
                </td>
                <th span style="font-weight:bold; text-align: left;">學生性質</th>
                <td>
                    <{$getGuidanceData->studentCharacter}>
                </td>
                <th span style="font-weight:bold; text-align: left;">性別</th>
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
                    <th class="personalGuidance_th">預警型態</th>
                    <td colspan="5" class="personalGuidance_td">
                        <span><{$warning.type}></span>
                    </td>					
                </tr>
                <{/if}>
                <{if $warning.poorSchoolSubject}>   
				<tr>
                    <th class="personalGuidance_th">上學期不及格科目</th>
                    <td colspan="5" class="personalGuidance_td">
                        <span><{$warning.poorSchoolSubject}></span>
                    </td>					
                </tr>
                <{/if}>
                <{if $warning.warningSubject}>   
				<tr>
                    <th class="personalGuidance_th">本學期期中預警科目</th>
                    <td colspan="5" class="personalGuidance_td">
                        <span><{$warning.warningSubject}></span>
                    </td>					
                </tr>
                <{/if}>
        </table>
    </fieldset>
            <br>
    <fieldset>
        <legend>
            輔導內容
        </legend>
        <table class="styleTable" cellpadding="3px" style="text-align: left;">
            <tr>
                <th style="width:100px; text-align: left;">輔導主題</th>
                <td>
                    <{$getGuidanceData->kcguidance_topic}>
                </td>
            </tr>
            <tr>
                <th style="text-align: left;">地點</th>
                <td>
                    <{$getGuidanceData->kcguidance_address}>
                </td>
            </tr>
            <tr>
                <th style="text-align: left;">開始時間</th>
                <td>
                    <{$getGuidanceData->kcguidance_start_time}>
                </td>
            </tr>
            <tr>
                <th style="text-align: left;">結束時間</th>
                <td>
                    <{$getGuidanceData->kcguidance_end_time}>
                </td>
            </tr>
            <tr>
                <th style="text-align: left;">輔導總時間</th>
                <td>
                    <{$getGuidanceData->kcguidance_total_hours}> 分鐘
                </td>
            </tr>
            <{foreach item=item  from=$optionList}>
            <tr>
                <th style="text-align: left;">
                    <div><{$item->kcgoption_text}></div>
                </th>
            <td  id="option<{$item->kcgoption_id}>">
                <ol style="margin-left:-40px" id="ol<{$item->kcgoption_id}>"></ol>
                <!-- 內部js產生畫面-->
            </td>
            </tr>
            <{/foreach}>
            <tr>
                <th style="text-align: left;">文件等級</th>
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
                <th style="text-align: left;">公開評論<br>( 給學生的話 )</th>
                <td>
                    <div style="height:100px; overflow: auto;">
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
    </fieldset>
     </br>
    <div style="text-align:right; font-weight: bold;">
        主任簽章 &nbsp;&nbsp; __________________________
    </div>
</div>