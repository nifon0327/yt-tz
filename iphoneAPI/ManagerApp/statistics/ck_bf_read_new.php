<?php 
//报废
$today=date("Y-m-d");$m=0;
//布局设置
 $targetTable = "ac.ck8_bfsheet";
$Layout=array("Col3"=>array("Frame"=>"120,32,68, 15","Align"=>"R"));
 include "../../basic/downloadFileIP.php";
$dataArray0 = array();

$limitSql = $LoginNumber == 11965 ? " limit 5 ":"";

$orderResult=mysql_query("
SELECT B.Id,B.StuffId,B.Qty,B.Remark,B.Date,B.Estate,M.Name AS Operator,D.StuffCname,D.Price,D.Picture,C.PreChar ,B.Bill
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Operator 
WHERE  B.Estate in (3,2,1)  ORDER BY field(B.Estate,2,1,3), B.Date DESC $limitSql",$link_id);
$baseUrl = "http://www.middlecloud.com/download/ckbf/";
		    while($orderRow = mysql_fetch_array($orderResult)) {
		             $Id=$orderRow["Id"];
			         $StuffId=$orderRow["StuffId"];
			         $StuffCname=$orderRow["StuffCname"];
			         $Remark=$orderRow["Remark"];
			         $RemarkDate=$orderRow["Date"];
			         
			         $RemarkDate=$APP_FACTORY_CHECK==true?'':$RemarkDate;
			         
			         $RemarkOperator=$orderRow["Operator"];
			         $PreChar = $orderRow["PreChar"];
			        $Estate=$orderRow["Estate"];
			        $hasBill = $orderRow["Bill"];
			        $FileArr = array();
			        if ($hasBill>0) {
				        $FileArr[]=array("Type"=>"img",
				        "url"=>"$baseUrl"."B$Id".".jpg",
				        "url_thumb"=>"$baseUrl"."B$Id"."_thumb.jpg",
				        "sType"=>"jpg");
			        }
			        
			        
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
                   "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","Margin"=>"0,0,-15,0"),
                   "Col1"=>array("Text"=>"$Qty","IconType"=>"20"),
                   "Col3"=>array("Text"=>"$PreChar$Price"),
                   "Col5"=>array("Text"=>"$PreChar$Amount"),
//                    "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                   //"rIcon"=>"estate_$Estate",
               );
              // $onEdit=$Estate==0?3:0;
                 $ReturnReasons =$ReturnDateTime =$ReturnOper ="";
               $rightString = "";
               $bg_color = "";
               $onEditSign = 0;
               switch($Estate) {
	               case 3:
	               $rightString = "待处理";
	               $bg_color = "#43B4E3";
	               $onEditSign = $Estate;
	               break;
	               case 2:
	               $rightString = "退回";
	               $bg_color = "#FF0000";
	               $onEditSign = 19;
	              
               
             {
	               $checkReason =mysql_query("select R.Reason,date_format(R.DateTime,'%Y-%m-%d') DateTime,M.Name from returnreason R
left join staffmain M on R.Operator=M.Number
 where  R.targetTable ='$targetTable' and R.tableId=$Id order by R.Id desc limit 1;");
 					if ($checkReasonRow = mysql_fetch_assoc($checkReason)) {
	 					$ReturnReasons = $checkReasonRow["Reason"];
	 					$ReturnDateTime = $checkReasonRow["DateTime"];
	 					$ReturnOper = $checkReasonRow["Name"];
 					}
 
 					
               }
               

	               break;
	               case 1:
	               $rightString = "待审核";
	               $bg_color = "#FF0000";
	               $onEditSign = 0;
	               break;
	               
               }
               $dataArray0[]=array("Tag"=>"data","onTap"=>array("Target"=>"static","Args"=>"$StuffId|$ImagePath","Title"=>"$StuffCname"),"onEdit"=>"$onEditSign","data"=>$tempArray,'rmk'=>"$Remark",
                'top_right'=>array("Text"=>"$rightString",
                              				"Align"=>"M",
                              				"bg_color"=>"$bg_color",
                              				"Bold"=>"1",
                              				"Color"=>"#FFFFFF","Frame"=>"292,0,28,10",
                              				"FontSize"=>"8" ),
);
               
               
                  
                  $dataArray0[]=array("Tag"=>"remark1",
						     	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark",
						   	"Recorder" => "$RemarkDate",
						   	"anti_oper"=>"$RemarkOperator",
						   	"headline"=>"报废原因：",
						   	 	"Files"=>$hasBill>0?"1": "$ReturnReasons",
						   	 	"FileArray"=>$FileArr,
						   	 "pad_attr"=>$ReturnReasons==""?array():array(array("退回原因：\n","#FF0000","11"),array("$ReturnReasons","#888888","11")),"needReason"=>$ReturnReasons==""?"0":"1",
						   	 "reason_oper"=>"$ReturnOper","reason_time"=>"$ReturnDateTime",
						   	'left_sper'=>"0","margin_left"=>"20"
						   	);

               
               
	   }   

                                              
$mySql="SELECT DATE_FORMAT(B.Date,'%Y-%m') AS Month,SUM(B.Qty) AS Qty,SUM(B.Qty*D.Price*C.Rate) AS Amount,SUM(IF(B.Estate=3,1,0)) AS Estates 
FROM $DataIn.ck8_bfsheet B 
LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
WHERE 1 AND B.Estate=0 GROUP BY  DATE_FORMAT(B.Date,'%Y-%m') ORDER BY Month DESC";	
$Result=mysql_query($mySql, $link_id); 
$jsondata=array();$hiddenSign=1;$sortAmount=array(); $m=0;
while($myRow = mysql_fetch_array($Result)) {
	  $Month=$myRow["Month"];
	  $Qty=$myRow["Qty"];
	  $Amount=round($myRow["Amount"],2);
	  
	   if ($m<13) $sortAmount[]=$Amount; $m++;

	  $Qty=number_format($Qty);
	  $Amount=number_format($Amount);
	  $Estates=$myRow["Estates"];
	  $headArray=array(
				                      "RowSet"=>array("height"=>"$height"),
				                      "onTap"=>array("Value"=>"1","Target"=>" ","Args"=>"$Month"),
				                      "Title"=>array("Text"=>" $Month","FontSize"=>"14"),
				                      "Col1"=>array("Text"=>"$Qty","Frame"=>"110, 2, 80, 30"),
				                      "Col3"=>array("Text"=>"¥$Amount","Frame"=>"205, 2, 100, 30"),
				                      "iNumber"=>"$Estates"
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
    $headArray["Rank"]=array("Icon"=>"2");
    $tmpArray["head"]=$headArray;
    $jsondata[$key]=$tmpArray;
    if ($j==3) break;
}
if (count($dataArray0) > 0) {
	$tempHead = array();
	$tempHead[]=array("hidden"=>"0","Layout"=>$Layout,"data"=>$dataArray0);
	
	$jsondata = array_merge($tempHead,$jsondata);
	
}
//,'nav_btns'=>array('add'=>'bf','search'=>'bf')
$jsonArray=array("data"=>$jsondata,'add'=>"bf"); 
?>