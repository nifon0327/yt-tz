<?php 
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增加班通知");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="154" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="120" height="34" align="right" class='A0010'>发布公司: </td>
    <td class='A0001'>
	<?php 
      //选择公司名称
        $cSignTB="O";
        $SharingShow="Y";
        include "../model/subselect/cSign.php";
     ?>
	</td>
  </tr>
  <tr>
    <td width="120" height="34" align="right" class='A0010'>加班日期: </td>
    <td class='A0001'><input name="Date" type="text" id="Date" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d")?>" size="85" maxlength="10" readonly  dataType="Date" format="ymd" msg="格式不对或未选择"></td>
  </tr>
    <tr>
		<td align="right" valign="top" class='A0010'>通知内容:</td>
	  <td class='A0001'><textarea name="Content" cols="67" rows="8" id="Content" datatype="Require" msg="未填写"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>