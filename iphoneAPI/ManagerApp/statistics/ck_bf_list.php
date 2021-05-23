<?php 
//报废明细
$today=date("Y-m-d");                      
 include "../../basic/downloadFileIP.php";

 $dataArray=array();$hiddenSign=0;
 include "ck_bf_list_sub.php";
 
$mySql="SELECT T.TypeId,T.TypeName,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,C.PreChar 
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN  $DataIn.stufftype  T ON T.TypeId=D.TypeId 
WHERE DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth'  GROUP BY T.TypeId ORDER BY Amount DESC";	

 $myResult = mysql_query($mySql,$link_id);
 $hiddenSign=1;
 while($myRow = mysql_fetch_array($myResult)) 
{
       $TypeId=$myRow["TypeId"];
       $TypeName=$myRow["TypeName"];
       $sumQty = number_format($myRow["Qty"]);
       $sumAmount=number_format($myRow["Amount"]);
       $PreChar=$myRow["PreChar"];
       
      $extdataArray=array();		
		$orderResult=mysql_query("
SELECT B.Id,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price*C.Rate) AS Amount  
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Operator 
WHERE 1 AND DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth' AND D.TypeId='$TypeId' ORDER BY Estate DESC",$link_id);
		    while($orderRow = mysql_fetch_array($orderResult)) {
		             $Id=$orderRow["Id"];
			         $StuffId=$orderRow["StuffId"];
			         $StuffCname=$orderRow["StuffCname"];
			         $Remark=$orderRow["Remark"];
			         $RemarkDate=$orderRow["Date"];
			         $RemarkOperator=$orderRow["Operator"];
			         
			        $Estate=$orderRow["Estate"];
			        $Estate=$Estate==1?2:$Estate;
                    $Qty=$orderRow["Qty"];
                    $Price=$orderRow["Price"];
                    $Amount=number_format($Qty*$Price,2);
                    $Picture = $orderRow["Picture"];
                    $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
                    include "submodel/stuffname_color.php";
                    
                  $Qty=number_format($Qty);
                  $Price=number_format($Price,2);
                  $tempArray=array(
                   "Id"=>"$Id|$StuffId",
                   "RowSet"=>array("bgColor"=>"$rowColor"),
                   "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
                   "Col1"=>array("Text"=>"$Qty","IconType"=>"20"),
                   "Col3"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$PreChar$Amount"),
                   "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                   "rIcon"=>"estate_$Estate",
               );
               $onEdit=$Estate==3?3:0;
               $extdataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Picture","Args"=>"$ImagePath"),"onEdit"=>"$onEdit","data"=>$tempArray);
               
                $RemarkResult=mysql_query("SELECT A.Date,A.Remark,M.Name   
		                                FROM  $DataIn.ck8_bfremark  A
		                                LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
										WHERE  A.Mid='$Id' ORDER BY A.Date DESC,A.Id DESC LIMIT 1",$link_id);
					 if($RemarkRow = mysql_fetch_array($RemarkResult)){
					         $RemarkDate=$RemarkRow["Date"];
					         $RemarkDate=GetDateTimeOutString($RemarkDate,'');
					         $tempArray=array(
		                       "Title"=>array("Text"=>$RemarkRow["Remark"]),
		                       "Col1"=> array("Text"=>$RemarkRow["Name"]),
		                       "Col3"=>array("Text"=>$RemarkDate)
		                   );
		                   $extdataArray[]=array("Tag"=>"Remark","data"=>$tempArray); 
					 } 
	   } 
	   
	    $totalArray=array(
				                      "Title"=>array("Text"=>"$TypeName","Color"=>"#0066FF"),
				                      "Col2"=>array("Text"=>"$sumQty","Margin"=>"-40,0,0,0"),
				                      "Col3"=>array("Text"=>"¥$sumAmount")
				                   );  
		$dataArray[]=array("Tag"=>"Total","onTap"=>"1","hidden"=>"$hiddenSign","data"=>$totalArray,"extList"=>$extdataArray); 
  
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>