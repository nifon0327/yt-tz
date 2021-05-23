<?php
	
	include "../basic/chksession.php";
	include "../basic/parameter.inc";
	//include "../model/modelhead.php";
	
	$Ids = implode(",", $checkid);
	if($CompanyId!="")
	{
		$ClientSTR="and M.CompanyId=\"$CompanyId\" AND S.Id IN ($Ids)";
		$OrderBY="order by  M.CompanyId,M.OrderDate ASC,M.Id DESC";
	}
	else
	{
		$ClientSTR=" AND S.Id IN ($Ids)";
		$OrderBY="order by M.CompanyId,M.OrderDate ASC";
	}
	
	/** Include PHPExcel */
	require_once '../plugins/PHPExcel/Classes/PHPExcel.php';
	// Create new PHPExcel object
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
							
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(100);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(45);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(100);
	

	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '出货日期');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO#');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice编号');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', '产品属性');	
	$objPHPExcel->getActiveSheet()->setCellValue('E1', '中文名');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Product Code');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', '售价');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', '数量');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', '金额');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', '出货方式');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', '报价规则');
	
	$objPHPExcel->getActiveSheet()->getStyle( 'A1:K1')->applyFromArray($style_center);
	$Rows=2;

	$mySql="SELECT M.Date,M.InvoiceNO,S.Id,S.Mid,S.POrderId,S.ProductId,S.Qty,S.Price,S.Type,S.YandN,P.cName,P.eCode,P.TestStandard,P.buySign,P.bjRemark,U.Name AS Unit,YS.OrderPO,YS.PackRemark,YS.DeliveryDate,YS.ShipType,E.Leadtime  ,YM.ClientOrder,YS.dcRemark,YM.OrderDate 
			FROM $DataIn.ch1_shipsheet S
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			LEFT JOIN $DataIn.yw1_ordersheet YS ON YS.POrderId=S.POrderId
			LEFT JOIN $DataIn.yw1_ordermain YM ON YM.OrderNumber=YS.OrderNumber
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
			LEFT JOIN $DataIn.yw3_pisheet E ON E.oId=YS.Id
			WHERE S.Type='1' 
			$ClientSTR 
			ORDER BY M.Date DESC";
	$i=0;
	$over5day = 0;
	$overIn5day = 0;
	$under = 0;
	
	$myResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($myResult))
	{
		$m=1;
		$thisBuyRMB=0;
		$theDefaultColor=$DefaultBgColor;
		$OrderPO=$myRow["OrderPO"];
		
		$InvoiceNO=$myRow["InvoiceNO"];
		
		$Id=$myRow["Id"];
		$ProductId=$myRow["ProductId"];
		$cName=$myRow["cName"];
		$eCode=$myRow["eCode"];			
		$TestStandard=$myRow["TestStandard"];
		
		$buySign=$myRow["buySign"];
        switch($buySign){
               case "0":  $buySign="";break;
               case "1":  $buySign="自购";break;
               case "2":  $buySign="代购";break;
               case "3":  $buySign="客供";break;
            }		
		$bjRemark=$myRow["bjRemark"];
		
		$POrderId=$myRow["POrderId"];
		$Qty=$myRow["Qty"];
		$Price=$myRow["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$sumAmount=$sumAmount+$Amount;
		$PackRemark=$myRow["PackRemark"];
        $dcRemark=$myRow["dcRemark"]==""?"&nbsp;":$myRow["dcRemark"];
		$DeliveryDate=$myRow["DeliveryDate"]=="0000-00-00"?"":$myRow["DeliveryDate"];
		$ShipType=$myRow["ShipType"];
		
		$ShipName="";
		$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Id='$ShipType' ",$link_id);
		  if($TypeRow = mysql_fetch_array($shipTypeResult)){
			  $ShipName=$TypeRow["Name"];
		  }		

		
		$Date=$myRow["Date"];
        $ClientOrder=$myRow["ClientOrder"];
		
		$Estate=$myRow["Estate"];
		$Locks=$myRow["Locks"];		
		$sumQty=$sumQty+$Qty;
		
		$Leadtime=$myRow["Leadtime"]; //PI交期
        
        $setColor = "0"; 
        if ($Leadtime!="" && strtotime($Leadtime)>0)
        {
        	$diffday=(strtotime($Date)-strtotime($Leadtime))/3600/24;
            if ($diffday<=0)
            {
            	if ($diffday<-5)
            	{
                	$setColor = "1";
                	//$under++;
                }
                $diffday="⬆" . abs($diffday) . "天";
                $under++;
            }
            else
            {
            	if ($diffday>5)
            	{ 
                	$setColor = "2";
                	$over5day++;
                }
                else
                {
	                $overIn5day++;
                }
                $diffday="⬇" . abs($diffday) . "天";
               
            }
        }          
        else
        {
        	$diffday=""; 
            $Leadtime=$Leadtime==""?"":$Leadtime;
            $under++;
        }
		
		$OrderDate=$myRow["OrderDate"];
        if ($OrderDate!="" && strtotime($OrderDate)>0)
        {
        	$cycleDay=((strtotime($Date)-strtotime($OrderDate))/3600/24)."天";
        }          
        else
        {
            $cycleDay=""; 
		}
		
		
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', '出货日期');
	$objPHPExcel->getActiveSheet()->setCellValue('B1', 'PO#');
	$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Invoice编号');
	$objPHPExcel->getActiveSheet()->setCellValue('D1', '产品属性');	
	$objPHPExcel->getActiveSheet()->setCellValue('E1', '中文名');
	$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Product Code');
	$objPHPExcel->getActiveSheet()->setCellValue('G1', '售价');
	$objPHPExcel->getActiveSheet()->setCellValue('H1', '数量');
	$objPHPExcel->getActiveSheet()->setCellValue('I1', '金额');
	$objPHPExcel->getActiveSheet()->setCellValue('J1', '出货方式');
	$objPHPExcel->getActiveSheet()->setCellValue('K1', '报价规则');
	
		
		$objPHPExcel->getActiveSheet()->getRowDimension('$Rows')->setRowHeight(20);
        $objPHPExcel->getActiveSheet()->getStyle("A$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$Date");
        		
        $objPHPExcel->getActiveSheet()->getStyle("B$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$OrderPO");
		
        $objPHPExcel->getActiveSheet()->getStyle("C$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$InvoiceNO");
		
		
        $objPHPExcel->getActiveSheet()->getStyle("D$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$buySign");
		
		
        $objPHPExcel->getActiveSheet()->getStyle("E$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$cName");
		
		
        $objPHPExcel->getActiveSheet()->getStyle("F$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$eCode");
		
        $objPHPExcel->getActiveSheet()->getStyle("G$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$Price");
		
        $objPHPExcel->getActiveSheet()->getStyle("H$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$Qty");
		
		$objPHPExcel->getActiveSheet()->getStyle("I$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Amount");
		
		$objPHPExcel->getActiveSheet()->getStyle("J$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$ShipName");

		$objPHPExcel->getActiveSheet()->getStyle("K$Rows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$bjRemark");
		
		
		$i++; 
		$Rows++;
		
	}
	
	$totleRows = $Rows+1;
	$objPHPExcel->getActiveSheet()->getRowDimension('$totleRows')->setRowHeight(30);
	$objPHPExcel->getActiveSheet()->getStyle("A$totleRows")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->setCellValue("A$totleRows", "合计:$i");
	

	
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename=OrderStatus.xlsx');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;

	
?>