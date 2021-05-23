<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新采购单扣款记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$OperatorsSTR="";
$upSql=mysql_query("SELECT R.StockId,R.Qty,R.StuffId,R.StuffName,S.FactualQty,S.AddQty,R.PurchaseID,R.Remark,R.Price
FROM $DataIn.cw15_gyskksheet R 
LEFT JOIN $DataIn.cg1_stocksheet S ON R.StockId=S.StockId 
WHERE R.Id=$Id ORDER BY R.Id DESC",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$StuffId=$upData["StuffId"];
	$StockId=$upData["StockId"];
	$Qty=$upData["Qty"];
	$Price=$upData["Price"];
	$FactualQty=$upData["FactualQty"];
	$AddQty=$upData["AddQty"];
	$StuffCname=$upData["StuffName"];
	$RealQty=$FactualQty+$AddQty;
	$PurchaseID=$upData["PurchaseID"];
	$SheetRemark=$upData["SheetRemark"];
	}
//$SaveSTR=$OperatorsSTR==""?"NO":"";
$CheckFormURL="thisPage";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td width="100" rowspan="13" class="A0010">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td width="100" rowspan="13" class="A0001">&nbsp;<input type="hidden" id="StockId" name="StockId" value="<?php  echo $StockId?>"></td></tr>
	<tr>
	  <td width="92" height="30" align="right">流 水 号：</td>
	  <td><?php  echo $StockId?><input name="StockId" type="hidden" id="StockId" value="<?php  echo $StockId?>"></td>
	</tr>
	<tr>
	  <td height="30" align="right">采购单号：</td>
	  <td><?php  echo $PurchaseID?></td>
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
	  <td><?php  echo $RealQty?><input type="hidden" name="RealQty" id="RealQty" value="<?php  echo $RealQty?>"></td>
  </tr>

	<tr>
	  <td height="30" align="right">旧扣款数量：</td>
	  <td><input name="oldQty" type="text" id="oldQty" value="<?php  echo $Qty?>" class="I0000L" readonly></td>
    </tr>
	<tr>
	  <td height="30" align="right">新扣款数量：</td>
	  <td><input name="newQty" type="text" id="newQty" value="" size="8"></td>
    </tr>
	
	<tr>
	  <td height="30" align="right">旧单价：</td>
	  <td><input name="oldPrice" type="text" id="oldPrice" value="<?php  echo $Price?>" class="I0000L" readonly></td>
    </tr>
    
	<tr>
	  <td height="30" align="right">新单价：</td>
	  <td><input name="newPrice" type="text" id="newPrice" value="" size="8"></td>
    </tr>
    
	<tr>
	  <td height="30" align="right">扣款原因：</td>
	  <td><input name="SheetRemark" type="text" id="SheetRemark" style='width:320px;' value="<?php  echo $SheetRemark?>"></td>
    </tr>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function CheckForm(){
	var Message="";
	var RealQty=document.getElementById('RealQty').value;
	var oldValue=document.getElementById('oldQty').value;						
	var changeQty=document.getElementById('newQty').value;							
	var CheckSTR=fucCheckNUM(changeQty,"");
	
	var changePrice=document.getElementById('newPrice').value;
	var CheckSTR2=fucCheckNUM(changePrice,"Price");
	var StockId=document.getElementById('StockId').value;
    if(StockId!=0){
	    if(CheckSTR==0 || changeQty==0 || CheckSTR2==0){
		  Message="不是规范或不允许的值！";		
		 }
	   else{
		  changeQty=Number(changeQty);
		   if(changeQty>RealQty){
		    Message="超出范围";}
		  }
	  }
	if(Message!=""){
		alert(Message);
		document.getElementById('newQty').value=oldValue;
		return false;
		}
	else{		
		document.form1.action="cw_cgkk_updated.php";
		document.form1.submit();
		}
	}

</script>
