<?php   
//电信-zxq 2012-08-01
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=OrderStatus.xls"); 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
if ($ActionId==175){
    $SearchRows =" AND M.Estate=0 ";
    if ($chooseDate!='') $SearchRows.=" AND DATE_FORMAT(M.Date,'%Y-%m')='$chooseDate' "; 
    if ($CompanyId!='')  $SearchRows.=" AND M.CompanyId='$CompanyId' ";

}else{
	$Lens=count($checkid);
    for($i=0;$i<$Lens;$i++){
		$Id=$checkid[$i];
		if ($Id!=""){
			$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
			}
	}
	$SearchRows =" AND  M.Id IN ($Ids) ";
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
  
  if ($ActionId==175){
      
	  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
	 
	  $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
	  $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
	  $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Date');
	  $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice No');
	  $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Declaration');
	  $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Qty');
	  $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Amount(USD)');
	  $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Amount');
	  $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Bank');
	  $objPHPExcel->getActiveSheet()->setCellValue('I1', 'TaxName');
	   
	  $objPHPExcel->getActiveSheet()->getStyle( 'A1:I1')->applyFromArray($style_center);
	
  }else{

	  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	 
	  $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
	  $objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
	  $objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO');
	  $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice No');
	  $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Shipping Qty');
	  $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Commission Code');
	    
	  $objPHPExcel->getActiveSheet()->getStyle( 'A1:E1')->applyFromArray($style_center);
  }
  $Rows=2;
  $i=1; 
  $mySql="SELECT M.Id,M.CompanyId,M.BankId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.ShipType,M.Remark,M.Operator,T.Type as incomeType,C.Forshort,C.PayType,D.Rate,D.Symbol,M.Ship,T.Attached,B.Title AS Bank 
	FROM $DataIn.ch1_shipmain M 
	LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
	LEFT JOIN  $DataPublic.my2_bankinfo B ON B.Id=M.BankId 
	WHERE 1 $SearchRows  ORDER BY M.Date DESC";
  	  
  $myResult = mysql_query($mySql);
  while($myRow = mysql_fetch_assoc($myResult))
  {
	      $Id=$myRow["Id"];
		  $InvoiceNO=$myRow["InvoiceNO"];
		 //客户退款
		 $checkReturnAmount=mysql_query("SELECT SUM(G.OrderQty*G.Price) AS ReturnAmount
	     FROM $DataIn.ch1_shipsheet S 
	     LEFT JOIN $DataIn.cg1_stocksheet G ON  G.POrderId=S.POrderId 
	     LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	     WHERE S.Mid=$Id AND D.TypeId=9104",$link_id);
		 $ReturnAmount=sprintf("%.2f",mysql_result($checkReturnAmount,0,"ReturnAmount"));

		 //出货总数--不包样品之类的--
		$sListResult = mysql_query("
			SELECT SUM(S.Qty) as ShippingQty
			FROM $DataIn.ch1_shipsheet S 
			WHERE S.Mid='$Id' AND S.Type='1'
		",$link_id);	
		$ShippingQty=mysql_result($sListResult,0,"ShippingQty");


		$sListResult = mysql_query("
			SELECT O.OrderPO,sum(S.Qty) as SQty,X.name as taxName 
			FROM $DataIn.ch1_shipsheet S 
		    LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
		    LEFT JOIN $DataIn.taxtype X ON X.Id=O.taxtypeId 
			WHERE S.Mid='$Id' AND S.Type='1' group by O.OrderPO
		",$link_id);

		$OrderPOstr="";
		if($Ordermyrow = mysql_fetch_array($sListResult)){
		         $taxName=$Ordermyrow["taxName"];
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
	  
	if ($ActionId==175){
	  $Date=$myRow["Date"];
	  $incomeType=$myRow["incomeType"]==1?'报关':'';
	  $Rate=$myRow["Rate"];
	  $Symbol=$myRow["Symbol"];
	  $Bank=$myRow["Bank"];
	  $Sign=$myRow["Sign"];
	  $usdAmount='';$rmbAmount='';
	  $amountResult = mysql_query(" SELECT SUM(S.Qty*S.Price) AS Amount FROM $DataIn.ch1_shipsheet S WHERE S.Mid='$Id'",$link_id);	
	  $Amount=mysql_result($amountResult,0,"Amount")*$Sign;
	  if ($Rate!=1){
		  $usdAmount = $Amount;
		  $rmbAmount=round($Amount*$Rate,2);
	  }else{
		  $rmbAmount=round($Amount*$Rate,2);
	  }
	  
	  $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
	  $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
	  $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$Date");
	  $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$InvoiceNO");
	  $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$incomeType");
	  $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$ShippingQty");
	  $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$usdAmount");
	  $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	  $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$rmbAmount");
	  $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);  
	  $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Bank");
	  $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
	   $objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$taxName");
	  $objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
	 }else{
	  $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
	  $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
	  $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$OrderPOstr");
	  $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$InvoiceNO");
	  $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$ShippingQty");
	  $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$ReturnAmount");
	  $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	 }
	  
	  
	  $i++; 
	  $Rows++;
	  
  }

  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header("Content-Disposition: attachment;filename=$invoiceNo.xlsx");
  header('Cache-Control: max-age=0');

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter->save('php://output');
  exit;
?>
