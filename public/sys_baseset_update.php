<?php 
//电信---yang 20120801
//代码、数据共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新底薪资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT Name,KqSign,Dx,Shbz,Zsbz,Jtbz FROM $DataIn.sys1_baseset WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$KqSign=$upData["KqSign"];
$Dx=$upData["Dx"];
$Shbz=$upData["Shbz"];
$Zsbz=$upData["Zsbz"];
$Jtbz=$upData["Jtbz"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
      <tr>
            <td width="150" height="28" align="right" scope="col">考勤设定</td>
            <td scope="col">
			<?php 
			include "../model/subselect/KqSign.php";
			?>  
		  </select></td>
		</tr>
        <tr>
          <td width="150" height="40" align="right" scope="col">底薪名称</td>
          <td scope="col"><input name="Name" type="text" id="Name" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" value="<?php  echo $Name?>" style="width:380px;" maxlength="16" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"></td>
        </tr>
        <tr>
          <td height="40" align="right" scope="col">预设底薪</td>
          <td scope="col"><input name="Dx" type="text" id="Dx" value="<?php  echo $Dx?>" style="width:380px;" dataType="Currency" Msg="未填写或格式不对"></td>
        </tr>
        <tr>
          <td height="40" align="right" scope="col">预设生活补助</td>
          <td scope="col"><input name="Shbz" type="text" id="Shbz" value="<?php  echo $Shbz?>" style="width:380px;" dataType="Currency" Msg="未填写或格式不对"></td>
        </tr>
        <tr>
          <td height="43" align="right" scope="col">预设住宿补助</td>
          <td height="43" scope="col"><input name="Zsbz" type="text" id="Zsbz" value="<?php  echo $Zsbz?>" style="width:380px;" dataType="Currency" Msg="未填写或格式不对"></td>
        </tr>
         <tr>
          <td height="43" align="right" scope="col">预设交通补助</td>
          <td height="43" scope="col"><input name="Jtbz" type="text" id="Jtbz" value="<?php  echo $Jtbz?>" style="width:380px;" dataType="Currency" Msg="未填写或格式不对"></td>
        </tr>
      </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>