<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="模板文件";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//读取附件
		$Result = mysql_query("SELECT Attached FROM $DataPublic.workmodelfile WHERE Id=$Id ORDER BY Id LIMIT 1",$link_id);
		if ($myrow = mysql_fetch_array($Result)) {
			$Attached=$myrow["Attached"];
			//删除数据库记录
			$Del = "DELETE FROM $DataPublic.workmodelfile WHERE Id=$Id LIMIT 1"; 
			$result = mysql_query($Del);
			if ($result){
				if($Attached!=""){//删除附件
					$FilePath="../download/modelfile/$Attached";
					if(file_exists($FilePath)){
						unlink($FilePath);
						}
					}
				$Log.="&nbsp;&nbsp; $x - ID号为 $Id 的 $Log_Item 删除操作成功.<br>";
				$y++;
				}
			else{
				$OperationResult="N";
				$Log.="<div class='redB'>$x - &nbsp;&nbsp;ID号为 $Id 的 $Log_Item 删除操作失败.</div><br>";
				}//end if ($Del_result)
			$x++;
			}
		}
	}//end for($i=1;$i<$IdCount;$i++)
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.workmodelfile");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>