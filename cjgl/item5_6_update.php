<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$OperatorsSTR="";
$tableWidth=400;
$funFrom="item5_6";
$updateWebPage=$funFrom . "_ajax.php?ActionId=2&Id=$Id";
$delWebPage=$funFrom . "_ajax.php?ActionId=3&Id=$Id";

$upSql=mysql_query("SELECT F.ProposerId,F.StuffId,F.Qty,F.Remark,F.Type,F.Date,D.StuffCname,K.tStockQty,K.oStockQty ,F.LocationId,L.Identifier AS LocationName
FROM $DataIn.ck8_bfsheet F 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=F.StuffId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=F.StuffId
LEFT JOIN $DataIn.ck_location L ON L.Id = F.LocationId
WHERE 1 AND F.Id=$Id LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upSql)){
	$ProposerId=$upData["ProposerId"];
	$StuffId=$upData["StuffId"];
	$StuffCname=$upData["StuffCname"];
	$Qty=$upData["Qty"];
	$Remark=$upData["Remark"];
	$Type=$upData["Type"];
	$Date=$upData["Date"];
	$tStockQty=$upData["tStockQty"];
	$oStockQty=$upData["oStockQty"];
	$LocationName=$upData["LocationName"];
    $LocationId=$upData["LocationId"];
	$TypeSTR="TypeSTR".strval($Type);
	$$TypeSTR="selected";
	if($tStockQty==0 || $oStockQty==0){
		$unllQtyINFO="<span class='redB'>(不可做增加报废数量的操作.)</span>";
		}
	else{
		$OperatorsSTR="<option value='1'>增加</option>";
		}
	$OperatorsSTR.=" <option value='-1'>减少</option>";
}

?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="" method="post"  target="FormSubmit" name="saveForm" id="saveForm" >
 <table width="<?php  echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
	 <tr>
            <td width="100" align="right">配件名称</td>
            <td ><?php  echo $StuffCname?>
                <input name="StuffId" type="hidden" id="StuffId" value="<?php  echo $StuffId?>"></td>
          </tr>
          <tr>
            <td align="right">报废数量</td>
            <td><input name="oldQty" type="text" id="oldQty" size="20" value="<?php  echo $Qty?>" style="border:0;background:none;" readonly>
                <input name="TempValue" type="hidden" id="TempValue">
            </td>
          </tr>
          <tr>
            <td align="right">在库</td>
            <td><input name="tStockQty" type="text" id="tStockQty" size="20" value="<?php  echo $tStockQty?>" style="border:0;background:none;" readonly></td>
          </tr>
          <tr>
            <td align="right">可用库存</td>
            <td><input name="oStockQty" type="text" id="oStockQty" size="20" value="<?php  echo $oStockQty?>" style="border:0;background:none;" readonly></td>
          </tr>
          <tr>
          		<td align="right" height="30">入库库位</td>
            	<td  align="left"><input name='LocationId' type='hidden' id='LocationId' value="<?php echo $LocationId?>" >
	            	<input name="Identifier" type="text" id="Identifier" size="22"  value="<?php echo $LocationName?>" dataType="Require"  msg="未输入入库库位" onkeyup="showResult(this.value,'Identifier','ck_location','5')" onblur="LoseFocus()" autocomplete="off">
            	</td>
          	</tr>
          <tr>
            <td align="right">报废日期</td>
            <td><input name="bfDate" class="Wdate" type="text" id="bfDate" size="24" value="<?php  echo $Date?>" onfocus="WdatePicker()"  dataType="Date" format="ymd" msg="日期不正确"  readonly>
            </td>
          </tr>
          <tr>
            <td height="22" align="right">申 请 人</td>
            <td height="22">
			<select name="ProposerId" id="ProposerId" width="28" style="width: 150px;">
			<?php
			//员工资料表
			$PD_Sql = "SELECT M.Number,M.Name FROM $DataPublic.staffmain M LEFT JOIN $DataIn.usertable U ON U.Number=M.Number WHERE M.Estate=1 and (M.JobId=3 OR M.JobId=11)";
			$PD_Result = mysql_query($PD_Sql);
			while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
				$Number=$PD_Myrow["Number"];
				$Name=$PD_Myrow["Name"];
				if($ProposerId==$Number){
					echo "<option value='$Number' selected>$Name</option>";
					}
				else{
					echo "<option value='$Number'>$Name</option>";
					}
				}
			?>
            </select></td>
          </tr>
          <tr>
            <td height="30" align="right">数量更新</td>
            <td><?php
			  if($OperatorsSTR==""){
				echo"<div class='redB'>条件不足,不能更新.</div>";
				}
			  else{
				echo"<select name='Operators' id='Operators'  style='width: 80px;'>$OperatorsSTR</select>&nbsp;<input name='Qty' type='text' id='Qty' size='7'  dataType='Number'  msg='报废数量不正确'>";
				}
				?>
            </td>
          </tr>
		<tr>
            <td align="right">报废分类</td>
            <td>
			<!--
            <select name="Type" id="Type" dataType="Require"  msg="未选择分类" style="width: 150px;">
              <option value="0" <=$TypeSTR0?>>配件报废</option>
              <option value="1" <=$TypeSTR1?>>配件调用</option>
              <option value="2" <=$TypeSTR2?>>无单出货(收费)</option>
              <option value="3" <=$TypeSTR3?>>无单出货(不收费)</option>
			</select>
            -->
 			<select name="Type" id="Type" style="width: 150px;" dataType="Require"  msg="未选择分类">
			<?php

			$Ck8_Sql = "SELECT Id,TypeName FROM  $DataPublic.ck8_bftype WHERE 1 AND Estate=1 AND mainType=1 ";
			$Ck8_Result = mysql_query($Ck8_Sql);
			echo "<option value=''>请选择</option>";
			while ( $PD_Myrow = mysql_fetch_array($Ck8_Result)){
				$TypeId=$PD_Myrow["Id"];
				$TypeName=$PD_Myrow["TypeName"];
				if($TypeId==$Type){
					echo "<option value='$TypeId' selected>$TypeName</option>";
					}
				else{
					echo "<option value='$TypeId'>$TypeName</option>";
					}
				}
			?>
            </select>
			</td>
          </tr>
          <tr>
            <td align="right" valign="top">报废原因</td>
            <td><textarea name="Remark" cols="40" rows="6" id="Remark" dataType="Require"  msg="未输入报废原因"><?php  echo $Remark?></textarea></td>
          </tr>
</table>

<table width="<?php  echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><span class='ButtonH_25' id='updateBtn' onclick="document.saveForm.action='<?php  echo $updateWebPage?>';if (Validator.Validate(document.saveForm,3)){if (CheckUpdata()) document.saveForm.submit();}">更新</span></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='delBtn' value='删除' onclick="document.saveForm.action='<?php  echo $delWebPage?>';if(confirm('你确认要删除该记录吗？')) document.saveForm.submit();"/></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
 </form>
