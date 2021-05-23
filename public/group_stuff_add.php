<?php 
//电信-EWEN
//代码共享，ＭＣ未使用-EWEN 2012-08-19
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 部门小组-加工类配件关系");//需处理
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
    <td align="right" class='A0010' width="200">所属小组:</td>
    <td class='A0001'><select name="GroupId" id="GroupId" style="width:380px" dataType="Require"  msg="未选择">
      <option value='' selected>全部</option>
      <?php 
				$result = mysql_query("SELECT GroupId,GroupName FROM $DataIn.staffgroup WHERE Estate='1' AND BranchId='8'",$link_id);
				while ($StuffType = mysql_fetch_array($result)){
					$GroupId=$StuffType["GroupId"];
					$GroupName=$StuffType["GroupName"];
					echo"<option value='$GroupId'>$GroupName</option>";
					}
				?>
    </select>
    </td>
  </tr>
  <tr>
      <td align="right" valign="top" class='A0010'>指定配件:</td>
      <td class='A0001' valign="top"><select name="ListId[]" size="18" id="ListId" multiple style="width: 380px;" ondblclick="SearchRecord('stuffdata','<?php  echo $funFrom?>',2,6)" datatype="autoList" readonly>
      </select><input type="button"  value="删除选定行"  onClick="delListRow()"> </td>
    </tr>
     <tr>
      <td height="34" align="right"  class='A0010'>操作提示:</td>
      <td class='A0001'>双击配件列表框可弹出选择配件对话框。</td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language="JavaScript">
function delListRow(){
   var cList = document.getElementById("ListId");
   for(var i=0; i<cList.length; i++){
      if(cList.options[i].selected){
       cList.options[i]=null;
	   i=i-1;
	  }
   }
}
</script>