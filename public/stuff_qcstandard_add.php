<?php 
//更新$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增配件QC检验图");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="138" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td align="right" class='A0010'>所属类别:</td>
    <td class='A0001'><select name="TypeId" id="TypeId" style="width:260px" dataType="Require"  msg="未选择分类">
      <option value='' selected>分类列表</option>
      <?php 
				$result = mysql_query("SELECT TypeId,TypeName,Letter FROM $DataIn.stufftype WHERE Estate='1' order by Letter",$link_id);
				while ($StuffType = mysql_fetch_array($result)){
					$TypeId=$StuffType["TypeId"];
					$Letter=$StuffType["Letter"];
					$TypeName=$StuffType["TypeName"];
					echo"<option value='$TypeId'>$Letter-$TypeName</option>";
					}
				?>
    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input name="IsType" type="checkbox" id="IsType">设为【类】QC标准图
    </td>
  </tr>
  <tr>
    <td width="100" align="right" class='A0010'>标准说明:</td>
    <td class='A0001'>      <input name="Title" type="text" id="Title" value="" size="80" dataType="Require" Msg="未填写"></td>
  </tr>
    <tr>
      <td class='A0010' align="right">标准图存档: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="67" dataType="Filter" msg="文件格式不对" accept="jpg" Row="3" Cel="1"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>