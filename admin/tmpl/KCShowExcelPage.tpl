<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
	  xmlns:o="urn:schemas-microsoft-com:office:office"
	  xmlns:x="urn:schemas-microsoft-com:office:excel"
	  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
	  xmlns:html="http://www.w3.org/TR/REC-html40">
    <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
    </DocumentProperties>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
    </ExcelWorkbook>
    <Worksheet ss:Name="預警資料">
	<Table>


	    <Row ss:AutoFitHeight="0">
		<Cell><Data ss:Type="String">學年度</Data></Cell>
		<Cell><Data ss:Type="String">學期</Data></Cell>
		<Cell><Data ss:Type="String">系所</Data></Cell>
		<Cell><Data ss:Type="String">班級</Data></Cell>
		<Cell><Data ss:Type="String">學號</Data></Cell>
		<Cell><Data ss:Type="String">學生名稱</Data></Cell>
		<Cell><Data ss:Type="String">導師</Data></Cell>
		<Cell><Data ss:Type="String">上學期課業不佳 - 1/2 預警</Data></Cell>
		<Cell><Data ss:Type="String">上學期課業不佳 - 2/3 預警</Data></Cell>
		<Cell><Data ss:Type="String">上學期不及格科目</Data></Cell>
		<Cell><Data ss:Type="String">本學期期中預警 - 1/2 預警 </Data></Cell>
		<Cell><Data ss:Type="String">本學期期中預警 - 2/3 預警 </Data></Cell>
		<Cell><Data ss:Type="String">本學期期中預警科目</Data></Cell>
		<Cell><Data ss:Type="String">通知次數</Data></Cell>
		<Cell><Data ss:Type="String">輔導狀況</Data></Cell>
	    </Row>
	    <{foreach item=result from=$results}>
	    <Row ss:AutoFitHeight="0">
		<Cell><Data ss:Type="String"><{$result.kcwarning_sem_year}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_sem_term_seesaw}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcunit_name}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcclass_name}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcstudent_user_id}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcstudent_name}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcteacher_name}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_half_poor_schoolwork}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_three_quarters_poor_schoolwork}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_poor_schoolwork_subject}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_half}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_three_quarters}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_subject}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_advice_times}></Data></Cell>
		<Cell><Data ss:Type="String"><{$result.kcwarning_have_counseling}></Data></Cell>
	    </Row>
	    <{/foreach}>


	</Table>
	<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
	</WorksheetOptions>
    </Worksheet>
</Workbook>