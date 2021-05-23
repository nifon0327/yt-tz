<?php 
/*
已更新电信---yang 20120801
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新工序登记记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$upSql="SELECT S.sPOrderId,S.Qty,OD.StuffCname AS cName,D.Qty AS scQty,D.ProcessId,D.Date,D.Remark
FROM $DataIn.sc1_gxtj D
LEFT JOIN $DataIn.yw1_scsheet S ON S.sPOrderId=D.sPOrderId
LEFT JOIN cg1_stocksheet G ON G.StockId=S.mStockId 
LEFT JOIN $DataOut.stuffdata OD ON OD.StuffId=G.StuffId 
WHERE 1 AND D.Id='$Id'";
$upResult = mysql_query($upSql." $PageSTR",$link_id);
if($upRow = mysql_fetch_array($upResult)){

	$sPOrderId=$upRow["sPOrderId"];
	$cName=$upRow["cName"];
	$Qty=$upRow["Qty"];			//订单数量，即加工数量
	$scQty=$upRow["scQty"];		//本次加工数量
	$Date=$upRow["Date"];		//生产登记日期
	$Remark=$upRow["Remark"];	//生产备注
	$ProcessId=$upRow["ProcessId"];	//工序类型
	
	 //工序登记数
	   $BassLossSql=mysql_fetch_array(mysql_query("SELECT D.BassLoss 
	                   FROM $DataIn.cg1_processsheet  S 
	                    LEFT JOIN $DataIn.process_data  D ON D.ProcessId=S.ProcessId 
				       WHERE S.StockId='$StockId'  AND S.ProcessId='$ProcessId' ",$link_id));
		$BassLoss=$BassLossSql["BassLoss"]==""?0:$BassLossSql["BassLoss"];	
		$GxOrderQty=ceil($Qty+$Qty*$BassLoss);
		
	//已完成数量
	$CheckCfQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS cfQty FROM $DataIn.sc1_gxtj WHERE 1 AND ProcessId='$ProcessId' AND POrderId='$POrderId'",$link_id));
	$OverPQty=$CheckCfQty["cfQty"]==""?0:$CheckCfQty["cfQty"];

	//未完成数量
	$UnPQty=$GxOrderQty-$OverPQty;

	//本次完成订单数
	$CanChangeQty=$scQty+$UnPQty;
	$UnPQtys=$CanChangeQty+1;
	
	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,POrderId,$POrderId,TempValue,,CanChangeQty,$CanChangeQty,chooseDay,$chooseDay";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="80%" height="143" border="0" cellpadding="5" cellspacing="0" align="center">
	  <tr valign="bottom">
        <td height="30" colspan="2" class="A0100" scope="col">基本资料</td>
        </tr>
      <tr>
        <td width="100" height="25" align="right" scope="col" class="A0111">配件名称</td>
        <td valign="middle" scope="col" class="A0101"><?php  echo $cName?></td>
      </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">加工数量</td>
        <td valign="middle" scope="col"  class="A0101"><?php  echo $GxOrderQty?></td>
      </tr>
      <tr valign="bottom">
        <td height="30" colspan="2" class="A0100" scope="col">生产情况(<span class='redB'>注意:修改最后一道工序数量时，同时需要手动修改车间生产登记数量</span>)</td>
        </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">已完成数量</td>
        <td valign="middle" scope="col" class="A0101"><input name='OverPQty' type='text' id='OverPQty' size='20' value="<?php  echo $OverPQty?>" class="I0000L" readonly> 
        </td>
      </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">未完成数量</td>
        <td valign="middle" scope="col" class="A0101"><input name='UnPQty' type='text' id='UnPQty' size='20' value="<?php  echo $UnPQty?>" class="I0000L" readonly>
          </td>
      </tr>
      <tr valign="bottom">
        <td height="25" colspan="2"  class="A0100" scope="col">本次登记</td>
        </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">生产日期</td>
        <td valign="middle" scope="col" class="A0101"><input name='Date' type='text' id='Date' size='79' maxlength='7' value="<?php  echo $Date?>" onfocus="WdatePicker()"></td>
      </tr>
      <tr>
        <td height="25" align="right" scope="col" class="A0111">本次完成数量</td>
        <td valign="middle" scope="col" class="A0101"><input name='NowQty' type='text' id='NowQty' onfocus="toTempValue(this.value)" onblur="Outdepot(this)" value="<?php  echo $scQty?>" size='79' max="<?php  echo $UnPQtys?>" min="0" dataType="Range"  msg="格式不对"></td>
      </tr>
      <tr>
        <td height="18" align="right" valign="top" scope="col" class="A0111">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
        <td valign="middle" scope="col" class="A0101"><textarea name="Remark" cols="51" rows="3" id="Remark"><?php  echo $Remark?></textarea></td>
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