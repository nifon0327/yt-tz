<?php  
include "../model/modelhead.php";
$Log_Item="菜单";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$parent_id=$parent_id==""?0:$parent_id;
$typeid=$typeid==""?0:$typeid;
$inRecode="INSERT INTO $DataIn.ac_menus (id, parent_id, typeid, csign, ModuleId,name, action, callback,badges,icon_type,icon, `order`, row, col, abs, level, Estate, Locks, creator, created, modifier, modified, PLocks, Date, Operator) VALUES (NULL,'$parent_id','$typeid','$cSign','$ModuleId','$name','$action','$callback','$badges','$icon_type','$icon','$order','$row','$col','$abs','$level','1','0','$Operator','$DateTime','$Operator','$DateTime','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!$inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
