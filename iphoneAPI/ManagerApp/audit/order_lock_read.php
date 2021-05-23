<?php 
//订单锁定审核
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];

$mySql="SELECT E.Id,E.POrderId,E.Remark,E.Operator,E.OPdatetime,C.Forshort,S.OrderPO,S.ProductId,S.Qty,S.Price,D.PreChar,P.cName,P.TestStandard,
                YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks  
FROM $DataIn.yw2_orderexpress  E 
LEFT JOIN $DataIn.yw1_ordersheet S  ON S.POrderId=E.POrderId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId 
WHERE  S.Estate>0  AND E.Estate=1 AND E.Type=2 ORDER BY E.OPdatetime";

 $Result=mysql_query($mySql,$link_id);
 while($myRow = mysql_fetch_array($Result)) 
 {
    $Id=$myRow["Id"];
    $POrderId=$myRow["POrderId"];
    $OrderPO=$myRow["OrderPO"];
    $Forshort=$myRow["Forshort"];
    $ProductId=$myRow["ProductId"];
    $cName=$myRow["cName"];
    $Qty=number_format($myRow["Qty"]);
     $PreChar=$myRow["PreChar"];
    $Price=sprintf("%.2f",$myRow["Price"]);
    $Remark=$myRow["Remark"];
    
    $TestStandard=$myRow["TestStandard"];
     include "order/order_TestStandard.php";
     
    $Weeks=$myRow["Weeks"]==""?"00":substr($myRow["Weeks"],4,2);               
    $WeeksColor=($myRow["Weeks"]<$curWeek && $myRow["Weeks"]!="") ?"#FF0000":"";
    
    $Operator=$myRow["Operator"];
     include "../../model/subprogram/staffname.php";
  
    $OPdatetime=$myRow["OPdatetime"];
    $Date=GetDateTimeOutString($OPdatetime,'');
    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
    if ($opHours>=$timeOut[0]) $OverNums++;
   
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"0","hidden"=>"$hidden","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Week"=>array("Text"=>"$Weeks","bgColor"=>"$WeeksColor"),
	                     "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
	                     "Col1"=>array("Text"=>"$OrderPO"),
	                     "Col2"=>array("Text"=>"$Forshort"),
	                     "Col3"=>array("Text"=>"$Qty"),
	                     "Col4"=>array("Text"=>"$PreChar$Price"),
	                     "Remark"=>array("Text"=>"$Remark"),
	                     "Date"=>array("Text"=>"$Date"),
	                     "Operator"=>array("Text"=>"$Operator")
                     );
 }

?>