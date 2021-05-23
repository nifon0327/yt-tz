<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增Forward公司资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page,Type,$Type";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5">
		 <tr>
            <td width="89" align="right" scope="col">结付货币</td>
            <td colspan="3" scope="col">
			<?php 
			include "../model/subselect/Currency.php";
			?>
			</td></tr>
		 <tr>
		   	<td scope="col" align="right">国家地区</td>
		   	<td colspan="3" scope="col">
              <input name="Area" type="text" id="Area" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
	      </tr> 
          <tr>
            <td align="right" >公司名称</td>
            <td colspan="3"><input name="Company" type="text" id="Company" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司简称</td>
            <td colspan="3"><input name="Forshort" type="text" id="Forshort" style="width:380px;" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司电话</td>
            <td colspan="3"><input name="Tel" type="text" id="Tel" style="width:380px;"></td>
          </tr>
          <tr>
            <td align="right">公司传真</td>
            <td colspan="3"><input name="Fax" type="text" id="Fax" style="width:380px;" require="false"></td>
          </tr>
          <tr>
            <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td colspan="3"><input name="Website" type="text" id="Website" style="width:380px;" require="false" dataType="Url" msg="非法的Url"></td>
          </tr>
          <tr>
            <td align="right">邮政编码</td>
            <td colspan="3"><input name="ZIP" type="text" id="ZIP" style="width:380px;" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
          </tr>
          <tr>
            <td align="right">通信地址</td>
            <td colspan="3"><input name="Address" type="text" require="false" id="Address" style="width:380px;" ataType="Limit" max="50" msg="必须在50个字之内"></td>
          </tr>
          <tr>
            <td align="right" valign="top">银行帐户</td>
            <td colspan="3"><textarea name="Bank" $Type id="Bank" style="width:380px;"></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan="3"><textarea name="Remark" $Type id="Remark" style="width:380px;"></textarea></td>
          </tr>
          <tr>
            <td colspan="4" align="center">默认联系人信息</td>
          </tr>
          <tr>
            <td align="right">联 系 人</td>
            <td><input name="Linkman" type="text" id="Linkman" style="width:150px;" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="64" align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
            <td>
              <select name="Sex" id="Sex" style="width:150px;">
              <option value="1" selected>男</option>
              <option value="0">女</option>
            </select></td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td width="153"><input name="Headship" type="text" id="Headship" style="width:150px;" maxlength="20"></td>
            <td align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
            <td width="411"><input name="Nickname" type="text" id="Nickname" style="width:150px;" maxlength="20"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" style="width:150px;" require="false"></td>
            <td align="right">固定电话</td>
            <td><input name="Tel2" type="text" id="Tel2" style="width:150px;" require="false"></td>
          </tr>
          <tr>
            <td align="right">MSN</td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" style="width:380px;" require="false" dataType="Email" msg="MSN格式不正确"></td>
          </tr>
          <tr>
            <td align="right">SKYPE</td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" style="width:380px;"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td colspan="3"><input name="Email" type="text" id="Email" style="width:380px;" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
            <td colspan="3"><textarea name="Remark2" $Type id="Remark2" style="width:380px;"></textarea></td>
          </tr>
        </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>