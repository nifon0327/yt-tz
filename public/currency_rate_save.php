<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
$Log_Item="每月汇率设置";			//需处理
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

$inRecode="INSERT INTO $DataPublic.currencyrate(`Month`, `Currency`, `Rate`, `Estate`, `Locks`, `Date`, `Operator`,  `creator`, `created`) 
                                                SELECT  '$Month',Id,Rate,'1','0',CURDATE(),'$Operator','$Operator','$DateTime'  FROM $DataPublic.currencydata 
                                                 WHERE Estate=1 AND Id NOT IN (SELECT Currency FROM $DataPublic.currencyrate WHERE Month='$Month') ";
 //  echo $inRecode;
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log=mysql_affected_rows()>0?"$TitleSTR 成功!<br>":"<div class=yellowB>$Month－$TitleSTR 未添加新记录!</div><br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inAction</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
