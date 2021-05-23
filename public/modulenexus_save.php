<?php 
//电信-yang 20120801
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage="modulenexus_read";
$nowWebPage="modulenexus_save";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="模块权限";		//需处理
$Log_Funtion="设定";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
//该功能模块的权限全部清0
$upSql = "UPDATE $DataIn.upopedom A LEFT JOIN $DataIn.usertable U ON U.Id=A.UserId SET A.Action=0 WHERE A.ModuleId=$ModuleId";
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
		$CheckResult = mysql_query("SELECT Id FROM $DataIn.upopedom WHERE 1 and UserId=$Number and ModuleId=$ModuleId ORDER BY Id LIMIT 1",$link_id);
		if ($CheckRow = mysql_fetch_array($CheckResult) && mysql_affected_rows()>0) {
			$upSql2="UPDATE $DataIn.upopedom SET Action=$Action WHERE UserId=$Number and ModuleId=$ModuleId";
			}
		else{
			$upSql2="INSERT INTO $DataIn.upopedom (Id,UserId,ModuleId,Action) VALUES (NULL,'$Number','$ModuleId','$Action')";
			}
		$upResult2=mysql_query($upSql2);
		if($upResult2){
			$Log.="$x 用户 $Number 使用 $ModuleId 的权限 $Action 设定成功!<br>";
			//检查上级
			$checkPreSql=mysql_query("SELECT ModuleId FROM $DataPublic.modulenexus WHERE dModuleId = 1060 OR dModuleId =(SELECT ModuleId  FROM $DataPublic.modulenexus WHERE dModuleId = 1060 LIMIT 1)",$link_id);
			if($checkPreRow=mysql_fetch_array($checkPreSql)){
				do{
					$PreModuleId=$checkPreRow["ModuleId"];
					//////////检查上级是否已经设定权限
					$CheckResult1 = mysql_query("SELECT Id FROM $DataIn.upopedom WHERE 1 and UserId=$Number and ModuleId=$PreModuleId ORDER BY Id LIMIT 1",$link_id);
					if ($CheckRow1 = mysql_fetch_array($CheckResult1) && mysql_affected_rows()>0) {
						$upPreSql="UPDATE $DataIn.upopedom SET Action=1 WHERE UserId=$Number and ModuleId=$PreModuleId";	//上级权限已有记录则设浏览权限
						}
					else{
						$upPreSql="INSERT INTO $DataIn.upopedom (Id,UserId,ModuleId,Action) VALUES (NULL,'$Number','$PreModuleId','1')";//如果没有则加浏览权限
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
