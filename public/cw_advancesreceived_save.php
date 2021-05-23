<?php 
//电信-zxq 2012-08-01
//步骤1： $DataIn.cw6_advancesreceived  二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="预收货款";			//需处理
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
$inRecode="INSERT INTO $DataIn.cw6_advancesreceived (Id,BankId,Mid,CompanyId,Amount,Remark,PayDate,Attached,Locks,Operator) VALUES (NULL ,'$BankId', '0', '$CompanyId','$Amount','$Remark','$PayDate','','1','$Operator')";
$inAction=@mysql_query($inRecode);
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	$Id=mysql_insert_id();
	//上传文件
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/cwadvance/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="H".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			$sql = "UPDATE $DataIn.cw6_advancesreceived SET Attached='$Attached' WHERE Id=$Id";
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
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
