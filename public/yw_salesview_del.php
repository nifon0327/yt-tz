<?php 
//电信-zxq 2012-08-01
//步骤1：$DataIn.yw6_salesview 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="业务查询权限";//需处理
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
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
	
if($Ids!=""){
	//删除数据库记录
	$Del = "DELETE FROM $DataIn.yw6_salesview WHERE Id IN ($Ids)"; 
	$result = mysql_query($Del);
	if ($result && mysql_affected_rows()>0){
		$Log="ID号为 ($Ids) 的 $TitleSTR 成功。<br>";
		}
	else{
		$Log="<div class='redB'>ID号为 ($Ids) 的 $TitleSTR 失败。</div><br>";
		$OperationResult="N";			
		}//end if ($result)
	//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.yw6_salesview");
	}
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>