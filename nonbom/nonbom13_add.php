<?php 
//EWEN 2013-04-19 OK
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
//步骤2：
ChangeWtitle("$SubCompany 新增非BOM采购员");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
            <tr>
            	<td width="150" height="40" align="right">采购</td>
                <td><input name="Name" type="text" id="Name" title="必填项,2-30个字节的范围"  style="width: 380px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'Name','staffmain','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off"></td>
            </tr>
            
            <tr>
              <td height="40" align="right" valign="top">备注</td>
              <td><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" msg="未填写或格式不对"></textarea></td>
            </tr>
          </table>
  </td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>