<?php   
//步骤1 电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 加工工序资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.process_type WHERE Id='$Id'",$link_id));
$gxTypeId=$upData["gxTypeId"];
$gxTypeName=$upData["gxTypeName"];
$SortId=$upData["SortId"];
$Remark=$upData["Remark"];
$Color=$upData["Color"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
$tableWidth=850;
$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
			<tr>
				<td align="right" scope="col">工序分类</td>
				<td width="480" scope="col"><input name="gxTypeName" type="text" value="<?php    echo $gxTypeName?>" id="gxTypeName" style="width:320px"  dataType="Require" Msg="未填写工序分类">
                                    <input name="gxTypeId" id="gxTypeId"  type="hidden" value="<?php    echo $gxTypeId?>" ></td>
			</tr> 
				<td align="right" scope="col">备注</td>
				<td width="480" scope="col"><textarea name="Remark" style="width:320px"  rows="6" id="Remark"  Msg="未填写内容"><?php    echo $Remark?></textarea></td>
			</tr> 
                 <tr>
				<td align="right" scope="col">排序</td>
				<td width="480" scope="col"><input name="SortId" type="text" id="SortId" style="width:320px" value="<?php    echo $SortId?>"></td>
			</tr> 
                 <tr>
				<td align="right" scope="col">颜色</td>
				<td width="480" scope="col"><input name="Color" type="text" id="Color" style="width:320px" value="<?php    echo $Color?>"></td>
			</tr> 
	   </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>