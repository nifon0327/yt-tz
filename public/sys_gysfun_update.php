<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新功能模块");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

$upRow = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.sys4_gysfunmodule WHERE Id='$Id'",$link_id));
$Estate=$upRow["Estate"];
$TempEstate="EstateSTR".strval($Estate);
$$TempEstate="selected";
$AutoName=$upRow["AutoName"];
$TempAutoName="AutoNameSTR".strval($AutoName);
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,ItemId,$ItemId,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5"> 
		<tr>
            <td width="100" align="right" scope="col">功能名称</td>
            <td scope="col"><input name="ModuleName" type="text" id="ModuleName" style="width:380px;" maxlength="30" value="<?php  echo $upRow["ModuleName"]?>" title="可输入1-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="1" Msg="没有填写或字符超出20字节">
		  </td>
		</tr>
		<tr>
			<td scope="col" align="right">连接参数</td>
		    <td scope="col"><input name="Parameter" type="text" id="Parameter" style="width:380px;" maxlength="100" value="<?php  echo $upRow["Parameter"]?>" title="可输入不超过100个字节的参数(每1英文字母占1个字节)"  Max="100" Msg="字符超出100个字节">
	        </td>
		  </tr>
		<tr>
				  <td scope="col" align="right">系统公司名</td>
		          <td scope="col"><select name="AutoName" id="AutoName" style="width:380px;">
		            <option value="0" <?php  echo $AutoNameSTR0?>>无</option>
		            <option value="1" <?php  echo $AutoNameSTR1?>>前置公司名称</option>
					<option value="2" <?php  echo $AutoNameSTR2?>>后置公司名称</option>
	              </select>
	              在功能名称前或后加入系统公司的名称</td>
		  </tr>
		<tr>
		  <td scope="col" align="right">可用状态</td>
		  <td scope="col"><select name="Estate" id="Estate" style="width:380px;">
            <option value="1" <?php  echo $EstateSTR1?>>可用</option>
            <option value="0" <?php  echo $EstateSTR0?>>禁用</option>
          </select></td>
		  </tr>
		<tr>
		  <td align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col">
		  <textarea name="Remark" style="width:380px;" id="Remark" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" max="100" msg="字符超出100个字节"><?php  echo $upRow["Remark"]?></textarea></td>
		  </tr>
      </table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>