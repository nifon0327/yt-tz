<?php 
//电信-ZX
//$DataIn.errorcasedata 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="出错案例";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//提取记录
		$checkPicture=mysql_fetch_array(mysql_query("SELECT Picture FROM $DataIn.errorcasedata WHERE Id='$Id' LIMIT 1",$link_id));
		$Picture=$checkPicture["Picture"];
		//删除数据库记录
		$Del = "DELETE FROM $DataIn.errorcasedata WHERE Id='$Id'"; 
		$result = mysql_query($Del);
		if ($result){
			$Log.="&nbsp;&nbsp;$x-ID号为 $Id 的 $TitleSTR 成功.<br>";
			$y++;
			$FilePath="../download/errorcase/".$Picture;
			if(file_exists($FilePath)){
				unlink($FilePath);
				}
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;$x-ID号为 $Id 的 $TitleSTR 失败.</div><br>";
			$OperationResult="N";
			}//end if ($result)
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>