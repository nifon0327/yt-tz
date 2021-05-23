<?php 
/*电信---yang 20120801
$DataIn.yw1_ordermain
$DataIn.yw1_ordersheet
$DataIn.yw2_orderexpress
$DataIn.cg1_stocksheet
$DataIn.ck5_llsheet
$DataIn.ck9_stocksheet
分开已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="车间产生订单";		//需处理
$upDataSheet="$DataIn.yw1_ordersheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 61:
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Check7Sql=mysql_query("SELECT S.Id FROM $upDataSheet S WHERE S.Id='$Id' AND S.scFrom>0 AND S.scFrom=1 ",$link_id);//查找
				if($Check7Row=mysql_fetch_array($Check7Sql)){
					$updateSC="S.scFrom=2";$Log_Funtion="标记生产";
					}
				else{
					$updateSC="S.scFrom=1";$Log_Funtion="取消生产";
					}
//////////////////////////////////////////////////////////
				$updateSql="UPDATE $upDataSheet S SET $updateSC WHERE Id='$Id' AND S.scFrom>0";
				$updateResult=mysql_query($updateSql);
				if($updateResult){
					$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单".$Log_Funtion."成功.</br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单".$Log_Funtion."失败. </div></br>";
					$OperationResult="N";
					}
/////////////////////////////////////////////////////////
				}
			}
		break;
	case 89://备料
		$Log_Funtion="备料标记";
		$Lens=count($checkid);
		$Ids="";
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			$Ids=$Ids==""?$Id:($Ids.",".$Id);
			}
		//检查主ID
		$checkNum=mysql_query("",$link_id);
		$maxSql = mysql_query("SELECT IFNULL(MAX(Num),0) AS Num FROM $DataIn.yw9_blsheet",$link_id);
		$Num=mysql_result($maxSql,0,"Num");
		$Num+=1;

		$inRecode="INSERT INTO $DataIn.yw9_blsheet SELECT NULL,'$Num',POrderId,'1','$Date','$Operator' FROM $upDataSheet WHERE Id IN ($Ids)";
		$inResult=@mysql_query($inRecode);
		if($inResult){
			$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单备料标记成功.</br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单备料标记失败. </div></br>";
			$OperationResult="N";
			}
		break;
	default://更新订单资料OK
		break;
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.yw1_ordermain,$DataIn.cg1_stocksheet,$DataIn.ck5_llsheet,$DataIn.yw2_orderexpress");
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>