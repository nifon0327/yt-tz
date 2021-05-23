<?php 
//电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增打印任务");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_printjob";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$CheckSql=mysql_query("SELECT P.CompanyId,C.Forshort,S.OrderPO,
S.ProductId,P.cName,P.eCode,S.POrderId,S.Qty
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 AND S.Id=$Id",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$CompanyId=$CheckRow["CompanyId"];
        $POrderId=$CheckRow["POrderId"];
	$Forshort=$CheckRow["Forshort"];
	$OrderPO=$CheckRow["OrderPO"];
	$ProductId=$CheckRow["ProductId"];
	$cName=$CheckRow["cName"];
	$eCode=$CheckRow["eCode"];
	}
        
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,POrderId,$POrderId,TempValue,,CanChangeQty,$CanChangeQty,ItemId,$ItemId,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class='A0011'>
   <table width="80%" height="86" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="center">
   <tr align="center" bgcolor="#CCCCCC">
    <td width="142" height="36" class="A1111">客户</td>
    <td width="116" class="A1101">PO</td>
    <td width="117" class="A1101">产品ID</td>
    <td width="390" class="A1101">产品名称</td>
    <td width="338" class="A1101">Product Code </td>
    <td width="195" class="A1101">订单流水号
      <input name="POrderId" type="hidden" id="POrderId" value="<?php  echo $POrderId?>"></td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?php  echo $Forshort?></td>
    <td class="A0101"><?php  echo $OrderPO?></td>
    <td class="A0101"><?php  echo $ProductId?></td>
    <td class="A0101"><?php  echo $cName?></td>
    <td class="A0101"><?php  echo $eCode?></td>
    <td class="A0101"><?php  echo $POrderId?></td>
  </tr>
</table>
<br>
</td></tr>
<tr><td class='A0011'>
<table width="80%" height="273" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr align="center" >
		<td height="44" colspan="3" class="A0000">打印任务</td>
  </tr>
	<tr align="center" >
		<td width="125" height="41"class="A1111">序号</td>
		<td width="168" class="A1101">分类</td>
		<td class="A1101">打印数量</td>
	</tr>
	<tr align="center" >
		<td width="125" height="41"  class="A0111" bgcolor="#CCCCCC">1、</td>
		<td width="168" bgcolor="#CCCCCC" class="A0101">背卡条码</td>
		<td class="A0101"><input name="Qty1" type="text" id="Qty1" size="68"></td>
	</tr>
  <tr align="center" >
    <td height="42" class="A0111" bgcolor="#CCCCCC">2、</td>
  	<td bgcolor="#CCCCCC" class="A0101">PE袋标签</td>
    <td class="A0101"><input name="Qty2" type="text" id="Qty2" size="68"></td>
  </tr>
    <tr align="center" >
    <td height="44" bgcolor="#CCCCCC" class="A0111">3、</td>
  	<td bgcolor="#CCCCCC" class="A0101">外箱标签</td>
    <td class="A0101"><input name="Qty3" type="text" id="Qty3" size="68"></td>
  </tr>
 <tr align="center">
    <td height="44" bgcolor="#CCCCCC" class="A0111">4、</td>
  	<td bgcolor="#CCCCCC" class="A0101">白盒/坑盒</td>
    <td class="A0101"><input name="Qty4" type="text" id="Qty4" size="68"></td>
  </tr>

    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}
function Outdepot(thisE){
	var oldValue=document.form1.TempValue.value;
	var thisValue=thisE.value;
	var UnreceiveQty=document.form1.CanChangeQty.value;
	var CheckSTR=fucCheckNUM(thisValue,"");
	if(CheckSTR==0){
		alert("不是规范的数字！");
		thisE.value=oldValue;
		return false;
		}
	else{
		if((thisValue*1>UnreceiveQty*1) || thisValue*1==0){
			alert("超出范围！");
			thisE.value=oldValue;
			return false;
			}
		else{
			document.form1.NowQty.value=thisValue;
			}
		}
	}
</script>