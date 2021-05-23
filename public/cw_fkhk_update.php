<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新供应商其它扣款回扣");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.CompanyId,S.Amount,S.Remark,S.Date,S.Rate
FROM $DataIn.cw2_hksheet S WHERE S.Id=$Id ORDER BY S.Id LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$Amount=$upData["Amount"];
$Remark=$upData["Remark"];
$Date=$upData["Date"];
$Rate=$upData["Rate"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">请款日期：</td>
	    <td valign="middle" class='A0001'><input name="Date" type="text" id="Date" size="59" value="<?php  echo $Date?>" dataType="Date" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()" readonly></td>
    </tr>   
      <tr>
    	<td height="35" valign="middle" class='A0010' align="right">供 应 商：</td>

	      <?php 
			$checkSql = "SELECT P.CompanyId,P.Forshort,P.Letter,C.Symbol 
			FROM $DataIn.trade_object P 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE  P.CompanyId='$CompanyId' AND P.Estate=1 ORDER BY P.Letter";
			$checkResult = mysql_query($checkSql); 
			if($checkRow = mysql_fetch_array($checkResult)){
					$CompanyIdTemp=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];
					$Letter=$checkRow["Letter"];
					$Symbol=$checkRow["Symbol"];
					$Forshort=$Letter." - ($Symbol) - ".$Forshort."";				
			}
	?>
        
        <td valign="middle" class='A0001'><input name="Forshort" type="text" id="Forshort" size="59"  value="<?php  echo $Forshort ?>" dataType="Require"  msg="未选择" readonly="readonly">
        <input name='CompanyId' type='hidden' id='CompanyId' value="<?php  echo $CompanyId ?> ">
        </td>       
    </tr>    
		<tr>
		  <td height="35" align="right" scope="col" class='A0010'>返款比率：</td>
		  <td scope="col"><input name="Rate" type="text" id="Rate" size="59" value="<?php echo $Rate;?>" readonly></td>
	    </tr>

 <tr>
    	<td height="35" valign="middle" class='A0010' align="right">回扣金额：</td>
	    <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" size="59" value="<?php  echo $Amount?>" dataType="Currency" msg="没有输入金额或金额格式不正确"></td>
    </tr>
	<tr>
		  <td height="35" align="right" valign="middle" scope="col" class='A0010'>单 &nbsp;&nbsp;&nbsp;据：</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
    <tr>
      <td height="47" align="right" valign="top" class='A0010'><br>备注：</td>
      <td valign="middle" class='A0001'><textarea name="Remark" cols="60" rows="6" id="Remark" dataType="Require" msg="没有输入备注"><?php  echo $Remark?></textarea></td>
    </tr>
</table>
<?php 
include "../model/subprogram/add_model_b.php";
?>