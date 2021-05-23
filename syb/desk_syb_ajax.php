<?php 
//电信
//代码共享-EWEN 2012-08-19
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo"<iframe name=\"download\" style=\"display:none\"></iframe>";
/*
Month：是全部数据(值为空)还是月份数据(值为YYYY-MM)
DataT：是已结付数据还是未结付数据还是合计数据，如果是月份，只读取合计
ItemId：项目ID，用来转向相应项目的处理文件(因为各自显示的表内容不一样，要分开处理，部分可以统一处理)
$tableWidth
*/
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}

$checkSubSql=mysql_fetch_array(mysql_query("SELECT Mid,ItemName,Remark,Parameters,AjaxNo FROM  $DataPublic.sys8_pandlsheet WHERE Id='$ItemId' LIMIT 1",$link_id));
$ItemMid=$checkSubSql["AjaxNo"]==0?$checkSubSql["Mid"]:$checkSubSql["AjaxNo"];//AjaxNo为0时使用分类处理页面，非0时使用特别处理页面
$ItemName=$checkSubSql["ItemName"];
$Remark=$checkSubSql["Remark"];
$Parameters=$checkSubSql["Parameters"];
if ($Login_P_Number==10868)echo "syb/syb_".$ItemMid.".php";
include "syb/syb_".$ItemMid.".php";//转相应处理子文件
?>