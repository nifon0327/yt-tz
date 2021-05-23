<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新出货附加图片");//需处理
$fromWebPage=$funFrom."_".$From;		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Mid,Remark,Picture FROM $DataIn.ch7_shippicture WHERE Id=$Id ORDER BY Id LIMIT 1",$link_id));
$Mid=$upData["Mid"];
$Remark=$upData["Remark"];
$Picture=$upData["Picture"];
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="0" id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor?>'>
			<td width="40" height="30" align="center" class="A1111">操作</td>
		  <td width="40" align="center" class="A1101">序号</td>
			<td class="A1101" align="center" width="200">文档说明</td>
			<td width="560" align="center" class="A1101"><input name="oldPicture" type="hidden" id="oldPicture" value="<?php  echo $Picture?>">
		    上传的文档</td>
		</tr>
		<tr>
            <td class="A0111" align="center" height="30">&nbsp;</td>
         	<td class="A0101" align="center">1</td>
          	<td class="A0101"><input name="Remark" type="text" id="Remark" size="28" value="<?php  echo $Remark?>"></td>
            <td class="A0101"><input name="Picture" type="file" id="Picture" size="45" DataType="Filter" Accept="jpg" Msg="格式不对,请重选" Row="1" Cel="3"></td>
    	</tr>
	</table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>