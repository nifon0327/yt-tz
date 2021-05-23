<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
 // Create new PHPExcel object
$InvoiceSql="SELECT M.DeliveryNumber FROM $DataIn.ch1_deliverymain M WHERE M.Id='$Id'";
$InvoiceResult=mysql_query($InvoiceSql,$link_id);
$InvoiceNO=mysql_result($InvoiceResult,0,"DeliveryNumber");

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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells("A1:J1"); 
$objPHPExcel->getActiveSheet()->setCellValue("A1", "$InvoiceNO");
 $objPHPExcel->getActiveSheet()->getStyle( 'A1:J1')->applyFromArray($style_center);
$objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setSize(18);

$Rows=2;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "箱数");
$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "箱号");
$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "PO");
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "产品名称");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "单品重(g)");
$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "单价");
$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "数量/箱");
$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "总数量");
$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "毛重");
$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "外箱尺寸");  
 $objPHPExcel->getActiveSheet()->getStyle( "A$Rows:J$Rows")->applyFromArray($style_center);
$Rows=3;
$result = mysql_query(" SELECT C.POrderId,P.ProductId,S.OrderPO,C.DeliveryQty  AS Qty,P.cName,P.eCode,P.MainWeight,P.Price  
					  FROM $DataIn.ch1_deliverysheet  C 
					  LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=C.POrderId 
					  LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
					  WHERE C.Mid='$Id'",$link_id);
 if($myrow = mysql_fetch_array($result)){
	                $plResult = mysql_query("SELECT L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.POrderId,L.BoxSpec FROM $DataIn.ch1_deliverypacklist L WHERE L.Mid='$Id' ORDER BY L.Id ",$link_id);	
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
					         $pSql = mysql_query("SELECT S.OrderPO,P.cName,P.eCode,P.Description,P.MainWeight,P.Price 
						     FROM $DataIn.yw1_ordersheet S 
						     LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
						     WHERE S.POrderId='$POrderId' LIMIT 1",$link_id);
						     if ($pRows = mysql_fetch_array($pSql)){
										$OrderPO=$pRows["OrderPO"];
										$cName=$pRows["cName"];
										$eCode=$pRows["eCode"];
										$Description=$pRows["Description"];	
										$MainWeight=$pRows["MainWeight"];
										$Price=$pRows["Price"];
										}                                                     
							  if($BoxRow==0){//并箱非首行		//取相应的行号
			                           $objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
                                         $objPHPExcel->getActiveSheet()->mergeCells("A$Rows:B$Rows"); 
										$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$OrderPO");
										$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$cName");
  								        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$MainWeight");
  								        $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Price");
                                         $objPHPExcel->getActiveSheet()->mergeCells("G$Rows:J$Rows"); 
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
								if($Most!=$Small){$Most=$Small."-".$Most;}						
								        $objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(20);
    								    $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  								       $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$BoxQty");
										$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$Most");
										$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$OrderPO");
										$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$cName");
  								        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$MainWeight");
  								        $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Price");
     								    $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$BoxPcs");
       								    $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$FullQty");	
   								        $objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$WG");	
     								    $objPHPExcel->getActiveSheet()->getStyle("J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
										$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$BoxSpec");	
							for($n=1;$n<=$OrderNum;$n++){
									if($rowArray[$n]==$POrderId){
										$theOrderNumRow=$n*6-3;
										}
									}
								$k=$j-1;						
								}
							$j++;$Rows++;
						}while ($plRows = mysql_fetch_array($plResult));
				}
	}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=DeliveryBill.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>