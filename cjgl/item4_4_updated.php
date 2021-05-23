<?php   
//电信-zxq 2012-08-01
include "cj_chksession.php";
include "../basic/parameter.inc";
//步骤2：
$Log_Item="iPad权限设置";			//需处理
$Log_Funtion="权限更新";
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$upDataSheet="$DataIn.sc4_upopedom";	//需处理
//步骤3：需处理
$upSql = "UPDATE $upDataSheet SET Action=0 WHERE UserId=$User";
$upResult = mysql_query($upSql);
$x=1;
if(upResult){//清0成功	
	$Log="用户 $User 的权限已做清0准备，开始更新...<br>";
	for($i=1;$i<$IdCount;$i++){
		$tempValue=$checkid[$i];
		if($tempValue!=""){
			$Field=explode(",",$tempValue);
			$Action=$Field[2];
			$ModuleId=$Field[3];
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
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
?>