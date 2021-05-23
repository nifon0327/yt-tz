<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage="ipad_popedom_read";
$nowWebPage="ipad_popedom_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="ipad功能权限";		//需处理
$upDataSheet="$DataIn.sc4_upopedom";	//需处理
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="User=$User";
//步骤3：需处理，更新操作
//权限全部清0
$upSql = "UPDATE $upDataSheet SET Action=0 WHERE UserId=$User";
$upResult = mysql_query($upSql);
$x=1;
if(upResult){//清0成功	
	$Log="用户 $User 的权限已做清0准备，开始更新...<br>";
	for($i=1;$i<$RowCount;$i++){
		$tempValue=$checkid[$i];
		if($tempValue!=""){
			$Field=explode(",",$tempValue);
			$Action=$Field[3];
			$ModuleId=$Field[4];
			$CheckResult = mysql_query("SELECT Id FROM $upDataSheet WHERE 1 AND UserId=$User AND ModuleId=$ModuleId ORDER BY Id LIMIT 1",$link_id);
			if ($CheckRow = mysql_fetch_array($CheckResult)) {
				//更新权限
				$upSql2="UPDATE $upDataSheet SET Action=$Action WHERE UserId=$User and ModuleId=$ModuleId";
				}
			else{
				////新增
				$upSql2="INSERT INTO $upDataSheet (Id,UserId,ModuleId,Action) VALUES (NULL,'$User','$ModuleId','$Action')";
				}				
			$upResult2=mysql_query($upSql2);
			if($upResult2){
				$Log=$Log."用户 $User 使用 $ModuleId 的权限 $Action 设定成功!<br>";
				}
			else{
				$Log=$Log."<div class='redB'>用户 $User 使用 $ModuleId 的权限 $Action 设定失败! $upSql2</div><br>";
				$OperationResult="N";
				}
			}//end if($tempValue!="")
		}//end for($i=1;$i<$IdCount;$i++)
	}//end if(upResult)
else{
	$Log="<div class='redB'>用户 $User 的权限清0失败!</div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>