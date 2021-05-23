<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增刀模资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="NewSign,$NewSign,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
		<tr>
				<td align="right" scope="col">刀模类型</td>
				<td width="460" scope="col">
				<select name="cutSign" id="cutSign" style="width:240px" dataType="Require" msg="未选择">
				<option value="">请选择</option>
				<option value='1'>铁刀模</option>
				<option value='2'>复啤刀模</option>
				<option value='3'>单头atom</option>
			    <option value='4'>激光刀模</option>
			    <option value='5' >双头atom</option>

				</select>
				</td>
			</tr> 
			<tr>
				<td align="right" scope="col">刀模编号</td>
				<td width="480" scope="col"><input name="CutName" type="text" id="CutName" size="40"  dataType="Require" msg="未填写"></td>
			</tr> 
			<tr>
				<td align="right" scope="col">刀模尺寸<br>(cm×cm)</td>
				<td width="480" scope="col"><input name="CutSize" type="text" id="CutSize" size="40"  ></td><!--dataType="Require" msg="未填写"-->
			</tr> 
	   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>