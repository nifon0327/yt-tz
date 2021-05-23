<?php 
include "../model/modelhead.php";
$Log_Item="报废记录";		
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Remark=FormatSTR($Remark);	
$inRecode= "INSERT INTO $DataIn.ck8_bfsheet  SELECT NULL,'$ProposerId',StuffId,'$Qty','$LocationId','$Remark','$Type','0','','','1','$bfDate','1','0','$Operator','$DateTime',0,'$Operator','$DateTime','$Operator','$DateTime'
FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' and (oStockQty-mStockQty)>=$Qty and tStockQty>=$Qty";
$inAction=@mysql_query($inRecode);
if ($inAction && mysql_affected_rows()>0){ 
	$Log="$TitleSTR 成功!<br>";	
	$Id=mysql_insert_id();
	//上传文件
	if($Attached!=""){//有上传文件
		$FileType=".jpg";
		$OldFile=$Attached;
		$FilePath="../download/ckbf/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName="B".$Id.$FileType;
		$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		if($Attached){
			$Log.="&nbsp;&nbsp;单据上传成功！$inRecode <br>";
			$Attached=1;
			$sql = "UPDATE $DataIn.ck8_bfsheet SET Bill='1' WHERE Id=$Id";
			$result = mysql_query($sql);
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;单据上传失败！$inRecode </div><br>";
			$OperationResult="N";			
			}
		}
	} 
else{
	$Log="<div class=redB>$TitleSTR 失败(库存不足或其它)!</div> $inRecode <br>";
	$OperationResult="N";
	} 

$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>