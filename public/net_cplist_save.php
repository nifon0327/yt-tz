<?php 
//电信-ZX  2012-08-01
//$DataPublic.net_cpdata 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="设备资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$CpName=FormatSTR($CpName);
$Model=FormatSTR($Model);
$SSNumber=FormatSTR($SSNumber);
$IpAddress=FormatSTR($IpAddress);
$MacAddress=FormatSTR($MacAddress);

if($Attached!=""){//有上传文件
	$FileType=substr("$Attached_name", -4, 4);
	$OldFile=$Attached;
	$FilePath="../download/cpreport/";
	if(!file_exists($FilePath)){
		makedir($FilePath);
		}
	$PreFileName=$CpName.$FileType;
	$AttachedName=UploadFiles($OldFile,$PreFileName,$FilePath);
	if ($Attached!=""){		
		$Log="附件上传成功.<br>";
		}
	else{
		$Log="<div class='redB'>附件上传失败！</div><br>";
		$OperationResult="N";
		}
	}
//增加新职位
$inRecode="INSERT INTO $DataPublic.net_cpdata (Id,CpName,TypeId,IpAddress,MacAddress,CompanyId,Model,SSNumber,BuyDate,Warranty,User,Remark,Attached,Locks,Date,Operator) VALUES (NULL,'$CpName','$TypeId','$IpAddress','$MacAddress','$CompanyId','$Model','$SSNumber','$BuyDate','$Warranty','$User','$Remark','$AttachedName','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
