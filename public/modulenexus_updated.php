<?php 
//电信-yang 20120801
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="模块关系";		//需处理
$upDataSheet="$DataPublic.modulenexus";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	
	$dModuleId=$checkid[$i];
	//检查是否已存在，是则更新；否则新增
	$OrderId=$i+1;
	$checkSql=mysql_query("SELECT Id FROM $upDataSheet WHERE 1 AND ModuleId=$ModuleId AND dModuleId=$dModuleId ORDER BY ModuleId,Id",$link_id);	
	if ($checkRow = mysql_fetch_array($checkSql)) {
		$inRecode = "UPDATE $upDataSheet SET ModuleId='$ModuleId',dModuleId='$dModuleId',OrderId='$OrderId',Date='$DateTime',Operator='$Operator' WHERE 1 and ModuleId=$ModuleId and dModuleId=$dModuleId LIMIT 1";
		$Log1="更新";
		}
	else{
		$inRecode="INSERT INTO $upDataSheet (Id,ModuleId,dModuleId,OrderId,Date,Operator) VALUE (NULL,'$ModuleId','$dModuleId','$OrderId','$DateTime','$Operator')";
		$Log1="新增";
		}
	$inRes=@mysql_query($inRecode);
	if($inRes){
		$Log.="上级模块 $ModuleName / $ModuleId 与下级模块 $dModuleId 的关系".$Log1."成功! <br>";
		} 
	else{
		$Log.="<div class='redB'>上级模块 $ModuleName / $ModuleId 与下级模块 $dModuleId 的关系".$Log1."失败! $inRecode</div><br>";
		$OperationResult="N";
		}
	}//end for
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
