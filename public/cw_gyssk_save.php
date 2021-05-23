<?php 
//电信-zxq 2012-08-01
/*
MC、DP共享代码
*/
//步骤1：
include "../model/modelhead.php";
//步骤2：
$Log_Item="供应商税款记录";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Forshort=FormatSTR($Forshort);
$InvoiceNUM=FormatSTR($InvoiceNUM);
$Remark=FormatSTR($Remark);




$inRecode="INSERT INTO $DataIn.cw2_gyssksheet (Id,Mid,Forshort,PayMonth,Currency,InvoiceNUM,InvoiceFile,InvoiceCollect,Amount,Rate,Fpamount,Getdate,Remark,Date,Estate,Locks,Operator) 
VALUES (NULL,'0','$Forshort','$PayMonth','$Currency','$InvoiceNUM','0','0','$Amount','$Rate','$Fpamount','0000-00-00','$Remark','$theDate','1','1','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
if ($inAction){
	$Log.="&nbsp;&nbsp; $TitleSTR 成功! $inRecode <br>";
	$Id=mysql_insert_id();
	//上传文件
	if($Attached!=""){//有上传文件
		$FileType=".pdf";
		$OldFile=$Attached;
		$FilePath="../download/cwgyssk/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="S".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功!<br>";
			$Attached=1;
			//更新刚才的记录
			$sql = "UPDATE $DataIn.cw2_gyssksheet SET InvoiceFile='1',Getdate='$Getdate' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败!</div><br>";
			$OperationResult="N";			
			}
		}

	if($_POST['ListId']){//如果指定了操作对象
		$Counts=count($_POST['ListId']);
		for($i=0;$i<$Counts;$i++){
			$thisId=$_POST[ListId][$i];
			$addRecodes="INSERT INTO $DataIn.cw2_gysskrelation (Id,Mid,nonbom6_sID,Date,Operator) VALUES (NULL,'$Mid','$thisId','$theDate','$Operator')";
			//echo ($addRecodes);
			$addAction=@mysql_query($addRecodes);
			if($addAction){
				$Log.="$Mid 关联申购单成功: $thisId.<br>";
			}
			else {
				$Log.="$Mid 关联申购单失败: $thisId.<br>";
			}
		}
	}
			
}
else{
	$Log.="<div class=redB>&nbsp;&nbsp; $TitleSTR 失败!/div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
