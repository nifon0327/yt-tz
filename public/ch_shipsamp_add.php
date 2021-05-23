<?php 
//电信-ZX  2012-08-01
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增随货样品");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="140" height="30" align="right" class="A0010">客&nbsp;&nbsp;&nbsp;&nbsp;户&nbsp;</td>
    <td class="A0001"><select name="CompanyId" Id="CompanyId" size="1" style="width: 298pt;" dataType="Require" msg="未选择客户"><option value="" selected>请选择</option>
        <?php 
		$cResult = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE ObjectSign IN (1,2) AND Estate=1 $CompanyIdSTR ORDER BY Id",$link_id);
		
		
		if($cRow = mysql_fetch_array($cResult)){
			do{
				echo"<option value='$cRow[CompanyId]'>$cRow[Forshort]</option>";
				} while ($cRow = mysql_fetch_array($cResult));
			}
		?>
    </select></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">PO&nbsp; </td>
    <td class="A0001"><input name="SampPO" type="text" id="SampPO" size="72" maxlength="50"></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">中文注释&nbsp; </td>
    <td class="A0001"><input name="SampName" type="text" id="SampName" size="72" dataType="LimitB" min="3" max="100"  msg="必须在2-100个字节之内"></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">英文注释&nbsp; </td>
    <td class="A0001"><input name="Description" type="text" id="Description" size="72" max="100"></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">样品数量&nbsp;</td>
    <td class="A0001"><input name="Qty" type="text" id="Qty" size="72" dataType="Number" msg="数量不正确" <?php  echo $EstateSTR?>></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">样品单价&nbsp;</td>
    <td class="A0001"><input name="Price" type="text" id="Price" size="72" dataType="Currency" msg="单价不正确"></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">样品单重&nbsp;</td>
    <td class="A0001"><input name="Weight" type="text" id="Weight" size="72" dataType="Currency" msg="单价不正确"></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">装箱设定&nbsp;</td>
    <td class="A0001"><select name="Type" Id="Type" size="1" style="width: 298pt;" dataType="Require" msg="未选择装箱设置">
      <option value="" selected>请选择</option>
      <option value="1">需要装箱设置</option>
      <option value="0">无需装箱设置</option>
        </select></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">类型&nbsp;</td>
    <td class="A0001"><select name="TypeId" Id="TypeId" size="1" style="width: 298pt;" dataType="Require" msg="未选择类型">
      <option value="" selected>请选择</option>
      <option value="0">样品</option>
      <option value="1">代购货款项目</option>
        </select></td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>