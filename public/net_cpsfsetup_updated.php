<?php 
//电信-ZX  2012-08-01
//$DataPublic.net_cpsfdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="软件使用";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$ALType="hdId=$hdId";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
switch($ActionId){
	case 54:
		$inRecode="INSERT INTO $DataPublic.net_cpsfdata SELECT NULL,'$hdId',Id,'$DateTime','$Operator',1,0,'$Operator',NOW(),'$Operator',NOW() FROM $DataPublic.net_softwarelist WHERE ID IN ($Ids)";
		$inAction=@mysql_query($inRecode);
		if ($inAction){ 
			$Log="$TitleSTR 成功!<br>";
			} 
		else{
			$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
			$OperationResult="N";
			} 
		break;
	case 55:
		//删除数据库记录
		$DelSql = "DELETE FROM $DataPublic.net_cpsfdata WHERE Id IN ($Ids)"; 
		$DelResult = mysql_query($DelSql);
		if($DelResult && mysql_affected_rows()>0){
			$Log.="ID号在 $Ids 的 $Log_Item 删除操作成功.<br>";
			}
		else{
			$Log.="<div class='redB'>ID号在 $Ids 的 $Log_Item 删除操作失败. $DelSql </div><br>";
			$OperationResult="N";
			}
		//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.net_cpsfdata");
		break;
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>