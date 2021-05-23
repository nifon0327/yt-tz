<?php
  include_once "../model/MyDbHelper.php";
  include_once "../plugins/PHPExcel/Classes/PHPExcel.php";
  include_once "../plugins/PHPExcel/Classes/PHPExcel/IOFactory.php";
  include_once("../plugins/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php"); 
  $temPath = "purchase_order_tpl.xlsx";
  if(!file_exists($temPath)){
       die('模板不存在');
  }

  if(!isset($_GET['poid'])){
  	  die('非法操作');
  }
  
  if(empty($_GET['poid'])){
  	  die('非法操作');
  }

  $poid=$_GET['poid'];
  $posql="SELECT a.Id,a.CompanyId,a.PurchaseID,a.Date,b.Forshort,c.`Name` ,
             d.Address,d.Tel,t.Name as LinkName,T.Mobile,T.Email
            FROM nonbom6_cgmain a  
       LEFT JOIN   nonbom3_retailermain b on a.CompanyId=b.CompanyId
       LEFT JOIN   nonbom3_retailersheet d on b.CompanyId=d.CompanyId 
       LEFT JOIN staffmain c on c.Number=a.BuyerId
       LEFT JOIN nonbom3_retailerlink t ON t.CompanyId = b.CompanyId 
       where a.Id=$poid limit 1";
  $POMaster=row($posql,$link);
  $phpexcel =  PHPExcel_IOFactory::createReader("Excel2007")->load($temPath);
  $phpexcel->getSheet(0)->setCellValue('B3',$POMaster['Forshort']);
  $phpexcel->getSheet(0)->setCellValue('B5',$POMaster['LinkName']);
  $phpexcel->getSheet(0)->setCellValue('B6',$POMaster['Mobile']);
  $phpexcel->getSheet(0)->setCellValue('B7',$POMaster['Address']);
  $phpexcel->getSheet(0)->setCellValue('B8',$POMaster['Email']);
  $phpexcel->getSheet(0)->setCellValue('I3',$POMaster['PurchaseID']);
  $phpexcel->getSheet(0)->setCellValue('I5',$POMaster['Date']);
  $phpexcel->getSheet(0)->setCellValue('B33','编制：'.$POMaster['Name']);
  $phpexcel->getSheet(0)->setCellValue('B34','日期：'.$POMaster['Date']);
  $date=date("Y-m-d");
  $outputFileName = "PO_".$date."_".$POMaster['PurchaseID'].".xlsx";
  $objWriter = new PHPExcel_Writer_Excel2007($phpexcel);
  
  $detailsql=" SELECT  b.GoodsName,b.GoodSpec,
                       b.Unit,a.Qty,a.Price,  (a.Price*a.Qty) as amount, a.Remark,b.GoodSpec,GoodsName
                 FROM  nonbom6_cgsheet  a 
           INNER JOIN  nonbom4_goodsdata b on a.GoodsId=b.GoodsId  WHERE a.Mid=$poid";
  $PODetail=result($detailsql,$link);
  $index=12;
  foreach ($PODetail as $key => $PO) {
  	$rowindex=$index+$key;

  	$goodName=stripos($PO['GoodsName'],']')==true? substr($PO['GoodsName'],stripos($PO['GoodsName'],']')+1):$PO['GoodsName'];
  	 $phpexcel->getSheet(0)->setCellValue('A'.$rowindex,($key+1));
  	 $phpexcel->getSheet(0)->setCellValue('B'.$rowindex,$goodName);
  	 $phpexcel->getSheet(0)->setCellValue('C'.$rowindex,$PO['GoodSpec']);
  	 $phpexcel->getSheet(0)->setCellValue('D'.$rowindex,$PO['Unit']);
  	 $phpexcel->getSheet(0)->setCellValue('E'.$rowindex,$PO['Qty']);
  	 $phpexcel->getSheet(0)->setCellValue('F'.$rowindex,$PO['Price']);
  	 $phpexcel->getSheet(0)->setCellValue('G'.$rowindex, number_format($PO['amount'],2));
  }
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  header("Content-Type: application/download");
  header('Content-Disposition:inline;filename="'.$outputFileName.'"');
  header("Content-Transfer-Encoding: binary");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Pragma: no-cache");
  $objWriter->save('php://output');