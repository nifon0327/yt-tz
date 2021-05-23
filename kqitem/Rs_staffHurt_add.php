<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工工伤报销费用");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,chooseMonth,$chooseMonth,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="543"  border="0" align="center" cellspacing="5">
         <td align="right">工伤日期</td>
         <td><input name="HurtDate" type="text" id="HurtDate" style="width:230px" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>
         </td>
         </tr>
	<tr>
	  <td align="right" valign="top">指定员工:</td>
	  <td valign="middle" >
	 	<select name="ListId[]" size="10" id="ListId" multiple style="width: 230px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,20)" dataType="PreTerm" Msg="没有指定员工" readonly>
		 	</select>
	    </td>
	</tr>
    

	  <tr>
		  <td height="20" align="right" >总金额:</td>
          <td><input name="AllAmout" type="text" id="AllAmout" size="40" dataType="Double" Msg="未填写或格式不对"></td>
	  </tr>
      
	  <tr>
		  <td height="20" align="right" >社保金额(公):</td>
          <td><input name="SSecurityAmout" type="text" id="SSecurityAmout" size="40" dataType="Double" Msg="未填写或格式不对"></td>
	  </tr> 
           
	  <tr>
		  <td height="20" align="right" >社保金额(私):</td>
          <td><input name="PSecurityAmout" type="text" id="PSecurityAmout" size="40" dataType="Double" Msg="未填写或格式不对"></td>
	  </tr>          
		<tr>
		  <td height="20" align="right" >实报金额:</td>
          <td><input name="Amount" type="text" id="Amount" size="40" dataType="Double" Msg="未填写或格式不对"></td>
	  </tr>
      
		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Remark" cols="30" rows="4" id="Remark" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
         <tr>
		  <td align="right" >工伤凭证:</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>   
        
         <tr>
		  <td align="right" >费用凭证:</td>
		  <td scope="col"><input name="HostpitalInvoice" type="file" id="HostpitalInvoice" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr> 

         <tr>
		  <td align="right" >社保凭证:</td>
		  <td scope="col"><input name="SSecurityInvoice" type="file" id="SSecurityInvoice" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>                 
        
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>