<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新领料记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT L.StockId,L.Qty,L.StuffId,D.StuffCname,S.OrderQty,U.Decimals,L.Type,L.sPOrderId 
FROM $DataIn.ck5_llsheet L 
LEFT JOIN $DataIn.stuffdata D ON L.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.cg1_stocksheet S ON L.StockId=S.StockId 
WHERE L.Id=$Id ORDER BY L.Id DESC LIMIT 1",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$StuffId=$upData["StuffId"];
	$StockId=$upData["StockId"];
	$Qty=$upData["Qty"];
	$OrderQty=$upData["OrderQty"];
	$StuffCname=$upData["StuffCname"];
	$Decimals=$upData["Decimals"];
	$Type=$upData["Type"];
	$sPOrderId=$upData["sPOrderId"];
	
	//领料情况				
	$llTemp=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);; 
	$llQty=mysql_result($llTemp,0,"llQty");
	$llQty=$llQty==""?0:$llQty;
	
   
	$upQty = $llQty - $OrderQty;
    $upQty=round($upQty,$Decimals);

	
	if($upQty==0){
		$unllQtyINFO="<span class='redB'>(已全部领料,不可增加减少领料数量的操作.)</span>";
		$OperatorsSTR = "";
		$ReadOnlyStr = "readonly";
		$unllQty = 0 ;
		
	}else if($upQty>0){
		
		if($upQty>$Qty){
			$unllQtyINFO="<span class='redB'>(多领数量$upQty 超出了本次领料数量$Qty,请直接删除)</span>";
		    $OperatorsSTR = "";
		    $ReadOnlyStr = "readonly";
		}else{
			$unllQtyINFO ="";
			$ReadOnlyStr ="";
			$OperatorsSTR ="减少";
		}
		$unllQty = 0 ;
		$titleSTR1 = "多领数量:";
		$titleSTR2 = "数量减少:";
		$OperatorAction ="0";
	}else{
		$unllQtyINFO="<span class='redB'>(还有未领料数,确定是否在此工单上增加？)</span>";
		$ReadOnlyStr = "readonly";
		$unllQty =$OrderQty-$llQty;
		$upQty =$unllQty;
		$OperatorsSTR ="增加";
		$titleSTR1 = "少领数量:";
		$titleSTR2 = "数量增加:";
		$OperatorAction ="1";
	}	
}

if($Type!=1){
		
		$unllQtyINFO="<span class='redB'>非工单领料</span>";
		$OperatorsSTR = "";
	}
	
$SaveSTR=$OperatorsSTR==""?"NO":"";
$CheckFormURL="thisPage";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth,TypeId,$TypeId,TempValue,";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="100" rowspan="10" class="A0010">&nbsp;</td>
    <td><p></td>
    <td>&nbsp;</td>
    <td width="100" rowspan="10" class="A0001">&nbsp;</td>
  </tr>
    <tr>
    <td width="92" height="30" align="right">工单流水号</td>
    <td><?php  echo $sPOrderId?>
    </td>
  </tr>
  <tr>
    <td width="92" height="30" align="right">采购流水号</td>
    <td><?php  echo $StockId?>
    <input name="StockId" type="hidden" id="StockId" value="<?php  echo $StockId?>">
    <input name="OperatorAction" type="hidden" id="OperatorAction" value="<?php  echo $OperatorAction?>">
    </td>
  </tr>
  <tr>
    <td width="92" height="30" align="right">配件名称：</td>
    <td><?php  echo $StuffCname?></td>
  </tr>
  <tr>
    <td height="30" align="right">需求数量：</td>
    <td><?php  echo $OrderQty?></td>
  </tr>

  <tr>
    <td height="30" align="right">领料数量：</td>
    <td><?php  echo $llQty?></td>
  </tr>
  
  <tr>
    <td height="30" align="right">未领数量：</td>
    <td><input name="unllQty" type="text" class="I0000L" id="unllQty" value="<?php  echo $unllQty?>" readonly>
        <?php  echo $unllQtyINFO?></td>
  </tr>

  <tr>
    <td height="30" align="right">本次领料：</td>
    <td><input name="oldQty" type="text" id="oldQty" value="<?php  echo $Qty?>" class="I0000L" readonly></td>
  </tr>
  
    <tr>
    <td height="30" align="right"><?php echo $titleSTR1?></td>
    <td><input name="upQty" type="text" id="upQty" value="<?php  echo $upQty?>" class="I0000L" readonly></td>
  </tr>
  <tr>
    <td height="30" align="right"><?php echo $titleSTR2?></td>
    <td><input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='10' value="<?php echo $upQty?>"  >
    </td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
	var Message="";
	var changeQty=document.form1.changeQty.value;							
	if(changeQty==0 || changeQty =="" ){
		Message="不是规范或不允许的值！";		
		}
	
	if(Message!=""){
		alert(Message);
		document.form1.changeQty.value=oldValue;
		return false;
		}
	else{		
		document.form1.action="ck_ll_updated.php";
		document.form1.submit();
		
	}
}	
</script>