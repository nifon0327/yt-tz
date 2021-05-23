<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany Forward公司资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
$upResult = mysql_query("SELECT F.CompanyId,F.Forshort,F.Currency,
I.Company,I.Tel,I.Fax,I.Area,I.ZIP,I.Website,I.Address,I.Bank,I.Remark
 FROM $DataPublic.freightdata F 
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
	$Website=$upRow["Website"];
	$ZIP=$upRow["ZIP"];
	$Address=$upRow["Address"];
	$Bank=$upRow["Bank"];
	$Remark=$upRow["Remark"];
	
	$lmanResult = mysql_query("SELECT * FROM $DataIn.linkmandata WHERE CompanyId=$CompanyId and Defaults=0 and Type='$Type' ORDER BY CompanyId DESC LIMIT 1",$link_id);
	$lmanRow = mysql_fetch_array($lmanResult);
	$LinkId=$lmanRow["Id"];
	$TempSex="SexSTR".strval($lmanRow["Sex"]);$$TempSex="selected";
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
    <td width="83" align="right" scope="col">结付货币</td>
    <td colspan="3" scope="col">
    <?php 
	include "../model/subselect/Currency.php";
	?>
  </td>
  </tr>
  <tr>
    <td scope="col" align="right">国家地区</td>
    <td colspan="3" scope="col">
<input name="Area" type="text" id="Area" value="<?php  echo $Area?>" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内">    </td>
  </tr>
  <tr>
    <td align="right" >公司名称</td>
    <td colspan="3"><input name="Company" type="text" id="Company" value="<?php  echo $Company?>" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
  </tr>
  <tr>
    <td align="right">公司简称</td>
    <td colspan="3"><input name="Forshort" type="text" id="Forshort" value="<?php  echo $Forshort?>" style="width:380px;" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
  </tr>
  <tr>
    <td align="right">公司电话</td>
    <td colspan="3"><input name="Tel" type="text" id="Tel" value="<?php  echo $Tel?>" style="width:380px;"></td>
  </tr>
  <tr>
    <td align="right">公司传真</td>
    <td colspan="3"><input name="Fax" type="text" id="Fax" value="<?php  echo $Fax?>" style="width:380px;" require="false"></td>
  </tr>
  <tr>
    <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
    <td colspan="3"><input name="Website" type="text" id="Website" value="<?php  echo $Website?>" style="width:380px;" require="false" dataType="Url" msg="非法的Url"></td>
  </tr>
  <tr>
    <td align="right">邮政编码</td>
    <td colspan="3"><input name="ZIP" type="text" id="ZIP" value="<?php  echo $ZIP?>" style="width:380px;" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
  </tr>
  <tr>
    <td align="right">通信地址</td>
    <td colspan="3"><input name="Address" type="text" id="Address" value="<?php  echo $Address?>" style="width:380px;" ataType="Limit" max="50" msg="必须在50个字之内"></td>
  </tr>
  <tr>
    <td align="right" valign="top">银行帐户</td>
    <td colspan="3"><textarea name="Bank" style="width:380px;" id="Bank"><?php  echo $Bank?></textarea></td>
  </tr>
  <tr>
    <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
    <td colspan="3"><textarea name="Remark" style="width:380px;" id="Remark"><?php  echo $Remark?></textarea></td>
  </tr>
  <tr>
    <td colspan="4"><div align="center">默认联系人信息</div></td>
	</tr>
          <tr>
            <td align="right">联 系 人</td>
            <td><input name="Linkman" type="text" id="Linkman" style="width:150px;" value="<?php  echo $lmanRow["Name"]?>" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="60" align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
            <td>
              <select name="Sex" id="Sex" style="width:150px;">
                <option value="0" <?php  echo $SexSTR0?>>女</option>
                <option value="1" <?php  echo $SexSTR1?>>男</option>
              </select></td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td width="156"><input name="Headship" type="text" id="Headship" style="width:150px;" value="<?php  echo $lmanRow["Headship"]?>" maxlength="20"></td>
            <td align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
            <td width="368"><input name="Nickname" type="text" id="Nickname" style="width:150px;" value="<?php  echo $lmanRow["Nickname"]?>" maxlength="20"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:150px;" value="<?php  echo $lmanRow["Mobile"]?>" require="false"></td>
            <td align="right">固定电话</td>
            <td><input name="Tel2" type="text" id="Tel2" style="width:150px;" value="<?php  echo $lmanRow["Tel"]?>" require="false"></td>
          </tr>
          <tr>
            <td align="right">MSN</td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" style="width:380px;" value="<?php  echo $lmanRow["MSN"]?>" require="false" dataType="Email" msg="MSN格式不正确"></td>
          </tr>
          <tr>
            <td align="right">SKYPE</td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" style="width:380px;" value="<?php  echo $lmanRow["SKYPE"]?>"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td colspan="3"><input name="Email" type="text" id="Email" style="width:380px;" value="<?php  echo $lmanRow["Email"]?>" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
            <td colspan="3"><textarea name="Remark2" style="width:380px;" id="Remark2"><?php  echo $lmanRow["Remark"]?></textarea></td>
		  </tr>
		</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>