<?php   
//电信---yang 20120801
/*下单和出货，以配件分类读取数据
图表高度固定
分隔线数量和代表金额值自动根据最高值计算
独立已更新
*/
include "../basic/chksession.php";
//立体柱形图
include "../basic/parameter.inc";
$CheckMonths=$Y;				//要计算的月份数
$CheckMonth=date("Y-m-01");		//当前月第一天
$StartDate=date("Y-m-01",strtotime("$CheckMonth -$CheckMonths month"));//计算的起始日期
$StartMonth=date("Y-m",strtotime("$CheckMonth -$CheckMonths month"));
$StartY=date("Y",strtotime("$StartDate"));
$ToDate=date("Y-m-d");
switch($Id){
	case 8022:$Name="水贴/丝印壳";break;
	case 8040:$Name="其他壳";break;
	case 8041:$Name="触屏笔";break;
	case 8044:$Name="贴皮皮套";break;
	case 8036:$Name="拉绳竖皮套";break;
	case 8049:$Name="其它皮套";break;
	case 8050:$Name="Slim皮套";break;
	case 8053:$Name="组合装";break;
	}
$ProductType=" AND P.TypeId='$Id'".$KeyWords;
include "chartgetcolor.php";   //取得相对应类别的颜色

//出货或下单最高金额
$TjOut="DATE_FORMAT(M.Date,'%Y-%m')>='$StartMonth'";
$TjIn="DATE_FORMAT(M.OrderDate,'%Y-%m')>='$StartMonth'";

$MaxResult = mysql_fetch_array(mysql_query("
	SELECT MAX(Qty) AS MaxValue1 FROM ( 
		SELECT SUM(Qty) AS Qty FROM(
			SELECT SUM(S.Qty) AS Qty,DATE_FORMAT(M.Date,'%Y-%m') AS Month 
			FROM $DataIn.ch1_shipsheet S 
			LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
			WHERE $TjOut $ProductType GROUP BY DATE_FORMAT(M.Date,'%Y-%m')
		) B GROUP BY Month
	UNION ALL 
		SELECT SUM(Qty) AS Qty FROM(
			SELECT SUM(S.Qty) AS Qty,DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month 
			FROM $DataIn.yw1_ordersheet S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
