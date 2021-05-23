<?php 

//代码 branchdata by zx 2012-08-13
/*
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工津贴扣款记录");//需处理
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
	<table width="750" height="120" border="0" cellspacing="5">
<tr>
	  <td width="150" height="18" align="right" scope="col">月份:</td>
	  <td  valign="middle" scope="col"><input name="Month" type="text" id="Month" style="width: 430px;" maxlength="7" dataType="Month" msg="月份格式不对"></td>
	  </tr>

	  <tr>
		<td height="18" align="right" valign="top" scope="col">员工:</td>
		<td valign="middle" scope="col">
		 	<select name="ListId[]" size="10" id="ListId" multiple style="width: 430px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,4)" dataType="PreTerm" Msg="没有指定员工" readonly>
		 	</select>
		</td>
	</tr>	
  <!--  
	  <td width="150" height="18" align="right" scope="col">迟到早退次数:</td>
	  <td  valign="middle" scope="col"><input name="cs" type="text" id="cs" style="width: 430px;" maxlength="7" dataType="Number" msg="次数格式不对"></td>
	  </tr>
  -->     
 	  <td width="150" height="18" align="right" scope="col">金额:</td>
	  <td  valign="middle" scope="col"><input name="Amount" type="text" id="Amount" style="width: 430px;" dataType="Double" Msg="未填写或格式不对"></td>
	  </tr>     
          
	<tr>
	  <td height="18" align="right" valign="top" scope="col">备注:</td>
	  <td valign="middle" scope="col"><textarea name="Remark" cols="51" style="width: 430px;" rows="4" id="Remark"></textarea>
		</td>
	  </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>