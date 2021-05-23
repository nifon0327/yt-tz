<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployj
$DataPublic.staffmain
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="排班资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
for($i=0;$i<count($checkid);$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
	
			$mySql="SELECT J.Id,J.Number,M.Name,C.Name as pbName,J.Operator
					FROM $DataIn.pbSetSheet J 
					LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
					Left Join $DataPublic.pbSheet C On C.Id = J.pbType
					WHERE Id = '$Id' ORDER BY J.Id DESC";
			$myResult = mysql_query($mySql);
			$myRow = mysql_fetch_assoc($myResult);
			$Name = $myRow["Name"];
			$Number = $myRow["Number"];
	
			///////////
			$DelSql= "Delete From $DataIn.pbSetSheet Where Id = '$Id'";
			$DelResult = mysql_query($DelSql);
			if($DelResult && mysql_affected_rows()>0){
				$Log.="$x-员工 $Name/$Number 的 $TitleSTR 成功.<br>";$y++;
				}
			else{
				$Log.="<div class='redB'>$x-员工 $Name/$Number 的 $TitleSTR 失败. $DelSql </div><br>";$OperationResult="N";
				}
			///////////
			$x++;
			}
		}
//条件：生效月份之后的薪资没有结付的且为最后一条记录,删除成功需改为人事表中的部门ID
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.redeployj");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>