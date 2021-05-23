<?php 
/*
$DataIn.zw1_assetrecord 物品资料库
$DataIn.zw1_assetuse 	 物品领用记录
$DataIn.zw1_assettypes  物品类型
$DataIn.zw1_brandtypes  物品牌子
$DataPublic.staffmain
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新物品记录");//需处理
$fromWebPage="zw_asset";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$Save="1";
$SaveInfo="";
$upSql=mysql_query("SELECT R.TypeId,R.Model,R.Photo,R.Number,R.BrandId,R.delSign FROM $DataIn.zw1_assetrecord R  WHERE R.Id=$Id LIMIT 1",$link_id);
if($upRow=mysql_fetch_array($upSql)){
	$TypeId=$upRow["TypeId"];
	$Model=$upRow["Model"];
	$Photo=$upRow["Photo"];
	if($Photo==1){
		$oldFile="Mobile".$Id.".jpg";
		}
	$Number=$upRow["Number"];
	$BrandId=$upRow["BrandId"];
	$delSign=$upRow["delSign"];
	$CheckUseSql=mysql_query("SELECT U.Id,U.Remark,U.Date,U.User FROM $DataIn.zw1_assetuse U WHERE U.AssetId='$Id' ORDER BY U.Date DESC,Id DESC LIMIT 1",$link_id);
	if($CheckRow=mysql_fetch_array($CheckUseSql)){
		$UseId=$CheckRow["Id"];
		$cRemark=$CheckRow["Remark"];
		$cDate=$CheckRow["Date"];
		$cUser=$CheckRow["User"];
		}
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,UseId,$UseId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
	<tr>
      <td width="150" height="30" align="right" class='A0010'>物品基本资料</td>
      <td class='A0001'>&nbsp;          </td>
  </tr>
	<tr>
      <td height="30" class='A0010' align="right">类&nbsp;&nbsp;&nbsp;&nbsp;型：
      </td>
      <td class='A0001'>
	  <select name="TypeId" id="TypeId" style="width:414px">
      <?php 
	  $checkType=mysql_query("SELECT Id,Name FROM $DataIn.zw1_assettypes WHERE Estate=1  AND Type=5",$link_id);
	  if($checkTypeRow=mysql_fetch_array($checkType)){
	  	do{
			$Id=$checkTypeRow["Id"];
			$Name=$checkTypeRow["Name"];
			if($TypeId==$Id){
				echo"<option value='$Id' selected>$Name</option>";
				}
			else{
				echo"<option value='$Id'>$Name</option>";
				}
			}while($checkTypeRow=mysql_fetch_array($checkType));
		}
	  ?>
	  </select>
	  	  </td>
    </tr>
    <tr>
    	<td height="30" class='A0010' align="right">品&nbsp;&nbsp;&nbsp;&nbsp;牌：

		</td>
	    <td class='A0001'>
		<select name="BrandId" id="BrandId" style="width:414px">
      <?php 
	  $checkBrand=mysql_query("SELECT Id,Name FROM $DataIn.zw1_brandtypes WHERE Estate=1",$link_id);
	  if($checkBrandRow=mysql_fetch_array($checkBrand)){
	  	do{
			$Id=$checkBrandRow["Id"];
			$Name=$checkBrandRow["Name"];
			if($BrandId==$Id){
				echo"<option value='$Id' selected>$Name</option>";
				}
			else{
				echo"<option value='$Id'>$Name</option>";
				}
			}while($checkBrandRow=mysql_fetch_array($checkBrand));
		}
	  ?>
        </select>
	  </td>
    </tr>
    <tr>
      <td height="30" align="right" class='A0010'>型&nbsp;&nbsp;&nbsp;&nbsp;号：</td>
      <td class='A0001'><input name="Model" type="text" id="Model" size="76" value="<?php  echo $Model?>" dataType="Require"  msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" class='A0010'>机&nbsp;身&nbsp;ID：</td>
      <td class='A0001'><input name="Number" type="text" id="Number" size="76" value="<?php  echo $Number?>" dataType="Require"  msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" class='A0010'>物品图片：</td>
      <td class='A0001'><input name="Photo" type="file" id="Photo" size="63" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
    </tr>
		  	<?php 
		   	if($Photo==1){
				echo"<tr><td class='A0010'>&nbsp;</td><td class='A0001'><input name='oldFile' type='checkbox' id='oldFile' value='$oldFile'><LABEL for='oldFile'>删除已传图片</LABEL></td></tr>";
				}
		  	?>
    <tr valign="bottom">
      <td height="30" align="right" class='A0010'>最后领用记录</td>
    <td height="30" class='A0001'>&nbsp;</td>
    </tr>
    <tr>
      <td height="30" class='A0010' align="right">领 用 人：</td>
      <td class='A0001'>
	  <select name="User" id="User" style="width:414px">
	  <?php 
	  	/*
		$P_Result = mysql_query("SELECT 
		P.Number,P.Name FROM $DataPublic.staffmain P WHERE P.Estate=1 AND P.JobId<15 ORDER BY BranchId,JobId,Number",$link_id);
		*/
		$P_Result = mysql_query("SELECT 
		P.Number,P.Name FROM $DataPublic.staffmain P WHERE P.Estate=1  ORDER BY BranchId,JobId,Number",$link_id);		
		if($P_Row = mysql_fetch_array($P_Result)){
			do{
				$Number=$P_Row["Number"];
				$Name=$P_Row["Name"];
				if($cUser==$Number){
					echo"<option value='$Number' selected>$Name</option>";
					}
				else{
					echo"<option value='$Number'>$Name</option>";
					}
				}while($P_Row = mysql_fetch_array($P_Result));
			}
		?>
      </select></td>
    </tr>
    <tr>
      <td height="30" class='A0010' align="right">领用日期：</td>
      <td class='A0001'><input name="useDate" type="text" id="useDate" value="<?php  echo $cDate?>" size="76" maxlength="10" dataType="Date" format="ymd" msg="未选择或格式不对" onfocus="WdatePicker()" readonly></td>
    </tr>
    <tr>
      <td height="30" valign="top" class='A0010' align="right">领用说明：</td>
      <td class='A0001'><textarea name="useRemark" cols="49" rows="4" id="useRemark" dataType="Require"  msg="未填写"><?php  echo $cRemark?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>