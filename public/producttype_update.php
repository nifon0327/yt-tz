<?php
//电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新产品分类资料");//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage =$funFrom."_update";
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage;
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT mainType,TypeName,scType,SortId,NameRule FROM $DataIn.producttype WHERE Id='$Id' LIMIT 1",$link_id));
$mainType=$upData["mainType"];
$TypeName=$upData["TypeName"];
$scType=$upData["scType"];
$SortId=$upData["SortId"];
$NameRule=$upData["NameRule"];
if($scType==1){$selected1="selected";$selected2="";}
else{$selected1="";$selected2="selected";}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" height="95" border="0" align="center" cellspacing="5">
          <tr>
            <td width="288" height="40" valign="middle" scope="col" align="right">产品分类名称</td>
            <td valign="middle" scope="col"><input name="TypeName" type="text" id="TypeName" title="必填项,2-30个字节的范围" value="<?php  echo $TypeName?>" style="width: 380px;" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围">
            </td>
          </tr>
          <tr>
            <td height="40" align="right" valign="middle" scope="col">主分类</td>
            <td valign="middle" scope="col"><select name="mainType" id="mainType" style="width: 380px;" dataType="Require"  msg="未选择主分类">
               <?php
			$result = mysql_query("SELECT Id,Name FROM $DataIn.productmaintype order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					$Id=$myrow["Id"];
					$Name=$myrow["Name"];
					if($Id==$mainType){
						echo "<option value='$Id' selected>$Name</option>";
						}
					else{
						echo "<option value='$Id'>$Name</option>";
						}
					}while ($myrow = mysql_fetch_array($result));
				}
			?>
            </select></td>
          </tr>
		  <tr>
		  <td height="40" align="right" valign="middle" scope="col">生产分类</td>
		  <td valign="middle" scope="col"><select name="scType" id="scType" style="width: 380px;" dataType="Require" msg="未选择生产分类">
		  <option value="1" <?php  echo $selected1?>>研砼</option>
		  <option value="2" <?php  echo $selected2?>>鼠宝</option>
          <option value="3" <?php  echo $selected3?>>皮套</option>
		  </select>
		  </td>
		</tr>
		<tr>
            <td align="right" valign="top">命名规则</td>
            <td><textarea name="NameRule" style="width:380px" rows="4" id="NameRule"><?php  echo $NameRule?></textarea></td>
        </tr>
        <tr>
		  <td height="40" align="right" valign="middle" scope="col">排序号码</td>
		  <td valign="middle" scope="col"><input name="SortId" type="text" id="SortId" title="必填项" value="<?php  echo $SortId?>" style="width: 380px;"  datatype="Number" msg="没有填写或格式不对" /></td>
	    </tr>
        </table>
</td></tr></table>
<?php
//步骤5：
include "../model/subprogram/add_model_b.php";
?>