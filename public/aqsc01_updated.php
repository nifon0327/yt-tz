<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="安全管理制度汇编记录";		//需处理
$upDataSheet="$DataPublic.aqsc01";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$Name=FormatSTR($Name);
		if($Name=="无"){
			$PreItem=0;
			$theGrade=1;
			$checkSort=mysql_fetch_array(mysql_query("SELECT Max(Sort+1) AS maxSort FROM $DataPublic.aqsc01 WHERE PreItem='$PreItem'",$link_id));
			$theSort=$checkSort["maxSort"];
			$SetStr="Grade='$theGrade',PreItem='$PreItem',Name='$AddName',Sort='$theSort',Date='$DateTime',Locks='0',Operator='$Operator'";
			include "../model/subprogram/updated_model_3a.php";
			}
		else{
			$checkSql=mysql_query("SELECT Id,Grade FROM $DataPublic.aqsc01 WHERE Name='$Name' LIMIT 1",$link_id);
			if($checkRow=mysql_fetch_array($checkSql)){
				$PreItem=$checkRow["Id"];
				$theGrade=$checkRow["Grade"]+1;
				$checkSort=mysql_fetch_array(mysql_query("SELECT Max(Sort+1) AS maxSort FROM $DataPublic.aqsc01 WHERE PreItem='$PreItem'",$link_id));
				$theSort=$checkSort["maxSort"];
				
				$SetStr="Grade='$theGrade',PreItem='$PreItem',Name='$AddName',Sort='$theSort',Date='$DateTime',Locks='0',Operator='$Operator'";
				include "../model/subprogram/updated_model_3a.php";
				}
			else{
				$Log=$Log."<div class=redB>读取不到分类名称为 $Name 的资料! </div><br>";
					$OperationResult="N";
				}
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>