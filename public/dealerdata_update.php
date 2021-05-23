<?php 
//步骤1电信-yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 经销商或其它公司");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
$upResult = mysql_query("SELECT F.CompanyId,F.Forshort,F.Currency,
I.Company,I.Tel,I.Fax,I.Area,I.ZIP,I.Address,I.Bank,I.Remark
 FROM $DataPublic.dealerdata F 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=F.CompanyId
WHERE F.Id='$Id' and I.Type='$Type' LIMIT 1",$link_id); 
if($upRow = mysql_fetch_array($upResult)){
	$CompanyId=$upRow["CompanyId"];
	$Forshort=$upRow["Forshort"];
	$Currency=$upRow["Currency"];
	$Company=$upRow["Company"];
	$Tel=$upRow["Tel"];
	$Fax=$upRow["Fax"];
	$Area=$upRow["Area"];
	$ZIP=$upRow["ZIP"];
	$Address=$upRow["Address"];
	$Bank=$upRow["Bank"];
	$Remark=$upRow["Remark"];
	
	$lmanResult = mysql_query("SELECT * FROM $DataIn.linkmandata WHERE CompanyId=$CompanyId and Defaults=0 and Type='$Type' ORDER BY CompanyId DESC LIMIT 1",$link_id);
	$lmanRow = mysql_fetch_array($lmanResult);
	$LinkId=$lmanRow["Id"];
	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page,LinkId,$LinkId,CompanyId,$CompanyId,Type,$Type";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
     <table width="700" border="0" align="center" cellspacing="5">
  <tr>
    <td scope="col"><div align="right">结付货币</div></td>
    <td colspan="3" scope="col">
        <select name="Currency" id="Currency" style="width:490px">
		<?php 
		$Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		if($Currency_Row = mysql_fetch_array($Currency_Result)){
			do{
				$Name=$Currency_Row["Name"];
				$Id=$Currency_Row["Id"];
				if($Id==$Currency){
					echo"<option value='$Id' selected>$Name</option>";
					}
				else{	
					echo"<option value='$Id'>$Name</option>";
					}
				}while ($Currency_Row = mysql_fetch_array($Currency_Result));
			}
		?>
        </select>
  </td>
  </tr>
  <tr>
    <td scope="col" align="right">国家地区</td>
    <td colspan="3" scope="col">
<input name="Area" type="text" id="Area" value="<?php  echo $Area?>" size="91" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内">    </td>
  </tr>
  <tr>
    <td ><div align="right">公司名称</div></td>
    <td colspan="3"><input name="Company" type="text" id="Company" value="<?php  echo $Company?>" size="91" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
  </tr>
  <tr>
    <td><div align="right">公司简称</div></td>
    <td colspan="3"><input name="Forshort" type="text" id="Forshort" value="<?php  echo $Forshort?>" size="91" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
  </tr>
  <tr>
    <td><div align="right">公司电话</div></td>
    <td colspan="3"><input name="Tel" type="text" id="Tel" value="<?php  echo $Tel?>" size="91"></td>
  </tr>
  <tr>
    <td><div align="right">公司传真</div></td>
    <td colspan="3"><input name="Fax" type="text" id="Fax" value="<?php  echo $Fax?>" size="91" require="false"></td>
  </tr>
  <tr>
    <td><div align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</div></td>
    <td colspan="3"><input name="Website" type="text" id="Website" value="<?php  echo $Website?>" size="91" require="false" dataType="Url" msg="非法的Url"></td>
  </tr>
  <tr>
    <td><div align="right">邮政编码</div></td>
    <td colspan="3"><input name="ZIP" type="text" id="ZIP" value="<?php  echo $ZIP?>" size="91" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
  </tr>
  <tr>
    <td><div align="right">通信地址</div></td>
    <td colspan="3"><input name="Address" type="text" id="Address" value="<?php  echo $Address?>" size="91" ataType="Limit" max="50" msg="必须在50个字之内"></td>
  </tr>
  <tr>
    <td valign="top"><div align="right">银行帐户</div></td>
    <td colspan="3"><textarea name="Bank" cols="59" id="Bank"><?php  echo $Bank?></textarea></td>
  </tr>
  <tr>
    <td valign="top"><div align="right">备&nbsp;&nbsp;&nbsp;&nbsp;注</div></td>
    <td colspan="3"><textarea name="Remark" cols="59" id="Remark"><?php  echo $Remark?></textarea></td>
  </tr>
  <tr>
    <td colspan="4"><div align="center">默认联系人信息</div></td>
	</tr>
          <tr>
            <td><div align="right">联 系 人</div></td>
            <td><input name="Linkman" type="text" id="Linkman" size="34" value="<?php  echo $lmanRow["Name"]?>" require="false" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="71"><div align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</div></td>
            <td>
              <select name="Sex" id="Sex" style="width:203px">
              <?php 
			  if($lmanRow["Sex"]==1){
			  	echo"<option value='1' selected>男</option><option value='0'>女</option>";
				} 
			  else{
			  	echo"<option value='1'>男</option><option value='0' selected>女</option>";
				}
				?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</div></td>
            <td width="203"><input name="Headship" type="text" id="Headship" size="34" value="<?php  echo $lmanRow["Headship"]?>" maxlength="20"></td>
            <td><div align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</div></td>
            <td width="310"><input name="Nickname" type="text" id="Nickname" size="33" value="<?php  echo $lmanRow["Nickname"]?>" maxlength="20"></td>
          </tr>
          <tr>
            <td><div align="right">移动电话</div></td>
            <td><input name="Mobile" type="text" id="Mobile" size="34" value="<?php  echo $lmanRow["Mobile"]?>" require="false"></td>
            <td><div align="right">固定电话</div></td>
            <td><input name="Tel2" type="text" id="Tel2" size="33" value="<?php  echo $lmanRow["Tel"]?>" require="false"></td>
          </tr>
          <tr>
            <td><div align="right">MSN</div></td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" size="91" value="<?php  echo $lmanRow["MSN"]?>" require="false" dataType="Email" msg="MSN格式不正确"></td>
          </tr>
          <tr>
            <td><div align="right">SKYPE</div></td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" size="91" value="<?php  echo $lmanRow["SKYPE"]?>"></td>
          </tr>
          <tr>
            <td><div align="right">邮件地址</div></td>
            <td colspan="3"><input name="Email" type="text" id="Email" size="91" value="<?php  echo $lmanRow["Email"]?>" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td><div align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</div></td>
            <td colspan="3"><textarea name="Remark2" cols="59" id="Remark2"><?php  echo $lmanRow["Remark"]?></textarea></td>
		  </tr>
		</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>