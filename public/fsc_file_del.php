<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="FSC资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);

$FilePath="../download/fscdata/";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){		
		$FileResult = mysql_query("SELECT * FROM $DataPublic.cg3_fscdata WHERE Id=$Id",$link_id);
		if($FileRow = mysql_fetch_array($FileResult)){
			$FscId=$FileRow["Id"];
			$Attached =$FileRow["Attached"];
			$oldFilePath=$FilePath.$Attached;
			//清除表记录
			$DelSql = "DELETE FROM $DataPublic.cg3_fscdata WHERE Id='$FscId'"; 
			$DelResult = mysql_query($DelSql);
			if($DelResult){
				if(file_exists($oldFilePath)){//清除文件
					unlink($oldFilePath);
					}
				$Log.="ID号为 $Id 的FSC资料成功删除.<br>";
				}
			else{
				$Log.="<div class=redB>ID号为 $Id 的FSC资料删除失败.</div><br>";
				$OperationResult="N";
				}//end if ($DelResult)
			}//end if($FileRow = mysql_fetch_array($FileResult))
		else{
			$Log.="<div class=redB>ID号为 $Id 的FSC资料不存在.</div><br>";
			$OperationResult="N";
			}
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>