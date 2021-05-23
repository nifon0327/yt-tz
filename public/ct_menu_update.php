<?php 
//电信-EWEN
//更新到public by zx 2012-08-03
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新菜式资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.ct_menu WHERE Id='$Id' LIMIT 1",$link_id));
$CtId=$upData["CtId"];
$mType=$upData["mType"];
$Name=$upData["Name"];
$Price=$upData["Price"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../Admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
            <td height="31" scope="col" align="right">餐厅</td>
            <td scope="col"><select name="CtId" id="CtId" style="width:380px" dataType="Require"  msg="未选择分类">
            <?php 
				$CheckResult = mysql_query("SELECT Id,Name FROM $DataPublic.ct_data WHERE Estate='1' ORDER BY Id",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$IdTemp=$CheckRow["Id"];
					$NameTemp=$CheckRow["Name"];
					if($IdTemp==$CtId){
						echo"<option value='$IdTemp' selected>$NameTemp</option>";
						}
					else{
						echo"<option value='$IdTemp'>$NameTemp</option>";
						}
					}
				?>
           </select></td></tr>
		<tr>
		  <td height="31" scope="col" align="right">菜式分类</td>
		  <td scope="col"><select name="mType" id="mType" style="width:380px" dataType="Require"  msg="未选择分类">
            <?php 
				$CheckResult = mysql_query("SELECT Id,Name FROM $DataPublic.ct_type WHERE Estate='1' ORDER BY Id",$link_id);
				while ($CheckRow = mysql_fetch_array($CheckResult)){
					$IdTemp=$CheckRow["Id"];
					$NameTemp=$CheckRow["Name"];
					if($IdTemp==$mType){
						echo"<option value='$IdTemp' selected>$NameTemp</option>";
						}
					else{
						echo"<option value='$IdTemp'>$NameTemp</option>";
						}
					}
				?>
           </select></td>
	    </tr>
		<tr>
		  <td height="31" scope="col" align="right">菜式名称</td>
		  <td scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="20" value="<?php  echo $Name?>" title="必选项,在20个汉字内." datatype="LimitB" min="1" max="40" msg="没有填写或超出许可范围" /></td>
	    </tr>
		<tr>
		  <td height="31" scope="col" align="right">价格</td>
		  <td scope="col"><input name="Price" type="text" id="Price" style="width:380px;" maxlength="20" value="<?php  echo $Price?>" datatype="Currency" msg="格式不对" /></td>
	    </tr> 
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../Admin/subprogram/add_model_b.php";
?>