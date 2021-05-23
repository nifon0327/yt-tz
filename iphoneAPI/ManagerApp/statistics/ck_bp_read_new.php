<?php 
	 include "../../basic/downloadFileIP.php";
 $targetTable = "ac.ck7_bprk";
	$didntPassed = array();
	$checkDidntPass = "SELECT B.Id,B.Estate,B.StuffId,B.Qty,B.Remark,B.Date,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,(B.Qty*D.Price) AS Amount,C.PreChar 
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Operator 
WHERE 1 AND B.Estate>=1 ORDER BY  field(B.Estate,1,3,2), B.Date DESC";
	
	$noPassSql = mysql_query($checkDidntPass);
	$noCount = 0;
	while ($noPassRow = mysql_fetch_assoc($noPassSql)) {
		
		$Id = $noPassRow["Id"];
		$Estate = $noPassRow["Estate"];
		$onEditSign = "";
		if ($Estate==2) {
			$onEditSign = "9";
		}
		
		$StuffId = $noPassRow["StuffId"];
		$Qty = $noPassRow["Qty"];
		$Remark = $noPassRow["Remark"];
		$StuffCname = $noPassRow["StuffCname"];
		$Date = $noPassRow["Date"];
		$Operator = $noPassRow["Operator"];
		$Price = $noPassRow["Price"];
		$Picture = $noPassRow["Picture"];
		$Amount = $noPassRow["Amount"];
		$Amount = number_format($Amount,2);
		$PreChar = $noPassRow["PreChar"];
		
		  $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
                    include "submodel/stuffname_color.php";
		// $Qty=number_format($Qty);
                  $Price=number_format($Price,2);
                  $tempArray=array(
                  "Id"=>"$Id",'estate'=>"$Estate",'has2'=>'1',
                   "RowSet"=>array("bgColor"=>"$rowColor"),
                   "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
                   "Col1"=>array("Text"=>"$Qty","LIcon"=>"ibl_gray","Margin"=>"10,0,0,0"),
                   "Col3"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$PreChar$Amount"),
               );
               $ReturnReasons =$ReturnDateTime =$ReturnOper ="";
               
               if ($Estate==2) {
	               $checkReason =mysql_query("select R.Reason,date_format(R.DateTime,'%Y-%m-%d') DateTime,M.Name from returnreason R
left join staffmain M on R.Operator=M.Number
 where  R.targetTable ='$targetTable' and R.tableId=$Id order by R.Id desc limit 1;");
 					if ($checkReasonRow = mysql_fetch_assoc($checkReason)) {
	 					$ReturnReasons = $checkReasonRow["Reason"];
	 					$ReturnDateTime = $checkReasonRow["DateTime"];
	 					$ReturnOper = $checkReasonRow["Name"];
 					}
 
 					
               }
               
// "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$Operator"),
                              $didntPassed[]=array("Tag"=>"data","onTap"=>array("Target"=>"static","Args"=>"$StuffId|$ImagePath","Title"=>"$StuffCname"),"data"=>$tempArray,"onEdit"=>"$onEditSign",'rmk'=>"$Remark",
                              'top_right'=>array("Text"=>$Estate==2?"退回": "待审核",
                              				"Align"=>"M",
                              				"bg_color"=>"#FF0000",
                              				"Bold"=>"1",
                              				"Color"=>"#FFFFFF","Frame"=>"292,0,28,10",
                              				"FontSize"=>"8" ),
                              				
                              	);  
                              
                                
						   $didntPassed[]=array("Tag"=>"remark1",
						   	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n\t",
						   	"Recorder" => "$Date",
						   	"anti_oper"=>"$Operator",
						   	"headline"=>"入库备注：",
						   	"reason"=>$ReturnReasons!=""?"\n"."$ReturnReasons":"",
						   	'left_sper'=>"0",'reason_oper'=>"$ReturnOper",'reason_time'=>"$ReturnDateTime","margin_left"=>"20"
						   	
						   	);

                              
	   $noCount++; 
		
	}
	
	
	
	
//备品
$today=date("Y-m-d");$m=0;
//布局设置
$Layout=array("Col3"=>array("Frame"=>"120,32,68, 15","Align"=>"R"));
                                              
$mySql="SELECT DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount 
FROM $DataIn.ck7_bprk B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 AND B.Estate=0 GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC";	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;$sortAmount=array();$m=0;
while($myRow = mysql_fetch_array($Result)) {
	  $Month=$myRow["Month"];
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
	  
	   if ($m<13) $sortAmount[]=$Amount; $m++;

	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Month"),
				                      "Title"=>array("Text"=>" $Month","FontSize"=>"14"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"110, 2, 80, 30"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"210, 2, 103, 30")
				                   ); 
				                                   	                   
	   $jsondata[]=array("head"=>$headArray,"hidden"=>"$hiddenSign","Layout"=>$Layout,"data"=>array()); 
}
//排序
arsort($sortAmount,SORT_NUMERIC);
$j=0;
while(list($key,$val)= each($sortAmount)) 
{
    $j++;
    $tmpArray=$jsondata[$key];
    $headArray=$tmpArray["head"];
    $headArray["Rank"]=array("Icon"=>"1");
    $tmpArray["head"]=$headArray;
    $jsondata[$key]=$tmpArray;
    if ($j==3) break;
}

{
	$tempHead = array();
	$tempHead[]=array("hidden"=>"0","Layout"=>$Layout,"data"=>$didntPassed);
	
	$jsondata = array_merge($tempHead,$jsondata);
	
}

$jsonArray=array("data"=>$jsondata,'add'=>"bp",'nav_btns'=>array('add'=>"bp",'sear'=>"bp")); 
?>