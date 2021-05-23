<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="其它出库记录";//需处理
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
	if ($Id!=""){			//先更新后删除
			
		$bfStateSql = "SELECT B.Estate,D.StuffCname FROM $DataIn.ck8_bfsheet B 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId = B.StuffId 
		WHERE B.Id = $Id";
		$bfStateResult = mysql_query($bfStateSql,$link_id);
		$bfStateRows = mysql_fetch_array($bfStateResult);
		$StuffCname= $bfStateRows['StuffCname'];	
		if($bfStateRows['Estate'] == 0){
		
		    $OperationResult="N";
			$Log.="<div class='redB'>配件名为: $StuffCname 的其它出库记录已审核，不能删除 </div><br>";
		}
		if($bfStateRows['Estate'] >0){
			//删除记录
			$delSql = "DELETE FROM $DataIn.ck8_bfsheet WHERE Id='$Id' AND Estate>0 "; 
			$delResult = mysql_query($delSql);
			if($delResult && mysql_affected_rows()>0){
				$Log.="$x -配件名为: $StuffCname 的其它出库记录删除成功.<br>";
				$y++;
				}
			else{
				$Log.="<div class='redB'>$x -配件名为: $StuffCname 的其它出库记录删除失败. $delSql </div><br>";
				$OperationResult="N";
				}
			}
	     $x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)

if($y==$IdCount){
	$chooseDate="";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>