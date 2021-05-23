<?php 
//备品明细
$today=date("Y-m-d");                      
 include "../../basic/downloadFileIP.php";
 
$mySql="SELECT T.TypeId,T.TypeName,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,C.PreChar 
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN  $DataIn.stufftype  T ON T.TypeId=D.TypeId 
WHERE DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth'  GROUP BY T.TypeId ORDER BY Amount DESC";	

 $myResult = mysql_query($mySql,$link_id);
 $dataArray=array();
 while($myRow = mysql_fetch_array($myResult)) 
{
       $TypeId=$myRow["TypeId"];
       $TypeName=$myRow["TypeName"];
       $sumQty = number_format($myRow["Qty"]);
       $sumAmount=number_format($myRow["Amount"]);
       $PreChar=$myRow["PreChar"];
       
       $totalArray=array(
				                      "Title"=>array("Text"=>"$TypeName","Color"=>"#43B4E3","Margin"=>"10,0,0,0"),
				                      "Col2"=>array("Text"=>"$sumQty","Margin"=>"-40,0,0,0"),
				                      "Col3"=>array("Text"=>"¥$sumAmount")
				                   );  
		$dataArray[]=array("Tag"=>"Total","data"=>$totalArray,"onTap"=>"2","hidden"=>"0","extList"=>array()); 
		
		$orderResult=mysql_query("
SELECT B.StuffId,B.Qty,B.Remark,B.Date,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price*C.Rate) AS Amount  
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Operator 
WHERE 1 AND DATE_FORMAT(B.Date,'%Y-%m')='$checkMonth' AND D.TypeId='$TypeId' ORDER BY Amount DESC",$link_id);
		    while($orderRow = mysql_fetch_array($orderResult)) {
			         $StuffId=$orderRow["StuffId"];
			         $StuffCname=$orderRow["StuffCname"];
			         $Remark=$orderRow["Remark"];
			         $RemarkDate=$orderRow["Date"];
			         $RemarkOperator=$orderRow["Operator"];
			         
			         $RemarkDate=$APP_FACTORY_CHECK==true?'':$RemarkDate;
			         
                    $Qty=$orderRow["Qty"];
                    $Price=$orderRow["Price"];
                    $Amount=number_format($Qty*$Price,2);
                    $Picture = $orderRow["Picture"];
                    $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
                    include "submodel/stuffname_color.php";
                    
                  $Qty=number_format($Qty);
                  $Price=number_format($Price,2);
                  $tempArray=array(
                  "Id"=>"$StuffId",'has2'=>'1',
                   "RowSet"=>array("bgColor"=>"$rowColor"),
                   "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
                   "Col1"=>array("Text"=>"$Qty","LIcon"=>"ibl_gray","Margin"=>"12,0,0,0",),
                   "Col3"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$PreChar$Amount"),
                  //"Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
               );
               $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Picture","Args"=>"$ImagePath"),"data"=>$tempArray,'rmk'=>"$Remark");
                $ReturnReasons = "";
						   $dataArray[]=array("Tag"=>"remark1",
						     	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n\t",
						   	"Recorder" => "$RemarkDate",
						   	"anti_oper"=>"$RemarkOperator",
						   	"headline"=>"入库备注：",
						   	"reason"=>$ReturnReasons!=""?"\n"."$ReturnReasons":"",
						   	'left_sper'=>"0","margin_left"=>"20"
						   	);
	   }   
	           
 }
if ($FromPage!="Read"){
	 $jsonArray=$dataArray;
}
?>