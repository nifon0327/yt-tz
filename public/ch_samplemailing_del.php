<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ch10_samplemail
$DataIn.ch10_samplepicture
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="客户样品寄送资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$DelSql= "DELETE FROM $DataIn.ch10_samplemail WHERE 1 AND Estate=1 AND Id='$Id'";
		$DelResult = mysql_query($DelSql);
		if($DelResult && mysql_affected_rows()>0){
			$Log="$x ID号为 $Id 的 $TitleSTR 成功.<br>";
			$FilePath2="../download/samplemail/Schedule".$Id.".jpg";//清除进度图片
			if(file_exists($FilePath2)){
				unlink($FilePath2);
				}
			$CheckSql=mysql_query("SELECT Picture FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'",$link_id);
			if($CheckRow=mysql_fetch_array($CheckSql)){
				do{
					$Picture=$CheckRow["Picture"];
					$FilePath="../download/samplemail/$Picture";
					echo $FilePath." q <br>";
					if(file_exists($FilePath)){
						unlink($FilePath);echo "删除";
						}
					}while($CheckRow=mysql_fetch_array($CheckSql));
				$DelImgSql="DELETE FROM $DataIn.ch10_samplepicture WHERE Mid='$Id'";
				$DelImgResult = mysql_query($DelImgSql);
				}
			$x++;
			}
		else{
			$Log="<div class='redB'>$x ID号为 $Id 的 $TitleSTR 失败.</div><br>";
			$OperationResult="N";
			}
		$y++;		
		}
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch10_samplemail,$DataIn.ch10_samplepicture");
//操作日志
$chooseDate=$x==$IdCount?"":$chooseDate;
$ALType="CompanyId=$CompanyId&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>