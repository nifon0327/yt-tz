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
$IdStr=$Ids!=""?" AND P.Id IN ($Ids)":"";      

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
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(60);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(25);


$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '产品ID');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '中文名');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Product Code');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Price');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '单位');
$objPHPExcel->getActiveSheet()->setCellValue('G1', '产品库存');
$objPHPExcel->getActiveSheet()->setCellValue('H1', '装箱数量');
$objPHPExcel->getActiveSheet()->setCellValue('I1', '外箱条码');
$objPHPExcel->getActiveSheet()->setCellValue('J1', '产品备注');
$objPHPExcel->getActiveSheet()->setCellValue('K1', '所属分类');
$objPHPExcel->getActiveSheet()->setCellValue('L1', '报价规则');
$objPHPExcel->getActiveSheet()->setCellValue('M1', '产品尺寸');
$objPHPExcel->getActiveSheet()->getStyle( 'A1:M1')->applyFromArray($style_center);


$Rows=2;
$mySql="SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.Weight,P.MisWeight,P.CompanyId,P.Description,
P.Remark,P.pRemark,P.bjRemark,P.dzSign,P.productsize,P.TestStandard,P.Date,
P.Code,T.TypeName,C.Forshort,D.Rate,D.Symbol,D.PreChar,U.Name AS UnitName,
P.MainWeight,BG.Name AS bgName,S.tStockQty
FROM $DataIn.productdata P
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata D ON D.Id=C.Currency
LEFT JOIN $DataIn.taxtype BG ON BG.Id = P.taxtypeId
LEFT JOIN $DataIn.packingunit U ON U.Id = P.PackingUnit
LEFT JOIN $DataIn.productstock S ON S.ProductId = P.ProductId 
WHERE  1  $IdStr and P.Estate>0 ";
   
 $myResult = mysql_query($mySql,$link_id);
 if($myRow = mysql_fetch_array($myResult)){
	$i=1;
	$SumQty=0;
	do{

        $Id=$myRow["Id"];		
		$ProductId=$myRow["ProductId"];
		$Rate=$myRow["Rate"];
		$Symbol=$myRow["Symbol"];
		$Client=$myRow["Forshort"];
		$cName=$myRow["cName"];
		$PreChar=$myRow["PreChar"];
		$eCode=$myRow["eCode"];
		$Remark=$myRow["Remark"];
		$Price=$myRow["Price"];
		$UnitName=$myRow["UnitName"];
		$Code=$myRow["Code"];

		$bjRemark=$myRow["bjRemark"];
		$bgName=$myRow["bgName"];
		$tStockQty=$myRow["tStockQty"];
		$TypeName=$myRow["TypeName"];
		$productsize=$myRow["productsize"];
		//装箱数量
		$BoxResult = mysql_query("SELECT P.Relation 
		            FROM $DataIn.pands P 
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040'",$link_id);
		if($BoxRows = mysql_fetch_array($BoxResult)){
			$Relation=$BoxRows["Relation"];
			}
		if($Relation ==""){
			$BoxRows1 = mysql_fetch_array(mysql_query("SELECT D.BoxPcs FROM $DataIn.pands P 
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId  
						WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 ",$link_id));
		    $Relation=$BoxRows1["BoxPcs"]==0?"":$BoxRows1["BoxPcs"];
		}
		$RelationArray=explode("/",$Relation);
        $BoxPcs=$RelationArray[1]==""?$RelationArray[0]:$RelationArray[1];
		

        $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$ProductId");
		
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$cName");
		$objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$eCode");
		
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$Price");
		
		$objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$UnitName");
        $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$tStockQty");
		
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$BoxPcs");

        $objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Code");


		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$Remark");
		$objPHPExcel->getActiveSheet()->getStyle("K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$TypeName");

		$objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$bjRemark");
	
		$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "$productsize");

	$i++; $Rows++;
	}while ($myRow = mysql_fetch_array($myResult));
 }

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=Productdata.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>

