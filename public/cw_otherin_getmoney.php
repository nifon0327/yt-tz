<?php   
include "../model/modelhead.php";
$Log_Item="收款单资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_topdf";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$chooseDate=substr($rkDate,0,7);
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
//保存主单资料
$mainInSql="INSERT INTO $DataIn.cw4_otherinsheet (Id,Mid,TypeId,getmoneyNO,Amount,Currency,Remark,payDate,Estate,Locks,Date,Operator,creator,created) VALUES(NULL,'0','$TypeId','$getmoneyNO','0','$Currency','$Remark','$payDate','3','0','$payDate','$Operator','$Operator',NOW())";
$mainInAction=@mysql_query($mainInSql);
$Mid=mysql_insert_id();
if($mainInAction){
	     $Log.="收款主单($Mid)创建成功.<br>";
		$pUpSql=mysql_query("UPDATE $DataIn.cw4_otherin SET Mid='$Mid',Estate='0' WHERE Id IN ($Ids)");
		if($pUpSql && mysql_affected_rows()>0){
                  $UpdateSql="UPDATE  $DataIn.cw4_otherinsheet   M 
                      LEFT JOIN ( SELECT  Mid,SUM(Amount) AS SumAmount FROM $DataIn.cw4_otherin WHERE Mid=$Mid ) S  ON S.Mid=M.Id 
                      SET  M.Amount=S.SumAmount
                     WHERE M.Id=$Mid";
                $UpdateResult=@mysql_query($UpdateSql);
			       $Log.="收款项目($Ids)的已处理状态更新成功.<br>";
			}
		else{
			$Log.="<div class='redB'>收款项目($Ids)的已处理状态更新失败. $pUpSql </div><br>";
			$OperationResult="N";
			}
	   $Id=$Mid;
	    include "cw_otherin_topdf.php";
	}
else{
	$Log.="<div class='redB'>收款主单($Mid)创建失败. $mainInSql </div><br>";
	$OperationResult="N";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&TypeId=$TypeId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>