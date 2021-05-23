<?php 
//yang步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增经销商或其它公司资料");//需处理
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
            <td width="89" scope="col" align="right">往来货币</td>
            <td colspan="3" scope="col">
			<select name="Currency" id="Currency" style="width:490px" dataType="Require" msg="未选择">
			<option value="">请选择</option>
			<?php 
			$Currency_Result = mysql_query("SELECT Id,Name FROM   $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
			if($Currency_Row = mysql_fetch_array($Currency_Result)){
				do{
					$Id=$Currency_Row["Id"];
					$Name=$Currency_Row["Name"];
					echo"<option value='$Id'>$Name</option>";
					}while ($Currency_Row = mysql_fetch_array($Currency_Result));
				}
			?>
            </select> 
			</td></tr>
		 <tr>
		   	<td scope="col" align="right">国家地区</td>
		   	<td colspan="3" scope="col">
              <input name="Area" type="text" id="Area" size="91" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
	      </tr> 
          <tr>
            <td align="right" >公司名称</td>
            <td colspan="3"><input name="Company" type="text" id="Company" size="91" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司简称</td>
            <td colspan="3"><input name="Forshort" type="text" id="Forshort" size="91" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司电话</td>
            <td colspan="3"><input name="Tel" type="text" id="Tel" size="91"></td>
          </tr>
          <tr>
            <td align="right">公司传真</td>
            <td colspan="3"><input name="Fax" type="text" id="Fax" size="91" require="false"></td>
          </tr>
          <tr>
            <td align="right">网&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td colspan="3"><input name="Website" type="text" id="Website" size="91" require="false" dataType="Url" msg="非法的Url"></td>
          </tr>
          <tr>
            <td align="right">邮政编码</td>
            <td colspan="3"><input name="ZIP" type="text" id="ZIP" size="91" require="false" dataType="Custom" regexp="^[1-9]\d{5}$" msg="邮政编码不存在"></td>
          </tr>
          <tr>
            <td align="right">通信地址</td>
            <td colspan="3"><input name="Address" type="text" require="false" id="Address" size="91" ataType="Limit" max="50" msg="必须在50个字之内"></td>
          </tr>
          <tr>
            <td align="right" valign="top">银行帐户</td>
            <td colspan="3"><textarea name="Bank" cols="59" id="Bank"></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td colspan="3"><textarea name="Remark" cols="59" id="Remark"></textarea></td>
          </tr>
          <tr>
            <td colspan="4"><div align="center">默认联系人信息</div></td>
          </tr>
          <tr>
            <td align="right">联 系 人</td>
            <td><input name="Linkman" type="text" id="Linkman" size="34" require="false" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
            <td width="72"><div align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</div></td>
            <td>
              <select name="Sex" id="Sex" style="width:203px" dataType="Require" msg="未选择">
			  <option value="">请选择</option>
			  <option value="0">女</option>
              <option value="1">男</option>
              </select></td>
          </tr>
          <tr>
            <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
            <td width="201"><input name="Headship" type="text" id="Headship" size="34" maxlength="20"></td>
            <td><div align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</div></td>
            <td width="355"><input name="Nickname" type="text" id="Nickname" size="33" maxlength="20"></td>
          </tr>
          <tr>
            <td align="right">移动电话</td>
            <td><input name="Mobile" type="text" id="Mobile" size="34" require="false"></td>
            <td><div align="right">固定电话</div></td>
            <td><input name="Tel2" type="text" id="Tel2" size="33" require="false"></td>
          </tr>
          <tr>
            <td align="right">MSN</td>
            <td colspan="3"><input name="MSN" type="text" id="MSN" size="91" require="false" dataType="Email" msg="MSN格式不正确"></td>
          </tr>
          <tr>
            <td align="right">SKYPE</td>
            <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" size="91"></td>
          </tr>
          <tr>
            <td align="right">邮件地址</td>
            <td colspan="3"><input name="Email" type="text" id="Email" size="91" require="false" dataType="Email" msg="信箱格式不正确"></td>
          </tr>
          <tr>
            <td align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
            <td colspan="3"><textarea name="Remark2" cols="59" id="Remark2"></textarea></td>
          </tr>
        </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>