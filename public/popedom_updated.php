<?php
//电信---yang 20120801
//代码共享-EWEN
include "../model/modelhead.php";
$fromWebPage="popedom_read";
$nowWebPage="popedom_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="系统权限";		//需处理
$upDataSheet="tasklistdata";	//需处理
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="User=$User";
//步骤3：需处理，更新操作
//该用户的权限全部清0
$upSql = "UPDATE $DataIn.upopedom SET Action='0' WHERE UserId='$User'";
$upResult = mysql_query($upSql);
$x=1;
if(upResult){//清0成功
	$Log="用户 $User 的权限已做清0准备，开始更新...<br>";
	for($i=1;$i<$IdCountNum;$i++){
		$tempValue=$checkid[$i];
		if($tempValue!=""){
		//分解值：上级，本级，级别，权限，模块ID
			$Action=0;
			$Field=explode(",",$tempValue);
			$Grade=$Field[3];
			if($Grade<4){
				$ModuleId=$Field[4];
				if($Grade==3){//3级菜单权限
					$tAction=0;
					for($j=2;$j<=6;$j++){
						$i++;
						$tValue=$checkid[$i];
						if($tValue!=""){
							$tField=explode(",",$tValue);
							$Action=$Action+$tField[4];
							}//end if($tValue!="")
						}//end for($j=2;$j<6;$j++)
					}//end if($Grade==3)
				else{
					$Action=1;
					}//end if($Grade==3)
				$CheckResult = mysql_query("SELECT Id FROM $DataIn.upopedom WHERE 1 AND UserId='$User' AND ModuleId='$ModuleId' ORDER BY Id LIMIT 1",$link_id);
				if ($CheckRow = mysql_fetch_array($CheckResult)) {
					//更新权限
					$upSql2="UPDATE $DataIn.upopedom SET Action='$Action' WHERE UserId='$User' AND ModuleId='$ModuleId'";
					}
				else{
					////新增
					$upSql2="INSERT INTO $DataIn.upopedom (Id,UserId,ModuleId,Action) VALUES (NULL,'$User','$ModuleId','$Action')";
					}
				$upResult2=mysql_query($upSql2);
				if($upResult2){
					$Log=$Log."$x 用户 $User 使用 $ModuleId 的权限 $Action 设定成功!<br>";
					}
				else{
					$Log=$Log."$x <div class='redB'>用户 $User 使用 $ModuleId 的权限 $Action 设定失败!</div><br>";
					$OperationResult="N";
					}
				$x++;
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