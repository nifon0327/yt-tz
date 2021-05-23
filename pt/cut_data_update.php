<?php 
/*
 * 鼠宝皮套专用 zhongxq-2012-08-17
 */
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 刀模资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.pt_cut_data WHERE Id='$Id'",$link_id));
$CutName=$upData["CutName"];
$CutSize=$upData["CutSize"]==""?"&nbsp;":$upData["CutSize"];
$Picture=$upData["Picture"];
$cutSign=$upData["cutSign"];
$cutSignSTR="cutSign" . $cutSign;
$$cutSignSTR="selected ";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="600" border="0" align="center" cellspacing="0">
	<tr>
				<td align="right" scope="col">刀模类型</td>
				<td width="460" scope="col">
				<select name="cutSign" id="cutSign" style="width:240px" dataType="Require" msg="未选择">
				<option value="1" <?php  echo $cutSign1?>>铁刀模</option>
				<option value="2" <?php  echo $cutSign2?>>复啤刀模</option>
				<option value="3" <?php  echo $cutSign3?>>单头atom</option>
                <option value="4" <?php  echo $cutSign4?>>激光刀模</option>
                <option value="5" <?php  echo $cutSign5?>>双头atom</option>
				</select>
				</td>
			</tr> 
	<tr>
				<td align="right" scope="col">刀模编号</td>
				<td width="460" scope="col"><input name="CutName" type="text" id="CutName" size="40" value="<?php  echo $CutName?>"  dataType="Require" msg="未填写"></td>
			</tr> 
			<tr>
				<td align="right" scope="col">刀模尺寸<br>(cm×cm)</td>
				<td width="460" scope="col"><input name="CutSize" type="text" id="CutSize" size="40" value="<?php  echo $CutSize?>"   dataType="Require" msg="未填写"></td>
			</tr> 
			<tr>
				<td align="right" scope="col">刀模图片</td>
				<td width="460" scope="col"><input name="Picture" type="file" id="Picture" style="width: 300px;" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="7" Cel="1"></td>
			</tr> 
		
 </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>