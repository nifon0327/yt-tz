<?php
include "../model/modelhead.php";
//步骤2：
$Log_Item="非bom采购单";			//需处理
$funFrom="nonbom5";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");

//判断是否能生成采购单
$err_ud = '';
foreach($StaffNoUd as $val){
	// if($val != $Login_P_Number){
	// 	$err_ud = '非采购员本人操作';
	// 	break;
	// }
}
if(!$err_ud){
	foreach($AuditStateUd as $val){
		if($val != 1){
			$err_ud = '非已审批';
			break;
		}
	}
}
if(!$err_ud) {
    foreach ($forShortUd as $val) {
        if ($val != $forShortUd[0]) {
			$err_ud = '非同一供应商';
			break;
		}
    }
}
if(!$err_ud){
    foreach ($mainTypeUd as $val) {
        if ($val != $mainTypeUd[0]) {
            $err_ud = '非同一主分类';
            break;
        }
    }
}
if($err_ud){
    $Log="<div class=redB>{$TitleSTR}{$err_ud}，保存失败！</div><br>";
    $OperationResult="N";
    $fromWebPage="nonbom6_read";
}
else{
	//步骤3：需处理
    $DateTemp=date("Y");
	$Lens=count($checkid);
	for($i=0;$i<$Lens;$i++){
		$Id=$checkid[$i];
		if($Id!=""){
			$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
    //自动单号计算
    $Bill_Temp=mysql_query("SELECT MAX(PurchaseID) AS maxID FROM $DataIn.nonbom6_cgmain WHERE PurchaseID LIKE '$DateTemp%'",$link_id);
    $PurchaseID =mysql_result($Bill_Temp,0,"maxID");
    if ($PurchaseID ){
        $PurchaseID =$PurchaseID+1;
    }else{
        $PurchaseID =$DateTemp."0001";
    }

    mysql_query('BEGIN');
	$insertFlag=0;
	//$changeBuyerID=$StaffNoUd[0];
	$changeBuyerID=$Operator;
	$inRecode="INSERT INTO $DataIn.nonbom6_cgmain (Id,mainType,PurchaseID,CompanyId,BuyerId,taxAmount,shipAmount,Attached,Remark,Locks,Date,Operator) VALUES
       (NULL,'$mainTypeUd[0]','$PurchaseID','$forShortUd[0]','$changeBuyerID','0','0','0','$Remark','0','$DateTime','$Operator')";
	$inAction=@mysql_query($inRecode);
	$Mid=mysql_insert_id();
	if($inAction && mysql_affected_rows()>0){
		$Log="$TitleSTR 成功!<br>";
		$Sql = "UPDATE $DataIn.nonbom6_cgsheet A
		LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId
		SET A.Mid='$Mid',A.Locks='0',B.oStockQty=B.oStockQty+A.Qty WHERE A.Id IN ($Ids) AND A.Estate='1' AND A.Mid='0'";//已审核的才加入采购单，同时更新采购库存
		$Result = mysql_query($Sql);
		if($Result && mysql_affected_rows()>0 && $Mid>0){
			$Log.="需求单明细 ($Ids) 加入主采购单 $Mid 成功!<br>";
            mysql_query('COMMIT');
		}else{
			$Log.="<div class=redB>需求单明细 ($Ids) 加入主采购单 $Mid 失败! <br/> $Sql </div>";
            mysql_query('ROLLBACK');
		}
   
	}else{
		$Log=$Log."<div class=redB>$TitleSTR 失败! <br> $inRecode </div>";
		$OperationResult="N";
		$fromWebPage="nonbom6_read";
        mysql_query('ROLLBACK');
	}
}
//步骤4：
$LogSql="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($LogSql);
include "../model/logpage.php";
?>
