<?php   
include "../basic/chksession.php" ;
include "../basic/parameter.inc";

/** Include PHPExcel */
require_once '../plugins/PHPExcel/Classes/PHPExcel.php';

$ClientSign=0;
if($myCompanyId!=""){
     $IdArray=explode("^^",$tempIds);
     $Lens=count($IdArray);
     for($i=0;$i<$Lens;$i++){
         	$Id=$IdArray[$i];
	       if($Id!=""){
		         $Ids=$Ids==""?$Id:$Ids.",".$Id;
		      }
        }
      $IdStr=$tempIds!=""?" AND P.Id IN ($Ids)":"";
  
      switch($myCompanyId){
                 case 1004:
                 case 1059:
                 case 1072:
                           $ClientSTR=" and (P.CompanyId='1004' OR P.CompanyId='1059' OR P.CompanyId='1072')";//CEL
                           $ClientSign=1;
                 break;
                case 1049:
                     $ClientSTR=" and P.CompanyId IN (1049)";//CG,CG-ASIA
                     $ClientSign=2;
                  break;
                case 1083:
                     $ClientSTR=" and P.CompanyId IN (1083)";
                     $ClientSign=2;
                break;
                case 100262:
		        case 100241:
                     $ClientSTR=" and P.CompanyId IN (100262,100241)";
                     $ClientSign=2;
                break;
                 default:
                    $ClientSTR=" and P.CompanyId IN ($myCompanyId)";
                   break;
             }
      $mySql="SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,
      P.Remark,P.PackingUnit,P.Code,P.productsize,P.MainWeight,P.Weight
      FROM $DataIn.productdata P
      WHERE 1 AND P.Estate=1 $ClientSTR $IdStr order by Estate DESC,Id DESC ";
}
else 	{
           $mySql="";
           $myCompanyId="aaaaa";
            }

//$Rows=@mysql_num_rows($result)+10;//行数
//$Cols=10;//列数

 // Create new PHPExcel object
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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'No.');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Chinese');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Product Code');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Price');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'P_Weight');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Weight');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Unit/Carton');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'ProductSize');
$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Width');
$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Longth');
$objPHPExcel->getActiveSheet()->setCellValue('K1', 'Heighth');
if($ClientSign==1){
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Est.Leadtime');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Order History');
		$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Date of Latest Order');
		$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Supplier Rating');	 
		$objPHPExcel->getActiveSheet()->setCellValue('P1', 'ProviderInfo');	 
}
else {
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'BoxSpec');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', 'Volume');
		$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Order History');
		$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Date of Latest Order');
}		
 $objPHPExcel->getActiveSheet()->getStyle( "A1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "B1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "C1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "D1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "E1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "F1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "G1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "H1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "I1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "J1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "K1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "L1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "M1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "N1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "O1")->applyFromArray($style_center);
 $objPHPExcel->getActiveSheet()->getStyle( "P1")->applyFromArray($style_center);
 $sRows=2;
 $Rows=$sRows;
 $result = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($result)){
	$i=1;
	do{
	       $ProductId=$myRow["ProductId"];
		   $cName=$myRow["cName"];
		   $eCode=$myRow["eCode"];
		   $productsize=$myRow["productsize"];
		   $Price=sprintf("%.2f",$myRow["Price"]);
		   $MainWeight = $myRow["MainWeight"];
		   $Weight = $myRow["Weight"];
			//检查装箱数量
		$checkNumbers=mysql_fetch_array(mysql_query("SELECT IFNULL(N.Relation,0) AS Relation,S.Spec
		FROM $DataIn.pands N
		LEFT JOIN $DataIn.stuffdata S ON S.StuffId=N.StuffId
		WHERE N.ProductId=$ProductId AND S.TypeId='9040'",$link_id));
		$BoxNums=$checkNumbers["Relation"];
        $BoxSpec=$checkNumbers["Spec"];
		if($BoxNums!=0){
			   $BoxNumsArray=explode("/",$BoxNums);
			   $BoxNums=$BoxNumsArray[1];
			   }
		else{
			   $BoxNums="";
			   }
      if (substr_count($BoxSpec,"*")>0){
				     $Spec=explode("*",substr($BoxSpec,0,-2));
                                }else{
                                     $Spec=explode("×",substr($BoxSpec,0,-2));
                                   
                                }
       $ThisCube=$Spec[0]*$Spec[1]*$Spec[2];
       $ThisCube=sprintf("%.2f",$ThisCube/1000000);
       if($productsize!=''){
		    $productsize = str_replace("×", "x", $productsize); 
		    $productsize = str_replace("cm", "", $productsize); 
			$sizeArray = 	explode("x",$productsize); 
			$Width = $sizeArray[0];
			$Longth = $sizeArray[1];
			$Height = $sizeArray[2]==""?"":$sizeArray[2];
		}else{
			$Width =""; $Longth =""; $Height ="";
		}

		//订单总数
		$checkAllQty= mysql_fetch_array(mysql_query("SELECT count(*) AS Orders,SUM(S.Qty) AS AllQty 
		FROM $DataIn.yw1_ordersheet S
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		WHERE P.ProductId='$ProductId'",$link_id));
		$Orders=$checkAllQty["Orders"];
		//已出货数量
		$checkShipQty= mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'",$link_id));
		$ShipQtySum=$checkShipQty["ShipQty"];
		$ShipQtySum=$ShipQtySum."($Orders)";
		 //最后出货日期
         $MonthResult=mysql_fetch_array(mysql_query("SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,
             TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId 
             FROM $DataIn.ch1_shipmain M 
	         LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
            WHERE 1 AND  S.ProductId='$ProductId' ORDER BY M.Date DESC",$link_id));
		$Months=$MonthResult["Months"];
		$LastMonth=$MonthResult["LastMonth"];
		if($Months!=NULL){
               $LastShipMonth=$LastMonth;
			     }
		else{//没有出过货
			    $LastShipMonth="";
			    }
		//*******************交货期
		include "../model/subprogram/product_chjq.php";
         $JqAvg=str_replace("days","d",$JqAvg);
         $JqAvg=$JqAvg=="&nbsp;"?"":$JqAvg;
         $EstResult=mysql_fetch_array(mysql_query("SELECT Estleadtime FROM $DataIn.product_estleadtime WHERE ProductId='$ProductId'",$link_id));
         $EstLeadtime=$EstResult["Estleadtime"];
         $RatingResult=mysql_fetch_array(mysql_query("SELECT pj_times FROM $DataIn.product_pj WHERE ProductId='$ProductId'",$link_id));
         $pj_times=$RatingResult["pj_times"];

         $ProResult=mysql_fetch_array(mysql_query("SELECT ProInfo FROM $DataIn.product_proinfo WHERE ProductId='$ProductId' ",$link_id));
         $ProviderInfo=$ProResult["ProInfo"]; 
         
        $objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "$i");
		$objPHPExcel->getActiveSheet()->setCellValue("B$Rows", "$cName");
		$objPHPExcel->getActiveSheet()->setCellValue("C$Rows", "$eCode");
		$objPHPExcel->getActiveSheet()->setCellValue("D$Rows", "$Price");
		$objPHPExcel->getActiveSheet()->setCellValue("E$Rows", "$MainWeight");
		$objPHPExcel->getActiveSheet()->setCellValue("F$Rows", "$Weight");
		$objPHPExcel->getActiveSheet()->setCellValue("G$Rows", "$BoxNums");
		$objPHPExcel->getActiveSheet()->setCellValue("H$Rows", "$productsize");
		$objPHPExcel->getActiveSheet()->setCellValue("I$Rows", "$Width");
		$objPHPExcel->getActiveSheet()->setCellValue("J$Rows", "$Longth");
		$objPHPExcel->getActiveSheet()->setCellValue("K$Rows", "$Height");
		  
if($ClientSign==1){
	    $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$EstLeadtime");
		$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "$ShipQtySum");  
		$objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "$LastShipMonth"); 
		$objPHPExcel->getActiveSheet()->setCellValue("O$Rows", "$pj_times");   
		$objPHPExcel->getActiveSheet()->setCellValue("P$Rows", "$ProviderInfo");   
        }
else{
       $objPHPExcel->getActiveSheet()->setCellValue("L$Rows", "$BoxSpec");
		$objPHPExcel->getActiveSheet()->setCellValue("M$Rows", "$ThisCube");  
		$objPHPExcel->getActiveSheet()->setCellValue("N$Rows", "$ShipQtySum"); 
		$objPHPExcel->getActiveSheet()->setCellValue("O$Rows", "$LastShipMonth");   
		$objPHPExcel->getActiveSheet()->setCellValue("P$Rows", " ");   
        }
        
         $objPHPExcel->getActiveSheet()->getStyle( "A$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "B$Rows")->applyFromArray($style_left);
         $objPHPExcel->getActiveSheet()->getStyle( "C$Rows")->applyFromArray($style_left);
         $objPHPExcel->getActiveSheet()->getStyle( "D$Rows")->applyFromArray($style_right);
         $objPHPExcel->getActiveSheet()->getStyle( "E$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "F$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "G$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "H$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "I$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "J$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "K$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "L$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "M$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "N$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "O$Rows")->applyFromArray($style_center);
         $objPHPExcel->getActiveSheet()->getStyle( "P$Rows")->applyFromArray($style_center);
        $Rows++;
		$i++; 
		}while ($myRow = mysql_fetch_array($result));
		 $eRows=$Rows-1;
		 $objPHPExcel->getActiveSheet()->getRowDimension("$sRows:$eRows")->setRowHeight(20);
	}

if($ClientSign==1){
	$objPHPExcel->getActiveSheet()->getRowDimension("$Rows")->setRowHeight(30);
	$objPHPExcel->getActiveSheet()->setCellValue("A$Rows", "Supplier Rating : 1 is meaning *,2 is meaning ** ,3 is meaning ***");
	$objPHPExcel->getActiveSheet()->mergeCells("A$Rows:P$Rows"); 
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=ProductInfo.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
