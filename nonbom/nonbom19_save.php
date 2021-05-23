<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="新增维修";			//需处理
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
$TypeName=FormatSTR($TypeName);
$chinese=new chinese;
$Letter=substr($chinese->c($TypeName),0,1);
$Date=date("Y-m-d");

$inRecode="INSERT INTO $DataIn.nonbom7_repair (Id,BarCode,WxNumber,WxCompanyId,WxReason,WxDate,Picture,Estate,Date,Operator) VALUES (NULL,'$BarCode','$WxNumber','$WxCompanyId','$WxReason','$WxDate','0','1','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	$Id=mysql_insert_id();
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/nonbom19/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="C".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			$Attached=1;
			//更新刚才的记录
			$sql = "UPDATE $DataIn.nonbom7_care SET Picture='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
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
