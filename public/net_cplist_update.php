<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_cpdata
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新设备资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.net_cpdata WHERE Id='$Id'",$link_id));
$CpName=$upData["CpName"];
$TypeId=$upData["TypeId"];
$IpAddress=$upData["IpAddress"];
$MacAddress=$upData["MacAddress"];
$theCompanyId=$upData["CompanyId"];
$Model=$upData["Model"];
$SSNumber=$upData["SSNumber"];
$BuyDate=$upData["BuyDate"];
$Warranty=$upData["Warranty"];
$User=$upData["User"];
$Remark=$upData["Remark"];
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			  <tr>
				<td width="150" align="right" scope="col">设备编号</td>
				<td valign="middle" scope="col"><input name="CpName" type="text" id="CpName" size="53" maxlength="20" value="<?php  echo $CpName?>" dataType="Require"  msg="未填写"></td>
			  </tr>
 <tr>
			    <td width="150" align="right" scope="col">设备分类</td>
			    <td valign="middle" scope="col"><select name="TypeId" id="TypeId" style="width: 300px;" dataType="Require"  msg="未选择">
                  <?php 
				$checkSql=mysql_query("SELECT Id,Name FROM $DataPublic.net_facilitytype WHERE 1 AND Estate=1 ORDER BY Id",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					echo"<option value='' selected>请选择</option>";
					do{
						$Id=$checkRow["Id"];
						$Name=$checkRow["Name"];
						if($Id==$TypeId){
							echo"<option value='$Id' selected>$Name</option>";
							}
						else{
							echo"<option value='$Id'>$Name</option>";
							}
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
             </select></td>
	    </tr>			  <tr>
                <td align="right" scope="col">ＩＰ地址</td>
                <td valign="middle" scope="col"><input name="IpAddress" type="text" id="IpAddress" value="<?php  echo $IpAddress?>" size="53" maxlength="15"></td>
	    </tr>
			  <tr>
                <td align="right" scope="col">MAC地址</td>
                <td valign="middle" scope="col"><input name="MacAddress" type="text" id="MacAddress" value="<?php  echo $MacAddress?>" size="53" maxlength="17"></td>
	    </tr>
			  <tr>
			    <td align="right" scope="col">设备型号</td>
			    <td valign="middle" scope="col"><input name="Model" type="text" id="Model" value="<?php  echo $Model?>" size="53" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">服务编号</td>
			    <td valign="middle" scope="col"><input name="SSNumber" type="text" id="SSNumber" value="<?php  echo $SSNumber?>" size="53" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">购买日期</td>
			    <td valign="middle" scope="col"><input name="BuyDate" type="text" id="BuyDate" value="<?php  echo $BuyDate?>" size="53" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">保修期</td>
			    <td valign="middle" scope="col"><input name="Warranty" type="text" id="Warranty" value="<?php  echo $Warranty?>" size="53" dataType="Number"  msg="格式不对"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">领用人</td>
			    <td valign="middle" scope="col"><input name="User" type="text" id="User" value="<?php  echo $User?>" size="53" maxlength="20" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">销售商</td>
			    <td valign="middle" scope="col"><select name="CompanyId" id="CompanyId" style="width: 300px;" dataType="Require"  msg="未选择">
				<?php 
				$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.dealerdata WHERE 1 ORDER BY Forshort",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					do{
						$CompanyId=$checkRow["CompanyId"];
						$Forshort=$checkRow["Forshort"];
						if($CompanyId==$theCompanyId){
							echo"<option value='$CompanyId' selected>$Forshort</option>";
							}
						else{
							echo"<option value='$CompanyId'>$Forshort</option>";
							}
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
		        </select></td>
	    </tr>
			  <tr>
			    <td align="right" scope="col">软件列表</td>
			    <td valign="middle" scope="col"><a href="net_cpsfsetup_read.php?hdId=<?php  echo $Id?>" target="_blank">查看及设定</a></td>
	    </tr>
			  <tr>
			    <td align="right" scope="col">自检报告</td>
			    <td valign="middle" scope="col"><input name="Attached" type="file" id="Attached" size="41" dataType="Filter" msg="非法的文件格式" accept="htm,html" Row="10" Cel="1"></td>
	    </tr>
			  <tr>
			    <td align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
			    <td valign="middle" scope="col"><textarea name="Remark" cols="53" rows="6" id="Remark"><?php  echo $Remark?></textarea></td>
	    </tr>
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>