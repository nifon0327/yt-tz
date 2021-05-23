<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_".$From;
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="FSC资料";		//需处理
$upDataSheet="$DataPublic.cg3_fscdata";	//需处理
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
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$FilePath="../download/fscdata/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		if($Attached!=""){
			if($oldAttached!=""){//先删除旧文件
				$oldFilePath=$FilePath.$oldAttached;
				if(file_exists($oldFilePath)){
					unlink($oldFilePath);
					}
				}		
			//上传新文件
			$FileType=substr("$Attached_name", -4, 4);
			$OldFile=$Attached;
			$PreFileName="fsc".$Id.$FileType;
			$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
			if($uploadInfo){//上传成功
				$UpSTR=",Attached='$PreFileName'";
				}
			else{//上传失败
				$UpSTR=",Attached=''";
				$Log="<div class=redB>&nbsp;&nbsp;附件上传失败!</div><br>";
				$OperationResult="N";			
				}
			}//结束文件处理
		$UpSql = "UPDATE $DataPublic.cg3_fscdata SET Remark='$Remark',Operator='$Operator' $UpSTR WHERE Id=$Id";
		$UpResult = mysql_query($UpSql);
		if($UpResult){
			$Log.="&nbsp;&nbsp;FSC备注更新成功!<br>";
			}
		else{
			$Log="<div class=redB>&nbsp;&nbsp;FSC资料更新失败!</div>";
			$OperationResult="N";	
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>