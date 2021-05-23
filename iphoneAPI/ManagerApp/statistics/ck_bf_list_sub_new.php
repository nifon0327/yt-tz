<?php 
//报废明细未处理

$mySql="SELECT SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,C.PreChar 
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
WHERE B.Estate=3 AND DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth'";	

 $myResult = mysql_query($mySql,$link_id);
 while($myRow = mysql_fetch_array($myResult)) 
{
      if($myRow["Qty"]<=0) break;
      
       $sumQty = number_format($myRow["Qty"]);
       $sumAmount=number_format($myRow["Amount"]);
       $PreChar=$myRow["PreChar"];
       
       $totalArray=array(
				                      "Title"=>array("Text"=>"未处理","Color"=>"#FF0000","Margin"=>"6,0,0,0"),
				                      "Col2"=>array("Text"=>"$sumQty","Margin"=>"-40,0,0,0"),
				                      "Col3"=>array("Text"=>"¥$sumAmount")
				                   );  
		$dataArray[]=array("Tag"=>"Total","onTap"=>"2","hidden"=>"$hiddenSign","data"=>$totalArray); 
		
		$orderResult=mysql_query("
SELECT B.Id,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price*C.Rate) AS Amount  
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Operator 
WHERE  B.Estate in (3,2,1) AND  DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth' ORDER BY B.Date",$link_id);
		    while($orderRow = mysql_fetch_array($orderResult)) {
		             $Id=$orderRow["Id"];
			         $StuffId=$orderRow["StuffId"];
			         $StuffCname=$orderRow["StuffCname"];
			         $Remark=$orderRow["Remark"];
			         $RemarkDate=$orderRow["Date"];
			         $RemarkOperator=$orderRow["Operator"];
			         
			         $RemarkDate=$APP_FACTORY_CHECK==true?'':$RemarkDate;
			         
			        $Estate=$orderRow["Estate"];
			        //$Estate=$Estate==1?2:$Estate;
                    $Qty=$orderRow["Qty"];
                    $Price=$orderRow["Price"];
                    $Amount=number_format($Qty*$Price,2);
                    $Picture = $orderRow["Picture"];
                    $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
                    include "submodel/stuffname_color.php";
                    
                  $Qty=number_format($Qty);
                  $Price=number_format($Price,2);
                  $tempArray=array(
                   "Id"=>"$Id|$StuffId",'has2'=>"1",
                   "RowSet"=>array("bgColor"=>"$rowColor"),
                   "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
                   "Col1"=>array("Text"=>"$Qty","IconType"=>"20"),
                   "Col3"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$PreChar$Amount"),
//                    "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                   "rIcon"=>"estate_$Estate",
               );
              // $onEdit=$Estate==0?3:0;
               $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Picture","Args"=>"$ImagePath"),"onEdit"=>"$Estate","data"=>$tempArray,'rmk'=>"$Remark",
                'top_right'=>array("Text"=>$Estate==2?"退回":( "待审核" ),
                              				"Align"=>"M",
                              				"bg_color"=>"#FF0000",
                              				"Color"=>"#FFFFFF","Frame"=>"292,0,28,10",
                              				"FontSize"=>"8" ),
);
               
               
                  
                  $dataArray[]=array("Tag"=>"remark1",
						     	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n\t",
						   	"Recorder" => "$RemarkDate",
						   	"anti_oper"=>"$RemarkOperator",
						   	"headline"=>"报废原因：",
						   	
						   	'left_sper'=>"0","margin_left"=>"20"
						   	);

               
               
	   }   
 }
?>