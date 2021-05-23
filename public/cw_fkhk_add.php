<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 新增供应商货款回扣");//需处理
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
    	<td height="35" valign="middle" class='A0010' align="right">请款日期：</td>
	    <td valign="middle" class='A0001'><input name="Date" type="text" id="Date" size="59" dataType="Date" format="ymd" value="<?php echo date("Y-m-d"); ?>" msg="日期未选择或格式不正确" onfocus="WdatePicker()" readonly></td>
    </tr>
    <tr>
    	<td width="150" height="35" align="right" valign="middle" class='A0010'><p>供 应 商：<br> 
      </td>
	    <td valign="middle" class='A0001'>
			<select name="CompanyId" id="CompanyId" style="width: 460px;" dataType="Require"  msg="未选择" onchange="getHkAmout()">
			<?php 
			$checkSql = "SELECT P.CompanyId,P.Forshort,P.Letter,C.Symbol 
			FROM $DataIn.trade_object P 
			LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE  P.CompanyId IN (2029) ORDER BY P.Letter";
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
		  <td height="35" align="right" scope="col" class='A0010'>本月请款金额：</td>
		  <td scope="col"><input name="MonthAmount" type="text" id="MonthAmount" size="59" readonly></td>
	    </tr> 
		<tr>
		  <td height="35" align="right" scope="col" class='A0010'>返款比率：</td>
		  <td scope="col"><input name="Rate" type="text" id="Rate" size="59" dataType="Double" Msg="未填写或格式不对" onblur="CountAmount()"></td>
	    </tr>

    <tr>
    	<td height="35" valign="middle" class='A0010' align="right">返回金额：</td>
	    <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" size="59" dataType="Currency" msg="没有输入金额或金额格式不正确" readonly></td>
    </tr>


		<tr>
		  <td height="35" class='A0010'  align="right" valign="middle" scope="col">单 &nbsp;&nbsp;&nbsp;据：</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="59" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>
    <tr>
      <td height="47" align="right" valign="top" class='A0010'><br>
     备注：</td>
      <td valign="middle" class='A0001'><textarea name="Remark" cols="62" rows="6" id="Remark" dataType="Require" msg="没有输入备注"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function getHkAmout(){
   var  DateTemp=document.getElementById("Date").value;
    var MonthTemp=DateTemp.substr(0,7);
   var TempCompanyId=document.getElementById("CompanyId").value;
  if(TempCompanyId!="" && MonthTemp!=""){
	    var url="cw_fkhk_auto_ajax.php?CompanyId="+TempCompanyId+"&MonthTemp="+MonthTemp+"&ActionId=8";
	    var ajax=InitAjax();
　	ajax.open("GET",url,true);
	    ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
                  document.getElementById("MonthAmount").value=ajax.responseText;
		    	}
		}
　	ajax.send(null);
   }
}
function CountAmount(){
  var Rate=document.getElementById("Rate").value;
   var MonthAmount=document.getElementById("MonthAmount").value;
  document.getElementById("Amount").value=(MonthAmount*Rate).toFixed(2);
}
</script>