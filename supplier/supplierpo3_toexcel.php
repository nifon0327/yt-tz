<?php 
//电信-zxq 2012-08-01
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Sheet.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
//步骤6：需处理数据记录处理
if($Estate==1){
	$SearchRows.=" AND F.Estate IS NULL";
	}
else{
	$SearchRows.=" AND F.Estate='$Estate'";
	}

$mySql="
SELECT M.Date,M.PurchaseID,M.Remark,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.StockRemark,S.AddRemark,S.Estate,S.Locks,
A.StuffCname,A.Picture,A.Gfile,A.Gremark 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
WHERE 1  AND M.CompanyId='$myCompanyId' AND DATE_FORMAT(M.Date,'%Y-%m')='$chooseDate' $SearchRows ORDER BY M.PurchaseID DESC
";
$mainResult = mysql_query($mySql,$link_id);
$Rows=@mysql_num_rows($mainResult)+10;//行数
$Cols=15;//列数

//头文件
echo"
<?xml version='1.0'?>
<?mso-application progid='Excel.Sheet'?>
<Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet'
 xmlns:o='urn:schemas-microsoft-com:office:office'
 xmlns:x='urn:schemas-microsoft-com:office:excel'
 xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet'
 xmlns:html='http://www.w3.org/TR/REC-html40'>
 <DocumentProperties xmlns='urn:schemas-microsoft-com:office:office'>
  <Author>Dragonp</Author>
  <LastAuthor>Dragonp</LastAuthor>
  <Created>2009-11-23T03:16:37Z</Created>
  <LastSaved>2009-11-23T03:54:20Z</LastSaved>
  <Company>Dragonp</Company>
  <Version>11.5606</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns='urn:schemas-microsoft-com:office:excel'>
  <WindowHeight>12045</WindowHeight>
  <WindowWidth>24675</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>105</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>";

//样式文件

echo"
 <Styles>
  <Style ss:ID='Default' ss:Name='Normal'>
   <Alignment ss:Vertical='Center'/>
   <Borders/>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='12'/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID='UnionRow1'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9' ss:Color='#333333'/>
   <Interior ss:Color='#FFFFFF' ss:Pattern='Solid'/>
   <NumberFormat ss:Format='Short Date'/>
  </Style>
  <Style ss:ID='UnionRow2'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9'/>
  </Style>
  <Style ss:ID='TitleA'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9' ss:Color='#333333'/>
   <Interior ss:Color='#C0C0C0' ss:Pattern='Solid'/>
  </Style>
  <Style ss:ID='TitleB'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9' ss:Color='#333333'/>
   <Interior ss:Color='#C0C0C0' ss:Pattern='Solid'/>
  </Style>
  <Style ss:ID='Style_Name'>
   <Alignment ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9'/>
   <Interior ss:Color='#FFFFFF' ss:Pattern='Solid'/>
  </Style>
  <Style ss:ID='Style_Number'>
   <Alignment ss:Horizontal='Right' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9'/>
   <Interior ss:Color='#FFFFFF' ss:Pattern='Solid'/>
  </Style>
  <Style ss:ID='Style_Id'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9'/>
   <Interior ss:Color='#FFFFFF' ss:Pattern='Solid'/>
  </Style>
  <Style ss:ID='Style_StockId'>
   <Alignment ss:Horizontal='Center' ss:Vertical='Center' ss:WrapText='1'/>
   <Borders>
    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
   </Borders>
   <Font ss:FontName='思源黑体' x:CharSet='134' ss:Size='9'/>
   <Interior ss:Color='#FFFFFF' ss:Pattern='Solid'/>
   <NumberFormat ss:Format='000000'/>
  </Style>
 </Styles>
";

//列宽行高设定
echo"
 <Worksheet ss:Name='Sheet1'>
  <Table ss:ExpandedColumnCount='18' ss:ExpandedRowCount='$Rows' x:FullColumns='1'";//ExpandedRowCount 总行数 ExpandedColumnCount列数
 echo"
   x:FullRows='1' ss:DefaultColumnWidth='54' ss:DefaultRowHeight='14.25'>
   <Column ss:Index='2' ss:Width='57'/>
   <Column ss:AutoFitWidth='0' ss:Width='33.75'/>
   <Column ss:AutoFitWidth='0' ss:Width='48'/>
   <Column ss:AutoFitWidth='0' ss:Width='132.75'/>
   <Column ss:AutoFitWidth='0' ss:Width='45' ss:Span='7'/>
   <Column ss:Index='14' ss:AutoFitWidth='0' ss:Width='33.75'/>
   <Column ss:AutoFitWidth='0' ss:Width='86.25'/>
   <Column ss:Index='17' ss:AutoFitWidth='0' ss:Width='26.25'/>
   <Column ss:AutoFitWidth='0' ss:Width='93.75'/>
";

//标题行
echo"
<Row ss:AutoFitHeight='0' ss:Height='15'>
    <Cell ss:StyleID='TitleA'><Data ss:Type='String'>下单日期</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>采购单号</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>行号</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>配件ID</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>配件名称</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>需求数</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>增购数</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>实购数</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>单价</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>金额</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>金额(RMB)</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>收货数</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>欠数</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>货款</Data></Cell>
    <Cell ss:StyleID='TitleB'><Data ss:Type='String'>采购流水号</Data></Cell>
   </Row>
";


$i=1;
if($mainRows = mysql_fetch_array($mainResult)){
	$tbDefalut=0;
	$midDefault="";
	do{
		$m=1;
		$Mid=$mainRows["Mid"];
		$Date=$mainRows["Date"];
		$PurchaseID=$mainRows["PurchaseID"];
		$StuffId=$mainRows["StuffId"];
		
		if($StuffId!=""){
			$StuffCname=$mainRows["StuffCname"];
			$FactualQty=$mainRows["FactualQty"];
			$AddQty=$mainRows["AddQty"];
			$Qty=$FactualQty+$AddQty;
			$Price=$mainRows["Price"];
			$Amount=sprintf("%.2f",$Qty*$Price);		
			$StockId=$mainRows["StockId"];
			$Estate=$mainRows["Estate"];
			$CompanyId=$mainRows["CompanyId"];
			
			//收货情况				
			$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id",$link_id);
			$rkQty=mysql_result($rkTemp,0,"Qty");
			$rkQty=$rkQty==""?0:$rkQty;

			$checkPay=mysql_query("SELECT Estate,Month FROM $DataIn.cw1_fkoutsheet WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1",$link_id);
			if($checkPayRow=mysql_fetch_array($checkPay)){
				$cwEstate=$checkPayRow["Estate"];
				$AskMonth=$checkPayRow["Month"];
				switch($cwEstate){
					case 0:$cwEstate="√";break;
					case 2:$cwEstate="×.";break;
					case 3:$cwEstate="√.";break;
					}
				}
			else{
				$cwEstate="×";
				}
//尾数
			$Mantissa=$Qty-$rkQty;
			//////////////////////////////////////////////////
			
			$midDefault=$midDefault==""?$Mid:$midDefault;
			
			if($midDefault==$Mid){//行开始
					if($tbDefalut==0){	//第一行
						$TableHtml="<Cell ss:StyleID='Style_Id'><Data ss:Type='Number'>$i</Data></Cell>
						<Cell ss:StyleID='Style_Id'><Data ss:Type='Number'>$StuffId</Data></Cell>
						<Cell ss:StyleID='Style_Name'><Data ss:Type='String'>$StuffCname</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$FactualQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$AddQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Qty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Price</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Amount</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Amount</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$rkQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Mantissa</Data></Cell>
						<Cell ss:StyleID='Style_Id'><Data ss:Type='String'>$cwEstate</Data></Cell>
						<Cell ss:StyleID='Style_StockId'><Data ss:Type='Number'>$StockId</Data></Cell>
					   </Row>";$i++;$tbDefalut++;
						}
					else{				//其它行
						$TableHtml.="
						<Row ss:AutoFitHeight='0'>
						<Cell ss:Index='3' ss:StyleID='Style_Id'><Data ss:Type='Number'>$i</Data></Cell>
						<Cell ss:StyleID='Style_Id'><Data ss:Type='Number'>$StuffId</Data></Cell>
						<Cell ss:StyleID='Style_Name'><Data ss:Type='String'>$StuffCname</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$FactualQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$AddQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Qty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Price</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Amount</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Amount</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$rkQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Mantissa</Data></Cell>
						<Cell ss:StyleID='Style_Id'><Data ss:Type='String'>$cwEstate</Data></Cell>
						<Cell ss:StyleID='Style_StockId'><Data ss:Type='Number'>$StockId</Data></Cell>
					   </Row>";$i++;$tbDefalut++;
						}
				}
			else{
				//结束上一行
				$Rows=$tbDefalut-1;
				echo"
				<Row ss:AutoFitHeight='0'>
				<Cell ss:MergeDown='$Rows' ss:StyleID='UnionRow1'><Data ss:Type='DateTime'>$Date</Data></Cell>
    			<Cell ss:MergeDown='$Rows' ss:StyleID='UnionRow2'><Data ss:Type='Number'>$PurchaseID</Data></Cell>".$TableHtml;
				//开始新一行
				$midDefault=$Mid;$tbDefalut=0;
				$TableHtml="<Cell ss:StyleID='Style_Id'><Data ss:Type='Number'>$i</Data></Cell>
						<Cell ss:StyleID='Style_Id'><Data ss:Type='Number'>$StuffId</Data></Cell>
						<Cell ss:StyleID='Style_Name'><Data ss:Type='String'>$StuffCname</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$FactualQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$AddQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Qty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Price</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Amount</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Amount</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$rkQty</Data></Cell>
						<Cell ss:StyleID='Style_Number'><Data ss:Type='Number'>$Mantissa</Data></Cell>
						<Cell ss:StyleID='Style_Id'><Data ss:Type='String'>$cwEstate</Data></Cell>
						<Cell ss:StyleID='Style_StockId'><Data ss:Type='Number'>$StockId</Data></Cell>
					   </Row>";$i++;$tbDefalut++;
				}
			}
		}while($mainRows = mysql_fetch_array($mainResult));
		$Rows=$tbDefalut-1;
		echo"
			<Row ss:AutoFitHeight='0'>
			<Cell ss:MergeDown='$Rows' ss:StyleID='UnionRow1'><Data ss:Type='DateTime'>$Date</Data></Cell>
    		<Cell ss:MergeDown='$Rows' ss:StyleID='UnionRow2'><Data ss:Type='Number'>$PurchaseID</Data></Cell>".$TableHtml;
	}
echo"
</Table>
  <WorksheetOptions xmlns='urn:schemas-microsoft-com:office:excel'>
   <Unsynced/>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <HorizontalResolution>600</HorizontalResolution>
    <VerticalResolution>0</VerticalResolution>
   </Print>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>20</ActiveRow>
     <ActiveCol>16</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
";
?>