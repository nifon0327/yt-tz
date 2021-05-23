<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=400;
$funFrom="item5_6";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php  echo $saveWebPage?>"  enctype="multipart/form-data" method="post"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "if(Validator.Validate(this,3)){return CheckForm();}else{return false;}">
 <table width="<?php  echo $tableWidth?>" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
            <td width="124" align="right">报废日期</td>
            <td><input name="bfDate" type="text" id="bfDate" size="28" value="<?php  echo date("Y-m-d")?>" onfocus="WdatePicker()"  class="Wdate" dataType="Date" format="ymd" msg="日期不正确" readonly>
			</td>
          </tr>          <tr>
            <td  align="right">配件名称</td>
            <td  align="left"><input name="StuffId" type="hidden" id="StuffId">
            <input name="StuffCname" type="text" id="StuffCname" size="28"  datatype="Require"  msg="未选择入库配件"  oninput="CnameChanged()" >
           <input name="stuffQuery" type="button" id="stuffQuery" value="查 询" onClick="viewStuffdata1111()">
           <input name="oStockQty" type="hidden" id="oStockQty">
                </td>
          </tr>
          <tr>
          		<td align="right" height="30">出库库位</td>
            	<td  align="left"><input name='LocationId' type='hidden' id='LocationId' >
	            	<input name="Identifier" type="text" id="Identifier" size="28" dataType="Require"  msg="未输入出库库位" onkeyup="showResult(this.value,'Identifier','ck_location','5')" onblur="LoseFocus()" autocomplete="off">
            	</td>
          	</tr>
           <tr>
            <td align="right">报废数量</td>
            <td><input name="Qty" type="text" id="Qty" size="28" dataType="double"  msg="报废数量不正确"/><font id="listmaxQty" style='position:relative;display:none;color:#06C;'></font></td>
          </tr>
          <tr>
            <td height="22" align="right">申 请 人</td>
            <td height="22">
			<select name="ProposerId" id="ProposerId" width="28" style="width:150px;" dataType="Require"  msg="未选择申请人">
			<?php
			//员工资料表
			$PD_Sql = "SELECT M.Number,M.Name FROM $DataIn.usertable U LEFT JOIN $DataPublic.staffmain M ON U.Number=M.Number WHERE M.Estate=1";
			$PD_Result = mysql_query($PD_Sql);
			echo "<option value=''>请选择</option>";
			while ( $PD_Myrow = mysql_fetch_array($PD_Result)){
				$Number=$PD_Myrow["Number"];
				$Name=$PD_Myrow["Name"];
				if($Number==$Login_P_Number){
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
            <td align="right">报废分类</td>
            <td>
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
		  <td align="right" valign="top" height="20">单 &nbsp;&nbsp;&nbsp;据</td>
		  <td valign="top"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" ></td>
	    </tr>
          <tr>
            <td align="right" valign="top">报废原因</td>
            <td><textarea name="Remark" cols="38" rows="6" id="Remark" dataType="Require"  msg="未输入报废原因"></textarea></td>
          </tr>
</table>
 <table width="<?php  echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input type='submit' id='submit' value='保存' /></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
</form>
