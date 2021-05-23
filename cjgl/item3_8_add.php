<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=500;
$funFrom="item3_8";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  enctype="multipart/form-data"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "if(Validator.Validate(this,3)){return true}else{return false;}">
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5" name="NoteTable" id="NoteTable">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
            <td width="64" height="30" align="right" scope="col">费用分类</td>
            <td scope="col">
			<select name="TypeId" id="TypeId" style="width:370px" dataType="Require" msg="未选择分类">
			<option value="" selected>请选择</option>
			<?php
			$result = mysql_query("SELECT * FROM $DataPublic.adminitype WHERE Estate=1 order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
				      if ($myrow["TypeId"]=='618'){
                                                                             echo"<option value='$myrow[TypeId]' selected>$myrow[Name]</option>";
                                                                         }
                                                                         else{
					echo"<option value='$myrow[TypeId]'>$myrow[Name]</option>";
                                                                         }
				 } while ($myrow = mysql_fetch_array($result));
				}
			?>
		 	</select></td></tr>
		<tr>
		  <td height="30" align="right" scope="col">登记日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" value="<?php    echo date("Y-m-d");?>" style="width:365px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style="width:365px" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  	<td height="30" align="right" scope="col">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:370px" dataType="Require"  msg="未选择货币">
			<option value="" selected>请选择</option>
		  	<?php
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
		  	</select></td>
	    </tr>
		<tr>
		  <td height="60" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Content" cols="49" rows="3" id="Content" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
		<tr>
		  <td height="30" align="right" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
		  <td scope="col"><input type="file" name="fileinput" id="fileinput" style="width:370px"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
</table>
 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input type='submit' id='submit' value='保存' /></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
</form>
