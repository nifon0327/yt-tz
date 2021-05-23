<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

switch($myCompanyId){
  case "1004"://CEL-A OR CEL-B OR CEL-C
  case "1059":
  case "1072":
			$SearchRows.=" and (M.CompanyId='1004' OR M.CompanyId='1059'  OR M.CompanyId='1072') ";
			$ClientAction =1;     
			break;
  case "1081":
  case "1002":
  case "1080":
  case "1065":
				$SearchRows.=" and M.CompanyId in ('1081','1002','1080','1065')";
			break;
  default:
			   $SearchRows.=" and M.CompanyId='$myCompanyId'";
			break;
}
if($chooseDate!=""){
	$StartDate=date("Y-m-01",strtotime($chooseDate."-01"));
	$EndDate=date("Y-m-t",strtotime($chooseDate."-01"));
	$SearchRows.=" and ((M.Date>'$StartDate' and M.Date<'$EndDate') OR M.Date='$StartDate' OR M.Date='$EndDate')";
}
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
 // Create new PHPExcel object
$objPHPExcel = new PHPExcel();
 $style_left= array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
$style_center = array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
  $style_right = array( 
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
	

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PO#');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Product Code');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Qty');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Price');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'OrderDate');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Ready Date');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'shipping date');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Running days');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:H1')->applyFromArray($style_center);
$Rows=2;

$mySql="SELECT M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,C.Forshort,D.Rate ,M.Ship,M.ShipType
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
WHERE 1 $SearchRows $CreditNoteStr ORDER BY M.Date DESC";
//echo $mySql;
//$objPHPExcel->getActiveSheet()->setCellValue('H2', "$mySql");
$result = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{
		$Id=$myRow["Id"];
		$ShipId=$Id;
		$CompanyId=$myRow["CompanyId"];
		$Rate=$myRow["Rate"];
		$Number=$myRow["Number"];
		$Forshort=$myRow["Forshort"];
		$InvoiceNO=$myRow["InvoiceNO"];
		$InvoiceFile=$myRow["InvoiceFile"];
		
        $subsql= "
		SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN,
		P.Weight AS Weight,P.TestStandard,M.Date,E.Leadtime,N.OrderDate AS orderDate 
			FROM ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid 
			LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId
			LEFT JOIN $DataIn.yw1_ordermain N ON N.OrderNumber=O.OrderNumber
			LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=O.Id 
			LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
			WHERE S.Mid='$ShipId' AND S.Type='1'
		UNION ALL
			SELECT S.Id,S.POrderId,'' AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,O.Weight AS Weight,'' AS TestStandard,M.Date,'' AS Leadtime,O.Date AS orderDate 
			FROM $DataIn.ch1_shipsheet S
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
			WHERE S.Mid='$ShipId' AND S.Type='2'
		UNION ALL
			SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN,'0' AS Weight ,'' AS TestStandard,M.Date,'' AS Leadtime,O.Date AS orderDate 
			FROM $DataIn.ch1_shipsheet S
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
			WHERE S.Mid='$ShipId' AND S.Type='3'
		";		
		
		
		$sListResult = mysql_query($subsql,$link_id);
		if ($StockRows = mysql_fetch_array($sListResult)) {
			do{		
				
				$OrderPO=$StockRows["OrderPO"]==""?"&nbsp;":$StockRows["OrderPO"];
				$POrderId=$StockRows["POrderId"];
				$eCode=$StockRows["eCode"];
				$Qty=$StockRows["Qty"];
				$Price=$StockRows["Price"];				
				$orderDate=$StockRows["orderDate"];
				$Type=$StockRows["Type"];
				$readyDate="  ";
				$cycleDay=" ";
				
				if($Type==1){  //表示订单
					$checkSplit=mysql_query("SELECT date FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId'  order by date desc LIMIT 1",$link_id);
					if($splitRow = mysql_fetch_array($checkSplit)){
		            	$readyDate=$splitRow["date"]; 
						$readyDate=date("Y-m-d",strtotime($readyDate));
						$cycleDay=(strtotime($readyDate)-strtotime($orderDate))/3600/24;
                     }
				}
				
				$Date=$StockRows["Date"];
				
				
		       $SUMQTY=$SUMQTY+$Qty;
	            
				$objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
				$objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$OrderPO");
				$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$eCode");
				$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$Qty");
				$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Price");
				$objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$orderDate");
				$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$readyDate");
				$objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Date");
				$objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$cycleDay");
				
				$Rows++;
				
		 		}while ($StockRows = mysql_fetch_array($sListResult));
		}
		
        //break;
		$i++; 
	}while ($myRow = mysql_fetch_array($result));
}
$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(30);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Total: ");
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:B$Rows"); 
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$SUMQTY");
$objPHPExcel->getActiveSheet()->mergeCells("D$Rows:H$Rows"); 

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=OrderStatus.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
