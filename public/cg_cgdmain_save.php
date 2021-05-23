<?php 
//电信-zxq 2012-08-01
//步骤1：
include "../model/modelhead.php";
//步骤2：
$Log_Item="采购单";			//需处理
$funFrom="cg_cgdmain";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$checkGysPayMode=mysql_fetch_array(mysql_query("SELECT GysPayMode FROM $DataIn.trade_object WHERE CompanyId='$CompanyId' LIMIT 1",$link_id));
$GysPayMode=$checkGysPayMode["GysPayMode"];
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&BuyerId=$Number&CompanyId=$CompanyId&GysPayMode=$GysPayMode";
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
for($i=1;$i<=$IdCount;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
//自动单号计算
//锁定
//$LockSql=" LOCK TABLES $DataIn.cg1_stockmain WRITE";$LockRes=@mysql_query($LockSql);
$Bill_Temp=mysql_query("SELECT MAX(PurchaseID) AS maxID FROM $DataIn.cg1_stockmain WHERE PurchaseID LIKE '$DateTemp%'",$link_id); 
$PurchaseID =mysql_result($Bill_Temp,0,"maxID");
if ($PurchaseID ){
	$PurchaseID =$PurchaseID+1;}
else{
	$PurchaseID =$DateTemp."00001";
	}
//保存主采购单资料
$inRecode="INSERT INTO $DataIn.cg1_stockmain (Id,CompanyId,BuyerId,PurchaseID,DeliveryDate,Remark,Date,Operator) VALUES (NULL,'$CompanyId','$Number','$PurchaseID','0000-00-00','$Remark','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
if($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	$Sql = "UPDATE $DataIn.cg1_stocksheet SET Mid='$Mid',Locks=0 WHERE Id IN ($Ids) AND Estate='0'";
	$Result = mysql_query($Sql);
	if($Result){
		$Log.="需求单明细 ($Ids) 加入主采购单 $Mid 成功!<br>";
		//备份初始采购单
		echo"<script language='javascript'>retCode1=openUrl('cg_cgdmain_tohtml.php?Id=$Mid');</script>";
		}
	else{
		$Log.="<div class=redB>需求单明细 ($Ids) 加入主采购单 $Mid 失败!</div><br>";
		$OperationResult="N";
		}
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
