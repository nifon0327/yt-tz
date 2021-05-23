<?php 
//步骤1： $DataIn.ch5_sampsheet 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="客户图例颜色资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
session_register("nowWebPage");
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理
//记录字段值

$SelColor=explode("#",$SelColor);
$ColorCode=$SelColor[1];
$inRecode="INSERT INTO $DataIn.chart3_color (Id, TypeId, ColorCode, Date, Estate, Locks, Operator) VALUES (NULL,'$TypeId','$ColorCode','$Date','1','0','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$ClientName 产品类型图例颜色资料添加成功!<br>";
 	} 
else{ 
	$Log="<div class=redB>$ClientName 产品类型已经存在 图例颜色资料添加失败!</div><br>"; 
	$OperationResult="N";
	}

//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>