<?php  
//电信-EWEN
//代码、数据共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
$Log_Item="立项分类";			//需处理
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
//步骤3：需处理
$Name=FormatSTR($Name);
$Remark=FormatSTR($Remark);
$chinese=new chinese;
$Letter=substr($chinese->c($Name),0,1);
$inRecode="INSERT INTO $DataIn.projectset_type (Id,Letter,Name,Remark,Estate,Locks,Date,Operator,PLocks,creator,created,modifier,modified) VALUES (NULL,'$Letter','$Name','$Remark','1','0','$DateTime','$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
