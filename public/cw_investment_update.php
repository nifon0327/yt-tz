<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新长期股权投资");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.cw22_investmentsheet WHERE Id='$Id'",$link_id));
$Company=$upData["Company"];
$InvestName=$upData["InvestName"];
$Amount=$upData["Amount"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		    <tr>
            <td height="30" scope="col" align="right">投资公司</td>
            <td scope="col"><input name="Company" type="text" id="Company" value="<?php  echo $Company?>" size="50" dataType="Require" Msg="未填写"></td></tr> 
            <tr>
            <td height="30" scope="col" align="right">投资项目</td>
            <td scope="col"><input name="InvestName" type="text" id="InvestName"  value="<?php  echo $InvestName?>" size="50" dataType="Require" Msg="未填写"></td></tr>
			<tr>
            <td scope="col"align="right">金&nbsp;&nbsp;额</td>
            <td scope="col"><input name="Amount" type="text" id="Amount" value="<?php  echo $Amount?>" size="50"  dataType="Double" Msg="未填写或格式不对"></td></tr>
			
			<tr>
		  <td height="30" scope="col" align="right" >单&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="50" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="51" rows="3" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
