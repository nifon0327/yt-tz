<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';


$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
$IdStr=$Ids!=""?" AND A.Id IN ($Ids)":"";      

$objPHPExcel = new PHPExcel();

$style_left= array( 
    'borders' => array( 
        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
       'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN) 
        ),
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
      'borders' => array( 
        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
       'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN) 
        ),
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
      'borders' => array( 
        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
       'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN) 
        ),
         'font'    => array (
                         'size'      => 14
          ),
        'alignment' => array(
          'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
          'wrap'       => true
        )
  );

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '配件ID');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '配件名称');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '单价');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '采购');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '供应商');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:F1')->applyFromArray($style_center);


$Rows=2;
$mySql="SELECT A.StuffId,A.StuffCname,A.Price,A.CostPrice,A.SendFloor,E.Forshort,C.Name,A.Date,D.Name AS UnitName,B.CompanyId
	FROM $DataIn.stuffdata A 
	LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId 
	LEFT JOIN $DataIn.staffmain C ON C.Number=B.BuyerId 
	LEFT JOIN $DataIn.stuffunit D ON D.Id=A.Unit
	LEFT JOIN $DataIn.trade_object E ON E.CompanyId=B.CompanyId  AND  E.ObjectSign IN (1,3)
	LEFT JOIN $DataIn.ck9_stocksheet H ON H.StuffId=A.StuffId
	WHERE 1  $IdStr  ";
   
 $myResult = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)){
	$i=1;
	do{

        $Id=$myRow["Id"];		
		$StuffId=$myRow["StuffId"];
		$StuffCname=$myRow["StuffCname"];
		$Price=$myRow["Price"];
		$Forshort=$myRow["Forshort"];
		$Name=$myRow["Name"];

        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$StuffId");
		
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$StuffCname");
		$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Price");
		
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Name");
		
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Forshort");

	$i++; $Rows++;
	}while ($myRow = mysql_fetch_array($myResult));
 }

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Stuffdata.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>

