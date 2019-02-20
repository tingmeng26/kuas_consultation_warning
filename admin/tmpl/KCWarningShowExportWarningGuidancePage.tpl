<link rel="stylesheet" type="text/css" href="./modules/kuas_consultation_common/admin/css/KCCommonCSS.css" />
<div style="margin:0 auto; width:750px " id="block">
    <div style="text-align:center">
        <h1><{$topic}></h1>
    </div>
    <div style="text-align:center;">
        <h2>學生學習輔導紀錄表</h2>
    </div>
    <table class="KCTable "cellpadding="1" cellspacing="0">
        <tr>
            <th colspan="6" class="tableTitle">學生資料</th>
        </tr>
        <tr>
            <th style=" width:140px; text-align: center;font-weight:bold;">學院</th><td style="width:150px"><{$getGuidanceData->college}></td>
            <th style=" width:70px; text-align: center;font-weight:bold;">系所</th><td style="width:100px"><{$getGuidanceData->unit}></td>
            <th style=" width:70px; text-align: center;font-weight:bold;">班級</th><td style="width:auto"><{$getGuidanceData->class}></td>
        </tr>
        <tr>
            <th style=" text-align: center;font-weight:bold;">輔導對象</th>
            <td><{$studentId}> &nbsp;-&nbsp; <{$getGuidanceData->kcstudent_name}>
            </td>
            <th style=" text-align: center;font-weight:bold;">學生性質</th>
            <td><{$getGuidanceData->studentCharacter}>
            </td>
            <th style=" text-align: center;font-weight:bold;">性別</th>
            <td><{if $getGuidanceData->studentSex == "male"}>男<{/if}><{if $getGuidanceData->studentSex == "female"}>女<{/if}>
            </td>
        </tr>
        <{if $warning.type}>    
                <tr>
                    <th  style="font-weight:bold;text-align: center;">預警型態</th>
                    <td colspan="5" style="width:auto;"><span><{$warning.type}></span></td>					
                </tr>
                <{/if}>
                <{if $warning.poorSchoolSubject}>   
				<tr>
                    <th  style="font-weight:bold;text-align: center;">上學期課業不佳科目</th>
                    <td colspan="5"  style="width:auto;"><span><{$warning.poorSchoolSubject}></span>
                    </td>					
                </tr>
                <{/if}>
                <{if $warning.warningSubject}>   
				<tr>
                    <th  style="font-weight:bold;text-align: center;">本學期期中預警科目</th>
                    <td colspan="5"  style="width:auto;"><span><{$warning.warningSubject}></span>
                    </td>					
                </tr>
                <{/if}>
    </table>
    <br/>
    
    <table class="KCTable" cellpadding="2" cellspacing="0" >
        <tr>
            <th class="tableTitle" >輔導內容</th>
        </tr>
        <tr>
            <th style="width:140px; text-align: center;font-weight:bold;">輔導主題</th>
            <td><{$getGuidanceData->kcguidance_topic}>
            </td>
        </tr>
        <tr>
		<th style="width:140px; text-align: center;font-weight:bold;">輔導老師</th>
		<td><{$teacherData->kcemployee_name}></td>
	    </tr>
        <tr>
            <th style="text-align: center;font-weight:bold;">地點</th>
            <td><{$getGuidanceData->kcguidance_address}></td>
        </tr>
        <tr>
            <th style="text-align:center;font-weight:bold;">開始時間</th>
            <td><{$getGuidanceData->kcguidance_start_time}></td>
        </tr>
        <tr>
            <th style="text-align: center;font-weight:bold;">結束時間</th>
            <td><{$getGuidanceData->kcguidance_end_time}></td>
        </tr>
        <tr>
            <th style="text-align: center;font-weight:bold;">輔導總時間</th>
            <td><{$getGuidanceData->kcguidance_total_hours}> 分鐘</td>
        </tr>
        <{foreach item=item  from=$optionList}>
        <tr>
            <th style="text-align: center;font-weight:bold;"><{$item.text}></th>
            <td style="word-break:break-all;height:auto"  id="option<{$item->kcgoption_id}>"><{$item.desc}></td>
        </tr>
        <{/foreach}>
        <tr>
            <th style="text-align: center;font-weight:bold;">文件等級</th>
            <td><{if $getGuidanceData->kcguidance_file_level=="normal"}>一般<{/if}><{if $getGuidanceData->kcguidance_file_level=="confidential"}>機密<{/if}>   
            </td>
        </tr>
        <tr>
            <th style="text-align: center;font-weight:bold;">公開評論<br>( 給學生的話 )</th>
            <td ><{if $perconalGuidance->kcgpersonal_public_comment==""}>無<{else}><{$perconalGuidance->kcgpersonal_public_comment}>
                <{/if}>
            </td>
        </tr>
    </table>
    <br/>
     <br/>
      <br/>
    <div style="text-align:right; font-weight: bold;font-size:36px">
        主任簽章 &nbsp;&nbsp; __________________________
    </div>
</div>