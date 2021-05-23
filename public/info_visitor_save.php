<? 
//`$DataIn`.`net_cpbylaw` 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="来访登记";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";


$MTime=$MTime." ".$DTime.":00";
$RTime=$RTime." ".$DRTime.":00";
//步骤3：需处理
$Remark=FormatSTR($Remark);
$inRecode="INSERT INTO  $DataPublic.come_data (Id ,cSign ,TypeId ,Name ,Persons ,ComeDate ,Remark ,InTime ,InOperator ,OutTime ,OutOperator ,CompanyId ,Mid ,Estate ,Locks ,Date ,Operator)VALUES (NULL,  '7',  '$TypeId',  '$Name',  '$Person',  '$ComeDate', '$Remark', NULL ,  '0', NULL ,  '0',  '0',  '0',  '1',  '0',  '$Date',  '$Operator')";

$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
