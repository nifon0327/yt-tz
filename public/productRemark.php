<?php 
include "../model/modelhead.php";	
$fromWebPage=$funFrom."_ts";		
$ProductId=$_GET['f'];
$Remark=$_GET['Remark'];
//步骤4：
$tableWidth=1000;$tableMenuS=1000;
?>
<table border="5" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			  <tr>
				<td height="100" width="150" align="right" scope="col">产品ID:</td>
				<td valign="middle" scope="col"><?php  echo $ProductId?></td>
			  </tr>
			   <tr>
			    <td height="100" align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注:</td>
			    <td >
			    <table border="2" width="300" <tr><td height="300" width="100" valign="top"><?php  echo $Remark?></td></tr></table>
			    </td>				
	    </tr>
	  </table>
</td></tr></table>

