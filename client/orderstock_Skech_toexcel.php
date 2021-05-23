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
    
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Product Code');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Price');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Qty');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'DeliveredQty');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'BalanceQty');
    
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->applyFromArray($style_center);
    $Rows=2;

    $mySql="SELECT * FROM (
                SELECT  P.Price, P.cName,P.eCode,P.TestStandard,P.ProductId,C.Forshort,SUM(S.Qty) AS ShipQty,IFNULL(SUM(D.DeliveryQty),0) AS DeliveryQty
                FROM  $DataIn.ch1_shipsheet S 
                LEFT JOIN ( 
                     SELECT POrderId,SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet  WHERE 1 GROUP BY  POrderId
                    ) D ON D.POrderId=S.POrderId
                    LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=S.Mid
                    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=SM.CompanyId 
                    LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=SM.Id
                    LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId  
                WHERE 1 AND O.Id IS NOT NULL  AND  P.ProductId!='' $CompanySTR   $SearchRows GROUP BY P.ProductId )  A   WHERE  1 and A.ShipQty>A.DeliveryQty AND A.ProductId in ($Ids)";
    $myResult = mysql_query($mySql." $PageSTR",$link_id);
    if($myRow = mysql_fetch_array($myResult)){
        $SumQty=0;$SumQty1=0;$SumQty2=0; $SumWaitingQty=0;$SumPreserveQty=0;
        do{
            $m=1;
            $ProductId=$myRow["ProductId"];
            $Forshort=$myRow["Forshort"];
            $Price=$myRow["Price"];
            $cName=$myRow["cName"];
            $eCode=$myRow["eCode"];
            $ProductId=$myRow["ProductId"];
            $TestStandard=$myRow["TestStandard"];
            $eCode=$myRow["eCode"];
            
            $eCode=$eCode==""?"&nbsp;":$eCode;
            $Operator=$myRow["Operator"];
            $ShipQty=$myRow["ShipQty"];
            $Amount=sprintf("%.2f",$Price*$ShipQty);    
            $DeliveryQty=$myRow["DeliveryQty"];
            $SumQty1+=$DeliveryQty;
           //*********************************等到发货和预留数量
            $PreserveReslut=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty),0) AS PreserveQty FROM $DataIn.skech_deliverysheet   S
            LEFT JOIN $DataIn.skech_deliverymain M ON M.Id=S.Mid
            LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
            WHERE Y.ProductId=$ProductId AND S.Type=2  AND M.Estate>0 AND M.CompanyId=$myCompanyId",$link_id));
            $PreserveQty=$PreserveReslut["PreserveQty"];
            $SumPreserveQty+=$PreserveQty;

            $unDeQty=$ShipQty-$DeliveryQty-$PreserveQty;
            $SumQty2+=$unDeQty;
            $SumQty=$SumQty1+$SumQty2;
            $thisProductDeliverStr="<input type='hidden' id='ProductId$i' name='ProductId$i' value='$ProductId'>";

            
            $objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
            $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$ProductId");
        
            $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$eCode");
        
            $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$Price");
                
            $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$ShipQty");
        
            $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$DeliveryQty");
        
            $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$unDeQty");
            $Rows++;
        }while ($myRow = mysql_fetch_array($myResult));
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=OrderStatus.xlsx');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
?>