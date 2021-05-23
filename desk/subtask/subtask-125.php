<?php 
//用桌面结余值－损益表最后一个月的结余  ewen 2013-11-27 OK
require(dirname(__FILE__)."/../../syb/sybpath.php");//从当前subtask目录切换至损益表目录syb
//汇率参数
$rateResult = mysql_query("SELECT Rate,Symbol FROM $DataPublic.currencydata WHERE 1 and Estate=1",$link_id);
if($rateRow = mysql_fetch_array($rateResult)){
	do{
		$Symbol=$rateRow["Symbol"];
		$TempRate=strval($Symbol)."_Rate";
		$$TempRate=$rateRow["Rate"];
		}while($rateRow = mysql_fetch_array($rateResult));
	}
//需计算的损益主项目数
$checkItemNum = mysql_fetch_array(mysql_query("SELECT count(*) AS ItemNum FROM $DataPublic.sys8_pandlmain WHERE Estate=1 ",$link_id));
$ItemNum=$checkItemNum["ItemNum"];

//最后月份
$CheckTime=date("Y-m");
$TempPayDatetj=" AND DATE_FORMAT(M.PayDate,'%Y-%m')='$CheckTime'";
$TempDatetj=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
$TempMonthtj="  AND M.Month='$CheckTime'";
$TempSendDatetj=" AND DATE_FORMAT(M.SendDate,'%Y-%m')='$CheckTime'";
$TempqkDatetj=" AND DATE_FORMAT(M.qkDate,'%Y-%m')='$CheckTime'";
$TempDateTax=" AND DATE_FORMAT(M.TaxDate,'%Y-%m')='$CheckTime'";
 $TempDateModelf=" AND DATE_FORMAT(M.Date,'%Y-%m')='$CheckTime'";
$TempDeliveryDate=" AND DATE_FORMAT(M.DeliveryDate,'%Y-%m')='$CheckTime'";

$Subscript=0;			//数组起始下标
$SumType_A=array();unset($SumType_A);
require_once "desk_pandl_data.php";//引用syb下的文件
$lastMonthAmount=$SumType_A[0][0];//收入
for($i=1;$i<7;$i++){
	$lastMonthAmount-=$SumType_A[0][$i];//其他支出
	}
$lastMonthAmount=sprintf("%.2f",$lastMonthAmount);
require(dirname(__FILE__)."/../deskpath.php");//将当前文件目录subtask切换至上级desk目录

//include "subtask-125_sub.php";
$Sum_A=$jy-$lastMonthAmount;
$Sum_AView=$Sum_A>0?"¥".number_format($Sum_A):"(¥".number_format(-$Sum_A).")";
$contentSTR="<li class=TitleBL>损益表</li>";
$contentSTR.="<li class=TitleBR><span class='yellowN'><a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>".$Sum_AView."</a></span></li>";
?>