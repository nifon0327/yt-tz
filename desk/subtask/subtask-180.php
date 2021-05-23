<?php   
//电信-zxq 2012-12-24
//功能：生产工期统计

//取得计算工价的时薪$OneHourSalaryt
include "../model/subprogram/onehoursalary.php";

$ShipResult = mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty*G.Price) AS Amonut 
	FROM $DataIn.yw1_ordersheet S
	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE 1 AND S.Estate=1 AND T.mainType=3 AND T.TypeId NOT IN ('7090','7070')",$link_id));
$xqAmonut=sprintf("%.0f",$ShipResult["Amonut"]);

//计算未出已生产
$checkOverSql=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(S.Qty*C.Price),0) AS scAmount 
			FROM $DataIn.sc1_cjtj S
			LEFT JOIN $DataIn.cg1_stocksheet C ON C.StockId = S.StockId
			LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = C.POrderId
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId = C.StuffId
			WHERE 1 AND Y.Estate=1 
		    ",$link_id));
$scAmount=sprintf("%.0f",$checkOverSql["scAmount"]);
$xqAmonut-=$scAmount;

//计算生产员工数量
		$checkStaffSql=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums 
		FROM $DataIn.staffgroup G 
		LEFT JOIN $DataPublic.staffmain S ON G.GroupId=S.GroupId
		WHERE G.TypeId>0 AND S.Estate=1 ",$link_id));

     $Nums=$checkStaffSql["Nums"];
     $ygAmount=$Nums*10*$OneHourSalaryt;
     
    $DaysSUM=$ygAmount==0?"<span style='color:#FF0000'>?</span>":ceil($xqAmonut/$ygAmount);
    
$tmpTitle="<font color='red'>" .$DaysSUM. "天</font>";
?> 