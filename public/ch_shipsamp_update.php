<?php 
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.ch5_sampsheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新随货样品资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE Id=$Id ORDER BY Id LIMIT 1",$link_id));
$thisCompanyId=$upData["CompanyId"];
$TypeId=$upData["TypeId"];
$SampName=$upData["SampName"];
$Description=$upData["Description"];
$Qty=$upData["Qty"];
$Price=$upData["Price"];
$Weight=$upData["Weight"];
$Type=$upData["Type"];
$SampPO=$upData["SampPO"];
$TempEstateSTR="TypeSTR".strval($Type); 
$$TempEstateSTR="selected";	

$TempSTR="TypeIdSTR".strval($TypeId); 
$$TempSTR="selected";	

$EstateSTR=$upData["Estate"]==1?"":"class='textINPUT' readonly";
$CompanyIdSTR=$upData["Estate"]==1?"":" and CompanyId='$thisCompanyId'";
$OperationInfo=$upData["Estate"]==1?"":"<div class='redB'>注：样品待出或已出状态,不能更新客户和数量</div>";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td width="140" height="30" align="right" class="A0010">客&nbsp;&nbsp;&nbsp;&nbsp;户&nbsp;</td>
        <td class="A0001"><select name="CompanyId" Id="CompanyId" size="1" style="width: 298pt;">
 		<?php 
		$cResult = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1  AND Estate=1 AND ObjectSign IN(1,2) $CompanyIdSTR ORDER BY Id",$link_id);
		if($cRow = mysql_fetch_array($cResult)){
			do{
			 	if($thisCompanyId==$cRow["CompanyId"]){
					echo"<option value='$cRow[CompanyId]' selected>$cRow[Forshort]</option>";
					}
				else{
					echo"<option value='$cRow[CompanyId]'>$cRow[Forshort]</option>";
					}
				} while ($cRow = mysql_fetch_array($cResult));
			}
		?>
		</select></td>
	</tr>
        <tr>
         <td class="A0010" align="right" height="30">PO&nbsp; </td>
         <td class="A0001"><input name="SampPO" type="text" id="SampPO" size="72"  value="<?php  echo $SampPO?>"  maxlength="50"></td>
       </tr>
	<tr>
    	<td class="A0010" align="right" height="30">中文注释&nbsp; </td>
        <td class="A0001"><input name="SampName" type="text" id="SampName" size="72" value="<?php  echo $SampName?>" dataType="LimitB" min="3" max="100"  msg="必须在2-100个字节之内"></td>
	</tr>
	<tr>
		<td class="A0010" align="right" height="30">英文注释&nbsp; </td>
		<td class="A0001"><input name="Description" type="text" id="Description" size="72" max="100" value="<?php  echo $Description?>"></td>
	</tr>
	<tr>
    	<td class="A0010" align="right" height="30">样品数量&nbsp;</td>
        <td class="A0001"><input name="Qty" type="text" id="Qty" size="72" value="<?php  echo $Qty?>" dataType="Number" msg="数量不正确" <?php  echo $EstateSTR?>></td>
	</tr>
	<tr>
    	<td class="A0010" align="right" height="30">样品单价&nbsp;</td>
        <td class="A0001"><input name="Price" type="text" id="Price" size="72" value="<?php  echo $Price?>" dataType="Currency" msg="错误的价格"></td>
	</tr>
	<tr>
      <td class="A0010" align="right" height="30">样品单重&nbsp;</td>
      <td class="A0001"><input name="Weight" type="text" id="Weight" size="72" value="<?php  echo $Price?>" dataType="Currency" msg="错误的重量"></td>
  </tr>
	<tr>
      <td class="A0010" align="right" height="30">装箱设定&nbsp;</td>
      <td class="A0001"><select name="Type" Id="Type" size="1" style="width: 298pt;">
          <option value="1" <?php  echo $TypeSTR1?>>需要装箱设置</option>
          <option value="0" <?php  echo $TypeSTR0?>>无需装箱设置</option>
            </select></td>
  </tr>
  <tr>
      <td class="A0010" align="right" height="30">类型&nbsp;</td>
      <td class="A0001"><select name="TypeId" Id="TypeId" size="1" style="width: 298pt;">
          <option value="0" <?php  echo $TypeIdSTR0?>>样品</option>
          <option value="1" <?php  echo $TypeIdSTR1?>>代购货款项目</option>
            </select></td>
  </tr>
	<tr>
	  <td class="A0010" align="right" height="30">&nbsp;</td>
	  <td class="A0001"><?php  echo $OperationInfo?></td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>