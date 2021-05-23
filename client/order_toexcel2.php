<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
if($myCompanyId!=""){
	switch($myCompanyId){
		case 1004:
		case 1059:
		case 1072:
		       $ClientSTR=" and (M.CompanyId='1004' OR M.CompanyId='1059'  OR M.CompanyId='1072') ";
		       break;
		case 1081:
		case 1002:
		case 1080:
		case 1065:
		       $ClientSTR=" and M.CompanyId in ('1081','1002','1080','1065')";
		       break;
	    default:
	         $ClientSTR="and M.CompanyId=\"$myCompanyId\"";
	        break;
	}
	$OrderBY="order by M.OrderDate desc";
}
else{
	    $ClientSTR="";
	    $OrderBY="order by M.CompanyId,M.OrderDate desc";
	}


/*
$Ids="";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
}
*/
$ClientSTR.=$Ids==""?"":" AND S.Id IN ($Ids)";

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
	
$result = mysql_query("SELECT S.OrderPO,M.OrderDate,S.ShipType,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.Unit,S.ShipType,P.Description,PI.Leadtime AS DeliveryDate 
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber  
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
where 1 $ClientSTR and S.Estate!='0' $OrderBY",$link_id);


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Document No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'No.');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Description');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'OutStanding Quantity');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Shipment date');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:E1')->applyFromArray($style_center);
$Rows=2;
$mySql="SELECT S.OrderPO,M.OrderDate,S.ProductId,S.ShipType,PI.Leadtime,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.Unit,P.Description 
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
where 1 $ClientSTR  $SearchRows and S.Estate!='0' $OrderBY";
$result = mysql_query($mySql,$link_id);
 if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{
	 	$OrderPO=$myrow["OrderPO"];
		$OrderDate=$myrow["OrderDate"];
		$eCode=$myrow["eCode"];
		$Description=$myrow["Description"];
		$ProductId=$myrow["ProductId"];
	  	$Qty=$myrow["Qty"];
		$ShipType=$myrow["ShipType"];
	   if (strlen(trim($ShipType))>0){
	   	    $shipTypeResult = mysql_fetch_array(mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Id=$ShipType",$link_id));
       	    $ShipTypeStr=$shipTypeResult["Name"];
          }
       else  $ShipTypeStr="";
		$Price="";
		$cName=$myrow["cName"];
       $char_change = array(
                    '<'=>'&lt;',
                    '>'=>'&gt;'
                    );
                $cName=strtr($cName,$char_change);  

        $DateSign=0;
        $Leadtime=$myrow["Leadtime"];
         if($Leadtime=="" || $Leadtime=="0000-00-00"){
                   $Leadtime="no delivery";
                 }
         else {
                    //$DeliveryDate=date("j/n/y" ,strtotime($DeliveryDate));
                     $Leadtime=str_replace("*", "", $Leadtime);
				      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
				      
			          $PIWeek=$dateResult["PIWeek"];
			          if ($PIWeek>0){
				          $week=substr($PIWeek, 4,2);
					      $Leadtime="Week " . $week;
				      }
                   }
		$SUMQTY=$SUMQTY+$Qty;
	   // $allDay=ceil((strtotime(date("Y-m-d"))-strtotime($OrderDate))/3600/24)."days";

     
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$OrderPO");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$eCode");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$Description");
		$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Qty");
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Leadtime");
		$i++; $Rows++;
		}while ($myrow = mysql_fetch_array($result));
	}
/*
//$Rows++;
$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(30);
$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Total: ");
$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:C$Rows"); 
$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$SUMQTY");
$objPHPExcel->getActiveSheet()->setCellValue("E$Rows",""); 
*/
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=OrderStatus.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
