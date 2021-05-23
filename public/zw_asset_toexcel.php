<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=phone.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_toexcel";
$_SESSION["nowWebPage"]=$nowWebPage;

$mySql="SELECT R.Id,R.Model,R.Photo,R.delSign,R.Number,B.Name AS Brand,U.User,P.Name AS UserName,U.Remark,U.Date,U.Estate,U.Operator 
                   FROM $DataIn.zw1_assetrecord R 
                   LEFT JOIN $DataIn.zw1_brandtypes B ON B.Id=R.BrandId	 INNER JOIN( 
                     SELECT U1.AssetId,U1.User,U1.Remark,U1.Date,U1.Estate,U1.Operator
                    FROM $DataIn.zw1_assetuse U1 INNER JOIN(SELECT AssetId,MAX(Id) AS Id FROM $DataIn.zw1_assetuse group by AssetId) U2 ON U1.AssetId=U2.AssetId and U1.Id=U2.Id ) U ON U.AssetId=R.Id LEFT JOIN $DataPublic.staffmain P ON P.Number=U.User WHERE R.Estate=1 and R.TypeId='1' ORDER BY R.delSign,R.BrandId,R.Model";
$myResult = mysql_query($mySql,$link_id);
					
$Rows=@mysql_num_rows($myResult)+10;//行数
$Cols=8;//列数
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
  <Created>2007-06-26T01:05:04Z</Created>
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
   <Alignment  ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
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
   <NumberFormat ss:Format='[ENG][$-409]d/mmm/yy;@'/>
  </Style>
 </Styles>
 <Worksheet ss:Name='Order Status'>
  <Table ss:ExpandedColumnCount='$Cols' ss:ExpandedRowCount='$Rows' x:FullColumns='1'
   x:FullRows='1' ss:StyleID='s21' ss:DefaultColumnWidth='54'
   ss:DefaultRowHeight='25'>";

 echo"
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='76.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='96.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='76.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='76.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='216.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='86.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='100.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='65.25'/>";
   echo" <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>序号</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>品牌</Data></Cell>
	<Cell ss:StyleID='s24'><Data ss:Type='String'>型号</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>机身ID</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>现使用情况</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>现领用人</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>交接日期</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>原领用人</Data></Cell>
   </Row>";
         $m=1;
  if($myRow = mysql_fetch_array($myResult)){
	do{
		$Id=$myRow["Id"];
		$Model=$myRow["Model"];
		$Photo=$myRow["Photo"];
		$delSign=$myRow["delSign"];
		$Number=$myRow["Number"]==""?"&nbsp;":$myRow["Number"];
		$Brand=$myRow["Brand"];
		$Remark=$myRow["Remark"];
		$Date=$myRow["Date"]."T00:00:00.000";
		$UserName=$myRow["UserName"];
		$showSign="1";
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		$User=$myRow["User"];
		if($Estate==0){
			$Operator="初始记录";
			}
		else{
			include "../model/subprogram/staffname.php";
			}
		
		if($delSign==1){
			$theDefaultColor="#FFA6D2";
			$UserName="";
			}
//$Brand $Model  $Number  $Remark  $UserName  $Date  $Operator
       echo"<Row ss:AutoFitHeight='0'>
		                    <Cell ss:StyleID='s24'><Data ss:Type='Number'>$m</Data></Cell>
		                    <Cell ss:StyleID='s25'><Data ss:Type='String'>$Brand</Data></Cell>
		                    <Cell ss:StyleID='s24'><Data ss:Type='String'>$Model</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$Number</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$Remark</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$UserName</Data></Cell>
	                        <Cell ss:StyleID='s28'><Data ss:Type='DateTime'></Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'></Data>$Operator</Cell></Row>";
		   $m++; 
		}while ($myRow = mysql_fetch_array($myResult));
	}
echo"</Table>
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
