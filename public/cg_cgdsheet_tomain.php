<?php  
include "../model/modelhead.php";
//步骤2：
$Log_Item="采购单";			//需处理
$funFrom="cg_cgdmain";
//$fromWebPage=$funFrom."_read";
$fromWebPage="cg_cgdsheet_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$checkGysPayMode=mysql_fetch_array(mysql_query("SELECT GysPayMode FROM $DataIn.trade_object WHERE CompanyId='$CompanyId' LIMIT 1",$link_id));
$GysPayMode=$checkGysPayMode["GysPayMode"];
//$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&BuyerId=$Number&CompanyId=$CompanyId&GysPayMode=$GysPayMode";
$ALType="bigClass=$bigClass&TypeId=$TypeId&fromWebPage=$fromWebPage&TradeNo=$TradeNo&OrderNo=$OrderNo&Number=$Number&CompanyId=$CompanyId&BuildNo=$BuildNo&OrderPO=$OrderPO";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");
$DateTemp=date("Y");
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}

//生成采购单
include "cg_cgdsheet_tomain_sub.php";

//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

include "../model/logpage.php";
?>
