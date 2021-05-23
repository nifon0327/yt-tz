<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=invoicefile.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_toexcel";
$_SESSION["nowWebPage"]=$nowWebPage;
$InvoiceSql="SELECT M.InvoiceNO FROM $DataIn.ch1_shipmain M WHERE M.Id='$Id'";
$InvoiceResult=mysql_query($InvoiceSql,$link_id);
$InvoiceNO=mysql_result($InvoiceResult,0,"InvoiceNO");
$result = mysql_query("SELECT * FROM (
                      SELECT L.Id,C.POrderId,C.ProductId,S.OrderPO,S.Qty,P.cName,P.eCode,P.MainWeight,S.Price  
					  FROM $DataIn.ch1_shipsheet C 
					  LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=C.POrderId 
					  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
					  LEFT JOIN  $DataIn.ch2_packinglist L ON L.POrderId=C.POrderId
					  WHERE C.Mid='$Id' and C.Type='1'
					  UNION ALL 
					  SELECT L.Id,C.POrderId,C.ProductId,'' AS OrderPO,S.Qty,S.SampName AS cName,
					  S.Description AS eCode,0 AS MainWeight ,'' AS Price  
					  FROM $DataIn.ch1_shipsheet C 
					  LEFT JOIN $DataIn.ch5_sampsheet S ON S.SampId=C.POrderId
					  LEFT JOIN  $DataIn.ch2_packinglist L ON L.POrderId=C.POrderId
					  WHERE C.Mid='$Id' AND C.Type='2' AND S.Type='1') A 
					  WHERE 1  ORDER BY A.Id",$link_id);
					
$Rows=@mysql_num_rows($result)*10+10;//行数
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
   <NumberFormat ss:Format='Short Date'/>
  </Style>
 </Styles>
 <Worksheet ss:Name='Order Status'>
  <Table ss:ExpandedColumnCount='$Cols' ss:ExpandedRowCount='$Rows' x:FullColumns='1'
   x:FullRows='1' ss:StyleID='s21' ss:DefaultColumnWidth='54'
   ss:DefaultRowHeight='18'>";

 echo"
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='185.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='126.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='106.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='106.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='56.25'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='120.75'/>
   <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='100.75'/>";
   echo" <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s24'><Data ss:Type='String'>&nbsp;</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>$InvoiceNO</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>&nbsp;</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>&nbsp;</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>&nbsp;</Data></Cell>
   </Row>";
   echo" <Row ss:AutoFitHeight='0'>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>箱数</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>箱号</Data></Cell>
	<Cell ss:StyleID='s24'><Data ss:Type='String'>PO</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>产品名称</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>海关编码</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>材质</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>用途</Data></Cell>
    <Cell ss:StyleID='s24'><Data ss:Type='String'>单品重(g)</Data></Cell>
    <Cell ss:StyleID='s23'><Data ss:Type='String'>单价</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>数量/箱</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>总数量</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>毛重</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>外箱尺寸</Data></Cell>
	<Cell ss:StyleID='s23'><Data ss:Type='String'>报关分类</Data></Cell>
   </Row>";
 
  if($myrow = mysql_fetch_array($result)){
	                $plResult = mysql_query("SELECT L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.POrderId,L.BoxSpec FROM $DataIn.ch2_packinglist L WHERE L.Mid='$Id' ORDER BY L.Id ",$link_id);	
					if ($plRows = mysql_fetch_array($plResult)){
						$j=1;
						do{
							$BoxRow=$plRows["BoxRow"];
							$BoxPcs=$plRows["BoxPcs"];
							$BoxQty=$plRows["BoxQty"];
							$POrderId=$plRows["POrderId"];
							$BoxSpec=$plRows["BoxSpec"];
							$FullQty=$plRows["FullQty"];
							$WG=$plRows["WG"];
			
							$checkType=mysql_fetch_array(mysql_query("SELECT Type FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId' LIMIT 1",$link_id));
							$Type=$checkType["Type"];
							switch($Type){
								case 1:	//产品
									$pSql = mysql_query("SELECT 
									S.OrderPO,P.cName,P.eCode,P.Description,P.MainWeight,S.Price,H.HSCode,
									BG.Name AS bgName,M.Name AS MaterialQ,W.Name AS UseWay
									FROM $DataIn.yw1_ordersheet S 
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
									LEFT JOIN $DataIn.productmq M ON M.Id = P.MaterialQ
                                    LEFT JOIN $DataIn.productuseway W ON W.Id = P.UseWay
									LEFT JOIN $DataIn.customscode H ON H.ProductId = P.ProductId
									LEFT JOIN $DataIn.taxtype BG ON BG.Id = P.taxtypeId
									WHERE S.POrderId='$POrderId' LIMIT 1",$link_id);
									if ($pRows = mysql_fetch_array($pSql)){
										$OrderPO=$pRows["OrderPO"];
										$cName=$pRows["cName"];
										$eCode=$pRows["eCode"];
										$Description=$pRows["Description"];	
										$MainWeight=$pRows["MainWeight"];
										$HSCode=$pRows["HSCode"];
										$MaterialQ=$pRows["MaterialQ"];
										$UseWay=$pRows["UseWay"];
										$bgName=$pRows["bgName"];
										$Price=$pRows["Price"];
										}
									break;
								case 2:	//样品
									$sSql = mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE SampId='$POrderId'",$link_id);
									if ($sRows = mysql_fetch_array($sSql)){
										$OrderPO="&nbsp;";
										$cName=$sRows["SampName"];
										$eCode="";
										$Description=$sRows["Description"];
										$HSCode="";
										$MaterialQ="";
										$UseWay="";
										$MainWeight=0;
										$Price=0;
										}		
									break;
								}
                                                       
							$BoxRowSTR=$BoxRow>1?"rowspan=$BoxRow":"";//检查是否合并行
							if($BoxRow==0){//并箱非首行
							     
								echo"<Row ss:AutoFitHeight='0'>
								<Cell ss:StyleID='s24'><Data ss:Type='String'></Data></Cell>
								<Cell ss:StyleID='s25'><Data ss:Type='String'></Data></Cell>
								<Cell ss:StyleID='s24'><Data ss:Type='String'>$OrderPO</Data></Cell>
								<Cell ss:StyleID='s26'><Data ss:Type='String'>$cName</Data></Cell>
								<Cell ss:StyleID='s26'><Data ss:Type='String'>$HSCode</Data></Cell>
								<Cell ss:StyleID='s26'><Data ss:Type='String'>$MaterialQ</Data></Cell>
								<Cell ss:StyleID='s26'><Data ss:Type='String'>$UseWay</Data></Cell>
								<Cell ss:StyleID='s24'><Data ss:Type='Number'>$MainWeight</Data></Cell>
								<Cell ss:StyleID='s24'><Data ss:Type='String'>$Price</Data></Cell>
								<Cell ss:StyleID='s26'><Data ss:Type='Number'>$BoxPcs</Data></Cell>
								<Cell ss:StyleID='s27'><Data ss:Type='String'></Data></Cell>
								<Cell ss:StyleID='s24'><Data ss:Type='String'></Data></Cell>
								<Cell ss:StyleID='s24'><Data ss:Type='String'></Data></Cell>
								<Cell ss:StyleID='s28'><Data ss:Type='String'></Data></Cell>";
								echo"</Row>";								
								//取相应的行号
								for($n=1;$n<=$OrderNum;$n++){
									if($rowArray[$n]==$POrderId){
										$theOrderNumRow=$n*6-3;
										}
									}
								//重新写入行集
								$k=$j-1;

								}
							else{
								$Sideline=1;
								$WgSUM=$WgSUM+$WG*$BoxQty;//毛重总计
								$NG=$WG;//净重			
								$NgSUM=$NgSUM+$NG*$BoxQty;//净重总计			
								$SUMQty=$SUMQty+$FullQty;//装箱总数合计
								
								$Small=$BoxSUM+1;//起始箱号
								$Most=$BoxSUM+$BoxQty;//终止箱号
								$BoxSUM=$Most;
								if($Most!=$Small){
									$Most=$Small."-".$Most;}
									
                              
							echo"<Row ss:AutoFitHeight='0'>
		                    <Cell ss:StyleID='s24'><Data ss:Type='String'>$BoxQty</Data></Cell>
		                    <Cell ss:StyleID='s25'><Data ss:Type='String'>$Most</Data></Cell>
		                    <Cell ss:StyleID='s24'><Data ss:Type='String'>$OrderPO</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$cName</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$HSCode</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$MaterialQ</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='String'>$UseWay</Data></Cell>
		                    <Cell ss:StyleID='s24'><Data ss:Type='Number'>$MainWeight</Data></Cell>
		                    <Cell ss:StyleID='s24'><Data ss:Type='String'>$Price</Data></Cell>
		                    <Cell ss:StyleID='s26'><Data ss:Type='Number'>$BoxPcs</Data></Cell>
		                    <Cell ss:StyleID='s27'><Data ss:Type='Number'>$FullQty</Data></Cell>
		                    <Cell ss:StyleID='s24'><Data ss:Type='Number'>$WG</Data></Cell>
	                        <Cell ss:StyleID='s28'><Data ss:Type='String'>$BoxSpec</Data></Cell>
	                        <Cell ss:StyleID='s24'><Data ss:Type='String'>$bgName</Data></Cell>";
	                        echo"</Row>";
								
									
								//读取行号								
								for($n=1;$n<=$OrderNum;$n++){
									if($rowArray[$n]==$POrderId){
										$theOrderNumRow=$n*6-3;
										}
									}
								$k=$j-1;						
								}
							$j++;
						}while ($plRows = mysql_fetch_array($plResult));
					}

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
