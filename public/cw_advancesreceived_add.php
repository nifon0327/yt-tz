<?php 
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增预收货款");//需处理
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
    <tr>
    	<td width="150" height="35" align="right" valign="middle" class='A0010'><p>客&nbsp;&nbsp;&nbsp;&nbsp;户：<br> 
      </td>
	    <td valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 500px;" dataType="Require"  msg="未选择">
			<?php 
			$checkSql = "SELECT P.CompanyId,P.Forshort,C.Symbol 
			FROM $DataIn.trade_object P 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE 1  AND  P.ObjectSign IN (1,2) AND P.Estate=1 ORDER BY P.OrderBy DESC,P.CompanyId";
			$checkResult = mysql_query($checkSql); 
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<option value=''>请选择</option>";
				do{
					$CompanyId=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];
					$Symbol=$checkRow["Symbol"];
					$Forshort="($Symbol) - ".$Forshort."";
					echo "<option value='$CompanyId'>$Forshort</option>";
					}while($checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		    </select>
		</td>
    </tr>
<tr>
    	<td height="35" valign="middle" class='A0010' align="right">收款日期：</td>
	    <td valign="middle" class='A0001'><input name="PayDate" type="text" id="PayDate" size="65" dataType="Date" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()" readonly></td>
    </tr>    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">预收金额：</td>
	    <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" size="65" dataType="Currency" msg="没有输入金额或金额格式不正确"></td>
    </tr>
     <tr>
				  <td height="32" scope="col" class='A0010' align="right">结付银行：</td>
				  <td scope="col" class='A0001'>
				<?php 
                include "../model/selectbank1.php";
				?>
                </td></tr>  
		<tr>
		  <td height="32" align="right"  scope="col" class='A0010' >单 &nbsp;&nbsp;&nbsp;据：</td>
		  <td scope="col" class='A0001'><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
    <tr>
      <td height="47" align="right" valign="top" class='A0010'><br>
      预收备注：</td>
      <td valign="middle" class='A0001'><textarea name="Remark" cols="60" rows="6" id="Remark" dataType="Require" msg="没有输入备注"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
