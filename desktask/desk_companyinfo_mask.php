<?php   
/*
已更新电信---yang 20120801
*/
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

?>
	<table width="450" border="0" cellspacing="0"><input name="TempValue" type="hidden" id="TempValue">
	    <tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td colspan="1" valign="top">&nbsp;</td>
		</tr>
		<tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td colspan="1" valign="top">请选择要导出的通讯录资料:</td>
		</tr>
		
        <tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td><input name="a[]" type="checkbox" id="a[]" value="1">员工</td>
  		</tr>
		<tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td><input name="a[]" type="checkbox" id="a[]" value="2">客户</td>
  		</tr>
		<tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td><input name="a[]" type="checkbox" id="a[]" value="3">供应商</td>
  		</tr>
		<tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td><input name="a[]" type="checkbox" id="a[]" value="4">货运公司</td>
  		</tr>
		<tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td><input name="a[]" type="checkbox" id="a[]" value="5">Forward公司</td>
  		</tr>
        <tr>
		 <td height="10" align="center">&nbsp;</td>
		  <td><input name="a[]" type="checkbox" id="a[]" value="6">经销商及其它</td>
  		</tr>
		<tr>
		  <td height="10" align="center">&nbsp;</td>
		  <td>&nbsp;</td>
	  </tr>
		<tr>
		  <td height="30" align="center" valign="top">&nbsp;</td>
  		  <td>
          <?php   
		  $SubMit="<span onClick='Validator.Validate(document.getElementById(document.form1.id),3,\"desk_companyinfo_save\",2)' $onClickCSS>确定</span>&nbsp;";
		  
		  ?>
		</td>
  		</tr>
		<tr valign="bottom"><td height="27" colspan="2" align="center"><?php    echo $SubMit?> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
</table>
