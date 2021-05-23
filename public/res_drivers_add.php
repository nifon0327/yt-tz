<?php 
//电信-ZX  2012-08-01
//$DataPublic.res_driverstype 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增驱动程序");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="88" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="30" align="right" class='A0010'>驱动分类:</td>
    <td class='A0001'><select name="TypeId" id="TypeId" style="width:556px " dataType="Require" Msg="未选择">
        <option value="" selected>请选择</option>
        <?php 
		$CheckSql=mysql_query("SELECT Id,Name FROM $DataPublic.res_driverstype WHERE Estate=1 ORDER BY Id",$link_id);
		if($CheckRow=mysql_fetch_array($CheckSql)){
			do{
				$Id=$CheckRow["Id"];
				$Name=$CheckRow["Name"];
				echo"<option value='$Id'>$Name</option>";
				}while($CheckRow=mysql_fetch_array($CheckSql));
			}
		?>
        </select></td>
  </tr>
  <tr>
    <td height="30" align="right" class='A0010'>驱动名称:</td>
    <td class='A0001'><input name="Name" type="text" id="Name" value="" size="105" maxlength="75" dataType="Require" Msg="未填写"></td>
  </tr>
    <tr>
		<td class='A0010' align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注:</td>
	  <td class='A0001'><textarea name="Remark" cols="67" rows="6" id="Remark" datatype="Require" msg="未填写"></textarea></td>
    </tr>
    <tr>
      <td height="30" align="right" class='A0010'>相关附件: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="92" dataType="Filter" msg="非法的文件格式" accept="zip,7z,rar" Row="3" Cel="1"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>