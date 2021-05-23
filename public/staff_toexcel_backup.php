<?
//已更新
include "../basic/chksession.php";
include "../basic/parameter.inc";
/*
$gResult = mysql_query("SELECT GroupName FROM `$DataIn`.`staffgroup`  WHERE  GroupId=$GroupId",$link_id);
		if($gRow = mysql_fetch_array($gResult)){
			$GroupName=$gRow["GroupName"];
		}
else{
	$GroupName="研砼公司";
}
*/
$GroupName="研砼公司";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $GroupName ."_员工信息表.xls"); 

$SortRows=" ORDER BY M.Estate DESC,M.BranchId,G.GroupName,M.JobId,M.ComeIn,M.Number";
if ($BranchId!="") $SearchRows.=" AND M.BranchId='$BranchId' ";
if ($GroupId!="") $SearchRows.=" AND M.GroupId='$GroupId' ";
if ($JobId!=""){
	if ($JobId=="-1") $SortRows=" ORDER BY M.Estate DESC,M.JobId,M.BranchId,G.GroupName,M.ComeIn,M.Number";
	else $SearchRows.=" AND M.JobId='$JobId' ";
}
$result = mysql_query("SELECT 
	M.Id,M.Number,M.Name,M.Grade,M.Mail,M.ExtNo,M.ComeIn,M.Introducer,M.Estate,M.Locks,M.Date,M.Operator,
	S.Birthday,S.Sex,S.Rpr,S.Idcard,S.Mobile,S.Address,S.Dh,S.InFile,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName,W.Name AS WorkAddName
	FROM $DataPublic.staffmain M
	LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId
	LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
	LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
	LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
   LEFT JOIN $DataPublic.staffworkadd W ON W.Id=M.WorkAdd
	WHERE 1   AND M.Estate=1 $SearchRows $SortRows",$link_id);
$Rows=@mysql_num_rows($result)+10;//行数
$Cols=14;//列数

echo"
<?xml version='1.0'?>
<?mso-application progid='Excel.Sheet'?>
<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet'
 xmlns:o='urn:schemas-microsoft-com:office:office'
 xmlns:x='urn:schemas-microsoft-com:office:excel'
 xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet'
 xmlns:html='http://www.w3.org/TR/REC-html40'>
 <DocumentProperties xmlns='urn:schemas-microsoft-com:office:office'>
  <Author>ewen</Author>
  <LastAuthor>ewen</LastAuthor>
  <Created>2012-09-07T01:05:04Z</Created>
  <Company>middlecloud</Company>
  <Version>11.8122</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns='urn:schemas-microsoft-com:office:excel'>
  <WindowHeight>8670</WindowHeight>
  <WindowWidth>11715</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>90</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID='Default' ss:Name='Normal'>
   <Alignment ss:Vertical='Center'/>
   <Borders/>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID='m29839800'>
   <Alignment ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s21'>
   <Borders/>
   <Font x:Family='Swiss' ss:Size='9'/>
  </Style>
  <Style ss:ID='s22'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center'/>
   <Borders/>
   <Font x:Family='Swiss' ss:Size='9'/>
  </Style>
  <Style ss:ID='s23'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s24'>
   <Alignment ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s25'>
   <Alignment ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
   <NumberFormat ss:Format='Medium Date'/>
  </Style>
  <Style ss:ID='s26'>
   <Alignment ss:Horizontal='Left' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s27'>
   <Alignment ss:Horizontal='Right' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s28'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
   <NumberFormat ss:Format='Short Date'/>
  </Style>
 </Styles>
 <Worksheet ss:Name='$GroupName'>
  <Table ss:ExpandedColumnCount='$Cols' ss:ExpandedRowCount='$Rows' x:FullColumns='1'
   x:FullRows='1' ss:StyleID='s21' ss:DefaultColumnWidth='54'
   ss:DefaultRowHeight='18'>
   ";

 echo"
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='30.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='30.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='60.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='80.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='80.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='100.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='150.75'/>
    <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='300.75'/>
   <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>序号</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>Number</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>姓名</Data></Cell>
     <Cell ss:StyleID='s23'><Data ss:Type='String'>性别</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>籍贯</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>部门</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>小组</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>职位</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>入职时间</Data></Cell>
	 <Cell ss:StyleID='s23'><Data ss:Type='String'>工作地址</Data></Cell>
	 <Cell ss:StyleID='s23'><Data ss:Type='String'>联系电话</Data></Cell>
	 <Cell ss:StyleID='s23'><Data ss:Type='String'>分机号</Data></Cell>
	 <Cell ss:StyleID='s23'><Data ss:Type='String'>身份证号码</Data></Cell>
	  <Cell ss:StyleID='s23'><Data ss:Type='String'>家庭地址</Data></Cell>
   </Row>";
     if($myRow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{
        $Number=$myRow["Number"];
	 	$Branch=$myRow["Branch"];
	 	$GroupName=$myRow["GroupName"];
		$Job=$myRow["Job"];
		$KqSign=$myRow["KqSign"];
		$KqSign=$KqSign==1?"√":" ";
		$Mobile=$myRow["Mobile"]==""?" ":$myRow["Mobile"];
		$Dh=$myRow["Dh"]==""?" ":$myRow["Dh"];
		$ExtNo=$myRow["ExtNo"]==""?" ":$myRow["ExtNo"];
		$ComeIn=$myRow["ComeIn"];
		$Name=$myRow["Name"];
		$Sex=$myRow["Sex"]==1?"男":"女";
		$Rpr=$myRow["Rpr"];
		$Address=$myRow["Address"];
		$Idcard=$myRow["Idcard"]==""?" ":$myRow["Idcard"];
		$rResult = mysql_query("SELECT Name FROM $DataPublic.rprdata WHERE Estate=1 and Id=$Rpr order by Id",$link_id);
		if($rRow = mysql_fetch_array($rResult)){
			$Rpr=$rRow["Name"];
		}
        $WorkAdd=$myRow["WorkAddName"];
		//include "../model/subselect/WorkAdd.php";
	   echo"
	   <Row ss:AutoFitHeight='0'>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$i</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='Number'>$Number</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Name</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Sex</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Rpr</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Branch</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$GroupName</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Job</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$ComeIn</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$WorkAdd</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Mobile</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$ExtNo</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Idcard</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$Address</Data></Cell>
	     </Row>";
		$i++; 
		}while ($myRow = mysql_fetch_array($result));
	}

   echo"  </Table>
  <WorksheetOptions xmlns='urn:schemas-microsoft-com:office:excel'>
   <Unsynced/>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <HorizontalResolution>1200</HorizontalResolution>
    <VerticalResolution>1200</VerticalResolution>
   </Print>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>5</ActiveRow>
     <ActiveCol>3</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>";
?>
