<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="装箱数据";			//需处理
$nowWebPage=$funFrom."_packingsave";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&CompanyId=$CompanyId";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

//先删除原有数据
$delOld = mysql_query("DELETE FROM $DataIn.ch1_deliverypacklist WHERE Mid='$Id'"); 
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch1_deliverypacklist");
//行数|订单或样品ID|装箱数量|箱数|总数|毛重|外箱尺寸
$Field=explode(",",$PackingList);
$Count=count($Field);
for($i=1;$i<$Count;$i++){
	$RowData=explode("^^",$Field[$i]);
	$BoxRow=$RowData[0]==""?0:$RowData[0];
	$POrderId=$RowData[1];	
	$BoxPcs=$RowData[2];
	$BoxQty=$RowData[3];	
	$FullQty=$RowData[4]==""?0:$RowData[4];
	$WG=$RowData[5]==""?0:$RowData[5];
	$BoxSpec=$RowData[6];
	if($BoxQty==""){
		$BoxQty=$oldBoxQty;
		}
	else{
		$oldBoxQty=$BoxQty;
		}
	$inRecode=$inRecode==""?"INSERT INTO $DataIn.ch1_deliverypacklist (Id,Mid,POrderId,BoxRow,BoxPcs,BoxQty,WG,FullQty,BoxSpec,Locks) VALUES (NULL,'$Id','$POrderId','$BoxRow','$BoxPcs','$BoxQty','$WG','$FullQty','$BoxSpec','1')":$inRecode.",(NULL,'$Id','$POrderId','$BoxRow','$BoxPcs','$BoxQty','$WG','$FullQty','$BoxSpec','1')";
	}
$inAction=@mysql_query($inRecode);
if ($inAction){
	include "billtopdf/ch_shipout_tobill.php";
	$Log.="装箱数据写入数据库成功!";
	}
else{
	$Log="<div class='redB'>装箱数据写入数据库失败! $inRecode</div>";
	$OperationResult="N";
	}
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>