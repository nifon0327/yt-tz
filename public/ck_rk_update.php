<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rksheet
$DataIn.stuffdata
$DataIn.cg1_stocksheet
$DataIn.ck9_stocksheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新入库记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT R.StockId,R.Qty,R.StuffId,R.llQty AS outQty,
D.StuffCname,S.FactualQty,S.AddQty,K.tStockQty 
FROM $DataIn.ck1_rksheet R 
LEFT JOIN $DataIn.stuffdata D ON R.StuffId=D.StuffId 
LEFT JOIN $DataIn.cg1_stocksheet S ON R.StockId=S.StockId 
LEFT JOIN $DataIn.ck9_stocksheet K ON R.StuffId=K.StuffId 
WHERE R.Id=$Id ORDER BY R.Id DESC",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$StuffId=$upData["StuffId"];
	$StockId=$upData["StockId"];
	$Qty=$upData["Qty"];
	$FactualQty=$upData["FactualQty"];
	$AddQty=$upData["AddQty"];
	$StuffCname=$upData["StuffCname"];
	$tStockQty=$upData["tStockQty"];
	$RealQty=$FactualQty+$AddQty;
	$outQty=$upData["outQty"]; //出库数量
	//收货情况				
	$Receive_Temp=mysql_query("SELECT SUM(Qty) AS rkQty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId'",$link_id);; 
	$rkQty=mysql_result($Receive_Temp,0,"rkQty");
	$rkQty=$rkQty==""?0:$rkQty;
	$MantissaQty=$RealQty-$rkQty;   
	
	$lastQty = $Qty-$outQty;
	
	if($tStockQty==0){
		$tStockQtyINFO="<span class='redB'>(没有在库,不可做减少入库数量的操作.)</span>";		
		}
	else{
		$OperatorsSTR="<option value='-1'>减少</option>";
		if($lastQty >$tStockQty){
			$lastQty =  $tStockQty;
		}
		
		$lastSTR  = "<span class='redB'>最高能减少的数量:$lastQty</span>";  
		}
	if($MantissaQty==0){
		$MantissaQtyINFO="<span class='redB'>(已全部收货,不可增加入库数量.)</span>";
		}
	else{
		$OperatorsSTR.=" <option value='1'>增加</option>";
		}
		
		
	}
$SaveSTR=$OperatorsSTR==""?"NO":"";
$CheckFormURL="thisPage";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId,TempValue,";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td width="100" rowspan="12" class="A0010">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width="100"  class="A0001">&nbsp;</td></tr>
	<tr>
	  <td width="92" height="30" align="right">流 水 号：</td>
	  <td><?php  echo $StockId?><input name="StockId" type="hidden" id="StockId" value="<?php  echo $StockId?>"></td>
	</tr>
	<tr>
	  <td height="30" align="right">配 件 ID：</td>
  		<td><input name="StuffId" type="text" id="StuffId" value="<?php  echo $StuffId?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">配件名称：</td>
	  <td><?php  echo $StuffCname?></td>
  </tr>
	<tr>
	  <td height="30" align="right">需求数量：</td>
	  <td><?php  echo $FactualQty?></td>
  </tr>
	<tr>
	  <td height="30" align="right">增购数量：</td>
	  <td><?php  echo $AddQty?></td>
  </tr>
	<tr>
	  <td height="30" align="right">实购数量：</td>
	  <td><?php  echo $RealQty?></td>
  </tr>
	<tr>
	  <td height="30" align="right">未收数量：</td>
	  <td><input name="MantissaQty" type="text" id="MantissaQty" value="<?php  echo $MantissaQty?>" class="I0000L" readonly><?php  echo $MantissaQtyINFO?></td>
  </tr>
	<tr>
	  <td height="30" align="right">在库：</td>
	  <td><input name="tStockQty" type="text" id="tStockQty" value="<?php  echo $tStockQty?>" class="I0000L" readonly><?php  echo $tStockQtyINFO?></td>
  </tr>
  
  	<tr>
	  <td height="30" align="right">出库：</td>
	  <td class="redB"><?php  echo $outQty?></td>
  </tr>
  
	<tr>
	  <td height="30" align="right">本次入库：</td>
	  <td><input name="oldQty" type="text" id="oldQty" value="<?php  echo $Qty?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">入库数量：</td>
	  <td>
	  <?php 
	  if($OperatorsSTR==""){
	  	echo"<div class='redB'>条件不足,不能更新.</div>";
		}
	  else{
	  	echo"<select name='Operators' id='Operators'>$OperatorsSTR</select>&nbsp;<input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='8'>";
      	}
		echo $lastSTR; ?><input name="lastQty" type="hidden" id="lastQty" value="<?php  echo $lastQty?>">
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
	var oldValue=document.form1.TempValue.value;						//上次输入的入库数量
	var Operators=Number(document.getElementById("Operators").value);
	var changeQty=document.form1.changeQty.value;								//新入库数量
	var MantissaQty=Number(document.form1.MantissaQty.value);					//未收数量
	var tStockQty=Number(document.form1.tStockQty.value);						//库存数量
	var oldQty=Number(document.form1.oldQty.value);								//原入库数量
	
	var laseQty = Number(document.form1.lastQty.value);	
	
	var CheckSTR=fucCheckNUM(changeQty,"");
	if(CheckSTR==0 || changeQty==0){
		Message="不是规范或不允许的值！";		
		}
	else{
		changeQty=Number(changeQty);
		if(Operators>0){//增加数量:不可大于未收数量或0或非法数字
			if(changeQty>MantissaQty){
				Message="超出未收货数量的范围!";
				}			
			}
		else{			//减少数量：不可大于在库数量,或大于等于本次入库的数量
		     	    
			if(changeQty>=laseQty){		
				Message="只能减少的数量应小于:"+laseQty;
				}
				
			}
		}
	
	if(Message!=""){
		alert(Message);
		document.form1.changeQty.value=oldValue;
		return false;
		}
	else{		
		document.form1.action="ck_rk_updated.php";
		document.form1.submit();
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
</script>
