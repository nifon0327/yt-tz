<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增劳务公司资料");//需处理
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
            <td width="150" align="right" >公司名称</td>
            <td ><input name="Company" type="text" id="Company" style="width:380px;" dataType="LimitB" max="100" min="2" msg="必须在2-100个字节之内"></td>
          </tr>
          <tr>
            <td align="right">公司简称</td>
            <td ><input name="Forshort" type="text" id="Forshort" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
          </tr>       
          
          <tr>
            <td align="right">公司电话</td>
            <td ><input name="Tel" type="text" id="Tel" style="width:380px;"></td>
          </tr>
            <tr>
            <td align="right">联系人</td>
            <td ><input name="LinkMan" type="text" id="LinkMan" style="width:380px;"></td>
          </tr>
          <tr>
            <td align="right">地&nbsp;&nbsp;&nbsp;&nbsp;址</td>
            <td ><textarea  name="Address" type="text" require="false" id="Address" style="width:380px;" dataType="Limit" max="50" msg="必须在50个字之内"></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">银行帐户</td>
            <td ><textarea name="Bank" style="width:380px;" id="Bank"></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td ><textarea name="Remark" style="width:380px;" id="Remark"></textarea></td>
          </tr>
        </table>
     </td>
   </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>