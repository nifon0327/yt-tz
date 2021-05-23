<?php
	
	include "../basic/chksession.php";
	include "../basic/parameter.inc";
	//include "../model/modelhead.php";
	
	$Ids = implode(",", $checkid);
	
	require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
	
	$objPHPExcel = new PHPExcel();
	$style_left= array( 
         				'font' => array ('size'=> 14),
		 				'alignment' => array(
          							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
		  							'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		  							'wrap'       => true)
		  					);
  
	$style_center = array( 
						   'font'=> array ('size'=> 14),
						   'alignment' => array(
						   				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						   				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						   				'wrap'       => true)
						   	);
  
	$style_right = array( 'font'=> array ('size'=> 14),
        				'alignment' => array(
									'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
									'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER,
									'wrap' => true)
							);


	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '产品ID');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', '产品中文名');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Product Code');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Price');
	$objPHPExcel->getActiveSheet()->setCellValue('E1', '数量');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', '金额');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', '已提');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', '未提');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', '未提箱数');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', '存储位置');
	
	$objPHPExcel->getActiveSheet()->getStyle( 'A1:J1')->applyFromArray($style_center);
	$Rows=2;

	$mySql=" SELECT * FROM (
SELECT  P.Price, P.cName,P.eCode,P.TestStandard, P.ProductId,C.Forshort,SUM(S.Qty) AS ShipQty,IFNULL(SUM(D.DeliveryQty),0) AS DeliveryQty
FROM  $DataIn.ch1_shipsheet S 
LEFT JOIN ( 
           SELECT POrderId,SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet  WHERE 1 GROUP BY  POrderId
        ) D ON D.POrderId=S.POrderId
LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=S.Mid
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=SM.CompanyId 
LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=SM.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId  
WHERE O.Id IS NOT NULL AND P.ProductId!='' GROUP BY P.ProductId )  A  WHERE A.ProductId in ($Ids) Order by eCode";

	$myResult = mysql_query($mySql);
	while($myRow = mysql_fetch_array($myResult))
	{
		$ProductId=$myRow["ProductId"];	
		$Forshort=$myRow["Forshort"];
		$Price=$myRow["Price"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];
		
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
        $ShipQty=$myRow["ShipQty"];
        $Amount=sprintf("%.2f",$Price*$ShipQty);
		$DeliveryQty=$myRow["DeliveryQty"];
        $SumQty1+=$DeliveryQty;
		$unDeQty=$ShipQty-$DeliveryQty;

		//9040 外箱，未设外箱检查配件是否设置箱/pcs
		$Relation="";
		$PcsPerBox=0;
		$HaveBox=0;
		$RelationResult=mysql_fetch_array(mysql_query("SELECT A.Relation  FROM $DataIn.pands A
												   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
												   WHERE A.ProductId='$ProductId' AND D.TypeId=9040
												   ",$link_id));
		if($RelationResult){
			  $Relation=$RelationResult["Relation"];
		}

		 if($Relation!=""){
			
			$ARelation=explode("/",$Relation);
			if(count($ARelation)==1){
				$PcsPerBox=$ARelation[0];
			}
			else{
				$PcsPerBox=$ARelation[1];
			}
			
			$HaveBox=ceil($unDeQty/$PcsPerBox);
			$MainBoxs="$HaveBox";	
			
		 }	
		 else{	
            $BoxPcsResult=mysql_fetch_array(mysql_query("SELECT D.BoxPcs  FROM $DataIn.pands A
												   		 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
												   		 WHERE A.ProductId='$ProductId' AND D.BoxPcs>0 Limit 1",$link_id));
             $BoxPcs=$BoxPcsResult["BoxPcs"];
             if($BoxPcs>0){
            	$MainBoxs=ceil($unDeQty/$BoxPcs);
                        }
			    else $MainBoxs="";	
	     	 }
		 
		 $theunDeQty=$unDeQty;
         $SumQty2+=$unDeQty;
		 $CheckAdressResult=mysql_fetch_array(mysql_query("SELECT   * FROM $DataIn.product_ckadress WHERE ProductId=$ProductId",$link_id));
         $CKAdress=$CheckAdressResult["Adress"]==""?"":$CheckAdressResult["Adress"];
		 $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$ProductId");
        
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$cName");
		
		$objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$eCode");
				
        $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Price");
		
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$ShipQty");
		
        $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Amount");
		
        $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$DeliveryQty");
		
		$objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$unDeQty");
		
		$objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$MainBoxs");
		
		$objPHPExcel->getActiveSheet()->getStyle("J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$CKAdress");
		
		$i++; 
		$Rows++;
	}
	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=OrderStatus.xlsx');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;

	
?>