<?php 
/*电信---yang 20120801
$DataIn.sc1_cjtj
二合一已更新
*/
//步骤1：初始化参数、页面基本信息及CSS、javascrip函数
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="工序登记记录";//需处理
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
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		//取相关的POrderId号
		$DelSql = "DELETE FROM $DataIn.sc1_gxtj WHERE Id='$Id'";
		$DelResult = mysql_query($DelSql);
		if($DelResult && mysql_affected_rows()>0){
			$Log.="ID号为 $Id 的 $Log_Item 删除操作成功.<br>";
		    $Log.="<div class='redB'>删除最后一道工序数量时，同时需要手动减少车间生产登记数量.</div>";
			}
		else{
			$Log.="<div class='redB'>ID号为 $Id 的 $Log_Item 删除操作失败. </div><br>";
			$OperationResult="N";
			}		
		}
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.sc1_cjtj");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth&Ptype=$Ptype";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>