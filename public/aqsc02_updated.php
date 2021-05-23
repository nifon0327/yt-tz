<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="安全管理制度文档";		//需处理
$upDataSheet="$DataPublic.aqsc02";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				
		include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				
		include "../model/subprogram/updated_model_3d.php";		break;
	default:
	    $checkSql = "SELECT A.Id AS TypeId FROM $DataPublic.aqsc01 A 
					   LEFT JOIN $DataPublic.aqsc01 B ON B.PreItem=A.Id
					   WHERE A.Name='$doucmentName' AND B.Id IS NULL";
					   
		$checkResult=mysql_query($checkSql,$link_id);
		if($checkRow=mysql_fetch_array($checkResult)){
			$TypeId=$checkRow["TypeId"];
			if($Attached!=""){//有上传文件
				$FileType=substr("$Attached_name", -4, 4);
				$OldFile=$Attached;
				$FilePath="../download/aqsc/";
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
			$SetStr="Caption='$Caption',TypeId='$TypeId',Date='$DateTime',Operator='$Operator',modified='$DateTime',modifier='$Operator',Locks='0' $AttachedSTR";
			include "../model/subprogram/updated_model_3a.php";
			}
		else{
			$Log="<div class='redB'>分类错误！$checkSql</div><br>";
			$OperationResult="N";
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>