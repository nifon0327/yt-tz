<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新补仓记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT S.Qty,S.StuffId,D.StuffCname,K.tStockQty,P.Forshort 
FROM $DataIn.ck3_bcsheet S
LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid
LEFT JOIN $DataIn.stuffdata D ON S.StuffId=D.StuffId 
LEFT JOIN $DataIn.ck9_stocksheet K ON S.StuffId=K.StuffId 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
WHERE S.Id=$Id ORDER BY S.Id DESC",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$Forshort=$upData["Forshort"];
	$StuffId=$upData["StuffId"];
	$StuffCname=$upData["StuffCname"];
	
	//该供应商该配件退换总数
	$check_thSql=mysql_query("SELECT SUM(S.Qty) AS thQty FROM $DataIn.ck2_thsheet S LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid WHERE M.CompanyId='$CompanyId' AND S.StuffId='$StuffId' ORDER BY StuffId",$link_id);
	$thQty=mysql_result($check_thSql,0,"thQty");
	
	//该供应商该补仓总数
	$check_bcSql=mysql_query("SELECT SUM(S.Qty) AS bcQty FROM $DataIn.ck3_bcsheet S LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid WHERE M.CompanyId='$CompanyId' AND S.StuffId='$StuffId' ORDER BY StuffId",$link_id);
	$bcQty=mysql_result($check_bcSql,0,"bcQty");
	
	//未补仓总数
	$MantissaQty=$thQty-$bcQty;       
	//在库
	$tStockQty=$upData["tStockQty"];
	//本次补仓
	$Qty=$upData["Qty"];
	
	
	if($tStockQty<=0){		//没有在库的情况下，不可以做减少补仓数量的操作
		$tStockQtyINFO="<span class='redB'>(没有在库,不可做减少补仓数量的操作.)</span>";		
		}
	else{
		$OperatorsSTR=" <option value='-1'>减少</option>";
		}
	
	if($MantissaQty==0){	//已全部补仓，不可以做增加补仓动作
		$MantissaQtyINFO="<span class='redB'>(已全部补仓,不可增加补仓数量.)</span>";
		}
	else{
		$OperatorsSTR.="<option value='1'>增加</option>";
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
	<tr><td width="100" rowspan="11" class="A0010">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width="100" rowspan="11" class="A0001">&nbsp;</td></tr>
	<tr>
	  <td width="92" height="30" align="right">供应商：</td>
	  <td><?php  echo $Forshort?></td>
	</tr>
	<tr>
	  <td height="30" align="right">配 件 ID：</td><td><input name="StuffId" type="text" id="StuffId" value="<?php  echo $StuffId?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">配件名称：</td><td><?php  echo $StuffCname?></td>
  </tr>
	<tr>
	  <td height="30" align="right">未补数量：</td><td><input name="MantissaQty" type="text" id="MantissaQty" value="<?php  echo $MantissaQty?>" class="I0000L" readonly><?php  echo $MantissaQtyINFO?></td>
  </tr>
	<tr>
	  <td height="30" align="right">在库：</td><td><input name="tStockQty" type="text" id="tStockQty" value="<?php  echo $tStockQty?>" class="I0000L" readonly><?php  echo $tStockQtyINFO?></td>
  </tr>
	<tr>
	  <td height="30" align="right">本次补仓：</td><td><input name="oldQty" type="text" id="oldQty" value="<?php  echo $Qty?>" class="I0000L" readonly></td>
  </tr>
	<tr>
	  <td height="30" align="right">补仓数量：</td>
	  <td>
	  <?php 
	  if($OperatorsSTR==""){
	  	echo"<div class='redB'>条件不足,不能更新.</div>";
		}
	  else{
	  	echo"<select name='Operators' id='Operators'>$OperatorsSTR</select>&nbsp;<input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='8'>";
      	}
		?>
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
	var oldValue=document.form1.TempValue.value;						//上次输入的补仓数量
	var Operators=Number(document.getElementById("Operators").value);
	var changeQty=document.form1.changeQty.value;								//新补仓数量
	var MantissaQty=Number(document.form1.MantissaQty.value);					//未补数量
	var tStockQty=Number(document.form1.tStockQty.value);						//库存数量
	var oldQty=Number(document.form1.oldQty.value);								//原补仓数量
	var CheckSTR=fucCheckNUM(changeQty,"");
	if(CheckSTR==0 || changeQty==0){
		Message="不是规范或不允许的值！";		
		}
	else{
		changeQty=Number(changeQty);
		if(Operators>0){//增加数量:在库要大于
			if(changeQty>MantissaQty){
				Message="超出未补数量!";
				}
			}
		else{			//减少数量：
			if(changeQty>tStockQty || changeQty>=oldQty){
				Message="超出在库范围或减少的数量或与减少的数量超出本次补仓数量!";
				}	
			}
		}
	
	if(Message!=""){
		alert(Message);
		document.form1.changeQty.value=oldValue;
		return false;
		}
	else{		
		document.form1.action="ck_bc_updated.php";
		document.form1.submit();
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
</script>
