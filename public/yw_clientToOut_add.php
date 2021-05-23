<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增客户出货指定转发对象");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="213" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="35" align="right" class='A0010'>客户:</td>
    <td class='A0001'><select name="CompanyId" id="CompanyId" style="width:438px" dataType="Require" Msg="未选择">
    <?php 
	//$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate='1' AND cSign='$Login_cSign' ORDER BY Id",$link_id);
	$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate='1' AND  ObjectSign IN (1,2)  ORDER BY Id",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		echo"<option value=''>请选择</option>";
		do{
			echo"<option value='$checkRow[CompanyId]'>$checkRow[Forshort]</option>";
			}while($checkRow=mysql_fetch_array($checkSql));
		}
	?>
	</select></td>
  </tr>
    <tr>
		<td height="35" align="right" class='A0010'>转发对象名称:</td>
	  <td class='A0001'><input name="ToOutName" type="text" id="ToOutName" value="" size="68" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
		<td height="35" align="right" class='A0010'>备&nbsp;&nbsp;&nbsp;&nbsp;注:</td>
	  <td class='A0001'><textarea name="Remark" style="width:470px" id="Remark"></textarea></td>
    </tr>    

</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>