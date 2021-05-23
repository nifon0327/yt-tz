<?php  
//已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="开发费用";			//需处理
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
//$ItemArray=explode("~",$ItemStr);
//$ItemId=$ItemArray[0];
$Provider=FormatSTR($Provider);
$Description=addslashes(FormatSTR($Description));
$Remark=addslashes(FormatSTR($Remark));
$ItemName=FormatSTR($ItemName);
$Date=date("Y-m-d");

$YearTemp=substr($Date,0,4);
$YearStr=$YearTemp."%";
$maxTemp=mysql_query("SELECT MAX(ItemId) AS MaxId FROM $DataIn.cwdyfsheet WHERE ItemId LIKE '$YearStr'",$link_id);
$ItemId=mysql_result($maxTemp,0,"MaxId");
if($ItemId==0){
	$ItemId=$YearTemp."90001";
	}
else{
	$ItemId=$ItemId+1;
	}

$inRecode="INSERT INTO $DataIn.cwdyfsheet (Id,Mid,ItemId,ItemName, CompanyId, TypeID,ModelDetail,Description,Amount,OutAmount,Currency,Remark,Provider,Bill,Estate,Locks,Date,Operator) VALUES (NULL,'0','$ItemId','$ItemName','$CompanyId','$TypeID','$ModelDetail','$Description','$Amount','0.00','$Currency','$Remark','$Provider','0','1','1','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){
	$Log="$TitleSTR 成功! <br>";
	//上传图档
	if($Attached!=""){//有上传文件
		$OldFile=$Attached;
		$PreFileName="DYF".$Id.".jpg";
		$FilePath="../download/dyf/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！<br>";
			$sql = "UPDATE $DataIn.cwdyfsheet SET Bill='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！ </div><br>";
			$OperationResult="N";			
			}
		}
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
