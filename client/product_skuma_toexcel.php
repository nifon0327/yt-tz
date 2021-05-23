<?php   
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=OrderStatus.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
if($myCompanyId!=""){
    switch($from){
        case "cel":
                    $CompanySTR="and (P.CompanyId='1004' OR P.CompanyId='1059' OR P.CompanyId='1072') "; break;
         case "AF":
                     $CompanySTR="and (P.CompanyId='1064' OR P.CompanyId='1071' )"; break;
           }
$mySql="SELECT P.ProductId,P.eCode,P.MainWeight,P.Weight,G.Relation,S.Spec,P.TestStandard
FROM $DataIn.productdata P
LEFT JOIN $DataIn.pands G ON. G.ProductId=P.ProductId
LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
WHERE 1  AND S.TypeId='9040' AND P.Estate=1 $CompanySTR order by P.Estate DESC,P.Id DESC ";
}
else 	$mySql="";
$result = mysql_query($mySql,$link_id);
$Rows=@mysql_num_rows($result)+10;//行数
$Cols=16;//列数
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
 <Worksheet ss:Name='Order Status'>
  <Table ss:ExpandedColumnCount='$Cols' ss:ExpandedRowCount='$Rows' x:FullColumns='1'
   x:FullRows='1' ss:StyleID='s21' ss:DefaultColumnWidth='54'
   ss:DefaultRowHeight='18'>";
 echo"<Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='165.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='65.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='65.25'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
           <Row ss:AutoFitHeight='0'>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>No.</Data></Cell>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>Product Code</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>quantity</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>net weight(g)</Data></Cell>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>gross weight(g)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>width mt.(cm)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>length mt.(cm)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>height mt.(cm)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>volume(cm3)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>pieces inside master carton(pcs)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>net weight(kg)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>gross weight(kg)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>width mt.carton(cm)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>length mt.carton(cm)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>height mt.carton(cm)</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>volume(m3)</Data></Cell>
           </Row>";
    if($myRow = mysql_fetch_array($result)){
	$i=1;
	do{
          $ProductId=$myRow["ProductId"];
		  $eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
          $MainWeight=$myRow["MainWeight"]==0?"&nbsp;":$myRow["MainWeight"];
		  $Weight=$myRow["Weight"]==0?"&nbsp;":$myRow["Weight"];
		  $Relation=explode("/",$myRow["Relation"]); 
		  $Boxs=$Relation[1];
		  $Spec=explode("cm", $myRow["Spec"]);        
          $SpecArray=explode("×",$Spec[0]);
          $BoxLenght=$SpecArray[0];
          $BoxWidth=$SpecArray[1];
          $BoxHight=$SpecArray[2];
          $BoxVolume=sprintf("%.2f",($BoxLenght*$BoxWidth*$BoxHight)/1000000);
		  $BoxNW=($Boxs*$Weight)/1000;
		  if($BoxNW>0){
			      $BoxGW=$BoxNW+1;
		         }
		  else{
			      $BoxNW="&nbsp;";$BoxGW="&nbsp;";
			    }

            $SizeResult=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.product_size WHERE ProductId='$ProductId'",$link_id));
            $Width=$SizeResult["Width"]==""?"&nbsp;":$SizeResult["Width"];
            $Lenght=$SizeResult["Length"]==""?"&nbsp;":$SizeResult["Length"];
            $Hight=$SizeResult["height"]==""?"&nbsp;":$SizeResult["height"];
            $Volume=$Width*$Lenght*$Hight;
            $Volume=$Volume==0?"&nbsp;":$Volume;

            $Type1=$MainWeight=="&nbsp;"?"String":"Number";
            $Type2=$Weight=="&nbsp;"?"String":"Number";
            $Type3=$Width=="&nbsp;"?"String":"Number";
            $Type4=$Lenght=="&nbsp;"?"String":"Number";
            $Type5=$Hight=="&nbsp;"?"String":"Number";
            $Type6=$Volume=="&nbsp;"?"String":"Number";
            $Type7=$Boxs=="&nbsp;"?"String":"Number";
            $Type8=$BoxNW=="&nbsp;"?"String":"Number";
            $Type9=$BoxGW=="&nbsp;"?"String":"Number";
            $Type10=$BoxWidth=="&nbsp;"?"String":"Number";
            $Type11=$BoxLenght=="&nbsp;"?"String":"Number";
            $Type12=$BoxHight=="&nbsp;"?"String":"Number";
            $Type13=$BoxVolume=="&nbsp;"?"String":"Number";

	   echo"<Row ss:AutoFitHeight='0'>
		<Cell ss:StyleID='s24'><Data ss:Type='Number'>$i</Data></Cell>
		<Cell ss:StyleID='s24'><Data ss:Type='String'>$eCode</Data></Cell>
		<Cell ss:StyleID='s27'><Data ss:Type='Number'>1</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type1'>$MainWeight</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type2'>$Weight</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type3'>$Width</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type4'>$Lenght</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type5'>$Hight</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type6'>$Volume</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type7'>$Boxs</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type8'>$BoxNW</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type9'>$BoxGW</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type10'>$BoxWidth</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type11'>$BoxLenght</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type12'>$BoxHight</Data></Cell>
        <Cell ss:StyleID='s27'><Data ss:Type='$Type13'>$BoxVolume</Data></Cell></Row>";
		$i++; 
		}while ($myRow = mysql_fetch_array($result));
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
