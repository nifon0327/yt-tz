<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=invoicefile.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_toexcel";
$_SESSION["nowWebPage"]=$nowWebPage;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
//$Date=date("Y-m-d");
$mySql="SELECT C.POrderId,C.ProductId,S.OrderPO,S.Qty,P.cName,P.eCode,P.MainWeight,P.Price,P.Code,P.Description,M.InvoiceNO,M.Date  
			   FROM $DataIn.ch0_shipsheet C 
               LEFT JOIN ch0_shipmain M ON M.Id=C.Mid
			   LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=C.POrderId 
			   LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
			   WHERE C.Mid  IN ($Ids)";
$result = mysql_query($mySql,$link_id);
					
$Rows=@mysql_num_rows($result)+10;//行数
$Cols=18;//列数
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
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='286.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='100.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='65.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='65.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='65.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='90.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='90.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='90.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='120.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='120.75'/>";
   echo" <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>CG Mobile PO#</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>Vendor confirmed Date</Data></Cell>
	<Cell ss:StyleID='s24'><Data ss:Type='String'>Vendor name</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>CG Mobile ref.</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>Vendor ref.</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Description</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>Bar code</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Order qty</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>No of Ctns</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Qty/ctn</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>L</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>I</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>H</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Weight/ctn (kg)</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Total Volume (cbm)</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Total Weight (KG)</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Vendor's Packing list no.</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>Vendor's Cargo ready date </Data></Cell>
   </Row>";
 
  if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{
	    $POrderId=$myrow["POrderId"];
	 	$OrderPO=$myrow["OrderPO"];
		$cName=$myrow["cName"];
		$Qty=$myrow["Qty"];
        $InvoiceNO=$myrow["InvoiceNO"];
		$Description=$myrow["Description"];
        $Code=$myrow["Code"];
        $eCode=$myrow["eCode"];
        $Date=$myrow["Date"];
        $CodeArray=explode("|",$Code);
        if($CodeArray[1]!="")$Code=$CodeArray[1];
        else $Code=$CodeArray[0];
		$MainWeight=$myrow["MainWeight"]==0?"":$myrow["MainWeight"];
        $plResult = mysql_query("SELECT L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.POrderId,L.BoxSpec FROM $DataIn.ch0_packinglist L WHERE L.POrderId='$POrderId' ORDER BY L.BoxRow DESC",$link_id);
        $WgSUM=0;
		$SUMQty=0;
        $CubeSUM=0;
        $L=0;$I=0;$H=0;$BoxPcs=0;$WG=0;
		       if($plRows = mysql_fetch_array($plResult)){
			      do{
		              $BoxRow=$plRows["BoxRow"];
			          $BoxPcs=$plRows["BoxPcs"];
			          $BoxQty=$plRows["BoxQty"];
			          $BoxSpec=$plRows["BoxSpec"];                     
			          $FullQty=$plRows["FullQty"];
			          $WG=$plRows["WG"];
			          $WgSUM=$WgSUM+$WG*$BoxQty;//毛重总计	
			          $SUMQty=$SUMQty+$BoxQty;//箱数
		              if(($strPos=strpos(strtoupper($BoxSpec),"CM"))>2){
			                 $BoxSize=substr($BoxSpec,0,$strPos); //去掉CM
                             $BoxSize=str_replace( '×', 'x',$BoxSize);
		                   }
                      $SpecArray=explode("x",$BoxSize);
                      $L=$SpecArray[0];
                      $I=$SpecArray[1];
                      $H=$SpecArray[2];
                      if (substr_count($BoxSpec,"*")>0){
				      $BoxSpec=explode("*",substr($BoxSpec,0,-2));
                                }else{
                                     $BoxSpec=explode("×",substr($BoxSpec,0,-2));
                                   
                                }
                     $ThisCube=$BoxSpec[0]*$BoxSpec[1]*$BoxSpec[2];
                     $CubeSUM=$CubeSUM+$ThisCube*$BoxQty;//总体积
                     }while($plRows = mysql_fetch_array($plResult));
		        }
    	$CubeSUM=sprintf("%.2f",$CubeSUM/1000000);
        echo"<Row ss:AutoFitHeight='0'>
		                    <Cell ss:StyleID='s24'><Data ss:Type='String'>$OrderPO</Data></Cell>
		                    <Cell ss:StyleID='s25'><Data ss:Type='String'></Data></Cell>
		                    <Cell ss:StyleID='s24'><Data ss:Type='String'>Ash Cloud</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$eCode</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$cName</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$Description</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$Code</Data></Cell>
		                    <Cell ss:StyleID='s27'><Data ss:Type='Number'>$Qty</Data></Cell>
		                    <Cell ss:StyleID='s27'><Data ss:Type='Number'>$SUMQty</Data></Cell>
		                    <Cell ss:StyleID='s24'><Data ss:Type='Number'>$BoxPcs</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='Number'>$L</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='Number'>$I</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='Number'>$H</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='Number'>$WG</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='Number'>$CubeSUM</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='Number'>$WgSUM</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='String'>$InvoiceNO</Data></Cell>
	                        <Cell ss:StyleID='s28'><Data ss:Type='DateTime'>$Date</Data></Cell></Row>";
		   $i++; 
		}while ($myrow = mysql_fetch_array($result));
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
