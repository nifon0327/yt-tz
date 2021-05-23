<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增特殊功能");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
	<!--<tr>
      <td height="35" class='A0010' align="right">使用标识：</td>
      <td class='A0001'><?php 
	  $SharingShow="Y";
      include "../model/subselect/cSign.php";
	  ?></td>
  </tr>-->
	<tr>
	  <td height="35" class='A0010' align="right">功能类别：</td>
	  <td class='A0001'><?php 
      include "../model/subselect/taskType.php";?></td>
  </tr>
	<tr>
      <td height="35" class='A0010' align="right">所 在 列：</td>
      <td class='A0001'><select name="InCol" id="InCol" style="width:380px">
        <option value="1" selected>第一列</option>
        <option value="2">第二列</option>
        <option value="3">第三列</option>
      </select></td>
	</tr>
    <tr>
    	<td width="150" height="35" class='A0010' align="right">功能名称：
      </td>
	    <td class='A0001'><input name="Title" type="text" id="Title" style="width:380px" title="可输入1-150个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="150" Min="1" Msg="没有填写或字符超出150字节"></td>
    </tr>
    <tr>
      <td height="35" class='A0010' align="right">描&nbsp;&nbsp;&nbsp;&nbsp;述：</td>
      <td  class='A0001'><input name="Description" type="text" id="Description" style="width:380px" DataType="Require" Msg="没有填写描述"></td>
    </tr>
	<tr>
      <td height="35" class='A0010' align="right">特别参数：</td>
      <td class='A0001'><input name="Extra" type="text" id="Extra" style="width:380px" DataType="LimitB" Max="100" Msg="超出100个字节的限制"></td>
    </tr>
	</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>