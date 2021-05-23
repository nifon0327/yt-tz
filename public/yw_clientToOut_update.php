<?php 
//电信-EWEN
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 客户出货指定转发对象");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT CompanyId,ToOutName,Remark FROM $DataIn.yw7_clientToOut WHERE Id=$Id ORDER BY Id LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$ToOutName=$upData["ToOutName"];
$Remark=$upData["Remark"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="213" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="35" align="right" class='A0010'>客户:</td>
    <td class='A0001'><select name="CompanyId" id="CompanyId" style="width:438px" dataType="Require" Msg="未选择">
        <?php 
	//$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate='1' AND cSign='$Login_cSign' ORDER BY Id",$link_id);
	$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate='1' AND  ObjectSign IN (1,2)  ORDER BY Id",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$cTemp=$checkRow["CompanyId"];
			$nTemp=$checkRow["Forshort"];
			if($cTemp==$CompanyId){
				echo"<option value='$cTemp' selected>$nTemp</option>";
				}
			else{
				echo"<option value='$cTemp'>$nTemp</option>";
				}
			}while($checkRow=mysql_fetch_array($checkSql));
		}
	?>
    </select></td>
  </tr>
  <tr>
    <td height="35" align="right" class='A0010'>转发对象名称:</td>
    <td class='A0001'><input name="ToOutName" type="text" id="ToOutName" value="<?php  echo $ToOutName?>" size="68" dataType="Require" Msg="未填写"></td>
  </tr>
  <tr>
    <td height="35" align="right" class='A0010'>备&nbsp;&nbsp;&nbsp;&nbsp;注:</td>
    <td class='A0001'><textarea name="Remark" style="width:470px" id="Remark"><?php  echo $Remark?></textarea></td>
  </tr>

</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>