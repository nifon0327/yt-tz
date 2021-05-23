<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新支票信息");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT ChequeNum,Amount,Receiver,Remark FROM $DataIn.cheque WHERE Id='$Id'",$link_id));
$ChequeNum=$upData["ChequeNum"];
$Amount=$upData["Amount"];
$Receiver=$upData["Receiver"];
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
            <td height="30" scope="col" align="right">支票号</td>
            <td scope="col"><input name="ChequeNum" type="text" id="ChequeNum" value="<?php  echo $ChequeNum?>" size="70" dataType="Require" Msg="未填写"></td></tr> 
			<tr>
            <td scope="col"align="right">金&nbsp;&nbsp;额</td>
            <td scope="col"><input name="Amount" type="text" id="Amount" value="<?php  echo $Amount?>" size="70"  dataType="Double" Msg="未填写或格式不对"></td></tr>
			<tr>
            <td height="30" scope="col" align="right">供应商</td>
            <td scope="col"><input name="Receiver" type="text" id="Receiver"  value="<?php  echo $Receiver?>" size="70" dataType="Require" Msg="未填写"></td></tr>
			<tr>
		  <td height="30" scope="col" align="right" >单&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="58" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
          <tr>
            <td valign="top" align="right">备&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="45" rows="3" id="Remark"><?php  echo $Remark?></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
