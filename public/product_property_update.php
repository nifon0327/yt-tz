<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新产品购买属性");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Name,pValue,Remark FROM $DataIn.product_property WHERE Id='$Id'",$link_id));
$pValue=$upData["pValue"];
$Remark=$upData["Remark"];
$Name  =$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
	    <tr>
            <td width="100"  align="right" scope="col">属性名称</td>
            <td scope="col"><input name="Name" type="text" id="Name" value="<?php echo $Name?>" style="width:380px;" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围"></td></tr>
            </tr>

		<tr>
		  	<td width="100" height="13" align="right" scope="col">参 数 值</td>
		  	<td scope="col"><input name="pValue" type="text" id="pValue" value="<?php echo $pValue?>" style="width: 380px;"  dataType="Double" Msg="未填写或格式不对">
		  	<input type="hidden" id="OldpValue" name="OldpValue" value="<?php echo $pValue?>">
		  	</td>
		</tr>
        <tr>
            <td align="right" valign="top">备 &nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="50" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
        </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>