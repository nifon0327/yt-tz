<?php 
/*
$DataIn.usertable
$DataPublic.staffmain
$DataIn.zw1_assettypes
$DataIn.zw1_brandtypes
电信-joseph
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 添加记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
	<tr>
      <td width="150" height="30" align="right" class='A0010'>物品基本资料</td>
      <td class='A0001'>&nbsp;</td>
	</tr>
	<tr>
      <td height="25" align="right" class='A0010'>类&nbsp;&nbsp;&nbsp;&nbsp;型：
      </td>
      <td class='A0001'>
	  <select name="TypeId" id="TypeId" style="width:414px" dataType="Require"  msg="未选择分类">
	   <option value=''>请选择</option>
      <?php 
	  $checkType=mysql_query("SELECT Id,Name FROM $DataIn.zw1_assettypes WHERE Estate=1  AND Type=5 ",$link_id);
	  if($checkTypeRow=mysql_fetch_array($checkType)){
	  	do{
			$Id=$checkTypeRow["Id"];
			$Name=$checkTypeRow["Name"];
			echo"<option value='$Id'>$Name</option>";
			}while($checkTypeRow=mysql_fetch_array($checkType));
		}
	  ?>
	  </select></td>
    </tr>
    <tr>
    	<td height="25" class='A0010' align="right">品&nbsp;&nbsp;&nbsp;&nbsp;牌：

		</td>
	    <td class='A0001'>
		<select name="BrandId" id="BrandId" style="width:414px" dataType="Require"  msg="未选择品牌">
		 <option value=''>请选择</option>
      <?php 
	  $checkBrand=mysql_query("SELECT Id,Name FROM $DataIn.zw1_brandtypes  WHERE Estate=1 ORDER BY Name",$link_id);
	  if($checkBrandRow=mysql_fetch_array($checkBrand)){
	  	do{
			$Id=$checkBrandRow["Id"];
			$Name=$checkBrandRow["Name"];
			echo"<option value='$Id'>$Name</option>";
			}while($checkBrandRow=mysql_fetch_array($checkBrand));
		}
	  ?>
        </select></td>
    </tr>
    <tr>
      <td height="25" align="right" class='A0010'>型&nbsp;&nbsp;&nbsp;&nbsp;号：</td>
      <td class='A0001'><input name="Model" type="text" id="Model" size="76" dataType="Require"  msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" align="right" class='A0010'>机&nbsp;身&nbsp;ID：</td>
      <td class='A0001'><input name="Number" type="text" id="Number" size="76" dataType="Require"  msg="未填写"></td>
    </tr>
    <tr>
      <td height="25" align="right" class='A0010'>图&nbsp;&nbsp;&nbsp;&nbsp;片：</td>
    <td class='A0001'><input name="Photo" type="file" id="Photo" size="63" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
    </tr>
    <tr valign="bottom">
      <td height="30" align="right" class='A0010'>初始领用记录</td>
      <td height="30" class='A0001'>&nbsp;</td>
    </tr>
    <tr>
      <td height="25" align="right" class='A0010'>领 用 人：</td>
      <td class='A0001'>
	  <select name="User" id="User" style="width:414px" dataType="Require"  msg="未选择领用人">
	  <option value=''>请选择</option>
	  <?php 
	  	/*
		$P_Result = mysql_query("SELECT 
		U.Number,P.Name FROM $DataIn.usertable U,$DataPublic.staffmain P WHERE U.uType='0' and P.Number=U.Number",$link_id);
		*/
	  	$P_Result = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1  ORDER BY BranchId,JobId,Number",$link_id);
		
		if($P_Row = mysql_fetch_array($P_Result)){
			do{
				$Number=$P_Row["Number"];
				$Name=$P_Row["Name"];
				echo"<option value='$Number'>$Name</option>";
				}while($P_Row = mysql_fetch_array($P_Result));
			}
		?>
      </select></td>
    </tr>
    <tr>
      <td height="25" align="right" class='A0010'>领用日期：</td>
      <td class='A0001'><input name="useDate" type="text" id="useDate" value="<?php  echo date("Y-m-d");?>" size="76" maxlength="10" dataType="Date" format="ymd" msg="未选择或格式不对" onfocus="WdatePicker()" readonly></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>领用说明：</td>
      <td class='A0001'><textarea name="useRemark" cols="49" rows="4" id="useRemark" dataType="Require"  msg="未填写"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>