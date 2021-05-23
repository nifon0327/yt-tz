<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';

$eDate=$eDate==""?date("Y-m-d"):$eDate;
$SearchRows=$CompanyId==""?"":" AND M.CompanyId='$CompanyId'";
$SearchRows.=$InvoiceNO==""?"":" AND M.InvoiceNO LIKE '$InvoiceNO%'";
$SearchRows.=" AND M.Date>='$sDate' AND M.Date<='$eDate' ";
$InvoiceNOSTR=$InvoiceNO;
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
  
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'NO');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice No');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Shipping Qty');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Commission Code');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:E1')->applyFromArray($style_center);
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('AAAAAA');
 $Rows=2;$i=1;
 $SumQty=0;
 
 $result = mysql_query("SELECT M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.ShipType,M.Remark,M.Operator,T.Type as incomeType,C.Forshort,C.PayType,D.Rate,D.Symbol,M.Ship,T.Attached
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
WHERE 1 $SearchRows  ORDER BY M.Date,M.InvoiceNO",$link_id);
 if($myrow = mysql_fetch_array($result)){
	do{
		  $Id=$myrow["Id"];
		  $InvoiceNO=$myrow["InvoiceNO"];
		 //客户退款
		 $checkReturnAmount=mysql_query("SELECT SUM(G.OrderQty*G.Price) AS ReturnAmount
	     FROM $DataIn.ch1_shipsheet S 
	     LEFT JOIN $DataIn.cg1_stocksheet G ON  G.POrderId=S.POrderId 
	     LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	     WHERE S.Mid=$Id AND D.TypeId=9104",$link_id);
		 $ReturnAmount=sprintf("%.2f",mysql_result($checkReturnAmount,0,"ReturnAmount"));
		 //$ReturnAmountSUM+=$ReturnAmount;
		 //$ReturnAmount=zerotospace($ReturnAmount);
		 
		 
		 //出货总数--不包样品之类的--
		$sListResult = mysql_query("
			SELECT SUM(S.Qty) as ShippingQty
			FROM $DataIn.ch1_shipsheet S 
			WHERE S.Mid='$Id' AND S.Type='1'
		",$link_id);	
		$ShippingQty=mysql_result($sListResult,0,"ShippingQty");
         $SumQty+=$ShippingQty;
         $ShippingQty=number_format($ShippingQty);
         
		$sListResult = mysql_query("
			SELECT O.OrderPO,sum(S.Qty) as SQty
			FROM $DataIn.ch1_shipsheet S 
		    LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
			WHERE S.Mid='$Id' AND S.Type='1' group by O.OrderPO
		",$link_id);
		/*
		echo "
			SELECT O.OrderPO,sum(S.Qty) as SQty
			FROM $DataIn.ch1_shipsheet S 
		    LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
			WHERE S.Mid='$Id' AND S.Type='1' group by O.OrderPO";
		*/	
		$OrderPOstr="";
		if($Ordermyrow = mysql_fetch_array($sListResult)){
			do{
				$OrderPO=$Ordermyrow["OrderPO"];
				if($OrderPOstr==""){
					$OrderPOstr=$OrderPO;
				}
				else {
					$OrderPOstr=$OrderPOstr."/".$OrderPO;
				}
				
			}while ($Ordermyrow = mysql_fetch_array($sListResult));
		}
		
		if ($ReturnAmount==0) continue;
		 $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$OrderPOstr");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$InvoiceNO");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$ShippingQty");
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$ReturnAmount");
	   $i++;
	   $Rows++;
		}while ($myrow = mysql_fetch_array($result));
		$objPHPExcel->getActiveSheet()->getStyle( "A2:A$Rows")->applyFromArray($style_center);
		$objPHPExcel->getActiveSheet()->getStyle( "B2:C$Rows")->applyFromArray($style_left);
		$objPHPExcel->getActiveSheet()->getStyle( "D2:E$Rows")->applyFromArray($style_right);
      
        $SumQty=number_format($SumQty);
         $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Total");
         $objPHPExcel->getActiveSheet()->getStyle( "A$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$SumQty");
         $objPHPExcel->getActiveSheet()->getStyle( "A$Rows:E$Rows")->applyFromArray($style_right);
         $objPHPExcel->getActiveSheet()->getStyle("A$Rows:E$Rows")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
         $objPHPExcel->getActiveSheet()->getStyle("A$Rows:E$Rows")->getFill()->getStartColor()->setARGB('AAAAAA');
         $Rows++;
	}
$TitleSTR=$CompanyId==""?"$InvoiceNOSTR":$CompanyId;
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$TitleSTR.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
