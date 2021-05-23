<?php 
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataPublic.currencydata
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增预付订金");//需处理
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
    	<td width="150" height="35" align="right" valign="middle" class='A0010'><p>供 应 商：<br> 
      </td>
	    <td valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 500px;" dataType="Require"  msg="未选择所用操作系统" onchange="document.form1.submit()">
			<?php 
			$checkSql = "SELECT P.CompanyId,P.Forshort,P.Letter,C.Symbol 
			FROM $DataIn.trade_object P 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE (P.cSign=$Login_cSign OR P.cSign=0) AND P.Estate=1 ORDER BY P.Letter";
			$checkResult = mysql_query($checkSql); 
			if($checkRow = mysql_fetch_array($checkResult)){
				echo "<option value=''>选择供应商</option>";
				do{
					//$CompanyId=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];
					$Letter=$checkRow["Letter"];
					$Symbol=$checkRow["Symbol"];
					$Forshort=$Letter." - ($Symbol) - ".$Forshort."";
					$thisCompanyId=$checkRow["CompanyId"];
					$CompanyId=$CompanyId==""?$thisCompanyId:$CompanyId;				
					if($CompanyId==$thisCompanyId){
						echo"<option value='$thisCompanyId' selected>$Forshort </option>";
						$SearchRows.=" and M.CompanyId='$thisCompanyId'";
						}
					else{
						echo"<option value='$thisCompanyId'>$Forshort</option>";
						}				
					//echo "<option value='$CompanyId'>$Forshort</option>";
					}while($checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		    </select>
		</td>
    </tr>
    
    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">采购单号：</td>
	    <td valign="middle" class='A0001'><input name="PurchaseID" type="text" id="PurchaseID" size="94" onClick='ViewShipId()' dataType="Require"  msg="未选择" readonly="readonly"><input type="hidden" id="rmbAmount" value=""></td>
    </tr> 
    
    
    
	<tr>
    	<td height="35" valign="middle" class='A0010' align="right">请款日期：</td>
	    <td valign="middle" class='A0001'><input name="Date" type="text" id="Date" size="94" dataType="Date" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()" readonly></td>
    </tr>
    
    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">预付金额：</td>
	    <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" size="94" dataType="Currency" msg="没有输入金额或金额格式不正确" onblur="CheckAmount(this)"></td>
    </tr>
    <tr>
      <td height="47" align="right" valign="top" class='A0010'><br>
      预付备注：</td>
      <td valign="middle" class='A0001'><textarea name="Remark" cols="60" rows="6" id="Remark" dataType="Require" msg="没有输入备注"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script language = "JavaScript">
//窗口打开方式修改为兼容性的模态框 by ckt 2018-01-16
function ViewShipId(){
    var SafariReturnValue = document.getElementById('SafariReturnValue');
    if (!arguments[0]) {
        var r = Math.random();
        SafariReturnValue.value = "";
        SafariReturnValue.callback = 'ViewShipId(true)';
        var CompanyId = document.form1.CompanyId.value;
        var url = "/public/cw_fkdj_s1.php?r=" + r + "&tSearchPage=cw_fkdj&fSearchPage=ch_shipforward&SearchNum=1&CompanyId=" + CompanyId + "&Action=12";
        openFrame(url, 1000, 500);//url需为绝对路径
        return false;
    }
	if(SafariReturnValue.value){
		var CL=SafariReturnValue.value.split("^^");
        SafariReturnValue.value = "";
        SafariReturnValue.callback = "";
		//document.form1.chId.value=CL[0];
		document.form1.PurchaseID.value=CL[1];
	   document.form1.rmbAmount.value=CL[2];
		}
	}
function CheckAmount(e){
       var Amount=e.value;
       var rmbAmount=document.getElementById("rmbAmount").value;
            if(parseInt(Amount)>parseInt(rmbAmount)){
                alert("申请的订金大于采购单的金额!");
                  document.getElementById("Amount").value=""; 
               }
}
</script> 