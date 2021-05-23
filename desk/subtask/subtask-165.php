<?php
$BuyerSTR = '';
$outputinfo1 = '';
$PageSTR = '';
$outputinfo2 = '';
$outputinfo3 = '';

/*
功能：统计采购交期
*/
if ($_SESSION["Login_GroupId"]==401 && $_SESSION["Login_P_Number"]!="10007")
{
   $BuyerSTR=" AND S.BuyerId='" .$_SESSION["Login_P_Number"] . "' ";
}

$curDate=date("Y-m-d");
//采购时间超过20天
$Result165=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums 
        FROM $DataIn.cg1_stocksheet S
        LEFT JOIN  $DataIn.cg1_stockmain M ON S.Mid=M.Id 
        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
        WHERE 1  AND S.rkSign>0 AND S.Mid>0  AND  DATEDIFF('$curDate',S.DeliveryDate)>20 AND D.Estate=1 $BuyerSTR 
                         AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=S.StockId) 
         ",$link_id));
$day_20=$Result165["Nums"]==""?0:$Result165["Nums"];

//采购时间超过10天
$Result1651=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums 
        FROM $DataIn.cg1_stocksheet S
        LEFT JOIN  $DataIn.cg1_stockmain M ON S.Mid=M.Id 
        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
        WHERE 1  AND S.rkSign>0 AND S.Mid>0  AND D.Estate=1 $BuyerSTR  AND  DATEDIFF('$curDate',S.DeliveryDate)>10 AND  DATEDIFF('$curDate',S.DeliveryDate)<=20  
                         AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=S.StockId) 
         ",$link_id));
$day_10=$Result1651["Nums"]==""?0:$Result1651["Nums"];
/*
//采购时间0-40天
$Result1652=mysql_fetch_array(mysql_query("SELECT count(*) AS Nums
        FROM $DataIn.cg1_stocksheet S
        LEFT JOIN  $DataIn.cg1_stockmain M ON S.Mid=M.Id
        WHERE 1  AND S.rkSign>0 AND S.Mid>0  AND  DATEDIFF('$curDate',M.Date)<=40
                         AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE 1 AND C.StockId=S.StockId)
         ",$link_id));
$day_30=$Result1652["Nums"]==""?0:$Result1652["Nums"];
*/
//$Title.="(>90:<font color='red'>$day_90</font>>60:<font color='red'>$day_60</font>>30:<font color='red'>$day_30</font>)";
$tmpTitle="<a href='$Extra?DiffDate=1'target='_blank'  title='>20days' style='color:#FF0000'>$day_20</a>/<a href='$Extra?DiffDate=2' target='_blank'   title='11~20days' style='color:#FF0000'>$day_10</a>";///<a href='$Extra?DiffDate=3' target='_blank'   title='0~40days' style='color:#FF0000'>$day_30</a>

//$Title.="<br>&nbsp;&nbsp;&nbsp;&nbsp;90days>:<font color='red'>$day_90</font><br>&nbsp;&nbsp;61-90days:<font color='red'>$day_60</font><br>&nbsp;&nbsp;31-60days:<font color='red'>$day_30</font>";
?>