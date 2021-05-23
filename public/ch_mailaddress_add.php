<?php 
//电信-zxq 2012-08-01
//步骤1 $DataIn.trade_object 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增客户寄样地址");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="5">
		<tr>
		  <td scope="col" width="150" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
		  <td  scope="col">
		  <select name="CompanyId" id="CompanyId" style="width: 460px;" dataType="Require"  msg="未填写">
		  <?php 
			$Client= mysql_query("SELECT CompanyId,Forshort 
			FROM $DataIn.trade_object WHERE ObjectSign IN (1,2) AND Estate='1' ORDER BY Id",$link_id);
			if($Client_myrow = mysql_fetch_array($Client)){
				echo"<option value=''>请选择</option>";
				do{
					echo"<option value='$Client_myrow[CompanyId]'>$Client_myrow[Forshort]</option>";
					} while ($Client_myrow = mysql_fetch_array($Client));
				}  		    
			?>
			</select></td>
		</tr>
		<tr>
		  <td scope="col" align="right">收 件 人</td>
		  <td  scope="col"><input name="LinkMan" type="text" id="LinkMan" size="85" dataType="Require"  msg="未填写"></td>
		</tr> 
          <tr>
            <td align="right">收件人公司</td>
            <td ><input name="Forshort" type="text" id="Forshort" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">寄件目的地</td>
            <td ><input name="Termini" type="text" id="Termini" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">收件人电话</td>
            <td ><input name="Tel" type="text" id="Tel" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">收件人传真</td>
            <td ><input name="Fax" type="text" id="Fax" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right">邮政编码</td>
            <td ><input name="ZIP" type="text" id="ZIP" size="85" dataType="Require"  msg="未填写"></td>
          </tr>
          <tr>
            <td align="right" valign="top">通信地址</td>
            <td ><textarea name="Address" cols="55" rows="4" id="Address" dataType="Require"  msg="未填写"></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td ><textarea name="Remark" cols="55" rows="4" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>