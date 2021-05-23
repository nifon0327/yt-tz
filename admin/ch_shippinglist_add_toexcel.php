<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
$ClientSTR="";
$ClientSTR.=$Ids==""?"":" AND SP.Id IN ($Ids)";
$ClientSTR.=$POrderId==""?"":" AND S.POrderId IN ($POrderId)";

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
 // Create new PHPExcel object
$objPHPExcel = new PHPExcel();
 $style_left= array( 
         'font'    => array (
                         'size'      => 12
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
$style_center = array( 
         'font'    => array (
                         'size'      => 12
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );
  
  $style_right = array( 
         'font'    => array (
                         'size'      => 12
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );

$objPHPExcel->getActiveSheet()->mergeCells("A1:M1");
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16) //字体大小
->setBold(true); //字体加粗
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A2', '序号');
$objPHPExcel->getActiveSheet()->setCellValue('B2', '项目');
$objPHPExcel->getActiveSheet()->setCellValue('C2', '楼栋');
$objPHPExcel->getActiveSheet()->setCellValue('D2', '楼层');
$objPHPExcel->getActiveSheet()->setCellValue('E2', '类型');
$objPHPExcel->getActiveSheet()->setCellValue('F2', '名称');
$objPHPExcel->getActiveSheet()->setCellValue('G2', '长');
$objPHPExcel->getActiveSheet()->setCellValue('H2', '宽');
$objPHPExcel->getActiveSheet()->setCellValue('I2', '厚');
$objPHPExcel->getActiveSheet()->setCellValue('J2', '方量');
$objPHPExcel->getActiveSheet()->setCellValue('K2', '重量');
$objPHPExcel->getActiveSheet()->setCellValue('L2', '库位');
$objPHPExcel->getActiveSheet()->setCellValue('M2', '备注');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:M2')->applyFromArray($style_center);
$Rows=3;

$mySql="SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,
	S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.dcRemark,S.SeatId,
	P.cName,P.eCode,P.TestStandard,
	SP.Id,SP.ShipType,SP.Qty AS thisQty,SP.OrderSign,K.tStockQty,PI.Leadtime,
	X.name as taxName,R.OrderPO as OutOrderPO,T.Forshort,PT.TypeName,TG.Length,TG.Width,TG.Thick,TG.CVol,TG.Weight,CS.InvoiceNO  
	FROM $DataIn.ch1_shipsplit SP  
    INNER JOIN $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	LEFT JOIN $DataIn.yw7_clientOutData OP ON OP.POrderId=S.POrderId AND OP.Sign=1
	LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN $DataIn.ch1_shipsheet SS ON P.ProductId=SS.ProductId 
    LEFT JOIN $DataIn.ch1_shipmain CS ON CS.Id=SS.Mid 
	LEFT JOIN $DataIn.producttype PT ON PT.TypeId = P.TypeId
	LEFT JOIN $DataIn.productstock K ON K.ProductId = P.ProductId
	LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
	LEFT JOIN $DataIn.trade_object T ON T.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.trade_drawing TG ON CONCAT_WS('-',TG.BuildingNo,TG.FloorNo,TG.CmptNo,TG.SN) = P.cName 
	WHERE   S.Estate>0   AND SP.Estate = '1' $ClientSTR AND K.tStockQty >= SP.Qty  
ORDER BY  S.POrderId";

$result = mysql_query($mySql,$link_id);
 if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	do{
	 	$OrderPO=$myrow["OrderPO"];
		$OrderDate=$myrow["OrderDate"];
		$cName=$myrow["cName"];
        $Field = explode("-", $cName);
        $Count = count($Field)-1;
        for ($j = 2; $j < $Count; $j++) {
            if ($j==2){
                $gjName = $Field[$j];
            }else {
                $gjName = $gjName . "-" . $Field[$j];
            }
        }
        $InvoiceNO = $myrow["InvoiceNO"];
		$eCode=$myrow["eCode"];
		$Description=$myrow["Description"];
        $SeatId=$myrow["SeatId"];
        $Qty=$myrow["Qty"];
        $ShipType=$myrow["ShipType"];
        $Forshort=$myrow["Forshort"];
        $TypeName = $myrow["TypeName"];
        $Length = $myrow["Length"];
        $Width = $myrow["Width"];
        $Thick = $myrow["Thick"];
        $CVol = $myrow["CVol"];
        $Weight = $myrow["Weight"];
	   if (strlen(trim($ShipType))>0){
	   	    $shipTypeResult = mysql_fetch_array(mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Id=$ShipType",$link_id));
       	    $ShipTypeStr=$shipTypeResult["Name"];
          }
       else  $ShipTypeStr="";

		$SUMQTY=$SUMQTY+$Qty;

		if ($i==1){
            $objPHPExcel->getActiveSheet()->setCellValue("A1", "出货单 - $InvoiceNO");
        }
     
        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$Forshort");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$Field[0]");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Field[1]");
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$TypeName");
        $objPHPExcel->getActiveSheet()->setCellValue("F$Rows","$gjName");
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Length");
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Width");
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Thick");
		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$CVol");
		$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$Weight");
		$objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$SeatId");
		$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "");
		$i++; $Rows++;
		}while ($myrow = mysql_fetch_array($result));
	}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=出货单-'.$InvoiceNO.'.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
