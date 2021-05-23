<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.staffmain
$DataIn.usertable

二合一已更新  new WdatePicker(this,'%Y-%M',false,'whyGreen')
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增每月报关金额");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,StuffType,$StuffType";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
	  
	  <tr>
        <td height="22" align="right">月份</td>
        <td width="612"><input name="Month" type="text" id="Month" size="89" value="<?php  echo date("Y-m")?>" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM'})"   readonly dataType="Require" msg="未填写"></td>
	    </tr>
	  <tr>
	  
        <tr>
          <td align="right">最高金额</td>
          <td><input name="Amount" type="text" id="Amount" size="89" dataType="Number" msg="错误的金额"></td>
        </tr>
          
        
        <tr>
            <td align="right" valign="top">说明</td>
            <td><textarea name="Remark" cols="57" rows="4" id="Remark" dataType="" msg="未填写"></textarea></td>
        </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>