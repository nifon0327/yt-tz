<?php 
//电信-zxq 2012-08-01
//步骤1 $DataIn.ch10_mailaddress 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 样品寄送地址更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataIn.ch10_mailaddress WHERE Id='$Id'",$link_id);
if ($upRow = mysql_fetch_array($upResult)){	
	$CompanyId=$upRow["CompanyId"];
	$LinkMan=$upRow["LinkMan"];
	$Forshort=$upRow["Forshort"];
	$Termini=$upRow["Termini"];
	$Tel=$upRow["Tel"];
	$Fax=$upRow["Fax"];
	$ZIP=$upRow["ZIP"];
	$Address=$upRow["Address"];
	$Remark=$upRow["Remark"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="5">
		<tr>
		  <td scope="col" width="150" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
		  <td  scope="col">
		  <select name="CompanyId" id="CompanyId" style="width: 460px;">
		  <?php 
			$Client= mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE ObjectSign IN (1,2) AND Estate=1 ORDER BY Id",$link_id);
			if($ClientRow = mysql_fetch_array($Client)){
				do{
					if($CompanyId==$ClientRow["CompanyId"]){
						echo"<option value='$ClientRow[CompanyId]' selected>$ClientRow[Forshort]</option>";
						}
					else{
						echo"<option value='$ClientRow[CompanyId]'>$ClientRow[Forshort]</option>";
						}
					} while ($ClientRow = mysql_fetch_array($Client));
				}  		    
			?>
			</select></td>
		</tr>
		<tr>
		  <td scope="col" align="right">收 件 人</td>
		  <td  scope="col"><input name="LinkMan" type="text" id="LinkMan" size="85" value="<?php  echo $LinkMan?>" dataType="Require"  msg="未填写"></td>
		</tr> 
        <tr>
            <td align="right">收件人公司</td>
            <td ><input name="Forshort" type="text" id="Forshort" size="85" value="<?php  echo $Forshort?>" dataType="Require"  msg="未填写"></td>
        </tr>
        <tr>
            <td align="right">寄件目的地</td>
            <td ><input name="Termini" type="text" id="Termini" size="85" value="<?php  echo $Termini?>" dataType="Require"  msg="未填写"></td>
        </tr>
        <tr>
            <td align="right">收件人电话</td>
            <td ><input name="Tel" type="text" id="Tel" size="85" value="<?php  echo $Tel?>" dataType="Require"  msg="未填写"></td>
        </tr>
        <tr>
            <td align="right">收件人传真</td>
            <td ><input name="Fax" type="text" id="Fax" size="85" value="<?php  echo $Fax?>" dataType="Require"  msg="未填写"></td>
        </tr>
        <tr>
            <td align="right">邮政编码</td>
            <td ><input name="ZIP" type="text" id="ZIP" size="85" value="<?php  echo $ZIP?>" dataType="Require"  msg="未填写"></td>
        </tr>
        <tr>
            <td align="right" valign="top">通信地址</td>
            <td ><textarea name="Address" cols="55" rows="4" id="Address" dataType="Require"  msg="未填写"><?php  echo $Address?></textarea></td>
        </tr>
        <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td ><textarea name="Remark" cols="55" rows="4" id="Remark" dataType="Require"  msg="未填写"><?php  echo $Remark?></textarea></td>
    	</tr>
	</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>