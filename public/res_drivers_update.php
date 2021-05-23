<?php 
//电信-ZX  2012-08-01
// $DataIn.bulletin 二合一已更新
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新公告");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Caption,Attached,cSign FROM $DataIn.bulletin WHERE Id=$Id order by Id",$link_id));
$Caption=$upData["Caption"];
$Attached=$upData["Attached"];
$cSign=$upData["cSign"];
$TempEstateSTR="cSignSTR".strval($cSign); 
$$TempEstateSTR="selected";	
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="88" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" align="right" class='A0010'>公告对象:</td>
    <td class='A0001'>      <select name="cSign" id="cSign" style="width:370px " dataType="Require" Msg="未选择">
        <option value="0" <?php  echo $cSignSTR0?>>共享公告</option>
        <option value="5" <?php  echo $cSignSTR5?>>5楼公告</option>
        <option value="7" <?php  echo $cSignSTR7?>>7楼公告</option>
          </select></td>
  </tr>
    <tr>
		<td class='A0010' align="right">公告标题:</td>
	  <td class='A0001'><input name="Caption" type="text" id="Caption" size="67" maxlength="60" value="<?php  echo $Caption?>" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td class='A0010' align="right">相关附件: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="55" dataType="Filter" msg="非法的文件格式" accept="pdf" Row="1" Cel="1"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>