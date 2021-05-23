<?php   
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=OrderStatus.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$ClientSign=0;
if($myCompanyId!=""){
     $IdArray=explode("^^",$tempIds);
     $Lens=count($IdArray);
     for($i=0;$i<$Lens;$i++){
         	$Id=$IdArray[$i];
	       if($Id!=""){
		         $Ids=$Ids==""?$Id:$Ids.",".$Id;
		      }
        }
      $IdStr=$tempIds==""?"":" AND P.Id IN ($Ids)";
    /* if($myCompanyId==1004 || $myCompanyId==1059 || $myCompanyId==1072){//CEL
                $ClientSTR=" and (P.CompanyId='1004' OR P.CompanyId='1059' OR P.CompanyId='1072')";
                $ClientSign=1;
                 }
      else {
                $ClientSTR=" and P.CompanyId IN (1049)";//CG
                $ClientSign=2;
             }*/
      switch($myCompanyId){
                 case 1004:
                 case 1059:
                 case 1072:
                           $ClientSTR=" and (P.CompanyId='1004' OR P.CompanyId='1059' OR P.CompanyId='1072')";//CEL
                           $ClientSign=1;
                 break;
                case 1049:
                     $ClientSTR=" and P.CompanyId IN (1049)";//CG,CG-ASIA
                     $ClientSign=2;
                  break;
                case 1083:
                     $ClientSTR=" and P.CompanyId IN (1083)";//CG,CG-ASIA
                     $ClientSign=2;
                break;
                default:
                    $ClientSTR=" and P.CompanyId IN ($myCompanyId)";
                   break;
             }
      $mySql="SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,P.Remark,P.PackingUnit,P.Code
      FROM $DataIn.productdata P
      WHERE 1 AND P.Estate=1 $ClientSTR $IdStr order by Estate DESC,Id DESC ";
}
else 	{
           $mySql="";
           $myCompanyId="aaaaa";
            }
$result = mysql_query($mySql,$link_id);
$Rows=@mysql_num_rows($result)+10;//行数
$Cols=10;//列数
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
if($ClientSign==1){
          echo"<Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='165.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='135.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='71.25'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='71.25'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='71.25'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='71.25'/>
           <Row ss:AutoFitHeight='0'>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>No.</Data></Cell>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>Chinese</Data></Cell>
           <Cell ss:StyleID='s24'><Data ss:Type='String'>Product Code</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Price</Data></Cell>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>Unit/Carton</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Average Leadtime</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Est.Leadtime</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Order History</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Date of Latest Order</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Supplier Rating</Data></Cell>
           </Row>";}
else {
           echo"<Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='165.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='135.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='50.25'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
            <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='81.75'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='91.25'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='71.25'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='71.25'/>
	        <Column ss:StyleID='s21' ss:AutoFitWidth='0' ss:Width='71.25'/>
           <Row ss:AutoFitHeight='0'>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>No.</Data></Cell>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>Chinese</Data></Cell>
           <Cell ss:StyleID='s24'><Data ss:Type='String'>Product Code</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Price</Data></Cell>
           <Cell ss:StyleID='s23'><Data ss:Type='String'>Unit/Carton</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Average Leadtime</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>BoxSpec</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>volume</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Order History</Data></Cell>
	       <Cell ss:StyleID='s23'><Data ss:Type='String'>Date of Latest Order</Data></Cell>
           </Row>";

}
    if($myRow = mysql_fetch_array($result)){
	$i=1;
	do{
	       $ProductId=$myRow["ProductId"];
		   $cName=$myRow["cName"];
		   $eCode=$myRow["eCode"]==""?"&nbsp;":$myRow["eCode"];
		   $Price=sprintf("%.2f",$myRow["Price"]);
			//检查装箱数量
		$checkNumbers=mysql_fetch_array(mysql_query("SELECT IFNULL(N.Relation,0) AS Relation,S.Spec
		FROM $DataIn.pands N
		LEFT JOIN $DataIn.stuffdata S ON S.StuffId=N.StuffId
		WHERE N.ProductId=$ProductId AND S.TypeId='9040'",$link_id));
		$BoxNums=$checkNumbers["Relation"];
        $BoxSpec=$checkNumbers["Spec"];
		if($BoxNums!=0){
			   $BoxNumsArray=explode("/",$BoxNums);
			   $BoxNums=$BoxNumsArray[1];
			   }
		else{
			   $BoxNums="&nbsp;";
			   }
      if (substr_count($BoxSpec,"*")>0){
				     $Spec=explode("*",substr($BoxSpec,0,-2));
                                }else{
                                     $Spec=explode("×",substr($BoxSpec,0,-2));
                                   
                                }
       $ThisCube=$Spec[0]*$Spec[1]*$Spec[2];
       $ThisCube=sprintf("%.2f",$ThisCube/1000000);

		//订单总数
		$checkAllQty= mysql_fetch_array(mysql_query("SELECT count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.ProductId='$ProductId' GROUP BY OrderPO)A",$link_id));
		$Orders=$checkAllQty["Orders"];
		//已出货数量
		$checkShipQty= mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id));
		$ShipQtySum=$checkShipQty["ShipQty"];
		$ShipQtySum=$ShipQtySum."($Orders)";
		 //最后出货日期
         $MonthResult=mysql_fetch_array(mysql_query("SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,
             TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId 
             FROM $DataIn.ch1_shipmain M 
	         LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
            WHERE 1 AND  S.ProductId='$ProductId' ORDER BY M.Date DESC",$link_id));
		$Months=$MonthResult["Months"];
		$LastMonth=$MonthResult["LastMonth"];
		if($Months!=NULL){
               $LastShipMonth=$LastMonth;
			     }
		else{//没有出过货
			    $LastShipMonth="&nbsp;";
			    }
		//*******************交货期
		include "../model/subprogram/product_chjq.php";
        $JqAvg=str_replace("days","d",$JqAvg);
         $EstResult=mysql_fetch_array(mysql_query("SELECT Estleadtime FROM $DataIn.product_estleadtime WHERE ProductId='$ProductId'",$link_id));
         $EstLeadtime=$EstResult["Estleadtime"]==""?"&nbsp;":$EstResult["Estleadtime"];
         $RatingResult=mysql_fetch_array(mysql_query("SELECT pj_times FROM $DataIn.product_pj WHERE ProductId='$ProductId'",$link_id));
         $pj_times=$RatingResult["pj_times"]==""?"&nbsp;":$RatingResult["pj_times"];
if($ClientSign==1){
	   echo"<Row ss:AutoFitHeight='0'>
		<Cell ss:StyleID='s24'><Data ss:Type='Number'>$i</Data></Cell>
		<Cell ss:StyleID='s25'><Data ss:Type='String'>$cName</Data></Cell>
		<Cell ss:StyleID='s24'><Data ss:Type='String'>$eCode</Data></Cell>
		<Cell ss:StyleID='s27'><Data ss:Type='Number'>$Price</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$BoxNums</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$JqAvg</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$EstLeadtime</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$ShipQtySum</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$LastShipMonth</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$pj_times</Data></Cell></Row>";
        }
else{

	   echo"<Row ss:AutoFitHeight='0'>
		<Cell ss:StyleID='s24'><Data ss:Type='Number'>$i</Data></Cell>
		<Cell ss:StyleID='s25'><Data ss:Type='String'>$cName</Data></Cell>
		<Cell ss:StyleID='s24'><Data ss:Type='String'>$eCode</Data></Cell>
		<Cell ss:StyleID='s27'><Data ss:Type='Number'>$Price</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$BoxNums</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$JqAvg</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$BoxSpec</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='Number'>$ThisCube</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$ShipQtySum</Data></Cell>
        <Cell ss:StyleID='s23'><Data ss:Type='String'>$LastShipMonth</Data></Cell>
       </Row>";

        }
		$i++; 
		}while ($myRow = mysql_fetch_array($result));
	}
if($ClientSign==1){
   echo"<Row ss:AutoFitHeight='0'>
    <Cell ss:MergeAcross='9' ss:StyleID='m29839800'><Data ss:Type='String'>Supplier Rating : 1 is meaning *,2 is meaning ** ,3 is meaning ***</Data></Cell>
   </Row>";
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
