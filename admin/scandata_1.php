<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="scandata_2.php"   target="mainFrame" >
    <table width="299" height="29" bgcolor="#CC99FF">
    <tr>
     <?php   
     echo "<input name='TClient' type='hidden' id='TClient' value='$Client' >";
     echo "<input name='TcName' type='hidden' id='TcName' value='$cName' >";
	 echo "<input name='TeCode' type='hidden' id='TeCode' value='$eCode' >";
	 echo "<input name='TBarCode' type='hidden' id='TBarCode' value='$BarCode' >";
	   echo "<input name='TTypeName' type='hidden' id='TTypeName' value='$TypeName' >";	 
    ?>    
    <td valign="top"><input name="BarCode"  id="BarCode" size="40" type="text" ></td>

	</tr>
    </table>
</form>

</body>
</html>
<script language="javascript" type="text/javascript">
document.form1.BarCode.focus();
function checkInfo()
{
	//onChange="checkInfo()"
	alert("123");
}
</script>