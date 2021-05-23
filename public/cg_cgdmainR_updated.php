<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet 
*/
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="需求单";		//需处理
$upDataSheet="$DataIn.cg1_stocksheet";	//需处理
$Log_Funtion="加急";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch ($ActionId){
	case 21://如果原来为加急，再点时则取消加急
		$Log_Funtion="加急";
		$Type=7;
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Check7Sql=mysql_query("SELECT E.Id FROM $DataIn.cg2_orderexpress E LEFT JOIN $upDataSheet S ON S.StockId=E.StockId WHERE S.Id='$Id' AND E.Type='$Type'",$link_id);
				if($Check7Row=mysql_fetch_array($Check7Sql)){//取消加急
					$DelSql="DELETE FROM $DataIn.cg2_orderexpress WHERE Type='$Type' AND StockId=(SELECT StockId FROM $upDataSheet WHERE Id='$Id')";
					$DelResult=mysql_query($DelSql);
					if($DelResult){
						$Log.="&nbsp;&nbsp;需求单(ID为 $Id)取消加急状态.</br>";
						}
					else{
						$Log.="<div class='redB'>&nbsp;&nbsp;需求单(ID为 $Id)取消加急状态失败. $DelSql </div></br>";
						$OperationResult="N";
						}
					
					}
				else{//加急
					$inRecode=$DataIn !== 'ac' ? "INSERT INTO $DataIn.cg2_orderexpress SELECT NULL,StockId,'$Type','$DateTime','$Operator' FROM $upDataSheet WHERE Id='$Id'" : 
					                             "INSERT INTO $DataIn.cg2_orderexpress SELECT NULL,StockId,'$Type','$DateTime','$Operator',1,0,0,'$Operator','$DateTime','$Operator','$DateTime' FROM $upDataSheet WHERE Id='$Id'";
					$inResult=@mysql_query($inRecode);
					if($inResult){
						$Log.="&nbsp;&nbsp;需求单(ID为 $Id)设为加急.</br>";
						}
					else{
						$Log.="<div class='redB'>&nbsp;&nbsp;需求单(ID为 $Id)设为加急失败. $inRecode </div></br>";
						$OperationResult="N";
						}
					}
				}
			}
		break;
	case 66://批量更新交货日期
		$Log_Funtion="批量更新交货日期";	$SetStr="DeliveryDate='$dDate'";				include "../model/subprogram/updated_model_3d.php";		break;
		break;
	}
//优化
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cg2_orderexpress");
//返回参数
$ALType="From=$From";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>