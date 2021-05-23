<?php   
//电信-zxq 2012-08-01
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=productlist_muvit.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$result = mysql_query("SELECT P.eCode,P.Description,P.Price,P.Weight,G.Relation,S.Spec
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.pands G ON. G.ProductId=P.ProductId
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
	WHERE 1 AND S.TypeId='9040' AND P.Estate=1 AND P.CompanyId='1064' ORDER BY P.Id DESC",$link_id);
$Rows=@mysql_num_rows($result)+10;//行数
$Cols=7;//列数

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
 <Worksheet ss:Name='Productlist_muvit'>
  <Table ss:ExpandedColumnCount='$Cols' ss:ExpandedRowCount='$Rows' x:FullColumns='1'
   x:FullRows='1' ss:StyleID='s21' ss:DefaultColumnWidth='54'
   ss:DefaultRowHeight='18'>
   ";
 echo"
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='135.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='350.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='70.25'/>
    <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='70.25'/>
   <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>No.</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>Product Code</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>Description</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Unit/Carton</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>Carton Size(CM)</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>NW(KG)</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>GW(KG)</Data></Cell>
   </Row>";
     if($myRow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{
	 	
		$eCode=$myRow["eCode"];
		$Description=$myRow["Description"];
		$Weight=$myRow["Weight"];
		$Relation=explode("/",$myRow["Relation"]); 
		$Boxs=$Relation[1];
		$Spec=$myRow["Spec"];
		$NW=($Boxs*$Weight)/1000;
		if($NW>0){
			$GW=$NW+1;
		}
		else{
			$NW="";$GW="";
		}
		
	   echo"
	   <Row ss:AutoFitHeight='0'>
		<Cell ss:StyleID='s23'><Data ss:Type='String'>$i</Data></Cell>
		<Cell ss:StyleID='s24'><Data ss:Type='String'>$eCode</Data></Cell>
		<Cell ss:StyleID='s24'><Data ss:Type='String'>$Description</Data></Cell>
		<Cell ss:StyleID='s23'><Data ss:Type='Number'>$Boxs</Data></Cell>
                <Cell ss:StyleID='s23'><Data ss:Type='String'>$Spec</Data></Cell>";
	   echo"<Cell ss:StyleID='s23'><Data ss:Type='Number'>$NW</Data></Cell>";
	    echo"<Cell ss:StyleID='s23'><Data ss:Type='Number'>$GW</Data></Cell>";
	   echo"</Row>";
		$i++; 
		}while ($myRow = mysql_fetch_array($result));
	}
echo "
  </Table>
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
