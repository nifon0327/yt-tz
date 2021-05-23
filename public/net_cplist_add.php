<?php 
//电信-ZX  2012-08-01
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增设备资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			  <tr>
				<td width="150" align="right" scope="col">设备编号</td>
				<td valign="middle" scope="col"><input name="CpName" type="text" id="CpName" size="53" maxlength="20" dataType="Require"  msg="未填写"></td>
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
						echo"<option value='$Id'>$Name</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
             </select></td>
	    </tr>
			  <tr>
                <td align="right" scope="col">ＩＰ地址</td>
                <td valign="middle" scope="col"><input name="IpAddress" type="text" id="IpAddress" size="53" maxlength="15"></td>
	    </tr>
			  <tr>
                <td align="right" scope="col">MAC地址</td>
                <td valign="middle" scope="col"><input name="MacAddress" type="text" id="MacAddress" size="53" maxlength="17"></td>
	    </tr>
			  <tr>
			    <td align="right" scope="col">型号</td>
			    <td valign="middle" scope="col"><input name="Model" type="text" id="Model" size="53" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">服务编号</td>
			    <td valign="middle" scope="col"><input name="SSNumber" type="text" id="SSNumber" size="53" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">购买日期</td>
			    <td valign="middle" scope="col"><input name="BuyDate" type="text" id="BuyDate" size="53" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">保修期</td>
			    <td valign="middle" scope="col"><input name="Warranty" type="text" id="Warranty" size="53" dataType="Number"  msg="格式不对"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">领用人</td>
			    <td valign="middle" scope="col"><input name="User" type="text" id="User" size="53" maxlength="20" dataType="Require"  msg="未填写"></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">销售商</td>
			    <td valign="middle" scope="col"><select name="CompanyId" id="CompanyId" style="width: 300px;" dataType="Require"  msg="未选择">
				<?php 
				$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataPublic.dealerdata WHERE 1 ORDER BY Forshort",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					echo"<option value='' selected>请选择</option>";
					do{
						$CompanyId=$checkRow["CompanyId"];
						$Forshort=$checkRow["Forshort"];
						echo"<option value='$CompanyId'>$Forshort</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
		        </select></td>
	    </tr>
			  <tr>
			    <td align="right" scope="col">自检报告</td>
			    <td valign="middle" scope="col">
			      <input name="Attached" type="file" id="Attached" size="41" dataType="Filter" msg="非法的文件格式" accept="htm,html" Row="10" Cel="1"></td>
	    </tr>
			  <tr>
			    <td align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
			    <td valign="middle" scope="col"><textarea name="Remark" cols="53" rows="6" id="Remark"></textarea></td>
	    </tr>
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>