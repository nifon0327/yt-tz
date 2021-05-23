<?php   
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增工序分类");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table  name='NoteTable' id='NoteTable' width="620" border="0" align="center" cellspacing="5">
                      			<tr>
				<td align="right" scope="col">分类名称</td>
				<td width="480" scope="col"><input name="gxTypeName" type="text" id="gxTypeName" style="width:320px"  dataType="Require" Msg="未填写工序分类名称"></td>
			</tr> 

                        <tr>
				<td align="right" scope="col">备注</td>
				<td width="480" scope="col"><textarea name="Remark" style="width:320px"  rows="6" id="Remark"  Msg="未填写内容"></textarea></td>
			</tr> 
                 <tr>
				<td align="right" scope="col">排序</td>
				<td width="480" scope="col"><input name="SortId" type="text" id="SortId" style="width:320px" value="0"></td>
			</tr> 
                 <tr>
				<td align="right" scope="col">颜色</td>
				<td width="480" scope="col"><input name="Color" type="text" id="Color" style="width:320px" value="0"></td>
			</tr> 
	   </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>