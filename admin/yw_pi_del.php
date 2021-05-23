<?php   
//电信-zxq 2012-08-01
//$DataIn.yw3_pisheet,$DataIn.yw3_piatt 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
//$_SESSION["nowWebPage"]=$nowWebPage; 
$_SEESION["nowWebPage"] = $nowWebPage;
//步骤2：
$Log_Item="PI资料";//需处理
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
		//删除数据库记录
		$delSql = "DELETE $DataIn.yw3_pisheet,$DataIn.yw3_piatt FROM $DataIn.yw3_pisheet LEFT JOIN $DataIn.yw3_piatt ON $DataIn.yw3_pisheet.PI=$DataIn.yw3_piatt.PI WHERE $DataIn.yw3_pisheet.PI='$Id'"; 
		$delRresult = mysql_query($delSql);
		if ($delRresult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp; $x - ID号为 $Id 的 $Log_Item 删除操作成功.<br>";
			//删除文件
			$Image1Path="../admin/pipdf/".$Id.".pdf";
			if(file_exists($Image1Path)){
				unlink($Image1Path);
				}
			$y++;
			}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>$x - &nbsp;&nbsp;ID号为 $Id 的 $Log_Item 删除操作失败.</div><br>";
			}//end if ($Del_result)
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.yw3_pisheet,$DataIn.yw3_piatt");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>