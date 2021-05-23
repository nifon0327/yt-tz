<?php 
//二合一已更新电信---yang 20120801
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=productlist.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$result = mysql_query("SELECT ProductId,eCode,Remark,Price,Moq,Date FROM $DataIn.productdata 
WHERE 1 AND CompanyId=$CompanyId AND Estate=1 ORDER BY Id DESC",$link_id);
$Rows=@mysql_num_rows($result)+10;
if($CompanyId==1003){
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
  <Version>11.9999</Version>
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
   <Font ss:FontName='??' x:CharSet='134' ss:Size='12'/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID='s21'>
   <Borders/>
   <Font x:Family='Swiss' ss:Size='9'/>
  </Style>
  <Style ss:ID='s22'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
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
   <NumberFormat ss:Format='@'/>
  </Style>
  <Style ss:ID='s24'>
   <Alignment ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='ˎ̥' x:CharSet='134' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s25'>
   <Alignment ss:Horizontal='Right' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
   <NumberFormat ss:Format='0.00_ '/>
  </Style>
  <Style ss:ID='s26'>
   <Alignment ss:Horizontal='Right' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
   <NumberFormat ss:Format='0_ '/>
  </Style>
  <Style ss:ID='s27'>
   <Alignment ss:Horizontal='Left' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s28'>
   <Alignment ss:Horizontal='Right' ss:Vertical='Center' ss:WrapText='1'/>
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
 <Worksheet ss:Name='Product List'>
  <Table ss:ExpandedColumnCount='7' ss:ExpandedRowCount='$Rows' x:FullColumns='1'
   x:FullRows='1' ss:StyleID='s21' ss:DefaultColumnWidth='54'
   ss:DefaultRowHeight='18'>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='33'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='48.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='242.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='42.75' ss:Span='1'/>
   <Column ss:Index='6' ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='53.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='290.25'/>
   <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Item</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>ID</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Product Code</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Price</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>MOQ</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Date</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Remark</Data></Cell>
   </Row>   ";
   if($myrow = mysql_fetch_array($result)){
	$i=1;
	do{
	 	$ProductId=$myrow["ProductId"];
		$eCode=$myrow["eCode"];		
	  	$Price=$myrow["Price"];	  	
		$Moq=$myrow["Moq"]; 
		$Remark=iconv("GB2312","UTF-8",$myrow["Remark"]);
		$Remark=$Remark==""?" ":$Remark;
		$Date=$myrow["Date"];
		   echo"
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:StyleID='s22'><Data ss:Type='Number'>$i</Data></Cell>
			<Cell ss:StyleID='s23'><Data ss:Type='Number'>$ProductId</Data></Cell>
			<Cell ss:StyleID='s24'><Data ss:Type='String'>$eCode</Data></Cell>
			<Cell ss:StyleID='s25'><Data ss:Type='Number'>$Price</Data></Cell>
			<Cell ss:StyleID='s26'><Data ss:Type='Number'>$Moq</Data></Cell>
			<Cell ss:StyleID='s28'><Data ss:Type='DateTime'>$Date</Data></Cell>
			<Cell ss:StyleID='s27'><Data ss:Type='String'>$Remark </Data></Cell>
		   </Row>";
		$i++; 
		}while ($myrow = mysql_fetch_array($result));
	}
   echo"
   </Table>
  <WorksheetOptions xmlns='urn:schemas-microsoft-com:office:excel'>
   <Unsynced/>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>7</ActiveRow>
     <ActiveCol>2</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
  <x:worksheetoptions>
    <x:unsynced/>
    <x:print>
     <x:validprinterinfo/>
     <x:papersizeindex>9</x:papersizeindex>
     <x:horizontalresolution>1200</x:horizontalresolution>
     <x:verticalresolution>1200</x:verticalresolution>
    </x:print>
    <x:selected/>
    <x:panes>
     <x:pane>
      <x:number>3</x:number>
      <x:activerow>3</x:activerow>
      <x:activecol>5</x:activecol>
     </x:pane>
    </x:panes>
    <x:protectobjects>False</x:protectobjects>
    <x:protectscenarios>False</x:protectscenarios>
   </x:worksheetoptions>
 </Worksheet>
</Workbook>";
}
else{
	echo"<?xml version='1.0'?>
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
  <Version>11.9999</Version>
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
   <Font ss:FontName='??' x:CharSet='134' ss:Size='12'/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID='s21'>
   <Borders/>
   <Font x:Family='Swiss' ss:Size='9'/>
  </Style>
  <Style ss:ID='s22'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
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
   <NumberFormat ss:Format='@'/>
  </Style>
  <Style ss:ID='s24'>
   <Alignment ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='ˎ̥' x:CharSet='134' ss:Size='9' ss:Color='#333333'/>
  </Style>
  <Style ss:ID='s25'>
   <Alignment ss:Horizontal='Right' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font x:Family='Swiss' ss:Size='9' ss:Color='#333333'/>
   <NumberFormat ss:Format='0.00_ '/>
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
   <NumberFormat ss:Format='[ENG][$-409]d/mmm/yy;@'/>
  </Style>
  <Style ss:ID='s28'>
   <Alignment ss:Horizontal='Left' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9' ss:Color='#333333'/>
  </Style>
 </Styles>
 <Worksheet ss:Name='Product List'>
 
  <Table ss:ExpandedColumnCount='6' ss:ExpandedRowCount='$Rows' x:FullColumns='1'
   x:FullRows='1' ss:StyleID='s21' ss:DefaultColumnWidth='54'
   ss:DefaultRowHeight='18'>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='33'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='48.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='242.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='42.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='53.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='290.25'/>
   <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Item</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>ID</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Product Code</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Price</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Date</Data></Cell>
    <Cell ss:StyleID='s22'><Data ss:Type='String'>Remark</Data></Cell>
   </Row>";
      if($myrow = mysql_fetch_array($result)){
	$i=1;
	do{
	 	$ProductId=$myrow["ProductId"];
		$eCode=$myrow["eCode"];		
	  	$Price=$myrow["Price"];	  	
		$Moq=$myrow["Moq"]; 
		$Date=$myrow["Date"]; 
		$Remark=iconv("GB2312","UTF-8",$myrow["Remark"]);
		$Remark=$Remark==""?" ":$Remark;
		   echo"
		   <Row ss:AutoFitHeight='0'>
			<Cell ss:StyleID='s22'><Data ss:Type='Number'>$i</Data></Cell>
			<Cell ss:StyleID='s23'><Data ss:Type='Number'>$ProductId</Data></Cell>
			<Cell ss:StyleID='s24'><Data ss:Type='String'>$eCode</Data></Cell>
			<Cell ss:StyleID='s25'><Data ss:Type='Number'>$Price</Data></Cell>
			<Cell ss:StyleID='s27'><Data ss:Type='DateTime'>$Date</Data></Cell>
			<Cell ss:StyleID='s28'><Data ss:Type='String'>$Remark </Data></Cell>
		   </Row>";
		$i++; 
		}while ($myrow = mysql_fetch_array($result));
	}
  echo"
  </Table>
  <WorksheetOptions xmlns='urn:schemas-microsoft-com:office:excel'>
   <Unsynced/>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>7</ActiveRow>
     <ActiveCol>2</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
  <x:worksheetoptions>
    <x:unsynced/>
    <x:print>
     <x:validprinterinfo/>
     <x:papersizeindex>9</x:papersizeindex>
     <x:horizontalresolution>1200</x:horizontalresolution>
     <x:verticalresolution>1200</x:verticalresolution>
    </x:print>
    <x:selected/>
    <x:panes>
     <x:pane>
      <x:number>3</x:number>
      <x:activerow>3</x:activerow>
      <x:activecol>5</x:activecol>
     </x:pane>
    </x:panes>
    <x:protectobjects>False</x:protectobjects>
    <x:protectscenarios>False</x:protectscenarios>
   </x:worksheetoptions>
 </Worksheet>
</Workbook>";
	}
?>
