<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件申领记录";		//需处理
$upDataSheet="$DataIn.nonbom8_outsheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 156:
	$Lens=count($checkid);
	for($i=0;$i<$Lens;$i++){
		$Id=$checkid[$i];
		if ($Id!=""){
			$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
			}
		}
		$Log_Funtion="收到确认";	
        $UpdateSql="UPDATE  $DataIn.nonbom8_outsheet  M  
        LEFT JOIN $DataIn.nonbom8_outfixed  F ON F.OutId=M.Id  
        SET M.Confirm=0,F.Estate=0  WHERE  M.Id IN ($Ids)";
        $updateResult=@mysql_query($UpdateSql);
          if($updateResult){
			   $Log=$Log_Item.$Log_Funtion."成功.<br>";
               }
		else{
			$Log="<div class='redB'>".$Log_Item.$Log_Funtion."失败. $UpdateSql</div><br>";
			$OperationResult="N";
			}
    	break;
	default://ewen 2013-03-27 更新：不限制数量的变化
		$updateSQL = "UPDATE $upDataSheet A SET A.Qty='$Qty',A.WorkAdd='$WorkAdd',A.Remark='$Remark',A.Date='$slDate',A.Operator='$Operator',A.Estate='2',A.Locks='0' WHERE A.Id='$Id' AND A.Locks='1'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log=$Log_Item.$Log_Funtion."成功.<br>";
			}
		else{
			$Log="<div class='redB'>".$Log_Item.$Log_Funtion."失败. $updateSQL</div><br>";
			$OperationResult="N";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&OperatorSign=$OperatorSign&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>