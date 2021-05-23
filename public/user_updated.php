<?php 
//电信-EWEN
//代码共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="用户资料";		//需处理
$upDataSheet="$DataIn.usertable";	//需处理
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
		$KillOnline=1;
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3b.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		//检查是否要上传印章
		$uPwd=MD5($uPwd);
		$uName=Chop(trim($uName));
		$FilePath="../download/userseal/";
		if($Attached!=""){
			$OldFile=$Attached;
			$PreFileName="u".$Number.".gif";
			$uSeal=UploadFiles($OldFile,$PreFileName,$FilePath);
			if($uSeal){
				$Log="&nbsp;&nbsp;&nbsp;&nbsp;上传印章成功！<br>";
				$uSealSTR=",uSeal='1'";
				}
			else{
				$Log="<div class=redB>&nbsp;&nbsp;&nbsp;&nbsp;上传印章失败！</div><br>";
				$OperationResult="N";
				$uSealSTR="";
				}
			}
		//检查是否要去除旧印章
		if($uSealSTR=="" && $oldAttached!=""){//没有上传且要求删除
			$oldAttachedSTR=$FilePath.$oldAttached;
			unlink($oldAttachedSTR);
			$uSealSTR=",uSeal='0'";
			}
		$uSign=$uSign==""?0:$uSign;
		$SetStr="uName='$uName',uPwd='$uPwd',Date='$DateTime',uSign='$uSign',Operator='$Operator'$uSealSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&uType=$uType&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>