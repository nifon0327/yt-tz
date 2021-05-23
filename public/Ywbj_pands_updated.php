<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";

ChangeWtitle("$SubCompany 产品BOM保存");
$fromWebPage="ywbj_pands_read";
$nowWebPage="ywbj_pands_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$url="ywbj_pands_read";
$Log_Item="产品BOM";
$Log_Funtion="设置产品BOM";
$ALType="From=$From";
//锁定表


$StuffArray=explode("|",$SIdList);
$Count=count($Price);
for ($i=0;$i<$Count;$i++){
	$Sid=$StuffArray[$i];
	$SidStr=$SidStr==""?$Sid:$SidStr.",".$Sid;
	}
$DelSql = "DELETE FROM $DataIn.ywbj_pands WHERE Pid='$Pid' AND Sid NOT IN($SidStr)"; 
$DelResult = mysql_query($DelSql);
$x=1;
for ($i=0;$i<$Count;$i++){
	$Sid=$StuffArray[$i];
	$Sprice=$Price[$i];
	$CheckSidSql=mysql_query("SELECT Id FROM $DataIn.ywbj_pands WHERE Pid='$Pid' AND Sid='$Sid' LIMIT 1",$link_id);
	if($CheckSidRow=mysql_fetch_array($CheckSidSql)){//更新
		$updateSQL = "UPDATE $DataIn.ywbj_pands SET Sprice='$Sprice' WHERE Pid='$Pid' AND Sid='$Sid'";
		$updateResult = mysql_query($updateSQL);
		}
	else{
		//插入新的关系	
		$IN_recodeN="INSERT INTO $DataIn.ywbj_pands (Id,Pid,Sid,Sprice,Simg,Date,Operator) VALUES (NULL,'$Pid','$Sid','$Sprice','0','$DateTime','$Operator')";
		$resN=@mysql_query($IN_recodeN);
		if($resN){
			$Log.="&nbsp;&nbsp; $x -配件ID号为 $Sid 的配件已加入产品 $Pid 的BOM!</br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp; $x -配件ID号为 $Sid 的配件未能加入产品 $Pid 的BOM! $IN_recodeN</div></br>";
			}
		}
	$x++;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>