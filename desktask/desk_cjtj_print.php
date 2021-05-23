<?php   
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
$tableMenuS=600;
$OrderType=$OrderType==""?1:$OrderType;
$Th_Col="序号|30|客户|70|PO号|80|中文名|300|订单数量|60|工序总数|60|已完成|60|未完成|60";
$ColsNumber=6;
$ChooseOut="N";
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth=$tableWidth-30;
$SearchRows=" AND A.TypeId='$TypeId' AND S.Estate>0";
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT S.OrderPO,S.Qty,S.POrderId,P.cName,P.eCode,C.Forshort
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
$DataSTR
WHERE 1 $SearchRows ORDER BY P.CompanyId ,S.Id DESC";

$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
	  	//初始化计算的参数
		$m=1;
		$thisBuyRMB=0;
		$OrderPO=toSpace($myRow["OrderPO"]);
		$cName=$myRow["cName"];
		$eCode=toSpace($myRow["eCode"]);
		$Forshort=$myRow["Forshort"];
		$Qty=$myRow["Qty"];
		$POrderId=$myRow["POrderId"];
		$sumQty=$sumQty+$Qty;
		$CheckStuffQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS cfQty 
		FROM $DataIn.cg1_stocksheet G,$DataIn.stuffdata A 
		WHERE G.POrderId='$POrderId' AND G.StuffId=A.StuffId AND A.TypeId='$TypeId'",$link_id));
		$stuffQty=$CheckStuffQty["cfQty"];$stuffQtySum+=$stuffQty;
		$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
		$cfQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];$cfQtySum+=$cfQty;
		$unQty=$stuffQty-$cfQty;$unQtySum+=$unQty;
		$cfQty=zerotospace($cfQty);
		$unQty=zerotospace($unQty);
			$ValueArray=array(
				array(0=>$Forshort),
				array(0=>$OrderPO),
				array(0=>$cName),
				array(0=>$Qty,		1=>"align='right'"),
				array(0=>$stuffQty,	1=>"align='right'"),
				array(0=>$cfQty,	1=>"align='right'"),
				array(0=>$unQty, 	1=>"align='right'")
				);
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	echo"<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='$thePointerColor'><tr>";
	echo"<td class='A0111' height='20'>合计</td>";
	echo"<td class='A0101' width='60' align='right'>$sumQty</td>";
	echo"<td class='A0101' width='60' align='right'>$stuffQtySum</td>";
	echo"<td class='A0101' width='60' align='right'>$cfQtySum</td>";
	echo"<td class='A0101' width='60' align='right'>$unQtySum</td>";
	echo"</tr></table>";
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
?>