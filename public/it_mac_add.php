<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增上网设备MAC地址");//需处理
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
            	<td scope="col" width="150" height="30" align="right">姓名:</td>
            	<td scope="col">
              	<input name="Name" type="text" id="Name" style="width:380px" maxlength="16" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"> 
				</td>
			</tr>
			<tr>
		  		<td height="40" align="right" scope="col">IPad:</td>
		  		<td scope="col"><input  type="text" name="IPad" style="width:380px"  id="IPad"></td>
	    	</tr>
			<tr>
		  		<td height="40" align="right" scope="col">IPhone:</td>
		  		<td scope="col"><input  type="text"  name="IPhone" style="width:380px"  id="IPhone"></td>
	    	</tr>
		<tr>
		  		<td height="40" align="right" scope="col">Mac:</td>
		  		<td scope="col"><input  type="text"  name="Mac" style="width:380px"  id="Mac"></td>
	    	</tr>
		<tr>
		  		<td height="40" align="right" scope="col">PC:</td>
		  		<td scope="col"><input  type="text"  name="PC" style="width:380px"  id="PC"></td>
	    	</tr>
		<tr>
		  		<td height="40" align="right" scope="col">Other:</td>
		  		<td scope="col"><input  type="text"  name="Other" style="width:380px"  id="Other"></td>
	    	</tr>


      	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>