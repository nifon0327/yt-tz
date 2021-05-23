<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployb
$DataPublic.staffmain
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="部门调动资料";//需处理
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
		//读取此记录的调出部门，以便删除成功后还原人事表中的部门ID
		$CheckSql=mysql_query("SELECT B.Month,B.Number,B.ActionOut,M.Name FROM $DataPublic.redeployb B,$DataPublic.staffmain M WHERE B.Id='$Id' AND M.Number=B.Number  LIMIT 1",$link_id);
		if($CheckRow=mysql_fetch_array($CheckSql)){
			$Month=$CheckRow["Month"];
			$Number=$CheckRow["Number"];
			$Name=$CheckRow["Name"];
			$ActionOut=$CheckRow["ActionOut"];
			///////////
			/*
			$DelSql= "DELETE $DataPublic.redeployb FROM $DataPublic.redeployb,
				(SELECT MAX(Id) AS MAXId FROM $DataPublic.redeployb WHERE Number='$Number') A,
				(SELECT MAX(Month) AS MaxMonth FROM $DataIn.cwxzsheet WHERE Number='$Number' AND Estate=0) B
				WHERE Id='$Id' 
				AND (Id=A.MAXId OR A.MAXId IS NULL)
				AND (B.MaxMonth IS NULL OR B.MaxMonth<'$Month')";
				*/
				
			$DelSql= "DELETE M FROM $DataPublic.redeployb M,
				(SELECT MAX(Id) AS MAXId FROM $DataPublic.redeployb WHERE Number='$Number') A,
				(SELECT MAX(Month) AS MaxMonth FROM $DataIn.cwxzsheet WHERE Number='$Number' AND Estate=0) B
				WHERE M.Id='$Id' 
				AND (M.Id=A.MAXId OR A.MAXId IS NULL)
				AND (B.MaxMonth IS NULL OR B.MaxMonth<'$Month')";
				
				
			$DelResult = mysql_query($DelSql);
			if($DelResult && mysql_affected_rows()>0){
				$Log.="$x-员工 $Name/$Number 的 $TitleSTR 成功.<br>";$y++;
				//恢复部门ID
				$UpSql="UPDATE $DataPublic.staffmain SET BranchId='$ActionOut' WHERE Number='$Number' LIMIT 1";
				$UpResult=mysql_query($UpSql);
				if($UpResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;人事资料表中的部门ID复原成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;$nbsp;人事资料表中的部门ID复原失败. $UpSql</div><br>";$OperationResult="N";
					}
				}
			else{
				$Log.="<div class='redB'>$x-员工 $Name/$Number 的 $TitleSTR 失败. $DelSql </div><br>";$OperationResult="N";
				}
			///////////
			$x++;
			}
		}
	}
//条件：生效月份之后的薪资没有结付的且为最后一条记录,删除成功需改为人事表中的部门ID
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.redeployb");
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>