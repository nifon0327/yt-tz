<?php 
//电信-ZX
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage="ipad_modulenexus_read";
$nowWebPage="ipad_modulenexus_save";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="ipad模块权限";		//需处理
$Log_Funtion="设定";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
//权限全部清0
$upSql = "UPDATE $DataIn.sc4_upopedom SET Action=0 WHERE ModuleId=$ModuleId";
$upResult = mysql_query($upSql);
for($i=1;$i<$IdCount;$i++){
	$Temp="checkid".strval($i);
	$Number=$$Temp;
	if($Number!=""){
		$Action=0;
		for($j=1;$j<6;$j++){
			$k=$i+$j;
			$Temp="checkid".strval($k);
			$ActionTemp=$$Temp;
			$Action=$Action*1+$ActionTemp*1;
			}
		//新增或更新权限
		$CheckResult = mysql_query("SELECT Id FROM $DataIn.sc4_upopedom WHERE 1 AND UserId=$Number AND ModuleId=$ModuleId ORDER BY Id LIMIT 1",$link_id);
		if ($CheckRow = mysql_fetch_array($CheckResult) && mysql_affected_rows()>0) {
			$upSql2="UPDATE $DataIn.sc4_upopedom SET Action=$Action WHERE UserId=$Number and ModuleId=$ModuleId";
			}
		else{
			$upSql2="INSERT INTO $DataIn.sc4_upopedom (Id,UserId,ModuleId,Action) VALUES (NULL,'$Number','$ModuleId','$Action')";
			}
		$upResult2=mysql_query($upSql2);
		if($upResult2){
			$Log.="$x 用户 $Number 使用 $ModuleId 的权限 $Action 设定成功!<br>";
			//检查上级此功能的上级，是否存在
			$checkPreSql=mysql_query("SELECT ModuleId FROM $DataPublic.sc4_modulenexus WHERE dModuleId ='$ModuleId' LIMIT 1",$link_id);
			if($checkPreRow=mysql_fetch_array($checkPreSql)){//如果存在上级，则检查上级是否已经加入权限
				do{
					$PreModuleId=$checkPreRow["ModuleId"];	//上级的ID
					//////////检查上级是否已经设定权限
					$CheckResult1 = mysql_query("SELECT Id FROM $DataIn.sc4_upopedom WHERE UserId='$Number' AND ModuleId='$PreModuleId' ORDER BY Id LIMIT 1",$link_id);
					if ($CheckRow1 = mysql_fetch_array($CheckResult1) && mysql_affected_rows()>0) {
						$upPreSql="UPDATE $DataIn.sc4_upopedom SET Action=1 WHERE UserId='$Number' AND ModuleId='$PreModuleId'";	//上级权限已有记录则设浏览权限
						}
					else{
						$upPreSql="INSERT INTO $DataIn.sc4_upopedom (Id,UserId,ModuleId,Action) VALUES (NULL,'$Number','$PreModuleId','1')";//如果没有则加浏览权限
						}
					$upPreResult=mysql_query($upPreSql);
					////////////
					}while($checkPreRow=mysql_fetch_array($checkPreSql));
				}
			}
		else{
			$Log.="$x <div class='redB'>用户 $Number 使用 $ModuleId 的权限 $Action 设定失败!</div><br>";
			$OperationResult="N";
			}
		$x++;
		}
		$i=$i+5;
	}//end for
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
