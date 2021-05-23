<?php   
include "../model/modelhead.php";
$fromWebPage=$funFrom."_m";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="配件采购单锁定";		//需处理
$Log_Funtion="审核";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
//步骤3：需处理，更新操作
$x=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		$x++;
		}
	}
switch($ActionId){
         case 17:
                    $sql = "UPDATE  $DataIn.cg1_lockstock SET Estate=0 WHERE Id IN($Ids) ";
					$result = mysql_query($sql);
					if ($result && mysql_affected_rows()>0){
						$Log.="&nbsp;&nbsp;锁定配件采购单审核通过.</br>";
						}
                 else{
                        $OperationResult="N";
						$Log.="&nbsp;&nbsp;锁定配件采购审核失败$sql</br>";
                       }
            break;
        case 15:
                    $sql = "UPDATE  $DataIn.cg1_lockstock SET Estate=2 ,ReturnReasons='$ReturnReasons' WHERE Id IN($Ids) ";
					$result = mysql_query($sql);
					if ($result && mysql_affected_rows()>0){
						$Log.="&nbsp;&nbsp;锁定配件采购单退回成功.</br>";
						}
                 else{
                        $OperationResult="N";
						$Log.="&nbsp;&nbsp;锁定配件采购退回失败$sql</br>";
                       }
            break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
?>