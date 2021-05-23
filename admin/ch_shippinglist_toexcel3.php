<?php 
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_toexcel";
$_SESSION["nowWebPage"]=$nowWebPage;
$InvoiceSql="SELECT M.InvoiceNO FROM $DataIn.ch1_shipmain M WHERE M.Id='$Id'";
$InvoiceResult=mysql_query($InvoiceSql,$link_id);
$InvoiceNO=mysql_result($InvoiceResult,0,"InvoiceNO");



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
  
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20); 
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);  



$Rows++;



$result = mysql_query("SELECT * FROM (
                      SELECT L.Id,C.POrderId,C.ProductId,S.OrderPO,S.Qty,P.cName,P.eCode,P.MainWeight,P.Price  
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
									S.OrderPO,P.cName,P.eCode,P.Description,P.MainWeight,P.Price 
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
									break;
								case 2:	//样品
									$sSql = mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE SampId='$POrderId'",$link_id);
									if ($sRows = mysql_fetch_array($sSql)){
										$OrderPO="&nbsp;";
										$cName=$sRows["SampName"];
										$eCode="";
										$Description=$sRows["Description"];
										$MainWeight=0;
										$Price=0;
										}		
									break;
								}
                                                       
							$BoxRowSTR=$BoxRow>1?"rowspan=$BoxRow":"";//检查是否合并行
							if($BoxRow==0){//并箱非首行
							     
							
								//11111							
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

								////222222	
								//读取行号								
								for($n=1;$n<=$OrderNum;$n++){
									if($rowArray[$n]==$POrderId){
										$theOrderNumRow=$n*6-3;
										}
									}
								//用javascript 写入数据
								$k=$j-1;						
								}
							$j++;
						}while ($plRows = mysql_fetch_array($plResult));
					}

	}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=InvoiceNO.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>