<?php
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$upResult = mysql_query("SELECT * FROM $DataIn.hzqksheet Where Id=$Id",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$Id=$upRow["Id"];
	$Content=$upRow["Content"];
	$Amount=$upRow["Amount"];
	$Currency=$upRow["Currency"];
	$Date=$upRow["Date"];
	$TypeId=$upRow["TypeId"];
                $Bill=$upRow["Bill"];
                $BillStr=$Bill==1?"已上传":"未上传";
	$Estate=$upRow["Estate"];
	}

$tableWidth=500;
$funFrom="item3_8";
$updateWebPage=$funFrom . "_ajax.php?ActionId=2&Id=$Id";
$delWebPage=$funFrom . "_ajax.php?ActionId=3&Id=$Id";
$qksubmitWebPage=$funFrom . "_ajax.php?ActionId=4&Id=$Id";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  enctype="multipart/form-data"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "if(Validator.Validate(this,3)){return true}else{return false;}">
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5" name="NoteTable" id="NoteTable">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
            <td width="64" height="30" align="right" scope="col">费用分类</td>
            <td scope="col">
			<select name="TypeId" id="TypeId" style="width:370px" dataType="Require" msg="未选择分类">
			<?php
			$result = mysql_query("SELECT * FROM $DataPublic.adminitype WHERE Estate=1 order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
                                                                        if ($TypeId==$myrow[TypeId]){
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
		  <td scope="col"><input name="theDate" type="text" id="theDate"  value="<?php    echo $Date?>" style="width:365px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" value="<?php    echo $Amount?>"  style="width:365px" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  	<td height="30" align="right" scope="col">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:370px" dataType="Require"  msg="未选择货币">
		  	<?php
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
                                                                        if ($Currency==$cRow[Id]){
                                                                            echo"<option value='$cRow[Id]' selected>$cRow[Name]</option>";
                                                                        }
                                                                        else{
                                                                            echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
                                                                        }
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
		  	</select></td>
	    </tr>
		<tr>
		  <td height="60" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Content" cols="49" rows="3" id="Content" dataType="Require" Msg="未填写说明"><?php    echo $Content?></textarea></td>
		</tr>
		<tr>
		  <td height="30" align="right" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
                  <td scope="col"><input type="file" name="fileinput" id="fileinput" style="width:370px"  DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"><span style="color:#FF0000;"><?php    echo $BillStr ?></span></td>
	    </tr>
</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input class='ButtonH_25' type='button'  id='updateBtn' value='更新' onclick="document.saveForm.action='<?php    echo $updateWebPage?>';if (Validator.Validate(document.saveForm,3)) document.saveForm.submit();"/></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='delBtn' value='删除' onclick="document.saveForm.action='<?php    echo $delWebPage?>';if(confirm('你确认要删除该记录吗？')) document.saveForm.submit();"/></td>
     <td align="center"><input class='ButtonH_25' type='button'  id='delBtn' value='请款' onclick="document.saveForm.action='<?php    echo $qksubmitWebPage?>';if(confirm('你确认要请款吗？')) document.saveForm.submit();"/></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
 </form>