<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 新增长期股权投资");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		    <tr>
            <td height="30" scope="col" align="right">投资公司</td>
            <td scope="col"><input name="Company" type="text" id="Company"  size="50" dataType="Require" Msg="未填写"></td></tr> 
            <tr>
            <td height="30" scope="col" align="right">投资项目</td>
            <td scope="col"><input name="InvestName" type="text" id="InvestName"  size="50" dataType="Require" Msg="未填写"></td></tr>
			<tr>
			<tr>
            <td scope="col"align="right">金额</td>
            <td scope="col"><input name="Amount" type="text" id="Amount"  size="50"  dataType="Double" Msg="未填写或格式不对"></td>
            </tr>
			
		  <td height="30" scope="col" align="right" >单&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="58" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="50" rows="3" id="Remark"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>