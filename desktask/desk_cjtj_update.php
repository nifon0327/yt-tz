<?php   
//已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增生产记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$scSTR=" AND T.Id='$Tid'";
//步骤3：
$upSql="SELECT S.OrderPO,S.POrderId,S.Qty,P.cName
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE 1 AND S.Id='$Id'";
$upResult = mysql_query($upSql." $PageSTR",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$OrderPO=$upRow["OrderPO"];
	$POrderId=$upRow["POrderId"];
	$Qty=$upRow["Qty"];
	$cName=$upRow["cName"];
	
	//已完成的工序数量
	$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS cfQty 
	FROM $DataIn.sc1_cjtj C 
	WHERE C.POrderId='$POrderId' AND C.Tid='$Tid'",$link_id));
	$OverPQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];
	//未完成订单数
	$UnPQty=$Qty-$OverPQty;
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,POrderId,$POrderId,TempValue,,Tid,$Tid";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="80%" height="143" border="0" cellpadding="5" cellspacing="0" align="center">
	  <tr valign="bottom">
        <td height="30" colspan="2" class="A0100" scope="col">基本资料</td>
        </tr>
      <tr>
        <td width="100" height="25" align="right" scope="col" class="A0111">产品名称</td>
        <td valign="middle" scope="col" class="A0101"><?php    echo $cName?></td>
      </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">订单ＰＯ</td>
        <td valign="middle" scope="col" class="A0101"><?php    echo $OrderPO?></td>
      </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">订单数量</td>
        <td valign="middle" scope="col"  class="A0101"><?php    echo $Qty?></td>
      </tr>
      <tr valign="bottom">
        <td height="30" colspan="2" class="A0100" scope="col">生产情况</td>
        </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">已完成产品数量</td>
        <td valign="middle" scope="col" class="A0101"><input name='OverPQty' type='text' id='OverPQty' size='20' value="<?php    echo $OverPQty?>" class="I0000L" readonly> 
        </td>
      </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">未完成产品数量</td>
        <td valign="middle" scope="col" class="A0101"><input name='UnPQty' type='text' id='UnPQty' size='20' value="<?php    echo $UnPQty?>" class="I0000L" readonly>
          </td>
      </tr>
      <tr valign="bottom">
        <td height="25" colspan="2"  class="A0100" scope="col">本次登记</td>
        </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">生产日期</td>
        <td valign="middle" scope="col" class="A0101"><input name='Date' type='text' id='Date' size='79' maxlength='7' value="<?php    echo date("Y-m-d")?>" onfocus="WdatePicker()"></td>
      </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">本次完成数量</td>
        <td valign="middle" scope="col" class="A0101"><input name='Qty' type='text' id='Qty' size='79' max="<?php    echo $UnPQtys?>" min="0" dataType="Number"  msg="格式不对" onblur="Outdepot(this)" onfocus="toTempValue(this.value)"></td>
      </tr>
      <tr>
        <td height="18" align="right" valign="top" scope="col" class="A0111">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
        <td valign="middle" scope="col" class="A0101"><textarea name="Remark" cols="51" rows="3" id="Remark"></textarea></td>
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
	if(thisValue!=""){
		var UnreceiveQty=document.form1.UnPQty.value;
		var CheckSTR=fucCheckNUM(thisValue,"");
		if(CheckSTR==0){
			alert("不是规范的数字！");
			thisE.value=oldValue;
			return false;
			}
		else{
			thisValue=Number(thisValue);
			UnreceiveQty=Number(UnreceiveQty);
			if((thisValue>UnreceiveQty) || thisValue==0){
				alert("超出范围！");
				thisE.value=oldValue;
				return false;
				}
			}
		}
	}
</script>