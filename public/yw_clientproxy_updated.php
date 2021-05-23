<?php 
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="客户授权书";		//需处理
$upDataSheet="$DataIn.yw7_clientproxy";	//需处理
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
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
case 82:
		$Log_Funtion="产品连接";
		$Date=date("Y-m-d");
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			 $TypeIdSTR="and ProductId IN ($Ids)";
			 $delSql="delete from yw7_clientproduct where cId='$Id'";
			 $delResult=mysql_query($delSql);
		     $inRecode="INSERT INTO $DataIn.yw7_clientproduct SELECT NULL,ProductId,'$Id','1','0','0','$Operator','$DateTime','$Operator','$DateTime','$Date','$Operator' 
		     FROM $DataIn.productdata WHERE 1 $TypeIdSTR";
		    $inResult=@mysql_query($inRecode);
		    if($inResult){
			$Log.="$Ids&nbsp;&nbsp;产品连接成功! </br>";
		      	}
		    else{
			    $Log.="<div class='redB'>&nbsp;&nbsp;产品连接失败!</div></br>";
			    $OperationResult="N";
			  }
		}else{
			 $delSql="delete from yw7_clientproduct where cId='$Id'";
			 $delResult=mysql_query($delSql);
			 if($delResult){
			     $Log.="&nbsp;&nbsp;产品连接删除成功! </br>";
		      	}
		    else{
			    $Log.="<div class='redB'>&nbsp;&nbsp;产品连接删除失败!</div></br>";
			    $OperationResult="N";
			  }
		}
		break;

	default:
		if($Attached!=""){//有上传文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$FilePath="../download/clientproxy/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			if($oldAttached!=""){
				$PreFileName=$oldAttached;
				}
			else{
				$datelist=newGetDateSTR();
				$PreFileName=$datelist.$FileType;
				}
			$upAttached=UploadPictures($OldFile,$PreFileName,$FilePath);
			if($upAttached!=""){
				$AttachedSTR=",Attached='$PreFileName'";
				}
			}
		$Caption=FormatSTR($Caption);
		$SetStr="CompanyId='$CompanyId',Caption='$Caption',TimeLimit='$TimeLimit',Date='$DateTime',Operator='$Operator',Locks='0' $AttachedSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>