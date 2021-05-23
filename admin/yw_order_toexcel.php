<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
if($CompanyId!=""){
	$ClientSTR="and M.CompanyId='$CompanyId' AND S.Id IN ($Ids)";
	$OrderBY="order by  M.CompanyId,M.OrderDate ASC,M.Id DESC";
	}
else{
	$ClientSTR=" AND S.Id IN ($Ids)";
	$OrderBY="order by M.CompanyId,M.OrderDate ASC";
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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(40);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A1', '订单流水号');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO#');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Product Code');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Description');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Remark');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Qty');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Price');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Amount');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '库存数量');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '出货方式');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '交期');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '下单日期');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '产品名称');
  
 $objPHPExcel->getActiveSheet()->getStyle( 'A1:M1')->applyFromArray($style_center);
$Rows=2;
$mySql="SELECT S.POrderId,S.OrderPO,M.OrderDate,S.ShipType,S.Qty,S.Price,S.PackRemark,
P.cName,P.ProductId,P.eCode,P.Unit,P.Description,S.POrderId,PI.Leadweek,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime 
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
where 1 $ClientSTR  $SearchRows and S.Estate!='0' $OrderBY";
$result = mysql_query($mySql,$link_id);
 if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	$SUMAmount = 0 ;
	do{
	 	$POrderId=$myrow["POrderId"];
	 	$ProductId=$myrow["ProductId"];
	 	$OrderPO=$myrow["OrderPO"];
		$OrderDate=$myrow["OrderDate"];
		$eCode=$myrow["eCode"];
		$Description=$myrow["Description"];
	  	$Qty=$myrow["Qty"];
		$Price=$myrow["Price"];
		$Amount = $Price * $Qty;
		$ShipType=$myrow["ShipType"];
        $POrderId = $myrow["POrderId"];
	    if (strlen(trim($ShipType))>0){
	   	    $shipTypeResult = mysql_fetch_array(mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Id='$ShipType'",$link_id));
       	    $ShipTypeStr=$shipTypeResult["Name"];
          }
        else  $ShipTypeStr="";

		$cName=$myrow["cName"];
		$char_change = array(
                    '<'=>'&lt;',
                    '>'=>'&gt;'
                    );
       $cName=strtr($cName,$char_change);  

       $Leadweek=$myrow["Leadweek"];
       $Leadtime=$myrow["Leadtime"];
       
       if($Leadweek ==""){
	       $Leadtime=str_replace("*", "", $Leadtime);
	       $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
           $Leadweek=$dateResult["PIWeek"];
       }
       
       $week=substr($Leadweek, 4,2);
       $weekName="Week " . $week;
       
       
       //库存数量
       
        $CheckrkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS rkQty 
		FROM $DataIn.yw1_orderrk R 
		WHERE R.POrderId='$POrderId' AND R.ProductId = '$ProductId' ",$link_id));
		$rkQty=$CheckrkQty["rkQty"];
		
        $CheckShipQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS shipQty 
		FROM $DataIn.ch1_shipsheet  
		WHERE POrderId='$POrderId'",$link_id));
		$ShipQty = $CheckShipQty["shipQty"]; //已出数量
		
		$tStockQty = $rkQty-$ShipQty;
		$tStockQty = $tStockQty==0?"":$tStockQty;
		
       
       

        //获取英文注释
        $enRemark="";
        $RemarkResult=mysql_query("SELECT Remark FROM $DataIn.yw2_orderremark WHERE POrderId='$POrderId' AND Type=1 ORDER BY Id DESC LIMIT 1",$link_id);
        if($RemarkRow=mysql_fetch_array($RemarkResult)){
           $enRemark=$RemarkRow["Remark"];
        }

		$SUMQTY=$SUMQTY+$Qty;
		$SUMAmount += $Amount;
		
     
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$POrderId");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$OrderPO");
        $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$eCode");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Description");
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$enRemark");
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Qty");
        $objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Price");
        $objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Amount");
        $objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$tStockQty");
        $objPHPExcel->getActiveSheet()->getStyle("J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$ShipTypeStr");
        $objPHPExcel->getActiveSheet()->getStyle("K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$weekName");
        $objPHPExcel->getActiveSheet()->getStyle("L$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$OrderDate");
		$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "$cName");

		$i++; $Rows++;
		}while ($myrow = mysql_fetch_array($result));
	}
$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(30);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Total: ");
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:D$Rows"); 
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$SUMQTY");
$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "");
$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$SUMAmount");
$objPHPExcel->getActiveSheet()->mergeCells("H$Rows:M$Rows"); 

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=OrderStatus.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>