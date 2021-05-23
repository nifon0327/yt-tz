<?php 
//报废明细
$today=date("Y-m-d");                      
 include "../../basic/downloadFileIP.php";

 $dataArray=array();$hiddenSign=0;
 //include "ck_bf_list_sub_new.php";
 
$mySql="SELECT T.TypeId,T.TypeName,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,C.PreChar 
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN  $DataIn.stufftype  T ON T.TypeId=D.TypeId 
WHERE DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth' and B.Estate=0 GROUP BY T.TypeId ORDER BY Amount DESC";	

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
WHERE 1 AND DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth' AND D.TypeId='$TypeId' AND B.Estate=0 ORDER BY B.Date DESC",$link_id);
		    while($orderRow = mysql_fetch_array($orderResult)) {
		             $Id=$orderRow["Id"];
			         $StuffId=$orderRow["StuffId"];
			         $StuffCname=$orderRow["StuffCname"];
			         $Remark=$orderRow["Remark"];
			         $RemarkDate=$orderRow["Date"];
			         $RemarkOperator=$orderRow["Operator"];
			         
			         $RemarkDate=$APP_FACTORY_CHECK==true?'':$RemarkDate;
			         
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
                   "Id"=>"$Id|$StuffId",'has2'=>"1",
                   "RowSet"=>array("bgColor"=>"$rowColor"),
                   "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","Margin"=>"0,0,-15,0"),
                   "Col1"=>array("Text"=>"$Qty","IconType"=>"20"),
                   "Col3"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$PreChar$Amount"),
//                    "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                   //"rIcon"=>"estate_$Estate",
               );
               $onEdit=$Estate==3?3:0;
               $extdataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"static","Args"=>"$StuffId|$ImagePath","Title"=>"$StuffCname"),"onEdit"=>"$onEdit","data"=>$tempArray,'rmk'=>"$Remark");
               
               
                   $RemarkResult=mysql_query("SELECT A.Date,A.Remark,M.Name   
		                                FROM  $DataIn.ck8_bfremark  A
		                                LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
										WHERE  A.Mid='$Id' ORDER BY A.Date DESC,A.Id DESC LIMIT 1",$link_id);
										 $ChuliDate=
					         $ChuliName = 
					         $ChuliRm ="";
					      
					 if($RemarkRow = mysql_fetch_array($RemarkResult)){
					         $ChuliDate=$RemarkRow["Date"];
					         $ChuliName = $RemarkRow["Name"];
					         $ChuliRm = $RemarkRow["Remark"];
					      
		                  // $extdataArray[]=array("Tag"=>"Remark","data"=>$tempArray); 
					 }
					 $ChuliDate=$APP_FACTORY_CHECK==true?'':$ChuliDate;
					 
                  $extdataArray[]=array("Tag"=>"remark1",
						     	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n",
						   	"Recorder" => "$RemarkDate",
						   	"anti_oper"=>"$RemarkOperator",
						   	"headline"=>"报废原因：",
						   	"reason"=>"",
						   	"Files"=>"$ChuliRm",
						   	 "pad_attr"=>array(array("处理备注：\n","#43B4E3","11"),array("$ChuliRm","#888888","11")),"needReason"=>$ChuliRm==""?"0":"1",
						   	 "reason_oper"=>"$ChuliName","reason_time"=>"$ChuliDate",
						   	'left_sper'=>"0","margin_left"=>"20"
						   	);
               
/*
               
*/ 
	   } 
	   
	    $totalArray=array(
				                      "Title"=>array("Text"=>"$TypeName","Color"=>"#43B4E3","Margin"=>"6,0,0,0"),
				                      "Col2"=>array("Text"=>"$sumQty","Margin"=>"-40,0,0,0"),
				                      "Col3"=>array("Text"=>"¥$sumAmount")
				                   );  
		$dataArray[]=array("Tag"=>"Total","onTap"=>"2","hidden"=>"$hiddenSign","data"=>$totalArray,"extList"=>$extdataArray); 
  
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>