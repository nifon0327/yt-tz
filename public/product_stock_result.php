<?php 
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber=14;
$tableMenuS=500;
$Th_Col="序号|30|项目|80|日期|80|相应单号|150|中文名|280|订单流水号|120|数量|80";
include "../model/subprogram/read_model_3.php";

echo "$ProductId-$cName"; 
List_Title($Th_Col,"1",1);

//订单总数
$UnionSTR="SELECT 1 AS Sign,'订单' as Item, M.OrderDate AS Date,S.OrderPO as No,S.POrderId,P.cName, S.Qty 
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE S.ProductId='$ProductId' AND S.Qty>0 ";

$UnionSTR.="
UNION ALL
SELECT 2 AS Sign,'入库' as Item, R.scDate AS Date,'&nbsp;' as No, R.POrderId,P.cName, R.Qty AS Qty 
FROM $DataIn.yw1_orderrk  R
LEFT JOIN $DataIn.productdata P ON P.ProductId=R.ProductId
WHERE R.ProductId='$ProductId' AND R.Qty >0 ";

//采购已下采购单    //和没下采购单
$UnionSTR.="
UNION ALL
SELECT 3 AS Sign,'出库' as Item,M.Date AS Date,M.InvoiceNO as No, S.POrderId,P.cName,S.Qty AS Qty 
FROM $DataIn.ch1_shipsheet S
LEFT JOIN $DataIn.ch1_shipmain M ON M.Id = S.Mid
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE  S.ProductId='$ProductId' " ;  


$UnionSTR="select * from ($UnionSTR) A order by Sign, Date ";

$myResult = mysql_query($UnionSTR,$link_id);
$j=1;
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Sign=$myRow["Sign"];
		
		$Item=$myRow["Item"]==NULL?"&nbsp;":$myRow["Item"];
		$Date=$myRow["Date"]==NULL?"&nbsp;":$myRow["Date"];
		$No=$myRow["No"]==NULL?"&nbsp;":$myRow["No"];
		$POrderId=$myRow["POrderId"];		
		$cName=$myRow["cName"]==NULL?"&nbsp;":$myRow["cName"];
		
		$Qty=$myRow["Qty"];			  

		$ChooseOut="N";	
		$ValueArray=array(
			0=>array(0=>$Item,
					 1=>"align='Left'"),
			1=>array(0=>$Date,
					 1=>"align='center'"),
			2=>array(0=>$No,
					 1=>"align='Left'"),
			3=>array(0=>$cName,
					 1=>"align='Left'"),
			4=>array(0=>$POrderId,
					 1=>"align='center'"),
			5=>array(0=>$Qty,
					 1=>"align='Left'"),	
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
  	}
List_Title($Th_Col,"0",1);
?>